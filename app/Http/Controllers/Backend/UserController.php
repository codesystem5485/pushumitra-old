<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Interfaces\State\StateRepositoryInterface;
use App\Http\Requests\UserProcessRequest;
use App\Http\Requests\PetownerProcessRequest;
use Auth;
use App\Http\Controllers\BaseController as BaseController;
class UserController extends BaseController
{
    protected $url = '';
    protected $userRepo;
    protected $stateRepo;
    protected $roleRepo;
    public function __construct(UserRepositoryInterface $userRepo,Role $role,StateRepositoryInterface $stateRepo){

        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','show']]);
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['delete']]);
        
        $this->url = [
            'listUrl' => route('user.index'),
            'createUrl' => route('user.create'),
        ];
        $this->userRepo = $userRepo;
        $this->stateRepo = $stateRepo;
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

    public function petowner() {
        
        $this->url = [
            'listUrl' => route('user.index'),
            'createUrl' => route('user.create-petowner'),
        ];
        $roles = $this->roleRepo::get();        
        return view('backend.users.petowner',['url' => $this->url,'roles' => $roles,'role_now'=>'Pet-owner']);
    }

    public function create_petowner(){
        
        $this->url = [
            'listUrl' => route('user.index'),
            'createUrl' => route('user.create-petowner'),
        ];
        $states = $this->stateRepo->getStates();

        return view('backend.users.create_petowner',['url' => $this->url,'states'=>$states]);
    }

    public function pashumitra() {
        
        $this->url = [
            'listUrl' => route('user.index'),
            'createUrl' => route('user.create-pashumitra'),
        ];
        $roles = $this->roleRepo::get();        
        return view('backend.users.index',['url' => $this->url,'roles' => $roles,'role_now'=>'Pashumitra']);
    }

    public function create_pashumitra(){
        
        $this->url = [
            'listUrl' => route('user.index'),
            'createUrl' => route('user.create-pashumitra'),
        ];
        return view('backend.users.create_pashumitra',['url' => $this->url]);
    }

    public function registeredvet() {
        
        $this->url = [
            'listUrl' => route('user.index'),
            'createUrl' => route('user.create-registered-vet'),
        ];
        $roles = $this->roleRepo::get();        
        return view('backend.users.registeredvet',['url' => $this->url,'roles' => $roles,'role_now'=>'RV']);
    }

    public function store(Request $request){
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

    public function store_petowner(PetownerProcessRequest $request){
        DB::beginTransaction();
        try{
            //set create by 
            $this->userRepo->setCreateBy(Auth::user()->id);  
            //store user data
            $aInsertData = $request->all();
            $aInsertData['is_phone_verify'] = 1;
            $aInsertData['is_active'] = 1;
            $aInsertData['country_code'] = 'IN';
            $aInsertData['dial_code'] = '+91';
            $user = $this->userRepo->create($aInsertData); 
            
            //assign role
            $roleData = $this->roleRepo->where('id',$request->role)->first();

            if($roleData){
                $user->assignRole($roleData->name);  
            }

            $paramDetail['user_id'] = $user->id;
            $userDetail = $this->userDetailRepo->create($paramDetail);
            
            DB::commit();
            Session::flash('success', trans('messages.user_register'));
            return redirect()->route('user.petowner');
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', trans('messages.something'));
            return redirect()->route('user.create_petowner');
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
       $user = $this->userRepo->getbyId($id,['roles:id']);
        return view('backend.users.detail',['user'=>$user,'url' => $this->url]);
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
        $users = $this->userRepo->getUsersData($request->role);
        // dd($users);
        return  $users;
    }
}
