<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GallerySetting extends Model
{
    protected $fillable = [
        'layout','columns','card_style','aspect_ratio',
        'show_title','show_description','show_overlay','autoplay',
        'section_title','section_subtitle',
    ];

    protected $casts = [
        'show_title' => 'boolean',
        'show_description' => 'boolean',
        'show_overlay' => 'boolean',
        'autoplay' => 'boolean',
    ];
}
