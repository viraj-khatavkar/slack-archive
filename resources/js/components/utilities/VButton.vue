<script setup>

</script>

<template>
    <button
        @click.prevent="$emit('submit-button-clicked')"
        type="button"
        class="rounded-md bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-100"
    >
        <slot>
            Load More
        </slot>
    </button>
</template>

<style scoped>

</style>
