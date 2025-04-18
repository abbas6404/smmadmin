@extends('frontend.layouts.master')

@section('title', 'Welcome to SMM Panel')

@section('content')
<div class="hero bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold mb-4">Boost Your Social Media Presence</h1>
                <p class="lead mb-4">Get high-quality social media services at competitive prices. Instant delivery and 24/7 support.</p>
                <a href="{{ route('login') }}" class="btn btn-light btn-lg">Get Started</a>
            </div>
            <div class="col-md-6 text-center">
                <img src="{{ asset('assets/images/hero-illustration.svg') }}" alt="Social Media Marketing" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<div class="features py-5">
    <div class="container">
        <h2 class="text-center mb-5">Why Choose Us?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-bolt fa-3x text-primary mb-3"></i>
                        <h4>Instant Delivery</h4>
                        <p class="text-muted">Get your orders delivered instantly after payment confirmation.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                        <h4>24/7 Support</h4>
                        <p class="text-muted">Our dedicated support team is always here to help you.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                        <h4>Secure Payments</h4>
                        <p class="text-muted">Your payments are secured with industry-standard encryption.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="services bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-5">Our Services</h2>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fab fa-instagram fa-2x text-primary mb-3"></i>
                        <h5>Instagram</h5>
                        <p class="small text-muted">Followers, Likes, Views</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fab fa-facebook fa-2x text-primary mb-3"></i>
                        <h5>Facebook</h5>
                        <p class="small text-muted">Likes, Followers, Views</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fab fa-youtube fa-2x text-primary mb-3"></i>
                        <h5>YouTube</h5>
                        <p class="small text-muted">Views, Subscribers, Likes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fab fa-tiktok fa-2x text-primary mb-3"></i>
                        <h5>TikTok</h5>
                        <p class="small text-muted">Followers, Likes, Views</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="cta py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h2 class="mb-4">Ready to Get Started?</h2>
                <p class="lead mb-4">Join thousands of satisfied customers who trust our services.</p>
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Create Account</a>
            </div>
        </div>
    </div>
</div>
@endsection 