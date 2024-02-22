@extends('backend.master')

@section('content')
<style>
        .card-count{width: 100%;text-align: center;}
        .card-count .counts{display:flex;justify-content: space-around;}
    </style>
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
		 @if(auth()->user()->can('dashboard'))
         <div class="row clearfix">
		 
		 @if($userrole == 'Partner')
                  <div class="col-lg-12 col-md-12" style="text-align:center;vertical-align:center;">
                  
								
					  <div class="card overflowhidden col-md-3">
                       <div class="body card-count">
						<div class="counts">
							<h3><a href="#">{{$pashumitraCount}}</a></h3>
							<h3><a href="#"> {{$totalpashumitraCount}}</a></h3>
						</div>
						<span>Pashumitra</span>
						</div>
						<div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                    </div>

					<div class="card overflowhidden col-md-3">
                        <div class="body card-count">
						<div class="counts">
                           <h3><a href="#">{{$registerVetCount}}</a></h3>
						   <h3><a href="#">{{$totalregisterVetCount}}</a></h3>
						   </div>
                           <span>Registered-vet</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$animalOwnerCount}}</a></h3>
						   <h3><a href="#">{{$totalanimalOwnerCount}}</a></h3>
						   </div>
                           <span>Animal owner</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$animalCount}}</a></h3>
						   <h3><a href="#">{{$totalAnimalsCount}}</a></h3>
						   </div>
                           <span>Animals </span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$animalSaleCount}}</a></h3>
						   <h3><a href="#">{{$totalanimalSaleCount}}</a></h3>
						    </div>
                           <span>Animal for sale</span>
						 
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$productSaleCount}}</a></h3>
						   <h3><a href="#">{{$totalproductSaleCount}}</a></h3>
						   </div>
                           <span>Product for sale</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$chemistCount}}</a></h3>
						   <h3><a href="#">{{$totalchemistCount}}</a></h3>
						   </div>
                           <span>Chemist</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$breederCount}}</a></h3>
						   <h3><a href="#">{{$totalbreederCount}}</a></h3>
						   </div>
                           <span>Breeders</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$transporterCount}}</a></h3>
						   <h3><a href="#">{{$totaltransporterCount}}</a></h3>
						    </div>
                           <span>Transporter</span>
						 
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$hospitalCount}}</a></h3>
						    <h3><a href="#">{{$totalhospitalCount}}</a></h3>
							</div>
                           <span>Vet Hospitals</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$supplierCount}}</a></h3>
						   <h3><a href="#">{{$totalsupplierCount}}</a></h3>
						   </div>
                           <span>Suppliers</span>
						  </div>
                        
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$dogShelterCount}}</a></h3>
						   <h3><a href="#">{{$totaldogShelterCount}}</a></h3>
						    </div>
                           <span>Dog Shelters</span>
						 
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$trainingCenterCount}}</a></h3>
						   <h3><a href="#">{{$totaltrainingCenterCount}}</a></h3>
						    </div>
                           <span>Training Centers</span>
						 
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$institutionCount}}</a></h3>
						   <h3><a href="#">{{$totalinstitutionCount}}</a></h3>
						   </div>
                           <span>Institutions</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$farmCount}}</a></h3>
						   <h3><a href="#">{{$totalfarmCount}}</a></h3>
						    </div>
                           <span>Farms</span>
						 
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$shopCount}}</a></h3>
						    <h3><a href="#">{{$totalshopCount}}</a></h3>
							</div>
                           <span>Shops</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$milkCollectionCount}}</a></h3>
						   <h3><a href="#">{{$totalmilkCollectionCount}}</a></h3>
						    </div>
                           <span>Milk Collections</span>
						 
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$poultryCount}}</a></h3>
						   <h3><a href="#">{{$totalpoultryCount}}</a></h3>
						   </div>
                           <span>Poultry Hatchery</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="#">{{$panjarpolCount}}</a></h3>
						   <h3><a href="#">{{$totalpanjarpolCount}}</a></h3>
						    </div>
                           <span>Panjarpol</span>
						 
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					
				  </div>
                  
		@else
			<div class="col-lg-12 col-md-12" style="text-align:center;vertical-align:center;">
                  
								
					  <div class="card overflowhidden col-md-3">
                       <div class="body card-count">
						<div class="counts">
							<h3><a href="{{url('pashumitra/pashumitra')}}">{{$pashumitraCount}}</a></h3>
							<h3><a href="{{url('pashumitra/pashumitra')}}"> {{$totalpashumitraCount}}</a></h3>
						</div>
						<span>Pashumitra</span>
						</div>
						<div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                    </div>

					<div class="card overflowhidden col-md-3">
                        <div class="body card-count">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/registered-vet')}}">{{$registerVetCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/registered-vet')}}">{{$totalregisterVetCount}}</a></h3>
						   </div>
                           <span>Registered-vet</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/animal-owner')}}">{{$animalOwnerCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/animal-owner')}}">{{$totalanimalOwnerCount}}</a></h3>
						   </div>
                           <span>Animal owner</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/add-animal')}}">{{$animalCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/add-animal')}}">{{$totalAnimalsCount}}</a></h3>
						   </div>
                           <span>Animals </span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/animal-sale')}}">{{$animalSaleCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/animal-sale')}}">{{$totalanimalSaleCount}}</a></h3>
						    </div>
                           <span>Animal for sale</span>
						 
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/product-sale')}}">{{$productSaleCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/product-sale')}}">{{$totalproductSaleCount}}</a></h3>
						   </div>
                           <span>Product for sale</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/chemist')}}">{{$chemistCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/chemist')}}">{{$totalchemistCount}}</a></h3>
						   </div>
                           <span>Chemist</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/breeders')}}">{{$breederCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/chemist')}}">{{$totalbreederCount}}</a></h3>
						   </div>
                           <span>Breeders</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/transporter')}}">{{$transporterCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/transporter')}}">{{$totaltransporterCount}}</a></h3>
						    </div>
                           <span>Transporter</span>
						 
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/hospitals')}}">{{$hospitalCount}}</a></h3>
						    <h3><a href="{{url('pashumitra/hospitals')}}">{{$totalhospitalCount}}</a></h3>
							</div>
                           <span>Vet Hospitals</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/suppliers')}}">{{$supplierCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/suppliers')}}">{{$totalsupplierCount}}</a></h3>
						   </div>
                           <span>Suppliers</span>
						  </div>
                        
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/dogshelters')}}">{{$dogShelterCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/dogshelters')}}">{{$totaldogShelterCount}}</a></h3>
						    </div>
                           <span>Dog Shelters</span>
						 
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/trainingcenters')}}">{{$trainingCenterCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/trainingcenters')}}">{{$totaltrainingCenterCount}}</a></h3>
						    </div>
                           <span>Training Centers</span>
						 
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/institutions')}}">{{$institutionCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/institutions')}}">{{$totalinstitutionCount}}</a></h3>
						   </div>
                           <span>Institutions</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					  <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/farms')}}">{{$farmCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/farms')}}">{{$totalfarmCount}}</a></h3>
						    </div>
                           <span>Farms</span>
						 
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/shops')}}">{{$shopCount}}</a></h3>
						    <h3><a href="{{url('pashumitra/shops')}}">{{$totalshopCount}}</a></h3>
							</div>
                           <span>Shops</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/milkcollections')}}">{{$milkCollectionCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/milkcollections')}}">{{$totalmilkCollectionCount}}</a></h3>
						    </div>
                           <span>Milk Collections</span>
						 
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/poultryhatchery')}}">{{$poultryCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/poultryhatchery')}}">{{$totalpoultryCount}}</a></h3>
						   </div>
                           <span>Poultry Hatchery</span>
						  
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
                        </div>
                     </div>
					 
					 <div class="card overflowhidden col-md-3">
                        <div class="body card-count ">
						<div class="counts">
                           <h3><a href="{{url('pashumitra/panjarpols')}}">{{$panjarpolCount}}</a></h3>
						   <h3><a href="{{url('pashumitra/panjarpols')}}">{{$totalpanjarpolCount}}</a></h3>
						    </div>
                           <span>Panjarpol</span>
						 
                        </div>
                        <div class="progress progress-xs progress-transparent custom-color-purple m-b-0">
                           <div class="progress-bar" data-transitiongoal="67"></div>
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
