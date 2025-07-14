<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'author',
        'description',
        'category',
        'status', 
        'cover',// 'available' or 'rented'
    ];

    // 📚 A book belongs to a user (owner)
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔗 A book can have many rents
    public function rents()
    {
        return $this->hasMany(Rent::class);
    }
}

