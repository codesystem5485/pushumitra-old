<!doctype html>
<html lang="en">
   <head>
      <title>:: {{config('app.name')}} :: </title>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
      <meta name="description" content="{{__('general.pashumitra')}}">
      <meta name="author" content="{{__('general.pashumitra')}}">
      <link rel="icon" href="favicon.ico" type="image/x-icon">
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/bootstrap/css/bootstrap.min.css')}}">
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/font-awesome/css/font-awesome.min.css')}}">
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/jvectormap/jquery-jvectormap-2.0.3.min.css')}}" />
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/morrisjs/morris.min.css')}}" />
      <link rel="stylesheet" href="{{asset('admin/assets/css/main.css')}}">
      <link rel="stylesheet" href="{{asset('admin/assets/css/color_skins.css')}}">
      <link rel="stylesheet" href="{{asset('admin/assets/css/custom.css')}}"> 
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <link rel="icon" href="{{asset('admin/assets/images/logo.png')}}" type="image/icon type">
      <script>
            var webUrl = '{{url("/")}}';
      </script>
      @yield('css')
   </head>
   <body class="theme-cyan">
      <div class="page-loader-wrapper">
         <div class="loader">
            <div class="m-t-30"><img src="https://www.wrraptheme.com/templates/lucid/html/assets/images/logo-icon.svg" width="48" height="48" alt="Lucid"></div>
            <p>{{__('general.please_wait')}}</p>
         </div>
      </div>
      <div id="wrapper">
         <nav class="navbar navbar-fixed-top">
            <div class="container-fluid">
               <div class="navbar-btn">
                  <button type="button" class="btn-toggle-offcanvas"><i class="lnr lnr-menu fa fa-bars"></i></button>
               </div>  
               <div class="navbar-brand"> 
                  <a href="{{url('/dashboard')}}"><img src="{{asset('admin/assets/images/logo.jpg')}}" alt="Lucid Logo" class="img-responsive logo"><strong class="logo-text"> &nbsp;{{ __('general.pashumitra') }}</strong></a>
               </div>
               <div class="navbar-right">
                  <div id="navbar-menu">
                     <ul class="nav navbar-nav"> 
                        <li><a href="{{route('profile')}}" class="icon-menu"><i class="icon-user"></i></a></li>
                        <li>
                           <a href="{{ route('auth.logout') }}"
                           onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();" class="icon-menu"><i class="icon-login"></i></a>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
         </nav>
         <div id="left-sidebar" class="sidebar">
            <div class="sidebar-scroll">
               <div class="user-account">
                  <img src="{{asset('admin/assets/images/user.png')}}" class="rounded-circle user-photo" alt="User Profile Picture">
                  <div class="dropdown">
                     <span>{{ __('general.welcome') }},</span>
                     <a href="javascript:void(0);" class="dropdown-toggle user-name" data-toggle="dropdown"><strong>{{ Auth::user()->first_name }}</strong></a>
                     <ul class="dropdown-menu dropdown-menu-right account">
                        <li><a href="{{route('profile')}}"><i class="icon-user"></i>{{__('general.my_profile')}}</a></li>
                        <li><a href="{{ route('auth.logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i class="icon-power"></i>Logout</a></li>
                     </ul> 
                  </div>      
               </div>   
               <div class="tab-content p-l-0 p-r-0"> 
                  <div class="tab-pane active" id="menu">
                     <nav id="left-sidebar-nav" class="sidebar-nav">
                        <ul id="main-menu" class="metismenu">
                           <li class="@if (\Request::is('dashboard')) active  @endif">
                              <a href="{{url('/dashboard')}}" class=""><i class="icon-home"></i> <span>{{__('general.dashboard')}}</span></a>
                           </li>
                           @if(auth()->user()->can('user-list') || auth()->user()->can('user-create') ||  auth()->user()->can('user-edit') ||  auth()->user()->can('user-delete'))
                           <li class="@if (\Request::is('user')) active  @endif">
                              <a href="{{url('/user')}}" class=""><i class="icon-home"></i> <span>{{__('general.all_user')}}</span></a>
                           </li>
                           @endif

                        <li class="@if (\Request::is('registered-vet') || \Request::is('registered-vet/*') || \Request::is('animal-owner') || \Request::is('animal-owner/*') || \Request::is('pashumitra') || \Request::is('pashumitra/*') || \Request::is('transporter') || \Request::is('transporter/*') || \Request::is('chemist') || \Request::is('chemist/*')) active  @endif">
                              <a href="javascript:void(0)" class="has-arrow" ><i class="icon-grid"></i> <span>Users</span></a>
                           <ul>
                           <li class="@if (\Request::is('registered-vet') || \Request::is('registered-vet/*')) active  @endif">
                              <a href="{{url('/registered-vet')}}" class="" ><i class=" icon-globe"></i> <span>Registered-Vet</span></a>
                           </li></ul><ul>
                           <li class="@if (\Request::is('animal-owner') || \Request::is('animal-owner/*'))  active  @endif">
                              <a href="{{url('/animal-owner')}}" class="" ><i class=" icon-layers"></i> <span>Animal-Owner</span></a>
                           </li>
                           </ul><ul>                  
                           <li class="@if (\Request::is('pashumitra') || \Request::is('pashumitra/*')) active  @endif">
                              <a href="{{url('/pashumitra')}}" class="" ><i class="icon-book-open"></i> <span>Pashumitra</span></a>
                           </li>                           
                           </ul><ul>
                           <li class="@if (\Request::is('transporter') || \Request::is('transporter/*')) active  @endif">
                              <a href="{{url('/transporter')}}" class="" ><i class="icon-book-open"></i> <span>Transporter</span></a>
                           </li>     
                           </ul>
                           <ul>
                              <li class="@if (\Request::is('chemist') || \Request::is('chemist/*'))  active  @endif">
                              <a href="{{url('/chemist')}}" class="" ><i class="icon-hourglass"></i> <span>Chemist </span></a>
                           </li>
</ul>

</li>
                           <li class="@if (\Request::is('animal') || \Request::is('breed') || \Request::is('species') || \Request::is('characteristics')) active  @endif">
                              <a href="javascript:void(0)" class="has-arrow" ><i class="icon-grid"></i> <span>Animal</span></a>
                              <ul>
                                 <li class="@if (\Request::is('animal')) active  @endif"><a href="{{url('/animal/')}}"><i class=" icon-globe"></i> <span>Type</span></a></li>
                              </ul>
                              <ul>
                                 <li class="@if (\Request::is('breed')) active  @endif"><a href="{{url('/breed')}}"><i class=" icon-globe"></i> <span>Breed</span></a></li>
                              </ul>
                              <ul>
                                 <li class="@if (\Request::is('species')) active  @endif"><a href="{{url('/species')}}"><i class=" icon-globe"></i> <span>Species</span></a></li>
                              </ul>
                              <ul>
                                 <li class="@if (\Request::is('characteristics')) active  @endif"><a href="{{url('/characteristics')}}"><i class=" icon-globe"></i> <span>Characterestics</span></a></li>
                              </ul>
                           </li>
                           <li class="@if (\Request::is('book')) active  @endif">
                              <a href="{{url('/book')}}" class="" ><i class="icon-hourglass"></i> <span>Library </span></a>
                           </li>
                           <li class="">
                              <a href="{{url('/animal-for-sale')}}" class="" ><i class="icon-hourglass"></i> <span>Animal for sale </span></a>
                           </li>

                           <li class="">
                              <a href="{{url('/product-for-sale')}}" class="" ><i class="icon-hourglass"></i> <span>Product for sale </span></a>
                           </li>

                           <li class="">
                              <a href="javascript:void(0)" class="has-arrow" ><i class="icon-grid"></i> <span>Location</span></a>
                              <ul>
                                 <li class=""><a href="">State</a></li>
                              </ul>
                              <ul>
                                 <li class=""><a href="">City</a></li>
                              </ul>
                              <ul>
                                 <li class=""><a href="">Village</a></li>
                              </ul>
                           </li>
                           <li class="@if (\Request::is('logs')) active  @endif">
                              <a href="{{route('logs')}}" class="" ><i class=" icon-doc"></i> <span>Logs</span></a>
                           </li>
                           <li class="">
                              <a href="{{url('/role')}}" class="" ><i class=" icon-globe"></i> <span>Roles</span></a>
                           </li>
                        </ul>
                     </nav>
                  </div>
               </div>
            </div>
         </div>
         <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="d-none">
               @csrf
         </form>
