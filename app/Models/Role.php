<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Traits\HasRoles;

class Role extends SpatieRole
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = ['name', 'level', 'created_at'];

    public function users(): BelongsToMany
    {
        return $this->hasMany(User::class);
    }

}