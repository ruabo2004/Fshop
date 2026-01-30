@extends('layouts.app')
@section('content')
<style>
    .table> :not(caption)>*>* {
        padding: .625rem 1.5rem .625rem !important;
    }
</style>
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
        <h2 class="page-title">Addresses</h2>
        <div class="row">
            <div class="col-lg-3">
                @include('user.account-nav')
            </div>
            <div class="col-lg-9">
                <div class="page-content my-account__address">
                    @if(Session::has('status'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{Session::get('status')}}
                        </div>
                    @endif

                    @if(Session::has('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{Session::get('error')}}
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-6">
                            <p class="notice">The following addresses will be used on the checkout page by default.</p>
                        </div>
                        <div class="col-6 text-right">
                            <a href="{{route('user.address.add')}}" class="btn btn-sm btn-info">Add New</a>
                        </div>
                    </div>
                    <div class="my-account__address-list row">
                        @forelse ($addresses as $address)
                        <div class="my-account__address-item col-md-6">
                            <div class="my-account__address-item__title">
                                <h5>{{$address->type == 'home' ? 'Home' : ($address->type == 'office' ? 'Office' : 'Other')}} Address</h5>
                                <a href="{{route('user.address.edit',['id'=>$address->id])}}">Edit</a>
                            </div>
                            <div class="my-account__address-item__detail">
                                <p>{{$address->name}}</p>
                                <p>{{$address->address}}</p>
                                <p>{{$address->city}}, {{$address->state}}, {{$address->country}}</p>
                                <p>{{$address->zip}}</p>
                                <br>
                                <p>Mobile: {{$address->phone}}</p>
                            </div>
                        </div>
                        @empty
                        <div class="col-md-12">
                            <p>No addresses found.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
