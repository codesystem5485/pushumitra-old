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
                <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>Registration Payment Details</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">Registration Payment Details</li>
                </ul>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                <div class="header">
                @include('backend.layouts.flash-message')
				
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover js-basic-example dataTable table-custom">
                            <thead>
                            <tr>
                                <th>User Code</th>
								<th>Name </th>								
								<th>Activity </th>
								<th>Amount</th>								
                                <th>Payment Date</th>
								
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($payments as $row)
							@php
							$user_code='';
							if($row->pm_code!=''){
								$user_code = $row->pm_code;
							}
							if($row->rv_code!=''){
								$user_code = $row->rv_code;
							}
							$paymentdate = '';
							if($row->payment_date!=''){
								$paymentdate = date("d-M-Y",strtotime($row->payment_date));
							}
							
							@endphp
                            <tr>
                                <td>{{$user_code}}</td>
								<td>{{$row->full_name}}</td>
								<td>{{$row->name}}</td>
								<td>{{$row->amount}}</td>
								<td>{{$paymentdate}}</td>								
                               
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
