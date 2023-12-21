<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Animals;
use App\Models\AddAnimalImages;
use App\Models\Breeds;
use App\Models\Species;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\AddanimalProcessRequest;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Interfaces\Addanimal\AddanimalRepositoryInterface;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;

class AddanimalController extends Controller
{
    use FileUpload;
    protected $url = '';
    protected $userRepo;
    protected $addAnimalRepo;
    /**
     * Addanimal Construct 
     * @return url 
     */
    public function __construct(UserRepositoryInterface $userRepo,AddanimalRepositoryInterface $addAnimalRepo){

        $this->middleware('permission:add-animal-list|add-animal-create|add-animal-edit|add-animal-delete', ['only' => ['index','show']]);
        $this->middleware('permission:add-animal-create', ['only' => ['create','store']]);
        $this->middleware('permission:add-animal-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:add-animal-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('add-animal.index'),
            'createUrl' => route('add-animal.create')
        ];
        $this->userRepo = $userRepo;
        $this->addAnimalRepo = $addAnimalRepo;
    } 

    /**
     * Add Animal List
     * @return View
     */
    public function index(){
      //  $animals = Animals::with('getAnimalOwner')->orderBy('id','desc')->get();//dd($animals);
		
		$animals = Animals::with('getAnimalOwner')->leftJoin('breeds', 'breeds.id', '=', 'animals.breed')
		->leftJoin('species', 'species.id', '=', 'animals.species')
		->select( 'animals.*','breeds.breed','species.specie as species')->get();
        return view('backend.add-animal.index',['animals'=>$animals,'url' => $this->url]); 
    }

    /**
     * Add Animal View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $animal_owners = $this->userRepo->getUsers(['sRoleName'=>'Animal-owner']);//dd($animal_owners);
        $species = Species::where('is_active','1')->get();
        $breed = Breeds::where('is_active','1')->get();
        return view('backend.add-animal.create',['breed'=>$breed,'species'=>$species,'animal_owners'=>$animal_owners,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Animal
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(AddAnimalProcessRequest $request){
        
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $addAnimal = $this->addAnimalRepo->create($aInsertData);
            if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'animal');
                    if($fileName)
                    {
                        AddAnimalImages::create(['animal_id'=>$addAnimal->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.add_animal_create',['name' => $request->input('UID_number')]);
            storeActicityLog(trans('messages.add_animal_create'),$message,Auth::user(),$addAnimal);
            return redirect()->route('add-animal.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('add-animal.index');   
            
        }
     
    }

    /**
     * Get Particular Animal
     * @param int $id (Animal Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $addAnimal = Animals::find($id);
        $animal_owners = $this->userRepo->getUsers(['sRoleName'=>'Animal-owner']);//dd($animal_owners);
        $species = Species::where('is_active','1')->get();
        $breed = Breeds::where('is_active','1')->get();
		$states = $this->stateRepo->getStates();
        $animalImages = AddAnimalImages::where('animal_id',$addAnimal->id)->get();   
        return view('backend.add-animal.create',['breed'=>$breed,'species'=>$species,'animal_owners'=>$animal_owners,'animalimages'=>$animalImages,'animal' => $addAnimal,'url' => $this->url]);  
    }

     /**
     * Update Animal
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(AddAnimalProcessRequest $request, $id) 
    {
		if($request->name=='' && $request->UID_number=='')
		{
			Session::flash('error', trans('messages.enter_name_or_uid'));
            return back();
		}
		
        DB::beginTransaction();
        try{
            $aInsertData = $request->all();
            $addAnimal = $this->addAnimalRepo->update($id,$request->all());
            if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'animal');
                    if($fileName)
                    {
                        AddAnimalImages::create(['animal_id'=>$addAnimal->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.add_animal_update',['name' => $request->input('shop_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$addAnimal);
            return redirect()->route('add-animal.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('add-animal.index'); 
        }
    }

    public function detail(Request $request, $id = ''){
        $addAnimal = Animals::with('getAnimalOwner')->where('id',$id)->orderBy('id','ASC')->first();//dd($animals);
        $animalImages = AddAnimalImages::where('animal_id',$id)->orderBy('id','ASC')->get();
		
        return view('backend.add-animal.detail',['animalImages'=>$animalImages,'animal' => $addAnimal,'url' => $this->url]);  
    }

    /**
     * Delete Animal
     * @param int $id (Chemist Id)
     * @return Route
     */
    public function delete($id){ 
        $addAnimal = Animals::where('id',$id)->first();

        $animalImage = AddAnimalImages::where('animal_id',$addAnimal->id)->get();
        // dd($animalImage);
        if(count($animalImage)>0)
        {
            foreach($animalImage as $image)
            {
                $this->removeFile($image->image_name,'animal');
                $image->delete();
            }
        }

        $addAnimal->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.add-animal_delete',['name' => $addAnimal->UID_number]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$addAnimal);
        return redirect()->route('add-animal.index');
    }

    public function removeImage($id)
    {
        $animalImage = AddAnimalImages::where('id',$id)->first();
        $this->removeFile($animalImage->image_name,'animal');
        $animalImage->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.animal_remove',['name' => $animalImage->id]);
        storeActicityLog(trans('messages.animal_remove'),$message,Auth::user(),$animalImage);
        // return redirect()->route('product-sale.edit',$shopImage->id);
        return true;
    }

}
