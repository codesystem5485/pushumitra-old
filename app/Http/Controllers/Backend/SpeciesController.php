<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Species;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
class SpeciesController extends Controller
{
    protected $url = '';
   
    /**
     * Species Construct 
     * @return url 
     */
    public function __construct(){

       $this->url = [   
            'listUrl' => route('species.index'),
            'createUrl' => route('species.create')
        ];
    } 

    /**
     * Species List
     * @return View
     */
    public function index(){
        $species = Species::orderBy('id','ASC')->get();
        return view('backend.species.index',['species'=>$species,'url' => $this->url]); 
    }

    /**
     * Add species View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        return view('backend.species.create',['permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store species
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(Request $request){
        $this->validate($request, [
            'specie' => 'required|unique:species,specie',            
        ]);
        DB::beginTransaction();
        try{
            
            $species = Species::create(['specie' => $request->input('specie')]);
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.species_create',['name' => $request->input('species')]);
            storeActicityLog(trans('messages.species_create'),$message,Auth::user(),$species);
            return redirect()->route('species.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('species.index');   
            
        }
     
    }

    /**
     * Get Particular species
     * @param int $id (species Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $species = Species::find($id);
        return view('backend.species.create',['species' => $species,'url' => $this->url]);  
    }

     /**
     * Update species
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(Request $request, $id) 
    {
        $this->validate($request, [
            'specie' => 'required|unique:species,specie,'.$id,
            
        ]);
        DB::beginTransaction();
        try{
            $species = Species::find($id);
            $species->specie = $request->input('specie');
            $species->save();
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.species_update',['name' => $request->input('specie')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$species);
            return redirect()->route('species.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('species.index'); 
        }
    }

    /**
     * Delete species
     * @param int $id (species Id)
     * @return Route
     */
    public function delete($id){ 
        $species = Species::where('id',$id)->first();
        $species->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.species_delete',['name' => $species->specie]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$species);
        return redirect()->route('species.index');
    }

}
