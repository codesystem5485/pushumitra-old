@extends('backend.master')
@section('css')
@endsection 
@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="block-header">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>
                    {{ __('general.easycare_details')}}
                   </h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{$url['listUrl']}}">{{ __('general.easycare_list')}}</a></li>
                    <li class="breadcrumb-item">
                    {{ __('general.easycare_details')}}
                    
                    </li>
                </ul>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                <div class="header">
                    @include('backend.layouts.flash-message')
                    <!-- <h2>Role Permissions</h2> -->
                    
                </div> 
                <form> 
                    @csrf  
                <div class="body">
                    <!-- <label for="basic-url">Your vanity URL</label> -->
					@if($easycares->is_verified==0)
						<div class="mb-5">
                        
                            <span class=""><a onclick="this.disabled=true;this.value='Sending, please wait...';" href="{{route('easycares.easycares-verify',['id' => $easycares->id])}}" class="btn btn-info" >Verify</a></span>
                    </div>
						
					@endif
                    <div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.easycare_title') }} :</span>
                            <span class="">{{ $easycares->title}}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.easycare_solutions') }} :</span>
                            <span class="">{{ $easycares->solutions}}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.easycare_education') }} :</span>
                            <span class="">{{ $easycares->education}}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.easycare_link') }} :</span>
                            <span class="">{{ $easycares->link}}</span>
                        </div>
                    </div>
					
					<div class="mb-5">
                        <div class="">
                            <span class="">{{ __('general.added_by') }} :</span>
                            <span class="">{{ $easycares->user_code}}</span>
                        </div>
                    </div>
					
					 <div class="input_wrapper input-group mb-3">
                    @if(!empty($images))
                        @if(count($images))
                            
                                <div class="input-group mb-10" style="align:left;">
								@foreach($images as $value)
                                    <img height="100" width="100"style="margin-left:10px;" src="{{ url("/upload/easycares/")}}/{{$value->image_name}}" />
                                 @endforeach
                                </div>
                            
                        @endif
                    @endif
                </div>
                   
                </div>
                </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@push('scripts') 
<script>
$("a").click(function (event) {
    if ($(this).hasClass("disabled")) {
        event.preventDefault();
    }
    $(this).addClass("disabled");
});
</script>
@endpush