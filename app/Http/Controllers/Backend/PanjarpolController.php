<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Panjarpol;
use App\Models\PanjarpolImages;
use App\Models\State;
use App\Repositories\Interfaces\Panjarpol\PanjarpolRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Http\Requests\PanjarpolProcessRequest;
use App\Models\Subcategories;

class PanjarpolController extends Controller
{
    use FileUpload;
    protected $panjarpolRepo;
	
    /**
     * panjarpol Construct 
     * @return url 
     */
    public function __construct(PanjarpolRepositoryInterface $panjarpolRepo,UserRepositoryInterface $userRepository){

        $this->middleware('permission:panjarpol-list|panjarpol-create|panjarpol-edit|panjarpol-delete', ['only' => ['index','show']]);
        $this->middleware('permission:panjarpol-create', ['only' => ['create','store']]);
        $this->middleware('permission:panjarpol-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:panjarpol-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('panjarpols.index'),
            'createUrl' => route('panjarpols.create')
        ];
        $this->panjarpolRepo = $panjarpolRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * panjarpols List
     * @return View
     */
    public function index(){
        
        return view('backend.panjarpols.index',['url' => $this->url]); 
    }

    /**
     * Add panjarpols View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = State::where('is_active','1')->get();
        return view('backend.panjarpols.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store panjarpols
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(PanjarpolProcessRequest $request){
        
        DB::beginTransaction();
        // try{            
            $aInsertData = $request->all();
            $panjarpols = $this->panjarpolRepo->create($aInsertData);

             if($request->panjarpol_photo)
            {
                foreach($request->panjarpol_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'panjarpol');
                    if($fileName)
                    {
                        PanjarpolImages::create(['panjarpol_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.panjarpols_create',['name' => $request->input('panjarpol_name')]);
            storeActicityLog(trans('messages.panjarpols_create'),$message,Auth::user(),$panjarpols);
            return redirect()->route('panjarpols.index');
        // }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('panjarpols.index');   
            
        // }
     
    }

    /**
     * Get Particular panjarpols
     * @param int $id (panjarpols Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $panjarpols = Panjarpol::find($id);
        $images = PanjarpolImages::where('panjarpol_id',$panjarpols->id)->get();
        $states = State::where('is_active','1')->get();
		$subcategories = Subcategories::where('status',1)->where('parent_category',4)->get();
		return view('backend.panjarpols.create',['subcategories'=>$subcategories,'images'=>$images,'states'=>$states,'panjarpols' => $panjarpols,'url' => $this->url]);  
    }

     /**
     * Update panjarpols
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(PanjarpolProcessRequest $request, $id) 
    {
       
            $panjarpols = $this->panjarpolRepo->update($id,$request->all());

             if($request->panjarpol_photo)
            {
                foreach($request->panjarpol_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'panjarpol');
                    if($fileName)
                    {
                        PanjarpolImages::create(['panjarpol_id'=>$panjarpols->id,'image_name' => $fileName]);
                    }
                }
            }
			
			$coordinateArr = $this->userRepo->getLatitudeLongitudes($panjarpols);
			$panjarpols->latitude=$coordinateArr['latitude'];
			$panjarpols->longitude=$coordinateArr['longitude'];
			$panjarpols->update();
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.panjarpols_update',['name' => $request->input('panjarpol_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$panjarpols);
            return redirect()->route('panjarpols.index');    
       /* }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('panjarpols.index'); 
        }*/
    }

    /**
     * Get Particular panjarpols
     * @param int $id (panjarpol Id) Request $request
     * @return View
     */
    public function detail(Request $request, $id = ''){
        $panjarpols = Panjarpol::find($id);
		$images = PanjarpolImages::where('panjarpol_id',$panjarpols->id)->get();
        $states = State::where('is_active','1')->get();
        return view('backend.panjarpols.detail',['images'=>$images,'states'=>$states,'panjarpols' => $panjarpols,'url' => $this->url]);  
    }

    /**
     * Delete panjarpols
     * @param int $id (panjarpols)
     * @return Route
     */
    public function delete($id){ 
        $panjarpols = Panjarpol::where('id',$id)->first();
        $panjarpols->delete();
        Session::flash('success', trans('messages.delete_records'));
		
        ## Store log panjarpols
        $message = trans('messages.panjarpols_delete',['name' => $panjarpols->panjarpol_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$panjarpols);
        return redirect()->route('panjarpols.index');
    }

    public function removeImage($id)
    {
        $image = PanjarpolImages::where('id',$id)->first();
        $this->removeFile($image->image_name,'panjarpol');
        $image->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.image_remove',['name' => $image->id]);
        storeActicityLog(trans('messages.image_remove'),$message,Auth::user(),$image);
        return true;
    }
	
	public function getAjaxList(Request $request){
        $list = $this->panjarpolRepo->getAjaxList();
        return  $list;
    }
}