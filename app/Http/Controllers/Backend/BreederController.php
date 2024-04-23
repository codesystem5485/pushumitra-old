<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Breeder;
use App\Models\BreederImages;
use App\Models\Breeds;
use App\Models\Species;
use App\Models\AnimalType;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\BreederProcessRequest;
use App\Repositories\Interfaces\Breeder\BreederRepositoryInterface;
use App\Repositories\Interfaces\State\StateRepositoryInterface;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;

class BreederController extends Controller
{
    use FileUpload;
    protected $url = '';
    protected $breederRepo;
	 protected $stateRepo;
    /**
     * Breeder Construct 
     * @return url 
     */
    public function __construct(BreederRepositoryInterface $breederRepo,StateRepositoryInterface $stateRepo){

      /*  $this->middleware('permission:animal-sale-list|animal-sale-create|animal-sale-edit|animal-sale-delete', ['only' => ['index','show']]);
        $this->middleware('permission:animal-sale-create', ['only' => ['create','store']]);
        $this->middleware('permission:animal-sale-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:animal-sale-delete', ['only' => ['delete']]);*/

        $this->url = [   
            'listUrl' => route('breeders.index'),
            'createUrl' => route('breeders.create')
        ];
        $this->breederRepo = $breederRepo;
		$this->stateRepo = $stateRepo;
    } 

    /**
     * breeders List
     * @return View
     */
    public function index(){
        $breeders = Breeder::select( 'breeders.*')->where('status',1)->orderBy('id','DESC')->get();
        return view('backend.breeders.index',['breeders'=>$breeders,'url' => $this->url]); 
    }

    /**
     * Add breeders View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        
        return view('backend.breeders.create',['permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store breeders
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(BreederProcessRequest $request){
        
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $breeder = $this->breederRepo->create($aInsertData);
            if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'breederanimals');
                    if($fileName)
                    {
                        $animalImage = BreederImages::create(['breeder_is'=>$breeder->id,'image_name' => $fileName]);
                    }
                }
            }
            
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.breeder_create',['name' => $request->input('UID_number')]);
            storeActicityLog(trans('messages.breeder_create'),$message,Auth::user(),$breeder);
            return redirect()->route('breeders.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('breeders.index');               
        }
     
    }

    /**
     * Get Particular breeder
     * @param int $id (breeder Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $breeder = Breeder::find($id);
        $states = $this->stateRepo->getStates();
        $animalimages = BreederImages::where('breeder_id',$breeder->id)->get();
	
        return view('backend.breeders.create',['animalimages'=>$animalimages,'states'=>$states,'breeder' => $breeder,'url' => $this->url]);  
    }

     /**
     * Update brreder
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(BreederProcessRequest $request, $id) 
    {
       DB::beginTransaction();
        try{
            $aInsertData = $request->all();
		
            $breeder = $this->breederRepo->update($id,$request->all());
            
			if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'breederanimals');
                    if($fileName)
                    {
                        $animalImage = BreederImages::create(['breeder_id'=>$breeder->id,'image_name' => $fileName]);
                    }
                } 
            }
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.breeder_update',['name' => $request->input('breeder_name')]);
            storeActicityLog(trans('messages.breeder_update'),$message,Auth::user(),$breeder);
            return redirect()->route('breeders.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('breeders.index'); 
        }
    }

    public function detail(Request $request, $id = ''){
        $breeder = Breeder::select('breeders.*')
		->where('breeders.id',$id)
		->first();
		$animalimages = BreederImages::where('breeder_id',$breeder->id)->get();
        return view('backend.breeders.detail',['breeder' => $breeder,'animalimages'=>$animalimages,'url' => $this->url]);  
    }

    /**
     * Delete breeder
     * @param int $id (breeder Id)
     * @return Route
     */
    public function delete($id){ 
        $breeder = Breeder::where('id',$id)->first();
        $animalImages = BreederImages::where('breeder_id',$id)->get();
        if($animalImages)
        {
            if(count($animalImages)>0)
            {
                foreach($animalImages as $image)
                {
                    $this->removeFile($image->image_name,'breederanimals');
                }
            }
        }
        $breederImages = BreederImages::where('breeder_id',$id)->delete();
		$arr = array('status'=>0);
		$breeder = $this->breederRepo->update($id,$arr);
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.breeder_delete',['name' => $breeder->breeder_name]);
        storeActicityLog(trans('messages.breeder_delete'),$message,Auth::user(),$breeder);
        return redirect()->route('breeders.index');
    }

    public function removeImage($id)
    {
        $breederImages = BreederImages::where('id',$id)->first();
        $this->removeFile($breederImages->image_name,'breederanimals');
        $breederImages->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.breeder_delete_remove',['name' => $breederImages->id]);
        storeActicityLog(trans('messages.breeder_delete_remove'),$message,Auth::user(),$animalImage);
       
        return true;
    }

}
