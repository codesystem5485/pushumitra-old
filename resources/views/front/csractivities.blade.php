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
			<h1>CSR Activities</h1>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{url('/home')}}">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page">CSR Activities</li>
				</ol>
			</nav>
		</div>
	</div>
</div>
<div class="contact-pages pt-70 mb-120">
        <div class="container">
            
               <div class="row middle">
					<h1>COMING SOON</h1>
				</div>
            
        </div>
    </div>
@endsection