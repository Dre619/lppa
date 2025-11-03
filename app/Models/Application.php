<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Application
 *
 * @property int $id
 * @property string $registration_type
 * @property int|null $registration_organization_id
 * @property int|null $registration_area_id
 * @property string $registration_number
 * @property string|null $registration_sub_number
 * @property string $application_id
 * @property bool $is_institution
 * @property string|null $institution_name
 * @property string $parcel_id
 * @property string|null $clean_parcel_id
 * @property string|null $affected_area
 * @property string|null $sub_plot_number
 * @property int|null $district_id
 * @property int|null $development_area_id
 * @property string|null $development_sub_area
 * @property string|null $local_area
 * @property int $print_order
 * @property string|null $current_use_of_land
 * @property int|null $global_key
 * @property string $application_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property DevelopmentArea|null $development_area
 * @property District|null $district
 * @property RegistrationArea|null $registration_area
 * @property RegistrationOrganization|null $registration_organization
 * @property Collection|ApplicationApplicant[] $application_applicants
 * @property Collection|ApplicationNote[] $application_notes
 * @property Collection|Resolution[] $resolutions
 * @property Collection|ApplicationSubmission[] $application_submissions
 * @property Collection|PrintHistory[] $print_histories
 *
 * @package App\Models
 */
class Application extends Model
{
	protected $table = 'applications';

	protected $casts = [
		'registration_organization_id' => 'int',
		'registration_area_id' => 'int',
		'is_institution' => 'bool',
		'district_id' => 'int',
		'development_area_id' => 'int',
		'print_order' => 'int',
		'global_key' => 'int'
	];

	protected $fillable = [
			'change_of_use_stage_id',
            'registration_organization_id',
            'application_classification_id',
            'registration_area_id',
            'registration_number',
            'registration_sub_number',
            'application_id',
            'is_institution',
            'institution_name',
            'parcel_id',
            'clean_parcel_id',
            'affected_area',
            'sub_plot_number',
            'district_id',
            'development_area_id',
            'development_sub_area',
            'local_area',
            'print_order',
            'current_use_of_land_id',
            'change_land_use_from',
            'change_land_use_to',
			'application_status',
			'application_date'
	];

	public function development_area()
	{
		return $this->belongsTo(DevelopmentArea::class);
	}


	public function change_of_land_use()
	{
		return $this->belongsTo(ChangeUseStage::class,'change_of_use_stage_id','id');
	}

	public function landUse()
	{
		return $this->belongsTo(LandUs::class,'current_use_of_land_id','id');
	}

    public function formLandUse()
    {
        return $this->belongsTo(LandUs::class, 'change_land_use_form','id');
    }

    public function toLandUse()
    {
        return $this->belongsTo(LandUs::class,'change_land_use_to','id');
    }

	public function district()
	{
		return $this->belongsTo(District::class);
	}

	public function registration_area()
	{
		return $this->belongsTo(RegistrationArea::class);
	}

	public function registration_organization()
	{
		return $this->belongsTo(RegistrationOrganization::class);
	}

	public function applicationApplicants()
	{
		return $this->hasMany(ApplicationApplicant::class);
	}

	public function applicationResolutions()
	{
		return $this->hasMany(ApplicationResolution::class,'application_id','id');
	}

	public function application_notes()
	{
		return $this->hasMany(ApplicationNote::class);
	}

	public function subArea()
	{
		return $this->belongsTo(SubArea::class,'sub_area_id','id');
	}

	public function resolutions()
	{
		return $this->belongsToMany(Resolution::class, 'application_resolutions')
					->withPivot('id', 'resolution_details', 'resolution_date', 'sequence')
					->withTimestamps();
	}

	public function applicationClassification()
	{
		return $this->belongsTo(RegistrationType::class,'application_classification_id','id');
	}

	public function reg_key()
	{
		return $this->applicationClassification->reg_key;
	}

	public function applicationSubmissions()
	{
		return $this->hasMany(ApplicationSubmission::class);
	}

	public function print_histories()
	{
		return $this->hasMany(PrintHistory::class);
	}
}
