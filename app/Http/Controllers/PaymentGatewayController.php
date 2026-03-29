<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentGatewayController extends Controller
{
    /**
     * Ödeme gateway'ine yönlendir
     */
    public function index($order)
    {
        // $order order_number olarak geliyor
        $orderModel = Order::with('items.urun')->where('order_number', $order)->firstOrFail();

        // Güvenlik kontrolü
        if ($orderModel->user_id !== Auth::id()) {
            abort(403, 'You are not the owner of this order.');
        }

        // Zaten ödenmiş mi kontrol et
        if ($orderModel->payment_status === 'paid') {
            return redirect()->route('checkout.success', ['order' => $order])
                ->with('info', 'This order is already paid.');
        }

        // Ödeme yöntemine göre gateway seç
        return match($orderModel->payment_method) {
            'credit_card' => $this->payTR($orderModel),
            'cash_on_delivery' => $this->cashOnDelivery($orderModel),
            'bank_transfer' => $this->bankTransfer($orderModel),
            default => redirect()->back()->with('error', 'Invalid payment method.')
        };
    }

    /**
     * PayTR entegrasyonu
     */
    private function payTR(Order $order)
    {
        try {
            $merchant_id = config('services.paytr.merchant_id');
            $merchant_key = config('services.paytr.merchant_key');
            $merchant_salt = config('services.paytr.merchant_salt');
            $test_mode = config('services.paytr.test_mode', 1);

            // Zorunlu alan kontrolü
            if (!$merchant_id || !$merchant_key || !$merchant_salt) {
                throw new \Exception('PayTR konfigürasyonu eksik');
            }

            $email = Auth::user()->email;
            $payment_amount = (int) ($order->total * 100); // PayTR kuruş istiyor
            $merchant_oid = $order->order_number;
            $user_name = $order->shipping_full_name ?? Auth::user()->name;
            $user_address = $order->shipping_address;
            $user_phone = $order->shipping_phone;

            // Sepet verisi
            $user_basket = base64_encode(json_encode($this->getBasketData($order)));

            // Token oluşturma - PayTR dokümantasyonuna göre sıralama
            $hash_str = $merchant_id . 
                       request()->ip() . 
                       $merchant_oid . 
                       $email . 
                       $payment_amount . 
                       $user_basket . 
                       $test_mode . 
                       '0' . // no_installment
                       '12' . // max_installment
                       'TL' . 
                       $merchant_salt;

            $paytr_token = base64_encode(hash_hmac('sha256', $hash_str, $merchant_key, true));

            $data = [
                'merchant_id' => $merchant_id,
                'user_ip' => request()->ip(),
                'merchant_oid' => $merchant_oid,
                'email' => $email,
                'payment_amount' => $payment_amount,
                'paytr_token' => $paytr_token,
                'user_basket' => $user_basket,
                'debug_on' => 0,
                'no_installment' => 0,
                'max_installment' => 12,
                'user_name' => $user_name,
                'user_address' => $user_address,
                'user_phone' => $user_phone,
                'merchant_ok_url' => route('checkout.success', $order->order_number),
                'merchant_fail_url' => route('checkout.index'),
                'timeout_limit' => 30,
                'currency' => 'TL',
                'test_mode' => $test_mode
            ];

            // PayTR API isteği
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $result = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code !== 200 || !$result) {
                throw new \Exception('PayTR API bağlantı hatası: HTTP ' . $http_code);
            }

            $result = json_decode($result, true);

            if (!isset($result['status']) || $result['status'] !== 'success') {
                $reason = $result['reason'] ?? 'Bilinmeyen hata';
                Log::error('PayTR Token Hatası', [
                    'order_id' => $order->id,
                    'reason' => $reason,
                    'response' => $result
                ]);
                throw new \Exception('PayTR: ' . $reason);
            }

            // Token alındı, iFrame ile ödeme sayfasına yönlendir
            return view('checkout.paytr_iframe', [
                'token' => $result['token'],
                'order' => $order
            ]);

        } catch (\Exception $e) {
            Log::error('PayTR Entegrasyon Hatası', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('myorders.index')
                ->with('error', 'Payment could not be initiated: ' . $e->getMessage());
        }
    }

    /**
     * Kapıda ödeme
     */
    private function cashOnDelivery(Order $order)
    {
        $order->update([
            'payment_status' => 'pending',
            'status' => 'processing'
        ]);

        return redirect()->route('checkout.success', $order->order_number)
            ->with('success', 'Your order has been placed. You selected cash on delivery.');
    }

    /**
     * Havale/EFT
     */
    private function bankTransfer(Order $order)
    {
        $order->update([
            'payment_status' => 'pending',
            'status' => 'awaiting_payment'
        ]);

        // TODO: E-posta gönderimi
        // Mail::to($order->user)->send(new BankTransferInstructions($order));

        return redirect()->route('checkout.success', $order->order_number)
            ->with('info', 'Bank transfer information has been sent to your email.');
    }

    /**
     * Sepet verisini PayTR formatına çevir
     */
    private function getBasketData(Order $order): array
    {
        $basket = [];
        
        foreach ($order->items as $item) {
            $basket[] = [
                $item->product_name ?? $item->urun?->name ?? 'Ürün',
                number_format($item->unit_price, 2, '.', ''),
                (int) $item->quantity
            ];
        }

        return $basket;
    }

    /**
     * PayTR callback/webhook
     */
    public function callback(Request $request)
    {
        try {
            $merchant_key = config('services.paytr.merchant_key');
            $merchant_salt = config('services.paytr.merchant_salt');

            // Hash doğrulama
            $hash = base64_encode(hash_hmac('sha256', 
                $request->merchant_oid . 
                $request->status . 
                $request->total_amount . 
                $merchant_salt, 
                $merchant_key, 
                true
            ));

            if ($hash !== $request->hash) {
                Log::warning('PayTR Callback - Geçersiz hash', [
                    'ip' => $request->ip(),
                    'data' => $request->all()
                ]);
                abort(403, 'Invalid hash');
            }

            $order = Order::where('order_number', $request->merchant_oid)->firstOrFail();

            if ($request->status === 'success') {
                $order->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                    'status' => 'processing',
                    'payment_transaction_id' => $request->payment_type ?? null
                ]);

                Log::info('PayTR Ödeme Başarılı', [
                    'order_id' => $order->id,
                    'amount' => $request->total_amount
                ]);
            } else {
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'payment_failed'
                ]);

                Log::warning('PayTR Ödeme Başarısız', [
                    'order_id' => $order->id,
                    'reason' => $request->failed_reason_code ?? 'Bilinmiyor'
                ]);
            }

            // PayTR "OK" yanıtı bekler
            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('PayTR Callback Hatası', [
                'message' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response('ERROR', 500);
        }
    }
}