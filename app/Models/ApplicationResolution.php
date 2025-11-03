<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ApplicationResolution
 * 
 * @property int $id
 * @property int $application_id
 * @property int $resolution_id
 * @property string|null $resolution_details
 * @property Carbon|null $resolution_date
 * @property int $sequence
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Application $application
 * @property Resolution $resolution
 *
 * @package App\Models
 */
class ApplicationResolution extends Model
{
	protected $table = 'application_resolutions';

	protected $casts = [
		'application_id' => 'int',
		'resolution_id' => 'int',
		'resolution_date' => 'datetime',
		'sequence' => 'int'
	];

	protected $fillable = [
		'application_id',
		'resolution_id',
		'resolution_details',
		'resolution_date',
		'sequence'
	];

	public function application()
	{
		return $this->belongsTo(Application::class);
	}

	public function resolution()
	{
		return $this->belongsTo(Resolution::class);
	}
}
