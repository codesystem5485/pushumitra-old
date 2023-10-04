<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chemist;
use App\Models\State;
use App\Models\Cities;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\ChemistProcessRequest;
use App\Repositories\Interfaces\Chemist\ChemistRepositoryInterface;
use DB;
use Session;
use Auth;
class ChemistController extends Controller
{
    protected $url = '';
    protected $chemistRepo;
    /**
     * Chemist Construct 
     * @return url 
     */
    public function __construct(ChemistRepositoryInterface $chemistRepo){

       $this->url = [   
            'listUrl' => route('chemist.index'),
            'createUrl' => route('chemist.create')
        ];
        $this->chemistRepo = $chemistRepo;
    } 

    /**
     * Chemist List
     * @return View
     */
    public function index(){
        $chemist = Chemist::orderBy('id','ASC')->get();
        return view('backend.chemist.index',['chemist'=>$chemist,'url' => $this->url]); 
    }

    /**
     * Add Chemist View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = State::where('is_active','1')->get();
        return view('backend.chemist.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Chemist
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(ChemistProcessRequest $request){
        
        DB::beginTransaction();
        // try{            
            $aInsertData = $request->all();
            $chemist = $this->chemistRepo->create($aInsertData);
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.chemist_create',['name' => $request->input('shop_name')]);
            storeActicityLog(trans('messages.chemist_create'),$message,Auth::user(),$chemist);
            return redirect()->route('chemist.index');
        // }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('chemist.index');   
            
        // }
     
    }

    /**
     * Get Particular Chemist
     * @param int $id (Chemist Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $chemist = Chemist::find($id);
        $states = State::where('is_active','1')->get();
        $cities = Cities::where('state_id',$chemist->state_id)->get();        
        return view('backend.chemist.create',['cities'=>$cities,'states'=>$states,'chemist' => $chemist,'url' => $this->url]);  
    }

     /**
     * Update Chemist
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(ChemistProcessRequest $request, $id) 
    {
        DB::beginTransaction();
        try{
            $aInsertData = $request->all();
            $chemist = $this->chemistRepo->update($id,$request->all());
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.chemist_update',['name' => $request->input('shop_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$chemist);
            return redirect()->route('chemist.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('chemist.index'); 
        }
    }

    /**
     * Delete Chemist
     * @param int $id (Chemist Id)
     * @return Route
     */
    public function delete($id){ 
        $chemist = Chemist::where('id',$id)->first();
        $chemist->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.chemist_delete',['name' => $chemist->owner_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$chemist);
        return redirect()->route('chemist.index');
    }

}
