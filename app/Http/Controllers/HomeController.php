<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use Session;
use Illuminate\Validation\Rule;
use Validator;
use App\Http\Requests\UserRequest;
use Hash;
use DB;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Chemist;
use App\Models\Transporters;
use App\Models\ProductForSale;
use App\Models\AnimalForSale;
use App\Models\Breeder;
use App\Models\Suppliers;

use App\Models\Veterinaryhospitals;
use App\Models\PoultryHatchery;
use App\Models\Panjarpol;
use App\Models\Shops;
use App\Models\Farms;
use App\Models\MilkCollections;
use App\Models\DogShelters;
use App\Models\TrainingCenters;
use App\Models\Institutions;
use App\Models\Animals;
use App\Models\Labs;
use App\Models\Ngo;
use App\Models\Easycares;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $userRepo;
    
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->middleware('permission:log', ['only' => ['getLogs','ajaxData']]);
        
        $this->userRepo = $userRepository;
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $nTotalUusers =  User::role('Administrator')->count();  
		$pashumitraCount = User::role('Pashumitra')->where('is_active',1)->whereDate('created_at', Carbon::today())->count();
		$animalOwnerCount = User::role('Animal-owner')->where('is_active',1)->whereDate('created_at', Carbon::today())->count();
		$otheruserCount = User::role('Other')->where('is_active',1)->whereDate('created_at', Carbon::today())->count();
		$registerVetCount = User::role('Registered-vet')->where('is_active',1)->whereDate('created_at', Carbon::today())->count();
		$chemistCount 	  = Chemist::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		$transporterCount = Transporters::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		$productSaleCount = ProductForSale::whereDate('created_at', Carbon::today())->count();
		$animalSaleCount = AnimalForSale::whereDate('created_at', Carbon::today())->count();
		$breederCount = Breeder::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		$hospitalCount = Veterinaryhospitals::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		$supplierCount = Suppliers::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		
		$shopCount = Shops::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		$farmCount = Farms::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		$trainingCenterCount = TrainingCenters::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		$institutionCount = Institutions::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		$milkCollectionCount = MilkCollections::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		$panjarpolCount = Panjarpol::whereDate('created_at', Carbon::today())->count();
		$poultryCount = PoultryHatchery::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		$dogShelterCount = DogShelters::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		$animalCount = Animals::whereDate('created_at', Carbon::today())->count();
		$labCount = Labs::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		$ngoCount = Ngo::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		$easycareCount = Easycares::where('status', 1)->whereDate('created_at', Carbon::today())->count();
		
		$totalpashumitraCount = User::role('Pashumitra')->where('is_active',1)->count();
		$totalanimalOwnerCount = User::role('Animal-owner')->where('is_active',1)->count();
		$totalregisterVetCount = User::role('Registered-vet')->where('is_active',1)->count();
		$totalotherCount = User::role('Other')->where('is_active',1)->count();
		
		$totalchemistCount 	  = Chemist::where('status', 1)->count();
		$totaltransporterCount = Transporters::where('status', 1)->count();
		$totalproductSaleCount = ProductForSale::count();
		$totalanimalSaleCount = AnimalForSale::count();
		$totalbreederCount = Breeder::where('status', 1)->count();
		$totalhospitalCount = Veterinaryhospitals::where('status', 1)->count();
		$totalsupplierCount = Suppliers::where('status', 1)->count();
		$totalshopCount = Shops::where('status', 1)->count();
		$totalfarmCount = Farms::where('status', 1)->count();
		$totaltrainingCenterCount = TrainingCenters::where('status', 1)->count();
		$totalinstitutionCount = Institutions::where('status', 1)->count();
		$totalmilkCollectionCount = MilkCollections::where('status', 1)->count();
		$totalpanjarpolCount = Panjarpol::where('status', 1)->count();
		$totalpoultryCount = PoultryHatchery::where('status', 1)->count();
		$totaldogShelterCount = DogShelters::where('status', 1)->count(); 
		$totalAnimalsCount = Animals::count();
		$totallabCount = Labs::where('status', 1)->count();
		$totalngoCount = Ngo::where('status', 1)->count();
		$totaleasycareCount = Easycares::where('status', 1)->count();
		
		$user =  Auth::user();
		$userrole = '';
		if($user->hasRole('Partner')){
			$userrole = 'Partner';
		}
		if($user->hasRole('Adevrtising')){
			$userrole = 'Adevrtising';
		}
		
        return view('home',compact('easycareCount','userrole','nTotalUusers','totalAnimalsCount','animalCount','otheruserCount','pashumitraCount','animalOwnerCount',
									'registerVetCount','chemistCount','transporterCount','productSaleCount',
									'animalSaleCount','dogShelterCount','institutionCount','trainingCenterCount',
									'breederCount','farmCount','hospitalCount','supplierCount',
									'shopCount','poultryCount','milkCollectionCount','panjarpolCount',
									'totalpashumitraCount','totalanimalOwnerCount','totalregisterVetCount','totalchemistCount','totaltransporterCount',
									'totalproductSaleCount','totalanimalSaleCount','totalbreederCount','totalhospitalCount',
									'totalsupplierCount','totalshopCount','totaltrainingCenterCount','totalinstitutionCount',
									'totalmilkCollectionCount','totalfarmCount','totalpanjarpolCount','totalpoultryCount',
									'totaldogShelterCount','totalngoCount','totalotherCount','totallabCount','labCount','ngoCount','totaleasycareCount'
									));
    }

    /**
     * Get Profile Data
     * 
     * @return Collection
     */
    public function profile(){ 
        $data = Auth::user();
        return view('backend.profile',compact('data'));
    }

    /**
     * Update Profile Data
     * 
     * @return true|false
     */
    public function updateProfile(UserRequest $request,$id){ 
        $res = $this->userRepo->updateUser($id,$request->all());
        if($res){
            Session::flash('success', trans('messages.update_information')); 
        }else{
            Session::flash('error', trans('messages.something')); 
        }
        return redirect()->back();
    }

    /**
     * Change Password
     * 
     * @return true|false
     */
    public function changePassword(Request $request){
        $data = $request->all(); 
        if(empty($data['old_password']) || empty($data['current_password']) || empty($data['confirm_password'])) {
           return response()->json(['statusCode' => 400 ,'message' => trans('messages.blank_field')]);
        }
        if(trim($data['current_password']) != trim($data['confirm_password'])) {
            return response()->json(['statusCode' => 400 ,'message' => trans('messages.not_match_password')]);
        }
        $user = Auth::user();
        $check = Hash::check($data['old_password'], $user->password); 
        if($check){
            // $user->password = Hash::make($data['current_password']); 
            $user->password = $data['current_password']; 
            $user->save(); 
            return response()->json(['statusCode' => 200 ,'message' => trans('messages.change_password')]);

        }else{
            return response()->json(['statusCode' => 400 ,'message' => trans('messages.not_old_match_password')]);

        }
    }

    public function mobileVerify(Request $request){
        $postData = $request->all();
        $validator = Validator::make($postData, [
            'mobile_number' => 'required|max:10',
        ]);
        $response = [];
        if ($validator->fails())
        {
            return response()->json(['statusCode' => 403 ,'message' => implode(',',$validator->errors()->all())]); 
        }
        if(Auth::user()->mobile_number == $postData['mobile_number']){
            return response()->json(['statusCode' => 500 ,'message' => trans('messages.exist_number')]);
        }
        $user = $this->userRepo->checkUniqueMobileNumber(Auth::user()->id,$postData['mobile_number']);
        if (!$user) {  
            DB::beginTransaction();
            // try{
                $otp = random_number();
                $expMin = '+'.config('constants.otp_expiration_min').' minutes';
                $newDate = date('Y-m-d H:i:s', strtotime($expMin));
                $response = ['otp' => $otp,'otp_expiration' =>  $newDate];
                $this->userRepo->update(Auth::user()->id,$response); 
                DB::commit();
                $html = view('backend.mobile_verify',['mobile_number' => $postData['mobile_number']])->render();
                return response()->json(['statusCode' => 200 ,'html' => $html,'message' => trans('messages.otp_send')]); 
            // }
            // catch(\Exception $e){  
            //    DB::rollback();
            //    return response()->json(['statusCode' => 500 ,'message' => trans('messages.something')]);
            // }     
        } else { 
            return response()->json(['statusCode' => 500 ,'message' => trans('messages.try_another_number')]);
        }
    }

    public function updateMobile(Request $request){
        $postData = $request->all();
        $validator = Validator::make($postData, [
            'mobile_number' => 'required',
            'otp' => 'required'
        ]);  
        $response = [];
        if ($validator->fails())
        {   return response()->json(['statusCode' => 403 ,'message' => implode(',',$validator->errors()   
            ->all())]); 
        }
        $response = [];
        $user = $this->userRepo->getSingleRecords(['mobile_number' => Auth::user()->mobile_number]);
        if ($user) {
            //check otp is valid or not 
            $checkOtp = $this->userRepo->getSingleRecords(['mobile_number' => Auth::user()->mobile_number,'otp' => $postData['otp']]);
            if(empty($checkOtp)){ 
                return response()->json(['statusCode' => 400 ,'message' => trans('messages.otp_invalid')]);  
            } 
            // chek otp expiration time
            if(strtotime(now()) >strtotime($user->otp_expiration)){
            // if($hours != 0 || $min>=config('constants.otp_expiration_min')){
                return response()->json(['statusCode' => 400 ,'message' => trans('messages.otp_expired')]);  
            }
            $param = ['mobile_number' => $postData['mobile_number'],'otp' => null,'otp_expiration' =>  null];
            $this->userRepo->update($user->id,$param);   
            return response()->json(['statusCode' => 200 ,'message' => trans('messages.verify_success')]);
        }else {
           return response()->json(['statusCode' => 404 ,'message' => trans('messages.user_not')]);
        } 
    }

    public function getLogs(){
        return view('backend.logs');

    }
    public function ajaxData(Request $request){
        $filter = ['log_type' => $request->log_type];
        return $this->userRepo->getLogsData($filter);
    }
}
