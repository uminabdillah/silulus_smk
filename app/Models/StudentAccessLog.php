<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAccessLog extends Model
{
    protected $fillable = [
        'student_id',
        'ip_address',
        'user_agent',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
