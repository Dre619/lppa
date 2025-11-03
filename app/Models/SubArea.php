<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SubArea
 * 
 * @property int $id
 * @property int $registration_area_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property RegistrationArea $registration_area
 *
 * @package App\Models
 */
class SubArea extends Model
{
	protected $table = 'sub_areas';

	protected $casts = [
		'registration_area_id' => 'int'
	];

	protected $fillable = [
		'registration_area_id',
		'name'
	];

	public function registration_area()
	{
		return $this->belongsTo(RegistrationArea::class);
	}
}
