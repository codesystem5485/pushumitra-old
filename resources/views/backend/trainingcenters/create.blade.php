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
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>@if(!empty($trainingcenters))
                    {{ __('general.trainingcenter_edit') }}
                    @else
                    {{ __('general.trainingcenter_create') }}
                    @endif </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.trainingcenter_list') }}</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($trainingcenters))
                    {{ __('general.trainingcenter_edit') }}
                    @else
                    {{ __('general.trainingcenter_create') }}
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
                <form action="@if(empty($trainingcenters)){{route('trainingcenters.store')}}@else{{route('trainingcenters.update',['id' => $trainingcenters->id])}}@endif" method="post" enctype="multipart/form-data"> 
                    @csrf  
                <div class="body">
				
				<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.subcategories') }}* :</span>
                        </div>
                       
                        <select id="sub_category" class="form-control"  aria-describedby="basic-addon3" name="sub_category" >
                            <option value="">{{ __('general.subcategories') }}</option>
                            @foreach($subcategories as $row)
                            <option @if(!empty($trainingcenters))@if($row->id == $trainingcenters->sub_category) selected='selected' @endif @endif  value="{{$row->id}}">{{$row->name}}</option> 
                            @endforeach
                        </select>
                    </div>
					
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.trainingcenter_name') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="training_center_name" value="@if(empty($trainingcenters)){{old('training_center_name')}}@else{{$trainingcenters->training_center_name}}@endif"placeholder="{{ __('general.trainingcenter_name') }}">
                    </div>
					
					 
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.trainingcenter_incharge') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="incharge_name" value="@if(empty($trainingcenters)){{old('incharge_name')}}@else{{$trainingcenters->incharge_name}}@endif"placeholder="{{ __('general.trainingcenter_incharge') }}">
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.trainingcenter_fees') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="fees" value="@if(empty($trainingcenters)){{old('fees')}}@else{{$trainingcenters->fees}}@endif"placeholder="{{ __('general.trainingcenter_fees') }}">
                    </div>
					<?php echo $trainingcenters->type; ?>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.type') }}* :</span>
                        </div>
						<div class="form-control">
						Private
						<input @if(!empty($trainingcenters))@if($trainingcenters->type == 'Private') checked @endif @endif  type="radio" id="Private" name="type" value="Private">
						  Goverment
						  <input @if(!empty($trainingcenters)) @if($trainingcenters->type == 'Goverment') checked @endif @endif type="radio" id="Goverment" name="type" value="Goverment">
						</div>  
					</div>
					
					 <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.trainingcenter_registration_number') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="registration_number" value="@if(empty($trainingcenters)){{old('registration_number')}}@else{{$trainingcenters->registration_number}}@endif"placeholder="{{ __('general.trainingcenters_registration_number') }}">
                    </div>
                   
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.mobile_number') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="mobile_number" value="@if(empty($trainingcenters)){{old('mobile_number')}}@else{{$trainingcenters->mobile_number}}@endif"placeholder="{{ __('general.enter_mobile_number') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.address') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="address" value="@if(empty($trainingcenters)){{old('address')}}@else{{$trainingcenters->address}}@endif"placeholder="{{ __('general.address') }}">
                    </div>
                   
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.state') }}* :</span>
                        </div>
                        <input type="hidden" value="@if(empty($trainingcenters)){{old('state_id')}}@else{{$trainingcenters->state_id}}@endif" name="state_id" id="state_id" />
                        <select id="state" class="form-control"  aria-describedby="basic-addon3" name="state" >
                            <option value="">{{ __('general.select_state') }}</option>
                            @foreach($states as $state)
                            <option @if(!empty($trainingcenters))@if($state->state_id == $trainingcenters->state_id) selected='selected' @endif @endif state_val="{{$state->state_id}}" value="{{$state->state}}">{{$state->state}}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ __('general.city') }}* :</span>
                        </div>
                        <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="city_town" value="@if(empty($trainingcenters)){{old('city_town')}}@else{{$trainingcenters->city_town}}@endif"placeholder="{{ __('general.city') }}" autocomplete="off">
                        
                        <div><span>{{ $errors->first('city_town') }}</span></div>
                    </div>
                   <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ __('general.taluka') }}:</span>
                        </div>
                        <input type="text" id="taluka" class="form-control" aria-describedby="basic-addon3" name="taluka" value="@if(empty($trainingcenters)){{old('taluka')}}@else{{$trainingcenters->taluka}}@endif"placeholder="{{ __('general.taluka') }}"><br>
                        <div><span>{{ $errors->first('taluka') }}</span></div>
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ __('general.district') }} :</span>
                        </div>
                        <input type="text" id="district" class="form-control" aria-describedby="basic-addon3" name="district" value="@if(empty($trainingcenters)){{old('district')}}@else{{$trainingcenters->district}}@endif" placeholder="{{ __('general.district') }}"><br>
                        <div><span>{{ $errors->first('disctrict') }}</span></div>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.pincode') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="pincode" value="@if(empty($trainingcenters)){{old('pincode')}}@else{{$trainingcenters->pincode}}@endif"placeholder="{{ __('general.enter_pincode') }}">
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.description') }} :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="description" value="@if(empty($trainingcenters)){{old('description')}}@else{{$trainingcenters->description}}@endif"placeholder="{{ __('general.enter_description') }}">
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ __('general.added_by') }}* :</span>
                        </div>
                        <input type="text" id="user_code" class="form-control" aria-describedby="basic-addon3" name="user_code" value="@if(empty($trainingcenters)){{old('user_code')}}@else{{$trainingcenters->user_code}}@endif"placeholder="{{ __('general.added_by') }}" readonly><br>
                        <div><span>{{ $errors->first('added_by') }}</span></div>
                    </div>
                    <div class="input_fields_wrap input-group mb-3">
                        <div><input type="file" class="form-control" name="trainingcenter_photo[]"></div>
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
                                    <img height="100" width="100" src="{{ url("/upload/trainingcenters/")}}/{{$value->image_name}}" />
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
        if(confirm("Do you really want to delete this training centers image?"))
        {
        var image_val = $(this).attr('image_val');
        var actionurl = webUrl+"/pashumitra/trainingcenters/"+image_val+"/remove";
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
            $(wrapper).append('<div class="input-group"><input type="file" class="form-control" name="trainingcenter_photo[]"/><a href="#" style="align:right;" class="remove_field">Remove</a></div>'); //add input box
        }
    });

    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })

    
}); 

</script>
@endpush
