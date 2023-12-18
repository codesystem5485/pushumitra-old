<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fee;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use Response;
use App\Traits\FileUpload;


class FeesController extends Controller
{
    use FileUpload;
    protected $url = '';
   
    /**
     * fees Type Construct 
     * @return url 
     */
    public function __construct(){

       /* $this->middleware('permission:fees-list|fees-create|fees-edit|fees-delete', ['only' => ['index','show']]);
        $this->middleware('permission:fees-create', ['only' => ['create','store']]);
        $this->middleware('permission:fees-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:fees-delete', ['only' => ['delete']]);*/
        
       $this->url = [   
            'listUrl' => route('fees.index'),
            'createUrl' => route('fees.create')
        ];
    } 

    /**
     * fees List
     * @return View
     */
    public function index(){
        $fees = Fee::where('status',1)->orderBy('id','ASC')->get();
        return view('backend.fees.index',['fees'=>$fees,'url' => $this->url]); 
    }

    /**
     * Add Library View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        return view('backend.fees.create',['permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Library
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required|unique:fee_structure,name',            
            'fee' => 'required|numeric', 
			'valid_months' => 'required|numeric', 			
        ]);
        DB::beginTransaction();
        try{
$fees = new Fee();
            $fees->name = $request->input('name');
			$fees->fee = $request->input('fee');
			$fees->valid_months = $request->input('valid_months');
            $fees->save();
            
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store logbbok
            $message = trans('messages.fees_create',['name' => $request->input('name')]);
            storeActicityLog(trans('messages.fees_create'),$message,Auth::user(),$fees);
            return redirect()->route('fees.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('fees.index');   
            
        }
     
    }

    /**
     * Get Particular Fee
     * @param int $id (Fee Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $fees = Fee::find($id);
        return view('backend.fees.create',['fees' => $fees,'url' => $this->url]);  
    }

     /**
     * Update Fee
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(Request $request, $id) 
    {
        

        $this->validate($request, [
            'name' => 'required|unique:fee_structure,name,'.$id,
            'fee' => 'required|numeric',   
			'valid_months' => 'required|numeric',
        ]);
        DB::beginTransaction();
        try{
            $fees = Fee::find($id);
            
            $fees->name = $request->input('name');
			$fees->fee = $request->input('fee');
			$fees->valid_months = $request->input('valid_months');
            $fees->save();
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.fees_update',['name' => $request->input('name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$fees);
            return redirect()->route('fees.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('fees.index'); 
        }
    }

    /**
     * Delete fees
     * @param int $id (fees Id)
     * @return Route
     */
    public function delete($id){ 
        $fees = Fee::where('id',$id)->first();
        $fees->status = 0;
        $fees->save();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.fees_delete',['name' => $fees->name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$fees);
        return redirect()->route('fees.index');
    }
}
