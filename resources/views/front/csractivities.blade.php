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
            <h1>Activities List</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Activities List</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="blog-grid-pages pt-50 pb-50 grey-bg">
    <div class="container">
        <div class="row g-lg-4 gy-5 justify-content-center mb-60">
		@foreach($csrActivities as $row)
            <div class="col-lg-4 col-md-6 col-sm-10">
                <div class="h1-blog-card">
                    <div class="blog-img">
                       <!-- <img class="img-fluid" src="{{asset('front/assets/images/bg/product/product-01.png')}}" alt>-->
						@if($row->image_name!='')
                                    <img src="{{ url("/upload/csractivities/")}}/{{$row->image_name}}" alt>
								@else
									<img src="{{asset('front/assets/images/bg/product/product-01.png')}}" alt>
								@endif	
                                </div>
								
                        <div class="category">
                            <a href="{{route("csr-activity-detail",['id'=>$row->id])}}"><!-- <i class="bi bi-calendar-date-fill mr-5"></i> -->{{$row->schedule_date}}</a>
                        </div>
                    </div>
                    <div class="blog-content">
                        <h4><a href="{{route("csr-activity-detail",['id'=>$row->id])}}">{{$row->title}}</a></h4>
                        <div class="blog-meta"><i class="bi bi-geo-alt mr-5"></i>{{$row->address}} {{$row->city_town}} {{$row->state}}</div>
                    </div>
                </div>
				@endforeach   
		@if($csrActivitiescnt==0)
		<div class="row middle">
					<h1>Csr Activities Coming Soon</h1>
				</div>				
            </div>
		 @endif
			<!--
            <div class="col-lg-4 col-md-6 col-sm-10">
                <div class="h1-blog-card">
                    <div class="blog-img">
                        <img class="img-fluid" src="{{asset('front/assets/images/bg/product/product-02.png')}}" alt>
                        <div class="category"> <a href="">August 12, 2022</a></div>
                    </div>
                    <div class="blog-content">
                        <h4><a href="activitie-details.html">Donec venenatis ex id nibh iaculisoni Clonal interdum Curabitur.</a></h4>
                        <div class="blog-meta"><i class="bi bi-geo-alt mr-5"></i>Shahu Maharaj Path, Nashik Road</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-10">
                <div class="h1-blog-card">
                    <div class="blog-img">
                        <img class="img-fluid" src="{{asset('front/assets/images/bg/product/product-03.png')}}" alt>
                        <div class="category"><a href="">August 11, 2022</a></div>
                    </div>
                    <div class="blog-content">
                        <h4><a href="activitie-details.html">Orci varius natoque penatibus etmal dis parturient montes.</a></h4>
                        <div class="blog-meta"><i class="bi bi-geo-alt mr-5"></i>Shahu Maharaj Path, Nashik Road</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-10">
                <div class="h1-blog-card">
                    <div class="blog-img">
                        <img class="img-fluid" src="{{asset('front/assets/images/bg/product/product-04.png')}}" alt>
                        <div class="category"><a href="">August 10, 2022</a></div>
                    </div>
                    <div class="blog-content">
                        <h4><a href="activitie-details.html">gravida ut malesuada in tristique sed eros Nunc sed efficitur.</a></h4>
                        <div class="blog-meta"><i class="bi bi-geo-alt mr-5"></i>Shahu Maharaj Path, Nashik Road</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-10">
                <div class="h1-blog-card">
                    <div class="blog-img">
                        <img class="img-fluid" src="{{asset('front/assets/images/bg/product/product-05.png')}}" alt>
                        <div class="category"><a href="">August 09, 2022</a></div>
                    </div>
                    <div class="blog-content">
                        <h4><a href="activitie-details.html">luctus justo quis feugiat lacus orcha ornare augue Integer.</a></h4>
                        <div class="blog-meta"><i class="bi bi-geo-alt mr-5"></i>Shahu Maharaj Path, Nashik Road</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-10">
                <div class="h1-blog-card">
                    <div class="blog-img">
                        <img class="img-fluid" src="{{asset('front/assets/images/bg/product/product-06.png')}}" alt>
                        <div class="category"><a href="">August 08, 2022</a></div>
                    </div>
                    <div class="blog-content">
                        <h4><a href="activitie-details.html">malesuada nibh Nulla lacinia miegetol bibendum euismod.</a></h4>
                        <div class="blog-meta"><i class="bi bi-geo-alt mr-5"></i>Shahu Maharaj Path, Nashik Road</div>
                        
                    </div>
                </div>
            </div>-->

        </div>
		<!--
        <div class="row">
            <div class="col-lg-12 d-flex justify-content-center">
                <div class="paginations-area">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item"><a class="page-link" href="#"><i
                                        class="bi bi-arrow-left-short"></i></a></li>
                            <li class="page-item active"><a class="page-link" href="#">01</a></li>
                            <li class="page-item"><a class="page-link" href="#">02</a></li>
                            <li class="page-item"><a class="page-link" href="#">03</a></li>
                            <li class="page-item"><a class="page-link" href="#"><i
                                        class="bi bi-arrow-right-short"></i></a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>-->
    </div>
</div>
@endsection