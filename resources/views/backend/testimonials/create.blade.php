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
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>@if(!empty($testimonials))
                    {{ __('general.testimonial_edit') }}
                    @else
                    {{ __('general.testimonial_create') }}
                    @endif </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.testimonial_list') }}</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($testimonials))
                    {{ __('general.testimonial_edit') }}
                    @else
                    {{ __('general.testimonial_create') }}
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
                <form action="@if(empty($testimonials)){{route('testimonials.store')}}@else{{route('testimonials.update',['id' => $testimonials->id])}}@endif" method="post" enctype="multipart/form-data"> 
                    @csrf  
                <div class="body">
                   
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.testimonial_name') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="testimonial_name" value="@if(empty($testimonials)){{old('testimonial_name')}}@else{{$testimonials->testimonial_name}}@endif"placeholder="{{ __('general.testimonial_name') }}">
                    </div>
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.testimonial_designation') }}* :</span>
                        </div>
                        <input type="text" class="form-control"  aria-describedby="basic-addon3" name="testimonial_designation" value="@if(empty($testimonials)){{old('testimonial_designation')}}@else{{$testimonials->testimonial_designation}}@endif"placeholder="{{ __('general.testimonial_designation') }}">
                    </div>
					
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.testimonial_message') }}* :</span>
                        </div>
                        <textarea type="text" class="form-control"  aria-describedby="basic-addon3" name="testimonial_message" placeholder="{{ __('general.testimonial_message') }}">@if(empty($testimonials)){{old('testimonial_message')}}@else{{$testimonials->testimonial_message}}@endif</textarea>
                    </div>
					
					
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{ __('general.testimonial_photo') }} :</span>
                        </div>
                        <input type="file" class="form-control" aria-describedby="basic-addon3" name="testimonial_photo" placeholder="{{ __('general.testimonial_photo') }}"><br>
                        <div><span>{{ $errors->first('testimonial_photo') }}</span></div>
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

@endpush
