@extends('backend.master')
@section('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/select2/select2.css')}}" />
@endsection 
@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>@if(!empty($chemist))
                    {{ __('general.chemist_edit') }}
                    @else
                    {{ __('general.chemist_create') }}
                    @endif </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.chemist_list') }}</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($chemist))
                    {{ __('general.chemist_edit') }}
                    @else
                    {{ __('general.chemist_create') }}
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
                </div> 
                <form action="@if(empty($chemist)){{route('chemist.store')}}@else{{route('chemist.update',['id' => $chemist->id])}}@endif" method="post"> 
                    @csrf  
                <div class="body">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.chemist_shop_name') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="shop_name" value="@if(empty($chemist)){{old('shop_name')}}@else{{$chemist->shop_name}}@endif"placeholder="{{ __('general.enter_chemist_shop_name') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.shop_owner_name') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="owner_name" value="@if(empty($chemist)){{old('owner_name')}}@else{{$chemist->owner_name}}@endif"placeholder="{{ __('general.enter_shop_owner_name') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.mobile_number') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="mobile_number" value="@if(empty($chemist)){{old('mobile_number')}}@else{{$chemist->mobile_number}}@endif"placeholder="{{ __('general.enter_mobile_number') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.address_line_1') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="address_line_1" value="@if(empty($chemist)){{old('address_line_1')}}@else{{$chemist->address_line_1}}@endif"placeholder="{{ __('general.enter_address_line_1') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.address_line_2') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="address_line_2" value="@if(empty($chemist)){{old('address_line_2')}}@else{{$chemist->address_line_2}}@endif"placeholder="{{ __('general.enter_address_line_2') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.state') }}* :</span>
                        </div>
                        <input type="hidden" value="@if(empty($chemist)){{old('state_id')}}@else{{$chemist->state_id}}@endif" name="state_id" id="state_id" />
                        <select id="state" class="form-control"  aria-describedby="basic-addon3" name="state" >
                            <option value="">{{ __('general.select_state') }}</option>
                            @foreach($states as $state)
                            <option @if($state->state_id == $chemist->state_id) selected='selected' @endif state_val="{{$state->state_id}}" value="{{$state->state}}">{{$state->state}}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.city') }}* :</span>
                        </div>
                        <input type="hidden" value="@if(empty($chemist)){{old('city_id')}}@else{{$chemist->city_id}}@endif" name="city_id" id="city_id" />

                        <select id="city_town" class="form-control"  aria-describedby="basic-addon3" name="city_town" >
                            <option value="">{{ __('general.select_city') }}</option>
                            @if($cities)
                            @foreach($cities as $city)
                            <option @if($city->city_id==$chemist->city_id) selected='selected' @endif city_val="{{$city->city_id}}" value="{{$city->city}}">{{$city->city}}</option> 
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.village') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="village" value="@if(empty($chemist)){{old('village')}}@else{{$chemist->village}}@endif"placeholder="{{ __('general.enter_village') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.pincode') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="pincode" value="@if(empty($chemist)){{old('pincode')}}@else{{$chemist->pincode}}@endif"placeholder="{{ __('general.enter_pincode') }}">
                    </div>
                    <div class="input-group mb-2">
                        <input type="submit" class="btn btn-primary" value="Submit" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();"/>
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
<script src="{{asset('admin/assets/js/common.js')}}"></script>  
<script src="{{asset('admin/assets/vendor/select2/select2.min.js')}}"></script> 
<script>
    $(".select2").select2();
</script>
@endpush
