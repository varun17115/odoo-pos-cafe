<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PosConfig;
use App\Models\RestaurantTable;

use App\Models\PosSession;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function open(Request $request)
    {
        $request->validate(['pos_config_id' => 'required|exists:pos_configs,id']);

        // Close any existing open sessions for this config
        PosSession::where('pos_config_id', $request->pos_config_id)
            ->where('status', 'open')
            ->update(['status' => 'closed', 'closed_at' => now()]);

        RestaurantTable::where('status','occupied')->update(['status'=> 'vacant']);

        PosSession::create([
            'pos_config_id' => $request->pos_config_id,
            'opened_by'     => auth()->id(),
            'opened_at'     => now(),
            'status'        => 'open',
            'total_sales'   => 0,
        ]);

        return redirect()->route('pos.terminal');
    }

    public function close(PosSession $session)
    {
        Order::where('pos_session_id', $session->id)->update([
            'status'=> 'paid',
            'payment_method'=>'cash'
        ]);

        $totalSales = $session->orders()
            ->where('status', 'paid')
            ->sum('total');

        $session->update([
            'closed_at'   => now(),
            'status'      => 'closed',
            'total_sales' => $totalSales,
        ]);

        return redirect()->route('dashboard')->with('success', 'Session closed successfully.');
    }
}
