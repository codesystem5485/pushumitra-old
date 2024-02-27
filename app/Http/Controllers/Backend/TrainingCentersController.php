<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingCenters;
use App\Models\TrainingCenterImages;
use App\Models\State;
use App\Repositories\Interfaces\Trainingcenters\TrainingcentersRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Http\Requests\TrainingCenterProcessRequest;
use App\Models\Subcategories;
use Illuminate\Support\Facades\Storage;
use File;

class TrainingCentersController extends Controller
{
    use FileUpload;
    protected $trainingcenterRepo;
	
    /**
     * TrainingCenters Construct 
     * @return url 
     */
    public function __construct(TrainingcentersRepositoryInterface $trainingcenterRepo,UserRepositoryInterface $userRepository){

      /*  $this->middleware('permission:transporter-list|transporter-create|transporter-edit|transporter-delete', ['only' => ['index','show']]);
        $this->middleware('permission:transporter-create', ['only' => ['create','store']]);
        $this->middleware('permission:transporter-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:transporter-delete', ['only' => ['delete']]);*/

        $this->url = [   
            'listUrl' => route('trainingcenters.index'),
            'createUrl' => route('trainingcenters.create')
        ];
        $this->trainingcenterRepo = $trainingcenterRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * trainingcenters List
     * @return View
     */
    public function index(){
        $trainingcenters = TrainingCenters::orderBy('id','DESC')->get();
        return view('backend.trainingcenters.index',['trainingcenters'=>$trainingcenters,'url' => $this->url]); 
    }

    /**
     * Add trainingcenters View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = State::where('is_active','1')->get();
        return view('backend.trainingcenters.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store trainingcenters
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(TrainingCenterProcessRequest $request){
        
        DB::beginTransaction();
        // try{            
            $aInsertData = $request->all();
            $trainingcenters = $this->trainingcenterRepo->create($aInsertData);

             if($request->trainingcenter_photo)
            {
                foreach($request->trainingcenter_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'trainingcenters');
                    if($fileName)
                    {
                        TrainingCenterImages::create(['training_center_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.trainingcenters_create',['name' => $request->input('training_center_name')]);
            storeActicityLog(trans('messages.trainingcenters_create'),$message,Auth::user(),$trainingcenters);
            return redirect()->route('trainingcenters.index');
        // }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('trainingcenters.index');   
            
        // }
     
    }

    /**
     * Get Particular trainingcenters
     * @param int $id (trainingcenters Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $trainingcenters = TrainingCenters::find($id);
        $images = TrainingCenterImages::where('training_center_id',$trainingcenters->id)->get();
        $states = State::where('is_active','1')->get();
		$subcategories = Subcategories::where('status',1)->where('parent_category',4)->get();
		return view('backend.trainingcenters.create',['subcategories'=>$subcategories,'images'=>$images,'states'=>$states,'trainingcenters' => $trainingcenters,'url' => $this->url]);  
    }

     /**
     * Update trainingcenters
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(TrainingCenterProcessRequest $request, $id) 
    {
       
            $trainingcenters = $this->trainingcenterRepo->update($id,$request->all());

             if($request->trainingcenter_photo)
            {
                foreach($request->trainingcenter_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'trainingcenters');
                    if($fileName)
                    {
                        TrainingCenterImages::create(['training_center_id'=>$trainingcenters->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.trainingcenters_update',['name' => $request->input('training_center_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$trainingcenters);
            return redirect()->route('trainingcenters.index');    
       /* }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('trainingcenters.index'); 
        }*/
    }

    /**
     * Get Particular trainingcenters
     * @param int $id (trainingcenters Id) Request $request
     * @return View
     */
    public function detail(Request $request, $id = ''){
        $trainingcenters = TrainingCenters::find($id);
		$images = TrainingCenterImages::where('training_center_id',$trainingcenters->id)->get();
        $states = State::where('is_active','1')->get();
        return view('backend.trainingcenters.detail',['images'=>$images,'states'=>$states,'trainingcenters' => $trainingcenters,'url' => $this->url]);  
    }

    /**
     * Delete trainingcenters
     * @param int $id (trainingcenters)
     * @return Route
     */
    public function delete($id){ 
        $trainingcenters = TrainingCenters::where('id',$id)->first();
        $trainingcenters->delete();
        Session::flash('success', trans('messages.delete_records'));
		
        ## Store log trainingcenters
        $message = trans('messages.trainingcenters_delete',['name' => $trainingcenters->taining_center_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$trainingcenters);
        return redirect()->route('trainingcenters.index');
    }

    public function removeImage($id)
    {
        $image = TrainingCenterImages::where('id',$id)->first();
        $this->removeFile($image->image_name,'trainingcenters');
        $image->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.image_remove',['name' => $image->id]);
        storeActicityLog(trans('messages.image_remove'),$message,Auth::user(),$image);
        return true;
    }
	
	public function getAjaxList(Request $request){
        $list = $this->trainingcenterRepo->getAjaxList();
        return  $list;
    }
	
	public function importTrainingCsv()
	{
		ini_set('max_execution_time', '0');
		$file   = public_path('/files/training.csv');
		
		$fileD = fopen($file,"r"); 
		
		$column=fgetcsv($fileD); 
		while(!feof($fileD)){ 
			$rowData[]=fgetcsv($fileD); 
		}
		
		foreach ($rowData as $key => $value) 
		{
			$zipcode='';
			if($value[10]==''){
				$address = $value[5].",".$value[6]." ".$value[7]." ".$value[8]." ".$value[11];
				$zipcode = $this->getZipcode($address);
			}else{
				$zipcode = $value[10];
			}
		
			$inserted_data=array(
				'training_center_name'=>$value[0], 
				'user_id'=>90, 
				'incharge_name'=>$value[2],
				'mobile_number'=>$value[3],
				'email_id'=>$value[1],
				'state'=>$value[11],
				'description'=>$value[4],
				'address'=>$value[5],
				'city_town'=>$value[6],
				'district'=>$value[7],
				'taluka'=>$value[8],
				'pincode'=>$zipcode,
				'parent_category'=>4,
				'sub_category'=>15,
				'type'=>'Government',
				'user_code'=>'PM0000000001',
				'state_id'=>$value[9],
				'subscriptionStartDate'=>'0000-00-00',
				'subscriptionEndDate'=>'0000-00-00',
				
			); 
			
			$results = $this->trainingcenterRepo->create($inserted_data);
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($results);
			$results->latitude=$coordinateArr['latitude'];
			$results->longitude=$coordinateArr['longitude'];
			
			$results->update();
			$fileName ='trainingcenters_'.$results->id.'.jpg';
			\File::copy(public_path('files/trainingcenters.jpg') , public_path('upload/trainingcenters/'.$fileName));
			
			if($fileName)
			{
				 TrainingCenterImages::create(['training_center_id'=>$results->id,'image_name' => $fileName]);
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