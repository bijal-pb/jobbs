<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\UserSetting;
use App\Models\UserOtp;
use Stripe;
use DB;
use Exception;
use Hash;


/**
* @OA\Info(
*      description="",
*     version="1.0.0",
*      title="Jobbs",
* )
**/
 
/**
*  @OA\SecurityScheme(
*     securityScheme="bearer_token",
*         type="http",
*         scheme="bearer",
*     ),
**/
class AuthController extends BaseController
{
    /**
    *  @OA\Post(
    *     path="/api/register",
    *     tags={"Register"},
    *     summary="Register",
    *     operationId="register",
    *     
    *     @OA\Parameter(
    *         name="first_name",
    *         in="query",
    *         required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="last_name",
    *         in="query",
    *         required=true,
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
    *         name="password",
    *         in="query",
    *         required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
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
    *         name="type",
    *         in="query",
    *         required=true,
    *         description="1 - seeker | 2 - provider",
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),   
    *    @OA\Parameter(
    *         name="firebase_id",
    *         in="query",
    *         required=true,
    *         description="Firebase id for chat",
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),   
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
    *     @OA\Response(
   *         response=422,
   *         description="Unprocessable entity"
   *     ),
    * )
    **/
   
   public function register(Request $request)
   {
        
		$validator = Validator::make($request->all(),[
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
			'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'country_code' => 'required',
            'phone' => 'required|unique:users,phone',
            'type' => 'required|in:1,2',
            'firebase_id' => 'required'
		]);

		if($validator->fails())
		{
			return $this->sendError($validator->errors()->first(),400);
		}
		DB::beginTransaction();
		try{
			$user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
			$user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->country_code = trim($request->country_code);
            $user->phone = trim($request->phone);
            $user->device_type = $request->device_type;
            $user->device_token = $request->device_token;
            $user->firebase_id = $request->firebase_id;
            $user->save();

            $user_setting = new UserSetting;
            $user_setting->user_id = $user->id;
            $user_setting->provider = $request->type;
            $user_setting->online = 0;
            $user_setting->notification = 0;
            $user_setting->save();

            // $otp = rand(1000,9999);
            $otp = 1234;
            $user_otp = new UserOtp;
            $user_otp->phone = $user->country_code.$user->phone;
            $user_otp->otp = $otp;
            $user_otp->save();

            sendSms($user_otp->phone, $otp);
            sendPushNotification($user->device_token,'Jobbs Verification Code','Jobbs Verification code is :'.$otp,1,$user->id,null);
            $user->assignRole([2]);
			DB::commit();
			return $this->sendResponse($user, 'User registered successfully.');
		} catch(Exception $e) {
			DB::rollback();
			return $this->sendError($e->getMessage(),422);
		}
		
   }
   /**
   *  @OA\Post(
   *     path="/api/otp/send",
   *     tags={"otp-send"},
   *     summary="otp-send",
   *     operationId="otp-send",
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
   *     @OA\Response(
   *         response=422,
   *         description="Unprocessable entity"
   *     ),
   * )
   **/
  
  
  public function otp_send(Request $request)
  {
    $validator = Validator::make($request->all(),[
        'country_code' => 'required',
        'phone' => 'required|exists:users,phone',
    ]);

    if($validator->fails())
    {
        return $this->sendError($validator->errors()->first(),400);
    }
    try {
        $uos = UserOtp::where('phone',trim($request->country_code).trim($request->phone))->get();
        foreach($uos as $uo)
        {
            $uo->delete();
        }

        // $otp = rand(1000,9999);
        $otp = 1234;
        $user_otp = new UserOtp;
        $user_otp->phone = trim($request->country_code).trim($request->phone);
        $user_otp->otp = $otp;
        $user_otp->save();
        
        sendSms($user_otp->phone, $otp);
        // sendPushNotification($user->device_token,'Jobbs Verification Code','Jobbs Verification code is :'.$otp,1,$user->id,null);

        return $this->sendResponse('', 'Send otp successfully!');
    } catch (Exception $e){
        return $this->sendError($e->getMessage(),422);
    }
          
  }
  /**
   *  @OA\Post(
   *     path="/api/call",
   *     tags={"call"},
   *     summary="call",
   *     operationId="call",
   * 
   *     @OA\Parameter(
   *         name="user_id",
   *         in="query",
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
   *     @OA\Response(
   *         response=400,
   *         description="Invalid request"
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="not found"
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Unprocessable entity"
   *     ),
   * )
   **/
  public function call(Request $request){
    try {
            $voice_call = voiceCall($request);
            return $this->sendResponse('', $voice_call);
    } catch (Exception $e){
        return $this->sendError($e->getMessage(),422);
    }

  }
  
   /**
   *  @OA\Post(
   *     path="/api/otp/verify",
   *     tags={"otp-verify"},
   *     summary="otp-verify",
   *     operationId="otp-verify",
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
   *         name="otp",
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
   *     @OA\Response(
   *         response=422,
   *         description="Unprocessable entity"
   *     ),
   * )
   **/
  public function otp_verify(Request $request)
  {
    $validator = Validator::make($request->all(),[
        'country_code' => 'required',
        'phone' => 'required|exists:users,phone',
        'otp' => 'required',
    ]);

    if($validator->fails())
    {
        return $this->sendError($validator->errors()->first(),400);
    }
    try {
        $uo = UserOtp::where('phone',trim($request->country_code).trim($request->phone))->latest()->first();
        if($uo->otp == $request->otp)
        {
            $user = User::where('phone', trim($request->phone))->where('country_code',trim($request->country_code))->first();
            if($user)
            {
                $user->phone_verified = 1;
                $user->save();
                $user->tokens()->delete();
                if($user->is_banned == 0){
                    return $this->sendError('Your account is block, Please contact to adminitrator!',422);
                }
                $tokenResult = $user->createToken('authToken')->plainTextToken;
                $data['token'] = $tokenResult;
                $data['user'] =  $user;
                return $this->sendResponse($data, 'Otp verified successfully.');
            }
            return $this->sendError('User Not registered using this phone!',404);
            
        }
        return $this->sendError('Enter Valid otp!',404);
    } catch (Exception $e){
        return $this->sendError($e->getMessage(),422);
    }
          
  }
   /**
   *  @OA\Post(
   *     path="/api/login",
   *     tags={"Login"},
   *     summary="Login",
   *     operationId="login",
   * 
   *     @OA\Parameter(
   *         name="country_code",
   *         in="query",
   *         @OA\Schema(
   *             type="string"
   *         )
   *     ), 
   *    @OA\Parameter(
   *         name="phone",
   *         in="query",
   *         @OA\Schema(
   *             type="string"
   *         )
   *     ),   
   *     @OA\Parameter(
   *         name="password",
   *         in="query",
   *         @OA\Schema(
   *             type="string"
   *         )
   *     ), 
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
   *     @OA\Response(
   *         response=422,
   *         description="Unprocessable entity"
   *     ),
   * )
   **/
  
   public function login(Request $request)
   {
       $validator = Validator::make($request->all(),[
           'country_code' => 'required',
           'phone' => 'required|',
           'password' => 'required',
       ]);

       if($validator->fails())
       {
           return $this->sendError($validator->errors()->first(),400);
       }
       try{
            $user = User::where('country_code',$request->country_code)
                        ->where('phone',$request->phone)->first();
            if($user){
                if($user->phone_verified != 1)
                {
                    return $this->sendError('Please first vrified mobile!',404);
                }
                if($user->is_banned == 0){
                    $user->tokens()->delete();
                    return $this->sendError('Your account is block, Please contact to adminitrator!',401);
                }
                if(Hash::check($request->password,$user->password))
                {
                    
                    $user->device_type = $request->device_type;
                    $user->device_token = $request->device_token;
                    $user->save();
                    $user->tokens()->delete();
                    $tokenResult = $user->createToken('authToken')->plainTextToken;
                    $user = User::with(['user_setting','user_service'])->find($user->id);
                    $approveDocCount = UserDocument::where('user_id',$user->id)->where('status',1)->count();
                    $user->document_approved = $approveDocCount >= 2 ? 1 : 0; 
                    $data['token'] = $tokenResult;
                    $data['user'] =   new UserResource($user);
                    return $this->sendResponse($data, 'User Logged in successfully!!');
                }
                return $this->sendError('Enter Valid password!',404);
            }
            return $this->sendError('Enter valid country and phone!',404);
       }catch(Exception $e){
        return $this->sendError('somthing went wrong, please try again!',422);
       }
            
   }
   /**
   *  @OA\Get(
   *     path="/api/logout",
   *     tags={"Logout"},
   *     summary="Logout",
   *     security={{"bearer_token":{}}},
   *     operationId="logout",
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
   public function logout()
   {
       Auth::user()->currentAccessToken()->delete();
       $data = null;
       return $this->sendResponse($data, 'User logout successfully!');
   }
   
}
