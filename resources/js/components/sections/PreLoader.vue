<template>
    <!-- A full-screen overlay with a spinner centered -->
    <div class="fixed inset-0 flex items-center justify-center bg-white z-50">
        <!-- Spinner using Tailwind's animate-spin -->
        <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>
</template>

<script>
export default {
    name: 'Preloader'
}
</script>
