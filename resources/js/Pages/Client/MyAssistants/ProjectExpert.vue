<template>
    <!-- Step 1: Nhập thông tin dự án -->
    <div class="bg-white shadow-xl lg:rounded-[35px] md:px-[80px] md:py-[20px] p-3" :class="sections[0].open ? 'lg:rounded-[35px]' : ''">
        <div :class="sections[0].open ? 'flex-col rounded-[35px] items-start' : 'flex-row rounded-full items-center'" class="flex justify-between text-black">
            <div :class="sections[0].open ? 'max-w-full' : 'max-w-[300px]'" class="md:max-w-full line-clamp-1 flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
                <Step class="flex-shrink-0" :step="1" stepName="Thông tin sản phẩm" />
            </div>
            <div :class="sections[0].open && !isLoadingCheckConversation ? 'block w-full' : 'absolute opacity-0 invisible'">
                <form @submit.prevent="handleFillBasicInformation">
                    <div>
                        <p class="text-xs md:text-[15px] font-bold">1. Nhập tên sản phẩm <span class="text-red-500">*</span></p>
                        <input v-model="formProject.title" type="text" placeholder="Nhập tên sản phẩm...." class="text-xs md:text-sm w-full rounded-lg lg:rounded-2xl border-[#D4D4D4] mt-1.5 lg:py-[22px] lg:px-7" />
                        <div v-show="mode != null">
                            <p class="text-xs md:text-[15px] font-bold mt-4">2. Mô tả ngắn ngọn về sản phẩm <span class="text-red-500">*</span></p>
                            <textarea v-model="formProject.description" placeholder="Mô tả các tính năng và lợi ích của sản phẩm..." rows="4" class="resize-none mt-1.5 text-xs md:text-sm w-full rounded-lg lg:rounded-2xl border-[#D4D4D4] lg:py-[22px] lg:px-7"></textarea>
                        </div>
                    </div>
                    <div class="pb-4 border-b-[3px] border-[#d6d6d6]">
                        <!-- Tạm ẩn -->

                        <div class="mt-4">
                            <p class="text-xs md:text-[15px] font-bold mt-4">3. Link website/sản phẩm</p>
                            <input id="product-link" v-model="formProject.productLink" type="text" placeholder="Link..." class="text-xs md:text-sm w-full rounded-lg lg:rounded-2xl text-black border-[#D4D4D4] mt-1.5 lg:py-[8px] lg:px-[24px]" />
                        </div>
                        <p class="text-xs md:text-[15px] font-bold mt-4">4. Tải tài liệu sản phẩm</p>
                        <div class="lg:grid grid-cols-2 gap-10 mt-6">
                            <div class="flex flex-col items-center">
                                <label for="image-product" class="flex-shrink-0 cursor-pointer text-xs md:text-[15px] font-bold w-[200px] h-[36px] flex items-center justify-center gap-2 border-2 border-ai3goc-primary text-ai3goc-primary rounded-xl mb-3">
                                    <div class="bg-ai3goc-primary p-1 rounded-full">
                                        <Upload size="16" color="#ffffff" />
                                    </div>
                                    <span>Tải ảnh sản phẩm</span>
                                    <input type="file" class="hidden" id="image-product" @change="uploadImage" />
                                </label>
                                <span class="text-sm text-gray-400 self-start inline-block mb-1 italic">Tải lên 01 hình ảnh đẹp nhất của sản phẩm.</span>
                                <div v-if="tempFormProject?.image_product?.url" class="flex items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden">
                                    <img v-if="tempFormProject.image_product.url" :src="tempFormProject.image_product.url" alt="" class="object-cover size-full" />
                                    <img v-else src="\assets\img\aiwow\image_placeholder.png" alt="" class="size-32 object-contain" />
                                </div>
                            </div>
                            <div class="flex flex-col items-center mt-6 lg:m-0">
                                <label for="image-model" class="flex-shrink-0 cursor-pointer text-xs md:text-[15px] font-bold w-[200px] h-[36px] flex items-center justify-center gap-2 border-2 border-ai3goc-primary text-ai3goc-primary rounded-xl mb-3">
                                    <div class="bg-ai3goc-primary p-1 rounded-full">
                                        <Upload size="16" color="#ffffff" />
                                    </div>
                                    <span>Tải lên ảnh người mẫu</span>
                                    <input type="file" class="hidden" id="image-model" @change="uploadModelImage" />
                                </label>
                                <span class="text-sm text-gray-400 self-start inline-block mb-1 italic">Tải lên 01 hình ảnh người mẫu sử dụng sản phẩm.</span>
                                <div v-if="tempFormProject?.model_product?.url" class="flex items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden">
                                    <img v-if="tempFormProject.model_product.url" :src="tempFormProject.model_product.url" alt="" class="object-cover size-full" />
                                    <img v-else src="\assets\img\aiwow\image_placeholder.png" alt="" class="size-32 object-contain" />
                                </div>
                            </div>
                            <div class="flex flex-col items-center mt-6 lg:m-0">
                                <label :class="documentLoading ? 'cursor-not-allowed !text-gray-400' : 'cursor-pointer'" for="file-product" class="flex-shrink-0 cursor-pointer text-xs md:text-[15px] font-bold w-[200px] h-[36px] flex items-center justify-center gap-2 border-2 border-ai3goc-primary text-ai3goc-primary rounded-xl mb-3">
                                    <div class="bg-ai3goc-primary p-1 rounded-full">
                                        <Upload size="16" color="#ffffff" />
                                    </div>
                                    <span>
                                        {{ documentLoading ? "Đang tải tài liệu..." : "Tải tài liệu sản phẩm" }}
                                    </span>
                                    <input type="file" class="hidden" id="file-product" @change="handleUploadDocument" accept=".docx, .pdf, .xls, .xlsx, .ppt, .pptx" />
                                </label>
                                <span class="text-sm text-white self-start inline-block mb-1">.</span>
                                <div v-if="tempFormProject?.files?.length > 0" class="flex flex-col items-center border-gray-300 border-2 rounded-[20px] aspect-[4/3] h-fit w-full p-4 overflow-hidden">
                                    <div class="flex flex-col gap-3 h-5/6 w-full overflow-y-scroll">
                                        <div class="flex items-center w-full gap-2" v-for="(file, index) in tempFormProject.files" :key="index">
                                            <div class="size-6 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all" :class="file.active ? 'border-orion-orange' : 'border-gray-400'" @click="toggleActiveFile(index)">
                                                <div class="size-[15px] bg-orion-orange rounded-full" v-if="file.active"></div>
                                            </div>
                                            <div class="flex items-center gap-2 rounded-full text-black flex-1 px-[10px] py-[6px] transition-all" :class="file.active ? 'bg-orion-orange-hover' : 'bg-gray-100'">
                                                <FileText class="size-5 cursor-pointer" />
                                                <span class="font-semibold line-clamp-1">{{ file.name }}</span>
                                                <Trash2 @click="handleRemoveDocument(index)" class="stroke-red-500 ml-auto size-5 cursor-pointer flex-shrink-0" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white flex items-center justify-center gap-[20px] mt-[20px]">
                                        <button type="button" class="text-xs md:text-[15px] font-bold w-fit px-3 h-[36px] flex items-center justify-center gap-2 border-2 border-ai3goc-primary text-ai3goc-primary rounded-lg lg:rounded-2xl mb-3" @click="selectALlDocument">
                                            <span>Chọn tất cả</span>
                                        </button>
                                        <button type="button" class="text-xs md:text-[15px] font-bold w-fit px-3 h-[36px] flex items-center justify-center gap-2 border-2 border-gray-400 text-gray-400 rounded-lg lg:rounded-2xl mb-3" @click="unSelectALlDocument">
                                            <span>Bỏ chọn</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center gap-4 mt-4 mb-4">
                        <button type="submit" class="h-[40px] md:h-[50px] min-w-[80%] lg:min-w-[360px] text-sm lg:text-base px-4 bg-ai3goc-sec text-white rounded-lg lg:rounded-2xl font-bold" :disabled="loading">
                            <div role="status" v-if="loading">
                                <svg aria-hidden="true" class="inline w-6 h-6 text-gray-200 animate-spin dark:text-gray-600 fill-gray-600 dark:fill-gray-300" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                                </svg>
                            </div>
                            <span v-else> Xác nhận </span>
                        </button>
                    </div>
                </form>
            </div>

            <div @click="toggleSection(0)" :class="sections[0].open ? 'self-end' : ''" class="flex-shrink-0 text-ai3goc-primary font-medium flex items-center cursor-pointer text-xs md:text-sm">
                <span class="hidden md:block">{{ sections[0].open ? "Thu gọn" : "Hiển thị" }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': sections[0].open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
        <LoadingCircle v-if="isLoadingCheckConversation" />
        <div v-if="!sections[0].open && selectedProject && !isLoadingCheckConversation" class="bg-orion-orange-hover rounded-full p-2 mx-auto mb-2">
            <div class="flex items-center">
                <img src="/assets/img/my_assistant/p_red.png" alt="Project Icon" class="lg:w-12 w-8 lg:h-12 h-8 rounded-full mr-4" />
                <div>
                    <h2 class="lg:text-2xl text-base font-semibold text-black line-clamp-1">{{ selectedProject?.title }}</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Step 2: Hiệu chỉnh thông tin -->
    <div ref="custom" :class="sections[1].open ? 'flex-col lg:rounded-[35px] items-start p-3' : 'flex-row lg:rounded-full items-center p-3'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] flex justify-between mt-4">
        <div class="flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
            <Step class="flex-shrink-0" :step="2" step-name="Thiết lập thông tin và hiệu chỉnh" />
        </div>
        <div v-if="sections[1].open" class="w-full lg:w-4/5 self-center">
            <div class="py-10">
                <div class="flex flex-col relative">
                    <!-- Container cho editor với id cố định -->
                    <Loading v-if="loading" position="absolute" msg="Đang phân tích"/>
                    <div id="editor-container" class="w-full border border-gray-200 rounded-lg mb-4"></div>
                    <div class="flex items-center justify-center gap-5 mt-7">
                        <button :disabled="loading" :class="loading ? 'bg-gray-300 cursor-not-allowed' : 'bg-ai3goc-sec'" type="button" @click="goToStepSelectCategory($event)" class="h-[40px] md:h-[50px] place-items-center w-full max-w-[180px] px-4 bg-ai3goc-sec text-white rounded-2xl font-bold">
                            <LoadingCircle v-if="loading" />
                            <p v-else>Xác nhận</p>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div @click="toggleSection(1)" :class="sections[1].open ? 'self-end' : ''" class="flex-shrink-0 text-ai3goc-primary font-medium flex items-center cursor-pointer text-xs md:text-sm">
            <span class="hidden md:block">{{ sections[1].open ? "Thu gọn" : "Hiển thị" }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': sections[1].open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
    <!-- Step 3: Phân tích doanh nghiệp -->
    <div ref="analysisBusiness" :class="sections[2].open ? 'flex-col lg:rounded-[35px] items-start p-3' : 'flex-row lg:rounded-full items-center p-3'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] flex justify-between mt-4">
        <div class="flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
            <Step class="flex-shrink-0" :step="3" step-name="Phân tích Doanh nghiệp" />
        </div>
        <div v-if="sections[2].open" class="w-full lg:w-4/5 self-center">
            <AnalysisBusiness :form-data="messagesMap['analysisBusiness']?.formData" :handle-button-click="handleSubmitAnalysicBusiness" :loading-submit="messagesMap['analysisBusiness']?.isLoading" />
        </div>
        <div @click="toggleSection(2)" :class="sections[2].open ? 'self-end' : ''" class="flex-shrink-0 text-ai3goc-primary font-medium flex items-center cursor-pointer text-xs md:text-sm">
            <span class="hidden md:block">{{ sections[2].open ? "Thu gọn" : "Hiển thị" }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': sections[2].open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
    <!-- Step 4: Chiến lược nội dung ngắn gọn -->
    <div ref="contentStrategy" :class="sections[3].open ? 'flex-col lg:rounded-[35px] items-start p-3' : 'flex-row lg:rounded-full items-center p-3'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] flex justify-between mt-4">
        <div class="flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
            <Step class="flex-shrink-0" :step="4" step-name="Chiến lược Nội dung ngắn gọn" />
        </div>
        <div v-if="sections[3].open" class="w-full lg:w-4/5 self-center">
            <LoadingCircle v-if="isLoadingContentStrategy" />
            <div class="mt-6 pb-8" v-if="messagesMap['contentStrategy'] && !isLoadingContentStrategy">
                <div class="max-h-[426px] border border-[#D4D4D4] overflow-y-scroll py-6 flex flex-col gap-4 rounded-[15px]">
                    <template v-for="(item, index) in messagesMap['contentStrategy']" :key="index">
                        <div class="flex items-center gap-5 lg:px-[80px]">
                            <div @click="handleToggleContentStrategy(index)" :class="item.isActive ? 'border-orion-orange' : 'border-gray-400'" class="flex-shrink-0 size-6 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all">
                                <div class="size-[15px] bg-orion-orange rounded-full" v-if="item.isActive"></div>
                            </div>
                            <div class="relative w-full">
                                <div @click="handleToggleContentStrategy(index)" :class="item.isActive ? 'border-orion-orange bg-orion-orange-hover' : 'border-gray-400'" class="cursor-pointer relative flex items-center justify-between border border-gray-300 rounded-2xl text-black px-4 py-2 flex-1 gap-1 h-[40px] transition-all">
                                    <span class="select-none text-sm md:text-[base] font-semibold">{{ item.name }}</span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="flex flex-col items-center justify-center gap-5 mt-5">
                    <button class="relative flex items-center justify-center h-[40px] md:h-[50px] place-items-center w-full max-w-[180px] px-4 bg-ai3goc-sec text-white rounded-2xl font-bold" @click="handleButtonSelectContentStrategy">
                        <span v-if="!isLoadingContentStrategy"> Xác nhận </span>
                        <LoadingCircle v-else class="!size-6" />
                    </button>
                </div>
            </div>
        </div>
        <div @click="toggleSection(3)" :class="sections[3].open ? 'self-end' : ''" class="flex-shrink-0 text-ai3goc-primary font-medium flex items-center cursor-pointer text-xs md:text-sm">
            <span class="hidden md:block">{{ sections[3].open ? "Thu gọn" : "Hiển thị" }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': sections[3].open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
    <!-- Step 5: Chọn thể loại nội dung -->
    <div ref="category" :class="sections[4].open ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex justify-between mt-4">
        <div class="flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black lg:w-4/5">
            <Step class="flex-shrink-0" :step="5" step-name="Chọn thể loại nội dung" />
        </div>
        <div v-if="sections[4].open" class="w-full lg:w-4/5 self-center">
            <!-- Options -->
            <LoadingCircle v-if="isLoadingAnalysis" />
            <div class="flex flex-col gap-4 mt-6" v-if="messagesMap['analysis'] && !isLoadingAnalysis">
                <template v-for="(item, index) in messagesMap['analysis']" :key="index">
                    <div class="flex items-center gap-5 lg:px-[90px]">
                        <div @click="handleToggleAnalysis(index)" :class="item.isActive ? 'border-orion-orange' : 'border-gray-400'" class="flex-shrink-0 size-6 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all">
                            <div class="size-[15px] bg-orion-orange rounded-full" v-if="item.isActive"></div>
                        </div>
                        <div class="relative w-full">
                            <div @click="handleToggleAnalysis(index)" :class="item.isActive ? 'border-orion-orange bg-orion-orange-hover' : 'border-gray-400'" class="cursor-pointer relative flex items-center justify-between border border-gray-300 rounded-2xl text-black px-4 py-2 flex-1 gap-1 h-[40px] transition-all">
                                <span class="select-none text-sm md:text-[base] font-semibold">{{ item.name }}</span>
                            </div>
                        </div>
                    </div>
                </template>
                <div class="flex justify-center gap-1 items-center mt-5">
                    <div class="flex flex-col items-center justify-center">
                        <button class="relative flex items-center justify-center h-[40px] md:h-[50px] place-items-center w-full max-w-[180px] px-4 bg-ai3goc-sec text-white rounded-2xl font-bold" v-if="['Bài viết Quảng cáo sản phẩm', 'Thơ Quảng cáo sản phẩm'].includes(analysisActive?.name)" @click="handleButtonSelectCreateContentAdvance">
                            <span v-if="!isLoadingSelectCreateContent" class="text-sm md:text-base"> Tiếp tục </span>
                            <LoadingCircle v-else class="!size-6" />
                        </button>
                        <button v-else :disabled="isLoadingSelectCreateContent" type="button" @click="handleButtonSelectCreateContent" class="relative flex gap-1 items-center justify-center h-[40px] md:h-[50px] place-items-center w-full max-w-[180px] px-4 bg-ai3goc-sec text-white rounded-2xl font-bold">
                            <span v-if="!isLoadingSelectCreateContent" class="text-sm md:text-base"> Tiếp tục </span>
                            <LoadingCircle v-else class="!size-6" />
                            <div v-if="isLoadingSelectCreateContent" class="text-xs md:text-sm font-medium text-center p-0.5 leading-none rounded-full transition-all duration-300">{{ loadingPercent }} %</div>
                        </button>
                    </div>
                    <!-- <div class="flex justify-center items-center" v-if="!['Phân tích thị trường', 'Nhạc Doanh nghiệp', 'Lời nhạc Quảng cáo sản phẩm'].includes(analysisActive?.name) && mode != 'basic'">
                        <div>hoặc</div>
                        <div>
                            <button class="text-sm md:text-base relative flex items-center justify-center h-[40px] md:h-[50px] place-items-center w-full max-w-[280px] px-4 bg-ai3goc-sec text-white rounded-2xl font-bold" @click="openPostDetail(item?.content ?? '', null, true)">
                                <span> AI AGENT tự động tạo nội dung và tự động đăng bài </span>
                            </button>
                        </div>
                    </div> -->
                </div>
                <ContentProductAd v-if="messagesMap['content_product_ad'] && !['Bài viết Quảng cáo sản phẩm', 'Thơ Quảng cáo sản phẩm'].includes(analysisActive?.name)" :form-data="messagesMap['content_product_ad']?.formData" :handle-button-click="handleSubmitMediaCampaign" :loading-submit="messagesMap['content_product_ad']?.isLoading" :selectedContent="analysisActive?.name" />
                <MusicStep v-if="messagesMap['music']" :data="messagesMap['music']" :handle-button-click="handleSubmitMusic" :toggleBigIdeaMusic="toggleBigIdeaMusic" :loading="messagesMap['music']?.isLoading" />
            </div>
        </div>
        <div @click="toggleSection(4)" :class="sections[4].open ? 'self-end' : ''" class="text-ai3goc-primary mt-2 font-medium flex items-center cursor-pointer text-xs md:text-sm">
            <span class="hidden md:block">{{ sections[4].open ? "Thu gọn" : "Hiển thị" }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': sections[4].open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
    <div v-if="isGenAIStatus == false">
        <div v-if="['Bài viết Quảng cáo sản phẩm', 'Thơ Quảng cáo sản phẩm'].includes(analysisActive?.name)" ref="addMoreInfo" :class="sections[5].open ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex justify-between mt-4">
            <div class="flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black lg:w-4/5">
                <Step class="flex-shrink-0" :step="6" step-name="Bổ sung thông tin nâng cao" />
            </div>
            <div v-if="sections[5].open" class="w-full lg:w-4/5 self-center">
                <ContentProductAd v-if="messagesMap['content_product_ad']" :form-data="messagesMap['content_product_ad']?.formData" :handle-button-click="handleSubmitMediaCampaign" :loading-submit="messagesMap['content_product_ad']?.isLoading" :selectedContent="analysisActive?.name" />
                <PoetryStep v-if="messagesMap['poetry']" :form-data="messagesMap['poetry']?.formData" :handle-button-click="handleSubmitPoetry" :loading-submit="messagesMap['poetry']?.isLoading" :selectedContent="analysisActive?.name" />
            </div>
            <div @click="toggleSection(5)" :class="sections[5].open ? 'self-end' : ''" class="text-ai3goc-primary mt-2 font-medium flex items-center cursor-pointer text-xs md:text-sm">
                <span class="hidden md:block">{{ sections[5].open ? "Thu gọn" : "Hiển thị" }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': sections[5].open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
        <AnalysisStep :mode="mode" v-if="analysisActive?.name === 'Phân tích thị trường'" :open="sections[5].open" :list="messagesMap['resultsAnalysis']" :handleSubmit="handleButtonClick" :loadingSubmit="eventLoading" :toggleItem="toggleItemResultAnalysis" :toggleSection="() => toggleSection(5)" :refScroll="resultsAnalysis" />
        <div :class="analysisActive?.name === 'Phân tích thị trường' && 'hidden'">
            <!-- Section 3 -->
            <LoadingCircle v-if="resultTargetLoading" loading-title="Hệ thống đang xử lý. Xin vui lòng đợi." />
            <!-- Result 1 -->
            <div ref="resultTarget" class="">
                <div :class="true ? 'flex-col lg:rounded-[35px]' : 'flex-row lg:rounded-full'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex items-center justify-between mt-4">
                    <div class="flex items-center self-start justify-between w-full gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
                        <Step class="flex-shrink-0" :step="['Bài viết Quảng cáo sản phẩm', 'Thơ Quảng cáo sản phẩm'].includes(analysisActive?.name) ? 7 : 6" step-name="Xem kết quả và hiệu chỉnh" />
                    </div>
                    <div v-for="(item, index) in messagesMap['resultTarget']" :key="index" class="w-full lg:w-4/5 mt-10 md:mt-5 text-black text-xs lg:text-[15px] relative">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-4">
                            <div class="relative flex flex-row lg:items-center gap-2 w-full">
                                <template v-for="(option, indexOptionRewrite) in item.options_rewrite" :key="indexOptionRewrite">
                                    <div class="w-full" v-if="option.name !== 'Định dạng' && option.name !== 'keyword'">
                                        <SelectRadix :disabled="item.isLoadingRewrite" :options="option.options" :selected="option.value" :placeholder="option.value || option.name" :handle-change="(value) => selectOption({ value: value, indexOptionRewrite: indexOptionRewrite, index: index })" />
                                    </div>
                                    <div v-if="option.name === 'keyword'" class="absolute -top-10 md:-top-[56px] right-24 md:right-8 flex items-center gap-2">
                                        <DropdowRadix :disabled="item.isLoadingRewrite" :open="option.isOpenKeyword" @update:open="option.isOpenKeyword = $event">
                                            <template #trigger>
                                                <button type="button" class="flex items-center justify-between gap-2 w-full px-4 py-2 bg-white border border-gray-200 rounded-full shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ai3goc-primary relative">
                                                    <img src="/assets/svgs/edit-icon.svg" class="size-4" alt="" />
                                                    <span class="text-xs lg:text-sm text-ai3goc-primary font-bold line-clamp-1">Từ khóa</span>
                                                </button>
                                            </template>
                                            <template #content>
                                                <div class="flex flex-col lg:mt-0 w-[270px] bg-white border border-gray-200 rounded-lg shadow-xl z-50 p-2 px-3">
                                                    <textarea placeholder="Nhập từ khóa" v-model="option.value" class="border-none rounded-[10px] bg-gray-200 h-[66px] w-[243px]"></textarea>
                                                    <div class="flex items-center justify-between h-[30px] mt-4">
                                                        <button
                                                            type="button"
                                                            class="border border-gray-300 rounded-lg h-full px-3"
                                                            @click="
                                                                option.isOpenKeyword = !option.isOpenKeyword;
                                                                option.value = '';
                                                            "
                                                        >
                                                            Huỷ
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="bg-ai3goc-sec rounded-lg text-white h-full px-3"
                                                            @click="
                                                                option.isOpenKeyword = !option.isOpenKeyword;
                                                                rewriteContentProductAd(index);
                                                            "
                                                        >
                                                            Xác nhận
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </DropdowRadix>
                                    </div>
                                    <div v-if="option.name === 'Định dạng'" class="absolute -top-10 md:-top-[56px] right-0 lg:-right-20 flex items-center gap-2">
                                        <DropdowRadix :disabled="item.isLoadingRewrite" :open="option.isOpen" @update:open="option.isOpen = $event">
                                            <template #trigger>
                                                <button :disabled="item.isLoadingRewrite" type="button" class="flex w-full items-center justify-center px-4 py-2 bg-ai3goc-primary rounded-2xl shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ai3goc-primary relative" :class="{ 'bg-gray-300': item.isLoadingRewrite }">
                                                    <span class="text-xs lg:text-sm text-white font-bold line-clamp-1">Định dạng</span>
                                                </button>
                                            </template>
                                            <template #content>
                                                <ul class="flex flex-col lg:mt-0 min-w-[200px] w-full bg-white border border-gray-200 rounded-lg shadow-xl z-50 p-2 px-3">
                                                    <li v-for="(optionItem, idx) in option.options" :key="idx" @click="selectOption({ value: optionItem, indexOptionRewrite: indexOptionRewrite, index: index })" class="px-4 py-2 cursor-pointer hover:bg-blue-50 hover:text-ai3goc-primary rounded-full" :class="{ 'bg-blue-100 text-ai3goc-primary': option.value === optionItem }">
                                                        {{ optionItem }}
                                                    </li>
                                                </ul>
                                            </template>
                                        </DropdowRadix>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex flex-col gap-4">
                                <div class="flex justify-between items-center">
                                    <p class="text-ai3goc-sec text-[15px] font-bold">Nội dung đã tạo:</p>
                                    <button @click="openGradeModal(item)" v-if="mode != 'basic'" class="bg-ai3goc-primary text-white py-2.5 text-xs lg:text-sm rounded-lg lg:rounded-2xl px-6 hover:bg-cyan-600 flex items-center gap-2">
                                        <img src="/assets/svgs/grade.svg" class="size-5" alt="" />
                                        Chấm điểm
                                    </button>
                                </div>
                                <textarea v-if="!item.isLoadingRewrite" rows="10" v-model="item.content" class="text-black whitespace-pre-line text-base border border-gray-400 rounded-xl p-4 max-h-96 overflow-y-auto"> </textarea>
                                <div v-else class="w-full">
                                    <LoadingCircle loading-title="Nội dung đang tạo..." />
                                </div>
                            </div>
                        </div>
                        <div v-if="!hiddenSocialMedia" class="flex flex-col lg:flex-row gap-4 mt-4">
                            <div v-if="!hiddenImage" class="w-full">
                                <!-- Upload Buttons -->
                                <div class="flex justify-center gap-2 md:gap-4 mb-4 h-[40px]">
                                    <button @click="autoCreateImage"
                                        :disabled="showImageGenerate == true"
                                        class="w-[35%] sm:w-1/3 bg-ai3goc-primary text-white py-2.5 text-xs font-bold lg:text-sm rounded-2xl lg:rounded-2xl text-center hover:bg-cyan-600 flex items-center gap-2 justify-center">
                                        Tự động tạo ảnh
                                    </button>
                                    <label :id="`image_${index}`" class="w-[30%] sm:w-1/3 cursor-pointer bg-ai3goc-primary text-white py-2 text-xs font-bold lg:text-sm rounded-2xl lg:rounded-2xl text-center hover:bg-cyan-600 flex items-center gap-2 justify-center">
                                        <!-- <div class="rounded-full">
                                                    <img src="/assets/svgs/upload-icon-w.svg" alt="" class="size-5">
                                                </div> -->
                                        <Upload size="16" color="#ffffff" class="hidden md:show" />
                                        <img src="/assets/svgs/aiwow/uploadMedia.svg" alt="" />
                                        Tải ảnh
                                        <input :id="`image_${index}`" type="file" accept="image/*" @change="handleImageUpload($event, index)" class="hidden" />
                                    </label>
                                    <div class="relative w-[30%] sm:w-1/3 text-center">
                                        <!-- <button @click="handleCreateImage(messagesMap['resultTarget'][index]?.content)" -->
                                        <button @click="isGenerateImage = !isGenerateImage" class="w-full h-full bg-ai3goc-primary text-white font-bold py-2.5 text-xs lg:text-sm rounded-2xl lg:rounded-2xl text-center hover:bg-cyan-600 flex items-center gap-2 justify-center">
                                            <img src="/assets/img/my_assistant/generate_image.png" class="hidden md:show size-5" alt="" />
                                            Tạo ảnh mới
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
                                </div>
                                <div class="grid grid-cols-2 items-center justify-center w-full gap-2">
                                    <div class="flex items-center h-full w-full rounded-xl overflow-hidden min-h-[120px]" v-for="(image, index_image) in item.images" :key="index_image">
                                        <div v-if="image?.imageRef || image?.previewImageUpload" class="relative m-auto">
                                            <input type="checkbox" :checked="checkImage(image)" @change="selectImage(image)" class="ml-0 top-2 right-2 absolute rounded-full" />
                                            <img v-if="image.imageRef" :src="image.imageRef.s3_url" alt="image generated by AI" class="rounded-lg lg:rounded-2xl bg-gray-200 object-contain aspect-[4/3] w-full" />
                                            <img v-else :src="image.previewImageUpload" alt="image generated by AI" class="rounded-lg lg:rounded-2xl bg-gray-200 object-contain aspect-[4/3] w-full" />
                                            <button type="button" @click="removeImage(index, index_image)" class="absolute bottom-2 right-2 bg-red-500 text-white w-6 h-6 rounded-full">x</button>
                                        </div>
                                        <div v-else class="bg-[#E6E6E6] flex h-full items-center justify-center w-full rounded-xl">
                                            <img src="/assets/svgs/aiwow/image.svg" alt="loading" class="mx-auto w-16" />
                                        </div>
                                    </div>
                                    <div v-for="i in getFakeNumberImage(item)">
                                        <label>
                                            <div v-if="autoCreaeImageLoading" class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden">
                                                <input type="radio" disabled class="bg-gray-200 ml-0 top-2 right-2 absolute rounded-full" />
                                                <div class="flex flex-col items-center">
                                                    <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                                    <h3 class="text-white text-center">Hệ thống đang xử lý, xin đợi một chút</h3>
                                                </div>
                                            </div>
                                            <div v-else-if="isLoadingSearchImage" class="bg-[#E6E6E6] flex items-center justify-center rounded-xl w-full h-full overflow-hidden">
                                                <div class="flex flex-col items-center w-full h-full">
                                                    <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                                    <h3 class="text-white">Hệ thống đang xử lý, xin đợi một chút</h3>
                                                </div>
                                            </div>
                                            <div v-else class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden">
                                                <input type="radio" disabled class="bg-gray-200 ml-0 top-2 right-2 absolute rounded-full" />
                                                <img src="/assets/svgs/aiwow/image.svg" class="mx-auto w-24" />
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div v-if="!hiddenVideo" class="lg:w-1/2">
                                <!-- Video Section -->
                                <div class="flex flex-col">
                                    <div class="flex justify-center gap-4 mb-4 h-[40px]">
                                        <label :id="`video_${index}`" class="bg-ai3goc-sec text-white px-6 py-2 text-xs lg:text-sm rounded-lg lg:rounded-2xl flex items-center gap-2">
                                            <div class="p-1 rounded-full">
                                                <Upload size="16" color="#ffffff" />
                                            </div>
                                            Tải video
                                            <input type="file" accept="video/mp4,video/x-m4v,video/webm,video/ogg,video/*,.flv,.3gp" class="inputMedia hidden" @change="handleFileChange($event, index)" />
                                        </label>
                                        <button @click="scrollToCreateFormRef(index)" class="bg-ai3goc-sec text-white px-4 py-2 text-xs lg:text-sm rounded-2xl flex items-center gap-2">
                                            <img src="/assets/img/my_assistant/generate_video.png" alt="" />
                                            Tạo video
                                        </button>
                                    </div>
                                    <div class="flex items-center justify-center h-[256px] w-full">
                                        <label class="text-xs lg:text-sm flex gap-1 items-center mb-1 w-full">
                                            <div class="flex gap-1 w-full items-center cursor-pointer">
                                                <input type="radio" v-model="uploadType" value="video" :checked="uploadType == 'video'" @change="uploadType = 'video'" class="ml-0 rounded-full" />
                                                <div class="w-full">
                                                    <div v-if="item.videoRef || item.previewVideoUpload" class="bg-[#E6E6E6] flex h-[256px] items-center justify-center rounded-xl w-full object-cover">
                                                        <video controls v-if="item.videoRef" :src="item.videoRef.s3_url" alt="image generated by AI" class="w-auto max-h-400 md:max-h-[500px] object-cover max-h-video" />
                                                        <video controls v-else :src="item.previewVideoUpload" alt="image generated by AI" class="w-auto max-h-400 md:max-h-[500px] object-cover max-h-video" />
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
                        <div class="mt-4" v-if="mode != 'basic' && selectedAnalysis != 'Nhạc Doanh nghiệp'">
                            <!-- Video Section -->
                            <div class="flex flex-col">
                                <div class="flex justify-center gap-2 md:gap-4 mb-4 h-[40px]">
                                    <button @click="autoCreateVideo"
                                        :disabled="showVideoGenerate == true"
                                        class="w-[35%] sm:w-1/3 bg-ai3goc-primary text-white py-2.5 font-bold text-xs lg:text-sm rounded-2xl lg:rounded-2xl text-center hover:bg-cyan-600 flex items-center gap-2 justify-center">
                                            Tự động tạo video
                                    </button>
                                    <label :id="`video_${index}`" class="w-[30%] sm:w-1/3 cursor-pointer bg-orion-sec hover:bg-cyan-600 font-bold text-white text-center py-2 text-xs lg:text-sm rounded-2xl lg:rounded-2xl flex items-center gap-2 justify-center">
                                        <div class="p-1 rounded-full hidden md:show">
                                            <Upload size="16" color="#ffffff" />
                                        </div>
                                        <img src="/assets/svgs/aiwow/uploadMedia.svg" alt="" />
                                        Tải video
                                        <input type="file" accept="video/mp4,video/x-m4v,video/webm,video/ogg,video/*,.flv,.3gp" class="inputMedia hidden" @change="handleFileChange($event, index)" />
                                    </label>
                                    <button @click="scrollToCreateFormRef(index)" class="w-[30%] sm:w-1/3 bg-ai3goc-primary hover:bg-cyan-600 font-bold text-white text-center py-2 text-xs lg:text-sm rounded-2xl flex items-center gap-2 justify-center">
                                        <img src="/assets/img/my_assistant/generate_video.png" alt="" class="hidden md:show" />
                                        Tạo video mới
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center justify-center w-full">
                                <label class="text-xs lg:text-sm flex gap-1 items-center mb-1 w-full">
                                    <div class="flex gap-1 w-full items-center cursor-pointer">
                                        <div class="relative w-full">
                                            <div v-if="item.videoRef || item.previewVideoUpload" class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden">
                                                <label class="ml-0 w-16 h-16 cursor-pointer top-0 right-0 p-2 z-10 text-right absolute">
                                                    <input type="checkbox" :checked="checkVideo(item)" @change="selectVideo(item)" class="rounded-full" />
                                                </label>
                                                <video controls v-if="item.videoRef" :src="item.videoRef.s3_url" alt="image generated by AI" class="w-auto object-cover max-h-video" />
                                                <video controls v-else :src="item.previewVideoUpload" alt="image generated by AI" class="w-auto object-cover max-h-video" />

                                                <button type="button" @click="removeVideo(index)" class="absolute bottom-2 right-2 bg-red-500 text-white w-6 h-6 rounded-full">x</button>
                                            </div>
                                            <div v-else class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden">
                                                <input type="radio" disabled class="bg-gray-200 ml-0 top-2 right-2 absolute rounded-full" />
                                                 <div class="flex flex-col items-center" v-if="isLoadingVideo">
                                                    <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                                    <h3 class="text-white text-center">Hệ thống đang xử lý, xin đợi một chút</h3>
                                                    <span class="color-greeen">{{percenLoadingVideo}}%</span>
                                                </div>
                                                <img v-else src="/assets/img/aiwow/homepage/play_button.png" alt="loading" class="mx-auto w-24" />
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-center gap-3 lg:gap-4 mt-6 mb-4" v-if="messagesMap['music']">
                            <button
                                @click="
                                    () => {
                                        toggleGenerateImage('music', index);
                                        openPostDetail(item?.content ?? '', index);
                                    }
                                "
                                class="min-w-[100px] place-items-center gap-2 md:min-w-[150px] px-4 py-2 lg:py-3 bg-ai3goc-primary text-white rounded-lg lg:rounded-2xl font-bold"
                            >
                                <img src="/assets/img/my_assistant/music.png" class="w-5 xs:block hidden" alt="" />
                                Tạo nhạc
                            </button>
                        </div>
                        <div class="flex justify-center gap-3 lg:gap-4 mt-6 mb-4" v-else>
                            <button @click="openPostDetail(item?.content ?? '', index)" class="min-w-[100px] md:min-w-[150px] px-2 py-2 lg bg-ai3goc-sec rounded-lg lg:rounded-2xl border border-[#C5C5C5] text-white font-bold place-items-center gap-2 text-sm">Xác nhận</button>
                        </div>
                    </div>
                </div>
                <div v-for="(_, index) in messagesMap['resultTarget']" :key="index" class="w-full">
                    <div :id="`target_section_${index}`" ref="myAiRef" v-if="myAi && index === targetActiveIndex" class="mt-4">
                        <MyAIComponent @saveGenerationResult="(value) => handleSaveGenerationResult(value, index)" :listCollection="listCollection" :collectionName="collectionSelected?.title" :collection-selected="collectionSelected" :my_ai_image_models="my_ai_image_models" />
                    </div>

                    <div :id="`target_section_${index}`" ref="textToImageRef" v-if="textToImage && index === targetActiveIndex" class="mt-4">
                        <TextToImage @saveGenerationResult="(value) => handleSaveGenerationResult(value, index)" :listCollection="listCollection" :collectionName="collectionSelected?.title" :collection-selected="collectionSelected" :my_ai_image_models="my_ai_image_models" :image-description="getActiveFinalTarget()?.content" :isMemo="true" />
                    </div>

                    <div :id="`target_section_${index}`" ref="backgroundRef" v-if="background && index === targetActiveIndex" class="mt-4">
                        <AIBackgroundComponent @saveGenerationResult="(value) => handleSaveGenerationResult(value, index)" :listCollection="listCollection" :collectionName="collectionSelected?.title" :collection-selected="collectionSelected" :my_ai_image_models="my_ai_image_models" />
                    </div>

                    <!-- Create video -->
                    <div :id="`target_section_${index}`" v-if="videoDetail && index === targetActiveIndex" ref="createFormRef" class="w-full h-full mt-10 rounded-[35px]">
                        <CreateForm @saveGenerationResult="(value) => handleSaveGenerationResultVideo(value, index)" :topic="getActiveFinalTarget()?.content ?? ''" :businessProjectId="businessProjectId" />
                    </div>
                    <div :id="`target_section_${index}`" v-if="musicAi && index === targetActiveIndex" class="w-full h-full mt-10 rounded-[35px]">
                        <CreateMusic @saveGenerationResult="(song) => handleSaveGenerationResultMusic(song, index)" :content="getActiveFinalTarget()?.content ?? ''" :titleMusic="formProject.title" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div ref="stepPostFormRef" :class="analysisActive?.name === 'Phân tích thị trường' && 'hidden'">
        <div :class="true ? 'flex-col lg:rounded-[35px]' : 'flex-row lg:rounded-full'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex items-center justify-between mt-4">
            <div class="flex items-center self-start justify-between w-full gap-2 mb-2 text-xs lg:text-sm mt-2 text-black" v-if="isGenAIStatus == false">
                <Step class="flex-shrink-0" :step="['Bài viết Quảng cáo sản phẩm', 'Thơ Quảng cáo sản phẩm'].includes(analysisActive?.name) ? 8 : 7" step-name="Đăng bài" />
            </div>
            <div class="w-full" v-if="stepPostFormRef">
                <PostForm ref="postFormRef" :templatePostForm="isGenAIStatus ? 'AutoPostAIForm' : 'BasicForm'" @beforeSubmit="beforeSubmitPostForm" @onSuccess="onSuccessPostForm" />
            </div>
        </div>
    </div>

    <Modal :show="showGradeModal" @close="closeGradeModal">
        <div class="p-4 md:p-12 max-h-[90vh] overflow-y-scroll">
            <div>
                <div class="flex gap-1">
                    <div class="flex justify-start items-center gap-2">
                        <div class="w-[52px] h-[75%] overflow-hidden rounded-[10px] border-[1px] border-[#d4d4d4]">
                            <img class="w-full h-auto object-top" src="/assets/img/orion/big-robot.png" alt="Robot" />
                        </div>

                        <h2 class="text-black font-bold text-xl lg:text-xl 2xl:text-3xl leading-none">Chấm điểm nội dung</h2>
                    </div>
                </div>
                <!-- <div class="flex justify-start gap-4">
                    <img src="/assets/svgs/robot.svg"/>
                    <h2 class="text-lg font-bold text-[#005F49]">Chấm điểm nội dung</h2>
                </div> -->
                <h2 class="text-sm md:text-base font-bold my-2">Nội dung đã tạo:</h2>
                <textarea v-model="gradeContentInput" class="w-full p-2 border-[1px] rounded-lg" rows="10"></textarea>
            </div>
            <!-- <div class="flex justify-center items-center">
                <button @click="gradeContent" class="bg-ai3goc-primary text-white px-4 py-2 rounded-full my-4 flex justify-center items-center gap-2" :disabled="gradeLoading">
                    <img src="/assets/svgs/grade.svg" class="size-5" alt="">Chấm điểm
                </button>
            </div> -->
            <div class="border-t-2 mt-4">
                <div v-if="gradeLoading">
                    <LoadingCircle loading-title="Đang chấm điểm..." />
                </div>
                <div v-else>
                    <h2 class="text-sm md:text-base font-bold mb-2 mt-4">Kết quả chấm điểm nội dung đã tạo:</h2>
                    <div class="p-2 border-[1px] rounded-lg">
                        <p class="whitespace-pre-line">{{ gradeResult }}</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-center items-center">
                <button @click="improveContent" class="bg-ai3goc-primary text-white px-4 py-2 rounded-full my-4 flex justify-center items-center gap-2" :class="gradeLoading || improveLoading ? 'opacity-35' : ''" :disabled="gradeLoading || improveLoading"><img src="/assets/svgs/grade.svg" class="size-5" alt="" />Cải thiện</button>
            </div>
            <div class="border-t-2" v-if="showImproveResult">
                <div v-if="improveLoading">
                    <LoadingCircle loading-title="Đang cải thiện..." />
                </div>
                <div v-else>
                    <h2 class="text-sm md:text-base font-bold mb-2 mt-4">Kết quả cải thiện nội dung đã tạo:</h2>
                    <div class="p-2 border-[1px] rounded-lg">
                        <p class="whitespace-pre-line">{{ improveResult }}</p>
                    </div>
                    <div v-if="improveResult" class="flex justify-center">
                        <button @click="replaceWithImprovedContent" class="bg-green-600 text-white px-4 py-2 rounded-full my-4 flex justify-center items-center gap-2">Thay thế nội dung</button>
                    </div>
                </div>
            </div>
            <div class="flex justify-center items-center">
                <button @click="closeGradeModal" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-full mt-4">Đóng</button>
            </div>
        </div>
    </Modal>
    <PopupConfirmVideo @confirmVideo="confirmVideo" :message="message" @close="showConfirmVideo=false" v-if="showConfirmVideo">
    </PopupConfirmVideo>
    <Loading v-if="isLoading" />
</template>

<script setup>
import ProjectHeader from "@/Components/Project/ProjectHeader.vue";
import Layout from "@/Layouts/Client/AiLayout.vue";
import DropdowRadix from "@/Components/DropdowRadix.vue";
import Loading from "@/Components/Loading.vue";
import LoadingCircle from "@/Components/LoadingCircle.vue";
import Modal from "@/Components/Modal.vue";
import SelectRadix from "@/Components/SelectRadix.vue";
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { FileText, Trash2, Upload } from "lucide-vue-next";
import { marked } from "marked";
import suneditor from "suneditor";
import "suneditor/dist/css/suneditor.min.css";
import { computed, nextTick, onBeforeMount, onBeforeUnmount, onMounted, onUnmounted, onUpdated, reactive, ref, watch } from "vue";
import { toast } from "vue3-toastify";
import AIBackgroundComponent from "../AIBackground/Component.vue";
import TextToImage from "../AIImage/CreateImage.vue";
import MyAIComponent from "../AIImage/MyAIComponent.vue";
import CreateMusic from "../TextToSong/CreateMusic.vue";
import CreateForm from "../TextToVideo/CreateForm.vue";
import AnalysisStep from "./AnalysisStep.vue";
import ContentProductAd from "./ContentProductAd.vue";
import MusicStep from "./MusicStep.vue";
import PoetryStep from "./PoetryStep.vue";
import PostForm from "@/Components/ShareAiText/PostForm.vue";
import { convertToSnakeCase } from "@/lib/utils";
import { inject } from "vue";
const helpers = inject("helpers");
import Step from "@/Components/Step.vue";
import MaintenanceModal from "@/Components/MaintenanceModal.vue";
import ProjectExpert from "./ProjectExpert.vue";
import AnalysisBusiness from "./AnalysisBusiness.vue";
import { eventBus } from "@/lib/eventBus.js";
import PopupConfirmVideo from '@/Components/PopupConfirmCreateVideo.vue';

const props = defineProps({
    project: Object,
})

const pageData = reactive({
    image_select_files: [],
    video_select_files: [],
});
const breadcrumbsData = [{ text: "A.I bán hàng", link: "ai-business.index" }];
const page = usePage();
const stepPostFormRef = ref(null);
const postFormRef = ref(null);

const businessProjectId = ref(0);
const createFormRef = ref(null);
const uploadType = ref("image");
const showAffKeyPopup = ref(false);
const additionalCredit = ref(0);
const createContentWithImage = ref(false);
const autoCreaeImageLoading = ref(false);
const MAX_IMAGE_FILES = 4;
const editor = ref(null);
const { props: pageProps } = usePage();

let urlParams = new URLSearchParams(window.location.search);
const mode = ref(urlParams.get("mode") || "basic");

const sections = reactive([
    // 5 sections
    { open: false },
    { open: false },
    { open: false },
    { open: false },

    // result
    { open: false },

    // scene
    { open: false },
]);

const options = reactive([
    {
        label: "Văn bản",
        subOptions: ["Status Fanpage", "Status profile", "Làm thơ chúc Tết"],
        isDropdownOpen: false,
    },
    { label: "Âm nhạc", subOptions: [], isDropdownOpen: false },
    { label: "Hình ảnh", subOptions: [], isDropdownOpen: false },
    { label: "Video", subOptions: [], isDropdownOpen: false },
    { label: "Chiến dịch", subOptions: [], isDropdownOpen: false },
]);

const listProject = ref([
    {
        image: "/assets/img/my_assistant/p_red.png",
        name: "Fanpage Hello!",
        isActive: true,
    },
    {
        image: "/assets/img/my_assistant/p_green.png",
        name: "Dự án 2",
        isActive: false,
    },
    {
        image: "/assets/img/my_assistant/p_blue.png",
        name: "Dự án 3",
        isActive: false,
    },
]);

const styles = ref(["Hài hước và vui vẻ", "Châm biếm", "Tự sự", "Khích lệ và truyền cảm hứng", "Thông tin", "Hướng dẫn và giảng dạy"]);
const resultsAnalysisSample = [
    {
        name: "Tìm hiểu sản phẩm",
        content: "",
        isActive: false,
        isLoading: false,
    },
    {
        name: "Tìm hiểu Thị trường",
        content: "",
        isActive: false,
        isLoading: false,
    },
    {
        name: "Tìm hiểu Khách hàng",
        content: "",
        isActive: false,
        isLoading: false,
    },
    {
        name: "Tìm hiểu Thương hiệu",
        content: "",
        isActive: false,
        isLoading: false,
    },
    {
        name: "Tìm hiểu Đối thủ cạnh tranh",
        content: "",
        isActive: false,
        isLoading: false,
    },
    {
        name: "Tìm hiểu Tài chính và hiệu quả Kinh doanh",
        content: "",
        isActive: false,
        isLoading: false,
    },
    {
        name: "Tìm hiểu Rủi ro và Pháp lý",
        content: "",
        isActive: false,
        isLoading: false,
    },
    {
        name: "Tìm hiểu Xu hướng và cơ hội Tương lai",
        content: "",
        isActive: false,
        isLoading: false,
    },
];
const sampleAnalysis = [
    {
        name: "Bài viết Quảng cáo sản phẩm",
        isActive: true,
    },
    {
        name: "Thơ Quảng cáo sản phẩm",
        isActive: false,
    },
    {
        name: "Chiến dịch Truyền thông",
        isActive: false,
    },
    {
        name: "Phân tích thị trường",
        isActive: false,
    },
    {
        name: "Lời nhạc Quảng cáo sản phẩm",
        isActive: false,
    },
];
const selectedOption = ref("");
const activePopup = ref(null);
const menuContainer = ref(null);
const activeListProject = ref(false);
const isStructureOpen = ref(false);
// Scroll ref
const category = ref(null);
const target = ref(null);
const resultTarget = ref(null);
const myAiRef = ref(null);
const textToImageRef = ref(null);
const backgroundRef = ref(null);
const resultsAnalysis = ref(null);

// Loading
const categoryLoading = ref(false);
const targetLoading = ref(false);
const informationLoading = ref(false);
const resultTargetLoading = ref(false);
const documentLoading = ref(false);
const isLoadingCheckConversation = ref(false);

const hiddenSocialMedia = ref(false);
const hiddenImage = ref(false);
const hiddenVideo = ref(true);
const imageUploadNumber = ref(0);

//Hiden component
const isGenerateImage = ref(false);
const textToImage = ref(false);
const background = ref(false);
const myAi = ref(false);
const videoDetail = ref(false);
const targetActiveIndex = ref(0);
const musicAi = ref(false);

const toggleGenerateImage = async (type, index) => {
    if (["background", "textToImage"].includes(type) && !validateLimitImage(index)) {
        return;
    }

    targetActiveIndex.value = index;
    handleActiveFinalTarget(index);
    if (type === "myAi") {
        myAi.value = true;
        background.value = false;
        textToImage.value = false;
        isGenerateImage.value = false;
        videoDetail.value = false;
        await nextTick();
    } else if (type === "background") {
        myAi.value = false;
        background.value = true;
        textToImage.value = false;
        isGenerateImage.value = false;
        videoDetail.value = false;
        await nextTick();
    } else if (type === "textToImage") {
        myAi.value = false;
        background.value = false;
        textToImage.value = true;
        isGenerateImage.value = false;
        videoDetail.value = false;
        await nextTick();
    } else if (type === "video") {
        background.value = false;
        textToImage.value = false;
        isGenerateImage.value = false;
        showVideoGenerate.value = true;
        // isLoadingVideo.value = true;
        videoDetail.value = true;
        await nextTick();
    } else if (type === "music") {
        background.value = false;
        textToImage.value = false;
        isGenerateImage.value = false;
        videoDetail.value = false;
        musicAi.value = true;
        await nextTick();
    }
    const targetSection = document.getElementById(`target_section_${index}`);
    targetSection.scrollIntoView({ behavior: "smooth" });
};

const toggleSection = (index) => {
    sections[index].open = !sections[index].open;
    if(index == 2 && sections[index].open) {
        handleShowAnalysisBusiness();
    }
};

const selectOption = ({ index = 0, value, indexOptionRewrite }) => {
    messagesMap["resultTarget"][index].options_rewrite[indexOptionRewrite].value = value;
    messagesMap["resultTarget"][index].options_rewrite[indexOptionRewrite].isOpen = false;
    rewriteContentProductAd(index);
};

// Khai báo refs
const editorElement = ref(null);
const sunEditorInstance = ref(null);

// Thêm hàm khởi tạo editor
const initEditor = () => {
    nextTick(() => {
        // Đảm bảo container tồn tại
        const container = document.getElementById("editor-container");
        if (!container) {
            console.log("Editor container not found");
            return;
        }

        // Tạo textarea nếu chưa có
        let textarea = container.querySelector("textarea");
        if (!textarea) {
            textarea = document.createElement("textarea");
            container.appendChild(textarea);
        }

        // Khởi tạo SunEditor
        if (!sunEditorInstance.value) {
            sunEditorInstance.value = suneditor.create(textarea, {
                height: "auto",
                minHeight: "300px",
                width: "100%",
                buttonList: [],
                defaultStyle: `
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                        font-size: 15px;
                        line-height: 1.6;
                        color: #333;
                        padding: 20px;
                    `,
                hideToolbar: true,
            });
        }

        // Set nội dung nếu có
        if (messagesMap["information_project"]) {
            sunEditorInstance.value.setContents(messagesMap["information_project"]);
        } else {
            sunEditorInstance.value.setContents(pendingEditorContent.value);
        }
    });
};

const updateEditorContent = (content) => {
    if (!sunEditorInstance.value) {
        console.warn("Editor not initialized");
        return;
    }

    try {
        // Format nội dung
        let formattedContent = content
            // Xử lý dấu gạch ngang làm separator
            .replace(/[-]{5,}/g, '<hr class="my-4">')
            // Xử lý dấu * cho in đậm (thay đổi regex để khớp với *)
            .replace(/\*(.*?)\*/g, "<strong>$1</strong>")
            // Xử lý dấu ** cho in nghiêng (đổi thứ tự để tránh conflict)
            .replace(/\*\*(.*?)\*\*/g, "<em>$1</em>")
            // Xử lý xuống dòng
            .replace(/\n/g, "<br>");

        // Đảm bảo content được bọc trong thẻ p
        formattedContent = formattedContent.startsWith("<p>") ? formattedContent : `<p>${formattedContent}</p>`;

        // Cập nhật nội dung editor
        sunEditorInstance.value.setContents(formattedContent);
    } catch (e) {
        console.error("Error updating editor content:", e);
    }
};

onMounted(() => {
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".dropdown")) {
            options.forEach((option) => (option.isDropdownOpen = false));
        }
    });
    initEditor(); // Khởi tạo editor
    if (mode.value) {
        sections[0].open = true;
        sections[1].open = false;
        sections[2].open = false;
        sections[3].open = false;
    }
});

watch(
    () => sections[1].open,
    (newVal) => {
        if (newVal) {
            // Khởi tạo editor khi section được mở
            nextTick(() => {
                if (sunEditorInstance.value) {
                    sunEditorInstance.value.destroy();
                    sunEditorInstance.value = null;
                }
                initEditor();
            });
        }
    }
);

onBeforeUnmount(() => {
    if (sunEditorInstance.value) {
        sunEditorInstance.value.destroy();
        sunEditorInstance.value = null;
    }
    // Xóa textarea element
    const textarea = document.getElementById("editor");
    if (textarea) {
        textarea.remove();
    }
});

const selectedProject = ref(listProject[0] || null);

// Tạm sửa để demo, demo xong tìm nguyên nhân rồi fix sau
onMounted(async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const projectIdFromUrl = urlParams.get("projectId");
    if (projectIdFromUrl) {
        try {
            const response = await axios.get(`/business/get-project-by-id`, {
                params: { projectId: projectIdFromUrl },
            });

            if (response.data) {
                selectProject(response.data.data);
            }
        } catch (error) {
            console.error("Error fetching project:", error);
        }
    }
});

const isSending = ref(false);
const loading = ref(false);

const messagesMap = reactive({});
const conversationId = ref(null);

const analysisActive = computed(() => {
    return messagesMap?.analysis?.find((item) => item.isActive);
});
const checkConversationExit = async (conversationId) => {
    const payload = {
        inputs: {},
        query: {},
        conversation_id: conversationId,
    };
    const response = await axios.post(route("ai-business.sendChat"), payload, {
        headers: {
            "Content-Type": "application/json;charset=UTF-8",
        },
    });

    return response.data;
};
function formatCategories(inputString) {
    // Split the input string by newlines and remove empty lines
    const lines = inputString
        .split("\n")
        .map((line) => line.trim())
        .filter((line) => line);

    // Initialize result array and temporary storage
    const result = [];
    const categoriesMap = new Map();

    lines.forEach((line) => {
        // Check if line contains ": " which indicates it has a parent category
        if (line.includes(": ")) {
            const [category, subItem] = line.split(": ");

            // If this category doesn't exist in our map yet, create it
            if (!categoriesMap.has(category)) {
                categoriesMap.set(category, {
                    name: category,
                    subName: [],
                });
            }

            // Add the sub-item to the category
            categoriesMap.get(category).subName.push({
                name: subItem,
                isActive: false,
            });
        } else {
            // This is a standalone category
            result.push({
                name: line,
            });
        }
    });

    // Add all categories with sub-items to the result array
    categoriesMap.forEach((category) => {
        result.unshift(category); // Add categories with sub-items at the beginning
    });

    return result;
}
const startChat = async (key) => {
    if (!key) return;
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        return;
    }
    if (mode.value == "" || mode.value == null) {
        toast.error("Vui lòng chọn chế độ để sử dụng tính năng.");
        return;
    }
    loading.value = true;
    try {
        const query = {
            currentStep: "buoc1",
            ten_san_pham_dich_vu: selectedProject.value.title,
            gioi_thieu_san_pham_dich_vu: selectedProject.value.description,
            url: formProject.productLink,
        };
        const files = [
            ...tempFormProject.value.files
                .filter((item) => item.active)
                .map((item) => {
                    return {
                        type: "document",
                        transfer_method: "remote_url",
                        url: item.url,
                    };
                }),
        ];
        if (selectedProject.value.image) {
            files.push({
                type: "image",
                transfer_method: "remote_url",
                url: selectedProject.value.image,
            });
        }
        const payload = {
            inputs: {},
            query: JSON.stringify(query),
            files,
            project_id: selectedProject.value.id,
        };

        if (selectedOption.value) {
            payload.inputs.chude = selectedOption.value;
        }

        // Initialize empty message data
        messagesMap[key] = "";

        let response = await fetch(route("ai-business.sendChatStreaming"), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            response = await fetch(route('ai-business.send-ai-business-stream'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let buffer = ""; // Buffer for incomplete chunks
        let messages = "";

        while (true) {
            const { value, done } = await reader.read();
            if (done) break;

            buffer += decoder.decode(value, { stream: true }); // Keep stream option for partial chunks

            const lines = buffer.split("\n");
            buffer = lines.pop() || "";

            for (const line of lines) {
                if (line.trim().startsWith("data: ")) {
                    try {
                        // Remove 'data: ' prefix and parse
                        const jsonStr = line.trim().slice(5);
                        const data = JSON.parse(jsonStr);

                        if (data.event === "message" && data.answer) {
                            messages += data.answer;
                            messagesMap["information_project"] = messages.trim();
                            if (sunEditorInstance.value) {
                                try {
                                    sunEditorInstance.value.setContents(marked(messages) || "");
                                } catch (e) {
                                    console.error("Error setting editor content:", e);
                                }
                            } else {
                                console.warn("Editor not initialized");
                                // Khởi tạo editor nếu chưa có
                                initEditor();
                                // Thử set content lại sau khi khởi tạo
                                nextTick(() => {
                                    if (sunEditorInstance.value) {
                                        sunEditorInstance.value.setContents(marked(messages) || "");
                                    }
                                });
                            }
                        }
                        if (data.event === "workflow_started" && data.conversation_id) {
                            conversationId.value = data.conversation_id;
                        }
                    } catch (e) {
                        console.error("Lỗi parse JSON:", e);
                        continue;
                    }
                }
            }
        }

        // Process any remaining data in buffer
        if (buffer.trim()) {
            try {
                const data = JSON.parse(buffer.trim().slice(5));
                if (data.event === "message" && data.answer) {
                    messagesMap[key] += data.answer;
                }
            } catch (e) {
                // Ignore parsing errors in final buffer
            }
        }

        // Update data after streaming completes
        await updateDataGenerative("information_project", {
            conversation_id: conversationId.value,
            answer: messagesMap[key],
        });
    } catch (error) {
        console.error("Error starting conversation:", error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại.");
    } finally {
        loading.value = false;
        isSending.value = false;
    }
};
const updateDataGenerative = async (key, data) => {
    formProject.data_json = {
        ...formProject.data_json,
        [key]: data,
    };
    await handleProjectSubmit(false);
};

const mockDataAnalysisBusiness = [
    {
        name: "company_name",
        label: "Tên Doanh Nghiệp",
        type: "text",
        placeholder: "Nhập tên doanh nghiệp của bạn",
        value: "",
    },
    {
        name: "industry",
        label: "Lĩnh vực hoạt động",
        type: "select",
        options: [
            { value: "Thời trang", label: "Thời trang" },
            { value: "Công nghệ", label: "Công nghệ" },
            { value: "Giáo dục", label: "Giáo dục" },
            { value: "Sức khỏe & Làm đẹp", label: "Sức khỏe & Làm đẹp" },
            { value: "Dịch vụ tài chính", label: "Dịch vụ tài chính" },
            { value: "Thực phẩm & Đồ uống", label: "Thực phẩm & Đồ uống" },
            { value: "Bất động sản", label: "Bất động sản" },
            { value: "Giải trí", label: "Giải trí" },
            { value: "Nông nghiệp & Lâm nghiệp", label: "Nông nghiệp & Lâm nghiệp" },
            { value: "Y tế & Dược phẩm", label: "Y tế & Dược phẩm" },
            { value: "Giao thông & Logistics", label: "Giao thông & Logistics" },
            { value: "Du lịch & Lữ hành", label: "Du lịch & Lữ hành" },
            { value: "Xây dựng & Kiến trúc", label: "Xây dựng & Kiến trúc" },
            { value: "Truyền thông & Quảng cáo", label: "Truyền thông & Quảng cáo" },
            { value: "Thương mại điện tử", label: "Thương mại điện tử" },
            { value: "Nhân sự & Tuyển dụng", label: "Nhân sự & Tuyển dụng" },
            { value: "Luật & Dịch vụ Tư vấn", label: "Luật & Dịch vụ Tư vấn" },
            { value: "Năng lượng & Môi trường", label: "Năng lượng & Môi trường" },
            { value: "Quốc phòng & An ninh", label: "Quốc phòng & An ninh" },
        ],
    },
    {
        name: "company_size",
        label: "Quy mô doanh nghiệp",
        type: "select",
        options: [
            { value: "Cá nhân kinh doanh", label: "Cá nhân kinh doanh" },
            { value: "Startup (Dưới 20 nhân viên)", label: "Startup (Dưới 20 nhân viên)" },
            { value: "Doanh nghiệp nhỏ (20 - 100 nhân viên)", label: "Doanh nghiệp nhỏ (20 - 100 nhân viên)" },
            { value: "Doanh nghiệp vừa (100 - 500 nhân viên)", label: "Doanh nghiệp vừa (100 - 500 nhân viên)" },
            { value: "Tập đoàn lớn (500+ nhân viên)", label: "Tập đoàn lớn (500+ nhân viên)" },
        ],
    },
    {
        name: "business_model",
        label: "Mô hình doanh nghiệp",
        type: "select",
        options: [
            { value: "B2C (Bán lẻ cho khách hàng cá nhân)", label: "B2C (Bán lẻ cho khách hàng cá nhân)" },
            { value: "B2B (Bán hàng cho doanh nghiệp)", label: "B2B (Bán hàng cho doanh nghiệp)" },
            { value: "D2C (Bán hàng trực tiếp đến khách hàng, không qua trung gian)", label: "D2C (Bán hàng trực tiếp đến khách hàng, không qua trung gian)" },
            { value: "Marketplace (Sàn thương mại điện tử)", label: "Marketplace (Sàn thương mại điện tử)" },
            { value: "Dịch vụ (Cung cấp dịch vụ thay vì sản phẩm)", label: "Dịch vụ (Cung cấp dịch vụ thay vì sản phẩm)" },
            { value: "Khác", label: "Khác" },
        ],
    },
    {
        name: "usp",
        label: "USP - Điểm khác biệt của doanh nghiệp",
        type: "text",
        placeholder: "Điểm độc đáo của doanh nghiệp bạn là gì?",
        value: "",
    },
    {
        name: "price_range",
        label: "Mức giá sản phẩm/dịch vụ",
        type: "select",
        options: [
            { value: "Phân khúc cao cấp", label: "Phân khúc cao cấp" },
            { value: "Trung cấp", label: "Trung cấp" },
            { value: "Bình dân", label: "Bình dân" },
            { value: "Giá linh hoạt (tùy sản phẩm)", label: "Giá linh hoạt (tùy sản phẩm)" },
        ],
    },
    {
        name: "brand_positioning",
        label: "Định vị thương hiệu",
        type: "select",
        options: [
            { value: "Chuyên nghiệp, đẳng cấp", label: "Chuyên nghiệp, đẳng cấp" },
            { value: "Thân thiện, gần gũi", label: "Thân thiện, gần gũi" },
            { value: "Hài hước, sáng tạo", label: "Hài hước, sáng tạo" },
            { value: "Cảm xúc, truyền cảm hứng", label: "Cảm xúc, truyền cảm hứng" },
            {
                value: "Khác",
                label: "Khác (Nhập vào)",
            },
        ],
    },
];

const continueChat = async (key, query) => {
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        return; // Dừng nếu không đủ credit
    }
    if (mode.value == "" || mode.value == null) {
        toast.error("Vui lòng chọn chế độ để sử dụng tính năng.");
        return;
    }
    if (isSending.value || !key) return;

    // if (key === "analysis") {
    //     return (messagesMap[key] = [
    //         { name: "Bài viết Quảng cáo sản phẩm", isActive: false },
    //         { name: "Thơ Quảng cáo sản phẩm", isActive: false },
    //         { name: "Chiến dịch Truyền thông", isActive: false },
    //         { name: "Phân tích thị trường", isActive: false },
    //         { name: "Nhạc Doanh nghiệp", isActive: false },
    //     ]);
    // }

    isSending.value = true;
    let retryCount = 0;
    const MAX_RETRIES = 5;

    const executeRequest = async () => {
        try {
            const payload = {
                inputs: {},
                query: query,
                conversation_id: conversationId.value,
                project_id: formProject.id
            };

            if (selectedOption.value) {
                payload.inputs.chude = selectedOption.value;
            }
            let response = null;
            response = await axios.post(route("ai-business.sendChat"), payload, {
                headers: {
                    "Content-Type": "application/json;charset=UTF-8",
                },
            });

            if (!conversationId.value && response.data.conversation_id) {
                conversationId.value = response.data.conversation_id;
            }
            let messageData = null;
            switch (key) {
                case "resultsAnalysis":
                    messageData = response.data.answer;
                    break;
                case "content_product_ad":
                    messageData = response.data.answer;
                    break;
                case "music":
                    messageData = response.data.answer;
                    break;
                case "statusFanpage":
                    messageData = response.data.answer;
                    break;
                case "poetry":
                    messageData = response.data.answer;
                    break;
                case "analysis":
                    console.log(response.data.answer);

                    messageData = response.data.answer.split("\n").map((item) => {
                        return { name: item, isActive: false };
                    });
                    messagesMap[key] = messageData;
                    break;
                case "analysisBusiness":
                    messageData = response.data.answer;
                    messagesMap[key] = messageData;
                    break;
                case "event":
                    messageData = response.data.answer;
                    messagesMap[key] = messageData;
                    break;
                case "categories":
                    messageData = formatCategories(response.data.answer);
                    messagesMap[key] = messageData;
                    break;
                case "ideas":
                    messageData = response.data.answer;
                    messagesMap[key] = messageData;
                    break;
                case "finalTarget":
                    messageData = response.data.answer.split("\n").map((item) => {
                        return { name: item };
                    });
                    messagesMap[key] = messageData;
                    break;
                case "result":
                    messageData = response.data.answer;
                    updateResulTargetContent(messageData);
                    messagesMap[key] = messageData;
                    break;
                default:
                    const fixedJson = fixJsonString(response.data.answer);
                    messageData = JSON.parse(fixedJson);
                    if (key !== "result") {
                        messagesMap[key] = messageData;
                    }
                    break;
            }
            return messageData; // Success
        } catch (error) {
            if (retryCount < MAX_RETRIES) {
                retryCount++;
                console.warn(`Retry attempt ${retryCount} of ${MAX_RETRIES}`);

                await new Promise((resolve) => setTimeout(resolve, 10000));

                return false; // Retry needed
            }
            throw error;
        }
    };

    let success = false;
    while (!success && retryCount < MAX_RETRIES) {
        success = await executeRequest();
        return success;
    }
    if (!success) {
        throw new Error("Xin vui lòng bấm lại để nhận kết quả");
    }
};
/**
 * Sửa và chuẩn hóa chuỗi JSON không hợp lệ
 * @param {string} jsonString - Chuỗi JSON cần sửa
 * @returns {string|null} - Trả về chuỗi JSON đã sửa hoặc null nếu không thể sửa
 */
function fixJsonString(jsonString) {
    // Nếu input không phải string, return null
    if (typeof jsonString !== "string") {
        console.error("Input phải là chuỗi");
        return null;
    }

    try {
        // Thử parse JSON gốc
        JSON.parse(jsonString);
        return jsonString;
    } catch (error) {
        let fixedString = jsonString
            // Bước 1: Xử lý các dấu nháy
            .replace(/^"|"$/g, "") // Xóa dấu nháy kép ở đầu và cuối
            .replace(/\\"/g, '"') // Sửa escaped quotes
            .replace(/[""]|["]/g, '"') // Chuẩn hóa smart quotes
            .replace(/['']|[']/g, "'") // Chuẩn hóa smart single quotes

            // Bước 2: Xử lý escape characters
            .replace(/\\\\/g, "\\") // Sửa double escapes
            .replace(/\\n\\n/g, "\\n") // Chuẩn hóa double newlines
            .replace(/\\t/g, " ") // Thay thế tabs bằng spaces

            // Bước 3: Xử lý các ký tự đặc biệt
            .replace(/[\x00-\x1F\x7F-\x9F]/g, "") // Xóa control characters
            .replace(/&nbsp;/g, " ") // Thay thế &nbsp; bằng space
            .replace(/\s+/g, " ") // Chuẩn hóa khoảng trắng

            // Bước 4: Sửa cấu trúc JSON
            .replace(/([{,]\s*)(\w+)\s*:/g, '$1"$2":') // Thêm quotes cho keys
            .replace(/,(\s*[}\]])/g, "$1") // Xóa trailing commas
            .replace(/}\s*{/g, "},{") // Sửa định dạng objects
            .replace(/\]\s*\[/g, "],[") // Sửa định dạng arrays

            // Bước 5: Kiểm tra và thêm dấu ngoặc nếu cần
            .trim();

        // Kiểm tra và thêm dấu ngoặc tổng thể
        if (!fixedString.startsWith("[") && !fixedString.startsWith("{")) {
            fixedString = "[" + fixedString;
        }
        if (!fixedString.endsWith("]") && !fixedString.endsWith("}")) {
            fixedString = fixedString + "]";
        }

        try {
            // Kiểm tra kết quả cuối cùng
            JSON.parse(fixedString);
            return fixedString;
        } catch (finalError) {
            console.error("Không thể sửa JSON:", finalError.message);
            console.error("JSON không hợp lệ tại vị trí:", finalError.message.match(/position (\d+)/)?.[1]);
            return null;
        }
    }
}
const updateResulTargetContent = (content) => {
    const itemReuslt = messagesMap["resultTarget"].findIndex((item) => item.resultTargetLoading === true);
    messagesMap["resultTarget"][itemReuslt].content = helpers.convertHtmlToText(content);
    messagesMap["resultTarget"][itemReuslt].resultTargetLoading = false;
    messagesMap["resultTarget"][itemReuslt].isStructureOpen = false;
    messagesMap["resultTarget"][itemReuslt].selectedStyle = null;
    messagesMap["resultTarget"][itemReuslt].selectedEditStyle = null;
};
const uploadImage = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    // Validate file type
    const validImageTypes = ["image/jpeg", "image/png", "image/jpg", "image/gif"];
    if (!validImageTypes.includes(file.type)) {
        toast.error("File phải là hình ảnh có định dạng: jpeg, png, jpg, gif");
        e.target.value = "";
        return;
    }

    // Validate file size (max 2MB)
    const maxSize = 10 * 1024 * 1024; // 2MB in bytes
    if (file.size > maxSize) {
        toast.error("Kích thước hình ảnh tối đa là 2MB");
        e.target.value = "";
        return;
    }

    // Validate image dimensions (optional)
    try {
        const dimensions = await getImageDimensions(file);
    } catch (error) {
        toast.error("Không thể đọc file ảnh");
        e.target.value = "";
        return;
    }

    // If all validations pass
    tempFormProject.value.image_product.file = file;
    tempFormProject.value.image_product.url = URL.createObjectURL(file);
};

const uploadModelImage = async (e) => {
    const file = e.target.files[0];
    console.log(file);
    if (!file) return;

    // Validate file type
    const validImageTypes = ["image/jpeg", "image/png", "image/jpg", "image/gif"];
    if (!validImageTypes.includes(file.type)) {
        toast.error("File phải là hình ảnh có định dạng: jpeg, png, jpg, gif");
        e.target.value = "";
        return;
    }

    // Validate file size (max 10MB)
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if (file.size > maxSize) {
        toast.error("Kích thước hình ảnh tối đa là 10MB");
        e.target.value = "";
        return;
    }

    // Validate image dimensions (optional)
    try {
        const dimensions = await getImageDimensions(file);
        // if (dimensions.width < 200 || dimensions.height < 200) {
        //     toast.error('Kích thước ảnh tối thiểu là 200x200 pixels');
        //     e.target.value = '';
        //     return;
        // }
    } catch (error) {
        toast.error("Không thể đọc file ảnh");
        e.target.value = "";
        return;
    }

    // If all validations pass
    tempFormProject.value.model_product.file = file;
    tempFormProject.value.model_product.url = URL.createObjectURL(file);
};

const DEFAULT_MODEL_IMAGE = "https://static2.yan.vn/YanNews/202308/202308020439097760-40d2f3e9-8f57-407c-bfb2-504fdd0ead22.jpeg";

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

const combineImages = async (productImage, modelImage) => {
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

// Helper function to get image dimensions
const getImageDimensions = (file) => {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => {
            resolve({
                width: img.width,
                height: img.height,
            });
        };
        img.onerror = () => {
            reject(new Error("Failed to load image"));
        };
        img.src = URL.createObjectURL(file);
    });
};
const toggleActiveFile = (index) => {
    tempFormProject.value.files[index].active = !tempFormProject.value.files[index].active;
};
const tempFormProject = ref({
    image_product: {
        url: "",
        file: null,
    },
    model_product: {
        url: "",
        file: null,
    },
    files: [],
});
const formProject = reactive({
    id: null,
    title: "",
    description: "",
    image_product: "",
    model_product: "",
    productLink: "",
    files: [],
});
const handleFillBasicInformation = async () => {
    if (formProject.title == "") {
        toast.error("Vui lòng nhập tên sản phẩm");
        return;
    }
    try {
        loading.value = true;
        if (mode.value == "basic") {
            sections[0].open = false;
            // sections[1].open = false;
            sections[2].open = true;
            isLoadingAnalysis.value = true;
            if (conversationId.value) {
                // formProject.description = ''
                formProject.image_product = "";
                formProject.data_json = "";
                await handleProjectSubmit(true);
            } else {
                await handleProjectSubmit();
            }

            await handleButtonClick("analysis", null, category.value, 2, null, formProject.title);
        } else {
            sections[0].open = false;
            sections[1].open = true;
            await handleProjectSubmit();
        }
    } catch (error) {
        console.error("Lỗi:", error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại.");
    } finally {
        loading.value = false;
    }
};
const handleProjectSubmit = async (isStartChat = true) => {
    try {
        if (mode.value == "" || mode.value == null) {
            toast.error("Vui lòng chọn chế độ để sử dụng tính năng.");
            return;
        }
        let isUpdate = false;
        const formData = new FormData();
        if (!formProject.title?.trim()) {
            toast.error("Vui lòng nhập tiêu đề sản phẩm");
            return;
        }

        formData.append("title", formProject.title);
        formData.append("description", formProject.description);
        formData.append("url", formProject.productLink);
        if (formProject?.data_json) {
            formData.append("data_json", JSON.stringify(formProject.data_json));
        }
        if (tempFormProject.value.image_product.file) {
            formData.append("image_product", tempFormProject.value.image_product.file);
        }
        if (tempFormProject.value.model_product.file) {
            formData.append("image_model", tempFormProject.value.model_product.file);
        }
        if (tempFormProject.value.files?.length > 0) {
            formData.append("files", JSON.stringify(tempFormProject.value.files));
        } else {
            formData.append("files", JSON.stringify([]));
        }
        let response = null;
        if (formProject.id) {
            isUpdate = true;
            formData.append("id", formProject.id);
            response = await axios.post(route("ai-business.update-project"), formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
        } else {
            response = await axios.post(route("ai-business.create-project"), formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
        }
        if (response.data) {
            const project = response.data.data;
            if (project.id) {
                businessProjectId.value = project.id;
            }
            //set data after create project or update project
            setValue(project, isUpdate);
            if (isStartChat) {
                await startChat("information_project");
            }
        }
    } catch (error) {
        console.error("Lỗi:", error);

        // Handle validation errors
        if (error.response?.status === 422) {
            const errors = error.response.data.errors;
            // Show first error message
            const firstError = Object.values(errors)[0][0];
            toast.error(firstError);
        }
        // Handle other errors
        else {
            toast.error(error.response?.data?.message || "Tạo sản phẩm thất bại!");
        }
    }
};
const setValue = (project, isUpdate) => {
    formProject.id = project.id;
    formProject.title = project.title;
    formProject.description = project.description;
    formProject.image_product = project.image_product;
    formProject.files = project.files;

    tempFormProject.value.image_product.url = project.image_product;
    tempFormProject.value.model_product.url = project.image_model;
    tempFormProject.value.files = project?.files ?? [];
    if (isUpdate) {
        listProject.value = listProject.value?.map((item) => {
            if (item.id === project.id) {
                return project;
            }
            return item;
        });
    } else {
        listProject.value.push(project);
    }
    selectedProject.value = project;
};

const acceptConversation = ref(false);
const goToStepSelectCategory = (event) => {
    if (selectedProject.value == null) {
        toast.error("Vui lòng chọn dự án để tiếp tục");
        return;
    }

    const editorContent = sunEditorInstance.value.getContents();
    if (editorContent == "") {
        toast.error("Thiết lập thông tin và hiệu chỉnh nội dung trước khi chuyển bước");
        return;
    }
    sections[0].open = false;
    sections[1].open = false;
    sections[2].open = true;
    isLoadingAnalysis.value = true;
    // handleButtonClick("analysisBusiness", event, category, 2);
    handleShowAnalysisBusiness();
    handleProjectUpdateStep02();
};

const handleShowAnalysisBusiness = () => {
    let key = "analysisBusiness";
    return (messagesMap[key] = {
        ...messagesMap[key],
        formData: mockDataAnalysisBusiness,
    });
};

const handleProjectUpdateStep02 = async () => {
    try {
        if (formProject.id && sunEditorInstance.value) {
            acceptConversation.value = true;
            const editorContent = sunEditorInstance.value.getContents();
            console.log("handleProjectUpdateStep02", editorContent);
            pendingEditorContent.value = editorContent;
            messagesMap["information_project"] = editorContent;
            // Update data_json với editorContent mới
            const updatedDataJson = {
                ...formProject.data_json,
                information_project: {
                    ...formProject.data_json.information_project,
                    answer: editorContent,
                },
            };

            const formData = new FormData();
            if (!formProject.title?.trim()) {
                toast.error("Vui lòng nhập tiêu đề sản phẩm");
                return;
            }

            formData.append("description", formProject.description);
            formData.append("title", formProject.title);
            formData.append("url", formProject.productLink);
            formData.append("id", formProject.id);
            formData.append("data_json", JSON.stringify(updatedDataJson));

            const response = await axios.post(route("ai-business.update-project"), formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });

            if (response.data.success) {
                toast.success("Cập nhật thành công!");
                // Update local state nếu cần
                formProject.data_json = updatedDataJson;
            }
        }
    } catch (error) {
        console.error("Lỗi:", error);

        if (error.response?.status === 422) {
            const errors = error.response.data.errors;
            const firstError = Object.values(errors)[0][0];
            toast.error(firstError);
        } else {
            toast.error(error.response?.data?.message || "Cập nhật sản phẩm thất bại!");
        }
    }
};

const handleUploadDocument = async (e) => {
    const file = e.target.files[0];
    //only accept pdf, doc, docx, xls, xlsx, ppt, pptx, jpg, jpeg, png, gif
    // const allowedExtensions = /(\.pdf|\.doc|\.docx|\.xls|\.xlsx|\.ppt|\.pptx|\.jpg|\.jpeg|\.png|\.gif)$/i;
    // Chỉ cho phép các file văn bản
    const allowedMimeTypes = [
        // Text documents
        "text/plain", // .txt
        // Microsoft Word
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document", // .docx
        // PDF
        "application/pdf", // .pdf
    ];
    if (!allowedMimeTypes.includes(file.type)) {
        toast.error("Chỉ chấp nhận các định dạng tài liệu: TXT, DOCX, PDF");
        return;
    }
    if (!file) return;
    try {
        documentLoading.value = true;
        const formData = new FormData();
        formData.append("file", file);
        const response = await axios.post(route("ai-business.upload-document"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        if (response.data) {
            const data = response.data.data;
            const url = data.url;
            const name = data.name;
            const type = data.type;
            const size = data.size;
            const path = data.path;
            const file = {
                url: url,
                name: name,
                type: type,
                size: size,
                active: false,
                path: path,
            };
            tempFormProject.value.files.push(file);
        }
    } catch (error) {
        console.error("Lỗi khi upload tài liệu:", error.response?.data || error.message);
        toast.error("Upload tài liệu thất bại!");
    } finally {
        documentLoading.value = false;
    }
};
const handleRemoveDocument = (index) => {
    tempFormProject.value.files.splice(index, 1);
    formProject.files = tempFormProject.value.files;
};
const selectALlDocument = (e) => {
    tempFormProject.value.files.forEach((item) => {
        item.active = true;
    });
};
const unSelectALlDocument = (e) => {
    tempFormProject.value.files.forEach((item) => {
        item.active = false;
    });
};

const pendingEditorContent = ref(null);
const selectProject = async (item) => {
    if (mode.value == "" || mode.value == null) {
        toast.error("Vui lòng chọn chế độ để sử dụng tính năng.");
        return;
    }
    sections[1].open = false;
    isLoadingCheckConversation.value = true;
    selectedProject.value = item;
    conversationId.value = null;
    hiddenSocialMedia.value = false;
    hiddenImage.value = false;
    hiddenVideo.value = true;
    Object.keys(messagesMap).forEach((key) => {
        delete messagesMap[key];
    });
    formProject.id = item.id;
    formProject.title = item.title;
    formProject.description = item.description;
    formProject.image_product = item.image_product;
    formProject.files = item.files;
    formProject.productLink = item.url;
    tempFormProject.value.image_product.url = item.image_product;
    tempFormProject.value.model_product.url = item.image_model;
    tempFormProject.value.files = item.files;
    activeListProject.value = false;
    //get data json
    const messageMapJson = item.data_json;
    formProject.data_json = messageMapJson;
    if (messageMapJson?.information_project) {
        let notFound = false;
        conversationId.value = messageMapJson.information_project.conversation_id;
        // try {
        //     await checkConversationExit(conversationId.value)
        //     notFound = false;
        // } catch (error) {
        //     const errorData = JSON.parse(error.response.data?.error);
        //     if (errorData.status === 404) {
        //         conversationId.value = null
        //         messagesMap['information_project'] = null
        //         notFound = true;
        //     }
        // }
        if (!notFound) {
            // Lưu content cần render
            pendingEditorContent.value = marked(messageMapJson?.information_project?.answer || "");
            if (sunEditorInstance.value) {
                sunEditorInstance.value.setContents(marked(messageMapJson.information_project.answer));
            }
            sections[0].open = false;
            sections[1].open = true;
            //scroll to section
            if (category.value) {
                category.value.scrollIntoView({ behavior: "smooth", block: "center", inline: "center" });
                messagesMap["analysis"] = sampleAnalysis;
            }
        }
    } else {
        sections[0].open = true;
    }
    isLoadingCheckConversation.value = false;
};

const selectedAnalysis = ref("");
const handleToggleAnalysis = (index) => {
    selectedAnalysis.value = messagesMap["analysis"][index].name;
    messagesMap["analysis"][index].isActive = true;
    messagesMap["analysis"].forEach((item, i) => {
        if (i !== index) {
            item.isActive = false;
        }
    });
    messagesMap["music"] = null;
    musicAi.value = false;
    stepPostFormRef.value = null;
    messagesMap.music = null;
    messagesMap.resultTarget = null;
    messagesMap.resultsAnalysis = null;
    messagesMap.content_product_ad = null;
    messagesMap.poetry = null;
};

const selectedContentStrategy = ref("");
const handleToggleContentStrategy = (index) => {
    selectedContentStrategy.value = messagesMap["contentStrategy"][index].name;
    messagesMap["contentStrategy"][index].isActive = true;
    messagesMap["contentStrategy"].forEach((item, i) => {
        if (i !== index) {
            item.isActive = false;
        }
    });
    messagesMap["music"] = null;
    musicAi.value = false;
    stepPostFormRef.value = null;
    messagesMap.music = null;
    messagesMap.resultTarget = null;
    messagesMap.resultsAnalysis = null;
    messagesMap.content_product_ad = null;
    messagesMap.poetry = null;
};

const handleActiveFinalTarget = (index) => {
    messagesMap["resultTarget"].forEach((item, i) => {
        if (i !== index) {
            item.isActive = false;
        } else {
            item.isActive = true;
        }
    });
};
const getActiveFinalTarget = () => {
    return messagesMap["resultTarget"]?.find((item) => item.isActive === true);
};

const marketAnalysis = ref('');

const toggleItemResultAnalysis = async (index, currentSubStep) => {
    const key = "resultsAnalysis";
    messagesMap.music = {};
    messagesMap.resultTarget = null;
    messagesMap.poetry = null;
    messagesMap.content_product_ad = {};
    if (!messagesMap[key]) {
        messagesMap[key] = resultsAnalysisSample;
    }

    messagesMap[key][index].isActive = !messagesMap[key][index].isActive;

    if (messagesMap[key][index].isActive) {
        if (messagesMap[key][index].content) {
            marketAnalysis.value += `${messagesMap[key][index].name}\n${messagesMap[key][index].content}`;
        } else {
            try {
                messagesMap[key][index].content = "";
                let dataJson = {
                    currentStep: "buoc3",
                    the_loai: analysisActive.value.name,
                };
                if (currentSubStep) {
                    dataJson = {
                        ...dataJson,
                        currentSubStep,
                    };
                }
                const result = await continueChatStreaming(key, JSON.stringify(dataJson), index);

                marketAnalysis.value += `${messagesMap[key][index].name}\n${messagesMap[key][index].content}`;
            } catch (error) {
                console.error(error);
            } finally {
                isSending.value = false;
            }
        }
    }

    const formData = new FormData();
    formData.append("id", formProject.id);
    formData.append("title", formProject.title);
    formData.append("content", marketAnalysis.value);

    await updateProject(formData);
};
const getFormContentProductAd = async (advanced = true) => {
    try {
        const key = "content_product_ad";

        if (advanced) {
            messagesMap[key] = {
                ...messagesMap[key],
                formData: mockFormDataContentProductAd,
            };
        }
        const result = await continueChat(
            key,
            JSON.stringify({
                currentStep: "buoc3",
                the_loai: analysisActive.value.name,
            })
        );
    } catch (error) {
        console.error("Lỗi khi lấy form:", error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại.");
    } finally {
        isSending.value = false;
    }
};
const getFormMediaCampaign = async (advanced = true) => {
    try {
        const key = "content_product_ad";
        const result = await continueChat(
            key,
            JSON.stringify({
                currentStep: "buoc3",
                the_loai: analysisActive.value.name,
            })
        );
        if (result && advanced) {
            try {
                const parsedResult = JSON.parse(result);
                messagesMap[key] = {
                    ...messagesMap[key],
                    formData: parsedResult,
                };
            } catch (jsonError) {
                const splitResult = result.split(/\r?\n|\r/).map((line) => line.trim());

                const options = splitResult.map((item) => ({
                    value: item,
                    label: item,
                }));

                const formData = [
                    {
                        name: "field_radio",
                        label: "Chọn một tùy chọn",
                        type: "radio",
                        options,
                        value: "",
                    },
                ];

                messagesMap[key] = {
                    ...messagesMap[key],
                    formData,
                };
            }
        }
    } catch (error) {
        console.error("Lỗi khi lấy form content product ad:", error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại.");
    } finally {
        isSending.value = false;
    }
};
const mockFormDataContentProductAd = [
    {
        name: "trending_context",
        label: "Bối cảnh",
        type: "text",
        placeholder: "Bối cảnh là xu hướng hoặc sự kiện để bài viết Chạm được tới tâm trí đám đông nhanh nhất và dễ nhất. Nó là thứ mà đám đông khách hàng đang quan tâm ở thời điểm này.",
        value: "",
    },
    {
        name: "goal",
        label: "Mục tiêu bài viết",
        type: "select",
        value: "",
        options: [
            { value: "Tăng Nhận Diện Thương Hiệu", label: "Tăng Nhận Diện Thương Hiệu" },
            { value: "Thông Tin Sản Phẩm/Dịch Vụ", label: "Thông Tin Sản Phẩm/Dịch Vụ" },
            { value: "Tạo trao đổi về Sản Phẩm / Dịch vụ", label: "Tạo trao đổi về Sản Phẩm / Dịch vụ" },
            { value: "Kích Thích Sự Tương Tác (Engagement)", label: "Kích Thích Sự Tương Tác (Engagement)" },
            { value: "Quảng bá về Tiện ích và Thái độ phục vụ", label: "Quảng bá về Tiện ích và Thái độ phục vụ" },
            { value: "Tạo Độ Tin Cậy và Chuyên Môn", label: "Tạo Độ Tin Cậy và Chuyên Môn" },
            { value: "Phá bỏ Rào cản và tạo động lực mua", label: "Phá bỏ Rào cản và tạo động lực mua" },
            { value: "Kích thích hành động mua ngay", label: "Kích thích hành động mua ngay" },
            { value: "Kết Nối và Phản Hồi Khách Hàng", label: "Kết Nối và Phản Hồi Khách Hàng" },
            { value: "Xây Dựng Cộng Đồng", label: "Xây Dựng Cộng Đồng" },
            { value: "Hướng Dẫn và Giáo Dục khách hàng", label: "Hướng Dẫn và Giáo Dục khách hàng" },
            { value: "Phân Tích Rào cản Khách Hàng", label: "Phân Tích Rào cản Khách Hàng" },
            { value: "Tạo Dựng Mối Quan Hệ Với Khách Hàng", label: "Tạo Dựng Mối Quan Hệ Với Khách Hàng" },
            { value: "Xu Hướng Và Tương Lai", label: "Xu Hướng Và Tương Lai" },
            { value: "Tăng Cường Hình Ảnh Thương Hiệu", label: "Tăng Cường Hình Ảnh Thương Hiệu" },
        ],
    },
    {
        name: "target_audience",
        label: "Đối tượng khách hàng",
        type: "text",
        placeholder: "Đối tượng khách hàng là những người mà bạn muốn nhắm đến chính cho bài viết. (Ví dụ: Mẹ bỉm sữa, dân văn phòng, học sinh)",
        value: "",
    },
    {
        name: "key_message",
        label: "Thông điệp chính",
        type: "textarea",
        placeholder: "Thông điệp chính là nội dung trọng tâm bạn muốn khách hàng ghi nhớ.",
        value: "",
    },
    {
        name: "desired_emotion",
        label: "Cảm xúc mong muốn truyền tải",
        type: "select",
        value: "",
        options: [
            { value: "Tin tưởng", label: "Tin tưởng" },
            { value: "Hứng khởi", label: "Hứng khởi" },
            { value: "Khẩn cấp", label: "Khẩn cấp" },
            { value: "Hạnh phúc", label: "Hạnh phúc" },
            { value: "Tự hào", label: "Tự hào" },
            { value: "Thấu hiểu", label: "Thấu hiểu" },
            { value: "Khao khát", label: "Khao khát" },
            { value: "An tâm", label: "An tâm" },
            { value: "Truyền cảm hứng", label: "Truyền cảm hứng" },
            { value: "Tò mò", label: "Tò mò" },
            { value: "Yêu thương", label: "Yêu thương" },
            { value: "Vui vẻ Hài hước", label: "Vui vẻ Hài hước" },
            { value: "Động lực", label: "Động lực" },
            { value: "Hoài niệm", label: "Hoài niệm" },
        ],
    },
    {
        name: "writing_style",
        label: "Phong cách viết",
        type: "select",
        value: "",
        options: [
            { value: "Trang Trọng", label: "Trang Trọng" },
            { value: "Hấp Dẫn và Sáng Tạo", label: "Hấp Dẫn và Sáng Tạo" },
            { value: "Thư Giãn và Thân Thiện", label: "Thư Giãn và Thân Thiện" },
            { value: "Chuyên Nghiệp", label: "Chuyên Nghiệp" },
            { value: "Hài hước và vui vẻ", label: "Hài hước và vui vẻ" },
            { value: "Thể Thao và Năng Động", label: "Thể Thao và Năng Động" },
            { value: "Hướng Dẫn và Giảng Dạy", label: "Hướng Dẫn và Giảng Dạy" },
            { value: "Chất Lượng và Tinh Tế", label: "Chất Lượng và Tinh Tế" },
            { value: "Ngắn Gọn và Súc Tích", label: "Ngắn Gọn và Súc Tích" },
            { value: "Chia Sẻ Kinh Nghiệm Cá Nhân", label: "Chia Sẻ Kinh Nghiệm Cá Nhân" },
            { value: "Truyện Cười và Giải Trí", label: "Truyện Cười và Giải Trí" },
            { value: "Đánh Giá và So Sánh", label: "Đánh Giá và So Sánh" },
            { value: "Cảm Xúc và Sâu Sắc", label: "Cảm Xúc và Sâu Sắc" },
            { value: "Tone Nghiêm túc", label: "Tone Nghiêm túc" },
            { value: "Tone Lạc quan", label: "Tone Lạc quan" },
            { value: "Tone Hấp Dẫn và Thú vị", label: "Tone Hấp Dẫn và Thú vị" },
            { value: "Tone Thư Thái, nhẹ nhàng", label: "Tone Thư Thái, nhẹ nhàng" },
        ],
    },
    {
        name: "post_format",
        label: "Định dạng bài viết",
        type: "select",
        value: "",
        options: [
            { value: "Status ngắn", label: "Status ngắn" },
            { value: "Bài viết dài", label: "Bài viết dài" },
            { value: "Dạng Danh sách", label: "Dạng Danh sách" },
            { value: "Câu chuyện kể", label: "Câu chuyện kể" },
            { value: "Kịch bản Video quảng cáo", label: "Kịch bản Video quảng cáo" },
            { value: "Câu hỏi thảo luận", label: "Câu hỏi thảo luận" },
            { value: "Trích dẫn", label: "Trích dẫn" },
            { value: "Hướng dẫn sử dụng", label: "Hướng dẫn sử dụng" },
            { value: "So sánh", label: "So sánh" },
            { value: "Feedback từ khách hàng", label: "Feedback từ khách hàng" },
            { value: "Câu chuyện khách hàng", label: "Câu chuyện khách hàng" },
            { value: "Bài viết dựa trên số liệu", label: "Bài viết dựa trên số liệu" },
        ],
    },
    {
        name: "promotion_gift",
        label: "Ưu đãi và quà tặng",
        type: "text",
        placeholder: "Ưu đãi/quà tặng giúp tăng sức hút và thúc đẩy hành động.",
        value: "",
    },
    {
        name: "cta",
        label: "Thêm CTA (Lời Kêu Gọi Hành Động)",
        type: "select",
        value: "",
        options: [
            { value: "Mua Ngay", label: "Mua Ngay" },
            { value: "Khuyến khích liên kết và tương tác", label: "Khuyến khích liên kết và tương tác" },
            { value: "Đăng Ký tham gia", label: "Đăng Ký tham gia" },
            { value: "Xem Chi Tiết", label: "Xem Chi Tiết" },
            { value: "Liên Hệ Chúng Tôi", label: "Liên Hệ Chúng Tôi" },
            { value: "Thêm Chia Sẻ", label: "Thêm Chia Sẻ" },
            { value: "Tăng bình luận", label: "Tăng bình luận" },
        ],
    },
];

const mockFormDataPoetry = [
    {
        name: "poem_style",
        type: "select",
        label: "Phong cách thơ",
        value: "",
        options: [
            { label: "Hài hước", value: "Hài hước" },
            { label: "Lãng mạn", value: "Lãng mạn" },
            { label: "Cảm động", value: "Cảm động" },
            { label: "Sôi động", value: "Sôi động" },
            { label: "Cổ điển", value: "Cổ điển" },
            { label: "Hiện đại", value: "Hiện đại" },
            { label: "Trang trọng", value: "Trang trọng" },
            { label: "Thân thiện", value: "Thân thiện" },
            { label: "Tinh nghịch", value: "Tinh nghịch" },
            { label: "Giản dị", value: "Giản dị" },
        ],
    },
    {
        name: "poem_emotion",
        type: "select",
        label: "Cảm xúc bài thơ",
        value: "",
        options: [
            { label: "Tin tưởng", value: "Tin tưởng" },
            { label: "Hứng khởi", value: "Hứng khởi" },
            { label: "Khẩn cấp", value: "Khẩn cấp" },
            { label: "Hạnh phúc", value: "Hạnh phúc" },
            { label: "Tự hào", value: "Tự hào" },
            { label: "Thấu hiểu", value: "Thấu hiểu" },
            { label: "Khao khát", value: "Khao khát" },
            { label: "An tâm", value: "An tâm" },
            { label: "Truyền cảm hứng", value: "Truyền cảm hứng" },
            { label: "Tò mò", value: "Tò mò" },
            { label: "Yêu thương", value: "Yêu thương" },
            { label: "Hài hước", value: "Hài hước" },
            { label: "Hoài niệm", value: "Hoài niệm" },
            { label: "Cảm động", value: "Cảm động" },
        ],
    },
    {
        name: "goal",
        type: "select",
        label: "Mục đích bài thơ",
        value: "",
        options: [
            { label: "Bán hàng", value: "Bán hàng" },
            { label: "Giới thiệu thương hiệu", value: "Giới thiệu thương hiệu" },
            { label: "Tạo tương tác", value: "Tạo tương tác" },
            { label: "Tăng nhận diện thương hiệu", value: "Tăng nhận diện thương hiệu" },
            { label: "Khuyến khích sử dụng dịch vụ", value: "Khuyến khích sử dụng dịch vụ" },
            { label: "Kích thích tò mò", value: "Kích thích tò mò" },
            { label: "Chào mừng sự kiện", value: "Chào mừng sự kiện" },
            { label: "Cảm ơn khách hàng", value: "Cảm ơn khách hàng" },
            { label: "Truyền cảm hứng", value: "Truyền cảm hứng" },
            { label: "Giải trí", value: "Giải trí" },
        ],
    },
    {
        name: "key_message",
        type: "textarea",
        label: "Thông điệp chính",
        value: "",
        placeholder: "Thông điệp chính cần nhấn mạnh là nội dung trọng tâm bạn muốn khách hàng ghi nhớ",
    },
    {
        name: "keywords",
        type: "text",
        label: "Từ khóa (key)",
        value: "",
        placeholder: "Từ khóa hoặc những từ/câu đặc biệt muốn có trong bài thơ? Hãy viết ngắn gọn, cách nhau dấu phẩy",
    },
    {
        name: "trending_context",
        type: "text",
        label: "Bối cảnh",
        value: "",
        placeholder: "Bối cảnh là xu hướng hoặc sự kiện để bài viết chạm được tới tâm trí đám đông nhanh nhất và dễ nhất.",
    },
    {
        name: "cta",
        type: "select",
        label: "Thêm CTA (Lời Kêu Gọi Hành Động)",
        value: "",
        options: [
            { label: "Mua Ngay", value: "Mua Ngay" },
            { label: "Khuyến khích liên kết và tương tác", value: "Khuyến khích liên kết và tương tác" },
            { label: "Thêm Chia Sẻ", value: "Thêm Chia Sẻ" },
            { label: "Tăng bình luận", value: "Tăng bình luận" },
        ],
    },
];

const getFormPoetry = async (advanced = true) => {
    // try {
    //     const key = "poetry"
    //     const result = await continueChat(key, JSON.stringify({
    //         currentStep: `buoc3`,
    //         the_loai: analysisActive.value.name
    //     }));
    //     if (result && advanced) {
    //         try {
    //             messagesMap[key] = {
    //                 ...messagesMap[key],
    //                 formData: JSON.parse(result),
    //             };
    //         } catch (jsonError) {
    //             console.log("getFormPoetry ~ jsonError:", jsonError)
    //         }
    //     }
    // } catch (error) {
    //     console.error("Lỗi khi lấy form content product ad:", error);
    //     toast.error("Có lỗi xảy ra, vui lòng thử lại.");
    // } finally {
    //     isSending.value = false;
    // }

    try {
        const key = "poetry";

        if (advanced) {
            messagesMap[key] = {
                ...messagesMap[key],
                formData: mockFormDataPoetry,
            };
        }
        await continueChat(
            key,
            JSON.stringify({
                currentStep: "buoc3",
                the_loai: analysisActive.value.name,
            })
        );
        sections[3].open = true;
        // sections[2].open = false;
    } catch (error) {
        console.error("Lỗi khi lấy form:", error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại.");
    } finally {
        isSending.value = false;
    }
};

const isLoadingContentStrategy = ref(false);
const handleSubmitAnalysicBusiness = async () => {
    if (!acceptConversation.value) {
        toast.error("Vui lòng chọn dự án để sử dụng tính năng.");
        return;
    }
    const key = "analysisBusiness";
    try {
        isLoadingContentStrategy.value = true;
        if (!messagesMap[key]) {
            messagesMap[key] = {
                formData: [],
                isLoading: false,
            };
            sections[3].open = false;
        }
        messagesMap["resultTarget"] = [];
        messagesMap[key].isLoading = true;
        console.log(messagesMap[key]);
        let result = null;

        result = await continueChat(
            key,
            JSON.stringify({
                currentStep: `buoc3`,
                currentSubStep: `buoc3_1`,
                mode: mode.value,
                ...Object.fromEntries(messagesMap[key].formData.map((item) => [item.name, item.value])),
            })
        );

        const contentStrategyArray = result.split("\n").map((item) => {
            return { name: item, isActive: false };
        });
        messagesMap["contentStrategy"] = contentStrategyArray;
        sections[2].open = false;
        sections[3].open = true;
    } catch (error) {
        console.error("handleSubmitContentProductAd ~ error:", error);
    } finally {
        // messagesMap[key].isLoading = false;
        isLoadingContentStrategy.value = false;
        isSending.value = false;
    }
};

const handleSubmitMediaCampaign = async () => {
    const key = "content_product_ad";
    try {
        if (!messagesMap[key]) {
            messagesMap[key] = {
                formData: [],
                isLoading: false,
            };
        }
        messagesMap["resultTarget"] = [];
        messagesMap[key].isLoading = true;
        let result = null;
        const analysisActive = messagesMap["analysis"].find((item) => item.isActive);
        if (analysisActive.name === "Chiến dịch Truyền thông") {
            const activeOptions = messagesMap[key].formData[0].options.filter((option) => option.isActive).map((option) => option.value);

            result = await continueChat(
                key,
                JSON.stringify({
                    currentStep: `buoc4`,
                    muc_tieu: activeOptions[0],
                })
            );
        } else if (analysisActive.name === "Tạo Status Fanpage 1 tháng") {
            result = await continueChatStreaming(
                key,
                JSON.stringify({
                    currentStep: `buoc4`,
                    ...Object.fromEntries(messagesMap[key].formData.map((item) => [item.name, item.value])),
                })
            );
        } else {
            result = await continueChat(
                key,
                JSON.stringify({
                    currentStep: `buoc4`,
                    ...Object.fromEntries(messagesMap[key].formData.map((item) => [item.name, item.value])),
                })
            );
        }

        if (result) {
            const jsonStartIndex = result.indexOf("{");
            const jsonEndIndex = result.indexOf("}") + 1;

            const jsonPart = result.substring(jsonStartIndex, jsonEndIndex);
            const content = result.substring(jsonEndIndex).trim();

            const jsonData = JSON.parse(jsonPart);
            if (!messagesMap["resultTarget"]) {
                messagesMap["resultTarget"] = [];
            }
            messagesMap["resultTarget"].push({
                content: helpers.convertHtmlToText(content),
                options_rewrite: [
                    ...Object.keys(jsonData).map((key) => ({
                        name: key,
                        value: "",
                        options: jsonData[key] || [],
                        isOpen: false,
                    })),
                ],
                images: [],
            });
            sections.forEach((item) => {
                item.open = false;
            });
            await nextTick();
            if (resultTarget.value) {
                resultTarget.value.scrollIntoView({ behavior: "smooth", block: "center", inline: "center" });
            }

            const formData = new FormData();
            formData.append("id", formProject.id);
            formData.append("title", formProject.title);
            formData.append("content", content);

            await updateProject(formData);
        }
    } catch (error) {
        console.error("handleSubmitContentProductAd ~ error:", error);
    } finally {
        messagesMap[key].isLoading = false;
        isSending.value = false;

        await callSearchImage();
    }
};

const updateProject = async (formData) => {
    try {
        const response = await axios.post(route("ai-business.update-project"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        console.log("Project updated successfully:", response.data);
        return response.data;
    } catch (error) {
        console.error("Error updating project:", error);
    }
};

const handleSubmitPoetry = async () => {
    const key = "poetry";
    try {
        if (!messagesMap[key]) {
            messagesMap[key] = {
                isLoading: false,
            };
        }
        messagesMap[key].isLoading = true;
        const body = Object.fromEntries(messagesMap[key].formData?.map((item) => [item.name, item.value]) || []) || {};
        const result = await continueChat(
            key,
            JSON.stringify({
                currentStep: `buoc4`,
                poem_style: "",
                goal: "",
                poem_emotion: "",
                key_message: "",
                keywords: "",
                trending_context: "",
                cta: "",
                ...body,
            })
        );
        if (result) {
            const jsonStartIndex = result.indexOf("{");
            const jsonEndIndex = result.indexOf("}") + 1;

            // Extract JSON and content
            const jsonPart = result.substring(jsonStartIndex, jsonEndIndex);
            const content = result.substring(jsonEndIndex).trim();

            const jsonData = JSON.parse(jsonPart);
            if (!messagesMap["resultTarget"]) {
                messagesMap["resultTarget"] = [];
            }
            messagesMap["resultTarget"].push({
                content: helpers.convertHtmlToText(content),
                options_rewrite: [
                    ...Object.keys(jsonData).map((key) => ({
                        name: key,
                        value: "",
                        options: jsonData[key] || [],
                        isOpen: false,
                    })),
                    {
                        name: "keyword",
                        value: "",
                        isOpen: false,
                    },
                ],
                images: [],
            });
            sections.forEach((item) => {
                item.open = false;
            });
            await nextTick();
            if (resultTarget.value) {
                resultTarget.value.scrollIntoView({ behavior: "smooth", block: "center", inline: "center" });
            }

            const formData = new FormData();
            formData.append("id", formProject.id);
            formData.append("title", formProject.title);
            formData.append("content", content);

            await updateProject(formData);
        }
    } catch (error) {
        console.error("Lỗi khi lấy form content product ad:", error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại.");
    } finally {
        messagesMap[key].isLoading = false;
        isSending.value = false;

        await callSearchImage();
    }
};
const rewriteContentProductAd = async (index) => {
    const key = "content_product_ad";
    try {
        messagesMap["resultTarget"][index].isLoadingRewrite = true;
        let allRewrite = {};
        messagesMap["resultTarget"][index].options_rewrite.forEach((item) => {
            allRewrite[convertToSnakeCase(item.name)] = item.value;
        });
        const result = await continueChat(
            key,
            JSON.stringify({
                currentStep: `buoc5`,
                text: messagesMap["resultTarget"][0].content,
                ...allRewrite,
            })
        );
        if (result) {
            messagesMap["resultTarget"][index].content = result;
        }
    } catch (error) {
        console.log("rewriteContentProductAd ~ error:", error);
    } finally {
        isSending.value = false;
        messagesMap["resultTarget"][index].isLoadingRewrite = false;
    }
};
const handleSubmitMusic = async (type) => {
    const key = "music";
    try {
        let query = null;
        let result = null;
        switch (type) {
            case "overview":
                messagesMap[key] = {
                    ...messagesMap[key],
                    big_ideas: {
                        isLoading: true,
                    },
                };
                query = JSON.stringify({
                    currentStep: "buoc3",
                    currentSubStep: "buoc3_1",
                    the_loai: analysisActive.value.name,
                    goal: messagesMap[key]?.overview?.formData[0].value,
                    so_luong: String(messagesMap[key]?.overview?.formData[1].value) || "1",
                });
                result = await continueChat(key, query);
                if (result) {
                    messagesMap[key] = {
                        ...messagesMap[key],
                        big_ideas: {
                            formData: JSON.parse(result),
                            isLoading: false,
                        },
                    };
                }
                break;
            case "lyric":
                let bigIdeaActive = messagesMap[key].big_ideas.formData.find((item) => item.isActive === true);
                if (!bigIdeaActive) {
                    toast.error("Vui lòng chọn Big Idea");
                    return;
                }
                messagesMap[key] = {
                    ...messagesMap[key],
                    lyric: {
                        isLoading: true,
                    },
                };
                query = JSON.stringify({
                    currentStep: "buoc3",
                    currentSubStep: "buoc3_2",
                    the_loai: "Lời nhạc Quảng cáo sản phẩm",
                    y_tuong: `${bigIdeaActive.y_tuong}:${bigIdeaActive.content}`,
                });
                result = await continueChat(key, query);
                if (result) {
                    messagesMap[key] = {
                        ...messagesMap[key],
                        lyric: {
                            formData: JSON.parse(result),
                            isLoading: false,
                        },
                    };
                }
                break;
            case "result":
                let body = {};
                messagesMap[key].isLoading = true;
                messagesMap[key]?.lyric?.formData.forEach((item) => {
                    body[item.name] = item.value;
                });
                result = await continueChat(
                    key,
                    JSON.stringify({
                        currentStep: `buoc4`,
                        ...body,
                    })
                );
                if (result) {
                    const jsonStartIndex = result.indexOf("{");
                    const jsonEndIndex = result.indexOf("}") + 1;

                    // Extract JSON and content
                    const jsonPart = result.substring(jsonStartIndex, jsonEndIndex);
                    const content = result.substring(jsonEndIndex).trim();

                    const jsonData = JSON.parse(jsonPart);
                    if (!messagesMap["resultTarget"]) {
                        messagesMap["resultTarget"] = [];
                    }
                    messagesMap["resultTarget"].push({
                        content: helpers.convertHtmlToText(content),
                        options_rewrite: [
                            ...Object.keys(jsonData).map((key) => ({
                                name: key,
                                value: "",
                                options: jsonData[key] || [],
                                isOpen: false,
                            })),
                            {
                                name: "keyword",
                                value: "",
                                isOpen: false,
                            },
                        ],
                    });

                    const formData = new FormData();
                    formData.append("id", formProject.id);
                    formData.append("title", formProject.title);
                    formData.append("content", content);

                    await updateProject(formData);
                }
                hiddenSocialMedia.value = true;
                messagesMap[key].isLoading = false;
                sections.forEach((item) => {
                    item.open = false;
                });
                await nextTick();
                if (resultTarget.value) {
                    resultTarget.value.scrollIntoView({ behavior: "smooth", block: "center", inline: "center" });
                }

                break;
            default:
                break;
        }
    } catch (error) {
        console.error("handleSubmitContentProductAd ~ error:", error);
    } finally {
        messagesMap[key].isLoading = false;
        isSending.value = false;
    }
};
const getFormContentMusic = async () => {
    try {
        const key = "music";
        messagesMap[key] = {
            overview: {
                isLoading: true,
            },
        };
        const result = await continueChat(
            key,
            JSON.stringify({
                currentStep: "buoc3",
                the_loai: analysisActive.value.name,
            })
        );
        if (result) {
            messagesMap[key] = {
                ...messagesMap[key],
                overview: {
                    formData: JSON.parse(result),
                    isLoading: false,
                },
            };
        }
    } catch (error) {
        console.error("Lỗi khi lấy form content product ad:", error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại.");
    } finally {
        isSending.value = false;
        messagesMap[key] = {
            overview: {
                isLoading: false,
            },
        };
    }
};
const toggleBigIdeaMusic = (index) => {
    messagesMap["music"].big_ideas.formData[index].isActive = !messagesMap["music"].big_ideas.formData[index]?.isActive;
    messagesMap["music"].big_ideas.formData.forEach((item, i) => {
        if (i !== index) {
            item.isActive = false;
        }
    });
};
const handleButtonSelectCreateContentNotAdvance = async (event) => {
    try {
        messagesMap.music = null;
        messagesMap.resultTarget = null;
        messagesMap.resultsAnalysis = null;
        messagesMap.content_product_ad = null;
        messagesMap.poetry = null;
        switch (analysisActive.value?.name) {
            case "Bài viết Quảng cáo sản phẩm":
                await getFormContentProductAd(false);
                break;
            case "Thơ Quảng cáo sản phẩm":
                await getFormPoetry(false);
                break;
            default:
                break;
        }
    } catch (error) {
    } finally {
    }
};

const handleButtonSelectCreateContentAdvance = async (event) => {
    try {
        isGenAIStatus.value = false;
        messagesMap.music = null;
        messagesMap.resultTarget = null;
        messagesMap.resultsAnalysis = null;
        messagesMap.content_product_ad = null;
        messagesMap.poetry = null;
        isLoadingSelectCreateContent.value = true;
        switch (analysisActive.value?.name) {
            case "Bài viết Quảng cáo sản phẩm":
                await getFormContentProductAd();
                break;
            case "Tạo Status Fanpage 1 tháng":
                await getFormContentProductAd();
                break;
            case "Lời nhạc Quảng cáo sản phẩm":
                await getFormContentProductAd();
                break;
            case "Thơ Quảng cáo sản phẩm":
                await getFormPoetry();
                break;
            default:
                break;
        }

        // Tạm sửa để demo, demo xong tìm nguyên nhân rồi fix sau
        isChoosedCategory.value = true;

        sections[4].open = false;
        sections[5].open = true;
    } catch (error) {
    } finally {
        isLoadingSelectCreateContent.value = false;
    }
};
const loadingPercent = ref(0);

const handleButtonSelectCreateContent = async (event) => {
    try {
        isGenAIStatus.value = false;
        loadingPercent.value = 0; // Initialize loading percentage
        // Function to simulate loading progress
        const simulateLoading = () => {
            if (loadingPercent.value < 99) {
                const increment = Math.floor(Math.random() * 8) + 5; // Random increment between 2-5%
                loadingPercent.value = Math.min(loadingPercent.value + increment, 99); // Cap at 99%
                setTimeout(simulateLoading, 3000); // Update every 10 seconds
            }
        };
        simulateLoading();
        messagesMap.music = null;
        messagesMap.resultTarget = null;
        messagesMap.resultsAnalysis = null;
        messagesMap.content_product_ad = null;
        messagesMap.poetry = null;
        isLoadingSelectCreateContent.value = true;
        switch (analysisActive.value?.name) {
            case "Bài viết Quảng cáo sản phẩm":
                // Tạm sửa để demo, demo xong tìm nguyên nhân rồi fix sau
                isChoosedCategory.value = true;

                // await getFormContentProductAd();
                await handleButtonSelectCreateContentNotAdvance();
                await handleSubmitMediaCampaign();
                break;
            case "Thơ Quảng cáo sản phẩm":
                // Tạm sửa để demo, demo xong tìm nguyên nhân rồi fix sau
                isChoosedCategory.value = true;

                await handleButtonSelectCreateContentNotAdvance();
                // await getFormPoetry();
                await handleSubmitPoetry();
                break;
            case "Chiến dịch Truyền thông":
                // Tạm sửa để demo, demo xong tìm nguyên nhân rồi fix sau
                if (isChoosedCategory.value) {
                    console.log(selectedProject.value.id);
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set("projectId", selectedProject.value.id);
                    window.location.replace(currentUrl.toString());
                    return;
                } else {
                    isChoosedCategory.value = true;
                }

                await getFormMediaCampaign();
                break;
            case "Phân tích thị trường":
                // Tạm sửa để demo, demo xong tìm nguyên nhân rồi fix sau
                isChoosedCategory.value = true;

                handleButtonClick("resultsAnalysis", event, target, 5);
                break;
            case "Tạo Status Fanpage 1 tháng":
                // Tạm sửa để demo, demo xong tìm nguyên nhân rồi fix sau
                isChoosedCategory.value = true;

                handleButtonClick("statusFanpage", event, target, 3);
                break;
            case "Lời nhạc Quảng cáo sản phẩm":
                // Tạm sửa để demo, demo xong tìm nguyên nhân rồi fix sau
                isChoosedCategory.value = true;
                console.log('ÂM NHẠC')
                await getFormContentMusic();
                break;
            default:
                break;
        }
    } catch (error) {
    } finally {
        isLoadingSelectCreateContent.value = false;
        loadingPercent.value = 100;
    }
};


const keyword = ref("");
const searchImages = ref([]);
const currentPage = ref(1);
const hasMore = ref(false);
const colorType = ref("imgColorTypeUndefined");

const canLoadImage = (url) => {
    return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => resolve(true);
        img.onerror = () => resolve(false);
        img.src = url;
    });
};

const promptBackground = ref("");
const genBackgroundProductImage = ref("");
const isLoadingSearchImage = ref(false);
const callSearchImage = async (targetIndex = 0) => {
    try {
        keyword.value = formProject.title || selectedProject?.value?.title;

        if (!keyword.value) {
            return;
        }

        console.log(keyword.value);

        isLoadingSearchImage.value = true;

        const response = await axios.get(route("ai-business.search-image"), {
            params: {
                keyword: keyword.value,
                color_type: colorType.value,
                page: currentPage.value,
                site: selectedProject.value.url || "",
            },
        });

        if (currentPage.value === 1) {
            searchImages.value = response.data.items;
        } else {
            searchImages.value = [...searchImages.value, ...response.data.items];
        }

        let savedCount = 0;
        let imageUrls = [];
        let productImage = null;
        let additionalImages = [];

        for (const img of searchImages.value) {
            const imageUrl = img.link;
            const canLoad = await canLoadImage(imageUrl);

            if (canLoad) {
                if (!productImage) {
                    productImage = imageUrl; // Lấy ảnh sản phẩm đầu tiên
                } else {
                    additionalImages.push(imageUrl);
                }

                savedCount++;
            }

            if (savedCount >= 5) break;
        }

        if (tempFormProject.value.image_product.url) {

            // Convert URL to File
            const proxyUrl = `/proxy-image?url=${encodeURIComponent(tempFormProject.value.image_product.url)}`;
            const response = await axios.get(proxyUrl, { responseType: "blob" });
            const blob = new Blob([response.data], { type: "image/jpeg" });
            const file = new File([blob], "image_product.jpg", { type: "image/jpeg" });
            tempFormProject.value.image_product.file = file;

            let combinedImage1, combinedImage2;
            // Ảnh sản phẩm gốc
            await saveGenerationResult(tempFormProject.value.image_product.url, "", "image", targetIndex);

            if (tempFormProject.value.model_product?.url) {
                // Model product URL exists, proceed with conversion to blob and image combining
                const proxyUrlModel = `/proxy-image?url=${encodeURIComponent(tempFormProject.value.model_product.url)}`;
                const responseModel = await axios.get(proxyUrlModel, { responseType: "blob" });
                const blobModel = new Blob([responseModel.data], { type: "image/jpeg" });
                const fileModel = new File([blobModel], "model_product.jpg", { type: "image/jpeg" });
                tempFormProject.value.model_product.file = fileModel;

                // Ảnh phối cảnh
                genBackgroundProductImage.value = await generatePromptPerspective(tempFormProject.value.image_product.file);
                await saveGenerationResult(genBackgroundProductImage.value, "", "image", targetIndex);

                // Ảnh combine từ AI
                combinedImage1 = await combineImages(tempFormProject.value.image_product.file, tempFormProject?.value?.model_product?.file);
                await saveGenerationResult(combinedImage1, "", "image", targetIndex);

                combinedImage2 = await combineImages(tempFormProject.value.image_product.file, tempFormProject?.value?.model_product?.file);
                await saveGenerationResult(combinedImage2, "", "image", targetIndex);
            } else {
                // Model product URL does not exist, generate perspective 3 times
                genBackgroundProductImage.value = await generatePromptPerspective(tempFormProject.value.image_product.file);
                await saveGenerationResult(genBackgroundProductImage.value, "", "image", targetIndex);
                combinedImage1 = await generatePromptPerspective(tempFormProject.value.image_product.file);
                await saveGenerationResult(combinedImage1, "", "image", targetIndex);
                combinedImage2 = await generatePromptPerspective(tempFormProject.value.image_product.file);
                await saveGenerationResult(combinedImage2, "", "image", targetIndex);
            }

            imageUrls = [
                tempFormProject.value.image_product.url,
                genBackgroundProductImage.value,
                combinedImage1,
                combinedImage2,
            ];
        } else {
            imageUrls = additionalImages.slice(0, 5);
            for (let i = 0; i < imageUrls.length; i++) {
                await saveGenerationResult(imageUrls[i], "", "image", targetIndex);
            }
        }

        await updateProject({
            id: formProject.id,
            title: formProject.title,
            content: formProject.content,
            image_urls: imageUrls,
        });
    } catch (e) {
        console.log(e);
    } finally {
        isLoading.value = false;
        isLoadingSearchImage.value = false;
    }
};

const generatePromptPerspective = async (imageFile) => {
    try {
        let formData = new FormData();
        formData.append("image_url", imageFile);

        const response = await axios.post(route("ai-business.generate-prompt-image"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        if (response.data.status === "success") {
            promptBackground.value = response.data.response;
            // Wait for generateAiBackground to return the URL
            return await generateAiBackground(); // It will return the URL from generateAiBackground
        } else {
            toast.error("Lỗi khi tạo phối cảnh: " + response.data.message);
            return null; // Return null if the response is not successful
        }
    } catch (error) {
        toast.error("Lỗi khi gọi API tạo phối cảnh!");
        return null; // Return null on error
    }
};

const generateAiBackground = async () => {
    try {
        if (!promptBackground.value || !tempFormProject.value.image_product.file) {
            toast.error("Thiếu prompt hoặc ảnh sản phẩm!");
            return null; // Return null if missing prompt or image
        }

        let formData = new FormData();
        formData.append("prompt_background", promptBackground.value);
        formData.append("imageFile", tempFormProject.value.image_product.file);

        const response = await axios.post(route("ai-background.generate-ai-background-v2"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        if (response.data.s3_url) {
            // genBackgroundProductImage.value = response.data.s3_url;
            return response.data.s3_url; // Return the URL if successful
        } else {
            toast.error("Lỗi khi tạo nền AI: " + response.data.message);
            return null; // Return null if the URL is not available
        }
    } catch (error) {
        toast.error("Lỗi khi gọi API tạo nền AI!");
        return null; // Return null on error
    }
};

const showImageGenerate = ref(false);
const autoCreateImage = () => {
    if (messagesMap["resultTarget"][0].images.length > 0) {
        toast.info("Vui lòng xoá tất cả ảnh cũ trước khi tạo lại.");
        return;
    }

    if (showImageGenerate.value) {
        return;
    }
    showImageGenerate.value = !showImageGenerate.value;
    if (showImageGenerate.value) {
        createImage();
    }
};
const prompt = ref([
    "Hãy tạo bức ảnh minh họa truyền tải cảm xúc sâu sắc từ bài viết, Ảnh chỉ dùng để hiển thị sản phẩm, không thêm chữ vào ảnh, giúp người xem không chỉ nhìn thấy sản phẩm/dịch vụ mà còn cảm nhận được giá trị tinh thần, lợi ích cảm xúc khi sở hữu nó.  - Chủ đề chính của ảnh: Không tập trung vào sản phẩm, mà khai thác trải nghiệm, cảm giác, giá trị tinh thần mà sản phẩm mang lại.  Hãy đi từ Đối tượng tiềm năng của sản phẩm - sau đó mô tả cảm giác của \"đối tượng\" đó khi sở hữu hoặc sử dụng sản phẩm. Ví dụ:  + Nếu là bất động sản → Cảm giác bình yên, an cư, gắn kết gia đình, sự thành công  + Nếu là sản phẩm công nghệ → Cảm giác hiện đại, tiện lợi, an tâm, kiểm soát tốt hơn cuộc sống + Nếu là sản phẩm sức khỏe → Cảm giác hạnh phúc, tràn đầy năng lượng, an tâm cho gia đình  - Bối cảnh phù hợp với cảm xúc: (VD: ngôi nhà ấm áp với gia đình hạnh phúc, doanh nhân thành đạt tự tin, một người thư giãn tận hưởng thành quả...) - Đảm bảo rằng Không thêm bất kỳ ký tự nào vào ảnh theo bất cứ trường hợp nào 🔥 Hãy đảm bảo hình ảnh không chỉ minh họa sản phẩm/dịch vụ, mà còn đánh trúng vào cảm xúc, giá trị tinh thần mà khách hàng sẽ có được khi sở hữu nó.  Hình ảnh phải trông thực tế nhất có thể",

    "Hãy tạo bức ảnh minh họa trực quan, chủ đề dựa trên Nội dung chính của bài viết, Ảnh chỉ dùng để hiển thị sản phẩm, không thêm chữ vào ảnh. - Đối tượng chính của ảnh: Khai thác từ phần [Nội dung bài viết] - Các yếu tố quan trọng cần minh họa: Hãy tìm kiếm các yếu tố liên quan tới Sản Phẩm / Dịch vụ mà bài viết quảng cáo. Hoặc nếu là bài viết không tập trung vào Sản phẩm/Dịch vụ thì tìm kiếm các yếu tố xung quanh bài viết. - Cảm xúc cần thể hiện: Cần truyền tải được [Cảm xúc mong muốn truyền tải] - Phong cách hình ảnh: Cần truyền tải được [Phong cách viết] - Đảm bảo rằng Không thêm bất kỳ ký tự nào vào ảnh theo bất cứ trường hợp nào - Tông màu và hiệu ứng phù hợp với nội dung: (VD: màu sắc thiên nhiên nếu sản phẩm là hữu cơ, màu sắc công nghệ nếu sản phẩm số...) Hãy đảm bảo mỗi ảnh thể hiện một khía cạnh khác nhau của bài viết, tạo nên sự đồng nhất nhưng không lặp lại. Hình ảnh phải trông thực tế nhất có thể",

    "Hãy tạo bức ảnh minh họa giúp truyền tải mục tiêu chính của bài viết, Ảnh chỉ dùng để hiển thị sản phẩm, không thêm chữ vào ảnh. Sử dụng [Nội dung bài viết] để nắm được đối tượng của bức ảnh. Nhưng Chủ đề Chính sẽ khai thác từ phần [Mục tiêu bài viết] (VD: tăng nhận diện thương hiệu, kích thích mua hàng, xây dựng niềm tin, tạo sự tương tác...) Dựa trên Chủ đề Chính sẽ tạo ra 1 bức ảnh tập trung vào yếu tố mục tiêu bài viết. Và Cảm xúc cần thể hiện: Cần truyền tải được [Cảm xúc mong muốn truyền tải] mà người dùng đã điền. - Đảm bảo rằng Không thêm bất kỳ ký tự nào vào ảnh theo bất cứ trường hợp nào Hãy đảm bảo hình ảnh thể hiện kết quả rõ ràng, giúp người xem dễ dàng cảm nhận được mục tiêu của bài viết. Hình ảnh phải trông thực tế nhất có thể",

    "Hãy tạo bức ảnh với  biểu tượng mạnh mẽ, không minh họa trực tiếp sản phẩm hay nội dung bài viết, Ảnh chỉ dùng để hiển thị sản phẩm, không thêm chữ vào ảnh, mà tập trung vào 1 key cụ thể về giá trị cốt lõi và thông điệp sâu xa của bài viết. - Key chính của bài viết: (Không phải sản phẩm, mà là ý nghĩa sâu xa mà sản phẩm mang lại. VD: \"Thành công\", \"Cân bằng\", \"Hồi sinh\", \"Sự bảo vệ\", \"Phát triển bền vững\"...)  - Hình ảnh biểu tượng để minh họa Key chính: (VD: Kim tự tháp thể hiện sự bền vững, Ngọn lửa tượng trưng cho đam mê, Dòng sông chảy mãi đại diện cho sự phát triển...) - Đảm bảo rằng Không thêm bất kỳ ký tự nào vào ảnh theo bất cứ trường hợp nào Hãy đảm bảo hình ảnh thể hiện kết quả rõ ràng, giúp người xem dễ dàng hiểu được tác động của sản phẩm/dịch vụ. Chú ý hình ảnh thực sự đơn giản, có tính biểu tượng cao, dễ nhớ, gây tò mò và thể hiện rõ thông điệp sâu xa của bài viết. Hình ảnh phải trông thực tế nhất có thể",
]);
const getReplacedPrompts = () => {
    const formData = messagesMap["content_product_ad"]?.formData || messagesMap["poetry"]?.formData;

    return prompt.value.map((p) => {
        let updatedPrompt = p;

        const matches = [...p.matchAll(/\[([^\]]+)\]/g)];

        matches.forEach((match) => {
            const label = match[1];
            const field = formData.find(item => item.label.trim() === label.trim());

            if (field) {
                updatedPrompt = updatedPrompt.replaceAll(`[${label}]`, field.value || '');
            }
        });

        return updatedPrompt;
    });
};

const createImage = async () => {
    const promises = [];
    const prompts = getReplacedPrompts();
    const content = messagesMap["resultTarget"][0].content;

    // const response = await axios.post(route("ai-business.summarize-content"), {
    //     content: messagesMap["resultTarget"][0].content,
    //     project_name: formProject.title,
    // });
    for (let index = 0; index < 4; index++) {
        let descriptionToSend = `${content} ${prompts[index]}`;
        promises.push(callGenerateImage(descriptionToSend, 0));
    }
    Promise.all(promises)
        .then(() => {
            autoCreaeImageLoading.value = false;
            if(isLoadingVideo.value) {
                showVideoGenerate.value = false;
                autoCreateVideo()
            }
        })
        .finally(() => {
            showImageGenerate.value = false;
        });
};

const showVideoGenerate = ref(false);

const handleButtonSelectContentStrategy = (event) => {
    // sections[3].open = false;
    // sections[4].open = true;
    isLoadingAnalysis.value = true;
    handleButtonClick("analysis", event, category, 4);
};

const isLoadingAnalysis = ref(false);
const ideaLoading = ref(false);
const eventLoading = ref(false);
const categoriesLoading = ref(false);
const finalTargetLoading = ref(false);
const isLoadingSelectCreateContent = ref(false);
const handleButtonClick = async (key, e, ref = null, sectionIndex = null, type = null, content) => {
    try {
        if (content == null || content == undefined || content == "") {
            content = messagesMap["information_project"];
        }
        if (isSending.value || !key || (messagesMap[key]?.content && key != "result")) return;
        isStructureOpen.value = false;
        if (sectionIndex && ref && type == null) {
            sections[sectionIndex - 1].open = false;
            sections[sectionIndex].open = true;

            await nextTick();

            if (ref?.scrollIntoView) {
                ref.scrollIntoView({ behavior: "smooth" });
            } else {
                console.warn("Target reference not found or not scrollable.");
            }
        }
        let query = null;
        let value = null;
        switch (key) {
            case "analysis":
                isLoadingAnalysis.value = true;
                query = JSON.stringify({
                    currentStep: `buoc3`,
                    currentSubStep: `buoc3_2`,
                    muc_tieu: selectedContentStrategy.value,
                    mode: mode.value,
                });
                console.log(query);
                await continueChat("analysis", query);
                break;
            case "statusFanpage":
                isLoadingAnalysis.value = true;
                query = JSON.stringify({
                    currentStep: `buoc3`,
                    the_loai: analysisActive.value?.name,
                });
                console.log(query);
                await continueChat("statusFanpage", query);
                break;
            case "analysisBusiness":
                isLoadingAnalysis.value = true;
                query = JSON.stringify({
                    currentStep: `buoc2`,
                    content: content,
                    mode: mode.value,
                });
                await continueChat("analysisBusiness", query);
                break;
            case "resultsAnalysis":
                toggleItemResultAnalysis(0);
                break;
            case "ideas":
                ideaLoading.value = true;
                await continueChat("ideas", "3A", {
                    text: messagesMap["analysis"].map((item) => `<H1>${item.title}</H1>: ${item.content}`).join("\n"),
                });
                break;
            case "event":
                eventLoading.value = true;
                if (!messagesMap["event"]?.trim() || messagesMap["event"]?.trim().length === 0) {
                    toast.error("Vui lòng nhập sự kiện bạn muốn chạy chiến dịch quảng cáo sản phẩm này.");
                } else {
                    await continueChat("big_ideas", "3B", {
                        su_kien: messagesMap["event"],
                    });
                }
                break;
            case "categories":
                categoriesLoading.value = true;
                const big_ideas_active = messagesMap["big_ideas"]?.find((item) => item.isActive === true);
                if (!big_ideas_active) {
                    toast.error("Vui lòng chọn ý tưởng.");
                } else {
                    await continueChat("categories", "4", {
                        y_tuong: `**${big_ideas_active.y_tuong}**${big_ideas_active.content}`,
                    });
                }
                break;
            case "finalTarget":
                finalTargetLoading.value = true;
                const categories_active = messagesMap["categories"]?.find((item) => item.isActive === true);
                if (categories_active?.subName) {
                    const subCategories_active = categories_active?.subName?.find((item) => item.isActive === true);
                    if (!subCategories_active) {
                        toast.error("Vui lòng chọn thể loại.");
                    } else {
                        await continueChat("finalTarget", "5", {
                            the_loai: `${subCategories_active?.name || ""}`,
                        });
                    }
                } else {
                    if (!categories_active) {
                        toast.error("Vui lòng chọn thể loại.");
                    } else {
                        await continueChat("finalTarget", "5", {
                            the_loai: `${categories_active?.name || ""}`,
                        });
                    }
                }
                break;
            case "resultTarget":
                resultTargetLoading.value = true;
                const finalTarget_active = messagesMap["finalTarget"].filter((item) => item.isActive === true);
                if (finalTarget_active.length === 0) {
                    toast.error("Vui lòng chọn ít nhất 1 mục tiêu.");
                } else {
                    await continueChat("resultTarget", "6", {
                        muc_tieu: finalTarget_active.map((item) => item.name),
                    });
                    sections[5].open = true;
                    if (ref) {
                        ref.scrollIntoView({ behavior: "smooth" });
                    }
                }
                break;
            case "result":
                const itemReuslt = messagesMap["resultTarget"].findIndex((item) => item.resultTargetLoading === true);
                await continueChat("result", "7", {
                    text: messagesMap["resultTarget"][itemReuslt]?.content || "",
                    edit: messagesMap["resultTarget"][itemReuslt]?.selectedStyle || messagesMap["resultTarget"][itemReuslt]?.selectedEditStyle || "",
                });
                break;
            default:
                break;
        }
    } catch (error) {
        console.error("Error when handleButtonClick:", error);
        toast.error("Xin vui lòng bấm lại để nhận kết quả.");
    } finally {
        isLoadingAnalysis.value = false;
        categoryLoading.value = false;
        targetLoading.value = false;
        informationLoading.value = false;
        resultTargetLoading.value = false;
        ideaLoading.value = false;
        eventLoading.value = false;
        categoriesLoading.value = false;
        finalTargetLoading.value = false;
        isSending.value = false;
    }
};
let showBuyCreditPopup = ref(false);

const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};

watch(() => props.project, (newValue) => {
    if (newValue) {
        selectProject(newValue);
    }
}, { deep: true, immediate: true });

const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append("model", "suggestBusiness");
        formData.append("type", "suggestBusiness");
        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
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

const isLoading = ref(false);

let allowedExtension = ["image/jpeg", "image/jpg", "image/png", "video/mp4"];

const resetVideoUpload = (index) => {
    messagesMap["resultTarget"][index].videoRef = null;
    messagesMap["resultTarget"][index].previewVideoUpload = null;
    messagesMap["resultTarget"][index].fileVideoUpload = null;
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

const handleImageStyle = (file, index) => {
    if (allowedExtension.indexOf(file.type) < 0) {
        toast.error("Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg");
        return;
    }

    if (!validateLimitImage(index)) {
        return;
    }

    if (file) {
        let imagePromise = new Promise(function (resolve) {
            const reader = new FileReader();
            reader.onload = () => {
                let objectURL = URL.createObjectURL(file);
                resolve(objectURL);
            };
            reader.readAsDataURL(file);
        });

        imagePromise.then(function (value) {
            let image = {
                previewImageUpload: value,
                fileImageUpload: file,
            };
            messagesMap["resultTarget"][index].images.push(image);
            selectImage(image);
        });
    }
};

const handleVideoStyle = (file, index) => {
    showVideoGenerate.value = true;
    resetVideoUpload(index);
    if (allowedExtension.indexOf(file.type) < 0) {
        toast.error("Vui lòng chọn lại video có định dạng .mp4");
        return;
    }

    if (file) {
        let videoPromise = new Promise(function (resolve) {
            const reader = new FileReader();
            reader.onload = () => {
                let objectURL = URL.createObjectURL(file);
                resolve(objectURL);
            };
            reader.readAsDataURL(file);
        });

        videoPromise.then(function (value) {
            messagesMap["resultTarget"][index].previewVideoUpload = value;
            messagesMap["resultTarget"][index].videoRef = null;
            messagesMap["resultTarget"][index].fileVideoUpload = file;

            selectVideo(messagesMap["resultTarget"][index]);
        });
    }
};

    const isGenAIStatus = ref(false);
    function openPostDetail(content, index, onlyAutoPostAi = false) {
        isGenAIStatus.value = onlyAutoPostAi
        let item = null
        if (!onlyAutoPostAi) {
            item = messagesMap['resultTarget'][index];
        } else {
            sections[2].open = false;
        }
        let plainTextContent = helpers.convertHtmlToText(content);
        let prompt_autopost = helpers.convertHtmlToText(formProject?.data_json?.information_project?.answer);

        if (postFormRef.value) {
            stepPostFormRef.value.scrollIntoView({ behavior: "smooth" });

            let targetValue = "Quảng Bá Sản Phẩm/Dịch Vụ";
            try {
                targetValue = getTarget() || targetValue;
            } catch (e) {
                console.warn("Lỗi khi lấy target từ getTarget:", e);
            }

            let postForm = {
                content: plainTextContent,
                published: 1,
                scheduled_publish_time: null,
                files: [],
                autoPostAi: {
                    description: prompt_autopost,
                    category: analysisActive.value.name,
                    project_name: selectedProject.value.title + ' ' + selectedProject.value.description,
                    target: targetValue,
                },
                onlyAutoPostAi: onlyAutoPostAi
            };

            item?.images?.forEach((image) => {
                if (checkImage(image)) {
                    if (image?.fileImageUpload) {
                        postForm.files.push(image.fileImageUpload);
                    } else if (image?.imageRef) {
                        postForm.files.push({
                            s3_url: image.imageRef.s3_url,
                            type: "image",
                        });
                    }
                }
            });

            if (!onlyAutoPostAi && checkVideo(item)) {
                const video = item.fileVideoUpload;
                const videoRef = item.videoRef;
                if (video) {
                    postForm.files = [video];
                } else if (videoRef) {
                    postForm.files = [
                        {
                            s3_url: videoRef.s3_url,
                            type: "video",
                        },
                    ];
                }
            }
            postFormRef.value.openPostDetail(postForm);
        };
    }

    const getTarget = () => {
        if(analysisActive.value.name !== 'Chiến dịch Truyền thông')
            return '';

        const key = 'content_product_ad';
        const activeOptions = messagesMap[key].formData[0].options
            .filter((option) => option.isActive)
            .map((option) => option.value);

        return activeOptions[0]
    };

const beforeSubmitPostForm = () => {};

const onSuccessPostForm = () => {};

const scrollToCreateFormRef = async (index) => {
    handleActiveFinalTarget(index);
    toggleGenerateImage("video", index);
};

// Tạo key cho localStorage dựa trên user ID và project ID
const getStorageKey = (userId, projectId) => {
    return `multi_form_data_${userId}_${projectId}`;
};

// Lấy tất cả data từ localStorage
const getAllFromStorage = () => {
    try {
        const userId = page.props.auth?.user?.id;
        const projectId = selectedProject.value?.id;
        if (!userId || !projectId) return [];

        const storageKey = getStorageKey(userId, projectId);
        const data = localStorage.getItem(storageKey);
        return data ? JSON.parse(data) : [];
    } catch (err) {
        console.error("Error reading from localStorage:", err);
        return [];
    }
};

// 2. Sửa lại fillFormData để check kỹ hơn
const fillFormData = async () => {
    await new Promise((resolve) => setTimeout(resolve, 500));

    const allData = getAllFromStorage();
    if (allData && allData.length > 0) {
        let attempts = 0;
        const maxAttempts = 5;

        const tryFillForm = async () => {
            const allForms = document.querySelectorAll("form");
            if (!allForms || allForms.length === 0) {
                if (attempts < maxAttempts) {
                    attempts++;
                    await new Promise((resolve) => setTimeout(resolve, 500));
                    await tryFillForm();
                }
                return;
            }

            // Duyệt qua tất cả data trong allData
            for (const savedData of allData) {
                if (!savedData?.formData) continue;

                const formDataKeys = Object.keys(savedData.formData);
                const targetForm = Array.from(allForms).find((form) => {
                    return formDataKeys.some((key) => form.querySelector(`[name="${key}"]`));
                });

                if (targetForm) {
                    Object.entries(savedData.formData).forEach(([key, value]) => {
                        const input = targetForm.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.value = value;
                            ["input", "change"].forEach((eventName) => {
                                input.dispatchEvent(
                                    new Event(eventName, {
                                        bubbles: true,
                                        cancelable: true,
                                    })
                                );
                            });
                        }
                    });
                }
            }
        };

        await tryFillForm();
    }
};
// 4. Sửa lại onUpdated
onUpdated(() => {
    if (messagesMap["info"]?.content) {
        fillFormData();
    }
});


const message = ref("Bạn có chắc chắn tạo video từ ảnh bên trên không?")
const audioURL = ref(null);
const taskId = ref(null);
const videoUrl = ref(null);
const isCallApi = ref(false);
const isLoadingVideo = ref(false);
const showConfirmVideo = ref(false)
const isConfirm = ref(false)

const confirmVideo = () => {
    isConfirm.value = true
    showConfirmVideo.value = false
    autoCreateVideo()
}

const createVideoRunway = async (video_des, audioUrl, imageUrl, imageUrl2, is_credit = 0) => {
    const formData = new FormData();
    formData.append("duration", 5);
    formData.append("number_result", 5);
    formData.append("number", 16);
    formData.append("resolution", "9:16");
    formData.append("is_credit", is_credit);
    formData.append("s3_image_url", imageUrl);
    formData.append("audio_url", audioUrl);
    formData.append("s3_image2_url", imageUrl2);
    try {
        const response = await axios.post(route("short-video.create-video-ai"), formData, {
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

const createVideoPre = async (video_des, audioUrl, imageUrl, imageUrl2, is_credit = false) => {
    const formData = new FormData();
    formData.append("duration", 5);
    formData.append("number_result", 5);
    formData.append("number", 16);
    formData.append("resolution", "9:16");
    formData.append("is_credit", is_credit);
    formData.append("start_image_url", imageUrl);
    formData.append("audio_url", audioUrl);
    formData.append("end_image_url", imageUrl2);
    try {
        const response = await axios.post(route("short-video.create-video-prediction"), formData, {
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

const textToSpeechGoogle = async (video_des, duration) => {
   try {
        const response = await axios.post(route("short-video.getAudioDes"), {
            video_des: video_des,
            duration:duration
        });
        const result = await axios.post(route("ai-audio.text-to-speech-google"), {
            text: response.data.audio_des,
            duration: duration,
        });
        if (result.data.success) {
            return result.data.data;
        }
    } catch (error) {
       return "";
    }
};

const createVideoScene = async (imageUrl, imageUrl2, video_des, is_credit = 0) => {
    var audioUrl = await textToSpeechGoogle(video_des, 5);
    if (!audioUrl) {
        return false;
    }
    var video = await createVideoRunway(video_des, audioUrl, imageUrl, imageUrl2, is_credit)

    if(!video) {
       video = await createVideoPre(video_des, audioUrl, imageUrl, imageUrl2, is_credit)
    }
    return video
}

const percenLoadingVideo = ref(0)
const isMergeVideo = ref(false)
const isLoadingPercent = ref(false)

const getImageBlobViaCanvas = (url) => {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';

        img.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);

            canvas.toBlob((blob) => {
                if (blob) {
                    resolve(blob);
                } else {
                    reject(new Error("Không thể chuyển ảnh sang blob"));
                }
            }, 'image/jpeg');
        };

        img.onerror = () => {
            reject(new Error("Không thể tải ảnh qua canvas"));
        };

        img.src = url;
    });
};

const getFileFromImageURL = async (url, index = 0) => {
    const proxyUrl = `/proxy-image?url=${encodeURIComponent(url)}`;

    try {
        const response = await axios.get(proxyUrl, { responseType: 'blob' });
        const blob = response.data;
        const fileName = url.split('/').pop().split('?')[0] || `image-${index}.jpg`;
        return new File([blob], fileName, { type: blob.type });
    } catch (err) {
        console.warn(`Proxy failed for ${url}, try canvas fallback`);
        try {
            const blob = await getImageBlobViaCanvas(url);
            const fileName = `image-${index}.jpg`;
            return new File([blob], fileName, { type: blob.type || 'image/jpeg' });
        } catch (err2) {
            console.error("❌ Không thể xử lý ảnh qua proxy/canvas:", url);
            return null;
        }
    }
};

const autoCreateVideo = async () => {
    if(!isConfirm.value) {
        showConfirmVideo.value = true;
        return
    }
    var video_des = messagesMap["resultTarget"][0].content
    if (showVideoGenerate.value || autoCreaeImageLoading.value) {
        return;
    }
    isLoadingVideo.value = true
    showVideoGenerate.value = true;
    percenLoadingVideo.value = 0
    isMergeVideo.value = false
    var images = messagesMap["resultTarget"][0]["images"]
    var imageVideos = []
    resetVideoUpload(0);
    if(images.length == 0) {
        await createImage()
        return
    } else {
        for (let i = 0; i < images.length; i++) {
            let file = images[i].fileImageUpload;

            if (!file && images[i].imageRef?.s3_url) {
                file = await getFileFromImageURL(images[i].imageRef.s3_url, i);
            }

            if (file) {
                const s3_url = await getS3URL(file);
                if (s3_url) {
                    imageVideos.push(s3_url);
                } else {
                    console.warn(`❌ Upload S3 thất bại cho ảnh ${i}`);
                }
            } else {
                console.warn(`❌ Bỏ qua ảnh ${i} vì không thể xử lý`);
            }
        }
    }

    var duration = 5
    if(imageVideos.length > 2) {
        isMergeVideo.value = true
        duration = 10
    }
    const hasEnoughCredit = await checkCreditVideo(duration);
    if (!hasEnoughCredit) {
        isLoadingVideo.value = false
        showVideoGenerate.value = false;
        return; // Dừng nếu không đủ credit
    }
    isLoadingPercent.value = true
    var image2 = ""
    var video2 = ""
    if(imageVideos.length > 1) {
        image2 = imageVideos[1]
    }
    var is_credit = 0
    if(imageVideos.length <= 2) {
        is_credit = 1
    }
    var video1 = await createVideoScene(imageVideos[0], image2, video_des, is_credit)
    if(!video1) {
        toast.error("Có lỗi xảy ra trong quá trình tạo video");
        isLoadingVideo.value = false
        showVideoGenerate.value = false;
        return
    }
    if(imageVideos.length > 2) {
        image2 = ""
        if(imageVideos.length >= 4) {
            image2 = imageVideos[3]
        }
        video2 = await createVideoScene(imageVideos[2], image2, video_des, is_credit)
        if(!video2) {
            toast.error("Có lỗi xảy ra trong quá trình tạo video");
            isLoadingVideo.value = false
            showVideoGenerate.value = false;
            return
        }
    }

    if(video2) {
        var audioUrl = await textToSpeechGoogle(video_des, 10);
        if (!audioUrl) {
            toast.error("Có lỗi khi tạo âm thanh");
            isLoadingVideo.value = false
            showVideoGenerate.value = false;
            return false;
        }
        var videoMerge = await mergeVideo(video1, video2, audioUrl)
        if(percenLoadingVideo.value < 99) {
            for(var i = percenLoadingVideo.value; i < 100; i++) {
                if(percenLoadingVideo.value < 99) {
                    await sleep(500)
                    percenLoadingVideo.value = percenLoadingVideo.value + 1
                }
            }
        }
        handleSaveGenerationResultVideo(videoMerge, 0);
    } else {
        if(percenLoadingVideo.value < 99) {
            for(var i = percenLoadingVideo.value; i < 100; i++) {
                if(percenLoadingVideo.value < 99) {
                    await sleep(500)
                    percenLoadingVideo.value = percenLoadingVideo.value + 1
                }
            }
        }
        handleSaveGenerationResultVideo(video1, 0);
    }
    isLoadingVideo.value = false
    showVideoGenerate.value = false;
    isConfirm.value = false
    percenLoadingVideo.value = 0
    isLoadingPercent.value = false
};
var count = 0
setInterval(async () => {
    if(isLoadingPercent.value) {
        count = count + 1
        if(isMergeVideo.value) {
            if(count == 3) {
                count = 0
                percenLoadingVideo.value =  percenLoadingVideo.value + 1
            }
        } else {
            percenLoadingVideo.value =  percenLoadingVideo.value + 1
        }

        if(percenLoadingVideo.value > 99) {
            percenLoadingVideo.value = 99
        }
    }
}, 400)

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

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

const mergeVideo = async(video1, video2, audio_url) => {
    const formData = new FormData();
    formData.append("video1_url", video1);
    formData.append("video2_url", video2);
    formData.append("audio_url", audio_url);
    try {
        const response = await axios.post(route("short-video.merge-video-auto"), formData, {
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

const checkCreditVideo = async (duration = 5) => {
    try {
        const formData = new FormData();
        formData.append('type', 'gen3-alphaturbo');
        formData.append('number_result', duration);
        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true;
            if (response.data.affCode == "aff_trial") {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này.";
                acceptUpgrade.value = false;
            }
            return false;
        } else {
            const currentCredit = pageProps.auth.user.credit || 0;
            const additionalCredit = response?.data?.data?.total_price - currentCredit;
            eventBus.emit("popup-buy-credit", { currentCredit, additionalCredit, showBuyCreditPopup: true });
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

const createAIImage = async (video_des) => {
    try {
        const response = await axios.post(route("ai-image.generate"), {
            description: video_des,
            style: "",
            artist: "",
            height: 1024,
            width: 1024,
            model: "Flux Schnell",
            aspect_ratio: "16:9",
        });

        if (response.status === 200 && response.data.url) {
            return response.data.url;
        } else {
            return "";
        }
    } catch (error) {
        return "";
    }
};

const handleSaveGenerationResult = (response, index) => {
    try {
        saveGenerationResult(response?.data, messagesMap["resultTarget"][index]?.content, "image", index);
    } catch (err) {
        console.error("handleSaveGenerationResult", err);
        return null;
    }
};
const handleSaveGenerationResultVideo = (response, index) => {
    try {
        saveGenerationResult(response, messagesMap["resultTarget"][index]?.content, "video", index);
    } catch (err) {
        console.error("handleSaveGenerationResult", err);
        return null;
    }
};

const handleSaveGenerationResultMusic = (song, index) => {
    try {
        saveGenerationResult(song, messagesMap["resultTarget"][index]?.content, "music", index);
    } catch (err) {
        console.error("handleSaveGenerationResultMusic", err);
        return null;
    }
};

const saveGenerationResult = async (dataParam, content, type = "image", index) => {
    const url = dataParam.url || dataParam.generate_file?.s3_url || dataParam;
    try {
        if (type == "video") {
            videoUrl.value = dataParam;
            if (videoUrl.value) {
                handleVideoUrl(videoUrl.value, index);
            }
        }

        if (type == "image") {
            if (!messagesMap["resultTarget"][index].images) {
                messagesMap["resultTarget"][index].images = [];
            }

            if (!validateLimitImage(index)) {
                return;
            }

            if (url != "") {
                let image = {
                    imageRef: {
                        s3_url: url,
                    },
                    previewImageUpload: null,
                    fileImageUpload: null,
                };
                messagesMap["resultTarget"][index].images.push(image);
                selectImage(image);
            }
        }
        if (type != "music") {
            targetActiveIndex.value = -1;
            resultTarget.value.scrollIntoView({ behavior: "smooth" });
        }
    } catch (error) {
        console.error("Lỗi khi lưu kết quả:", error);
        toast.error("Không thể lưu kết quả. Vui lòng thử lại sau.");
        throw error;
    }
};

const handleImageUpload = (e, index) => {
    const file = e.target.files[0];
    if (!file) return;
    handleFileChange(e, index);
};

const continueChatStreaming = async (key, query, index = 0) => {
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        return; // Dừng nếu không đủ credit
    }
    if (mode.value == "" || mode.value == null) {
        toast.error("Vui lòng chọn chế độ để sử dụng tính năng.");
        return;
    }
    if (!key == "resultsAnalysis") {
        console.log(key);
        if (isSending.value || !key) return;
    }

    isSending.value = true;
    let retryCount = 0;
    const MAX_RETRIES = 5;

    const executeRequest = async () => {
        try {
            const payload = {
                inputs: {},
                query: query,
                conversation_id: conversationId.value,
                project_id: selectedProject.value.id
            };

            if (selectedOption.value) {
                payload.inputs.chude = selectedOption.value;
            }

            // Khởi tạo giá trị ban đầu cho text box dựa vào key
            switch (key) {
                case "analysis":
                    messagesMap[key] = [
                        {
                            name: "Đang phân tích...",
                            isActive: false,
                        },
                    ];
                    break;
                case "finalTarget":
                    messagesMap[key] = [
                        {
                            name: "Đang xử lý...",
                        },
                    ];
                    break;
                case "result":
                    const itemResult = messagesMap["resultTarget"].findIndex((item) => item.resultTargetLoading === true);
                    if (itemResult !== -1) {
                        messagesMap["resultTarget"][itemResult].content = "Đang tạo nội dung...";
                    }
                    break;
                case "resultsAnalysis":
                    if (!messagesMap[key]) {
                        messagesMap[key] = resultsAnalysisSample;
                    }
                    messagesMap[key][index].content = "Đang phân tích...";
                    break;
                default:
                    messagesMap[key] = "Đang xử lý...";
                    break;
            }

            const response = await fetch(route("ai-business.sendChatStreaming"), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
                body: JSON.stringify(payload),
            });

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let fullAnswer = "";
            let buffer = ""; // Buffer để tích lũy JSON chưa hoàn chỉnh

            while (true) {
                const { value, done } = await reader.read();
                if (done) break;

                const chunk = decoder.decode(value);
                buffer += chunk;

                // Tìm các dòng hoàn chỉnh trong buffer
                while (true) {
                    const newlineIndex = buffer.indexOf("\n");
                    if (newlineIndex === -1) break; // Không còn dòng hoàn chỉnh

                    const line = buffer.slice(0, newlineIndex);
                    buffer = buffer.slice(newlineIndex + 1);

                    if (line.startsWith("data: ")) {
                        try {
                            const data = JSON.parse(line.slice(6));
                            if (data.answer) {
                                fullAnswer += data.answer;
                                // Cập nhật UI dựa trên loại key
                                switch (key) {
                                    case "analysis":
                                    case "finalTarget":
                                        // Chỉ cập nhật khi có dòng hoàn chỉnh
                                        const lines = fullAnswer.split("\n").filter((item) => item.trim());
                                        if (lines.length > 0) {
                                            messagesMap[key] = lines.map((item) => ({
                                                name: item,
                                                isActive: false,
                                            }));
                                        }
                                        break;
                                    case "result":
                                        const itemResult = messagesMap["resultTarget"].findIndex((item) => item.resultTargetLoading === true);
                                        if (itemResult !== -1) {
                                            messagesMap["resultTarget"][itemResult].content = fullAnswer;
                                        }
                                        break;
                                    case "resultsAnalysis":
                                        if (messagesMap[key]?.[index]) {
                                            messagesMap[key][index].content = fullAnswer;
                                        }
                                        break;
                                    case "categories":
                                        // Chỉ format categories khi có đủ dữ liệu
                                        if (fullAnswer.includes("\n")) {
                                            messagesMap[key] = formatCategories(fullAnswer);
                                        }
                                        break;
                                    case "information_project":
                                        if (typeof messagesMap[key] === "string") {
                                            messagesMap[key] = fullAnswer;
                                            // sunEditorInstance.value.setContents(marked(fullAnswer));
                                        }
                                        break;
                                    default:
                                        if (typeof messagesMap[key] === "string") {
                                            messagesMap[key] = fullAnswer;
                                        } else {
                                            // Chỉ parse JSON khi nhận được dấu } cuối cùng
                                            if (fullAnswer.trim().endsWith("}")) {
                                                try {
                                                    const fixedJson = fixJsonString(fullAnswer);
                                                    const parsedData = JSON.parse(fixedJson);
                                                    messagesMap[key] = parsedData;
                                                } catch (e) {
                                                    // Nếu chưa phải JSON hoàn chỉnh, giữ nguyên text
                                                    messagesMap[key] = fullAnswer;
                                                }
                                            } else {
                                                // Hiển thị text đang stream
                                                messagesMap[key] = fullAnswer;
                                            }
                                        }
                                        break;
                                }
                            }
                            if (data.event === "workflow_started" && data.conversation_id) {
                                conversationId.value = data.conversation_id;
                            }
                        } catch (e) {
                            // Bỏ qua lỗi parse JSON không hoàn chỉnh
                            continue;
                        }
                    }
                }
            }

            return fullAnswer;
        } catch (error) {
            console.error("Error starting conversation:", error);
            toast.error("Có lỗi xảy ra, vui lòng thử lại.");
        } finally {
            loading.value = false;
            isSending.value = false;
        }
    };

    let success = false;
    while (!success && retryCount < MAX_RETRIES) {
        success = await executeRequest();
        return success;
    }
    if (!success) {
        throw new Error("Xin vui lòng bấm lại để nhận kết quả");
    }
};

const callGenerateImage = async (finalDescription, index, models) => {
    let attempts = 0;
    const maxRetries = 3;

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
            handleSaveGenerationResult(response, index);
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

const gradeResult = ref("");
const gradeLoading = ref(false);
const improveResult = ref("");
const improveLoading = ref(false);
const gradeContentInput = ref("");
const showGradeModal = ref(false);
const showImproveResult = ref(false);
let currentItem = null;

const openGradeModal = (item) => {
    currentItem = item;
    gradeContentInput.value = helpers.convertHtmlToText(item?.content);
    showGradeModal.value = true;
    gradeContent();
};

const gradeContent = async () => {
    try {
        gradeLoading.value = true;
        const response = await axios.post(route("ai-business.grade-content"), { content: gradeContentInput.value });
        gradeResult.value = response.data;
    } catch (e) {
        toast.error("Error grading content");
    } finally {
        gradeLoading.value = false;
    }
};

const improveContent = async () => {
    try {
        showImproveResult.value = true;
        improveLoading.value = true;
        const response = await axios.post(route("ai-business.improve-content"), { content: gradeContentInput.value });
        improveResult.value = response.data;
    } catch (e) {
        toast.error("Error improving content");
    } finally {
        improveLoading.value = false;
    }
};

const replaceWithImprovedContent = () => {
    if (currentItem) {
        currentItem.content = improveResult.value;
    }
    resetAndCloseModal();
};

const resetAndCloseModal = () => {
    gradeResult.value = "";
    improveResult.value = "";
    gradeContentInput.value = "";
    showImproveResult.value = false;
    closeGradeModal();
};

const closeGradeModal = () => {
    showGradeModal.value = false;
};

// onBeforeMount(() => {
//     const urlParams = new URLSearchParams(window.location.search);
//     const modeFromUrl = urlParams.get('mode');

//     if (modeFromUrl === 'expert') {
//         alert("Chức năng đang phát triển. Xin vui lòng quay lại sau");
//         window.location.replace('/business');
//     }
// });

const maintenanceMode = computed(() => import.meta.env.VITE_MAINTENANCE_MODE === "true");

const removeImage = (index, index_image) => {
    let removedImage = messagesMap["resultTarget"][index]["images"][index_image];

    if (removedImage.hasOwnProperty("imageRef")) {
        removedImage = {
            url: removedImage.imageRef.s3_url,
        };
    } else {
        removedImage = {
            url: removedImage.previewImageUpload,
        };
    }

    pageData.image_select_files = pageData.image_select_files.filter((file) => file.url !== removedImage.url);

    messagesMap["resultTarget"][index]["images"] = messagesMap["resultTarget"][index]["images"].filter((_, i) => i !== index_image);
};

const selectImage = (image) => {
    image = {
        url: image.imageRef?.s3_url ? image.imageRef?.s3_url : image.previewImageUpload,
    };
    const index = pageData.image_select_files.findIndex((i) => i.url === image.url);

    if (index !== -1) {
        pageData.image_select_files = pageData.image_select_files.filter((_, i) => i !== index);
    } else {
        pageData.image_select_files = [...pageData.image_select_files, image];
    }

    pageData.video_select_files = [];
};

const checkImage = (image) => {
    image = {
        url: image.imageRef?.s3_url ? image.imageRef?.s3_url : image.previewImageUpload,
    };

    return pageData.image_select_files.some((i) => image.url === i.url);
};

const checkVideo = (item) => {
    let video = {
        url: item.videoRef?.s3_url ? item.videoRef?.s3_url : item.previewVideoUpload,
    };
    return pageData.video_select_files.some((i) => i.url === video.url);
};

const selectVideo = (item) => {
    let video = {
        url: item.videoRef?.s3_url ? item.videoRef?.s3_url : item.previewVideoUpload,
    };
    pageData.video_select_files = [video];
    pageData.image_select_files = [];
};

const removeVideo = (index) => {
    pageData.video_select_files = [];
    showVideoGenerate.value = false
    resetVideoUpload(index);
};

const handleVideoUrl = (videoUrl, index = null) => {
    if (index === null) {
        for (let i = 0; i < messagesMap["resultTarget"].length; i++) {
            setVideoRef(videoUrl, i);
        }
    } else {
        setVideoRef(videoUrl, index);
    }
};

const setVideoRef = (videoUrl, index) => {
    messagesMap["resultTarget"][index].previewVideoUpload = null;
    messagesMap["resultTarget"][index].videoRef = {
        s3_url: videoUrl,
        type: "video",
    };
    messagesMap["resultTarget"][index].fileVideoUpload = null;
    selectVideo(messagesMap["resultTarget"][index]);
};

const getFakeNumberImage = (item) => {
    let numberImage = MAX_IMAGE_FILES - item.images.length;
    return numberImage > 0 ? numberImage : 0;
};

const validateLimitImage = (index) => {
    if (messagesMap["resultTarget"][index].images.length >= MAX_IMAGE_FILES) {
        toast.error("Chỉ được phép tải lên tối đa 4 ảnh.");
        return false;
    }
    return true;
};

// Tạm sửa để demo, demo xong tìm nguyên nhân rồi fix sau
const isChoosedCategory = ref(false);
</script>

<style>
.sun-editor-common {
    display: none !important;
}
.sun-editor {
    width: 100% !important;
}
.p-editor {
    --p-editor-content-color: #000000;
    /* Đặt màu mặc định cho văn bản */
    font-size: 14px;
}

.sun-editor-editable p,
.sun-editor-editable h1,
.sun-editor-editable h2,
.sun-editor-editable h3,
.sun-editor-editable h4,
.sun-editor-editable h5,
.sun-editor-editable h6,
.sun-editor-editable ul,
.sun-editor-editable ol,
.sun-editor-editable li,
.sun-editor-editable blockquote,
.sun-editor-editable pre,
.sun-editor-editable code,
.sun-editor-editable table,
.sun-editor-editable tr,
.sun-editor-editable th,
.sun-editor-editable td {
    font-size: 15px;
}

.sun-editor-editable h1 {
    color: #ff5733 !important;
    font-weight: bold;
}

.sun-editor {
    flex: 1 !important;
    border-radius: 12px;
    overflow: hidden;
}

#editor-container {
    min-height: 300px;
    background: white;
}

.sun-editor {
    width: 100% !important;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
}

.sun-editor-editable {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    font-size: 15px !important;
    line-height: 1.6 !important;
    color: #333 !important;
    padding: 20px !important;
    min-height: 300px;
    white-space: pre-wrap !important;
    word-wrap: break-word !important;
}

/* Hide toolbar */
.sun-editor .se-toolbar {
    display: none !important;
}

/* Hide unnecessary elements */
.sun-editor-common {
    display: none !important;
}

.sun-editor-editable hr {
    border: 0;
    height: 1px;
    background: #e2e8f0;
    margin: 1rem 0;
}

.sun-editor-editable em {
    font-style: italic;
    color: #4a5568;
}

.sun-editor-editable strong {
    font-weight: 600;
    color: #2d3748;
}

.sun-editor-editable br {
    display: block;
    content: "";
    margin-top: 0.5em;
}

.sun-editor-editable p {
    margin-bottom: 1em;
    line-height: 1.6;
}
.max-h-video {
    max-height:660px;
}
@media (max-width: 768px) {
    .max-h-video {
        max-height:310px;
    }
}
.color-greeen {
    color: #24aa69;
    font-weight: bold;
}
</style>
