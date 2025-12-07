@csrf
<div class="stack" style="display:grid;gap:1rem;">
    <label>
        <span>Name</span>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required>
    </label>
    <label>
        <span>Product code</span>
        <input type="text" name="product_code" value="{{ old('product_code', $product->product_code ?? '') }}" required>
    </label>
    <div class="grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;">
        <label>
            <span>Vendor</span>
            <input type="text" name="vendor" value="{{ old('vendor', $product->vendor ?? '') }}">
        </label>
        <label>
            <span>Category</span>
            <input type="text" name="category" value="{{ old('category', $product->category ?? '') }}">
        </label>
        <label>
            <span>Price (USD per seat)</span>
            <input type="number" name="price" min="0" step="0.01" value="{{ old('price', $product->price ?? '0.00') }}" required>
        </label>
        <label>
            <span>License duration (months)</span>
            <input type="number" name="duration_months" min="1" max="60" value="{{ old('duration_months', $product->duration_months ?? 12) }}" required>
        </label>
    </div>
    <label>
        <span>Description</span>
        <textarea name="description" rows="4" style="width:100%;border:1px solid rgba(15,23,42,0.15);border-radius:0.9rem;padding:0.85rem 1rem;font-size:1rem;">{{ old('description', $product->description ?? '') }}</textarea>
    </label>
</div>

<div style="margin-top:1.5rem;display:flex;gap:1rem;flex-wrap:wrap;">
    <button type="submit">{{ $submitLabel }}</button>
    <a class="link" href="{{ route('admin.products.index') }}">Cancel</a>
</div>
