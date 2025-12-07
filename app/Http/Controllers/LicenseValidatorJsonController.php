<?php

namespace App\Http\Controllers;

use App\Models\License;
use Illuminate\Http\JsonResponse;

class LicenseValidatorJsonController extends Controller
{
    public function __invoke(string $key): JsonResponse
    {
        $license = License::with('product')->where('identifier', $key)->first();

        if (! $license) {
            return response()->json([
                'valid' => false,
                'reason' => 'License not found.',
            ], 404);
        }

        $isExpired = $license->expires_at && $license->expires_at->isPast();
        $valid = ! $isExpired;

        return response()->json([
            'valid' => $valid,
            'reason' => $valid ? null : ($isExpired ? 'License expired.' : 'License invalid.'),
            'expires_at' => optional($license->expires_at)->toDateString(),
            'license' => [
                'id' => $license->id,
                'seats_total' => $license->seats_total,
                'inspect_uri' => $license->inspect_uri,
                'public_validator_uri' => $license->public_validator_uri,
            ],
            'product' => [
                'id' => $license->product->id,
                'name' => $license->product->name,
                'product_code' => $license->product->product_code,
                'vendor' => $license->product->vendor,
                'category' => $license->product->category,
            ],
        ]);
    }
}
