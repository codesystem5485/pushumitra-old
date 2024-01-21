<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Institutions;
use App\Models\InstitutionImages;
use App\Models\State;
use App\Repositories\Interfaces\Institutions\InstitutionsRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Http\Requests\InstitutionProcessRequest;
use App\Models\Subcategories;

class InstitutionsController extends Controller
{
    use FileUpload;
    protected $institutionsRepo;
	
    /**
     * institutions Construct 
     * @return url 
     */
    public function __construct(InstitutionsRepositoryInterface $institutionsRepo){

      /*  $this->middleware('permission:transporter-list|transporter-create|transporter-edit|transporter-delete', ['only' => ['index','show']]);
        $this->middleware('permission:transporter-create', ['only' => ['create','store']]);
        $this->middleware('permission:transporter-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:transporter-delete', ['only' => ['delete']]);*/

        $this->url = [   
            'listUrl' => route('institutions.index'),
            'createUrl' => route('institutions.create')
        ];
        $this->institutionsRepo = $institutionsRepo;
    } 

    /**
     * institutions List
     * @return View
     */
    public function index(){
        $institutions = Institutions::orderBy('id','DESC')->get();
        return view('backend.institutions.index',['institutions'=>$institutions,'url' => $this->url]); 
    }

    /**
     * Add institutions View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = State::where('is_active','1')->get();
        return view('backend.institutions.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store institutions
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(InstitutionProcessRequest $request){
        
        DB::beginTransaction();
        // try{            
            $aInsertData = $request->all();
            $institutions = $this->institutionsRepo->create($aInsertData);

             if($request->institution_photo)
            {
                foreach($request->institution_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'institutions');
                    if($fileName)
                    {
                        InstitutionImages::create(['institution_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.institutions_create',['name' => $request->input('institution_name')]);
            storeActicityLog(trans('messages.institutions_create'),$message,Auth::user(),$institutions);
            return redirect()->route('institutions.index');
        // }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('institutions.index');   
            
        // }
     
    }

    /**
     * Get Particular institutions
     * @param int $id (institutions Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $institutions = Institutions::find($id);
        $images = InstitutionImages::where('institution_id',$institutions->id)->get();
        $states = State::where('is_active','1')->get();
		$subcategories = Subcategories::where('status',1)->where('parent_category',4)->get();
		return view('backend.institutions.create',['subcategories'=>$subcategories,'images'=>$images,'states'=>$states,'institutions' => $institutions,'url' => $this->url]);  
    }

     /**
     * Update institutions
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(InstitutionProcessRequest $request, $id) 
    {
       
            $institutions = $this->institutionsRepo->update($id,$request->all());

              if($request->institution_photo)
            {
                foreach($request->institution_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'institutions');
                    if($fileName)
                    {
                        InstitutionImages::create(['institution_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.institutions_update',['name' => $request->input('institution_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$institutions);
            return redirect()->route('institutions.index');    
       /* }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route(institutions); 
        }*/
    }

    /**
     * Get Particular institutions
     * @param int $id (institutions Id) Request $request
     * @return View
     */
    public function detail(Request $request, $id = ''){
        $institutions = Institutions::find($id);
		$images = InstitutionImages::where('institution_id',$institutions->id)->get();
        $states = State::where('is_active','1')->get();
        return view('backend.institutions.detail',['images'=>$images,'states'=>$states,'institutions' => $institutions,'url' => $this->url]);  
    }

    /**
     * Delete institutions
     * @param int $id (institutions)
     * @return Route
     */
    public function delete($id){ 
        $institutions = Institutions::where('id',$id)->first();
        $institutions->delete();
        Session::flash('success', trans('messages.delete_records'));
		
        ## Store log institutions
        $message = trans('messages.institutions_delete',['name' => $institutions->institution_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$institutions);
        return redirect()->route('institutions.index');
    }

    public function removeImage($id)
    {
        $image = InstitutionImages::where('id',$id)->first();
        $this->removeFile($image->image_name,'institutions');
        $image->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.image_remove',['name' => $image->id]);
        storeActicityLog(trans('messages.image_remove'),$message,Auth::user(),$image);
        return true;
    }
	
	public function getAjaxList(Request $request){
        $list = $this->institutionRepo->getAjaxList();
        return  $list;
    }
}