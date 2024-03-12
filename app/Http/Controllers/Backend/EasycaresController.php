<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Easycares;
use App\Models\EasycaresImages;
use App\Repositories\Interfaces\Easycares\EasycaresRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Models\Cities;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Http\Requests\EasycaresProcessRequest;
use Illuminate\Support\Facades\Storage;
use File;
use App\Models\User;
use App\Models\Notifications;


class EasycaresController extends Controller
{
    use FileUpload;
    protected $url = '';
    protected $easycareRepo;
    /**
     * easycare Construct 
     * @return url 
     */
    public function __construct(EasycaresRepositoryInterface $easycareRepo,UserRepositoryInterface $userRepository){

        $this->middleware('permission:easycare-list|easycare-create|easycare-edit|easycare-delete', ['only' => ['index','show']]);
        $this->middleware('permission:easycare-create', ['only' => ['create','store']]);
        $this->middleware('permission:easycare-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:easycare-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('easycares.index'),
            'createUrl' => route('easycares.create')
        ];
        $this->easycareRepo = $easycareRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * easycares List
     * @return View
     */
    public function index(){
       
        return view('backend.easycares.index',['url' => $this->url]); 
    }

    /**
     * Add easycares View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = [];
        return view('backend.easycares.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store easycares
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(EasycaresProcessRequest $request){
        
        DB::beginTransaction();
        // try{            
            $aInsertData = $request->all();
            $easycares = $this->easycareRepo->create($aInsertData);

            if($request->easy_care_photo)
            {
                foreach($request->easy_care_photo as $photo)
                {
                     $fileName ='';
                    $fileName = $this->uploadFile($photo,'easycares');
                    if($fileName)
                    {
                        EasycaresImages::create(['easycare_id'=>$easycares->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.easycare_create',['name' => $request->input('tilte')]);
            storeActicityLog(trans('messages.easycare_create'),$message,Auth::user(),$easycares);
            return redirect()->route('easycares.index');
        // }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('easycares.index');   
            
        // }
     
    }

    /**
     * Get Particular easycares
     * @param int $id (easycares Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $easycares = Easycares::find($id);
        $images = EasycaresImages::where('easycare_id',$easycares->id)->get();
        $states =[];
        return view('backend.easycares.create',['images'=>$images,'states'=>$states,'easycares' => $easycares,'url' => $this->url]);  
    }

     /**
     * Update easycares
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(EasycaresProcessRequest $request, $id) 
    {
        DB::beginTransaction();
        try{
		    $easycares = $this->easycareRepo->update($id,$request->all());
            if($request->easy_care_photo)
            {
                foreach($request->easy_care_photo as $photo)
                {
                    
                    $fileName = $this->uploadFile($photo,'easycares');
                    if($fileName)
                    {
                        EasycaresImages::create(['easycare_id'=>$easycares->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.easycare_update',['name' => $request->input('title')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$easycares);
            return redirect()->route('easycares.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('easycares.index'); 
        }
    }

    /**
     * Get Particular easycares
     * @param int $id (easycares Id) Request $request
     * @return View
     */
    public function detail(Request $request, $id = ''){
        $easycares = Easycares::find($id);
		$images = EasycaresImages::where('easycare_id',$easycares->id)->get();
        
        return view('backend.easycares.detail',['images'=>$images,'easycares' => $easycares,'url' => $this->url]);  
    }

    /**
     * Delete Easycares
     * @param int $id (easycares Id)
     * @return Route
     */
    public function delete($id){ 
        $easycares = Easycares::where('id',$id)->first();
        $easycares->delete();
        Session::flash('success', trans('messages.delete_records'));
		
        ## Store easycares
        $message = trans('messages.easycare_delete',['name' => $easycares->title]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$easycares);
        return redirect()->route('easycares.index');
    }

    public function removeImage($id)
    {
        $image = EasycaresImages::where('id',$id)->first();
        $this->removeFile($image->image_name,'easycares');
        $image->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.image_remove',['name' => $image->id]);
        storeActicityLog(trans('messages.image_remove'),$message,Auth::user(),$image);
        return true;
    }
	
	public function getAjaxList(Request $request){
        $list = $this->easycareRepo->getAjaxList();
        return  $list;
    }
	
	public function easycareVerify($id)
	{
		$inputDetail['is_verified'] = 1;
        $easycare = $this->easycareRepo->update($id,$inputDetail);
		$easycareDetails = Easycares::find($id);
		$title = 'New Article In Knowledge Sharing Is Added';
		$message ='';
		if($easycareDetails){
			$message =$easycareDetails->title;
		}
		//$fcmArray = $this->userRepo->getUsersFcmIds(['sRoleName' => $sRoleName]);
	
    
    //	$fcmArray = DB::table('users')->whereIn('id', array(90,104,96))->get();
	$fcmArray = User::select('fcm_id','id')
					->where('is_active',1)
					->where('fcm_id','!=','')
					->orderBy('id', 'DESC')
					->get();
	
  	
		$notifications = new Notifications();
		$notifications->title = $title;
		$notifications->message = $message;
		$notifications->show_role ='';
		$notifications->type = 4;//easy cares
		$notifications->send_flag = 1;
		$notifications->scheduled_date= date("Y-m-d");
		$notifications->send_date = date("Y-m-d");
		$notifications->save();
			
		$sendFcmArray =array();
		
		foreach($fcmArray as $row)
		{
		$fcmId = $row->fcm_id;
		
		
		/*DB::beginTransaction();
        try{*/
			//insert to notifications
			
		
			$body 	= $message;
			$title	= $title;
					$sendFcmArray =array();
				$data = [
					//"registration_ids"=>$fcmId,
					"to"=>$fcmId,
					"notification" => [
						"body"  => $body,
						"title" => $title,
					
					],
					"priority" =>  "high",
					"data" => [
						"info"          =>  [
							"title"  => $title,
						]
					],
					
				];
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
				curl_setopt($ch, CURLOPT_POST, 1);

				$headers = array();
				$headers[] = 'Content-Type: application/json';
				$headers[] = 'Authorization: key=AAAA3VOatmM:APA91bH0smliP78ILBQ96TDyZvZoTkCaWQOZaBhMXROKgEXlmUjdJIkEHqFbk5B5zET51aFicM_tdH72oFtml_fkPPkuWR2ARpWFOnkVOne_LWlQ12sUuQ5t5UhWzx90dDMW0kqh5XAK';
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$result = curl_exec($ch);
				curl_close ($ch);
				
				if ($result === FALSE) {
					//echo 'Android: Curl failed: ' . curl_error($ch);
				}
				// Close connection
				curl_close($ch);
			}
			
			$message = 'Knowledge Sharing Push notification send successfully';
            storeActicityLog('Push notification',$message,Auth::user(),$notifications);
			/* Session::flash('success', 'Knowledge Sharing Push notifications send successfully');
            return redirect()->route('sendnotifications.create'); */   
      /*  }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('sendnotifications.create'); 
        }*/
		
		Session::flash('success', trans('messages.verify_success'));
		## Store log
		$message = trans('messages.verify_success'); 
		storeActicityLog(trans('messages.verify'),$message,Auth::user(),$easycare);
		return redirect()->route('easycares.index');
	}
}