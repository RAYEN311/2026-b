<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data['product_image'] = $this->product_image ? url('storage/'.$this->product_image) : null;
        $data['images'] = $this->images->map(function ($image) {
            return url('storage/'.$image->image_path);
        });

        return $data;
    }
}
