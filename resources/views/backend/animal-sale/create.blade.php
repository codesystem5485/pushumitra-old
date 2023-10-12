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
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>@if(!empty($animalsale))
                    {{ __('general.animal-sale_edit') }}
                    @else
                    {{ __('general.animal-sale_create') }}
                    @endif </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.animal-sale_list') }}</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($animalsale))
                    {{ __('general.animal-sale_edit') }}
                    @else
                    {{ __('general.animal-sale_create') }}
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
                <form action="@if(empty($animalsale)){{route('animal-sale.store')}}@else{{route('animal-sale.update',['id' => $animalsale->id])}}@endif" method="post"> 
                    @csrf  
                <div class="body">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.UID_number') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="UID_number" value="@if(empty($animalsale)){{old('UID_number')}}@else{{$animalsale->UID_number}}@endif"placeholder="{{ __('general.enter_UID_number') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.species') }}* :</span>
                        </div>
                        <select class="form-control"  aria-describedby="basic-addon3" name="species" value="@if(empty($animalsale)){{old('species')}}@else{{$animalsale->species}}@endif" >
                            <option value="">{{ __('general.select_species') }}</option>
                            @if(!empty($species))
                            @foreach($species as $spe)
                                    <option @if(!empty($animalsale)) @if($animalsale->species==$spe->specie) selected='selected' @endif  @endif value="{{$spe->specie}}">{{$spe->specie}}</option>                            
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.breed') }}* :</span>
                        </div>
                        <select class="form-control"  aria-describedby="basic-addon3" name="breed" value="@if(empty($animalsale)){{old('breed')}}@else{{$animalsale->breed}}@endif" >
                            <option value="">{{ __('general.select_breeds') }}</option>
                            @if(!empty($breed))
                            @foreach($breed as $bre)
                                    <option @if(!empty($animalsale)) @if($animalsale->breed==$bre->breed) selected='selected' @endif  @endif value="{{$bre->breed}}">{{$bre->breed}}</option>                            
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.age') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="age" value="@if(empty($animalsale)){{old('age')}}@else{{$animalsale->age}}@endif"placeholder="{{ __('general.enter_age') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.contact_number_of_owner') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="contact_number_of_owner" value="@if(empty($animalsale)){{old('contact_number_of_owner')}}@else{{$animalsale->contact_number_of_owner}}@endif"placeholder="{{ __('general.enter_contact_number_of_owner') }}">
                    </div>
                 
                   
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.price') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="price" value="@if(empty($animalsale)){{old('price')}}@else{{$animalsale->price}}@endif"placeholder="{{ __('general.enter_price') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.address') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="address" value="@if(empty($animalsale)){{old('address')}}@else{{$animalsale->address}}@endif"placeholder="{{ __('general.enter_address') }}">
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.description') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="description" value="@if(empty($animalsale)){{old('description')}}@else{{$animalsale->description}}@endif"placeholder="{{ __('general.enter_description') }}">
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
