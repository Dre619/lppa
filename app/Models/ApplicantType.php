<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ApplicantType
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|ApplicationApplicant[] $application_applicants
 *
 * @package App\Models
 */
class ApplicantType extends Model
{
	protected $table = 'applicant_types';

	protected $fillable = [
		'name'
	];

	public function application_applicants()
	{
		return $this->hasMany(ApplicationApplicant::class);
	}
}
