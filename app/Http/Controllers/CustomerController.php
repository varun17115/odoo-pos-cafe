<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount('orders')->latest()->get();
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'nullable|email|max:150',
            'phone'   => 'nullable|string|max:20',
            'street1' => 'nullable|string|max:200',
            'street2' => 'nullable|string|max:200',
            'city'    => 'nullable|string|max:100',
            'state'   => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        $customer = Customer::create($data);

        if ($request->wantsJson()) {
            return response()->json($customer);
        }

        return back()->with('success', 'Customer created.');
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'nullable|email|max:150',
            'phone'   => 'nullable|string|max:20',
            'street1' => 'nullable|string|max:200',
            'street2' => 'nullable|string|max:200',
            'city'    => 'nullable|string|max:100',
            'state'   => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        $customer->update($data);

        if ($request->wantsJson()) {
            return response()->json($customer);
        }

        return back()->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success', 'Customer deleted.');
    }

    // API: search customers for terminal picker
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $customers = Customer::when($q, fn($query) =>
            $query->where('name', 'like', "%$q%")
                  ->orWhere('phone', 'like', "%$q%")
                  ->orWhere('email', 'like', "%$q%")
        )->orderBy('name')->limit(20)->get(['id', 'name', 'phone', 'email']);

        return response()->json($customers);
    }
}
