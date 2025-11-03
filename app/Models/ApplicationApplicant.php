<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ApplicationApplicant
 * 
 * @property int $id
 * @property int $application_id
 * @property int $applicant_title_id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $middle_name
 * @property int $applicant_type_id
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property string|null $nrc_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property ApplicantTitle $applicant_title
 * @property ApplicantType $applicant_type
 * @property Application $application
 *
 * @package App\Models
 */
class ApplicationApplicant extends Model
{
	protected $table = 'application_applicants';

	protected $casts = [
		'application_id' => 'int',
		'applicant_title_id' => 'int',
		'applicant_type_id' => 'int'
	];

	protected $fillable = [
		'application_id',
		'applicant_title_id',
		'first_name',
		'last_name',
		'middle_name',
		'applicant_type_id',
		'phone',
		'email',
		'address',
		'nrc_number'
	];

	public function applicant_title()
	{
		return $this->belongsTo(ApplicantTitle::class);
	}

	public function applicant_type()
	{
		return $this->belongsTo(ApplicantType::class);
	}

	public function application()
	{
		return $this->belongsTo(Application::class);
	}
}
