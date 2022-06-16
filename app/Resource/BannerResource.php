<?php

namespace App\Resource;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image' => $this->image ? asset('storage/Banners/' . $this->image) : null,
           // 'image' =>  Storage::url("app/Banners/{$this->image}"),
            'redirect_type' => $this->redirect_type,
            'click_url' => $this->image_url,
            'status' => $this->status === 1 ? 'active' : 'inactive'
        ];
    }
}
