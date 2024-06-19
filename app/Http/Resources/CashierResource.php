<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $parent = parent::toArray($request);
        $allowed_keys = ['id', 'email', 'owner_name', 'profile_photo_url', 'umkm_name'];

        foreach ($parent as $key => $value) {
            if (!in_array($key, $allowed_keys)) {
                unset($parent[$key]);
            }
        }

        $parent['umkm_name'] = "Kasir " . $parent['umkm_name'];

        return $parent;
    }
}
