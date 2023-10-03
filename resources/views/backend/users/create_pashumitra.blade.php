@extends('backend.master')
@section('css')
@endsection 
@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>User Registration</h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">User List</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($user))
                    Edit User
                    @else
                    Create User
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
                            <span class="input-group-text" id="basic-addon3">Name* :</span>
                        </div>
                        <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="first_name" value="@if(empty($user)){{old('name')}}@else{{$user->name}}@endif"placeholder="Fillup Full Name">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">Email* :</span>
                        </div>
                        <input type="email" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="email" value="@if(empty($user)){{old('email')}}@else{{$user->email}}@endif"placeholder="Fillup Email Address" autocomplete="off" required>
                    </div> 
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">Phone Number* :</span>
                        </div>
                        <input type="text" onkeyup="check(); return false;"  id="mobile" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="mobile_number" value="@if(empty($user)){{old('phone_number')}}@else{{$user->phone_number}}@endif"placeholder="Fillup Phone Number" required><br>
                    </div>
                    <span id="message"></span><br>
                    <b>Note: You can't assign SuperAdmin And User role. </b>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">Role *:</span>
                        </div>
                        <select class="form-control" name="role" required>
                            <option value="">Select Role</option>
                            @if(!empty($roles))  
                                @foreach($roles as $role)
                                    <option value="{{$role->id}}" @if(isset($user->roles[0]) && $user->roles[0]->id == $role->id) selected @endif>{{$role->name}}</option>
                                @endforeach
                            @endif  
                        </select> 
                    </div>
                    <!-- <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">Password</span>
                        </div>
                        <input type="password" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="password" value=""placeholder="Enter password"  autocomplete="off">
                    </div> --> 
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
@endpush
