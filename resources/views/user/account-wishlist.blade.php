@extends('layouts.app')
@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
        <h2 class="page-title">Wishlist</h2>
        <div class="row">
            <div class="col-lg-3">
                @include('user.account-nav')
            </div>
            <div class="col-lg-9">
                <div class="page-content my-account__wishlist">
                    @if($items->count() > 0)
                    <div class="shopping-cart">
                        <div class="cart-table__wrapper">
                            <table class="cart-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th></th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                    <tr>
                                        <td>
                                            <div class="shopping-cart__product-item">
                                                <img loading="lazy" src="{{ asset('uploads/products/thumbnails') }}/{{ $item->model->image }}" width="120" height="120" alt="{{ $item->name }}" />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="shopping-cart__product-item__detail">
                                                <h4>{{ $item->name }}</h4>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="shopping-cart__product-price">${{ $item->price }}</span>
                                        </td>
                                        <td>
                                            <div class="list-icon-function">
                                                <form action="{{route('wishlist.item.remove',['rowId'=>$item->rowId])}}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:void(0)" class="remove-cart" onclick="this.closest('form').submit()">Remove</a>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                        <div class="row">
                            <div class="col-md-12">
                                <p>No item found in your wishlist</p>
                                <a href="{{route('shop.index')}}" class="btn btn-info">Wishlist Now</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
