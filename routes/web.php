<?php

use Illuminate\Support\Facades\Route;
use App\Models\Lead;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/leads', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
    ]);

    Lead::create($validated);

    return redirect()->back()->with('success', 'Obrigado pelo interesse! Entraremos em contato em breve.');
});