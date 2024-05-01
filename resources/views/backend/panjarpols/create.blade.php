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
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>@if(!empty($panjarpols))
                    {{ __('general.panjarpol_edit') }}
                    @else
                    {{ __('general.panjarpol_create') }}
                    @endif </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.panjarpol_list') }}</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($panjarpols))
                    {{ __('general.panjarpol_edit') }}
                    @else
                    {{ __('general.panjarpol_create') }}
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
                <form action="@if(empty($panjarpols)){{route('panjarpols.store')}}@else{{route('panjarpols.update',['id' => $panjarpols->id])}}@endif" method="post" enctype="multipart/form-data"> 
                    @csrf  
                <div class="body">
				
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.panjarpol_name') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="panjarpol_name" value="@if(empty($panjarpols)){{old('panjarpol_name')}}@else{{$panjarpols->panjarpol_name}}@endif"placeholder="{{ __('general.panjarpol_name') }}">
                    </div>
					
					 
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.panjarpol_incharge') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="manager_name" value="@if(empty($panjarpols)){{old('incharge_name')}}@else{{$panjarpols->manager_name}}@endif"placeholder="{{ __('general.panjarpol_incharge') }}">
                    </div>
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >Registration Number :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="registration_number" value="@if(empty($panjarpols)){{old('registration_number')}}@else{{$panjarpols->registration_number}}@endif"
						placeholder="Registration Number">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.mobile_number') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="mobile_number" value="@if(empty($panjarpols)){{old('mobile_number')}}@else{{$panjarpols->mobile_number}}@endif"placeholder="{{ __('general.enter_mobile_number') }}">
                    </div>
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >Email Id :</span>
                        </div>
                        <input type="email" class="form-control"  aria-describedby="basic-addon3" name="email_id" value="@if(empty($panjarpols)){{old('email_id')}}@else{{$panjarpols->email_id}}@endif"placeholder="Enter Email Id">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.address') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="address" value="@if(empty($panjarpols)){{old('address')}}@else{{$panjarpols->address}}@endif"placeholder="{{ __('general.address') }}">
                    </div>
                   
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.state') }}* :</span>
                        </div>
                        <input type="hidden" value="@if(empty($panjarpols)){{old('state_id')}}@else{{$panjarpols->state_id}}@endif" name="state_id" id="state_id" />
                        <select id="state" class="form-control"  aria-describedby="basic-addon3" name="state" >
                            <option value="">{{ __('general.select_state') }}</option>
                            @foreach($states as $state)
                            <option @if(!empty($panjarpols))@if($state->state_id == $panjarpols->state_id) selected='selected' @endif @endif state_val="{{$state->state_id}}" value="{{$state->state}}">{{$state->state}}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ __('general.city') }}* :</span>
                        </div>
                        <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="city_town" value="@if(empty($panjarpols)){{old('city_town')}}@else{{$panjarpols->city_town}}@endif"placeholder="{{ __('general.city') }}" autocomplete="off">
                        
                        <div><span>{{ $errors->first('city_town') }}</span></div>
                    </div>
                   <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ __('general.taluka') }}:</span>
                        </div>
                        <input type="text" id="taluka" class="form-control" aria-describedby="basic-addon3" name="taluka" value="@if(empty($panjarpols)){{old('taluka')}}@else{{$panjarpols->taluka}}@endif"placeholder="{{ __('general.taluka') }}"><br>
                        <div><span>{{ $errors->first('taluka') }}</span></div>
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ __('general.district') }} :</span>
                        </div>
                        <input type="text" id="district" class="form-control" aria-describedby="basic-addon3" name="district" value="@if(empty($panjarpols)){{old('district')}}@else{{$panjarpols->district}}@endif" placeholder="{{ __('general.district') }}"><br>
                        <div><span>{{ $errors->first('disctrict') }}</span></div>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.pincode') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="pincode" value="@if(empty($panjarpols)){{old('pincode')}}@else{{$panjarpols->pincode}}@endif"placeholder="{{ __('general.enter_pincode') }}">
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.description') }} :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="description" value="@if(empty($panjarpols)){{old('description')}}@else{{$panjarpols->description}}@endif"placeholder="{{ __('general.enter_description') }}">
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ __('general.added_by') }}* :</span>
                        </div>
                        <input type="text" id="user_code" class="form-control" aria-describedby="basic-addon3" name="user_code" value="@if(empty($panjarpols)){{old('user_code')}}@else{{$panjarpols->user_code}}@endif"placeholder="{{ __('general.added_by') }}" readonly><br>
                        <div><span>{{ $errors->first('added_by') }}</span></div>
                    </div>
                    <div class="input_fields_wrap input-group mb-3">
                        <div><input type="file" class="form-control" name="panjarpol_photo[]"></div>
                        <div class="input-group-prepend"><button class="add_field_button">Add More Photos</button></div>
                    </div>

                    <div class="input-group mb-2">
                        <input type="submit" class="btn btn-primary" value="Submit" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();"/>
                    </div>

                    <div class="input_wrapper input-group mb-3">
                    @if(!empty($images))
                        @if(count($images))
                            @foreach($images as $value)
                                <div class="input-group mb-2" style="align:left;">
                                    <img height="100" width="100" src="{{ url("/upload/panjarpol/")}}/{{$value->image_name}}" />
                                    <a href="javascript:void(0);" class="removeImage" image_val="{{$value->id}}"> Delete</a>
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
    $(document).on('click',".removeImage",function(e){
        e.preventDefault();
        if(confirm("Do you really want to delete this image?"))
        {
        var image_val = $(this).attr('image_val');
        var actionurl = webUrl+"/panjarpols/"+image_val+"/remove";
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
            $(wrapper).append('<div class="input-group"><input type="file" class="form-control" name="panjarpol_photo[]"/><a href="#" style="align:right;" class="remove_field">Remove</a></div>'); //add input box
        }
    });

    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })

    
}); 

</script>
@endpush
