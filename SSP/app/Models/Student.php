<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'student_id', 
        'matric_number', 
        'name', 
        'email', 
        'department', 
        'contact_number'
    ];

    /**
     * Get the user that owns the student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }


    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'student_id');
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }
    public function projects()
    {
        return $this->hasOne(Project::class);
    }

    public function projectss()
    {
        return $this->belongsToMany(Project::class, 'project_students', 'student_id', 'project_id');
    }
    public function tasks()
{
    return $this->hasMany(Task::class, 'student_id');
}

    
}