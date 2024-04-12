<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grfiles;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use Response;
use App\Traits\FileUpload;
use File;
use Config;
use App\Repositories\Interfaces\State\StateRepositoryInterface;

class GrfileController extends Controller
{
    use FileUpload;
	 protected $stateRepo;
    protected $url = '';
   
    /**
     * Grfiles Type Construct 
     * @return url 
     */
    public function __construct(StateRepositoryInterface $stateRepo){

        $this->middleware('permission:grfile-list|grfile-create|grfile-edit|grfile-delete', ['only' => ['index','show']]);
        $this->middleware('permission:grfile-create', ['only' => ['create','store']]);
        $this->middleware('permission:grfile-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:grfile-delete', ['only' => ['delete']]);
        
       $this->url = [   
            'listUrl' => route('grfiles.index'),
            'createUrl' => route('grfiles.create')
        ];
		$this->stateRepo = $stateRepo;
    } 

    /**
     * Grfiles List
     * @return View
     */
    public function index(){
		$grfiles = Grfiles::orderBy('id','ASC')->get();
        return view('backend.grfiles.index',['grfiles'=>$grfiles,'url' => $this->url]); 
    }

    /**
     * Add Library View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
		$states = $this->stateRepo->getStates();
        return view('backend.grfiles.create',['permission'=>$permission,'url' => $this->url,'states'=>$states]); 
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
			$path = Config::get('constants.file.grfiles_file_path');
			
			$fileName = $title.'.'.$file->extension();
            $file->move(public_path($path), $fileName);
			$grfiles = Grfiles::create(['title' => $title,'gr_file'=>$fileName,'state_id'=>$state_id]);
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.grfile_create',['name' => $request->input('title')]);
            storeActicityLog(trans('messages.grfile_create'),$message,Auth::user(),$grfiles);
             return redirect()->route('grfiles.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('grfiles.index');   
            
        }
     
    }

    /**
     * Get Particular grfiles
     * @param int $id (grfiles Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $grfiles = Grfiles::find($id);
		$states = $this->stateRepo->getStates();
        return view('backend.grfiles.create',['grfiles' => $grfiles,'url' => $this->url,'states'=>$states]);  
    }

     /**
     * Update grfiles
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
            $grfiles = Grfiles::find($id);
			$file = $request->gr_file;
			$title = $request->title;
            if($request->gr_file)
            {
                $this->removeFile($grfiles->gr_file,'grfiles');
				
				if($request->gr_file!='')
				{
					$file = $request->gr_file;
					$title = $request->title;
					
					$extension = $file->getClientOriginalExtension();
					$path = Config::get('constants.file.grfiles_file_path');
				
					$fileName = $title.'.'.$file->extension();
					$file->move(public_path($path), $fileName);
					 $grfiles->gr_file = $fileName;
				}
				
            }
			
            $grfiles->state_id = $request->input('state_id');
			$grfiles->title = $request->input('title');
			
            $grfiles->save();
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.grfiles_update',['name' => $request->input('title')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$grfiles);
            return redirect()->route('grfiles.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('grfiles.index'); 
        }
    }

    /**
     * Delete grfiles
     * @param int $id (grfiles Id)
     * @return Route
     */
    public function delete($id){ 
        $grfiles = Grfiles::where('id',$id)->first();
        $this->removeFile($grfiles->title,'grfiles');

        $grfiles->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.grfiles_delete',['name' => $grfiles->title]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$grfiles);
        return redirect()->route('grfiles.index');
    }

    public function getDownload($file_name){

        $file = public_path()."/upload/grfiles/".urldecode($file_name);
		$headers = array('Content-Type: application/pdf',
						'Access-Control-Allow-Origin:*','Access-Control-Allow-Methods:
		GET, POST, PUT, DELETE, OPTIONS');
        return Response :: download($file);
        
       //  return response()->download($file, $file_name, $headers);
        return Response::download($file,$file_name, $headers);
    }

}
