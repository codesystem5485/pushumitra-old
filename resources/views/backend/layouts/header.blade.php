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
      <link rel="icon" href="{{asset('admin/assets/images/logo1.jpg')}}" type="image/icon type">
      
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
      
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
                  <a href="{{url('pashumitra/dashboard')}}"><img src="{{asset('admin/assets/images/pashumitra-logo-admin.svg')}}" alt="Pashumitra ADMIN" class="img-responsive logo">
                  <!--<strong class="logo-text"> &nbsp;{{ __('general.pashumitra') }}</strong>-->
                  </a>
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
						@if(auth()->user()->can('dashboard')) 
                           <li class="@if (\Request::is('dashboard')) active  @endif">
                              <a href="{{url('pashumitra/dashboard')}}" class=""><i class="bi bi-house"></i> <span>{{__('general.dashboard')}}</span></a>
                           </li>
						   @endif
                           @if(auth()->user()->can('user-list') || auth()->user()->can('user-create') ||  auth()->user()->can('user-edit') ||  auth()->user()->can('user-delete'))
                           <li class="@if (\Request::is('user')) active  @endif">
                              <a href="{{url('/user')}}" class=""><i class="icon-home"></i> <span>{{__('general.all_user')}}</span></a>
                           </li>
                           @endif
                          
                        @if(auth()->user()->can('registered-vet-list') || auth()->user()->can('animal-owner-list') ||  auth()->user()->can('pashumitra-list'))
                        <li class="@if (\Request::is('pashumitra/registered-vet') || \Request::is('pashumitra/registered-vet/*') || \Request::is('pashumitra/animal-owner') || \Request::is('pashumitra/animal-owner/*') || \Request::is('pashumitra/pashumitra') || \Request::is('pashumitra/pashumitra/*')) active  @endif">
                           <a href="javascript:void(0)" class="has-arrow" ><i class="bi bi-people"></i> <span>Users</span></a>
                        @if(auth()->user()->can('registeredvet-list') || auth()->user()->can('registeredvet-create') ||  auth()->user()->can('registeredvet-edit') ||  auth()->user()->can('registeredvet-delete'))
                           <ul>
                           <li class="@if (\Request::is('pashumitra/registered-vet') || \Request::is('pashumitra/registered-vet/*')) active  @endif">
                              <a href="{{url('pashumitra/registered-vet')}}" class="" ><span>Registered-Vet</span></a>
                           </li>
                           </ul>
                        @endif
                        @if(auth()->user()->can('animal-owner-list') || auth()->user()->can('animal-owner-create') ||  auth()->user()->can('animal-owner-edit') ||  auth()->user()->can('animal-owner-delete'))
                           <ul>
                           <li class="@if (\Request::is('pashumitra/animal-owner') || \Request::is('pashumitra/animal-owner/*'))  active  @endif">
                              <a href="{{url('pashumitra/animal-owner')}}" class="" ><span>Animal-Owner</span></a>
                           </li>
                           </ul>
                           @endif

                           @if(auth()->user()->can('pashumitra-list') || auth()->user()->can('pashumitra-create') ||  auth()->user()->can('pashumitra-edit') ||  auth()->user()->can('pashumitra-delete'))
                           <ul>                  
                           <li class="@if (\Request::is('pashumitra/pashumitra') || \Request::is('pashumitra/pashumitra/*')) active  @endif">
                              <a href="{{url('pashumitra/pashumitra')}}" class="" ><span>Pashumitra</span></a>
                           </li>                           
                           </ul>
                           @endif
						   <ul>
                           <li class="@if (\Request::is('pashumitra/otheruser') || \Request::is('pashumitra/otheruser/*'))  active  @endif">
                              <a href="{{url('pashumitra/otheruser')}}" class="" ><span>Other</span></a>
                           </li>
                           </ul>

                          
                           </li>
                           @endif
                            @if(auth()->user()->can('transporter-list') || auth()->user()->can('transporter-create') ||  auth()->user()->can('transporter-edit') ||  auth()->user()->can('transporter-delete'))
                           
                           <li class="menunew ">
                              <a href="{{url('pashumitra/transporter')}}" class="@if (\Request::is('pashumitra/transporter') || \Request::is('pashumitra/transporter/*')) active  @endif" ><i class="bi bi-truck"></i> <span>Transporter</span></a>
                           </li>     
                           
                           @endif

                           @if(auth()->user()->can('chemist-list') || auth()->user()->can('chemist-create') ||  auth()->user()->can('chemist-edit') ||  auth()->user()->can('chemist-delete'))
                           <li class="@if (\Request::is('pashumitra/chemist') || \Request::is('pashumitra/chemist/*'))  active  @endif">
                              <a href="{{url('pashumitra/chemist')}}" class="@if (\Request::is('pashumitra/chemist') || \Request::is('pashumitra/chemist/*'))  active  @endif" ><i class="bi bi-capsule-pill"></i> <span>Chemist </span></a>
                           </li>
                           
                           @endif
						     @if(auth()->user()->can('sendnotifications-create'))
                                            
                           <li class="@if (\Request::is('pashumitra/sendnotifications/create') || \Request::is('pashumitra/sendNotifications/create/*')) active  @endif">
                              <a href="{{url('pashumitra/sendnotifications/create')}}" class="" ><i class="bi bi-bell"></i> <span>Send Notifications</span></a>
                           </li>                           
                           @endif
						   @if(auth()->user()->can('categories-list'))
						    <li class="@if (\Request::is('pashumitra/categories') || \Request::is('pashumitra/categories/*'))  active  @endif">
                              <a href="{{url('pashumitra/categories')}}" class="@if (\Request::is('pashumitra/categories') || \Request::is('pashumitra/categories/*'))  active  @endif" ><i class="bi bi-ui-checks-grid"></i> <span>Categories </span></a>
                           </li>
						    @endif
                           @if(auth()->user()->can('animal-type-list') || auth()->user()->can('breed-list') ||  auth()->user()->can('species-list') ||  auth()->user()->can('characteristics-list'))
                           <li class="@if (\Request::is('pashumitra/animal') || \Request::is('pashumitra/breed') || \Request::is('pashumitra/species') || \Request::is('pashumitra/characteristics')) active  @endif">
                              <a href="javascript:void(0)" class="has-arrow" > <span>Animal</span></a>
                              
                              <ul>
                              @if(auth()->user()->can('animal-type-list') || auth()->user()->can('animal-type-create') ||  auth()->user()->can('animal-type-edit') ||  auth()->user()->can('animal-type-delete'))
                                 <li class="@if (\Request::is('pashumitra/animal')) active  @endif"><a href="{{url('pashumitra/animal/')}}"><span>Type</span></a></li>
                              @endif

                              @if(auth()->user()->can('breed-list') || auth()->user()->can('breed-create') ||  auth()->user()->can('breed-edit') ||  auth()->user()->can('breed-delete'))
                                 <li class="@if (\Request::is('pashumitra/breed')) active  @endif"><a href="{{url('pashumitra/breed')}}"><span>Breed</span></a></li>
                              @endif

                              @if(auth()->user()->can('species-list') || auth()->user()->can('species-create') ||  auth()->user()->can('species-edit') ||  auth()->user()->can('species-delete'))
                                 <li class="@if (\Request::is('pashumitra/species')) active  @endif"><a href="{{url('pashumitra/species')}}"><span>Species</span></a></li>
                              @endif

                              @if(auth()->user()->can('characteristics-list') || auth()->user()->can('characteristics-create') ||  auth()->user()->can('characteristics-edit') ||  auth()->user()->can('characteristics-delete'))
                                 <li class="@if (\Request::is('pashumitra/characteristics')) active  @endif"><a href="{{url('pashumitra/characteristics')}}"><span>Characterestics</span></a></li>
                              @endif
                              </ul>
                           </li>
                           @endif
                           
                           
                           @if(auth()->user()->can('add-animal-list') || auth()->user()->can('add-animal/*')) 
                           <li class="">
                              <a href="{{url('pashumitra/add-animal')}}" class="" ><i class="bi bi-patch-plus"></i> <span>Add Animals </span></a>
                           </li>
                           @endif

                           @if(auth()->user()->can('product-list') || auth()->user()->can('product/*')) 
                          <!-- <li class="">
                              <a href="{{url('/add-product')}}" class="" ><i class="icon-hourglass"></i> <span>Add Products </span></a>
                           </li>-->
                           @endif

                           @if(auth()->user()->can('book-list') || auth()->user()->can('book-create') ||  auth()->user()->can('book-edit') ||  auth()->user()->can('book-delete'))
                           <li class="@if (\Request::is('pashumitra/book')) active  @endif">
                              <a href="{{url('pashumitra/book')}}" class="" ><i class="bi bi-journal-richtext"></i> <span>Library </span></a>
                           </li>
                           @endif
						   @if(auth()->user()->can('grfile-list') || auth()->user()->can('grfile-create') ||  auth()->user()->can('grfile-edit') ||  auth()->user()->can('grfile-delete'))
                           <li class="@if (\Request::is('pashumitra/grfiles')) active  @endif">
                              <a href="{{url('pashumitra/grfiles')}}" class="" > <span>Government Schemes </span></a>
                           </li>
                           @endif
						   @if(auth()->user()->can('fees-list') || auth()->user()->can('fees-create') || auth()->user()->can('fees-edit') || auth()->user()->can('fees-delete')) 
                           
						   <li class="@if (\Request::is('pashumitra/fees')) active  @endif">
                              <a href="{{url('pashumitra/fees')}}" class="" ><i class="bi bi-wallet2"></i> <span>Fees </span></a>
                           </li>
						   @endif
                           @if(auth()->user()->can('animal-sale-list') || auth()->user()->can('animal-sale-create') || auth()->user()->can('animal-sale-edit') || auth()->user()->can('animal-sale-delete')) 
                           <li class="">
                              <a href="{{url('pashumitra/animal-sale')}}" class="@if (\Request::is('pashumitra/animal-sale')) active  @endif" ><i class="bi bi-receipt"></i> <span>Animal for sale </span></a>
                           </li>
                           @endif
						   @if(auth()->user()->can('advertisement-list') || auth()->user()->can('advertisement-create') || auth()->user()->can('adevertisement-edit') || auth()->user()->can('adevertisement-delete')) 
                           <li class="">
                              <a href="{{url('pashumitra/advertisements')}}" class="@if (\Request::is('pashumitra/advertisements')) active  @endif" ><i class="bi bi-badge-ad"></i> <span>Advertisements </span></a>
                           </li>
                           @endif
						   
						   @if(auth()->user()->can('testimonial-list') || auth()->user()->can('testimonial-create') || auth()->user()->can('testimonial-edit') || auth()->user()->can('testimonial-delete')) 
                           <li class="">
                              <a href="{{url('pashumitra/testimonials')}}" class="@if (\Request::is('pashumitra/testimonials')) active  @endif" ><i class="bi bi-chat-left-quote"></i> <span>Testimonials </span></a>
                           </li>
                           @endif
						    @if(auth()->user()->can('rating-list') || auth()->user()->can('rating-delete')) 
						    <li class="">
                              <a href="{{url('pashumitra/ratings')}}" class="" ><i class="bi bi-star-half"></i> <span>Ratings</span></a>
                           </li>
						   @endif
						    @if(auth()->user()->can('csractivities-list'))
						   <li class="">
                              <a href="{{url('pashumitra/csractivities')}}" class="" ><i class="bi bi-calendar-event"></i> <span>Csr Activities</span></a>
                           </li>
						   @endif
						    @if(auth()->user()->can('easycare-list'))
						   <li class="">
                              <a href="{{url('pashumitra/easycares')}}" class="" ><i class="bi bi-share"></i> <span>Knowledge Sharing</span></a>
                           </li>
						   @endif
						   @if(auth()->user()->can('breeder-list') || auth()->user()->can('breeder-create') || auth()->user()->can('breeder-edit') || auth()->user()->can('breeder-delete')) 
                           
						   <li class="">
                              <a href="{{url('pashumitra/breeders')}}" class="@if (\Request::is('pashumitra/breeders')) active  @endif" > <span>Breeders </span></a>
                           </li>
							@endif
                           @if(auth()->user()->can('product-sale-list')) 
                           <li class="">
                              <a href="{{url('pashumitra/product-sale')}}" class="@if (\Request::is('pashumitra/product-sale')) active  @endif" > <span>Product for sale </span></a>
                           </li>
                           @endif
						   @if(auth()->user()->can('supplier-list') || auth()->user()->can('supplier-create') || auth()->user()->can('supplier-edit') || auth()->user()->can('supplier-delete')) 
                           <li class="">
                              <a href="{{url('pashumitra/suppliers')}}" class="@if (\Request::is('pashumitra/suppliers')) active  @endif" > <span>Suppliers </span></a>
                           </li>
						    @endif
						   @if(auth()->user()->can('hospital-list') || auth()->user()->can('hospital-create') || auth()->user()->can('hospital-edit') || auth()->user()->can('hospital-delete')) 
                           <li class="">
                              <a href="{{url('pashumitra/hospitals')}}" class="@if (\Request::is('pashumitra/hospitals')) active @endif" ><i class="bi bi-hospital"></i> <span>Veterinary Hospitals </span></a>
                           </li>
						   @endif
						    @if(auth()->user()->can('lab-list') || auth()->user()->can('lab-create') || auth()->user()->can('lab-edit') || auth()->user()->can('lab-delete')) 
                           <li class="">
                              <a href="{{url('pashumitra/labs')}}" class="@if (\Request::is('pashumitra/labs')) active @endif" ><i class="bi bi-thermometer-half"></i> <span>Labs </span></a>
                           </li>
						   @endif
						   @if(auth()->user()->can('trainingcenter-list') || auth()->user()->can('trainingcenter-create') || auth()->user()->can('trainingcenter-edit') || auth()->user()->can('trainingcenter-delete')) 
                           <li class="">
                              <a href="{{url('pashumitra/trainingcenters')}}" class="@if (\Request::is('pashumitra/trainingcenters')) active  @endif" > <span>Training Centers </span></a>
                           </li>
						   @endif
						   @if(auth()->user()->can('institution-list') || auth()->user()->can('institution-create') || auth()->user()->can('institution-edit') || auth()->user()->can('institution-delete')) 
                           
						   <li class="">
                              <a href="{{url('pashumitra/institutions')}}" class="@if (\Request::is('pashumitra/institutions')) active  @endif" > <span>Institutions </span></a>
                           </li>
						   @endif
						    @if(auth()->user()->can('farm-list') || auth()->user()->can('farm-create') || auth()->user()->can('farm-edit') || auth()->user()->can('farm-delete')) 
                           
						   <li class="">
                              <a href="{{url('pashumitra/farms')}}" class="@if (\Request::is('pashumitra/farms')) active  @endif" > <span>Farms </span></a>
                           </li>
						    @endif
							@if(auth()->user()->can('panjarpol-list') || auth()->user()->can('panjarpol-create') || auth()->user()->can('panjarpol-edit') || auth()->user()->can('panjarpol-delete')) 
							<li class="">
                              <a href="{{url('pashumitra/panjarpols')}}" class="@if (\Request::is('pashumitra/panjarpols')) active  @endif" > <span>Panjarpols </span></a>
							</li>
						    @endif
							@if(auth()->user()->can('ngo-list') || auth()->user()->can('ngo-create') || auth()->user()->can('ngo-edit') || auth()->user()->can('ngo-delete')) 
							<li class="">
                              <a href="{{url('pashumitra/ngo')}}" class="@if (\Request::is('pashumitra/ngo')) active  @endif" ><i class="bi bi-building"></i> <span>Ngo </span></a>
							</li>
						    @endif
							@if(auth()->user()->can('milkcollection-list') || auth()->user()->can('milkcollection-create') || auth()->user()->can('milkcollection-edit') || auth()->user()->can('milkcollection-delete')) 
							<li class="">
                              <a href="{{url('pashumitra/milkcollections')}}" class="@if (\Request::is('pashumitra/milkcollections')) active  @endif" > <span>Milkcollection Centers </span></a>
							</li>
						    @endif
							@if(auth()->user()->can('poultryhatchery-list') || auth()->user()->can('poultryhatchery-create') || auth()->user()->can('poultryhatchery-edit') || auth()->user()->can('poultryhatchery-delete')) 
							<li class="">
                              <a href="{{url('pashumitra/poultryhatchery')}}" class="@if (\Request::is('pashumitra/poultryhatchery')) active  @endif" > <span>Poultry Hatchery</span></a>
							</li>
						    @endif
							@if(auth()->user()->can('dogshelter-list') || auth()->user()->can('dogshelter-create') || auth()->user()->can('dogshelter-edit') || auth()->user()->can('dogshelter-delete')) 
                            <li class="">
                              <a href="{{url('pashumitra/dogshelters')}}" class="@if (\Request::is('pashumitra/dogshelters')) active  @endif" > <span>Shelter </span></a>
                            </li>
						    @endif
							@if(auth()->user()->can('shop-list') || auth()->user()->can('shop-create') || auth()->user()->can('shop-edit') || auth()->user()->can('shop-delete')) 
                            <li class="">
                              <a href="{{url('pashumitra/shops')}}" class="@if (\Request::is('pashumitra/shops')) active  @endif" ><i class="bi bi-shop"></i> <span>Shops </span></a>
                            </li>
						    @endif
							@if(auth()->user()->can('cms-list')) 
						    <li class="">
                              <a href="{{url('pashumitra/content-management')}}" class="" > <span>Front Pages</span></a>
							</li>
							@endif
							@if(auth()->user()->can('paymentreport-list')) 
						   <li class="">
                              <a href="{{url('pashumitra/paymentreport')}}" class="" ><i class="bi bi-currency-rupee"></i> <span>Payment Report</span></a>
                           </li>
						   @endif
						   @if(auth()->user()->can('registrationpaymentreport-list'))
						   <li class="">
                              <a href="{{url('pashumitra/paymentreport/regPaymentReport')}}" class="" ><i class="bi bi-piggy-bank"></i> <span>Registration Payments</span></a>
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
                              <a href="{{url('pashumitra/role')}}" class="" ><i class="bi bi-toggles"></i> <span>Roles</span></a>
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
