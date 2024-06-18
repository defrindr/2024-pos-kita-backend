<?php

namespace App\Models;

use App\Models\UMKMGroupList;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UMKMGroups extends Model
{
    use HasFactory;
    protected $table = 'umkm_groups';

    protected $guarded = ['id'];

    public function umkmGroupLists()
    {
        return $this->hasMany(UMKMGroupList::class, 'id_umkm_group');
    }
}
