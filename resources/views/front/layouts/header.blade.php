<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pashumitra</title>
    <link rel="icon" href="{{asset('front/assets/images/sm-logo.svg')}}" type="image/gif" sizes="20x20">
    <link rel="stylesheet" href="{{asset('front/assets/css/animate.css')}}"> 
    <link rel="stylesheet" href="{{asset('front/assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('front/assets/css/boxicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('front/assets/css/bootstrap-icons.css')}}">
    <!-- <link rel="stylesheet" href="assets/css/jquery-ui.css"> -->
    <link rel="stylesheet" href="{{asset('front/assets/css/swiper.css')}}">
    <!-- <link rel="stylesheet" href="assets/css/datepicker.min.css"> -->
    <link rel="stylesheet" href="{{asset('front/assets/css/style.css')}}">
</head>

<body class="home-pages-2">

    <header class="header-area style-2">
        <div class="container">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <div class="header-logo">
                    <a href="{{url('/')}}"><img alt="image" class="img-fluid" src="{{asset('front/assets/images/pashumitra-logo.svg')}}"></a>
                </div>
                <div class="main-menu">
                    <div class="mobile-logo-area d-lg-none">
                        <div class="menu-close-btn">
                            <i class="bi bi-x-lg"></i>
                        </div>
                        <div class="mobile-logo-wrap">
                            <a href="{{url('/')}}"><img alt="image" src="{{asset('front/assets/images/pashumitra-logo.svg')}}"></a>
                        </div>
                        
                    </div>
                    <ul class="menu-list">
                        <li><a href="{{url('/home')}}">Home</a></li>
                        <li><a href="{{url('/about-us')}}">About</a></li>
                        <li><a href="{{url('/library')}}">Library</a></li>
                        <li><a href="{{url('/contact-us')}}">Contact</a></li>
    					<li><a href="{{url('/csr-activities')}}">CSR Activities</a></li>
                        <!--<li><a href="{{url('/government-schemes')}}">Government Schemes</a></li>-->
                        <!--<li><a href="javascript:void(0)">Login</a></li>-->
    
                    </ul>
                </div>
                <div class="nav-right d-lg-none d-block">
                    <div class="sidebar-button mobile-menu-btn ">
                        <i class="bi bi-list"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

