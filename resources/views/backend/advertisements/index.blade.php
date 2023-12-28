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
                <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>{{ __('general.advertisement_list') }}</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">{{ __('general.advertisement_list') }}</li>
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
                    @can('advertisement-create')
                   <a href="{{$url['createUrl']}}" class="btn btn-info">{{ __('general.advertisement_add') }} </a>
                    @endcan
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover js-basic-example dataTable table-custom">
                            <thead>
                            <tr>
                                <th>{{ __('general.advertisement_title') }}</th>                                
                                <th>{{ __('general.advertiser_name') }}</th>        
                                <th>{{ __('general.advertiser_contactnumber') }}</th>                                
                                <th>{{ __('general.advertisement_startdate') }}</th>                              
                                <th>{{ __('general.advertisement_enddate') }}</th>
								<th>{{ __('general.advertisement_total_cost') }}</th> 
								<th>{{ __('general.advertisement_cost_paid') }}</th>
								<th>{{ __('general.advertisement_balance_cost') }}</th>
								<th>{{ __('general.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($advertisements as $row)
                            <tr>
							@php
							$startdate = '';$enddate='';
							if($row->advertisement_startdate!=''){
								$startdate = date("d-M-Y",strtotime($row->advertisement_startdate));
							}
							if($row->advertisement_enddate!=''){
									$enddate = date("d-M-Y",strtotime($row->advertisement_enddate));
								}
								
								if($row->advertisement_enddate!=''){
									$enddate = date("d-M-Y",strtotime($row->advertisement_enddate));
								}
							@endphp
                                <td>{{$row->advertisement_title}}</td>  
								<td>{{$row->advertiser_name}}</td> 								
                                <td>{{$row->advertiser_contactnumber}}</td>                                 
                                <td>{{$startdate}}</td> 
								<td>{{$enddate}}</td> 
								<td>{{$row->advertisement_total_cost}}</td>
								<td>{{$row->advertisement_cost_paid}}</td>
								<td>{{$row->advertisement_balance_cost}}</td>
                                <td>
                                    @can('advertisement-detail')
                                  <!--  <a href="{{route('advertisements.detail',['id' => $row->id])}}"><button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="{{ __('general.detail') }}"><i class="icon-user" aria-hidden="true"></i> 
                                    </button></a>-->
                                    @endcan

                                    @can('advertisement-edit')
                                    <a href="{{route('advertisements.edit',['id' => $row->id])}}">
                                    <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="{{ __('general.edit') }}"><i class="icon-pencil" aria-hidden="true"></i> 
                                    </button></a>
                                    @endcan

                                    @can('advertisement-delete')
                                    <a href="{{route('advertisements.delete',['id' => $row->id])}}" onclick="return confirm('Do you really want to delete the record(s)?')">
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
