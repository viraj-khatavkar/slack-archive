<template>
    <div>
        <div class="flex justify-end">
            <select
                v-model="sortDirection"
                id="sort-direction"
                class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                @change="fetchMessages(1)"
            >
                <option value="asc">Ascending</option>
                <option value="desc">Descending</option>
            </select>
        </div>

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
                <VLightButton
                    @submit-button-clicked="fetchMessages(currentPage + 1)"
                    v-if="lastPage > currentPage && !loadingMessages"
                >
                    Load More Messages
                </VLightButton>
            </div>
        </div>
    </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import MessageDetails from "./MessageDetails.vue";
import MessageThread from "./MessageThread.vue";
import VSpinner from "./utilities/VSpinner.vue";
import VLightButton from "./utilities/VLightButton.vue";

const props = defineProps({
    channel: {
        type: Object,
        required: true
    },
})

const messages = ref([]);
const loadingMessages = ref(false);
const sortDirection = ref('desc');

onMounted(() => {
    fetchMessages();
});

function fetchMessages(page = 1) {
    loadingMessages.value = true;

    if (page === 1) {
        messages.value = [];
    }

    axios.get(`/api/messages/${props.channel.name}`, {
        params: {
            page: page,
            sort_direction: sortDirection.value,
        }
    }).then(response => {
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
