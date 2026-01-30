<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart',compact('items'));
    }

    public function add_to_cart(Request $request)
    {
        Cart::instance('cart')->add($request->id,$request->name,$request->quantity,$request->price)->associate('App\Models\Product');
        return redirect()->back();        
    } 

    public function increase_cart_quantity($rowId)
    {
        \Illuminate\Support\Facades\Log::info("Increase Cart Quantity Request for RowID: " . $rowId);
        \Illuminate\Support\Facades\Log::info("Wants JSON: " . request()->wantsJson());
        \Illuminate\Support\Facades\Log::info("Is AJAX: " . request()->ajax());

        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId,$qty);
        
        if(request()->wantsJson() || request()->ajax()) {
            $product = Cart::instance('cart')->get($rowId);
            return response()->json([
                'status' => 'success',
                'rowId' => $rowId,
                'qty' => $product->qty,
                'item_subtotal' => number_format($product->price * $product->qty, 2, '.', ','),
                'cart_subtotal' => Cart::instance('cart')->subtotal(),
                'cart_tax' => Cart::instance('cart')->tax(),
                'cart_total' => Cart::instance('cart')->total()
            ]);
        }
        
        \Illuminate\Support\Facades\Log::info("Returning Redirect Back");
        return redirect()->back();
    }

    public function decrease_cart_quantity($rowId)
    {
        \Illuminate\Support\Facades\Log::info("Decrease Cart Quantity Request for RowID: " . $rowId);
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId,$qty);
        
        if(request()->wantsJson() || request()->ajax()) {
             $product = Cart::instance('cart')->get($rowId);
             return response()->json([
                'status' => 'success',
                'rowId' => $rowId,
                'qty' => $product->qty,
                'item_subtotal' => number_format($product->price * $product->qty, 2, '.', ','),
                'cart_subtotal' => Cart::instance('cart')->subtotal(),
                'cart_tax' => Cart::instance('cart')->tax(),
                'cart_total' => Cart::instance('cart')->total()
            ]);
        }

        return redirect()->back();
    }

    public function remove_item($rowId)
    {
        \Illuminate\Support\Facades\Log::info("Remove Item Request for RowID: " . $rowId);
        
        Cart::instance('cart')->remove($rowId);
        
        if(request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Item removed successfully',
                'cart_subtotal' => Cart::instance('cart')->subtotal(),
                'cart_tax' => Cart::instance('cart')->tax(),
                'cart_total' => Cart::instance('cart')->total(),
                'cart_count' => Cart::instance('cart')->content()->count()
            ]);
        }

        return redirect()->back();
    }



    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $address = Address::where('user_id', Auth::user()->id)->where('isdefault', 1)->first();
        return view('checkout', compact('address'));
    }

    public function place_an_order(Request $request)
    {
        \Illuminate\Support\Facades\Log::info("DEBUG: place_an_order hit. Method: " . $request->method());
        \Illuminate\Support\Facades\Log::info("DEBUG: Request all: ", $request->all());
        
        try {
            $user_id = Auth::user()->id;
            $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();
            
            if(!$address) {
                $request->validate([
                    'name' => 'required|max:100',
                    'phone' => 'required|numeric|digits:10',
                    'zip' => 'required|numeric|digits_between:4,10',
                    'state' => 'required',
                    'city' => 'required',
                    'address' => 'required',
                    'locality' => 'required',
                    'landmark' => 'required',
                ]);

                $address = new Address();
                $address->name = $request->name;
                $address->phone = $request->phone;
                $address->zip = $request->zip;
                $address->state = $request->state;
                $address->city = $request->city;
                $address->address = $request->address;
                $address->locality = $request->locality;
                $address->landmark = $request->landmark;
                $address->country = 'Vietnam';
                $address->user_id = $user_id;
                $address->isdefault = true;
                $address->save();
            }

            \Illuminate\Support\Facades\Log::info("Validation Passed/Skipped. Proceeding to create order.");

            $this->setAmountForCheckout();

            $order = new Order();
            $order->user_id = $user_id;
            $order->subtotal = session()->get('checkout')['subtotal'];
            $order->tax = session()->get('checkout')['tax'];
            $order->total = session()->get('checkout')['total'];
            $order->name = $address->name;
            $order->phone = $address->phone;
            $order->locality = $address->locality;
            $order->address = $address->address;
            $order->city = $address->city;
            $order->state = $address->state;
            $order->country = $address->country;
            $order->landmark = $address->landmark;
            $order->zip = $address->zip;
            $order->save();

            foreach(Cart::instance('cart')->content() as $item)
            {
                $orderItem = new OrderItem();
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->price = $item->price;
                $orderItem->quantity = $item->qty;
                $orderItem->save();
            }

            if($request->checkout_payment_method == 'momo')
            {
                $transaction = new Transaction();
                $transaction->user_id = $user_id;
                $transaction->order_id = $order->id;
                $transaction->mode = 'momo';
                $transaction->status = 'pending';
                $transaction->save();

                session()->put('order_id', $order->id); 
                return redirect()->route('momo.payment');
            }
            else 
            {
                $transaction = new Transaction();
                $transaction->user_id = $user_id;
                $transaction->order_id = $order->id;
                $transaction->mode = $request->checkout_payment_method;
                $transaction->status = "pending";
                $transaction->save();
            }

            Cart::instance('cart')->destroy();
            session()->forget('checkout');
            session()->forget('coupon');
            session()->forget('discounts');
            session()->put('order_id',$order->id);
            
            return redirect()->route('cart.order.confirmation');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Order Placement Failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to place order. Please try again.');
        }
    }

    public function setAmountForCheckout()
    {
        if(!Cart::instance('cart')->count() > 0)
        {
            session()->forget('checkout');
            return;
        }

        if(session()->has('coupon'))
        {
            session()->put('checkout',[
                'discount' => session()->get('coupon')['discount'],
                'subtotal' => session()->get('coupon')['subtotal'],
                'tax' => session()->get('coupon')['tax'],
                'total' => session()->get('coupon')['total']
            ]);
        }
        else
        {
            session()->put('checkout',[
                'discount' => 0,
                'subtotal' => Cart::instance('cart')->subtotal(),
                'tax' => Cart::instance('cart')->tax(),
                'total' => Cart::instance('cart')->total()
            ]);
        }
    }

    public function order_confirmation()
    {
        if(session()->has('order_id'))
        {
            $order = Order::find(session()->get('order_id'));
            return view('order_confirmation',compact('order'));
        }
        return redirect()->route('cart.index');
    }
}
