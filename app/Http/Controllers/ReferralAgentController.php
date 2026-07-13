<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReferralAgentRequest;
use App\Http\Requests\UpdateReferralAgentRequest;
use App\Models\ReferralAgent;
use App\Services\ReferralAgentService;
use Illuminate\View\View;

class ReferralAgentController extends Controller
{
    public function index(ReferralAgentService $referralAgentService): View
    {
        $agents = ReferralAgent::withCount('murid')->latest()->get();

        return view('admin.referral-agent', [
            'agents' => $agents,
            'referralAgentService' => $referralAgentService,
        ]);
    }

    public function store(StoreReferralAgentRequest $request, ReferralAgentService $referralAgentService)
    {
        $referralAgentService->createAgent($request->validated());

        return redirect()
            ->route('admin.referral-agent.index')
            ->with('success', 'Referral Agent berhasil ditambahkan.');
    }

    public function update(UpdateReferralAgentRequest $request, ReferralAgent $referralAgent)
    {
        $referralAgent->update($request->validated());

        return redirect()
            ->route('admin.referral-agent.index')
            ->with('success', 'Referral Agent berhasil diperbarui.');
    }

    public function toggleStatus(ReferralAgent $referralAgent, ReferralAgentService $referralAgentService)
    {
        $referralAgentService->toggleStatus($referralAgent);

        return redirect()
            ->route('admin.referral-agent.index')
            ->with('success', 'Status Referral Agent berhasil diubah.');
    }
}
