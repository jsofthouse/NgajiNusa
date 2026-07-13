<?php

namespace App\Http\Requests;

use App\Models\ReferralAgent;
use Illuminate\Foundation\Http\FormRequest;

class StoreReferralAgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:150', 'unique:referral_agents,email'],
            // Format sama seperti nomor WA murid: 08xx / 62xx
            'whatsapp' => ['required', 'regex:/^(\+?62|0)8[0-9]{8,12}$/', 'unique:referral_agents,whatsapp'],
            'status' => ['required', 'string', 'in:' . implode(',', ReferralAgent::STATUS_OPTIONS)],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama agent wajib diisi.',
            'nama.min' => 'Nama terlalu pendek.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah dipakai agent lain.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Format nomor WhatsApp tidak valid. Gunakan format 08xx atau 62xx.',
            'whatsapp.unique' => 'Nomor WhatsApp ini sudah dipakai agent lain.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ];
    }
}
