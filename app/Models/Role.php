<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * 
 * @property int $id
 * @property string $name
 * @property string $role_slug
 * @property string|null $description
 * @property bool $is_active
 * @property bool $is_default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Role extends Model
{
	protected $table = 'roles';

	protected $casts = [
		'is_active' => 'bool',
		'is_default' => 'bool'
	];

	protected $fillable = [
		'name',
		'role_slug',
		'description',
		'is_active',
		'is_default'
	];

	public function users()
	{
		return $this->hasMany(User::class);
	}
}
