<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RegistrationType
 * 
 * @property int $id
 * @property string $name
 * @property string|null $reg_key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class RegistrationType extends Model
{
	protected $table = 'registration_types';

	protected $fillable = [
		'name',
		'reg_key'
	];
}
