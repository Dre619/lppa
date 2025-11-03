<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ConstructionStage
 * 
 * @property int $id
 * @property string $stage_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class ConstructionStage extends Model
{
	protected $table = 'construction_stages';

	protected $fillable = [
		'stage_name'
	];
}
