@extends('backend.master')
@section('css')
<style>
   .rounded-circle{
      width:100px;
   }
   .error-msg{
      color:red;
      background:lightgoldenrodyellow;
   }
   .success-msg{
      color:green;
      background:lightgoldenrodyellow;
   }
</style>
@endsection
@section('content')
<div id="main-content" class="profilepage_2">
   <div class="container-fluid">
      <div class="block-header">
         <div class="row">
            <div class="col-lg-5 col-md-8 col-sm-12">
               <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a> {{ __('general.profile') }}</h2>
               <ul class="breadcrumb"> 
                  <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="icon-home"></i></a></li>
                  <li class="breadcrumb-item">{{ __('general.profile') }}</li>
               </ul>
            </div>
         </div>
      </div>
      @include('backend.layouts.flash-message')
      <div class="row clearfix">
         <div class="col-lg-4 col-md-12">
            <div class="card profile-header">
               <div class="body">
                  <div class="profile-image"> <img src="{{asset('admin/assets/images/user.png')}}" class="rounded-circle" alt=""> </div>
                  <div>
                     <h4 class="m-b-0"><strong> {!!\Auth::user()->first_name!!} {!!\Auth::user()->last_name!!}</strong></h4>
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="header">
                  <h2>{{ __('general.info') }}</h2>
               </div>
               <div class="body">
                  <small class="text-muted">{{ __('general.email_address') }}: </small>
                  <p>{{Auth::user()->email}}</p>
                  <hr>
                  <small class="text-muted">{{ __('general.role') }}: </small>
                  <p>@if(!empty(Auth::user()->roles[0]->name)){{Auth::user()->roles[0]->name}}@endif</p>
                 
               </div>
               
            </div>
            <div class="card mobile-update">
                    <form class="form form-mobile-otp" action="" method="post">
                        @csrf  
                     <div class="body">
                        <span class="success-msg"></span>
                        
                        <h6>{{ __('general.mobile_number') }}</h6>
                        <div class="row clearfix">
                           <div class="col-lg-12 col-md-12">
                              <div class="form-group">
                                 <input type="text" name="mobile_number" class="form-control phone" value="{{\Auth::user()->mobile_number}}"  placeholder="{{ __('general.mobile_number') }}">
                                 <span class="error-msg"></span>
                              </div>
                           </div>
                        </div>
                        <input type="submit" class="btn btn-primary" value="{{ __('general.update') }}"/>
                     </div>
                    </form>
                  </div>
         </div>
         <div class="col-lg-5 col-md-12">
            <div class="tab-content padding-0">
               <div class="tab-pane active" id="Settings">
                  <div class="card">
                    <form action="{{route('update.profile',['id' => Auth::user()->id])}}" method="post">
                        @csrf
                     <div class="body">
                        <h6>{{ __('general.basic_information') }}</h6>
                        
                        <div class="row clearfix">
                           <div class="col-lg-12 col-md-12">
                              <div class="form-group">
                                 <input type="text" name="first_name" value="{{\Auth::user()->first_name}}" class="form-control" placeholder="{{ __('general.first_name') }}">
                              </div>
                              <div class="form-group">
                                 <input type="text" name="middle_name" value="{{\Auth::user()->middle_name}}" class="form-control" placeholder="{{ __('general.middle_name') }}">
                              </div>
                              <div class="form-group">
                                 <input type="text" name="last_name" value="{{\Auth::user()->last_name}}" class="form-control" placeholder="{{ __('general.last_name') }}">
                              </div>
                              <div class="form-group">
                                 <input type="text" name="email" class="form-control" value="{{\Auth::user()->email}}"  placeholder="{{ __('general.email_address') }}">
                              </div>
                           </div>
                        </div>
                        <input type="submit" class="btn btn-primary" value="{{ __('general.update') }}"/> &nbsp;&nbsp;
                        
                     </div>
                    </form>
                  </div>
                  
                   <div class="card">
                    <form class="form-change-password" action="{{route('change.password')}}" method="post">
                        @csrf
                     <div class="body">
                        <h6>{{ __('general.change_password') }}</h6>
                        
                        <div class="row clearfix">
                           <div class="col-lg-12 col-md-12">
                              <div class="form-group">
                                 <input type="password" name="old_password" id="old_password" value="" class="form-control" placeholder="{{ __('general.old_password') }}">
                                 <span class="error-msg" id="old_password_err" style="display:none;">{{__('messages.enter_old_password')}}</span>
                              </div>
                              <div class="form-group">
                                 <input type="password" name="current_password" id="current_password" value="" class="form-control" placeholder="{{ __('general.current_password') }}">
                                 <span class="error-msg" id="current_password_err" style="display:none;">{{__('messages.enter_current_password')}}</span>
                              </div>
                              <div class="form-group">
                                 <input type="password" name="confirm_password" id="confirm_password" value="" class="form-control" placeholder="{{ __('general.confirm_password') }}">
                                 <span class="error-msg" id="confirm_password_err" style="display:none;">{{__('messages.enter_confirm_password')}}</span>
                              </div>
                             
                           </div>
                        </div>
                        <span class="error-msg" id="error-msg" style="display:none;"></span>
                        <span class="success-msg" id="success-msg" style="display:none;"></span>
                        
                        <input type="submit" class="btn btn-primary" value="{{ __('general.change_password') }}"/> &nbsp;&nbsp;
                        
                     </div>
                    </form>
                  </div>
               </div>
            </div>
         </div>
         
      </div>
   </div>
</div>
@endsection
@push('scripts')

<script src="{{asset('admin/assets/js/profile.js')}}"></script>  
@endpush