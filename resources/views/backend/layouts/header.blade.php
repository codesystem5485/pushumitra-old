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
   <style>
    .menunew .active a{
    font-weight: 700;
	color: #777;
}
 .activecls{
    font-weight: 700;
	color: #777;
}
   </style>
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
                              <a href="{{url('pashumitra/dashboard')}}" class=""><i class="icon-home"></i> <span>{{__('general.dashboard')}}</span></a>
                           </li>
                           @if(auth()->user()->can('user-list') || auth()->user()->can('user-create') ||  auth()->user()->can('user-edit') ||  auth()->user()->can('user-delete'))
                           <li class="@if (\Request::is('user')) active  @endif">
                              <a href="{{url('/user')}}" class=""><i class="icon-home"></i> <span>{{__('general.all_user')}}</span></a>
                           </li>
                           @endif
                          
                        @if(auth()->user()->can('registered-vet-list') || auth()->user()->can('animal-owner-list') ||  auth()->user()->can('pashumitra-list'))
                        <li class="@if (\Request::is('pashumitra/registered-vet') || \Request::is('pashumitra/registered-vet/*') || \Request::is('pashumitra/animal-owner') || \Request::is('pashumitra/animal-owner/*') || \Request::is('pashumitra/pashumitra') || \Request::is('pashumitra/pashumitra/*')) active  @endif">
                           <a href="javascript:void(0)" class="has-arrow" ><i class="icon-grid"></i> <span>Users</span></a>
                        @if(auth()->user()->can('registeredvet-list') || auth()->user()->can('registeredvet-create') ||  auth()->user()->can('registeredvet-edit') ||  auth()->user()->can('registeredvet-delete'))
                           <ul>
                           <li class="@if (\Request::is('pashumitra/registered-vet') || \Request::is('pashumitra/registered-vet/*')) active  @endif">
                              <a href="{{url('pashumitra/registered-vet')}}" class="" ><i class=" icon-globe"></i> <span>Registered-Vet</span></a>
                           </li>
                           </ul>
                        @endif
                        @if(auth()->user()->can('animal-owner-list') || auth()->user()->can('animal-owner-create') ||  auth()->user()->can('animal-owner-edit') ||  auth()->user()->can('animal-owner-delete'))
                           <ul>
                           <li class="@if (\Request::is('pashumitra/animal-owner') || \Request::is('pashumitra/animal-owner/*'))  active  @endif">
                              <a href="{{url('pashumitra/animal-owner')}}" class="" ><i class=" icon-layers"></i> <span>Animal-Owner</span></a>
                           </li>
                           </ul>
                           @endif

                           @if(auth()->user()->can('pashumitra-list') || auth()->user()->can('pashumitra-create') ||  auth()->user()->can('pashumitra-edit') ||  auth()->user()->can('pashumitra-delete'))
                           <ul>                  
                           <li class="@if (\Request::is('pashumitra/pashumitra') || \Request::is('pashumitra/pashumitra/*')) active  @endif">
                              <a href="{{url('pashumitra/pashumitra')}}" class="" ><i class="icon-book-open"></i> <span>Pashumitra</span></a>
                           </li>                           
                           </ul>
                           @endif

                          
                           </li>
                           @endif
                            @if(auth()->user()->can('transporter-list') || auth()->user()->can('transporter-create') ||  auth()->user()->can('transporter-edit') ||  auth()->user()->can('transporter-delete'))
                           
                           <li class="menunew ">
                              <a href="{{url('pashumitra/transporter')}}" class="@if (\Request::is('pashumitra/transporter') || \Request::is('pashumitra/transporter/*')) active  @endif" ><i class="icon-book-open"></i> <span>Transporter</span></a>
                           </li>     
                           
                           @endif

                           @if(auth()->user()->can('chemist-list') || auth()->user()->can('chemist-create') ||  auth()->user()->can('chemist-edit') ||  auth()->user()->can('chemist-delete'))
                           <li class="@if (\Request::is('pashumitra/chemist') || \Request::is('pashumitra/chemist/*'))  active  @endif">
                              <a href="{{url('pashumitra/chemist')}}" class="@if (\Request::is('pashumitra/chemist') || \Request::is('pashumitra/chemist/*'))  active  @endif" ><i class="icon-hourglass"></i> <span>Chemist </span></a>
                           </li>
                           
                           @endif
                           @if(auth()->user()->can('animal-type-list') || auth()->user()->can('breed-list') ||  auth()->user()->can('species-list') ||  auth()->user()->can('characteristics-list'))
                           <li class="@if (\Request::is('pashumitra/animal') || \Request::is('pashumitra/breed') || \Request::is('pashumitra/species') || \Request::is('pashumitra/characteristics')) active  @endif">
                              <a href="javascript:void(0)" class="has-arrow" ><i class="icon-grid"></i> <span>Animal</span></a>
                              @if(auth()->user()->can('animal-type-list') || auth()->user()->can('animal-type-create') ||  auth()->user()->can('animal-type-edit') ||  auth()->user()->can('animal-type-delete'))
                              <ul>
                                 <li class="@if (\Request::is('pashumitra/animal')) active  @endif"><a href="{{url('pashumitra/animal/')}}"><i class=" icon-globe"></i> <span>Type</span></a></li>
                              </ul>
                              @endif

                              @if(auth()->user()->can('breed-list') || auth()->user()->can('breed-create') ||  auth()->user()->can('breed-edit') ||  auth()->user()->can('breed-delete'))
                              <ul>
                                 <li class="@if (\Request::is('pashumitra/breed')) active  @endif"><a href="{{url('pashumitra/breed')}}"><i class=" icon-globe"></i> <span>Breed</span></a></li>
                              </ul>
                              @endif

                              @if(auth()->user()->can('species-list') || auth()->user()->can('species-create') ||  auth()->user()->can('species-edit') ||  auth()->user()->can('species-delete'))
                              <ul>
                                 <li class="@if (\Request::is('pashumitra/species')) active  @endif"><a href="{{url('pashumitra/species')}}"><i class=" icon-globe"></i> <span>Species</span></a></li>
                              </ul>
                              @endif

                              @if(auth()->user()->can('characteristics-list') || auth()->user()->can('characteristics-create') ||  auth()->user()->can('characteristics-edit') ||  auth()->user()->can('characteristics-delete'))
                              <ul>
                                 <li class="@if (\Request::is('pashumitra/characteristics')) active  @endif"><a href="{{url('pashumitra/characteristics')}}"><i class=" icon-globe"></i> <span>Characterestics</span></a></li>
                              </ul>
                              @endif
                           </li>
                           @endif
                           
                           @if(auth()->user()->can('add-animal-list') || auth()->user()->can('add-animal/*')) 
                           <li class="">
                              <a href="{{url('pashumitra/add-animal')}}" class="" ><i class="icon-hourglass"></i> <span>Add Animals </span></a>
                           </li>
                           @endif

                           @if(auth()->user()->can('product-list') || auth()->user()->can('product/*')) 
                          <!-- <li class="">
                              <a href="{{url('/add-product')}}" class="" ><i class="icon-hourglass"></i> <span>Add Products </span></a>
                           </li>-->
                           @endif

                           @if(auth()->user()->can('book-list') || auth()->user()->can('book-create') ||  auth()->user()->can('book-edit') ||  auth()->user()->can('book-delete'))
                           <li class="@if (\Request::is('pashumitra/book')) active  @endif">
                              <a href="{{url('pashumitra/book')}}" class="" ><i class="icon-hourglass"></i> <span>Library </span></a>
                           </li>
                           @endif
						   <li class="@if (\Request::is('pashumitra/fees')) active  @endif">
                              <a href="{{url('pashumitra/fees')}}" class="" ><i class="icon-hourglass"></i> <span>Fees </span></a>
                           </li>
                           @if(auth()->user()->can('animal-sale-list') || auth()->user()->can('animal-sale-create') || auth()->user()->can('animal-sale-edit') || auth()->user()->can('animal-sale-delete')) 
                           <li class="">
                              <a href="{{url('pashumitra/animal-sale')}}" class="@if (\Request::is('pashumitra/animal-sale')) active  @endif" ><i class="icon-hourglass"></i> <span>Animal for sale </span></a>
                           </li>
                           @endif
						   
						   <li class="">
                              <a href="{{url('pashumitra/breeders')}}" class="@if (\Request::is('pashumitra/breeders')) active  @endif" ><i class="icon-hourglass"></i> <span>Breeders </span></a>
                           </li>

                           @if(auth()->user()->can('product-sale-list')) 
                           <li class="">
                              <a href="{{url('pashumitra/product-sale')}}" class="" ><i class="icon-hourglass"></i> <span>Product for sale </span></a>
                           </li>
                           @endif
						   @if(auth()->user()->can('cms-list')) 
						    <li class="">
                              <a href="{{url('pashumitra/content-management')}}" class="" ><i class="icon-hourglass"></i> <span>Front Pages</span></a>
                           </li>
						   @endif
                           
                           {{-- <li class="">
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
                           </li>--}}
                           @if(auth()->user()->can('log')) 
                           <li class="@if (\Request::is('logs')) active  @endif">
                              <a href="{{route('logs')}}" class="" ><i class=" icon-doc"></i> <span>Logs</span></a>
                           </li>
                           @endif

                           @if(auth()->user()->can('role-list') || auth()->user()->can('role-create') || auth()->user()->can('role-edit') || auth()->user()->can('role-delete')) 
                           <li class="">
                              <a href="{{url('pashumitra/role')}}" class="" ><i class=" icon-globe"></i> <span>Roles</span></a>
                           </li>
                           @endif
                        </ul>
                     </nav>
                  </div>
               </div>
            </div>
         </div>
         <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="d-none">
               @csrf
         </form>
