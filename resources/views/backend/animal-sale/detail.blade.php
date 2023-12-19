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
                    {{ __('general.animal-sale_details')}}
                   </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.animal-sale_list')}}</a></li>
                    <li class="breadcrumb-item">
                    {{ __('general.animal-sale_details')}}
                    
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
                            <span class="">{{ __('general.UID_number') }} :</span>
                            <span class="">{{ $animalsale->UID_number}}</span>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.species') }} :</span>
                            <span class="">{{ $animalsale->species_name }}</span>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.breed') }} :</span>
                            <span class="">{{ $animalsale->breed_name }}</span>
                        </div>
                    </div>
                    
					 <div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.age') }} :</span>
                            <span class="">{{ $animalsale->age }}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.sex') }} :</span>
                            <span class="">{{ $animalsale->sex }}</span>
                        </div>
                    </div>
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.contact_name_of_owner') }} :</span>
                            <span class="">{{ $animalsale->contact_name_of_owner }}</span>
                        </div>
                    </div>
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.contact_number_of_owner') }} :</span>
                            <span class="">{{ $animalsale->contact_number_of_owner }}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.price') }} :</span>
                            <span class="">{{ $animalsale->price }}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">Address :</span>
                            <span class="">{{ $animalsale->address_line_1." ".$animalsale->state.", ".$animalsale->city_town." ".$animalsale->taluka." ".$animalsale->district.", ".$animalsale->pincode }}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.description') }} :</span>
                            <span class="">{{ $animalsale->description }}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.added_by') }} :</span>
                            <span class="">{{ $animalsale->pm_code }}</span>
                        </div>
                    </div>
					
					 <div class="input_wrapper input-group mb-3">
                    @if(!empty($animalimages))
                        @if(count($animalimages))
                            
                                <div class="input-group mb-10" style="align:left;">
								@foreach($animalimages as $value)
                                    <img height="100" width="100"style="margin-left:10px;" src="{{ url("/upload/animalsale/")}}/{{$value->image_name}}" />
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

