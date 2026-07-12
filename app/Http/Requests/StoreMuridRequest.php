<?php

namespace App\Http\Requests;

use App\Models\Murid;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMuridRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:150'],
            // Terima format umum nomor HP Indonesia: 08xx, 62xx, dengan/tanpa strip
            'whatsapp' => ['required', 'regex:/^(\+?62|0)8[0-9]{8,12}$/'],
            'level_belajar' => ['required', 'string', 'in:' . implode(',', Murid::LEVEL_OPTIONS)],
            'paket' => ['required', 'string', 'in:' . implode(',', Murid::PAKET_OPTIONS)],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.min' => 'Nama terlalu pendek.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Format nomor WhatsApp tidak valid. Gunakan format 08xx atau 62xx.',
            'level_belajar.required' => 'Level belajar wajib dipilih.',
            'level_belajar.in' => 'Level belajar tidak valid.',
            'paket.required' => 'Paket wajib dipilih.',
            'paket.in' => 'Paket tidak valid.',
        ];
    }

    /**
     * Pastikan response tetap JSON walau request AJAX dianggap "expectsJson"
     * secara default oleh Laravel — override supaya format errornya konsisten.
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
