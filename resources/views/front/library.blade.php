@extends('front.master')
@section('content')
<div class="inner-page-banner">
    <div class="container">
        <div class="banner-content d-flex align-items-center justify-content-between">
            <h1>Library</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/home')}}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Library</li>
                </ol>
            </nav>
        </div>
    </div>
</div>


<div class="h1-story-area two mb-70 pt-70 book">
    <div class="container">
        <div class="tab-content2 table-responsive pt-0">
            <table class="table total-table2">
            <thead>
            <tr>
                <th>Sr No</th>
                <th>Book Name</th>
               <!-- <th>File Nmae</th>-->
                <th>Download</th>
            </tr>
            </thead>
            <tbody>
			@php 
				$i = 1;
			@endphp
			@foreach($books as $index => $book)
			
            <tr>
                
				<td>{{$index + $books->firstItem()}}.</td>
                <td>{{$book->book_name}}</td>
               <!-- <td><i class="bi bi-file-pdf mr-5"></i>(Marathi) (86KB)</td>-->
                <td> @if(!empty($book->book_file))
                @php
               
               $file_name = $book->book_file;
                 
                @endphp
                        <a class="primary-btn1 sm-but" target="_new" target="_blank" href="{{ url('/') }}/upload/book/<?php echo urldecode($file_name);?>" >Download<i class="bi bi-download ml-5"></i></a>
                        <!-- <a class="primary-btn1 sm-but" target="_new" target="_blank" href="{{route("library.download",['file_id'=>$book->id])}}" >Download<i class="bi bi-download ml-5"></i></a>
                        -->
                        @endif
						<!--<a href="" class="primary-btn1 sm-but">Download 
                    <i class="bi bi-download ml-5"></i></a>-->
                </td>
            </tr>
			@php 
				$i++;
			@endphp
			@endforeach
            
            
            </tbody>
        </table>
        </div>
		<div class="paginations-area">
		<ul class="pagination">
        {{ $books->links() }}
		</ul>
		</div>
       
        <!-- <div class="paginations-area">
            <ul class="pagination">
			{{ $books->links() }}
               <li class="page-item"><a class="page-link" href="javascript:void(0)"><i class="bi bi-arrow-left"></i></a></li>
                <li class="page-item active"><a class="page-link" href="javascript:void(0)">1</a></li>
                <li class="page-item"><a class="page-link" href="javascript:void(0)">2</a></li>
                <li class="page-item"><a class="page-link" href="javascript:void(0)">3</a></li>
                <li class="page-item"><a class="page-link" href="javascript:void(0)">4</a></li>
                <li class="page-item"><a class="page-link" href="javascript:void(0)"><i class="bi bi-arrow-right"></i></a></li>
            </ul>
        </div>-->
    </div>
</div>
@endsection