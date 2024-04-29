<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnimalForSale;
use App\Models\AnimalImages;
use App\Models\Breeds;
use App\Models\Species;
use App\Models\AnimalType;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\AnimalsaleProcessRequest;
use App\Repositories\Interfaces\Animalsale\AnimalsaleRepositoryInterface;
use App\Repositories\Interfaces\State\StateRepositoryInterface;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;

class AnimalsaleController extends Controller
{
    use FileUpload;
    protected $url = '';
    protected $animalsaleRepo;
    /**
     * Animal Sale Construct 
     * @return url 
     */
    public function __construct(AnimalsaleRepositoryInterface $animalsaleRepo,StateRepositoryInterface $stateRepo){

        $this->middleware('permission:animal-sale-list|animal-sale-create|animal-sale-edit|animal-sale-delete', ['only' => ['index','show']]);
        $this->middleware('permission:animal-sale-create', ['only' => ['create','store']]);
        $this->middleware('permission:animal-sale-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:animal-sale-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('animal-sale.index'),
            'createUrl' => route('animal-sale.create'),
			
        ];
        $this->animalsaleRepo = $animalsaleRepo;
		$this->stateRepo = $stateRepo;
	} 

    /**
     * Animal Sale List
     * @return View
     */
    public function index(Request $request){
		$postData = $request->all();
		$deletereason = '';
		if(isset($postData['delete_reason'])){
			$deletereason = $postData['delete_reason'];
		}
		
        $animalsale = Animalforsale::leftJoin('breeds', 'breeds.id', '=', 'animal_for_sales.breed')
		->leftJoin('species', 'species.id', '=', 'animal_for_sales.species')
		->select( 'animal_for_sales.*','breeds.breed as breed_name','species.specie as species_name');
		
		if($deletereason!=''){
			 $animalsale = $animalsale->where('animal_for_sales.status', 0)
						->where('animal_for_sales.delete_reason', $deletereason);
		}else{
			$animalsale = $animalsale->where('animal_for_sales.status', 1);
		}
		$animalsale = $animalsale->orderBy('id','DESC')->get();
        return view('backend.animal-sale.index',['animalsale'=>$animalsale,'url' => $this->url]); 
    }
	
	public function deleteList()
	{
		$animalsale = Animalforsale::leftJoin('breeds', 'breeds.id', '=', 'animal_for_sales.breed')
		->leftJoin('species', 'species.id', '=', 'animal_for_sales.species')
		->select( 'animal_for_sales.*','breeds.breed as breed_name','species.specie as species_name');
		$animalsale = $animalsale->where('animal_for_sales.status', 0);
		$animalsale = $animalsale->orderBy('id','DESC')->get();
        return view('backend.animal-sale.deleteList',['animalsale'=>$animalsale,'url' => $this->url]);
	}

    /**
     * Add Animal for sale View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $species = Species::where('is_active','1')->get();
        $breed = Breeds::where('is_active','1')->get();
        $AnimalType = AnimalType::where('is_active','1')->get();
        return view('backend.animal-sale.create',['animalType'=>$AnimalType,'breed'=>$breed,'species'=>$species,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Animal for sale
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(AnimalsaleProcessRequest $request){
        
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $animalsale = $this->animalsaleRepo->create($aInsertData);
            if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'animalsale');
                    if($fileName)
                    {
                        $animalImage = AnimalImages::create(['animal_sale_id'=>$animalsale->id,'image_name' => $fileName]);
                    }
                }
            }
            
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.animalsale_create',['name' => $request->input('UID_number')]);
            storeActicityLog(trans('messages.animalsale_create'),$message,Auth::user(),$animalsale);
            return redirect()->route('animal-sale.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('animal-sale.index');               
        }
     
    }

    /**
     * Get Particular Animal for sale
     * @param int $id (Animal for sale Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $animalsale = Animalforsale::find($id);
        $states = $this->stateRepo->getStates();
        $AnimalType = AnimalType::where('is_active','1')->get();
        $animalimages = AnimalImages::where('animal_sale_id',$animalsale->id)->get();
        return view('backend.animal-sale.create',['states'=>$states,'animalType'=>$AnimalType,'animalimages'=>$animalimages,'animalsale' => $animalsale,'url' => $this->url]);  
    }

     /**
     * Update Animal for sale
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(AnimalsaleProcessRequest $request, $id) 
    {
        DB::beginTransaction();
        try{
            $aInsertData = $request->all();
            $animalsale = $this->animalsaleRepo->update($id,$request->all());
             if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'animalsale');
                    if($fileName)
                    {
                        $animalImage = AnimalImages::create(['animal_sale_id'=>$animalsale->id,'image_name' => $fileName]);
                    }
                }
            }
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.animalsale_update',['name' => $request->input('shop_name')]);
            storeActicityLog(trans('messages.animalsale_update'),$message,Auth::user(),$animalsale);
            return redirect()->route('animal-sale.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('animal-sale.index'); 
        }
    }

    public function detail(Request $request, $id = ''){
        $animalsale = Animalforsale::leftJoin('breeds', 'breeds.id', '=', 'animal_for_sales.breed')
		->leftJoin('species', 'species.id', '=', 'animal_for_sales.species')
		->select('animal_for_sales.*','breeds.breed as breed_name','species.specie as specie_name')
		->where('animal_for_sales.id',$id)
		->first();
		$animalimages = AnimalImages::where('animal_sale_id',$animalsale->id)->get();
        return view('backend.animal-sale.detail',['animalsale' => $animalsale,'animalimages'=>$animalimages,'url' => $this->url]);  
    }
	
	public function showDeleteInfo($id)
	{
		 return view('backend.animal-sale.showDeleteInfo',['id'=>$id,'url' => $this->url]); 
    }

    /**
     * Delete Animal for sale
     * @param int $id (Animal for sale Id)
     * @return Route
     */
    public function delete(Request $request, $id = ''){ 
		$this->validate($request, [
            'delete_reason' => 'required',            
            'delete_note' => 'required', 
					
        ]);
        $animalsale = Animalforsale::where('id',$id)->first();
        $animalImages = AnimalImages::where('animal_sale_id',$id)->get();
        if($animalImages)
        {
            if(count($animalImages)>0)
            {
                foreach($animalImages as $image)
                {
                    $this->removeFile($image->image_name,'animalsale');
                }
            }
        }
        $animalImages = AnimalImages::where('animal_sale_id',$id)->delete();
		 $arr = array(
				'status'=>0,
				'delete_reason'=>$request->delete_reason,
				'delete_note'=>$request->delete_note,
			);
			$animals = $this->animalsaleRepo->update($id,$arr);
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.animalsale_delete',['name' => $animalsale->UID_number]);
        storeActicityLog(trans('messages.animalsale_delete'),$message,Auth::user(),$animalsale);
        return redirect()->route('animal-sale.index');
    }

    public function removeImage($id)
    {
        $animalImage = AnimalImages::where('id',$id)->first();
        $this->removeFile($animalImage->image_name,'animalsale');
        $animalImage->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.animalsale_remove',['name' => $animalImage->id]);
        storeActicityLog(trans('messages.animalsale_remove'),$message,Auth::user(),$animalImage);
        // return redirect()->route('animal-sale.edit',$animalImage->id);
        return true;
    }

}
