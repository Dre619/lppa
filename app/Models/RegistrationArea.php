<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RegistrationArea
 * 
 * @property int $id
 * @property int $district_id
 * @property string $name
 * @property string $area_key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property District $district
 * @property Collection|Application[] $applications
 * @property Collection|SubArea[] $sub_areas
 *
 * @package App\Models
 */
class RegistrationArea extends Model
{
	protected $table = 'registration_areas';

	protected $casts = [
		'district_id' => 'int'
	];

	protected $fillable = [
		'district_id',
		'name',
		'area_key'
	];

	public function district()
	{
		return $this->belongsTo(District::class);
	}

	public function applications()
	{
		return $this->hasMany(Application::class);
	}

	public function sub_areas()
	{
		return $this->hasMany(SubArea::class);
	}
}
