@extends('backend.master')
@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>@if(!empty($reference)) Edit Reference @else Create Reference @endif</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">References List</a></li>
                        <li class="breadcrumb-item">@if(!empty($reference)) Edit Reference @else Create Reference @endif</li>
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
                    <form action="@if(empty($reference)){{route('references.store')}}@else{{route('references.update',['id' => $reference->id])}}@endif" method="post">
                        @csrf
                        <div class="body">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon3">Name* :</span>
                                </div>
                                <input type="text" class="form-control" aria-describedby="basic-addon3" name="name" value="@if(empty($reference)){{old('name')}}@else{{$reference->name}}@endif" placeholder="Enter Name">
                            </div>

                            @if(!empty($reference))
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon4">Collaboration Code :</span>
                                </div>
                                <input type="text" class="form-control" aria-describedby="basic-addon4" value="{{$reference->collaboration_code}}" readonly>
                            </div>
                            @endif

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon5">Institution Code :</span>
                                </div>
                                <input type="text" class="form-control" aria-describedby="basic-addon5" name="institution_code" value="@if(empty($reference)){{old('institution_code')}}@else{{$reference->institution_code}}@endif" placeholder="Enter Institution Code">
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="active" value="1" @if(empty($reference) || old('active', $reference->active ?? 1)) checked @endif>
                                    Active
                                </label>
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
