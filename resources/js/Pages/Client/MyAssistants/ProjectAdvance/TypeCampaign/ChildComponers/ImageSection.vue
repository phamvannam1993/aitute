<template>
   <div class="flex flex-col lg:flex-row gap-4 mt-4">
        <div class="w-full">
            <!-- Upload Buttons -->
            <!-- <div class="flex justify-center gap-2 md:gap-4 mb-4 h-[40px]">
                 <button @click="autoCreateImage"
                    :disabled="showImageGenerate == true"
                    class="w-[35%] sm:w-1/3 bg-orion-button-sec text-white py-2.5 text-xs font-bold lg:text-sm rounded-2xl lg:rounded-2xl text-center hover:bg-cyan-600 flex items-center gap-2 justify-center">
                    Tự động tạo ảnh {{  showImageGenerate == true ? '...' : '' }}
                </button>
                <label id="image_0" class="w-[30%] sm:w-1/3 cursor-pointer bg-orion-button-sec text-white py-2 text-xs font-bold lg:text-sm rounded-2xl lg:rounded-2xl text-center hover:bg-cyan-600 flex items-center gap-2 justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-upload-icon hidden md:show">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" x2="12" y1="3" y2="15"></line>
                    </svg>
                    <label id="image_upload" class="cursor-pointer bg-orion-button-sec text-white py-2 text-xs font-bold lg:text-sm rounded-2xl lg:rounded-2xl text-center hover:bg-cyan-600 flex items-center gap-2 justify-center">
                        <Upload size="16" color="#ffffff" class="hidden md:show" />
                        <img src="/assets/svgs/aiwow/uploadMedia.svg" alt="" />
                        Tải ảnh
                        <input id="image_upload" type="file" accept="image/*" @change="handleImageUpload($event, 0)" class="hidden" />
                    </label>
                </label>
                <div class="relative w-[30%] sm:w-1/3 text-center">
                    <button @click="isGenerateImage = !isGenerateImage"  class="w-full h-full bg-orion-button-sec text-white font-bold py-2.5 text-xs lg:text-sm rounded-2xl lg:rounded-2xl text-center hover:bg-cyan-600 flex items-center gap-2 justify-center">
                        <img src="/assets/img/my_assistant/generate_image.png" class="hidden md:show size-5" alt=""> Tạo ảnh mới
                    </button>
                    <div v-if="isGenerateImage" class="absolute z-10 right-0 w-52 bg-white shadow-xl rounded-2xl p-2 py-4 space-y-2">
                        <div @click="toggleGenerateImage('textToImage', index)" class="flex items-center cursor-pointer text-xs md:text-sm px-4 py-2 hover:bg-[#DEECFF] rounded-md">
                            <p>Tạo ảnh từ văn bản</p>
                        </div>
                        <div @click="toggleGenerateImage('background', index)" class="flex items-center cursor-pointer text-xs md:text-sm px-4 py-2 hover:bg-[#DEECFF] rounded-md">
                            <p>Hình phối cảnh</p>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="mb-6 mt-2">
                <p class="text-business-primary font-bold">Hình ảnh: </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 items-center justify-center w-full gap-2">
                 <div class="flex flex-col justify-center items-center h-full w-full rounded-xl overflow-hidden min-h-[120px]" v-for="(image, index_image) in images" :key="index_image">
                    <div v-if="!image.isLoading && (image?.imageRef?.s3_url || image?.previewImageUpload)" class="relative m-auto box-image rounded-lg lg:rounded-2xl bg-gray-200 justify-center items-center flex">
                        <input type="checkbox" :checked="checkImage(index_image)" @change="selectImage(index_image)" class="ml-0 top-2 left-2 absolute rounded-full checked:bg-orion-orange hover:checked:bg-orion-orange checked:text-orion-orange checked:ring-orion-orange z-9999" />
                        <img  v-if="image.imageRef" :src="image.imageRef.s3_url" class="center-image max-h-[250px]" :class="image.className" />
                        <div class="absolute top-2 right-2  text-white rounded-full flex justify-center items-center md:gap-2">
                            <svg class="cursor-pointer z-50" width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg" @click="toggleShowOption(index_image)">
                                <rect width="29" height="29" rx="7" fill="black" fill-opacity="0.3"/>
                                <path d="M14.5013 15.7082C15.1686 15.7082 15.7096 15.1672 15.7096 14.4998C15.7096 13.8325 15.1686 13.2915 14.5013 13.2915C13.834 13.2915 13.293 13.8325 13.293 14.4998C13.293 15.1672 13.834 15.7082 14.5013 15.7082Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14.5013 7.25016C15.1686 7.25016 15.7096 6.70917 15.7096 6.04183C15.7096 5.37449 15.1686 4.8335 14.5013 4.8335C13.834 4.8335 13.293 5.37449 13.293 6.04183C13.293 6.70917 13.834 7.25016 14.5013 7.25016Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14.5013 24.1667C15.1686 24.1667 15.7096 23.6257 15.7096 22.9583C15.7096 22.291 15.1686 21.75 14.5013 21.75C13.834 21.75 13.293 22.291 13.293 22.9583C13.293 23.6257 13.834 24.1667 14.5013 24.1667Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div class="absolute right-2 top-8 w-[172px] md:w-[200px] p-2 rounded-md lg:rounded-2xl bg-black bg-opacity-30 font-bold z-50" v-if="image.isShowOptionImage">
                                <ul class="flex flex-col gap-[2px] m-0">
                                    <li class="flex flex-row hover:bg-black hover:bg-opacity-40 items-center px-1 rounded-md cursor-pointer" @click="generateCustomeImage(index_image)">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12.6667 2H3.33333C2.59695 2 2 2.59695 2 3.33333V12.6667C2 13.403 2.59695 14 3.33333 14H12.6667C13.403 14 14 13.403 14 12.6667V3.33333C14 2.59695 13.403 2 12.6667 2Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M5.66797 6.6665C6.22025 6.6665 6.66797 6.21879 6.66797 5.6665C6.66797 5.11422 6.22025 4.6665 5.66797 4.6665C5.11568 4.6665 4.66797 5.11422 4.66797 5.6665C4.66797 6.21879 5.11568 6.6665 5.66797 6.6665Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M13.9987 9.99984L10.6654 6.6665L3.33203 13.9998" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="ml-2">Tạo lại ảnh</span>
                                    </li>
                                    <li class="flex flex-row hover:bg-black hover:bg-opacity-40 items-center px-1 rounded-md cursor-pointer" @click="upscaleImage(index_image, image.imageRef.s3_url)">
                                        <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M16.8275 7.8697H16.7404M16.7404 7.8697C15.0003 7.82432 13.3466 7.1016 12.1314 5.8554C10.9161 4.60921 10.2351 2.93793 10.2335 1.19727C10.2318 2.93793 9.55084 4.60921 8.33556 5.8554C7.12028 7.1016 5.46663 7.82432 3.72656 7.8697C5.46657 7.91066 7.12149 8.63075 8.33746 9.87603C9.55344 11.1213 10.2339 12.7929 10.2335 14.5334C10.233 12.7929 10.9135 11.1213 12.1295 9.87603C13.3454 8.63075 15.0004 7.91066 16.7404 7.8697Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7.30656 14.1428C6.46313 14.1224 5.66115 13.7728 5.07194 13.169C4.48274 12.5651 4.15304 11.7548 4.15329 10.9111C4.15354 11.7548 3.82384 12.5651 3.23463 13.169C2.64543 13.7728 1.84343 14.1224 1 14.1428C1.84343 14.1633 2.64543 14.5128 3.23463 15.1166C3.82384 15.7205 4.15354 16.5308 4.15329 17.3745C4.15304 16.5308 4.48274 15.7205 5.07194 15.1166C5.66115 14.5128 6.46313 14.1633 7.30656 14.1428Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="ml-2">Làm nét ảnh</span>
                                    </li>
                                    <li class="flex flex-row hover:bg-black hover:bg-opacity-40 items-center px-1 rounded-md cursor-pointer" @click="showPopupCreateVideo(index_image, image.imageRef.s3_url)">
                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.75187 16.0443C12.7796 16.0443 16.0448 12.7791 16.0448 8.75138C16.0448 4.72363 12.7796 1.4585 8.75187 1.4585C4.72412 1.4585 1.45898 4.72363 1.45898 8.75138C1.45898 12.7791 4.72412 16.0443 8.75187 16.0443Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7.29297 5.83447L11.6687 8.75163L7.29297 11.6688V5.83447Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="ml-2">Ảnh thành video</span>
                                    </li>
                                    <li class="flex flex-row hover:bg-black hover:bg-opacity-40 items-center px-1 rounded-md cursor-pointer" @click="openSearchModal(index, index_image)">
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.59131 12.3145C9.6793 12.3145 12.1826 9.81114 12.1826 6.72314C12.1826 3.63515 9.6793 1.13184 6.59131 1.13184C3.50331 1.13184 1 3.63515 1 6.72314C1 9.81114 3.50331 12.3145 6.59131 12.3145Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M13.1992 13.3311L11.166 11.2979" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="ml-2">Tìm kiếm ảnh</span>
                                    </li>
                                        <li class="flex flex-row hover:bg-black hover:bg-opacity-40 items-center px-1 rounded-md cursor-pointer" @click="trigUploadImage(index_image)">
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.8993 9.39648V12.1519C13.8993 12.5173 13.7542 12.8677 13.4958 13.1261C13.2374 13.3844 12.887 13.5296 12.5216 13.5296H2.8777C2.51231 13.5296 2.16189 13.3844 1.90352 13.1261C1.64515 12.8677 1.5 12.5173 1.5 12.1519V9.39648" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M10.7993 4.23167L7.69944 1.13184L4.59961 4.23167" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7.69922 1.13184V9.39805" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="ml-2">Tải lên</span>
                                    </li>
                                    <li class="flex flex-row hover:bg-black hover:bg-opacity-40 items-center px-1 rounded-md cursor-pointer" @click="downloadImage(image.imageRef.s3_url)">
                                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.8993 9.39648V12.1519C13.8993 12.5173 13.7542 12.8677 13.4958 13.1261C13.2374 13.3844 12.887 13.5296 12.5216 13.5296H2.8777C2.51231 13.5296 2.16189 13.3844 1.90352 13.1261C1.64515 12.8677 1.5 12.5173 1.5 12.1519V9.39648" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M10.7993 6.29812L7.69944 9.39795L4.59961 6.29812" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7.69922 9.39795V1.13173" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="ml-2">Tải về</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <button type="button" @click="removeImage(index_image)" class="absolute bottom-2 left-2 bg-red-500 text-white w-4 h-4 rounded-full flex justify-center items-center z-50">
                            <img src="/assets/svgs/remove-icon.svg"/>
                        </button>
                        <button type="button" @click="showFullImage(image.imageRef.s3_url)" class="absolute bottom-2 right-2 text-white w-8 h-8 rounded-full flex justify-center items-center cursor-pointer z-50">
                            <img src="/assets/svgs/show-full.svg"/>
                        </button>
                    </div>
                    <div v-if="!image.isLoading && !(image?.imageRef || image?.previewImageUpload)" class="bg-[#E6E6E6] flex h-full items-center justify-center w-full rounded-xl relative min-h-[250px]">
                        <img src="/assets/svgs/aiwow/image.svg" alt="loading" class="mx-auto w-16" />
                        <div class="absolute top-2 right-2  text-white rounded-full flex justify-center items-center md:gap-2">
                            <svg class="cursor-pointer" width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg" @click="toggleShowOption(index_image)">
                                <rect width="29" height="29" rx="7" fill="black" fill-opacity="0.3"/>
                                <path d="M14.5013 15.7082C15.1686 15.7082 15.7096 15.1672 15.7096 14.4998C15.7096 13.8325 15.1686 13.2915 14.5013 13.2915C13.834 13.2915 13.293 13.8325 13.293 14.4998C13.293 15.1672 13.834 15.7082 14.5013 15.7082Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14.5013 7.25016C15.1686 7.25016 15.7096 6.70917 15.7096 6.04183C15.7096 5.37449 15.1686 4.8335 14.5013 4.8335C13.834 4.8335 13.293 5.37449 13.293 6.04183C13.293 6.70917 13.834 7.25016 14.5013 7.25016Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14.5013 24.1667C15.1686 24.1667 15.7096 23.6257 15.7096 22.9583C15.7096 22.291 15.1686 21.75 14.5013 21.75C13.834 21.75 13.293 22.291 13.293 22.9583C13.293 23.6257 13.834 24.1667 14.5013 24.1667Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div class="absolute right-2 top-8 w-[172px] md:w-[200px] p-2 rounded-md lg:rounded-2xl bg-black bg-opacity-30 font-bold z-50" v-if="image.isShowOptionImage">
                                <ul class="flex flex-col gap-[2px] m-0">
                                    <li class="flex flex-row hover:bg-black hover:bg-opacity-40 items-center px-1 rounded-md cursor-pointer" @click="generateCustomeImage(index_image)">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12.6667 2H3.33333C2.59695 2 2 2.59695 2 3.33333V12.6667C2 13.403 2.59695 14 3.33333 14H12.6667C13.403 14 14 13.403 14 12.6667V3.33333C14 2.59695 13.403 2 12.6667 2Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M5.66797 6.6665C6.22025 6.6665 6.66797 6.21879 6.66797 5.6665C6.66797 5.11422 6.22025 4.6665 5.66797 4.6665C5.11568 4.6665 4.66797 5.11422 4.66797 5.6665C4.66797 6.21879 5.11568 6.6665 5.66797 6.6665Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M13.9987 9.99984L10.6654 6.6665L3.33203 13.9998" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="ml-2">Tạo lại ảnh</span>
                                    </li>
                                    <li class="flex flex-row hover:bg-black hover:bg-opacity-40 items-center px-1 rounded-md cursor-pointer" @click="openSearchModal(index, index_image)">
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.59131 12.3145C9.6793 12.3145 12.1826 9.81114 12.1826 6.72314C12.1826 3.63515 9.6793 1.13184 6.59131 1.13184C3.50331 1.13184 1 3.63515 1 6.72314C1 9.81114 3.50331 12.3145 6.59131 12.3145Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M13.1992 13.3311L11.166 11.2979" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="ml-2">Tìm kiếm ảnh</span>
                                    </li>
                                        <li class="flex flex-row hover:bg-black hover:bg-opacity-40 items-center px-1 rounded-md cursor-pointer" @click="trigUploadImage(index_image)">
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.8993 9.39648V12.1519C13.8993 12.5173 13.7542 12.8677 13.4958 13.1261C13.2374 13.3844 12.887 13.5296 12.5216 13.5296H2.8777C2.51231 13.5296 2.16189 13.3844 1.90352 13.1261C1.64515 12.8677 1.5 12.5173 1.5 12.1519V9.39648" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M10.7993 4.23167L7.69944 1.13184L4.59961 4.23167" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7.69922 1.13184V9.39805" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="ml-2">Tải lên</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div v-if="image.isLoading" class="bg-[#E6E6E6] flex h-full items-center justify-center w-full rounded-xl">
                        <div class="flex flex-col justify-center items-center w-full min-h-[250px]">
                            <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                            <h3 class="text-white text-center">Hệ thống đang xử lý, xin đợi một chút</h3>
                        </div>
                    </div>
                    <h2 class="italic mt-1">{{ label_images[image.type_image ? image.type_image : 4] }}</h2>
                </div>
            </div>
        </div>
        <!--v-if-->
    </div>
        <div v-if="showSearchModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center" style="z-index: 9999999;">
        <div class="bg-white w-[80vw] md:w-[50vw] p-4 rounded-xl">
            <div class="flex items-center justify-between">
                <div class="flex justify-start items-center gap-4">
                    <img src="/assets/img/ai3goc/logo/logo_img.svg" alt="step" class="w-[24px] h-auto" />
                    <span class="text-[15px] lg:text-[18px] leading-none font-bold text-black">Tìm kiếm hình ảnh</span>
                </div>
                <button type="button" @click="showSearchModal = false" class="p-2 rounded-full bg-gray-300">
                    <img src="/assets/svgs/remove-icon.svg"/>
                </button>
            </div>
            <div class="border-t border-gray-300 my-2"></div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 max-h-[60vh] overflow-y-auto">
                <div
                    v-for="(img, i) in searchImages"
                    :key="i"
                    @click="selectedSearchImage = img.link"
                    :class="[
                        'cursor-pointer border-2 rounded-lg overflow-hidden',
                        selectedSearchImage === img.link ? 'border-blue-500' : 'border-transparent'
                    ]"
                >
                    <img :src="img.link" class="w-full aspect-[4/3] object-cover" />
                </div>
                <div class="flex justify-center items-center">
                    <button type="button" @click="loadMoreImages" :disabled="isLoadingMore" :class="isLoadingMore ? 'bg-opacity-60' : 'bg-opacity-100'" class="min-w-[100px] md:min-w-[150px] h-fit px-2 py-2 lg rounded-lg lg:rounded-2xl border border-[#C5C5C5] text-white font-bold place-items-center gap-2 text-sm bg-[#37009A]">{{ isLoadingMore ? 'Đang tải thêm ảnh...' : 'Xem thêm...' }} </button>
                </div>
            </div>
            <div class="flex justify-center items-center my-4">
                <button @click="confirmSelectedImage" class="min-w-[100px] md:min-w-[150px] px-2 py-2 lg bg-[#149CBE] rounded-lg lg:rounded-2xl border border-[#C5C5C5] text-white font-bold place-items-center gap-2 text-sm">Xác nhận</button>
            </div>
        </div>
    </div>
     <ImageToVideo ref="imageTovideoRef" @updateVideos="updateVideos" :dataVideo="facebookContentDetail" @updateFacebookContent="handleUpdateFacebookContent" @toggleGenerateImage="toggleGenerateImage" :facebookContentDetail="facebookContentDetail" />
     <input type="file" ref="uploadImageItem" accept="image/*" @change="handleImageUploadImageItem" class="hidden" />
     <PopupCreateImage @close="isShowCreateImage=false" :is_show="isShowCreateImage" @showImageList="showModalImageList" ref="createImageRef" :productImage="createProductImage" @updateImage="handleUpdateImage" />
     <PopupCreateVideo @close="isShowCreateVideo=false" :is_show="isShowCreateVideo" ref="createVideoRef" @createShortVideo="createShortVideo" />
     <PopupImageList @close="isShowImage=false" :is_show="isShowImage" @showCreateImage="showModelCreateImage" />
     <div>
        <div v-if="isShowFullImage" class="overlay" style="z-index: 9999999;" @click="isShowFullImage = false">
            <button class="close-btn" @click="isShowFullImage = false">✖</button>
            <img :src="thumbnail" class="full-image" />
        </div>
    </div>
    <div v-if="showOptions" class="overlay-option" @click="hideOption()"></div>
</template>

<script setup>
    import { ref, watch, onMounted, inject, reactive, onUnmounted} from 'vue';
    import { toast } from "vue3-toastify";
    import { eventBus } from "@/lib/eventBus.js";

    import ImageToVideo from "./Components/ImageToVideo.vue";
    import PopupCreateImage from "./Components/PopupCreateImage.vue";
    import PopupCreateVideo from "./Components/PopupCreateVideo.vue";
    import PopupImageList from "./Components/PopupImageList.vue";
import axios from "axios";
    const uploadImageItem = ref(null)
    const showOptions = ref(false)
    const emit = defineEmits(['submit', 'toggle']);
    const props = defineProps({
        facebookContentDetail: {
            type: Object,
            default: {
                content:""
            }
        },
        selectedProject: {
            type: Object,
            default: {
                content:""
            }
        },
        projectId: {
            type: Number,
            default: 0
        },
    });

    const facebookContentDetail = ref(props.facebookContentDetail)
    const selectedProject = ref(props.selectedProject)
    watch(() => props.selectedProject, newValue => {
        selectedProject.value = newValue
    }, {deep: true})
    function handleUpdateResult(data) {
        facebookContentDetail.value = data;
    }

    onMounted(() => {
        eventBus.on('updateFaceBookContent', handleUpdateResult);
    });
    const images = ref(props.facebookContentDetail.images)

    const toggleGenerateImage = (type) => {
        isGenerateImage.value = false
        emit("toggleGenerateImage", type)
    }

    const selectImage = (index) => {
        images.value[index].is_post = !images.value[index].is_post
        updateData(true)
    }
    const isGenerateImage = ref(false)
    const checkImage = (index) => {
        updateClassImage()
        return images.value[index].is_post
    };
    const keyword = ref("");
    const searchImages = ref([]);
    const currentPage = ref(1);
    const hasMore = ref(false);
    const colorType = ref("imgColorTypeUndefined");
    const content = ref("Con mèo")
    const canLoadImage = (url) => {
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = () => resolve(true);
            img.onerror = () => resolve(false);
            img.src = url;
        });
    };

    const showImageGenerate = ref(false);
    const autoCreateImage = async(isAuto = false) => {
        var modelProject = selectedProject.value?.model_product || selectedProject.value?.image_model
        var imageProject = selectedProject.value?.image_product
        if(!modelProject && !imageProject) {
            for(var idx = 0; idx < 4; idx ++) {
                images.value[idx].isLoading = true
                images.value[idx].imageRef = {};
            }
            await searchImageByKeyword(true);
            return
        }
        if (showImageGenerate.value) {
            return;
        }
        const promises = [];
        showImageGenerate.value = !showImageGenerate.value;
        if (showImageGenerate.value) {
            await updateImageType()
            for(var idx = 0; idx < 4; idx ++) {
                images.value[idx].isLoading = true
                images.value[idx].imageRef = {};
            }
            for (let index = 0; index < 4; index++) {
                promises.push(createImageBy(index, isAuto));
            }
            Promise.all(promises).then(() => {
                showImageGenerate.value = false;
            });
        }
    };
    const prompts = ref([
        "Hãy tạo bức ảnh minh họa truyền tải cảm xúc sâu sắc từ bài viết, Ảnh chỉ dùng để hiển thị sản phẩm, không thêm chữ vào ảnh, giúp người xem không chỉ nhìn thấy sản phẩm/dịch vụ mà còn cảm nhận được giá trị tinh thần, lợi ích cảm xúc khi sở hữu nó.  - Chủ đề chính của ảnh: Không tập trung vào sản phẩm, mà khai thác trải nghiệm, cảm giác, giá trị tinh thần mà sản phẩm mang lại.  Hãy đi từ Đối tượng tiềm năng của sản phẩm - sau đó mô tả cảm giác của \"đối tượng\" đó khi sở hữu hoặc sử dụng sản phẩm. Ví dụ:  + Nếu là bất động sản → Cảm giác bình yên, an cư, gắn kết gia đình, sự thành công  + Nếu là sản phẩm công nghệ → Cảm giác hiện đại, tiện lợi, an tâm, kiểm soát tốt hơn cuộc sống + Nếu là sản phẩm sức khỏe → Cảm giác hạnh phúc, tràn đầy năng lượng, an tâm cho gia đình  - Bối cảnh phù hợp với cảm xúc: (VD: ngôi nhà ấm áp với gia đình hạnh phúc, doanh nhân thành đạt tự tin, một người thư giãn tận hưởng thành quả...) - Đảm bảo rằng Không thêm bất kỳ ký tự nào vào ảnh theo bất cứ trường hợp nào 🔥 Hãy đảm bảo hình ảnh không chỉ minh họa sản phẩm/dịch vụ, mà còn đánh trúng vào cảm xúc, giá trị tinh thần mà khách hàng sẽ có được khi sở hữu nó.  Hình ảnh phải trông thực tế nhất có thể",
        "Hãy tạo bức ảnh minh họa trực quan, chủ đề dựa trên Nội dung chính của bài viết, Ảnh chỉ dùng để hiển thị sản phẩm, không thêm chữ vào ảnh. - Đối tượng chính của ảnh: Khai thác từ phần [Nội dung bài viết] - Các yếu tố quan trọng cần minh họa: Hãy tìm kiếm các yếu tố liên quan tới Sản Phẩm / Dịch vụ mà bài viết quảng cáo. Hoặc nếu là bài viết không tập trung vào Sản phẩm/Dịch vụ thì tìm kiếm các yếu tố xung quanh bài viết. - Cảm xúc cần thể hiện: Cần truyền tải được [Cảm xúc mong muốn truyền tải] - Phong cách hình ảnh: Cần truyền tải được [Phong cách viết] - Đảm bảo rằng Không thêm bất kỳ ký tự nào vào ảnh theo bất cứ trường hợp nào - Tông màu và hiệu ứng phù hợp với nội dung: (VD: màu sắc thiên nhiên nếu sản phẩm là hữu cơ, màu sắc công nghệ nếu sản phẩm số...) Hãy đảm bảo mỗi ảnh thể hiện một khía cạnh khác nhau của bài viết, tạo nên sự đồng nhất nhưng không lặp lại. Hình ảnh phải trông thực tế nhất có thể",
        "Hãy tạo bức ảnh minh họa giúp truyền tải mục tiêu chính của bài viết, Ảnh chỉ dùng để hiển thị sản phẩm, không thêm chữ vào ảnh. Sử dụng [Nội dung bài viết] để nắm được đối tượng của bức ảnh. Nhưng Chủ đề Chính sẽ khai thác từ phần [Mục tiêu bài viết] (VD: tăng nhận diện thương hiệu, kích thích mua hàng, xây dựng niềm tin, tạo sự tương tác...) Dựa trên Chủ đề Chính sẽ tạo ra 1 bức ảnh tập trung vào yếu tố mục tiêu bài viết. Và Cảm xúc cần thể hiện: Cần truyền tải được [Cảm xúc mong muốn truyền tải] mà người dùng đã điền. - Đảm bảo rằng Không thêm bất kỳ ký tự nào vào ảnh theo bất cứ trường hợp nào Hãy đảm bảo hình ảnh thể hiện kết quả rõ ràng, giúp người xem dễ dàng cảm nhận được mục tiêu của bài viết. Hình ảnh phải trông thực tế nhất có thể",
        "Hãy tạo bức ảnh với  biểu tượng mạnh mẽ, không minh họa trực tiếp sản phẩm hay nội dung bài viết, Ảnh chỉ dùng để hiển thị sản phẩm, không thêm chữ vào ảnh, mà tập trung vào 1 key cụ thể về giá trị cốt lõi và thông điệp sâu xa của bài viết. - Key chính của bài viết: (Không phải sản phẩm, mà là ý nghĩa sâu xa mà sản phẩm mang lại. VD: \"Thành công\", \"Cân bằng\", \"Hồi sinh\", \"Sự bảo vệ\", \"Phát triển bền vững\"...)  - Hình ảnh biểu tượng để minh họa Key chính: (VD: Kim tự tháp thể hiện sự bền vững, Ngọn lửa tượng trưng cho đam mê, Dòng sông chảy mãi đại diện cho sự phát triển...) - Đảm bảo rằng Không thêm bất kỳ ký tự nào vào ảnh theo bất cứ trường hợp nào Hãy đảm bảo hình ảnh thể hiện kết quả rõ ràng, giúp người xem dễ dàng hiểu được tác động của sản phẩm/dịch vụ. Chú ý hình ảnh thực sự đơn giản, có tính biểu tượng cao, dễ nhớ, gây tò mò và thể hiện rõ thông điệp sâu xa của bài viết. Hình ảnh phải trông thực tế nhất có thể",
    ]);
    const autoCreaeImageLoading = ref(false)
    const createImage = async () => {
        const promises = [];
        const prompt = facebookContentDetail.value.content;
        if(!prompt) {
            autoCreaeImageLoading.value = false;
            showImageGenerate.value = false
            toast.error("Nội dung không được để trống");
            return
        }
        const response = await axios.post(route("ai-business.summarize-content"), {
            content: prompt,
            project_name: "tÔI LÀ AI",
        });
        for (let index = 0; index < 4; index++) {
            images.value[index].isLoading = true
            let descriptionToSend = prompt
            promises.push(callGenerateImage(descriptionToSend, 0));
        }
        Promise.all(promises).then(() => {
            autoCreaeImageLoading.value = false;
        });
    };
    const callGenerateImage = async (finalDescription, index, models) => {
        let attempts = 0;
        const maxRetries = 3;
         hideOption()
        while (attempts < maxRetries) {
            try {
                autoCreaeImageLoading.value = true;
                const response = await axios.post(route("ai-image.generate"), {
                    description: finalDescription,
                    style: "Thực tế",
                    artist: "Leonardo da Vinci",
                    height: 1024,
                    width: 1024,
                    model: "Flux Schnell",
                    aspect_ratio: "1:1",
                });
                saveGenerationResult(response?.data, "image", index);
                return;
            } catch (error) {
                attempts++;
                if (error.response && error.response.status === 403) {
                    // showCreditModal();
                    throw error;
                }
                if (attempts >= maxRetries) {
                    console.error(`Gọi API thất bại sau ${attempts} lần thử`, error);
                    throw error;
                }
                console.warn(`Thử lại lần ${attempts}...`);
                await new Promise((resolve) => setTimeout(resolve, 1000));
            }
        }
    };
    const saveGenerationResult = async (dataParam) => {
        const url = dataParam.url || dataParam.generate_file?.s3_url || dataParam;
        try {
            let image = {
                imageRef: {
                    s3_url: url,
                }
            };
            saveImage(image)
        } catch (error) {
            console.error("Lỗi khi lưu kết quả:", error);
            toast.error("Không thể lưu kết quả. Vui lòng thử lại sau.");
            throw error;
        }
    };
    const handleImageUploadImageItem = (e) => {
        hideOption()
        const file = e.target.files[0];
        if (!file) return;
        handleFileChange(e, imageKey.value);
    };
    const handleFileChange = async (event, index) => {
        if (event.target?.files?.length === 0) {
            return;
        }
        const file = event.target.files[0];
        if (file.type.includes("image")) {
            handleImageStyle(file, index);
            return;
        }
        if (file.type.includes("video")) {
            handleVideoStyle(file, index);
            return;
        }
    };
    let allowedExtension = ["image/jpeg", "image/jpg", "image/png", "video/mp4"];
    const handleImageStyle = async (file, index) => {
        if (allowedExtension.indexOf(file.type) < 0) {
            toast.error("Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg");
            return;
        }
        if (file) {
            images.value[index].isLoading = true;
            let imagePromise = new Promise(function (resolve) {
                const reader = new FileReader();
                reader.onload = () => {
                    let objectURL = URL.createObjectURL(file);
                    resolve(objectURL);
                };
                reader.readAsDataURL(file);
            });
            const s3_url = await getS3URL(file)
            imagePromise.then(function (value) {
                let image = {
                    imageRef: {
                        s3_url: s3_url,
                    },
                    isLoading: false
                };
                saveImage(image, index)
            });
        }
    };
    const getS3URL = async (file) => {
        const formData = new FormData();
        formData.append("image_file", file);
        try {
            const response = await axios.post(route("short-video.uploadImageToS3"), formData, {
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
    const saveImage = (image, index = -1) => {
        if(index > -1) {
            images.value[index] = image;
            images.value[index].isLoading = false
            images.value[index].is_post = false
            updateData()
            return
        }
        var isSave = false
        for(var i = 0; i < images.value.length; i++) {
            if(!images.value[i].previewImageUpload && !images.value[i].imageRef && !isSave) {
                isSave = true
                images.value[i] = image;
                images.value[i].isLoading = false
                images.value[i].is_post = false
            }
        }
        updateClassImage()
        updateData()
    }

    const updateData = (isTick = false) => {
        hideOption()
        eventBus.emit('updateImages', images);
        const formData = new FormData()
        formData.append("id", facebookContentDetail.value.id)
        formData.append("images", JSON.stringify(images.value))
        if(isTick) {
            formData.append("is_video", 1)
        }
        emit('updateFacebookContent', formData);
    }

    const updateVideos = (videos) => {
        const formData = new FormData()
        formData.append("id", facebookContentDetail.value.id)
        formData.append("videos", JSON.stringify(videos))
        emit('updateFacebookContent', formData);
    }

    const removeImage = (index) => {
        images.value[index].imageRef = ""
        images.value[index].is_post = false
        updateData()
    }
    const showSearchModal = ref(false);
    const selectedSearchImage = ref(null);
    const currentSearchIndex = ref(null);
    const currentSearchIndexImage = ref(null);
    const openSearchModal = (index, index_image) => {
        currentSearchIndex.value = index;
        currentSearchIndexImage.value = index_image;
        selectedSearchImage.value = false;
        showSearchModal.value = true;
        searchImages.value = [];
        currentPage.value = 1;
        searchImageByKeyword();
    };
    const isLoadingMore = ref(false);
    const loadMoreImages = async () => {
        if (isLoadingMore.value) return;
        isLoadingMore.value = true;
        currentPage.value += 1;
        await searchImageByKeyword();
        isLoadingMore.value = false;
    };
    const searchImageByKeyword = async (isAuto = false) => {
        try {
            const response = await axios.get(route("ai-business.search-image"), {
                params: {
                    keyword: selectedProject.value.title,
                    color_type: colorType.value,
                    page: currentPage.value,
                    site: selectedProject.value.url || "",
                },
            });
            const loadedImages = [];
            for (const img of response.data.items) {
                const imageUrl = img.link;
                const canLoad = await canLoadImage(imageUrl);

                if (canLoad) {
                    loadedImages.push(img);
                }
            }
            if(isAuto) {
                if(loadedImages.length >= 4) {
                    for(var i = 0; i < 4; i++) {
                        const image = {
                            imageRef: {
                                s3_url: loadedImages[i].link,
                                is_post:true
                            }
                        };
                        images.value[i] = image
                    }
                    updateData()
                }

            }
            if (currentPage.value === 1) {
                searchImages.value = loadedImages;
            } else {
                searchImages.value = [...searchImages.value, ...loadedImages];
            }
        } catch (error) {
            console.error("Lỗi khi tìm kiếm ảnh:", error);
        }
    };
    const confirmSelectedImage = () => {
        const index = currentSearchIndex.value;
        const index_image = currentSearchIndexImage.value;
        if ( !selectedSearchImage.value ) { return; }
        const image = {
            imageRef: {
                s3_url: selectedSearchImage.value,
            },
        };
        saveImage(image, index_image)
        showSearchModal.value = false;
    };
    //type_image: 0.Ảnh tạo từ AI; 1. Ảnh phối cảnh của sản phẩm; 2. Ảnh ghép người mẫu với sản phẩm, 3. Ảnh ghép tự sướng của bạn với sản phẩm, 4. Ảnh tìm kiếm từ internet
    const label_images = ref(['Ảnh tạo từ AI', 'Ảnh phối cảnh của sản phẩm', 'Ảnh ghép người mẫu với sản phẩm', 'Ảnh ghép tự sướng của bạn với sản phẩm', 'Ảnh tìm kiếm từ internet']);
    const generateCustomeImage = async (index, isAuto = false) => {
        await updateImageType(isAuto)
        await createImageBy(index)
    };

    const updateImageType = async (isAuto = false) => {
        var modelProject = selectedProject.value?.model_product || selectedProject.value?.image_model
        var imageProject = selectedProject.value?.image_product
        for(var i = 0; i < images.value.length; i++) {
            if(!imageProject && !modelProject) {
                if(isAuto) {
                    images.value[i].type_image = 4
                } else {
                    images.value[i].type_image = 0
                }
            } else {
                images.value[i].type_image = 2
                if(i == 0) {
                    images.value[i].type_image = 1
                } else if(imageProject && modelProject) {
                    if(i > 1) {
                        images.value[i].type_image = 3
                    }
                }
            }
        }
    }
    const createImageBy = async (index) => {
        const modelProject = selectedProject.value?.model_product || selectedProject.value?.image_model;
        const imageProject = selectedProject.value?.image_product;
        const i = index;
        let s3_url = images.value[i].imageRef ? images.value[i].imageRef.s3_url : '';
        images.value[i].isLoading = true
        if (images.value[i].type_image  == 1) {
            const imageProjectFile = await convertUrlToFile(imageProject)
            if(!imageProjectFile) {
                return null
            }
            s3_url = await generateImageBackgroundWithReplicate(imageProjectFile, imageProject);
        } else if(images.value[i].type_image  == 0) {
            s3_url = await createAIImage();
        } else if(images.value[i].type_image  == 2) {
           s3_url = await combineImagesReplicate(imageProject, modelProject);
        } else if(images.value[i].type_image  == 3) {
            let model_link = await generateSelfImage();
            if(model_link) {
                s3_url = await createImageV2(imageProject, model_link);
            } else {
                images.value[i].type_image = 2;
                s3_url = await combineImagesReplicate(imageProject, modelProject);
            }
        }
        if (s3_url) {
            if (typeof images.value[i].imageRef !== 'object') {
                images.value[i].imageRef = {};
            }
            images.value[i].imageRef.s3_url = s3_url;
        }
        images.value[i].isLoading = false;
        updateData();
    };

    const createAIImage = async (index) => {
        let s3_url = '';
        try {
            const content = facebookContentDetail.value.content
            const response = await axios.post(route("ai-image.generate"), {
                description: `${content} ${prompts.value[0]}`,
                style: "Thực tế",
                artist: "Leonardo da Vinci",
                height: 1024,
                width: 1024,
                model: "Flux Schnell",
                aspect_ratio: "1:1",
            });
            return response.data.url;
        } catch (error) {
            console.error('Lỗi tạo ảnh:', error);
            toast.error('Tạo ảnh không thành công. Vui lòng thử lại.');
            return s3_url
        }
    }

    const generatePromptPerspective = async (imageFile) => {
        const resizedFile = await resizeAndPadImage(imageFile);
        var s3Url = await generateImageBackgroundWithFile(resizedFile);
        return s3Url
    };

    const resizeAndPadImage = async (file) => {
        return new Promise((resolve) => {
            const img = new Image();
            const reader = new FileReader();

            reader.onload = (e) => {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);

            img.onload = () => {
                const canvas = document.createElement("canvas");
                const targetWidth = 1080;
                const targetHeight = 608;
                const targetRatio = targetWidth / targetHeight;

                const originalWidth = img.width;
                const originalHeight = img.height;
                const originalRatio = originalWidth / originalHeight;

                let drawWidth, drawHeight;

                if (originalRatio > targetRatio) {
                    drawWidth = targetWidth;
                    drawHeight = Math.round(targetWidth / originalRatio);
                } else {
                    drawHeight = targetHeight;
                    drawWidth = Math.round(targetHeight * originalRatio);
                }

                canvas.width = targetWidth;
                canvas.height = targetHeight;

                const ctx = canvas.getContext("2d");

                // Fill trắng toàn bộ nền
                ctx.fillStyle = "#FFFFFF";
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                // Vẽ ảnh đã resize vào giữa canvas
                const offsetX = Math.round((targetWidth - drawWidth) / 2);
                const offsetY = Math.round((targetHeight - drawHeight) / 2);
                ctx.drawImage(img, offsetX, offsetY, drawWidth, drawHeight);

                canvas.toBlob((blob) => {
                    const resizedFile = new File([blob], file.name, { type: "image/jpeg" });
                    resolve(resizedFile);
                }, "image/jpeg", 0.95);
            };
        });
    };

    const generateImageBackgroundWithReplicate = async (inputImageFile, imageProject) => {
        let formData = new FormData();

        formData.append("image", imageProject);
        formData.append("ratio", "16:9");
        formData.append("prompt_kh", "");

        try {
            const response = await axios.post(route('ai-business.create-image-background-by-replicate'), formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });

            if (response.data.success == true) {
                return response.data.s3_url;
            } else {
                return await generateImageBackgroundWithFile(inputImageFile);
            }
        } catch (error) {
            toast.error("Lỗi khi tạo ảnh!");
        }
    };

    const generateImageBackgroundWithFile = async (inputImageFile) => {
        let formData = new FormData();

        formData.append("image", inputImageFile);

        try {
            const response = await axios.post(route('ai-business.generate-image-background-with-file'), formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });

            if (response.data.status === 'success') {
                return response.data.s3_url;
            } else {
                console.warn("Tạo ảnh nền không thành công. Chuyển sang generatePromptPerspective.");
                return await generatePromptPerspectiveWithFile(inputImageFile);
            }
        } catch (error) {
            console.error(error);
            let s3url = await generatePromptPerspectiveWithFile(inputImageFile);
            if (s3url) {
                return s3url;
            }
            toast.error("Lỗi khi gọi API tạo ảnh nền!");
        }
    };

    const generatePromptPerspectiveWithFile = async (imageFile) => {
        try {
            let formData = new FormData();
            formData.append("image", imageFile);

            const response = await axios.post(route("ai-business.generate-prompt-image-with-file"), formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });

            if (response.data.status === "success") {
                promptBackground.value = response.data.response;
                // Wait for generateAiBackground to return the URL
                return await generateAiBackground(); // It will return the URL from generateAiBackground
            } else {
                toast.error("Lỗi khi tạo phối cảnh: " + response.data.message);
                toast.error('Tạo ảnh không thành công. Vui lòng thử lại.');
                return null; // Return null if the response is not successful
            }
        } catch (error) {
            toast.error("Lỗi khi gọi API tạo phối cảnh!");
            toast.error('Tạo ảnh không thành công. Vui lòng thử lại.');
            return null; // Return null on error
        }
    };

    const generateAiBackground = async (imageProjectFile, promptBackground) => {
        try {
            let formData = new FormData();
            formData.append("prompt_background", promptBackground);
            formData.append("imageFile", imageProjectFile);
            const response = await axios.post(route("ai-background.generate-ai-background-v2"), formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });
            if (response.data.s3_url) {
                // genBackgroundProductImage.value = response.data.s3_url;
                return response.data.s3_url; // Return the URL if successful
            } else {
                toast.error("Lỗi khi tạo nền AI: " + response.data.message);
                toast.error('Tạo ảnh không thành công. Vui lòng thử lại.');
                return null; // Return null if the URL is not available
            }
        } catch (error) {
            toast.error("Lỗi khi gọi API tạo nền AI!");
            toast.error('Tạo ảnh không thành công. Vui lòng thử lại.');
            return null; // Return null on error
        }
    };
    const DEFAULT_MODEL_IMAGE = "https://static2.yan.vn/YanNews/202308/202308020439097760-40d2f3e9-8f57-407c-bfb2-504fdd0ead22.jpeg";
    const convertUrlToFile = async (s3_image_url) => {
        var url = route("ai-image.showImage", { s3_url:s3_image_url})
        const response = await axios.get(url);
        if (response.data.success) {
            var imageUrl = 'data:image/jpeg;base64,'+response.data.data;
            return base64ToFile(imageUrl, 'image.jpg', "image/jpg")
        } else {
            return ""
        }
    };
    const base64ToFile = async (base64, filename, mimeType) => {
        const arr = base64.split(',');
        const bstr = atob(arr[1]); // decode base64
        let n = bstr.length;
        const u8arr = new Uint8Array(n);
        while (n--) {
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new File([u8arr], filename, { type: mimeType });
    }

    const combineImagesReplicate = async (modelImage, productImage) => {
        let formData = new FormData();

        formData.append("image_model", modelImage);
        formData.append("image_product", productImage);
        formData.append("ratio", "16:9");
        formData.append("prompt_kh", "");

        try {
            const response = await axios.post(route('ai-business.create-image-by-replicate'), formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });

            if (response.data.success == true) {
                return response.data.s3_url;
            } else {
                console.log("backup combine image");
                const proxyProductUrl = `/proxy-image?url=${encodeURIComponent(productImage)}`;
                const response = await axios.get(proxyProductUrl, { responseType: "blob" });
                const productImageBlob = new Blob([response.data], { type: "image/jpeg" });
                const product = await resizeAndPadImage(productImageBlob);

                const proxyModelUrl = `/proxy-image?url=${encodeURIComponent(modelImage)}`;
                const responseModel = await axios.get(proxyModelUrl, { responseType: "blob" });
                const modelImageBlob = new Blob([responseModel.data], { type: "image/jpeg" });
                const model = await resizeAndPadImage(modelImageBlob);

                const resizedFile = await resizeAndPadImage(product);
                const resizedFile2 = await resizeAndPadImage(model);
                return await combineImages(resizedFile, resizedFile2);
            }
        } catch (error) {
            toast.error("Lỗi khi kết hợp ảnh!");
        }
    };

    const combineImages = async (productImage, modelImage) => {
        let formData = new FormData();

        formData.append("image_model", modelImage);
        formData.append("image_product", productImage);
        formData.append("ratio", "16:9");
        formData.append("prompt_kh", "");

        try {
            const response = await axios.post(route('ai-business.create-image'), formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });

            if (response.data.success) {
                return response.data.s3_url;
            } else {
                toast.error(response.data.message);
            }
        } catch (error) {
            toast.error("Lỗi khi kết hợp ảnh!");
        }
    };

    const createImageV2 = async (imageProduct, modelProduct) => {
        let formData = new FormData();
        const imageProjectFile = await convertUrlToFile(imageProduct)
        if(!imageProjectFile) {
            return null
        }
        const modelProductFile = await convertUrlToFile(modelProduct)
        if(!modelProductFile) {
            return null
        }
        const resizedFile = await resizeAndPadImage(modelProductFile);
        const resizedFile2 = await resizeAndPadImage(imageProjectFile);
        formData.append("image_model", resizedFile);
        formData.append("ratio", "16:9");
        formData.append("prompt_kh", "");
        formData.append("image_product", resizedFile2);
        try {
            const response = await axios.post(route('ai-business.combine-images'), formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });
            if (response.data.success) {
                return response.data.data.aiGeneratedMedia.s3_url;
            } else {
                toast.error(response.data.message);
            }
        } catch (error) {
            toast.error("Lỗi khi kết hợp ảnh!");
        }
    };

    // Ảnh tự sướng
    const generateSelfImage = async () => {
        try {
            const latestModelResponse = await axios.get(route('ai-image.get-latest-my-ai-image'));
            const latestModel = latestModelResponse.data?.data;
            if (!latestModel || !latestModel.id) {
                return "";
            }
            const modelId = latestModel.id;
            let formData = new FormData();
            formData.append("aspect_ratio", "9:16");
            formData.append("model_id", modelId);
            formData.append("image_description", "Hình ảnh một người mẫu ăn mặc thời trang, đẹp mắt và phù hợp với giới tính, đang đứng tạo dáng chuyên nghiệp với một tay đặt phía trước ở vị trí như thể đang giới thiệu hoặc tương tác với một vật thể. Bối cảnh xung quanh được thiết kế hài hòa với tư thế, ánh sáng đẹp, phong cách chụp ảnh tạp chí, chất lượng cao, toàn thân.");
            try {
                const response = await axios.post(route('ai-image.generate-my-ai-image'), formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                });
                if (response.data?.generated_images?.s3_url) {
                    return response.data.generated_images.s3_url;
                } else {
                    return "";
                }
            } catch (error) {
                return "";
            }
        } catch (error) {
            return "";
        }
    };
    // Ảnh phối cảnh sản phẩm
    const generateImageBackground = async (inputImageFile) => {
        let formData = new FormData();
        formData.append("image", inputImageFile);
        try {
            const response = await axios.post(route('ai-business.generate-image-background'), formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });
            if (response.data.status === 'success') {
                return response.data.s3_url;
            } else {
                console.warn("Tạo ảnh nền không thành công. Chuyển sang generatePromptPerspective.");
                return await generatePromptPerspective(inputImageFile);
            }
        } catch (error) {
            console.error(error);
            let s3url = await generatePromptPerspective(inputImageFile);
            if (s3url) {
                return s3url;
            }
            toast.error("Lỗi khi gọi API tạo ảnh nền!");
        }
    };

    const upscaleImage = async (index, s3_url) => {
        let imageUrl = s3_url;
        hideOption()
        images.value[index].isLoading = true
        let fileBlob = await convertUrlToFile(imageUrl)
        let formData = new FormData();
        formData.append("image", fileBlob);
        try {
            const response = await axios.post(route('ai-business.upscale-image'), formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });
            if (response.data.status === 'success') {
                images.value[index].imageRef.s3_url = response.data.s3_url;
                images.value[index].isLoading = false
                updateData();
            } else {
                toast.error("Làm nét ảnh không thành công. Vui lòng thử lại.");
            }
        } catch (error) {
            console.error(error);
            toast.error("Làm nét ảnh không thành công. Vui lòng thử lại.");
        } finally {
            images.value[index].isLoading = false;
        }
    }

    const toggleShowOption = (index) => {
        if(images.value) {
            for(var i = 0; i < images.value.length; i++) {
                if(index == i && !images.value[i].isShowOptionImage) {
                    images.value[i].isShowOptionImage = true
                    showOptions.value = true
                } else {
                    images.value[i].isShowOptionImage = false
                }
            }
        }
    }
    const imageKey = ref(0)
    const isShowFullImage = ref(false)
    const thumbnail = ref("")
    const trigUploadImage = (index) => {
        imageKey.value = index
        if (uploadImageItem.value instanceof HTMLInputElement) {
            uploadImageItem.value.click()
        } else {
            console.error('uploadImageItem is not a valid input element', uploadImageItem.value)
        }
    }
    const downloadImage = (image) => {
        var url = route(("ai-video.downloadFile"), {url:image, name:"image.png"})
        window.open(url, '_blank');
    };

    const hideOption = () => {
        if(images.value) {
            for(var i = 0; i < images.value.length; i++) {
                images.value[i].isShowOptionImage = false
            }
        }
        showOptions.value = false
    }

    const showFullImage = (imageUrl) => {
        thumbnail.value = imageUrl
        isShowFullImage.value = true
    }
    const createVideoRef = ref("")
    const imageTovideoRef = ref(null)
    const showPopupCreateVideo = (index, s3_url) => {
        if(imageTovideoRef.value) {
            imageTovideoRef.value.showPopupCreateVideo(index, s3_url)
        }
    }

    const getImageRatioFromURL = async (url) => {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => {
                const width = img.naturalWidth;
                const height = img.naturalHeight;
                const ratio = width / height;
                resolve({ width, height, ratio });
            };
            img.onerror = () => {
                reject(new Error("Không thể tải ảnh từ URL"));
            };
            img.src = url;
        });
    }

    const updateClassImage = async () => {
        for (const image of images.value) {
            try {
                const { ratio } = await getImageRatioFromURL(image.imageRef.s3_url);
                image.className = ratio > 1 ? 'w-full' : ''; // hoặc 'h-full' nếu ảnh dọc
            } catch (error) {
                console.error('Lỗi ảnh:', error);
            }
        }
    }

    defineExpose({ saveImage, saveGenerationResult, autoCreateImage });
</script>
<style>
    .min-h-[250px] {
        min-height: 250px;
    }
</style>
