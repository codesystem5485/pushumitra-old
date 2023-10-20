<?php

namespace App\Repositories\Implementation\User;

use App\Base\BaseRepository;
use App\Models\User;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use DB;
use DataTables;
use Spatie\Activitylog\Models\Activity;
class UserRepository  extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    protected $userModel; 

    /**
     * UserRepository constructor.
     *
     * @param User $userModel
     */
    public function __construct(User $userModel)
    {
        parent::__construct($userModel);
        $this->userModelRepo = $userModel;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsers(array $input = [])
    {     
        return  $this->userModelRepo->with(['roles','getCreatedBy:id,first_name,middle_name,last_name,mobile_number'])
        ->whereHas('roles', function($q) use($input) {
            if(!empty($input['sRoleName'])){
                $q->where('name', $input['sRoleName']);
            }
        })
        //->where('id','!=',1)->where('is_phone_verify',1)
            ->orderBy('id', 'DESC')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getUser(int $userId)
    {
        return  $this->userModelRepo->findOrFail($userId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateUser($userId, $request = []) 
    {
        DB::beginTransaction();
        try {
            $user =  $this->userModelRepo->find($userId);
            $user->update($request);
            DB::commit();
            return true;
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deleteUser(int $userId)
    { 
        try{
            $category =  $this->userModelRepo->findOrFail($catId);
            return $category->delete();
        } catch (\Exception $e) {  
            DB::rollback();
            return false;
        }
    }

    public function with(array $input)
    {
        return  $this->userModelRepo->with($input)->orderBy('id', 'ASC')
        ->get();
    }
    
    public function logout($id = ''){
        DB::table('oauth_access_tokens')
        ->where('id', $id)
        ->delete();
    }
    
    public function checkUniqueMobileNumber($userId,$mobile){
       return $this->userModelRepo->where('id','!=',$userId)->where('mobile_number' ,$mobile)->first();
    }

    public function getUsersData($sRoleName = ''){
        
        $users = $this->getUsers(['sRoleName' => $sRoleName]); 
        return Datatables::of($users)
        ->addIndexColumn()
        ->addColumn('roles', function ($user) { 
            return isset($user->roles[0]['name']) ? $user->roles[0]['name'] : "-";
        })
        ->editColumn('first_name', function ($user) { 
            return $user->first_name." ".$user->middle_name." ".$user->last_name;
        })
        ->editColumn('mobile_number', function ($user) { 
            return !empty($user->dial_code) ? $user->dial_code.$user->mobile_number: $user->mobile_number;
        })
        ->addColumn('action', function($user){
            $actionBtn = '';
            if(auth()->user()->can('user-list')){
                $actionBtn .= '<a href="'.route('user.detail',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
            }
            if(auth()->user()->can('user-edit')){
                $actionBtn .= '<a href="'.route('user.edit',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
            }
            if(auth()->user()->can('user-delete')){
                $actionBtn .= '<a href="'.route('user.delete',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
            }
            return $actionBtn;
           
        })
        ->rawColumns(['action','roles'])
        ->make(true);
    }

    public function getAnimalownersData($sRoleName = ''){
        
        $users = $this->getUsers(['sRoleName' => $sRoleName]); 
        return Datatables::of($users)
        ->addIndexColumn()
        ->addColumn('roles', function ($user) { 
            return isset($user->roles[0]['name']) ? $user->roles[0]['name'] : "-";
        })
        ->editColumn('first_name', function ($user) { 
            return $user->first_name." ".$user->middle_name." ".$user->last_name;
        })
        ->editColumn('mobile_number', function ($user) { 
            return !empty($user->dial_code) ? $user->dial_code.$user->mobile_number: $user->mobile_number;
        })
        ->addColumn('action', function($user){
            $actionBtn = '';
            if(auth()->user()->can('animal-owner-detail')){
                $actionBtn .= '<a href="'.route('animal-owner.detail',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
            }
            if(auth()->user()->can('animal-owner-edit')){
                $actionBtn .= '<a href="'.route('animal-owner.edit',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
            }
            if(auth()->user()->can('animal-owner-delete')){
                $actionBtn .= '<a href="'.route('animal-owner.delete',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
            }
            return $actionBtn;
           
        })
        ->rawColumns(['action','roles'])
        ->make(true);
    }

    public function getPashumitrasData($sRoleName = ''){
        
        $users = $this->getUsers(['sRoleName' => $sRoleName]); 
        return Datatables::of($users)
        ->addIndexColumn()
        ->addColumn('roles', function ($user) { 
            return isset($user->roles[0]['name']) ? $user->roles[0]['name'] : "-";
        })
        ->editColumn('first_name', function ($user) { 
            return $user->first_name." ".$user->middle_name." ".$user->last_name;
        })
        ->editColumn('mobile_number', function ($user) { 
            return !empty($user->dial_code) ? $user->dial_code.$user->mobile_number: $user->mobile_number;
        })
        ->addColumn('action', function($user){
            $actionBtn = '';
            if(auth()->user()->can('pashumitra-detail')){
                $actionBtn .= '<a href="'.route('pashumitra.detail',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
            }
            if(auth()->user()->can('pashumitra-edit')){
                $actionBtn .= '<a href="'.route('pashumitra.edit',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
            }
            if(auth()->user()->can('pashumitra-delete')){
                $actionBtn .= '<a href="'.route('pashumitra.delete',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
            }
            return $actionBtn;
           
        })
        ->rawColumns(['action','roles'])
        ->make(true);
    }

    public function getRegisteredvetData($sRoleName = ''){
        
        $users = $this->getUsers(['sRoleName' => $sRoleName]); 
        return Datatables::of($users)
        ->addIndexColumn()
        ->addColumn('roles', function ($user) { 
            return isset($user->roles[0]['name']) ? $user->roles[0]['name'] : "-";
        })
        ->editColumn('first_name', function ($user) { 
            return $user->first_name." ".$user->middle_name." ".$user->last_name;
        })
        ->editColumn('mobile_number', function ($user) { 
            return !empty($user->dial_code) ? $user->dial_code.$user->mobile_number: $user->mobile_number;
        })
        ->addColumn('action', function($user){
            $actionBtn = '';
            if(auth()->user()->can('registeredvet-detail')){
                $actionBtn .= '<a href="'.route('registered-vet.detail',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                </button></a>';
            }
            if(auth()->user()->can('registeredvet-edit')){
                $actionBtn .= '<a href="'.route('registered-vet.edit',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                </button></a>';
            }
            if(auth()->user()->can('registeredvet-delete')){
                $actionBtn .= '<a href="'.route('registered-vet.delete',['id' => $user->id]).'">
                <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i></button></a>';
            }
            return $actionBtn;
           
        })
        ->rawColumns(['action','roles'])
        ->make(true);
    }

    public function generateOtp(){ 
        $otp = random_number();
        $expMin = '+'.config('constants.otp_expiration_min').' minutes';
        $newDate = date('Y-m-d H:i:s', strtotime($expMin));
        return ['otp' => $otp,'otp_expiration' =>  $newDate];
    }

    public function getLogsData($filter = []){
        $oLogs = Activity::where(function($query) use ($filter){
            if(!empty($filter['log_type'])){
                $query->where('log_name',$filter['log_type']);
            }else{
                $query->where('log_name','!=','error');
            }
        })->orderBy('id','desc')->get();
        return Datatables::of($oLogs)
        ->addIndexColumn()
        ->addColumn('log_name', function ($value) { 
           
            switch($value->log_name){
                case 'error':
                $class = 'badge badge-danger';
                break;
                case 'created':
                $class = 'badge badge-success';
                break;
                case 'updated':
                $class = 'badge badge-info';
                break;
                case 'deleted':
                $class = 'badge badge-light';
                break;       
                default:
                $class = 'badge badge-light';
            } 
         
            return  "<span class='".$class."'>".ucfirst($value->log_name)."</span>";
                              
        })
        ->editColumn('subject', function ($value) { 
            return !empty($value->subject->name) ? $value->subject->name: '-';
        })
        ->editColumn('properties', function ($value) { 
            return count($value->properties)>0 ? $value->properties: '-';
        })
        ->editColumn('causer_type', function ($value) { 
            return (isset($value->causer_type) &&!empty($value->causer_type)) ? $value->causer->name: '-';
        })
        ->editColumn('created_at', function ($value) { 
            return $value->created_at->format('d-m-Y H:i:s');
        })
        ->rawColumns(['log_name'])
        ->make(true);
    }
}
