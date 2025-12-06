<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseLicenseRequest;
use App\Models\License;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserLicenseController extends Controller
{
    public function show(License $license): View
    {
        abort_unless($license->user_id === Auth::id(), 404);

        return view('licenses.show', [
            'license' => $license->load(['product', 'user']),
        ]);
    }

    public function store(PurchaseLicenseRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $license = License::create([
            'product_id' => $data['product_id'],
            'user_id' => $request->user()->id,
            'seats_total' => $data['seats_total'],
            'seats_used' => 0,
            'expires_at' => now()->addYear(),
        ]);

        return redirect()
            ->route('licenses.show', $license)
            ->with('status', 'License purchased successfully.');
    }
}
