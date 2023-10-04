<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Characteristic;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
class CharacteristicsController extends Controller
{
    protected $url = '';
   
    /**
     * Characteristic Type Construct 
     * @return url 
     */
    public function __construct(){

       $this->url = [   
            'listUrl' => route('characteristics.index'),
            'createUrl' => route('characteristics.create')
        ];
    } 

    /**
     * Characteristic List
     * @return View
     */
    public function index(){
        $characteristics = Characteristic::orderBy('id','ASC')->get();
        return view('backend.characteristics.index',['characteristics'=>$characteristics,'url' => $this->url]); 
    }

    /**
     * Add Characteristic View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        return view('backend.characteristics.create',['permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Characteristic
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(Request $request){
        $this->validate($request, [
            'characteristic' => 'required|unique:characteristics,characteristic',            
        ]);
        DB::beginTransaction();
        try{
            
            $characteristics = Characteristic::create(['characteristic' => $request->input('characteristic')]);
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.characteristics_create',['name' => $request->input('characteristic')]);
            storeActicityLog(trans('messages.characteristics_create'),$message,Auth::user(),$characteristics);
            return redirect()->route('characteristics.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('characteristics.index');   
            
        }
     
    }

    /**
     * Get Particular Characteristic
     * @param int $id (characteristics Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $characteristics = Characteristic::find($id);
        return view('backend.characteristics.create',['characteristics' => $characteristics,'url' => $this->url]);  
    }

     /**
     * Update Characteristic
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(Request $request, $id) 
    {
        $this->validate($request, [
            'characteristic' => 'required|unique:characteristics,characteristic,'.$id,
            
        ]);
        DB::beginTransaction();
        try{
            $characteristics = characteristic::find($id);
            $characteristics->characteristic = $request->input('characteristic');
            $characteristics->save();
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.characteristics_update',['name' => $request->input('characteristic')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$characteristics);
            return redirect()->route('characteristics.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('characteristics.index'); 
        }
    }

    /**
     * Delete Characteristic
     * @param int $id (characteristics Id)
     * @return Route
     */
    public function delete($id){ 
        $characteristics = characteristic::where('id',$id)->first();
        $characteristics->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.characteristics_delete',['name' => $characteristics->characteristic]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$characteristics);
        return redirect()->route('characteristics.index');
    }

}
