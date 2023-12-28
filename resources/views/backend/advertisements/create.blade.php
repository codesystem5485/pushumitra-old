@extends('backend.master')
@section('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('/admin/assets/css/bootstrap-datepicker3.min.css')}}">
@endsection 
@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>@if(!empty($advertisements))
                    {{ __('general.advertisement_edit') }}
                    @else
                    {{ __('general.advertisement_create') }}
                    @endif </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.advertisement_list') }}</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($advertisements))
                    {{ __('general.advertisement_edit') }}
                    @else
                    {{ __('general.advertisement_create') }}
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
                <form action="@if(empty($advertisements)){{route('advertisements.store')}}@else{{route('advertisements.update',['id' => $advertisements->id])}}@endif" method="post" enctype="multipart/form-data"> 
                    @csrf  
                <div class="body">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.advertisement_title') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="advertisement_title" value="@if(empty($advertisements)){{old('advertisement_title')}}@else{{$advertisements->advertisement_title}}@endif"placeholder="{{ __('general.advertisement_title') }}">
                    </div>
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.advertisement_startdate') }}* :</span>
                        </div>
						<input data-date-autoclose="true" data-provide="datepicker" type="text" id="advertisement_startdate" class="form-control" aria-describedby="basic-addon3" name="advertisement_startdate" value="@if(empty($advertisements)){{old('advertisement_startdate')}}@else{{$advertisements->advertisement_startdate}}@endif" placeholder="{{ __('general.advertisement_startdate') }}" required><br>
                        
                       </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.advertisement_enddate') }}* :</span>
                        </div>
                        <input data-date-autoclose="true" data-provide="datepicker" type="text" id="advertisement_enddate" class="form-control" aria-describedby="basic-addon3" name="advertisement_enddate" value="@if(empty($advertisements)){{old('advertisement_enddate')}}@else{{$advertisements->advertisement_enddate}}@endif" placeholder="{{ __('general.advertisement_enddate') }}" required><br>
                     </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.advertiser_name') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="advertiser_name" value="@if(empty($advertisements)){{old('advertiser_name')}}@else{{$advertisements->advertiser_name}}@endif"placeholder="{{ __('general.advertiser_name') }}">
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.advertiser_address') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="advertiser_address" value="@if(empty($advertisements)){{old('advertiser_address')}}@else{{$advertisements->advertiser_address}}@endif"placeholder="{{ __('general.advertiser_address') }}">
                    </div>
					
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.advertiser_contactnumber') }}* :</span>
                        </div>
                        <input type="number" class="form-control"  aria-describedby="basic-addon3" name="advertiser_contactnumber" value="@if(empty($advertisements)){{old('advertiser_contactnumber')}}@else{{$advertisements->advertiser_contactnumber}}@endif"placeholder="{{ __('general.advertiser_contactnumber') }}">
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.advertisement_cost') }}* :</span>
                        </div>
                        <input type="number" class="form-control"  aria-describedby="basic-addon3" name="advertisement_cost" value="@if(empty($advertisements)){{old('advertisement_cost')}}@else{{$advertisements->advertisement_cost}}@endif"placeholder="{{ __('general.advertisement_cost') }}">
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.advertisement_cost_paid') }}* :</span>
                        </div>
                        <input type="number" class="form-control"  aria-describedby="basic-addon3" name="advertisement_cost_paid" value="@if(empty($advertisements)){{old('advertisement_cost_paid')}}@else{{$advertisements->advertisement_cost_paid}}@endif"placeholder="{{ __('general.advertisement_cost_paid') }}">
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ __('general.advertisement_app_image') }} :</span>
                        </div>
                        <input type="file" id="advertisement_app_image" class="form-control" aria-describedby="basic-addon3" name="advertisement_app_image" value="@if(empty($advertisements)){{old('advertisement_app_image')}}@elseif(isset($advertisements->getUserDetail)) {{$advertisements->getUserDetail->pm_cheque_photo}}@endif" placeholder="{{ __('general.advertisement_app_image') }}"><br>
                        <div><span>{{ $errors->first('advertisement_app_image') }}</span></div>
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ __('general.advertisement_web_image') }}* :</span>
                        </div>
                        <input type="file" id="advertisement_website_image" class="form-control" aria-describedby="basic-addon3" name="advertisement_website_image" value="@if(empty($advertisements)){{old('advertisement_web_image')}}@elseif(isset($advertisements->advertisement_web_image)) {{$advertisements->advertisement_web_image}}@endif" placeholder="{{ __('general.advertisement_website_image') }}"><br>
                        <div><span>{{ $errors->first('advertisement_web_image') }}</span></div>
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
<script src="{{asset('/admin/assets/js/bootstrap-datepicker.min.js')}}"></script>
<script>
    $(".select2").select2();
</script>
<script>
$(document).ready(function(){
   $("#advertisement_startdate").datepicker();
   $("#advertisement_enddate").datepicker();
});

   $(document).on('click',".removeAnimalImage",function(e){
        e.preventDefault();
        if(confirm("Do you really want to delete this Animal sale image?"))
        {
        var image_val = $(this).attr('image_val');
        var actionurl = webUrl+"/pashumitra/breeder/"+image_val+"/remove";
         $.ajax({
            url: actionurl,
            type: "get",
            dataType: "application/json",
            data: { id: image_val },
            dataType: "JSON",
            success: function (res) {
                // $("input_wrapper").refresh();
                $(".input_wrapper").load(location.href + " .input_wrapper");

                // animal-sale.edit
            },
        });
        }
        else{
            return false;
        }
    })
    //$(document).on event
$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID

    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class="input-group"><input type="file" class="form-control" name="animal_photo[]"/><a href="#" style="align:right;" class="remove_field">Remove</a></div>'); //add input box
        }
    });

    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })

    
});    
</script>
@endpush
