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
                <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>{{ __('general.animal-sale_list') }}</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">Deleted Animal Sale List</li>
                </ul>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                <div class="header">
                @include('backend.layouts.flash-message')
                    <a href="{{route('animal-sale.index')}}" class="btn btn-info">Animal Sale List </a>
                </div>
                <div class="body">
				 
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover js-basic-example dataTable table-custom">
                            <thead>
                            <tr>
                                <th>{{ __('general.UID_number') }}</th>                                
                                <th>{{ __('general.breed') }}</th>
								<th>{{ __('general.owner_contact_name') }}</th>                                
                                <th>{{ __('general.owner_contact_number') }}</th>
								<th>{{ __('general.added_by') }}</th>								
                                <th>Delete Reason</th>                                
                                <!--<th>Note</th> -->                               
                                <th>{{ __('general.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($animalsale as $sale)
                            <tr>
							@php
							$startdate = '';$enddate='';
							if($sale->subscriptionStartDate!=''){
								$startdate = date("d-M-Y",strtotime($sale->subscriptionStartDate));
							}
							if($sale->subscriptionEndDate!=''){
									$enddate = date("d-M-Y",strtotime($sale->subscriptionEndDate));
								}
							
							@endphp
                                <td>{{$sale->UID_number}}</td>                                 
                                <td>{{$sale->breed}}</td>                                 
                                <td>{{$sale->contact_name_of_owner}}</td>                                 
                                <td>{{$sale->contact_number_of_owner}}</td>
								<td>{{$sale->user_code}}</td>								
                                <td>{{$sale->delete_reason}}</td> 
								<td>
                                    @can('animal-sale-detail')
                                    <a href="{{route('animal-sale.detail',['id' => $sale->id])}}"><button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="{{ __('general.detail') }}"><i class="icon-user" aria-hidden="true"></i> 
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
<script>
function deleteAnimalsale(id) {
    var ask = window.confirm("Do you really want to delete the record(s)?");
    if (ask) {
		//var id = $(this).attr('id');
		var actionurl = webUrl+"/pashumitra/animal-sale/"+id+"/show_delete";
		window.location.href = actionurl;
	}
}
</script>
@endpush
