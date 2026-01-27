<?php

namespace App\Models;

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
        'description',
        'impact',
        'estimated_hours',
        'estimated_end_date',
        'area_id',
        'requested_by_id',
        'requirement_path',
    ];

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

    // Public function approvals()
    // {
    //     return $this->hasMany(DevelomentApproval::class, 'develoment_request_id');
    // }

}
