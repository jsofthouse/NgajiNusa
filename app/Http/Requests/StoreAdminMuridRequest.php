<?php

namespace App\Http\Requests;

use App\Models\Murid;
use App\Services\MuridService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAdminMuridRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalisasi whatsapp dulu sebelum divalidasi, biar pengecekan unique
     * dibandingkan dengan format yang sama seperti yang tersimpan di DB (62xxxx).
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('whatsapp')) {
            $this->merge([
                'whatsapp' => app(MuridService::class)->normalizeWhatsapp((string) $this->input('whatsapp')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:150', 'unique:murid,email'],
            'whatsapp' => ['required', 'regex:/^(\+?62|0)8[0-9]{8,12}$/', 'unique:murid,whatsapp'],
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
            'email.unique' => 'Email ini sudah dipakai murid lain.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Format nomor WhatsApp tidak valid. Gunakan format 08xx atau 62xx.',
            'whatsapp.unique' => 'Nomor WhatsApp ini sudah dipakai murid lain.',
            'level_belajar.required' => 'Level belajar wajib dipilih.',
            'level_belajar.in' => 'Level belajar tidak valid.',
            'paket.required' => 'Paket wajib dipilih.',
            'paket.in' => 'Paket tidak valid.',
        ];
    }

    /**
     * Semua request modul admin Murid ini AJAX (fetch) — pastikan error selalu JSON 422,
     * sama seperti pola StoreMuridRequest di flow pendaftaran publik.
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
