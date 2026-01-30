@extends('layouts.app')

@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="contact-us container">
        <div class="mw-930">
            <h2 class="page-title">CONTACT US</h2>
        </div>
        <div class="row mb-5 pb-3">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h3 class="mb-4">Get In Touch</h3>
                <p class="mb-4">Have questions or feedback? We'd love to hear from you. Fill out the form below or reach out to us via email or phone.</p>
                <form name="contact-form" action="#" method="POST" class="needs-validation" novalidate>
                    <div class="form-floating mb-3">
                        <input name="name" type="text" class="form-control" id="contactFormName" placeholder="Name" required>
                        <label for="contactFormName">Name *</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input name="email" type="email" class="form-control" id="contactFormEmail" placeholder="Email address" required>
                        <label for="contactFormEmail">Email address *</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea name="message" class="form-control" placeholder="Your Message" id="contactFormMessage" style="height: 150px" required></textarea>
                        <label for="contactFormMessage">Message *</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
            <div class="col-lg-6">
                <h3 class="mb-4">Store Information</h3>
                <div class="mb-4">
                    <h5 class="mb-2">Address</h5>
                    <p class="mb-0">So 1 Giai Phong</p>
                    <p class="mb-0">Ha Noi, Viet Nam</p>
                </div>
                <div class="mb-4">
                    <h5 class="mb-2">Phone</h5>
                    <p class="mb-0">+1 000-000-0000</p>
                </div>
                <div class="mb-4">
                    <h5 class="mb-2">Email</h5>
                    <p class="mb-0">contact@gmail.com</p>
                </div>
                <div class="mb-4">
                    <h5 class="mb-2">Opening Hours</h5>
                    <p class="mb-0">Mon - Fri: 8am - 9pm</p>
                    <p class="mb-0">Sat - Sun: 9am - 10pm</p>
                </div>
            </div>
        </div>
        <div class="google-map mb-5">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.6369796677936!2d105.83921831476239!3d21.00718598601007!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ac71294bf091%3A0xf635817c67425126!2zR2nhuqNpIFBow7FuZywgSMOgIE7hu5lp!5e0!3m2!1sen!2s!4v1684313262615!5m2!1sen!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>
</main>
@endsection
