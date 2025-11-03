<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StopOrder
 * 
 * @property int $id
 * @property string|null $remarks
 * @property int $district_id
 * @property string|null $location
 * @property string $name
 * @property string|null $plot_number
 * @property string|null $phone_number
 * @property string|null $description
 * @property string $stage_of_construction
 * @property string|null $observation_notes
 * @property string|null $inspection_officer
 * @property string|null $supervisor
 * @property Carbon|null $inspection_date
 * @property string $zoning
 * @property string|null $picture
 * @property Carbon|null $response_date
 * @property string|null $responded
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property District $district
 * @property Collection|Enforcement[] $enforcements
 * @property Collection|Warning[] $warnings
 *
 * @package App\Models
 */
class StopOrder extends Model
{
	protected $table = 'stop_orders';

	

	protected $fillable = [
		"REMARKS", "District", "Location","Photo","Time", "Name", "Plot_No", "Phone_No", "Description_of__Development", "Stage_of__Construction", "Observation_Notes", "Inspection__Officer", "Supervisor", "Date", "Zoning", "Picture", "Response_Date", "Responded"
	];

	public function district()
	{
		return $this->belongsTo(District::class);
	}

	public function enforcements()
	{
		return $this->hasMany(Enforcement::class);
	}

	public function warnings()
	{
		return $this->hasMany(Warning::class);
	}
}
