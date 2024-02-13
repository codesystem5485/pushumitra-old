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
                    {{ __('general.breeder_details')}}
                   </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.breeder_list')}}</a></li>
                    <li class="breadcrumb-item">
                    {{ __('general.breeder_details')}}
                    
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
                <form> 
                    @csrf  
                <div class="body">
                    <!-- <label for="basic-url">Your vanity URL</label> -->
                    <div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.breeder_name') }} :</span>
                            <span class="">{{ $breeder->breeder_name}}</span>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.breeder_firm_reg_no') }} :</span>
                            <span class="">{{ $breeder->firm_registration_number }}</span>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.breed') }} :</span>
                            <span class="">{{ $breeder->animal_breed }}</span>
                        </div>
                    </div>
                    
					 <div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.breeder_mobile_number') }} :</span>
                            <span class="">{{ $breeder->mobile_number }}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">Email Id :</span>
                            <span class="">{{ $breeder->email_id }}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.breeder_animal_description') }} :</span>
                            <span class="">{{ $breeder->breeder_animal_description }}</span>
                        </div>
                    </div>
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.age') }} :</span>
                            <span class="">{{ $breeder->age }}</span>
                        </div>
                    </div>
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.breeder_vaccination_done') }} :</span>
                            <span class="">{{ $breeder->vaccination_done }}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.breeder_expected_price') }} :</span>
                            <span class="">{{ $breeder->expected_price }}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">Address :</span>
                            <span class="">{{ $breeder->address_line_1." ".$breeder->state.", ".$breeder->city_town." ".$breeder->taluka." ".$breeder->district.", ".$breeder->pincode }}</span>
                        </div>
                    </div>
					
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.added_by') }} :</span>
                            <span class="">{{ $breeder->user_code }}</span>
                        </div>
                    </div>
					
					 <div class="input_wrapper input-group mb-3">
                    @if(!empty($animalimages))
                        @if(count($animalimages))
                            
                                <div class="input-group mb-10" style="align:left;">
								@foreach($animalimages as $value)
                                    <img height="100" width="100"style="margin-left:10px;" src="{{ url("/upload/breeder/")}}/{{$value->image_name}}" />
                                 @endforeach
                                </div>
                            
                        @endif
                    @endif
                </div>
                   
                </div>
                </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection 

