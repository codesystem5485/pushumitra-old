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
                    {{ __('general.edit_animal_owner')}}
                    @else
                    {{ __('general.create_animal_owner')}}
                    @endif  </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.animal_owner_list')}}</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($user))
                    {{ __('general.edit_animal_owner')}}
                    @else
                    {{ __('general.create_animal_owner')}}
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
                <form action="@if(empty($user)){{route('animal-owner.store')}}@else{{route('animal-owner.update',['id' => $user->id])}}@endif" method="post" enctype="multipart/form-data"> 
                    @csrf  
                <div class="body">
                    <!-- <label for="basic-url">Your vanity URL</label> -->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Full Name* :</span>
                        </div>
                        <input type="text" class="form-control" id="full_name" aria-describedby="basic-addon3" name="full_name" value="@if(empty($user)){{old('full_name')}}@else{{$user->full_name}}@endif"placeholder="Full Name">
                        <div><span>{{ $errors->first('full_name') }}</span></div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Email :</span>
                        </div>
                        <input type="email" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="email" value="@if(empty($user)){{old('email')}}@else{{$user->email}}@endif"placeholder="Email Id" autocomplete="off">
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
                        <input type="hidden"  id="state_id" name="state_id" value="@if(!empty($user)){{$user->state_id}}@endif" />
                        <select id="state" class="form-control" aria-describedby="basic-addon3" name="state">
                            <option value="">--State--</option> 
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
                        <input type="hidden"  id="city_id" name="city_id" value="@if(!empty($user)){{$user->city_id}}@endif" />
                        <select id="city_town" class="form-control" aria-describedby="basic-addon3" name="city_town">
                            <option value="">{{ __('general.select_city') }}</option>
                        @if(!empty($cities) && !empty($user))
                            @foreach($cities as $city)
                            <option @if($city->city_id==$user->city_id) selected='selected' @endif city_val="{{$city->city_id}}" value="{{$city->city}}">{{$city->city}}</option> 
                            @endforeach
                            @endif
                        </select>
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
                            <span class="input-group-text">Date of Birth :</span>
                        </div>
                        <input data-date-autoclose="true" data-provide="datepicker" type="text" id="date_of_birth" class="form-control" aria-describedby="basic-addon3" name="date_of_birth" value="@if(empty($user)){{old('date_of_birth')}}@else{{$user->date_of_birth}}@endif" placeholder="Date of Birth" required><br>
                        <div><span>{{ $errors->first('date_of_birth') }}</span></div>
                    </div>
					
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
</script>
<script src="{{asset('admin/assets/js/common.js')}}"></script>
<script src="{{asset('/admin/assets/js/bootstrap-datepicker.min.js')}}"></script>
@endpush
