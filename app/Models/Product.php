<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'product_code',
        'vendor',
        'category',
        'description',
    ];

    public function licenses()
    {
        return $this->hasMany(License::class);
    }
}
