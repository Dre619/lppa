<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class District
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Application[] $applications
 * @property Collection|RegistrationArea[] $registration_areas
 * @property Collection|StopOrder[] $stop_orders
 *
 * @package App\Models
 */
class District extends Model
{
	protected $table = 'districts';

	protected $fillable = [
		'name'
	];

	public function applications()
	{
		return $this->hasMany(Application::class);
	}

	public function registration_areas()
	{
		return $this->hasMany(RegistrationArea::class);
	}

	public function stop_orders()
	{
		return $this->hasMany(StopOrder::class);
	}

	public function aliases()
	{
		return $this->hasMany(Alias::class);
	}
}
