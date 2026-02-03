<?php

namespace App\Models;

use App\Enums\User\UserCharge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DevelopmentRequest extends Model
{
    //
    protected $table = 'development_requests';

    protected $fillable = [
        'title',
        'priority',
        'status',
        'position',
        'description',
        'impact',
        // 'devs_needed',
        'estimated_hours',
        'estimated_end_date',
        'project_url',
        'completed_at',
        'actual_hours',
        'area_id',
        'requested_by_id',
        'requirement_path',
    ];

    // protected $casts = [
    //     'estimated_end_date' => 'date',
    // ];

    protected $appends = [
        'requirement_url',
    ];


    public function getRequirementUrlAttribute()
    {
        if ($this->requirement_path) {
            return Storage::disk('public')->url($this->requirement_path);
        }
        return null;
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id_area');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_id', 'staff_id');
    }

    public function approvals()
    {
        return $this->hasMany(DevelopmentApproval::class, 'development_request_id');
    }

    public function technicalApproval()
    {
        return $this->hasOne(DevelopmentApproval::class, 'development_request_id')
            ->whereHas('approvedBy', function ($query) {
                $query->where('id_cargo', UserCharge::TI_MANAGER->value);
            });
    }

    public function strategicApproval()
    {
        return $this->hasOne(DevelopmentApproval::class, 'development_request_id')
            ->whereHas('approvedBy', function ($query) {
                $query->where('id_cargo', UserCharge::TI_ASSISTANT_MANAGER->value);
            });
    }

    public function developmentProgress()
    {
        return $this->hasMany(DevelopmentProgress::class, 'development_request_id');
    }


    public function firstProgress()
    {
        return $this->hasOne(DevelopmentProgress::class, 'development_request_id')->oldestOfMany();
    }

    public function latestProgress()
    {
        return $this->hasOne(DevelopmentProgress::class, 'development_request_id')->latestOfMany();
    }


    public function developers()
    {
        return $this->belongsToMany(
            User::class,
            'development_request_developers',
            'development_request_id',
            'developer_id'
        )
     ->withTimestamps();
    }

}
