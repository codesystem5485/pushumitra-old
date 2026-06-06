@extends('backend.master')
@section('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/jquery-datatable/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('/admin/assets/css/bootstrap-datepicker3.min.css')}}">

@endsection 
@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>Payment Details</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">Payment Details</li>
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
		
				   
		<form action="{{route('paymentreport.index')}}" method="post">
        @csrf
        <div class="row g-3">
            <div class="col-auto">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">From Date :</span>
                </div>
                <input data-date-autoclose="true" data-provide="datepicker" type="text" id="from_date"
                    class="form-control" aria-describedby="basic-addon3" name="from_date"
                    value="@if(empty($from_date)){{old('from_date')}}@else{{$from_date}}@endif"
                    placeholder="Enter From Date">
            </div>
            </div>
            <div class="col-auto">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">To Date :</span>
                </div>
                <input data-date-autoclose="true" data-provide="datepicker" type="text" id="to_date"
                    class="form-control" aria-describedby="basic-addon3" name="to_date"
                    value="@if(empty($to_date)){{old('to_date')}}@else{{$to_date}}@endif" placeholder="Enter To Date">
            </div>
            </div>
            <div class="col-auto">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Activities :</span>
                </div>
                <select id="activity" class="form-control" aria-describedby="basic-addon3" name="activity">
                    <option value="">Select Activity</option>
                    @foreach($fees as $row)
                    <option value="{{$row->id}}" @if($selactivity==$row->id) selected @endif>{{$row->name}}</option>
                    @endforeach
                </select>
            </div>
            </div>
            <div class="col-auto">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Reference :</span>
                </div>
                <select id="referenceid" class="form-control" aria-describedby="basic-addon3" name="referenceid">
                    <option value="">Select Reference</option>
                    @foreach($references as $reference)
                    <option value="{{$reference->id}}" @if($selreferenceid==$reference->id) selected @endif>{{$reference->name}}</option>
                    @endforeach
                </select>
            </div>
            </div>
            <div class="col-auto">
                <div class="input-group mb-2">
                    <input type="submit" class="btn btn-primary" value="Search" />
                </div>
            </div>
        </div>
            
       

    </form>
                <div class="body">
                    <div class="row clearfix mb-3">
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <strong>Total Business Added Count:</strong> {{$totalBusinessCount}}
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <strong>Total Business Amount:</strong> {{number_format($totalBusinessAmount, 2)}}
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover js-basic-example dataTable table-custom">
                            <thead>
                            <tr>
                                <th>Added by</th>  
                                <th>Reference</th>
								<th>Activity </th>
								<th>Amount</th>								
                                <th>Payment Date</th>
								<!--<th>Action</th>-->
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($payments as $row)
							@php
							$paymentdate = '';
							if($row->payment_date!=''){
								$paymentdate = date("d-M-Y",strtotime($row->payment_date));
							}
							
							@endphp
                            <tr>
                                <td>{{$row->user_code}}</td>
                                <td>{{$row->reference_name}}</td>
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
<script src="{{asset('/admin/assets/js/bootstrap-datepicker.min.js')}}"></script>
<script>
$(document).ready(function(){
  
   $("#from_date").datepicker();
   $("#to_date").datepicker();
   
});
</script>
@endpush