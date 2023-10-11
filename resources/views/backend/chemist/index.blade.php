@extends('backend.master')
@section('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/jquery-datatable/dataTables.bootstrap4.min.css')}}">

@endsection 
@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>{{ __('general.chemist_list') }}</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">{{ __('general.chemist_list') }}</li>
                </ul>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                <div class="header">
                @include('backend.layouts.flash-message')
                    <!-- <h2>Basic Table <small>Basic example without any additional modification classes</small> </h2> -->
                    @can('chemist-create')
                    <a href="{{$url['createUrl']}}" class="btn btn-info">{{ __('general.chemist_add') }} </a>
                    @endcan
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover js-basic-example dataTable table-custom">
                            <thead>
                            <tr>
                                <th>{{ __('general.chemist_shop_name') }}</th>                                
                                <th>{{ __('general.shop_owner_name') }}</th>                                
                                <th>{{ __('general.mobile_number') }}</th>                                
                                <th>{{ __('general.address') }}</th>                                
                                <th>{{ __('general.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($chemist as $type)
                            <tr>
                                <td>{{$type->shop_name}}</td>                                 
                                <td>{{$type->owner_name}}</td>                                 
                                <td>{{$type->mobile_number}}</td>                                 
                                <td>{{$type->address_line_1." ".$type->address_line_2." ".$type->state." ".$type->city_town.", ".$type->village." ".$type->pincode }}</td>                                 
                                <td>
                                    @can('chemist-detail')
                                    <a href="{{route('chemist.detail',['id' => $type->id])}}"><button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="{{ __('general.edit') }}"><i class="icon-user" aria-hidden="true"></i> 
                                    </button></a>
                                    @endcan

                                    @can('chemist-edit')
                                    <a href="{{route('chemist.edit',['id' => $type->id])}}">
                                    <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="{{ __('general.edit') }}"><i class="icon-pencil" aria-hidden="true"></i> 
                                    </button></a>
                                    @endcan

                                    @can('chemist-delete')
                                    <a href="{{route('chemist.delete',['id' => $type->id])}}" onclick="return confirm('Do you really want to delete the record(s)?')">
                                    <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="{{ __('general.remove') }}"><i class="icon-trash" aria-hidden="true"></i>
                                    </button></a>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                           </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
@push('scripts')  
<script src="{{asset('admin/assets/bundles/datatablescripts.bundle.js')}}"></script>
<script src="{{asset('admin/assets/vendor/jquery-datatable/jquery-datatable.js')}}"></script>
@endpush
