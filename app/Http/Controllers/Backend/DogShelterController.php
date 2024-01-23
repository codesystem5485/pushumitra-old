<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DogShelters;
use App\Models\DogshelterImages;
use App\Models\State;
use App\Repositories\Interfaces\Dogshelters\DogsheltersRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Http\Requests\DogShelterProcessRequest;
use App\Models\Subcategories;

class DogShelterController extends Controller
{
    use FileUpload;
    protected $dogsheltersRepo;
	
    /**
     * dogshelters Construct 
     * @return url 
     */
    public function __construct(DogsheltersRepositoryInterface $dogsheltersRepo){

        $this->middleware('permission:dogshelter-list|dogshelter-create|dogshelter-edit|dogshelter-delete', ['only' => ['index','show']]);
        $this->middleware('permission:dogshelter-create', ['only' => ['create','store']]);
        $this->middleware('permission:dogshelter-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:dogshelter-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('dogshelters.index'),
            'createUrl' => route('dogshelters.create')
        ];
        $this->dogsheltersRepo = $dogsheltersRepo;
    } 

    /**
     * dogshelters List
     * @return View
     */
    public function index(){
       
        return view('backend.dogshelters.index',['dogshelters'=>[],'url' => $this->url]); 
    }

    /**
     * Add dogshelter View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = State::where('is_active','1')->get();
        return view('backend.dogshelters.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store dogshelters
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(DogShelterProcessRequest $request){
        
        DB::beginTransaction();
        // try{            
            $aInsertData = $request->all();
            $dogshelters = $this->dogsheltersRepo->create($aInsertData);

            if($request->dogshelter_photo)
            {
                foreach($request->dogshelter_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'dogshelters');
                    if($fileName)
                    { 
                        DogshelterImages::create(['dog_shelter_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.dogshelters_create',['name' => $request->input('dogshelter_name')]);
            storeActicityLog(trans('messages.dogshelters_create'),$message,Auth::user(),$dogshelters);
            return redirect()->route('dogshelters.index');
        // }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('dogshelters.index');   
            
        // }
     
    }

    /**
     * Get Particular dogshelters
     * @param int $id (dogshelters Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $dogshelters = DogShelters::find($id);
        $images = DogshelterImages::where('dog_shelter_id',$dogshelters->id)->get();
        $states = State::where('is_active','1')->get();
		//$subcategories = Subcategories::where('status',1)->where('parent_category',6)->get();
		return view('backend.dogshelters.create',['subcategories'=>[],'images'=>$images,'states'=>$states,'dogshelters' => $dogshelters,'url' => $this->url]);  
    }

     /**
     * Update dogshelters
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(DogShelterProcessRequest $request, $id) 
    {
        $dogshelters = $this->dogsheltersRepo->update($id,$request->all());

		if($request->dogshelter_photo)
		{
			foreach($request->dogshelter_photo as $photo)
			{
				$fileName ='';
				$fileName = $this->uploadFile($photo,'dogshelters');
				if($fileName)
				{ 
					DogshelterImages::create(['dog_shelter_id'=>$results->id,'image_name' => $fileName]);
				}
			}
		}
		Session::flash('success', trans('messages.update_records'));

		## Store log
		$message = trans('messages.dogshelters_update',['name' => $request->input('dogshelter_name')]);
		storeActicityLog(trans('messages.update'),$message,Auth::user(),$dogshelters);
		return redirect()->route('dogshelters.index');
    }

    /**
     * Get Particular dogshelters
     * @param int $id (dogshelters Id) Request $request
     * @return View
     */
    public function detail(Request $request, $id = ''){
        $dogshelters = DogShelters::find($id);
		$images = DogshelterImages::where('dog_shelter_id',$dogshelters->id)->get();
        $states = State::where('is_active','1')->get();
        return view('backend.dogshelters.detail',['images'=>$images,'states'=>$states,'dogshelters' => $dogshelters,'url' => $this->url]);  
    }

    /**
     * Delete dogshelters
     * @param int $id (dogshelters)
     * @return Route
     */
    public function delete($id)
	{ 
        $dogshelters = DogShelters::where('id',$id)->first();
        $dogshelters->delete();
		$images = DogshelterImages::where('dog_shelter_id',$id)->get();
        if($images)
        {
            if(count($images)>0)
            {
                foreach($images as $image)
                {
                    $this->removeFile($image->image_name,'dogshelters');
                }
            }
        }
        $moduleimages = DogshelterImages::where('dog_shelter_id',$id)->delete();
        Session::flash('success', trans('messages.delete_records'));
		
        ## Store log dogshelters
        $message = trans('messages.dogshelter_delete',['name' => $dogshelters->dogshelter_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$dogshelters);
        return redirect()->route('dogshelters.index');
    }

    public function removeImage($id)
    {
        $image = DogshelterImages::where('id',$id)->first();
        $this->removeFile($image->image_name,'dogshelters');
        $image->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.image_remove',['name' => $image->id]);
        storeActicityLog(trans('messages.image_remove'),$message,Auth::user(),$image);
        return true;
    }
	
	public function getAjaxList(Request $request){
        $list = $this->dogsheltersRepo->getAjaxList();
        return  $list;
    }
}