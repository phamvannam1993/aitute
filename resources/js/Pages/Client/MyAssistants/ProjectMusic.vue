<template>

    <Head title="Tạo nội dung bán hàng - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div class="w-full">
        <div class="md:px-6 min-h-screen mb-8">
            <div>
                <div v-if="isShowTitle" class="flex flex-col md:flex-row justify-between">
                    <div class="flex justify-start items-center gap-2">
                        <h2 class="text-black font-bold text-[25px] lg:text-[30px]">Bài hát từ văn bản</h2>
                    </div>
                </div>
                <div class="w-full flex items-center justify-between mb-6 px-2">
                    <div class="flex items-center gap-3">
                        <p class="text-[15px] md:text-lg font-bold text-ai3goc-primary">Các bước triển khai:</p>
                    </div>
                    <div class="relative" ref="menuContainer">
                        <div @click="toggleListProject"
                            class="flex items-center w-fit ml-auto gap-2 px-4 md:px-8 justify-center bg-ai3goc-primary py-1 lg:py-3 rounded-full text-xs lg:text-[15px] font-bold text-white cursor-pointer">
                            <img src="/assets/img/my_assistant/new_project.png" alt="new project" />
                            <span>Dự án của bạn</span>
                        </div>
                        <div v-if="activeListProject && listProject.length > 0 " class="absolute z-10 top-14 right-0 bg-white text-black rounded-2xl p-4 shadow-custom-shadow w-[200px] md:w-[320px]">
                            <div v-for="(item, index) in listProject" :key="index" @click="selectProject(item)" class="flex items-center p-2 rounded-[10px] cursor-pointer"
                                :class="item.isActive ? 'bg-[#D7EEFF]' : ''">
                                <img :src="item.image ?? '/assets/img/my_assistant/p_red.png'" class="w-8" />
                                <p class="ml-2 font-semibold line-clamp-1">{{ item.title }}</p>
                                <div class="flex justify-end flex-1 cursor-pointer relative" @click="togglePopup(index)" @click.stop>
                                    <img v-if="item.isActive" src="/assets/img/my_assistant/active_action.png" class="min-w-1.5" />
                                    <img v-else src="/assets/img/my_assistant/action.png" class="min-w-4 min-h-4 w-4 h-4" />

                                    <div v-if="activePopup === index" class="absolute right-0 top-10 bg-white shadow-custom-shadow rounded-lg w-[180px] p-2 z-50 overflow-y-auto">
                                        <ul class="space-y-1">
                                            <li class="p-2 hover:bg-gray-100 flex items-center cursor-pointer" @click="renameProject(index)">
                                                <img src="/assets/img/my_assistant/edit.png" class="w-4 h-4 mr-2" />
                                                Đổi tên dự án
                                            </li>
                                            <li class="p-2 hover:bg-red-100 text-red-500 flex items-center cursor-pointer" @click="deleteProject(item)">
                                                <img src="/assets/img/my_assistant/delete.png" class="w-4 h-4 mr-2" />
                                                Xóa dự án
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="activeListProject && listProject.length === 0 " class="absolute z-10 top-14 right-0 bg-white rounded-2xl p-4 shadow-custom-shadow w-[200px] md:w-[320px] text-center text-red-600">
                            Chưa có dự án nào
                        </div>
                    </div>
                </div>
                <!-- Section 1 -->
                <div class="bg-white shadow-custom-shadow lg:rounded-[35px] md:p-5 p-3" :class="sections[0].open ? 'lg:rounded-[35px]' : ''">
                    <div :class="sections[0].open ? 'flex-col rounded-[35px] items-start' : 'flex-row rounded-full items-center'" class=" flex justify-between text-black">
                        <div class="flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
                            <Step :step="1" stepName="Thiết lập dự án"/>
                        </div>
                        <div :class="sections[0].open && !isLoadingCheckConversation ? 'block':'absolute opacity-0 invisible'" class="w-full">
                            <form @submit.prevent="handleProjectSubmit">
                                <div>
                                    <p class="text-sm md:text-base font-bold ">1. Tên dự án / SP <span class="text-red-500">*</span></p>
                                    <input v-model="formProject.title" type="text" placeholder="Nhập tên dự án của bạn...."
                                        class="w-full rounded-lg lg:rounded-2xl border-[#D4D4D4] mt-1.5 lg:py-[22px] lg:px-7">
                                    <p class="text-sm md:text-base font-bold mt-4">2. Mô tả ngắn ngọn về dự án / SP</p>
                                    <textarea v-model="formProject.description" placeholder="Dự án làm cái gì? làm cho ai? làm để làm gì?..." rows="4"
                                        class="resize-none mt-1.5 w-full rounded-lg lg:rounded-2xl border-[#D4D4D4] lg:py-[22px] lg:px-7"></textarea>
                                        <div class="flex flex-col items-start mt-6 lg:m-0">
                                            <div class="w-full flex justify-start">
                                                <p class="text-sm md:text-[19px] text-business-primary font-bold leading-6 mr-[6px]">Tải tài liệu sản phẩm:</p>
                                                <label :class="documentLoading ? '' : 'bg-orion-orange'" for="file-product" class="flex items-center rounded-lg justify-center cursor-pointer text-sm md:text-[19px] w-[26px] h-[26px]">
                                                    <svg v-if="!documentLoading" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M16.5 11.3359L16.5 14.3359C16.5 14.7338 16.342 15.1153 16.0607 15.3966C15.7794 15.6779 15.3978 15.8359 15 15.8359L4.5 15.8359C4.10218 15.8359 3.72064 15.6779 3.43934 15.3966C3.15804 15.1153 3 14.7338 3 14.3359L3 11.3359" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M13.5 6.08594L9.75 2.33594L6 6.08594" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M9.75 2.33594L9.75 11.3359" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
    <!--                                                <img src="/assets/svgs/upload_white.svg" alt="">-->
                                                    <input v-if="!documentLoading" ref="uploadDoc" type="file" class="hidden" id="file-product" @change="handleUploadDocument" accept=".docx, .pdf, .xls, .xlsx, .ppt, .pptx" />
                                                    <div v-else>
                                                        <div role="status" class="w-6 h-6 flex items-center flex-col gap-2">
                                                            <svg aria-hidden="true" class="h-10 mx-auto text-gray-200 animate-spin dark:text-gray-600 fill-orion-sec"
                                                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                                    fill="currentColor" />
                                                                <path
                                                                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                                    fill="currentFill" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>

                                            <span class="text-sm md:text-[16px] text-gray-400 self-start inline-block mb-1 italic"> Tải lên file mô tả chi tiết sản phẩm (PDF, DOC, DOCX, TXT). </span>
                                            <span class="text-sm text-white self-start inline-block mb-1">.</span>
                                            <div v-if="tempFormProject?.files?.length > 0" class="flex flex-col items-center border-gray-300 border-2 rounded-[20px] h-fit w-full md:w-[50%] p-4 overflow-hidden">
                                                <div class="flex flex-col gap-3 h-5/6 w-full overflow-y-scroll">
                                                    <div class="flex items-center w-full gap-2" v-for="(file, index) in tempFormProject.files" :key="index">
                                                        <div class="size-6 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all" :class="file.active ? '' : 'border-gray-400'" @click="toggleActiveFile(index)">
                                                            <div class="size-[15px] bg-[#0e68ef] rounded-full" v-if="file.active"></div>
                                                        </div>
                                                        <div class="flex items-center gap-2 rounded-full text-black flex-1 px-[10px] py-[6px] transition-all" :class="file.active ? 'bg-orion-hover' : 'bg-gray-100'">
                                                            <FileText class="size-5 cursor-pointer" />
                                                            <span class="font-semibold line-clamp-1">{{ file.name }}</span>
                                                            <Trash2 @click="handleRemoveDocument(index)" class="stroke-red-500 ml-auto size-5 cursor-pointer flex-shrink-0" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="bg-white flex items-center justify-center gap-[20px] mt-[20px]">
                                                    <button type="button" class="text-sm md:text-[19px] font-bold w-fit px-3 h-[36px] flex items-center justify-center gap-2 border-2 border-business-primary text-business-primary rounded-lg lg:rounded-2xl mb-3" @click="selectALlDocument">
                                                        <span>Chọn tất cả</span>
                                                    </button>
                                                    <button type="button" class="text-sm md:text-[19px] font-bold w-fit px-3 h-[36px] flex items-center justify-center gap-2 border-2 border-gray-400 text-gray-400 rounded-lg lg:rounded-2xl mb-3" @click="unSelectALlDocument">
                                                        <span>Bỏ chọn</span>
                                                    </button>
                                                </div> -->
                                            </div>
                                        </div>
<!--                                    <p class="text-sm md:text-base font-bold mt-4">3. Tải thông tin dự án / SP</p>-->
<!--                                    <div class="lg:grid grid-cols-2 gap-10 mt-6">-->
<!--                                        <div class="flex flex-col items-center">-->
<!--                                            <label for="image-product" class="flex-shrink-0 cursor-pointer text-sm md:text-base font-bold min-w-[200px] h-[36px]-->
<!--                                                flex items-center justify-center gap-2-->
<!--                                                border-2 border-ai3goc-primary text-ai3goc-primary-->
<!--                                                rounded-xl mb-3">-->
<!--                                                <div class="bg-ai3goc-primary p-1 rounded-full">-->
<!--                                                    <Upload size="16" color="#ffffff" />-->
<!--                                                </div>-->
<!--                                                <span>Tải ảnh sản phẩm</span>-->
<!--                                                <input type="file" class="hidden" id="image-product" @change="uploadImage" />-->
<!--                                            </label>-->
<!--                                            <span class="text-sm md:text-base text-gray-400 self-start inline-block mb-1 italic">Tải lên 01 hình ảnh đẹp nhất của sản phẩm.</span>-->
<!--                                            <div class="flex items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden">-->
<!--                                                <img v-if="tempFormProject.image_product.url" :src="tempFormProject.image_product.url" alt="" class="object-cover size-full">-->
<!--                                                <img v-else src="\assets\img\aiwow\image_placeholder.png" alt="" class="size-32 object-contain">-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="flex flex-col items-center mt-6 lg:m-0">-->
<!--                                            <label :class="documentLoading ? 'cursor-not-allowed !text-gray-400' : 'cursor-pointer'" for="file-product" class="flex-shrink-0 cursor-pointer text-sm md:text-base font-bold w-[200px] h-[36px]-->
<!--                                                flex items-center justify-center gap-2-->
<!--                                                border-2 border-ai3goc-primary text-ai3goc-primary-->
<!--                                                rounded-xl mb-3">-->
<!--                                                <div class="bg-ai3goc-primary p-1 rounded-full">-->
<!--                                                    <Upload size="16" color="#ffffff" />-->
<!--                                                </div>-->
<!--                                                <span>-->
<!--                                                    {{ documentLoading ? 'Đang tải tài liệu...' : 'Tải tài liệu sản phẩm' }}-->
<!--                                                </span>-->
<!--                                                <input type="file" class="hidden" id="file-product" @change="handleUploadDocument" accept=".doc, .docx, .pdf, .xls, .xlsx, .ppt, .pptx" />-->
<!--                                            </label>-->
<!--                                            <span class="text-sm text-white self-start inline-block mb-1">.</span>-->
<!--                                            <div class="flex flex-col items-center border-gray-300 border-2 rounded-[20px] aspect-[4/3] h-fit w-full p-4 overflow-hidden">-->
<!--                                                &lt;!&ndash; Item file &ndash;&gt;-->
<!--                                                <div class="flex flex-col gap-3 h-5/6 w-full overflow-y-scroll">-->
<!--                                                    <div class="flex items-center w-full gap-2" v-for="(file, index) in tempFormProject.files" :key="index">-->
<!--                                                        <div class="size-6 rounded-full border-2   flex items-center justify-center cursor-pointer transition-all"-->
<!--                                                            :class="file.active ? 'border-ai3goc-primary' : 'border-gray-400'" @click="toggleActiveFile(index)">-->
<!--                                                            <div class="size-[15px] bg-ai3goc-primary rounded-full" v-if="file.active"></div>-->
<!--                                                        </div>-->
<!--                                                        <div class="flex items-center gap-2 rounded-full  text-black flex-1 px-[10px] py-[6px] transition-all"-->
<!--                                                            :class="file.active ? 'bg-orion-orange-hover' : 'bg-gray-100'">-->
<!--                                                            <FileText class="size-5 cursor-pointer" />-->
<!--                                                            <span class="font-semibold line-clamp-1">{{ file.name }}</span>-->
<!--                                                            <Trash2 @click="handleRemoveDocument(index)" class="stroke-red-500 ml-auto size-5 cursor-pointer flex-shrink-0" />-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                                <div class="bg-white flex items-center justify-center gap-[20px] mt-[20px]">-->
<!--                                                    <button type="button" class="text-sm md:text-base font-bold w-fit px-3 h-[36px]-->
<!--                                                        flex items-center justify-center gap-2-->
<!--                                                        border-2 border-ai3goc-primary text-ai3goc-primary-->
<!--                                                        rounded-lg lg:rounded-2xl mb-3" @click="selectALlDocument">-->
<!--                                                        <span>Chọn tất cả</span>-->
<!--                                                    </button>-->
<!--                                                    <button type="button" class="text-sm md:text-base font-bold w-fit px-3 h-[36px]-->
<!--                                                        flex items-center justify-center gap-2-->
<!--                                                        border-2 border-gray-400 text-gray-400-->
<!--                                                        rounded-lg lg:rounded-2xl mb-3" @click="unSelectALlDocument">-->
<!--                                                        <span>Bỏ chọn</span>-->
<!--                                                    </button>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
                                </div>
                                <div class="flex justify-center gap-4 mt-4 mb-4">
                                    <button type="submit" class="min-w-[80%] lg:min-w-[260px] text-sm md:text-base px-4 py-3 bg-ai3goc-primary text-white rounded-lg lg:rounded-2xl font-bold"
                                        :disabled="loading">
                                        <div role="status" v-if="loading">
                                            <svg aria-hidden="true" class="inline w-6 h-6 text-gray-200 animate-spin dark:text-gray-600 fill-gray-600 dark:fill-gray-300" viewBox="0 0 100 101"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                    fill="currentFill" />
                                            </svg>
                                        </div>
                                        <span v-else>
                                            Xác nhận
                                        </span>
                                    </button>
                                </div>
                                <div class="flex flex-col" :class="messagesMap['information_project'] ? 'flex' : 'hidden'">
                                    <p class="text-sm md:text-base font-bold mt-4 mb-6">4. Thông tin dự án / SP</p>
                                    <p class="text-ai3goc-primary mb-6 font-bold">Hãy bổ sung hoặc sửa các thông tin sau</p>
                                    <textarea ref="editor" rows="14"
                                        class="text-sm px-[23px] w-full border border-gray-300 rounded-[15px] placeholder:text-gray-400">
                                    </textarea>
                                    <div class="flex items-center justify-center gap-5 mt-7">
                                        <button type="button" class="w-full max-w-[180px] px-4 py-3 border border-gray-300 text-gray-500 rounded-2xl font-bold">Huỷ</button>
                                        <button type="button" @click="handleButtonClick('analysis', $event, category, 1)"
                                            class="w-full max-w-[180px] px-4 py-3 bg-ai3goc-primary text-white rounded-2xl font-bold">Xác nhận</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div @click="toggleSection(0)" :class="sections[0].open ? 'self-end' : ''" class="text-ai3goc-primary font-medium flex items-center cursor-pointer">
                            <span>{{ sections[0].open ? "Thu gọn" : "Hiển thị" }}</span>
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
                                <h2 class="lg:text-2xl text-base font-semibold ">{{ selectedProject?.title }}
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2 -->
                <div ref="category" :class="sections[1].open ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'"
                    class="bg-white shadow-custom-shadow md:p-5 p-3 flex justify-between mt-4">
                    <div class="flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black lg:w-4/5">
                        <Step :step="2" stepName="Tạo ý tưởng tổng quan"/>
                    </div>
                    <div v-if="sections[1].open" class="w-full lg:w-4/5 self-center">
                        <!-- Options -->
                        <LoadingCircle v-if="isLoadingAnalysis" />
                        <div class="flex flex-col gap-4 mt-6" v-if="messagesMap['analysis'] && !isLoadingAnalysis">
                            <!-- <template  v-for="(item, index) in messagesMap['analysis']" :key="index">
                                <div class="flex items-center gap-5 lg:px-[90px]" >
                                    <div @click="handleToggleAnalysis(index)" :class="item.isActive ? 'border-orion-orange' : 'border-gray-400'"
                                        class="flex-shrink-0 size-6 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all">
                                        <div class="size-[15px] bg-orion-orange rounded-full" v-if="item.isActive"></div>
                                    </div>
                                    <div class="relative w-full">
                                        <div @click="handleToggleAnalysis(index)"
                                            :class="item.isActive ? 'border-orion-orange bg-orion-orange-hover' : 'border-gray-400'"
                                            class="cursor-pointer relative flex items-center justify-between border border-gray-300 rounded-2xl text-black px-4 py-2 flex-1 gap-1 h-[40px] transition-all">
                                            <span class="select-none text-sm md:text-[base] font-semibold">{{ item.name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div class="flex items-center justify-center gap-5 mt-5">
                                <button type="button" class="w-full max-w-[180px] px-4 py-3 border border-gray-300 text-gray-500 rounded-2xl font-bold">Huỷ</button>
                                <button
                                    :disabled="isLoadingSelectCreateContent"
                                type="button" @click="handleButtonSelectCreateContent" class="flex items-center justify-center w-full max-w-[180px] px-4 py-3 bg-ai3goc-primary text-white rounded-2xl font-bold">
                                    <span v-if="!isLoadingSelectCreateContent">
                                        Xác nhận
                                    </span>
                                    <LoadingCircle v-else class="!size-6" />
                                </button>
                            </div> -->
                            <ContentProductAd
                                v-if="messagesMap['content_product_ad']"
                                :form-data="messagesMap['content_product_ad'].formData"
                                :handle-button-click="handleSubmitMediaCampaign"
                                :loading-submit="messagesMap['content_product_ad'].isLoading"

                            />
                            <PoetryStep
                                v-if="messagesMap['poetry']"
                                :form-data="messagesMap['poetry'].formData"
                                :handle-button-click="handleSubmitPoetry"
                                :loading-submit="messagesMap['poetry'].isLoading"

                            />
                            <MusicStep
                                v-if="messagesMap['music']"
                                :data="messagesMap['music']"
                                :handle-button-click="handleSubmitMusic"
                                :toggleBigIdeaMusic="toggleBigIdeaMusic"
                                :loading="messagesMap['music'].isLoading"
                            />
                        </div>
                    </div>
                    <div @click="toggleSection(1)" :class="sections[1].open ? 'self-end' : ''" class="text-ai3goc-primary mt-2 font-medium flex items-center cursor-pointer">
                        <span>{{ sections[1].open ? "Thu gọn" : "Hiển thị" }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': sections[1].open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
                <AnalysisStep
                    v-if="analysisActive?.name === 'Phân tích thị trường' && messagesMap['resultsAnalysis']"
                    :open="sections[2].open"
                    :list="messagesMap['resultsAnalysis']"
                    :handleSubmit="handleButtonClick"
                    :loadingSubmit="eventLoading"
                    :toggleItem="toggleItemResultAnalysis"
                    :toggleSection="toggleSection"
                    :refScroll="resultsAnalysis"
                />
                <div class="w-full">
                    <!-- Section 3 -->
                    <LoadingCircle v-if="resultTargetLoading" loading-title="Hệ thống đang xử lý. Xin vui lòng đợi." />
                    <!-- Result 1 -->
                    <div ref="resultTarget" class="w-full">
                        <div :class="true ? 'flex-col lg:rounded-[35px]' : 'flex-row lg:rounded-full'"
                            class="bg-white shadow-custom-shadow md:p-5 p-3 flex items-center justify-between mt-4">
                            <div class="flex items-center self-start justify-between w-full gap-2 mb-2 text-xs lg:text-sm mt-2 text-black lg:w-4/5">
                                <Step :step="3" stepName="Xem kết quả và hiệu chỉnh"/>
                            </div>
                            <div v-for="(item, index) in messagesMap['resultTarget']" :key="index" class="w-full lg:w-4/5 mt-10 md:mt-5 text-black text-xs lg:text-[15px] relative">
                                <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-4">
                                    <div class="relative flex flex-row lg:items-center gap-2 w-full">
                                        <template v-for="(option, indexOptionRewrite) in item.options_rewrite" :key="indexOptionRewrite">
                                            <div class="w-full" v-if="option.name !== 'Định dạng' && option.name !== 'keyword'">
                                                <SelectRadix :disabled="item.isLoadingRewrite" :options="option.options" :selected="option.value" :placeholder="option.value || option.name"
                                                    :handle-change="(value)=>selectOption({ value:value,indexOptionRewrite: indexOptionRewrite, index:index})" />
                                            </div>
                                            <div v-if="option.name === 'keyword'" class="absolute -top-10 md:-top-[56px] right-24 md:right-8 flex items-center gap-2 ">
                                                <DropdowRadix :disabled="item.isLoadingRewrite" :open="option.isOpenKeyword" @update:open="option.isOpenKeyword = $event">
                                                    <template #trigger>
                                                        <button type="button"
                                                            class="flex items-center justify-between gap-2  w-full  px-4 py-2 bg-white border border-gray-200 rounded-full shadow-sm text-sm font-medium   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ai3goc-primary relative">
                                                            <img src="/assets/img/my_assistant/pencil.png" class="size-4 " alt="">
                                                            <span class="text-xs lg:text-sm text-ai3goc-primary font-bold line-clamp-1">Từ khóa</span>
                                                        </button>
                                                    </template>
                                                    <template #content>
                                                        <div class="flex flex-col lg:mt-0 w-[270px] bg-white border border-gray-200 rounded-lg shadow-custom-shadow z-50 p-2 px-3">
                                                            <textarea placeholder="Nhập từ khóa" v-model="option.value"
                                                                class="border-none rounded-[10px] bg-gray-200 h-[66px] w-[243px]"></textarea>
                                                            <div class="flex items-center justify-between h-[30px] mt-4">
                                                                <button type="button" class="border border-gray-300 rounded-lg h-full px-3"
                                                                    @click="option.isOpenKeyword = !option.isOpenKeyword; option.value = '';">Huỷ</button>
                                                                <button type="button" class="bg-ai3goc-primary rounded-lg text-white h-full px-3"
                                                                    @click="option.isOpenKeyword = !option.isOpenKeyword;rewriteContentProductAd(index)">Xác nhận</button>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </DropdowRadix>
                                            </div>
                                            <div v-if="option.name === 'Định dạng'" class="absolute -top-10 md:-top-[56px] right-0 lg:-right-20 flex items-center gap-2">
                                                <DropdowRadix :disabled="item.isLoadingRewrite" :open="option.isOpen" @update:open="option.isOpen = $event">
                                                    <template #trigger>
                                                        <button :disabled="item.isLoadingRewrite" type="button"
                                                            class="flex w-full items-center justify-center  px-4 py-2 bg-ai3goc-primary rounded-2xl shadow-sm text-sm font-medium   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ai3goc-primary relative"
                                                            :class="{ 'bg-gray-300': item.isLoadingRewrite }">
                                                            <span class="text-xs lg:text-sm text-white font-bold line-clamp-1">Định dạng</span>
                                                        </button>
                                                    </template>
                                                    <template #content>
                                                        <ul class="flex flex-col lg:mt-0 min-w-[200px] w-full bg-white border border-gray-200 rounded-lg shadow-custom-shadow z-50 p-2 px-3">
                                                            <li v-for="(optionItem, idx) in option.options" :key="idx"
                                                                @click="selectOption({ value: optionItem, indexOptionRewrite: indexOptionRewrite, index: index })"
                                                                class="px-4 py-2 cursor-pointer hover:bg-blue-50 hover:text-ai3goc-primary rounded-full "
                                                                :class="{ 'bg-blue-100 text-ai3goc-primary': option.value === optionItem }">
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
                                        <p class="text-ai3goc-primary text-[15px] font-bold">Nội dung đã tạo:</p>
                                        <div v-if="!item.isLoadingRewrite" v-html="item?.content ?? ''"
                                            class="text-black whitespace-pre-line text-base border border-gray-400 rounded-xl p-4 max-h-96 overflow-y-auto"></div>
                                        <div v-else class="w-full">
                                            <LoadingCircle loading-title="Nội dung đang tạo..." />
                                        </div>
                                    </div>
                                </div>
                                <div v-if="!hiddenSocialMedia" class="flex flex-col lg:flex-row gap-4 mt-4 ">
                                    <div v-if="!hiddenImage" class="w-full">
                                        <!-- Upload Buttons -->
                                        <p class="text-ai3goc-primary mb-2 font-bold mt-8">Hình ảnh:</p>
                                        <div class="flex justify-center gap-4 mb-4 h-[40px]">

                                            <label :id="`image_${index}`"
                                                class="bg-ai3goc-primary text-white py-2 text-xs font-bold lg:text-sm rounded-lg lg:rounded-2xl px-6 hover:bg-cyan-600 flex items-center gap-2">
                                                <div class="rounded-full">
                                                    <img src="/assets/img/orion/icon/upload-white.svg" alt="">
                                                </div>
                                                Tải ảnh
                                                <input
                                                :id="`image_${index}`" type="file" accept="image/*" @change="handleImageUpload($event, index)" class="hidden" />
                                            </label>
                                            <div class="relative">
                                                <!-- <button @click="handleCreateImage(messagesMap['resultTarget'][index]?.content)" -->
                                                <button @click="isGenerateImage = !isGenerateImage"
                                                    class="bg-ai3goc-primary text-white py-2.5 text-xs lg:text-sm rounded-lg lg:rounded-2xl px-6 hover:bg-cyan-600 flex items-center gap-2">
                                                    <img src="/assets/img/my_assistant/generate_image.png" class="size-5" alt="">
                                                    Tạo ảnh
                                                </button>
                                                <div v-if="isGenerateImage" class="absolute z-10 right-0 w-52 bg-white shadow-custom-shadow rounded-2xl p-2 py-4 space-y-2">
                                                    <div @click="toggleGenerateImage('textToImage', index)" class="flex items-center cursor-pointer px-4 py-2 hover:bg-orion-orange-hover rounded-md">
                                                        <p>Tạo ảnh từ văn bản</p>
                                                    </div>
                                                    <div @click="toggleGenerateImage('background', index)" class="flex items-center cursor-pointer px-4 py-2 hover:bg-orion-orange-hover rounded-md">
                                                        <p>Hình phối cảnh</p>
                                                    </div>
                                                    <!-- <div @click="toggleGenerateImage('myAi', index)" class="flex items-center cursor-pointer px-4 py-2 hover:bg-orion-orange-hover rounded-md">
                                                        <p>Tự sướng</p>
                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 items-center justify-center w-full gap-2">
                                            <div class="flex items-center  h-full w-full  rounded-xl overflow-hidden min-h-[120px]" v-for="(image, index) in item.images" :key="index">
                                                <div v-if="image?.imageRef || image?.previewImageUpload" class="relative m-auto ">
                                                    <img v-if="image.imageRef" :src="image.imageRef.s3_url" alt="image generated by AI" class="w-full h-full object-contain cursor-pointer " />
                                                    <img v-else :src="image.previewImageUpload" alt="image generated by AI" class="w-full h-full object-contain cursor-pointer " />
                                                </div>
                                                <div v-else class="bg-[#E6E6E6] flex h-full items-center justify-center w-full rounded-xl">
                                                    <img v-if="!isLoadingCreateImage" src="/assets/svgs/aiwow/image.svg" alt="loading" class="mx-auto w-16" />
                                                    <div v-else class="flex flex-col items-center">
                                                        <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                                        <h3 class="text-white text-center">Hệ thống đang xử lý, xin đợi một
                                                            chút
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>

                                    <div v-if="!hiddenVideo" class="lg:w-1/2">
                                        <!-- Video Section -->
                                        <div class="flex flex-col">
                                            <div class="flex justify-center gap-4 mb-4 h-[40px]">
                                                <label :id="`video_${index}`"
                                                    class="bg-ai3goc-primary  text-white px-6 py-2 text-xs lg:text-sm rounded-lg lg:rounded-2xl flex items-center gap-2">
                                                    <div class=" p-1 rounded-full">
                                                        <Upload size="16" color="#ffffff" />
                                                    </div>
                                                    Tải video
                                                    <input type="file" accept="video/mp4,video/x-m4v,video/webm,video/ogg,video/*,.flv,.3gp" class="inputMedia hidden"
                                                        @change="handleFileChange($event, index)">
                                                </label>
                                                <button @click="scrollToCreateFormRef(index)"
                                                    class="bg-ai3goc-primary  text-white px-4 py-2 text-xs lg:text-sm rounded-2xl flex items-center gap-2">
                                                    <img src="/assets/img/my_assistant/generate_video.png" alt="">
                                                    Tạo video
                                                </button>
                                            </div>
                                            <div class="flex items-center justify-center h-[256px] w-full">
                                                <label class="text-xs lg:text-sm flex gap-1 items-center mb-1 w-full">
                                                    <div class='flex gap-1 w-full items-center cursor-pointer'>
                                                        <input type="radio" v-model="uploadType" value="video" :checked="uploadType == 'video'" @change="uploadType = 'video'"
                                                            class="ml-0 rounded-full" />
                                                        <div class="w-full">
                                                            <div v-if="item.videoRef || item.previewVideoUpload" class="bg-[#E6E6E6] flex h-[256px] items-center justify-center rounded-xl w-full object-cover">
                                                                <video controls v-if="item.videoRef" :src="item.videoRef.s3_url" alt="image generated by AI"
                                                                    class="w-full h-full object-contain cursor-pointer rounded-xl" />
                                                                <video controls v-else :src="item.previewVideoUpload" alt="image generated by AI"
                                                                    class="w-full h-full object-contain cursor-pointer rounded-xl" />
                                                            </div>
                                                            <div v-else class="bg-[#E6E6E6] flex h-[256px] items-center justify-center rounded-xl w-full">
                                                                <img src="/assets/img/aiwow/homepage/play_button.png" class="w-16" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="!messagesMap['music']" class="gap-4 mt-4">
                                    <div class="flex flex-wrap flex-1 items-center justify-center space-x-2">
                                        <div v-for="(fanpage) in fanpages" :key="fanpage.id" class="flex items-center">
                                            <div @click="activeFanpage(fanpage)" :class="facebook_fanpage_id == fanpage.id ? 'border-blue-500' : ''"
                                                class="cursor-pointer relative flex flex-col items-center border-b-4 p-2">
                                                <img :src="fanpage.page_image ? fanpage.page_image : '/assets/img/login_icon/success.png'" class="w-10 h-10" alt="Avatar" />
                                                <span class="text-xs font-medium text-gray-700">{{ fanpage.page_name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="page.props?.errors?.length" class="gap-4 mt-4 text-red-500">
                                    <p>Lỗi: </p>
                                    <ul class="flex-col gap-4 mt-2">
                                        <li v-for="(errors, key) in page.props.errors" :key="key">
                                            <strong>{{ key }}: </strong>
                                            <span>{{ errors[0] }}</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="flex justify-center gap-3 lg:gap-4 mt-6 mb-4" v-if="messagesMap['music']">
                                    <button @click="toggleGenerateImage('music', index)"
                                        class="min-w-[100px] flex items-center justify-center gap-2 md:min-w-[150px] px-4 py-2 lg:py-3 bg-ai3goc-primary text-white rounded-lg lg:rounded-2xl font-bold">
                                        <img src="/assets/img/my_assistant/music.png" class="w-5 xs:block hidden" alt="">
                                        Tạo nhạc
                                    </button>
                                </div>
                                <div class="flex justify-center gap-3 lg:gap-4 mt-6 mb-4" v-else>
                                    <button @click="openPostDetail(item?.content ?? '', index)"
                                        class="min-w-[100px] md:min-w-[150px] px-2 py-2 lg:py-3 bg-ai3goc-primary rounded-lg lg:rounded-2xl border border-[#C5C5C5] text-white font-bold flex items-center justify-center gap-2 text-sm">
                                        <img src="/assets/img/orion/icon/create_post-white.svg" class="w-5 xs:block hidden" alt="">
                                        Đăng bài
                                    </button>
                                    <button @click="shareAIContent(index)"
                                        class="min-w-[100px] flex items-center justify-center gap-2 md:min-w-[150px] px-2 md:px-4 py-2 lg:py-3 bg-ai3goc-primary text-white rounded-lg lg:rounded-2xl font-bold text-sm">
                                        <img src="/assets/img/my_assistant/share.png" class="w-5 xs:block hidden" alt="">
                                        Chia sẻ
                                    </button>
                                    <button @click="downloadfile(index)"
                                        class="min-w-[100px] flex items-center justify-center gap-2 md:min-w-[150px] px-2 md:px-4 py-2 lg:py-3 bg-ai3goc-primary text-white rounded-lg lg:rounded-2xl font-bold text-sm">
                                        <img src="/assets/img/my_assistant/download.png" class="w-5 xs:block hidden" alt="">
                                        Tải xuống
                                    </button>
                                </div>

                            </div>
                        </div>
                        <div v-for="(_, index) in messagesMap['resultTarget']" :key="index" class="w-full">
                            <div :id="`target_section_${index}`" ref="myAiRef" v-if="myAi && index === targetActiveIndex" class="mt-4">
                                <MyAIComponent @saveGenerationResult="(value)=>handleSaveGenerationResult(value,index)" :listCollection="listCollection" :collectionName="collectionSelected?.title"
                                    :collection-selected="collectionSelected" :my_ai_image_models="my_ai_image_models" />
                            </div>

                            <div :id="`target_section_${index}`" ref="textToImageRef" v-if="textToImage && index === targetActiveIndex" class="mt-4">
                                <TextToImage @saveGenerationResult="(value)=>handleSaveGenerationResult(value,index)" :listCollection="listCollection" :collectionName="collectionSelected?.title"
                                    :collection-selected="collectionSelected" :my_ai_image_models="my_ai_image_models" :image-description="getActiveFinalTarget()?.content" />
                            </div>

                            <div :id="`target_section_${index}`" ref="backgroundRef" v-if="background && index === targetActiveIndex" class="mt-4">
                                <AIBackgroundComponent @saveGenerationResult="(value)=>handleSaveGenerationResult(value,index)" :listCollection="listCollection" :collectionName="collectionSelected?.title"
                                    :collection-selected="collectionSelected" :my_ai_image_models="my_ai_image_models" />
                            </div>

                            <!-- Create video -->
                            <div :id="`target_section_${index}`" v-if="videoDetail && index === targetActiveIndex" ref="createFormRef" class="w-full h-full mt-10 rounded-[35px] overflow-hidden">
                                <CreateForm
                                    @saveGenerationResult="(value)=>handleSaveGenerationResultVideo(value,index)"
                                :topic="getActiveFinalTarget()?.content ?? ''" />
                            </div>
                            <div :id="`target_section_${index}`" v-if="musicAi && index === targetActiveIndex"class="w-full h-full mt-10 rounded-[35px] overflow-hidden">
                                <CreateMusic
                                    :content="getActiveFinalTarget()?.content ?? ''"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Loading v-if="isLoading" />

    <PostForm ref="postFormRef"  @onSuccess="onSuccessPostForm" />

    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade"/>
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup"
                    @buyCredit="handleBuyCredit"
                    @cancel="handleBuyCancel"
                    :currentCredit = "pageProps.auth?.user?.credit || 0"
                    :additionalCredit = "additionalCredit">

    </CreditBuyModal>
</template>


<script setup>
import FormShareLink from "@/Components/FormShareLink.vue";
import Loading from "@/Components/Loading.vue";
import LoadingCircle from "@/Components/LoadingCircle.vue";
import Modal from "@/Components/Modal.vue";
import PopupAff from '@/Components/PopupAff.vue';
import PostForm from '@/Components/ShareAiText/PostForm.vue';
import Step from "@/Components/Step/Index.vue";
import { convertToSnakeCase, extractRelativePathFromSignedS3Url } from "@/lib/utils";
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { ChevronDown, FileText, Trash2, Upload } from "lucide-vue-next";
import { computed, nextTick, onBeforeUnmount, onMounted, onUpdated, reactive, ref, onUnmounted,inject  } from "vue";
import { toast } from "vue3-toastify";
import CreditBuyModal from '../../../Components/ModalBuyCredit/BuyCredit.vue';
import AIBackgroundComponent from "../AIBackground/Component.vue";
import MyAIComponent from "../AIImage/MyAIComponent.vue";
import TextToImage from "../AIImage/TextToImageComponent.vue";
import CreateForm from "../TextToVideo/CreateForm.vue";
import AnalysisStep from "./AnalysisStep.vue";
import ContentProductAd from "./ContentProductAd.vue";
import MusicStep from "./MusicStep.vue";
import CreateMusic from "../TextToSong/CreateMusic.vue";
import PoetryStep from "./PoetryStep.vue";
import suneditor from 'suneditor'
import 'suneditor/dist/css/suneditor.min.css'
import { marked } from 'marked';
import SelectRadix from "@/Components/SelectRadix.vue";
import DropdowRadix from "@/Components/DropdowRadix.vue";
const page = usePage();
const fanpages = ref([]);
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const facebook_fanpage_id = ref(null);
const postFormRef = ref(null);
const createFormRef = ref(null);
const uploadType = ref('image');
const showAffKeyPopup = ref(false);
const additionalCredit = ref(0);

const editor = ref(null);
let sunEditorInstance = ref(null);

const { props: pageProps } = usePage();
const sections = reactive([
    // 5 sections
    { open: true },
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
        image: '/assets/img/my_assistant/p_red.png',
        name: 'Fanpage Hello!',
        isActive: true,
    },
    {
        image: '/assets/img/my_assistant/p_green.png',
        name: 'Dự án 2',
        isActive: false,
    },
    {
        image: '/assets/img/my_assistant/p_blue.png',
        name: 'Dự án 3',
        isActive: false,
    },
])

const styles = ref([
    "Hài hước và vui vẻ",
    "Châm biếm",
    "Tự sự",
    "Khích lệ và truyền cảm hứng",
    "Thông tin",
    "Hướng dẫn và giảng dạy",
]);
const resultsAnalysisSample = [
    {
        name: 'Tìm hiểu sản phẩm',
        content: '',
        isActive: false,
        isLoading: false
    },
    {
        name: 'Tìm hiểu Thị trường',
        content: '',
        isActive: false,
        isLoading: false
    },
    {
        name: 'Tìm hiểu Khách hàng',
        content: '',
        isActive: false,
        isLoading: false
    },
    {
        name: 'Tìm hiểu Thương hiệu',
        content: '',
        isActive: false,
        isLoading: false
    },
    {
        name: 'Tìm hiểu Đối thủ cạnh tranh',
        content: '',
        isActive: false,
        isLoading: false
    },
    {
        name: 'Tìm hiểu Tài chính và hiệu quả Kinh doanh',
        content: '',
        isActive: false,
        isLoading: false
    },
    {
        name: 'Tìm hiểu Rủi ro và Pháp lý',
        content: '',
        isActive: false,
        isLoading: false
    },
    {
        name: 'Tìm hiểu Xu hướng và cơ hội Tương lai',
        content: '',
        isActive: false,
        isLoading: false
    }
]
const sampleAnalysis = [
        {
            "name": "Tạo Bài viết quảng cáo SP",
            "isActive": false
        },
        {
            "name": "Tạo thơ Quảng Cáo Sản phẩm",
            "isActive": false
        },
        {
            "name": "Tạo chiến dịch Truyền thông",
            "isActive": false
        },
        {
            "name": "Phân tích thị trường",
            "isActive": false
        },
        {
            "name": "Tạo Nhạc Doanh Nghiệp",
            "isActive": false
        }
    ]
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

//Hiden component
const isGenerateImage = ref(false);
const textToImage = ref(false);
const background = ref(false);
const myAi = ref(false);
const videoDetail = ref(false);
const targetActiveIndex = ref(0);
const musicAi = ref(false);
const isShowTitle = ref(false);
const helpers = inject("helpers");

const checkIfMusicForBusiness = () => {
    const currentUrl = window.location.href;
    isShowTitle.value = currentUrl.includes('music-for-business');
};

const toggleGenerateImage = async (type, index) => {
    targetActiveIndex.value = index;
    handleActiveFinalTarget(index);
    if (type === 'myAi') {
        myAi.value = true;
        background.value = false;
        textToImage.value = false;
        isGenerateImage.value = false;
        videoDetail.value = false;
        await nextTick();
    } else if (type === 'background') {
        myAi.value = false;
        background.value = true;
        textToImage.value = false;
        isGenerateImage.value = false;
        videoDetail.value = false;
        await nextTick();
    } else if (type === 'textToImage') {
        myAi.value = false;
        background.value = false;
        textToImage.value = true;
        isGenerateImage.value = false;
        videoDetail.value = false;
        await nextTick();
    }
    else if (type === 'video') {
        background.value = false;
        textToImage.value = false;
        isGenerateImage.value = false;
        videoDetail.value = true;
        await nextTick();
    }
    else if (type === 'music') {
        background.value = false;
        textToImage.value = false;
        isGenerateImage.value = false;
        videoDetail.value = false;
        musicAi.value = true;
        await nextTick();
    }
    const targetSection = document.getElementById(`target_section_${index}`);
    targetSection.scrollIntoView({ behavior: "smooth" });
}

const toggleSection = (index) => {
    sections[index].open = !sections[index].open;
};

const togglePopup = (index) => {
    activePopup.value = activePopup.value === index ? null : index;
};

const closePopup = (event) => {
    if (menuContainer.value && !menuContainer.value.contains(event.target)) {
        activePopup.value = null;
    }
};
const toggleListProject = () => {
    if (!page.props.auth.user) {
        return window.location.href = '/login'
    }
    activeListProject.value = !activeListProject.value;
};
const closeListProject = (event) => {
    if (menuContainer.value && !menuContainer.value.contains(event.target)) {
        activeListProject.value = false;
    }
};

const selectOption = ({
    index = 0,
    value,
    indexOptionRewrite
}) => {
    messagesMap['resultTarget'][index].options_rewrite[indexOptionRewrite].value = value;
    messagesMap['resultTarget'][index].options_rewrite[indexOptionRewrite].isOpen = false;
    rewriteContentProductAd(index)
};

onMounted(() => {
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".dropdown")) {
            options.forEach((option) => (option.isDropdownOpen = false));
        }
    });
    document.addEventListener("click", closePopup);
    document.addEventListener("click", closeListProject);
    if (page.props.auth.user) {
        getListProjects();
    }
    sunEditorInstance.value = suneditor.create(editor.value, {
        hideToolbar: true,
        height:500
    });
    sunEditorInstance.value.onChange = function (contents) {
        messagesMap['information_project'] = contents;
    };
    getFacebooksFanpagesUser();
    checkIfMusicForBusiness();
});

onBeforeUnmount(() => {
    document.removeEventListener("click", closePopup);
    document.removeEventListener("click", closeListProject);
});

const handleClickOutside = (event) => {
    if (menuContainer.value && !menuContainer.value.contains(event.target)) {
        activeListProject.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

const selectedProject = ref(listProject[0] || null);
const isSending = ref(false);
const loading = ref(false);




const messagesMap = reactive({});
const conversationId = ref(null)

const analysisActive = computed(() => {
    return messagesMap?.analysis?.find((item) => item.isActive);
});
const checkConversationExit = async (conversationIdp, projectId) => {
    try {
        let query = JSON.stringify({
                    currentStep: `buoc2`,
                    // ...messagesMap['information_project']
                    content: messagesMap['information_project']
                });
    const payload = {
        inputs: {},
        query: query,
        conversation_id: conversationIdp,
        project_id: projectId
    };
    const response = await axios.post(route('ai-business.sendChat'), payload, {
        headers: {
            'Content-Type': 'application/json;charset=UTF-8'
        },
    });

    if (!conversationId.value && response.data.conversation_id) {
                conversationId.value = response.data.conversation_id;
            }
            let messageData = null;
            messageData = response.data.answer.split('\n').map((item) => {
                        return { name: item, isActive: false }
                    });
                    messagesMap['analysis'] = messageData
                console.log("executeRequest ~ messagesMap:", messagesMap)
            await new Promise(resolve => setTimeout(resolve, 100));
            // Gọi API tính credit
            await calculateStreamCredit(
                messageData,
                conversationId.value
            );
            return messageData;
    } catch (error) {
        console.log(error);
    } finally {
        isLoadingAnalysis.value = false;
    }

}
function formatCategories(inputString) {
    // Split the input string by newlines and remove empty lines
    const lines = inputString
        .split('\n')
        .map(line => line.trim())
        .filter(line => line);

    // Initialize result array and temporary storage
    const result = [];
    const categoriesMap = new Map();

    lines.forEach(line => {
        // Check if line contains ": " which indicates it has a parent category
        if (line.includes(': ')) {
            const [category, subItem] = line.split(': ');

            // If this category doesn't exist in our map yet, create it
            if (!categoriesMap.has(category)) {
                categoriesMap.set(category, {
                    name: category,
                    subName: []
                });
            }

            // Add the sub-item to the category
            categoriesMap.get(category).subName.push({
                name: subItem,
                isActive: false
            });
        } else {
            // This is a standalone category
            result.push({
                name: line
            });
        }
    });

    // Add all categories with sub-items to the result array
    categoriesMap.forEach(category => {
        result.unshift(category); // Add categories with sub-items at the beginning
    });

    return result;
}
const projectId = ref(0)
const startChat = async (key) => {
    if (!key) return;
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        return;
    }
    loading.value = true;
    try {
        const query = {
            currentStep: "buoc1",
            ten_san_pham_dich_vu: selectedProject.value.title,
            gioi_thieu_san_pham_dich_vu: selectedProject.value.description,
        }
        const files = [...tempFormProject.value.files.filter((item) => item.active).map((item) => {
            return {
                type: 'document',
                transfer_method: "remote_url",
                url: item.url,
            };
        })];
        if (selectedProject.value.image) {
            files.push({
                type: 'image',
                transfer_method: "remote_url",
                url: selectedProject.value.image,
            });
        }
        const payload = {
            inputs: {},
            query: JSON.stringify(query),
            files,
            project_id:projectId.value || selectedProject.value.id
        };

        if (selectedOption.value) {
            payload.inputs.chude = selectedOption.value;
        }

        // Initialize empty message data
        messagesMap[key] = '';

        const response = await fetch(route('ai-business.sendChatStreaming'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        });

        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let buffer = ''; // Buffer for incomplete chunks
        let messages = '';
        let fullSAnswer = '';
        let isWorkflowFinished = false;
        while (true) {
            const {value, done} = await reader.read();
            if (done) break;

            buffer += decoder.decode(value, {stream: true}); // Keep stream option for partial chunks

            // Process complete lines from buffer
            const lines = buffer.split('\n');
            // Keep the last potentially incomplete line in the buffer
            buffer = lines.pop() || '';

            for (const line of lines) {
                if (line.trim().startsWith('data: ')) {
                    try {
                        // Remove 'data: ' prefix and parse
                        const jsonStr = line.trim().slice(5);
                        const data = JSON.parse(jsonStr);

                        if (data.event === 'message' && data.answer) {
                            messages += data.answer;
                            sunEditorInstance.value.setContents(marked(messages.trim()));
                            fullSAnswer += data.answer;
                        }
                        if (data.event === 'workflow_started' && data.conversation_id) {
                            conversationId.value = data.conversation_id;
                        }
                        if (data.event === 'workflow_finished') {
                            isWorkflowFinished = true;
                            // Đảm bảo đã nhận được toàn bộ content
                            await new Promise(resolve => setTimeout(resolve, 100));
                            // Gọi API tính credit
                            await calculateStreamCredit(
                                fullSAnswer,
                                conversationId.value
                            );
                        }
                    } catch (e) {
                        // Silently ignore parsing errors for incomplete chunks
                        continue;
                    }
                }
            }
        }

        // Process any remaining data in buffer
        if (buffer.trim()) {
            try {
                const data = JSON.parse(buffer.trim().slice(5));
                if (data.event === 'message' && data.answer) {
                    messagesMap[key] += data.answer;
                }
            } catch (e) {
                // Ignore parsing errors in final buffer
            }
        }

        // Update data after streaming completes
        await updateDataGenerative('information_project', {
            conversation_id: conversationId.value,
            answer: messagesMap[key]
        });

        // Check lại một lần nữa nếu chưa tính credit
        if (!isWorkflowFinished && fullSAnswer) {
            await calculateStreamCredit(
                fullSAnswer,
                conversationId.value
            );
        }
    } catch (error) {
        console.error('Error starting conversation:', error);
        toast.error('Có lỗi xảy ra, vui lòng thử lại.');
    } finally {
        loading.value = false;
        isSending.value = false;
    }
};

// Function riêng để gọi API tính credit
const calculateStreamCredit = async (fullAnswer, conversationId) => {
    try {
        const response = await fetch(route('ai-business.credit.calculate-stream'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                content: fullAnswer,
                conversation_id: conversationId
            })
        });
        const result = await response.json();
        if (!result.success) {

        }
        return result;
    } catch (error) {
        console.error('Error calculating stream credit:', error);
        throw error;
    }
};

const updateDataGenerative = async (key, data) => {
    formProject.data_json = {
        ...formProject.data_json,
        [key]: data
    }
    await handleProjectSubmit(false);
}
const continueChat = async (key, query) => {
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        return; // Dừng nếu không đủ credit
    }
    if (isSending.value || !key) return;

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
            let response = null
            response = await axios.post(route('ai-business.sendChat'), payload, {
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8'
                },

            });

            if (!conversationId.value && response.data.conversation_id) {
                conversationId.value = response.data.conversation_id;
            }
            let messageData = null;
            console.log(key)
            switch (key) {
                case 'resultsAnalysis':
                    messageData = response.data.answer;
                    break;
                case 'content_product_ad':
                    messageData = response.data.answer;
                    break;
                case 'music':
                    messageData = response.data.answer;
                    break;
                case 'poetry':
                    messageData = response.data.answer;
                    break;
                case 'analysis':
                    messageData = response.data.answer.split('\n').map((item) => {
                        return { name: item, isActive: false }
                    });
                    messagesMap[key] = messageData
                    break;
                case 'event':
                    messageData = response.data.answer;
                    messagesMap[key] = messageData
                    break;
                case 'categories':
                    messageData = formatCategories(response.data.answer);
                    messagesMap[key] = messageData
                    break;
                case 'ideas':
                    messageData = response.data.answer;
                    messagesMap[key] = messageData
                    break;
                case 'finalTarget':
                    messageData = response.data.answer.split('\n').map((item) => {
                        return { name: item }
                    });
                    messagesMap[key] = messageData
                    break;
                case 'result':
                    messageData = response.data.answer;
                    updateResulTargetContent(messageData)
                    messagesMap[key] = messageData
                    break;
                default:
                    const fixedJson = fixJsonString(response.data.answer);
                    messageData = JSON.parse(fixedJson);
                    if (key !== 'result') {
                        messagesMap[key] = messageData
                    }
                    break;
                }
                console.log("executeRequest ~ messagesMap:", messagesMap)
            await new Promise(resolve => setTimeout(resolve, 100));
            // Gọi API tính credit
            await calculateStreamCredit(
                messageData,
                conversationId.value
            );
            isLoadingAnalysis.value = false;
            return messageData; // Success
        } catch (error) {
            if (retryCount < MAX_RETRIES) {
                retryCount++;
                console.warn(`Retry attempt ${retryCount} of ${MAX_RETRIES}`);

                await new Promise(resolve => setTimeout(resolve, 10000));

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
        throw new Error('Xin vui lòng bấm lại để nhận kết quả');
    }
};
/**
 * Sửa và chuẩn hóa chuỗi JSON không hợp lệ
 * @param {string} jsonString - Chuỗi JSON cần sửa
 * @returns {string|null} - Trả về chuỗi JSON đã sửa hoặc null nếu không thể sửa
 */
 function fixJsonString(jsonString) {
    // Nếu input không phải string, return null
    if (typeof jsonString !== 'string') {
        console.error('Input phải là chuỗi');
        return null;
    }

    try {
        // Thử parse JSON gốc
        JSON.parse(jsonString);
        return jsonString;
    } catch (error) {
        console.log('Đang sửa JSON không hợp lệ...');

        let fixedString = jsonString
            // Bước 1: Xử lý các dấu nháy
            .replace(/^"|"$/g, '')                  // Xóa dấu nháy kép ở đầu và cuối
            .replace(/\\"/g, '"')                   // Sửa escaped quotes
            .replace(/[""]|["]/g, '"')              // Chuẩn hóa smart quotes
            .replace(/['']|[']/g, "'")              // Chuẩn hóa smart single quotes

            // Bước 2: Xử lý escape characters
            .replace(/\\\\/g, '\\')                 // Sửa double escapes
            .replace(/\\n\\n/g, '\\n')             // Chuẩn hóa double newlines
            .replace(/\\t/g, ' ')                   // Thay thế tabs bằng spaces

            // Bước 3: Xử lý các ký tự đặc biệt
            .replace(/[\x00-\x1F\x7F-\x9F]/g, '')  // Xóa control characters
            .replace(/&nbsp;/g, ' ')                // Thay thế &nbsp; bằng space
            .replace(/\s+/g, ' ')                   // Chuẩn hóa khoảng trắng

            // Bước 4: Sửa cấu trúc JSON
            .replace(/([{,]\s*)(\w+)\s*:/g, '$1"$2":')  // Thêm quotes cho keys
            .replace(/,(\s*[}\]])/g, '$1')              // Xóa trailing commas
            .replace(/}\s*{/g, '},{')                   // Sửa định dạng objects
            .replace(/\]\s*\[/g, '],[')                 // Sửa định dạng arrays

            // Bước 5: Kiểm tra và thêm dấu ngoặc nếu cần
            .trim();

        // Kiểm tra và thêm dấu ngoặc tổng thể
        if (!fixedString.startsWith('[') && !fixedString.startsWith('{')) {
            fixedString = '[' + fixedString;
        }
        if (!fixedString.endsWith(']') && !fixedString.endsWith('}')) {
            fixedString = fixedString + ']';
        }

        try {
            // Kiểm tra kết quả cuối cùng
            JSON.parse(fixedString);
            console.log('Đã sửa JSON thành công!');
            return fixedString;
        } catch (finalError) {
            console.error('Không thể sửa JSON:', finalError.message);
            console.error('JSON không hợp lệ tại vị trí:', finalError.message.match(/position (\d+)/)?.[1]);
            return null;
        }
    }
}
const updateResulTargetContent = (content) => {
    const itemReuslt = messagesMap['resultTarget'].findIndex(item => item.resultTargetLoading === true);
    messagesMap['resultTarget'][itemReuslt].content = content;
    messagesMap['resultTarget'][itemReuslt].resultTargetLoading = false;
    messagesMap['resultTarget'][itemReuslt].isStructureOpen = false;
    messagesMap['resultTarget'][itemReuslt].selectedStyle = null;
    messagesMap['resultTarget'][itemReuslt].selectedEditStyle = null;
}
const uploadImage = async (e) => {
    if (!page.props.auth.user) {
        return window.location.href = '/login'
    }
    const file = e.target.files[0];
    if (!file) return;

    // Validate file type
    const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if (!validImageTypes.includes(file.type)) {
        toast.error('File phải là hình ảnh có định dạng: jpeg, png, jpg, gif');
        e.target.value = '';
        return;
    }

    // Validate file size (max 2MB)
    const maxSize = 2 * 1024 * 1024; // 2MB in bytes
    if (file.size > maxSize) {
        toast.error('Kích thước hình ảnh tối đa là 2MB');
        e.target.value = '';
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
        toast.error('Không thể đọc file ảnh');
        e.target.value = '';
        return;
    }

    // If all validations pass
    tempFormProject.value.image_product.file = file;
    tempFormProject.value.image_product.url = URL.createObjectURL(file);
}

// Helper function to get image dimensions
const getImageDimensions = (file) => {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => {
            resolve({
                width: img.width,
                height: img.height
            });
        };
        img.onerror = () => {
            reject(new Error('Failed to load image'));
        };
        img.src = URL.createObjectURL(file);
    });
}
const toggleActiveFile = (index) => {
    tempFormProject.value.files[index].active = !tempFormProject.value.files[index].active;
}
const tempFormProject = ref({
    image_product: {
        url: "",
        file: null,
    },
    files: [],
})
const formProject = reactive({
    id: null,
    title: "",
    description: "",
    image_product: "",
    files: [],
});
const handleProjectSubmit = async (isStartChat = true) => {
    try {
        let isUpdate = false;
        const formData = new FormData();
        if (!formProject.title?.trim()) {
            toast.error('Vui lòng nhập tiêu đề dự án');
            return;
        }

        formData.append('title', formProject.title);
        formData.append('description', formProject.description);
        if (formProject?.data_json) {
            formData.append('data_json', JSON.stringify(formProject.data_json));
        }
        if (tempFormProject.value.image_product.file) {
            formData.append('image_product', tempFormProject.value.image_product.file);
        }
        if (tempFormProject.value.files?.length > 0) {
            formData.append('files', JSON.stringify(tempFormProject.value.files));
        }
        else {
            formData.append('files', JSON.stringify([]));
        }
        let response = null
        if (formProject.id) {
            isUpdate = true;
            formData.append('id', formProject.id);
            response = await axios.post(route('ai-business.update-project'), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                }
            });
        } else {
            response = await axios.post(route('ai-business.create-project'), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                }
            });
        }
        if (response.data) {
            const project = response.data.data
            projectId.value = response.data.data.id
            //set data after create project or update project
            setValue(project, isUpdate)
            if (isStartChat) {
                startChat('information_project')
            }
        }

    } catch (error) {
        console.error('Lỗi:', error);

        // Handle validation errors
        if (error.response?.status === 422) {
            const errors = error.response.data.errors;
            // Show first error message
            const firstError = Object.values(errors)[0][0];
            toast.error(firstError);
        }
        // Handle other errors
        else {
            toast.error(error.response?.data?.message || 'Tạo dự án thất bại!');
        }
    }
};
const setValue = (project, isUpdate) => {
    formProject.id = project.id;
    formProject.title = project.title;
    formProject.description = project.description;
    formProject.image_product = project.image_product;
    formProject.files = project.files;

    tempFormProject.value.image_product.url = project.image_product
    tempFormProject.value.files = project?.files ?? [];
    if (isUpdate) {
        listProject.value = listProject.value?.map((item) => {
            if (item.id === project.id) {
                return project
            }
            return item
        })
    } else {
        listProject.value.push(project)
    }
    selectedProject.value = project
    // const messageMapJson = project?.data_json;
    // if (messageMapJson?.information_project) {
    //     conversationId.value = messageMapJson?.information_project?.conversation_id;
    // }
}
const getListProjects = async () => {
    try {
        const response = await axios.get(route('ai-business.get-list-project'), {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.status === 200) {
            listProject.value = response.data.data.data
            // selectedProject.value = listProject.value[0]

        } else {
            console.error('Không thể lấy danh sách dự án:', response.data.message);
            return [];
        }
    } catch (error) {
        console.error('Lỗi khi gọi API danh sách dự án:', error.response?.data || error.message);
        return [];
    }
}
const handleUploadDocument = async (e) => {
    if (!page.props.auth.user) {
        return window.location.href = '/login'
    }
    const file = e.target.files[0];
    //only accept pdf, doc, docx, xls, xlsx, ppt, pptx, jpg, jpeg, png, gif
    // const allowedExtensions = /(\.pdf|\.doc|\.docx|\.xls|\.xlsx|\.ppt|\.pptx|\.jpg|\.jpeg|\.png|\.gif)$/i;
    // Chỉ cho phép các file văn bản
    const allowedMimeTypes = [
        // Text documents
        'text/plain', // .txt
        // Microsoft Word
        'application/msword', // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
        // PDF
        'application/pdf' // .pdf
    ];
    if (!allowedMimeTypes.includes(file.type)) {
        toast.error('Chỉ chấp nhận các định dạng tài liệu: TXT, DOC, DOCX, PDF');
        return;
    }
    if (!file) return;
    try {
        documentLoading.value = true;
        const formData = new FormData();
        formData.append('file', file);
        const response = await axios.post(route('ai-business.upload-document'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
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
                path: path
            }
            tempFormProject.value.files.push(file);
        }
    } catch (error) {
        console.error('Lỗi khi upload tài liệu:', error.response?.data || error.message);
        toast.error('Upload tài liệu thất bại!');
    }
    finally {
        documentLoading.value = false;
    }
}
const handleRemoveDocument = (index) => {
    tempFormProject.value.files.splice(index, 1);
    formProject.files = tempFormProject.value.files;
}
const selectALlDocument = (e) => {
    tempFormProject.value.files.forEach((item) => {
        item.active = true;
    });
}
const unSelectALlDocument = (e) => {
    tempFormProject.value.files.forEach((item) => {
        item.active = false;
    });
}


const selectProject = async (item) => {
    isLoadingAnalysis.value = true;
    isLoadingCheckConversation.value = true;
    selectedProject.value = item;
    conversationId.value = null
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
    tempFormProject.value.image_product.url = item.image_product;
    tempFormProject.value.files = item.files;
    activeListProject.value = false;
    //get data json
    const messageMapJson = item.data_json;
    if (messageMapJson?.information_project) {
        let notFound = false;
        conversationId.value = messageMapJson.information_project.conversation_id;
        try {
            await checkConversationExit(conversationId.value,formProject.id);
            handleToggleAnalysis()
            handleButtonSelectCreateContent()
            notFound = false;
        } catch (error) {
            console.log(error)
            messagesMap['information_project'] = null
            conversationId.value = null
            notFound = true;
        }
        if (!notFound) {
            sunEditorInstance.value.setContents(marked(messageMapJson.information_project.answer));
            sections[1].open = true;
            sections[0].open = false;
            //scroll to section
            if (category.value) {
                category.value.scrollIntoView({ behavior: "smooth", block:'center',inline: 'center'});
                messagesMap['analysis'] = sampleAnalysis
            }
        }
    } else {
        sections[0].open = true;
    }
    isLoadingCheckConversation.value = false;
}

const handleToggleAnalysis = () => {
    // messagesMap['analysis'][index].isActive = true;
    messagesMap['analysis'].forEach((item, i) => {
        if (item.name == 'Nhạc Doanh nghiệp') {
            item.isActive = true;
        } else {
            item.isActive = false;
        }
        // if (i !== index) {
        //     item.isActive = false;
        // }
    });
    messagesMap.music = null;
    messagesMap.resultTarget = null;
    messagesMap.resultsAnalysis = null;
    messagesMap.content_product_ad = null;
    messagesMap.poetry = null;
}
const handleActiveFinalTarget = (index) => {
    messagesMap['resultTarget'].forEach((item, i) => {
        if (i !== index) {
            item.isActive = false;
        } else {
            item.isActive = true;
        }
    });
}
const getActiveFinalTarget = () => {
    return messagesMap['resultTarget']?.find(item => item.isActive === true);
}

const toggleItemResultAnalysis = async (index, currentSubStep) => {
    const key = 'resultsAnalysis';
    messagesMap.music = {};
    messagesMap.resultTarget = null;
    messagesMap.poetry = null
    messagesMap.content_product_ad = {};
    if (!messagesMap[key]) {
        messagesMap[key] = resultsAnalysisSample;
    }

    messagesMap[key][index].isActive = !messagesMap[key][index].isActive;
    if(messagesMap[key][index].isActive && !messagesMap[key][index].content){
        try {
            // Thay vì set loading, ta khởi tạo content rỗng
            messagesMap[key][index].content = '';
            let dataJson = {
                currentStep: 'buoc3',
                the_loai: analysisActive.value.name
            }
            if (currentSubStep) {
                dataJson = {
                    ...dataJson,
                    currentSubStep
                }
            }
            const result = await continueChatStreaming(key, JSON.stringify(dataJson), index);
            // Không cần gán result vì đã stream trực tiếp vào content
        } catch (error) {
            console.error(error);
        }finally{
            isSending.value = false;
        }
    }

}
const getFormContentProductAd = async () => {
    try {
        const key = "content_product_ad"
        const result = await continueChat(key, JSON.stringify({
            currentStep: `buoc3`,
            the_loai: analysisActive.value.name
        }));
        if (result) {
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

                const formData = [{
                    name: "field_radio",
                    label: "Chọn một tùy chọn",
                    type: "radio",
                    options,
                    value: "",
                }];

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
const getFormPoetry = async () => {
    try {
        const key = "poetry"
        const result = await continueChat(key, JSON.stringify({
            currentStep: `buoc3`,
            the_loai: analysisActive.value.name
        }));
        if (result) {
            try {
                messagesMap[key] = {
                    ...messagesMap[key],
                    formData: JSON.parse(result),
                };
            } catch (jsonError) {
                console.log("getFormPoetry ~ jsonError:", jsonError)
            }
        }
    } catch (error) {
        console.error("Lỗi khi lấy form content product ad:", error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại.");
    } finally {
        isSending.value = false;
    }
};
const handleSubmitMediaCampaign = async () => {
    const key = 'content_product_ad';
    try {
        messagesMap[key].isLoading = true;
        let result = null;
        const analysisActive = messagesMap['analysis'].find((item) => item.isActive);
        if (analysisActive.name === 'Tạo chiến dịch Truyền thông') {
            const activeOptions = messagesMap[key].formData[0].options
                .filter((option) => option.isActive)
                .map((option) => option.value);

            result = await continueChat(
                key,
                JSON.stringify({
                    currentStep: `buoc4`,
                    muc_tieu: activeOptions[0],
                })
            );
        }
        else {
            result = await continueChat(key, JSON.stringify({
            currentStep: `buoc4`,
            ...Object.fromEntries(messagesMap[key].formData.map(item => [item.name, item.value]))
        }));
        }

        if (result) {
            const jsonStartIndex = result.indexOf('{');
            const jsonEndIndex = result.indexOf('}') + 1;

            const jsonPart = result.substring(jsonStartIndex, jsonEndIndex);
            const content = result.substring(jsonEndIndex).trim();

            const jsonData = JSON.parse(jsonPart);
            if (!messagesMap['resultTarget']) {
                messagesMap['resultTarget'] = [];
            }
            messagesMap['resultTarget'].push({
                content: content,
                options_rewrite: [
                    ...Object.keys(jsonData).map((key) => ({
                        name: key,
                        value: '',
                        options: jsonData[key] || [],
                        isOpen: false,
                    })),
                ],
                images: Array.from({ length: 4 }, (_, i) => ({
                    url: '',
                    file: null,
                })),
            });
            sections.forEach((item) => {
                item.open = false;
            })
            await nextTick()
            if(resultTarget.value){
                resultTarget.value.scrollIntoView({ behavior: "smooth",block:'center',inline: 'center'});
            }

        }
    } catch (error) {
        console.error("handleSubmitContentProductAd ~ error:", error);
    } finally {
        messagesMap[key].isLoading = false;
        isSending.value = false;
    }
};
const handleSubmitPoetry = async () => {
    const key = 'poetry';
    try {
        messagesMap[key].isLoading = true;
        const result = await continueChat(key, JSON.stringify({
            currentStep: `buoc4`,
            ...Object.fromEntries(messagesMap[key].formData.map(item => [item.name, item.value]))
        }));
        if (result) {
            const jsonStartIndex = result.indexOf('{');
            const jsonEndIndex = result.indexOf('}') + 1;

            // Extract JSON and content
            const jsonPart = result.substring(jsonStartIndex, jsonEndIndex);
            const content = result.substring(jsonEndIndex).trim();

            const jsonData = JSON.parse(jsonPart);
            console.log("handleSubmitPoetry ~ jsonData:", jsonData)
            if(!messagesMap['resultTarget']){
                messagesMap['resultTarget'] = [];
            }
            messagesMap['resultTarget'].push({
                content: content,
                options_rewrite: [
                    ...Object.keys(jsonData).map((key) => ({
                        name: key,
                        value: '',
                        options: jsonData[key] || [],
                        isOpen: false
                    })),
                    {
                        name: 'keyword',
                        value: '',
                        isOpen: false
                    }
                ],
                images: Array.from({ length: 4 }, (_, i) => ({
                    url: '',
                    file: null,
                })),

            })
            sections.forEach((item) => {
                item.open = false;
            })
            await nextTick();
            if(resultTarget.value){
                resultTarget.value.scrollIntoView({ behavior: "smooth",block:'center',inline: 'center'});
            }

        }
    } catch (error) {
        console.error("Lỗi khi lấy form content product ad:", error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại.");
    } finally {
        messagesMap[key].isLoading = false;
        isSending.value = false;
    }
}
const rewriteContentProductAd = async (index) => {
    const key = 'content_product_ad';
    try {
        messagesMap['resultTarget'][index].isLoadingRewrite = true;
        let allRewrite = {}
        messagesMap['resultTarget'][index].options_rewrite.forEach((item) => {
            allRewrite[convertToSnakeCase(item.name)] = item.value
        })
        const result = await continueChat(key, JSON.stringify({
            currentStep: `buoc5`,
            text: messagesMap['resultTarget'][0].content,
            ...allRewrite
        }));
        if (result) {
            messagesMap['resultTarget'][index].content = result;
        }
    } catch (error) {
        console.log("rewriteContentProductAd ~ error:", error)
    } finally {
        isSending.value = false;
        messagesMap['resultTarget'][index].isLoadingRewrite = false;
    }
}
const handleSubmitMusic = async (type) => {
    const key = 'music';
    try {
        let query = null
        let result = null
        switch (type) {
            case 'overview':
                messagesMap[key] = {
                    ...messagesMap[key],
                    big_ideas: {
                        isLoading: true
                    }
                }
                query = JSON.stringify({
                    "currentStep": "buoc3",
                    "currentSubStep":"buoc3_1",
                    "the_loai": analysisActive?.value?.name || 'Nhạc Doanh nghiệp',
                    "goal": messagesMap[key]?.overview?.formData[0].value,
                    "so_luong": String(messagesMap[key]?.overview?.formData[1].value) || "1",
                    }
                );
                result = await continueChat(key, query);
                if (result) {
                    messagesMap[key] = {
                        ...messagesMap[key],
                        big_ideas: {
                            formData: JSON.parse(result),
                            isLoading: false
                        }
                    }
                }
                break;
            case 'lyric':
                let bigIdeaActive = messagesMap[key].big_ideas.formData.find(item => item.isActive === true);
                if(!bigIdeaActive){
                    toast.error('Vui lòng chọn Big Idea');
                    return;
                }

                let params = {};
                const content = sunEditorInstance.value.getContents();
                params.answer = content
                params.y_tuong = bigIdeaActive.content
                messagesMap[key] = {
                    ...messagesMap[key],
                    lyric: {
                        isLoading: true,
                    },
                };
                result = await continueChat(
                    key,
                    JSON.stringify({
                        currentStep: `buoc4`,
                        ...params,
                    })
                );
                messagesMap[key] = {
                    ...messagesMap[key],
                    lyric: {
                        isLoading: false,
                    },
                };
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
            case 'result':
                let body = {}
                messagesMap[key].isLoading = true;
                messagesMap[key]?.lyric?.formData.forEach((item) =>  {
                    body[item.name] = item.value
                })
                result = await continueChat(key, JSON.stringify({
                    currentStep: `buoc4`,
                    ...body
                }));
                if (result) {
                    const jsonStartIndex = result.indexOf('{');
                    const jsonEndIndex = result.indexOf('}') + 1;

                    // Extract JSON and content
                    const jsonPart = result.substring(jsonStartIndex, jsonEndIndex);
                    const content = result.substring(jsonEndIndex).trim();

                    const jsonData = JSON.parse(jsonPart);
                    if(!messagesMap['resultTarget']){
                        messagesMap['resultTarget'] = [];
                    }
                    messagesMap['resultTarget'].push({
                        content: content,
                        options_rewrite: [
                            ...Object.keys(jsonData).map((key) => ({
                                name: key,
                                value: '',
                                options: jsonData[key] || [],
                                isOpen: false
                            })),
                            {
                                name: 'keyword',
                                value: '',
                                isOpen: false
                            }
                        ],
                    })
                }
                hiddenSocialMedia.value = true;
                messagesMap[key].isLoading = false;
                sections.forEach((item) => {
                    item.open = false;
                })
                await nextTick()
                if(resultTarget.value){
                    resultTarget.value.scrollIntoView({ behavior: "smooth",block:'center',inline: 'center' });
                }

                break;
            default:
                break;
        }
    } catch (error) {
        console.error("handleSubmitContentProductAd ~ error:", error)
    }finally{
        messagesMap[key].isLoading = false;
        isSending.value = false;
    }
}
const getFormContentMusic = async () => {
    try {
        const key = "music"
        messagesMap[key] = {
            overview: {
               isLoading: true
            }
        }
        const result = await continueChat(key, JSON.stringify({
            currentStep: `buoc3`,
            the_loai: 'Nhạc Doanh nghiệp'
        }));
        if (result) {
            messagesMap[key] = {
                ...messagesMap[key],
                overview: {
                    formData: JSON.parse(result),
                    isLoading: false
                }
            }
        }
    } catch (error) {
        console.error('Lỗi khi lấy form content product ad:', error);
        toast.error('Có lỗi xảy ra, vui lòng thử lại.');
    } finally {
        isSending.value = false;
        isLoadingSelectCreateContent.value = true;
        messagesMap[key] = {
            overview: {
               isLoading: false
            }
        }
    }
}
const toggleBigIdeaMusic = (index) => {
    messagesMap['music'].big_ideas.formData[index].isActive = !messagesMap['music'].big_ideas.formData[index]?.isActive;
    messagesMap['music'].big_ideas.formData.forEach((item, i) => {
        if (i !== index) {
            item.isActive = false;
        }
    });
}
const handleButtonSelectCreateContent = async (event) => {
    try {
        messagesMap.music = null;
        messagesMap.resultTarget = null;
        messagesMap.resultsAnalysis = null;
        messagesMap.content_product_ad = null;
        messagesMap.poetry = null;
        isLoadingSelectCreateContent.value = true;
        await getFormContentMusic()
        // switch (analysisActive.value?.name) {
        //     case 'Tạo Bài viết quảng cáo SP':
        //         await getFormContentProductAd();
        //         break;
        //     case 'Tạo thơ Quảng Cáo Sản phẩm':
        //         await getFormPoetry();
        //         break;
        //     case 'Tạo chiến dịch Truyền thông':
        //         await getFormContentProductAd();
        //         break;
        //     case 'Phân tích thị trường':
        //         handleButtonClick('resultsAnalysis', event, target, 2)
        //         break;
        //     case 'Nhạc Doanh nghiệp':
        //         isLoadingSelectCreateContent.value = true;
        //         await getFormContentMusic()
        //         break;
        //     default:
        //         break;
        // }
    } catch (error) {

    } finally {
        isLoadingSelectCreateContent.value = false;
    }
}
const isLoadingAnalysis = ref(false);
const ideaLoading = ref(false);
const eventLoading = ref(false);
const categoriesLoading = ref(false);
const finalTargetLoading = ref(false);
const isLoadingSelectCreateContent = ref(false);
const handleButtonClick = async (key, e, ref = null, sectionIndex = null, type = null) => {
    try {
        if (isSending.value || !key || (messagesMap[key]?.content && key != 'result')) return;
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
            case 'analysis':
                isLoadingAnalysis.value = true;
                query = JSON.stringify({
                    currentStep: `buoc2`,
                    // ...messagesMap['information_project']
                    content: messagesMap['information_project']
                });
                await continueChat('analysis', query);
                handleToggleAnalysis()
                handleButtonSelectCreateContent()
                break;
            case 'resultsAnalysis':
                toggleItemResultAnalysis(0);
                break;
            case 'ideas':
                ideaLoading.value = true;
                await continueChat('ideas', "3A", {
                    text: messagesMap['analysis'].map((item) => `<H1>${item.title}</H1>: ${item.content}`).join('\n')
                });
                break;
            case 'event':
                eventLoading.value = true;
                if (!messagesMap['event']?.trim() || messagesMap['event']?.trim().length === 0) {
                    toast.error('Vui lòng nhập sự kiện bạn muốn chạy chiến dịch quảng cáo sản phẩm này.');
                } else {
                    await continueChat('big_ideas', "3B", {
                        'su_kien': messagesMap['event']
                    });
                }
                break;
            case 'categories':
                categoriesLoading.value = true;
                const big_ideas_active = messagesMap['big_ideas']?.find(item => item.isActive === true);
                if (!big_ideas_active) {
                    toast.error('Vui lòng chọn ý tưởng.');
                } else {
                    await continueChat('categories', "4", {
                        'y_tuong': `**${big_ideas_active.y_tuong}**${big_ideas_active.content}`
                    });
                }
                break;
            case 'finalTarget':
                finalTargetLoading.value = true;
                const categories_active = messagesMap['categories']?.find(item => item.isActive === true);
                if (categories_active?.subName) {
                    const subCategories_active = categories_active?.subName?.find(item => item.isActive === true);
                    if (!subCategories_active) {
                        toast.error('Vui lòng chọn thể loại.');
                    } else {
                        await continueChat('finalTarget', "5", {
                            'the_loai': `${subCategories_active?.name || ""}`
                        });
                    }
                }
                else {
                    if (!categories_active) {
                        toast.error('Vui lòng chọn thể loại.');
                    } else {
                        await continueChat('finalTarget', "5", {
                            'the_loai': `${categories_active?.name || ""}`
                        });
                    }
                }
                break;
            case 'resultTarget':
                resultTargetLoading.value = true;
                const finalTarget_active = messagesMap['finalTarget'].filter(item => item.isActive === true);
                if (finalTarget_active.length === 0) {
                    toast.error('Vui lòng chọn ít nhất 1 mục tiêu.');
                } else {
                    await continueChat('resultTarget', "6", {
                        'muc_tieu': finalTarget_active.map(item => item.name)
                    });
                    sections[5].open = true;
                    if (ref) {
                        ref.scrollIntoView({ behavior: "smooth" });
                    }
                }
                break;
            case 'result':
                const itemReuslt = messagesMap['resultTarget'].findIndex(item => item.resultTargetLoading === true);
                await continueChat('result', "7", {
                    'text': messagesMap['resultTarget'][itemReuslt]?.content || "",
                    'edit': messagesMap['resultTarget'][itemReuslt]?.selectedStyle || messagesMap['resultTarget'][itemReuslt]?.selectedEditStyle || ""
                });
                break;
            default:
                break;
        }
    } catch (error) {
        console.error('Error when handleButtonClick:', error);
        toast.error('Xin vui lòng bấm lại để nhận kết quả.');
    } finally {
        isLoadingAnalysis.value = false;
        categoryLoading.value = false
        targetLoading.value = false
        informationLoading.value = false
        resultTargetLoading.value = false
        ideaLoading.value = false
        eventLoading.value = false
        categoriesLoading.value = false
        finalTargetLoading.value = false
        isSending.value = false;
    }
};
let showBuyCreditPopup = ref(false);
const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};
const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};

const handleBuyCredit = () => {
    window.location.href = '/pricing'
}
const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append('model', "suggestBusiness");
        formData.append('type', 'suggestBusiness');
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
const isLoadingCreateImage = ref(false);

let allowedExtension = ["image/jpeg", "image/jpg", "image/png", "video/mp4"];

const resetImageUpload = (index) => {
    messagesMap['resultTarget'][index].imageRef = null;
    messagesMap['resultTarget'][index].previewImageUpload = null;
    messagesMap['resultTarget'][index].fileImageUpload = null;
};

const resetVideoUpload = (index) => {
    messagesMap['resultTarget'][index].videoRef = null;
    messagesMap['resultTarget'][index].previewVideoUpload = null;
    messagesMap['resultTarget'][index].fileVideoUpload = null;
};

const handleFileChange = async (event, index) => {
    if (event.target?.files?.length === 0) {
        return;
    }

    const file = event.target.files[0];

    if (file.type.includes('image')) {
        handleImageStyle(file, index);
        return;
    }

    if (file.type.includes('video')) {
        handleVideoStyle(file, index);
        return;
    }
};

const handleImageStyle = (file, index) => {
    if (allowedExtension.indexOf(file.type) < 0) {
        toast.error('Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg');
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

        imagePromise.then(
            function (value) {
                let isUpload = false
                messagesMap['resultTarget'][index].images = messagesMap['resultTarget'][index].images.map((image, i) => {
                    if (!image?.previewImageUpload && !isUpload  && !image?.imageRef) {
                        isUpload = true
                        return {
                            previewImageUpload: value,
                            fileImageUpload: file
                        }
                    }
                    return image;
                });
            }
        );
    }

};

const handleVideoStyle = (file, index) => {
    resetVideoUpload(index);
    if (allowedExtension.indexOf(file.type) < 0) {
        toast.error('Vui lòng chọn lại video có định dạng .mp4');
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

        videoPromise.then(
            function (value) {
                messagesMap['resultTarget'][index].previewVideoUpload = value;
                messagesMap['resultTarget'][index].fileVideoUpload = file;
            }
        );
    }
};

const activeFanpage = (fanpage) => {
    facebook_fanpage_id.value = fanpage.id
}

function openPostDetail(content, index) {
    let plainTextContent = content.replace(/<[^>]*>.*?<\/[^>]*>/gs, '').replace(/<[^>]*>/g, '').trim();

    if (postFormRef.value) {
        let postForm = {
            content: plainTextContent,
            social_postable_id: facebook_fanpage_id.value,
            published: 1,
            scheduled_publish_time: null,
            files: [],
        };

        if (uploadType.value === 'image') {
            messagesMap['resultTarget'][index]?.images?.forEach((image) => {
                if (image?.fileImageUpload) {
                    postForm.files.push(image.fileImageUpload);
                }else if(image?.imageRef){
                    postForm.files.push({
                        s3_url: image.imageRef.s3_url,
                        type: 'image'
                    });
                }
            });
        }

        if (uploadType.value === 'video') {
            const video = messagesMap['resultTarget'][index].fileVideoUpload
            const videoRef = messagesMap['resultTarget'][index].videoRef
            if (video) {
                postForm.files = [video]
            }
            if (videoRef) {
                postForm.files = [{
                    s3_url: videoRef.s3_url,
                    type: 'video'
                }];
            }
        }

        postFormRef.value.openPostDetail(postForm);
    }
}
const onSuccessPostForm = () => {

};


const scrollToCreateFormRef = async (index) => {
    handleActiveFinalTarget(index);
    toggleGenerateImage('video', index);
};



const shareAIContent = async (index) => {
    try {
        const content = messagesMap['resultTarget'][index]?.content;
        if (!content) return;
        isLoading.value = true;

        let data = {
            content: content.replace(/<[^>]*>.*?<\/[^>]*>/gs, '').replace(/<[^>]*>/g, '').trim(),
            files: [],
        }

        if (uploadType.value === 'image') {
            const imageRef = messagesMap['resultTarget'][index].imageRef;
            if (imageRef) {
                data.files = [{
                    url: imageRef.s3_url,
                    type: 'image'
                }];
            }
        }

        const social = await axios.post(route("social.store"), data);
        console.log("shareAIContent ~ social:", social)

        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: social.data?.data?.id,
            share_linkable_type: "SocialPost",
        });

        shareLink.value = route("share-link.show", { token: response.data.data.link_token });
        openShareLink();

    } catch (error) {
        toast.error("Chia sẻ tin thất bại");
    } finally {
        isLoading.value = false;
    }
};

const openShareLink = () => {
    isShowShareLinkModal.value = true;
};

const closeShareLink = () => {
    isShowShareLinkModal.value = false;
};


const downloadfile = (index) => {
    let s3Url;
    if (uploadType.value === 'image') {
        const imageRef = messagesMap['resultTarget'][index].imageRef;
        if (imageRef) {
            s3Url = imageRef.s3_url;
        }
        if (!s3Url) {
            toast.error("Không có file để tải");
            return;
        }
    }

    if (uploadType.value === 'video') {
        const videoRef = messagesMap['resultTarget'][index].videoRef;
        if (videoRef) {
            s3Url = videoRef.s3_url;
        }
    }

    var url = route(("ai-video.downloadFile"), { url: s3Url, name: uploadType.value === 'image'?"image.png":'video.mp4' })
    window.open(url, '_blank');
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
        console.error('Error reading from localStorage:', err);
        return [];
    }
};


// 2. Sửa lại fillFormData để check kỹ hơn
const fillFormData = async () => {
    await new Promise(resolve => setTimeout(resolve, 500));

    const allData = getAllFromStorage();
    console.log("All saved data:", allData);

    if (allData && allData.length > 0) {
        let attempts = 0;
        const maxAttempts = 5;

        const tryFillForm = async () => {
            const allForms = document.querySelectorAll('form');
            if (!allForms || allForms.length === 0) {
                console.log("No forms found");
                if (attempts < maxAttempts) {
                    attempts++;
                    await new Promise(resolve => setTimeout(resolve, 500));
                    await tryFillForm();
                }
                return;
            }

            // Duyệt qua tất cả data trong allData
            for (const savedData of allData) {
                if (!savedData?.formData) continue;

                const formDataKeys = Object.keys(savedData.formData);
                const targetForm = Array.from(allForms).find(form => {
                    return formDataKeys.some(key => form.querySelector(`[name="${key}"]`));
                });

                if (targetForm) {
                    Object.entries(savedData.formData).forEach(([key, value]) => {
                        const input = targetForm.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.value = value;
                            ['input', 'change'].forEach(eventName => {
                                input.dispatchEvent(new Event(eventName, {
                                    bubbles: true,
                                    cancelable: true
                                }));
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
    if (messagesMap['info']?.content) {
        fillFormData();
    }
});

const handleSaveGenerationResult = (response,index) => {
    try {
        saveGenerationResult(response?.data, messagesMap['resultTarget'][index]?.content, 'image',index)
    } catch (err) {
        console.error('handleSaveGenerationResult', err);
        return null;
    }
};
const handleSaveGenerationResultVideo = (response,index) => {
    try {
        saveGenerationResult(response, messagesMap['resultTarget'][index]?.content, 'video',index)
    } catch (err) {
        console.error('handleSaveGenerationResult', err);
        return null;
    }
};

const saveGenerationResult = async (dataParam, prompt, type = 'image',index) => {
    const url = dataParam.url || dataParam.generate_file?.s3_url;
    try {
        if(!messagesMap['resultTarget'][index].images){
            messagesMap['resultTarget'][index].images = []
        }
        let isUpload = false
        messagesMap['resultTarget'][index].images = messagesMap['resultTarget'][index].images.map((image, i) => {
            if (!image?.previewImageUpload && !isUpload  && !image?.imageRef) {
                isUpload = true
                return {
                    imageRef: {
                        s3_url: url
                    },
                }
            }
            return image;
        });
        // const baseData = {
        //     project_id: selectedProject.value?.id,
        //     type: type,
        //     prompt: prompt,
        //     metadata: {}
        // };
        // let additionalData = {};
        // switch (type) {
        //     case 'image':
        //         const generateFile = dataParam.generate_file || dataParam.generated_images || {};
        //         resetImageUpload(index);
        //         messagesMap['resultTarget'][index].imageRef = generateFile;
        //         additionalData = {
        //             s3_url: generateFile.s3_url,
        //             path: dataParam.path,
        //             metadata: {
        //                 path: dataParam.path,
        //                 created_at: generateFile.created_at,
        //                 width: generateFile.width,
        //                 height: generateFile.height,
        //             }
        //         };
        //         break;
        //     case 'video':
        //         const videoUrl =dataParam
        //         messagesMap['resultTarget'][index].videoRef = {
        //             s3_url: videoUrl
        //         };
        //         additionalData = {
        //             s3_url: videoUrl,
        //             path: extractRelativePathFromSignedS3Url(videoUrl),
        //             metadata: {
        //                 path: extractRelativePathFromSignedS3Url(videoUrl)
        //             }
        //         };
        //         break;
        //     default:
        //         additionalData = {
        //             metadata: generateFile
        //         };
        // }
        // // Merge base data với additional data
        // const data = {
        //     ...baseData,
        //     ...additionalData
        // };
        // const response = await axios.post(route('ai-business.save-result'), data);
        // if (response.data.success) {
        //     toast.success("Đã lưu kết quả thành công");
        //     return response.data.data;
        // }
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
}

const continueChatStreaming = async (key, query, index = 0) => {
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        return; // Dừng nếu không đủ credit
    }
    if (isSending.value || !key) return;

    isSending.value = true;
    let retryCount = 0;
    const MAX_RETRIES = 5;

    const executeRequest = async () => {
        try {
            const payload = {
                inputs: {},
                query: query,
                conversation_id: conversationId.value,
            };

            if (selectedOption.value) {
                payload.inputs.chude = selectedOption.value;
            }

            // Khởi tạo giá trị ban đầu cho text box dựa vào key
            switch (key) {
                case 'analysis':
                    messagesMap[key] = [{
                        name: 'Đang phân tích...',
                        isActive: false
                    }];
                    break;
                case 'finalTarget':
                    messagesMap[key] = [{
                        name: 'Đang xử lý...'
                    }];
                    break;
                case 'result':
                    const itemResult = messagesMap['resultTarget'].findIndex(item => item.resultTargetLoading === true);
                    if (itemResult !== -1) {
                        messagesMap['resultTarget'][itemResult].content = 'Đang tạo nội dung...';
                    }
                    break;
                case 'resultsAnalysis':
                    if (!messagesMap[key]) {
                        messagesMap[key] = resultsAnalysisSample;
                    }
                    messagesMap[key][index].content = 'Đang phân tích...';
                    break;
                default:
                    messagesMap[key] = 'Đang xử lý...';
                    break;
            }

            const response = await fetch(route('ai-business.sendChatStreaming'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let fullAnswer = '';
            let buffer = ''; // Buffer để tích lũy JSON chưa hoàn chỉnh
            let isWorkflowFinished = false;
            while (true) {
                const {value, done} = await reader.read();
                if (done) break;

                const chunk = decoder.decode(value);
                buffer += chunk;

                // Tìm các dòng hoàn chỉnh trong buffer
                while (true) {
                    const newlineIndex = buffer.indexOf('\n');
                    if (newlineIndex === -1) break; // Không còn dòng hoàn chỉnh

                    const line = buffer.slice(0, newlineIndex);
                    buffer = buffer.slice(newlineIndex + 1);

                    if (line.startsWith('data: ')) {
                        try {
                            const data = JSON.parse(line.slice(6));
                            if (data.answer) {
                                fullAnswer += data.answer;
                                console.log(key)
                                // Cập nhật UI dựa trên loại key
                                switch (key) {
                                    case 'analysis':
                                    case 'finalTarget':
                                        // Chỉ cập nhật khi có dòng hoàn chỉnh
                                        const lines = fullAnswer.split('\n').filter(item => item.trim());
                                        if (lines.length > 0) {
                                            messagesMap[key] = lines.map((item) => ({
                                                name: item,
                                                isActive: false
                                            }));
                                        }
                                        break;
                                    case 'result':
                                        const itemResult = messagesMap['resultTarget'].findIndex(item => item.resultTargetLoading === true);
                                        if (itemResult !== -1) {
                                            messagesMap['resultTarget'][itemResult].content = fullAnswer;
                                        }
                                        break;
                                    case 'resultsAnalysis':
                                        if (messagesMap[key]?.[index]) {
                                            messagesMap[key][index].content = fullAnswer;
                                        }
                                        break;
                                    case 'categories':
                                        // Chỉ format categories khi có đủ dữ liệu
                                        if (fullAnswer.includes('\n')) {
                                            messagesMap[key] = formatCategories(fullAnswer);
                                        }
                                        break;
                                    case 'information_project':
                                        if (typeof messagesMap[key] === 'string') {
                                            messagesMap[key] = fullAnswer;
                                            // sunEditorInstance.value.setContents(marked(fullAnswer));
                                        }
                                        break;
                                    default:
                                        if (typeof messagesMap[key] === 'string') {
                                            messagesMap[key] = fullAnswer;
                                        } else {
                                            // Chỉ parse JSON khi nhận được dấu } cuối cùng
                                            if (fullAnswer.trim().endsWith('}')) {
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
                            if (data.event === 'workflow_started' && data.conversation_id) {
                                conversationId.value = data.conversation_id;
                            }
                            if (data.event === 'workflow_finished') {
                                isWorkflowFinished = true;
                                // Đảm bảo đã nhận được toàn bộ content
                                await new Promise(resolve => setTimeout(resolve, 100));
                                // Gọi API tính credit
                                await calculateStreamCredit(
                                    fullAnswer,
                                    conversationId.value
                                );
                            }
                        } catch (e) {
                            // Bỏ qua lỗi parse JSON không hoàn chỉnh
                            continue;
                        }
                    }
                }
            }
            // Check lại một lần nữa nếu chưa tính credit
            if (!isWorkflowFinished && fullAnswer) {
                await calculateStreamCredit(
                    fullAnswer,
                    conversationId.value
                );
            }

            return fullAnswer;

        } catch (error) {
            console.log("executeRequest ~ error:", error);
            if (retryCount < MAX_RETRIES) {
                retryCount++;
                console.warn(`Retry attempt ${retryCount} of ${MAX_RETRIES}`);
                await new Promise(resolve => setTimeout(resolve, 10000));
                return false;
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
        throw new Error('Xin vui lòng bấm lại để nhận kết quả');
    }
};

const getFacebooksFanpagesUser = async () => {
    try {
        const response = await axios.get(route('get-facebooks-fanpages-user'))
        fanpages.value = response.data.data
        activeFanpage(response.data?.data[0])
    } catch (error) {
        console.error(error);
    }
};

// Hàm xử lý xóa dự án
const deleteProject = async (item) => {
    if (!confirm(`Bạn có muốn xóa dự án ${item.title}?`)) {
        return;
    }
    try {
        // Tiến hành xóa project
        const response = await axios.delete(route('ai-business.projects.destroy', item.id));

        if (response.data.success) {
            toast.success('Xóa dự án thành công');
            if (page.props.auth.user) {
                listProject.value = listProject.value.filter(p => p.id !== item.id);
                await getListProjects();
            }
        }

    } catch (error) {
        console.error("Lỗi khi xóa dự án:", error);

        if (error.response) {
            switch (error.response.status) {
                case 404:
                    toast.error('Không tìm thấy dự án này');
                    // Cập nhật lại danh sách khi project không tồn tại
                    listProject.value = listProject.value.filter(p => p.id !== item.id);
                    break;

                case 403:
                    toast.error('Bạn không có quyền xóa dự án này');
                    break;

                default:
                    toast.error(error.response.data.message || "Đã xảy ra lỗi khi xóa dự án");
            }
        } else if (error.request) {
            toast.error("Không thể kết nối đến server");
        } else {
            toast.error("Lỗi: " + error.message);
        }
    }
};
</script>



<style>
    .p-editor {
        --p-editor-content-color: #000000;
        /* Đặt màu mặc định cho văn bản */
        font-size: 14px;
    }
    .sun-editor-editable p, .sun-editor-editable h1, .sun-editor-editable h2, .sun-editor-editable h3, .sun-editor-editable h4, .sun-editor-editable h5, .sun-editor-editable h6,
    .sun-editor-editable ul, .sun-editor-editable ol, .sun-editor-editable li, .sun-editor-editable blockquote, .sun-editor-editable pre, .sun-editor-editable code, .sun-editor-editable table, .sun-editor-editable tr, .sun-editor-editable th, .sun-editor-editable td
    {
        font-size: 15px;
    }
    .sun-editor-editable h1 {
        color: #FF5733 !important;
        font-weight: bold;
    }
    .sun-editor {
        flex: 1 !important;
        border-radius: 12px;
        overflow: hidden;
    }
</style>
