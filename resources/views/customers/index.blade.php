<x-app-layout>
    <x-slot name="title">Customers</x-slot>

    <div class="min-h-screen p-6" x-data="customersPage()" x-init="init()">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-white font-bold text-xl">Customers</h1>
            <button @click="openNew()"
                    class="flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Customer
            </button>
        </div>

        {{-- Search --}}
        <div class="mb-4">
            <input x-model="search" type="text" placeholder="Search by name, phone or email..."
                   class="w-full max-w-sm bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500">
        </div>

        {{-- Table --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left px-5 py-3 text-gray-500 font-medium">Name</th>
                        <th class="text-left px-5 py-3 text-gray-500 font-medium">Contact</th>
                        <th class="text-left px-5 py-3 text-gray-500 font-medium">City / State</th>
                        <th class="text-left px-5 py-3 text-gray-500 font-medium">Orders</th>
                        <th class="text-right px-5 py-3 text-gray-500 font-medium">Total Sales</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr class="border-b border-gray-800 hover:bg-gray-800/40 transition"
                        x-show="!search.trim() || $el.dataset.search.includes(search.trim().toLowerCase())"
                        data-search="{{ strtolower($customer->name . ' ' . $customer->phone . ' ' . $customer->email) }}">
                        <td class="px-5 py-3 text-white font-medium">{{ $customer->name }}</td>
                        <td class="px-5 py-3">
                            @if($customer->email)
                            <div class="flex items-center gap-1.5 text-gray-400 text-xs mb-0.5">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ $customer->email }}
                            </div>
                            @endif
                            @if($customer->phone)
                            <div class="flex items-center gap-1.5 text-gray-400 text-xs">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                {{ $customer->phone }}
                            </div>
                            @endif
                            @if(!$customer->email && !$customer->phone)
                                <span class="text-gray-600">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-400 text-xs">
                            {{ collect([$customer->city, $customer->state])->filter()->implode(', ') ?: '—' }}
                        </td>
                        <td class="px-5 py-3 text-gray-400">{{ $customer->orders_count }}</td>
                        <td class="px-5 py-3 text-right text-orange-400 font-semibold">₹{{ number_format($customer->total_sales, 2) }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openEdit({{ $customer->toJson() }})"
                                        class="text-xs text-gray-400 hover:text-white border border-gray-700 hover:border-gray-500 px-2 py-1 rounded-lg transition">
                                    Edit
                                </button>
                                <button style="color:white" @click="deleteCustomer({{ $customer->id }})"
                                        class="text-xs text-red-500 hover:text-red-400 border border-red-900 hover:border-red-700 px-2 py-1 rounded-lg transition">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-500">No customers yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ===== CUSTOMER FORM MODAL ===== --}}
        <div x-show="modalOpen" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
            <div class="bg-gray-900 border border-gray-700 rounded-2xl w-full max-w-lg" @click.stop>

                <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-800">
                    <h2 class="text-white font-bold text-base" x-text="editId ? 'Edit Customer' : 'New Customer'"></h2>
                    <button @click="modalOpen = false" class="text-gray-500 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="save()" class="px-6 py-5 space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="text-gray-400 text-xs mb-1 block">Full Name *</label>
                        <input x-model="form.name" type="text" placeholder="e.g. Eric Smith" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500">
                    </div>

                    {{-- Email + Phone --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-gray-400 text-xs mb-1 block">Email</label>
                            <input x-model="form.email" type="email" placeholder="email@example.com"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500">
                        </div>
                        <div>
                            <label class="text-gray-400 text-xs mb-1 block">Phone</label>
                            <input x-model="form.phone" type="text" placeholder="+91 98989 89898"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500">
                        </div>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="text-gray-400 text-xs mb-1 block">Address</label>
                        <input x-model="form.street1" type="text" placeholder="Street line 1"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500 mb-2">
                        <input x-model="form.street2" type="text" placeholder="Street line 2 (optional)"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500">
                    </div>

                    {{-- City / State / Country --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="text-gray-400 text-xs mb-1 block">City</label>
                            <input x-model="form.city" type="text" placeholder="Gandhinagar"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500">
                        </div>
                        <div>
                            <label class="text-gray-400 text-xs mb-1 block">State</label>
                            <select x-model="form.state"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-orange-500">
                                <option value="">Select state</option>
                                @foreach(['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Delhi','Jammu & Kashmir','Ladakh','Puducherry','Chandigarh'] as $state)
                                <option value="{{ $state }}">{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-400 text-xs mb-1 block">Country</label>
                            <input x-model="form.country" type="text" placeholder="India"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-orange-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="modalOpen = false"
                                class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-400 text-sm rounded-xl transition">
                            Cancel
                        </button>
                        <button type="submit" :disabled="saving"
                                class="px-5 py-2 bg-orange-500 hover:bg-orange-400 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition">
                            <span x-text="saving ? 'Saving...' : (editId ? 'Update' : 'Create')"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    function customersPage() {
        return {
            search: '',
            modalOpen: false,
            editId: null,
            saving: false,
            form: { name: '', email: '', phone: '', street1: '', street2: '', city: '', state: '', country: 'India' },

            init() {},

            openNew() {
                this.editId = null;
                this.form = { name: '', email: '', phone: '', street1: '', street2: '', city: '', state: '', country: 'India' };
                this.modalOpen = true;
            },

            openEdit(customer) {
                this.editId = customer.id;
                this.form = {
                    name: customer.name || '',
                    email: customer.email || '',
                    phone: customer.phone || '',
                    street1: customer.street1 || '',
                    street2: customer.street2 || '',
                    city: customer.city || '',
                    state: customer.state || '',
                    country: customer.country || 'India',
                };
                this.modalOpen = true;
            },

            async deleteCustomer(id) {
                const result = await Swal.fire({
                    title: 'Delete customer?',
                    text: 'This cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                });
                if (!result.isConfirmed) return;
                await fetch(`/customers/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                });
                window.location.reload();
            },

            async save() {
                this.saving = true;
                const url    = this.editId ? `/customers/${this.editId}` : '/customers';
                const method = this.editId ? 'PUT' : 'POST';
                const resp   = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify(this.form),
                });
                this.saving = false;
                if (resp.ok) {
                    this.modalOpen = false;
                    window.location.reload();
                } else {
                    const err = await resp.json();
                    Swal.fire({ icon: 'error', title: 'Error', text: Object.values(err.errors || {}).flat().join('\n') });
                }
            },
        };
    }
    </script>
</x-app-layout>
