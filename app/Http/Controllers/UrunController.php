<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Urun;
use App\Models\Category;
use App\Models\UrunVariant;
use Illuminate\Support\Str;
use App\Models\About;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class UrunController extends Controller
{
    public function urun(Request $request)
    {
        $query = Urun::with(['variants', 'images', 'category']);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('variants', function($sq) use ($searchTerm) {
                      $sq->where('sku', 'like', '%' . $searchTerm . '%')
                        ->orWhere('brand', 'like', '%' . $searchTerm . '%')
                        ->orWhere('collection', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $uruns = $query->latest()->get();
        $categories = Category::all();

        $totalProducts = \App\Models\UrunVariant::distinct('urun_id', 'color_code')->count(['urun_id', 'color_code']);
        $totalVariants = \App\Models\UrunVariant::count();
        $totalStock = \App\Models\UrunVariant::sum('stock');
        $totalCategories = Category::count();

        return view('adminpanel', compact('uruns', 'categories', 'totalProducts', 'totalVariants', 'totalStock', 'totalCategories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', 1)->get();
        return view('create', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'variants'    => 'required|array',
                'variants.*.brand'      => 'nullable|regex:/^[a-zA-ZçÇğĞıİöÖşŞüÜ\s]+$/u|max:255',
                'variants.*.collection' => 'nullable|regex:/^[a-zA-ZçÇğĞıİöÖşŞüÜ\s]+$/u|max:255',
                'variants.*.price'      => 'required|numeric|min:0',
                'variants.*.stock'      => 'required|integer|min:0',
                'variants.*.is_active'  => 'required|in:0,1',
            ]);

            $urun = Urun::create([
                'category_id' => $request->category_id,
                'name'        => $request->name,
                'slug'        => Str::slug($request->name . '-' . uniqid()),
                'description' => $request->description,
            ]);

            foreach ($request->variants as $v) {
                $v_brand = isset($v['brand']) ? mb_convert_case(trim($v['brand']), MB_CASE_TITLE, "UTF-8") : null;
                $v_collection = isset($v['collection']) ? mb_convert_case(trim($v['collection']), MB_CASE_TITLE, "UTF-8") : null;

                $variant = $urun->variants()->create([
                    'color'          => $v['color'],
                    'color_code'     => $v['color_code'],
                    'size'           => $v['size'],
                    'sku'            => isset($v['sku']) ? strtoupper(trim($v['sku'])) : null,
                    'price'          => $v['price'],
                    'discount_price' => $v['discount_price'] ?? null,
                    'stock'          => $v['stock'],
                    'brand'          => $v_brand,
                    'collection'     => $v_collection,
                    'is_active'      => $v['is_active'],
                ]);

                if (!empty($v['variant_images'])) {
                    $urls = preg_split('/\r\n|\r|\n/', $v['variant_images']); 
                    foreach ($urls as $url) {
                        if (!empty(trim($url))) {
                            $variant->images()->create([
                                'image_url' => trim($url),
                                'urun_id'   => $urun->id,
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('create')->with('success', 'Product and variants created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function urunler(Request $request)
    {
        $query = Urun::with(['category', 'variants' => function($q) use ($request) {
            if($request->filled('status'))     $q->where('is_active', $request->status);
            if($request->filled('brand'))      $q->whereIn('brand', (array)$request->brand);
            if($request->filled('collection')) $q->whereIn('collection', (array)$request->collection);
            if($request->filled('size'))       $q->whereIn('size', (array)$request->size);
            if($request->filled('min'))        $q->where('price', '>=', $request->min);
            if($request->filled('max'))        $q->where('price', '<=', $request->max);
        }, 'variants.images']);

        if($request->filled('q')){
            $search = strtoupper(trim($request->q));
            $query->where(function($q) use ($search){
                $q->whereRaw('UPPER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereHas('variants', function($sq) use ($search){
                      $sq->whereRaw('UPPER(sku) LIKE ?', ["%{$search}%"])
                         ->orWhereRaw('UPPER(brand) LIKE ?', ["%{$search}%"])
                         ->orWhereRaw('UPPER(collection) LIKE ?', ["%{$search}%"]);
                  });
            });
        }

        if($request->anyFilled(['status', 'brand', 'collection', 'size', 'min', 'max'])) {
            $query->whereHas('variants', function($q) use ($request) {
                if($request->filled('status'))     $q->where('is_active', $request->status);
                if($request->filled('brand'))      $q->whereIn('brand', (array)$request->brand);
                if($request->filled('collection')) $q->whereIn('collection', (array)$request->collection);
                if($request->filled('size'))       $q->whereIn('size', (array)$request->size);
                if($request->filled('min'))        $q->where('price', '>=', $request->min);
                if($request->filled('max'))        $q->where('price', '<=', $request->max);
            });
        }

        if($request->filled('cat')) {
            $query->whereIn('category_id', (array)$request->cat);
        }

        $allProducts = $query->latest()->get();
        $colorBasedItems = collect();

        foreach($allProducts as $urun){
            $variantsByColor = $urun->variants->groupBy('color_code');

            foreach($variantsByColor as $colorCode => $variantGroup){
                $firstV = $variantGroup->first();
                if(!$firstV) continue;

                if($request->filled('q')){
                    $searchTerm = strtoupper(request('q'));
                    $matchInVariant = $variantGroup->contains(function($v) use ($searchTerm){
                        return str_contains(strtoupper($v->sku), $searchTerm) || 
                               str_contains(strtoupper($v->brand), $searchTerm) || 
                               str_contains(strtoupper($v->collection), $searchTerm);
                    });

                    if(!str_contains(strtoupper($urun->name), $searchTerm) && !$matchInVariant){
                        continue; 
                    }
                }

                $colorBasedItems->push((object)[
                    'id'            => $urun->id,
                    'name'          => $urun->name,
                    'brand'         => $firstV->brand,
                    'collection'    => $firstV->collection,
                    'description'   => $urun->description,
                    'category_name' => $urun->category->name ?? '',
                    'color_code'    => $colorCode,
                    'color_name'    => $firstV->color,
                    'is_active'     => $firstV->is_active,
                    'variants'      => $variantGroup->values(),
                    'images'        => $variantGroup->flatMap->images->unique('image_url')->values(),
                    'first_variant' => $firstV,
                    'all_colors'    => UrunVariant::where('urun_id', $urun->id)->distinct()->pluck('color_code')
                ]);
            }
        }

        // PAGINATION KALDIRILDI
        $urunler = $colorBasedItems;

        $categories  = Category::all();
        $brands      = UrunVariant::whereNotNull('brand')->distinct()->pluck('brand');
        $collections = UrunVariant::whereNotNull('collection')->distinct()->pluck('collection');
        $sizes       = UrunVariant::distinct()->pluck('size')->filter();

        return view('urunler', compact('urunler', 'categories', 'brands', 'collections', 'sizes'));
    }

    public function urundetay($id)
    {
        $varsayilanVariant = UrunVariant::with('images')->findOrFail($id);
        $urun = Urun::with(['variants.images', 'category'])->findOrFail($varsayilanVariant->urun_id);
        $categories = Category::all(); 

        return view('urundetay', compact('urun', 'varsayilanVariant', 'categories'));
    }

    public function variantDelete(Request $request)
    {
        $variant = UrunVariant::findOrFail($request->variant_id);
        $urun_id = $variant->urun_id;
        $variant->delete();

        $kalanVarmi = UrunVariant::where('urun_id', $urun_id)->exists();

        if ($kalanVarmi) {
            return redirect()->route('urunler')->with('success', 'The variant has been deleted; you will be redirected to the product list.');
        } else {
            return redirect()->route('urunler')->with('success', 'The product has been completely deleted.');
        }
    }

    public function adminpanel()
    {
        $totalProducts = UrunVariant::distinct('urun_id', 'color_code')->count(['urun_id', 'color_code']);
        $totalVariants = UrunVariant::count();
        $totalStock = UrunVariant::sum('stock');
        $totalCategories = Category::count();
        $uruns = Urun::with(['variants', 'images', 'category'])->latest()->get();

        return view('adminpanel', compact('totalProducts', 'totalVariants', 'totalStock', 'totalCategories', 'uruns'));
    }

    public function variantUpdate(Request $request)
    {
        try {
            $request->validate([
                'urun_id'         => 'required|exists:uruns,id',
                'variant_id'      => 'required|exists:urun_variants,id',
                'brand'           => 'nullable|regex:/^[a-zA-ZçÇğĞıİöÖşŞüÜ\s]+$/u|max:255',
                'collection'      => 'nullable|regex:/^[a-zA-ZçÇğĞıİöÖşŞüÜ\s]+$/u|max:255',
                'sku'             => 'nullable|string|max:100|unique:urun_variants,sku,' . $request->variant_id,
                'price'           => 'required|numeric|min:0',
                'is_active'       => 'required|in:0,1',
            ]);

            $brand = $request->brand ? mb_convert_case(trim($request->brand), MB_CASE_TITLE, "UTF-8") : null;
            $collection = $request->collection ? mb_convert_case(trim($request->collection), MB_CASE_TITLE, "UTF-8") : null;

            $urun = Urun::findOrFail($request->urun_id);
            $urun->update([
                'category_id' => $request->category_id,
                'name'        => $request->name,
                'description' => $request->description,
            ]);

            $variant = UrunVariant::findOrFail($request->variant_id);
            $variant->update([
                'brand'          => $brand,
                'collection'     => $collection,
                'is_active'      => $request->is_active, 
                'sku'            => $request->sku ? strtoupper(trim($request->sku)) : null,
                'price'          => $request->price,
                'discount_price' => $request->discount_price ?? null,
                'stock'          => $request->stock,
                'size'           => $request->size,
                'color'          => $request->color,
                'color_code'     => $request->color_code,
            ]);

            if ($request->filled('variant_images')) {
                $variant->images()->delete();
                $urls = preg_split('/\r\n|\r|\n/', $request->variant_images); 
                foreach ($urls as $url) {
                    if (!empty(trim($url))) {
                        $variant->images()->create([
                            'image_url'  => trim($url),
                            'urun_id'    => $urun->id,
                            'variant_id' => $variant->id,
                        ]);
                    }
                }
            }

            return redirect()->back()->with('success', '
The variant has been successfully updated!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function about(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'second_paragraph' => 'nullable|string',
            'last_paragraph' => 'nullable|string',
            'image' => 'nullable|url',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'instagram_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'github_url' => 'nullable|url', 
        ]);

        About::create($validated);
        return redirect()->route('about')->with('success', 'Our About Us page has been created!');
    }

    public function aboutPage() {
        $about = About::first();
        return view('about', compact('about'));
    }

    public function updateAbout($id, Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'second_paragraph' => 'nullable|string',
            'last_paragraph' => 'nullable|string',
            'image' => 'nullable|url',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'instagram_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
        ]);

        $about = About::findOrFail($id);
        $about->update($validated);

        return redirect()->route('about')->with('success', 'The About Us page has been successfully updated!');
    }

    public function editAbout($id) {
        $about = About::findOrFail($id); 
        return view('editAbout', ['ourPost' => $about]);
    }

    public function index(Request $request)
    {
        $query = Urun::with(['category', 'variants' => function($q) use ($request) {
            if($request->filled('status'))     $q->where('is_active', $request->status);
            if($request->filled('brand'))      $q->whereIn('brand', (array)$request->brand);
            if($request->filled('collection')) $q->whereIn('collection', (array)$request->collection);
            if($request->filled('size'))       $q->whereIn('size', (array)$request->size);
            if($request->filled('min'))        $q->where('price', '>=', $request->min);
            if($request->filled('max'))        $q->where('price', '<=', $request->max);
        }, 'variants.images']);

        if($request->filled('q')){
            $search = strtoupper(trim($request->q));
            $query->where(function($q) use ($search){
                $q->whereRaw('UPPER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereHas('variants', function($sq) use ($search){
                      $sq->whereRaw('UPPER(sku) LIKE ?', ["%{$search}%"])
                         ->orWhereRaw('UPPER(brand) LIKE ?', ["%{$search}%"])
                         ->orWhereRaw('UPPER(collection) LIKE ?', ["%{$search}%"]);
                  });
            });
        }

        if($request->anyFilled(['status', 'brand', 'collection', 'size', 'min', 'max'])) {
            $query->whereHas('variants', function($q) use ($request) {
                if($request->filled('status'))     $q->where('is_active', $request->status);
                if($request->filled('brand'))      $q->whereIn('brand', (array)$request->brand);
                if($request->filled('collection')) $q->whereIn('collection', (array)$request->collection);
                if($request->filled('size'))       $q->whereIn('size', (array)$request->size);
                if($request->filled('min'))        $q->where('price', '>=', $request->min);
                if($request->filled('max'))        $q->where('price', '<=', $request->max);
            });
        }

        if($request->filled('cat')) {
            $query->whereIn('category_id', (array)$request->cat);
        }

        $allProducts = $query->latest()->get();
        $colorBasedItems = collect();

        foreach($allProducts as $urun){
            $variantsByColor = $urun->variants->groupBy('color_code');

            foreach($variantsByColor as $colorCode => $variantGroup){
                $firstV = $variantGroup->first();
                if(!$firstV) continue;

                if($request->filled('q')){
                    $searchTerm = strtoupper(request('q'));
                    $matchInVariant = $variantGroup->contains(function($v) use ($searchTerm){
                        return str_contains(strtoupper($v->sku), $searchTerm) || 
                               str_contains(strtoupper($v->brand), $searchTerm) || 
                               str_contains(strtoupper($v->collection), $searchTerm);
                    });

                    if(!str_contains(strtoupper($urun->name), $searchTerm) && !$matchInVariant){
                        continue; 
                    }
                }

                $allVariants = $urun->variants->map(function($v) {
                    return [
                        'id' => $v->id,
                        'color' => $v->color,
                        'color_code' => $v->color_code,
                        'size' => $v->size,
                        'price' => $v->price,
                        'discount_price' => $v->discount_price,
                        'stock' => $v->stock,
                        'sku' => $v->sku,
                        'brand' => $v->brand,
                        'collection' => $v->collection,
                        'is_active' => $v->is_active,
                        'images' => $v->images->map(function($img) {
                            return ['image_url' => $img->image_url];
                        })->values()
                    ];
                })->values();

                $colorBasedItems->push((object)[
                    'id'            => $urun->id,
                    'name'          => $urun->name,
                    'brand'         => $firstV->brand,
                    'collection'    => $firstV->collection,
                    'description'   => $urun->description,
                    'category_name' => $urun->category->name ?? '',
                    'color_code'    => $colorCode,
                    'color_name'    => $firstV->color,
                    'is_active'     => $firstV->is_active,
                    'variants'      => $variantGroup->values(),
                    'images'        => $variantGroup->flatMap->images->unique('image_url')->values(),
                    'first_variant' => $firstV,
                    'all_colors'    => \App\Models\UrunVariant::where('urun_id', $urun->id)->distinct()->pluck('color_code'),
                    'all_variants'  => $allVariants
                ]);
            }
        }

        // PAGINATION KALDIRILDI
        $urunler = $colorBasedItems;

        $about = \App\Models\About::first();

        $categories  = \App\Models\Category::all();
        $brands      = \App\Models\UrunVariant::whereNotNull('brand')->distinct()->pluck('brand');
        $collections = \App\Models\UrunVariant::whereNotNull('collection')->distinct()->pluck('collection');
        $sizes       = \App\Models\UrunVariant::distinct()->pluck('size')->filter();

        return view('welcome', compact('urunler', 'categories', 'brands', 'collections', 'sizes', 'about'));
    }

    public function login(){
        return view('userlogin');
    }

    // YENİ: Reviews API endpoint
    public function getVariantReviews($variantId)
    {
        $variant = UrunVariant::findOrFail($variantId);
        
        $reviews = Review::where('urun_id', $variant->urun_id)
            ->where('is_approved', true)
            ->with(['user:id,name'])
            ->latest()
            ->get();
        
        $avgRating = $reviews->avg('rating') ?? 0;
        $totalReviews = $reviews->count();
        $totalLikes = $reviews->sum('likes_count') ?? 0;
        
        return response()->json([
            'reviews' => $reviews,
            'avg_rating' => round($avgRating, 1),
            'total_reviews' => $totalReviews,
            'total_likes' => $totalLikes
        ]);
    }

    public function dashboard(Request $request)
    {
        $query = Urun::with(['category', 'variants' => function($q) use ($request) {
            if($request->filled('status'))     $q->where('is_active', $request->status);
            if($request->filled('brand'))      $q->whereIn('brand', (array)$request->brand);
            if($request->filled('collection')) $q->whereIn('collection', (array)$request->collection);
            if($request->filled('size'))       $q->whereIn('size', (array)$request->size);
            if($request->filled('min'))        $q->where('price', '>=', $request->min);
            if($request->filled('max'))        $q->where('price', '<=', $request->max);
        }, 'variants.images']);

        if($request->filled('q')){
            $search = strtoupper(trim($request->q));
            $query->where(function($q) use ($search){
                $q->whereRaw('UPPER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereHas('variants', function($sq) use ($search){
                      $sq->whereRaw('UPPER(sku) LIKE ?', ["%{$search}%"])
                         ->orWhereRaw('UPPER(brand) LIKE ?', ["%{$search}%"])
                         ->orWhereRaw('UPPER(collection) LIKE ?', ["%{$search}%"]);
                  });
            });
        }

        if($request->anyFilled(['status', 'brand', 'collection', 'size', 'min', 'max'])) {
            $query->whereHas('variants', function($q) use ($request) {
                if($request->filled('status'))     $q->where('is_active', $request->status);
                if($request->filled('brand'))      $q->whereIn('brand', (array)$request->brand);
                if($request->filled('collection')) $q->whereIn('collection', (array)$request->collection);
                if($request->filled('size'))       $q->whereIn('size', (array)$request->size);
                if($request->filled('min'))        $q->where('price', '>=', $request->min);
                if($request->filled('max'))        $q->where('price', '<=', $request->max);
            });
        }

        if($request->filled('cat')) {
            $query->whereIn('category_id', (array)$request->cat);
        }

        // TÜM REVIEWLARI TEK SORGUDA ÇEK
        $allReviews = Review::where('is_approved', true)
            ->select('urun_id', 'rating')
            ->get()
            ->groupBy('urun_id');

        $allProducts = $query->latest()->get();
        $colorBasedItems = collect();

        foreach($allProducts as $urun){
            $variantsByColor = $urun->variants->groupBy('color_code');

            foreach($variantsByColor as $colorCode => $variantGroup){
                $firstV = $variantGroup->first();
                if(!$firstV) continue;

                if($request->filled('q')){
                    $searchTerm = strtoupper(request('q'));
                    $matchInVariant = $variantGroup->contains(function($v) use ($searchTerm){
                        return str_contains(strtoupper($v->sku), $searchTerm) || 
                               str_contains(strtoupper($v->brand), $searchTerm) || 
                               str_contains(strtoupper($v->collection), $searchTerm);
                    });

                    if(!str_contains(strtoupper($urun->name), $searchTerm) && !$matchInVariant){
                        continue; 
                    }
                }

                // REVIEW İSTATİSTİKLERİNİ HESAPLA
                $productReviews = $allReviews->get($urun->id, collect());
                $reviewsAvg = $productReviews->avg('rating') ?? 0;
                $reviewsCount = $productReviews->count();

                $allVariants = $urun->variants->map(function($v) {
                    return [
                        'id' => $v->id,
                        'color' => $v->color,
                        'color_code' => $v->color_code,
                        'size' => $v->size,
                        'price' => $v->price,
                        'discount_price' => $v->discount_price,
                        'stock' => $v->stock,
                        'sku' => $v->sku,
                        'brand' => $v->brand,
                        'collection' => $v->collection,
                        'is_active' => $v->is_active,
                        'images' => $v->images->map(function($img) {
                            return ['image_url' => $img->image_url];
                        })->values()
                    ];
                })->values();

                $colorBasedItems->push((object)[
                    'id'            => $urun->id,
                    'name'          => $urun->name,
                    'brand'         => $firstV->brand,
                    'collection'    => $firstV->collection,
                    'description'   => $urun->description,
                    'category_name' => $urun->category->name ?? '',
                    'color_code'    => $colorCode,
                    'color_name'    => $firstV->color,
                    'is_active'     => $firstV->is_active,
                    'variants'      => $variantGroup->values(),
                    'images'        => $variantGroup->flatMap->images->unique('image_url')->values(),
                    'first_variant' => $firstV,
                    'all_colors'    => \App\Models\UrunVariant::where('urun_id', $urun->id)->distinct()->pluck('color_code'),
                    'all_variants'  => $allVariants,
                    'reviews_avg_rating' => round($reviewsAvg, 1),
                    'reviews_count'      => $reviewsCount
                ]);
            }
        }

        // PAGINATION KALDIRILDI
        $urunler = $colorBasedItems;

        $about = \App\Models\About::first();
        
        $userFavorites = [];
        if (\Auth::check()) {
            $userFavorites = \Auth::user()->favorites()->pluck('urun_variant_id')->toArray();
        }
        
        $categories  = \App\Models\Category::all();
        $brands      = \App\Models\UrunVariant::whereNotNull('brand')->distinct()->pluck('brand');
        $collections = \App\Models\UrunVariant::whereNotNull('collection')->distinct()->pluck('collection');
        $sizes       = \App\Models\UrunVariant::distinct()->pluck('size')->filter();
        
        return view('dashboard', compact('urunler', 'categories', 'brands', 'collections', 'sizes', 'about', 'userFavorites'));
    }
}
