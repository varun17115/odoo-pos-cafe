<x-app-layout>
    <x-slot name="title">Settings</x-slot>

    <div class="min-h-screen p-6" x-data="settingsPage()">

        {{-- Flash --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 px-4 py-2.5 bg-green-500/10 border border-green-500/30 text-green-400 text-sm rounded-xl flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="text-green-600 hover:text-green-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endif

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-white text-xl font-semibold">Settings</h1>
                <p class="text-gray-500 text-sm mt-0.5">Manage POS terminals and payment methods</p>
            </div>
        </div>

        {{-- Point of Sale bar --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl px-5 py-3 flex items-center gap-3 mb-6">
            <span class="text-gray-400 text-sm font-medium">Point of Sale</span>

            {{-- Config tabs --}}
            <div class="flex items-center gap-2 flex-wrap flex-1">
                @foreach($configs as $config)
                <button @click="activeConfig = {{ $config->id }}"
                        :class="activeConfig === {{ $config->id }}
                            ? 'bg-gray-700 text-white border-gray-600'
                            : 'text-gray-400 border-gray-700 hover:text-white hover:border-gray-500'"
                        class="px-3 py-1 text-sm rounded-lg border transition flex items-center gap-1.5">
                    {{ $config->name }}
                    @if($config->is_active)
                    <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                    @endif
                </button>
                @endforeach
            </div>

            {{-- + New --}}
            <button @click="newModal = true"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-orange-400 hover:text-orange-300 text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                New
            </button>
        </div>

        {{-- Config panels --}}
        @forelse($configs as $config)

        {{-- Activate form lives OUTSIDE the update form to avoid nesting --}}
        <form method="POST" action="{{ route('settings.activate', $config) }}" id="activate-form-{{ $config->id }}">
            @csrf
        </form>

        {{-- Delete form lives OUTSIDE the update form to avoid nesting --}}
        <form method="POST" action="{{ route('settings.destroy', $config) }}"
              id="delete-form-{{ $config->id }}"
              onsubmit="return confirmDelete(this)">
            @csrf @method('DELETE')
        </form>

        <div x-show="activeConfig === {{ $config->id }}" x-transition>
            <form method="POST" action="{{ route('settings.update', $config) }}">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                    {{-- Left: General --}}
                    <div class="xl:col-span-2 space-y-5">

                        {{-- Name + Active badge --}}
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-white font-semibold">Terminal Info</h2>
                                @if($config->is_active)
                                <span class="flex items-center gap-1.5 px-2.5 py-1 bg-green-500/10 border border-green-500/30 text-green-400 text-xs font-medium rounded-lg">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                    Active Terminal
                                </span>
                                @else
                                <button type="submit" form="activate-form-{{ $config->id }}"
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-800 hover:bg-orange-500/20 text-gray-400 hover:text-orange-400 text-xs font-medium rounded-lg border border-gray-700 hover:border-orange-500/40 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Set as Active
                                </button>
                                @endif
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1.5">Terminal Name</label>
                                <input type="text" name="name" value="{{ old('name', $config->name) }}" required
                                       class="w-full px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                                       placeholder="e.g. Main Counter" />
                            </div>
                        </div>

                        {{-- Payment Methods --}}
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                            <h2 class="text-white font-semibold mb-1">Payment Method</h2>
                            <p class="text-gray-500 text-xs mb-5">Enable the payment methods available at this terminal</p>

                            <div class="space-y-4">

                                {{-- Cash --}}
                                <div class="flex items-start gap-4 p-4 bg-gray-800/50 rounded-xl border border-gray-700/50">
                                    <div class="flex items-center h-5 mt-0.5">
                                        <input type="hidden" name="payment_cash" value="0" />
                                        <input type="checkbox" name="payment_cash" value="1" id="cash_{{ $config->id }}"
                                               {{ $config->payment_cash ? 'checked' : '' }}
                                               class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-orange-500 focus:ring-orange-500 focus:ring-offset-gray-900" />
                                    </div>
                                    <div class="flex-1">
                                        <label for="cash_{{ $config->id }}" class="flex items-center gap-2 cursor-pointer">
                                            <div class="w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-white text-sm font-medium">Cash</p>
                                                <p class="text-gray-500 text-xs">Accept cash payments at this terminal</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Card / Digital --}}
                                <div class="flex items-start gap-4 p-4 bg-gray-800/50 rounded-xl border border-gray-700/50">
                                    <div class="flex items-center h-5 mt-0.5">
                                        <input type="hidden" name="payment_card" value="0" />
                                        <input type="checkbox" name="payment_card" value="1" id="card_{{ $config->id }}"
                                               {{ $config->payment_card ? 'checked' : '' }}
                                               class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-orange-500 focus:ring-orange-500 focus:ring-offset-gray-900" />
                                    </div>
                                    <div class="flex-1">
                                        <label for="card_{{ $config->id }}" class="flex items-center gap-2 cursor-pointer">
                                            <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-white text-sm font-medium">Digital (Bank, Card)</p>
                                                <p class="text-gray-500 text-xs">Accept debit/credit card and net banking</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- UPI --}}
                                <div class="p-4 bg-gray-800/50 rounded-xl border border-gray-700/50"
                                     x-data="{ upiEnabled: {{ $config->payment_upi ? 'true' : 'false' }} }">
                                    <div class="flex items-start gap-4">
                                        <div class="flex items-center h-5 mt-0.5">
                                            <input type="hidden" name="payment_upi" value="0" />
                                            <input type="checkbox" name="payment_upi" value="1" id="upi_{{ $config->id }}"
                                                   x-model="upiEnabled"
                                                   {{ $config->payment_upi ? 'checked' : '' }}
                                                   class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-orange-500 focus:ring-orange-500 focus:ring-offset-gray-900" />
                                        </div>
                                        <div class="flex-1">
                                            <label for="upi_{{ $config->id }}" class="flex items-center gap-2 cursor-pointer">
                                                <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-white text-sm font-medium">QR Payment (UPI)</p>
                                                    <p class="text-gray-500 text-xs">Generate a QR code on the payment page based on the UPI ID</p>
                                                </div>
                                            </label>

                                            {{-- UPI ID field --}}
                                            <div x-show="upiEnabled" x-transition class="mt-3">
                                                <label class="block text-xs text-gray-500 mb-1.5">UPI ID</label>
                                                <input type="text" name="upi_id"
                                                       value="{{ old('upi_id', $config->upi_id) }}"
                                                       class="w-full max-w-xs px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                                                       placeholder="e.g. 123@ybl.com" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    {{-- Right: Summary card --}}
                    <div class="space-y-4">
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 sticky top-4">
                            <h3 class="text-white font-semibold mb-4">Summary</h3>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Terminal</span>
                                    <span class="text-white font-medium">{{ $config->name }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Status</span>
                                    @if($config->is_active)
                                    <span class="text-green-400 font-medium">Active</span>
                                    @else
                                    <span class="text-gray-500">Inactive</span>
                                    @endif
                                </div>
                                <div class="border-t border-gray-800 pt-3">
                                    <p class="text-xs text-gray-500 mb-2">Payment Methods</p>
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2 text-xs {{ $config->payment_cash ? 'text-green-400' : 'text-gray-600' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $config->payment_cash ? 'bg-green-400' : 'bg-gray-700' }}"></span>
                                            Cash
                                        </div>
                                        <div class="flex items-center gap-2 text-xs {{ $config->payment_card ? 'text-blue-400' : 'text-gray-600' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $config->payment_card ? 'bg-blue-400' : 'bg-gray-700' }}"></span>
                                            Digital / Card
                                        </div>
                                        <div class="flex items-center gap-2 text-xs {{ $config->payment_upi ? 'text-purple-400' : 'text-gray-600' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $config->payment_upi ? 'bg-purple-400' : 'bg-gray-700' }}"></span>
                                            UPI {{ $config->upi_id ? '('.$config->upi_id.')' : '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 space-y-2">
                                <button type="submit"
                                        class="w-full py-2.5 bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold rounded-xl transition">
                                    Save Settings
                                </button>
                                <button type="submit" form="delete-form-{{ $config->id }}"
                                        class="w-full py-2 bg-gray-800 hover:bg-red-500/20 text-gray-500 hover:text-red-400 text-xs font-medium rounded-xl border border-gray-700 hover:border-red-500/30 transition">
                                    Delete Terminal
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-16 h-16 bg-gray-900 border border-gray-800 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-gray-400 font-medium">No POS terminals yet</p>
            <p class="text-gray-600 text-sm mt-1">Create your first terminal to configure payment methods</p>
            <button @click="newModal = true"
                    class="mt-4 px-5 py-2.5 bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold rounded-xl transition">
                Create First Terminal
            </button>
        </div>
        @endforelse

        {{-- ===== NEW CONFIG MODAL ===== --}}
        <div x-show="newModal" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70">
            <div @click.outside="newModal = false" x-transition
                 class="w-full max-w-sm bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl p-6">
                <h2 class="text-white font-semibold text-lg mb-1">New POS Terminal</h2>
                <p class="text-gray-500 text-xs mb-5">Give this terminal a name to identify it</p>
                <form method="POST" action="{{ route('settings.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs text-gray-500 mb-1.5">Name</label>
                        <input type="text" name="name" required autofocus
                               class="w-full px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                               placeholder="e.g. Main Counter" />
                        @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="newModal = false"
                                class="flex-1 py-2.5 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm font-medium rounded-xl transition">
                            Discard
                        </button>
                        <button type="submit"
                                class="flex-1 py-2.5 bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold rounded-xl transition">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
    function settingsPage() {
        return {
            activeConfig: {{ $configs->firstWhere('is_active', true)?->id ?? $configs->first()?->id ?? 'null' }},
            newModal: false,
        }
    }
    </script>
</x-app-layout>
