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
                    {{ __('general.registered_vet_details')}}
                   </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.registered_vet_list')}}</a></li>
                    <li class="breadcrumb-item">
                    {{ __('general.registered_vet_details')}}
                    
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
                        
                            <span class=""><a href="{{route('registered-vet.registeredvet-verify',['id' => $user->id])}}" class="btn btn-info" >Verify Registered-vet</a></span>
                    </div>
						
					@endif
                    <div class="mb-5">
                        <div class="">
                            <span class="">Name :</span>
                            <span class="">{{ $user->full_name }}</span>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="">
                            <span class="">Email ID :</span>
                            <span class="">{{ $user->email }}</span>
                        </div>
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

                    <!--<div class="mb-5">
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
                            <span class="">Speciality :</span>
                            <span class="">@if(isset($user->getUserDetail)) {{ $user->getUserDetail->rv_speciality}}@endif</span>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="">
                            <span class="">Name of state veterinary council :</span>
                            <span class="">@if(isset($user->getUserDetail)) {{ $user->getUserDetail->rv_state_verternity_council}}@endif</span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="">
                            <span class="">Name of state veterinary council number:</span>
                            <span class="">@if(isset($user->getUserDetail)) {{ $user->getUserDetail->rv_state_verternity_council_no}}@endif</span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="">
                            <span class="">Name of organization working with:</span>
                            <span class="">@if(isset($user->getUserDetail)) {{ $user->getUserDetail->rv_name_of_working_org}}@endif</span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="">
                            <span class="">Working organization address:</span>
                            <span class="">@if(isset($user->getUserDetail)) {{ $user->getUserDetail->rv_working_state}}

                            {{ $user->getUserDetail->rv_working_state." ".$user->getUserDetail->rv_working_city_town." ".$user->getUserDetail->rv_working_village.", ".$user->getUserDetail->rv_working_pincode }}
                            @endif </span>
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

