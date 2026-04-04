<div class="flex gap-3 pt-1">
    <button type="button" @click="modal = false"
            class="flex-1 py-2.5 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm font-medium rounded-xl transition">
        Cancel
    </button>
    <button type="submit"
            class="flex-1 py-2.5 bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold rounded-xl transition"
            x-text="editId ? 'Save Changes' : 'Create User'">
    </button>
</div>
