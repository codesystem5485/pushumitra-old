@extends('backend.master')
@section('css')

@endsection 
@section('content')
<style>
 <style>
        .checkbox-container {display: block;}
        .checkbox-container label {font-size:14px;display:inline-block;align-items: center;padding:0 10px 5px 0;}
        .checkbox-container input[type="checkbox"] {margin:0 3px 0 0;}
    </style>
</style>
<div id="main-content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>@if(!empty($gresolutionfiles))
                    {{ __('general.gresolutionfiles_edit') }}
                    @else
                    {{ __('general.gresolutionfiles_create') }}
                    @endif </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.gresolutionfiles_list') }}</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($gresolutionfiles))
                    {{ __('general.gresolutionfiles_edit') }}
                    @else
                    {{ __('general.gresolutionfiles_create') }}
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
                <form action="@if(empty($gresolutionfiles)){{route('gresolutionfiles.store')}}@else{{route('gresolutionfiles.update',['id' => $gresolutionfiles->id])}}@endif" method="post" enctype="multipart/form-data"> 
                    @csrf  
                <div class="body">
					
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">{{ __('general.gresolutionfiles_name') }}* :</span>
                        </div>
                        <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="title" value="@if(empty($gresolutionfiles)){{old('title')}}@else{{$gresolutionfiles->title}}@endif"placeholder="{{ __('general.gresolutionfiles_name') }}">
                    </div>
					 <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.state') }}* :</span>
                        </div>
                        <select id="state" class="form-control"  aria-describedby="basic-addon3" name="state_id" >
                            <option value="">{{ __('general.select_state') }}</option>
                            @foreach($states as $state)
                            <option @if(!empty($gresolutionfiles)) @if($state->state_id == $gresolutionfiles->state_id) selected='selected' @endif @endif state_val="{{$state->state_id}}" value="{{$state->state_id}}">{{$state->state}}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">{{ __('general.gresolutionfiles') }}* :</span>
                        </div>
                        <input type="file" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="gr_file" value="@if(empty($gresolutionfiles)){{old('gr_file')}}@else{{$gresolutionfiles->gr_file}}@endif"placeholder="{{ __('general.enter_gresolutionfiles') }}">
                        @if(!empty($gresolutionfiles->gr_file))
                        <a target="_new" href="{{route("gresolutionfiles.download",['file_name'=>rawurlencode($gresolutionfiles->gr_file)])}}" >Download PDF</a>
                        @endif
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

@endpush
