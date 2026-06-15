<template>
  <div class="px-2 overflow-y-auto top-16 left-0 h-[calc(100vh-64px)] z-100">
    <nav class="flex flex-col items-start py-2 ">
      <SidebarItem
        v-for="(item, index) in navItems"
        :key="index"
        :icon="item.icon"
        :text="item.text"
        :active="item.active"
        @click="setActive(index)"
      />
    </nav>
  </div>
</template>
  
  <script setup>
  import { reactive } from 'vue';
  import SidebarItem from './SidebarItems.vue'; 


  const navItems = reactive([
    { icon: '/assets/svgs/home-icon.svg', text: 'Trang chủ', active: true },
    { icon: '/assets/svgs/chat-icon.svg', text: 'Chat', active: false },
    { icon: '/assets/svgs/image-icon.svg', text: 'Hình ảnh', active: false },
    { icon: '/assets/svgs/job-icon.svg', text: 'Ngành nghề', active: false },
    { icon: '/assets/svgs/task-icon.svg', text: 'Nghiệp vụ', active: false },
    { icon: '/assets/svgs/ai-new-icon.svg', text: 'AI mới', active: false },
    { icon: '/assets/svgs/favorite-icon.svg', text: 'Yêu thích', active: false },
    { icon: '/assets/svgs/popular-icon.svg', text: 'Nhiều người yêu thích', active: false },
    { icon: '/assets/svgs/timeline-icon.svg', text: 'Mốc thời gian', active: false },
    { icon: '/assets/svgs/featured-icon.svg', text: 'Nổi bật', active: false },
    { icon: '/assets/svgs/influencer-icon.svg', text: 'Người ảnh hưởng', active: false },
    { icon: '/assets/svgs/news-icon.svg', text: 'Bản tin', active: false },
    { icon: '/assets/svgs/forum-icon.svg', text: 'Diễn đàn', active: false },
    { icon: '/assets/svgs/request-icon.svg', text: 'Yêu cầu', active: false },
    { icon: '/assets/svgs/connect-icon.svg', text: 'Kết nối với chúng tôi', active: false },
    { icon: '/assets/svgs/setting-icon.svg', text: 'Tài khoản', active: false },
  ]);

const setActive = (index) => {
  navItems.forEach((item, i) => {
    item.active = i === index;
  });
};
</script>