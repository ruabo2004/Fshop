@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Order Details</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Order Items</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <h5>Order Details</h5>
                </div>
                <a class="tf-button style-1 w208" href="{{route('admin.orders')}}">Back</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                        <tr>
                            <th>Order No</th>
                            <td>{{$order->id}}</td>
                            <th>Mobile</th>
                            <td>{{$order->phone}}</td>
                            <th>Zipcode</th>
                            <td>{{$order->zip}}</td>
                        </tr>
                        <tr>
                            <th>Order Date</th>
                            <td>{{$order->created_at}}</td>
                            <th>Delivered Date</th>
                            <td>{{$order->delivered_date}}</td>
                            <th>Canceled Date</th>
                            <td>{{$order->canceled_date}}</td>
                        </tr>
                        <tr>
                            <th>Order Status</th>
                            <td colspan="5">
                                @if($order->status=='delivered')
                                    <span class="badge badge-success">Delivered</span>
                                @elseif($order->status=='canceled')
                                    <span class="badge badge-danger">Canceled</span>
                                @else
                                    <span class="badge badge-warning">Ordered</span>
                                @endif
                            </td>
                        </tr>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <h5>Ordered Items</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">SKU</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Brand</th>
                            <th class="text-center">Options</th>
                            <th class="text-center">Return Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderItems as $item)
                        <tr>
                            <td class="pname">
                                <div class="image">
                                    <img src="{{asset('uploads/products/thumbnails')}}/{{$item->product->image}}" alt="" class="image">
                                </div>
                                <div class="name">
                                    <a href="{{route('shop.product.details',['product_slug'=>$item->product->slug])}}" target="_blank" class="body-title-2">{{$item->product->name}}</a>
                                </div>
                            </td>
                            <td class="text-center">${{$item->price}}</td>
                            <td class="text-center">{{$item->quantity}}</td>
                            <td class="text-center">{{$item->product->SKU}}</td>
                            <td class="text-center">{{$item->product->category->name}}</td>
                            <td class="text-center">{{$item->product->brand->name}}</td>
                            <td class="text-center">{{$item->options}}</td>
                            <td class="text-center">{{$item->rstatus == 0 ? "No" : "Yes"}}</td>
                            <td class="text-center">
                                <div class="list-icon-function view-icon">
                                    <div class="item eye">
                                        <i class="icon-eye"></i>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <h5>Shipping Address</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Name</th>
                        <td>{{$order->name}}</td>
                        <th>Mobile</th>
                        <td>{{$order->phone}}</td>
                        <th>Locality</th>
                        <td>{{$order->locality}}</td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td>{{$order->address}}</td>
                        <th>City</th>
                        <td>{{$order->city}}</td>
                        <th>Landmark</th>
                        <td>{{$order->landmark}}</td>
                    </tr>
                    <tr>
                        <th>State</th>
                        <td>{{$order->state}}</td>
                        <th>Country</th>
                        <td>{{$order->country}}</td>
                        <th>Zipcode</th>
                        <td>{{$order->zip}}</td>
                    </tr>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <h5>Transactions</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Subtotal</th>
                        <td>${{$order->subtotal}}</td>
                        <th>Tax</th>
                        <td>${{$order->tax}}</td>
                        <th>Discount</th>
                        <td>${{$order->discount}}</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>${{$order->total}}</td>
                        <th>Payment Mode</th>
                        <td>{{$transaction ? $transaction->mode : 'N/A'}}</td>
                        <th>Status</th>
                        <td>
                            @if($transaction)
                                @if($transaction->status=='approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($transaction->status=='declined')
                                    <span class="badge badge-danger">Declined</span>
                                @elseif($transaction->status=='refunded')
                                    <span class="badge badge-secondary">Refunded</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            @else
                                <span class="badge badge-secondary">N/A</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <h5>Update Order Status</h5>
                </div>
            </div>
            <div class="wg-box">
                <form action="{{route('admin.order.status.update')}}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="order_id" value="{{$order->id}}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="select">
                                <select id="order_status" name="order_status">
                                    <option value="ordered" {{$order->status=='ordered' ? 'selected' : ''}}>Ordered</option>
                                    <option value="delivered" {{$order->status=='delivered' ? 'selected' : ''}}>Delivered</option>
                                    <option value="canceled" {{$order->status=='canceled' ? 'selected' : ''}}>Canceled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            @if($order->status != 'delivered' && $order->status != 'canceled')
                                <button type="submit" class="btn btn-primary tf-button w208">Update Status</button>
                            @else
                                <button type="button" class="btn btn-secondary tf-button w208" disabled>Status Locked</button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
