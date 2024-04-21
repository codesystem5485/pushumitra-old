@extends('backend.master')
@section('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/select2/select2.css')}}" />
@endsection 
@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i>Send Notifications</a></h2>
                <ul class="breadcrumb"> 
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="#">Send Notifications</a></li>
                   
                </ul>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                <div class="header">
                    @include('backend.layouts.flash-message')
                </div> 
                <form action="{{route('sendnotifications.sendnotifications')}}" method="post"> 
                    @csrf  
                <div class="body">
				
				 <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" >User Role* :</span>
                        </div>
                        <select class="form-control" aria-describedby="basic-addon3" name="role" value="" required> 
                            <option value="" >Select User Role </option>
							<option value="Registered-vet">Registered-vet</option>
							<option value="Pashumitra">Pashumitra</option>
							<option value="Animal-owner">Animal-owner</option>
							<option value="Other">Other</option>
                        </select>
                    </div>
					<div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">Title* :</span>
                        </div>
                        <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="title" value=""placeholder="Enter Title"></textarea>
                    </div>
					
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">Message* :</span>
                        </div>
                        <textarea type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" name="message" value=""placeholder="Enter Message"></textarea>
                    </div>
                    <div class="input-group mb-2">
                        <input type="submit" class="btn btn-primary" value="Submit" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();"/>
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
<script src="{{asset('admin/assets/vendor/select2/select2.min.js')}}"></script> 
<script>
    $(".select2").select2();
</script>
@endpush
