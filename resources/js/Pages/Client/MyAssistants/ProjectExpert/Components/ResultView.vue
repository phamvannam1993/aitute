<!-- components/ResultView.vue -->
<template>
    <div ref="resultTarget">
        <div class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex items-center justify-between mt-4 flex-col lg:rounded-[35px]">
            <div class="flex items-center self-start justify-between w-full gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
                <Step
                    class="flex-shrink-0"
                    :step="7"
                    step-name="Xem kết quả và hiệu chỉnh"
                />
            </div>

            <div v-for="(item, index) in messagesMap['resultTarget']" :key="index" class="w-full lg:w-4/5 mt-10 md:mt-5 text-black text-xs lg:text-[15px] relative">
                <!-- Options/Filters -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-4">
                    <div class="relative flex flex-row lg:items-center gap-2 w-full">
                        <template v-for="(option, indexOptionRewrite) in item.options_rewrite" :key="indexOptionRewrite">
                            <!-- Regular Select Options -->
                            <div class="w-full" v-if="option.name !== 'Định dạng' && option.name !== 'keyword'">
                                <SelectRadix
                                    :disabled="item.isLoadingRewrite"
                                    :options="option.options"
                                    :selected="option.value"
                                    :placeholder="option.value || option.name"
                                    :handle-change="(value) => selectOption({ value: value, indexOptionRewrite: indexOptionRewrite, index: index })"
                                />
                            </div>

                            <!-- Keyword Option -->
                            <div v-if="option.name === 'keyword'" class="absolute -top-10 md:-top-[56px] right-24 md:right-8 flex items-center gap-2">
                                <DropdowRadix
                                    :disabled="item.isLoadingRewrite"
                                    :open="option.isOpenKeyword"
                                    @update:open="option.isOpenKeyword = $event"
                                >
                                    <template #trigger>
                                        <button type="button" class="flex items-center justify-between gap-2 w-full px-4 py-2 bg-white border border-gray-200 rounded-full shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orion-sec relative">
                                            <img src="/assets/svgs/edit-icon.svg" class="size-4" alt="" />
                                            <span class="text-xs lg:text-sm text-ai3goc-sec font-bold line-clamp-1">Từ khóa</span>
                                        </button>
                                    </template>
                                    <template #content>
                                        <div class="flex flex-col lg:mt-0 w-[270px] bg-white border border-gray-200 rounded-lg shadow-xl z-50 p-2 px-3">
                                            <textarea
                                                placeholder="Nhập từ khóa"
                                                v-model="option.value"
                                                class="border-none rounded-[10px] bg-gray-200 h-[66px] w-[243px]"
                                            />
                                            <div class="flex items-center justify-between h-[30px] mt-4">
                                                <button
                                                    type="button"
                                                    class="border border-gray-300 rounded-lg h-full px-3"
                                                    @click="() => {
                                                        option.isOpenKeyword = !option.isOpenKeyword;
                                                        option.value = '';
                                                    }"
                                                >
                                                    Huỷ
                                                </button>
                                                <button
                                                    type="button"
                                                    class="bg-ai3goc-primary rounded-lg text-white h-full px-3"
                                                    @click="() => {
                                                        option.isOpenKeyword = !option.isOpenKeyword;
                                                        rewriteContentProductAd(index);
                                                    }"
                                                >
                                                    Xác nhận
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </DropdowRadix>
                            </div>

                            <!-- Format Option -->
                            <div v-if="option.name === 'Định dạng'" class="absolute -top-10 md:-top-[56px] right-0 lg:-right-20 flex items-center gap-2">
                                <DropdowRadix
                                    :disabled="item.isLoadingRewrite"
                                    :open="option.isOpen"
                                    @update:open="option.isOpen = $event"
                                >
                                    <template #trigger>
                                        <button
                                            :disabled="item.isLoadingRewrite"
                                            type="button"
                                            class="flex w-full items-center justify-center px-4 py-2 bg-orion-sec rounded-2xl shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orion-sec relative"
                                            :class="{ 'bg-gray-300': item.isLoadingRewrite }"
                                        >
                                            <span class="text-xs lg:text-sm text-white font-bold line-clamp-1">Định dạng</span>
                                        </button>
                                    </template>
                                    <template #content>
                                        <ul class="flex flex-col lg:mt-0 min-w-[200px] w-full bg-white border border-gray-200 rounded-lg shadow-xl z-50 p-2 px-3">
                                            <li
                                                v-for="(optionItem, idx) in option.options"
                                                :key="idx"
                                                @click="selectOption({ value: optionItem, indexOptionRewrite: indexOptionRewrite, index: index })"
                                                class="px-4 py-2 cursor-pointer hover:bg-blue-50 hover:text-ai3goc-sec rounded-full"
                                                :class="{ 'bg-blue-100 text-ai3goc-sec': option.value === optionItem }"
                                            >
                                                {{ optionItem }}
                                            </li>
                                        </ul>
                                    </template>
                                </DropdowRadix>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="w-full">
                    <div class="flex flex-col gap-4">
                        <div class="flex justify-between items-center">
                            <p class="text-orion-primary text-[15px] font-bold">Nội dung đã tạo:</p>
                            <button
                                @click="openGradeModal(item)"
                                v-if="mode != 'basic'"
                                class="bg-orion-sec text-white py-2.5 text-xs lg:text-sm rounded-lg lg:rounded-2xl px-6 hover:bg-cyan-600 flex items-center gap-2"
                            >
                                <img src="/assets/svgs/grade.svg" class="size-5" alt="" />
                                Chấm điểm
                            </button>
                        </div>

                        <textarea
                            v-if="!item.isLoadingRewrite"
                            rows="10"
                            v-model="item.content"
                            class="text-black whitespace-pre-line text-base border border-gray-400 rounded-xl p-4 max-h-96 overflow-y-auto"
                        />
                        <div v-else class="w-full">
                            <LoadingCircle loading-title="Nội dung đang tạo..." />
                        </div>
                    </div>
                </div>

                <!-- Media Section -->
                <div v-if="!hiddenSocialMedia" class="flex flex-col lg:flex-row gap-4 mt-4">
                    <!-- Image Section -->
                    <div v-if="!hiddenImage" class="w-full">
                        <div class="flex justify-center gap-2 md:gap-4 mb-4 h-[40px]">
                            <button
                                @click="autoCreateImage"
                                :disabled="showImageGenerate"
                                class="w-[35%] sm:w-1/3 bg-orion-button-sec text-white py-2.5 text-xs font-bold lg:text-sm rounded-2xl lg:rounded-2xl text-center hover:bg-cyan-600 flex items-center gap-2 justify-center"
                            >
                                Tự động tạo ảnh {{ showImageGenerate ? '...' : '' }}
                            </button>

                            <label :id="`image_${index}`" class="w-[30%] sm:w-1/3 cursor-pointer bg-orion-button-sec text-white py-2 text-xs font-bold lg:text-sm rounded-2xl lg:rounded-2xl text-center hover:bg-cyan-600 flex items-center gap-2 justify-center">
                                <Upload size="16" color="#ffffff" class="hidden md:show" />
                                <img src="/assets/svgs/aiwow/uploadMedia.svg" alt="" />
                                Tải ảnh
                                <input
                                    :id="`image_${index}`"
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageUpload($event, index)"
                                    class="hidden"
                                />
                            </label>

                            <div class="relative w-[30%] sm:w-1/3 text-center">
                                <button
                                    @click="isGenerateImage = !isGenerateImage"
                                    class="w-full h-full bg-orion-button-sec text-white font-bold py-2.5 text-xs lg:text-sm rounded-2xl lg:rounded-2xl text-center hover:bg-cyan-600 flex items-center gap-2 justify-center"
                                >
                                    <img src="/assets/img/my_assistant/generate_image.png" class="hidden md:show size-5" alt="" />
                                    Tạo ảnh mới
                                </button>
                                <div v-if="isGenerateImage" class="absolute z-10 right-0 w-52 bg-white shadow-xl rounded-2xl p-2 py-4 space-y-2">
                                    <div
                                        @click="toggleGenerateImage('textToImage', index)"
                                        class="flex items-center cursor-pointer text-xs md:text-sm px-4 py-2 hover:bg-[#DEECFF] rounded-md"
                                    >
                                        <p>Tạo ảnh từ văn bản</p>
                                    </div>
                                    <div
                                        @click="toggleGenerateImage('background', index)"
                                        class="flex items-center cursor-pointer text-xs md:text-sm px-4 py-2 hover:bg-[#DEECFF] rounded-md"
                                    >
                                        <p>Hình phối cảnh</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image Grid -->
                        <div class="grid grid-cols-2 items-center justify-center w-full gap-2">
                            <div
                                v-for="(image, index_image) in item.images"
                                :key="index_image"
                                class="flex items-center h-full w-full rounded-xl overflow-hidden min-h-[120px]"
                            >
                                <div v-if="image?.imageRef || image?.previewImageUpload" class="relative m-auto">
                                    <input
                                        type="checkbox"
                                        :checked="checkImage(image)"
                                        @change="selectImage(image)"
                                        class="ml-0 top-2 right-2 absolute rounded-full"
                                    />
                                    <img
                                        v-if="image.imageRef"
                                        :src="image.imageRef.s3_url"
                                        alt="image generated by AI"
                                        class="rounded-lg lg:rounded-2xl bg-gray-200 object-contain aspect-[4/3] w-full"
                                    />
                                    <img
                                        v-else
                                        :src="image.previewImageUpload"
                                        alt="image generated by AI"
                                        class="rounded-lg lg:rounded-2xl bg-gray-200 object-contain aspect-[4/3] w-full"
                                    />
                                    <button
                                        type="button"
                                        @click="removeImage(index, index_image)"
                                        class="absolute bottom-2 right-2 bg-red-500 text-white w-6 h-6 rounded-full"
                                    >
                                        x
                                    </button>
                                </div>
                                <div v-else class="bg-[#E6E6E6] flex h-full items-center justify-center w-full rounded-xl">
                                    <img src="/assets/svgs/aiwow/image.svg" alt="loading" class="mx-auto w-16" />
                                </div>
                            </div>

                            <!-- Loading Placeholders -->
                            <div v-for="i in getFakeNumberImage(item)">
                                <label>
                                    <div
                                        v-if="autoCreaeImageLoading"
                                        class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden"
                                    >
                                        <input type="radio" disabled class="bg-gray-200 ml-0 top-2 right-2 absolute rounded-full" />
                                        <div class="flex flex-col items-center">
                                            <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                            <h3 class="text-white text-center">Hệ thống đang xử lý, xin đợi một chút</h3>
                                        </div>
                                    </div>
                                    <div
                                        v-else
                                        class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden"
                                    >
                                        <input type="radio" disabled class="bg-gray-200 ml-0 top-2 right-2 absolute rounded-full" />
                                        <img src="/assets/svgs/aiwow/image.svg" class="mx-auto w-24" />
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Video Section -->
                    <div v-if="!hiddenVideo" class="lg:w-1/2">
                        <div class="flex flex-col">
                            <div class="flex justify-center gap-4 mb-4 h-[40px]">
                                <label :id="`video_${index}`" class="bg-orion-primary text-white px-6 py-2 text-xs lg:text-sm rounded-lg lg:rounded-2xl flex items-center gap-2">
                                    <div class="p-1 rounded-full">
                                        <Upload size="16" color="#ffffff" />
                                    </div>
                                    Tải video
                                    <input
                                        type="file"
                                        accept="video/mp4,video/x-m4v,video/webm,video/ogg,video/*,.flv,.3gp"
                                        class="inputMedia hidden"
                                        @change="handleFileChange($event, index)"
                                    />
                                </label>
                                <button
                                    @click="scrollToCreateFormRef(index)"
                                    class="bg-orion-primary text-white px-4 py-2 text-xs lg:text-sm rounded-2xl flex items-center gap-2"
                                >
                                    <img src="/assets/img/my_assistant/generate_video.png" alt="" />
                                    Tạo video
                                </button>
                            </div>

                            <!-- Video Preview -->
                            <div class="flex items-center justify-center h-[256px] w-full">
                                <label class="text-xs lg:text-sm flex gap-1 items-center mb-1 w-full">
                                    <div class="flex gap-1 w-full items-center cursor-pointer">
                                        <input
                                            type="radio"
                                            v-model="uploadType"
                                            value="video"
                                            :checked="uploadType == 'video'"
                                            @change="uploadType = 'video'"
                                            class="ml-0 rounded-full"
                                        />
                                        <div class="w-full">
                                            <div
                                                v-if="item.videoRef || item.previewVideoUpload"
                                                class="bg-[#E6E6E6] flex h-[256px] items-center justify-center rounded-xl w-full object-cover"
                                            >
                                                <video
                                                    controls
                                                    v-if="item.videoRef"
                                                    :src="item.videoRef.s3_url"
                                                    alt="image generated by AI"
                                                    class="w-full h-full object-contain cursor-pointer rounded-xl"
                                                />
                                                <video
                                                    controls
                                                    v-else
                                                    :src="item.previewVideoUpload"
                                                    alt="image generated by AI"
                                                    class="w-full h-full object-contain cursor-pointer rounded-xl"
                                                />
                                            </div>
                                            <div v-else class="bg-[#E6E6E6] flex h-[256px] items-center justify-center rounded-xl w-full">
                                                <img src="/assets/img/aiwow/homepage/play_button.png" class="w-16" alt="" />
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Video Section -->
                <div class="mt-4" v-if="mode != 'basic' && selectedAnalysis != 'Nhạc Doanh nghiệp'">
                    <div class="flex flex-col">
                        <div class="flex justify-center gap-2 md:gap-4 mb-4 h-[40px]">
                            <button
                                @click="autoCreateVideo"
                                :disabled="showVideoGenerate"
                                class="w-[35%] sm:w-1/3 bg-orion-sec text-white py-2.5 font-bold text-xs lg:text-sm rounded-2xl lg:rounded-2xl text-center hover:bg-cyan-600 flex items-center gap-2 justify-center"
                            >
                                Tự động tạo video
                            </button>
                            <label :id="`video_${index}`" class="w-[30%] sm:w-1/3 cursor-pointer bg-orion-sec hover:bg-cyan-600 font-bold text-white text-center py-2 text-xs lg:text-sm rounded-2xl lg:rounded-2xl flex items-center gap-2 justify-center">
                                <div class="p-1 rounded-full hidden md:show">
                                    <Upload size="16" color="#ffffff" />
                                </div>
                                <img src="/assets/svgs/aiwow/uploadMedia.svg" alt="" />
                                Tải video
                                <input
                                    type="file"
                                    accept="video/mp4,video/x-m4v,video/webm,video/ogg,video/*,.flv,.3gp"
                                    class="inputMedia hidden"
                                    @change="handleFileChange($event, index)"
                                />
                            </label>
                            <button
                                @click="scrollToCreateFormRef(index)"
                                class="w-[30%] sm:w-1/3 bg-orion-sec hover:bg-cyan-600 font-bold text-white text-center py-2 text-xs lg:text-sm rounded-2xl flex items-center gap-2 justify-center"
                            >
                                <img src="/assets/img/my_assistant/generate_video.png" alt="" class="hidden md:show" />
                                Tạo video mới
                            </button>
                        </div>
                    </div>

                    <!-- Additional Video Preview -->
                    <div class="flex items-center justify-center w-full">
                        <label class="text-xs lg:text-sm flex gap-1 items-center mb-1 w-full">
                            <div class="flex gap-1 w-full items-center cursor-pointer">
                                <div class="relative w-full">
                                    <div
                                        v-if="item.videoRef || item.previewVideoUpload"
                                        class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden"
                                    >
                                        <label class="ml-0 w-16 h-16 cursor-pointer top-0 right-0 p-2 z-10 text-right absolute">
                                            <input
                                                type="checkbox"
                                                :checked="checkVideo(item)"
                                                @change="selectVideo(item)"
                                                class="rounded-full"
                                            />
                                        </label>
                                        <video
                                            controls
                                            v-if="item.videoRef"
                                            :src="item.videoRef.s3_url"
                                            alt="image generated by AI"
                                            class="w-full h-auto rounded-md"
                                        />
                                        <video
                                            controls
                                            v-else
                                            :src="item.previewVideoUpload"
                                            alt="image generated by AI"
                                            class="w-full h-auto rounded-md"
                                        />
                                        <button
                                            type="button"
                                            @click="removeVideo(index)"
                                            class="absolute bottom-2 right-2 bg-red-500 text-white w-6 h-6 rounded-full"
                                        >
                                            x
                                        </button>
                                    </div>
                                    <div
                                        v-else
                                        class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden"
                                    >
                                        <input type="radio" disabled class="bg-gray-200 ml-0 top-2 right-2 absolute rounded-full" />
                                        <div class="flex flex-col items-center" v-if="isLoadingVideo">
                                            <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                            <h3 class="text-white text-center">Hệ thống đang xử lý, xin đợi một chút</h3>
                                        </div>
                                        <img v-else src="/assets/img/aiwow/homepage/play_button.png" alt="loading" class="mx-auto w-24" />
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-center gap-3 lg:gap-4 mt-6 mb-4" v-if="messagesMap['music']">
                    <button
                        @click="() => {
                            toggleGenerateImage('music', index);
                            openPostDetail(item?.content ?? '', index);
                        }"
                        class="min-w-[100px] place-items-center gap-2 md:min-w-[150px] px-4 py-2 lg:py-3 bg-orion-sec text-white rounded-lg lg:rounded-2xl font-bold"
                    >
                        <img src="/assets/img/my_assistant/music.png" class="w-5 xs:block hidden" alt="" />
                        Tạo nhạc
                    </button>
                </div>
                <div class="flex justify-center gap-3 lg:gap-4 mt-6 mb-4" v-else>
                    <button
                        @click="openPostDetail(item?.content ?? '', index)"
                        class="min-w-[100px] md:min-w-[150px] px-2 py-2 lg bg-ai3goc-primary rounded-lg lg:rounded-2xl border border-[#C5C5C5] text-white font-bold place-items-center gap-2 text-sm"
                    >
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>

        <!-- AI Components -->
        <div v-for="(_, index) in messagesMap['resultTarget']" :key="index" class="w-full">
            <div
                :id="`target_section_${index}`"
                ref="myAiRef"
                v-if="myAi && index === targetActiveIndex"
                class="mt-4"
            >
                <MyAIComponent
                    @saveGenerationResult="(value) => handleSaveGenerationResult(value, index)"
                    :listCollection="listCollection"
                    :collectionName="collectionSelected?.title"
                    :collection-selected="collectionSelected"
                    :my_ai_image_models="my_ai_image_models"
                />
            </div>

            <div
                :id="`target_section_${index}`"
                ref="textToImageRef"
                v-if="textToImage && index === targetActiveIndex"
                class="mt-4"
            >
                <TextToImage
                    @saveGenerationResult="(value) => handleSaveGenerationResult(value, index)"
                    :listCollection="listCollection"
                    :collectionName="collectionSelected?.title"
                    :collection-selected="collectionSelected"
                    :my_ai_image_models="my_ai_image_models"
                    :image-description="getActiveFinalTarget()?.content"
                    :isMemo="true"
                />
            </div>

            <div
                :id="`target_section_${index}`"
                ref="backgroundRef"
                v-if="background && index === targetActiveIndex"
                class="mt-4"
            >
                <AIBackgroundComponent
                    @saveGenerationResult="(value) => handleSaveGenerationResult(value, index)"
                    :listCollection="listCollection"
                    :collectionName="collectionSelected?.title"
                    :collection-selected="collectionSelected"
                    :my_ai_image_models="my_ai_image_models"
                />
            </div>

            <div
                :id="`target_section_${index}`"
                v-if="videoDetail && index === targetActiveIndex"
                ref="createFormRef"
                class="w-full h-full mt-10 rounded-[35px]"
            >
                <CreateForm
                    @saveGenerationResult="(value) => handleSaveGenerationResultVideo(value, index)"
                    :topic="getActiveFinalTarget()?.content ?? ''"
                    :businessProjectId="businessProjectId"
                />
            </div>

            <div
                :id="`target_section_${index}`"
                v-if="musicAi && index === targetActiveIndex"
                class="w-full h-full mt-10 rounded-[35px]"
            >
                <CreateMusic
                    @saveGenerationResult="(song) => handleSaveGenerationResultMusic(song, index)"
                    :content="getActiveFinalTarget()?.content ?? ''"
                    :titleMusic="formProject.title"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed } from 'vue';
    import Step from '@/Components/Step.vue';
    import LoadingCircle from '@/Components/LoadingCircle.vue';
    import SelectRadix from '@/Components/SelectRadix.vue';
    import DropdowRadix from '@/Components/DropdowRadix.vue';
    import { FileText, Trash2, Upload } from "lucide-vue-next";

    import MyAIComponent from '../../../../Client/AIImage/MyAIComponent.vue';
    import TextToImage from '../../../../Client/AIImage/CreateImage.vue';
    import AIBackgroundComponent from "../../../../Client/AIBackground/Component.vue";
    import CreateMusic from "../../../../Client/TextToSong/CreateMusic.vue";
    import CreateForm from "../../../../Client/TextToVideo/CreateForm.vue";

    const props = defineProps({
        mode: String,
        analysisActive: Object,
        messagesMap: Object,
        hiddenSocialMedia: Boolean,
        hiddenImage: Boolean,
        hiddenVideo: Boolean,
        showImageGenerate: Boolean,
        showVideoGenerate: Boolean,
        autoCreaeImageLoading: Boolean,
        isLoadingVideo: Boolean,
        targetActiveIndex: Number,
        myAi: Boolean,
        textToImage: Boolean,
        background: Boolean,
        videoDetail: Boolean,
        musicAi: Boolean,
        listCollection: Array,
        collectionSelected: Object,
        my_ai_image_models: Array,
        businessProjectId: String,
        formProject: Object,
        selectedAnalysis: String
    });

    const emit = defineEmits([
        'toggle-generate-image',
        'handle-image-upload',
        'handle-file-change',
        'select-image',
        'select-video',
        'remove-image',
        'remove-video',
        'open-post-detail',
        'save-generation-result',
        'save-generation-result-video',
        'save-generation-result-music',
        'open-grade-modal',
        'auto-create-image',
        'auto-create-video',
        'scroll-to-create-form',
        'select-option',
        'rewrite-content'
    ]);

    const isGenerateImage = ref(false);
    const uploadType = ref('video');

    const toggleGenerateImage = (type, index) => {
        emit('toggle-generate-image', type, index);
        isGenerateImage.value = false;
    };

    const handleImageUpload = (event, index) => {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file type
        const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!validImageTypes.includes(file.type)) {
            alert('Chỉ chấp nhận file ảnh định dạng: JPG, PNG, GIF, WEBP');
            return;
        }

        // Validate file size (max 5MB)
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
        if (file.size > maxSize) {
            alert('Kích thước file không được vượt quá 5MB');
            return;
        }

        emit('handle-image-upload', file, index);
    };

    const handleFileChange = (event, index) => {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file type
        const validVideoTypes = ['video/mp4', 'video/webm', 'video/ogg', 'video/x-m4v'];
        if (!validVideoTypes.includes(file.type)) {
            alert('Chỉ chấp nhận file video định dạng: MP4, WEBM, OGG, M4V');
            return;
        }

        // Validate file size (max 100MB)
        const maxSize = 100 * 1024 * 1024; // 100MB in bytes
        if (file.size > maxSize) {
            alert('Kích thước file không được vượt quá 100MB');
            return;
        }

        emit('handle-file-change', file, index);
    };

    const selectImage = (image) => {
        emit('select-image', image);
    };

    const selectVideo = (video) => {
        emit('select-video', video);
    };

    const removeImage = (index, imageIndex) => {
        emit('remove-image', index, imageIndex);
    };

    const removeVideo = (index) => {
        emit('remove-video', index);
    };

    const openPostDetail = (content, index) => {
        emit('open-post-detail', content, index);
    };

    const openGradeModal = (item) => {
        emit('open-grade-modal', item);
    };

    const autoCreateImage = () => {
        emit('auto-create-image');
    };

    const autoCreateVideo = () => {
        emit('auto-create-video');
    };

    const scrollToCreateFormRef = (index) => {
        emit('scroll-to-create-form', index);
    };

    const selectOption = ({ value, indexOptionRewrite, index }) => {
        emit('select-option', { value, indexOptionRewrite, index });
    };

    const rewriteContentProductAd = (index) => {
        emit('rewrite-content', index);
    };

    const handleSaveGenerationResult = (value, index) => {
        emit('save-generation-result', value, index);
    };

    const handleSaveGenerationResultVideo = (value, index) => {
        emit('save-generation-result-video', value, index);
    };

    const handleSaveGenerationResultMusic = (song, index) => {
        emit('save-generation-result-music', song, index);
    };

    const checkImage = (image) => {
        return image?.isSelected || false;
    };

    const checkVideo = (item) => {
        return item?.isSelectedVideo || false;
    };

    const getFakeNumberImage = (item) => {
        const currentImages = item?.images?.length || 0;
        return Math.max(0, 4 - currentImages);
    };

    const getActiveFinalTarget = () => {
        if (!props.messagesMap?.resultTarget?.length) return null;
        return props.messagesMap.resultTarget[props.targetActiveIndex];
    };

</script>

<style scoped></style>
