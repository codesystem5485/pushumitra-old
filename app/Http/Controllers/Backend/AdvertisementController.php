<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advertisements;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use Response;
use App\Traits\FileUpload;


class AdvertisementController extends Controller
{
    use FileUpload;
    protected $url = '';
   
    /**
     * Advertisements Type Construct 
     * @return url 
     */
    public function __construct(){

       /* $this->middleware('permission:advertisements-list|advertisements-create|advertisements-edit|advertisements-delete', ['only' => ['index','show']]);
        $this->middleware('permission:advertisements-create', ['only' => ['create','store']]);
        $this->middleware('permission:advertisements-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:advertisements-delete', ['only' => ['delete']]);*/
        
       $this->url = [   
            'listUrl' => route('advertisements.index'),
            'createUrl' => route('advertisements.create')
        ];
    } 

    /**
     * Advertisements List
     * @return View
     */
    public function index(){
        $advertisements = Advertisements::where('status',1)->orderBy('id','ASC')->get();
        return view('backend.advertisements.index',['advertisements'=>$advertisements,'url' => $this->url]); 
    }

    /**
     * Add Advertisements View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        return view('backend.advertisements.create',['permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Advertisements
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(Request $request){
        $this->validate($request, [
            'advertisement_title' => 'required',            
            'advertisement_startdate' => 'required', 
			'advertisement_enddate' => 'required',
			'advertiser_name' => 'required',
			'advertiser_address' => 'required',
			'advertiser_contactnumber' => 'required',
			'advertisement_cost' => 'required',
			'advertisement_cost_paid' => 'required',
			'advertisement_app_image' => 'required|mimes:jpeg,jpg,png',
			'advertisement_website_image' => 'required|mimes:jpeg,jpg,png', 			
        ]);
        DB::beginTransaction();
        try{
			$advertisement_app_image='';
			$advertisement_web_image='';
			
			if(!empty($request->advertisement_app_image))
			{
				$advertisement_app_imagename = $this->uploadFile($request->advertisement_app_image,'adevertisements_app');
				if(!empty($advertisement_app_imagename))
				{
					$advertisement_app_image = $advertisement_app_imagename;
				}
			}
			
			if(!empty($request->advertisement_website_image))
			{
				$advertisement_web_imagename = $this->uploadFile($request->advertisement_website_image,'adevertisements_web');
				if(!empty($advertisement_web_imagename))
				{
					$advertisement_web_image = $advertisement_web_imagename;
				}
			}
			
			$advertisement_startdate = '';
			if($request->advertisement_startdate!=''){
				$advertisement_startdate = date('Y-m-d',strtotime($request->advertisement_startdate));
			}
			
			$advertisement_enddate = '';
			if($request->advertisement_enddate!=''){
				$advertisement_enddate = date('Y-m-d',strtotime($request->advertisement_enddate));
			}
			
			$advertisements = new Advertisements();
            $advertisements->advertisement_title = $request->input('advertisement_title');
			$advertisements->advertisement_startdate = $advertisement_startdate;
			$advertisements->advertisement_enddate = $advertisement_enddate;
			$advertisements->advertiser_name = $request->input('advertiser_name');
			$advertisements->advertiser_address = $request->input('advertiser_address');
			$advertisements->advertiser_contactnumber = $request->input('advertiser_contactnumber');
			$advertisements->advertisement_cost = $request->input('advertisement_cost');
			$advertisements->advertisement_cost_paid = $request->input('advertisement_cost_paid');
			$advertisements->advertisement_app_image = $advertisement_app_image;
			$advertisements->advertisement_website_image = $advertisement_web_image;
			$advertisements->user_id = Auth::user()->id;
			
            $advertisements->save();
            
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store logbbok
            $message = trans('messages.advertisements_create',['name' => $request->input('name')]);
            storeActicityLog(trans('messages.advertisements_create'),$message,Auth::user(),$advertisements);
            return redirect()->route('advertisements.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('advertisements.index');   
            
        }
     
    }

    /**
     * Get Particular advertisements
     * @param int $id (advertisements Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $advertisements = Advertisements::find($id);
        return view('backend.advertisements.create',['advertisements' => $advertisements,'url' => $this->url]);  
    }

     /**
     * Update advertisements
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(Request $request, $id) 
    {
        

        $this->validate($request, [
           'advertisement_title' => 'required',            
            'advertisement_startdate' => 'required', 
			'advertisement_enddate' => 'required',
			'advertiser_name' => 'required',
			'advertiser_address' => 'required',
			'advertiser_contactnumber' => 'required',
			'advertisement_cost' => 'required',
			'advertisement_cost_paid' => 'required',
			'advertisement_app_image' => 'mimes:jpeg,jpg,png',
			'advertisement_website_image' => 'mimes:jpeg,jpg,png', 
        ]);
        DB::beginTransaction();
        try{
            $advertisements = Advertisements::find($id);
            
            $advertisement_app_image='';
			$advertisement_web_image='';
			
			if(!empty($request->advertisement_app_image))
			{
				$advertisement_app_imagename = $this->uploadFile($request->advertisement_app_image,'adevertisements_app');
				if(!empty($advertisement_app_imagename))
				{
					$advertisement_app_image = $advertisement_app_imagename;
				}
			}
			
			if(!empty($request->advertisement_website_image))
			{
				$advertisement_web_imagename = $this->uploadFile($request->advertisement_website_image,'adevertisements_web');
				if(!empty($advertisement_web_imagename))
				{
					$advertisement_web_image = $advertisement_web_imagename;
				}
			}
			$advertisement_startdate = '';
			if($request->advertisement_startdate!=''){
				$advertisement_startdate = date('Y-m-d',strtotime($request->advertisement_startdate));
			}
			
			$advertisement_enddate = '';
			if($request->advertisement_enddate!=''){
				$advertisement_enddate = date('Y-m-d',strtotime($request->advertisement_enddate));
			}
			
            $advertisements->advertisement_title = $request->input('advertisement_title');
			$advertisements->advertisement_startdate = $advertisement_startdate;
			$advertisements->advertisement_enddate = $advertisement_enddate;
			$advertisements->advertiser_name = $request->input('advertiser_name');
			$advertisements->advertiser_address = $request->input('advertiser_address');
			$advertisements->advertiser_contactnumber = $request->input('advertiser_contactnumber');
			$advertisements->advertisement_cost = $request->input('advertisement_cost');
			$advertisements->advertisement_cost_paid = $request->input('advertisement_cost_paid');
			$advertisements->advertisement_app_image = $advertisement_app_image;
			$advertisements->advertisement_website_image = $advertisement_web_image;
			$advertisements->user_id = Auth::user()->id;
			
            $advertisements->save();
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.advertisements_update',['name' => $request->input('name')]);
            storeActicityLog(trans('messages.advertisements_update'),$message,Auth::user(),$advertisements);
            return redirect()->route('advertisements.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('advertisements.index'); 
        }
    }

    /**
     * Delete advertisements
     * @param int $id (advertisements Id)
     * @return Route
     */
    public function delete($id){ 
        $advertisements = Advertisements::where('id',$id)->first();
        $advertisements->status = 0;
        $advertisements->save();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.advertisements_delete',['name' => $advertisements->name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$advertisements);
        return redirect()->route('advertisements.index');
    }
}
