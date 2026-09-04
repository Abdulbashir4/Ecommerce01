<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('settings.general.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:20'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'timezone' => ['required', 'timezone'],
            'date_format' => ['required', 'string', 'max:50'],
            'time_format' => ['required', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'default_country' => ['required', 'string', 'max:100'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'default_shipping_cost' => ['required', 'numeric', 'min:0'],
            'order_notification_email' => ['nullable', 'email', 'max:255'],
            'maintenance_message' => ['required', 'string', 'max:500'],
        ];
    }
}
