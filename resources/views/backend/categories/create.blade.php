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
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>@if(!empty($species))
                    {{ __('general.categories_edit') }}
                    @else
                    {{ __('general.categories_create') }}
                    @endif </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">Categories List</a></li>
                    <li class="breadcrumb-item">
                    @if(!empty($categories))
                    {{ __('general.categories_edit') }}
                    @else
                    {{ __('general.categories_create') }}
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
                <form action="@if(empty($categories)){{route('categories.store')}}@else{{route('categories.update',['id' => $categories->id])}}@endif" method="post"> 
                    @csrf  
                <div class="body">
				
				 <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >Category* :</span>
                        </div>
                        <select class="form-control"  aria-describedby="basic-addon3" name="parent_category" value="@if(empty($categories)){{old('parent_category')}}@else{{$categories->parent_category}}@endif"> 
                            <option value="" > Select Category </option>
                            @if(!empty($parentcategories))
								@foreach($parentcategories as $cat)
                                    <option @if(old('parent_category')==$cat->id) selected='selected' @endif @if(!empty($categories)) @if($categories->parent_category==$cat->id) selected='selected' @endif  @endif value="{{$cat->id}}">{{$cat->name}}</option>                            
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">Type* :</span>
                        </div>
                        <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="name" value="@if(empty($categories)){{old('name')}}@else{{$categories->name}}@endif"placeholder="Enter Type">
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
