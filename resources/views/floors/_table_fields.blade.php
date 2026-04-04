<div>
    <label class="block text-xs text-gray-500 mb-1.5">Table Number</label>
    <input type="text" name="number" x-model="tableForm.number" required
           class="w-full px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
           placeholder="e.g. 101" />
</div>
<div>
    <label class="block text-xs text-gray-500 mb-1.5">Seats</label>
    <input type="number" name="seats" x-model="tableForm.seats" min="1" max="50" required
           class="w-full px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" />
</div>
<div>
    <label class="block text-xs text-gray-500 mb-1.5">Status</label>
    <div class="relative">
        <select name="status" x-model="tableForm.status"
                class="w-full px-3 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 appearance-none pr-8">
            <option value="vacant">Vacant</option>
            <option value="occupied">Occupied</option>
            <option value="reserved">Reserved</option>
            <option value="inactive">Inactive</option>
        </select>
        <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>
</div>
