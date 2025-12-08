<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(): View
    {
        return view('shop.index', [
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function show(Product $product): View
    {
        return view('shop.show', [
            'product' => $product,
            'paypalClientId' => config('paypal.client_id'),
            'paypalCurrency' => config('paypal.currency', 'USD'),
            'stripePublicKey' => config('stripe.public_key'),
            'stripeCurrency' => config('stripe.currency', 'USD'),
            'paypalEnabled' => (bool) (config('payment.providers.paypal.enabled') && config('paypal.client_id')),
            'stripeEnabled' => (bool) (config('payment.providers.stripe.enabled') && config('stripe.public_key') && config('stripe.secret')),
        ]);
    }
}
