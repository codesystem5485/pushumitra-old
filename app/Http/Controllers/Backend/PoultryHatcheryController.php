<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PoultryHatchery;
use App\Models\PoultryHatcheryImages;
use App\Models\State;
use App\Repositories\Interfaces\Poultryhatchery\PoultryhatcheryRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Http\Requests\PoultryhatcheryProcessRequest;

class PoultryHatcheryController extends Controller
{
    use FileUpload;
    protected $poultryhatcheryRepo;
	
    /**
     * poultryhatchery Construct 
     * @return url 
     */
    public function __construct(PoultryhatcheryRepositoryInterface $poultryhatcheryRepo){

        $this->middleware('permission:poultryhatchery-list|poultryhatchery-create|poultryhatchery-edit|poultryhatchery-delete', ['only' => ['index','show']]);
        $this->middleware('permission:poultryhatchery-create', ['only' => ['create','store']]);
        $this->middleware('permission:poultryhatchery-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:poultryhatchery-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('poultryhatchery.index'),
            'createUrl' => route('poultryhatchery.create')
        ];
        $this->poultryhatcheryRepo = $poultryhatcheryRepo;
    } 

    /**
     * poultryhatchery List
     * @return View
     */
    public function index(){
        
        return view('backend.poultryhatchery.index',['url' => $this->url]); 
    }

    /**
     * Add poultryhatchery View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = State::where('is_active','1')->get();
        return view('backend.poultryhatchery.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store poultryhatchery
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(PoultryhatcheryProcessRequest $request){
        
        DB::beginTransaction();
        // try{            
            $aInsertData = $request->all();
            $poultryhatchery = $this->poultryhatcheryRepo->create($aInsertData);

             if($request->poultryhatchery_photo)
            {
                foreach($request->poultryhatchery_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'poultryhatchery');
                    if($fileName)
                    {
                        PoultryHatcheryImages::create(['poultryhatchery_center_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.poultryhatchery_create',['name' => $request->input('poultryhatchery_center_name')]);
            storeActicityLog(trans('messages.poultryhatchery_create'),$message,Auth::user(),$poultryhatchery);
            return redirect()->route('poultryhatchery.index');
        // }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('poultryhatchery.index');   
            
        // }
     
    }

    /**
     * Get Particular poultryhatchery
     * @param int $id (poultryhatchery Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $poultryhatchery = PoultryHatchery::find($id);
        $images = PoultryHatcheryImages::where('poultryhatchery_center_id',$poultryhatchery->id)->get();
        $states = State::where('is_active','1')->get();
		
		return view('backend.poultryhatchery.create',['images'=>$images,'states'=>$states,'poultryhatchery' => $poultryhatchery,'url' => $this->url]);  
    }

     /**
     * Update poultryhatchery
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(PoultryhatcheryProcessRequest $request, $id) 
    {
       
            $poultryhatchery = $this->poultryhatcheryRepo->update($id,$request->all());

             if($request->poultryhatchery_photo)
            {
                foreach($request->poultryhatchery_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'poultryhatchery');
                    if($fileName)
                    {
                        PoultryHatcheryImages::create(['poultryhatchery_center_id'=>$poultryhatchery->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.poultryhatchery_update',['name' => $request->input('poultryhatchery_center_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$poultryhatchery);
            return redirect()->route('poultryhatchery.index');    
       /* }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('poultryhatchery.index'); 
        }*/
    }

    /**
     * Get Particular poultryhatchery
     * @param int $id (poultryhatchery Id) Request $request
     * @return View
     */
    public function detail(Request $request, $id = ''){
        $poultryhatchery = PoultryHatchery::find($id);
		$images = PoultryHatcheryImages::where('poultryhatchery_center_id',$poultryhatchery->id)->get();
        $states = State::where('is_active','1')->get();
        return view('backend.poultryhatchery.detail',['images'=>$images,'states'=>$states,'poultryhatchery' => $poultryhatchery,'url' => $this->url]);  
    }

    /**
     * Delete poultryhatchery
     * @param int $id (poultryhatchery)
     * @return Route
     */
    public function delete($id){ 
        $poultryhatchery = PoultryHatchery::where('id',$id)->first();
		$images = PoultryHatcheryImages::where('poultryhatchery_center_id',$id)->get();
        if($images)
        {
            if(count($images)>0)
            {
                foreach($images as $image)
                {
                    $this->removeFile($image->image_name,'poultryhatchery');
                }
				$images = PoultryHatcheryImages::where('poultryhatchery_center_id',$id)->delete();
            }
        }
        
        $poultryhatchery->delete();
        Session::flash('success', trans('messages.delete_records'));
		
        ## Store log poultryhatchery
        $message = trans('messages.poultryhatchery_delete',['name' => $poultryhatchery->poultryhatchery_center_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$poultryhatchery);
        return redirect()->route('poultryhatchery.index');
    }

    public function removeImage($id)
    {
        $image = PoultryHatcheryImages::where('id',$id)->first();
        $this->removeFile($image->image_name,'poultryhatchery');
        $image->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.image_remove',['name' => $image->id]);
        storeActicityLog(trans('messages.image_remove'),$message,Auth::user(),$image);
        return true;
    }
	
	public function getAjaxList(Request $request){
        $list = $this->poultryhatcheryRepo->getAjaxList();
        return  $list;
    }
}