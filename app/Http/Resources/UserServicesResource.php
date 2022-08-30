<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserServicesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $photo = null;
        if($this->photo != null)
        {
            $photo = url('/uploads/'.$this->photo);
        }
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'price' => $this->price,
            // 'status' => $this->status,
            'service_category_id' => $this->service_category_id,

            // 'service_id' => $this->service_id,
            'service_detail' => $this->servicecategory,
            'rate' => 0,
            'address' => 0,
            'provider_id' => $this->user_id,
            'provider_name' => $this->user->first_name.' '.$this->user->last_name,
            'provider_image' => $this->user->photo,
            'bio' =>$this->user->bio

           
        ];
    }
}
