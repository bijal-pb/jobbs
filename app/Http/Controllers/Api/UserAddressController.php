<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserAddressResource;
use Auth;
use Exception;
use Illuminate\Http\Request;

class UserAddressController extends BaseController
{
    /**
	 *  @OA\Post(
	 *     path="/api/add/user/address",
	 *     tags={"Add user address"},
	 *     summary="add user address",
	 *     security={{"bearer_token":{}}},
	 *     operationId="add/user/address",
	 * 
     *     @OA\Parameter(
	 *         name="first_name",
	 *         in="query",
	 * 	 	   description="first name",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),   
     *       @OA\Parameter(
	 *         name="last_name",
	 *         in="query",
	 * 	 	   description="last name",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),   
     *      @OA\Parameter(
	 *         name="mobile",
	 *         in="query",
	 * 	 	   description="mobile",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ), 
     *       @OA\Parameter(
	 *         name="address",
	 *         in="query",
	 * 	 	   description="address",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),  
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
     *      @OA\Parameter(
	 *         name="city",
	 *         in="query",
	 * 	 	   description="city",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),     
     *      @OA\Parameter(
	 *         name="zip",
	 *         in="query",
	 * 	 	   description="zip",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ), 
     *     @OA\Parameter(
	 *         name="address_type",
	 *         in="query",	
     *         description="home | office | other",		
	 *         @OA\Schema(
	 *             type="string"
	 *         )
     *     ),
     *      @OA\Parameter(
	 *         name="default",
	 *         in="query",	
     *         description="default-boolean",		
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
    public function add_user_address(Request $request)
    {
        $validator = Validator::make($request->all(),[
           
        ]);
		if($validator->fails())
		{
			return $this->sendError($validator->errors()->first(),400);
		}
        try {
			
			 
			 if($request->default == 1)
			{
				$default = UserAddress::where('user_id',Auth::id())->get();
				foreach($default as $defaults){
					$defaults->default = null;
					$defaults->save();
					//return $this->sendResponse($request->default,422);   
				 }	
			}	
            $user_address = new UserAddress;
            $user_address->user_id = Auth::id();
            $user_address->first_name = $request->first_name;
            $user_address->last_name = $request->last_name;
            $user_address->mobile = $request->mobile;
            $user_address->address = $request->address;
			$user_address->lat = $request->latitude;
			$user_address->lang = $request->longitude;
            $user_address->city = $request->city;
            $user_address->zip = $request->zip;
            $user_address->address_type = $request->address_type;
			$user_address->default = $request->default;
            $user_address->save();
			return $this->sendResponse($user_address, 'User Address added successfully!.');         
		}catch(Exception $e)
        {
            return $this->sendError($e->getMessage(),422);
        }
    }
	/**
	 *  @OA\Post(
	 *     path="/api/edit/user/address",
	 *     tags={"edit user address"},
	 *     summary="edit user address",
	 *     security={{"bearer_token":{}}},
	 *     operationId="edit/user/address",
	 * 
	 *      @OA\Parameter(
	 *         name="user_addresses_id",
	 *         in="query",
	 * 	 	   description="user_addresses_id",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),  
     *     @OA\Parameter(
	 *         name="first_name",
	 *         in="query",
	 * 	 	   description="first name",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),   
     *       @OA\Parameter(
	 *         name="last_name",
	 *         in="query",
	 * 	 	   description="last name",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),   
     *      @OA\Parameter(
	 *         name="mobile",
	 *         in="query",
	 * 	 	   description="mobile",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ), 
     *       @OA\Parameter(
	 *         name="address",
	 *         in="query",
	 * 	 	   description="address",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),   
	 *      @OA\Parameter(
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
     *      @OA\Parameter(
	 *         name="city",
	 *         in="query",
	 * 	 	   description="city",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),     
     *      @OA\Parameter(
	 *         name="zip",
	 *         in="query",
	 * 	 	   description="zip",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ), 
     *     @OA\Parameter(
	 *         name="address_type",
	 *         in="query",	
     *         description="home | office | other",		
	 *         @OA\Schema(
	 *             type="string"
	 *         )
     *     ),
     *      @OA\Parameter(
	 *         name="default",
	 *         in="query",	
     *         description="default-boolean",		
	 *         @OA\Schema(
	 *              type="integer"
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
    public function edit_user_address(Request $request)
    {
        $validator = Validator::make($request->all(),[
           'user_addresses_id' => 'required',
        ]);
		if($validator->fails())
		{
			return $this->sendError($validator->errors()->first(),400);
		}
        try {
			if($request->default == 1)
			{
				$default = UserAddress::where('user_id',Auth::id())->get();
				foreach($default as $defaults){
					$defaults->default = null;
					$defaults->save(); 
				 }	
			}	
            $user_address = UserAddress::find($request->user_addresses_id);
            $user_address->user_id = Auth::id();
            $user_address->first_name = $request->first_name;
            $user_address->last_name = $request->last_name;
            $user_address->mobile = $request->mobile;
            $user_address->address = $request->address;
			$user_address->lat = $request->latitude;
			$user_address->lang = $request->longitude;
            $user_address->city = $request->city;
            $user_address->zip = $request->zip;
            $user_address->address_type = $request->address_type;
            $user_address->default = $request->default;
            $user_address->save();
			return $this->sendResponse($user_address, 'User Address is edited successfully.');         
		}catch(Exception $e)
        {
            return $this->sendError($e->getMessage(),422);
        }
    }
	/**
	 *  @OA\Post(
	 *     path="/api/delete/user/address",
	 *     tags={"delete user address"},
	 *     summary="delete user Address",
	 *     security={{"bearer_token":{}}},
	 *     operationId="delete/user/address",
	 * 
	 * 	   
	 *     @OA\Parameter(
	 *         name="user_addresses_id",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
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
	public function delete_user_address(Request $request)
	{
		$validator = Validator::make($request->all(),[
			'user_addresses_id' => 'required',
		]);

		if($validator->fails())
		{
			return $this->sendError($validator->errors()->first(),400);
		}
		try {
			$user_address = UserAddress::find($request->user_addresses_id);
			if($user_address)
			{
				if($user_address->delete())
				{
					return $this->sendResponse(null,'User address is deleted successfully.');
				}
			}
            return $this->sendError('Enter valid user_address_id!.',422);
		}
		catch( Exception $e){
			return $this->sendError('Something went wrong, Please try again!.',422);
		}
	}

	  /**
     * @OA\Get(
     *     path="/api/get/user/address",
     *     tags={"Get User Address"},
     *     summary="get user address",
     *     security={{"bearer_token":{}}},
     *     operationId="get/user/address",
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
    public function get_user_address(Request $request)
    {
		    try{
                  $user_address = UserAddress::where('user_id',Auth::id())->get();
                  return $this->sendResponse($user_address, 'User Addresses Data.');
			}catch(Exception $e)
			{
			return $this->sendError('Something went wrong, Please try again!.',422);
			}   		
    }

}
