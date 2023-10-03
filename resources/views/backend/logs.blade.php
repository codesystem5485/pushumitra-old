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
                <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>Logs</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">Logs</li>
                </ul>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                <div class="header">
                    <p class="pull-right">
                            <select class="form-control" id="log-type" onChange="refreshTable()">
                                <option value="">Select Log Type </option>
                                <option value="error">Error</option>  
                                <option value="updated">Updated</option>  
                                <option value="created">Created</option>  
                                <option value="default">Default</option>  
                            </select> 
                        </p>
                </div>
                <div class="body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-hover table-custom" id="log_datatable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Log Type</th>
                                <th>Description</th>
                                <th>Properties</th>
                                <th>Created By</th>
                                <th>Created Date</th>
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
<script>
$(document).ready( function () {
    $('#log_datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            "url":"{{ route('log.ajax') }}",
            "type": "GET",
            "data": function(d){
                d.log_type = $("#log-type").val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            { data: 'log_name', name: 'log_name' },
            { data: 'description', name: 'description' },
            { data: 'properties', name: 'properties'},
            { data: 'causer_type', name: 'causer_type' },
            { data: 'created_at', name: 'created_at' }
        ]
    });
});
function refreshTable(){
    $('#log_datatable').each(function() {
        dt = $(this).dataTable();
        dt.fnDraw();
    })
}
</script>
@endpush
