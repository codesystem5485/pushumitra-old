@extends('front.master')
@section('content')
<div class="hero2">
        
        <div class="swiper hero2-slider">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="{{asset('front/assets/images/advertising-banner/atmanirbhar-bharat.jpg')}}" class="img-fluid w-100" alt="Atmanirbhar Bharat">
                </div>
                <div class="swiper-slide">
                    <img src="{{asset('front/assets/images/advertising-banner/advertise-banner.jpg')}}" class="img-fluid w-100" alt="Advertise">
                </div>
            </div>
        </div>
       <div class="left-sidebar">
            <div class="swiper-pagination121"></div>
        </div>
    </div>

    <!--<div class="hero3 mb-90">
        <div class="background-text">
            <h2 class="marquee_text"><img src="{{asset('front/assets/images/icon/marque-foot.svg')}}" alt="image"><span>Get exciting
                    Discount</span> Up To 50%<img src="{{asset('front/assets/images/icon/marque-foot.svg')}}" alt="image"><span>On Your
                    first buying</span> Up To 50%</h2>
        </div>
    </div>-->
    
    <!--<div class="hero2">-->
    <!--    <div class="container">-->
    <!--        <div class="row">-->
    <!--            <div class="col-lg-12">-->
    <!--                <img src="{{asset('front/assets/images/advertising-banner/atmanirbhar-bharat.jpg')}}" class="img-fluid w-100" alt="Atmanirbhar Bharat">-->
    <!--            </div>-->
                <!--<div class="col-lg-6"></div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->

    <div class="h2-services-area">
        <div class="services-btm pt-120 mb-120">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="services-img">
                            <div class="services-img-bg">
                                <img src="{{asset('front/assets/images/icon/h2-services-img-bg.svg')}}" alt>
                            </div>
                            <img class="img-fluid" src="{{asset('front/assets/images/bg/h2-contact-img.png')}}" alt> 
                        </div>
                    </div>
					
                    <div class="col-lg-7">
                        <div class="services-content">
                            <img src="{{asset('front/assets/images/icon/section-sl-no.svg')}}" alt>
                            <h2>we are providing Pashumitra service</h2>
                            <p>Pashumitra app developed for Farmers, Livestock Owner, Pet Owner, Dairy Farms, Sheep & Goat Farms, Pig Farms, Stud Farms and Veterinary and Farm Consultant.
                            </p>
                            <p> Pashumitra app gives SMS facility to remind dates and to do work.
                            </p>
                            <p>Better Management is very important in Livestock Farming. Many times Farmers, Livestock Owner, Pet Owner, Dairy Farms, Sheep & Goat Farms, Pig Farms, Stud Farms and Veterinary and Farm Consultant forgot the dates of Inspection, Vaccination, Health Check-up. For that Pashumitra Android app is useful and this will help us to remind on the scheduled date.</p>
                            <div class="author-area">
                                <div class="author-quat">
                                <p><b>Pashumitra app helps to prevents unnecessary loss by doing work on Scheduled Date..</b></p></div>
                                <!-- <ul>
                                    <li>In Cow/Shebuff after A.I./N.S., to keep observation on 21st/90/210/210/310th days.</li>
                                    <li>2nd Deworming after 15/30/180th days.</li>
                                </ul> -->
                                <a class="primary-btn2 mt-10" href="{{url('/about-us')}}">More About</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


   <!-- <div class="h2-team-area mb-120">
        <div class="vector1">
            <img src="{{asset('front/assets/images/bg/team/team-vector-1.png')}}" alt>
			
			
        </div>
        <div class="container-fluid">
            <div class="row justify-content-center mb-60">
                <div class="col-lg-11">
                    <div class="section-title2 text-center">
                        <h2>Top Rated Pashumitra</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-11 justify-content-center">
                    <div class="swiper h2-team-slider">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="team-card">
                                    <div class="team-card-inner">
                                        <div class="card-style-1">
                                            <div class="team-img">
                                                <img class="img-fluid" src="{{asset('front/assets/images/bg/team/h2-team-1.png')}}" alt>
												
                                            </div>
                                            <div class="team-content">
                                                <h3>Kash Preston</h3>
                                                <span>Co-Founder</span>
                                            </div>
                                        </div>
                                        <div class="card-style-2">
                                            <div class="team-content">
                                                <h3>Kash Preston</h3>
                                                <span>Co-Founder</span>
                                            </div>
                                            <div class="team-img">
                                                <img class="img-fluid" src="{{asset('front/assets/images/bg/team/h2-team-1.png')}}" alt>
                                                <div class="social-area">
                                                    <div class="share-icon">
                                                        <i class="bi bi-share-fill"></i>
                                                    </div>
                                                    <ul class="social-icons">
                                                        <li><a href="https://www.facebook.com/"><i
                                                                    class="bx bxl-facebook"></i></a></li>
                                                        <li><a href="https://twitter.com/"><i
                                                                    class="bx bxl-twitter"></i></a></li>
                                                        <li><a href="https://www.pinterest.com/"><i
                                                                    class="bx bxl-pinterest-alt"></i></a></li>
                                                        <li><a href="https://www.instagram.com/"><i
                                                                    class="bx bxl-instagram"></i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="team-card">
                                    <div class="team-card-inner">
                                        <div class="card-style-1">
                                            <div class="team-img">
                                                <img class="img-fluid" src="{{asset('front/assets/images/bg/team/h2-team-2.png')}}" alt>
                                            </div>
                                            <div class="team-content">
                                                <h3>Scarlett Emily</h3>
                                                <span>Kennel Assistant</span>
                                            </div>
                                        </div>
                                        <div class="card-style-2">
                                            <div class="team-content">
                                                <h3>Scarlett Emily</h3>
                                                <span>Kennel Assistant</span>
                                            </div>
                                            <div class="team-img">
                                                <img class="img-fluid" src="{{asset('front/assets/images/bg/team/h2-team-2.png')}}" alt>
                                                <div class="social-area">
                                                    <div class="share-icon">
                                                        <i class="bi bi-share-fill"></i>
                                                    </div>
                                                    <ul class="social-icons">
                                                        <li><a href="https://www.facebook.com/"><i
                                                                    class="bx bxl-facebook"></i></a></li>
                                                        <li><a href="https://twitter.com/"><i
                                                                    class="bx bxl-twitter"></i></a></li>
                                                        <li><a href="https://www.pinterest.com/"><i
                                                                    class="bx bxl-pinterest-alt"></i></a></li>
                                                        <li><a href="https://www.instagram.com/"><i
                                                                    class="bx bxl-instagram"></i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="team-card">
                                    <div class="team-card-inner">
                                        <div class="card-style-1">
                                            <div class="team-img">
                                                <img class="img-fluid" src="{{asset('front/assets/images/bg/team/h2-team-3.png')}}" alt>
                                            </div>
                                            <div class="team-content">
                                                <h3>Jackson Mateo</h3>
                                                <span>Veterinary Assistant</span>
                                            </div>
                                        </div>
                                        <div class="card-style-2">
                                            <div class="team-content">
                                                <h3>Jackson Mateo</h3>
                                                <span>Veterinary Assistant</span>
                                            </div>
                                            <div class="team-img">
                                                <img class="img-fluid" src="{{asset('front/assets/images/bg/team/h2-team-3.png')}}" alt>
                                                <div class="social-area">
                                                    <div class="share-icon">
                                                        <i class="bi bi-share-fill"></i>
                                                    </div>
                                                    <ul class="social-icons">
                                                        <li><a href="https://www.facebook.com/"><i
                                                                    class="bx bxl-facebook"></i></a></li>
                                                        <li><a href="https://twitter.com/"><i
                                                                    class="bx bxl-twitter"></i></a></li>
                                                        <li><a href="https://www.pinterest.com/"><i
                                                                    class="bx bxl-pinterest-alt"></i></a></li>
                                                        <li><a href="https://www.instagram.com/"><i
                                                                    class="bx bxl-instagram"></i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="team-card">
                                    <div class="team-card-inner">
                                        <div class="card-style-1">
                                            <div class="team-img">
                                                <img class="img-fluid" src="{{asset('front/assets/images/bg/team/h2-team-4.png')}}" alt>
                                            </div>
                                            <div class="team-content">
                                                <h3>Madison Ellie</h3>
                                                <span>Groomer Manager</span>
                                            </div>
                                        </div>
                                        <div class="card-style-2">
                                            <div class="team-content">
                                                <h3>Madison Ellie</h3>
                                                <span>Groomer Manager</span>
                                            </div>
                                            <div class="team-img">
                                                <img class="img-fluid" src="{{asset('front/assets/images/bg/team/h2-team-4.png')}}" alt>
                                                <div class="social-area">
                                                    <div class="share-icon">
                                                        <i class="bi bi-share-fill"></i>
                                                    </div>
                                                    <ul class="social-icons">
                                                        <li><a href="https://www.facebook.com/"><i
                                                                    class="bx bxl-facebook"></i></a></li>
                                                        <li><a href="https://twitter.com/"><i
                                                                    class="bx bxl-twitter"></i></a></li>
                                                        <li><a href="https://www.pinterest.com/"><i
                                                                    class="bx bxl-pinterest-alt"></i></a></li>
                                                        <li><a href="https://www.instagram.com/"><i
                                                                    class="bx bxl-instagram"></i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="team-card">
                                    <div class="team-card-inner">
                                        <div class="card-style-1">
                                            <div class="team-img">
                                                <img class="img-fluid" src="{{asset('front/assets/images/bg/team/h2-team-5.png')}}" alt>
                                            </div>
                                            <div class="team-content">
                                                <h3>Gorjona Hiller</h3>
                                                <span>Daycare Manager</span>
                                            </div>
                                        </div>
                                        <div class="card-style-2">
                                            <div class="team-content">
                                                <h3>Gorjona Hiller</h3>
                                                <span>Daycare Manager</span>
                                            </div>
                                            <div class="team-img">
                                                <img class="img-fluid" src="{{asset('front/assets/images/bg/team/h2-team-5.png')}}" alt>
                                                <div class="social-area">
                                                    <div class="share-icon">
                                                        <i class="bi bi-share-fill"></i>
                                                    </div>
                                                    <ul class="social-icons">
                                                        <li><a href="https://www.facebook.com/"><i
                                                                    class="bx bxl-facebook"></i></a></li>
                                                        <li><a href="https://twitter.com/"><i
                                                                    class="bx bxl-twitter"></i></a></li>
                                                        <li><a href="https://www.pinterest.com/"><i
                                                                    class="bx bxl-pinterest-alt"></i></a></li>
                                                        <li><a href="https://www.instagram.com/"><i
                                                                    class="bx bxl-instagram"></i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pagination-area">
                            <div class="team-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="h1-blog-area mb-120">
        <div class="container">
            <div class="row mb-50">
                <div class="col-lg-12 d-flex justify-content-center">
                    <div class="section-title1 text-center">
                        <span>Latest Events</span>
                        <h2>valueable words from Customers</h2>
                    </div>
                </div>
            </div>
            <div class="row g-lg-4 gy-5 justify-content-center">
                <div class="col-lg-4 col-md-6 col-sm-10">
                    <div class="h1-blog-card">
                        <div class="blog-img">
                            <img class="img-fluid" src="{{asset('front/assets/images/blog/blog1.png')}}" alt>
                            <div class="category">
                                <a href="blog-grid.html">Dog bording</a>
                            </div>
                        </div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <a href="blog-grid.html">August 13, 2022</a>
                            </div>
                            <h4><a href="blog-details.html">lobortis pharetra In necat boi risuse osae that one far This
                                    fox.</a></h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-10">
                    <div class="h1-blog-card">
                        <div class="blog-img">
                            <img class="img-fluid" src="{{asset('front/assets/images/blog/blog2.png')}}" alt>
                            <div class="category">
                                <a href="blog-grid.html">Day Care</a>
                            </div>
                        </div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <a href="blog-grid.html">August 10, 2022</a>
                            </div>
                            <h4><a href="blog-details.html">Donec venenatis ex id nibh iaculisoni Clonal interdum
                                    Curabitur.</a></h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-10">
                    <div class="h1-blog-card">
                        <div class="blog-img">
                            <img class="img-fluid" src="{{asset('front/assets/images/blog/blog3.png')}}" alt>
                            <div class="category">
                                <a href="blog-grid.html">Grooming</a>
                            </div>
                        </div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <a href="blog-grid.html">August 05, 2022</a>
                            </div>
                            <h4><a href="blog-details.html">Orci varius natoque penatibus etmal dis parturient
                                    montes.</a></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>-->

    <div class="h2-testimonial-area mb-90">
        <div class="container">
            <div class="row mb-40">
                <div class="col-lg-12">
                    <div class="section-title2 text-center">
                        <h2>What Our Customer Say</h2>
                    </div>
                </div>
            </div>
                <div class="d-flex mb-10">
                    <div class="slider-btn prev-btn-5 mr-10">
                        <i class="bi bi-arrow-left"></i>
                    </div>
                    <div class="swiper-scrollbar"></div>
                    <div class="slider-btn next-btn-5">
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            <div class="mb-50">
                <div class="swiper h2-testimonial-slider">
                    <div class="swiper-wrapper">
					@foreach($testimonials as $testimonial)
                        <div class="swiper-slide">
                            <div class="testimonial-wrap">
                                <div class="testimonial-content text-center">
                                    <div class="quat-icon">
                                        <img class="left-quat" src="{{asset('front/assets/images/icon/left-quat.svg')}}" alt>
                                        <img class="right-quat" src="{{asset('front/assets/images/icon/right-quat.svg')}}" alt>
										
                                    </div>
                                    <div class="foot-vector">
                                        <img class="testimonial-vec-left"
                                            src="{{asset('front/assets/images/icon/h2-testimonial-vec-left.svg')}}" alt>
											
											
                                        <img class="testimonial-vec-right"
                                            src="{{asset('front/assets/images/icon/h2-testimonial-vec-right.svg')}}" alt>
											
                                    </div>
                                    <div class="author-name-deg">
                                        <h3>{{$testimonial->testimonial_name}}</h3>
                                        <span>{{$testimonial->testimonial_designation}}</span>
                                    </div>
									@php
									$testimonial_message = $testimonial->testimonial_message;
									
									@endphp
                                    <p>{{$testimonial_message}}.</p>
                                    <div class="review">
                                        <ul>
                                            <li><i class="bi bi-star-fill"></i></li>
                                            <li><i class="bi bi-star-fill"></i></li>
                                            <li><i class="bi bi-star-fill"></i></li>
                                            <li><i class="bi bi-star-fill"></i></li>
                                            <li><i class="bi bi-star-fill"></i></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="testimonial-img">
								@if($testimonial->testimonial_photo!='')
                                    <img  src="{{ url("/upload/testimonials/")}}/{{$testimonial->testimonial_photo}}" alt>
								@else
									<img src="{{asset('front/assets/images/user_default.png')}}" alt>
								@endif	
                                </div>
                            </div>
                        </div>
                    @endforeach   
					   <!--<div class="swiper-slide">
                            <div class="testimonial-wrap">
                                <div class="testimonial-content text-center">
                                    <div class="quat-icon">
                                        <img class="left-quat" src="{{asset('front/assets/images/icon/left-quat.svg')}}" alt>
										
                                        <img class="right-quat" src="{{asset('front/assets/images/icon/right-quat.svg')}}" alt>
                                    </div>
                                    <div class="foot-vector">
                                        <img class="testimonial-vec-left"
                                            src="{{asset('front/assets/images/icon/h2-testimonial-vec-left.svg')}}" alt>
											
											
                                        <img class="testimonial-vec-right"
                                            src="{{asset('front/assets/images/icon/h2-testimonial-vec-right.svg')}}" alt>
                                    </div>
                                    <div class="author-name-deg">
                                        <h3>Anthony Dylan</h3>
                                        <span>Customer</span>
                                    </div>
                                    <p>Pellentesque maximus augue orci, quisdal andosp
                                        Pellentesque maximus augue orci, quisoki congue
                                        Nullam egestas, nisi id mollis elementum.</p>
                                    <div class="review">
                                        <ul>
                                            <li><i class="bi bi-star-fill"></i></li>
                                            <li><i class="bi bi-star-fill"></i></li>
                                            <li><i class="bi bi-star-fill"></i></li>
                                            <li><i class="bi bi-star-fill"></i></li>
                                            <li><i class="bi bi-star-fill"></i></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="testimonial-img">
                                    <img src="{{asset('front/assets/images/bg/h2-testi-2.png')}}" alt>
									
                                </div>
                            </div>
                        </div>
                    -->
					</div>
                </div>
            </div>
          
        </div>
    </div>

	@endsection