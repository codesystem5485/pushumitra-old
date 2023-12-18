@extends('front.master')
@section('content') 
<div class="inner-page-banner">
	<div class="container">
		<div class="banner-content d-flex align-items-center justify-content-between">
			<h1>Contact Us</h1>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{url('/home')}}">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page">Contact Us</li>
				</ol>
			</nav>
		</div>
	</div>
</div>
<div class="contact-pages pt-70 mb-120">
        <div class="container">
            <div class="row align-items-center g-lg-4 gy-5">
                <div class="col-lg-5">
                    <div class="contact-left">
                        <div class="hotline mb-80">
                            <h3>Call Us Now</h3>
                            <div class="icon">
                                <img src="{{asset('front/assets/images/icon/phone-icon4.svg')}}" alt>
								
                            </div>
                            <div class="info">
                                <h6><a href="tel:+919702830971">+91 970 283 0971</a></h6>
                                <!--<h6><a href="tel:+919702830971">+91 970 283 0971</a></h6>-->
                            </div>
                        </div>
                        <div class="location">
                            <h3>Call Us Now</h3>
                            <div class="icon">
                                <img src="{{asset('front/assets/images/icon/location4.svg')}}" alt>
								
								
                            </div>
                            <div class="info">
                                <h6><a href="#">S-12, Regimental Plaza, Gaikwad Mala, Bitco Point, Nashik Road, Maharashtra, India 422101</a></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="contact-form">
                        <h2>Have Any Questions</h2>
                        <form>
                            <div class="row">
                                <div class="col-lg-12 mb-40">
                                    <div class="form-inner">
                                        <input type="text" placeholder="Enter your name">
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-40">
                                    <div class="form-inner">
                                        <input type="text" placeholder="Enter your email">
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-40">
                                    <div class="form-inner">
                                        <input type="text" placeholder="Subject">
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-40">
                                    <div class="form-inner">
                                        <textarea placeholder="Your message"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-inner">
                                        <button class="primary-btn1">Send Message <i
                                                class="bi bi-arrow-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="location-map">
        <div class="vector">
            <img src="assets/images/bg/map-vector.png" alt>
        </div>
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d937.5799221020912!2d73.83652836956783!3d19.953053459441993!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdd958b6fd7e5e9%3A0x4f982ea2f76d676b!2sRegimental!5e0!3m2!1sen!2sin!4v1697357904074!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
            style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>
@endsection