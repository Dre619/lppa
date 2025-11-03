<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alias extends Model
{
    protected $fillable = ['district_id', 'alias'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
