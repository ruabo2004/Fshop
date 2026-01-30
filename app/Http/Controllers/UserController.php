<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }

    public function orders()
    {
        $orders = Order::where('user_id',Auth::user()->id)->orderBy('created_at','DESC')->paginate(10);
        return view('user.orders',compact('orders'));
    }

    public function order_details($order_id)
    {
        $order = Order::where('user_id',Auth::user()->id)->where('id',$order_id)->first();
        if($order) {
            $orderItems = OrderItem::where('order_id',$order_id)->orderBy('id')->get();
            $transaction = Transaction::where('order_id',$order_id)->first();
            return view('user.order_details',compact('order','orderItems','transaction'));
        }
        return redirect()->route('login');
    }
    public function addresses()
    {
        $addresses = Auth::user()->addresses;
        return view('user.account-address', compact('addresses'));
    }

    public function account_details()
    {
        return view('user.account-details');
    }

    public function wishlist()
    {
        $items = \Surfsidemedia\Shoppingcart\Facades\Cart::instance('wishlist')->content();
        return view('user.account-wishlist', compact('items'));
    }

    public function account_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required|numeric',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;

        if($request->new_password)
        {
            $request->validate([
                'old_password' => 'required',
                'new_password' => 'required|confirmed', 
            ]);

            if(!\Illuminate\Support\Facades\Hash::check($request->old_password, $user->password))
            {
                return back()->with("error", "Old Password Doesn't match!");
            }
            $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        }

        $user->save();
        return back()->with('status',"Account Details Updated Successfully!");
    }

    public function add_address()
    {
        return view('user.account-address-add');
    }

    public function store_address(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'phone' => 'required|numeric|digits:10',
            'zip' => 'required|numeric|digits:6',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'locality' => 'required',
            'landmark' => 'required',
        ]);

        $address = new \App\Models\Address();
        $address->user_id = Auth::user()->id;
        $address->name = $request->name;
        $address->phone = $request->phone;
        $address->zip = $request->zip;
        $address->state = $request->state;
        $address->city = $request->city;
        $address->address = $request->address;
        $address->locality = $request->locality;
        $address->landmark = $request->landmark;
        $address->country = 'India'; // Default or from form
        $address->isdefault = false; // Default logic
        $address->save();

        return redirect()->route('user.addresses')->with('status','Address has been added successfully!');
    }

    public function edit_address($id)
    {
        $address = \App\Models\Address::where('user_id',Auth::user()->id)->where('id',$id)->first();
        if($address) {
            return view('user.account-address-edit',compact('address'));
        }
        return redirect()->route('user.addresses');
    }

    public function update_address(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100',
            'phone' => 'required|numeric|digits:10',
            'zip' => 'required|numeric|digits:6',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'locality' => 'required',
            'landmark' => 'required',
        ]);

        $address = \App\Models\Address::where('user_id',Auth::user()->id)->where('id',$id)->first();
        if($address) {
            $address->name = $request->name;
            $address->phone = $request->phone;
            $address->zip = $request->zip;
            $address->state = $request->state;
            $address->city = $request->city;
            $address->address = $request->address;
            $address->locality = $request->locality;
            $address->landmark = $request->landmark;
            $address->save();
            return redirect()->route('user.addresses')->with('status','Address has been updated successfully!');
        }
        return redirect()->route('user.addresses');
    }
}
