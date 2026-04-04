<div x-data="productForm({{ json_encode(isset($product) ? $product->variants->map(fn($v) => ['name' => $v->name, 'price' => $v->price]) : []) }})">

    {{-- Tabs --}}
    <div class="flex border-b border-gray-800 mb-6">
        <button type="button" @click="tab = 'general'"
                :class="tab === 'general' ? 'text-white border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-300'"
                class="px-4 py-2.5 text-sm font-medium transition -mb-px">
            General Info
        </button>
        <button type="button" @click="tab = 'variant'"
                :class="tab === 'variant' ? 'text-white border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-300'"
                class="px-4 py-2.5 text-sm font-medium transition -mb-px">
            Variant
            <span x-show="variants.length > 0"
                  class="ml-1.5 px-1.5 py-0.5 bg-orange-500 text-white text-xs rounded-full"
                  x-text="variants.length"></span>
        </button>
    </div>

    {{-- ===== GENERAL INFO TAB ===== --}}
    <div x-show="tab === 'general'" class="space-y-5">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left col --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Product name --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Product Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                           class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                           placeholder="e.g. Burger with cheese" />
                    @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Category + Price row --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Categories</label>
                        <div x-data="categoryPicker({{ json_encode(isset($product) ? $product->categories->pluck('id') : []) }})"
                             class="relative">
                            <div class="min-h-[38px] w-full px-2 py-1.5 bg-gray-800 border border-gray-700 rounded-lg flex flex-wrap gap-1.5 cursor-pointer"
                                 @click="open = !open">
                                <template x-for="id in selected" :key="id">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium text-white"
                                          :style="'background-color:' + colorOf(id)">
                                        <span x-text="nameOf(id)"></span>
                                        <button type="button" @click.stop="remove(id)" class="hover:opacity-70">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </span>
                                </template>
                                <span x-show="selected.length === 0" class="text-gray-500 text-xs py-0.5 px-1">Select categories…</span>
                                <template x-for="id in selected" :key="'input-'+id">
                                    <input type="hidden" name="categories[]" :value="id" />
                                </template>
                            </div>
                            <div x-show="open" @click.outside="open = false" x-transition
                                 class="absolute z-30 top-full mt-1 w-full bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
                                @foreach($categories as $cat)
                                <button type="button" @click="toggle({{ $cat->id }})"
                                        class="w-full flex items-center gap-2.5 px-3 py-2 hover:bg-gray-700 transition text-left">
                                    <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $cat->color }}"></span>
                                    <span class="text-sm text-white flex-1">{{ $cat->name }}</span>
                                    <svg x-show="selected.includes({{ $cat->id }})" class="w-3.5 h-3.5 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                                @endforeach
                                @if($categories->isEmpty())
                                <p class="px-3 py-3 text-xs text-gray-500">No categories. <a href="{{ route('categories.index') }}" class="text-orange-400">Create one</a></p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Sale Price <span class="text-red-400">*</span></label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-3 flex items-center text-gray-500 text-sm">$</span>
                                <input type="number" name="price" step="0.01" min="0"
                                       value="{{ old('price', $product->price ?? '0.00') }}" required
                                       class="w-full pl-7 pr-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" />
                            </div>
                            {{-- UOM --}}
                            <div class="relative w-28">
                                <select name="unit"
                                        class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 appearance-none pr-7">
                                    <option value="">Unit</option>
                                    @foreach(['Piece','KG','Gram','Litre','ML','Pack','Dozen','Box'] as $u)
                                    <option value="{{ $u }}" {{ old('unit', $product->unit ?? '') === $u ? 'selected' : '' }}>{{ $u }}</option>
                                    @endforeach
                                </select>
                                <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        @error('price')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Tax --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Tax</label>
                        <div class="relative">
                            <select name="tax"
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 appearance-none pr-8">
                                <option value="0" {{ old('tax', $product->tax ?? 0) == 0 ? 'selected' : '' }}>No Tax</option>
                                <option value="5" {{ old('tax', $product->tax ?? 0) == 5 ? 'selected' : '' }}>5%</option>
                                <option value="10" {{ old('tax', $product->tax ?? 0) == 10 ? 'selected' : '' }}>10%</option>
                                <option value="15" {{ old('tax', $product->tax ?? 0) == 15 ? 'selected' : '' }}>15%</option>
                                <option value="18" {{ old('tax', $product->tax ?? 0) == 18 ? 'selected' : '' }}>18%</option>
                                <option value="25" {{ old('tax', $product->tax ?? 0) == 25 ? 'selected' : '' }}>25%</option>
                            </select>
                            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <p class="text-gray-600 text-xs mt-1">Drop down: 5%, 15%, 25%</p>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Product Description</label>
                    <textarea name="description" rows="3"
                              class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 resize-none"
                              placeholder="e.g. Burger with cheese">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
            </div>

            {{-- Right col: image --}}
            <div x-data="imageUpload('{{ isset($product) && $product->image ? Storage::url($product->image) : '' }}')" >
                <label class="block text-xs text-gray-500 mb-1">Product Image</label>
                <div class="aspect-square bg-gray-800 border-2 border-dashed border-gray-700 rounded-xl overflow-hidden flex items-center justify-center cursor-pointer hover:border-orange-500 transition relative"
                     @click="$refs.fileInput.click()"
                     @dragover.prevent
                     @drop.prevent="handleDrop($event)">
                    <img x-show="preview" :src="preview" class="w-full h-full object-cover" />
                    <div x-show="!preview" class="text-center p-4">
                        <svg class="w-8 h-8 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-gray-500 text-xs">Click or drag image</p>
                    </div>
                    <button x-show="preview" type="button" @click.stop="clearImage()"
                            class="absolute top-2 right-2 w-5 h-5 bg-red-500 hover:bg-red-400 text-white rounded-full flex items-center justify-center">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <input type="file" name="image" accept="image/*" x-ref="fileInput"
                       @change="handleFile($event)" class="hidden" />
                @error('image')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ===== VARIANT TAB ===== --}}
    <div x-show="tab === 'variant'">

        <div class="mb-3 flex items-center justify-between">
            <p class="text-xs text-gray-500">Define size/portion variants with optional price overrides.</p>
            <button type="button" @click="addVariant()"
                    class="flex items-center gap-1 px-3 py-1.5 bg-orange-500 hover:bg-orange-400 text-white text-xs font-medium rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                New
            </button>
        </div>

        <div class="bg-gray-800 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500">Attribute</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500">Value</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500">Unit</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500">Extra Price</th>
                        <th class="w-10 px-4 py-2.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    <template x-for="(variant, index) in variants" :key="index">
                        <tr>
                            {{-- Attribute (fixed: Size) --}}
                            <td class="px-4 py-2.5">
                                <input type="text" :name="`variants[${index}][attribute]`"
                                       x-model="variant.attribute"
                                       class="w-full px-2.5 py-1.5 bg-gray-700 border border-gray-600 rounded-lg text-white text-xs focus:outline-none focus:ring-1 focus:ring-orange-500"
                                       placeholder="Size" />
                            </td>
                            {{-- Value (e.g. 6, 8, 12) --}}
                            <td class="px-4 py-2.5">
                                <input type="text" :name="`variants[${index}][name]`"
                                       x-model="variant.name"
                                       class="w-full px-2.5 py-1.5 bg-gray-700 border border-gray-600 rounded-lg text-white text-xs focus:outline-none focus:ring-1 focus:ring-orange-500"
                                       placeholder="e.g. 6 inch" />
                            </td>
                            {{-- Unit dropdown --}}
                            <td class="px-4 py-2.5">
                                <div class="relative">
                                    <select :name="`variants[${index}][unit]`" x-model="variant.unit"
                                            class="w-full px-2.5 py-1.5 bg-gray-700 border border-gray-600 rounded-lg text-white text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 appearance-none pr-6">
                                        <option value="">Unit</option>
                                        <option value="KG">KG</option>
                                        <option value="Piece">Piece</option>
                                        <option value="Litre">Litre</option>
                                        <option value="MiliLiter">MiliLiter</option>

                                        <option value="Pack">Pack</option>
                                        <option value="Gram">Gram</option>
                                    </select>
                                    <svg class="absolute right-1.5 top-1/2 -translate-y-1/2 w-3 h-3 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </td>
                            {{-- Extra price --}}
                            <td class="px-4 py-2.5">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-2 flex items-center text-gray-500 text-xs">$</span>
                                    <input type="number" :name="`variants[${index}][price]`"
                                           x-model="variant.price"
                                           step="0.01" min="0"
                                           class="w-full pl-5 pr-2.5 py-1.5 bg-gray-700 border border-gray-600 rounded-lg text-white text-xs focus:outline-none focus:ring-1 focus:ring-orange-500"
                                           placeholder="0.00" />
                                </div>
                            </td>
                            <td class="px-4 py-2.5">
                                <button type="button" @click="removeVariant(index)"
                                        class="p-1 text-gray-600 hover:text-red-400 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="variants.length === 0">
                        <td colspan="5" class="px-4 py-8 text-center text-gray-600 text-xs">
                            No variants — click "New" to add size/portion options
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex items-center gap-3 pt-5 mt-5 border-t border-gray-800">
        <a href="{{ route('products.index') }}"
           class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm font-medium rounded-lg transition">
            Discard
        </a>
        <button type="submit"
                class="px-5 py-2 bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold rounded-lg transition">
            {{ isset($product) ? 'Save Changes' : 'Create Product' }}
        </button>
    </div>
</div>

<script>
function categoryPicker(existing) {
    const cats = @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'color' => $c->color]));
    return {
        open: false,
        selected: existing.map(Number),
        cats,
        toggle(id) { this.selected.includes(id) ? this.remove(id) : this.selected.push(id); },
        remove(id) { this.selected = this.selected.filter(s => s !== id); },
        nameOf(id) { return this.cats.find(c => c.id === id)?.name ?? ''; },
        colorOf(id) { return this.cats.find(c => c.id === id)?.color ?? '#6366f1'; },
    }
}

function productForm(existingVariants) {
    return {
        tab: 'general',
        variants: existingVariants.length
            ? existingVariants.map(v => ({ attribute: v.attribute || 'Size', name: v.name, unit: v.unit || '', price: v.price || '' }))
            : [],
        addVariant() {
            this.variants.push({ attribute: 'Size', name: '', unit: '', price: '' });
        },
        removeVariant(i) { this.variants.splice(i, 1); },
    }
}

function imageUpload(existing) {
    return {
        preview: existing || null,
        handleFile(e) {
            const file = e.target.files[0];
            if (file) this.preview = URL.createObjectURL(file);
        },
        handleDrop(e) {
            const file = e.dataTransfer.files[0];
            if (file) {
                this.$refs.fileInput.files = e.dataTransfer.files;
                this.preview = URL.createObjectURL(file);
            }
        },
        clearImage() {
            this.preview = null;
            this.$refs.fileInput.value = '';
        }
    }
}
</script>
