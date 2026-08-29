<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = ['title', 'description', 'image_path', 'sort_order', 'status'];

    protected $casts = ['status' => 'boolean'];
}
