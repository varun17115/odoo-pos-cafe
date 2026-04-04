<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class FloorController extends Controller
{
    public function index()
    {
        $floors = Floor::withCount('tables')->with('tables')->orderBy('id')->get();
        return view('floors.index', compact('floors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        Floor::create($data); // booted() seeds 5 tables automatically
        return back()->with('success', 'Floor created with 5 default tables.');
    }

    public function update(Request $request, Floor $floor)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        $floor->update($data);
        if ($request->expectsJson()) return response()->json(['ok' => true]);
        return back()->with('success', 'Floor updated.');
    }

    public function destroy(Floor $floor)
    {
        $floor->delete();
        return back()->with('success', 'Floor deleted.');
    }

    // ---- Table actions ----

    public function storeTable(Request $request, Floor $floor)
    {
        $data = $request->validate([
            'number' => 'required|string|max:20',
            'seats'  => 'required|integer|min:1|max:50',
            'status' => 'required|in:vacant,occupied,reserved,inactive',
        ]);
        $table = $floor->tables()->create($data);
        if ($request->expectsJson()) return response()->json($table);
        return back()->with('success', 'Table added.');
    }

    public function updateTable(Request $request, Floor $floor, RestaurantTable $table)
    {
        $data = $request->validate([
            'number' => 'required|string|max:20',
            'seats'  => 'required|integer|min:1|max:50',
            'status' => 'required|in:vacant,occupied,reserved,inactive',
        ]);
        $table->update($data);
        if ($request->expectsJson()) return response()->json(['ok' => true]);
        return back()->with('success', 'Table updated.');
    }

    public function destroyTable(Floor $floor, RestaurantTable $table)
    {
        $table->delete();
        return back()->with('success', 'Table deleted.');
    }

    public function bulkDestroyTables(Request $request, Floor $floor)
    {
        $floor->tables()->whereIn('id', $request->input('ids', []))->delete();
        return back()->with('success', 'Tables deleted.');
    }

    public function bulkUpdateStatus(Request $request, Floor $floor)
    {
        $request->validate(['status' => 'required|in:vacant,occupied,reserved,inactive']);
        $floor->tables()->whereIn('id', $request->input('ids', []))->update(['status' => $request->status]);
        return back()->with('success', 'Table status updated.');
    }
}
