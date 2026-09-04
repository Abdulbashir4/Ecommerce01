<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class AdminContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $messages = ContactMessage::query()
            ->when(in_array($status, ['new', 'read', 'replied'], true), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin/settings/contact-messages/index', compact('messages', 'status'));
    }

    public function show(ContactMessage $contactMessage)
    {
        if ($contactMessage->status === 'new') $contactMessage->update(['status' => 'read']);
        return view('admin/settings/contact-messages/show', compact('contactMessage'));
    }

    public function update(Request $request, ContactMessage $contactMessage)
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,read,replied'],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);
        $contactMessage->update($data);
        return back()->with('success', 'Message updated successfully.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();
        return redirect()->route('admin.settings.contact-messages')->with('success', 'Message deleted successfully.');
    }
}
