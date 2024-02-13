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
                    {{ __('general.transporter_details')}}
                   </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.transporter_list')}}</a></li>
                    <li class="breadcrumb-item">
                    {{ __('general.transporter_details')}}
                    
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
                            <span class="">{{ __('general.transporter_name') }} :</span>
                            <span class="">{{ $transporter->transporter_name}}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.transporter_vehicle_name') }} :</span>
                            <span class="">{{ $transporter->vehicle_name}}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.mobile_number') }} :</span>
                            <span class="">{{ $transporter->mobile_number }}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">Email Id :</span>
                            <span class="">{{ $transporter->email_id }}</span>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.address') }} :</span>
                            <span class="">{{ $transporter->address." ".$transporter->state.", ".$transporter->city_town." ".$transporter->taluka." ".$transporter->district.", ".$transporter->pincode }}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.description') }} :</span>
                            <span class="">{{ $transporter->description }}</span>
                        </div>
                    </div>
					
					 <div class="input_wrapper input-group mb-3">
                    @if(!empty($images))
                        @if(count($images))
                            <div class="input-group mb-10" style="align:left;">
								@foreach($images as $value)
                                    <img height="100" width="100"style="margin-left:10px;" src="{{ url("/upload/vehicle/")}}/{{$value->image_name}}" />
                                 @endforeach
                                </div>
                            
                        @endif
                    @endif
                </div>
				
				<div class="input_wrapper input-group mb-3">
                    @if(!empty($images_rcbook))
                        @if(count($images_rcbook))
                            <div class="input-group mb-10" style="align:left;">
								@foreach($images_rcbook as $row)
                                    <img height="100" width="100"style="margin-left:10px;" src="{{ url("/upload/rcbooks/")}}/{{$row->image_name}}" />
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

