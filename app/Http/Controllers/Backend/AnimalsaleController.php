<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnimalForSale;
use App\Models\Breeds;
use App\Models\Species;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\AnimalsaleProcessRequest;
use App\Repositories\Interfaces\Animalsale\AnimalsaleRepositoryInterface;
use DB;
use Session;
use Auth;
class AnimalsaleController extends Controller
{
    protected $url = '';
    protected $animalsaleRepo;
    /**
     * Animal Sale Construct 
     * @return url 
     */
    public function __construct(AnimalsaleRepositoryInterface $animalsaleRepo){

        $this->middleware('permission:animal-sale-list|animal-sale-create|animal-sale-edit|animal-sale-delete', ['only' => ['index','show']]);
        $this->middleware('permission:animal-sale-create', ['only' => ['create','store']]);
        $this->middleware('permission:animal-sale-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:animal-sale-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('animal-sale.index'),
            'createUrl' => route('animal-sale.create')
        ];
        $this->animalsaleRepo = $animalsaleRepo;
    } 

    /**
     * Animal Sale List
     * @return View
     */
    public function index(){
        $animalsale = Animalforsale::orderBy('id','ASC')->get();
        return view('backend.animal-sale.index',['animalsale'=>$animalsale,'url' => $this->url]); 
    }

    /**
     * Add Animal for sale View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $species = Species::where('is_active','1')->get();
        $breed = Breeds::where('is_active','1')->get();
        return view('backend.animal-sale.create',['breed'=>$breed,'species'=>$species,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Animal for sale
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(AnimalsaleProcessRequest $request){
        
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $animalsale = $this->animalsaleRepo->create($aInsertData);
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.animalsale_create',['name' => $request->input('UID_number')]);
            storeActicityLog(trans('messages.animalsale_create'),$message,Auth::user(),$animalsale);
            return redirect()->route('animal-sale.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('animal-sale.index');               
        }
     
    }

    /**
     * Get Particular Animal for sale
     * @param int $id (Animal for sale Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $animalsale = Animalforsale::find($id);
        $species = Species::where('is_active','1')->get();
        $breed = Breeds::where('is_active','1')->get();
        return view('backend.animal-sale.create',['breed'=>$breed,'species'=>$species,'animalsale' => $animalsale,'url' => $this->url]);  
    }

     /**
     * Update Animal for sale
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(AnimalsaleProcessRequest $request, $id) 
    {
        DB::beginTransaction();
        try{
            $aInsertData = $request->all();
            $animalsale = $this->animalsaleRepo->update($id,$request->all());
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.animalsale_update',['name' => $request->input('shop_name')]);
            storeActicityLog(trans('messages.animalsale_update'),$message,Auth::user(),$animalsale);
            return redirect()->route('animal-sale.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('animal-sale.index'); 
        }
    }

    public function detail(Request $request, $id = ''){
        $animalsale = Animalforsale::find($id);
        return view('backend.animal-sale.detail',['animalsale' => $animalsale,'url' => $this->url]);  
    }

    /**
     * Delete Animal for sale
     * @param int $id (Animal for sale Id)
     * @return Route
     */
    public function delete($id){ 
        $animalsale = Animalforsale::where('id',$id)->first();
        $animalsale->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.animalsale_delete',['name' => $animalsale->UID_number]);
        storeActicityLog(trans('messages.animalsale_delete'),$message,Auth::user(),$animalsale);
        return redirect()->route('animal-sale.index');
    }

}
