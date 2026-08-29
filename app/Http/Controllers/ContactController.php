<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $company = CompanyInfo::query()->first() ?? new CompanyInfo();
        return view('contact.index', compact('company'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:190'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        ContactMessage::create($data);
        return back()->with('success', 'Thank you. Your message has been sent successfully.');
    }
}
