<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyTransaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Ukuran max 2MB (2048 KB) — belum ada standar file upload lain di project ini,
            // dipilih wajar utk foto bukti transfer.
            'bukti_transfer' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'catatan_internal' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'bukti_transfer.required' => 'Bukti transfer wajib diunggah.',
            'bukti_transfer.image' => 'File harus berupa gambar.',
            'bukti_transfer.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'bukti_transfer.max' => 'Ukuran gambar maksimal 2MB.',
            'catatan_internal.max' => 'Catatan maksimal 2000 karakter.',
        ];
    }

    /**
     * Request ini selalu AJAX (fetch FormData) — error selalu JSON 422,
     * pola sama seperti StoreAdminMuridRequest.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
