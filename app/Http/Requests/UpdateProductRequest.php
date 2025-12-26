<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'sku' => [
                'sometimes',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('products')
                    ->where(function ($query) {
                        $categoryId = $this->input('category_id') ?? $this->route('product')->category_id;
                        $query->where('category_id', $categoryId);
                    })
                    ->ignore($this->route('product')->id)
            ],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['integer', 'min:0'],
            'min_stock' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
