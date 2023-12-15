@extends('backend.master')
@section('css')
<link rel="stylesheet" href="{{asset('/admin/assets/css/bootstrap-datepicker3.min.css')}}">
@endsection 
@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a> @if(!empty($user))
                    {{ __('general.edit_pashumitra')}}
                    @else
                    {{ __('general.create_pashumitra')}}
                    @endif  </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.pashumitra_list')}}</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($user))
                    {{ __('general.edit_pashumitra')}}
                    @else
                    {{ __('general.create_pashumitra')}}
                    @endif    
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
                <form action="@if(empty($user)){{route('pashumitra.store')}}@else{{route('pashumitra.update',['id' => $user->id])}}@endif" method="post" enctype="multipart/form-data"> 
                    @csrf  
                <div class="body">
                    <!-- <label for="basic-url">Your vanity URL</label> -->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Full Name* :</span>
                        </div>
                        <input type="text" class="form-control" id="full_name" aria-describedby="basic-addon3" name="full_name" value="@if(empty($user)){{old('full_name')}}@else{{$user->full_name}}@endif"placeholder="Full name">
                        <div><span>{{ $errors->first('full_name') }}</span></div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Email :</span>
                        </div>
                        <input type="email" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="email" value="@if(empty($user)){{old('email')}}@else{{$user->email}}@endif"placeholder="Email Id" autocomplete="false">
                        <div><span>{{ $errors->first('email') }}</span></div>
                    </div> 
					
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Mobile Number* :</span>
                        </div>
                        <input type="text" onkeyup="check(); return false;"  id="mobile_number" class="form-control" aria-describedby="basic-addon3" name="mobile_number" value="@if(empty($user)){{old('mobile_number')}}@else{{$user->mobile_number}}@endif"placeholder="Mobile Number" required><br>
                        <div><span>{{ $errors->first('mobile_number') }}</span></div>
                    </div>
                    <span id="message"></span>
                    
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Password*</span>
                        </div>
                        <input type="password" class="form-control"  aria-describedby="basic-addon3" name="password" value=""placeholder="Password"  autocomplete="off">
                        <div><span>{{ $errors->first('password') }}</span></div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Confirm Password*</span>
                        </div>
                        <input type="password" class="form-control"  aria-describedby="basic-addon3" name="confirm_password" value=""placeholder="Confirm password"  autocomplete="off">
                        <div><span>{{ $errors->first('confirm_password') }}</span></div>
                    </div>

                     <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Address Line 1* :</span>
                        </div>
                        <input type="text" id="address_line_1" class="form-control" aria-describedby="basic-addon3" name="address_line_1" value="@if(empty($user)){{old('address_line_1')}}@else{{$user->address_line_1}}@endif"placeholder="Address Line 1" required><br>
                        <div><span>{{ $errors->first('address_line_1') }}</span></div>
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">State* :</span>
                        </div>
                        <input type="hidden"  id="state_id" name="state_id" value="@if(empty($user)){{old('state_id')}}@else{{$user->state_id}}@endif" />
                        <select id="state" class="form-control" aria-describedby="basic-addon3" name="state">
                            <option value="">Select State</option> 
                            @foreach($states as $state)
                            <option @if(!empty($user)) @if($state->state_id == $user->state_id) selected='selected'@endif @endif state_val="{{$state->state_id}}" value="{{$state->state}}">{{$state->state}}</option> 
                            @endforeach
                        </select>
                        <div><span>{{ $errors->first('state') }}</span></div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">City/Town* :</span>
                        </div>
                        <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="city_town" value="@if(empty($user)){{old('city_town')}}@else{{$user->city_town}}@endif"placeholder="City/Town" autocomplete="off">
                        
                        <div><span>{{ $errors->first('city_town') }}</span></div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Taluka:</span>
                        </div>
                        <input type="text" id="taluka" class="form-control" aria-describedby="basic-addon3" name="taluka" value="@if(empty($user)){{old('taluka')}}@else{{$user->taluka}}@endif"placeholder="Taluka"><br>
                        <div><span>{{ $errors->first('taluka') }}</span></div>
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">District :</span>
                        </div>
                        <input type="text" id="district" class="form-control" aria-describedby="basic-addon3" name="district" value="@if(empty($user)){{old('district')}}@else{{$user->district}}@endif" placeholder="District"><br>
                        <div><span>{{ $errors->first('disctrict') }}</span></div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Pincode* :</span>
                        </div>
                        <input type="text" id="pincode" class="form-control" aria-describedby="basic-addon3" name="pincode" value="@if(empty($user)){{old('pincode')}}@else{{$user->pincode}}@endif"placeholder="Pincode" required><br>
                        <div><span>{{ $errors->first('pincode') }}</span></div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Education* :</span>
                        </div>
                        <input type="text" id="education" class="form-control" aria-describedby="basic-addon3" name="education" value="@if(empty($user)){{old('education')}}@else{{$user->education}}@endif"placeholder="Education" required><br>
                        <div><span>{{ $errors->first('education') }}</span></div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Education Certificate* :</span>
                        </div>
                        <input type="file" id="education_certificate" class="form-control" aria-describedby="basic-addon3" name="education_certificate" value="@if(empty($user)){{old('education_certificate')}}@else{{$user->education_certificate}}@endif" placeholder="education_certificate" required><br>
                        <div><span>{{ $errors->first('education_certificate') }}</span></div>
                    </div>
                    
                   <!--  
					
					 <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Nationality* :</span>
                        </div>
                        <input type="text" id="nationality" class="form-control" aria-describedby="basic-addon3" name="nationality" value="@if(empty($user)){{old('nationality')}}@else{{$user->nationality}}@endif" placeholder="nationality" required><br>
                        <div><span>{{ $errors->first('nationality') }}</span></div>
                    </div>
					-->
					@php
					if(isset($users))
					{
						if($user->date_of_birth!=''
					}	
					@endphp
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Date of Birth :</span>
                        </div>
                        <input data-date-autoclose="true" data-provide="datepicker" type="text" id="date_of_birth" class="form-control" aria-describedby="basic-addon3" name="date_of_birth" value="@if(empty($user)){{old('date_of_birth')}}@else{{$user->date_of_birth}}@endif" placeholder="Date of Birth" required><br>
                        <div><span>{{ $errors->first('date_of_birth') }}</span></div>
                    </div>

                   <!-- <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Age* :</span>
                        </div>
                        <input type="text" id="age" class="form-control" aria-describedby="basic-addon3" name="age" value="@if(empty($user)){{old('age')}}@else{{$user->age}}@endif" placeholder="Age" required><br>
                        <div><span>{{ $errors->first('age') }}</span></div>
                    </div>-->

                   
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Sex* :</span>
                        </div>
                        <select id="sex" class="form-control" aria-describedby="basic-addon3" name="sex" required>
                            <option value="">--Sex--</option>
                            <option @if(empty($user->sex)) @if(old('sex')=='Male') selected='selected' @endif @elseif($user->sex=='Male') selected='selected' @endif value="Male">Male</option>
                            <option @if(empty($user->sex)) @if(old('sex')=='Female') selected='selected' @endif @elseif($user->sex=='Female') selected='selected' @endif value="Female">Female</option>
                            <option @if(empty($user->sex)) @if(old('sex')=='Other') selected='selected' @endif @elseif($user->sex=='Other') selected='selected' @endif value="Other">Other</option>
                        </select>    
                        <br>
                        <div><span>{{ $errors->first('sex') }}</span></div>
                    </div>
					@php
						$jobtypeArray=  Config::get('constants.jobtype_array');
					@endphp

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Job Type* :</span>
                        </div>
                        <select id="job_type" class="form-control" aria-describedby="basic-addon3" name="job_type" required>
                            <option value="">--Job Type--</option>
							@foreach($jobtypeArray as $item)
							 <option @if(empty($user->getUserDetail->job_type)) @if(old('job_type')==$item) selected='selected' @endif @elseif($user->getUserDetail->job_type==$item) selected='selected' @endif value="{{$item}}">{{$item}}</option>
                            @endforeach
                                
                        </select>    
                        <br>
                        <div><span>{{ $errors->first('job_type') }}</span></div>
                    </div>

                     <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Name of organization working with :</span>
                        </div>
                        <input type="text" id="pm_name_of_org" class="form-control" aria-describedby="basic-addon3" name="pm_name_of_org" value="@if(empty($user)){{old('pm_name_of_org')}}@elseif(isset($user->getUserDetail)){{$user->getUserDetail->pm_name_of_org}}@endif" placeholder="Name of organization working with"><br>
                        <div><span>{{ $errors->first('pm_name_of_org') }}</span></div>
                    </div>

                  <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Nominee Name :</span>
                        </div>
                        <input type="text" id="pm_nominee_name" class="form-control" aria-describedby="basic-addon3" name="pm_nominee_name" value="@if(empty($user)){{old('pm_nominee_name')}}@elseif(isset($user->getUserDetail)){{$user->getUserDetail->pm_nominee_name}}@endif" placeholder="Nominee Name"><br>
                        <div><span>{{ $errors->first('pm_nominee_name') }}</span></div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Nominee Date of Birth :</span>
                        </div>
                        <input data-date-autoclose="true" data-provide="datepicker" type="text" id="pm_nominee_dob" class="form-control" aria-describedby="basic-addon3" name="pm_nominee_dob" value="@if(empty($user)){{old('pm_nominee_dob')}}@elseif(isset($user->getUserDetail)){{ $user->getUserDetail->pm_nominee_dob}} @endif" placeholder="Nominee Date of Birth"><br>
                        <div><span>{{ $errors->first('pm_nominee_dob') }}</span></div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Relation with Nominee :</span>
                        </div>
                        <input type="text" id="pm_nominee_relationship" class="form-control" aria-describedby="basic-addon3" name="pm_nominee_relationship" value="@if(empty($user)){{old('pm_nominee_relationship')}}@elseif(isset($user->getUserDetail)){{ $user->getUserDetail->pm_nominee_relationship}} @endif" placeholder="Relation with Nominee"><br>
                        <div><span>{{ $errors->first('pm_nominee_relationship') }}</span></div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Aadhar card number :</span>
                        </div>
                        <input type="text" id="pm_aadhar_no" class="form-control" aria-describedby="basic-addon3" name="pm_aadhar_no" value="@if(empty($user)){{old('pm_aadhar_no')}}@elseif(isset($user->getUserDetail)){{$user->getUserDetail->pm_aadhar_no}}@endif" placeholder="Aadhar card number"><br>
                        <div><span>{{ $errors->first('pm_aadhar_no') }}</span></div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">PAN Card Number :</span>
                        </div>
                        <input type="text" id="pm_pan_no" class="form-control" aria-describedby="basic-addon3" name="pm_pan_no" value="@if(empty($user)){{old('pm_pan_no')}}@elseif(isset($user->getUserDetail)){{$user->getUserDetail->pm_pan_no}}@endif" placeholder="PAN Card Number"><br>
                        <div><span>{{ $errors->first('pm_pan_no') }}</span></div>
                    </div>

                   <!-- <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">PAN Card Photo* :</span>
                        </div>
                        <input type="file" id="pm_pan_photo" class="form-control" aria-describedby="basic-addon3" name="pm_pan_photo" value="@if(empty($user)){{old('pm_pan_photo')}}@elseif(isset($user->getUserDetail)){{$user->getUserDetail->pm_pan_photo}}@endif" placeholder="pm_pan_photo" required><br>
                        <div><span>{{ $errors->first('pm_pan_photo') }}</span></div>
                    </div>-->

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Bank Name :</span>
                        </div>
                        <input type="text" id="pm_bank_name" class="form-control" aria-describedby="basic-addon3" name="pm_bank_name" value="@if(empty($user)){{old('pm_bank_name')}}@elseif(isset($user->getUserDetail)) {{ $user->getUserDetail->pm_bank_name}} @endif" placeholder="Bank Name"><br>
                        <div><span>{{ $errors->first('pm_bank_name') }}</span></div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Bank Account Number :</span>
                        </div>
                        <input type="text" id="pm_account_no" class="form-control" aria-describedby="basic-addon3" name="pm_account_no" value="@if(empty($user)){{old('pm_account_no')}}@elseif(isset($user->getUserDetail)) {{ $user->getUserDetail->pm_account_no}} @endif" placeholder="Bank Account Number"><br>
                        <div><span>{{ $errors->first('pm_account_no') }}</span></div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Bank IFSC Code :</span>
                        </div>
                        <input type="text" id="pm_ifsc_code" class="form-control" aria-describedby="basic-addon3" name="pm_ifsc_code" value="@if(empty($user)){{old('pm_ifsc_code')}}@elseif(isset($user->getUserDetail)) {{ $user->getUserDetail->pm_ifsc_code}} @endif" placeholder="IFSC code"><br>
                        <div><span>{{ $errors->first('pm_ifsc_code') }}</span></div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Upload Cheque Photo :</span>
                        </div>
                        <input type="file" id="pm_cheque_photo" class="form-control" aria-describedby="basic-addon3" name="pm_cheque_photo" value="@if(empty($user)){{old('pm_cheque_photo')}}@elseif(isset($user->getUserDetail)) {{$user->getUserDetail->pm_cheque_photo}}@endif" placeholder="Cheque Photo"><br>
                        <div><span>{{ $errors->first('pm_cheque_photo') }}</span></div>
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Upload Profile Photo :</span>
                        </div>
                        <input type="file" id="profile_photo" class="form-control" aria-describedby="basic-addon3" name="profile_photo" value="@if(empty($user)){{old('profile_photo')}}@elseif(isset($user->profile_photo)) {{$user->profile_photo}}@endif" placeholder="Profile Photo"><br>
                        <div><span>{{ $errors->first('profile_photo') }}</span></div>
                    </div>
					
                    <div class="input-group mb-2">
                    @php if(isset($aRecord)) {$sButton = 'Update';} else { $sButton = 'Submit';} @endphp
                   
                        <input type="submit" class="btn btn-primary" value="{{$sButton}}" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();"/>
                    </div>
                </div>
                </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection 
@push('scripts') 
<script>
$(document).ready(function(){
   $("#date_of_birth").datepicker();
});
        function check()
        {
            var mobile = document.getElementById('mobile');
            var message = document.getElementById('message');
            var goodColor = "white";
            var badColor = "#FF9B37";
       
            if(mobile.value.length!=10){
                mobile.style.backgroundColor = badColor;
                message.style.color = badColor;
                message.innerHTML = "Required 10 digits, match requested format!"
            }else{
                mobile.style.backgroundColor = goodColor;
                message.innerHTML = '';
            }
        }

        date_of_birth
        
</script>
<script src="{{asset('admin/assets/js/common.js')}}"></script>  
<script src="{{asset('/admin/assets/js/bootstrap-datepicker.min.js')}}"></script>
@endpush
