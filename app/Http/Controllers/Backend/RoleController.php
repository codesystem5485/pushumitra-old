<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
class RoleController extends Controller
{
    protected $url = '';
   
    /**
     * Role Construct 
     * @return url 
     */
    public function __construct(){

        $this->middleware('permission:role-list', ['only' => ['index','show']]);
        $this->middleware('permission:role-create', ['only' => ['create','store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role-delete', ['only' => ['delete']]); 

        $this->url = [
            'listUrl' => route('role.index'),
            'createUrl' => route('role.create')
        ];
    } 

    /**
     * Role List
     * @return View
     */
    public function index(){
        $roles = Role::with('permissions:name')->orderBy('id','ASC')->get();
        return view('backend.roles.index',['roles'=>$roles,'url' => $this->url]); 
    }

    /**
     * Add Role View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        return view('backend.roles.create',['permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Role
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
        DB::beginTransaction();
        try{
            
            $role = Role::create(['name' => $request->input('name')]);
            $role->syncPermissions($request->input('permission'));
            DB::commit();
            Session::flash('success', trans('messages.update_records'));
            
            ## Store log
            $message = trans('messages.role_create',['name' => $request->input('name')]);
            storeActicityLog(trans('messages.create'),$message,Auth::user(),$role);

            return redirect()->route('role.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('role.index');   
            
        }
     
    }

    /**
     * Get Particular Role
     * @param int $id (Role Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        return view('backend.roles.create',['role' => $role,'permission' => $permission,'rolePermissions' => $rolePermissions,'url' => $this->url]);  
    }

     /**
     * Update Role
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(Request $request, $id) 
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name,'.$id,
            'permission' => 'required',
        ]);
        DB::beginTransaction();
        try{
            $role = Role::find($id);
            $role->name = $request->input('name');
            $role->save();
            $role->syncPermissions($request->input('permission'));
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.role_update',['name' => $request->input('name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$role);

            return redirect()->route('role.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('role.index'); 
        }
    }

    /**
     * Delete Role
     * @param int $id (Role Id)
     * @return Route
     */
    public function delete($id){ 
        $role = Role::where('id',$id)->first();
        $role->delete();
        DB::table("role_has_permissions")->where('role_id',$id)->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.role_delete',['name' => $role->name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$role);

        return redirect()->route('role.index');
    }

}
