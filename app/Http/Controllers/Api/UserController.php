<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Setting;
use App\Models\UserSetting;
use App\Models\UserRequests;
use App\Models\UserServices;
use App\Models\Order;
use App\Models\UserDocument;
use App\Models\UserLike;
use App\Models\ServiceCategories;
use App\Models\CustomerService;
use App\Models\RecentlyProvider;
use App\Http\Resources\UserDocumentResource;
use Hash;
use Mail;
use Stripe;
use Exception;
use DB;

class UserController extends BaseController
{
	/**
    *  @OA\Post(
    *     path="/api/edit-profile",
    *     tags={"Edit Profile"},
	*     summary="Edit profile",
	*     security={{"bearer_token":{}}},
    *     operationId="edit-profile",
    *     
    *     @OA\Parameter(
    *         name="first_name",
    *         in="query",
	* 		  required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
	* 	  @OA\Parameter(
    *         name="last_name",
    *         in="query",
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="email",
    *         in="query",
    *         required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),   
	*     @OA\Parameter(
    *         name="bio",
    *         in="query",
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),  
	*     @OA\Parameter(
    *         name="address",
    *         in="query",
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),  
	*     @OA\Parameter(
    *         name="lat",
    *         in="query",
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),  
	*     @OA\Parameter(
    *         name="lang",
    *         in="query",
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),  
    *    @OA\RequestBody(
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *                  @OA\Property(
    *                      property="photo",
    *                      description="photo",
    *                      type="array",
    *                      @OA\Items(type="string", format="binary")
    *                   ),
    *               ),
    *           ),
    *       ),
    *     @OA\Response(
    *         response=200,
    *         description="Success",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthorized"
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Invalid request"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="not found"
    *     ),
    * )
    **/
	public function edit_profile(Request $request)
	{
		$validator = Validator::make($request->all(),[
			'first_name' => 'required|max:255',
			'last_name' => 'max:255',
			'email' => 'required|email|unique:users,email,'.Auth::id(),
			'photo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
		]);

		if($validator->fails())
		{
			return $this->sendError($validator->errors()->first(),null,200);
		}
		$filename = null;
		if($request->hasfile('photo')) {
			$file = $request->file('photo');
			$filename = time().$file->getClientOriginalName();
			$file->move(public_path().'/uploads/', $filename);  
		}
		$user = User::find(Auth::id());
		$user->first_name = $request->first_name;
		$user->last_name = $request->last_name;
		$user->email = $request->email;
		$user->bio = $request->bio;
		$user->address = $request->address;
		$user->lat = $request->lat;
		$user->lang = $request->lang;
		if($request->hasfile('photo'))
		{
			$user->photo = $filename;
		}
		$user->save();
		$data = new UserResource($user);
		return $this->sendResponse($data, 'User profile updated successfully.');
	}

	/**
	 *  @OA\Post(
	 *     path="/api/change-password",
	 *     tags={"change password"},
	 *     summary="Change password",
	 *     security={{"bearer_token":{}}},
	 *     operationId="change-password",
	 * 
	 *     @OA\Parameter(
	 *         name="old_password",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *     @OA\Parameter(
	 *         name="new_password",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),    
	 *     @OA\Parameter(
	 *         name="password_confirmation",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),    
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid request"
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="not found"
	 *     ),
	 * )
	**/

	public function change_password(Request $request)
	{
		$validator = Validator::make($request->all(),[
			'old_password' => 'required',
			'new_password' => 'required|min:8|same:password_confirmation',
		]);

		if($validator->fails())
		{
			return $this->sendError($validator->errors()->first(),400);
		}
		try{
			$user = User::find(Auth::id());
			if(Hash::check($request->old_password,$user->password)) {
				$user->password = bcrypt($request->new_password);
				$user->save();
				return $this->sendResponse('', 'Password changed successfully!.');
			}
			return $this->sendError('old password incorrect!',404);
		}catch (Exception $e){
            return $this->sendError('Something went wrong, Please try again!.',200);
        }     
		

	}

	/**
	 *  @OA\Post(
	 *     path="/api/change-token",
	 *     tags={"change device token"},
	 *     summary="Change token",
	 *     security={{"bearer_token":{}}},
	 *     operationId="change-token",
	 * 
	 *     @OA\Parameter(
	 *         name="device_type",
	 *         in="query",
	 *         description="android | ios",
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *     @OA\Parameter(
	 *         name="device_token",
	 *         in="query",
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid request"
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="not found"
	 *     ),
	 * )
	**/

	public function change_token(Request $request)
	{

		$user = User::find(Auth::id());
		$user->device_type = $request->device_type;
		$user->device_token = $request->device_token;
		$user->save();
		return $this->sendResponse(null, 'Device token changed successfully!.');

	}

	/**
	 *  @OA\Post(
	 *     path="/api/update/password",
	 *     tags={"forgot password"},
	 *     summary="Forgot password",
	 *     operationId="forgot-password",
	 * 
	 *     @OA\Parameter(
	 *         name="country_code",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),    
	 *     @OA\Parameter(
	 *         name="phone",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),   
	 *     @OA\Parameter(
	 *         name="password",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),    
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid request"
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="not found"
	 *     ),
	 * )
	**/
	public function forgot_password(Request $request)
	{
		
		$validator = Validator::make($request->all(),[
			'country_code' => 'required',
			'phone' => 'required|exists:users,phone',
			'password' => 'required|min:8',
		]);

		if($validator->fails())
		{
			return $this->sendError($validator->errors()->first(),400);
		}
		
        try{
			$user = User::where('country_code',$request->country_code)
						->where('phone',$request->phone)
						->first();
			if(empty($user)){
				return $this->sendError('This phone not registered',404); 
			}
            $user->password = bcrypt($request->password);
            $user->save();
            return $this->sendResponse('', 'Password updated succesfully!');

        } catch (Exception $e)
        {
            return $this->sendError('Something went wrong, Please try again!.',200);
        }      
		
	}
	/**
	 *  @OA\Post(
	 *     path="/api/change-location",
	 *     tags={"change location"},
	 *     summary="Change Location",
	 *     security={{"bearer_token":{}}},
	 *     operationId="change-location",
	 * 
	*     @OA\Parameter(
    *         name="latitude",
    *         in="query",
	*         required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),  
	*     @OA\Parameter(
    *         name="longitude",
    *         in="query",
	*         required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),  
	 *     @OA\Parameter(
	 *         name="address",
	 *         in="query",
	 *         @OA\Schema(
	 *             type="text"
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid request"
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="not found"
	 *     ),
	 * )
	**/
	public function change_location(Request $request)
	{
		$validator = Validator::make($request->all(),[
			'latitude' => 'required',
			'longitude' => 'required',
		]);

		if($validator->fails())
		{
			return $this->sendError($validator->errors()->first(),400);
		}
		try {
			$user = User::find(Auth::id());
			$user->lat = $request->latitude;
			$user->lang = $request->longitude;
			$user->address = $request->address;
			$user->save();
			$data = null;
            return $this->sendResponse($data, 'Location updated successfully!');
		} catch (Exception $e)
		{
			return $this->sendError($e->getMessage(),null,422);
		}
	}
	/**
     * @OA\Get(
     *     path="/api/application/status",
     *     tags={"Application Status"},
     *     summary="get application status",
     *     security={{"bearer_token":{}}},
     *     operationId="aplication status",
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     * )
     * )
    **/
	public function application_status()
	{
		try{
			$user = User::find(Auth::id());
			$userDocuments = UserDocument::with('documentname')->where('user_id',Auth::id())->get();
			$approveDocCount = UserDocument::where('user_id',Auth::id())->where('status',1)->count();
			$data = [
				'phone_verified' => $user->phone_verified,
				'uploaded_document' => UserDocumentResource::collection($userDocuments),
				'approve_doc_count' => $approveDocCount,
				'document_approved' => $approveDocCount >= 2 ? 1 : 0, 
			];
			return $this->sendResponse($data,'Application Status!');
		}catch(Exception $e){
			return $this->sendError('Something went wrong, Please try again!.', 422);
		}
		
	}

	/**
	 *  @OA\Post(
	 *     path="/api/change/status",
	 *     tags={"change status"},
	 *     summary="Change Status",
	 *     security={{"bearer_token":{}}},
	 *     operationId="change-status",
	 * 
	 *     @OA\Parameter(
	 *         name="is_change",
	 *         in="query",
	 *         required=true,
	 * 		   description = "provider or online or notification",
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),   
	 *     @OA\Parameter(
	 *         name="status",
	 *         in="query",
	 *         required=true,
	 * 		   description = " 0 | 1",
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid request"
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="not found"
	 *     ),
	 * )
	**/
	public function change_status(Request $request)
	{
		$validator = Validator::make($request->all(),[
			'is_change' => 'required|in:provider,online,notification',
			'status' => 'required',
		]);

		if($validator->fails())
		{
			return $this->sendError($validator->errors()->first(),400);
		}

	   try{
		   $user_setting = UserSetting::where('user_id',Auth::id())->first();
		   if($request->is_change == 'provider')
		   {
				$user_setting->provider =  $request->status;
		   }
		   if($request->is_change == 'online')
		   {
				$user_setting->online =  $request->status;
		   }
		   if($request->is_change == 'notification')
		   {
				$user_setting->notification =  $request->status;
		   }
		   $user_setting->save();
		   return $this->sendResponse($user_setting,'Updated Successully!');
	   }catch(Exception $e){
		   return $this->sendError('Something went wrong, Please try again!.', 422);
	   }
	   
   }
   /**
	 *  @OA\Get(
	 *     path="/api/nearby/providers",
	 *     tags={"Nearby Provider"},
	 *     summary="get nearby Provider",
	 *     security={{"bearer_token":{}}},
	 *     operationId="nearby Provider",
	 * 
	 *     @OA\Parameter(
	 *         name="lat",
	 *         in="query",
	 *         required=true,
	 * 		   description = "latitude",
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),  
	 * 	   @OA\Parameter(
	 *         name="lang",
	 *         in="query",
	 *         required=true,
	 * 		   description = "longitude",
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),   
	 *  	   @OA\Parameter(
	 *         name="service_category_id",
	 *         in="query",
	 * 	 	   description="service category id",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="integer"
	 *         )
	 *     ),   
	 * 
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid request"
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="not found"
	 *     ),
	 * )
	**/
   public function nearby_provider(Request $request)
   {
		$validator = Validator::make($request->all(),[
			'lat' => 'required',
			'lang' => 'required',
		]);

		if($validator->fails())
		{
			return $this->sendError($validator->errors()->first(),400);
		}
		try{
			$latitude = $request->lat;
			$longitude = $request->lang;
			$setting = Setting::latest()->first();
			$distance = $setting->distance;
			$users = User::whereRaw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(lat) ) * cos( radians(lang) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(lat) ) ) ) <= $distance")
					->where('id','!=',Auth::id())
					->paginate(10);

			return $this->sendResponse($users,'Nearby Providers');
		}catch(Exception $e){
			return $this->sendError($e->getMessage(), 422);
		}
		
   }
   /**
     * @OA\Get(
     *     path="/api/user/profile",
     *     tags={"User Profile"},
     *     summary="User Profile",
     *     security={{"bearer_token":{}}},
     *     operationId="User Profile",
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     * )
     * )
    **/
   public function get_profile(){
		try{
			$user = User::with(['user_setting','user_service'])->find(Auth::id());
			$user = new UserResource($user);
			return $this->sendResponse($user,'Profile');
		}catch(Exception $e){
			return $this->sendError($e->getMessage(), 422);
		}
   }
   /**
     * @OA\Get(
     *     path="/api/get/provider",
     *     tags={"Fetch providers "},
     *     summary="Fetch providers ",
     *     security={{"bearer_token":{}}},
     *     operationId="Fetch providers ",
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     * )
     * )
    **/
   public function get_providers(){
	   try{
		   /** recommended */
			$user_services = UserRequests::where('from',Auth::id())->pluck('user_service_id');
			$service_category_ids = UserServices::whereIn('id', $user_services)->pluck('service_category_id')->toArray();
			$customer_services = CustomerService::where('user_id',Auth::id())->pluck('service_category_id')->toArray(); 
			$service_categories = array_merge($service_category_ids, $customer_services);
			$service_ids = ServiceCategories::whereIn('id',$service_categories)->pluck('service_id');
			$service_ids = array_unique($service_ids->toArray());
			$service_cat_ids = ServiceCategories::whereIn('service_id',$service_ids)->pluck('id');
			$provider_ids = UserServices::where('user_id','!=',Auth::id())->whereIn('service_category_id',$service_cat_ids)->limit(5)->pluck('user_id');
		
			$recommended = User::with(['user_setting','user_service'])->whereIn('id',$provider_ids)->get();
			foreach($recommended as $rec)
			{
				$user_like = UserLike::where('like_to', $rec->id)->where('like_by',Auth::id())->first();
				if(isset($user_like)){
					$rec->is_like = 1;
				}else{
					$rec->is_like = 0;
				}
			}

			/** popular */
			$order_providers = Order::select('user_requests.to',DB::raw('count(orders.id) as total'))
									->leftJoin('user_requests', 'orders.user_request_id', '=', 'user_requests.id')
									->where('user_requests.to','!=',Auth::id())
									->where('orders.status',2)
									->groupBy('user_requests.to')
									->orderBy('total','desc')
									->limit(5)
									->pluck('user_requests.to');

			$popular = User::with(['user_setting','user_service'])->whereIn('id',$order_providers)->get();
			foreach($popular as $p)
                {
                    $user_like = UserLike::where('like_to', $p->id)->where('like_by',Auth::id())->first();
                    if(isset($user_like)){
                        $p->is_like = 1;
                    }else{
                        $p->is_like = 0;
                    }
				}

			/** recently */
			$recent_providers = RecentlyProvider::where('user_id',Auth::id())->latest()->limit(5)->pluck('provider_id');
			$recently = User::with(['user_setting','user_service'])->whereIn('id',$recent_providers)->get(); 
			foreach($recently as $r)
                {
                    $user_like = UserLike::where('like_to', $r->id)->where('like_by',Auth::id())->first();
                    if(isset($user_like)){
                        $r->is_like = 1;
                    }else{
                        $r->is_like = 0;
                    }
				}
	
			$data['Recommended For You'] = UserResource::collection($recommended);
			$data['Popular'] = UserResource::collection($popular);
			$data['Recently'] = UserResource::collection($recently);
			return $this->sendResponse($data,'Providers list');
	   }catch(Exception $e){
			return $this->sendError($e->getMessage(), 422);
	   }
   }
   /**
     * @OA\Get(
     *     path="/api/get/provider/{slug}",
     *     tags={"Fetch Respected providers "},
     *     summary="Fetch Respected providers ",
     *     security={{"bearer_token":{}}},
     *     operationId="Fetch Respected providers ",
     *     
	 * 	   @OA\Parameter(
	 *         name="slug",
	 *         in="path",
	 *         required=true,
	 * 		   description = "recommended or popular or recently",
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),  
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     * )
     * )
    **/
   public function providers($slug){
		try{
			if($slug == 'recommended')
			{
				$user_services = UserRequests::where('from',Auth::id())->pluck('user_service_id');
				$service_category_ids = UserServices::whereIn('id', $user_services)->pluck('service_category_id')->toArray();
				$customer_services = CustomerService::where('user_id',Auth::id())->pluck('service_category_id')->toArray(); 
				$service_categories = array_merge($service_category_ids, $customer_services);
				$service_ids = ServiceCategories::whereIn('id',$service_categories)->pluck('service_id');
				$service_ids = array_unique($service_ids->toArray());
				$service_cat_ids = ServiceCategories::whereIn('service_id',$service_ids)->pluck('id');
				$provider_ids = UserServices::where('user_id','!=',Auth::id())->whereIn('service_category_id',$service_cat_ids)->pluck('user_id');
		
				$recommended = User::with(['user_setting','user_service'])->whereIn('id',$provider_ids)->get();

				$data = UserResource::collection($recommended);
				$data = UserResource::collection($recommended);
				return $this->sendResponse($data,'Recommended Providers list');
			}
			if($slug == 'popular'){
				$order_providers = Order::select('user_requests.to',DB::raw('count(orders.id) as total'))
									->leftJoin('user_requests', 'orders.user_request_id', '=', 'user_requests.id')
									->where('orders.status',2)
									->where('user_requests.to','!=',Auth::id())
									->groupBy('user_requests.to')
									->orderBy('total','desc')
									->pluck('user_requests.to');

				$popular = User::with(['user_setting','user_service'])->whereIn('id',$order_providers)->get();
				$data = UserResource::collection($popular);
				return $this->sendResponse($data,'popular Providers list');	
			}
			if($slug == 'recently')
			{
				$recent_providers = RecentlyProvider::where('user_id',Auth::id())->latest()->pluck('provider_id');
				$recently = User::with(['user_setting','user_service'])->whereIn('id',$recent_providers)->get(); 
				$data = UserResource::collection($recently);
				return $this->sendResponse($data,'recently viewed Providers list');	
			}
			return $this->sendError('Enter Valid slug', 422);
		}catch(Exception $e){
			return $this->sendError($e->getMessage(), 422);
		}
   }
    /**
     * @OA\Get(
     *     path="/api/check/banned",
     *     tags={"User banned"},
     *     summary="0 - user banned | 1 - user not banned",
     *     security={{"bearer_token":{}}},
     *     operationId="Check User Banned",
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     * )
     * )
    **/
   public function check_banned()
   {
		try{
			$user = User::find(Auth::id());
			$is_banned = $user->is_banned;
			return $this->sendResponse($is_banned,'0 - User Banned | 1 - User not banned');
		}catch(Exception $e){
			return $this->sendError($e->getMessage(), 422);
		}
		
   }
   /**
	 *  @OA\Post(
	 *     path="/api/viewed/provider",
	 *     tags={"Viewed Provider"},
	 *     summary="Viewed Provider for recently viewed",
	 *     security={{"bearer_token":{}}},
	 *     operationId="Viewed Provider",
	 * 
	 *     @OA\Parameter(
	 *         name="provider_id",
	 *         in="query",
	 *         required=true,
	 * 		   description = "provider id",
	 *         @OA\Schema(
	 *             type="integer"
	 *         )
	 *     ),   
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid request"
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="not found"
	 *     ),
	 * )
	**/
   public function view_provider(Request $request)
   {
		$validator = Validator::make($request->all(),[
			'provider_id' => 'required|exists:users,id',
		]);

		if($validator->fails()){
			return $this->sendError($validator->errors()->first(),400);
		}
		try{
			$recent = new RecentlyProvider;
			$recent->user_id = Auth::id();
			$recent->provider_id = $request->provider_id;
			$recent->save();
			return $this->sendResponse(null,'Recently added successfully!');
		}catch(Exception $e){
			return $this->sendError($e->getMessage(), 422);
		}
   }
   /**
    *  @OA\Post(
    *     path="/api/chat/image",
    *     tags={"chat Image upload"},
	*     summary="image upload",
	*     security={{"bearer_token":{}}},
    *     operationId="image-upload",
    *     
    *    @OA\RequestBody(
	*          @OA\MediaType(
	*              mediaType="multipart/form-data",
	*              @OA\Schema(
	*                  @OA\Property(
	*                      property="image",
	*                      description="image",
	*                      type="array",
	*                      @OA\Items(type="string", format="binary")
	*                   ),
	*               ),
	*           ),
	*       ),
    *     @OA\Response(
    *         response=200,
    *         description="Success",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthorized"
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Invalid request"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="not found"
    *     ),
    * )
    **/
   public function chat_image(Request $request)
   {
		$validator = Validator::make($request->all(),[
			'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
		]);

		if($validator->fails())
		{
			return $this->sendError($validator->errors()->first(),400);
		}
		try{
			$file = $request->file('image');
			$filename = time().$file->getClientOriginalName();
			$file->move(public_path().'/chats/', $filename);  
			return $this->sendResponse(['file_name' => asset('/chats/'.$filename)], 'Image uploaded successfully.');
		}catch(Exception $e){
			return $this->sendError($e->getMessage(), 422);
		}
   }

   /**
	 *  @OA\Post(
	 *     path="/api/user/like",
	 *     tags={"User like"},
	 *     summary="User Like",
	 *     security={{"bearer_token":{}}},
	 *     operationId="user/like",
	 * 
	 *     @OA\Parameter(
	 *         name="like_to",
	 *         in="query",
	 * 		   description="user id",
	 *         @OA\Schema(
	 *             type="integer"
	 *         )
	 *     ),
     *    @OA\Parameter(
	 *         name="status",
	 *         in="query",
	 * 		   description="like - 1, remove - 2",
	 *         @OA\Schema(
	 *             type="integer"
	 *         )
	 *     ),
	 *     @OA\Response(
    *         response=200,
    *         description="Success",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthorized"
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Invalid request"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="not found"
    *     ),
    * )
	**/
    public function user_like(Request $request)
	{
		$validator = Validator::make($request->all(),[
		
		]);
		if($validator->fails())
		{
			return $this->sendError($validator->errors()->first(),400);
		}
		try {
			if($request->status == 1)
			{
				$user_like = UserLike::where('like_to',$request->like_to)->where('like_by',Auth::id())->first();
				if($user_like)
				{
					return $this->sendResponse(null, 'Already liked.');
				} else {
					$user_like = new UserLike;
					$user_like->like_to = $request->like_to;
					$user_like->like_by = Auth::id();
					$user_like->save();
					return $this->sendResponse(null, 'liked successfully!');
				}
			}
			if($request->status == 2)
			{
				$user_like = UserLike::where('like_to',$request->like_to)->where('like_by',Auth::id())->first();
				if($user_like->delete())
				{
					$user_like->delete();
					return $this->sendResponse(null, 'Unliked successfully!');    
				}  
			}
		}catch(Exception $e){
			return $this->sendError($e->getMessage(),200);
		}
	}
	  
	/**
	 *  @OA\Post(
	 *     path="/api/add/customer/service",
	 *     tags={"Add Customer Service category"},
	 *     summary="Add Customer Service category",
	 *     security={{"bearer_token":{}}},
	 *     operationId="Add Customer Service",
	 * 
	 *     @OA\Parameter(
	 *         name="service_category[]",
	 *         in="query",
	 * 		   description="user id",
	 *         @OA\Schema(
	 *             type="array",
	 * 			   @OA\Items(type="integer")
	 *         )
	 *     ),
	 *     @OA\Response(
    *         response=200,
    *         description="Success",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthorized"
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Invalid request"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="not found"
    *     ),
    * )
	**/
	public function add_customer_service(Request $request)
	{
		try{
			if(count($request->service_category) > 0){
				foreach($request->service_category as  $sc){
					$cs = new CustomerService;
					$cs->user_id = Auth::id();
					$cs->service_category_id = $sc;
					$cs->save();
				}
				return $this->sendResponse(null, 'custome service request added successfully!');
			}
			return $this->sendError("Service category required!",400);
		}catch(Exception $e){
			return $this->sendError($e->getMessage(),200);
		}
		
	}
	/**
   *  @OA\Get(
   *     path="/api/voice/accessToken",
   *     tags={"voice access token"},
   *     summary="voice access token",
   *     security={{"bearer_token":{}}},
   *     operationId="voice access token",
   *     
   *     @OA\Response(
   *         response=200,
   *         description="Success",
   *         @OA\MediaType(
   *             mediaType="application/json",
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthorized"
   *     ),
   * )
   **/
	public function voice_token(Request $request)
	{
		try{
			$token = twillioVoiceToken(Auth::id());
			return $this->sendResponse($token, 'voice access token');
		}catch(Exception $e){
			return $this->sendError($e->getMessage(),200);
		}
	}
}