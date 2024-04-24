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
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth">
					<i class="fa fa-arrow-left"></i></a>
                    </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.animal-sale_list') }}</a></li>
                    <li class="breadcrumb-item">
					Reason for Delete
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
                <form action="{{route('animal-sale.delete',['id' => $id])}}" method="post" enctype="multipart/form-data"> 
                @csrf  
                <div class="body">
                   <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Reason For Delete * :</span>
                        </div>
                        <select id="sex" class="form-control" aria-describedby="basic-addon3" name="delete_reason" required>
                            <option value="">-- Reason For Delete --</option>
                            <option @if(empty($animalsale->delete_reason)) @if(old('delete_reason')=='Sold') selected='selected' @endif @elseif($animalsale->delete_reason=='Sold') selected='selected' @endif value="Sold">Sold</option>
                            <option @if(empty($animalsale->delete_reason)) @if(old('delete_reason')=='Death') selected='selected' @endif @elseif($animalsale->delete_reason=='Death') selected='selected' @endif value="Death">Death</option>
                            <option @if(empty($animalsale->delete_reason)) @if(old('delete_reason')=='Other') selected='selected' @endif @elseif($animalsale->delete_reason=='Other') selected='selected' @endif value="Other">Other</option>
                        </select>    
                        <br>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >Note *:</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="delete_note" value="@if(empty($animalsale)){{old('delete_note')}}@else{{$animalsale->delete_note}}@endif"placeholder="Note">
                    </div>
					
                   <div class="input-group mb-2">
                        <input type="submit" class="btn btn-primary" value="Submit" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();"/>
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
<script>
   $(document).on('click',".removeAnimalImage",function(e){
        e.preventDefault();
        if(confirm("Do you really want to delete this Animal sale image?"))
        {
        var image_val = $(this).attr('image_val');
        var actionurl = webUrl+"/animal-sale/"+image_val+"/remove";
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
