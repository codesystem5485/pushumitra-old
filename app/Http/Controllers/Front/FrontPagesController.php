<?php

namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Models\Books;
use Response;

class FrontPagesController extends Controller
{
    use FileUpload;
    protected $url = '';
    
    public function __construct(){

        
    } 

    /**
     * Home page
     * @return View
     */
    public function index(){
        
        return view('front.index'); 
    }
	
	public function aboutus(){
          
        return view('front.aboutus'); 
    }
	
	public function contactus(){
        
        return view('front.contactus'); 
    }
	
	public function library(){
       // $books = Books::orderBy('id','ASC')->paginate(15);
	   $books = Books::orderBy('id','ASC')->paginate(200);
        return view('front.library',compact('books')); 
    }
	
	public function termsConditions(){
        return view('front.termsconditions'); 
    }
	
	public function getDownload($file_name){
		$file = public_path()."/upload/book/".urldecode($file_name);
        $headers = array('Content-Type: application/pdf',
						'Access-Control-Allow-Origin:*','Access-Control-Allow-Methods:
		GET, POST, PUT, DELETE, OPTIONS');
        return Response :: download($file);
        
       //  return response()->download($file, $file_name, $headers);
        return Response::download($file,$file_name, $headers);
    }
	
}
