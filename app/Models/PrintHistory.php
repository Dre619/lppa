<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PrintHistory
 * 
 * @property int $id
 * @property int $application_id
 * @property string $print_type
 * @property int $printed_by
 * @property Carbon $printed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Application $application
 * @property User $user
 *
 * @package App\Models
 */
class PrintHistory extends Model
{
	protected $table = 'print_histories';

	protected $casts = [
		'application_id' => 'int',
		'printed_by' => 'int',
		'printed_at' => 'datetime'
	];

	protected $fillable = [
		'application_id',
		'print_type',
		'printed_by',
		'printed_at'
	];

	public function application()
	{
		return $this->belongsTo(Application::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'printed_by');
	}
}
