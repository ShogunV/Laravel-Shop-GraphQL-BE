<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'=> ['required', 'string', 'between:3,255', Rule::unique('products', 'title')->ignore($this->product)],
            'description' => 'nullable|string',
            'category_id'=>'required|numeric',
            'price'=>'required|numeric',
            'discount' => 'nullable|numeric|between:0,100',
            'image' => 'sometimes|nullable|image|max:3072'
        ];
    }
}
