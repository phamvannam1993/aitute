<template>
    <div class="mt-4 px-4 md:px-8">
        <div v-for="(post, index) in posts" :key="index" class="mb-8">
            <div class="flex items-start space-x-2 md:space-x-4">
                <div class="relative w-1/10">
                    <img src="/assets/svgs/facebook.svg" class="w-8 h-8" alt="icon">
                    <img src="/assets/svgs/upload.svg" class="absolute -top-1 -left-1 w-4 h-4" alt="icon">
                </div>
                <div class="hidden md:flex flex-col flex-1 border-[1px] border-[#999999] rounded-[10px] bg-white">
                    <div class="flex items-center space-x-2 pt-4 px-4">
                        <img :src="'/assets/svgs/accountExample1.svg'" class="w-10 h-10 border-[1px] border-[#34A753] rounded-full" alt="icon">
                        <div class="flex flex-col">
                            <span class="text-[12px] font-semibold text-[#545252]">{{ post.account.name }}</span>
                            <span class="text-[10px] font-semibold text-[#666666]">{{ calculateTimeAgo(post.createdAt) }}</span>
                        </div>
                    </div>
                    <div class="">
                        <div class="p-4">
                            <span class="text-[12px] mb-2 text-black">Nội dung bài đăng</span>
                            <p class="text-[12px] text-black">{{ post.content }}</p>
                        </div>
                        <div>
                            <img :src="post.image || '/assets/svgs/postImage.svg'" class="w-full h-auto cursor-pointer" alt="icon">
                        </div>
                    </div>
                </div>
                <div class="hidden md:flex flex-col w-3/10 space-x-2 border-[1px] border-[#999999] rounded-[10px] bg-white p-2">
                    <div class="flex items-center">
                        <img :src=" '/assets/svgs/accountExample1.svg'"  class="w-10 h-10 border-[1px] border-[#34A753] rounded-full" alt="icon">
                        <input type="text" 
                            @focus="setFocus(index, true)"
                            class="w-full h-full text-[#545252] text-[12px] border-none outline-none focus:outline-none focus:ring-0 focus:border-transparent"
                            placeholder="Bình luận...">
                    </div>
                    <div v-if="focusedPosts[index]?.isFocused" class="flex items-center mt-2 justify-between">
                        <div class="flex items-center space-x-1">
                            <button @click="toggleStatus(index)" 
                                class="relative w-[23px] h-[11px] flex items-center bg-gray-300 rounded-full p-[1px] transition-colors duration-300"
                                :class="{'bg-green-500': focusedPosts[index]?.isInternal}">
                                <div class="w-[9px] h-[9px] bg-white rounded-full shadow-md transform          transition-transform duration-300"
                                :class="{'translate-x-3': focusedPosts[index]?.isInternal, 'translate-x-0': !focusedPosts[index]?.isInternal}"></div>
                            </button>
                            <span class="text-[8px]" :class="focusedPosts[index]?.isInternal ? 'text-green-600' : 'text-gray-600'">
                                Nội bộ
                            </span>
                            <img src="/assets/svgs/atIcon.svg" class="w-2 h-2" alt="mention">
                            <img src="/assets/svgs/attachmentIcon.svg" class="w-2 h-2" alt="attachment">
                        </div>
                        <button class="bg-[#092A99] font-medium text-white text-[8px] rounded-[2px] p-1">
                            Đăng
                        </button>
                    </div>
                    
                </div>
                <div class="flex flex-col flex-1 md:hidden">
                    <div class="flex flex-col border-[1px] border-[#999999] rounded-[10px] bg-white">
                    <div class="flex items-center space-x-2 pt-4 px-4">
                        <img :src="post.account.avatar || '/assets/svgs/accountExample1.svg'" class="w-10 h-10 border-[1px] border-[#34A753] rounded-full" alt="icon">
                        <div class="flex flex-col">
                            <span class="text-[12px] font-semibold text-[#545252]">{{ post.account.name }}</span>
                            <span class="text-[10px] font-semibold text-[#666666]">{{ calculateTimeAgo(post.createdAt) }}</span>
                        </div>
                    </div>
                    <div class="">
                        <div class="p-4">
                            <span class="text-[12px] mb-2 text-black">Nội dung bài đăng</span>
                            <p class="text-[12px] text-black">{{ post.content}}</p>
                        </div>
                        <div>
                            <img :src="post.image || '/assets/svgs/postImage.svg'" class="w-full h-auto cursor-pointer" alt="icon">
                        </div>
                    </div>
                </div>
                <div class="flex flex-col mt-2 space-x-2 border-[1px] border-[#999999] rounded-[10px] bg-white p-2">
                    <div class="flex items-center">
                        <img :src="'/assets/svgs/accountExample1.svg'" class="w-10 h-10 border-[1px] border-[#34A753] rounded-full" alt="icon">
                        <input type="text" 
                            @focus="setFocus(index, true)"
                            class="w-full h-full text-[#545252] text-[12px] border-none outline-none focus:outline-none focus:ring-0 focus:border-transparent"
                            placeholder="Bình luận...">
                    </div>
                    <div v-if="focusedPosts[index]?.isFocused" class="flex items-center mt-2 justify-between">
                        <div class="flex items-center space-x-1">
                            <button @click="toggleStatus(index)" 
                                class="relative w-[23px] h-[11px] flex items-center bg-gray-300 rounded-full p-[1px] transition-colors duration-300"
                                :class="{'bg-green-500': focusedPosts[index]?.isInternal}">
                                <div class="w-[9px] h-[9px] bg-white rounded-full shadow-md transform          transition-transform duration-300"
                                :class="{'translate-x-3': focusedPosts[index]?.isInternal, 'translate-x-0': !focusedPosts[index]?.isInternal}"></div>
                            </button>
                            <span class="text-[8px]" :class="focusedPosts[index]?.isInternal ? 'text-green-600' : 'text-gray-600'">
                                Nội bộ
                            </span>
                            <img src="/assets/svgs/atIcon.svg" class="w-2 h-2" alt="mention">
                            <img src="/assets/svgs/attachmentIcon.svg" class="w-2 h-2" alt="attachment">
                        </div>
                        <button class="bg-[#092A99] font-medium text-white text-[8px] rounded-[2px] p-1">
                            Đăng
                        </button>
                    </div>
                    
                </div>
                </div>
            </div>
        </div>
        
    </div> 
</template>

<script setup> 
import { ref, onMounted } from 'vue'

const posts = ref([]);
const accounts = ref([]);

const focusedPosts = ref([]);


const calculateTimeAgo = (createdAt) => {
    const now = new Date();
    const timeDifference = now - new Date(createdAt);
    const seconds = Math.floor(timeDifference / 1000);
    if (seconds < 60) return `${seconds} giây trước`;
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes} phút trước`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours} giờ trước`;
    const days = Math.floor(hours / 24);
    return `${days} ngày trước`;
};


const setFocus = (index, isFocused) => {
    if (!focusedPosts.value[index]) {
        focusedPosts.value[index] = { isFocused: false, isInternal: false };
    }
    focusedPosts.value[index].isFocused = isFocused;
};

const toggleStatus = (index) => {
    if (!focusedPosts.value[index]) {
        focusedPosts.value[index] = { isFocused: false, isInternal: false };
    }
    focusedPosts.value[index].isInternal = !focusedPosts.value[index].isInternal;
};


const fetchData = async () => {
    try {
        const [postsResponse, accountsResponse] = await Promise.all([
            fetch('https://668792af0bc7155dc01828b0.mockapi.io/Posts?AccountId=1'),
            fetch('https://668792af0bc7155dc01828b0.mockapi.io/Accounts')
        ]);
        
        posts.value = await postsResponse.json();
        accounts.value = await accountsResponse.json();

        posts.value = posts.value.map(post => ({
            ...post,
            account: accounts.value.find(acc => acc.id === post.AccountId) || {}
        }));
        console.log(posts.value)
    } catch (error) {
        console.error('Error fetching data:', error);
    }
};

onMounted(() => {
    fetchData();
});

</script>