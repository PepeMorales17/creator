<script setup>
import { Link } from '@inertiajs/inertia-vue3';
defineProps({
    withText: {
        default: true
    }
});
</script>
<template>
    <Link :href="route('dashboard')">
        <img
            alt="GrupoTermo"
            class="w-6"
            src="storage/images/logo.svg"
        />
        <span class="text-white text-lg ml-3" v-if="withText"> GrupoTermo </span>
    </Link>
</template>

