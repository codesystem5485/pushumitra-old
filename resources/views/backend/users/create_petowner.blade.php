@extends('backend.master')
@section('css')
@endsection 
@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>Pet Owner Registration</h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">Pet Owner's List</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($user))
                    Edit Pet Owner
                    @else
                    Create Pet Owner
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
                <form action="@if(empty($user)){{route('user.store')}}@else{{route('user.update',['id' => $user->id])}}@endif" method="post"> 
                    @csrf  
                <div class="body">
                    <!-- <label for="basic-url">Your vanity URL</label> -->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">First Name* :</span>
                        </div>
                        <input type="text" class="form-control" id="first_name" aria-describedby="basic-addon3" name="first_name" value="@if(empty($user)){{old('first_name')}}@else{{$user->first_name}}@endif"placeholder="First Name">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Middle Name* :</span>
                        </div>
                        <input type="text" class="form-control" id="middle_name" aria-describedby="basic-addon3" name="middle_name" value="@if(empty($user)){{old('middle_name')}}@else{{$user->middle_name}}@endif"placeholder="Middle Name">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Last Name* :</span>
                        </div>
                        <input type="text" class="form-control" id="last_name" aria-describedby="basic-addon3" name="last_name" value="@if(empty($user)){{old('last_name')}}@else{{$user->last_name}}@endif"placeholder="Last Name">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Email* :</span>
                        </div>
                        <input type="email" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="email" value="@if(empty($user)){{old('email')}}@else{{$user->email}}@endif"placeholder="Email Id" autocomplete="off" required>
                    </div> 
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Mobile Number* :</span>
                        </div>
                        <input type="text" onkeyup="check(); return false;"  id="mobile_number" class="form-control" aria-describedby="basic-addon3" name="mobile_number" value="@if(empty($user)){{old('mobile_number')}}@else{{$user->mobile_number}}@endif"placeholder="Mobile Number" required><br>
                    </div>
                    <span id="message"></span>
                    
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Password*</span>
                        </div>
                        <input type="password" class="form-control"  aria-describedby="basic-addon3" name="password" value=""placeholder="Password"  autocomplete="off">
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Confirm Password*</span>
                        </div>
                        <input type="password" class="form-control"  aria-describedby="basic-addon3" name="confirm_password" value=""placeholder="Confirm password"  autocomplete="off">
                    </div>

                     <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Address Line 1* :</span>
                        </div>
                        <input type="text" id="address_line_1" class="form-control" aria-describedby="basic-addon3" name="address_line_1" value="@if(empty($user)){{old('address_line_1')}}@else{{$user->address_line_1}}@endif"placeholder="Address Line 1" required><br>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Address Line 2* :</span>
                        </div>
                        <input type="text" id="address_line_2" class="form-control" aria-describedby="basic-addon3" name="address_line_2" value="@if(empty($user)){{old('address_line_2')}}@else{{$user->address_line_2}}@endif"placeholder="Address Line 2" required><br>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">State* :</span>
                        </div>
                        <select id="state" class="form-control" aria-describedby="basic-addon3" name="state">
                            <option>--State--</option> 
                            @foreach($states as $state)
                            <option state_val="{{$state->state_id}}" value="{{$state->state_id}}">{{$state->state}}</option> 
                            @endforeach
                        </select>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">City/Town* :</span>
                        </div>
                        <select id="city_town" class="form-control" aria-describedby="basic-addon3" name="city_town">   City/Town  </select>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Village* :</span>
                        </div>
                        <input type="text" id="village" class="form-control" aria-describedby="basic-addon3" name="village" value="@if(empty($user)){{old('village')}}@else{{$user->village}}@endif"placeholder="Village" required><br>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Pincode* :</span>
                        </div>
                        <input type="text" id="pincode" class="form-control" aria-describedby="basic-addon3" name="pincode" value="@if(empty($user)){{old('pincode')}}@else{{$user->pincode}}@endif"placeholder="Pincode" required><br>
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
@endpush
