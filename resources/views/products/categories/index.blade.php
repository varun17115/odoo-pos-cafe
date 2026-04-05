<x-app-layout>
    <x-slot name="title">Categories</x-slot>

    <div class="min-h-screen p-6">

        {{-- Flash --}}
        @if(session('success'))
        <div class="mb-4 px-4 py-2.5 bg-green-500/10 border border-green-500/30 text-green-400 text-sm rounded-xl">
            {{ session('success') }}
        </div>
        @endif

        <div class="max-w-4xl" x-data="categoriesPage()" x-init="init()">

            {{-- Header row: New + Category label --}}
            <div class="flex items-center gap-3 mb-4">
                <button @click="addRow()"
                        class="px-3 py-1 bg-orange-500 hover:bg-orange-400 text-white text-xs font-semibold rounded-md transition">
                    New
                </button>
                <span class="text-gray-300 text-sm font-medium">Category</span>
            </div>

            {{-- Table --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-800">
                            <th class="w-8 px-3 py-3"></th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400">Product Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400">Color</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400">Products</th>
                            <th class="w-10 px-3 py-3"></th>
                        </tr>
                    </thead>
                    <tbody id="categories-tbody">

                        {{-- Existing rows --}}
                        @foreach($categories as $category)
                        <tr class="border-b border-gray-800/60 hover:bg-gray-800/30 transition category-row"
                            data-id="{{ $category->id }}"
                            x-data="categoryRow({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ $category->color }}', false)">

                            {{-- Drag handle --}}
                            <td class="px-3 py-2.5 cursor-grab active:cursor-grabbing drag-handle">
                                <svg class="w-4 h-4 text-gray-600 hover:text-gray-400 transition" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm8-12a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                                </svg>
                            </td>

                            {{-- Name (inline editable) --}}
                            <td class="px-4 py-2.5">
                                <input type="text" x-model="name" @blur="save()" @keydown.enter="save()"
                                       class="w-full bg-transparent text-white text-sm focus:outline-none focus:bg-gray-800 focus:px-2 focus:rounded-lg transition px-0 py-0.5 border-b border-transparent focus:border-gray-600"
                                       placeholder="Category name" />
                            </td>

                            {{-- Color swatches --}}
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-1.5" x-data="{ open: false }">                                    {{-- Current color dot (click to open picker) --}}
                                    <button type="button" @click="open = !open"
                                            class="w-5 h-5 rounded-full border-2 border-white/20 hover:border-white/60 transition flex-shrink-0"
                                            :style="'background-color:' + color"
                                            title="Click to change color"></button>

                                    {{-- Preset swatches --}}
                                    <template x-for="preset in presets" :key="preset">
                                        <button type="button" @click="color = preset; save()"
                                                class="w-5 h-5 rounded-full transition hover:scale-110 border-2"
                                                :style="'background-color:' + preset"
                                                :class="color === preset ? 'border-white' : 'border-transparent'">
                                        </button>
                                    </template>

                                    {{-- Custom color picker (hidden input, opens on dot click) --}}
                                    <div x-show="open" @click.outside="open = false" class="relative">
                                        <input type="color" x-model="color" @change="save(); open = false"
                                               class="w-6 h-6 rounded cursor-pointer border-0 bg-transparent p-0"
                                               title="Custom color" />
                                    </div>
                                </div>
                            </td>

                            {{-- Products count --}}
                            <td class="px-4 py-2.5">
                                <span class="text-gray-400 text-sm">{{ $category->products_pivot_count }}</span>
                                <span class="text-gray-600 text-xs ml-1">{{ $category->products_pivot_count === 1 ? 'product' : 'products' }}</span>
                            </td>

                            {{-- Delete --}}
                            <td class="px-3 py-2.5">
                                <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                      onsubmit="return confirmDelete(this)">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1 text-gray-600 hover:text-red-400 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach

                        {{-- New inline rows (Alpine-managed) --}}
                        <template x-for="(row, index) in newRows" :key="row.key">
                            <tr class="border-b border-gray-800/60 bg-orange-500/5">
                                <td class="px-3 py-2.5">
                                    <svg class="w-4 h-4 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm8-12a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                                    </svg>
                                </td>
                                <td class="px-4 py-2.5">
                                    <input type="text" x-model="row.name"
                                           x-ref="newInput"
                                           @keydown.enter="saveNew(row)"
                                           @blur="saveNew(row)"
                                           class="w-full bg-transparent text-white text-sm focus:outline-none border-b border-gray-600 py-0.5"
                                           placeholder="Category name" />
                                </td>
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-1.5">
                                        <button type="button"
                                                class="w-5 h-5 rounded-full border-2 border-white/20 flex-shrink-0"
                                                :style="'background-color:' + row.color"></button>
                                        <template x-for="preset in presets" :key="preset">
                                            <button type="button" @click="row.color = preset"
                                                    class="w-5 h-5 rounded-full transition hover:scale-110 border-2"
                                                    :style="'background-color:' + preset"
                                                    :class="row.color === preset ? 'border-white' : 'border-transparent'">
                                            </button>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5">
                                    {{-- count shown after save --}}
                                </td>
                                <td class="px-3 py-2.5">
                                    <button type="button" @click="removeNew(index)"
                                            class="p-1 text-gray-600 hover:text-red-400 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SortableJS for drag & drop --}}
    <script src="{{ asset('assets/js/sortable.min.js') }}"></script>

    <script>
    function categoriesPage() {
        return {
            newRows: [],
            presets: ['#22c55e', '#ef4444', '#a855f7', '#f59e0b', '#3b82f6', '#ec4899'],

            init() {
                const tbody = document.getElementById('categories-tbody');
                Sortable.create(tbody, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'opacity-30',
                    onEnd: () => {
                        const order = [...tbody.querySelectorAll('.category-row')]
                            .map(r => r.dataset.id);
                        fetch('{{ route('categories.reorder') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            },
                            body: JSON.stringify({ order }),
                        });
                    }
                });
            },

            addRow() {
                this.newRows.push({ key: Date.now(), name: '', color: '#6366f1' });
                this.$nextTick(() => {
                    const inputs = document.querySelectorAll('[x-ref="newInput"]');
                    if (inputs.length) inputs[inputs.length - 1].focus();
                });
            },

            removeNew(index) {
                this.newRows.splice(index, 1);
            },

            saveNew(row) {
                if (!row.name.trim() || row._saving) return;
                row._saving = true;
                fetch('{{ route('categories.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ name: row.name, color: row.color }),
                }).then(r => {
                    if (r.ok) window.location.reload();
                });
            },
        }
    }

    function categoryRow(id, name, color, isNew) {
        return {
            id, name, color, isNew,
            presets: ['#22c55e', '#ef4444', '#a855f7', '#f59e0b', '#3b82f6', '#ec4899'],
            _saveTimer: null,

            save() {
                if (!this.name.trim()) return;
                clearTimeout(this._saveTimer);
                this._saveTimer = setTimeout(() => {
                    fetch(`/categories/${this.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'X-HTTP-Method-Override': 'PUT',
                        },
                        body: JSON.stringify({ name: this.name, color: this.color, _method: 'PUT' }),
                    });
                }, 400);
            },
        }
    }
    </script>
</x-app-layout>
