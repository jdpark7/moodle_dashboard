<?php

namespace Modules\MoodleDash\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'lastname',
        'firstname',
        'email',
        'password',
        'phone_number',
        'address',
        'role',
        'moodle_token',
        'userpictureurl',
        'lastaccess',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Getter for Full Name
     */
    public function getFullnameAttribute()
    {
        return $this->lastname . $this->firstname;
    }

    /**
     * Relationship with Enrolled Courses
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'user_id', 'course_id')
                    ->withPivot('id', 'progress', 'feedback')
                    ->withTimestamps();
    }

    /**
     * Relationship with Submissions
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'user_id');
    }
}
