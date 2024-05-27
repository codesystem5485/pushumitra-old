<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Labs;
use App\Models\LabsImages;
use App\Models\State;
use App\Repositories\Interfaces\Labs\LabsRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Models\Cities;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Http\Requests\LabsProcessRequest;
use Illuminate\Support\Facades\Storage;
use File;


class LabsController extends Controller
{
    use FileUpload;
    protected $url = '';
    protected $labsRepo;
    /**
     * labs Construct 
     * @return url 
     */
    public function __construct(LabsRepositoryInterface $labsRepo,UserRepositoryInterface $userRepository){

        $this->middleware('permission:lab-list|lab-create|lab-edit|lab-delete', ['only' => ['index','show']]);
        $this->middleware('permission:lab-create', ['only' => ['create','store']]);
        $this->middleware('permission:lab-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:lab-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('labs.index'),
            'createUrl' => route('labs.create')
        ];
        $this->labsRepo = $labsRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * labs List
     * @return View
     */
    public function index(){
       
        return view('backend.labs.index',['url' => $this->url]); 
    }

    /**
     * Add labs View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = State::where('is_active','1')->get();
        return view('backend.labs.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store labs
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(LabsProcessRequest $request){
        
        DB::beginTransaction();
        // try{            
            $aInsertData = $request->all();
            $labs = $this->labsRepo->create($aInsertData);

            if($request->labs_photo)
            {
                foreach($request->labs_photo as $photo)
                {
                     $fileName ='';
                    $fileName = $this->uploadFile($photo,'labs');
                    if($fileName)
                    {
                        LabsImages::create(['lab_id'=>$labs->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.lab_create',['name' => $request->input('lab_name')]);
            storeActicityLog(trans('messages.lab_create'),$message,Auth::user(),$labs);
            return redirect()->route('labs.index');
        // }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('labs.index');   
            
        // }
     
    }

    /**
     * Get Particular labs
     * @param int $id (labs Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $labs = Labs::find($id);
        $images = LabsImages::where('lab_id',$labs->id)->get();
        $states = State::where('is_active','1')->get();
        return view('backend.labs.create',['images'=>$images,'states'=>$states,'labs' => $labs,'url' => $this->url]);  
    }

     /**
     * Update labs
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(LabsProcessRequest $request, $id) 
    {
        DB::beginTransaction();
        try{
		    $labs = $this->labsRepo->update($id,$request->all());
            if($request->labs_photo)
            {
                foreach($request->labs_photo as $photo)
                {
                     $fileName ='';
                    $fileName = $this->uploadFile($photo,'labs');
                    if($fileName)
                    {
                        LabsImages::create(['lab_id'=>$labs->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($labs);
			$labs->latitude=$coordinateArr['latitude'];
			$labs->longitude=$coordinateArr['longitude'];
			$labs->update();

            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.lab_update',['name' => $request->input('lab_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$labs);
            return redirect()->route('labs.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('labs.index'); 
        }
    }

    /**
     * Get Particular labs
     * @param int $id (labs Id) Request $request
     * @return View
     */
    public function detail(Request $request, $id = ''){
        $labs = Labs::find($id);
		$images = LabsImages::where('lab_id',$labs->id)->get();
        $states = State::where('is_active','1')->get();
        return view('backend.labs.detail',['images'=>$images,'states'=>$states,'labs' => $labs,'url' => $this->url]);  
    }

    /**
     * Delete labs
     * @param int $id (labs Id)
     * @return Route
     */
    public function delete($id){ 
        $labs = Labs::where('id',$id)->first();
		if($labs){
			$name = $labs->lab_name;
			$images = LabsImages::where('lab_id',$labs->id)->get();
			
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					$this->removeFile($image->image_name,'labs');
					$image->delete();
				}
			}
			
			$arr = array('status'=>0);
			$labs = $this->labsRepo->update($id,$arr);
		}
        //$labs->delete();
        Session::flash('success', trans('messages.delete_records'));
		
        ## Store labs
        $message = trans('messages.lab_delete',['name' => $labs->labs_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$labs);
        return redirect()->route('labs.index');
    }

    public function removeImage($id)
    {
        $image = LabsImages::where('id',$id)->first();
        $this->removeFile($image->image_name,'labs');
        $image->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.image_remove',['name' => $image->id]);
        storeActicityLog(trans('messages.image_remove'),$message,Auth::user(),$image);
        return true;
    }
	
	public function getAjaxList(Request $request){
        $list = $this->labsRepo->getAjaxList();
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
				$zipcode = $value[4];
			}
		
			$inserted_data=array(
				'lab_name'=>$value[0], 
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
			$labs=$this->labsRepo->create($inserted_data);
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
				'lab_name'=>$value[1], 
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
			$labs = $this->labsRepo->create($inserted_data);
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($labs);
			$labs->latitude=$coordinateArr['latitude'];
			$labs->longitude=$coordinateArr['longitude'];
			
			$labs->update();
			$fileName ='lab_'.$labs->id.'.jpg';
			

			\File::copy(public_path('files/lab.jpg') , public_path('upload/labs/'.$fileName));
			
			if($fileName)
			{
				LabsImages::create(['lab_id'=>$labs->id,'image_name' => $fileName]);
			}
		}
	}

}