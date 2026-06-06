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
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>@if(!empty($grfiles))
                    {{ __('general.grfiles_edit') }}
                    @else
                    {{ __('general.grfiles_create') }}
                    @endif </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.grfiles_list') }}</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($grfiles))
                    {{ __('general.grfiles_edit') }}
                    @else
                    {{ __('general.grfiles_create') }}
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
                <form action="@if(empty($grfiles)){{route('grfiles.store')}}@else{{route('grfiles.update',['id' => $grfiles->id])}}@endif" method="post" enctype="multipart/form-data"> 
                    @csrf  
                <div class="body">
					
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">{{ __('general.grfiles_name') }}* :</span>
                        </div>
                        <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="title" value="@if(empty($grfiles)){{old('title')}}@else{{$grfiles->title}}@endif"placeholder="{{ __('general.grfiles_name') }}">
                    </div>
					 <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >{{ __('general.state') }}* :</span>
                        </div>
                        <select id="state" class="form-control"  aria-describedby="basic-addon3" name="state_id" >
                            <option value="">{{ __('general.select_state') }}</option>
                            @foreach($states as $state)
                            <option @if(!empty($grfiles)) @if($state->state_id == $grfiles->state_id) selected='selected' @endif @endif state_val="{{$state->state_id}}" value="{{$state->state_id}}">{{$state->state}}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">{{ __('general.grfiles') }}* :</span>
                        </div>
                        <input type="file" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="gr_file" value="@if(empty($grfiles)){{old('gr_file')}}@else{{$grfiles->gr_file}}@endif"placeholder="{{ __('general.enter_grfiles') }}">
                        @if(!empty($grfiles->gr_file))
                        <a target="_new" href="{{route("grfiles.download",['file_name'=>rawurlencode($grfiles->gr_file)])}}" >Download PDF</a>
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
