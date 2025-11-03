<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ApplicantTitle
 * 
 * @property int $id
 * @property string $title
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|ApplicationApplicant[] $application_applicants
 *
 * @package App\Models
 */
class ApplicantTitle extends Model
{
	protected $table = 'applicant_titles';

	protected $fillable = [
		'title'
	];

	public function application_applicants()
	{
		return $this->hasMany(ApplicationApplicant::class);
	}
}
