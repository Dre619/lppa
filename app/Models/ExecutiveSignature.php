<?php
namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ExecutiveSignature extends Model
{
    protected $table = 'executive_signatures';

    protected $fillable = [
        'signature',
        'is_active'
    ];

    /**
     * Get the created_at attribute formatted as a Carbon instance.
     *
     * @return Carbon
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value);
    }

    /**
     * Get the updated_at attribute formatted as a Carbon instance.
     *
     * @return Carbon
     */
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value);
    }
}