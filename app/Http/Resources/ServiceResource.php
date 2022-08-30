<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $icon = null;
        // if($this->icon != null)
        // {
        //     $icon = url('/serviceimages/'.$this->icon);
        // }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon,
            'detail' => $this->detail
        ];
    }
}
