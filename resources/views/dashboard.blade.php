<x-app-layout>
    <x-slot name="title">POS Home</x-slot>

    <div class="min-h-screen p-8">

        <h2 class="text-gray-400 text-sm font-medium mb-4 uppercase tracking-wider">POS Terminals</h2>

        <div class="flex flex-wrap gap-4">

            {{-- POS Terminal Card --}}
            <div class="w-72 bg-gray-900 border border-gray-800 rounded-xl p-5 relative"
                 x-data="{ terminalMenu: false }">

                {{-- Header --}}
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-white font-semibold text-base">
                            {{ $activeConfig ? $activeConfig->name : config('app.name') }}
                        </h3>
                        <div class="mt-2 space-y-0.5">
                            @if($openSession)
                                <p class="text-gray-500 text-xs">Session open since:
                                    <span class="text-green-400">{{ $openSession->opened_at->format('d M, H:i') }}</span>
                                </p>
                                <p class="text-gray-500 text-xs">Sales this session:
                                    <span class="text-gray-300">₹{{ number_format($openSession->total_sales, 2) }}</span>
                                </p>
                            @elseif($lastSession)
                                <p class="text-gray-500 text-xs">Last open:
                                    <span class="text-gray-400">{{ $lastSession->opened_at->format('d M, H:i') }}</span>
                                </p>
                                <p class="text-gray-500 text-xs">Last sell:
                                    <span class="text-gray-400">₹{{ number_format($lastSession->total_sales, 2) }}</span>
                                </p>
                            @else
                                <p class="text-gray-500 text-xs">Last open: <span class="text-gray-400">—</span></p>
                                <p class="text-gray-500 text-xs">Last sell: <span class="text-gray-400">—</span></p>
                            @endif
                        </div>
                    </div>

                    {{-- Three-dot menu --}}
                    <div class="relative">
                        <button @click.stop="terminalMenu = !terminalMenu"
                                class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-white hover:bg-gray-800 rounded-lg transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 5a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 7a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 7a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                            </svg>
                        </button>

                        <div x-show="terminalMenu" @click.outside="terminalMenu = false" x-transition
                             class="absolute right-0 top-full mt-1 w-44 bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden z-50">
                            <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Setting
                            </a>
                            <a href="{{ route('kitchen.display') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Kitchen Display
                            </a>
                            <a href="{{ route('customer.display') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Customer Display
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Session Button --}}
                @if($openSession)
                    <a href="{{ route('pos.terminal') }}"
                       class="block w-full py-2 px-4 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold rounded-lg transition text-center">
                        Continue Session
                    </a>
                @elseif($activeConfig)
                    <form method="POST" action="{{ route('pos.session.open') }}">
                        @csrf
                        <input type="hidden" name="pos_config_id" value="{{ $activeConfig->id }}">
                        <button type="submit"
                                class="w-full py-2 px-4 bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold rounded-lg transition">
                            Open Session
                        </button>
                    </form>
                @else
                    <a href="{{ route('settings.index') }}"
                       class="block w-full py-2 px-4 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm font-semibold rounded-lg transition text-center">
                        Configure POS First
                    </a>
                @endif
            </div>

        </div>
    </div>

</x-app-layout>
