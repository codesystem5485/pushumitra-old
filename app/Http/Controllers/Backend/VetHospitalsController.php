<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Veterinaryhospitals;
use App\Models\VeterinaryhospitalsImages;
use App\Models\State;
use App\Repositories\Interfaces\Vethospitals\VethospitalsRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Models\Cities;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Http\Requests\VetHospitalsProcessRequest;
use Illuminate\Support\Facades\Storage;
use File;


class VetHospitalsController extends Controller
{
    use FileUpload;
    protected $url = '';
    protected $vethospitalsRepo;
    /**
     * Hospitals Construct 
     * @return url 
     */
    public function __construct(VethospitalsRepositoryInterface $vethospitalsRepo,UserRepositoryInterface $userRepository){

        $this->middleware('permission:hospital-list|hospital-create|hospital-edit|hospital-delete', ['only' => ['index','show']]);
        $this->middleware('permission:hospital-create', ['only' => ['create','store']]);
        $this->middleware('permission:hospital-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:hospital-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('hospitals.index'),
            'createUrl' => route('hospitals.create')
        ];
        $this->vethospitalsRepo = $vethospitalsRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * hospitals List
     * @return View
     */
    public function index(){
        $hospitals =[];
        return view('backend.hospitals.index',['hospitals'=>$hospitals,'url' => $this->url]); 
    }

    /**
     * Add hospitals View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = State::where('is_active','1')->get();
        return view('backend.hospitals.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store hospitals
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(VetHospitalsProcessRequest $request){
        
        DB::beginTransaction();
        // try{            
            $aInsertData = $request->all();
            $hospitals = $this->vethospitalsRepo->create($aInsertData);

            if($request->hospitals_photo)
            {
                foreach($request->hospitals_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'hospitals');
                    if($fileName)
                    {
                        VeterinaryhospitalsImages::create(['veterinary_hospitals_id'=>$hospitals->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.hospitals_create',['name' => $request->input('hospitals_name')]);
            storeActicityLog(trans('messages.hospitals_create'),$message,Auth::user(),$hospitals);
            return redirect()->route('hospitals.index');
        // }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('hospitals.index');   
            
        // }
     
    }

    /**
     * Get Particular hospitals
     * @param int $id (hospitals Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $hospitals = Veterinaryhospitals::find($id);
        $images = VeterinaryhospitalsImages::where('veterinary_hospitals_id',$hospitals->id)->get();
        $states = State::where('is_active','1')->get();
        return view('backend.hospitals.create',['images'=>$images,'states'=>$states,'hospitals' => $hospitals,'url' => $this->url]);  
    }

     /**
     * Update hospitals
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(VetHospitalsProcessRequest $request, $id) 
    {
        DB::beginTransaction();
        try{
            $hospitals = $this->vethospitalsRepo->update($id,$request->all());

            if($request->hospitals_photo)
            {
                foreach($request->hospitals_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'hospitals');
                    if($fileName)
                    {
                        VeterinaryhospitalsImages::create(['veterinary_hospitals_id'=>$hospitals->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.hospitals_update',['name' => $request->input('hospital_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$hospitals);
            return redirect()->route('hospitals.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('hospitals.index'); 
        }
    }

    /**
     * Get Particular hospitals
     * @param int $id (hospitals Id) Request $request
     * @return View
     */
    public function detail(Request $request, $id = ''){
        $hospitals = Veterinaryhospitals::find($id);
		$images = VeterinaryhospitalsImages::where('veterinary_hospitals_id',$hospitals->id)->get();
        $states = State::where('is_active','1')->get();
        return view('backend.hospitals.detail',['images'=>$images,'states'=>$states,'hospitals' => $hospitals,'url' => $this->url]);  
    }

    /**
     * Delete hospitals
     * @param int $id (hospitals Id)
     * @return Route
     */
    public function delete($id){ 
        $hospitals = Veterinaryhospitals::where('id',$id)->first();
        $hospitals->delete();
        Session::flash('success', trans('messages.delete_records'));
		
        ## Store loghospitals
        $message = trans('messages.hospitals_delete',['name' => $hospitals->hospital_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$hospitals);
        return redirect()->route('hospitals.index');
    }

    public function removeImage($id)
    {
        $image = VeterinaryhospitalsImages::where('id',$id)->first();
        $this->removeFile($image->image_name,'hospitals');
        $image->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.image_remove',['name' => $image->id]);
        storeActicityLog(trans('messages.image_remove'),$message,Auth::user(),$image);
        return true;
    }
	
	public function getAjaxList(Request $request){
        $list = $this->vethospitalsRepo->getAjaxList();
        return  $list;
    }
	
	public function importCsv1()
	{
		ini_set('max_execution_time', '15500');
		$file   = public_path('/files/aurangabad_csv.csv');
		$fileD = fopen($file,"r"); 
		$column=fgetcsv($fileD); 
		while(!feof($fileD)){ 
			$rowData[]=fgetcsv($fileD); 
		} 
		foreach ($rowData as $key => $value) 
		{
			if($value[4]==''){
				$address = $value[3].",".$value[2].",".$value[1];
				$zipcode = $this->getZipcode($address);
			}else{
				$zipcode = $value[4];
			}
		
			$inserted_data=array(
				'hospital_name'=>$value[0], 
				'city_town'=>$value[1],
				'district'=>$value[3],
				'taluka'=>$value[2],
				'pincode'=>$zipcode,
				'user_id'=>90,
				'sub_category'=>0,
				'type'=>'Government',
				'user_code'=>'PM0000000001',
				'state_id'=>'22',
				'state'=>'Maharashtra',
				'subscriptionStartDate'=>'0000-00-00',
				'subscriptionEndDate'=>'0000-00-00',
				'address'=>'',
				
				
			); 
			
			
		
			$hospitals = $this->vethospitalsRepo->create($inserted_data);
			
			
			
		}
	}
	
	public function importCsv()
	{
		ini_set('max_execution_time', '0');
		$file   = public_path('/files/latur_1.csv');
		
		$fileD = fopen($file,"r"); 
		
		$column=fgetcsv($fileD); 
		while(!feof($fileD)){ 
			$rowData[]=fgetcsv($fileD); 
		}
		
		foreach ($rowData as $key => $value) 
		{
			/*if($value[4]==''){
				$address = $value[3].",".$value[2].",".$value[1];
				$zipcode = $this->getZipcode($address);
			}else{
				$zipcode = $value[4];
			}*/
		
			$inserted_data=array(
				'sub_category'=>$value[0],
				'hospital_name'=>$value[1], 
				'address'=>$value[2],
				'city_town'=>$value[3],
				'district'=>$value[4],
				'taluka'=>$value[5],
				'pincode'=>$value[7],
				'state'=>'Maharashtra',
				'parent_category'=>1,
				'type'=>'Government',
				'user_code'=>'PM0000000001',
				'user_id'=>90,
				'state_id'=>'22',
				'subscriptionStartDate'=>'0000-00-00',
				'subscriptionEndDate'=>'0000-00-00',
				
			); 
			$hospitals = $this->vethospitalsRepo->create($inserted_data);
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($hospitals);
			$hospitals->latitude=$coordinateArr['latitude'];
			$hospitals->longitude=$coordinateArr['longitude'];
			
			$hospitals->update();
			$fileName ='hospital_'.$hospitals->id.'.jpg';
			

			\File::copy(public_path('files/hospital.jpg') , public_path('upload/hospitals/'.$fileName));
			
			if($fileName)
			{
				VeterinaryhospitalsImages::create(['veterinary_hospitals_id'=>$hospitals->id,'image_name' => $fileName]);
			}
		}
	}

	public function getZipcode($address){
		$code ='';
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
		 $latitude  = ''; 
        $longitude = '';
		if($output1!=''){
		
        //Get latitude and longitute from json data
		if(isset($output1->results[0])){
			$latitude  = $output1->results[0]->geometry->location->lat; 
			$longitude = $output1->results[0]->geometry->location->lng;
		}
        //Send request and receive json data by latitude longitute
       // $geocodeFromLatlon = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='.$latitude.','.$longitude.'&sensor=true_or_false&key={$GOOGLE_API_KEY}');
        if($latitude!='' && $longitude!=''){
		$geocodeFromLatlon = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$GOOGLE_API_KEY}"); 
		
		$output2 = json_decode($geocodeFromLatlon);
		
		
        if(!empty($output2)){
            $addressComponents = $output2->results[0]->address_components;
			
            foreach($addressComponents as $addrComp){
                if($addrComp->types[0] == 'postal_code'){
                    //Return the zipcode
                    $code =  $addrComp->long_name;
                }
            }
            
        }
		}
	}
	}
	
		return $code;
	}
}