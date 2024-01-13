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
use App\Http\Requests\VetHospitalsRepoProcessRequest;

class VetHospitalsController extends Controller
{
    use FileUpload;
    protected $url = '';
    protected $vethospitalsRepo;
    /**
     * Hospitals Construct 
     * @return url 
     */
    public function __construct(VethospitalsRepositoryInterface $vethospitalsRepo){

      /*  $this->middleware('permission:transporter-list|transporter-create|transporter-edit|transporter-delete', ['only' => ['index','show']]);
        $this->middleware('permission:transporter-create', ['only' => ['create','store']]);
        $this->middleware('permission:transporter-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:transporter-delete', ['only' => ['delete']]);*/

        $this->url = [   
            'listUrl' => route('hospitals.index'),
            'createUrl' => route('hospitals.create')
        ];
        $this->vethospitalsRepo = $vethospitalsRepo;
    } 

    /**
     * hospitals List
     * @return View
     */
    public function index(){
        $hospitals = Veterinaryhospitals::orderBy('id','DESC')->get();
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
    public function store(VetHospitalsRepoProcessRequest $request){
        
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
    public function update(VetHospitalsRepoProcessRequest $request, $id) 
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
}