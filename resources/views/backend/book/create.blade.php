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
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>@if(!empty($books))
                    {{ __('general.book_edit') }}
                    @else
                    {{ __('general.book_create') }}
                    @endif </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.book_list') }}</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($books))
                    {{ __('general.book_edit') }}
                    @else
                    {{ __('general.book_create') }}
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
                <form action="@if(empty($books)){{route('book.store')}}@else{{route('book.update',['id' => $books->id])}}@endif" method="post" enctype="multipart/form-data"> 
                    @csrf  
                <div class="body">
					<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" id="basic-addon3">Role(s)* :</span>
							</div>
							@php
							$chkArr = array();
							if(!empty($books)){
								$chkArr1 = $books->book_role;
								$chkArr =explode(',',$chkArr1);
							}
							$chk ='checked';
							@endphp
							<div class="checkbox-container">
							

<label for="Pashumitra">Pashumitra <input class="" type="checkbox" id="Pashumitra" name="book_role[]" value="Pashumitra" <?php if(in_array('Pashumitra',$chkArr)){ echo $chk;} ?>></label>

<label for="Registered-vet">Registered-vet <input class="" type="checkbox" id="Registred-vet" name="book_role[]" value="Registred-vet" <?php if(in_array('Registred-vet',$chkArr)){ echo $chk;} ?>></label>

<label for="Animal-owner">Animal-owner <input class="" type="checkbox" id="Animal-owner" name="book_role[]" value="Animal-owner" <?php if(in_array('Animal-owner',$chkArr)){ echo $chk;} ?>></label>

<label for="Guest">Guest <input class="" type="checkbox" id="Guest" name="book_role[]" value="Guest" <?php if(in_array('Guest',$chkArr)){ echo $chk;} ?>></label>

 </div>
 
     

					</div>
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

@endpush
