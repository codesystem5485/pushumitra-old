<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GResolutionfiles;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use Response;
use App\Traits\FileUpload;
use File;
use Config;
use App\Repositories\Interfaces\State\StateRepositoryInterface;

class GResolutionfileController extends Controller
{
    use FileUpload;
	 protected $stateRepo;
     protected $url = '';
   
    /**
     * GResolutionfiles Type Construct 
     * @return url 
     */
    public function __construct(StateRepositoryInterface $stateRepo){

        $this->middleware('permission:grfile-list|grfile-create|grfile-edit|grfile-delete', ['only' => ['index','show']]);
        $this->middleware('permission:grfile-create', ['only' => ['create','store']]);
        $this->middleware('permission:grfile-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:grfile-delete', ['only' => ['delete']]);
        
       $this->url = [   
            'listUrl' => route('gresolutionfiles.index'),
            'createUrl' => route('gresolutionfiles.create')
        ];
		$this->stateRepo = $stateRepo;
    } 

    /**
     * GResolutionfiles List
     * @return View
     */
    public function index(){
		$gresolutionfiles = GResolutionfiles::orderBy('id','ASC')->get();
        return view('backend.gresolutionfiles.index',['gresolutionfiles'=>$gresolutionfiles,'url' => $this->url]); 
    }

    /**
     * Add Library View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
		$states = $this->stateRepo->getStates();
        return view('backend.gresolutionfiles.create',['permission'=>$permission,'url' => $this->url,'states'=>$states]); 
    }
    /**
     * Store Library
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(Request $request){
        $this->validate($request, [
            'title' => 'required',            
            'gr_file' => 'required|max:20240', 
					
        ]);
       DB::beginTransaction();
        try{
			
			$file = $request->gr_file;
			$title = $request->title;
			$state_id= $request->state_id;
			
			$extension = $file->getClientOriginalExtension();
			$path = Config::get('constants.file.gresolutionfiles_file_path');
			
			$fileName = $title.'.'.$file->extension();
            $file->move(public_path($path), $fileName);
			$GResolutionfiles = GResolutionfiles::create(['title' => $title,'gr_file'=>$fileName,'state_id'=>$state_id]);
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.gresolutionfile_create',['name' => $request->input('title')]);
            storeActicityLog(trans('messages.gresolutionfiles_create'),$message,Auth::user(),$GResolutionfiles);
             return redirect()->route('gresolutionfiles.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('gresolutionfiles.index');   
            
        }
     
    }

    /**
     * Get Particular GResolutionfiles
     * @param int $id (GResolutionfiles Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $gresolutionfiles = GResolutionfiles::find($id);
        
		$states = $this->stateRepo->getStates();
        
        return view('backend.gresolutionfiles.create',['gresolutionfiles' => $gresolutionfiles,'url' => $this->url,'states'=>$states]);  
    }

     /**
     * Update GResolutionfiles
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(Request $request, $id) 
    {
        $this->validate($request, [
            'title' => 'required',            
           // 'gr_file' => 'required|max:20240',             
        ]);
        DB::beginTransaction();
        try{
            $GResolutionfiles = GResolutionfiles::find($id);
			$file = $request->gr_file;
			$title = $request->title;
            if($request->gr_file)
            {
                $this->removeFile($GResolutionfiles->gr_file,'gresolutionfiles');
				
				if($request->gr_file!='')
				{
					$file = $request->gr_file;
					$title = $request->title;
					
					$extension = $file->getClientOriginalExtension();
					$path = Config::get('constants.file.gresolutionfiles_file_path');
				
					$fileName = $title.'.'.$file->extension();
					$file->move(public_path($path), $fileName);
					 $GResolutionfiles->gr_file = $fileName;
				}
				
            }
			
            $GResolutionfiles->state_id = $request->input('state_id');
			$GResolutionfiles->title = $request->input('title');
			
            $GResolutionfiles->save();
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.GResolutionfiles_update',['name' => $request->input('title')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$GResolutionfiles);
            return redirect()->route('gresolutionfiles.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('gresolutionfiles.index'); 
        }
    }

    /**
     * Delete GResolutionfiles
     * @param int $id (GResolutionfiles Id)
     * @return Route
     */
    public function delete($id){ 
        $GResolutionfiles = GResolutionfiles::where('id',$id)->first();
        $this->removeFile($GResolutionfiles->title,'GResolutionfiles');

        $GResolutionfiles->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.GResolutionfiles_delete',['name' => $GResolutionfiles->title]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$GResolutionfiles);
        return redirect()->route('gresolutionfiles.index');
    }

    public function getDownload($file_name){

//         $file = public_path()."/upload/GResolutionfiles/".urldecode($file_name);
// 		$headers = array('Content-Type: application/pdf',
// 						'Access-Control-Allow-Origin:*','Access-Control-Allow-Methods:
// 		GET, POST, PUT, DELETE, OPTIONS');
//         return Response :: download($file);
        
//       //  return response()->download($file, $file_name, $headers);
//         return Response::download($file,$file_name, $headers);
        
         $file_name = urldecode($file_name);

        $path = public_path('upload/gresolutionfiles/' . $file_name);
    
        if (!file_exists($path)) {
            abort(404, 'File not found: ' . $file_name);
        }
    
        return response()->download($path, $file_name);
        }

}
