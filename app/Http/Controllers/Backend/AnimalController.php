<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnimalType;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
class AnimalController extends Controller
{
    protected $url = '';
   
    /**
     * Animal Type Construct 
     * @return url 
     */
    public function __construct(){

       $this->url = [
            'listUrl' => route('animal.index-type'),
            'createUrl' => route('animal.create-type')
        ];
    } 

    /**
     * Animal type List
     * @return View
     */
    public function index(){
        $animalTypes = AnimalType::orderBy('id','ASC')->get();
        return view('backend.animal_type.index',['animalTypes'=>$animalTypes,'url' => $this->url]); 
    }

    /**
     * Add Animal type View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        return view('backend.animal_type.create',['permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Animal Type
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(Request $request){
        $this->validate($request, [
            'animal_type' => 'required|unique:animal_types,animal_type',            
        ]);
        DB::beginTransaction();
        try{
            
            $animalType = AnimalType::create(['animal_type' => $request->input('animal_type')]);
            DB::commit();
            Session::flash('success', trans('messages.update_records'));
            
            ## Store log
            $message = trans('messages.animal_type_create',['name' => $request->input('animal_type')]);
            storeActicityLog(trans('messages.animal_type_create'),$message,Auth::user(),$animalType);
            return redirect()->route('animal.index-type');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('animal.index-type');   
            
        }
     
    }

    /**
     * Get Particular Animal type
     * @param int $id (Animal type Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $animalType = AnimalType::find($id);
        return view('backend.animal_type.create',['animalType' => $animalType,'url' => $this->url]);  
    }

     /**
     * Update Animal type
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(Request $request, $id) 
    {
        $this->validate($request, [
            'animal_type' => 'required|unique:animal_types,animal_type,'.$id,
            
        ]);
        DB::beginTransaction();
        try{
            $animalType = AnimalType::find($id);
            $animalType->animal_type = $request->input('animal_type');
            $animalType->save();
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.animal_type_update',['name' => $request->input('animal_type')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$animalType);
            return redirect()->route('animal.index-type');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('animal.index-type'); 
        }
    }

    /**
     * Delete Animal type
     * @param int $id (Animal type Id)
     * @return Route
     */
    public function delete($id){ 
        $animalType = AnimalType::where('id',$id)->first();
        $animalType->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.animal_type_delete',['name' => $animalType->animal_type]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$animalType);
        return redirect()->route('animal.index-type');
    }

}
