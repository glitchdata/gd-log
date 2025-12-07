@extends('layouts.app')

@section('title', 'Shop · ' . $product->name)

@section('content')
<header class="hero">
    <div>
        <p class="eyebrow">Shop</p>
        <h1>{{ $product->name }}</h1>
        <p class="lead">{{ $product->description ?: 'No marketing copy available yet.' }}</p>
    </div>
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;">
        <a class="link" href="{{ route('shop') }}">&larr; Back to catalog</a>
        <a class="link" style="font-weight:600;" href="{{ route('login') }}">Purchase in dashboard →</a>
    </div>
</header>

<section class="card" style="display:grid;gap:1.5rem;">
    <div style="display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-end;">
        <span style="font-size:3rem;font-weight:700;">${{ number_format($product->price, 2) }}<span style="font-size:1rem;font-weight:500;color:var(--muted);">/seat</span></span>
        <span style="font-weight:600;color:var(--muted);">{{ $product->duration_months }}-month term</span>
        <span style="font-family:monospace;color:var(--muted);">Code: {{ $product->product_code }}</span>
    </div>
    <dl class="details">
        <div>
            <dt>Vendor</dt>
            <dd>{{ $product->vendor ?? '—' }}</dd>
        </div>
        <div>
            <dt>Category</dt>
            <dd>{{ $product->category ?? '—' }}</dd>
        </div>
    </dl>
    <div style="display:flex;flex-wrap:wrap;gap:0.75rem;">
        <a class="link" style="font-weight:600;" href="{{ route('register') }}">Need an account? Sign up →</a>
        <a class="link" href="{{ route('api.lab') }}">Validate via API Lab</a>
    </div>
</section>

@auth
    <section class="card" style="margin-top:1.5rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
            <div>
                <p class="eyebrow" style="margin-bottom:0.35rem;">Purchase</p>
                <h2 style="margin:0;">Buy seats for {{ $product->name }}</h2>
            </div>
            <span style="font-size:0.9rem;color:var(--muted);">Charged in dashboard currency (USD)</span>
        </div>

        <form method="POST" action="{{ route('licenses.store') }}" style="display:grid;gap:1rem;margin-top:1.25rem;" id="shop-purchase-form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="seats_total" id="shop-seats-input" value="1">
            <label>
                <span>Primary domain (optional)</span>
                <input type="text" name="domain" placeholder="acme.com" value="{{ old('domain') }}">
            </label>
            <div style="padding:0.75rem 1rem;background:var(--bg);border-radius:0.75rem;font-weight:600;display:flex;justify-content:space-between;align-items:center;">
                <span>
                    Estimated total
                    <small style="display:block;font-weight:400;color:var(--muted);">Renews every {{ $product->duration_months }} months</small>
                </span>
                <span id="shop-purchase-total">$0.00</span>
            </div>
            <input type="hidden" name="paypal_order_id" id="shop-paypal-order">
            <p style="margin:0;color:var(--muted);font-size:0.95rem;">Checkout is powered by PayPal. Each purchase provides one license seat—approve the popup to finish.</p>
            <div id="paypal-buttons-shop"></div>
            <p id="paypal-errors-shop" style="display:none;color:var(--error);font-weight:600;"></p>
            @error('payment')
                <p style="color:var(--error);font-weight:600;">{{ $message }}</p>
            @enderror
            @if (! config('paypal.client_id'))
                <p style="color:var(--error);font-weight:600;">Set PAYPAL_CLIENT_ID to enable purchase flow.</p>
            @endif
        </form>
    </section>
@else
    <section class="card" style="margin-top:1.5rem;">
        <h2 style="margin-top:0;">Sign in to purchase</h2>
        <p style="color:var(--muted);">Create an account or log in to buy seats for this product from your dashboard.</p>
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
            <a class="link" style="font-weight:600;" href="{{ route('login') }}">Log in →</a>
            <a class="link" href="{{ route('register') }}">Register</a>
        </div>
    </section>
@endauth
@endsection

@auth
    @push('scripts')
        @if (config('paypal.client_id'))
            <script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.client_id') }}&currency={{ config('paypal.currency') }}" data-sdk-integration-source="button-factory"></script>
        @endif
        <script>
        (function () {
            const seatsInput = document.getElementById('shop-seats-input');
            const totalEl = document.getElementById('shop-purchase-total');
            const paypalErrors = document.getElementById('paypal-errors-shop');
            const paypalOrderInput = document.getElementById('shop-paypal-order');
            const form = document.getElementById('shop-purchase-form');
            const domainInput = form ? form.querySelector('input[name="domain"]') : null;
            const pricePerSeat = {{ number_format($product->price, 2, '.', '') }};

            const update = () => {
                if (!totalEl) {
                    return;
                }
                totalEl.textContent = pricePerSeat > 0 ? `$${pricePerSeat.toFixed(2)}` : '$0.00';
                if (paypalOrderInput) {
                    paypalOrderInput.value = '';
                }
                if (form) {
                    form.dataset.paypalReady = 'false';
                }
            };

            update();

            if (form) {
                form.dataset.paypalReady = 'false';
                form.addEventListener('submit', (event) => {
                    if (form.dataset.paypalReady !== 'true') {
                        event.preventDefault();
                    }
                });
            }

            const showError = (message) => {
                if (!paypalErrors) {
                    return;
                }
                paypalErrors.textContent = message;
                paypalErrors.style.display = 'block';
            };

            const clearError = () => {
                if (paypalErrors) {
                    paypalErrors.style.display = 'none';
                }
            };

            if (window.paypal && document.getElementById('paypal-buttons-shop')) {
                paypal.Buttons({
                    style: {
                        layout: 'vertical',
                        color: 'gold',
                        shape: 'rect',
                    },
                    createOrder: async () => {
                        clearError();
                        if (!form) {
                            throw new Error('Form not ready.');
                        }
                        const payload = {
                            product_id: form.querySelector('input[name="product_id"]').value,
                            seats_total: 1,
                            domain: domainInput ? domainInput.value : null,
                        };
                        const response = await fetch('{{ route('paypal.orders.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify(payload),
                        });
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Unable to create a PayPal order.');
                        }
                        paypalOrderInput.value = data.order_id;
                        return data.order_id;
                    },
                    onApprove: (data) => {
                        clearError();
                        if (paypalOrderInput) {
                            paypalOrderInput.value = data.orderID;
                        }
                        if (form) {
                            form.dataset.paypalReady = 'true';
                            form.submit();
                        }
                    },
                    onCancel: () => {
                        showError('PayPal checkout was cancelled.');
                        if (paypalOrderInput) {
                            paypalOrderInput.value = '';
                        }
                        if (form) {
                            form.dataset.paypalReady = 'false';
                        }
                    },
                    onError: (err) => {
                        showError(err && err.message ? err.message : 'PayPal reported an error.');
                        if (paypalOrderInput) {
                            paypalOrderInput.value = '';
                        }
                        if (form) {
                            form.dataset.paypalReady = 'false';
                        }
                    }
                }).render('#paypal-buttons-shop');
            } else if (!window.paypal && document.getElementById('paypal-buttons-shop')) {
                showError('PayPal SDK is not available.');
            }
        })();
        </script>
    @endpush
@endauth
