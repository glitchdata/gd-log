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
            'licenses' => License::with('product')
                ->where('user_id', Auth::id())
                ->orderBy('expires_at')
                ->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }
}
