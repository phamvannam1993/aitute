<template>

    <Head title="Quản lý social - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div ref="dropdownContainer">
        <Header @open="toggleDropdown" @display="displayComponent" :togglePosting="togglePosting" />

        <PostContent v-if="displayPostContent" />

        <Calendar v-if="displayCalendar" :togglePosting="togglePosting" :events="events" />

        <Filter v-if="openDropdown === 'filter'" @close="toggleDropdown('filter')" />
        <div v-if="isPosting"
            class="fixed z-50 top-0 left-0 right-0 bottom-0 flex items-center justify-center bg-[#0000004D]">
            <PostingPlan :date="postingDate" :togglePosting="togglePosting" :postingImageUrl="postingImageUrl"
                :postingContent="postingContent" :activePage="activePage" />
        </div>
    </div>
    <Footer />
</template>

<script setup>
import Layout from "@/Layouts/Client/ClientLayout.vue";
import { Head } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import Footer from '../Home/Components/Footer.vue';
import PostingPlan from '../PageManagement/Component/PostingPlan.vue';
import Calendar from "./Calendar.vue";
import Filter from "./Filter.vue";
import Header from "./Header.vue";
import PostContent from "./PostContent.vue";

defineOptions({ layout: Layout });
const props = defineProps({
    socialPosts: Array,
    fanpages: Array
})

const activePage = ref(props.fanpages[0] || '')
console.log(activePage.value)

const isPast = (dateStr) => {
    const now = new Date();
    const date = new Date(dateStr);

    return date < now;
};
const events = ref([]);
const getEventStyle = (status) => {
    const styles = {
        'Published': {
            color: 'green',
            className: 'event-text-green'
        },
        'Scheduled': {
            color: 'blue',
            className: 'event-text-blue'
        },
        'Failed': {
            color: 'red',
            className: 'event-text-red'
        },
        'Retrying': {
            color: 'orange',
            className: 'event-text-yellow'
        }
    }

    return styles[status] ?? {};
}

const formatEvents = () => {
    events.value = props.socialPosts.map(post => ({
        title: post.content,
        date: post.scheduled_at,
        allDay: false,
        ...getEventStyle(post.status),
    }));
};
formatEvents()



const openDropdown = ref(null);
const dropdownContainer = ref(null);

const displayCalendar = ref(false);
const displayPostContent = ref(true);

const isPosting = ref(false);

const postingDate = ref(null);
const togglePosting = (date) => {
    postingDate.value = date;
    isPosting.value = !isPosting.value;
    if (!isPosting.value) {
        postingImageUrl.value = null; 
    }
}

const postingImageUrl = ref('');
const postingContent = ref('');

const displayComponent = (component) => {
    displayCalendar.value = component === 'calendar';
    displayPostContent.value = component === 'post';
};

const toggleDropdown = (dropdown) => {
    if (openDropdown.value && openDropdown.value !== dropdown) {
        openDropdown.value = null;
    }

    openDropdown.value = openDropdown.value === dropdown ? null : dropdown;
};

const handleClickOutside = (event) => {
    if (dropdownContainer.value && !dropdownContainer.value.contains(event.target)) {
        openDropdown.value = null;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);

    postingImageUrl.value = localStorage.getItem('imageUrl');
    if (postingImageUrl.value) {
        isPosting.value = true;
        localStorage.removeItem('imageUrl');
    }
    postingContent.value = localStorage.getItem('postContent');
    if (postingContent.value) {
        isPosting.value = true;
        localStorage.removeItem('postContent');
    }
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
