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

class TrainingCentersController extends Controller
{
    use FileUpload;
    protected $trainingcenterRepo;
	
    /**
     * TrainingCenters Construct 
     * @return url 
     */
    public function __construct(TrainingcentersRepositoryInterface $trainingcenterRepo){

      /*  $this->middleware('permission:transporter-list|transporter-create|transporter-edit|transporter-delete', ['only' => ['index','show']]);
        $this->middleware('permission:transporter-create', ['only' => ['create','store']]);
        $this->middleware('permission:transporter-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:transporter-delete', ['only' => ['delete']]);*/

        $this->url = [   
            'listUrl' => route('trainingcenters.index'),
            'createUrl' => route('trainingcenters.create')
        ];
        $this->trainingcenterRepo = $trainingcenterRepo;
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
            $message = trans('messages.trainingcenters_update',['name' => $request->input('hospital_name')]);
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
        $message = trans('messages.trainingcenters_delete',['name' => $trainingcenters->hospital_name]);
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
}