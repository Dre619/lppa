<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Enforcement
 * 
 * @property int $id
 * @property int $stop_order_id
 * @property Carbon|null $date_issued
 * @property string|null $description
 * @property string|null $current_stage
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property StopOrder $stop_order
 *
 * @package App\Models
 */
class Enforcement extends Model
{
	protected $table = 'enforcements';

	protected $casts = [
		'stop_order_id' => 'int',
		'date_issued' => 'datetime'
	];

	protected $fillable = [
		'stop_order_id',
		'date_issued',
		'description',
		'current_stage',
		'status'
	];

	public function stop_order()
	{
		return $this->belongsTo(StopOrder::class);
	}
}
