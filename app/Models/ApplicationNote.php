<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ApplicationNote
 * 
 * @property int $id
 * @property int $application_id
 * @property string $note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Application $application
 *
 * @package App\Models
 */
class ApplicationNote extends Model
{
	protected $table = 'application_notes';

	protected $casts = [
		'application_id' => 'int'
	];

	protected $fillable = [
		'application_id',
		'note'
	];

	public function application()
	{
		return $this->belongsTo(Application::class);
	}
}
