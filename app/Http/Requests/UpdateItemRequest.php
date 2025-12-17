<?php

namespace App\Http\Requests;

use App\Enums\ItemStatus;
use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Item|null $item */
        $item = $this->route('item');

        return $item !== null && $this->user()?->can('update', $item) === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Item $item */
        $item = $this->route('item');

        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:10'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'tag_ids' => ['nullable', 'array', 'max:10'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'status' => ['required', new Enum(ItemStatus::class)],

            'photos' => ['nullable', 'array', 'max:8'],
            'photos.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'delete_photo_ids' => ['nullable', 'array', 'max:20'],
            'delete_photo_ids.*' => [
                'integer',
                Rule::exists('item_photos', 'id')->where('item_id', $item->id),
            ],
        ];
    }
}
