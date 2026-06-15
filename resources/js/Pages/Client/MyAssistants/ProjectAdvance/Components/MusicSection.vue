<template>
    <div class="flex flex-col flex-1" >
        <div class="w-full flex flex-col">
            <div class="border-t border-gray-300 my-2 md:my-8"></div>
            <div class="" v-if="formData.overview.length > 0"  v-for='(item, index) in formData.overview' :key="index">
                <div class="flex flex-col gap-2 mt-4 w-full">
                    <div v-if="item.type === 'text'" class="flex flex-col gap-1">
                        <label class="text-[14px] font-semibold text-black">{{ index + 1 }}. {{ item.label }}</label>
                        <input v-model="item.value" class="w-full border border-gray-300 rounded-2xl p-3 text-[14px] text-black" />
                    </div>
                    <div v-else-if="item.type === 'textarea'" class="flex flex-col gap-1">
                        <label class="text-[14px] font-semibold text-black">{{ index + 1 }}. {{ item.label }}</label>
                        <textarea v-model="item.value" class="w-full border border-gray-300 rounded-2xl p-3 text-[14px] text-black"></textarea>
                    </div>
                    <div v-else-if="item.type === 'select'" class="flex flex-col gap-1 md:flex-row items-center">
                        <label class="text-[14px] font-semibold text-black w-full">{{ index + 1 }}. {{ item.label }}</label>
                        <select v-model="item.value" class="w-full max-w-96 border border-gray-300 rounded-2xl p-3 text-[14px] text-black">
                            <option v-for="(option, index) in item.options" :key="index" :value="option.value">{{ option.label }}</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="flex items-center justify-center gap-5 mt-7">
                <button :disabled="formData?.overview?.isLoading" type="button" @click="handleButtonClick('overview')"
                        class="w-full max-w-[180px] px-4 py-3 bg-aiwow-primary text-white rounded-2xl font-bold">
                    <span v-if="!formData?.big_ideas?.isLoading">
                        Xác nhận
                    </span>
                    <LoadingCircle v-else size="!size-6" />
                </button>
            </div>
        </div>
        <div class="w-full  flex flex-col" v-if="formData.big_ideas.length > 0">
            <div class="border-t border-gray-300 my-2 md:my-8"></div>
            <div class="flex items-center gap-3 mt-4">
                <img src="/assets/img/icon_ai.png" alt="step" class="w-[24px] h-auto" />
                <span class="text-[15px] lg:text-[18px] leading-none font-bold text-black">Lựa chọn Big Idea</span>
            </div>
            <div class="flex flex-col gap-8 border border-gray-300 rounded-2xl p-3 md:p-8 mt-6">
                <div class="flex items-start gap-4 md:gap-6" v-for='(item, index) in formData.big_ideas' :key="index">
                    <div @click="toggleBigIdeaMusic(index)" class="flex gap-2 items-center flex-shrink-0">
                        <div :class="item.isActive ? 'border-aiwow-sec' : 'border-gray-400'"
                             class="size-6 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all">
                            <div class="size-[15px] bg-aiwow-sec rounded-full" v-if="item.isActive">
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col flex-1 gap-3">
                        <p @click="toggleBigIdeaMusic(index)" class="font-bold text-[15px] leading-none text-sm text-black" :class="{ 'text-aiwow-sec': item.isActive }">
                            Big idea {{ index + 1}}:"{{ item.y_tuong }}"
                        </p>
                        <p class="text-sm font-normal text-black" @click="toggleBigIdeaMusic(index)">
                            {{ item.content }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-center gap-5 my-5 ">
                <button type="button" class="w-full max-w-[180px] px-4 py-3 border border-gray-300 text-gray-500 rounded-xl text-sm lg:text-base lg:rounded-2xl font-bold">Huỷ</button>
                <button
                    @click="handleButtonClick('lyric')"
                    :disabled="formData?.lyric?.isLoading"
                    type="button" class="w-full max-w-[180px] px-4 py-3 bg-aiwow-primary text-white rounded-xl text-sm lg:text-base lg:rounded-2xl font-bold">
                    <LoadingCircle v-if="formData?.lyric?.isLoading" size="!size-6" />
                    <span v-else>Xác nhận</span>
                </button>
            </div>
        </div>
        <div class="flex flex-col flex-1" v-if="formData?.lyric?.length > 0">
            <div class="border-t border-gray-300 my-2 md:my-8"></div>
            <div class="flex items-center gap-3 my-4">
                <img src="/assets/img/icon_ai.png" alt="step" class="w-[24px] h-auto" />
                <span class="text-[15px] lg:text-[18px] leading-none font-bold text-black">Tạo lời bài hát (Lyric)</span>
            </div>
            <div class="" v-for='(item, index) in formData.lyric' :key="index">
                <div class="flex flex-col gap-2 mt-4 w-full">
                    <div v-if="item.type === 'text'" class="flex flex-col gap-1">
                        <label class="text-[14px] font-semibold text-black">{{index+1}}. {{ item.label }}</label>
                        <input v-model="item.value" class="w-full border border-gray-300 rounded-2xl p-3 text-[14px] text-black" />
                    </div>
                    <div v-else-if="item.type === 'textarea'" class="flex flex-col gap-1">
                        <label class="text-[14px] font-semibold text-black">{{index+1}}. {{ item.label }}</label>
                        <textarea v-model="item.value" class="w-full border border-gray-300 rounded-2xl p-3 text-[14px] text-black"></textarea>
                    </div>
                    <div v-else-if="item.type === 'select'" class="flex flex-col gap-1 md:flex-row items-center">
                        <label class="text-[14px] font-semibold text-black w-full">{{index+1}}. {{ item.label }}</label>
                        <select v-model="item.value" class="w-full max-w-96 border border-gray-300 rounded-2xl p-3 text-[14px] text-black">
                            <option v-for="(option, index) in item.options" :key="index" :value="option.value">{{ option.label }}</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="flex items-center justify-center gap-5 mt-7">
                <button type="button" class="w-full max-w-[180px] px-4 py-3 border border-gray-300 text-gray-500 rounded-2xl font-bold">Huỷ</button>
                <button
                    @click="handleButtonClick('result')"
                    type="button" class="w-full max-w-[180px] px-4 py-3 bg-aiwow-primary text-white rounded-2xl font-bold">
                    <LoadingCircle size="!size-6" v-if="loading" />
                    <span v-else>
                        Xác nhận
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
    import LoadingCircle from '@/Components/LoadingCircle.vue';
    import { ref, watch, onMounted } from 'vue';
    const emit = defineEmits([]);
    const props = defineProps({
        open: Boolean,
        data: Object,
        loading: Boolean,
    })

    const toggleBigIdeaMusic = (index) => {
        for(var i = 0; i < formData.value.big_ideas.length; i++) {
            formData.value.big_ideas[i].isActive = false
        }
        formData.value.big_ideas[index].isActive = true
    }

    const formData = ref({
        overview:[
            {
                "name": "goal",
                "label": "Mục tiêu chiến dịch",
                "type": "select",
                "options": [
                    {
                        "value": "Mục đích bài hát này là: Bán hàng. Hãy tạo ra 1 bài hát để có được mục đích cuối là nhấn mạnh lợi ích và giá trị của sản phẩm/dịch vụ, tạo động lực để khách hàng quyết định mua ngay.",
                        "label": "Bán hàng"
                    },
                    {
                        "value": "Mục đích bài hát này là: Giới thiệu thương hiệu. Hãy tạo ra 1 bài hát để có được mục đích cuối là làm nổi bật giá trị cốt lõi và hình ảnh thương hiệu, giúp khách hàng ghi nhớ.",
                        "label": "Giới thiệu thương hiệu"
                    },
                    {
                        "value": "Mục đích bài hát này là: Tạo tương tác. Hãy tạo ra 1 bài hát để có được mục đích cuối là khuyến khích khách hàng tham gia bình luận, chia sẻ hoặc thảo luận về sản phẩm/dịch vụ.",
                        "label": "Tạo tương tác"
                    },
                    {
                        "value": "Mục đích bài hát này là: Tăng nhận diện thương hiệu. Hãy tạo ra 1 bài hát để có được mục đích cuối là làm nổi bật thương hiệu với phong cách độc đáo, dễ nhớ.",
                        "label": "Tăng nhận diện thương hiệu"
                    },
                    {
                        "value": "Mục đích bài hát này là: Khuyến khích sử dụng dịch vụ. Hãy tạo ra 1 bài hát để có được mục đích cuối là nhấn mạnh sự tiện lợi và lợi ích khi khách hàng chọn dịch vụ này.",
                        "label": "Khuyến khích sử dụng dịch vụ"
                    },
                    {
                        "value": "Mục đích bài hát này là: Kích thích tò mò. Hãy tạo ra 1 bài hát để có được mục đích cuối là khơi dậy sự tò mò của khách hàng, khiến họ muốn tìm hiểu thêm về sản phẩm/dịch vụ.",
                        "label": "Kích thích tò mò"
                    },
                    {
                        "value": "Mục đích bài hát này là: Chào mừng sự kiện. Hãy tạo ra 1 bài hát để có được mục đích cuối là để kỷ niệm hoặc quảng bá cho một sự kiện đặc biệt của thương hiệu.",
                        "label": "Chào mừng sự kiện"
                    },
                    {
                        "value": "Mục đích bài hát này là: Cảm ơn khách hàng. Hãy tạo ra 1 bài hát để có được mục đích cuối là thể hiện lòng biết ơn sâu sắc đối với khách hàng đã đồng hành cùng thương hiệu.",
                        "label": "Cảm ơn khách hàng"
                    },
                    {
                        "value": "Mục đích bài hát này là: Truyền cảm hứng. Hãy tạo ra 1 bài hát để có được mục đích cuối là mang đến động lực và niềm tin tích cực cho khách hàng.",
                        "label": "Truyền cảm hứng"
                    },
                    {
                        "value": "Mục đích bài hát này là: Giải trí. Hãy tạo ra 1 bài hát để có được mục đích cuối là với nội dung vui nhộn, nhẹ nhàng, tạo cảm giác thoải mái cho người đọc.",
                        "label": "Giải trí"
                    }
                ]
            },
            {
                "name": "so_luong",
                "label": "Số lượng ý tưởng",
                "type": "select",
                "options": [
                    {
                        "value": "1",
                        "label": "1"
                    },
                    {
                        "value": "2",
                        "label": "2"
                    },
                    {
                        "value": "3",
                        "label": "3"
                    },
                    {
                        "value": "4",
                        "label": "4"
                    },
                    {
                        "value": "5",
                        "label": "5"
                    },
                    {
                        "value": "6",
                        "label": "6"
                    },
                    {
                        "value": "7",
                        "label": "7"
                    },
                    {
                        "value": "8",
                        "label": "8"
                    },
                    {
                        "value": "9",
                        "label": "9"
                    },
                    {
                        "value": "10",
                        "label": "10"
                    }
                ]
            }
        ],
        big_ideas:[],
        lyric:[]
    })

    const handleButtonClick = (type) => {
        if(type == "overview") {
            var goal = formData.value.overview[0].value
            var so_luong = formData.value.overview[1].value
            formData.value.big_ideas.isLoading = true
            emit("getFormBigIdeas", goal, so_luong)
        } else if(type == "lyric") {
            var y_tuong = ""
            for(var i = 0; i < formData.value.big_ideas.length; i++) {
                if(formData.value.big_ideas[i].isActive) {
                    y_tuong = formData.value.big_ideas[i].y_tuong+":"+formData.value.big_ideas[i].content
                }
            }
            formData.value.lyric.isLoading = true
            emit("getFormLyric", y_tuong)
        } else if(type == "result") {
            const data = {};
            formData.value.lyric.forEach(item => {
                data[item.name] = item.value;
            });
            loading.value = true
            emit("submitForm", data)
        }
    }
    const loading = ref(false)
    const updateData = (data, isSuccess = false) => {
        if(isSuccess) {
            loading.value = false
            return
        }
        if(data.overview) {
            formData.value.overview = data.overview
        }
        if(data.big_ideas) {
            formData.value.lyric.overview = true
            formData.value.big_ideas = data.big_ideas
        }
        if(data.lyric) {
            formData.value.big_ideas.overview = true
            formData.value.lyric = data.lyric
        }
    }
    defineExpose({ updateData });
</script>

<style scoped></style>
