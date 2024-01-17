<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transporters;
use App\Models\VehicleImages;
use App\Models\State;
use App\Models\Cities;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\TransporterProcessRequest;
use App\Repositories\Interfaces\Transporter\TransporterRepositoryInterface;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;

class TransporterController extends Controller
{
    use FileUpload;
    protected $url = '';
    protected $transporterRepo;
    /**
     * Transporter Construct 
     * @return url 
     */
    public function __construct(TransporterRepositoryInterface $transporterRepo){

        $this->middleware('permission:transporter-list|transporter-create|transporter-edit|transporter-delete', ['only' => ['index','show']]);
        $this->middleware('permission:transporter-create', ['only' => ['create','store']]);
        $this->middleware('permission:transporter-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:transporter-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('transporter.index'),
            'createUrl' => route('transporter.create')
        ];
        $this->transporterRepo = $transporterRepo;
    } 

    /**
     * Transporter List
     * @return View
     */
    public function index(){
        $transporter = Transporters::orderBy('id','ASC')->get();
        return view('backend.transporter.index',['transporter'=>$transporter,'url' => $this->url]); 
    }

    /**
     * Add Transporter View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = State::where('is_active','1')->get();
        return view('backend.transporter.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Transporter
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(TransporterProcessRequest $request){
        
        DB::beginTransaction();
        // try{            
            $aInsertData = $request->all();
            $transporter = $this->transporterRepo->create($aInsertData);

            if($request->vehicle_photo)
            {
                foreach($request->vehicle_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'vehicle');
                    if($fileName)
                    {
                        VehicleImages::create(['transporter_id'=>$transporter->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.transporter_create',['name' => $request->input('transporter_name')]);
            storeActicityLog(trans('messages.transporter_create'),$message,Auth::user(),$transporter);
            return redirect()->route('transporter.index');
        // }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('transporter.index');   
            
        // }
     
    }

    /**
     * Get Particular Transporter
     * @param int $id (Transporter Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $transporter = Transporters::find($id);
        $vehicleimages = VehicleImages::where('transporter_id',$transporter->id)->get();
        $states = State::where('is_active','1')->get();
        $cities = Cities::where('state_id',$transporter->state_id)->get();        
        return view('backend.transporter.create',['vehicleimages'=>$vehicleimages,'cities'=>$cities,'states'=>$states,'transporter' => $transporter,'url' => $this->url]);  
    }

     /**
     * Update Transporter
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(TransporterProcessRequest $request, $id) 
    {
        DB::beginTransaction();
        try{
            $transporter = $this->transporterRepo->update($id,$request->all());

            if($request->vehicle_photo)
            {
                foreach($request->vehicle_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'vehicle');
                    if($fileName)
                    {
                        VehicleImages::create(['transporter_id'=>$transporter->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.transporter_update',['name' => $request->input('transporter_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$transporter);
            return redirect()->route('transporter.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('transporter.index'); 
        }
    }

    /**
     * Get Particular Transporter
     * @param int $id (Transporter Id) Request $request
     * @return View
     */
    public function detail(Request $request, $id = ''){
        $transporter = Transporters::find($id);
        $states = State::where('is_active','1')->get();
		
		$images_arr = VehicleImages::where('transporter_id',$transporter->id)->get();
		$images_rcbook_arr = VehicleImages::where('transporter_id',$transporter->id)->get();
              
        return view('backend.transporter.detail',['images'=>$images_arr,'images_rcbook'=>$images_rcbook_arr,'states'=>$states,'transporter' => $transporter,'url' => $this->url]);  
    }

    /**
     * Delete Transporter
     * @param int $id (Transporter Id)
     * @return Route
     */
    public function delete($id){ 
        $transporter = Transporters::where('id',$id)->first();
        $transporter->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.transporter_delete',['name' => $transporter->transporter_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$transporter);
        return redirect()->route('transporter.index');
    }

    public function removeImage($id)
    {
        $vehicleImage = VehicleImages::where('id',$id)->first();
        $this->removeFile($vehicleImage->image_name,'vehicle');
        $vehicleImage->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.vehicle_remove',['name' => $vehicleImage->id]);
        storeActicityLog(trans('messages.vehicle_remove'),$message,Auth::user(),$vehicleImage);
        // return redirect()->route('product-sale.edit',$vehicleImage->id);
        return true;
    }
	
	public function getAjaxList(Request $request){
        $list = $this->transporterRepo->getAjaxList();
        return  $list;
    }
}