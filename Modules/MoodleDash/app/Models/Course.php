<?php

namespace Modules\MoodleDash\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'fullname',
        'shortname',
        'summary',
        'topics',
    ];

    /**
     * Relationship with Enrolled Users
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'user_id')
                    ->withPivot('id', 'progress', 'feedback')
                    ->withTimestamps();
    }

    /**
     * Relationship with Assignments
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'course_id');
    }
}
