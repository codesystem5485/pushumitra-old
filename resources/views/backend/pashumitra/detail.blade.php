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
                            <span class="">{{ $user->first_name." ".$user->middle_name." ".$user->last_name}}</span>
                        
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
                            <span class="">{{ $user->address_line_1." ".$user->address_line_2." ".$user->state.", ".$user->city." ".$user->village.", ".$user->pincode }}</span>
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
                            <span class="">Collage Name/Address :</span>
                            <span class="">{{ $user->getUserDetail->pm_collage_name}} {{$user->getUserDetail->pm_collage_address }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-5">
                        <div class="">
                            <span class="">Date of Birth :</span>
                            <span class="">{{ date('d-m-Y',strtotime($user->date_of_birth))}}</span>
                        </div>
                        <div class="">
                            <span class="">Age :</span>
                            <span class="">{{ $user->age }}</span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="">
                            <span class="">Nationality :</span>
                            <span class="">{{ $user->nationality}}</span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="">
                            <span class="">Sex :</span>
                            <span class="">{{ $user->sex}}</span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="">
                            <span class="">Naminee Name :</span>
                            <span class="">{{ $user->getUserDetail->pm_nominee_name}}</span>
                        </div>
                        <div class="">
                            <span class="">Naminee Date of birth :</span>
                            <span class="">{{ $user->getUserDetail->pm_nominee_dob}}</span>
                        </div>
                        <div class="">
                            <span class="">Naminee Relationship :</span>
                            <span class="">{{ $user->getUserDetail->pm_nominee_relationship}}</span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="">
                            <span class="">Marital Status :</span>
                            <span class="">{{ $user->marital_status}}</span>
                        </div>
                    </div>
					 <div class="mb-5">
                        <div class="">
                            <span class="">Job Type :</span>
                            <span class="">{{ $user->getUserDetail->job_type}}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">Name of organization working with :</span>
                            <span class="">{{ $user->getUserDetail->pm_name_of_org}}</span>
                        </div>
                    </div>
					
					 <div class="mb-5">
                        <div class="">
                            <span class="">Aadhar card number :</span>
                            <span class="">{{ $user->getUserDetail->pm_aadhar_no }}</span>
                        </div>
                        <div class="">
                            <span class="">PAN Card Number :</span>
                            <span class="">{{ $user->getUserDetail->pm_pan_no}}</span>
                        </div>
                        <div class="">
                            <span class="">Bank Name :</span>
                            <span class="">{{ $user->getUserDetail->pm_bank_name}}</span>
                        </div>
						<div class="">
                            <span class="">Bank Account Number :</span>
                            <span class="">{{ $user->getUserDetail->pm_account_no}}</span>
                        </div>
						<div class="">
                            <span class="">Bank IFSC Code :</span>
                            <span class="">{{ $user->getUserDetail->pm_ifsc_code}}</span>
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
                            <span class=""><a target="_blank" href="{{ url("/upload/cheque_photo/")}}/{{$user->getUserDetail->pm_cheque_photo}}">{{ $user->getUserDetail->pm_cheque_photo}}</a></span>
                        </div>
                    </div>
					<div class="mb-5">
                        <div class="">
                            <span class="">Aadhar Card Photo (Front) :</span>
                            <span class=""><a target="_blank" href="{{ url("/upload/aadhar_photo_front/")}}/{{$user->getUserDetail->pm_aadhar_photo_front}}">{{ $user->getUserDetail->pm_aadhar_photo_front}}</a></span>
                        </div>
                    </div>
					<div class="mb-5">
                        <div class="">
                            <span class="">Aadhar Card Photo (Back) :</span>
                            <span class=""><a target="_blank" href="{{ url("/upload/aadhar_photo_back/")}}/{{$user->getUserDetail->pm_aadhar_photo_back}}">{{ $user->getUserDetail->pm_aadhar_photo_back}}</a></span>
                        </div>
                    </div>
					<div class="mb-5">
                        <div class="">
                            <span class="">PAN Card Photo :</span>
                            <span class=""><a target="_blank" href="{{ url("/upload/pan_photo/")}}/{{$user->getUserDetail->pm_pan_photo}}">{{ $user->getUserDetail->pm_pan_photo}}</a></span>
                        </div>
                    </div>
					
					<!--<div class="mb-5">
                        <div class="">
                            <span class="">Certificate :</span>
                            <span class=""><a href="{{ url("/upload/pashumitra_downloaded_certificate/")}}/{{$user->getUserDetail->pm_download_certificate}}">{{ $user->getUserDetail->pm_download_certificate}}</a></span>
                        </div>
                    </div>-->
					
                     
                </div>
                </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection 

