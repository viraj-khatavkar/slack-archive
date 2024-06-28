import './bootstrap';
import {createApp} from "vue";
import AppLayout from "./components/AppLayout.vue";
import MessageList from "./components/MessageList.vue";
import Search from "./components/Search.vue";

const app = createApp({})

app.component('app-layout', AppLayout);
app.component('message-list', MessageList);
app.component('search', Search);

app.mount('#app');

