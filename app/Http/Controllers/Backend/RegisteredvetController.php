<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Http\Requests\UserProcessRequest;
use Auth;
use App\Http\Controllers\BaseController as BaseController;
class RegisteredvetController extends BaseController
{
    protected $url = '';
    protected $userRepo;
    protected $roleRepo;
    public function __construct(UserRepositoryInterface $userRepo,Role $role){

        $this->url = [
            'listUrl' => route('user.index'),
            'createUrl' => route('user.create'),
        ];
        $this->userRepo = $userRepo;
        $this->roleRepo = $role;
    }

    public function index() {
        $roles = $this->roleRepo::get();
        return view('backend.users.index',['url' => $this->url,'roles' => $roles]);
    }

    public function getRoles(){
        return $this->roleRepo::orderBy('id','ASC')->get();
    }
    public function create(){
        $roles = $this->getRoles();
        return view('backend.users.create',['roles' => $roles,'url' => $this->url]);
    }
    public function store(UserProcessRequest $request){
        DB::beginTransaction();
        try{//set create by 
            $this->userRepo->setCreateBy(Auth::user()->id);  
            //store user data
            $aInsertData = $request->all();
            $aInsertData['is_phone_verify'] = 1;
            $aInsertData['is_active'] = 1;
            $aInsertData['country_code'] = 'IN';
            $aInsertData['dial_code'] = '+91'; 
            $user = $this->userRepo->create($aInsertData); 
            
            //asign role
            $roleData = $this->roleRepo->where('id',$request->role)->first();

            if($roleData){
                $user->assignRole($roleData->name);  
            }
            DB::commit();
            Session::flash('success', trans('messages.user_register'));
            return redirect()->route('user.index');
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('user.create');
        }    
    }
    public function edit(Request $request, $id = ''){
        $user = $this->userRepo->getbyId($id,['roles:id']);
       
        $roles = $this->getRoles();
        return view('backend.users.create',['user' => $user,'roles' => $roles,'url' => $this->url]);
    }   

    public function update(UserProcessRequest $request, $id) 
    {
        DB::beginTransaction();
        try{
            //set create by 
            $this->userRepo->setCreateBy(Auth::user()->id);  
            //store user data
            $user = $this->userRepo->update($id,$request->except('_token','role'));
            $oOldUserRole = $user->getRoleNames();
            //asign role
            $roleData = $this->roleRepo->where('id',$request->role)->first();
             
            if(!empty($oOldUserRole)){ 
                $user->removeRole($oOldUserRole[0]);  
                $user->assignRole($roleData->name);  
            }
            DB::commit(); 
            Session::flash('success', trans('messages.update_records'));
            ## Store log
            $message = trans('messages.update_records'); 
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$user);
            return redirect()->route('user.index');  
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('user.edit',['id' => $id]);
        } 
    }

    public function delete($id){
        DB::beginTransaction();
        try{   
            $this->userRepo->delete($id);
            DB::commit(); 
            Session::flash('success', trans('messages.delete_records'));
            return redirect()->route('user.index');
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('user.index');
        } 
    }

    public function userDetail($id){
        $filter = ['id'=>$id];
        $select = ['id','name','email','phone_number'];
        $with = ['getUserDetail:id,user_id,pan_number,dob,profile_pic,city,state,gender','roles']; 
        $userDetail = $this->userRepo->getSingleRecords($filter,$select,$with); 
        return view('backend.users.detail',['userDetail'=>$userDetail,'url' => $this->url]);
    }
    public function getRoleWiseUser(Request $request){
        $roleName = $request->role;
        $userData = $this->roleRepo->with('users')->where('name','!=','Super-Admin')
        ->where(function($query) use ($roleName){
            $query->where('name',$roleName);
        })
        ->first(); 
       $html = view('backend.users.ajax_table',['roles' => $userData])->render();
        return response()->json(['status' => true,'html' => $html]);
    }

    public function getAjaxUser(Request $request){
        return $this->userRepo->getUsersData($request->role);
    }
}
