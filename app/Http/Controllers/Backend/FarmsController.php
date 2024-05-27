<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farms;
use App\Models\FarmsImages;
use App\Models\State;
use App\Repositories\Interfaces\Farms\FarmsRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Http\Requests\FarmsProcessRequest;
use App\Models\Subcategories;

class FarmsController extends Controller
{
    use FileUpload;
    protected $farmsRepo;
	
    /**
     * farms Construct 
     * @return url 
     */
    public function __construct(FarmsRepositoryInterface $farmsRepo, UserRepositoryInterface $userRepository){

      /*  $this->middleware('permission:transporter-list|transporter-create|transporter-edit|transporter-delete', ['only' => ['index','show']]);
        $this->middleware('permission:transporter-create', ['only' => ['create','store']]);
        $this->middleware('permission:transporter-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:transporter-delete', ['only' => ['delete']]);*/

        $this->url = [   
            'listUrl' => route('farms.index'),
            'createUrl' => route('farms.create')
        ];
        $this->farmsRepo = $farmsRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * farms List
     * @return View
     */
    public function index(){
        $farms= Farms::orderBy('id','DESC')->get();
        return view('backend.farms.index',['farms'=>$farms,'url' => $this->url]); 
    }

    /**
     * Add farms View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = State::where('is_active','1')->get();
        return view('backend.farms.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store farmsRepo
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(FarmsProcessRequest $request){
        
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $farms = $this->farmsRepo->create($aInsertData);

             if($request->farm_photo)
            {
                foreach($request->farm_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'farms');
                    if($fileName)
                    {
                        FarmsImages::create(['farm_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.farm_create',['name' => $request->input('farm_name')]);
            storeActicityLog(trans('messages.farm_create'),$message,Auth::user(),$farms);
            return redirect()->route('farms.index');
         }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('farms.index');
        }
     
    }

    /**
     * Get Particular farms
     * @param int $id (farms) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $farms = Farms::find($id);
        $images = FarmsImages::where('farm_id',$farms->id)->get();
        $states = State::where('is_active','1')->get();
		$subcategories = Subcategories::where('status',1)->where('parent_category',3)->get();
		return view('backend.farms.create',['subcategories'=>$subcategories,'images'=>$images,'states'=>$states,'farms' => $farms,'url' => $this->url]);  
    }

     /**
     * Update farms
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(FarmsProcessRequest $request, $id) 
    {
		//DB::beginTransaction();
        //try{
            $farms = $this->farmsRepo->update($id,$request->all());

            if($request->farm_photo)
            {
                foreach($request->farm_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'farms');
                    if($fileName)
                    {
                        FarmsImages::create(['farm_id'=>$farms->id,'image_name' => $fileName]);
                    }
                }
            }

			$coordinateArr = $this->userRepo->getLatitudeLongitudes($farms);
			$farms->latitude=$coordinateArr['latitude'];
			$farms->longitude=$coordinateArr['longitude'];
			$farms->update();
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.farms_update',['name' => $request->input('farm_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$farms);
            return redirect()->route('farms.index');    
       /* }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('farms.index'); 
        }*/
    }

    /**
     * Get Particular farms
     * @param int $id (farms Id) Request $request
     * @return View
     */
    public function detail(Request $request, $id = ''){
        $farms = Farms::find($id);
		$images = FarmsImages::where('farm_id',$farms->id)->get();
        $states = State::where('is_active','1')->get();
        return view('backend.farms.detail',['images'=>$images,'states'=>$states,'farms' => $farms,'url' => $this->url]);  
    }

    /**
     * Delete farms
     * @param int $id (farms)
     * @return Route
     */
    public function delete($id){ 
        $farms = Farms::where('id',$id)->first();
		if($farms){
			$name = $farms->farm_name;
			$images = FarmsImages::where('farm_id',$farms->id)->get();
			
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					$this->removeFile($image->image_name,'farms');
					$image->delete();
				}
			}
			$arr = array('status'=>0);
			$farms = $this->farmsRepo->update($id,$arr);
		}
        //$farms->delete();
        Session::flash('success', trans('messages.delete_records'));
		
        ## Store log farms
        $message = trans('messages.farms_delete',['name' => $farms->farm_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$farms);
        return redirect()->route('farms.index');
    }

    public function removeImage($id)
    {
        $image = FarmsImages::where('id',$id)->first();
        $this->removeFile($image->image_name,'farms');
        $image->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.image_remove',['name' => $image->id]);
        storeActicityLog(trans('messages.image_remove'),$message,Auth::user(),$image);
        return true;
    }
	
	public function getAjaxList(Request $request){
        $list = $this->farmsRepo->getAjaxList();
        return  $list;
    }
}