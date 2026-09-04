<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;

class CompanyProfileController extends Controller
{
    public function index()
    {
        $company = CompanyInfo::query()->first() ?? new CompanyInfo();
        return view('profile', compact('company'));
    }
}
