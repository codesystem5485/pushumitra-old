<footer class="style2">
        <div class="container">
            <div class="row pt-40 pb-40 justify-content-center">
                <div class="col-lg-3 col-md-12 footer-about">
                    <div class="footer-widget">
                        <div class="footer-icon">
                            <img src="{{asset('front/assets/images/bg/pashumitra-barcode.jpg')}}" alt="">
							
                        </div>
                        <div class="widget-title">
                            <p> QR code For Application</p>
                        </div>
                    
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6">
                    <div class="footer-widget one">
                        <div class="widget-title">
                            <h3>Useful Links</h3>
                        </div>
                        <div class="menu-container">
                            <ul>
                                <!-- <li><a href="about.html">Home</a></li> -->
                                <li><a href="{{url('/about-us')}}">About Us</a></li>
                                <li><a href="{{url('/library')}}">Library</a></li>
                                <li><a href="{{url('/contact-us')}}">Contact</a></li>
                                <li><a href="{{url('/terms-conditions')}}">Terms & Conditions</a></li>
								<li><a href="{{url('/privacy-policy')}}">Privacy Policy</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="footer-items contact footer-widget">
                        <div class="widget-title">
                            <h3>Contacts</h3>
                        </div>
                        <div class="hotline mb-20 mt-25">
                            <div class="hotline-icon">
                                <img src="{{asset('front/assets/images/icon/phone-icon.svg')}}" alt>
								
                            </div>
                            <div class="hotline-info">
                                <h6><a href="tel:+918805700570">+91 880 570 0570</a></h6>
                            </div>
                        </div>
                        <div class="email mb-20">
                            <div class="email-icon">
                                <img src="{{asset('front/assets/images/icon/envelope.svg')}}" alt>
								
                            </div>
                            <div class="email-info">
                                <h6><a href="#"><span>info@pashumitra.com</span></a></h6>
                            </div>
                        </div>
                        <div class="email">
                            <div class="email-icon">
                                <img src="{{asset('front/assets/images/icon/location.svg')}}" alt>
								
                            </div>
							
                            <div class="email-info">
                                <h6 class="mb-1"><a>S-12, Regimental Plaza, Gaikwad Mala, </a></h6>
                                <h6><a>Bitco Point, Nashik Road, Maharashtra, India 422101</a></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget one mb-0">
                        <div class="widget-title">
                            <h3>Install app</h3>
                            <p>Form App Store or Google Play</p>
                        </div>
                        <div class="download-link">
                            <ul>
                                <li><a href="https://play.google.com/store/apps/details?id=com.codesystem.pashumitra&pcampaignid=web_share"><img src="{{asset('front/assets/images/icon/google-play.svg')}}" alt></a></li>
								
                                <li><a href="#"><img src="{{asset('front/assets/images/icon/app-store.svg')}}" alt></a></li>
								
                            </ul>
                        </div>
    
                    </div>
                </div>
            </div>
            <div class="row border-top align-items-center">
                <div class="col-lg-6">
                    <div class="copyright-area">
                        <p>© Copyrights 2023 <a target="_blank" href="https://www.codesystem.co.in/">Code System.</a> All rights reserved. </p>
                    </div>
                </div>
                <div class="col-lg-6 d-flex justify-content-md-end justify-content-center">
                    <div class="social-area">
                        <ul>
                            <li><a target="_blank" href="https://www.facebook.com/PashumitraOfficialPage"><i class="bx bxl-facebook"></i></a></li>
                            <li><a  target="_blank" href="https://www.linkedin.com/in/pashumitraOfficial"><i class="bx bxl-linkedin"></i></a></li>
                            <li><a target="_blank" href="https://www.youtube.com/@PashumitraOfficial"><i class="bx bxl-youtube"></i></a></li>
                            <li><a target="_blank" href="https://www.instagram.com/PashumitraOfficial"><i class="bx bxl-instagram"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{asset('front/assets/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{asset('front/assets/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('front/assets/js/swiper.min.js')}}"></script> 
    <script src="{{asset('front/assets/js/jquery.marquee.min.js')}}"></script>
    <script src="{{asset('front/assets/js/jquery.marquee.min.js')}}"></script>
    <script src="{{asset('front/assets/js/main.js')}}"></script>
    <script>

        // Home Page min slider
        var swiper = new Swiper(".hero2-slider", {
        slidesPerView: 1,
        spaceBetween: 12,
        effect: 'fade',
        loop: true,
        speed: 1500,
        autoplay: {
            delay: 3000,
        },
        pagination: {
            el: ".swiper-pagination121",
            clickable: true,
        },
         });    

        // Home Page Testimonial Slider
        var swiper = new Swiper(".h2-testimonial-slider", {
            spaceBetween: 24,
            slidesPerView: 1,
            loop: true,
            speed: 2000,
            autoplay: {
                delay: 3000,
            },
            navigation: {
                nextEl: ".next-btn-5",
                prevEl: ".prev-btn-5",
            },
            breakpoints: {
                280: {
                    slidesPerView: 1,
                },
                480: {
                    slidesPerView: 1
                },
                768: {
                    slidesPerView: 1
                },
                992: {
                    slidesPerView: 1
                },
                1200: {
                    slidesPerView: 1
                },
                1400: {
                    slidesPerView: 1
                },
                1600: {
                    slidesPerView: 1
                },
            }
        });

        // Home Page Pashu Mitra Slider
        var swiper = new Swiper(".h2-team-slider", {
            spaceBetween: 24,
            slidesPerView: 2,
            loop: true,
            speed: 1500,
            autoplay: {
                delay: 2200,
            },
            pagination: {
                el: ".team-pagination",
                clickable: true,
            },
            breakpoints: {
                280: {
                    slidesPerView: 1,
                },
                480: {
                    slidesPerView: 2
                },
                768: {
                    slidesPerView: 3
                },
                992: {
                    slidesPerView: 3
                },
                1200: {
                    slidesPerView: 4
                },
                1400: {
                    slidesPerView: 4
                },
                1500: {
                    slidesPerView: 5
                },
                1600: {
                    slidesPerView: 5
                },
            }
        });

        // Home Page marquee text Slider
        $(".marquee_text").marquee({
            direction: "left",
            duration: 50000,
            gap: 50,
            delayBeforeStart: 0,
            duplicated: true,
            startVisible: true,
        });
    </script>
</body>

</html>