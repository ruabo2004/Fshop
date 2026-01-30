@extends('layouts.app')

@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="about-us container">
        <div class="mw-930">
            <h2 class="page-title">ABOUT FSHOP</h2>
        </div>
        <div class="about-us__content pb-5 mb-5">
            <p class="mb-5">
                <img loading="lazy" class="w-100 h-auto d-block" src="{{ asset('assets/images/about/about-1.jpg') }}" width="1410" height="550" alt="About Fshop">
            </p>
            <div class="mw-930">
                <h3 class="mb-4">OUR STORY</h3>
                <p class="fs-6 fw-medium mb-4">Fshop was founded with a simple mission: to make high-quality fashion accessible to everyone. We believe that style should not break the bank, and that every individual deserves to feel confident and comfortable in what they wear.</p>
                <p class="mb-4">Starting as a small boutique in Hanoi, we have grown into a leading online fashion destination, serving customers across the country. Our team is passionate about curating the latest trends and verifying the quality of every item we sell.</p>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5 class="mb-3">Our Mission</h5>
                        <p class="mb-3">To empower individuals through fashion, providing diverse styles that cater to all tastes and occasions.</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Our Vision</h5>
                        <p class="mb-3">To be the most trusted and loved fashion retailer in Vietnam, known for our exceptional customer service and innovative shopping experience.</p>
                    </div>
                </div>
            </div>
            <div class="mw-930 d-lg-flex align-items-lg-center">
                <div class="image-wrapper col-lg-6">
                    <img class="h-auto" loading="lazy" src="{{ asset('assets/images/about/about-2.jpg') }}" width="450" height="500" alt="Our Team">
                </div>
                <div class="content-wrapper col-lg-6 px-lg-4">
                    <h5 class="mb-3">The Company</h5>
                    <p>We work tirelessly to ensure that your shopping experience is seamless, from browsing our collection to receiving your package at your doorstep.</p>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
