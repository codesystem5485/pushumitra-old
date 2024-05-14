@extends('backend.master')

@section('content')
<style>
   .card-count {
      width: 100%;
      display: flex;
      flex-direction: column-reverse;
      text-align: left;
   }
   .card-count .counts {
      display: flex;
      justify-content: space-between;
   }
   .card-count .counts h3{margin:0;}
   .card-count .counts a{
      font-size:32px;
      color: #1a2237;
      line-height: 28px;
      font-weight:500;
      border-bottom:1px dashed #496eae;
   }
   .card-count span{
      font-size:19px;
      color: #496eae;
      display: block;
      padding: 0 0 10px 0;
      font-weight:500;
   }
   .card-count small{
      font-size: 14px;
      display: block;
      color: #1a2237;
      opacity: 0.8;
      margin:0 0 1px 0;
   }
</style>
<div id="main-content">
   <div class="container-fluid">
      <div class="block-header">
         <div class="row">
            <div class="col-lg-12">
                
               <h2>
                   <a href="javascript:void(0);" class="btn btn-xs btn-link text-logo btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a> 
                  Dashboard
               </h2>
               
               <nav aria-label="breadcrumb" class="pb-2 mb-3 border-bottom">
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item text-logo"><a href="{{route('home')}}"><i class="icon-home"></i></a>
                     </li>
                     <li class="breadcrumb-item active">Dashboard</li>
                  </ol>
               </nav>
            </div>
         </div>
      </div>

      @if(auth()->user()->can('dashboard'))
      <div class="">

         @if($userrole == 'Partner')
         <div class="row">

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count">
                     <div class="counts">
                        <h3><a href="#">{{$pashumitraCount}}</a></h3>
                        <h3><a href="#"> {{$totalpashumitraCount}}</a></h3>
                     </div>
                     <span>Pashumitra</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count">
                     <div class="counts">
                        <h3><a href="#">{{$registerVetCount}}</a></h3>
                        <h3><a href="#">{{$totalregisterVetCount}}</a></h3>
                     </div>
                     <span>Registered-vet</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$animalOwnerCount}}</a></h3>
                        <h3><a href="#">{{$totalanimalOwnerCount}}</a></h3>
                     </div>
                     <span>Animal owner</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count">
                     <div class="counts">
                        <h3><a href="#">{{$otheruserCount}}</a></h3>
                        <h3><a href="#"> {{$totalotherCount}}</a></h3>
                     </div>
                     <span>Other Users</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$animalCount}}</a></h3>
                        <h3><a href="#">{{$totalAnimalsCount}}</a></h3>
                     </div>
                     <span>Animals </span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$animalSaleCount}}</a></h3>
                        <h3><a href="#">{{$totalanimalSaleCount}}</a></h3>
                     </div>
                     <span>Animal for sale</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$productSaleCount}}</a></h3>
                        <h3><a href="#">{{$totalproductSaleCount}}</a></h3>
                     </div>
                     <span>Product for sale</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$chemistCount}}</a></h3>
                        <h3><a href="#">{{$totalchemistCount}}</a></h3>
                     </div>
                     <span>Chemist</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$breederCount}}</a></h3>
                        <h3><a href="#">{{$totalbreederCount}}</a></h3>
                     </div>
                     <span>Breeders</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$transporterCount}}</a></h3>
                        <h3><a href="#">{{$totaltransporterCount}}</a></h3>
                     </div>
                     <span>Transporter</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$hospitalCount}}</a></h3>
                        <h3><a href="#">{{$totalhospitalCount}}</a></h3>
                     </div>
                     <span>Vet Hospitals</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$supplierCount}}</a></h3>
                        <h3><a href="#">{{$totalsupplierCount}}</a></h3>
                     </div>
                     <span>Suppliers</span>
                  </div>

                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$dogShelterCount}}</a></h3>
                        <h3><a href="#">{{$totaldogShelterCount}}</a></h3>
                     </div>
                     <span>Shelter</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$trainingCenterCount}}</a></h3>
                        <h3><a href="#">{{$totaltrainingCenterCount}}</a></h3>
                     </div>
                     <span>Training Centers</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$institutionCount}}</a></h3>
                        <h3><a href="#">{{$totalinstitutionCount}}</a></h3>
                     </div>
                     <span>Institutions</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$farmCount}}</a></h3>
                        <h3><a href="#">{{$totalfarmCount}}</a></h3>
                     </div>
                     <span>Farms</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$shopCount}}</a></h3>
                        <h3><a href="#">{{$totalshopCount}}</a></h3>
                     </div>
                     <span>Shops</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$milkCollectionCount}}</a></h3>
                        <h3><a href="#">{{$totalmilkCollectionCount}}</a></h3>
                     </div>
                     <span>Milk Collections</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$poultryCount}}</a></h3>
                        <h3><a href="#">{{$totalpoultryCount}}</a></h3>
                     </div>
                     <span>Poultry Hatchery</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$panjarpolCount}}</a></h3>
                        <h3><a href="#">{{$totalpanjarpolCount}}</a></h3>
                     </div>
                     <span>Panjarpol</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$labCount}}</a></h3>
                        <h3><a href="#">{{$totallabCount}}</a></h3>
                     </div>
                     <span>Labs</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$ngoCount}}</a></h3>
                        <h3><a href="#">{{$totalngoCount}}</a></h3>
                     </div>
                     <span>NGO</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><a href="#">{{$easycareCount}}</a></h3>
                        <h3><a href="#">{{$totaleasycareCount}}</a></h3>
                     </div>
                     <span>Knowledge Sharing</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-purple m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

         </div>

         @else
         <div class="row">

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('pashumitra')}}">{{$pashumitraCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('pashumitra')}}"> {{$totalpashumitraCount}}</a></h3>
                     </div>
                     <span>Pashumitra</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('registered-vet')}}">{{$registerVetCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('registered-vet')}}">{{$totalregisterVetCount}}</a></h3>
                     </div>
                     <span>Registered-vet</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('animal-owner')}}">{{$animalOwnerCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('animal-owner')}}">{{$totalanimalOwnerCount}}</a></h3>
                     </div>
                     <span>Animal owner</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('otheruser')}}">{{$otheruserCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('otheruser')}}"> {{$totalotherCount}}</a></h3>
                     </div>
                     <span>Other Users</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('add-animal')}}">{{$animalCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('add-animal')}}">{{$totalAnimalsCount}}</a></h3>
                     </div>
                     <span>Animals </span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('animal-sale')}}">{{$animalSaleCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('animal-sale')}}">{{$totalanimalSaleCount}}</a></h3>
                     </div>
                     <span>Animal for sale</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('product-sale')}}">{{$productSaleCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('product-sale')}}">{{$totalproductSaleCount}}</a></h3>
                     </div>
                     <span>Product for sale</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('chemist')}}">{{$chemistCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('chemist')}}">{{$totalchemistCount}}</a></h3>
                     </div>
                     <span>Chemist</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('breeders')}}">{{$breederCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('breeders')}}">{{$totalbreederCount}}</a></h3>
                     </div>
                     <span>Breeders</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('transporter')}}">{{$transporterCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('transporter')}}">{{$totaltransporterCount}}</a></h3>
                     </div>
                     <span>Transporter</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('hospitals')}}">{{$hospitalCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('hospitals')}}">{{$totalhospitalCount}}</a></h3>
                     </div>
                     <span>Vet Hospitals</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('suppliers')}}">{{$supplierCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('suppliers')}}">{{$totalsupplierCount}}</a></h3>
                     </div>
                     <span>Suppliers</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('dogshelters')}}">{{$dogShelterCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('dogshelters')}}">{{$totaldogShelterCount}}</a></h3>
                     </div>
                     <span>Shelter</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('trainingcenters')}}">{{$trainingCenterCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('trainingcenters')}}">{{$totaltrainingCenterCount}}</a></h3>
                     </div>
                     <span>Training Centers</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('institutions')}}">{{$institutionCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('institutions')}}">{{$totalinstitutionCount}}</a></h3>
                     </div>
                     <span>Institutions</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('farms')}}">{{$farmCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('farms')}}">{{$totalfarmCount}}</a></h3>
                     </div>
                     <span>Farms</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('shops')}}">{{$shopCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('shops')}}">{{$totalshopCount}}</a></h3>
                     </div>
                     <span>Shops</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('milkcollections')}}">{{$milkCollectionCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('milkcollections')}}">{{$totalmilkCollectionCount}}</a></h3>
                     </div>
                     <span>Milk Collections</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('poultryhatchery')}}">{{$poultryCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('poultryhatchery')}}">{{$totalpoultryCount}}</a></h3>
                     </div>
                     <span>Poultry Hatchery</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('panjarpols')}}">{{$panjarpolCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('panjarpols')}}">{{$totalpanjarpolCount}}</a></h3>
                     </div>
                     <span>Panjarpol</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('labs')}}">{{$labCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('labs')}}">{{$totallabCount}}</a></h3>
                     </div>
                     <span>Labs</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('ngo')}}">{{$ngoCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('ngo')}}">{{$totalngoCount}}</a></h3>
                     </div>
                     <span>NGO</span>
                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

            <div class=" col-md-3">
               <div class="card overflowhidden">
                  <div class="body card-count ">
                     <div class="counts">
                        <h3><small class="today-counts">Today's Counts</small><a href="{{url('easycares')}}">{{$easycareCount}}</a></h3>
                        <h3><small class="total-counts">Total Counts</small><a href="{{url('easycares')}}">{{$totaleasycareCount}}</a></h3>
                     </div>
                     <span>Knowledge Sharing</span>

                  </div>
                  <div class="progress progress-sm progress-transparent custom-color-success bg-logo m-b-0">
                     <div class="progress-bar" data-transitiongoal="67"></div>
                  </div>
               </div>
            </div>

         </div>

         @endif

      </div>
      @endif
   </div>
</div>
</div>
@endsection