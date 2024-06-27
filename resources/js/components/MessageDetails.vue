<template>
    <article :aria-labelledby="'message-title-' + message.id">
        <div>
            <div class="flex space-x-3">
                <div class="flex-shrink-0">
                    <img class="h-10 w-10 rounded-full" :src="message.user.image_url" alt=""/>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900">
                        {{ message.user.name }}
                    </p>
                    <p class="text-sm text-gray-500">
                        <time :datetime="message.slack_timestamp">
                            
                            {{ formatDate(message.slack_timestamp, 'DD MMM YYYY, hh:mm A') }}
                        </time>
                    </p>
                </div>
            </div>
        </div>
        <div style="white-space: pre-wrap;" v-html="message.content" class="mt-4 space-y-4 text-sm text-gray-700 message-content-html">

        </div>
        <div class="mt-6 flex justify-between space-x-8">
            <div class="flex space-x-6">
                <span class="inline-flex items-center text-sm" v-if="message.children_count > 0 && showThreadCount">
                    <button
                        type="button"
                        class="inline-flex space-x-2 text-gray-400 hover:text-gray-500 border b-0 p-2"
                        @click.prevent="emitOpenThread"
                    >
                        <ChatBubbleLeftEllipsisIcon class="h-5 w-5" aria-hidden="true"/>
                        <span class="font-medium text-gray-900">{{ message.children_count }} replies</span>
                        <span class="sr-only">replies</span>
                    </button>
                </span>
            </div>
            <div class="flex text-sm">
                <span class="inline-flex items-center text-sm" v-if="showShareLink">
                    <button type="button" class="inline-flex space-x-2 text-gray-400 hover:text-gray-500">
                        <ShareIcon class="h-5 w-5" aria-hidden="true"/>
                        <span class="font-medium text-gray-900">Share</span>
                    </button>
                </span>
            </div>
        </div>
    </article>
</template>

<script setup>
import {ChatBubbleLeftEllipsisIcon, ShareIcon} from "@heroicons/vue/20/solid/index.js";
import formatDate from "../dater.js";

const props = defineProps({
    message: {
        type: Object,
        required: true
    },

    showThreadCount: {
        type: Boolean,
        default: true
    },

    showShareLink: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['open-thread'])

function emitOpenThread() {
    emit('open-thread', props.message)
}
</script>

<style>
.message-content-html a {
    color: #3182ce;
    text-decoration: underline;
}
</style>
