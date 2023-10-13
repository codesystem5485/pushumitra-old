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
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>@if(!empty($transporter))
                    {{ __('general.transporter_edit') }}
                    @else
                    {{ __('general.transporter_create') }}
                    @endif </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.transporter_list') }}</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($transporter))
                    {{ __('general.transporter_edit') }}
                    @else
                    {{ __('general.transporter_create') }}
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
                <form action="@if(empty($transporter)){{route('transporter.store')}}@else{{route('transporter.update',['id' => $transporter->id])}}@endif" method="post" enctype="multipart/form-data"> 
                    @csrf  
                <div class="body">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.transporter_name') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="transporter_name" value="@if(empty($transporter)){{old('transporter_name')}}@else{{$transporter->transporter_name}}@endif" placeholder="{{ __('general.enter_transporter_name') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.transporter_vehicle_name') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="vehicle_name" value="@if(empty($transporter)){{old('vehicle_name')}}@else{{$transporter->vehicle_name}}@endif"placeholder="{{ __('general.enter_transporter_vehicle_name') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.mobile_number') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="mobile_number" value="@if(empty($transporter)){{old('mobile_number')}}@else{{$transporter->mobile_number}}@endif"placeholder="{{ __('general.enter_mobile_number') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.address_line_1') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="address_line_1" value="@if(empty($transporter)){{old('address_line_1')}}@else{{$transporter->address_line_1}}@endif"placeholder="{{ __('general.enter_address_line_1') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.address_line_2') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="address_line_2" value="@if(empty($transporter)){{old('address_line_2')}}@else{{$transporter->address_line_2}}@endif"placeholder="{{ __('general.enter_address_line_2') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.state') }}* :</span>
                        </div>
                        <input type="hidden" value="@if(empty($transporter)){{old('state_id')}}@else{{$transporter->state_id}}@endif" name="state_id" id="state_id" />
                        <select id="state" class="form-control"  aria-describedby="basic-addon3" name="state" >
                            <option value="">{{ __('general.select_state') }}</option>
                            @foreach($states as $state)
                            <option @if(!empty($transporter)) @if($state->state_id == $transporter->state_id) selected='selected' @endif @endif state_val="{{$state->state_id}}" value="{{$state->state}}">{{$state->state}}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.city') }}* :</span>
                        </div>
                        <input type="hidden" value="@if(empty($transporter)){{old('city_id')}}@else{{$transporter->city_id}}@endif" name="city_id" id="city_id" />

                        <select id="city_town" class="form-control"  aria-describedby="basic-addon3" name="city_town" >
                            <option value="">{{ __('general.select_city') }}</option>
                            @if(!empty($cities))
                            @foreach($cities as $city)
                            <option @if($city->city_id==$transporter->city_id) selected='selected' @endif city_val="{{$city->city_id}}" value="{{$city->city}}">{{$city->city}}</option> 
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.village') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="village" value="@if(empty($transporter)){{old('village')}}@else{{$transporter->village}}@endif"placeholder="{{ __('general.enter_village') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.pincode') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="pincode" value="@if(empty($transporter)){{old('pincode')}}@else{{$transporter->pincode}}@endif"placeholder="{{ __('general.enter_pincode') }}">
                    </div>
                    <div class="input_fields_wrap input-group mb-3">
                        <div><input type="file" class="form-control" name="vehicle_photo[]"></div>
                        <div class="input-group-prepend"><button class="add_field_button">Add More Photos</button></div>
                    </div>

                    <div class="input-group mb-2">
                        <input type="submit" class="btn btn-primary" value="Submit" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();"/>
                    </div>

                    <div class="input_wrapper input-group mb-3">
                    @if(!empty($vehicleimages))
                        @if(count($vehicleimages))
                            @foreach($vehicleimages as $value)
                                <div class="input-group mb-2" style="align:left;">
                                    <img height="100" width="100" src="{{ "/upload/vehicle/"}}{{$value->image_name}}" />
                                    <a href="javascript:void(0);" class="removeTransImage" image_val="{{$value->id}}"> Delete</a>
                                </div>
                            @endforeach
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
@push('scripts') 
<script src="{{asset('admin/assets/js/common.js')}}"></script>  
<script src="{{asset('admin/assets/vendor/select2/select2.min.js')}}"></script> 
<script>
    $(".select2").select2();

    $(document).on('click',".removeTransImage",function(e){
        e.preventDefault();
        if(confirm("Do you really want to delete this vehicle image?"))
        {
        var image_val = $(this).attr('image_val');
        var actionurl = webUrl+"/transporter/"+image_val+"/remove";
         $.ajax({
            url: actionurl,
            type: "get",
            dataType: "application/json",
            data: { id: image_val },
            dataType: "JSON",
            success: function (res) {
                // $("input_wrapper").refresh();
                $(".input_wrapper").load(location.href + " .input_wrapper");

                // product-sale.edit
            },
        });
        }
        else{
            return false;
        }
    });

    $(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID

    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class="input-group"><input type="file" class="form-control" name="vehicle_photo[]"/><a href="#" style="align:right;" class="remove_field">Remove</a></div>'); //add input box
        }
    });

    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })

    
}); 

</script>
@endpush
