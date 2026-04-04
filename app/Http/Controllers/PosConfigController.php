<?php

namespace App\Http\Controllers;

use App\Models\PosConfig;
use Illuminate\Http\Request;

class PosConfigController extends Controller
{
    public function index()
    {
        $configs = PosConfig::orderByDesc('is_active')->orderBy('id')->get();
        return view('settings.index', compact('configs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        PosConfig::create([
            'name'         => $data['name'],
            'payment_cash' => true,
        ]);
        return redirect()->route('settings.index')->with('success', 'POS config created.');
    }

    public function update(Request $request, PosConfig $posConfig)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'payment_cash' => 'nullable|boolean',
            'payment_card' => 'nullable|boolean',
            'payment_upi'  => 'nullable|boolean',
            'upi_id'       => 'nullable|string|max:100',
        ]);

        $posConfig->update([
            'name'         => $data['name'],
            'payment_cash' => $request->boolean('payment_cash'),
            'payment_card' => $request->boolean('payment_card'),
            'payment_upi'  => $request->boolean('payment_upi'),
            'upi_id'       => $data['upi_id'] ?? null,
            // is_active is NOT touched here — use the activate route for that
        ]);

        return redirect()->route('settings.index')->with('success', 'Settings saved.');
    }

    public function activate(PosConfig $posConfig)
    {
        PosConfig::query()->update(['is_active' => false]);
        $posConfig->update(['is_active' => true]);
        return redirect()->route('settings.index')->with('success', '"'.$posConfig->name.'" is now the active terminal.');
    }

    public function destroy(PosConfig $posConfig)
    {
        $posConfig->delete();
        return redirect()->route('settings.index')->with('success', 'Config deleted.');
    }
}
