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
                <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>{{ __('general.testimonial_list') }}</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">{{ __('general.testimonial_list') }}</li>
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
                    @can('testimonial-create')
                   <a href="{{$url['createUrl']}}" class="btn btn-info">{{ __('general.testimonial_add') }} </a>
                    @endcan
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover js-basic-example dataTable table-custom">
                            <thead>
                            <tr>
                                <th>{{ __('general.testimonial_name') }}</th>                                
                                <th>{{ __('general.testimonial_designation') }}</th>        
                                <th>{{ __('general.testimonial_message') }}</th>                                
                                <th>{{ __('general.testimonial_photo') }}</th>                              
                                <th>{{ __('general.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($testimonials as $row)
                            <tr>
							
                                <td>{{$row->testimonial_name}}</td>  
								<td>{{$row->testimonial_designation}}</td> 								
                                <td>{{$row->testimonial_message}}</td>                                 
                                <td>
								
								@if($row->testimonial_photo!='')
									<img src="{{ url("/upload/testimonials/")}}/{{$row->testimonial_photo}}" height="50" width="50"></img></td>
                                
								@endif
								<td>
                                   @can('testimonial-edit')
                                    <a href="{{route('testimonials.edit',['id' => $row->id])}}">
                                    <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="{{ __('general.edit') }}"><i class="icon-pencil" aria-hidden="true"></i> 
                                    </button></a>
                                    @endcan

                                    @can('testimonial-delete')
                                    <a href="{{route('testimonials.delete',['id' => $row->id])}}" onclick="return confirm('Do you really want to delete the record(s)?')">
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
