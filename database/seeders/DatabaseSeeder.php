<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
 public function run(): void {
   User::updateOrCreate(['phone'=>env('ADMIN_PHONE','01700000000')],['name'=>'Administrator','password'=>Hash::make('password'),'is_admin'=>true,'status'=>'active']);
   Company::firstOrCreate(['company_id'=>1],['company_name'=>'Optimum Biomedical','email'=>'optimumbiomedical.50@gmail.com','mobile_number'=>'01862252456','office_address'=>'Khilkhet, Dhaka','status'=>true]);
   $this->call([RbacSeeder::class, SettingsSeeder::class]);
 }
}
