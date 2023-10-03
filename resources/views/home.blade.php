@extends('backend.master')

@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a> Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ul>
                </div>
                
            </div>
        </div>
         <div class="row clearfix">
                  <div class="col-lg-3 col-md-6">
                     <div class="card overflowhidden">
                        <div class="body">
                           <h3>{{$nTotalUusers}}<i class="icon-user-follow float-right"></i></h3>
                           <span>New Users</span>
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
                  </div>
                
               </div>
    </div>
    </div>
@endsection
