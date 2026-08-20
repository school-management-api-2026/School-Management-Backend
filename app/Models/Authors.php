<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authors extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'date_of_birth',
        'gender',
        'nation',
    ];

    public function authors() {
        return $this->hasMany(Authors::class, 'author_id');
    }
}
