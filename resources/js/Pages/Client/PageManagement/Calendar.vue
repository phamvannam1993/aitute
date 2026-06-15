<template>
    <div class="bg-white my-4 mx-2 border-[1px] border-[#ddd] p-2 rounded-lg h-full text-black">
        <div class="max-h-[calc(100vh-200px)] overflow-y-auto">
            <FullCalendar :options="calendarOptions" />
        </div>
        <!-- <NewPostForm v-if="isNewPostFormOpen" @close="closeNewPostForm" /> -->
    </div>
</template>


<script setup>
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import FullCalendar from '@fullcalendar/vue3';
import { defineEmits, onMounted, ref } from 'vue';
const isNewPostFormOpen = ref(false);

const props = defineProps({
  togglePosting: Function,
  events: Array
});

const emit = defineEmits();

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
  // Configure the locale to Vietnamese
  locale: 'vi',
  dayCellDidMount: function (info) {
    const today = new Date().setHours(0, 0, 0, 0);
    const cellDate = new Date(info.date).setHours(0, 0, 0, 0);

    if (cellDate >= today) {
      const button = document.createElement('button');
      button.innerHTML = `
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-size">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>
          <span class="hidden md:block">Thêm bài viết</span>
        `;
      button.classList.add('new-post-btn');
      button.addEventListener('click', () => {
          openNewPostForm(cellDate)
      });
      info.el.querySelector('.fc-daygrid-day-frame').appendChild(button);

      const dayTop = info.el.querySelector('.fc-daygrid-day-top');
      if (dayTop) {
        const topHeight = dayTop.offsetHeight;
        button.style.top = `${topHeight}px`;
      }
    }
  },
  titleFormat: { year: 'numeric', month: '2-digit', day: '2-digit' },

  datesSet: function(dateInfo) {
    const startDate = dateInfo.start;
    const endDate = dateInfo.end;

    const formattedStartDate = `${startDate.getDate().toString().padStart(2, '0')}/${(startDate.getMonth() + 1).toString().padStart(2, '0')}`;
    const formattedEndDate = `${endDate.getDate().toString().padStart(2, '0')}/${(endDate.getMonth() + 1).toString().padStart(2, '0')}/${endDate.getFullYear()}`;


    document.querySelector('.fc-toolbar-title').innerText = `${formattedStartDate} - ${formattedEndDate}`;
  },
    events: props.events,
    eventClick: function(info) {
        console.log(info.event, info);
    }

});

function formatTimestamp(timestamp) {
    const date = new Date(timestamp);

    // Format hours and minutes
    const currentDateTime = new Date();
    const hours = currentDateTime.getHours().toString().padStart(2, '0'); // "h"
    const minutes = currentDateTime.getMinutes().toString().padStart(2, '0'); // "i"

    // Format year, month, and day
    const year = date.getFullYear(); // "Y"
    const month = (date.getMonth() + 1).toString().padStart(2, '0'); // "m" (months are 0-indexed)
    const day = date.getDate().toString().padStart(2, '0'); // "d"

    // Return formatted string
    return `${year}/${month}/${day} ${hours}:${minutes}`;
}

const openNewPostForm = (date) => {
  const formattedDate = formatTimestamp(date);
  props.togglePosting(formattedDate)
};

const closeNewPostForm = () => {
  isNewPostFormOpen.value = false;
};

onMounted(() => {
  document.querySelectorAll('.new-post-btn').forEach(button => {
    const dayTop = button.closest('.fc-day').querySelector('.fc-daygrid-day-top');
    if (dayTop) {
      const topHeight = dayTop.offsetHeight;
      button.style.top = `${topHeight}px`;
    }
  });
});
</script>
<style>
.fc-toolbar-chunk div{
  display: flex;
  align-items: center;
  justify-content: center;
}

.fc-toolbar-title {
  margin: 0 15px;
 font-weight: 400 !important;
 font-size: 14px !important;

}

.fc-prev-button {
  margin-right: 10px;
  border: none !important;
  width: 32px !important;
    padding-inline-start: 0 !important;
    padding-inline-end: 0 !important;
    align-items: center !important;
    display: flex !important;
    justify-content: center !important;
    font-size: 14px !important;
    line-height: 1.5714285714285714 !important;
    height: 32px !important;
    padding: 4px 15px !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    background: transparent !important;
    color: rgba(0, 0, 0, 0.88) !important;
}

.fc-next-button {
  margin-left: 10px;
  border: none !important;
  width: 32px !important;
    padding-inline-start: 0 !important;
    padding-inline-end: 0 !important;
    align-items: center !important;
    display: flex !important;
    justify-content: center !important;
    font-size: 14px !important;
    line-height: 1.5714285714285714 !important;
    height: 32px !important;
    padding: 4px 15px !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    background: transparent !important;
    color: rgba(0, 0, 0, 0.88) !important;
}

.fc-today-button {
    text-transform: capitalize !important;
    height: 32px !important;
    padding: 5px 16px !important;
    background: #ffffff !important;
    border-color: #d9d9d9 !important;
    color: rgba(0, 0, 0, 0.88) !important;
    box-shadow: 0 2px 0 rgba(0, 0, 0, 0.02) !important;
    border-radius: 4px !important;
    font-size: 14px !important;
    cursor: pointer !important;
    opacity: 1 !important;
}
.fc-today-button:hover {
    border-color: #4096ff !important;
    color: #4096ff !important;
}

.fc-button-group {
    background-color: #f5f6f7 !important;
    padding: 2px !important;
    color: rgba(0, 0, 0, 0.65) !important;
    font-size: 14px !important;
    line-height: 1.5714285714285714 !important;
    border-radius: 6px !important;
    transition: all 0.2s cubic-bezier(0.645, 0.045, 0.355, 1) !important;
}

.fc-dayGridMonth-button  {
    text-transform: capitalize !important;
    background-color: #f5f6f7 !important;
    color: rgba(0, 0, 0, 0.65) !important;
    min-height: 28px !important;
    line-height: 28px !important;
    padding: 0 11px !important;
    overflow: hidden !important;
    white-space: nowrap !important;
    text-overflow: ellipsis !important;
    border: none !important;
    cursor: pointer !important;

}

.fc-dayGridWeek-button  {
    text-transform: capitalize !important;
    background-color: #f5f6f7 !important;
    color: rgba(0, 0, 0, 0.65) !important;
    min-height: 28px !important;
    line-height: 28px !important;
    padding: 0 11px !important;
    overflow: hidden !important;
    white-space: nowrap !important;
    text-overflow: ellipsis !important;
    border: none !important;
    cursor: pointer !important;

}
.fc-button-primary:focus {
    box-shadow: none !important;
}
.fc-button-active {
    background-color: #ffffff !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03), 0 1px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px 0rgba(0, 0, 0, 0.02) !important;
    color: #232426 !important;
    border-radius: 4px !important;
    transition: all 0.2s cubic-bezier(0.645, 0.045, 0.355, 1) !important;
}

.fc-day {
   position: relative !important;
}
.new-post-btn {
  display: none;
  position: absolute;
  z-index: 20;
  left: 50%;
  top: 100% !important;
  transform: translate(-50%, calc(-100% - 10px));
  background-color: #fff;
  color: #999;
  border-radius: 8px;
  cursor: pointer;
  width: 90%;
  height: 20%;
  border: 1px dashed #d4d4d4;
  font-size: 12px;
  justify-content: center;
  align-items: center;
  gap: 5px;
  /* padding: 8px 0px; */
}

.new-post-btn .icon-size {
  width: 16px;
  height: 16px;
}

.fc-day:hover .new-post-btn {
  display: flex;
}

.new-post-btn:hover {
  border-color: #26cf7a;
  color: #26cf7a;
}

.fc-day.fc-day-past {
  background-color: #f9f9f9 !important;
}

@media (max-width: 768px) {
  .fc-toolbar-title {
    font-size: 12px !important;
  }

  .new-post-btn {
    font-size: 10px;
  }

  .fc-prev-button,
  .fc-next-button {
    width: 28px !important;
    height: 28px !important;
    font-size: 12px !important;
  }

  .fc-today-button {
    height: 28px !important;
    font-size: 12px !important;
  }

  .fc-dayGridWeek-button, .fc-dayGridMonth-button {
    height: 28px !important;
    font-size: 12px !important;
  }
}

.event-text-red {
    color: red;
}

.event-text-green {
    color: green;
}

.event-text-blue {
    color: blue;
}

.event-text-yellow {
    color: orange;
}
</style>
