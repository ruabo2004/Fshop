@extends('layouts.app')
@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
        <h2 class="page-title">Address</h2>
        <div class="row">
            <div class="col-lg-3">
                @include('user.account-nav')
            </div>
            <div class="col-lg-9">
                <div class="page-content my-account__edit">
                    <div class="my-account__edit-form">
                        <form name="address_add_form" action="{{route('user.address.store')}}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" placeholder="Full Name" name="name" value="{{old('name')}}" required>
                                        <label for="name">Full Name *</label>
                                        @error('name')<span class="text-danger">{{$message}}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" placeholder="Phone Number" name="phone" value="{{old('phone')}}" required>
                                        <label for="phone">Phone Number *</label>
                                        @error('phone')<span class="text-danger">{{$message}}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" placeholder="Pincode" name="zip" value="{{old('zip')}}" required>
                                        <label for="zip">Pincode *</label>
                                        @error('zip')<span class="text-danger">{{$message}}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" placeholder="State" name="state" value="{{old('state')}}" required>
                                        <label for="state">State *</label>
                                        @error('state')<span class="text-danger">{{$message}}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" placeholder="City" name="city" value="{{old('city')}}" required>
                                        <label for="city">City *</label>
                                        @error('city')<span class="text-danger">{{$message}}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" placeholder="House No, Building" name="address" value="{{old('address')}}" required>
                                        <label for="address">House No, Building, Street *</label>
                                        @error('address')<span class="text-danger">{{$message}}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" placeholder="Road Name, Area, Colony" name="locality" value="{{old('locality')}}" required>
                                        <label for="locality">Road Name, Area, Colony *</label>
                                        @error('locality')<span class="text-danger">{{$message}}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" placeholder="Landmark" name="landmark" value="{{old('landmark')}}" required>
                                        <label for="landmark">Landmark *</label>
                                        @error('landmark')<span class="text-danger">{{$message}}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="my-3">
                                        <button type="submit" class="btn btn-primary">Save Address</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
