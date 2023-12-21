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
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>@if(!empty($animal))
                    {{ __('general.add_animal_edit') }}
                    @else
                    {{ __('general.add_animal_create') }}
                    @endif </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.add_animal_list') }}</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($animal))
                    {{ __('general.add_animal_edit') }}
                    @else
                    {{ __('general.add_animal_create') }}
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
                <form action="@if(empty($animal)){{route('add-animal.store')}}@else{{route('add-animal.update',['id' => $animal->id])}}@endif" method="post" enctype="multipart/form-data"> 
                    @csrf  
                <div class="body">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.animal_owner') }}* :</span>
                        </div>
                        <select class="form-control"  aria-describedby="basic-addon3" name="animal_owner" id="animal_owner" value="@if(empty($animal)){{old('shop_name')}}@else{{$animal->shop_name}}@endif"> 
                            <option value="" > {{ __('general.select_animal_owner') }} </option>
                            @if(!empty($animal_owners))
                            @foreach($animal_owners as $own)
                                    <option @if(old('animal_owner')==$own->id) selected='selected' @endif  @if(!empty($animal)) @if($animal->animal_owner==$own->id) selected='selected' @endif  @endif mob_val={{$own->mobile_number}} value="{{$own->id}}">{{$own->first_name." ".$own->middle_name." ".$own->last_name}}</option>                            
                                @endforeach
                            @endif
                            
                        </select>
                    </div>
                   
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.mobile_number') }}* :</span>
                        </div>
                        <input type="text" readonly class="form-control"  aria-describedby="basic-addon3" name="mobile_number" id="mobile_number" value="@if(empty($animal)){{old('mobile_number')}}@else{{$animal->mobile_number}}@endif" placeholder="{{ __('general.enter_mobile_number') }}">
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.add_animal_name') }} :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="name" id="name" value="@if(empty($animal)){{old('name')}}@else{{$animal->name}}@endif" placeholder="{{ __('general.add_animal_name') }}">
                    </div>
                   
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.UID_number') }} :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="UID_number" value="@if(empty($animal)){{old('UID_number')}}@else{{$animal->UID_number}}@endif"placeholder="{{ __('general.enter_UID_number') }}">
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.age') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="age" value="@if(empty($animal)){{old('age')}}@else{{$animal->age}}@endif"placeholder="{{ __('general.enter_age') }}">
                    </div>
					
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.description') }}*:</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="description" value="@if(empty($animal)){{old('description')}}@else{{$animal->description}}@endif"placeholder="{{ __('general.description') }}">
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ __('general.sex') }}* :</span>
                        </div>
                        <select id="sex" class="form-control" aria-describedby="basic-addon3" name="sex" required>
                            <option value="">-- {{ __('general.select_sex') }} --</option>
                            <option @if(empty($animal->sex)) @if(old('sex')=='Male') selected='selected' @endif @elseif($animal->sex=='Male') selected='selected' @endif value="Male">Male</option>
                            <option @if(empty($animal->sex)) @if(old('sex')=='Female') selected='selected' @endif @elseif($animal->sex=='Female') selected='selected' @endif value="Female">Female</option>
                            <option @if(empty($animal->sex)) @if(old('sex')=='Other') selected='selected' @endif @elseif($animal->sex=='Other') selected='selected' @endif value="Other">Other</option>
                        </select>    
                        <br>
                    </div>
                    
                    <div class="input_fields_wrap input-group mb-3">
                        <div><input type="file" class="form-control" name="animal_photo[]"></div>
                        <div class="input-group-prepend"><button class="add_field_button">Add More Photos</button></div>
                    </div>

                    <div class="input-group mb-2">
                        <input type="submit" class="btn btn-primary" value="Submit" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();"/>
                    </div>

                    <div class="input_wrapper input-group mb-3">
                    @if(!empty($animalimages))
                        @if(count($animalimages))
                            @foreach($animalimages as $value)
                                <div class="input-group mb-2" style="align:left;">
                                    <img height="100" width="100" src="{{ url("/upload/animal/")}}/{{$value->image_name}}" />
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

$(document).on('click',"#animal_owner",function(e){
var mob_val = $(this).children("option:selected").attr("mob_val");
$("#mobile_number").val(mob_val);
});
    $(".select2").select2();
    $(document).on('click',".removeImage",function(e){
        e.preventDefault();
        if(confirm("Do you really want to delete this animal image?"))
        {
        var image_val = $(this).attr('image_val');
        var actionurl = webUrl+"/add-animal/"+image_val+"/remove";
         $.ajax({
            url: actionurl,
            type: "get",
            dataType: "application/json",
            data: { id: image_val },
            dataType: "JSON",
            success: function (res) {
                $(".input_wrapper").load(location.href + " .input_wrapper");
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
            $(wrapper).append('<div class="input-group"><input type="file" class="form-control" name="animal_photo[]"/><a href="#" style="align:right;" class="remove_field">Remove</a></div>'); //add input box
        }
    });

    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })

    
}); 

</script>
@endpush
