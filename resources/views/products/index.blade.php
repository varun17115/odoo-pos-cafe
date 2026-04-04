<x-app-layout>
    <x-slot name="title">Products</x-slot>

    <div class="min-h-screen p-6" x-data="productsTable()">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-white text-lg font-semibold">Products</h1>
            <div class="flex items-center gap-2">
                {{-- Bulk action dropdown (visible when rows selected) --}}
                <div x-show="selected.length > 0" x-transition class="flex items-center gap-2">
                    <span class="text-xs text-gray-400" x-text="selected.length + ' selected'"></span>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-medium rounded-lg border border-gray-700 transition">
                            Action
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                             class="absolute right-0 top-full mt-1 w-36 bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden z-20">
                            <form method="POST" action="{{ route('products.bulk-delete') }}" id="bulk-delete-form">
                                @csrf @method('DELETE')
                                <template x-for="id in selected">
                                    <input type="hidden" name="ids[]" :value="id">
                                </template>
                                <button type="submit" onclick="return confirm('Delete selected products?')"
                                        class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-400 hover:bg-gray-700 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <a href="{{ route('products.create') }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 bg-orange-500 hover:bg-orange-400 text-white text-xs font-semibold rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    New
                </a>
            </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
        <div class="mb-4 px-4 py-2.5 bg-green-500/10 border border-green-500/30 text-green-400 text-sm rounded-xl">
            {{ session('success') }}
        </div>
        @endif

        {{-- Table --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="w-10 px-4 py-3">
                            <input type="checkbox" @change="toggleAll($event)"
                                   class="w-3.5 h-3.5 rounded border-gray-600 bg-gray-800 text-orange-500 focus:ring-orange-500 focus:ring-offset-gray-900" />
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UOM</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variants</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="w-16 px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-800/50 transition group" :class="selected.includes({{ $product->id }}) ? 'bg-orange-500/5' : ''">
                        <td class="px-4 py-3">
                            <input type="checkbox" :value="{{ $product->id }}" x-model="selected"
                                   class="w-3.5 h-3.5 rounded border-gray-600 bg-gray-800 text-orange-500 focus:ring-orange-500 focus:ring-offset-gray-900" />
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                {{-- Thumbnail --}}
                                <div class="w-8 h-8 rounded-lg bg-gray-800 flex-shrink-0 overflow-hidden">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <span class="text-white font-medium">{{ $product->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-300">₹{{ number_format($product->price, 2) }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $product->tax > 0 ? $product->tax.'%' : '—' }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $product->unit ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @if($product->variants->count())
                                <div class="flex flex-wrap gap-1">
                                    @foreach($product->variants as $variant)
                                    @php
                                        $price = $variant->price + $product->price;
                                    @endphp
                                    <span class="px-2 py-0.5 bg-gray-800 text-gray-400 text-xs rounded-md border border-gray-700">
                                        {{ $variant->name }}@if($variant->price) <span class="text-orange-400">₹{{ number_format($price, 2)   }}</span>@endif
                                    </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-600">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($product->categories->count())
                                <div class="flex flex-wrap gap-1">
                                    @foreach($product->categories as $cat)
                                    <span class="px-2 py-0.5 text-xs font-medium text-white rounded-md"
                                          style="background-color: {{ $cat->color }}">
                                        {{ $cat->name }}
                                    </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-600">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                                <a href="{{ route('products.edit', $product) }}"
                                   class="p-1.5 text-gray-500 hover:text-white hover:bg-gray-700 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}"
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
                        <td colspan="8" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <p class="text-gray-500 text-sm">No products yet</p>
                                <a href="{{ route('products.create') }}" class="text-orange-500 hover:text-orange-400 text-xs transition">Add your first product</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function productsTable() {
        return {
            selected: [],
            toggleAll(e) {
                if (e.target.checked) {
                    this.selected = [{{ $products->pluck('id')->join(', ') }}];
                } else {
                    this.selected = [];
                }
            }
        }
    }
    </script>
</x-app-layout>
