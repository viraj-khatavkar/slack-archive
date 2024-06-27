<template>
    <TransitionRoot as="template" :show="props.open">
        <Dialog class="relative z-10" @close="emitCloseModal">
            <div class="fixed inset-0" />

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                        <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
                            <DialogPanel class="pointer-events-auto w-screen max-w-2xl">
                                <div class="flex h-full flex-col overflow-y-scroll py-6 shadow-xl bg-gray-50">
                                    <div class="px-4 sm:px-6">
                                        <div class="flex items-start justify-between">
                                            <DialogTitle class="text-base font-semibold leading-6 text-gray-900">Thread</DialogTitle>
                                            <div class="ml-3 flex h-7 items-center">
                                                <button type="button" class="relative rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" @click="emitCloseModal">
                                                    <span class="absolute -inset-2.5" />
                                                    <span class="sr-only">Close panel</span>
                                                    <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative mt-6 flex-1 px-4 sm:px-6">
                                        <ul role="list" class="space-y-4">
                                            <li :key="props.message.id" class="bg-white px-4 py-6 shadow sm:rounded-lg sm:p-6">
                                                <MessageDetails
                                                    :message="props.message"
                                                    :show-thread-count="false"
                                                    :show-share-link="false"
                                                />
                                            </li>
                                            <li>
                                                <div class="relative">
                                                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                                        <div class="w-full border-t border-gray-300" />
                                                    </div>
                                                    <div class="relative flex justify-center">
                                                        <span class="bg-white px-3 text-base font-semibold leading-6 text-gray-900">Replies</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div v-if="loadingChildren" class="flex items-center">
                                                    <VSpinner />
                                                </div>
                                            </li>
                                            <li v-for="message in children" :key="message.id" class="bg-white px-4 py-6 shadow sm:rounded-lg sm:p-6">
                                                <MessageDetails :message="message" :show-share-link="false" />
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import {onMounted, ref, watch} from 'vue'
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from '@headlessui/vue'
import {ArrowPathIcon} from '@heroicons/vue/20/solid'
import {XMarkIcon} from '@heroicons/vue/24/outline'
import MessageDetails from "./MessageDetails.vue";
import VSpinner from "./utilities/VSpinner.vue";

const props = defineProps({
    open: {
        type: Boolean,
        required: true,
    },
    message: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits(['close-modal']);

function emitCloseModal() {
    emit('close-modal');
}

onMounted(() => {
    fetchThread(props.message);
})

const children = ref([]);
const loadingChildren = ref(false);

function fetchThread(message) {
    loadingChildren.value = true;
    // setTimeout(() => {
    //     axios.get(`/api/channels/${message.id}/children`).then(response => {
    //         children.value = response.data;
    //         loadingChildren.value = false;
    //     });
    // }, 5000);
    axios.get(`/api/messages/${message.id}/children`).then(response => {
        children.value = response.data;
        loadingChildren.value = false;
    });
}

watch(() => props.message, (message) => {
    children.value = [];
    fetchThread(message);
})

</script>
