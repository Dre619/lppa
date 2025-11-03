<?php
namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ChangeUseStage extends Model
{
    protected $table = 'change_of_use';

    protected $fillable = [
        'stage_name',
        'description'
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