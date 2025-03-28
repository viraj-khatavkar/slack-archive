<template>
    <div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full">
            <div>
                <label for="search" class="mb-2 block text-sm font-medium leading-6 text-gray-900">Search Text</label>
                <input
                    type="text"
                    v-model="search"
                    id="search"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    placeholder="Enter text to search"
                    @keyup.enter="searchMessages(1)"
                />
            </div>
            <div>
                <label for="from-date" class="mb-2 block text-sm font-medium leading-6 text-gray-900">From Date</label>
                <input
                    type="date"
                    id="from-date"
                    v-model="fromDate"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    @keyup.enter="searchMessages(1)"
                />
            </div>
            <div>
                <label for="from-date" class="mb-2 block text-sm font-medium leading-6 text-gray-900">To Date</label>
                <input
                    type="date"
                    id="to-date"
                    v-model="toDate"
                    @keyup.enter="searchMessages(1)"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                />
            </div>
            <div>
                <label for="channel-id" class="mb-2 block text-sm font-medium leading-6 text-gray-900">Channel</label>
                <select
                    id="channel-id"
                    v-model="channel_id"
                    @keyup.enter="searchMessages(1)"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                >
                    <option value="-1">
                        Any
                    </option>
                    <option v-for="channel in channels" :value="channel.id">
                        {{ channel.name }}
                    </option>
                </select>
            </div>
            <div>
                <label for="sort-by" class="mb-2 block text-sm font-medium leading-6 text-gray-900">Sort By</label>
                <select
                    id="sort-by"
                    v-model="sort_by"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                >
                    <option v-for="sortByOption in sortByOptions" :value="sortByOption.id">
                        {{ sortByOption.name }}
                    </option>
                </select>
            </div>
            <div>
                <label for="sort-direction" class="mb-2 block text-sm font-medium leading-6 text-gray-900">Sort Direction</label>
                <select
                    id="sort-direction"
                    v-model="sort_direction"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                >
                    <option value="asc">Asc</option>
                    <option value="desc">Desc</option>
                </select>
            </div>
        </div>

        <div class="flex justify-center mt-4">
            <VPrimaryButton class="px-8 mt-4" @submit-button-clicked="searchMessages(1)">Search</VPrimaryButton>
        </div>

        <SearchMessageList :messages="messages" />

        <div class="mt-4 text-center">
            <VSpinner v-if="searchingMessages" />
            <VLightButton
                @submit-button-clicked="searchMessages(currentPage + 1, false)"
                v-if="lastPage > currentPage && !searchingMessages"
            >
                Load More Messages
            </VLightButton>
        </div>
    </div>
</template>

<script setup>
import {inject, onMounted, ref} from "vue";
import SearchMessageList from "./SearchMessageList.vue";
import VSpinner from "./utilities/VSpinner.vue";
import VLightButton from "./utilities/VLightButton.vue";
import VPrimaryButton from "./utilities/VPrimaryButton.vue";

const props = defineProps({
    q: {
        type: String,
        required: false,
        default: '',
    },
});

const sortByOptions = [{id: 'slack_timestamp', name: 'Date'}, {id: 'children_count', name: 'Number of Replies'}];
const search = ref('');
const fromDate = ref('');
const toDate = ref('');
const messages = ref([]);
const searchingMessages = ref(false);
const currentPage = ref(1);
const lastPage = ref(null);
const channel_id = ref(-1);
const channels = inject('channels');
const sort_by = ref('slack_timestamp');
const sort_direction = ref('desc');

onMounted(() => {
    if (props.q.length > 0) {
        search.value = props.q;
        searchMessages(currentPage.value);
    }
});

function searchMessages(page, replaceMessages = true) {
    searchingMessages.value = true;

    if (replaceMessages) {
        messages.value = [];
    }

    axios.get(`/api/search`, {
        params: {
            q: search.value,
            from_date: fromDate.value,
            to_date: toDate.value,
            page: page,
            channel_id: channel_id.value,
            sort_by: sort_by.value,
            sort_direction: sort_direction.value,
        },
    }).then(response => {
        if (replaceMessages) {
            messages.value = response.data.data;
            // messages.value.forEach(message => {
            //     let regEx = new RegExp(search.value, 'ig');
            //     let replaceMask = `<span class="bg-yellow-100">${search.value}</span>`;
            //     message.content = message.content.replace(regEx, replaceMask);
            // });
        } else {
            response.data.data.forEach(message => {
                // let regEx = new RegExp(search.value, 'ig');
                // let replaceMask = `<span class="bg-yellow-100">${search.value}</span>`;
                // message.content = message.content.replace(regEx, replaceMask);
                messages.value.push(message);
            });
        }

        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
        searchingMessages.value = false;
    });
}
</script>

<style scoped>

</style>
