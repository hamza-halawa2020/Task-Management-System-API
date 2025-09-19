<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Get the users that have this role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if this is the manager role.
     */
    public function isManager()
    {
        return $this->slug === 'manager';
    }

    /**
     * Check if this is the user role.
     */
    public function isUser()
    {
        return $this->slug === 'user';
    }
}
