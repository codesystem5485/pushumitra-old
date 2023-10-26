<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductForSale;
use App\Models\ProductImages;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\ProductsaleProcessRequest;
use App\Repositories\Interfaces\Productsale\ProductsaleRepositoryInterface;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;

class ProductsaleController extends Controller
{
    use FileUpload;
    protected $url = '';
    protected $productsaleRepo;
    /**
     * Product Sale Construct 
     * @return url 
     */
    public function __construct(ProductsaleRepositoryInterface $productsaleRepo){

        $this->middleware('permission:product-sale-list|product-sale-create|product-sale-edit|product-sale-delete', ['only' => ['index','show']]);
        $this->middleware('permission:product-sale-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-sale-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-sale-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('product-sale.index'),
            'createUrl' => route('product-sale.create')
        ];
        $this->productsaleRepo = $productsaleRepo;
    } 

    /**
     * Product Sale List
     * @return View
     */
    public function index(){
        $productsale = Productforsale::orderBy('id','ASC')->get();
        return view('backend.product-sale.index',['productsale'=>$productsale,'url' => $this->url]); 
    }

    /**
     * Add Product for sale View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        return view('backend.product-sale.create',['permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Product for sale
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(ProductsaleProcessRequest $request){
        
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $productsale = $this->productsaleRepo->create($aInsertData);
            if($request->product_photo)
            {
                foreach($request->product_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'productsale');
                    if($fileName)
                    {
                        ProductImages::create(['product_sale_id'=>$productsale->id,'image_name' => $fileName]);
                    }
                }
            }
            
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.productsale_create',['name' => $request->input('UID_number')]);
            storeActicityLog(trans('messages.productsale_create'),$message,Auth::user(),$productsale);
            return redirect()->route('product-sale.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('product-sale.index');               
        }
     
    }

    /**
     * Get Particular Product for sale
     * @param int $id (Product for sale Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $productsale = Productforsale::find($id);
        $productimages = ProductImages::where('product_sale_id',$productsale->id)->get();
        return view('backend.product-sale.create',['productimages'=>$productimages,'productsale' => $productsale,'url' => $this->url]);  
    }

     /**
     * Update Product for sale
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(productsaleProcessRequest $request, $id) 
    {
        DB::beginTransaction();
        try{
            $aInsertData = $request->all();
            $productsale = $this->productsaleRepo->update($id,$request->all());
             if($request->product_photo)
            {
                foreach($request->product_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'productsale');
                    if($fileName)
                    {
                        $animalImage = ProductImages::create(['product_sale_id'=>$productsale->id,'image_name' => $fileName]);
                    }
                }
            }
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.productsale_update',['name' => $request->input('shop_name')]);
            storeActicityLog(trans('messages.productsale_update'),$message,Auth::user(),$productsale);
            return redirect()->route('product-sale.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('product-sale.index'); 
        }        
    }

    public function detail(Request $request, $id = ''){
        $productsale = Productforsale::find($id);
        return view('backend.product-sale.detail',['productsale' => $productsale,'url' => $this->url]);  
    }

    /**
     * Delete Product for sale
     * @param int $id (Product for sale Id)
     * @return Route
     */
    public function delete($id){ 
        $productsale = Productforsale::where('id',$id)->first();
        $ProductImages = ProductImages::where('product_sale_id',$id)->get();
        if($ProductImages)
        {
            if(count($ProductImages)>0)
            {
                foreach($ProductImages as $image)
                {
                    $this->removeFile($image->image_name,'productsale');
                }
            }
        }
        $ProductImages = ProductImages::where('product_sale_id',$id)->delete();
        // $ProductImages->delete();
        $productsale->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.productsale_delete',['name' => $productsale->UID_number]);
        storeActicityLog(trans('messages.productsale_delete'),$message,Auth::user(),$productsale);
        return redirect()->route('product-sale.index');
    }

    public function removeImage($id)
    {
        $animalImage = ProductImages::where('id',$id)->first();
        $this->removeFile($animalImage->image_name,'productsale');
        $animalImage->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.productsale_remove',['name' => $animalImage->id]);
        storeActicityLog(trans('messages.productsale_remove'),$message,Auth::user(),$animalImage);
        // return redirect()->route('product-sale.edit',$animalImage->id);
        return true;
    }

}
