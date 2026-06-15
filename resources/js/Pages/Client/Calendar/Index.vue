<template>

  <Head :title="'Quản lý social'" />

  <Layout :breadcrumbs="'Quản lý social'">
    <div class="flex flex-col mb-4">
      <div class="flex gap-1">
        <div class="flex justify-start items-center gap-2">
          <div class="w-[52px] overflow-hidden rounded-full border-[1px] border-[#d4d4d4]">
                            <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
                        </div>
          <h2 class="text-black font-bold text-xl lg:text-xl 2xl:text-3xl leading-none">
            Quản lý Social
          </h2>
        </div>
      </div>
    </div>
    <Header @openPostForm="openPostForm" @openPostAI="openPostAI" :fanpages="fanpages" :facebooks="facebooks" />

    <div
      class="bg-white relative my-4 mx-2  border-[1px] border-[#ddd] md:p-5 p-2 rounded-3xl shadow-[0px_3px_3px_1px_#00000024] ">
      <div class="text-black">
        <FullCalendar :options="calendarOptions" class="custom-calendar">
          <template v-slot:eventContent='arg'>
            <div class="overflow-hidden">
              <li class="mb-1">
                <span @click="openPostByEvent(arg.event)">
                  {{ formatTimestampToYMD(arg.event.start, 'hh:ii') + ' ' +
                    arg.event.title }}
                </span>
                <span @click="deletePostByEvent(arg.event)"
                  class="absolute right-0 p-1 rounded bg-red-500 text-white hover:bg-red-400">X</span>
              </li>
            </div>
          </template>
        </FullCalendar>
      </div>

      <PostForm ref="postFormRef" @onSuccess="onSuccessPostForm" />

      <Modal :show="isShowAutoPostAIModal" maxWidth="xl-4xl" minWidth="w-full" @close="closeAutoPostAI">
        <AutoPostAI @closeAutoPostAI="closeAutoPostAI" />
      </Modal>

      <Loading v-if="pageData.is_loading" :position="'absolute'" />

    </div>
  </Layout>

</template>

<script setup>
import Modal from '@/Components/Modal.vue';
import Layout from "@/Layouts/Client/Layout.vue";
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import FullCalendar from '@fullcalendar/vue3';
import { usePage, Head } from "@inertiajs/vue3";
import { onMounted, provide, reactive, ref, watch } from 'vue';
import { toast } from "vue3-toastify";
import AutoPostAI from './components/AutoPostAI.vue';
import Header from './components/Header.vue';
import PostForm from '@/Components/ShareAiText/PostForm.vue';
import { inject } from 'vue'
import Loading from "@/Components/Loading.vue";

import './styles/Index.css';

defineProps({ fanpages: Array, facebooks: Array });

const helpers = inject('helpers')
const page = usePage();
const isShowAutoPostAIModal = ref(false);
const facebookFanpageIdActived = ref(null);
const facebookIdActived = ref(null);
const postFormRef = ref(null);
const pageData = reactive({
  is_loading: false,
});

const getCalendarForm = reactive({
  from: null,
  to: null,
  social_postable_id: facebookFanpageIdActived.value,
  social_postable_type: 'FacebookFanpage',
});

provide('facebookFanpageIdActived', facebookFanpageIdActived);
provide('facebookIdActived', facebookIdActived);

const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  editable: true,
  selectable: true,
  headerToolbar: {
    left: 'today',
    center: 'prev,title,next',
    right: 'dayGridMonth,dayGridWeek'
  },
  buttonText: {
    today: 'Hôm nay',
    month: 'Tháng',
    week: 'Tuần',
    day: 'Ngày',
  },
  locale: 'vi',
  dayCellDidMount: function (info) { },
  titleFormat: { year: 'numeric', month: '2-digit', day: '2-digit' },
  datesSet: function (dateInfo) {
    const startDate = dateInfo.start;
    const endDate = dateInfo.end;
    const formattedStartDate = helpers.formatTimestampToYMD(startDate, 'dd/mm/yyyy');
    const formattedEndDate = helpers.formatTimestampToYMD(endDate, 'dd/mm/yyyy');
    getCalendarForm.from = formattedStartDate
    getCalendarForm.to = formattedEndDate
    getCalendar()
  },
  events: [],
  eventClick: function (info) { }
});

watch(
  () => facebookFanpageIdActived.value,
  () => {
    getCalendar()
  },
);

onMounted(() => {
  showError();
  getAiImage();
});


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

const getEventDate = (socialPost) => {
  if (socialPost.status == 'Published') {
    return socialPost.published_at
  }

  return socialPost.scheduled_at
}

function formatTimestampToYMD(timestamp, format = 'yyyy/mm/dd hh:ii:ss') {
  let date = new Date();

  if (typeof timestamp === 'string') {
    date = new Date(timestamp);
  }

  if (typeof timestamp === 'object') {
    date = timestamp;
  }

  const mapObj = {
    yyyy: date.getFullYear(),
    mm: String(date.getMonth() + 1).padStart(2, '0'),
    dd: String(date.getDate()).padStart(2, '0'),
    hh: String(date.getHours()).padStart(2, '0'),
    ii: String(date.getMinutes()).padStart(2, '0'),
    ss: String(date.getSeconds()).padStart(2, '0'),
  };

  return format.replace(/\b(?:yyyy|mm|dd|hh|ii|ss)\b/gi, matched => mapObj[matched]);
}

const deletePostByEvent = (event = {}) => {
  let socialPost = event.extendedProps.socialPost
  if (confirm("Bạn có chắc chắn xóa bài viết?")) {
    submitDeletePost(socialPost)
  }
};

const openPostByEvent = (event = {}) => {
  let socialPost = event.extendedProps.socialPost
  let eventDate = getEventDate(socialPost);
  const dateToCompare = new Date(eventDate);
  const currentDate = new Date();
  let published = 1
  if (currentDate < dateToCompare) {
    published = 0
  }
  let files = [];

  if (socialPost.video) {
    files.push({
      's3_url': socialPost.video,
      'type': 'video',
    });
  }

  for (let index = 0; index < socialPost.medias.length; index++) {
    const element = socialPost.medias[index];
    files.push({
      's3_url': element,
      'type': 'image',
    });
  }

  openPostForm({
    id: socialPost.id,
    content: socialPost.content,
    published: published,
    scheduled_publish_time: eventDate,
    files: files,
  })
};

const openPostForm = (postForm = {}) => {
  if (!facebookFanpageIdActived.value) {
    toast.error("Vui lòng thêm fanpages để tiếp tục.");
    return false;
  }
  page.props.errors = [];
  postForm.social_postable_id = facebookFanpageIdActived.value;
  postForm.social_postable_type = "FacebookFanpage";
  if(!postForm.id) {
    postForm.scheduled_publish_time = ""
  } 
  postFormRef.value.openPostDetail(postForm);

};

const openPostAI = () => {
  if (!facebookFanpageIdActived.value) {
    toast.error('Vui lòng thêm fanpages để tiếp tục.');
    return false;
  }

  isShowAutoPostAIModal.value = true;
};

const closeAutoPostAI = () => {
  isShowAutoPostAIModal.value = false;
};

const getCalendar = async () => {
  try {
    getCalendarForm.social_postable_id = facebookFanpageIdActived.value
    const response = await axios.get(route('get-calendar', getCalendarForm))

    let events = response.data.map(socialPost => ({
      title: socialPost.content,
      date: getEventDate(socialPost),
      allDay: false,
      ...getEventStyle(socialPost.status),
      extendedProps: {
        socialPost
      }
    }));

    calendarOptions.value.events = [...events];

  } catch (error) {
    page.props.errors = error.response.data.errors
  } finally {

  }
};

const submitDeletePost = async (socialPost) => {
  try {
    setLoadding(true);
    await axios.post(route('social.ajaxDestroy', { id: socialPost.id, _method: 'DELETE' }))
    toast.success("Xóa thành công");
    getCalendar()
  } catch (error) {
    console.error(error)
    page.props.errors = error.response.data.errors
    toast.error('Xóa bài thất bại!');
  } finally {
    setLoadding(false);
  }
}

const getAiImage = () => {
  let aiImage = localStorage.getItem('aiImage');
  if (aiImage) {
    openPostForm({
      files: [JSON.parse(aiImage)],
    })
    localStorage.removeItem('aiImage');
  }
}

const showError = () => {
  if (page.props.flash?.data?.show_popup_error) {
    toast.error(page.props.errors?.message);
  }
}

const setLoadding = (status) => {
  pageData.is_loading = status;
}

const onSuccessPostForm = () => {
  getCalendar();
};
</script>
