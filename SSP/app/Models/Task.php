<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'student_id', 'title', 'start_date', 'due_date', 'status',];
    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function student()
{
    return $this->belongsTo(Student::class, 'student_id', 'student_id');
}

    
}
