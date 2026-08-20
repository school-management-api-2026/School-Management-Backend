<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentParents extends Model
{
    use HasFactory;
    protected $fillable = [
        'relation',
        'is_primary',
        'student_id',
        'parent_id',
    ];

    public function student() {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function parent() {
        return $this->belongsTo(Parents::class, 'parent_id');
    }
}
