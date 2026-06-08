<?php

use Illuminate\Support\Facades\Route;
use App\Models\Lead;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/leads', function (Request $request) {
    $validated = $request->validate([
        'name'   => 'required|string',
        'email'  => 'required|email',
        'phone'  => 'required|string',
        'plano'  => 'nullable|string',
        'periodo'=> 'required|string',
    ]);

    Lead::create($validated);

    return redirect()->back()->with('success', 'Interesse no plano ' . $request->plano . ' registrado com sucesso!');
});