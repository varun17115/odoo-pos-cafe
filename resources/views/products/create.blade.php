<x-app-layout>
    <x-slot name="title">New Product</x-slot>

    <div class="min-h-screen p-6">
        <div class="max-w-4xl mx-auto">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
                <a href="{{ route('products.index') }}" class="hover:text-white transition">Products</a>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-300">New Product</span>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
                <h1 class="text-white font-semibold text-lg mb-6">New Product</h1>
                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                    @csrf
                    @include('products._form')
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
