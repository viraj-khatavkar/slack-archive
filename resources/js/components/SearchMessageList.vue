<template>
    <div class="mt-4">
        <h1 class="sr-only">Recent messages</h1>
        <ul role="list" class="space-y-4">
            <li v-for="message in props.messages" :key="message.id" class="bg-white px-4 py-6 shadow sm:rounded-lg sm:p-6">
                <span class="mb-4 inline-flex items-center rounded-md bg-pink-50 px-2 py-1 text-xs font-medium text-pink-800 ring-1 ring-inset ring-pink-600/20">
                    #{{ message.channel.name }}
                </span>
                <MessageDetails
                    :message="message"
                    @open-thread="openThread"
                    :show-thread-count="true"
                    :show-open-thread-button="true"
                />
            </li>
        </ul>

        <MessageThread
            v-if="selectedMessage"
            :message="selectedMessage"
            :open="openModal"
            @close-modal="closeThread"
        />
    </div>
</template>

<script setup>
import {ref} from "vue";
import MessageDetails from "./MessageDetails.vue";
import MessageThread from "./MessageThread.vue";

const props = defineProps({
    messages: {
        type: Array,
        required: true
    },
})

const openModal = ref(false);
const selectedMessage = ref(null);
function openThread(message) {
    selectedMessage.value = message;
    openModal.value = true;
}

function closeThread() {
    openModal.value = false;
}

</script>

<style scoped>

</style>
