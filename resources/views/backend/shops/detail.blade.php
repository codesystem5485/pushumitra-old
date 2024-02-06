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
                    {{ __('general.shop_details')}}
                   </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.shop_list')}}</a></li>
                    <li class="breadcrumb-item">
                    {{ __('general.shop_details')}}
                    
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
                            <span class="">{{ __('general.shop_name') }} :</span>
                            <span class="">{{ $shops->shop_name}}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.shop_incharge') }} :</span>
                            <span class="">{{ $shops->shop_owner_name}}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.mobile_number') }} :</span>
                            <span class="">{{ $shops->mobile_number }}</span>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.address') }} :</span>
                            <span class="">{{ $shops->address." ".$shops->state.", ".$shops->city_town." ".$shops->taluka." ".$shops->district.", ".$shops->pincode }}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.description') }} :</span>
                            <span class="">{{ $shops->description }}</span>
                        </div>
                    </div>
					
					 <div class="input_wrapper input-group mb-3">
                    @if(!empty($images))
                        @if(count($images))
                            
                                <div class="input-group mb-10" style="align:left;">
								@foreach($images as $value)
                                    <img height="100" width="100"style="margin-left:10px;" src="{{ url("/upload/shops/")}}/{{$value->image_name}}" />
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

