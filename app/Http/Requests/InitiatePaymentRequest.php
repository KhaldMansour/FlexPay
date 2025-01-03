<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="InitiatePaymentRequest", 
 *     type="object", 
 *     required={"gateway", "product_name", "amount", "quantity", "currency"},
 *     @OA\Property(property="gateway", type="string", example="stripe"),
 *     @OA\Property(property="product_name", type="string", example="hello"),
 *     @OA\Property(property="amount", type="number", format="float", example=150),
 *     @OA\Property(property="quantity", type="integer", example=1),
 *     @OA\Property(property="currency", type="string", example="usd")
 * )
 */
class InitiatePaymentRequest extends FormRequest
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
            'gateway' => 'required|string',
            'product_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1', 
            'quantity' => 'required|integer|min:1', 
            'currency' => 'required|string|max:3',
        ];
    }
}
