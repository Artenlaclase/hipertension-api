<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HydrationRecordResource extends JsonResource
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
            'user_id' => $this->user_id,
            'liquid_type' => $this->liquid_type,
            'amount_ml' => $this->amount_ml,
            'note' => $this->note,
            'recorded_at' => $this->recorded_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
