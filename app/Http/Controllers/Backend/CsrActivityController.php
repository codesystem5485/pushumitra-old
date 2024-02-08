<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CsrActivities;
use App\Models\CsrActivityImages;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use Response;
use App\Traits\FileUpload;
use Redirect;
use App\Models\State;

class CsrActivityController extends Controller
{
    use FileUpload;
    protected $url = '';
   
    /**
     * Csr Activity Construct 
     * @return url 
     */
    public function __construct(){

        $this->middleware('permission:csractivities-list|csractivities-create|csractivities-edit|csractivities-delete', ['only' => ['index','show']]);
        $this->middleware('permission:csractivities-create', ['only' => ['create','store']]);
        $this->middleware('permission:csractivities-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:csractivities-delete', ['only' => ['delete']]);
        
       $this->url = [   
            'listUrl' => route('csractivities.index'),
            'createUrl' => route('csractivities.create')
        ];
    } 

    /**
     * Csr Activity List
     * @return View
     */
    public function index(){
        $csractivities = CsrActivities::where('status',1)->orderBy('id','DESC')->get();
        return view('backend.csractivities.index',['csractivities'=>$csractivities,'url' => $this->url]); 
    }

    /**
     * Add csractivities View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
		$states = State::where('is_active','1')->get();
        return view('backend.csractivities.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store csractivities
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(Request $request){
		$postData = $request->all();
        $this->validate($request, [
            'title' => 'required',            
            //'description' => 'required', 
			'schedule_date' => 'required',
			'address' => 'required',
			'city_town' => 'required',
			'state' => 'required',
        ]);
		
		
		/*if($postData['advertisement_enddate']!=''){
			$errDateMessage = trans('messages.invalid_end_date');
			$advertisement_enddate = date("Y-m-d",strtotime($postData['advertisement_enddate']));
			$advertisement_startdate =date("Y-m-d",strtotime($postData['advertisement_startdate']));;
			if($advertisement_enddate < $advertisement_startdate){
				Session::flash('error',$errDateMessage);
				
				//return Redirect::back()->withInput($postData);
			}
		}*/
		
        DB::beginTransaction();
        try{

			$csractivities = new CsrActivities();
            $csractivities->title = $request->input('title');
			$csractivities->description = $request->input('description');
			$csractivities->schedule_date = date("Y-m-d",strtotime($request->input('schedule_date')));
			$csractivities->address = $request->input('address');
			$csractivities->city_town = $request->input('city_town');
			$csractivities->state_id = $request->input('state_id'); 
			$csractivities->state = $request->input('state');
			$csractivities->user_id = Auth::user()->id;
			$csractivities->save();
			
			if($request->csractivities_photo)
            {
                foreach($request->csractivities_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'csractivities');
                    if($fileName)
                    {
                        CsrActivityImages::create(['csr_activity_id'=>$csractivities->id,'image_name' => $fileName]);
                    }
                }
            }
			
			
            
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store logbbok
            $message = trans('messages.csractivities_create',['name' => $request->input('name')]);
            storeActicityLog(trans('messages.csractivities_create'),$message,Auth::user(),$csractivities);
            return redirect()->route('csractivities.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('csractivities.index');   
            
        }
     
    }

    /**
     * Get Particular csractivities
     * @param int $id (csractivities Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $csractivities = CsrActivities::find($id);
		 $images = CsrActivityImages::where('csr_activity_id',$csractivities->id)->get();
		$states = State::where('is_active','1')->get();
        return view('backend.csractivities.create',['images'=>$images,'states'=>$states,'csractivities' => $csractivities,'url' => $this->url]);  
    }

     /**
     * Update csractivities
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(Request $request, $id) 
    {
		$postData = $request->all();
       $this->validate($request, [
            'title' => 'required',            
            //'description' => 'required', 
			'schedule_date' => 'required',
			'address' => 'required',
			'city_town' => 'required',
			'state' => 'required',
        ]);
		
		
        DB::beginTransaction();
        try{
            $csractivities = CsrActivities::find($id);
           
            $csractivities->title = $request->input('title');
			$csractivities->description = $request->input('description');
			$csractivities->schedule_date = date("Y-m-d",strtotime($request->input('schedule_date')));
			$csractivities->address = $request->input('address');
			$csractivities->city_town = $request->input('city_town');
			$csractivities->state_id = $request->input('state_id'); 
			$csractivities->state = $request->input('state');
            $csractivities->save();
			
			if($request->csractivities_photo)
            {
                foreach($request->csractivities_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'csractivities');
                    if($fileName)
                    {
                        CsrActivityImages::create(['csr_activity_id'=>$csractivities->id,'image_name' => $fileName]);
                    }
                }
            }
			
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.csractivities_update',['name' => $request->input('title')]);
            storeActicityLog(trans('messages.csractivities_update'),$message,Auth::user(),$csractivities);
            return redirect()->route('csractivities.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('csractivities.index'); 
        }
    }

    /**
     * Delete csractivities
     * @param int $id (csractivities Id)
     * @return Route
     */
    public function delete($id){ 
        $csractivities = CsrActivities::where('id',$id)->first();
        $csractivities->status = 0;
        $csractivities->save();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.csractivities_delete',['name' => $csractivities->title]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$csractivities);
        return redirect()->route('csractivities.index');
    }
}
