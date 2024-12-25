<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="VerifyPaymentRequest", 
 *     type="object", 
 *     required={"gateway", "paymentId"},
 *     @OA\Property(property="gateway", type="string", example="stripe"),
 *     @OA\Property(property="paymentId", type="string", example="pi_3QYXmSHWKbP0IZxM1Syds395")
 * )
 */
class VerifyPaymentRequest extends FormRequest
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
            'paymentId' => 'required|string',
        ];
    }
}
