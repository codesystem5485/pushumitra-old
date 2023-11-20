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
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>
                    
                    {{ __('general.cms_create') }}
                    </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.cms_list') }}</a></li>
                    <li class="breadcrumb-item">
                    {{ __('general.cms_create') }}
                    
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
                <form action="{{route('book.store')}" method="post" enctype="multipart/form-data"> 
                    @csrf  
                <div class="body">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">{{ __('general.book') }}* :</span>
                        </div>
                        <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="book_name" value="@if(empty($books)){{old('book_name')}}@else{{$books->book_name}}@endif"placeholder="{{ __('general.enter_book') }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">{{ __('general.book_file') }}* :</span>
                        </div>
                        <input type="file" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="book_file" value="@if(empty($books)){{old('book_name')}}@else{{$books->book_name}}@endif"placeholder="{{ __('general.enter_book') }}">
                        @if(!empty($books->book_file))
                        <a target="_new" href="{{route("book.download",['file_name'=>$books->book_file])}}" >Download PDF</a>
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
<script src="{{asset('admin/assets/vendor/select2/select2.min.js')}}"></script> 
<script>
    $(".select2").select2();
</script>
@endpush
