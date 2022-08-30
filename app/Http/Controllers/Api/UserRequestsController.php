<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\UserRequests;
use App\Models\Order;
use App\Http\Resources\UserRequestsResource;
use App\Models\UserServices;
use App\Models\Services;
use Carbon\Carbon;
use App\Http\Resources\UserServicesResource;
use Exception;
use Auth;

class UserRequestsController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/get-user-requests",
     *     tags={"get user request"},
     *     summary="get user request",
     *     security={{"bearer_token":{}}},
     *     operationId="get-user-requests",
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
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     * )
    **/
    public function get_user_requests(Request $request)
    {
      try{
         $date = Carbon::now()->subDays(7);
         $user_req = UserRequests::with(['userservice','from_user','to_user','order_review','order_status'])
                        ->where('to',Auth::id())
                        ->where('status',0)
                        ->get();
          $user_requests = UserRequests::where('to',Auth::id())->where('status',1)->pluck('id');
          $orders = Order::whereIn('user_request_id',$user_requests)
                  ->where('status',2)
                  ->where('complete_time','>=',$date)
                  ->get();
          
          $total_earning = 0;
          foreach($orders as $o){
            $total_earning += $o->total_amount;
          }
         return $this->sendResponse([
            'request_list' => $user_req,
            'weekly_order' => count($orders),
				    'weekly_earning' => $total_earning,
         ], 'Get User Requests data.');
      }catch(Exception $e)
      {
      return $this->sendError($e->getMessage(),422);
      }         
    }
   
}
