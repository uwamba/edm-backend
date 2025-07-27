<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'manager_id',
        'job_title_id', 
    ];

    // Relationship: user belongs to a company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Relationship: user’s direct manager
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Relationship: all subordinates of this user
    public function subordinates()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    // Get all managers up the hierarchy
    public function getApprovalChain()
    {
        $chain = [];
        $current = $this->manager;

        while ($current) {
            $chain[] = $current;
            $current = $current->manager;
        }

        return $chain;
    }
    // app/Models/User.php

public function jobTitle()
{
    return $this->belongsTo(JobTitle::class);
}

}
