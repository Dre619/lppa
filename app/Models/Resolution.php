<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Resolution
 * 
 * @property int $id
 * @property string $resolution_type
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Application[] $applications
 *
 * @package App\Models
 */
class Resolution extends Model
{
	protected $table = 'resolutions';

	protected $fillable = [
		'resolution_type',
		'description'
	];

	public function applications()
	{
		return $this->belongsToMany(Application::class, 'application_resolutions')
					->withPivot('id', 'resolution_details', 'resolution_date', 'sequence')
					->withTimestamps();
	}
}
