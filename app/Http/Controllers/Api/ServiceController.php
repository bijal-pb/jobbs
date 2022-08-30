<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Service;
use App\Http\Resources\ServiceResource;
use App\Models\ServiceCategories;
use App\Http\Resources\ServiceCategoriesResource;
use App\Models\UserServices;
use App\Models\User;
use App\Http\Resources\UserServicesResource;

use App\Models\UserRequests;
use App\Http\Resources\UserRequestsResource;
use App\Models\Order;

use App\Models\UserOrderRate;

use App\Http\Resources\UserResource;
use App\Models\UserLike;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
use Exception;
use DB;

class ServiceController extends BaseController
{
 /**
     * @OA\Get(
     *     path="/api/service",
     *     tags={"Get Service"},
     *     summary="get service",
     *     security={{"bearer_token":{}}},
     *     operationId="get-service",
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
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     * )
    **/
    public function get_service(Request $request)
    {
      try{
         $service = Service::get();
         $service =  ServiceResource::collection($service);
         return $this->sendResponse($service, 'Service data.');
      }catch(Exception $e)
      {
      return $this->sendError('Something went wrong, Please try again!.',null,422);
      }         
    }
     /**
     *  @OA\Get(
     *     path="/api/service/category",
     *     tags={"get service categories"},
     *     summary="get service categories",
     *     security={{"bearer_token":{}}},
     *     operationId="get service categories",
	 * 
     *    @OA\Parameter(
     *         name="service_id",
     *         in="query",
	  *         required=true,
	  * 		   description="service id",
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
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     * )
     **/
    public function get_service_categories(Request $request)
    {
     $validator = Validator::make($request->all(),[
        'service_id' => 'required',     
     ]);
     if($validator->fails())
     {
        return $this->sendError($validator->messages()->first(),null,200);
     } 
    try{
     $servicecat = ServiceCategories::where('service_id',$request->service_id)->get();
     $servicecat =  ServiceCategoriesResource::collection($servicecat);
     return $this->sendResponse($servicecat,'Service Categories detail.');

    }catch(Exception $e)
    {
    return $this->sendError('Something went wrong, Please try again!.',null,422);
    }
 
   }
   /**
	 *  @OA\Post(
	 *     path="/api/service/provider/add",
	 *     tags={"Add Provider Services"},
	 *     summary="add provider services",
	 *     security={{"bearer_token":{}}},
	 *     operationId="add-provider-services",
	 * 
	 * 	   @OA\Parameter(
	 *         name="service_category_id",
	 *         in="query",
	 * 	 	   description="category_id",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),   
	 *     @OA\Parameter(
	 *         name="price",
	 *         in="query",
	 * 	 	   description="price",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),   
    *     @OA\Parameter(
	 *         name="status",
	 *         in="query",	
     *         description=" 1-active | 2-deactive",		
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
   public function add_provider_services(Request $request)
   {
      {
         $validator = Validator::make($request->all(),[
             'service_category_id' => 'required',
             'price' => 'required'
         ]);
         if($validator->fails())
       {
          return $this->sendError($validator->messages()->first(),null,200);
         } 
       try{
          $userservice = new UserServices;
          $userservice->id = $request->id;
          $userservice->user_id= Auth::id();
          $userservice->service_category_id = $request->service_category_id;
          $userservice->price = $request->price;
          $userservice->status = $request->status;
          $userservice->save();
          $userservice =  new UserServicesResource($userservice);
          return $this->sendResponse($userservice, 'User Services Added successfully!.');         
       }catch(Exception $e)
         {
             return $this->sendError('Something went wrong, Please try again!.',$e,200);
         }
      } 
   }


    /**
	 *  @OA\Post(
	 *     path="/api/fetch/provider/detail/customer",
	 *     tags={"fetch provider detail customer"},
	 *     summary="fetch provider detail customer",
	 *     security={{"bearer_token":{}}},
	 *     operationId="fetch/provider/detail/customer",
	 * 
	 * 	   @OA\Parameter(
	 *         name="provider_id",
	 *         in="query",
	 * 	 	   description="provider_id",				
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
	 * )
	**/
   public function fetch_provider_detail_customer(Request $request)
   {
      {
         $validator = Validator::make($request->all(),[
            'provider_id' => 'required',     
	     ]);
         if($validator->fails())
          	{
          		return $this->sendError($validator->errors()->first(),400);
            } 
			 try{
               $user = User::find($request->provider_id);
               $user_req = UserRequests::where('to',$request->provider_id)->where('status',1)->pluck('id');
               $user->total_jobbs = Order::whereIn('user_request_id',$user_req)->where('status',2)->count();
               $user->rate = UserOrderRate::where('rate_to',$request->provider_id)->avg('rate');
               $user->reviews = UserOrderRate::with('rate_by')->where('rate_to',$request->provider_id)->get();
               $user_like = UserLike::where('like_to', $request->provider_id)->where('like_by',Auth::id())->first();
               if(isset($user_like)){
                    $user->is_like = 1;
               }else{
                    $user->is_like = 0;
               }
               $user = new UserResource($user);
               return $this->sendResponse($user,'fetch provider detail.');
			    }
			    catch(Exception $e)
			    {
               return $this->sendError('Something went wrong, Please try again!.',422);
			    }  
      }
   }

   
    /**
     *  @OA\Get(
     *     path="/api/get-fetch-service-customer",
     *     tags={"get-fetch-service-customer"},
     *     summary="get fetch service customer",
     *     security={{"bearer_token":{}}},
     *     operationId="get fetch service customer",
	 * 
     *    @OA\Parameter(
     *         name="service_id",
     *         in="query",
	  *         required=true,
	  * 		   description="service id",
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
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     * )
     **/
    public function get_fetch_service_customer(Request $request)
    {
     $validator = Validator::make($request->all(),[
        'service_id' => 'required',     
     ]);
     if($validator->fails())
     {
        return $this->sendError($validator->messages()->first(),null,200);
     } 
    try{
        
          $fetch_service = ServiceCategories::where('service_id',$request->service_id)->pluck('id'); 
          $service = UserServices::with(['user'])->whereIn('service_category_id',$fetch_service)  
                    ->get();
          $service_req = UserServicesResource::collection($service); 
          return $this->sendResponse($service_req,'Fetch service of provider detail');
        }catch(Exception $e)
        {
          return $this->sendError($e->getMessage(),null,422);
        }
 
   }
 /**
	 *  @OA\Post(
	 *     path="/api/get/provider/services",
	 *     tags={"get provider service category wise"},
	 *     summary="get provider service category wise",
	 *     security={{"bearer_token":{}}},
	 *     operationId="get/provider/services",
	 * 
	 * 	   @OA\Parameter(
	 *         name="service_category_id",
	 *         in="query",
	 * 	 	   description="service category id",				
	 *         required=true,
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
	 * )
	**/
   public function get_provider_services(Request $request)
   {
      $validator = Validator::make($request->all(),[
            'service_category_id' => 'required',     
	   ]);
      if($validator->fails()){
         return $this->sendError($validator->errors()->first(),400);
      } 
      try{
               $fetch_service = UserServices::where('service_category_id',$request->service_category_id)->pluck('user_id')->toArray();
               $service = User::whereIn('id',$fetch_service)->get();
               $service_req = UserResource::collection($service); 
               return $this->sendResponse($service_req,'provider service data');
      }
      catch(Exception $e)
      {
            return $this->sendError($e->getMessage(),422);
      }  
   }
}
