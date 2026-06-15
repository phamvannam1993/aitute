<template>
    <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96 text-center">
            <h2 class="text-xl font-semibold text-gray-800">Chức năng đang bảo trì</h2>
            <p class="text-gray-600 mt-2">Vui lòng thử lại sau.</p>

            <div class="mt-4">
                <a :href="route('home.index')" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">Quay lại trang chủ</a>
            </div>
        </div>
    </div>
</template>

<script setup>
</script>