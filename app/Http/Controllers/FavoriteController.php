<?php

namespace App\Http\Controllers;

use App\Models\Urun;
use App\Models\UrunVariant;
use App\Models\Category;
use App\Models\User;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Direkt DB sorgusu — belongsToMany karmaşası yok
        $favoritedVariantIds = DB::table('favorites')
            ->where('user_id', $user->id)
            ->pluck('urun_variant_id')
            ->toArray();

        if (empty($favoritedVariantIds)) {
            $urunler = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), 0, 16, 1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            $categories  = Category::all();
            $brands      = UrunVariant::whereNotNull('brand')->distinct()->pluck('brand');
            $collections = UrunVariant::whereNotNull('collection')->distinct()->pluck('collection');
            $sizes       = UrunVariant::distinct()->pluck('size')->filter();
            $about       = \App\Models\About::first();
            $userFavorites = [];
            return view('favorites', compact('urunler', 'categories', 'brands', 'collections', 'sizes', 'about', 'userFavorites'));
        }

        $query = Urun::with(['category', 'variants.images'])
            ->whereHas('variants', function($q) use ($favoritedVariantIds) {
                $q->whereIn('id', $favoritedVariantIds);
            });

        if ($request->filled('q')) {
            $search = strtoupper(trim($request->q));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('UPPER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereHas('variants', function ($sq) use ($search) {
                      $sq->whereRaw('UPPER(sku) LIKE ?', ["%{$search}%"])
                         ->orWhereRaw('UPPER(brand) LIKE ?', ["%{$search}%"])
                         ->orWhereRaw('UPPER(collection) LIKE ?', ["%{$search}%"]);
                  });
            });
        }

        if ($request->filled('cat')) {
            $query->whereIn('category_id', (array)$request->cat);
        }

        if ($request->anyFilled(['brand', 'collection', 'size', 'min', 'max'])) {
            $query->whereHas('variants', function($q) use ($request) {
                if($request->filled('brand'))      $q->whereIn('brand', (array)$request->brand);
                if($request->filled('collection')) $q->whereIn('collection', (array)$request->collection);
                if($request->filled('size'))       $q->whereIn('size', (array)$request->size);
                if($request->filled('min'))        $q->where('price', '>=', $request->min);
                if($request->filled('max'))        $q->where('price', '<=', $request->max);
            });
        }

        $allReviews = \App\Models\Review::where('is_approved', true)
            ->select('urun_id', 'rating')
            ->get()
            ->groupBy('urun_id');

        $allProducts = $query->latest()->get();
        $colorBasedItems = collect();

        foreach ($allProducts as $urun) {
            $variantsByColor = $urun->variants->groupBy('color_code');

            foreach ($variantsByColor as $colorCode => $variantGroup) {
                // Sadece bu renk grubunda favorili varyant varsa ekle
                $hasFavoriteInGroup = $variantGroup->whereIn('id', $favoritedVariantIds)->isNotEmpty();
                if (!$hasFavoriteInGroup) continue;

                $firstV = $variantGroup->first();

                $productReviews = $allReviews->get($urun->id, collect());
                $reviewsAvg     = $productReviews->avg('rating') ?? 0;
                $reviewsCount   = $productReviews->count();

                $allVariants = $urun->variants->map(function($v) {
                    return [
                        'id'             => $v->id,
                        'color'          => $v->color,
                        'color_code'     => $v->color_code,
                        'size'           => $v->size,
                        'price'          => $v->price,
                        'discount_price' => $v->discount_price,
                        'stock'          => $v->stock,
                        'sku'            => $v->sku,
                        'brand'          => $v->brand,
                        'collection'     => $v->collection,
                        'is_active'      => $v->is_active,
                        'images'         => $v->images->map(fn($img) => ['image_url' => $img->image_url])->values()
                    ];
                })->values();

                $colorBasedItems->push((object)[
                    'id'                 => $urun->id,
                    'name'               => $urun->name,
                    'brand'              => $firstV->brand,
                    'collection'         => $firstV->collection,
                    'description'        => $urun->description,
                    'category_name'      => $urun->category->name ?? '',
                    'color_code'         => $colorCode,
                    'color_name'         => $firstV->color,
                    'is_active'          => $firstV->is_active,
                    'variants'           => $variantGroup->values(),
                    'images'             => $variantGroup->flatMap->images->unique('image_url')->values(),
                    'first_variant'      => $firstV,
                    'all_colors'         => $urun->variants->pluck('color_code')->unique()->values(),
                    'all_variants'       => $allVariants,
                    'reviews_avg_rating' => round($reviewsAvg, 1),
                    'reviews_count'      => $reviewsCount,
                ]);
            }
        }

        $currentPage = request()->get('page', 1);
        $perPage     = 16;
        $urunler     = new \Illuminate\Pagination\LengthAwarePaginator(
            $colorBasedItems->forPage($currentPage, $perPage),
            $colorBasedItems->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $categories    = Category::all();
        $brands        = UrunVariant::whereNotNull('brand')->distinct()->pluck('brand');
        $collections   = UrunVariant::whereNotNull('collection')->distinct()->pluck('collection');
        $sizes         = UrunVariant::distinct()->pluck('size')->filter();
        $about         = \App\Models\About::first();
        $userFavorites = $favoritedVariantIds;

        return view('favorites', compact('urunler', 'categories', 'brands', 'collections', 'sizes', 'about', 'userFavorites'));
    }

    public function check($variantId)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['isFavorite' => false]);
            }

            $isFavorite = DB::table('favorites')
                ->where('user_id', Auth::id())
                ->where('urun_variant_id', $variantId)
                ->exists();

            return response()->json(['isFavorite' => $isFavorite]);

        } catch (\Exception $e) {
            Log::error('Favori check hatası: ' . $e->getMessage());
            return response()->json(['isFavorite' => false]);
        }
    }

    public function toggle(UrunVariant $urunVariant)
    {
        $user = Auth::user();

        $exists = DB::table('favorites')
            ->where('user_id', $user->id)
            ->where('urun_variant_id', $urunVariant->id)
            ->exists();

        if ($exists) {
            DB::table('favorites')
                ->where('user_id', $user->id)
                ->where('urun_variant_id', $urunVariant->id)
                ->delete();
            $isAdded = false;
        } else {
            DB::table('favorites')->insert([
                'user_id'          => $user->id,
                'urun_variant_id'  => $urunVariant->id,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
            $isAdded = true;
        }

        return response()->json([
            'status'  => $isAdded ? 'added' : 'removed',
            'message' => $isAdded ? 'Added to favorites' : 'Removed from favorites',
        ]);
    }

    public function destroy(UrunVariant $urunVariant)
    {
        DB::table('favorites')
            ->where('user_id', Auth::id())
            ->where('urun_variant_id', $urunVariant->id)
            ->delete();

        return redirect()->back()->with('success', 'Removed from favorites');
    }
}
