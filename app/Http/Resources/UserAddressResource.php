<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'mobile' => $this->mobile,
            'address' => $this->address,
            'city' => $this->city,
            'zip' => $this->zip,
            'address_type' => $this->address_type,
            'default' => $this->default,
            'user_id' => $this->user_id
        ];
    }
}
