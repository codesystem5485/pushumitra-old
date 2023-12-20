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
                <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>{{ __('general.breeder_list') }}</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">{{ __('general.breeder_list') }}</li>
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
                    @can('breeder-create')
                  <!--  <a href="{{$url['createUrl']}}" class="btn btn-info">{{ __('general.breeder_add') }} </a>-->
                    @endcan
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover js-basic-example dataTable table-custom">
                            <thead>
                            <tr>
                                <th>{{ __('general.breeder_name') }}</th>                                
                                                                
                                <th>{{ __('general.breed') }}</th>                                
                                                               
                                <th>{{ __('general.added_on') }}</th>                                
                                <th>{{ __('general.added_by') }}</th>                                
                                                               
                                <th>{{ __('general.breeder_mobile') }}</th>                                
                                <th>{{ __('general.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($breeders as $row)
                            <tr>
							@php
							$startdate = '';
							if($row->subscriptionStartDate!=''){
								$startdate = date("d-M-Y",strtotime($row->subscriptionStartDate));
							}
							
							@endphp
                                <td>{{$row->breeder_name}}</td>                                 
                                                               
                                <td>{{$row->animal_breed}}</td>                                 
                                                        
                                <td>{{$startdate}}</td>                                 
                                <td>{{$row->user_code}}</td>                                 
                                <td>{{$row->contact_number_of_owner}}</td>                                 
                                                               
                                <td>
                                    @can('animal-sale-detail')
                                    <a href="{{route('breeders.detail',['id' => $row->id])}}"><button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="{{ __('general.detail') }}"><i class="icon-user" aria-hidden="true"></i> 
                                    </button></a>
                                    @endcan

                                    @can('animal-sale-edit')
                                    <a href="{{route('breeders.edit',['id' => $row->id])}}">
                                    <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="{{ __('general.edit') }}"><i class="icon-pencil" aria-hidden="true"></i> 
                                    </button></a>
                                    @endcan

                                    @can('animal-sale-delete')
                                    <a href="{{route('breeders.delete',['id' => $row->id])}}" onclick="return confirm('Do you really want to delete the record(s)?')">
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
