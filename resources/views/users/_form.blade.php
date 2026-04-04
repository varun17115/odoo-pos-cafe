<div>
    <label class="block text-xs text-gray-500 mb-1.5">Full Name</label>
    <input type="text" name="name" x-model="form.name" required
           class="w-full px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
           placeholder="e.g. John Doe" />
    @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-xs text-gray-500 mb-1.5">Email</label>
    <input type="email" name="email" x-model="form.email" required
           class="w-full px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
           placeholder="staff@restaurant.com" />
    @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-xs text-gray-500 mb-1.5">Role</label>
    <div class="relative">
        <select name="role" x-model="form.role"
                class="w-full px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 appearance-none pr-8">
            @if(!isset($isCreate) || !$isCreate)
            <option value="admin">👑 Admin</option>
            @endif
            <option value="cashier">💳 Cashier</option>
            <option value="chef">👨‍🍳 Chef</option>
        </select>
        <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>
</div>
<div>
    <label class="block text-xs text-gray-500 mb-1.5">
        Password {{ isset($isCreate) && !$isCreate ? '(leave blank to keep current)' : '' }}
    </label>
    <input type="password" name="password" x-model="form.password"
           {{ isset($isCreate) && $isCreate ? 'required' : '' }}
           class="w-full px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
           placeholder="Min. 8 characters" />
    @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
</div>
