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
                <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>{{ __('general.pashumitra_list') }}</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">{{ __('general.pashumitra_list') }}</li>
                </ul>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                <div class="header">
                @include('backend.layouts.flash-message')
                    @can('pashumitra-create')
                    <a href="{{$url['createUrl']}}" class="btn btn-info">{{ __('general.create_pashumitra') }}</a>
                    @endcan
                    
                    <p class="pull-right">
                        <select disabled="disabled" class="form-control" id="role" onChange="refreshTable()">
                            <option value="">Select Role Wise User </option>
                            @foreach($roles as $role)
                            <option @if ($role->name == "Pashumitra") selected="selected" @endif value="{{$role->name}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                    </p>
                </div>
                <div class="body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-hover table-custom" id="user_datatable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                            </thead>
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
<script src="{{asset('admin/assets/js/common.js')}}"></script>
<script>
    $(document).ready( function () {
       var table =  $('#user_datatable').DataTable({
           processing: true,
           serverSide: true,
            ajax: {
                "url":"{{ route('pashumitra.list') }}",
                "type": "GET",
                "data": function(d){
                    d.role = $("#role").val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                { data: 'first_name', name: 'first_name' },
                { data: 'email', name: 'email' },
                { data: 'mobile_number', name: 'mobile_number' },
                { data: 'roles', name: 'roles',orderable: false, searchable: true},
                { data: 'created_date', name: 'created_date'},
                { data: 'action', name: 'action',orderable: false, 
                searchable: false } 
            ]
        });
    });
    
    function refreshTable(){
        $('#user_datatable').each(function() {
            dt = $(this).dataTable();
            dt.fnDraw();
        })
    }
</script>
@endpush
