<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shops;
use App\Models\ShopImages;
use App\Models\State;
use App\Repositories\Interfaces\Shops\ShopsRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Http\Requests\ShopsProcessRequest;
use App\Models\Subcategories;

class ShopsController extends Controller
{
    use FileUpload;
    protected $shopsRepo;
	
    /**
     * Shops Construct 
     * @return url 
     */
    public function __construct(ShopsRepositoryInterface $shopsRepo, UserRepositoryInterface $userRepository){

       $this->middleware('permission:shop-list|shop-create|shop-edit|shop-delete', ['only' => ['index','show']]);
        $this->middleware('permission:shop-create', ['only' => ['create','store']]);
        $this->middleware('permission:shop-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:shop-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('shops.index'),
            'createUrl' => route('shops.create')
        ];
        $this->shopsRepo = $shopsRepo;
		$this->userRepo = $userRepository;
    } 

    /**
     * shops List
     * @return View
     */
    public function index(){
        
        return view('backend.shops.index',['url' => $this->url]); 
    }

    /**
     * Add shops View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = State::where('is_active','1')->get();
        return view('backend.shops.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store shops
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(ShopsProcessRequest $request){
        
        DB::beginTransaction();
        // try{            
            $aInsertData = $request->all();
            $shops = $this->shopsRepo->create($aInsertData);

             if($request->shop_photo)
            {
                foreach($request->shop_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'shops');
                    if($fileName)
                    {
                        ShopImages::create(['shop_id'=>$shops->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.shops_create',['name' => $request->input('shop_name')]);
            storeActicityLog(trans('messages.shops_create'),$message,Auth::user(),$shops);
            return redirect()->route('shops.index');
        // }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('shops.index');   
            
        // }
     
    }

    /**
     * Get Particular shops
     * @param int $id (shops Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $shops = Shops::find($id);
        $images = ShopImages::where('shop_id',$shops->id)->get();
        $states = State::where('is_active','1')->get();
		$subcategories = Subcategories::where('status',1)->where('parent_category',5)->get();
		return view('backend.shops.create',['subcategories'=>$subcategories,'images'=>$images,'states'=>$states,'shops' => $shops,'url' => $this->url]);  
    }

     /**
     * Update shops
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(ShopsProcessRequest $request, $id) 
    {
			$shops = $this->shopsRepo->update($id,$request->all());
			if($request->shop_photo)
            {
                foreach($request->shop_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'shops');
                    if($fileName)
                    {
                        ShopImages::create(['shop_id'=>$shops->id,'image_name' => $fileName]);
                    }
                }
            }

			$coordinateArr = $this->userRepo->getLatitudeLongitudes($shops);
			$shops->latitude=$coordinateArr['latitude'];
			$shops->longitude=$coordinateArr['longitude'];
			$shops->update();
            
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.shops_update',['name' => $request->input('shop_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$shops);
            return redirect()->route('shops.index');    
       /* }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('shops.index'); 
        }*/
    }

    /**
     * Get Particular shops
     * @param int $id (shops Id) Request $request
     * @return View
     */
    public function detail(Request $request, $id = ''){
        $shops = Shops::find($id);
		$images = ShopImages::where('shop_id',$shops->id)->get();
        $states = State::where('is_active','1')->get();
        return view('backend.shops.detail',['images'=>$images,'states'=>$states,'shops' => $shops,'url' => $this->url]);  
    }

    /**
     * Delete shops
     * @param int $id (shops)
     * @return Route
     */
    public function delete($id){ 
        $shops = Shops::where('id',$id)->first();
        $shops->delete();
        Session::flash('success', trans('messages.delete_records'));
		
        ## Store log shops
        $message = trans('messages.shops_delete',['name' => $shops->hospital_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$shops);
        return redirect()->route('shops.index');
    }

    public function removeImage($id)
    {
        $image = ShopImages::where('id',$id)->first();
        $this->removeFile($image->image_name,'shops');
        $image->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.image_remove',['name' => $image->id]);
        storeActicityLog(trans('messages.image_remove'),$message,Auth::user(),$image);
        return true;
    }
	
	public function getAjaxList(Request $request){
        $list = $this->shopsRepo->getAjaxList();
        return  $list;
    }
}