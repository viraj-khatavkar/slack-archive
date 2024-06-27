<template>
    <div class="mt-4">
        <h1 class="sr-only">Recent messages</h1>
        <ul role="list" class="space-y-4">
            <li v-for="message in messages" :key="message.id" class="bg-white px-4 py-6 shadow sm:rounded-lg sm:p-6">
                <MessageDetails :message="message" @open-thread="openThread" />
            </li>
        </ul>

        <MessageThread
            v-if="selectedMessage"
            :message="selectedMessage"
            :open="openModal"
            @close-modal="closeThread"
        />

        <div class="mt-4 text-center">
            <VSpinner v-if="loadingMessages" />
            <VButton
                @submit-button-clicked="fetchMessages(currentPage + 1)"
                v-if="lastPage > currentPage && !loadingMessages"
            >
                Load More Messages
            </VButton>
        </div>
    </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import MessageDetails from "./MessageDetails.vue";
import MessageThread from "./MessageThread.vue";
import VButton from "./utilities/VButton.vue";
import VSpinner from "./utilities/VSpinner.vue";

const props = defineProps({
    channel: {
        type: Object,
        required: true
    },
})

const messages = ref([]);
const loadingMessages = ref(false);

onMounted(() => {
    fetchMessages();
});

function fetchMessages(page = 1) {
    loadingMessages.value = true;
    axios.get(`/api/messages/${props.channel.name}?page=${page}`).then(response => {
        response.data.data.forEach(message => {
            messages.value.push(message);
        });
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
        loadingMessages.value = false;
    });
}

const openModal = ref(false);
const selectedMessage = ref(null);
const currentPage = ref(1);
const lastPage = ref(null);
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
