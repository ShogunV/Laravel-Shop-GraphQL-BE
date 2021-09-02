<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'totalOrders' => $this->orders->count(),
            'totalItems' => $this->orders->sum('total_quantity'),
            'totalDineros' => number_format($this->orders->sum('total'), 2),
        ];
    }
}
