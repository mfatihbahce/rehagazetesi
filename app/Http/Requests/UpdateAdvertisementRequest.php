<?php

namespace App\Http\Requests;

use App\Models\Advertisement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdvertisementRequest extends FormRequest
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
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'mobile_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'target_url' => ['nullable', 'url', 'max:2048'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'html_code' => ['nullable', 'string', 'required_if:type,html'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $advertisement = $this->route('advertisement');
            $requiresImage = $this->input('type') === 'image';
            $hasExistingImage = (bool) ($advertisement?->image_url ?? false);
            $hasExistingMobileImage = (bool) ($advertisement?->mobile_image_url ?? false);

            if ($requiresImage && !$this->hasFile('image_file') && !$hasExistingImage) {
                $validator->errors()->add('image_file', 'Görsel reklam için bir görsel yüklemelisiniz.');
            }

            if ($requiresImage && !$this->hasFile('mobile_image_file') && !$hasExistingMobileImage) {
                $validator->errors()->add('mobile_image_file', 'Mobil görünüm için bir görsel yüklemelisiniz.');
            }
        });
    }
}
