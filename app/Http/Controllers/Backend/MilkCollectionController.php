<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MilkCollections;
use App\Models\MilkCollectionImages;
use App\Models\State;
use App\Repositories\Interfaces\Milkcollections\MilkCollectionRepositoryInterface;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Http\Requests\MilkCollectionProcessRequest;

class MilkCollectionController extends Controller
{
    use FileUpload;
    protected $milkcollectionRepo;
	
    /**
     * milkcollection Construct 
     * @return url 
     */
    public function __construct(MilkCollectionRepositoryInterface $milkcollectionRepo){

        $this->middleware('permission:milkcollection-list|milkcollection-create|milkcollection-edit|milkcollection-delete', ['only' => ['index','show']]);
        $this->middleware('permission:milkcollection-create', ['only' => ['create','store']]);
        $this->middleware('permission:milkcollection-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:milkcollection-delete', ['only' => ['delete']]);

        $this->url = [   
            'listUrl' => route('milkcollections.index'),
            'createUrl' => route('milkcollections.create')
        ];
        $this->milkcollectionRepo = $milkcollectionRepo;
    } 

    /**
     * milkcollections List
     * @return View
     */
    public function index(){
        
        return view('backend.milkcollections.index',['url' => $this->url]); 
    }

    /**
     * Add milkcollections View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        $states = State::where('is_active','1')->get();
        return view('backend.milkcollections.create',['states'=>$states,'permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store milkcollections
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(MilkCollectionProcessRequest $request){
        
        DB::beginTransaction();
        // try{            
            $aInsertData = $request->all();
            $milkcollections = $this->milkcollectionRepo->create($aInsertData);

             if($request->milkcollection_photo)
            {
                foreach($request->milkcollection_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'milkcollections');
                    if($fileName)
                    {
                        MilkCollectionImages::create(['milkcollection_center_id'=>$results->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.milkcollection_create',['name' => $request->input('milkcollection_center_name')]);
            storeActicityLog(trans('messages.milkcollection_create'),$message,Auth::user(),$milkcollections);
            return redirect()->route('milkcollections.index');
        // }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('milkcollections.index');   
            
        // }
     
    }

    /**
     * Get Particular milkcollections
     * @param int $id (milkcollections Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $milkcollections = MilkCollections::find($id);
        $images = MilkCollectionImages::where('milkcollection_center_id',$milkcollections->id)->get();
        $states = State::where('is_active','1')->get();
		
		return view('backend.milkcollections.create',['images'=>$images,'states'=>$states,'milkcollections' => $milkcollections,'url' => $this->url]);  
    }

     /**
     * Update milkcollections
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(MilkCollectionProcessRequest $request, $id) 
    {
       
            $milkcollections = $this->milkcollectionRepo->update($id,$request->all());

             if($request->milkcollection_photo)
            {
                foreach($request->milkcollection_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'milkcollections');
                    if($fileName)
                    {
                        MilkCollectionImages::create(['milkcollection_center_id'=>$milkcollections->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.milkcollection_update',['name' => $request->input('milkcollection_center_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$milkcollections);
            return redirect()->route('milkcollections.index');    
       /* }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('milkcollections.index'); 
        }*/
    }

    /**
     * Get Particular milkcollections
     * @param int $id (MilkCollection Id) Request $request
     * @return View
     */
    public function detail(Request $request, $id = ''){
        $milkcollections = MilkCollections::find($id);
		$images = MilkCollectionImages::where('milkcollection_center_id',$milkcollections->id)->get();
        $states = State::where('is_active','1')->get();
        return view('backend.milkcollections.detail',['images'=>$images,'states'=>$states,'milkcollections' => $milkcollections,'url' => $this->url]);  
    }

    /**
     * Delete milkcollections
     * @param int $id (milkcollections)
     * @return Route
     */
    public function delete($id){ 
        $milkcollections = MilkCollections::where('id',$id)->first();
        $milkcollections->delete();
        Session::flash('success', trans('messages.delete_records'));
		
        ## Store log milkcollections
        $message = trans('messages.milkcollection_delete',['name' => $milkcollections->milkcollection_center_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$milkcollections);
        return redirect()->route('milkcollections.index');
    }

    public function removeImage($id)
    {
        $image = MilkCollectionImages::where('id',$id)->first();
        $this->removeFile($image->image_name,'milkcollections');
        $image->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.image_remove',['name' => $image->id]);
        storeActicityLog(trans('messages.image_remove'),$message,Auth::user(),$image);
        return true;
    }
	
	public function getAjaxList(Request $request){
        $list = $this->milkcollectionRepo->getAjaxList();
        return  $list;
    }
}