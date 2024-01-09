<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\AddProductImages;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\ProductProcessRequest;
use App\Repositories\Interfaces\Product\ProductRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;

class ProductController extends Controller
{
    use FileUpload;
    protected $url = '';
    protected $productRepo;
    protected $userRepo;
    /**
     * Product Construct 
     * @return url 
     */
    public function __construct(ProductRepositoryInterface $productRepo,UserRepositoryInterface $userRepo){

        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
        $this->middleware('permission:product-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('add-product.index'),
            'createUrl' => route('add-product.create')
        ];
        $this->productRepo = $productRepo;
        $this->userRepo = $userRepo;
    } 

    /**
     * Product List
     * @return View
     */
    public function index(){
        $product = Product::with('getProductOwner')->orderBy('id','ASC')->get(); 
        return view('backend.add-product.index',['product'=>$product,'url' => $this->url]); 
    }

    /**
     * Add Product View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $product_owner = $this->userRepo->getSiteUsers();
        return view('backend.add-product.create',['product_owner'=>$product_owner,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Product
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(ProductProcessRequest $request){
        
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $product = $this->productRepo->create($aInsertData);
            if($request->product_photo)
            {
                foreach($request->product_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'product');
                    if($fileName)
                    {
                        AddProductImages::create(['product_id'=>$product->id,'image_name' => $fileName]);
                    }
                }
            }
            
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.product_create',['name' => $request->input('UID_number')]);
            storeActicityLog(trans('messages.product_create'),$message,Auth::user(),$product);
            return redirect()->route('add-product.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('add-product.index');               
        }
     
    }

    /**
     * Get Particular Product
     * @param int $id (Product Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $product = Product::find($id);
        $product_owner = $this->userRepo->getSiteUsers();
        $productimages = AddProductImages::where('product_id',$product->id)->get();
        return view('backend.add-product.create',['product_owner'=>$product_owner,'productimages'=>$productimages,'product' => $product,'url' => $this->url]);  
    }

     /**
     * Update Product
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(productProcessRequest $request, $id) 
    {
        DB::beginTransaction();
        try{
            $aInsertData = $request->all();
            $product = $this->productRepo->update($id,$request->all());
             if($request->product_photo)
            {
                foreach($request->product_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'product');
                    if($fileName)
                    {
                        $animalImage = AddProductImages::create(['product_id'=>$product->id,'image_name' => $fileName]);
                    }
                }
            }
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.product_update',['name' => $request->input('shop_name')]);
            storeActicityLog(trans('messages.product_update'),$message,Auth::user(),$product);
            return redirect()->route('add-product.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('add-product.index'); 
        }        
    }

    public function detail(Request $request, $id = ''){
        $product = Product::find($id);
        return view('backend.add-product.detail',['product' => $product,'url' => $this->url]);  
    }

    /**
     * Delete Product
     * @param int $id (Product Id)
     * @return Route
     */
    public function delete($id){ 
        $product = Product::where('id',$id)->first();
        $ProductImages = AddProductImages::where('product_id',$id)->get();
        if($ProductImages)
        {
            if(count($ProductImages)>0)
            {
                foreach($ProductImages as $image)
                {
                    $this->removeFile($image->image_name,'product');
                }
            }
        }
        $ProductImages = AddProductImages::where('product_id',$id)->delete();
        // $ProductImages->delete();
        $product->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.product_delete',['name' => $product->UID_number]);
        storeActicityLog(trans('messages.product_delete'),$message,Auth::user(),$product);
        return redirect()->route('add-product.index');
    }

    public function removeImage($id)
    {
        $animalImage = AddProductImages::where('id',$id)->first();
        $this->removeFile($animalImage->image_name,'product');
        $animalImage->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.product_remove',['name' => $animalImage->id]);
        storeActicityLog(trans('messages.product_remove'),$message,Auth::user(),$animalImage);
        // return redirect()->route('add-product.edit',$animalImage->id);
        return true;
    }

}
