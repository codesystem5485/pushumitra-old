@extends('backend.master')
@section('css')
<style>
   .image{
    height:132px;
    width:150px;
   }
</style>
@endsection 
@section('content') 
<div id="main-content" class="profilepage_2">
   <div class="container-fluid">
      <div class="block-header">
         <div class="row">
            <div class="col-lg-5 col-md-8 col-sm-12">
               <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a> User Detail</h2>
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                  <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">User</a></li>
                  <li class="breadcrumb-item active">User Detail</li>
               </ul>
            </div>
         </div>
      </div>
   </div>
   @if(isset($userDetail))
   <div class="row clearfix">
      <div class="col-lg-4 col-md-12">
         <div class="card profile-header">
            <div class="body">
               @php $path = ''; @endphp
               @isset($userDetail->getUserDetail)    
               @php 
               $path =   public_path('/').'/upload/user/'.$userDetail->getUserDetail->profile_pic;
               @endphp
               @endisset  
               <div class="profile-image"> 
                  @if(!empty($userDetail->getUserDetail->profile_pic) && file_exists($path))
                    <img src="{{$userDetail->getUserDetail->profile_image}}" class="rounded-circle image" alt=""> 
                    @else 
                    <img src="{{url('/')}}/admin/assets/images/broken-image.jpg" class="rounded-circle image" alt=""> 
                  @endif 
               </div>
               <div>
                  <h4 class="m-b-0"><strong>{{$userDetail->name}}</strong></h4>
                  <span><strong>Role:</strong> &nbsp; {{$userDetail->roles[0]->name}}</span>
               </div>
            </div>
         </div>
         <div class="card">
            <div class="body">
               <small class="text-muted">Address: </small>
               <p>
                @isset($userDetail->getUserDetail->city)
                    {{$userDetail->getUserDetail->city}} - {{$userDetail->getUserDetail->state}}
                @endif
               </p>
               <hr>
               <small class="text-muted">Email address: </small>
               <p>
                @isset($userDetail->email)
                    {{$userDetail->email}} 
                @endif
               </p>
               <hr>
               <small class="text-muted">Mobile: </small>
               <p>
               @isset($userDetail->phone_number)
                    {{$userDetail->phone_number}} 
                @endif
               </p>
               <hr>
               <small class="text-muted">Birth Date: </small>
               <p class="m-b-0">
               @isset($userDetail->getUserDetail->dob)
                    {{date('d M Y', strtotime($userDetail->getUserDetail->dob))}} 
                @endif
               </p>
               <hr>
               <small class="text-muted">Social: </small>
               <p><i class="fa fa-twitter m-r-5"></i> twitter.com/example</p>
               <p><i class="fa fa-facebook  m-r-5"></i> facebook.com/example</p>
               <p><i class="fa fa-github m-r-5"></i> github.com/example</p>
               <p><i class="fa fa-instagram m-r-5"></i> instagram.com/example</p>
            </div>
         </div>
      </div>
      <div class="col-lg-5 col-md-12">
         <div class="card">
            <div class="body">
               <ul class="nav nav-tabs-new">
                  <li class="nav-item active"><a class="nav-link" data-toggle="tab" href="#Settings">Detail</a></li>
               </ul>
            </div>
         </div>
         <div class="tab-content padding-0">
            <div class="tab-pane active" id="Settings">
               <div class="card">
                  <div class="body">
                     <h6>Additional Information</h6>
                     <div class="row clearfix">
                        <div class="col-lg-12 col-md-12">
                            <small class="text-muted">Gender: </small>
                            <p class="m-b-0">
                            @isset($userDetail->getUserDetail->gender)
                                    {{$userDetail->getUserDetail->gender}} 
                                @endif
                            </p>
                            <hr>

                            <small class="text-muted">Pan card: </small>
                            <p class="m-b-0">
                            @isset($userDetail->getUserDetail->pan_number)
                                    {{$userDetail->getUserDetail->pan_number}} 
                                @endif
                            </p>
                            <hr>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   @else
   <div class="row clearfix">
      <div class="col-md-12">
         <div class="card profile-header">
            <div class="body">
            <img src="{{url('/')}}/admin/assets/images/not-found.jpg" class="rounded-circle image" alt=""> 
            </div>
         </div>
      </div>
    </div>
   @endisset 
</div>
</div>
@endsection