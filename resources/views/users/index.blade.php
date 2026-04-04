<x-app-layout>
    <x-slot name="title">Users</x-slot>

    <div class="min-h-screen p-6" x-data="usersPage()">

        {{-- Flash --}}
        @if(session('success'))
        <div class="mb-4 px-4 py-2.5 bg-green-500/10 border border-green-500/30 text-green-400 text-sm rounded-xl">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="mb-4 px-4 py-2.5 bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-xl">{{ session('error') }}</div>
        @endif

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-white text-xl font-semibold">Users</h1>
                <p class="text-gray-500 text-sm mt-0.5">Manage staff accounts and roles</p>
            </div>
            <button @click="openCreate()"
                    class="flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-400 text-white text-sm font-semibold rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                New User
            </button>
        </div>

        {{-- Users table --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        <th class="w-20 px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-800/40 transition group">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0
                                    {{ $user->role === 'admin' ? 'bg-orange-500' : ($user->role === 'chef' ? 'bg-purple-500' : 'bg-blue-500') }}">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $user->name }}</p>
                                    @if($user->id === auth()->id())
                                    <span class="text-xs text-gray-600">(you)</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-400">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            @php
                                $roleColors = ['admin' => 'bg-orange-500/20 text-orange-400 border-orange-500/30', 'cashier' => 'bg-blue-500/20 text-blue-400 border-blue-500/30', 'chef' => 'bg-purple-500/20 text-purple-400 border-purple-500/30'];
                                $roleIcons  = ['admin' => '👑', 'cashier' => '💳', 'chef' => '👨‍🍳'];
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-medium rounded-lg border {{ $roleColors[$user->role] }}">
                                {{ $roleIcons[$user->role] }} {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                                <button @click="openEdit({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->role }}')"
                                        class="p-1.5 text-gray-500 hover:text-white hover:bg-gray-700 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirmDelete(this)">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-500 hover:text-red-400 hover:bg-gray-700 rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Role legend --}}
        <div class="flex items-center gap-6 mt-4">
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <span class="px-2 py-0.5 bg-orange-500/20 text-orange-400 border border-orange-500/30 rounded-lg text-xs">👑 Admin</span>
                Full access to everything
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <span class="px-2 py-0.5 bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-lg text-xs">💳 Cashier</span>
                POS terminal access
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <span class="px-2 py-0.5 bg-purple-500/20 text-purple-400 border border-purple-500/30 rounded-lg text-xs">👨‍🍳 Chef</span>
                Kitchen display only
            </div>
        </div>

        {{-- Create Modal --}}
        <div x-show="modal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70">
            <div @click.outside="modal = false" x-transition class="w-full max-w-md bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl p-6">
                <h2 class="text-white font-semibold text-lg mb-5" x-text="editId ? 'Edit User' : 'New User'"></h2>

                {{-- Create form --}}
                <form x-show="!editId" method="POST" action="{{ route('users.store') }}" class="space-y-4">
                    @csrf
                    @include('users._form', ['isCreate' => true])
                    @include('users._modal_buttons')
                </form>

                {{-- Edit forms --}}
                @foreach($users as $user)
                <form x-show="editId === {{ $user->id }}" method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                    @csrf @method('PUT')
                    @include('users._form', ['isCreate' => false])
                    @include('users._modal_buttons')
                </form>
                @endforeach
            </div>
        </div>

    </div>

    <script>
    function usersPage() {
        return {
            modal: false,
            editId: null,
            form: { name: '', email: '', role: 'cashier', password: '' },

            openCreate() {
                this.editId = null;
                this.form = { name: '', email: '', role: 'cashier', password: '' };
                this.modal = true;
            },
            openEdit(id, name, email, role) {
                this.editId = id;
                this.form = { name, email, role, password: '' };
                this.modal = true;
            },
        }
    }
    </script>
</x-app-layout>
