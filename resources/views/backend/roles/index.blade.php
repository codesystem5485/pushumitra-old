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
                <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>Role List</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">Role List</li>
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
                    @can('role-create')
                    <a href="{{$url['createUrl']}}" class="btn btn-info">Add Role </a>
                    @endcan
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover js-basic-example dataTable table-custom">
                            <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Permissions</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>{{$role->name}}</td> 
                                @php $permission = []; @endphp
                                @if(!empty($role->permissions)) 
                                    @foreach($role->permissions as $value)
                                        @php $permission[] = convert_permission_name($value->name); @endphp
                                    @endforeach
                                @endif 
                                <td>{{implode(',',$permission)}}</td>
                                <td>
                                    @can('role-edit')
                                        <a href="{{route('role.edit',['id' => $role->id])}}">
                                        <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                                        </button></a>
                                    @endcan
                                    @can('role-delete')
                                        <a href="{{route('role.delete',['id' => $role->id])}}" onclick="return confirm('Are you sure want to delete if you delete then it will delete all permission realted to role?')">
                                        <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i>
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
