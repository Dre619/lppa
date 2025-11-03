<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ApplicationSubmission
 * 
 * @property int $id
 * @property int $application_id
 * @property int $application_classification_id
 * @property string|null $application_text
 * @property Carbon|null $submission_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property ApplicationClassification $application_classification
 * @property Application $application
 *
 * @package App\Models
 */
class ApplicationSubmission extends Model
{
	protected $table = 'application_submissions';

	protected $casts = [
		'application_id' => 'int',
		'application_classification_id' => 'int',
		'submission_date' => 'datetime'
	];

	protected $fillable = [
		'application_id',
		'application_classification_id',
		'application_text',
		'submission_date'
	];

	public function application_classification()
	{
		return $this->belongsTo(RegistrationType::class);
	}

	public function application()
	{
		return $this->belongsTo(Application::class);
	}
}
