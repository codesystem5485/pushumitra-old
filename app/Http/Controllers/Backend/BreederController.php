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
     * Animal Sale Construct 
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
        $breeders = Breeder::select( 'breeders.*')->orderBy('id','DESC')->get();
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
            $breeder = $this->animalsaleRepo->create($aInsertData);
            if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'breeder');
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
        $breederImages = BreederImages::where('breeder_id',$breeder->id)->get();
        return view('backend.breeders.create',['breederImages'=>$breederImages,'states'=>$states,'breeder' => $breeder,'url' => $this->url]);  
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
            $animalsale = $this->breederRepo->update($id,$request->all());
             if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'breeder');
                    if($fileName)
                    {
                        $breederImage = BreederImages::create(['breeder_id'=>$animalsale->id,'image_name' => $fileName]);
                    }
                }
            }
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.breeder_update',['name' => $request->input('shop_name')]);
            storeActicityLog(trans('messages.breeder_update'),$message,Auth::user(),$animalsale);
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
     * Delete Animal for sale
     * @param int $id (Animal for sale Id)
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
                    $this->removeFile($image->image_name,'breeder');
                }
            }
        }
        $breederImages = BreederImages::where('breeder_id',$id)->delete();
        // $animalImages->delete();
        $breeder->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.breeder_delete',['name' => $breeder->breeder_name]);
        storeActicityLog(trans('messages.breeder_delete'),$message,Auth::user(),$breeder);
        return redirect()->route('breeders.index');
    }

    public function removeImage($id)
    {
        $breederImages = BreederImages::where('id',$id)->first();
        $this->removeFile($breederImages->image_name,'breeder');
        $breederImages->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.animalsale_remove',['name' => $breederImages->id]);
        storeActicityLog(trans('messages.breeder_delete_remove'),$message,Auth::user(),$animalImage);
        // return redirect()->route('animal-sale.edit',$animalImage->id);
        return true;
    }

}
