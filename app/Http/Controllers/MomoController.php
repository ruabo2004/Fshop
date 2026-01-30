<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class MomoController extends Controller
{
    public function payment()
    {
        try {
            $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
            $partnerCode = env('MOMO_PARTNER_CODE');
            $accessKey = env('MOMO_ACCESS_KEY');
            $secretKey = env('MOMO_SECRET_KEY');
            $orderInfo = "Thanh toan don hang Fshop"; 
            $exchangeRate = 25000; 
            $usdTotal = Cart::instance('cart')->total();
            
            $usdTotal = str_replace([',', '.'], ['', '.'], $usdTotal); 
            
            $rawTotal = (float)str_replace(',', '', Cart::instance('cart')->total());
            $vndAmount = (int)round($rawTotal * $exchangeRate);
            
            $amount = (string)$vndAmount;
            $orderId = (string) round(microtime(true) * 10000);
            $redirectUrl = route('momo.callback');
            $ipnUrl = route('momo.callback'); 
            $requestId = (string) round(microtime(true) * 10000);
            $requestType = "payWithATM";
            $extraData = "";
            
            $rawHash = "accessKey=" . $accessKey . 
                    "&amount=" . $amount . 
                    "&extraData=" . $extraData . 
                    "&ipnUrl=" . $ipnUrl . 
                    "&orderId=" . $orderId . 
                    "&orderInfo=" . $orderInfo . 
                    "&partnerCode=" . $partnerCode . 
                    "&redirectUrl=" . $redirectUrl . 
                    "&requestId=" . $requestId . 
                    "&requestType=" . $requestType;
            
            $signature = hash_hmac("sha256", $rawHash, $secretKey);
            
            $data = array(
                'partnerCode' => $partnerCode,
                'partnerName' => "Test",
                'storeId' => "MomoTestStore",
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl' => $ipnUrl,
                'lang' => 'en',
                'extraData' => $extraData,
                'requestType' => $requestType,
                'signature' => $signature
            );
            
            Log::info("Momo Request Data: " . json_encode($data));

            $result = $this->execPostRequest($endpoint, json_encode($data));
            
            Log::info("Momo Response: " . $result);

            $jsonResult = json_decode($result, true); 
            
            if (isset($jsonResult['payUrl'])) {
                Log::info("Redirecting to Momo: " . $jsonResult['payUrl']);
                return Redirect::to($jsonResult['payUrl']);
            } else {
                Log::error("Momo Payment Init Failed: " . $result);
                return redirect()->back()->with('error', 'Momo Error: ' . ($jsonResult['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error("Momo Payment Exception: " . $e->getMessage());
            return redirect()->back()->with('error', 'Momo Payment Error: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        try {
            $orderId = session()->get('order_id'); 
            
            if ($request->resultCode == 0) {
                if($orderId) {
                    $transaction = \App\Models\Transaction::where('order_id', $orderId)->first();
                    if($transaction) {
                        $transaction->status = 'approved';
                        $transaction->save();
                    }
                }

                Cart::instance('cart')->destroy();
                session()->forget('checkout');
                session()->forget('coupon');
                session()->forget('discounts');
                session()->forget('order_id');
                
                return redirect()->route('cart.order.confirmation')->with('success', 'Thanh toán thành công qua MoMo!');
            } else {
                Log::error("Momo Payment Failed Callback: " . $request->message);
                
                if($orderId) {
                   $transaction = \App\Models\Transaction::where('order_id', $orderId)->first();
                   if($transaction) {
                       $transaction->status = 'declined';
                       $transaction->save();
                   }
                }
                
                return redirect()->route('cart.index')->with('error', 'Thanh toán thất bại: ' . $request->message);
            }
        } catch (\Exception $e) {
            Log::error("Momo Callback Exception: " . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Callback Error');
        }
    }

    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        
        // Disable SSL verification for local development context
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::error('Momo Curl Error: ' . curl_error($ch));
        }

        curl_close($ch);
        return $result;
    }
}
