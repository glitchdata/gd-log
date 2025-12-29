@extends('layouts.app')

@section('content')
<header class="hero">
    <div>
        <p class="eyebrow">Certificate</p>
        <h1>TLS Certificate lookup</h1>
        <p class="lead">Fetch the peer TLS certificate for a host and display parsed details.</p>
    </div>

    <div style="margin-top:1rem;display:flex;justify-content:center;">
        <form id="cert-form" style="display:grid;gap:0.5rem;max-width:720px;width:100%;">
            @csrf
            <label>
                <span>Host (or IP)</span>
                <input type="text" name="host" placeholder="example.com" required>
            </label>
            <label>
                <span>Port (optional)</span>
                <input type="number" name="port" placeholder="443">
            </label>
            <div style="display:flex;gap:0.5rem;justify-content:flex-start;">
                <button type="submit">Lookup</button>
                @if(config('shop.enabled'))
                    <a class="link" href="{{ url('/shop') }}">Browse products</a>
                @endif
            </div>
        </form>
    </div>
</header>

<section class="card">
    <h2>Result</h2>
    <pre id="cert-result" style="white-space:pre-wrap;max-height:60vh;overflow:auto;padding:1rem;background:#0f172a;color:#e6eef8;border-radius:0.5rem;"></pre>
</section>

@push('scripts')
<script>
document.getElementById('cert-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    const form = e.target;
    const data = new FormData(form);
    const btn = form.querySelector('button[type=submit]');
    btn.disabled = true;
    const resultEl = document.getElementById('cert-result');
    resultEl.textContent = 'Looking up…';

    try {
        const token = document.querySelector('input[name=_token]').value;
        const res = await fetch('{{ url('/cert/lookup') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: data
        });

        if (!res.ok) {
            const err = await res.json().catch(()=>({error:res.statusText}));
            resultEl.textContent = err.error || JSON.stringify(err, null, 2);
        } else {
            const json = await res.json();
            resultEl.textContent = JSON.stringify(json, null, 2);
        }
    } catch (err) {
        resultEl.textContent = String(err);
    } finally {
        btn.disabled = false;
    }
});
</script>
@endpush

@endsection
