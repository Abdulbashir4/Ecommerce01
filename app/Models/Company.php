<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Company extends Model { protected $primaryKey='company_id'; protected $fillable=['company_name','logo','about_us','mobile_number','hotline_number','whatsapp_number','email','facebook_page','youtube_channel','office_address','google_map_location','support_hours','privacy_policy','terms_conditions','refund_policy','shipping_policy','average_rating','total_reviews','status']; }
