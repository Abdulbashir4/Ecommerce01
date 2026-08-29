<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LayoutSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('settings.layout.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'product_layout' => ['required', Rule::in(['grid', 'list'])],
            'mobile_columns' => ['required', 'integer', Rule::in([1, 2])],
            'tablet_columns' => ['required', 'integer', Rule::in([2, 3, 4])],
            'desktop_columns' => ['required', 'integer', Rule::in([3, 4, 5, 6])],
            'gap' => ['required', 'integer', Rule::in([3, 4, 5, 6, 8])],
            'text_align' => ['required', Rule::in(['left', 'center'])],
            'sidebar' => ['required', Rule::in(['left', 'right', 'none'])],
            'container' => ['required', Rule::in(['5xl', '6xl', '7xl', 'full'])],
            'section_spacing' => ['required', Rule::in(['compact', 'normal', 'large'])],
        ];
    }
}
