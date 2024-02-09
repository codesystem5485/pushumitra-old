@extends('front.master')
@section('content') 
<style>
.middle {
  position: absolute;
  top:35%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
}
</style>

<div class="inner-page-banner">
    <div class="container">
        <div class="banner-content d-flex align-items-center justify-content-between">
            <h1>Activities</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/home')}}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Activities</li>
                </ol>
            </nav>
        </div>
    </div>
</div>


<div class="shop-details-page activities-details pb-50 pt-50">
    <div class="container">
        <div class="row g-lg-4 gy-5 mb-50">
            <div class="col-lg-12">
                <div class="tab-content tab-content1" id="v-pills-tabContent">
				
					@foreach($images as $row)
                    <div class="tab-pane fade active show" id="v-pills-img<?php echo $row->id;?>" role="tabpanel" aria-labelledby="v-pills-img<?php echo $row->id;?>-tab">
                        <img class="img-fluid" src="{{ url("/upload/csractivities/")}}/{{$row->image_name}}" alt>
                    </div>
					@endforeach
					
					@if(count($images)==0)
					<div class="tab-pane fade" id="v-pills-img4" role="tabpanel" aria-labelledby="v-pills-img4-tab">
                        <img class="img-fluid" src="{{asset('front/assets/images/bg/product/product-01.png')}}" alt>
                    </div>	
					@endif
					
					<!--
                    
                    <div class="tab-pane fade" id="v-pills-img4" role="tabpanel" aria-labelledby="v-pills-img4-tab">
                        <img class="img-fluid" src="assets/images/bg/product/product-06.png" alt>
                    </div>
                    <div class="tab-pane fade" id="v-pills-img5" role="tabpanel" aria-labelledby="v-pills-img5-tab">
                        <img class="img-fluid" src="assets/images/bg/product/product-03.png" alt>
                    </div>
-->
                </div>
                <div class="nav nav1 nav-pills active" id="v-pills-tab" role="tablist" aria-orientation="vertical">
				@foreach($images as $row)
                    <button class="nav-link" id="v-pills-img<?php echo $row->id;?>-tab" data-bs-toggle="pill"
                        data-bs-target="#v-pills-img<?php echo $row->id;?>" type="button" role="tab" aria-controls="v-pills-img<?php echo $row->id;?>"
                        aria-selected="false">
                      <!--  <img src="assets/images/bg/product/feature-bg.png" alt>-->
						<img class="img-fluid" src="{{ url("/upload/csractivities/")}}/{{$row->image_name}}" alt>
                    </button>
					@endforeach
					@if(empty($images))
					<div class="tab-pane fade" id="v-pills-img4" role="tabpanel" aria-labelledby="v-pills-img4-tab">
                        <img class="img-fluid" src="{{asset('front/assets/images/bg/product/product-01.png')}}" alt>
                    </div>	
					@endif
					
					<!--
                    <button class="nav-link" id="v-pills-img2-tab" data-bs-toggle="pill"
                        data-bs-target="#v-pills-img2" type="button" role="tab" aria-controls="v-pills-img2"
                        aria-selected="false">
                        <img src="assets/images/bg/slider-3.jpg" alt>
                    </button>
                    <button class="nav-link" id="v-pills-img3-tab" data-bs-toggle="pill"
                        data-bs-target="#v-pills-img3" type="button" role="tab" aria-controls="v-pills-img3"
                        aria-selected="true">
                        <img src="assets/images/bg/product/feature-bg.png" alt>
                    </button>
                    <button class="nav-link" id="v-pills-img4-tab" data-bs-toggle="pill"
                        data-bs-target="#v-pills-img4" type="button" role="tab" aria-controls="v-pills-img4"
                        aria-selected="false">
                        <img src="assets/images/bg/product/product-06.png" alt>
                    </button>
                    <button class="nav-link" id="v-pills-img5-tab" data-bs-toggle="pill"
                        data-bs-target="#v-pills-img5" type="button" role="tab" aria-controls="v-pills-img5"
                        aria-selected="false">
                        <img src="assets/images/bg/product/product-03.png" alt>
                    </button>
                    -->
                </div>
            </div>
          
        </div>
        <div class="row mb-50">
            <div class="col-lg-12">
                <div class="shop-details-content">
                    <h3>{{$csrActivities->title}}</h3>
					@php
						$date = date("d-M-Y",strtotime($csrActivities->schedule_date));
					@endphp
                    <div class="model-number">
                        <span class="d-block">{{$date}}</span>
                        <span class="d-block">{{$csrActivities->address}} {{$csrActivities->city_town}} {{$csrActivities->state}}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="nav nav2 nav  nav-pills" id="v-pills-tab2" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active" id="v-pills-home-tab" data-bs-toggle="pill"
                        data-bs-target="#v-pills-home" type="button" role="tab" aria-controls="v-pills-home"
                        aria-selected="false">Description</button>
                </div>
                <div class="tab-content tab-content2 p-4" id="v-pills-tabContent2">
                    <div class="tab-pane fade active show" id="v-pills-home" role="tabpanel"
                        aria-labelledby="v-pills-home-tab">
                        <div class="description">
                            <p class="para-2 mb-3">{{$csrActivities->description}}</p>
                         </div>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection