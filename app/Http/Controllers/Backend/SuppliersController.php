<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Suppliers;
use App\Models\SupplierProductImages;
use App\Models\State;
use App\Repositories\Interfaces\Suppliers\SuppliersRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Models\Cities;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Http\Requests\SuppliersProcessRequest;

class SuppliersController extends Controller
{
    use FileUpload;
    protected $url = '';
    protected $suppliersRepo;
    /**
     * Suppliers Construct 
     * @return url 
     */
    public function __construct(SuppliersRepositoryInterface $suppliersRepo){

        $this->middleware('permission:supplier-list|supplier-create|supplier-edit|supplier-delete', ['only' => ['index','show']]);
        $this->middleware('permission:supplier-create', ['only' => ['create','store']]);
        $this->middleware('permission:supplier-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:supplier-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('suppliers.index'),
            'createUrl' => route('suppliers.create')
        ];
        $this->suppliersRepo = $suppliersRepo;
    } 

    /**
     * suppliers List
     * @return View
     */
    public function index(){
        $suppliers = [];
        return view('backend.suppliers.index',['suppliers'=>$suppliers,'url' => $this->url]); 
    }

    /**
     * Add Suppliers View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = State::where('is_active','1')->get();
        return view('backend.suppliers.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Suppliers
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(SuppliersRepoProcessRequest $request){
        
        DB::beginTransaction();
        // try{            
            $aInsertData = $request->all();
            $suppliers = $this->suppliersRepo->create($aInsertData);

            if($request->supplier_photo)
            {
                foreach($request->supplier_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'suppliers');
                    if($fileName)
                    {
                        SupplierProductImages::create(['supplier_id'=>$suppliers->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.suppliers_create',['name' => $request->input('suppliers_name')]);
            storeActicityLog(trans('messages.suppliers_create'),$message,Auth::user(),$suppliers);
            return redirect()->route('suppliers.index');
        // }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('suppliers.index');   
            
        // }
     
    }

    /**
     * Get Particular suppliers
     * @param int $id (suppliers Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $suppliers = Suppliers::find($id);
        $images = SupplierProductImages::where('supplier_id',$suppliers->id)->get();
        $states = State::where('is_active','1')->get();
        $cities = Cities::where('state_id',$suppliers->state_id)->get();        
        return view('backend.suppliers.create',['images'=>$images,'cities'=>$cities,'states'=>$states,'suppliers' => $suppliers,'url' => $this->url]);  
    }

     /**
     * Update suppliers
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(SuppliersProcessRequest $request, $id) 
    {
        DB::beginTransaction();
        try{
            $suppliers = $this->suppliersRepo->update($id,$request->all());

            if($request->supplier_photo)
            {
                foreach($request->supplier_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'suppliers');
                    if($fileName)
                    {
                        SupplierProductImages::create(['supplier_id'=>$suppliers->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.suppliers_update',['name' => $request->input('suppliers_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$suppliers);
            return redirect()->route('suppliers.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('suppliers.index'); 
        }
    }

    /**
     * Get Particular suppliers
     * @param int $id (suppliers Id) Request $request
     * @return View
     */
    public function detail(Request $request, $id = ''){
        $suppliers = Suppliers::find($id);
        $states = State::where('is_active','1')->get();
        $cities = Cities::where('state_id',$suppliers->state_id)->get();        
        return view('backend.suppliers.detail',['cities'=>$cities,'states'=>$states,'suppliers' => $suppliers,'url' => $this->url]);  
    }

    /**
     * Delete suppliers
     * @param int $id (suppliers Id)
     * @return Route
     */
    public function delete($id){ 
        $suppliers = Suppliers::where('id',$id)->first();
		if($suppliers){
			 
			$images = SupplierProductImages::where('supplier_id',$suppliers->id)->get();
			if(count($images)>0)
			{
				foreach($images as $image)
				{
					$this->removeFile($image->image_name,'suppliers');
					$image->delete();
				}
			}
			$arr = array('status'=>0);
			$Suppliers = $this->suppliersRepo->update($id,$arr);
		}
		$arr = array('status'=>0);
		$Suppliers = $this->suppliersRepo->update($id,$arr);
        //$suppliers->delete();
        Session::flash('success', trans('messages.delete_records'));
		
        ## Store log
        $message = trans('messages.suppliers_delete',['name' => $suppliers->supplier_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$suppliers);
        return redirect()->route('suppliers.index');
    }

    public function removeImage($id)
    {
        $image = SupplierProductImages::where('id',$id)->first();
        $this->removeFile($image->image_name,'suppliers');
        $image->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.image_remove',['name' => $image->id]);
        storeActicityLog(trans('messages.image_remove'),$message,Auth::user(),$image);
       
        return true;
    }
	
	public function getAjaxList(Request $request){
        $list = $this->suppliersRepo->getAjaxList();
        return  $list;
    }
}