<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Breeds;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Models\Species;

class BreedController extends Controller
{
    protected $url = '';
   
    /**
     * Breedq Type Construct 
     * @return url 
     */
    public function __construct(){

        $this->middleware('permission:breed-list|breed-create|breed-edit|breed-delete', ['only' => ['index','show']]);
        $this->middleware('permission:breed-create', ['only' => ['create','store']]);
        $this->middleware('permission:breed-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:breed-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('breed.index'),
            'createUrl' => route('breed.create')
        ];
    } 

    /**
     * Breed List
     * @return View
     */
    public function index(){
        $breeds = Breeds::leftJoin('species', 'species.id', '=', 'breeds.species')
		->select( 'breeds.*','species.specie as specie_name')->orderBy('id','DESC')->get();
        return view('backend.breed.index',['breeds'=>$breeds,'url' => $this->url]); 
    }

    /**
     * Add Breed View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
		$species = Species::where('is_active','1')->get();
        return view('backend.breed.create',['species'=>$species,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Breed
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(Request $request){
        $this->validate($request, [
            'breed' => 'required|unique:breeds,breed',
			'species' => 'required',            
        ]);
        DB::beginTransaction();
        try{
            
			$insertArray = array(
									'breed'=>$request->input('breed'),
									'species'=>	$request->input('species'),
								 );
            $breed = Breeds::create($insertArray);
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.breed_create',['name' => $request->input('breed')]);
            storeActicityLog(trans('messages.breed_create'),$message,Auth::user(),$breed);
            return redirect()->route('breed.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('breed.index');   
            
        }
     
    }

    /**
     * Get Particular Breeds
     * @param int $id (Breed Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $breed = Breeds::find($id);
		$species = Species::where('is_active','1')->get();
        return view('backend.breed.create',['species'=>$species,'breeds' => $breed,'url' => $this->url]);  
    }

     /**
     * Update breed
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(Request $request, $id) 
    {
        $this->validate($request, [
            'breed' => 'required|unique:breeds,breed,'.$id,
			 'species' => 'required',
            
        ]);
		
		
        DB::beginTransaction();
        try{
            $breed = Breeds::find($id);
            $breed->breed = $request->input('breed');
			$breed->species = $request->input('species');
            $breed->save();
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.breed_update',['name' => $request->input('breed')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$breed);
            return redirect()->route('breed.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('breed.index'); 
        }
    }

    /**
     * Delete breed
     * @param int $id (Breed Id)
     * @return Route
     */
    public function delete($id){ 
        $breed = Breeds::where('id',$id)->first();
        $breed->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.breed_delete',['name' => $breed->breed]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$breed);
        return redirect()->route('breed.index');
    }

}
