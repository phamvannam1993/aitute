<template>
    <div class="flex flex-col justify-between gap-4 w-full flex-wrap md:flex-nowrap rounded-[25px]">
        <div  class="bg-white w-full rounded-[10px] md:rounded-[25px] p-[12px] md:p-[25px] h-fit drop-shadow-xl">
            <div class="flex flex-col">
                <div class="flex flex-wrap justify-between items-center gap-2">
                    <div class="flex items-center gap-2 flex-shrink min-w-[200px]">
                        <div class="w-[52px] overflow-hidden rounded-full border border-[#d4d4d4]">
                        <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
                        </div>
                        <h2 class="text-black font-bold text-xl lg:text-xl 2xl:text-3xl leading-none">
                            {{ props.collectionName ? `Tạo ${capitalizeText(props.collectionName)}` : "Tạo ảnh xây thương hiệu cá nhân" }}
                        </h2>
                    </div>

                    <div class="mt-2 sm:mt-0 ml-auto">
                        <select id="tool-dropdown" class="rounded-full border border-[#D4D4D4] w-[260px]">
                        <option
                            v-for="(tool, index) in toolsList"
                            :key="index"
                            :value="tool"
                        >
                            {{ tool }}
                        </option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="flex justify-between items-center mb-4 flex-wrap mt-2">
                <p class="text-ai3goc-sec text-sm sm:text-[20px] font-bold ">Thực hiện theo các bước sau:</p>
                <span @click="handleCheckCredit" class="text-ai3goc-primary underline  ml-auto cursor-pointer text-sm sm:text-xl font-bold italic ">Đào tạo ảnh mẫu</span>
            </div>
            <form @submit.prevent="generateImage" class="flex flex-col gap-4">
                <div class="relative">
                    <div class="text-black text-[14px] flex md:items-center flex-col md:flex-row justify-between mb-2">
                        <Step :step="1" stepName="Tải mẫu ảnh hoặc chọn mẫu bạn muốn tạo" forName="selectUpload"/>
                        <input type="file" id="avatar" @change="handleAvatarChange" accept=".jpg,.png,.jpeg" class="hidden" ref="avatarInput" />
                        <button v-if="!isShowImageGallery" type="button" @click="$refs.avatarInput.click()" class="flex items-center font-bold px-6 py-2 justify-center gap-2 bg-transparent  rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-ai3goc-sec">
                            <img src="/assets/img/ai3goc/icon/upload.svg" class="size-6 md:size-5 xl:size-[50px]" />
                            <span class="text-[12px] md:hidden xl:block xl:text-[14px] text-sm sm:text-sm text-ai3goc-sec">Tải ảnh lên</span>
                        </button>
                        <div v-if="isShowImageGallery" class="flex flex-wrap gap-2 ml-auto my-1">
                            <div class="relative inline-block lg:my-2" ref="dropdownRef">
                                <button type="button" @click="isDropdownOpen = !isDropdownOpen" class="flex text-ai3goc-sec gap-2 items-center border-2 border-[#D4D4D4] px-3 h-10 rounded-[20px] outline-none min-w-40 w-[260px]">
                                    <div class="flex w-full justify-between items-center gap-2">
                                        <p class="capitalize text-[14px]">
                                            {{ categoryTitle || "Chọn chủ đề" }}
                                        </p>
                                        <svg :class="isDropdownOpen ? 'rotate-0' : 'rotate-180'" width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 1L5 5L9 1" stroke="#C5C5C5" />
                                        </svg>
                                    </div>
                                </button>

                                <div v-if="isDropdownOpen" class="absolute mt-2 bg-white border-2 border-[#D4D4D4] rounded-[20px] shadow-lg w-full z-10 px-2 py-1">
                                    <div class="h-[300px] max-h-[300px] overflow-y-auto custom-scrollbar">
                                        <div v-for="category in listCategories" :key="category.id" class="px-2 py-2 hover:bg-ai3goc-primary/10 rounded-[20px] cursor-pointer text-black text-sm sm:text-sm" @click="selectCategory(category)">
                                            {{ category.title }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col lg:flex-row justify-between lg:gap-2 my-2 lg:items-center">

                    </div>
                    <div class="w-fit overflow-y-auto h-[60vh] lg:h-[70vh] mb-2 relative text-center custom-scrollbar" @mousedown.prevent>
                        <div class="grid grid-cols-3 lg:grid-cols-3 2xl:grid-cols-5 gap-2 lg:gap-5 w-full">
                            <UploadButton :text="'Tải ảnh mẫu'" :handleClick="triggerFileInput" imageButtonSrc="/assets/img/ai3goc/icon/home_upload.svg" class-name=""/>
                            <div @click="setImageTemplate(template.s3_url)" v-for="template in templates" :key="template.id" class="relative rounded-xl cursor-pointer">
                                <img :src="template.s3_url" :alt="template.id" class="w-full h-full rounded-lg object-cover" />
                                <input type="radio" :value="template.s3_url" v-model="avatarPreview" class="absolute top-2 checked:bg-ai3goc-sec lg:top-4 right-2 lg:right-4 cursor-pointer outline-none size-6" />
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center items-center w-full my-3">
                        <button @click.prevent="loadMoreFunc" :disabled="isLoadingMore" class="bg-[#DDDDDD] text-black px-4 py-2 text-center rounded-full hover:scale-105 transform transition-transform w-28">
                            <span class="" v-if="!isLoadingMore">Xem thêm</span>
                            <span v-else role="status" class="">
                                <svg aria-hidden="true" class="w-6 h-6 mx-auto text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                                </svg>
                                <span class="sr-only">Loading...</span>
                            </span>
                        </button>
                    </div>
                    <hr class="border-t-2 border-gray-300 my-4">
                    <div class="grid lg:grid-cols-2 gap-6">
                        <section>
                            <div class="text-black" v-if="!isUseMask">
                                <label for="image-description" class="text-sm md:text-base flex gap-1 items-center mb-1">
                                    <!-- <div class="flex items-center py-1 px-2 rounded-2xl text-white bg-primary-color">
                                        <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                                        <p>Bước 1</p>
                                    </div> -->
                                    <p>Mô tả nội dung ảnh</p>
                                </label>
                                <div class="relative">
                                    <SuggestionPrompt v-model="imageDescription" :type="'image'" :screenId="2" />
                                    <textarea id="image-description" v-model="imageDescription" type="text" placeholder="Nhập mô tả ảnh bạn muốn tạo..." :disabled="loadingCreateImage" class="w-full p-3 mt-1 h-[200px] border text-black border-[#D4D4D4] rounded-[10px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder:font-light"></textarea>

                                    <div class="object-mic relative ml-auto">
                                        <div v-if="isRecording" class="outline-mic right-3 bottom-3 flex items-center justify-center"></div>
                                        <div v-if="isRecording" class="outline-mic right-3 bottom-3 flex items-center justify-center" id="delayed"></div>
                                        <div v-if="isRecording" class="button-mic right-3 bottom-3 flex items-center justify-center"></div>
                                        <button class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center" @click="startRecording" :disabled="disabledRecord" type="button">
                                           <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                           <circle cx="9.4939" cy="9.4939" r="9.4939" fill="#B4B4B4"/>
                                           <path d="M13.116 5.19287C13.116 3.07407 11.4592 1.35645 9.41544 1.35645C7.37166 1.35645 5.71484 3.07407 5.71484 5.19287V7.38828C5.71484 9.50708 7.37166 11.2247 9.41544 11.2247C11.4592 11.2247 13.116 9.50708 13.116 7.38828V5.19287Z" fill="white"/>
                                           <path d="M13.973 9.48881C13.8378 9.48752 13.7046 9.52244 13.5868 9.59009C13.4689 9.65773 13.3706 9.75573 13.3014 9.87431C12.9083 10.5712 12.342 11.1501 11.6599 11.5526C10.9777 11.9551 10.2036 12.167 9.41562 12.167C8.62763 12.167 7.85356 11.9551 7.17138 11.5526C6.4892 11.1501 5.92299 10.5712 5.52981 9.87431C5.46082 9.75561 5.36245 9.65751 5.24458 9.58985C5.12672 9.52219 4.99349 9.48734 4.85827 9.48881C4.72055 9.48864 4.58521 9.52534 4.46575 9.59526C4.34629 9.66518 4.2469 9.76587 4.17751 9.88726C4.10812 10.0087 4.07116 10.1465 4.07033 10.287C4.06949 10.4276 4.1048 10.5659 4.17274 10.6881C4.63836 11.5082 5.28225 12.2082 6.0548 12.7342C6.82734 13.2602 7.70792 13.5982 8.62866 13.7221V15.2837H6.80467C6.59596 15.2837 6.39579 15.3683 6.24821 15.519C6.10062 15.6696 6.01772 15.8738 6.01772 16.0868C6.01772 16.2998 6.10062 16.5041 6.24821 16.6547C6.39579 16.8053 6.59596 16.89 6.80467 16.89H12.0371C12.2458 16.89 12.4459 16.8053 12.5935 16.6547C12.7411 16.5041 12.824 16.2998 12.824 16.0868C12.824 15.8738 12.7411 15.6696 12.5935 15.519C12.4459 15.3683 12.2458 15.2837 12.0371 15.2837H10.2026V13.7221C11.1232 13.5978 12.0036 13.2597 12.7761 12.7337C13.5486 12.2077 14.1926 11.5079 14.6585 10.6881C14.7264 10.5659 14.7617 10.4276 14.7609 10.287C14.7601 10.1465 14.7231 10.0087 14.6537 9.88726C14.5843 9.76587 14.4849 9.66518 14.3655 9.59526C14.246 9.52534 14.1107 9.48864 13.973 9.48881Z" fill="white"/>
                                           </svg>
                                        </button>
                                    </div>
                                </div>

                                <span v-if="errors.description" class="text-red-500 text-sm">{{ errors.description }}</span>
                            </div>
                            <!-- <div class="flex justify-start items-center gap-2 mb-4">
                                <input type="checkbox" v-model="isUseMask" class="rounded-md text-ai3goc-sec shadow-sm focus:ring-ai3goc-sec focus:border-ai3goc-sec"/>
                                <label class="text-black">Giữ nguyên trang phục và bối cảnh</label>
                            </div> -->
                            <div class="flex justify-between items-start gap-4 mt-4">
                                <div class="flex flex-col gap-2 w-1/2">
                                    <div v-if="avatarPreview" class="flex items-center gap-2">
                                        <span class="text-black text-sm md:text-base leading-none">Ảnh mẫu đã chọn:</span>
                                    </div>
                                    <div v-else class="flex items-center gap-2">
                                        <span class="text-black text-sm md:text-base leading-none">Chưa chọn ảnh mẫu:</span>
                                    </div>

                                    <!-- Ảnh mẫu (avatar) với tỷ lệ 9/16 -->
                                    <div class="w-full aspect-[9/16] bg-[#E6E6E6] rounded-lg flex items-center justify-center my-2 overflow-hidden">
                                        <img v-if="avatarPreview" :src="avatarPreview" ref="imageSelectedRef" class="h-full object-cover" />
                                        <img v-else src="/assets/svgs/aiwow/image.svg" alt="placeholder" class="w-20" />
                                        <img v-else src="/assets/svgs/aiwow/image.svg" alt="placeholder" class="w-20" />
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 w-1/2">
                                    <div v-if="avatarPreview" class="flex items-center gap-2">
                                        <span class="text-black text-sm md:text-base leading-none">Ảnh sản phẩm của bạn:</span>
                                    </div>
                                    <div v-else class="flex items-center gap-2">
                                        <span class="text-black text-sm md:text-base leading-none">Chưa tải ảnh sản phẩm:</span>
                                    </div>

                                    <!-- Ảnh sản phẩm với tỷ lệ 9/16 -->
                                    <div class="w-full aspect-[9/16] bg-[#E6E6E6] rounded-lg flex items-center justify-center my-2 overflow-hidden">
                                        <img v-if="productPreview" :src="productPreview" ref="imageSelectedRef" class="h-full object-cover" />
                                        <img v-else src="/assets/svgs/aiwow/image.svg" alt="placeholder" class="w-20" />
                                    </div>
                                </div>
                            </div>


                            <div class="text-black text-[14px] flex md:items-center flex-row justify-between mb-2">
                                <Step :step="2" stepName="Tải ảnh sản phẩm" forName="selectUpload" :required="false"/>
                                <input type="file" id="product" @change="handleProductChange" accept=".jpg,.png,.jpeg" class="hidden" ref="productInput" />
                                <button type="button" @click="$refs.productInput.click()" class="flex items-center font-bold px-8 py-2 justify-center gap-2 bg-transparent  rounded-[10px] backdrop-blur-sm transition-colors hover:scale-105 bg-[#F49A23] mt-1 md:mt-0">
                                    <img src="/assets/img/ai3goc/icon/home_upload_step2.svg" class="size-[14px] md:size-5 xl:size-[16px]" />
                                    <span class="text-[12px] md:hidden xl:block xl:text-[14px] text-sm sm:text-sm">Tải lên</span>
                                </button>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-1 md:flex items-center justify-between text-black">
                                <div class="w-full md:w-3/4">
                                    <Step :step="3" stepName="Số lượng ảnh" forName="image-model" required="true" />
                                </div>
                                <select
                                    v-model="numberImageSelect"
                                    :disabled="loadingCreateImage"
                                    :class="{
                                        'bg-gray-200 border-gray-200': !numberImageSelect,
                                        'bg-white border-[#D4D4D4]': numberImageSelect,
                                    }"
                                    class="block mt-1 text-[14px] appearance-none w-full md:w-3/12 xl:w-1/2 text-black py-2 px-4 md:px-2 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    id="image-model"
                                >
                                    <option v-for="(model, index) in 4" :value="model" :key="index">
                                        {{ model }}
                                    </option>
                                </select>
                                <span v-if="errors.number" class="text-red-500 text-sm">{{ errors.number }}</span>
                            </div>

                            <div :key="index" class="mt-4 grid grid-cols-2 gap-1 md:flex justify-between items-center text-black">
                                <div class="w-full md:w-3/4"><Step step="4" :stepName="`Kích thước ảnh`" :forName="`image-dimensions`" /></div>
                                <select
                                    v-model="imageDimensions"
                                    :id="`image-dimensions`"
                                    :disabled="loadingCreateImage"
                                    :class="{
                                        'bg-gray-200 border-gray-200': !imageDimensions,
                                        'bg-white border-[#D4D4D4]': imageDimensions,
                                    }"
                                    class="block mt-1 text-[14px] appearance-none w-full md:w-3/12 xl:w-1/2 text-black py-2 px-4 md:px-2 pr-8 rounded-[10px] leading-tight focus:outline-none bg-white border-[#D4D4D4]"
                                >
                                    <option v-for="dimension in validDimensions" :value="dimension" :key="dimension.key">
                                        {{ dimension.key }}
                                    </option>
                                </select>
                                <span v-if="errors.dimensions && errors.dimensions[index]" class="text-red-500 text-sm">
                                    {{ errors.dimensions[index] }}
                                </span>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-1 md:flex md:flex-col xl:flex-row justify-between items-center md:items-start lg:items-center text-black">
                                <div class="w-full md:w-3/4">
                                    <Step step="5" stepName="Chọn mẫu AI" forName="model-select" required="true" />
                                </div>
                                <select v-model="selectedModelId" id="model-select" class="block mt-1 text-[14px] appearance-none w-full md:w-full xl:w-1/2 text-black py-2 px-4 md:px-2 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500 border-[#D4D4D4]">
                                    <option v-for="model in props.my_ai_image_models" :key="model.id" :value="model.id" :disabled="model.status !== 'succeeded'">
                                        {{ model.alias_name }}
                                    </option>
                                </select>
                            </div>
                            <!-- <div v-if="isUseMask" class="mt-4 flex flex-col justify-start items-start text-black">
                                <Step :step="numberImageSelect + 5" step-name="Điều chỉnh nâng cao" required="true"/>
                                <div class="w-full flex items-center">
                                    <input
                                        id="strength-prompt"
                                        type="range"
                                        v-model="strengthPrompt"
                                        min="0.6"
                                        max="1.2"
                                        step="0.01"
                                        class="w-full accent-ai3goc-sec"
                                    />
                                    <span class="ml-4 text-sm font-bold text-oriaccent-ai3goc-sec">
                                        {{ strengthPrompt }}
                                    </span>
                                </div>
                                <p class="text-[#989898] text-sm mt-1 italic">Nếu khuôn mặt chưa giống hãy điều chỉnh tăng dần về 1.2</p>
                                <div class="w-full flex items-center">
                                    <input
                                        id="denoise"
                                        type="range"
                                        v-model="denoise"
                                        min="0.3"
                                        max="0.45"
                                        step="0.01"
                                        class="w-full accent-ai3goc-sec"
                                    />
                                    <span class="ml-4 text-sm font-bold text-oriaccent-ai3goc-sec">
                                        {{ denoise }}
                                    </span>
                                </div>
                                <p class="text-[#989898] text-sm mt-1 italic">Nếu quần áo chưa giống với hình mẫu hãy điều chỉnh tăng dần về 0.45</p>
                            </div> -->
                            <div class="mt-4 flex md:flex-col xl:flex-row justify-between items-center md:items-start lg:items-center text-black">
                                <div class="w-full">
                                    <Step :step="6" stepName="Bấm vào đây" forName="create-image" />
                                </div>
                                <div class="flex justify-start w-1/2 md:w-full lg:w-1/2">
                                    <button id="create-image" :disabled="loadingCreateImage" type="submit" class="px-2 lg:px-4 py-2 bg-ai3goc-sec text-white w-full rounded-[10px] text-sm md:text-base font-bold disabled:opacity-50 disabled:pointer-events-none hover:scale-105">Gửi yêu cầu</button>
                                </div>
                            </div>
                        </section>

                        <section ref="resultBox">
                            <p class="text-ai3goc-sec italic mb-1 text-center">Hiển thị kết quả</p>
                            <div class="flex flex-col justify-between items-center h-fit">
                                <div :class="`${numberImageSelect > 1 ? 'grid grid-cols-2 gap-1' : 'grid grid-cols-1 text-sm'}`" class="gap-1 md:gap-4 w-full columns-2">
                                    <div v-for="index in numberImageSelect" :key="index">
                                        <div v-if="imageRef[index - 1]?.s3_url" class="relative bg-gray-300 rounded-sm mb-4 border lg:border-none">
                                            <div class="absolute top-2 left-2 bg-black bg-opacity-20 text-white px-1 rounded-md">Ảnh avatar</div>
                                            <img @click="openDetail(imageRef[index - 1])" :src="imageRef[index - 1].s3_url" alt="image generated by AI" class="w-full h-auto object-contain cursor-pointer" />
                                            <button v-if="imageRef[index - 1].s3_url" @click="upscaleImage(index - 1, imageRef[index - 1].s3_url)" class="absolute bottom-2 right-2 rounded-[7.4px] text-white bg-black bg-opacity-30 flex justify-center items-center px-1 py-[2px] font-bold" type="button">
                                                <img class="w-4 h-4" src="/assets/svgs/star-icon.svg"/>
                                                Làm nét
                                            </button>
                                        </div>
                                        <div v-else class="bg-[#E6E6E6] flex items-center justify-center rounded-xl mb-6 relative" :style="{ height: `${imageDimensions.value[1] / (numberImageSelect > 1 ? 3 : 2)}px`}">
                                            <div class="absolute top-2 left-2 bg-black bg-opacity-20 text-white px-1 rounded-md">Ảnh avatar</div>
                                            <img v-if="!loadingImageState[index - 1]" src="/assets/svgs/aiwow/image.svg" alt="loading" class="mx-auto w-24" />
                                            <div v-else class="flex flex-col items-center justify-center">
                                                <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                                <h3 class="text-white text-center m-2">Hệ thống đang xử lý, xin đợi một chút</h3>
                                            </div>
                                        </div>
                                        <ButtonTaskBarNoIcon :is-multiple-result="numberImageSelect > 1" :isActive="imageRef[index - 1]" :selectedImage="imageRef[index - 1]" type="image" :shareLinkableType="'AIGeneratedMedia'" :features="['video', 'swap_face', 'background']" :handleSharpenClick="() => sendImage(index - 1)" />
                                    </div>
                                </div>
                                <div class="w-full justify-center mt-4 md:w-full xl:w-full lg:pb-[4px]">
                                    <div class="flex justify-center">
                                        <a :href="route('ai-image.history-my-ai-image')" class="px-4 w-[180px] text-center py-2 bg-ai3goc-sec text-white font-bold text-[15px] rounded-[10px] h-fit"> Lịch sử </a>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </form>
        </div>
        <Popup :message="errorMessage" v-show="isShowErrorMessage" @close="isShowErrorMessage = false"></Popup>
        <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
            <FormShareLink :shareLink="shareLink" />
        </Modal>
        <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup"
            @buyCredit="handleBuyCredit"
            @cancel="handleBuyCancel"
            :currentCredit = "pageProps.auth.user.credit || 0"
            :additionalCredit = "additionalCredit"
        />
        <PopupConfirmFineTune v-if="isShowConfirmModal" :handleConfirm="handleCheckCredit" :message="'Để tạo được ảnh Tự Sướng, bạn phải cung cấp 10 ảnh mẫu cho A.I đào tạo ảnh của bạn, thời gian khoảng 45 phút. \n Chi phí cho 1 lần với 1 mẫu : 250.000 điểm. Bạn có muốn tiếp tục không?'" @close="isShowConfirmModal = false" />
        <div v-if="isShowTrainingModel && isShow" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100]">
            <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[480px] xl:w-[580px] rounded-[40px]">
                <div class="w-full flex items-center justify-center">
                    <img src="/assets/img/ai3goc/logo/logo-text.svg" alt="Thông báo" class="size-[160px] md:size-[200px]" />
                </div>
                <div class="text-black text-center">
                    <h2 class="text-[16px] md:text-[24px] font-bold text-center mt-1 md:mt-2">Xin chào {{ pageProps.auth.user.name || "" }}</h2>
                    <p class="text-[12px] md:text-[16px] font-medium mb-4 lg:px-10">Mẫu ảnh A.I của bạn đang được đào tạo, quá trình đào tạo sẽ diễn ra trong khoảng 30-45 phút.</p>
                    <p class="text-[12px] md:text-[16px] font-medium mb-4">Xin vui lòng chờ!</p>
                </div>
                <div class="flex items-center gap-4 md:gap-8 justify-center mt-6">
                    <button @click="isShow = false" class="rounded-[16px] w-[200px] font-bold bg-white text-ai3goc-sec border-[2px] border-ai3goc-sec py-4 text-[14px] md:text-[16px] cursor-pointer">Bỏ qua</button>
                </div>
            </div>
        </div>
    </div>
    <div v-if="isLoadingChooseImage" class="fixed inset-0 flex flex-col gap-2 items-center justify-center z-[60] bg-black bg-opacity-30">
        <div class="loader"></div>
        <p class="text-base 2xl:text-xl">Đang phân tích ảnh để tạo prompt. Vui lòng chờ!!!</p>
    </div>
    <div
        v-if="trainingImagePopup"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        @click.self="trainingImagePopup = false"
    >
        <!-- Modal -->
        <div
        class="relative z-50 bg-white rounded-[25px] shadow-lg h-fit overflow-auto w-[90%] md:w-3/4"
        >
        <!-- Nút đóng -->
        <div class="absolute top-2 right-2 z-10 flex justify-end p-2">
            <button
            @click="trainingImagePopup = false"
            class="text-gray-500 hover:text-gray-800 text-2xl"
            >
             <img src="/assets/img/orion/icon/close_yellow.svg" alt="Close" class="w-6 h-auto" />
            </button>
        </div>

        <!-- Nội dung -->
        <TraningImageComponent />
        </div>
    </div>
</template>
<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed, nextTick, onBeforeMount, onMounted, ref, watch, onBeforeUnmount } from "vue";
import { toast } from "vue3-toastify";
import axios from "axios";
import FormShareLink from "@/Components/FormShareLink.vue";
import Modal from "@/Components/Modal.vue";
import Popup from "@/Components/Popup.vue";
import CreditBuyModal from '../../../Components/ModalBuyCredit/BuyCredit.vue';
import PopupConfirmFineTune from '@/Components/PopupConfirmFineTune.vue';
import UploadButton from '@/Components/UploadButton.vue'
import Taskbar from "@/Components/TaskBar.vue";
import Step from "@/Components/Step/Index.vue";
// import Step from "@/Components/Step.vue";
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
import ButtonTaskBar from "@/Components/ButtonTaskBar.vue";
import ButtonTaskBarNoIcon from "@/Components/ButtonTaskBarNoIcon.vue";
import TraningImageComponent from "./FineTune/TraningImageComponent.vue";
const denoise = ref(0.35);

const props = defineProps({
    ai_assistant: Object,
    my_ai_image_models: Array,
    collectionSelected: Object,
    collectionName: String,
    listCollection: Array
});
const breadcrumbsData = [{ text: "Hình AI của tôi", link: "ai-image.history-my-ai-image" }];

const isShowConfirmModal = ref(false)
const { props: pageProps } = usePage();
const auth = pageProps.auth;
const isLoadingChooseImage = ref(false);

const additionalCredit = ref(0);
const isDropdownOpen = ref(false);
const isShowImageGallery = ref(true);

const credits = ref(0);
const imageRef = ref([]);
const currentImgSelect = ref(null);
const selectedModelId = ref(null);
const isShowTrainingModel = ref(false);
const isShow = ref(false);
const toolsList = ref(['Tạo ảnh từ ảnh mẫu và text']);
const trainingImagePopup = ref(false);
const sizeImage = ref();

onMounted(async () => {
    const response = await axios.get(route("ai-image.has-fine-tune-model"));
    const hasImage = response.data.exists;
    if (hasImage) {
        isShowTrainingModel.value = true;
    }
});
const avatarInput = ref(null);

const triggerFileInput = () => {
  avatarInput.value.click();
};


const succeededModel = computed(() => {
    return props.my_ai_image_models?.find((model) => model.status === "succeeded");
});

if (succeededModel.value) {
    selectedModelId.value = succeededModel.value.id;
}

watch(imageRef, (newValue) => {
    // Set currentImgSelect to the first element of imageRef if it exists
    currentImgSelect.value = newValue.length > 0 ? newValue[0] : null;
});

const popupMessage = ref("Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!");
const acceptUpgrade = ref(true);

const validDimensions = computed(() => {
    return [
        { key: "9:16", value: [768, 1360] },
        { key: "16:9", value: [1360, 768] },
        { key: "1:1", value: [1024, 1024] },
    ];
});

const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
// setDefault Value For Model = Flux Schnell
const imageModel = ref("Flux Schnell");
const videoDimensions = ref("16:9");
const imageDimensions = ref(validDimensions.value[0]);
const imageDescription = ref("");
const imageStyle = ref("Thực tế");
const imageArtist = ref("");
const imageHeight = ref(1024);
const imageWidth = ref(1024);
const isLoading = ref(false);
const isShowDes = ref(true);
const resultBox = ref(null);
const imageResponse = ref([]);
const errors = ref({});
const numberImageSelect = ref(1);
const loadingCreateImage = ref(false);
const loadingImageState = ref([]);
const selectedImage = ref(null);
const isLoadingCategory = ref(false);
const openDetail = (item) => {
    selectedImage.value = item;
    currentImgSelect.value = item;
};


const showConfirmModal = ref(false);
const imageDeleteId = ref(null);

const closeDetail = () => {
    selectedImage.value = null;
};

const upscaleImage = async (index, s3_url) => {
    let imageUrl = s3_url;
    imageRef.value[index].s3_url = null
    console.log(index);
    loadingImageState.value[index + 1] = true
    let fileBlob = await convertUrlToFile(imageUrl)
    let formData = new FormData();
    formData.append("image", fileBlob);
    try {
        const response = await axios.post(route('ai-business.upscale-image'), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        console.log('123');
        if (response.data.status === 'success') {
            imageRef.value[index].s3_url = response.data.s3_url;
            loadingImageState.value[index] = false
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

// watch(numberImageSelect, (newVal) => {
//     imageDimensions.value = Array(newVal).fill(validDimensions.value[0]);
// });

const isUseMask = ref(false);
const avatarPreview = ref(null);
const imageSelectedRef = ref(null);
// Xử lý khi chọn ảnh
const avatarName = ref("");
const avatarFile = ref(null);
const productName = ref("");
const productFile = ref(null);
const productPreview = ref(null);

const handleProductChange = async (event) => {
    const file = event.target.files[0];
    event.target.value = "";
    if (file instanceof File) {
        if(props.my_ai_image_models.length == 0){
            isShowConfirmModal.value = true;
        }
        const validImageTypes = ["image/jpeg", "image/png", "image/jpg"];
        if (!validImageTypes.includes(file.type)) {
            toast.error("File ảnh phải có định dạng: jpeg, png, jpg.");
            return;
        }

        productFile.value = file;
        productName.value = file.name;

        productPreview.value = URL.createObjectURL(file);
        await nextTick();

        imageSelectedRef.value?.scrollIntoView({ behavior: "smooth" });
    } else {
        console.error("File không hợp lệ");
        productFile.value = null;
        productName.value = "Chưa có ảnh nào được chọn";
    }
};

const handleAvatarChange = async (event) => {
    const file = event.target.files[0];
    event.target.value = "";
    if (file instanceof File) {
        const validImageTypes = ["image/jpeg", "image/png", "image/jpg"];
        if (!validImageTypes.includes(file.type)) {
            toast.error("File ảnh phải có định dạng: jpeg, png, jpg.");
            return;
        }

        avatarFile.value = file;
        avatarName.value = file.name;

        avatarPreview.value = URL.createObjectURL(file);
        await nextTick();
        imageSelectedRef.value?.scrollIntoView({ behavior: "smooth" });
        if(props.my_ai_image_models.length == 0){
            isShowConfirmModal.value = true;
        }
    } else {
        console.error("File không hợp lệ");
        avatarFile.value = null;
        avatarName.value = "Chưa có ảnh nào được chọn";
    }
};

const showAffKeyPopup = ref(false);
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
        formData.append("model", imageModel.value);
        formData.append("type", "image");
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true;
            if (response.data.affCode == "aff_trial") {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này.";
                acceptUpgrade.value = false;
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
            Object.values(errors).forEach((errorMessages) => {
                if (Array.isArray(errorMessages)) {
                    errorMessages.forEach((error) => {
                        toast.error(error);
                    });
                } else {
                    toast.error(errorMessages);
                }
            });
        } else {
            if (error.response) {
                toast.error(error.response.data.message || "Đã xảy ra lỗi.");
            } else if (error.request) {
                toast.error("Không thể kết nối đến server.");
            } else {
                toast.error("Lỗi: " + error.message);
            }
        }
        return false;
    }
};

const checkCreditFineTune = async () => {
    try {
        const formData = new FormData();
        formData.append("model", "my_ai_image");
        formData.append("type", "my_ai_image");
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true;
            if (response.data.affCode == "aff_trial") {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này.";
                acceptUpgrade.value = false;
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
            Object.values(errors).forEach((errorMessages) => {
                if (Array.isArray(errorMessages)) {
                    errorMessages.forEach((error) => {
                        toast.error(error);
                    });
                } else {
                    toast.error(errorMessages);
                }
            });
        } else {
            if (error.response) {
                toast.error(error.response.data.message || "Đã xảy ra lỗi.");
            } else if (error.request) {
                toast.error("Không thể kết nối đến server.");
            } else {
                toast.error("Lỗi: " + error.message);
            }
        }
        return false;
    }
};

const errorMessage = ref("");
const isShowErrorMessage = ref(false);
const callGenerateImageResponse = ref([]);
const strengthPrompt = ref(1.0);
const generateImage = async () => {
    loadingCreateImage.value = true;
    imageRef.value = [];
    loadingImageState.value = [];

    try {
        for (let index = 0; index < numberImageSelect.value; index++) {
            const hasEnoughCredit = await checkCredit();
            if (!hasEnoughCredit) {
                isLoading.value = false;
                return;
            }
            if (!avatarFile.value && !avatarPreview.value && imageDescription.value.trim() == '') {
                toast.error("Phải tải ảnh lên hoặc chọn mẫu có sẵn hoặc nhập mô tả nội dung ảnh.");
                loadingCreateImage.value = false;
                return;
            }

            if (isUseMask.value && !avatarFile.value && !avatarPreview.value) {
                toast.error("Bạn phải tải ảnh lên hoặc chọn mẫu nếu bật tính năng giữ nguyên trang phục và bối cảnh.");
                loadingCreateImage.value = false;
                return;
            }

            // if (!imageDimensions.value[index] || !imageDimensions.value[index].key) {
            //     toast.error("Tỉ lệ ảnh không hợp lệ.");
            //     loadingCreateImage.value = false;
            //     return;
            // }

            if (!selectedModelId.value) {
                toast.error("Mô hình AI chưa được chọn.");
                loadingCreateImage.value = false;
                return;
            }

            loadingImageState.value[index] = true;
            callGenerateImageResponse.value[index] = [];

            try {
                const response = await callGenerateImage(index);

                if (response?.status === 200 && response?.data?.generated_images) {
                    isLoading.value = false;
                    const modelImageUrl = response.data.generated_images.s3_url;
                    credits.value = response.data.credits;

                    if (productFile.value && modelImageUrl) {
                        try {
                            loadingImageState.value[index + 1] = true;
                            const modelImageBlob = await urlToBlob(modelImageUrl);
                            const combinedImage = await combineImages(productFile.value, modelImageBlob, imageDimensions.value);

                            if (combinedImage) {
                                imageResponse.value.push(combinedImage.s3_url);
                                imageRef.value.push(combinedImage);
                            }
                        } catch (combineError) {
                            console.error("Lỗi khi kết hợp ảnh:", combineError);
                            toast.error("Không thể kết hợp ảnh với sản phẩm.");
                        }
                    } else {
                        if (response?.data?.prompt_id) {
                            callGenerateImageResponse.value[index] = response.data;
                        }

                        imageResponse.value.push(modelImageUrl);
                        imageRef.value.push(response.data.generated_images);
                    }
                }
            } catch (error) {
                if (error.response && error.response.status === 403) {
                    console.log("error.response ", error.response);
                    showCreditModal();
                } else if (error.response && error.response.status === 500) {
                    toast.error("Đã có lỗi xảy ra khi tạo ảnh. Xin vui lòng tạo lại!");
                    loadingImageState.value[index] = false;
                    console.error("Có lỗi xảy ra: 500 Internal Server Error.");
                    continue;
                } else {
                    console.error("Có lỗi xảy ra:", error);
                    toast.error(error.response?.data?.message || "Đã có lỗi xảy ra khi tạo ảnh. Xin vui lòng tạo lại!");
                }
            }


            await new Promise((resolve) => setTimeout(resolve, 2000));
        }
        loadingCreateImage.value = false;

        if (!isShowDes.value) {
            await nextTick(() => {
                resultBox.value.scrollIntoView({ behavior: "smooth" });
            });
        }
    } catch (error) {
        console.error("Có lỗi xảy ra:", error);
        toast.error(error.response?.data?.message || "Unknown error occurred");
    } finally {
        isLoading.value = false;
    }
};

const urlToBlob = async (imageUrl) => {
    try {
        const response = await fetch(imageUrl);
        const blob = await response.blob();
        return new File([blob], "default_model_image.jpg", { type: blob.type });
    } catch (error) {
        console.error("Lỗi khi tải ảnh mặc định:", error);
        return null;
    }
};

const combineImages = async (productImage, modelImage, ratio) => {
    let formData = new FormData();

    if (!modelImage) {
        const defaultModelBlob = await urlToBlob(DEFAULT_MODEL_IMAGE);
        if (!defaultModelBlob) {
            toast.error("Không thể tải ảnh mẫu mặc định!");
            return;
        }
        formData.append("image_model", defaultModelBlob);
    } else {
        formData.append("image_model", modelImage);
    }

    formData.append("image_product", productImage);
    formData.append("ratio", ratio.key);
    formData.append("mode", 'my_ai_image');

    try {
        const response = await axios.post(route('ai-business.combine-images'), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        if (response.data.success) {
            return response.data.data.aiGeneratedMedia;
        } else {
            toast.error(response.data.message);
        }
    } catch (error) {
        toast.error("Lỗi khi kết hợp ảnh!");
    }
};

const listenWebhookGPUMyAIImage =  (index) => {
    window.Echo.channel('message-to-user.'+ auth.user.id)
        .listen('MessageAIGeneratedMediaWebhookGPUMyAIImage', (promptResponse) => {
            const key = callGenerateImageResponse.value.findIndex(item => item.alias_name === promptResponse.data.generate_file.alias_name);
            imageResponse.value = [...imageResponse.value, promptResponse.data];
            credits.value = promptResponse.data.credits;
            imageRef.value[key] = promptResponse.data.generate_file;
        });
};

const emit = defineEmits(["saveGenerationResult"]);
const callGenerateImage = async (index) => {
    try {
        const hasEnoughCredit = await checkCredit();
        if (!hasEnoughCredit) {
            return;
        }

        const formData = new FormData();
        if (avatarFile.value) {
            formData.append("avatar_file", avatarFile.value);
        }
        formData.append("aspect_ratio", imageDimensions.value.key);
        if (avatarPreview.value) {
            formData.append("avatar_url", avatarPreview.value);
        }
        if (isUseMask.value) {
            formData.append("is_use_mask", isUseMask.value);
        }
        formData.append("model_id", selectedModelId.value);
        formData.append("strength_prompt", strengthPrompt.value);
        formData.append("denoise", denoise.value);
        if (imageDescription.value.trim() != '') {
            formData.append("image_description", imageDescription.value.trim());
        }

        const response = await axios.post(route("ai-image.generate-my-ai-image"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        emit('saveGenerationResult', response)
        return response;
    } catch (error) {
        if (error.response && error.response.status === 403) {
            showCreditModal();
            throw error;
        }

        if (error?.response?.status === 400) {
            errorMessage.value = error.response.data.message;
            isShowErrorMessage.value = true;
        }
    }
};

let showCreditPopup = ref(false);
const handleBuyCredit = () => {
    window.location.href = "/pricing";
};

const handleCancel = () => {
    showCreditPopup.value = false;
};

const showCreditModal = () => {
    showCreditPopup.value = true;
};

const confirmDelete = async () => {
    isLoading.value = true;
    showConfirmModal.value = false;
    try {
        const response = await axios.post(route("ai-image.delete", { id: imageDeleteId.value }));
        if (response.status === 200) {
            const index = imageRef.value.findIndex((image) => image?.id === imageDeleteId.value);
            if (index !== -1) {
                imageRef.value[index] = null;
            }
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi xóa:", error.message);
    } finally {
        isLoading.value = false;
    }
};

const cancelDelete = () => {
    showConfirmModal.value = false;
};

const downloadImage = (url) => {
    if (!url) {
        alert("Chưa có hình ảnh");
        return;
    }
    var url = route("ai-video.downloadFile", { url: url, name: "image.png" });
    window.open(url, "_blank");
};

const createPost = (url) => {
    if (url) {
        let image = {
            s3_url: url,
            type: "image",
        };
        localStorage.setItem("aiImage", JSON.stringify(image));
        window.location.href = route("calendar");
    } else {
        alert("Vui lòng tạo một hình ảnh trước khi tạo bài đăng.");
    }
};

const handleCheckSieze = () => {
    if (window.innerWidth >= 1024) {
        isShowDes.value = true;
    } else {
        isShowDes.value = false;
    }
};

const openShareLink = () => {
    isShowShareLinkModal.value = true;
};

const closeShareLink = () => {
    isShowShareLinkModal.value = false;
};

const isShowModalChooseTemplate = ref(false);
const showModalChooseTemplate = () => {
    isShowModalChooseTemplate.value = true;
    selectCategory("tet-nu");
};
const closeModalChooseTemplate = () => {
    isShowModalChooseTemplate.value = false;
};

const shareAIGeneratedMedia = async (id) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: id,
            share_linkable_type: "AIGeneratedMedia",
        });
        shareLink.value = route("share-link.show", { token: response.data.data.link_token });
        openShareLink();
    } catch (error) {
        toast.error("Chia sẻ ảnh thất bại");
    }
};

onMounted(() => {
    handleCheckSieze();
    window.addEventListener("resize", handleCheckSieze);
});

const showOptions = ref(false);
const toggleOptions = () => {
    showOptions.value = !showOptions.value;
};
const templates = ref([]);
const loadMore = ref(null);
const currentCategory = ref(null);
const dropdownRef = ref(null);
const dropdownCollectionRef = ref(null);
const categoryTitle = ref("");
const training = ref(false);
const selectCategory = async (category) => {
    try {
        isLoading.value = true;
        isDropdownOpen.value = false;
        currentCategory.value = category.id;
        categoryTitle.value = category.title;
        const response = await axios.get(route("ai-image.get-list-template-by-category", { id: category.id }));
        templates.value = response.data.templates.data;
        loadMore.value = response.data?.templates?.next_page_url;
    } catch (error) {
        console.error("Đã xảy ra lỗi:", error.message);
    } finally {
        isLoading.value = false;
    }
};

const isLoadingMore = ref(false);
const loadMoreFunc = async () => {
    try {
        if (!loadMore.value) return;
        isLoading.value = true;
        isLoadingMore.value = true;
        if (loadMore.value) {
            const response = await axios.get(loadMore.value, {
                params: {
                    id: currentCategory.value,
                },
            });
            templates.value = [...templates.value, ...response.data.templates.data];
            loadMore.value = response.data?.templates?.next_page_url;
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi:", error.message);
    } finally {
        isLoading.value = false;
        isLoadingMore.value = false;
    }
};
const onScroll = (event) => {
    const container = event.target;
    if (container.scrollTop + container.clientHeight >= container.scrollHeight) {
        loadMoreFunc();
    }
};

const setImageTemplate = async (url) => {
    avatarPreview.value = url;
    isShowModalChooseTemplate.value = false;
    avatarFile.value = null;
    avatarName.value = "";
    await nextTick();
    imageSelectedRef.value?.scrollIntoView({ behavior: "smooth" });
    if(props.my_ai_image_models.length == 0){
        isShowConfirmModal.value = true;
    }
    if(isShowTrainingModel.value){
        isShow.value = true;
    }
};

const handleCheckCredit = async () => {
    isLoading.value = true;
    const hasEnoughCredit = await checkCreditFineTune();
    console.log(hasEnoughCredit);
    if (!hasEnoughCredit) {
        return;
    }

    const response = await axios.get(route("ai-image.has-fine-tune-model"));
    training.value = response.data.exists;
    if (training.value) {
        isShow.value = true;
        return;
    }
    trainingImagePopup.value = true;
    isShowConfirmModal.value = false;
    // window.location.href = route("ai-image.training-my-ai-image");
};

const isDropdownCollectionOpen = ref(false);

const selectCollection = (collection) => {
    collectionActive.value = collection;
    const indexCollection = collectionActive.value.categories.findIndex((item) => item.slug === "noel-nu");
    selectCategory(collectionActive.value.categories[indexCollection === -1 ? 0 : indexCollection]);
    categoryTitle.value = collectionActive.value.categories[indexCollection === -1 ? 0 : indexCollection].title;
    isDropdownCollectionOpen.value = false;
};

const closeDropdown = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isDropdownOpen.value = false;
    }
    if (dropdownCollectionRef.value && !dropdownCollectionRef.value.contains(event.target)) {
        isDropdownCollectionOpen.value = false;
    }
};

const listCollection = ref(props.listCollection || []);
const collectionActive = ref();
const listCategories = ref([]);
const getCollections = async () => {
    try {
        const response = await axios.get(route("ai-image.get-list-collections"));
        console.log(response);

        listCollection.value = response.data.listCollections.data;
        listCollection.value.forEach(element => {
            if (Array.isArray(element.categories)) {
                listCategories.value = listCategories.value.concat(element.categories);
            }
        });
        listCategories.value.sort((a, b) => (a.order ?? Infinity) - (b.order ?? Infinity));
        collectionActive.value = listCollection.value[0];
        const indexCollection = collectionActive.value.categories.findIndex((item) => item.slug === "noel-nu");
        selectCategory(collectionActive.value.categories[indexCollection === -1 ? 0 : indexCollection]);
    } catch (error) {
        console.error("Error fetching collections:", error);
    }
};

watch(
    () => props?.collectionSelected,
    (collection) => {
        if (collection) {
            selectCollection(collection);
        }
    },
    { immediate: true } // Đảm bảo chạy ngay khi component được mounted
);

onMounted(() => {
    const url = window.location.href;
    const urlParams = new URLSearchParams(new URL(url).search);
    avatarPreview.value = urlParams.get("videoRef");

    // getCollections()
    if(props.collectionSelected) {
        const indexCollection = props.collectionSelected.categories.findIndex((item) => item.slug === "noel-nu");
        selectCategory(props.collectionSelected.categories[indexCollection === -1 ? 0 : indexCollection])
        categoryTitle.value = props.collectionSelected.categories[0].title;
        selectCollection(props.collectionSelected)
    } else {
        getCollections()
    }
    document.addEventListener("click", closeDropdown);
    listenWebhookGPUMyAIImage();

});

onBeforeUnmount(() => {
    document.removeEventListener("click", closeDropdown);
});

const handleShowImageGallery = () => {
    if (listCollection.value.length == 0) {
        getCollections();
    }
};

const capitalizeText = (text) => {
    return text.toLowerCase();
}

const sendImage = async (index) => {
    let imageUrl = imageRef.value[index].s3_url;
    if (!imageUrl) {
        toast.error('Không tìm thấy URL hình ảnh');
        return;
    }
    imageRef.value[index].s3_url = null;
    loadingImageState.value[index] = true;
    try {
        const response = await axios.post(route('ai-image.improve-quality-image'), {
            imageUrl,
        });
        imageRef.value[index].s3_url = response.data.s3_url;a
        loadingImageState.value[index] = false;
    } catch (error) {
        console.error('Error:', error);
        loadingImageState.value[index] = false;
    }
};
watch(avatarPreview, async (newPreview) => {
    if (isUseMask.value) {
        return;
    }

    if (!newPreview) {
        console.log("Không có avatar preview để xử lý.");
        return;
    }
    isLoadingChooseImage.value = true
    try {
        const formData = new FormData();
        formData.append("avatar_file", avatarFile.value)
        formData.append("avatar_url", newPreview);

        const response = await axios.post(route("ai-image.get-image-description"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        if (response.data && response.data.content) {
            imageDescription.value = response.data.content;
        } else {
            imageDescription.value = "";
        }
    } catch (error) {
        console.error("Lỗi khi gọi API:", error);
        imageDescription.value = "";
    } finally {
        isLoadingChooseImage.value = false
    }
});

</script>

<style scoped>
.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24AA69;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
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

.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #C6C6C6;
    border-radius: 10px;
    transition: background-color 0.3s ease;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #1E405A;
}
</style>
