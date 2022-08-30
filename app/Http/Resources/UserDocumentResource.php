<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

     
    public function toArray($request)
    {
        $document = null;
        if($this->document != null)
        {
            $document = url('/documentimages/'.$this->document);
        }
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'document_type_id' => $this->document_type_id,
            'document' => $document,
            'status' => $this->status,
            'document_name' => isset($this->documentname) ? $this->documentname->name : null,
        ];
    }
}
