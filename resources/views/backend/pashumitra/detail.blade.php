@extends('backend.master')
@section('css')
@endsection 
@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>
                    {{ __('general.pashumitra_details')}}
                   </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.pashumitra_list')}}</a></li>
                    <li class="breadcrumb-item">
                    {{ __('general.pashumitra_details')}}
                    
                    </li>
                </ul>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                <div class="header">
                    @include('backend.layouts.flash-message')
                    <!-- <h2>Role Permissions</h2> -->
                    
                </div> 
                <form action="@if(empty($user)){{route('animal-owner.store')}}@else{{route('animal-owner.update',['id' => $user->id])}}@endif" method="post"> 
                    @csrf  
                <div class="body">
                    <!-- <label for="basic-url">Your vanity URL</label> -->
					
					@if($user->is_verified==0)
						<div class="mb-5">
                        
                            <span class=""><a href="{{route('pashumitra.pashumitra-verify',['id' => $user->id])}}" class="btn btn-info" >Verify Pashumitra</a></span>
                    </div>
						
					@endif
                    <div class="mb-5">
                        
                            <span class="">Name :</span>
                            <span class="">{{ $user->full_name }}</span>
                        
                    </div>
					 <div class="mb-5">
                        
                            <span class="">Pashumitra Code :</span>
                            <span class="">{{ $user->pm_code}}</span>
                        
                    </div>
                    <div class="mb-5">
                        
                            <span class="">Email ID :</span>
                            <span class="">{{ $user->email }}</span>
                        
                    </div>
                    <div class="mb-5">
                        <div class="">
                            <span class="">Mobile Number :</span>
                            <span class="">{{ $user->mobile_number }}</span>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="">
                            <span class="">Address :</span>
                            <span class="">{{ $user->address_line_1." ".$user->state.", ".$user->city_town.", ".$user->district.", ".$user->taluka.", ".$user->pincode }}</span>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="">
                            <span class="">Education :</span>
                            <span class="">{{ $user->education }}</span>
                        </div>
                    </div>
                    
                   
                    
                    <div class="mb-5">
                        <div class="">
                            <span class="">Date of Birth :</span>
							@if($user->date_of_birth!=null && $user->date_of_birth!='0000-00-00')
                            <span class="">{{ date('d-m-Y',strtotime($user->date_of_birth))}}</span>
							@else
								<span class="">-</span>
							@endif
                        </div>
                        
                    </div>

               <!--     <div class="mb-5">
                        <div class="">
                            <span class="">Nationality :</span>
                            <span class="">{{ $user->nationality}}</span>
                        </div>
                    </div>-->

                    <div class="mb-5">
                        <div class="">
                            <span class="">Gender :</span>
                            <span class="">{{ $user->sex}}</span>
                        </div>
                    </div>
					 <div class="mb-5">
                        <div class="">
                            <span class="">Job Type :</span>
                            <span class="">@if(isset($user->getUserDetail)) {{ $user->getUserDetail->job_type}}@endif</span>
                        </div>
                    </div>
					
					<div class="mb-5">
					
                        <div class="">
                            <span class="">Nominee Name :</span>
                            <span class="">@if(isset($user->getUserDetail)){{ $user->getUserDetail->pm_nominee_name}} @endif</span>
                        </div>
                        <div class="">
                            <span class="">Nominee Date of birth :</span>
                            <span class="">@if(isset($user->getUserDetail)){{ $user->getUserDetail->pm_nominee_dob}} @endif</span>
                        </div>
                        <div class="">
                            <span class="">Nominee Relationship :</span>
                            <span class="">@if(isset($user->getUserDetail)){{ $user->getUserDetail->pm_nominee_relationship}} @endif</span>
                        </div>
                    </div>
					
					
					<div class="mb-5">
                        <div class="">
                            <span class="">Name of organization working with :</span>
                            <span class="">@if(isset($user->getUserDetail)) {{ $user->getUserDetail->pm_name_of_org}} @endif</span>
                        </div>
                    </div>
					
					 <div class="mb-5">
                        <div class="">
                            <span class="">Aadhar card number :</span>
                            <span class=""> @if(isset($user->getUserDetail)) {{ $user->getUserDetail->pm_aadhar_no }} @endif</span>
                        </div>
                        <div class="">
                            <span class="">PAN Card Number :</span>
                            <span class="">@if(isset($user->getUserDetail)) {{ $user->getUserDetail->pm_pan_no}} @endif</span>
                        </div>
                        <div class="">
                            <span class="">Bank Name :</span>
                            <span class="">@if(isset($user->getUserDetail)) {{ $user->getUserDetail->pm_bank_name}} @endif</span>
                        </div>
						<div class="">
                            <span class="">Bank Account Number :</span>
                            <span class="">@if(isset($user->getUserDetail)) {{ $user->getUserDetail->pm_account_no}} @endif</span>
                        </div>
						<div class="">
                            <span class="">Bank IFSC Code :</span>
                            <span class="">@if(isset($user->getUserDetail)) {{ $user->getUserDetail->pm_ifsc_code}} @endif</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">Education certificate :</span>
                            <span class=""><a target="_blank" href="{{ url("/upload/education_certificate/")}}/{{$user->education_certificate}}">{{ $user->education_certificate}}</a></span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">Cheque Photo :</span>
                            <span class="">@if(isset($user->getUserDetail)) <a target="_blank" href="{{ url("/upload/cheque_photo/")}}/{{$user->getUserDetail->pm_cheque_photo}}">{{ $user->getUserDetail->pm_cheque_photo}} @endif</a></span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">Profile Photo :</span>
                            <span class="">@if(isset($user->profile_photo)) <a target="_blank" href="{{ url("/upload/profile_photo/")}}/{{$user->profile_photo}}">{{ $user->profile_photo}} @endif</a></span>
                        </div>
                    </div>
					
					
					<div class="mb-5">
                        <div class="">
                            <span class="">Recommendation Letter :</span>
                            <span class="">@if(isset($user->getUserDetail)) <a href="{{ url("/upload/recommendation_letter/")}}/{{$user->getUserDetail->pm_recommendation_letter}}">{{ $user->getUserDetail->pm_recommendation_letter}} @endif</a></span>
                        </div>
                    </div>
					
                     
                </div>
                </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection 

