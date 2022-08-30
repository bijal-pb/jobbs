<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\DocumentTypeResource;
use App\Http\Resources\UserDocumentResource;
use App\Http\Resources\UserResource;
use App\Models\UserDocument;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\Request;

class DocumentTypeController extends BaseController
{
     /**
     * @OA\Get(
     *     path="/api/get-document-type",
     *     tags={"Get Document Type"},
     *     summary="get document type",
     *     security={{"bearer_token":{}}},
     *     operationId="get-document-type",
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
    public function get_document_type(Request $request)
    {
		    try{
                $doctype = DocumentType::get();
                $doctype =  DocumentTypeResource::collection($doctype);
                return $this->sendResponse($doctype, 'Document Types data.');
			}catch(Exception $e)
			{
			return $this->sendError('Something went wrong, Please try again!.',422);
			}   		
    }


    /**
	 *  @OA\Post(
	 *     path="/api/add/user/document",
	 *     tags={"Add user document"},
	 *     summary="add user document",
	 *     security={{"bearer_token":{}}},
	 *     operationId="add/user/document",
     *
     *     @OA\Parameter(
	 *         name="document_type_id",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="integer"
	 *         )
	 *     ),
    *     @OA\RequestBody(
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *                  @OA\Property(
    *                      property="document",
    *                      description="document image/file",
    *                      type="array",
    *                      @OA\Items(type="file", format="binary")
    *                   ),
	* 				),
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
    public function add_user_document(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'document_type_id' => 'required',
			 'document'  => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);
		if($validator->fails())
		{
			return $this->sendError($validator->errors()->first(),400);
		}
		$filename = null;
		if($request->hasfile('document')) {
			$file = $request->file('document');
			$filename = time().$file->getClientOriginalName();
			$file->move(public_path().'/documentimages/', $filename);  
		   }
		try{
			$userdoc = new UserDocument;
			$userdoc->user_id= Auth::id();
            $userdoc->document_type_id = $request->document_type_id;
			// $userdoc->status = $request->status;
			if($request->hasfile('document')) {
				$userdoc->document = $filename;
			}
			$userdoc->save();
			$userdoc =  new UserDocumentResource($userdoc);
			return $this->sendResponse($userdoc, 'User Document added successfully!.');         
		}catch(Exception $e)
        {
            return $this->sendError($e->getMessage(),200);
        }
        
    }
}
