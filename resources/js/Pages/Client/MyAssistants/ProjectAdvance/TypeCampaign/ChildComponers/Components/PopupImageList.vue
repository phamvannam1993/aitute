<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" :class="props.is_show ? '':'hidden'">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[700px] xl:w-[1000px] rounded-[40px] relative" >
            <div class="">
                <div class="flex flex-col lg:flex-row md:justify-end md:items-end lg:items-center mt-4 flex-wrap lg:flex-nowrap lg:justify-between">
                    <h2 class="text-black font-bold text-2xl mb-4"> Kho ảnh mẫu </h2>
                    <img src="/assets/img/close2.png" class="cursor-pointer absolute top-2 right-5" @click="closePopup" />
                    <label v-if="typeImage != 1" class="relative h-[40px] mb-5 cursor-pointer flex flex-row text-sm lg:text-base px-4 rounded-lg lg:rounded-2xl font-bold line-h-40">
                        <button @click="isShowOption = !isShowOption" class="w-full h-ful border-[#d6d6d6] px-4 border-2  font-bold py-2.5 text-xs text-black lg:text-sm rounded-2xl lg:rounded-2xl text-center flex items-center gap-2 justify-center">
                             {{typeImage == 2 ? 'Ảnh sản phẩm đã tải' : 'Ảnh bối cảnh'}}
                             <svg data-v-8e5f379b="" class="rotate-0" width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path data-v-8e5f379b="" d="M1 1L5 5L9 1" stroke="#C5C5C5"></path></svg>
                        </button>
                        <div class="absolute z-10 right-0 w-52 bg-white shadow-xl rounded-2xl p-2 py-4 space-y-2 mt-10" v-if="isShowOption">
                            <div @click="showImageList(2)" class="flex items-center cursor-pointer text-xs md:text-sm px-4 py-2 rounded-md text-black">
                                <p>Ảnh sản phẩm đã tải</p>
                            </div>
                            <div @click="showImageList(3)" class="flex items-center cursor-pointer text-xs md:text-sm px-4 py-2 rounded-md text-black">
                                <p>Ảnh bối cảnh</p>
                            </div>
                        </div>
                    </label>
                    <input id="upload-image" type="file" class="hidden">
                </div>
                 <div class="flex w-full border-b-[3px] border-[#d6d6d6] mb-5 px-5"></div>
            </div>

            <div class="relative">
                <div class="w-fit overflow-y-auto h-[60vh] lg:h-[70vh] mb-2 relative text-center">
                    <div class="grid grid-cols-3 lg:grid-cols-5 gap-2 lg:gap-5 w-full">
                        <div @click="selectedImage(image.s3_url)" v-for="image in images" :key="image.id" class="relative rounded-xl cursor-pointer">
                            <img :src="image.s3_url" :alt="image.id" class="w-full h-full rounded-lg object-cover" />
                            <input type="radio" :value="image.s3_url" :checked="imageSelect == image.s3_url ? true : false" class="absolute top-2 checked:bg-[#0e68ef] checked:bg-orion-orange hover:checked:bg-orion-orange checked:text-orion-orange checked:ring-orion-orange lg:top-4 right-2 lg:right-4 cursor-pointer outline-none size-6" />
                        </div>
                    </div>
                    <button v-if="nextPage" @click="loadMore" class="h-[40px] md:h-[50px] min-w-[80%] lg:min-w-[200px] text-sm lg:text-base px-4 bg-ai3goc-bgclr text-white rounded-lg lg:rounded-2xl font-bold">Xem thêm</button>
                </div>
            </div>
            <div class="flex items-center gap-4 md:gap-8 justify-center mt-6">
                <button @click="confirmImage" class="h-[40px] md:h-[50px] min-w-[80%] lg:min-w-[360px] text-sm lg:text-base px-4 bg-ai3goc-bgclr text-white rounded-lg lg:rounded-2xl font-bold">
                   Xác nhận
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineEmits } from 'vue';

const props = defineProps({
    message: String,
    isError:Boolean,
    is_show:Boolean,
    type: String
});

const emit  = defineEmits();

const closePopup = () => {
    emit('close');
};

const confirmVideo = () => {
    emit('confirmVideo');
};

const showModalHistory = ref(false);
const showHistory = () => {
    showModalHistory.value = true;
}
const langValue = ref("")
const nextPage = ref(null)
const images = ref([])
const typeImage = ref(1)
const imageSelect = ref(null)
const isShowOption = ref(false)
const showImage = ref(false)
const selectedImage = (imageUrl) => {
    imageSelect.value = imageUrl
}

const selectOptionLang = async (value) => {
  langValue.value = value
}

const confirmImage = () => {
    emit('showCreateImage', imageSelect.value);
    showImage.value = false
}

const showImageList = async (type = 2, isLoadMore = false) => {
    if(isLoadMore) {
        const response = await axios.get(nextPage.value, {});
        const dataImage = response.data.data.data
        for(var i = 0; i < dataImage.length; i++) {
            images.value.push(dataImage[i])
        }
        nextPage.value = response.data.data.next_page_url
        return
    }
    var url = route("ai-image.api-ai-image-history")
    nextPage.value = ""
    if(type == 2) {
        url = route("ai-business.product-image-list")
    }
    if(type == 3) {
        url = route("ai-background.api-history")
    }
    const response = await axios.get(url, {});
    images.value = response.data.data.data
    nextPage.value = response.data.data.next_page_url
    showImage.value = true
    typeImage.value = type
    isShowOption.value = false
}
showImageList(1)
const loadMore = async () => {
    if(nextPage.value) {
        showImageList(0, nextPage.value)
    }
}

const getS3URL = async (file, type = 1) => {
    const formData = new FormData();
    formData.append("image_file", file);
    var url = route("ai-business.product-image-upload")
    if(type == 1) {
        url = route("short-video.uploadImageToS3")
    }
    try {
        const response = await axios.post(url, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        if (response.data.success) {
            return response.data.s3_url
        } else {
            return ""
        }
    } catch (err) {
        return ""
    }
}
</script>
