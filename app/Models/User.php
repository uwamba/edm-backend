<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'job_title',
        'manager_id', // NEW: user’s direct manager
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
}
