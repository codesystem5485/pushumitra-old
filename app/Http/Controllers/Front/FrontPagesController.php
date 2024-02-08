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
use App\Models\Testimonials;
use File;
use App\Models\CsrActivities;
use App\Models\CsrActivityImages;

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
        $testimonials = Testimonials::where('status',1)->orderBy('id','DESC')->get();
        return view('front.index',compact('testimonials')); 
    }
	
	public function aboutus(){
          
        return view('front.aboutus'); 
    }
	
	public function contactus(){
        
        return view('front.contactus'); 
    }
	
	public function library(){
       // $books = Books::orderBy('id','ASC')->paginate(15);
	   $books = Books::orderBy('id','ASC')->paginate(20);
       return view('front.library',compact('books')); 
    }
	
	public function termsConditions(){
        return view('front.termsconditions'); 
    }
	
	public function privacyPolicy(){
        return view('front.privacypolicy'); 
    }
	
	public function csrActivities(){
		$csrActivities = CsrActivities::orderBy('id','DESC')->get();
		$csrActivitiescnt = CsrActivities::count();
        return view('front.csractivities',compact('csrActivities','csrActivitiescnt')); 
    }
	
	public function csrActivityDetails(Request $request, $id = ''){
		$csrActivities = CsrActivities::find($id);
		$images = CsrActivityImages::where('csr_activity_id',$csrActivities->id)->get();
        return view('front.csr_activity_detail',compact('csrActivities','images')); 
    }
	
	public function governmentSchemes(){
        return view('front.governmentschemes'); 
    }
	
	public function getDownload($file_id){ 
		
		$books = Books::where('id',$file_id)->first();
		$bookname = $books->book_name.'.pdf';
		$file_name = $books->book_file;
		$file = public_path()."/upload/book/".urldecode($file_name);
        $headers = array('Content-Type: application/pdf','Access-Control-Allow-Origin:*','Access-Control-Allow-Methods:GET, POST, PUT, DELETE, OPTIONS');
        //return Response :: download($file);
        
       //  return response()->download($file, $file_name, $headers);
        return Response::download($file,$bookname, $headers);
    }
    
    public function changeFiles(){
		 $books = Books::orderBy('id','ASC')->get();
		 foreach($books as $row){
//96.189.181.72.196.125.36.
			 echo $id= $row->id;echo "-";
			 echo $bookname = $row->book_name.'.pdf'; echo "<br>";
			 
			 
			 $file_name= $row->book_file;
			 
			$file = public_path()."/upload/book/".urldecode($file_name);
			
			//echo  $file = public_path()."/upload/book/".urldecode($file_name);exit;
			File::move($file, public_path('upload/book_test/'.$bookname));
			$updateArray = array(
					'book_file_changes'=>$bookname,
					
					
				);
				
				$update = Books::where('id',$id)->update($updateArray);
		//	exit;
			
			//rename(public_path('/upload/book/'.$file_name), public_path('/upload/book_test/'.$bookname));
			
			
			 
			 
		 }
		
	}
	
	public function getZipcode($address){
    if(!empty($address)){
       /*
        $formattedAddr = str_replace(' ','+',$address);
      
        $geocodeFromAddr = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddr.'&sensor=true_or_false'); 
        $output1 = json_decode($geocodeFromAddr);
		*/
		
		$GOOGLE_API_KEY = 'AIzaSyBMNKT7xu6QAhJckofnXO_hFFB2OMs4u-s'; 
		$formatted_address = str_replace(' ', '+', $address);
		$geocodeFromAddr = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address={$formatted_address}&key={$GOOGLE_API_KEY}"); 
		 
		// Decode JSON data returned by API 
		$output1 = json_decode($geocodeFromAddr);
		
		
		
        //Get latitude and longitute from json data
       $latitude  = $output1->results[0]->geometry->location->lat; 
        $longitude = $output1->results[0]->geometry->location->lng;
        //Send request and receive json data by latitude longitute
       // $geocodeFromLatlon = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='.$latitude.','.$longitude.'&sensor=true_or_false&key={$GOOGLE_API_KEY}');
        
		$geocodeFromLatlon = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$GOOGLE_API_KEY}"); 
		
		$output2 = json_decode($geocodeFromLatlon);
		
		
        if(!empty($output2)){
            $addressComponents = $output2->results[0]->address_components;
			
            foreach($addressComponents as $addrComp){
                if($addrComp->types[0] == 'postal_code'){
                    //Return the zipcode
                    return $addrComp->long_name;
                }
            }
            return false;
        }else{
            return false;
        }
    }else{
        return false;   
    }
	}
	
	public function addInfo()
	{
		$address = 'Akole,Akole,Ahmednagar';
		$zipcode = $this->getZipcode($address);
		$zipcode = $zipcode?$zipcode:'Not found';
		echo $zipcode;
	}
	
	public function importCsv()
	{
		$file   = public_path('/files/workingCSV.csv');
		$fileD = fopen($file,"r"); 
		$column=fgetcsv($fileD); 
		while(!feof($fileD)){ 
			$rowData[]=fgetcsv($fileD); 
		} 
		foreach ($rowData as $key => $value) 
		{
			$inserted_data=array(
				'hospital_name'=>$value[0], 
				'city_town'=>$value[1],
				'district'=>$value[2],
				'taluka'=>$value[3],
				'pincode'=>$value[4],
				'user_id'=>90,
				'user_code'=>$value[4],
				
			); 
			Product::create($inserted_data); 
		
		}
		
    
	/*$header = null;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== false)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
        {
            if (!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }

    return $data;*/
		
	}
	
	

	
}
