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
                  <div class="col-lg-12 col-md-12" style="text-align:center;vertical-align:center;">
                   <!--<h2> Coming Soon...</h2>-->
                     <!-- <div class="card overflowhidden">
                        <div class="body">
                           <h3>{{$nTotalUusers}}<i class="icon-user-follow float-right"></i></h3>
                           <span>New Users</span>
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div> -->
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body ">
						<div class="">
                           <h3><a href="{{url('pashumitra/pashumitra')}}">{{$pashumitraCount}}</a></h3>
                           <span>Pashumitra</span>
						  </div>
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>

					<div class="card overflowhidden col-md-3">
                        <div class="body ">
						<div class="">
                           <h3><a href="{{url('pashumitra/registered-vet')}}">{{$registerVetCount}}</a></h3>
                           <span>Registered-vet</span>
						  </div>
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body ">
						<div class="">
                           <h3><a href="{{url('pashumitra/animal-owner')}}">{{$animalOwnerCount}}</a></h3>
                           <span>Animal owner</span>
						  </div>
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body ">
						<div class="">
                           <h3><a href="{{url('pashumitra/animal-sale')}}">{{$animalSaleCount}}</a></h3>
                           <span>Animal for sale</span>
						  </div>
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body ">
						<div class="">
                           <h3><a href="{{url('pashumitra/product-sale')}}">{{$productSaleCount}}</a></h3>
                           <span>Product for sale</span>
						  </div>
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body ">
						<div class="">
                           <h3><a href="{{url('pashumitra/chemist')}}">{{$chemistCount}}</a></h3>
                           <span>Chemist</span>
						  </div>
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body ">
						<div class="">
                           <h3><a href="{{url('pashumitra/breeders')}}">{{$breederCount}}</a></h3>
                           <span>Breeders</span>
						  </div>
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body ">
						<div class="">
                           <h3><a href="{{url('pashumitra/transporter')}}">{{$transporterCount}}</a></h3>
                           <span>Transporter</span>
						  </div>
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body ">
						<div class="">
                           <h3><a href="{{url('pashumitra/hospitals')}}">{{$hospitalCount}}</a></h3>
                           <span>Vet Hospitals</span>
						  </div>
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body ">
						<div class="">
                           <h3><a href="{{url('pashumitra/suppliers')}}">{{$supplierCount}}</a></h3>
                           <span>Supplier</span>
						  </div>
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
                  </div>
                  </div>
                
               </div>
    </div>
    </div>
@endsection
