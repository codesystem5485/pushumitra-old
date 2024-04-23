<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ngo;
use App\Models\NgoImages;
use App\Models\State;
use App\Repositories\Interfaces\Ngo\NgoRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Http\Requests\NgoProcessRequest;
use App\Models\Subcategories;

class NgoController extends Controller
{
    use FileUpload;
    protected $ngoRepo;
	
    /**
     * ngo Construct 
     * @return url 
     */
    public function __construct(NgoRepositoryInterface $ngoRepo){

        $this->middleware('permission:ngo-list|ngo-create|ngo-edit|ngo-delete', ['only' => ['index','show']]);
        $this->middleware('permission:ngo-create', ['only' => ['create','store']]);
        $this->middleware('permission:ngo-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:ngo-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('ngo.index'),
            'createUrl' => route('ngo.create')
        ];
        $this->ngoRepo = $ngoRepo;
    } 

    /**
     * ngo List
     * @return View
     */
    public function index(){
        
        return view('backend.ngo.index',['url' => $this->url]); 
    }

    /**
     * Add ngo View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = State::where('is_active','1')->get();
        return view('backend.ngo.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store ngo
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(NgoProcessRequest $request){
        
        DB::beginTransaction();
        // try{            
            $aInsertData = $request->all();
            $ngos = $this->ngoRepo->create($aInsertData);

             if($request->ngo_photo)
            {
                foreach($request->ngo_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'ngo');
                    if($fileName)
                    {
                        NgoImages::create(['ngo_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.ngo_create',['name' => $request->input('ngo_name')]);
            storeActicityLog(trans('messages.ngo_create'),$message,Auth::user(),$ngos);
            return redirect()->route('ngo.index');
        // }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('ngo.index');   
            
        // }
     
    }

    /**
     * Get Particular ngos
     * @param int $id (ngos Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $ngos = Ngo::find($id);
        $images = NgoImages::where('ngo_id',$ngos->id)->get();
        $states = State::where('is_active','1')->get();
		
		return view('backend.ngo.create',['images'=>$images,'states'=>$states,'ngos' => $ngos,'url' => $this->url]);  
    }

     /**
     * Update ngos
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(NgoProcessRequest $request, $id) 
    {
       
            $ngos = $this->ngoRepo->update($id,$request->all());

             if($request->ngo_photo)
            {
                foreach($request->ngo_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'ngo');
                    if($fileName)
                    {
                        NgoImages::create(['ngo_id'=>$ngos->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.ngo_update',['name' => $request->input('ngo_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$ngos);
            return redirect()->route('ngo.index');    
       /* }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('ngo.index'); 
        }*/
    }

    /**
     * Get Particular ngo
     * @param int $id (ngo Id) Request $request
     * @return View
     */
    public function detail(Request $request, $id = ''){
        $ngos = Ngo::find($id);
		$images = NgoImages::where('ngo_id',$ngos->id)->get();
        $states = State::where('is_active','1')->get();
        return view('backend.ngo.detail',['images'=>$images,'states'=>$states,'ngos' => $ngos,'url' => $this->url]);  
    }

    /**
     * Delete ngos
     * @param int $id (ngos)
     * @return Route
     */
    public function delete($id){ 
        $ngos = Ngo::where('id',$id)->first();
		if($ngos){
			$name = $ngos->ngo_name;
			$images = NgoImages::where('ngo_id',$ngos->id)->get();
			
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					$this->removeFile($image->image_name,'ngo');
					$image->delete();
				}
			}
			$arr = array('status'=>0);
			$ngo = $this->ngoRepo->update($id,$arr);
		}
       // $ngos->delete();
        Session::flash('success', trans('messages.delete_records'));
		
        ## Store log ngos
        $message = trans('messages.ngo_delete',['name' => $ngos->ngo_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$ngos);
        return redirect()->route('ngo.index');
    }

    public function removeImage($id)
    {
        $image = NgoImages::where('id',$id)->first();
        $this->removeFile($image->image_name,'ngo');
        $image->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.image_remove',['name' => $image->id]);
        storeActicityLog(trans('messages.image_remove'),$message,Auth::user(),$image);
        return true;
    }
	
	public function getAjaxList(Request $request){
        $list = $this->ngoRepo->getAjaxList();
        return  $list;
    }
}