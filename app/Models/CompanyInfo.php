<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    protected $table = 'company_info';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'company_name', 'slider_image', 'right_slider', 'phone', 'email',
        'address', 'logo', 'banner', 'facebook', 'youtube', 'gallery',
        'about_us', 'map_location',
    ];

    protected $casts = [
        'slider_image' => 'array',
        'right_slider' => 'array',
    ];
}
