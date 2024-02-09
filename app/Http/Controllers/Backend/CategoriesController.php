<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Models\Subcategories;
use App\Models\Categories;

class CategoriesController extends Controller
{
    protected $url = '';
   
    /**
     * categories Construct 
     * @return url 
     */
    public function __construct(){

      /*  $this->middleware('permission:species-list|species-create|species-edit|species-delete', ['only' => ['index','show']]);
        $this->middleware('permission:species-create', ['only' => ['create','store']]);
        $this->middleware('permission:species-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:species-delete', ['only' => ['delete']]);*/

        $this->url = [   
            'listUrl' => route('categories.index'),
            'createUrl' => route('categories.create')
        ];
    } 

    /**
     * categories List
     * @return View
     */
    public function index(){
        $categories = Subcategories::leftJoin('categories', 'categories.id', '=', 'subcategories.parent_category')
		->select('subcategories.*','categories.name as category_name')
		->orderBy('id','ASC')->get();
        return view('backend.categories.index',['categories'=>$categories,'url' => $this->url]); 
    }

    /**
     * Add categories View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
		$parentcategories = Categories::get();
        return view('backend.categories.create',['parentcategories'=>$parentcategories,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store categories
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required', 
			'parent_category' => 'required', 			
        ]);
        DB::beginTransaction();
        try{
            $array =array(
				'parent_category'=>$request->input('parent_category'),
				'name'=>$request->input('name')
			);
			
            $categories = Subcategories::create($array);
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.categories_create',['name' => $request->input('name')]);
            storeActicityLog(trans('messages.categories_create'),$message,Auth::user(),$categories);
            return redirect()->route('categories.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('categories.index');   
            
        }
     
    }

    /**
     * Get Particular Subcategories
     * @param int $id (Subcategories Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $categories = Subcategories::find($id);
		$parentcategories = Categories::get();
        return view('backend.categories.create',['parentcategories'=>$parentcategories,'categories' => $categories,'url' => $this->url]);  
    }

     /**
     * Update categories
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(Request $request, $id) 
    {
        $this->validate($request, [
           'name' => 'required', 
			'parent_category' => 'required',
            
        ]);
        DB::beginTransaction();
        try{
            $categories = Subcategories::find($id);
            $categories->name = $request->input('name');
			$categories->parent_category = $request->input('parent_category');
            $categories->save();
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.categories_update',['name' => $request->input('name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$categories);
            return redirect()->route('categories.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('categories.index'); 
        }
    }

    /**
     * Delete categories
     * @param int $id (categories Id)
     * @return Route
     */
    public function delete($id){ 
        $categories = Subcategories::where('id',$id)->first();
        $categories->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.categories_delete',['name' => $categories->name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$categories);
        return redirect()->route('categories.index');
    }

}
