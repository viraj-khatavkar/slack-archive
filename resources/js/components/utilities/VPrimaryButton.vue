<script setup>

</script>

<template>
    <button
        @click.prevent="$emit('submit-button-clicked')"
        type="button"
        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
    >
        <slot>
            Submit
        </slot>
    </button>
</template>

<style scoped>

</style>
