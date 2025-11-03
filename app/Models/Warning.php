<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Warning
 * 
 * @property int $id
 * @property int $stop_order_id
 * @property Carbon|null $notice_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property StopOrder $stop_order
 *
 * @package App\Models
 */
class Warning extends Model
{
	protected $table = 'warnings';

	protected $casts = [
		'stop_order_id' => 'int',
		'notice_date' => 'datetime'
	];

	protected $fillable = [
		'stop_order_id',
		'notice_date'
	];

	public function stop_order()
	{
		return $this->belongsTo(StopOrder::class);
	}
}
