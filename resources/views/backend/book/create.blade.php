@extends('backend.master')
@section('css')

@endsection 
@section('content')
<style>
.position-menu-within {
    width: 18em;
    height: 15em;
    background: #eee;
    overflow: auto;
    padding: 2em 0 0 2em;
}

.modal-example .multi-select-menu {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    max-height: 50%;
    min-width: 0;
    overflow: auto;
    border: none;
    border-radius: 0.3em;
    box-shadow: 0 1em 3em rgba(0,0,0,0.4);
}

.modal-example .multi-select-menuitem {
    font-size: 1em;
    padding: 1.5em 2.5em 1.5em 3.5em;
}

.modal-example .multi-select-menuitem + .multi-select-menuitem {
    padding-top: 0;
}

.modal-example .multi-select-menuitem input {
    margin-left: -2.5em;
}

.multi-select-modal {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1;
    background: rgba(0, 0, 0, 0.4);
    display: none;
}

.multi-select-container--open .multi-select-modal {
    display: block;
}

.multi-select-container {
    display: inline-block;
    position: relative;
}

.multi-select-menu {
    position: absolute;
    left: 0;
    top: 0.8em;
    z-index: 1;
    float: left;
    min-width: 100%;
    background: #fff;
    margin: 1em 0;
    border: 1px solid #aaa;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    display: none;
}

.multi-select-menuitem {
    display: block;
    font-size: 0.875em;
    padding: 0.6em 1em 0.6em 30px;
    white-space: nowrap;
}

.multi-select-menuitem--titled:before {
    display: block;
    font-weight: bold;
    content: attr(data-group-title);
    margin: 0 0 0.25em -20px;
}

.multi-select-menuitem--titledsr:before {
    display: block;
    font-weight: bold;
    content: attr(data-group-title);
    border: 0;
    clip: rect(0 0 0 0);
    height: 1px;
    margin: -1px;
    overflow: hidden;
    padding: 0;
    position: absolute;
    width: 1px;
}

.multi-select-menuitem + .multi-select-menuitem {
    padding-top: 0;
}

.multi-select-presets {
    border-bottom: 1px solid #ddd;
}

.multi-select-menuitem input {
    position: absolute;
    margin-top: 0.25em;
    margin-left: -20px;
}

.multi-select-button {
    display: inline-block;
    font-size: 0.875em;
    padding: 0.2em 0.6em;
    max-width: 16em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: -0.5em;
    background-color: #fff;
    border: 1px solid #aaa;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    cursor: default;
}

.multi-select-button:after {
    content: "";
    display: inline-block;
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 0.4em 0.4em 0 0.4em;
    border-color: #999 transparent transparent transparent;
    margin-left: 0.4em;
    vertical-align: 0.1em;
}

.multi-select-container--open .multi-select-menu {
    display: block;
}

.multi-select-container--open .multi-select-button:after {
    border-width: 0 0.4em 0.4em 0.4em;
    border-color: transparent transparent #999 transparent;
}

.multi-select-container--positioned .multi-select-menu {
    /* Avoid border/padding on menu messing with JavaScript width calculation */
    box-sizing: border-box;
}

.multi-select-container--positioned .multi-select-menu label {
    /* Allow labels to line wrap when menu is artificially narrowed */
    white-space: normal;
}

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
							
<input class="form-control" type="checkbox" id="Pashumitra" name="book_role[]" value="Pashumitra" <?php if(in_array('Pashumitra',$chkArr)){ echo $chk;} ?>>
<label for="Pashumitra">Pashumitra</label><br>
<input class="form-control" type="checkbox" id="Registred-vet" name="book_role[]" value="Registred-vet" <?php if(in_array('Registred-vet',$chkArr)){ echo $chk;} ?>>
<label for="Registered-vet">Registered-vet</label><br>
<input class="form-control" type="checkbox" id="Animal-owner" name="book_role[]" value="Animal-owner" <?php if(in_array('Animal-owner',$chkArr)){ echo $chk;} ?>>
<label for="Animal-owner">Animal-owner</label>
<input class="form-control" type="checkbox" id="Guest" name="book_role[]" value="Guest" <?php if(in_array('Guest',$chkArr)){ echo $chk;} ?>>
<label for="Guest">Guest</label><br>

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
