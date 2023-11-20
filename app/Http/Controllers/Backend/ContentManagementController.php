<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContentManagement;

use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use Response;
use App\Traits\FileUpload;


class ContentManagementController extends Controller
{
    use FileUpload;
    protected $url = '';
   
    public function __construct(){

        $this->middleware('permission:registeredvet-list|registeredvet-create|registeredvet-edit|registeredvet-delete', ['only' => ['index','show']]);
        $this->middleware('permission:registeredvet-create', ['only' => ['create','store']]);
        $this->middleware('permission:registeredvet-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:registeredvet-delete', ['only' => ['delete']]);

        $this->url = [
            'listUrl' => route('content-management.index'),
            'createUrl' => route('content-management.create'),
			'editUrl' => route('content-management.edit'),
        ];
        
    }

    public function index()
	{
		$pages = ContentManagement::orderBy('id','ASC')->get();
		
        return view('backend.content-management.index',['pages'=>$pages,'url' => $this->url]);
    }

   
    public function create(){
        
        return view('backend.content-management.create',['url' => $this->url]);
    }
    public function store(RegisteredvetProcessRequest $request){
        DB::beginTransaction();
        try{//set create by 
            $this->userRepo->setCreateBy(Auth::user()->id);  
            //store user data
            $aInsertData = $request->all();

            
            $aInsertData['state'] = $request->state;
            $aInsertData['state_id'] = $request->state_id;
            $aInsertData['city_id'] = $request->city_id;
            $aInsertData['pincode'] = $request->pincode;
            $aInsertData['education'] = $request->education;
            
            $education_certificateName = $this->uploadFile($request->education_certificate,'education_certificate');
            $aInsertData['education_certificate'] = $education_certificateName;

           
            $user = $this->userRepo->create($aInsertData);

            DB::commit();
            Session::flash('success', trans('messages.user_register'));
            return redirect()->route('content-management.index');
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('content-management.create');
        }    
    }
    
	public function edit(Request $request, $id = ''){
        $pages = ContentManagement::find($id);
        return view('backend.content-management.edit',['pages' => $pages,'url' => $this->url]);  
    }  

    public function update(RegisteredvetProcessRequest $request, $id) 
    {
        DB::beginTransaction();
        $filter = ['id'=>$id];
        $select = ['id'];
        $with = ['getUserDetail']; 
        $userData = $this->userRepo->getSingleRecords($filter,$select,$with); 
        // dd($userDetail);
        $userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;

        // try{
            //set create by 
            $this->userRepo->setCreateBy(Auth::user()->id);  
            $aUpdateData['first_name'] = $request->first_name;
            $aUpdateData['middle_name'] = $request->middle_name;
            $aUpdateData['last_name'] = $request->last_name;
            if(!empty($request->email))
            {
                $aUpdateData['email'] = $request->email;
            }
            if(!empty($request->mobile_number))
            {
                $aUpdateData['mobile_number'] = $request->mobile_number;
            }
            if(!empty($request->password))
            {
                $aUpdateData['password'] = $request->password;
            }
            $aUpdateData['address_line_1'] = $request->address_line_1;
            $aUpdateData['address_line_2'] = $request->address_line_2;
            $aUpdateData['village'] = $request->village;
            $aUpdateData['city_town'] = $request->city_town;
            $aUpdateData['state'] = $request->state;
            $aUpdateData['state_id'] = $request->state_id;
            $aUpdateData['city_id'] = $request->city_id;
            $aUpdateData['pincode'] = $request->pincode;
            $aUpdateData['education'] = $request->education;
            if(!empty($request->education_certificate)){
            $education_certificateName = $this->uploadFile($request->education_certificate,'education_certificate');
            $aUpdateData['education_certificate'] = $education_certificateName;
            }
            $aUpdateData['date_of_birth']=date('Y-m-d',strtotime($request->date_of_birth));
            $aUpdateData['age']=$request->age;
            $aUpdateData['nationality']=$request->nationality;
            $aUpdateData['sex']=$request->sex;
            $aUpdateData['marital_status']=$request->marital_status;
            $user = $this->userRepo->update($id,$aUpdateData);
            
            if($userDetailId != null)
            {
            $inputDetail['job_type'] = $request->job_type;
            $inputDetail['rv_state_verternity_council'] = $request->rv_state_verternity_council;
            $inputDetail['rv_state_verternity_council_no'] = $request->rv_state_verternity_council_no;
            $inputDetail['rv_speciality'] = $request->rv_speciality;
            $inputDetail['rv_name_of_working_org'] = $request->rv_name_of_working_org;
            $inputDetail['rv_working_state'] = $request->rv_working_state;
            $inputDetail['rv_working_state_id'] = $request->rv_working_state_id;
            $inputDetail['rv_working_city_town'] = $request->rv_working_city_town;
            $inputDetail['rv_working_city_id'] = $request->rv_working_city_id;
            $inputDetail['rv_working_village'] = $request->rv_working_village;
            $inputDetail['rv_working_pincode'] = $request->rv_working_pincode;
            $oUser = $this->userDetailRepo->update($userDetailId,$inputDetail);            
            }
            DB::commit(); 
            Session::flash('success', trans('messages.update_records'));
            ## Store log
            $message = trans('messages.update_records'); 
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$user);
            return redirect()->route('content-management.index');  
        // }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('content-management.edit',['id' => $id]);
        // } 
    }

    public function delete($id){
        DB::beginTransaction();
        try{   
            $filter = ['id'=>$id];
            $select = ['id'];
            $with = ['getUserDetail']; 
            $userData = $this->userRepo->getSingleRecords($filter,$select,$with); 
            // dd($userDetail);
            $userDetailId = isset($userData->getUserDetail->id) ? $userData->getUserDetail->id : null;
            
            $this->userRepo->delete($id);
            $this->userDetailRepo->delete($userDetailId);
            DB::commit(); 
            Session::flash('success', trans('messages.delete_records'));
            return redirect()->route('registered-vet.index');
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('registered-vet.index');
        } 
    }

}
