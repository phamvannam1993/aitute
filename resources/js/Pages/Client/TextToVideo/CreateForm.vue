<template>
    <div class="flex flex-col gap-4 justify-center items-center relative text-black ">
        <div class="bg-white lg:py-[20px] p-[12px] lg:px-[80px] border-[3px] border-[#d4d4d4] rounded-[25px] w-full">
            <div class="flex flex-col mb-2">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex gap-1">
                        <div class="flex justify-start items-center gap-2">
                            <div class="w-[52px] overflow-hidden rounded-full border-[1px] border-[#d4d4d4]">
                                <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
                            </div>
                            <h2 class="text-black font-bold text-xl lg:text-xl 2xl:text-3xl leading-none flex items-center gap-1">
                                Video cá nhân hóa nội dung
                            </h2>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 md:gap-2">
                        <a :href="route('video.history')" class="px-4 lg:px-16 flex items-center gap-2 py-2 bg-ai3goc-sec text-white font-bold text-[12px] lg:text-[16px] rounded-[10px]">
                            <img class="w-auto h-[22px]" src="/assets/img/orion/icon/play-circle.svg" />
                            <span>Video đã tạo</span>
                        </a>
                    </div>
                </div>
                <p class="text-ai3goc-sec text-sm md:text-base font-bold mt-4">Thực hiện theo các bước sau:</p>
            </div>

            <form @submit.prevent="submitForm">
                <div class="flex flex-col gap-1">
                    <div class="flex">
                        <Step :step="1" stepName="Tải hình ảnh, kịch bản của bạn" forName="video-description" />
                        <span class="text-red-500">*</span>
                    </div>
                    <span class="md:ml-[88px] italic text-[#B4B4B4] text-sm md:text-base">Bạn cần cung cấp 1 trong 2 yêu cầu, hoặc cả 2 yêu cầu dưới đây để có kết quả video tốt nhất.</span>
                </div>
                <div class="my-4">
                    <div class="flex items-center justify-between mb-2">
                        <label for="video-artist" class="text-sm md:text-base flex gap-1 items-center mb-1 font-semibold w-1/2" @click="enterContent">
                            <div class='flex gap-1 items-center cursor-pointer'>
                                <input type="checkbox" :checked="enableContent ? true : false" class="rounded-full text-orion-orange focus:ring-orion-orange" />
                            </div>
                            <p class="text-sm lg:text-base">Nhập nội dung kịch bản</p>
                        </label>
                        <div class="flex  items-center gap-1 md:gap-2" :class="inputFileName ? 'mb-6' : ''">
                            <p class=" text-[#8B8B8B] text-xs font-normal italic">Hoặc</p>
                            <div class='flex gap-1 items-center cursor-pointer' @click="handleUploadFile">
                                <input type="checkbox" :checked="enableContent ? false : true" class="rounded-full text-orion-orange b focus:ring-orion-orange" />
                            </div>
                            <div class="relative">
                                <button type="button" @click="handleUploadFile"
                                    class=" flex-1 flex items-center w-fit border-[2px] border-[#149CBE] justify-center gap-2 p-1 bg-white hover:bg-black/10 text-[#149CBE] rounded-lg backdrop-blur-sm transition-colors lg:w-[212px]">
                                    <img src="/assets/img/ai3goc/icon/upload.svg" class="size-6 md:size-5 xl:size-6" />
                                    <span class="text-[12px] xl:text-[16px] font-semibold">Tải file</span>
                                </button>
                                <p class="absolute max-w-[85px] md:max-w-[200px] right-1/2 translate-x-1/2 md:-bottom-6 -bottom-5 text-xs md:text-sm font-normal text-ai3goc-primary italic text-start
                                            overflow-hidden text-ellipsis whitespace-nowrap">
                                    {{ inputFileName ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <SuggestionPrompt v-model="topic" :type="'video'" :screenId="4" />
                        <textarea v-model="topic" :disabled="enableContent ? false : true" class="block bg-white border-gray-300 resize-none w-full rounded-xl max-h-[120px] h-[40vh] min-h-[80px]"
                            placeholder="Nhập nội dung kịch bản"></textarea>
                    </div>
                    <div class="object-mic relative ml-auto mt-0">
                        <div v-if="isRecording" class="outline-mic right-3 bottom-1.5 flex items-center justify-center"></div>
                        <div v-if="isRecording" class="outline-mic right-3 bottom-1.5 flex items-center justify-center" id="delayed"></div>
                        <div v-if="isRecording" class="button-mic right-3 bottom-1.5 flex items-center justify-center"></div>
                        <button class="button-mic icon-mic absolute right-3 bottom-1 flex items-center justify-center" @click="startRecording" :disabled="disabledRecord" type="button">
                            <img src="/assets/img/ai3goc/icon/mic.svg" class="w-6 h-6" />
                        </button>
                    </div>
                    <p class="text-red-600" v-show="validateErrors.content?.[0]">{{ validateErrors.content?.[0]
                        }}</p>
                </div>
                <input type="file" ref="fileUploadV2" id="file-upload" accept=".doc, .docx, .pdf, .dotx" @change="onFileChange" class="hidden" />
                <div class="flex items-center justify-between mb-2">
                <label for="video-artist" class="text-sm md:text-base flex gap-1 items-center mb-1  w-1/2" @click="uploadImageProduct">
                    <div class='flex gap-1 items-center cursor-pointer'>
                        <input type="checkbox" :checked="enableCamera ? false : true" class="rounded-full text-orion-orange  focus:ring-orion-orange" />
                    </div>
                    <button type="button"
                        class="flex items-center w-fit border-[2px] border-[#149CBE] justify-center gap-2 p-1 bg-white hover:bg-black/10 text-[#149CBE] rounded-lg backdrop-blur-sm transition-colors lg:w-[212px]">
                        <img src="/assets/img/ai3goc/icon/upload.svg" class="size-6 md:size-5 xl:size-6" />
                        <span class="text-[10px] xl:text-[15px]">Tải ảnh lên</span>
                    </button>
                </label>
                <div class="flex items-center gap-1 md:gap-2">
                    <p class=" text-[#8B8B8B] text-xs font-normal italic">Hoặc</p>
                    <div class='flex gap-1 items-center cursor-pointer' @click="initializeCamera">
                        <input type="checkbox" :checked="enableCamera ? true : false" class="rounded-full text-orion-orange  focus:ring-orion-orange" />
                    </div>
                    <button type="button" @click="initializeCamera"
                        class="flex items-center w-fit border-[2px] border-[#149CBE] justify-center gap-2 p-1 bg-white hover:bg-black/10 text-[#149CBE] rounded-lg backdrop-blur-sm transition-colors lg:w-[212px]">
                        <img src="/assets/img/ai3goc/icon/camera.svg" class="size-6 md:size-5 xl:size-6" />
                        <span class="text-[12px] xl:text-[16px] font-semibold">Chụp ảnh</span>
                    </button>
                </div>
            </div>
            <input type="file" id="image-file" @change="handleImageProduct" accept="image/*" class="hidden" ref="fileImageProduct" />
            <input type="file" id="image-file" @change="handleUploadImageItem" accept="image/*" class="hidden" ref="fileImageItem" />
            <div v-if="showCamera && enableCamera" class="relative w-full h-[30rem] bg-black rounded-lg overflow-hidden">
                <video ref="videoRef" autoplay playsinline class="w-full h-full object-contain" style="transform: scaleX(-1)" />
                <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-4">
                    <button @click="capturePhoto" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">Chụp
                        ảnh</button>
                    <button @click="stopCamera" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">Hủy</button>
                </div>
            </div>
            <div class="flex items-center justify-center mb-2">
                <div v-if="uploadedImages.length > 0 && enableCamera" class="mt-4">
                    <h4 class="text-black font-semibold">Ảnh đã tải lên:</h4>
                    <div :class="uploadedImages.length <= 1 ? 'grid-cols-1' : 'lg:grid-cols-2'" class="grid gap-1">
                        <div v-for="(image, index) in uploadedImages" :key="index" class="relative">
                            <img :src="image" class="w-full h-[30vh] object-contain rounded-lg border" alt="Uploaded image" />
                            <img @click="removeImage(index)" src="/assets/svgs/aiwow/remove_icon.svg" class="absolute size-6 md:size-5 xl:size-6 top-2 right-2 cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="flex flex-row items-center justify-center w-full" v-else>
                    <img v-if="imageProductUrl" :src="imageProductUrl" class="w-full h-auto md:max-w-[500px] aspect-square object-contain bg-[#E6E6E6]" />
                    <div v-else class="bg-[#E6E6E6] w-full md:w-2/5 aspect-square rounded-2xl showLoaderVideo items-center justify-between">
                        <img src="\assets\img\aiwow\image_placeholder.png" alt="" class="w-20 h-fit">
                    </div>
                </div>
            </div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex">
                        <Step :step="2" stepName="Thời lượng video" forName="video-artist" />
                        <!-- <span class="text-red-500">*</span> -->
                    </div>
                    <select v-model="videoDuration" :class="{
                        'bg-gray-200 border-gray-200': !videoDuration,
                        'bg-white border-[#D4D4D4]': videoDuration,
                    }"
                        class="block mt-1 text-[14px] appearance-none lg:w-1/2 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-1/2"
                        id="video-artist">
                        <option v-for="duration in durations" :value="duration" :key="duration">
                            {{ duration }}
                        </option>
                    </select>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex">
                        <Step :step="3" stepName="Kích thước" forName="video-dimensions" />
                        <!-- <span class="text-red-500">*</span> -->
                    </div>
                    <select v-model="videoDimensions" id="video-dimensions" :class="{
                        'bg-gray-200 border-gray-200': !videoDimensions,
                        'bg-white border-[#D4D4D4]': videoDimensions,
                    }" class="block mt-1 text-[14px] appearance-none w-1/2 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option v-for="dimension in validDimensions" :value="dimension" :key="dimension">
                            {{ dimension }}
                        </option>
                    </select>
                </div>
                <div class="grid grid-cols-2 w-full mb-2">
                    <input type="file" ref="fileInputV2" accept="audio/*,video/mp4,.mp4" @change="handleAudioFileChangeV2" class="hidden" />
                    <div class="flex flex-col gap-1 my-auto">
                        <Step :step="4" stepName="Tải nhạc nền" forName="video-dimensions" />
                        <p v-if="audioNameV2" class="text-[#149CBE] text-sm font-thin italic w-5/6 max-sm:hidden">{{ audioNameV2 }}
                        </p>
                    </div>
                    <button type="button" @click="handleUploadClickV2"
                        class="flex items-center border-[2px] border-[#149CBE] justify-center gap-2 p-1 bg-white hover:bg-black/10 text-[#149CBE] rounded-lg backdrop-blur-sm transition-colors w-full h-fit">
                        <img src="/assets/img/ai3goc/icon/upload.svg" class="size-6 md:size-5 xl:size-6" />
                        <span class="text-[12px] xl:text-[16px] font-semibold">Tải nhạc nền</span>
                    </button>
                </div>
                <div class="flex items-center mb-2 justify-between hidden">
                    <Step :step="5" stepName="Tạo phụ đề" forName="video-dimensions" />
                    <div class="grid grid-cols-2 w-1/2 gap-2">
                        <div class='flex gap-1 items-center cursor-pointer'>
                            <input type="radio" id="enableCaptionTrue" name="enableCaption" value="true"  @click="onOffCaption(true)" :checked="enableCaption ? true : false" class="rounded-full text-orion-orange focus:ring-orion-orange" />
                            <label for="enableCaptionTrue" class="text-[12px] lg:text-[14px]">Bật</label>
                        </div>
                        <div class='flex gap-1 items-center cursor-pointer'>
                            <input type="radio" id="enableCaptionFalse" name="enableCaption" value="false"  @click="onOffCaption(false)" :checked="enableCaption ? false : true" class="rounded-full text-orion-orange  focus:ring-orion-orange" />
                            <label for="enableCaptionFalse" class="text-[12px] lg:text-[14px]">Tắt</label>
                        </div>
                    </div>
                </div>
                <!-- <div class="flex items-center justify-between mb-2">
                            <Step :step="5" :stepName="'Tạo phụ đề'" :forName="'video-description'" />

                            <div class="grid grid-cols-2 w-1/2">
                                <div class='flex gap-1 items-center cursor-pointer'>
                                    <input type="radio" id="enableCaptionTrue" name="enableCaption" value="true"  @click="onOffCaption(true)" :checked="enableCaption ? true : false"
                                    class="ml-0 rounded-full text-secondary-lurcinn focus:ring-secondary-lurcinn focus:outline-none" />
                                    <label for="enableCaptionTrue">Bật</label>
                                </div>
                                <div class='flex gap-1 items-center cursor-pointer'>
                                    <input type="radio" id="enableCaptionFalse" name="enableCaption" value="false" @click="onOffCaption(false)" :checked="enableCaption ? false : true"
                                    class="ml-0 rounded-full text-secondary-lurcinn focus:ring-secondary-lurcinn focus:outline-none" />
                                    <label for="enableCaptionFalse">Tắt</label>
                                </div>
                            </div>
                        </div> -->
                <div class="flex items-start justify-between mb-2" v-if="enableCaption">
                    <label data-v-b62bbf78="" for="video-description" class="text-black font-semibold text-[14px] cursor-pointer flex w-1/2">
                        <div class="lg:w-1/5 w-1/2"></div>
                        <span class="font-semibold text-[12px] lg:text-[14px]">Vị trí</span>
                    </label>
                    <div class="grid grid-cols-2 w-1/2 gap-2">
                        <div class='flex gap-1 items-center cursor-pointer'>
                            <input type="radio" id="enablePosition2" name="positionCaption" value="true" @click="changePosition('bottom75')" :checked="enablePosition == 'bottom75'"
                                class="ml-0 rounded-full text-orion-orange focus:ring-orion-orange" />
                            <label for="enablePosition2" class="text-[12px] lg:text-[14px]">Dưới</label>
                        </div>
                        <div class='flex gap-1 items-center cursor-pointer'>
                            <input type="radio" id="enablePosition4" name="positionCaption" value="false" @click="changePosition('center')" :checked="enablePosition == 'center'"
                                class="ml-0 rounded-full text-orion-orange focus:ring-orion-orange" />
                            <label for="enablePosition4" class="text-[12px] lg:text-[14px]">Giữa</label>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-2" v-if="enableCaption">
                    <label data-v-b62bbf78="" for="video-description" class="text-black font-semibold text-[14px] cursor-pointer flex w-1/2 items-center">
                        <div class="lg:w-1/5 w-1/2"></div>
                        <span class="font-semibold text-[12px] lg:text-[14px]">Màu sắc</span>
                    </label>
                    <div class="w-1/2">
                        <div class="relative flex gap-1 items-center cursor-pointer w-fit">
                            <a href="javascript:void(0)" class="w-[86px] h-[26px] rounded-[16px] border-[1px] border-ai3goc-primary" :style="{ backgroundColor: captionerColor }"
                                @click="toggleShowColor"></a>
                            <div ref="colorPickerRef" class="w-[212px] absolute top-[105%] lg:top-0 -left-[100%] lg:left-[105%] grid grid-cols-6 gap-2 p-2 bg-white rounded-[20px] border-[2px] z-50"
                                v-if="isShowColor">
                                <p v-for="color in captionerColors" :key="color" :style="{ backgroundColor: color }"
                                    class="w-[24px] h-[24px] rounded-full cursor-pointer border-[1px] border-[#E4E4E4] relative" @click="changeColor(color)">
                                    <img v-if="color === captionerColor" :src="checkIcon(color)" class="absolute top-1 right-1 w-4 h-4 " />
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-2" v-if="enableCaption">
                    <label data-v-b62bbf78="" for="video-description" class="text-black font-semibold text-[14px] cursor-pointer flex w-1/2 items-center">
                        <div class="lg:w-1/5 w-1/2"></div>
                        <span class="font-semibold text-[12px] lg:text-[14px]">Kiểu chữ</span>
                    </label>
                    <div class="w-1/2">
                        <select v-model="fontCaptioner" :class="{
                            'bg-gray-200 border-gray-200': !fontCaptioner,
                            'bg-white border-ai3goc-primary': fontCaptioner,
                        }"
                            class="appearance-none text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500 text-[12px] lg:text-[14px] max-w-full"
                            id="video-artist">
                            <option v-for="font in fontCaptioners" :value="font" :key="font">
                                {{ font }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-2 py-1">
                    <Step :step="5" :stepName="'Hiệu ứng'" :forName="'xFade'" />
                    <div class="grid grid-cols-2 w-1/2 gap-2">
                        <div class='flex gap-1 items-center cursor-pointer'>
                            <input type="radio" id="enableFadeTrue" name="enableFade"  @click="onOffFade(true)" :checked="enableFade ? true : false"
                                class="ml-0 rounded-full text-orion-orange focus:ring-orion-orange" />
                            <label for="enableFadeTrue" class="text-[12px] lg:text-[14px]">Bật</label>
                        </div>
                        <div class='flex gap-1 items-center cursor-pointer'>
                            <input type="radio" id="enableFadeFalse" name="enableFade" @click="onOffFade(false)" :checked="enableFade ? false : true"
                                    class="ml-0 rounded-full text-orion-orange focus:ring-orion-orange" />
                            <label for="enableFadeFalse" class="text-[12px] lg:text-[14px]">Tắt</label>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-2" v-if="enableFade">
                    <label data-v-b62bbf78="" for="video-description" class="text-black font-semibold text-[14px] cursor-pointer flex w-1/2 items-center">
                        <div class="lg:w-1/5 w-1/2"></div>
                        <span class="font-semibold text-[12px] lg:text-[14px]"></span>
                    </label>
                    <select v-model="xFadeSeleted" :class="{
                            'bg-gray-200 border-gray-200': !xFadeSeleted,
                            'bg-white border-blue-500': xFadeSeleted,
                        }" class="bg-white border-[#2C75E3] block mt-1 text-[14px] appearance-none w-1/2 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            id="video-artist">
                            <option v-for="xFade in xFades" :value="xFade.key" :key="xFade.key">
                                {{ xFade.value }}
                        </option>
                    </select>
                </div>
                <div class="flex flex-row items-center justify-between mb-2 gap-1">
                    <Step :step="6" stepName="Bấm vào đây" forName="video-dimensions" />
                    <div class="relative xs:w-1/2 text-center-mb">
                        <button type="submit"
                            class="md:px-4 px-2.5 py-2 bg-ai3goc-sec text-white rounded-[10px] min-w-36 text-[13px] md:text-[15px] font-bold disabled:opacity-50 disabled:pointer-events-none w-full hover:scale-105">Tạo
                            nội dung phân cảnh</button>
                    </div>
                </div>
                <!-- <span class="text-green-700 font-bold">Tải ảnh lên nếu bạn muốn tạo người thuyết trình của riêng mình: </span> -->
                <!-- <div class="mb-4 flex justify-between items-center">
                            <div class="flex flex-col">
                                <div class="input-file-container">
                                    <input class="input-file hidden" id="my-file" type="file" @input="handleChangeFile" accept=".jpg,.png,.jpeg" />
                                    <label tabindex="0" for="my-file" class="input-file-trigger cursor-pointer text-ai3goc-primary hover:underline">Chọn Ảnh...</label>
                                </div>
                                <img :src="fileUpload.url || imageUrl" class="w-[200px]" />
                            </div>
                            <button type="button" @click="generateMcByImage()" class="px-4 py-2 rounded-md bg-green-500 text-white">Tạo mc ảo của tôi</button>
                        </div> -->
                <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-25 flex flex-col items-center justify-center z-[999]">
                    <img src="/assets/img/dashboard/loading.gif" alt="" class="relative z-10"/>
                    <p class="text-black text-lg font-semibold absolute top-[55%] z-20">🔄 Hệ thống đang phân tích chuyên sâu. Vui lòng chờ...</p>
                </div>
                <div>
                    <h2 class="text-red-600">{{ errorMessage }}</h2>
                </div>
                <div class="flex justify-end gap-2">
                    <!-- <button type="button" @click="clearStorageData()" class="px-4 py-2 bg-red-500 text-white rounded-md">Tạo lại</button> -->
                </div>
            </form>
        </div>
        <div v-if="slideContents.length > 0" class="flex items-center justify-between mt-4 mb-1">
            <h2 class="font-semibold text-black">NỘI DUNG PHÂN CẢNH</h2>
        </div>

        <div v-for="(slide, index) in slideContents" :key="index" class="bg-white lg:py-[20px] p-[12px] lg:px-[80px] border-[3px] border-[#d4d4d4] rounded-[25px] w-full relative "
            :class="{ 'pointer-events-none': slide.isLoadingVideo }">
            <div class="absolute w-full h-full inset-0 bg-gray-600 opacity-20 z-10 rounded-3xl cursor-not-allowed" v-if="slide.isLoadingVideo">
            </div>
            <div class=" relative rounded-md flex transition-all duration-500 z-[0]">
                <div class=" flex-1 transition-transform duration-500 rounded-md rounded-t-3xl" :class="isShowVideo && isSubmit ? 'lg:-translate-x-[15%] xl:-translate-x-[25%]' : ''">
                    <div>
                        <div class="w-full flex justify-between items-center mb-1">
                            <div class="text-white font-semibold text-[18px] flex items-center gap-2 rounded-full bg-ai3goc-primary px-2.5 py-1">
                                <img src="/assets/img/orion/icon/scene.svg" alt="step" class="w-[16px] h-auto" />
                                <span class="text-white text-[12px] lg:text-[14px] leading-none font-semibold">CẢNH {{ index + 1 }}</span>
                            </div>
                            <div v-if="slide.showSlide" class="flex flex-row cursor-pointer" @click="showSlide(index)">
                                <p class="text-[#a8a8a8] text-[12px] lg:text-[14px]">Hiển thị</p>
                                <span class="ml-2 mt-2">
                                    <svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 1.43848L5 5.43848L1 1.43848" stroke="#a8a8a8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div v-if="!slide.showSlide">
                            <div class="mb-4">
                                <div v-for="(desc, subTitleIndex) in slide.descriptions" :key="subTitleIndex" class="mb-4">
                                    <div>
                                        <label :for="'sub_title_' + subTitleIndex" class="block text-sm md:text-base  text-gray-700 font-semibold">
                                            <div class="flex items-center">
                                                <span class="ml-2">1. Nội dung phân cảnh</span>
                                            </div>
                                        </label>
                                        <textarea v-model="desc.sub_title" :id="'sub_title_' + subTitleIndex" rows="4"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 min-h-[160px] lg:min-h-0"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex w-full mt-6">
                                <label for="video-dimensions" class="text-sm md:text-base gap-1 items-center mb-1 font-semibold">
                                    <div class="flex gap-1 items-center">
                                        <span class="ml-2">2. Tải hình ảnh hoặc tạo ảnh A.I</span>
                                    </div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between my-2">
                                <div class="flex gap-1 lg:gap-2 items-center mb-1" @click="enterContent">
                                    <div class='flex gap-1 items-center cursor-pointer'>
                                        <input type="checkbox" :checked="slide.updateLoadImage === 'upload'" @input="(e) => handleChangeInputImage(e, index)" value="upload" class="ml-0 rounded-full text-orion-orange  focus:ring-orion-orange" />
                                    </div>
                                    <button type="button" @click="handleUploadImage(index, slide)"
                                        class="flex items-center lg:w-[212px] border-[2px] border-ai3goc-primary justify-center gap-1 lg:gap-2 p-1 bg-white hover:bg-black/10 text-ai3goc-primary rounded-lg backdrop-blur-sm transition-colors">
                                        <img src="/assets/img/ai3goc/icon/upload.svg" class="w-4 h-4 md:w-6 md:h-6" />
                                        <span class="text-[10px] xl:text-[16px] font-semibold">Tải ảnh</span>
                                    </button>
                                </div>
                                <div class="flex gap-1 lg:gap-2 items-center mb-1" @click="enterContent">
                                    <div class='flex gap-1 items-center cursor-pointer'>
                                        <input type="checkbox" value="library" :checked="slide.updateLoadImage === 'library'" @input="(e) => handleChangeInputImage(e, index)"
                                            class="ml-0 rounded-full checked:bg-orion-orange checked:border-orion-orange checked:text-orion-orange focus:ring-orion-orange focus:outline-none" />
                                    </div>
                                    <!-- Kho ảnh -->
                                    <ImageDropdown :default-selected-image="imageTypeActive[index]" @selectImage="(value) => selectimageActive(value, index)" />
                                </div>
                                <div class="flex items-center gap-1 lg:gap-2">
                                    <div class='flex gap-1 items-center cursor-pointer' @click="handleCreateImage(index, slide)">
                                        <input type="checkbox" :checked="slide.updateLoadImage === 'ai'" @input="(e) => handleChangeInputImage(e, index)" value="ai" class="rounded-full text-orion-orange  focus:ring-orion-orange" />
                                    </div>
                                    <button type="button" @click="handleCreateImage(index, slide)"
                                        class="flex items-center lg:w-[212px] border-[2px] border-ai3goc-primary justify-center gap-1 lg:gap-2 p-1 bg-ai3goc-primary hover:bg-black/10 text-ai3goc-primary rounded-lg backdrop-blur-sm transition-colors">
                                        <img  class="w-4 h-4 md:w-6 md:h-6" src="/assets/img/ai3goc/icon/img-ai.svg" alt="upload" />
                                        <span class="text-[10px] xl:text-[16px] font-semibold text-white">Tạo bằng A.I</span>
                                    </button>
                                </div>
                            </div>
                            <div class="p-2 border border-gray-400 rounded-xl mb-4" v-if="slide.updateLoadImage === 'library'">
                                <ImageList :image-type-active="imageTypeActive[index]" @selectImage="(value) => handleSelectSelfImage(value, index)" />
                            </div>
                            <div class="flex flex-rows gap-2 text-center self-center mb-2">
                                <div class="flex flex-col w-full" :id="'image_' + index">
                                    <img v-if="slide.imageUrl" :src="slide.imageUrl" class="w-full h-auto md:max-w-[500px] md:max-h-[500px] mx-auto object-cover" />
                                    <div v-else class="bg-[#E6E6E6] w-full md:w-2/3 mx-auto aspect-square rounded-2xl showLoaderVideo items-center">
                                        <div v-if="slide.isLoadingImage">
                                            <div class="loaderVideo loadingImage"></div>
                                        </div>
                                        <img v-else src="\assets\img\aiwow\image_placeholder.png" alt="" class="w-20 h-fit">
                                    </div>
                                </div>
                            </div>
                            <div v-for="(desc, descIndex) in slide.descriptions" :key="descIndex" class="mb-4">
                                <div>
                                    <label :for="'description_' + descIndex" class="block text-sm md:text-base text-gray-700 font-semibold">
                                        <div class="flex items-center">
                                            <span class="ml-2">3. Nội dung thuyết minh:</span>
                                        </div>
                                    </label>
                                    <textarea v-model="desc.description" :id="'description_' + descIndex" rows="2"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 min-h-[160px] lg:min-h-0"></textarea>
                                        <span class="text-sm text-gray-400 self-center inline-block mb-1 italic">Nội dung thuyết minh không được vượt quá 18 từ!</span>
                                </div>
                            </div>

                            <label for="" class="block text-sm md:text-base font-medium">
                                <div class="flex items-center mb-4">
                                    <img class="h-2" src="/assets/img/orion/icon/label-arrow.svg" /> <span class="ml-2"> Chọn giọng</span>
                                </div>
                            </label>
                            <div class="flex items-start gap-2">
                                <div class="flex gap-1 items-center font-normal w-1/2">
                                    <div class='flex gap-1 items-center cursor-pointer'>
                                        <input type="checkbox" :checked="!slide.isVoice" @input="slide.isVoice = !slide.isVoice" class="rounded-full text-orion-orange bg-orion-orange focus:ring-orion-orange" />
                                    </div>
                                    <div class="relative w-full">
                                        <div @click="toggleDropdown(index)"
                                            class="w-full p-2 text-[10px] md:text-sm md:p-3 border text-black border-ai3goc-primary rounded-xl shadow-sm cursor-pointer focus:outline-none focus:ring-2 focus:ring-green-500">
                                            <span v-if="slide.voice_type">{{ getVoiceName(slide.voice_type) }}</span>
                                            <span v-if="!slide.voice_type">Bạn chưa chọn giọng nói</span>
                                        </div>
                                        <ul v-if="isDropdownOpen" class="absolute left-0 right-0 mt-1 bg-white border border-gray-300 text-black rounded-lg shadow-lg max-h-60 overflow-y-auto z-10">
                                            <template v-for="voice in listVoice">
                                                <li :key="voice.id" :value="voice.type" v-if="!voice?.user_id" @click="selectVoice(voice.type, index)"
                                                    class="flex justify-between items-center px-2 py-1 text-[10px] md:text-sm md:px-4 md:py-2 hover:bg-gray-100 cursor-pointer">
                                                    <span class="inline-block w-[200px]">{{ voice.name }}</span>
                                                    <div class="flex items-center">
                                                        <div @click.stop="playSampleVoice(voice.demo)" class="text-white px-3 py-1 rounded-lg focus:outline-none w-[50px]">
                                                            <img src="/assets/img/ai_audio/speaker.png" alt="">
                                                        </div>
                                                    </div>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                                <p class=" text-[#8B8B8B] text-sm md:text-base font-normal mt-3">Hoặc</p>
                                <div class='flex gap-1 items-center cursor-pointer'>
                                    <input type="checkbox" :checked="slide.isVoice" @input="slide.isVoice = !slide.isVoice" class="ml-0 rounded-full mt-4" />
                                </div>
                                <div class="w-1/2 flex flex-col gap-1">
                                    <div class="flex flex-col items-center gap-2 relative justify-end">
                                        <button type="button" :disabled="isLoadingClonedVoices" @click="textToSpeech(index)"
                                            :class="isLoadingClonedVoices ? 'bg-gray-400 cursor-not-allowed' : 'bg-ai3goc-primary text-white'"
                                            class="relative  md:w-11/12 w-full flex items-center font-bold px-2 py-2 justify-center gap-2 bg-ai3goc-primary text-white rounded-[10px] backdrop-blur-sm transition-colors hover:scale-105 border-2 border-ai3goc-primary">
                                            <img src="/assets/img/aiwow/ai_audio/vip_feature.png" alt="">
                                            <span class="text-sm md:text-base"> {{ isLoadingClonedVoices ? 'Đang nhái giọng...' : 'Nhái giọng' }} </span>
                                        </button>
                                        <p class="text-[10px] font-normal italic text-[#B7B7B7] text-center min-w-[125px] md:min-w-[210px] text-wrap">
                                            (Chỉ áp dụng cho tài khoản được cấp quyền)
                                        </p>

                                    </div>
                                    <div :class="isLoadingClonedVoices ? 'pointer-events-none opacity-50' : ''" class="flex items-center gap-1"
                                        v-if="isShowUploadFileOrRecord && clonedVoices.length === 0">
                                        <label for="audioInputCloneVoice" type="button" :disabled="isLoadingClonedVoices"
                                            class="cursor-pointer flex-1 flex items-center gap-2 px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg backdrop-blur-sm transition-colors justify-center">
                                            <img src="/assets/img/upload.png" class="size-5" />
                                            <span class="hidden xl:block text-[12px]">Tải giọng nói của bạn lên</span>
                                        </label>
                                        <input id="audioInputCloneVoice" type="file" accept="audio/*" @change="(event) => handleAudioUpload(event, index)" class="hidden"
                                            @click="$event.target.value = null" />
                                        <div class="text-center">
                                            <span class="px-3 text-gray-500">hoặc</span>
                                        </div>
                                        <button type="button" @click="isRecordVoice ? stopRecorCloneVoice(index) : startRecordingCloneVoice(index)"
                                            class="flex-1 flex items-center gap-2 px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg backdrop-blur-sm transition-colors justify-center"
                                            :class="{ 'bg-red-500 hover:bg-red-600': isRecordVoice }">
                                            <img :src="isRecordVoice ? '/assets/img/ai_audio/pause.png' : '/assets/img/mic.png'" class="size-5" />
                                            <span v-if="isRecordVoice">{{
                                                formatTime(recordingTime) }}</span>
                                            <span v-else class="hidden xl:block text-[12px]">Ghi âm (30s>)</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-if="slide.audioUrl" class=" bg-gray-100 rounded-lg flex-1 mt-3 ">
                                <audio controls :src="slide.audioUrl" class="w-full"></audio>
                            </div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="video-description"
                                    class="text-sm md:text-base flex gap-1 items-center mb-1 font-semibold">
                                    <div class="flex items-center">
                                        <span class="ml-2">4. Di chuyển của camera:</span>
                                    </div>
                                </label>
                                <select v-model="slide.position_camera" :class="{
                                        'bg-gray-200 border-gray-200': !slide.position_camera,
                                        'bg-white border-blue-500': slide.position_camera,
                                    }" class="bg-white border-[#2C75E3] block mt-1 text-[14px] appearance-none w-1/2 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                        id="video-artist">
                                        <option v-for="pCamera in positionCameras" :value="pCamera.key" :key="pCamera.key">
                                            {{ pCamera.value }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <div>
                                    <label
                                        class="block text-sm md:text-base text-gray-700 font-semibold">
                                        <div class="flex items-center">
                                            <span class="ml-2">5. Mô tả chuyển động của ảnh:</span>
                                        </div>
                                    </label>
                                    <textarea v-model="slide.image_description"
                                        rows="2"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[160px] lg:min-h-0"></textarea>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 w-full mt-10">
                                <label for="video-description" class="text-sm md:text-base flex gap-1 items-center mb-1 font-semibold">
                                    <div class="flex items-center">
                                        <span class="ml-2">6. Bấm vào đây</span>
                                    </div>
                                </label>
                                <button type="button" @click="previewVideo(index)"
                                    class="flex items-center font-bold px-3 py-1.5 justify-center gap-2 bg-ai3goc-sec text-ai3goc-primary rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-ai3goc-primary">
                                    <img src="/assets/img/aiwow/create-scene.png" class="size-6 md:size-5 xl:size-6" />
                                    <span class="text-sm md:text-base text-white">Tạo video cảnh
                                        {{ index + 1 }}</span>
                                </button>
                            </div>
                            <div class="grid items-center mt-5">
                                <div class="flex flex-col items-center w-full" :class="slide.videoRef && videoDimensions != '16:9' ? 'bg-black' : ''">
                                    <video v-if="slide.videoRef" :src="slide.videoRef" alt="video-generate-by-AI" controls="" preload="auto" class="w-auto max-h-400 md:max-h-[500px] object-cover" />
                                    <div v-else class="bg-[#E6E6E6] w-full md:w-1/2 aspect-square rounded-2xl showLoaderVideo items-center">
                                        <div v-if="slide.isLoadingVideo">
                                            <div class="loaderVideo"></div>
                                            <p class="text-loading">Video đang được tạo. Vui lòng chờ !</p>
                                        </div>
                                        <img v-else src="\assets\img\aiwow\play_button.png" alt="" class="w-20 h-fit">
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-5">
                                <label for="video-artist" class="text-sm md:text-base flex gap-1 items-center mb-1 font-semibold w-1/2">
                                </label>
                                <div class="flex flex-row cursor-pointer" @click="hideSlide(index)">
                                    <p class="text-[#a8a8a8] text-[12px] md:hidden xl:block xl:text-[14px]">
                                        Thu gọn </p>
                                    <span class="ml-2 mt-2">
                                        <svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 1.43848L5 5.43848L1 1.43848" stroke="#a8a8a8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
                                <div class="loader"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="file" ref="fileInput" accept="audio/*" @change="handleAudioFileChange" class="hidden" />
        <div class="flex flex-col gap-2 justify-center items-center relative">
            <div class="flex justify-center items-center" v-if="slideContents.length > 0">
                <p class="italic text-orion-orange font-semibold text-[15px]">Kiểm tra lại thông tin các phân cảnh. Nếu đã hài lòng, vui lòng bấm vào đây
                    để bắt đầu tạo video!</p>
            </div>
        </div>
        <div class="flex flex-col gap-2 justify-center items-center relative">
            <div class="flex justify-center items-center">
                <button v-if="slideContents.length > 0" type="button" @click="createSlide" class="px-4 w-[300px] text-center py-2 bg-ai3goc-sec text-white font-bold text-[15px] rounded-[10px] h-fit">
                    <span class="text-[12px] xl:block xl:text-[14px]">Tạo video hoàn chỉnh</span>
                </button>
            </div>
        </div>
        <div class="bg-white lg:py-[20px] p-[12px] lg:px-[80px] border-[3px] border-[#d4d4d4] rounded-[25px] w-full relative" v-if="slideContents.length > 0">
                <p class="text-ai3goc-primary italic mb-4 text-center">Hiển thị kết quả</p>
                <div id="video-create" class="flex justify-center items-center mb-4" :class="videoRef && videoDimensions != '16:9' ? 'bg-black' : ''">
                    <video v-if="videoRef" :src="videoRef" alt="video-generate-by-AI" controls="" preload="auto" class="w-auto max-h-400 md:max-h-[500px] object-cover" />
                    <div v-else class="bg-[#E6E6E6] w-full aspect-video rounded-2xl showLoaderVideo items-center">
                        <div v-if="isLoadingVideo">
                            <div class="loaderVideo"></div>
                            <p class="text-loading">Video đang được tạo. Vui lòng chờ !</p>
                        </div>
                        <img v-else src="\assets\img\aiwow\ai_image\placeholder.png" alt="" class="w-20 h-fit">
                    </div>
                </div>
                <div class="flex gap-2 justify-evenly w-full" :class="{ 'pointer-events-none': !videoRef }">
                    <button type='button' @click="createPost(videoRef)" class="flex flex-col justify-start items-center gap-1 transition-colors duration-300 text-white">
                        <img src="/assets/img/orion/icon/taskbar/create_post.svg" class="w-auto h-[32px] lg:h-[28px]" alt="Create Post" />
                        <span class="font-medium hover:scale-105 rounded-md bg-ai3goc-primary text-white px-2 lg:px-1 xl:px-2 py-1.5 text-xs">Đăng bài</span>
                    </button>
                    <button type='button' class="flex flex-col items-center gap-1 text-white" @click="downloadVideo(videoRef)">
                        <img src="/assets/img/orion/icon/taskbar/download.svg" alt="Download" class="w-auto h-[32px] lg:h-[28px]" />
                        <span class="font-medium hover:scale-105 rounded-md bg-ai3goc-primary text-white px-2 lg:px-1 xl:px-2 py-1.5 text-xs">Tải
                            xuống</span>
                    </button>
                    <button type='button' @click="shareVideo(resultValue)" class="text-white flex flex-col items-center gap-1">
                        <img src="/assets/img/orion/icon/taskbar/share.svg" class="w-auto h-[32px] lg:h-[28px]" />
                        <span class="font-medium hover:scale-105 rounded-md bg-ai3goc-primary text-white px-2 lg:px-1 xl:px-2 py-1.5 text-xs">Chia
                            sẻ</span>
                    </button>
                </div>
                <div class="flex flex-col lg:flex-row justify-between gap-4">
                    <div v-if="isCropping" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                        <div class="bg-white p-4 rounded-md w-full md:w-[80%] lg:w-[40%]">
                            <VueCropper ref="cropper" :src="imageUrlCrop" :aspect-ratio="aspectRatio" :zoomable="true" class="max-w-full max-h-[60vh] mx-auto overflow-hidden" />
                            <div class="flex justify-between mt-4">
                                <button @click="cropImage" class="bg-ai3goc-sec text-white py-2 px-4 rounded hover:bg-green-600">Cắt
                                    ảnh</button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
            <FormShareLink :shareLink="shareLink" />
        </Modal>
        <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true"
            :acceptUpgrade="acceptUpgrade" />

            <PopupN
        v-if="showConfirmCaption"
        title=""
        :message="'Nếu bật phụ đề việc tạo video sẽ lâu hơn bình thường. Bạn có muốn bật phụ đề không?'"
        cancelButtonText="Đóng"
        acceptButtonText="Đồng ý"
        @cancel="onOffCaption(false, 2)"
        @accept="onOffCaption(true, 2)"
    />

            <PopupN
        v-if="showConfirmCreateVideo"
        title=""
        :message="'Bạn có muốn tạo lại video không?'"
        cancelButtonText="Đóng"
        acceptButtonText="Đồng ý"
        @cancel="closeConfirm()"
        @accept="acceptConfirm()"
    />


        <Popup v-if="showError" :message="popupMessageError"  @close="showError = false" :type="errorFile" :canClose="true" :isError="true" />
         <PopupConfirmVideo v-if="showConfirm" :message="messageSuccessVideo" @close="showConfirm = false" :id="videoId"
            :canClose="true" :isError="true" />
        <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel"
            :currentCredit="pageProps.auth.user?.credit || 0" :additionalCredit="additionalCredit" />

            <PopupN
        v-if="isOpenWarrningUseVoice"
        title="Cảnh báo !"
        :message="'Tính năng này có thể sử dụng vào mục đích xấu nên để sử dụng tính năng này cần phải liên hệ với quản trị viên để ký cam kết sử dụng theo đúng pháp luật hiện hành. Liên hệ quản trị viên theo sđt <strong>098 878 6699</strong> (Mr. Hùng)'"
        cancelButtonText="Đóng"
        @cancel="isOpenWarrningUseVoice = false"
    />

    <PostForm ref="postFormRef" @onSuccess="onSuccessPostForm" />


</template>

<script setup>
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
import FormShareLink from "@/Components/FormShareLink.vue";
import Modal from "@/Components/Modal.vue";
import Popup from '@/Components/Popup.vue';
import PopupAff from '@/Components/PopupAff.vue';
import { useTextToSpeech } from "@/helper/useTextToSpeech";
import { router, usePage } from "@inertiajs/vue3";
import axios from "axios";
import "cropperjs/dist/cropper.css";
import { computed, nextTick, onBeforeUnmount, onMounted, onUnmounted, ref, watch, defineEmits } from "vue";
import VueCropper from "vue-cropperjs";
import { toast } from "vue3-toastify";
import CreditBuyModal from '../../../Components/ModalBuyCredit/BuyCredit.vue';
import { systemThemes as themes } from "./Components/CardStyleEditor/themes";
import ImageDropdown from "@/Components/ImageDropdown.vue";
import ImageList from "@/Components/ImageList.vue";
import Step from "@/Components/Step/Index.vue";
import PopupN from '@/Components/Popup/Index.vue'
import PostForm from '@/Components/ShareAiText/PostForm.vue';

const props = defineProps({
    imageUrl: String,
    topic: String,
    businessProjectId: Number,
    xFades:String,
    positionCameras:String,
});
onMounted( async () => {
    if (props.topic) {
        topic.value = props.topic
    }
    if (props.businessProjectId) {
        businessProjectId.value = props.businessProjectId
    }
    await listXfade()
})
watch(() => props.topic, (value) => {
    topic.value = value
})
const { props: pageProps } = usePage();
const user = computed(() => pageProps.auth.user);
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);
const showAffKeyPopup = ref(false);
const showError = ref(false);
const keySlide = ref(-1)
const keySlideCreate = ref(-1)
const validDimensions = computed(() => {
    return ["9:16", "16:9"];
});
const showConfirm = ref(false)
const isShowUploadFileOrRecord = ref(false);
const isOpenWarrningUseVoice = ref(false);
const audioFile_clonevoice = ref(null);
const audioFile = ref(null);
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const imageUrlCrop = ref(null)
const enableCaption = ref(false);
const enableContent = ref(true)
const fileUploadV2 = ref(null)
const isLoadingClonedVoices = ref(false);
const clonedVoices = ref([]);
const isDropdownOpen = ref(false);
const postFormRef = ref(null);
const { listVoice,handleTextToSpeech } = useTextToSpeech()
const emit = defineEmits(['saveGenerationResult'])

const imageTypeActive = ref([]);
const selectimageActive = (image,index) => {
    imageTypeActive.value[index] = image;
    isDropdownOpen.value = false;
    slideContents.value[index].updateLoadImage = 'library';
}
const handleChangeInputImage = (event, index) => {
    const value = event.target.value
    if(value === 'library') {
        imageTypeActive.value[index] = {
            key: "",
            value: "Kho ảnh"
        }
    }
    slideContents.value[index].updateLoadImage = value
}
const handleSelectSelfImage = async (image,index) => {
    if(videoRef.value) {
        showConfirm.value = true
        return false;
    }
    var isSizeImage = await checkSizeImage(image.s3_url, index)
    if(!isSizeImage) {
        return;
    }
    var isCrop = await checkCropImage(image.s3_url)
    if(!isCrop) {
        keySlide.value = index
    } else {
        slideContents.value[index].imageUrl = image.s3_url
    }
    slideContents.value[index].imageUrl = image.s3_url
    slideContents.value[index].imageFile = null;
    slideContents.value[index].s3_image_url = image.s3_url
    slideContents.value[index].updateLoadImage = 'library';
    // scroll to image_index
    const element = document.getElementById(`image_${index}`);
    element.scrollIntoView({ behavior: "smooth" });
}

const checkCropImage = async (imageUrl) => {
    var url = route("ai-image.showImage", { s3_url:imageUrl})
    const response = await axios.get(url);

    var imageS3Url = "";
    if (response.data.success) {
        imageS3Url = 'data:image/jpeg;base64,'+response.data.data;
    } else {
        console.error('Failed to fetch media list:', response.data.errors);
    }

    const blob = base64ToBlob(imageS3Url, 'image/jpeg');
    const {width, height } = await getImageDimensions(blob)
    var imageRatio = (width/height)

    if (imageRatio < 0.5 || imageRatio > 2) {
        isCropping.value = true;
        imageGenerate.value = null;
        imageUrlCrop.value = imageS3Url
        checkImageTransparency(imageUrlCrop.value, function (isTransparent) {
            isTransparentImage.value = isTransparent;
        });
        return false
    }
    return true
}
function base64ToBlob(base64, contentType = '', sliceSize = 512) {
  const byteCharacters = atob(base64.split(',')[1]); // Loại bỏ tiền tố "data:image/jpeg;base64,"
  const byteArrays = [];
  for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
    const slice = byteCharacters.slice(offset, offset + sliceSize);
    const byteNumbers = new Array(slice.length);
    for (let i = 0; i < slice.length; i++) {
      byteNumbers[i] = slice.charCodeAt(i);
    }
    const byteArray = new Uint8Array(byteNumbers);
    byteArrays.push(byteArray);
  }
  return new Blob(byteArrays, { type: contentType });
}

//watch listVoice change then update
const handleUploadFile = () => {
    if(videoRef.value) {
        showConfirm.value = true
        return false;
    }
    topic.value = ""
    enableContent.value = false
    fileUploadV2.value.click();
};
const popupMessageError = ref("")
const enterContent = () => {
    if(videoRef.value) {
        showConfirm.value = true
        return false;
    }
    inputFileName.value = ""
    enableContent.value = true
}
const removeImage = (index) => {
    if(videoRef.value) {
        showConfirm.value = true
        return false;
    }
    uploadedImages.value.splice(index, 1);
    if (numberImageSelect.value > 1) {
        numberImageSelect.value -= 1;
    }
};
const showConfirmCreateVideo = ref(false)
const isShowColor = ref(false)
const enablePosition = ref("bottom75")
const captionerColors = computed(() => {
    return ["black", "white", "grey", "red", "yellow", "blue", "green"];
});
const captionerColor = ref(captionerColors.value[0]);
const toggleShowColor = (event) => {
  event.stopPropagation(); // Prevent event from propagating to the document
  isShowColor.value = !isShowColor.value;
};
const changeColor = (color) => {
    captionerColor.value = color
    isShowColor.value = false
}
const changePosition = (pos) => {
    enablePosition.value = pos
}
const colorPickerRef = ref(null)
const handleClickOutside = (event) => {
  if (colorPickerRef.value && !colorPickerRef.value.contains(event.target)) {
    isShowColor.value = false;
  }
};
const enableFade = ref(false)
const onOffFade = (value) => {
    enableFade.value = value
}

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});

const checkIcon = (backgroundColor) => {
    const lightColors = ["white", "yellow", "grey"];
    const darkColors = ["black", "blue", "green", "red"];

    if (lightColors.includes(backgroundColor)) {
        return '/assets/img/icon/check-black.png';
    }

    if (darkColors.includes(backgroundColor)) {
        return '/assets/img/icon/check-white.png';
    }

    return '/assets/img/icon/check-black.png';
}

const xFadeSeleted= ref("slideleft")
const xFades = ref([])
const positionCameras = ref([])
const showConfirmCaption = ref(false)
const onOffCaption = (value, type = 1) => {
    showConfirmCaption.value = false
    if(type == 1 && value) {
        showConfirmCaption.value = true
    }
    enableCaption.value = value
}
const fontCaptioner = ref("Arial/Arial_Bold.ttf")
const fontCaptioners = computed(() => {
    var fontCaptionerArr = ["Arial/Arial_Bold.ttf"];
    return fontCaptionerArr;
});

const listXfade = async () => {
    const response = await axios.get(route("video.getXface"))
    xFades.value = response.data.xFades
    positionCameras.value = response.data.positionCameras
}

const prompt = ref("Hãy tạo bức ảnh trực quan, chân thực, siêu chi tiết, độ phân giải cao, kết cấu da tự nhiên, biểu cảm khuôn mặt sống động., chủ đề dựa trên Nội dung chính của bài viết. - Đối tượng chính của ảnh: Khai thác từ phần [Nội dung bài viết] - Các yếu tố quan trọng: Hãy tìm kiếm các yếu tố liên quan tới Sản Phẩm / Dịch vụ mà bài viết quảng cáo. Hoặc nếu là bài viết không tập trung vào Sản phẩm/Dịch vụ thì tìm kiếm các yếu tố xung quanh bài viết. - Cảm xúc cần thể hiện: Cần truyền tải được [Cảm xúc mong muốn truyền tải] - Phong cách hình ảnh: Cần truyền tải được [Phong cách viết] - Đảm bảo rằng Không thêm bất kỳ ký tự nào vào ảnh theo bất cứ trường hợp nào - Tông màu và hiệu ứng phù hợp với nội dung: (VD: màu sắc thiên nhiên nếu sản phẩm là hữu cơ, màu sắc công nghệ nếu sản phẩm số...) Hãy đảm bảo mỗi ảnh thể hiện một khía cạnh khác nhau của bài viết, tạo nên sự đồng nhất nhưng không lặp lại")

const videoRef = ref(null)
const handleCreateImage = async (index, slide) => {
    if(slideContents.value[index].isLoadingImage) {
        return false
    }
    slideContents.value[index].isLoadingImage = true
    if(videoRef.value) {
        slideContents.value[index].isLoadingImage = false
        showConfirm.value = true
        return false;
    }
    var sub_title = slide.descriptions[0].sub_title
    if (sub_title.trim() == "") {
        slideContents.value[index].isLoadingImage = false
        toast.error("Vui lòng nhập mô tả nội dung video");
        return;
    }
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
      slideContents.value[index].isLoadingImage = false
      return; // Dừng nếu không đủ credit
    }
    slideContents.value[index].isLoadingImage = true
    slideContents.value[index].imageUrl = ""
    slideContents.value[index].updateLoadImage = 'ai';
    sub_title = `${sub_title} ${prompt.value}`;
    try {
        const response = await axios.post(route("ai-image.generate"), {
            description: sub_title,
            style: "",
            artist: "",
            height: 1024,
            width: 1024,
            model: "GPT Image",
            aspect_ratio: videoDimensions.value,
        });

        if (response.status === 200 && response.data.url) {
            // Add each response to the array for display
            for (var i = 0; i < slideContents.value.length; i++) {
                if (i == index) {
                    slideContents.value[i].imageUrl = response.data.url
                    slideContents.value[i].imageFile = null;
                    slideContents.value[i].s3_image_url = response.data.url
                }
            }
        } else {
            console.error("Không có URL hình ảnh trong phản hồi.");
            showError.value = true
            popupMessageError.value = "Không thể tạo hình ảnh. Vui lòng thử lại."
        }
        slideContents.value[index].isLoadingImage = false
    } catch(error) {
        showError.value = true
        popupMessageError.value = "Không thể tạo hình ảnh. Vui lòng thử lại."
        slideContents.value[index].isLoadingImage = false
    }
};

function handleUploadImage(index, slide) {
    fileImageItem.value?.click();
    keySlide.value = index
}

const downloadVideo = (videoUrl) => {
    var url = route("ai-video.downloadFile", { url: videoUrl, name: "video.mp4" });
    window.open(url, "_blank");
};

const redirectEdit = () => {
    window.location.href = route('video.edit', {id:videoId.value})
}

const openShareLink = () => {
    isShowShareLinkModal.value = true;
};

const closeShareLink = () => {
    isShowShareLinkModal.value = false;
};

const shareVideo = async (item) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: item.id,
            share_linkable_type: "Video",
        });

        shareLink.value = route("share-link.show", { token: response.data.data.link_token });
        openShareLink();
    } catch (error) {
        toast.error("Chia sẻ tin thất bại");
    }
};
const createPost = (videoUrl) => {
    let postForm = {
        files: [{
            s3_url: videoUrl,
            type: 'video'
        }],
    };

    postFormRef.value.openPostDetail(postForm);

};

const handleUploadImageItem = async (event) => {
    let type = event.target.files[0].type;
    if (allowedExtension.indexOf(type) < 0) {
        showError.value = true
        popupMessageError.value = "Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg, .webp"
        keySlide.value = -1
        return false;
    }
    const fileImage = event.target.files[0];

    const { width, height } = await getImageDimensions(fileImage)
    var imageRatio = (width / height)
    if (imageRatio < 0.5 || imageRatio > 2) {
        isCropping.value = true;
        imageGenerate.value = null;
        imageUrlCrop.value = URL.createObjectURL(fileImage)
        checkImageTransparency(imageUrlCrop.value, function (isTransparent) {
            isTransparentImage.value = isTransparent;
        });
        fileImageItem.value.value = ""
        return false
    }

    slideContents.value[keySlide.value].imageUrl = URL.createObjectURL(fileImage);
    slideContents.value[keySlide.value].imageFile = fileImage;
    slideContents.value[keySlide.value].updateLoadImage = 'upload';
    keySlide.value = -1
    fileImageItem.value.value = ""
}

const showCamera = ref(false);
let mediaStream = null;
const error = ref("");
const uploadedImages = ref([]);
const enableCamera = ref(false)
const initializeCamera = async () => {
    if(videoRef.value) {
        showConfirm.value = true
        return false;
    }
    enableCamera.value = true
    try {
        showCamera.value = true;
        await nextTick();
        await startCamera();
    } catch (err) {
        console.error("Failed to initialize camera:", err);
        showCamera.value = false;
    }
};

const capturePhoto = async () => {
    if (!videoRef.value) return;

    const canvas = document.createElement("canvas");
    const video = videoRef.value;
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const ctx = canvas.getContext("2d");
    // Flip the image horizontally to match the preview
    ctx.scale(-1, 1);
    ctx.translate(-canvas.width, 0);
    ctx.drawImage(video, 0, 0);

    const imageUrl = canvas.toDataURL("image/jpeg", 0.9);
    uploadedImages.value = []
    uploadedImages.value.push(imageUrl);
    // Convert base64 to File object
    fetch(imageUrl)
        .then((res) => res.blob())
        .then((blob) => {
            const file = new File([blob], "camera-capture.jpg", { type: "image/jpeg" });
            imageProductFile.value = file;
            fileCreateVideo.value = file;
            const data = pasteToFile(fileCreateVideo.value, 1);
        });

    stopCamera();
};
const showSlide = (index) => {
    slideContents.value[index].showSlide = false
}

const hideSlide = (index) => {
    slideContents.value[index].showSlide = true
}

const startCamera = async () => {
    try {
        if (!navigator.mediaDevices?.getUserMedia) {
            throw new Error("Browser không hỗ trợ truy cập camera");
        }

        mediaStream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 1280 },
                height: { ideal: 720 },
                facingMode: "user",
            },
            audio: false,
        });

        if (!videoRef.value) {
            throw new Error("Video element không tồn tại");
        }

        videoRef.value.srcObject = mediaStream;
        await videoRef.value.play();
        error.value = "";
    } catch (err) {
        console.error("Camera error:", err);
        error.value = `Không thể truy cập camera: ${err.message}`;
        await stopCamera();
        throw err;
    }
};

const stopCamera = async () => {
    if (mediaStream) {
        mediaStream.getTracks().forEach((track) => track.stop());
        mediaStream = null;
    }

    if (videoRef.value) {
        videoRef.value.srcObject = null;
    }

    showCamera.value = false;
    error.value = "";
};

function countWords(str) {
    const words = str.trim().split(/\s+/);
    return words.filter(word => word.length > 0).length;
}

const handleUpdateCaption = (index, type) => {
    for (var i = 0; i < slideContents.value.length; i++) {
        if (i == index) {
            if (type == 1) {
                slideContents.value[i].isOn = true
                slideContents.value[i].isOff = false
            } else {
                slideContents.value[i].isOn = false
                slideContents.value[i].isOff = true
            }
            console.log(slideContents.value[i].isOn)
        }
    }
}
const keyUpload = ref(-1)
const fileInput = ref(null);
const fileInputV2 = ref(null);
const fileImageProduct = ref(null)
const fileImageItem = ref(null)
const handleUploadClick = (index) => {
    keyUpload.value = index
    fileInput.value?.click();
};
const handleUploadClickV2 = (index) => {
    fileInputV2.value?.click();
};
const uploadImageProduct = (index) => {
    enableCamera.value = false
    fileImageProduct.value?.click();
};
const messageSuccessVideo = ref("Video của bạn đã xong. Nếu muốn chỉnh sửa video thì bấm vào đây.")
const indexConfirm = ref(-1)
const closeConfirm = () => {
    indexConfirm.value = -1
    showConfirmCreateVideo.value = false
}
const acceptConfirm = async () => {
    showConfirmCreateVideo.value = false
    previewVideo(indexConfirm.value)
}
const previewVideo = async (index) => {
    if(videoRef.value) {
        showConfirm.value = true
        return false;
    }
    if(slideContents.value[index].isLoadingVideo) {
        return false;
    }
    if(slideContents.value[index].videoRef && indexConfirm.value == -1) {
        indexConfirm.value = index
        showConfirmCreateVideo.value = true
        return false;
    }
    indexConfirm.value = -1
    try {
        var message = validateForm(index)
        if (message) {
            showError.value = true
            popupMessageError.value = message
            return false;
        }
        if (keySlideCreate.value >= 0) {
            return
        }
        keySlideCreate.value = index
        slideContents.value[keySlideCreate.value].videoRef = ""
        if (!slideContents.value[keySlideCreate.value].imageUrl) {
            keySlideCreate.value = -1
            return false;
        }
        var isSizeImage = await checkSizeImage(slideContents.value[keySlideCreate.value].imageUrl, index)
        if(!isSizeImage) {
            keySlideCreate.value = -1
            return false;
        }
        var dimension = videoDimensions.value
        slideContents.value[keySlideCreate.value].isLoadingVideo = true
        slideContents.value[keySlideCreate.value].videoRef = ""
        await generateVideo()
    } catch (error) {
        if (keySlideCreate.value >= 0) {
            slideContents.value[keySlideCreate.value].isLoadingVideo = false
            keySlideCreate.value = -1
        }
    }
}

const checkSizeImage = async (imageUrl, index) => {
    try {
        const formData = new FormData();
        formData.append("image_url", imageUrl);
        if (slideContents.value[index].imageFile) {
            formData.append("image_file", slideContents.value[index].imageFile);
        }
        const response = await axios.post(route("short-video.checkSizeImage"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        if(response.data.success) {
            return true
        } else {
            showError.value = true
            popupMessageError.value = response.data.message
            return false
        }
    } catch(error) {
        showError.value = true
        popupMessageError.value = "Đã có lỗi trong quá trình tạo video. Xin vui lòng chọn ảnh khác";
        return false
    }
}

const generateVideo = async () => {
    const voiceType = slideContents.value[keySlideCreate.value].voice_type

    const slide = slideContents.value[keySlideCreate.value]
    var audioDescription = slide.descriptions[0].description
    var countWord = countWords(audioDescription)
    if(countWord > 20) {
        showError.value = true
        popupMessageError.value = "nội dung thuyết mình của bạn có thể vượt quá 5s. Vui lòng điều chỉnh thuyết minh không quá 20 từ"
        slideContents.value[keySlideCreate.value].isLoadingVideo = false
        keySlideCreate.value = -1
        return
    }
    var sub_title = slide.descriptions[0].sub_title
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        isLoading.value = false;
        keySlideCreate.value = -1
        return; // Dừng nếu không đủ credit
    }
    if (!slideContents.value[keySlideCreate.value].isVoice) {
        if (!voiceType) {
            showError.value = true
            popupMessageError.value = "Vui lòng chọn giọng nói cho video"
            slideContents.value[keySlideCreate.value].isLoadingVideo = false
            keySlideCreate.value = -1
            throw new Error("Vui lòng chọn giọng nói cho video")
        }
        else {
            try {
                const result = await handleTextToSpeech({
                text: audioDescription,
                voice: voiceType,
                cloned: listVoice.value.find((voice) => voice.type === voiceType).model === 'cloned',
                screen_id:4
            })
                if (result) {
                    const urlAudio = result;
                    slideContents.value[keySlideCreate.value].audioUrl = urlAudio;
                    slideContents.value[keySlideCreate.value].isAudioDescription = false;
                }
            } catch (error) {
               var checkGoogle = await textToSpeechGoogle()
               if(!checkGoogle) {
                    showError.value = true
                    popupMessageError.value = "Đã có lỗi trong quá trình tạo âm thanh. Xin vui lòng chọn giọng khác và tạo lại";
                    slideContents.value[keySlideCreate.value].isLoadingVideo = false
                    keySlideCreate.value = -1
                    return false
               }
            }
        }
    }

    var isAudio = await checkAudio(slideContents.value[keySlideCreate.value].audioUrl)
    if(!isAudio) {
        slideContents.value[keySlideCreate.value].isLoadingVideo = false
        keySlideCreate.value = -1
        return;
    }
    const formData = new FormData();
    formData.append("video_description", sub_title);
    formData.append("audio_description", audioDescription);
    formData.append("model", "Runway/gen3-alphaturbo");
    formData.append("duration", 5);
    formData.append("scene_number", keySlideCreate.value);
    formData.append("number_result", 5);
    formData.append("voice_type", voiceType);
    formData.append("resolution", videoDimensions.value);
    if (slideContents.value[keySlideCreate.value].imageFile) {
        formData.append("image_file", slideContents.value[keySlideCreate.value].imageFile);
    } else {
        formData.append("s3_image_url", slideContents.value[keySlideCreate.value].s3_image_url);
    }
    const response = await axios.post(route("short-video.generate-video-audio"), formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });

    if (response.status === 200) {
        slideContents.value[keySlideCreate.value].videoRef = null;
        slideContents.value[keySlideCreate.value].shortVideoNewId = response?.data?.id;
    } else {

    }

};

const checkAudio = async (audio_url) => {
    try {
        const formData = new FormData();
        formData.append("audio_url", audio_url);
        const response = await axios.post(route("short-video.checkAudio"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        if(response.data.success) {
            return true
        } else {
            showError.value = true
            popupMessageError.value = response.data.message
            console.log(response.data.message)
            return false
        }
    } catch(error) {
        showError.value = true
        popupMessageError.value = "Đã có lỗi trong quá trình tạo âm thanh. Xin vui lòng chọn giọng khác và tạo lại";
        return false
    }
}

const handleAudioFileChange = (event) => {
     if(videoRef.value) {
        showConfirm.value = true
        return false;
    }
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav"];
    if (!allowedAudioTypes.includes(type)) {
        showError.value = true
        popupMessageError.value = "Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav."
        return;
    }
    for (var i = 0; i < slideContents.value.length; i++) {
        if (i == keyUpload.value) {
            const audioInput = event.target.files[0]
            slideContents.value[i].audioFile = event.target.files[0];
            slideContents.value[i].audioName = audioInput ? audioInput.name : 'Chưa có tệp nào được chọn';
            slideContents.value[i].isUploadAudio = true
        }
    }
    fileInput.value.value = ""
};
const audioInputV2 = ref(null)
const audioNameV2 = ref("")
const handleAudioFileChangeV2 = (event) => {
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav", "audio/mp4", "video/mp4"];
    if (!allowedAudioTypes.includes(type)) {
        showError.value = true
        popupMessageError.value = "Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3, .wav hoặc .mp4."
        return;
    }
    audioInputV2.value = event.target.files[0]
    audioNameV2.value = audioInputV2.value.name
    fileInputV2.value.value = ""
};
const topic2 = ref("")
const contentImage = ref("")
const contentFile = ref("")
const imageProductUrl = ref(null)
const imageProductFile = ref(null)
let allowedExtension = ["image/jpeg", "image/jpg", "image/png", "image/webp"];
const handleImageProduct = async (event) => {
    let type = event.target.files[0].type;
    if (allowedExtension.indexOf(type) < 0) {
        showError.value = true
        popupMessageError.value = "Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg, .webp"
        return false;
    }
    const fileImage = event.target.files[0];
    fileCreateVideo.value = fileImage;
    const data = await pasteToFile(fileCreateVideo.value, 1);
    fileImageProduct.value.value = "";
    if(!data) {
        return false
    }
    imageProductFile.value = fileImage
    imageProductUrl.value = URL.createObjectURL(fileImage);
    contentImage.value = data.content
    if (contentFile.value && contentImage.value) {
        topic2.value = contentFile.value +" và "+ contentImage.value
    } else if(contentImage.value) {
        topic2.value = contentImage.value
    } else if(contentFile.value) {
        topic2.value = contentFile.value
    }
};

const loadSlideContents = () => {
    const storedSlideContents = localStorage.getItem("slideContents");
    const storeTopic = localStorage.getItem("topic");

    if (storedSlideContents && storeTopic) {
        try {
            const slideContents = JSON.parse(storedSlideContents);
            topic.value = storeTopic;
            localStorage.removeItem("slideContents");
            localStorage.removeItem("topic");
            localStorage.removeItem("numberOfSlides");
            return slideContents;
        } catch (error) {
            console.error("Error parsing slideContents from local storage:", error);
            return [];
        }
    } else {
        console.log("No slideContents found in local storage.");
        return [];
    }
};

const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)

const avatars = ref([]);
const fetchVoiceTypes = async () => {
    try {
        const response = await axios.get(route('voice-type.list'));
        clonedVoices.value = response.data.data;
    } catch (error) {
        console.error("Error fetching voice types:", error);
    }
};
onMounted(() => {
    fetchAvatars();
    fetchVoiceTypes();
});

const fetchAvatars = async () => {
    try {
        const response = await axios.get(route("text-to-video.get-list-presenter"));
        avatars.value = response.data.data.data;
    } catch (error) {
        console.error("Error fetching avatars:", error);
    }
};
const durations = computed(() => {
    var durationArr = [];
    for(var i = 2; i <= 60; i++) {
        durationArr.push(i*5)
    }
    return durationArr;
});

const slideContents = ref([]);
const videoDuration = ref(10);
const videoDimensions = ref("16:9");
const loading = ref(false);
const isLoading = ref(false);
const isLoadingVideo = ref(false);
const topic = ref("");
const businessProjectId = ref(0);
const numberOfSlides = ref(3);

const themeActive = ref(themes[0]);
const mcVirtualId = ref();

const fileUpload = ref({ url: "", file: "" });

const handleChangeFile = (e) => {
    const reader = new FileReader();
    fileUpload.value.file = e.target.files[0];

    reader.onload = (e) => {
        fileUpload.value.url = e.target.result;
    };

    reader.readAsDataURL(fileUpload.value.file);
};

const generateMcByImage = async () => {
    try {
        loading.value = true;
        const formData = new FormData();
        formData.append("avatar", fileUpload.value.file);
        formData.append("content", "Xin chào mình là trợ lý ảo AIWOW");
        formData.append("voice", "vi-VN-HoaiMyNeural");

        const res = await axios.post(route("text-to-video.generate-mc-by-image"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        console.log(res);
        if (res.status === 200) {
            mcVirtualId.value = res.data.mc_virtual_id;
            alert("Đã tạo xong người thuyết trình của bạn");
            console.log("Video updated successfully before leaving the page");
        }
    } catch (error) {
        console.error("Error updating video before leaving the page:", error);
    } finally {
        loading.value = false;
    }
};

const addSlide = () => {
    slideContents.value.push({
        title: "",
        descriptions: [
            {
                sub_title: "",
                description: "",
            },
        ],
    });
};
const deleteSlide = (index) => {
    slideContents.value.splice(index, 1);
};

const showJsonText = ref(false);
const jsonText = ref("");
const contentShow = ref("");
const errorMessage = ref("");
const isSubmit = ref(false);

const validateErrors = ref({});
const fileCreateVideo = ref(null);
const inputFileName = ref(null)
const onFileChange = async (e) => {
    if(videoRef.value) {
        showConfirm.value = true
        return false;
    }
    var name = e.target.files[0].name;
    if(name.indexOf('doc') <= -1 && name.indexOf('dotx') <= -1 && name.indexOf('docx') <= -1 && name.indexOf('pdf') <= -1) {
        showError.value = true
        popupMessageError.value = "Vui lòng chọn file đúng định dạng .doc, .docx, .pdf"
        return false
    }
    const file = e.target.files[0];
    fileUploadV2.value.value = ""
    const data = await pasteToFile(file);
    if(!data) {
        return false
    }
    inputFileName.value = file.name
    contentFile.value = data.content
    if (contentFile.value && contentImage.value) {
        topic2.value = contentFile.value +" và "+ contentImage.value
    } else if(contentImage.value) {
        topic2.value = contentImage.value
    } else if(contentFile.value) {
        topic2.value = contentFile.value
    }
};

const pasteToFile = async (file, type = 0) => {
    try {
        loading.value = true;
        validateErrors.value = {};
        var image_url = ""
        if(file) {
            let name = file.name;
            if(name.indexOf('JPG') > -1 || name.indexOf('JPEG') > -1 || name.indexOf('PNG') > -1 || type == 1) {
                image_url = await uploadImagePro()
            }
        }
        const response = await axios.post(
            route("video.read-file"),
            {
                content: topic.value,
                file: file,
                imageUrl: image_url ? image_url : props.imageUrl
            },
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        if(type == 1) {
            contentImage.value = response.data.content
            if (contentFile.value && contentImage.value) {
                topic2.value = contentFile.value +" và "+ contentImage.value
            } else if(contentImage.value) {
                topic2.value = contentImage.value
            } else if(contentFile.value) {
                topic2.value = contentFile.value
            }
        }
        loading.value = false;
        return response.data;
    } catch (error) {
        if(type == 0) {
            popupMessageError.value = "File của bạn không hợp lệ. Xin vui lòng tải file hợp lệ"
        } else {
            popupMessageError.value = error.response.data.message
        }

        showError.value = true
        loading.value = false;
    }
};

const uploadImagePro = async () => {
    if(videoRef.value) {
        showConfirm.value = true
        return false;
    }
    const formData = new FormData();
    formData.append('image_file', fileCreateVideo.value);

    const response = await axios.post(route("video.uploadImage"), formData);
    if (response.data.image_url) {
        return response.data.image_url;
    } else {
        showError.value = true
        popupMessageError.value = response.data.message
        return false
    }
}

const fileName = ref("");
const submitForm = async () => {
    // slideContents.value = [
    //     {
    //         "title": "Slide 1: Gi\u1edbi thi\u1ec7u v\u1ec1 con m\u00e8o",
    //         "descriptions": [
    //             {
    //                 "sub_title": "Con m\u00e8o \u2013 ng\u01b0\u1eddi b\u1ea1n trung th\u00e0nh c\u1ee7a con ng\u01b0\u1eddi",
    //                 "description": "M\u00e8o l\u00e0 lo\u00e0i \u0111\u1ed9ng v\u1eadt th\u00e2n th\u01b0\u01a1ng v\u00e0 ph\u1ed5 bi\u1ebfn tr\u00ean to\u00e0n th\u1ebf gi\u1edbi."
    //             }
    //         ]
    //     },
    //     {
    //         "title": "Slide 2: \u0110\u1eb7c \u0111i\u1ec3m c\u1ee7a m\u00e8o",
    //         "descriptions": [
    //             {
    //                 "sub_title": "Nh\u1eefng \u0111\u1eb7c \u0111i\u1ec3m n\u1ed5i b\u1eadt c\u1ee7a lo\u00e0i m\u00e8o",
    //                 "description": "M\u00e8o c\u00f3 b\u1ed9 l\u00f4ng m\u1ec1m m\u1ea1i v\u00e0 d\u1ea1 d\u00e0y nh\u1ea1y c\u1ea3m, gi\u00fap ch\u00fang ho\u1ea1t \u0111\u1ed9ng linh ho\u1ea1t."
    //             }
    //         ]
    //     }
    // ]
    if(videoRef.value) {
        showConfirm.value = true
        return false;
    }
   loading.value = true;
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        loading.value = false;
        return; // Dừng nếu không đủ credit
    }
    numberOfSlides.value = videoDuration.value / 5

    errorMessage.value = "";
    validateErrors.value = {};
    var topicRe = ""
    if(inputFileName.value) {
        topicRe = topic2.value
    } else {
        topicRe = topic.value
        if(contentImage.value) {
            topicRe = topic.value + " và " + contentImage.value
        }
    }
    if (!topicRe.trim()) {
        validateErrors.value.content = ["Nội dung kịch bản không được để trống"];
        loading.value = false;
        return
    }
    videoRef.value = ""
    resultValue.value = ""

    try {
        const url = `${route("text-to-video.createVideoStreamV2")}`;
        const response = await axios.post(url, {topic:topicRe, numberOfSlides:numberOfSlides.value});
        if(response.data.data) {
            slideContents.value = response.data.data
            if(listVoice.value) {
                for (var i = 0; i < slideContents.value.length; i++) {
                    const voiceDefault = listVoice.value.find((voice) => voice.type === 'hn_female_hermer_stor_48k-fhg')
                    if (voiceDefault) {
                        slideContents.value[i].voice_type = voiceDefault.type
                    } else {
                        slideContents.value[i].voice_type = listVoice.value[0].type
                    }
                    slideContents.value[i].position_camera = 1
                    slideContents.value[i].image_description = ""
                }
            }
            loading.value = false;
        }
    } catch (error) {
        console.error("Error creating slides:", error);
        loading.value = false;
    }
};

const createSlidePpt = async () => {
    loading.value = true;
    try {
        // storageData();
        router.post(route("slide-ai.mapping-ppt"), {
            slide_contents: slideContents.value,
            topic: topic.value,
        });
    } catch (error) {
        console.error("Error creating slide:", error);
    }
};
let showBuyCreditPopup = ref(false);
const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};
const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};
const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append('type', 'text_to_video');
        formData.append('number_result', numberOfSlides.value || 1);

        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true
            if (response.data.affCode == 'aff_trial') {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này."
                acceptUpgrade.value = false
            }
            return false;
        } else {
            additionalCredit.value = response?.data?.data?.total_price - pageProps.auth.user.credit;
            // Không đủ credit, hiển thị modal yêu cầu nạp thêm
            showBuyCreditModal();
            return false;
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi kiểm tra credit:", error);
        toast.error("Không thể kiểm tra credit. Vui lòng thử lại.");
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            Object.values(errors).forEach(errorMessages => {
                if (Array.isArray(errorMessages)) {
                    errorMessages.forEach(error => {
                        toast.error(error);
                    });
                } else {
                    toast.error(errorMessages);
                }
            });
        } else {
            if (error.response) {
                toast.error((error.response.data.message || 'Đã xảy ra lỗi.'));
            } else if (error.request) {
                toast.error('Không thể kết nối đến server.');
            } else {
                toast.error('Lỗi: ' + error.message);
            }
        }
        return false;
    }
};

const createSlide = async () => {
    if(videoRef.value) {
        showConfirm.value = true
        return false;
    }
    try {
        for (var i = 0; i < slideContents.value.length; i++) {
            var index = i + 1
            var message = validateForm(i)
            if (message) {
                showError.value = true
                popupMessageError.value = message
                return false;
            }
            if(!slideContents.value[i].videoRef) {
                showError.value = true
                popupMessageError.value = "Phân cảnh " + index + " chưa tạo video"
                return
            }
        }
        const videoCreateElement = document.getElementById("video-create");
        if (videoCreateElement) {
            videoCreateElement.scrollIntoView({ behavior: "smooth" });
        }
        isLoadingVideo.value = true;
    } catch (error) {
        isLoadingVideo.value = false;
    }
};
const validateForm = (i) => {
    var index = i + 1;
    if (!slideContents.value[i].imageUrl) {
        var message = "Phân cảnh " + index + " chưa tạo ảnh";
        return message
    }
    const slide = slideContents.value[i]
    var audioDescription = slide.descriptions[0].description
    if (!audioDescription) {
        var message = "Phân cảnh " + index + " nội dung thuyết minh đang để trống";
        return message
    }
    if (audioDescription.trim().indexOf("[") > -1 || audioDescription.trim().indexOf("]") > -1) {
        var message = "Phân cảnh " + index + ' nội dung thuyết minh đang chưa ký tự đặc biệt dấu "[ hoặc ]"';
        return message
    }
    if (audioDescription.trim().indexOf("https") > -1 || audioDescription.trim().indexOf("http") > -1) {
        var message = "Phân cảnh " + index + ' nội dung thuyết minh đang chứa đường dẫn http hoặc https';
        return message
    }
    if (audioDescription.trim().indexOf("..") > -1 && audioDescription.trim().length < 4) {
        var message = "Phân cảnh " + index + ' nội dung thuyết minh đang chứa dấu ..';
        return message
    }
    if (audioDescription.trim().length < 2) {
        var message = "Phân cảnh " + index + ' nội dung thuyết minh tối thiểu 2 ký tự';
        return message
    }
    if(!slide.audioUrl && !slide.voice_type) {
        var message = "Phân cảnh " + index + " chưa chọn giọng";
        return message
    }
}


const dataPreviews = ref([]);
// Preview Slides
const dataPreviewSlides = async () => {
    try {
        const respone = await axios.post(route("text-to-video.mapping-preview"), {
            slide_contents: slideContents.value,
            file_name: fileName.value,
            theme: themeActive.value,
        });

        const slideSuggestions = respone.data.slideSuggestions;
        const theme = respone.data.theme;
        topic.value = respone.data.topic;
        slideSuggestions.forEach((slide) => {
            if (!slide.virtual) {
                slide.virtual = {
                    id: "v2_public_alex@qcvo4gupoy",
                    name: "alex",
                    s3_url: "/assets/img/gifs/alex_360.gif",
                    x: 100,
                    y: 130,
                    width: 300,
                    animation: "",
                    delayTime: 0,
                };
            }

            if (!slide.voice_type) {
                slide.voice_type = "old_male";
            }
        });

        // update new data for slideContents
        dataPreviews.value = slideSuggestions.length > 0 ? slideSuggestions.slice(0, slideSuggestions.length - 1) : [];
    } catch (error) {
        console.error("Error creating slide:", error);
    }
};

// follow change data
watch(dataPreviews, (newVal) => {
    if (newVal.length > 0) {
        slideContents.value = [...newVal];
    } else {
        slideContents.value = [];
    }
});

const storageData = () => {
    localStorage.setItem("slideContents", JSON.stringify(slideContents.value));
    localStorage.setItem("topic", topic.value);
    localStorage.setItem("numberOfSlides", numberOfSlides.value);
};

const clearStorageData = () => {
    localStorage.removeItem("slideContents");
    localStorage.removeItem("topic");
    localStorage.removeItem("numberOfSlides");
    window.location.reload();
};
const isRecording = ref(false);
const audioBlob = ref(null);
const audioUrl = ref(null);
let mediaRecorder = null;
let audioChunks = [];
const audioResult = ref({});
let device = ref(null);
const resultValue = ref(null)
const isTranscribing = ref(false);
const startRecording = async () => {
    if (!isRecording.value) {
        // Nếu chưa ghi âm
        try {
            isRecording.value = true; // Bắt đầu ghi âm
            device = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(device);

            // Khi có dữ liệu âm thanh
            mediaRecorder.addEventListener("dataavailable", (event) => {
                audioChunks.value.push(event.data);
            });

            // Khi dừng ghi âm
            mediaRecorder.addEventListener("stop", async () => {
                audioBlob.value = new Blob(audioChunks.value, { type: "audio/mp3" });
                audioUrl.value = URL.createObjectURL(audioBlob.value);

                // Tạo FormData và gửi yêu cầu API
                const formData = new FormData();

                // Sửa lại: Gói Blob thành File object trước khi thêm vào FormData
                const file = new File([audioBlob.value], "audio.mp3", { type: "audio/mp3" });
                formData.append("audio", file);

                // isTranscribing.value = true;
                try {
                    const response = await axios.post("/speech-to-text", formData, {
                        headers: { "Content-Type": "multipart/form-data" },
                    });
                    // Xử lý văn bản trả về từ API

                    topic.value = response?.data?.text || "Vui lòng thử lại";
                } catch (error) {
                    console.error("Error in Speech-to-Text:", error);
                } finally {
                    // isTranscribing.value = false;
                }

                isRecording.value = false;
            });

            // Bắt đầu ghi âm
            mediaRecorder.onstart = () => {
                audioChunks.value = []; // Xóa các đoạn âm thanh trước đó
            };

            mediaRecorder.start(); // Bắt đầu ghi
        } catch (error) {
            console.error("Lỗi khi bắt đầu ghi âm:", error);
            isRecording.value = false; // Trở lại trạng thái chưa ghi âm nếu có lỗi
        }
    } else {
        // Nếu đang ghi âm
        isRecording.value = false; // Dừng ghi âm
        mediaRecorder.stop(); // Kết thúc ghi âm
        device.getTracks().forEach((track) => track.stop()); // Dừng tất cả các tracks
    }
};

const stopRecording = () => {
    if (mediaRecorder) {
        mediaRecorder.stop();
        isRecording.value = false;
    }
};

const textToSpeechGoogle = async () => {
    const slide = slideContents.value[keySlideCreate.value]
    var audioDescription = slide.descriptions[0].description
    if (audioDescription) {
        const result = await axios.post(route("ai-audio.text-to-speech-google"), {
            text: audioDescription,
        })
        if (result.data.success) {
            const urlAudio = result.data.data;
            slideContents.value[keySlideCreate.value].audioUrl = urlAudio;
            slideContents.value[keySlideCreate.value].isAudioDescription = true;
        }
    }
    return true
}
const videoId = ref(null)
const isCallUploadAudio = ref(false)
setInterval(async () => {
    try {
        if (isLoadingVideo.value && keySlideCreate.value == -1) {
        var ids = []
        for (var i = 0; i < slideContents.value.length; i++) {
            if (!slideContents.value[i].videoRef) {
                previewVideo(i)
                return
            }
            ids.push(slideContents.value[i].shortVideoId)
        }
        mergeData(ids)
        keySlideCreate.value = 1000
    }
    } catch (error) {
        isLoadingVideo.value = false;
        toast.error("Đã xảy ra lỗi khi tạo video");
    }
}, 10000)
const messageXfade = ref(null)
const mergeData = async (ids) => {
    if (ids.length < 2 && videoId.value) {
        return false;
    }
    const formData = new FormData();
    formData.append('ratio', videoDimensions.value);
    if (imageProductFile.value) {
        formData.append('image_file', imageProductFile.value);
    }
    formData.append('description', topic.value);
    formData.append('is_transcription', enableCaption.value);
    formData.append('duration', videoDuration.value);
    if(audioInputV2.value) {
        formData.append('audio_file', audioInputV2.value);
        formData.append('audio_name', audioNameV2.value);
    }
    formData.append('highlight_color', captionerColor.value);
    formData.append('subs_position', enablePosition.value);
    formData.append('font', fontCaptioner.value);
    formData.append('is_fade', enableFade.value);
    formData.append('x_fade', xFadeSeleted.value);
     formData.append('businessProjectId', businessProjectId.value);
    var url = route("video.mergeVideo", { ids: ids })
    try {
        const response = await axios.post(url, formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        if (response.data.success) {
            videoId.value = response.data.data.id
            if(response.data.message) {
                messageXfade.value = response.data.message
            }
            if (enableCaption.value && !isCallUploadAudio.value) {
                if (!isTranscription.value) {
                    await toggleTranscription(response?.data.data);
                    if (audioInputV2.value) {
                        isLoadingVideo.value = true;
                        const formData = new FormData();
                        formData.append('video_id', videoId.value);
                        formData.append('type', 5);
                        formData.append('audio_file', audioInputV2.value);
                        formData.append('audio_name', audioNameV2.value);
                        const response = await axios.post(
                            route("video.mergeAudioVideoQueue"),
                            formData,
                            {
                                headers: {
                                    "Content-Type": "multipart/form-data",
                                },
                            }
                        );
                        if (response.data.success) {
                            videoRef.value = response.data.s3_url
                            resultValue.value = response.data.data;
                            isLoadingVideo.value = false;
                            keySlideCreate.value = -1;
                            emit('saveGenerationResult', response.data.s3_url);
                            showErrorXfade()
                        }  else {
                            showError.value = true
                            popupMessageError.value = response.data.message
                            isLoadingVideo.value = false;
                            keySlideCreate.value = -1;
                        }
                    }
                }
            } else if (audioInputV2.value) {
                const formData = new FormData();
                formData.append('video_id', videoId.value);
                formData.append('type', 5);
                formData.append('audio_file', audioInputV2.value);
                formData.append('audio_name', audioNameV2.value);
                const response = await axios.post(
                    route("video.mergeAudioVideoQueue"),
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );
                if (response.data.success) {
                    videoRef.value = response.data.s3_url
                    emit('saveGenerationResult', response.data.s3_url);
                    resultValue.value = response.data.data;
                    isLoadingVideo.value = false;
                    keySlideCreate.value = -1;
                    showErrorXfade()
                } else {
                    showError.value = true
                    popupMessageError.value = response.data.message
                    isLoadingVideo.value = false;
                    keySlideCreate.value = -1;
                }
            } else {
                videoRef.value = response.data.s3_url
                emit('saveGenerationResult', response.data.s3_url);
                resultValue.value = response.data.data;
                isLoadingVideo.value = false;
                keySlideCreate.value = -1;
                showErrorXfade()
            }
        } else {
            showError.value = true
            popupMessageError.value = response.data.message
            isLoadingVideo.value = false;
            keySlideCreate.value = -1;
        }
    } catch(error) {
        showError.value = true
        isLoadingVideo.value = false;
        popupMessageError.value = response.data.message
        keySlideCreate.value = -1;
    }
}

const showErrorXfade = () => {
    if(messageXfade.value) {
        popupMessageError.value = messageXfade.value
        messageXfade.value = ""
        showError.value = true
    }
}

setInterval(async () => {
    if (keySlideCreate.value == 1000 || keySlideCreate.value < 0 || slideContents.value[keySlideCreate.value].isCallApi) {
        return false;
    }
    if (slideContents.value[keySlideCreate.value].shortVideoNewId && !slideContents.value[keySlideCreate.value].videoRef) {
        var url = "/short-video/get-video-url-run/" + slideContents.value[keySlideCreate.value].shortVideoNewId;
        slideContents.value[keySlideCreate.value].isCallApi = true
        const response = await axios.get(url);
        const s3_url = response.data.s3_url ? response.data.s3_url : ""
        const id = response.data.id ? response.data.id : 0
        if (response.data.s3_url) {
            slideContents.value[keySlideCreate.value].shortVideoId = id
            slideContents.value[keySlideCreate.value].shortVideoNewId = ""
            const slide = slideContents.value[keySlideCreate.value]
            var type = 4;
            var audioUrl = slide.audioUrl
            const formData = new FormData();
            formData.append('short_video_id', slideContents.value[keySlideCreate.value].shortVideoId);
            formData.append('type', type);
            formData.append('audioUrl', slideContents.value[keySlideCreate.value].audioUrl);
            try {
                const response = await axios.post(
                    route("short-video.mergeAudioVideoQueue"),
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );
                if (response.data.success) {
                    slideContents.value[keySlideCreate.value].isAudioDescription = false
                    slideContents.value[keySlideCreate.value].isLoadingVideo = false;
                    slideContents.value[keySlideCreate.value].videoRef = response.data.s3_url;
                } else {
                    showError.value = true
                    slideContents.value[keySlideCreate.value].isLoadingVideo = false;
                    popupMessageError.value = response.data.message
                }
                slideContents.value[keySlideCreate.value].isCallApi = false
                keySlideCreate.value = -1;
            } catch(error) {
                slideContents.value[keySlideCreate.value].isLoadingVideo = false;
                slideContents.value[keySlideCreate.value].isCallApi = false
                keySlideCreate.value = -1;
            }
        } else if(response.data.code == 500) {
            if(response.data.model == "Runway") {
                const formData = new FormData();
                formData.append("model", "kling");
                formData.append("duration", 5);
                formData.append("short_video_id", slideContents.value[keySlideCreate.value].shortVideoNewId);
                formData.append("resolution", videoDimensions.value);
                const response = await axios.post(route("short-video.generate-video-audio"), formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                });
                if (response.status != 200) {
                    showError.value = true
                    popupMessageError.value = response.data.message
                    slideContents.value[keySlideCreate.value].isLoadingVideo = false
                    keySlideCreate.value = -1
                }
                slideContents.value[keySlideCreate.value].videoRef = null
                slideContents.value[keySlideCreate.value].isCallApi = false
            } else {
                showError.value = true
                popupMessageError.value = response.data.message
                slideContents.value[keySlideCreate.value].isCallApi = false
                slideContents.value[keySlideCreate.value].isLoadingVideo = false
                keySlideCreate.value = -1
            }
        } else {
            slideContents.value[keySlideCreate.value].isCallApi = false
            return false;
        }
    }
}, 10000)
const isTranscription = ref(false)
const toggleTranscription = async (data) => {
    if (isTranscription.value) {
        return
    }
    isTranscription.value = true
    try {
        const response = await axios.post(route("video.create-video-with-transcription"), {
            video_id: data.id,
            duration: videoDuration.value,
            screen_id: 3,
            highlight_color:captionerColor.value,
            subs_position:enablePosition.value,
            font:fontCaptioner.value
        });
        if (!audioInputV2.value) {
            const transcription = response?.data?.video;
            emit('saveGenerationResult', response?.data?.video);
            videoRef.value = transcription
            data.s3_url = transcription
            resultValue.value = data;
            isLoadingVideo.value = false;
            keySlideCreate.value = -1;
            showErrorXfade()
        }
    } catch (error) {
        isTranscription.value = false
    } finally {
    }
};

const cropper = ref(null);
const isCropping = ref(false);
const imageName = ref('');
const imageGenerate = ref(null);
const imageUrl = ref(null)
const isTransparentImage = ref(null);
const imageInput = ref(null)
async function getImageDimensions(file) {
    let img = new Image();
    img.src = URL.createObjectURL(file);
    await img.decode();
    let width = img.width;
    let height = img.height;
    return {
        width,
        height,
    }
}
const aspectRatio = computed(() => {
    const resolution = videoDimensions.value;
    const [width, height] = resolution.split(":");
    return parseFloat(width) / parseFloat(height);
});
const cancelCropping = (key) => {
    if (imageUrlCrop.value) {
        const img = new Image();
        img.src = imageUrlCrop.value;
        img.onload = function () {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            const targetSize = 1080;
            const aspectRatio = img.width / img.height;
            if (aspectRatio > 1) {
                canvas.width = targetSize;
                canvas.height = targetSize / aspectRatio;
            } else {
                canvas.height = targetSize;
                canvas.width = targetSize * aspectRatio;
            }
            ctx.drawImage(img, 0, 0, img.width, img.height, 0, 0, canvas.width, canvas.height);
            const resizedImageUrl = canvas.toDataURL();
            const file = dataURLtoFile(resizedImageUrl, "resized-image.png");
            slideContents.value[keySlide.value].imageUrl = resizedImageUrl;
            slideContents.value[keySlide.value].imageFile = file;
            isCropping.value = false;
            keySlide.value = -1
            fileImageItem.value.value = ""
        };
    }
};
const cropImage = () => {
    if (cropper.value) {
        const croppedCanvas = cropper.value.getCroppedCanvas();
        let width = croppedCanvas.width;
        let height = croppedCanvas.height;
        if (width > 2048 || height > 2048) {
            const resizedCanvas = document.createElement("canvas");
            const ctx = resizedCanvas.getContext("2d");
            const maxDimension = 1080;
            if (width > height) {
                resizedCanvas.width = maxDimension;
                resizedCanvas.height = (height * maxDimension) / width;
            } else {
                resizedCanvas.height = maxDimension;
                resizedCanvas.width = (width * maxDimension) / height;
            }
            ctx.drawImage(croppedCanvas, 0, 0, width, height, 0, 0, resizedCanvas.width, resizedCanvas.height);
            croppedCanvas.width = resizedCanvas.width;
            croppedCanvas.height = resizedCanvas.height;
            croppedCanvas.getContext("2d").drawImage(resizedCanvas, 0, 0);
            width = croppedCanvas.width;
            height = croppedCanvas.height;
        }
        const croppedImage = croppedCanvas.toDataURL();
        const file = dataURLtoFile(croppedImage, "cropped-image.png");
        console.log("Image Dimensions:", width, "x", height);
        slideContents.value[keySlide.value].imageUrl = croppedImage;
        slideContents.value[keySlide.value].imageFile = file;
        keySlide.value = -1
        fileImageItem.value.value = ""
        isCropping.value = false;
        if (imageInput.value) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imageSelected.value.s3_url = e.target.result;
            };
            reader.readAsDataURL(imageInput.value);
            firebase
                .uploadImage(imageInput.value)
                .then((url) => {
                    imageSelected.value.s3_url = url
                })
                .catch((error) => {
                    console.error("Lỗi upload ảnh lên Firebase:", error);
                });
        }
    }
};
const dataURLtoFile = (dataUrl, fileName) => {
    const [header, base64Data] = dataUrl.split(";base64,");
    const mime = header.split(":")[1];
    const binary = atob(base64Data);
    const array = new Uint8Array(binary.length);
    for (let i = 0; i < binary.length; i++) {
        array[i] = binary.charCodeAt(i);
    }
    return new File([array], fileName, { type: mime });
};

function checkImageTransparency(imageUrl, callback) {
    const img = new Image();
    img.src = imageUrl;
    img.onload = function () {
        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");
        canvas.width = img.width;
        canvas.height = img.height;
        ctx.drawImage(img, 0, 0);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imageData.data;
        console.log(data)
        let hasTransparentPixel = false;
        for (let i = 0; i < data.length; i += 4) {
            if (data[i + 3] < 255) {
                // Kiểm tra alpha channel (pixel trong suốt)
                hasTransparentPixel = true;
                break;
            }
        }
        callback(hasTransparentPixel);
    };
    img.onerror = function () {
        callback(false);
    };
}
const checkCreditV2 = async ({
    model = 'd-id',
    type = 'mc_virtual',
    number_result = null
}) => {
    try {
        const formData = new FormData();
        formData.append('model', model);
        formData.append('type', type);
        if (number_result) {
            formData.append('number_result', number_result);
        }
        console.log("🚀 ~ checkCredit ~ formData", formData)
        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true
            if (response.data.affCode == 'aff_trial') {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này."
                acceptUpgrade.value = false
            }
            return false;
        } else {
            additionalCredit.value = response?.data?.data?.total_price - pageProps.auth.user.credit;
            // Không đủ credit, hiển thị modal yêu cầu nạp thêm
            showBuyCreditModal();
            return false;
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi kiểm tra credit:", error);
        toast.error("Không thể kiểm tra credit. Vui lòng thử lại.");
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            Object.values(errors).forEach(errorMessages => {
                if (Array.isArray(errorMessages)) {
                    errorMessages.forEach(error => {
                        toast.error(error);
                    });
                } else {
                    toast.error(errorMessages);
                }
            });
        } else {
            if (error.response) {
                toast.error((error.response.data.message || 'Đã xảy ra lỗi.'));
            } else if (error.request) {
                toast.error('Không thể kết nối đến server.');
            } else {
                toast.error('Lỗi: ' + error.message);
            }
        }
        return false;
    }
};
// api clone-voices api
const handleVoiceClone = async (indexSlide) => {
    if(videoRef.value) {
        showConfirm.value = true
        return false;
    }
    const videoDescription = slideContents.value[indexSlide].descriptions[0];
    if (!videoDescription) {
        toast.error("Vui lòng nhập lời thoại trước khi nhái giọng");
        return;
    }
    try {
        const formData = new FormData();
        if (!audioFileCloneVoice.value) {
            toast.warning("Vui lòng tải audio hoặc ghi âm giọng của bạn")
            return;
        }
        isLoadingClonedVoices.value = true;
        formData.append("name", window.location.hostname + "_" + user.value.id)
        formData.append("description", "Giọng nói thuyết trình mạnh mẽ")
        formData.append("files", audioFileCloneVoice.value);
        const response = await axios.post(route("ai-audio.clone-voice"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            }
        }
        );

        if (response.status === 200) {
            toast.success("Clone giọng thành công.");
            clonedVoices.value = [response.data.data]
            // selectedClonedVoice.value = response.data.data.type;

            // credits.value = response.data.data.credits;
            audioFileCloneVoice.value = ""
            // Sau khi voice đã được clone thì chuyển sang bước nhái giọng dựa theo lời thoại
            console.log('clonedVoices1', clonedVoices.value)
            await textToSpeech(indexSlide);
        } else {
            console.error("Phản hồi không thành công:", response.data);
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi gửi tin nhắn:", error.message);
    } finally {
        isLoadingClonedVoices.value = false;
    }
}
const handleShowUploadFileOrRecord = () => {
    console.log("isShowUploadFileOrRecord", isShowUploadFileOrRecord.value)
    isShowUploadFileOrRecord.value = !isShowUploadFileOrRecord.value;
}
const textToSpeech = async (index) => {
    slideContents.value[index].isVoice = true;
    if(videoRef.value) {
        showConfirm.value = true
        return false;
    }
    const videoDescription = slideContents.value[index].descriptions[0].description;
    if (!user.value.clone_voice) {
        isOpenWarrningUseVoice.value = true;
        return;
    }
    const hasEnoughCredit = await checkCreditV2({
        type: 'elevenlab',
        model: "Elevenlab",
        number_result:  videoDescription.length || null,
    });
    if (!hasEnoughCredit) {
        return; // Dừng nếu không đủ credit
    }
    if (clonedVoices.value.length === 0) {
        handleShowUploadFileOrRecord()
        return;
    }
    if (!videoDescription) {
        toast.error("Vui lòng nhập lời thoại trước khi nhái giọng");
        return;
    }
    if (clonedVoices.value.length === 0) {
        toast.error("Vui lòng chọn giọng nhái");
        return;
    }
    try {
        isLoadingClonedVoices.value = true;
        const result = await axios.post(route("ai-audio.text-to-speech"), {
            voice_id: clonedVoices.value[0].type,
            language: 'vi',
            text: videoDescription,
            isSaveDb: false,
            screen_id: 4
        })
        if (result.data.success) {
            const urlAudio = result.data.data;
            slideContents.value[index].audioUrl = urlAudio;
            slideContents.value[index].voice_type = null;
            slideContents.value[index].isAudioDescription = false;
        }
    } catch (error) {
        console.log(error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại sau");
    } finally {
        isLoadingClonedVoices.value = false;
    }
};
// Add new refs for recording
const isRecordVoice = ref(false);
const recordingTime = ref(0);
const recordingInterval = ref(null);
let recordedAudioChunks = [];
let recordingMediaRecorder = null;
const uploadedAudioPreview = ref(null);
const audioFileCloneVoice = ref(null);
const audioPreviewKey = ref(0);
const audioPreview = ref(null);
// Format time function
const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

// Start recording function
const startRecordingCloneVoice = async (index) => {
    if(videoRef.value) {
        showConfirm.value = true
        return false;
    }
    const videoDescription = slideContents.value[index].descriptions[0];
    if (!videoDescription) {
        toast.error("Vui lòng nhập lời thoại trước khi nhái giọng");
        return;
    }
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        recordingMediaRecorder = new MediaRecorder(stream);
        recordedAudioChunks = [];

        recordingMediaRecorder.ondataavailable = (e) => {
            if (e.data.size > 0) {
                recordedAudioChunks.push(e.data);
            }
        };

        recordingMediaRecorder.onstop = async () => {
            const audioBlob = new Blob(recordedAudioChunks, { type: 'audio/wav' });
            const file = new File([audioBlob], "recorded_audio.wav", { type: "audio/wav" });
            audioFileCloneVoice.value = file;
            if (recordingTime.value <= 60) {
                toast.error("Âm thanh không được ít hơn 60 giây");
                audioFileCloneVoice.value = null;
                recordedAudioChunks = [];
                return;
            }
            handleVoiceClone(index);
            recordingTime.value = 0;
        };

        recordingMediaRecorder.start();
        isRecordVoice.value = true;
        recordingTime.value = 0;

        // Start timer
        recordingInterval.value = setInterval(() => {
            recordingTime.value++;
            // Automatically stop after 5 minutes
            if (recordingTime.value >= 5 * 60) {
                stopRecorCloneVoice();
            }
        }, 1000);

    } catch (err) {
        console.error("Error accessing microphone:", err);
        toast.error("Không thể truy cập microphone");
    }
};
const handleAudioUpload = (event,index) => {
    const videoDescription = slideContents.value[index].descriptions[0];
    if (!videoDescription) {
        toast.error("Vui lòng nhập lời thoại trước khi nhái giọng");
        return;
    }
    const file = event.target.files[0];

    if (file) {
        // Clear previous audio data
        if (uploadedAudioPreview.value) {
            URL.revokeObjectURL(uploadedAudioPreview.value);
            uploadedAudioPreview.value = null;
            audioFileCloneVoice.value = null;
            audioPreviewKey.value++;
        }

        // Rest of your existing validation and handling code
        const allowedAudioTypes = ["audio/mpeg", "audio/wav", "audio/x-m4a"];
        if (!allowedAudioTypes.includes(file.type)) {
            showError.value = true
            popupMessageError.value = "Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav. hoặc .m4a"
            audioInput.value.value = "";
            return;
        }

        const audio = new Audio();
        const objectUrl = URL.createObjectURL(file);
        audio.src = objectUrl;
        // kiểm tra thời lượng file âm thanh
        audio.addEventListener('loadedmetadata', () => {
            const duration = audio.duration; // Thời lượng file (giây)
            if (duration < 60) {
                toast.warning('Thời lượng file âm thanh phải lớn hơn hoặc bằng 60 giây!');
            }
            else if (duration > 60 * 5) {
                toast.warning('Thời lượng file âm thanh phải nhỏ hơn hoặc bằng 5p!');
            } else {
                toast.success('File âm thanh hợp lệ!');
                // Revoke old URL if exists
                if (uploadedAudioPreview.value) {
                    URL.revokeObjectURL(uploadedAudioPreview.value);
                }
                // Update with new file
                audioFileCloneVoice.value = file;
                uploadedAudioPreview.value = URL.createObjectURL(file);
                // Force audio preview to reload
                audioPreviewKey.value += 1;
                // Ensure audio is loaded with new source
                nextTick(() => {
                    if (audioPreview.value) {
                        audioPreview.value.load();
                    }
                    handleVoiceClone(index);
                });
            }
            // Dọn dẹp URL đối tượng
            URL.revokeObjectURL(objectUrl);
        });
    }
};
// Stop recording function
const stopRecorCloneVoice = () => {
    if (recordingMediaRecorder && isRecordVoice.value) {
        recordingMediaRecorder.stop();
        recordingMediaRecorder.stream.getTracks().forEach(track => track.stop());
        clearInterval(recordingInterval.value);
        isRecordVoice.value = false;
    }
};
const toggleDropdown = (index) => {
    slideContents.value[index].isVoice = false;
    isDropdownOpen.value = !isDropdownOpen.value;
};
const getVoiceName = (type) => {
    const voice = listVoice.value.find(v => v.type === type);
    return voice ? voice.name : 'Bạn chưa tạo giọng nói';
};
const selectVoice = (voice,index) => {
    slideContents.value[index].voice_type = voice;
    isDropdownOpen.value = false;
};
let currentAudio = null;
const playSampleVoice = (demoUrl) => {
    console.log(demoUrl)
    if (currentAudio) {
        currentAudio.pause();
        currentAudio.currentTime = 0;
    }

    currentAudio = new Audio(demoUrl);
    currentAudio.play();
};
// Clean up on component unmount
onBeforeUnmount(() => {
    if (recordingInterval.value) {
        clearInterval(recordingInterval.value);
    }
    if (recordingMediaRecorder) {
        recordingMediaRecorder.stream.getTracks().forEach(track => track.stop());
    }
});
</script>

<style scoped>
.spinner {
    border: 4px solid rgba(0, 0, 0, 0.1);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border-left-color: #09f;
    animation: spin 1s ease infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.json-viewer {
    width: 800px;
    background-color: #f4f4f4;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    font-family: monospace;
    font-size: 16px;
    line-height: 1.4;
    overflow-x: auto;
}

.json-viewer pre {
    white-space: pre-wrap;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.object-mic {
    display: flex;
    flex: 0 1 100%;
    justify-content: center;
    align-items: center;
    align-content: stretch;
}

.outline-mic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 8px solid #b5a4a4;
    animation: pulseMic 3s;
    animation-timing-function: ease-out;
    animation-iteration-count: infinite;
    position: absolute;
}

.button-mic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    /* background: #50CDDD; */
    box-shadow: 0px 0px 80px #0084f9;
    position: absolute;
}

@keyframes pulseMic {
    0% {
        transform: scale(0.5);
        opacity: 1;
    }

    50% {
        transform: scale(1.5);
        opacity: 0.4;
    }

    100% {
        transform: scale(2);
        opacity: 0;
    }
}

.loaderVideo {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #207A91;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    margin-left: 30%;
    animation: spin 2s linear infinite;
}

.loadingImage {
    margin-left: 0%;
}

.showLoaderVideo {
    display: flex;
    justify-content: center;
    flex-direction: column-reverse;
    justify-content: center;
    margin-left: 50;
}

.w-200px {
    width: 200px;
}

.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #207A91;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
}
@media only screen and (max-width: 600px) {
  .fz-18 {
    font-size:18px;
  }
}
</style>
