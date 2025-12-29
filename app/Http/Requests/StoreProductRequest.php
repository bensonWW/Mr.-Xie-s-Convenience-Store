<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'original_price' => 'nullable|integer|min:0',
            'category' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'information' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
            'store_id' => 'nullable|integer|exists:stores,id',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ];
    }
}
