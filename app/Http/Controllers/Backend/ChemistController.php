<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chemist;
use App\Models\ChemistShopImages;
use App\Models\State;
use App\Models\Cities;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\ChemistProcessRequest;
use App\Repositories\Interfaces\Chemist\ChemistRepositoryInterface;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;

class ChemistController extends Controller
{
    use FileUpload;
    protected $url = '';
    protected $chemistRepo;
    /**
     * Chemist Construct 
     * @return url 
     */
    public function __construct(ChemistRepositoryInterface $chemistRepo){

        $this->middleware('permission:chemist-list|chemist-create|chemist-edit|chemist-delete', ['only' => ['index','show']]);
        $this->middleware('permission:chemist-create', ['only' => ['create','store']]);
        $this->middleware('permission:chemist-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:chemist-delete', ['only' => ['delete']]);

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
        $chemist = Chemist::where('status',1)->orderBy('id','ASC')->get();
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
        try{            
            $aInsertData = $request->all();
            $chemist = $this->chemistRepo->create($aInsertData);
            if($request->shop_photo)
            {
                foreach($request->shop_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'chemist');
                    if($fileName)
                    {
                        ChemistShopImages::create(['chemist_id'=>$chemist->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.chemist_create',['name' => $request->input('shop_name')]);
            storeActicityLog(trans('messages.chemist_create'),$message,Auth::user(),$chemist);
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
     * Get Particular Chemist
     * @param int $id (Chemist Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $chemist = Chemist::find($id);
        $states = State::where('is_active','1')->get();
        $cities = Cities::where('state_id',$chemist->state_id)->get();     
        $shopimages = ChemistShopImages::where('chemist_id',$chemist->id)->get();   
        return view('backend.chemist.create',['shopimages'=>$shopimages,'cities'=>$cities,'states'=>$states,'chemist' => $chemist,'url' => $this->url]);  
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
            if($request->shop_photo)
            {
                foreach($request->shop_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'chemist');
                    if($fileName)
                    {
                        ChemistShopImages::create(['chemist_id'=>$chemist->id,'image_name' => $fileName]);
                    }
                }
            }

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

    public function detail(Request $request, $id = ''){
        $chemist = Chemist::find($id);
        $states = State::where('is_active','1')->get();
        $shopimages = ChemistShopImages::where('chemist_id',$id)->get();           
        return view('backend.chemist.detail',['shopimages'=>$shopimages,'states'=>$states,'chemist' => $chemist,'url' => $this->url]);  
    }

    /**
     * Delete Chemist
     * @param int $id (Chemist Id)
     * @return Route
     */
    public function delete($id){ 
        $chemist = Chemist::where('id',$id)->first();
        $shopImage = ChemistShopImages::where('chemist_id',$chemist->id)->get();
        
        if(count($shopImage)>0)
        {
            foreach($shopImage as $image)
            {
                $this->removeFile($image->image_name,'chemist');
                $image->delete();
            }
        }
        //$chemist->delete();
		$arr = array('status'=>0);
		$chemist = $this->chemistRepo->update($id,$arr);
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.chemist_delete',['name' => $chemist->owner_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$chemist);
        return redirect()->route('chemist.index');
    }

    public function removeImage($id)
    {
        $shopImage = ChemistShopImages::where('id',$id)->first();
        $this->removeFile($shopImage->image_name,'chemist');
        $shopImage->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.productsale_remove',['name' => $shopImage->id]);
        storeActicityLog(trans('messages.productsale_remove'),$message,Auth::user(),$shopImage);
        // return redirect()->route('product-sale.edit',$shopImage->id);
        return true;
    }

}
