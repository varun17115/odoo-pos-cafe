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
            'name'               => 'required|string|max:100',
            'payment_cash'       => 'nullable|boolean',
            'payment_card'       => 'nullable|boolean',
            'payment_upi'        => 'nullable|boolean',
            'upi_id'             => 'nullable|string|max:100',
            'self_ordering'      => 'nullable|boolean',
            'self_ordering_type' => 'nullable|in:online_ordering,qr_menu',
            'bg_color'           => 'nullable|string|max:7',
            'bg_image_1'         => 'nullable|image|max:2048',
            'bg_image_2'         => 'nullable|image|max:2048',
            'bg_image_3'         => 'nullable|image|max:2048',
        ]);

        $update = [
            'name'               => $data['name'],
            'payment_cash'       => $request->boolean('payment_cash'),
            'payment_card'       => $request->boolean('payment_card'),
            'payment_upi'        => $request->boolean('payment_upi'),
            'upi_id'             => $data['upi_id'] ?? null,
            'self_ordering'      => $request->boolean('self_ordering'),
            'self_ordering_type' => $request->boolean('self_ordering') ? ($data['self_ordering_type'] ?? null) : null,
            'bg_color'           => $data['bg_color'] ?? '#111827',
        ];

        // Auto-generate token if self ordering enabled and no token yet
        if ($request->boolean('self_ordering') && !$posConfig->self_ordering_token) {
            $update['self_ordering_token'] = \Illuminate\Support\Str::random(12);
        }

        // Handle background image uploads
        foreach (['bg_image_1', 'bg_image_2', 'bg_image_3'] as $field) {
            if ($request->hasFile($field)) {
                if ($posConfig->$field) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($posConfig->$field);
                }
                $update[$field] = $request->file($field)->store('pos-bg', 'public');
            }
        }

        $posConfig->update($update);

        return redirect()->route('settings.index')->with('success', 'Settings saved.');
    }

    public function downloadQr(PosConfig $posConfig)
    {
        if (!$posConfig->self_ordering_token) {
            return back()->with('error', 'No token generated yet. Save settings first.');
        }
        $floors = \App\Models\Floor::with('tables')->get();
        return view('settings.qr', compact('posConfig', 'floors'));
    }
    public function activate(PosConfig $posConfig){
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
