<x-app-layout>
    <x-slot name="title">Floors & Tables</x-slot>

    <div class="min-h-screen p-6" x-data="floorsPage()">

        {{-- Flash --}}
        @if(session('success'))
        <div class="mb-4 px-4 py-2.5 bg-green-500/10 border border-green-500/30 text-green-400 text-sm rounded-xl">
            {{ session('success') }}
        </div>
        @endif

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-white text-xl font-semibold">Floors & Tables</h1>
                <p class="text-gray-500 text-sm mt-0.5">Manage restaurant floors and table layout</p>
            </div>
            <button @click="newFloorModal = true"
                    class="flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                New Floor
            </button>
        </div>

        {{-- Floor tabs --}}
        @if($floors->count())
        <div class="flex items-center gap-2 mb-6 flex-wrap">
            @foreach($floors as $floor)
            <button @click="activeFloor = {{ $floor->id }}; selectedTables = []"
                    :class="activeFloor === {{ $floor->id }}
                        ? 'bg-orange-500 text-white border-orange-500'
                        : 'bg-gray-900 text-gray-400 border-gray-700 hover:text-white hover:border-gray-500'"
                    class="px-4 py-2 text-sm font-medium rounded-xl border transition flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                {{ $floor->name }}
                <span class="text-xs opacity-70">({{ $floor->tables_count }})</span>
            </button>
            @endforeach
        </div>

        {{-- Floor panels --}}
        @foreach($floors as $floor)
        <div x-show="activeFloor === {{ $floor->id }}" x-transition>

            {{-- Floor header --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3" x-data="{ editing: false, name: '{{ addslashes($floor->name) }}' }">
                    <span x-show="!editing" class="text-white font-semibold text-lg" x-text="name"></span>
                    <input x-show="editing" type="text" x-model="name"
                           @keydown.enter="saveFloorName({{ $floor->id }}, name); editing = false"
                           @keydown.escape="editing = false"
                           @blur="saveFloorName({{ $floor->id }}, name); editing = false"
                           class="bg-gray-800 border border-gray-600 rounded-lg px-3 py-1 text-white text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500" />
                    <button x-show="!editing" @click="editing = true"
                            class="p-1.5 text-gray-600 hover:text-white hover:bg-gray-800 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Bulk actions --}}
                    <div x-show="selectedTables.length > 0" x-transition class="flex items-center gap-2">
                        <span class="text-xs text-gray-400" x-text="selectedTables.length + ' selected'"></span>

                        {{-- Change Status --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-medium rounded-lg border border-gray-700 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Set Status
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                 class="absolute left-0 top-full mt-1 w-40 bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden z-30">
                                @foreach(['vacant' => ['label' => 'Vacant', 'color' => 'bg-green-500'], 'occupied' => ['label' => 'Occupied', 'color' => 'bg-red-500'], 'reserved' => ['label' => 'Reserved', 'color' => 'bg-yellow-500'], 'inactive' => ['label' => 'Inactive', 'color' => 'bg-gray-500']] as $status => $meta)
                                <form method="POST" action="{{ route('floors.tables.bulk-status', $floor) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $status }}" />
                                    <template x-for="id in selectedTables" :key="id">
                                        <input type="hidden" name="ids[]" :value="id" />
                                    </template>
                                    <button type="submit"
                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-gray-300 hover:bg-gray-700 hover:text-white transition">
                                        <span class="w-2 h-2 rounded-full {{ $meta['color'] }}"></span>
                                        {{ $meta['label'] }}
                                    </button>
                                </form>
                                @endforeach
                            </div>
                        </div>

                        {{-- Delete --}}
                        <form method="POST" action="{{ route('floors.tables.bulk-destroy', $floor) }}">
                            @csrf @method('DELETE')
                            <template x-for="id in selectedTables" :key="id">
                                <input type="hidden" name="ids[]" :value="id" />
                            </template>
                            <button type="submit" onclick="return confirmDelete(this.closest('form'))"
                                    class="flex items-center gap-1.5 px-3 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-400 text-xs font-medium rounded-lg border border-red-500/30 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>

                    <button @click="openAddTable({{ $floor->id }})"
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-medium rounded-lg border border-gray-700 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Table
                    </button>

                    <form method="POST" action="{{ route('floors.destroy', $floor) }}"
                          onsubmit="return confirmDelete(this)">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-800 hover:bg-red-500/20 text-gray-500 hover:text-red-400 text-xs font-medium rounded-lg border border-gray-700 hover:border-red-500/30 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Floor
                        </button>
                    </form>
                </div>
            </div>

            {{-- Tables table --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mb-8">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-800">
                            <th class="w-10 px-4 py-3">
                                <input type="checkbox" @change="toggleAllTables($event, {{ $floor->id }})"
                                       class="w-3.5 h-3.5 rounded border-gray-600 bg-gray-800 text-orange-500 focus:ring-orange-500 focus:ring-offset-gray-900" />
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table Number</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seats</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointment</th>
                            <th class="w-16 px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($floor->tables as $table)
                        <tr class="hover:bg-gray-800/40 transition group"
                            :class="selectedTables.includes({{ $table->id }}) ? 'bg-orange-500/5' : ''">
                            <td class="px-4 py-3">
                                <input type="checkbox" :value="{{ $table->id }}" x-model="selectedTables"
                                       class="w-3.5 h-3.5 rounded border-gray-600 bg-gray-800 text-orange-500 focus:ring-orange-500 focus:ring-offset-gray-900" />
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-white font-medium">{{ $table->number }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $table->seats }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $colors = [
                                        'vacant'   => 'bg-green-500/20 text-green-400 border-green-500/30',
                                        'occupied' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                        'reserved' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                                        'inactive' => 'bg-gray-500/20 text-gray-400 border-gray-500/30',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-medium rounded-lg border {{ $colors[$table->status] }}">
                                    {{ ucfirst($table->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                Table {{ $table->number }} (Seating {{ $table->seats }})
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                                    <button @click="openEditTable({{ $floor->id }}, {{ $table->id }}, '{{ addslashes($table->number) }}', {{ $table->seats }}, '{{ $table->status }}')"
                                            class="p-1.5 text-gray-500 hover:text-white hover:bg-gray-700 rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('floors.tables.destroy', [$floor, $table]) }}"
                                          onsubmit="return confirmDelete(this)">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-red-400 hover:bg-gray-700 rounded-lg transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-600 text-sm">
                                No tables on this floor.
                                <button @click="openAddTable({{ $floor->id }})" class="text-orange-500 hover:text-orange-400 ml-1">Add one</button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Visual floor map --}}
            <div class="mb-8">
                <h3 class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-3">Floor Map</h3>
                <div class="flex flex-wrap gap-3">
                    @foreach($floor->tables as $table)
                    @php
                        $mapColors = [
                            'vacant'   => ['bg' => 'bg-green-500/10', 'border' => 'border-green-500/40', 'dot' => 'bg-green-500', 'text' => 'text-green-400'],
                            'occupied' => ['bg' => 'bg-red-500/10',   'border' => 'border-red-500/40',   'dot' => 'bg-red-500',   'text' => 'text-red-400'],
                            'reserved' => ['bg' => 'bg-yellow-500/10','border' => 'border-yellow-500/40','dot' => 'bg-yellow-500','text' => 'text-yellow-400'],
                            'inactive' => ['bg' => 'bg-gray-800',     'border' => 'border-gray-700',     'dot' => 'bg-gray-600',  'text' => 'text-gray-500'],
                        ];
                        $mc = $mapColors[$table->status];
                    @endphp
                    <div class="w-24 h-24 {{ $mc['bg'] }} border-2 {{ $mc['border'] }} rounded-2xl flex flex-col items-center justify-center gap-1 cursor-pointer hover:scale-105 transition"
                         @click="openEditTable({{ $floor->id }}, {{ $table->id }}, '{{ addslashes($table->number) }}', {{ $table->seats }}, '{{ $table->status }}')">
                        <span class="w-2.5 h-2.5 rounded-full {{ $mc['dot'] }}"></span>
                        <span class="text-white font-bold text-sm">{{ $table->number }}</span>
                        <span class="{{ $mc['text'] }} text-xs">{{ $table->seats }} seats</span>
                    </div>
                    @endforeach
                    <button @click="openAddTable({{ $floor->id }})"
                            class="w-24 h-24 bg-gray-900 border-2 border-dashed border-gray-700 hover:border-orange-500 rounded-2xl flex flex-col items-center justify-center gap-1 text-gray-600 hover:text-orange-500 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="text-xs">Add</span>
                    </button>
                </div>

                {{-- Legend --}}
                <div class="flex items-center gap-4 mt-3">
                    @foreach(['vacant' => 'Vacant', 'occupied' => 'Occupied', 'reserved' => 'Reserved', 'inactive' => 'Inactive'] as $s => $label)
                    @php $dot = ['vacant'=>'bg-green-500','occupied'=>'bg-red-500','reserved'=>'bg-yellow-500','inactive'=>'bg-gray-600'][$s]; @endphp
                    <span class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-2 h-2 rounded-full {{ $dot }}"></span>{{ $label }}
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach

        @else
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-16 h-16 bg-gray-900 border border-gray-800 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <p class="text-gray-400 font-medium">No floors yet</p>
            <p class="text-gray-600 text-sm mt-1">Create your first floor to start managing tables</p>
            <button @click="newFloorModal = true"
                    class="mt-4 px-5 py-2.5 bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold rounded-xl transition">
                Create First Floor
            </button>
        </div>
        @endif

        {{-- ===== NEW FLOOR MODAL ===== --}}
        <div x-show="newFloorModal" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70">
            <div @click.outside="newFloorModal = false" x-transition
                 class="w-full max-w-sm bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl p-6">
                <h2 class="text-white font-semibold text-lg mb-5">New Floor</h2>
                <form method="POST" action="{{ route('floors.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs text-gray-500 mb-1.5">Floor Name</label>
                        <input type="text" name="name" required autofocus
                               class="w-full px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                               placeholder="e.g. Ground Floor" />
                        @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <p class="text-gray-600 text-xs mb-4">5 default tables (4 seats each) will be created automatically.</p>
                    <div class="flex gap-3">
                        <button type="button" @click="newFloorModal = false"
                                class="flex-1 py-2.5 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm font-medium rounded-xl transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 py-2.5 bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold rounded-xl transition">
                            Create Floor
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== ADD / EDIT TABLE MODAL ===== --}}
        <div x-show="tableModal" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70">
            <div @click.outside="tableModal = false" x-transition
                 class="w-full max-w-sm bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl p-6">
                <h2 class="text-white font-semibold text-lg mb-5" x-text="tableForm.id ? 'Edit Table' : 'Add Table'"></h2>

                {{-- Add forms per floor --}}
                @foreach($floors as $floor)
                <form x-show="!tableForm.id && tableForm.floorId === {{ $floor->id }}"
                      method="POST" action="{{ route('floors.tables.store', $floor) }}" class="space-y-4">
                    @csrf
                    @include('floors._table_fields')
                    @include('floors._table_buttons')
                </form>

                {{-- Edit forms per table --}}
                @foreach($floor->tables as $table)
                <form x-show="tableForm.id === {{ $table->id }}"
                      method="POST" action="{{ route('floors.tables.update', [$floor, $table]) }}" class="space-y-4">
                    @csrf @method('PUT')
                    @include('floors._table_fields')
                    @include('floors._table_buttons')
                </form>
                @endforeach
                @endforeach
            </div>
        </div>

    </div>

    <script>
    function floorsPage() {
        return {
            activeFloor: {{ $floors->first()?->id ?? 'null' }},
            newFloorModal: false,
            tableModal: false,
            selectedTables: [],
            tableForm: { id: null, floorId: null, number: '', seats: 4, status: 'vacant' },

            openAddTable(floorId) {
                this.tableForm = { id: null, floorId, number: '', seats: 4, status: 'vacant' };
                this.tableModal = true;
            },
            openEditTable(floorId, id, number, seats, status) {
                this.tableForm = { id, floorId, number, seats, status };
                this.tableModal = true;
            },
            toggleAllTables(e, floorId) {
                const floorTableIds = {
                    @foreach($floors as $floor)
                    {{ $floor->id }}: [{{ $floor->tables->pluck('id')->join(', ') }}],
                    @endforeach
                };
                const ids = floorTableIds[floorId] ?? [];
                if (e.target.checked) {
                    // add only this floor's IDs, keep others untouched
                    const others = this.selectedTables.filter(id => !ids.includes(id));
                    this.selectedTables = [...others, ...ids];
                } else {
                    this.selectedTables = this.selectedTables.filter(id => !ids.includes(id));
                }
            },
            saveFloorName(id, name) {
                fetch(`/floors/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ name, _method: 'PUT' }),
                });
            },
        }
    }
    </script>
</x-app-layout>
