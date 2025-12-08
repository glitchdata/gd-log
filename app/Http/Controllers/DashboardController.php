<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'user' => Auth::user(),
            'licenses' => License::with(['product', 'domains'])
                ->where('user_id', Auth::id())
                ->orderBy('expires_at')
                ->get(),
            'products' => Product::orderBy('name')->get(),
            'paypalClientId' => config('paypal.client_id'),
            'paypalCurrency' => config('paypal.currency', 'USD'),
            'stripePublicKey' => config('stripe.public_key'),
            'stripeCurrency' => config('stripe.currency', 'USD'),
            'paypalEnabled' => (bool) (config('payment.providers.paypal.enabled') && config('paypal.client_id')),
            'stripeEnabled' => (bool) (config('payment.providers.stripe.enabled') && config('stripe.public_key') && config('stripe.secret')),
        ]);
    }
}
