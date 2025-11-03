<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ApplicationClassification
 * 
 * @property int $id
 * @property string $classification
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|ApplicationSubmission[] $application_submissions
 *
 * @package App\Models
 */
class ApplicationClassification extends Model
{
	protected $table = 'application_classifications';

	protected $fillable = [
		'classification'
	];

	public function application_submissions()
	{
		return $this->hasMany(ApplicationSubmission::class);
	}
}
