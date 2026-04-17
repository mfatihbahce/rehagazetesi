<?php

namespace App\Http\Requests;

use App\Models\Advertisement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdvertisementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'placement' => ['required', Rule::in(array_keys(Advertisement::placements()))],
            'type' => ['required', Rule::in(['image', 'html'])],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096', 'required_if:type,image'],
            'mobile_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096', 'required_if:type,image'],
            'target_url' => ['nullable', 'url', 'max:2048'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'html_code' => ['nullable', 'string', 'required_if:type,html'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }
}
