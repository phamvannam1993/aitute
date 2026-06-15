<template>
    <Head title="AI Bán Hàng - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div class="w-full mb-8 lg:mt-0">
        <ProjectHeader v-model:mode="mode" @select-project="selectProject" @new-project="newProject" @show-history="showHistory" :showModalHistory="showModalHistory" />
        <ProjectExpert :project="projectExpert" :key="projectExpertKey" v-if="mode == 'expert'" ref="refProjectExpert" />
        <ProjectBusinessHistory v-if="showModalHistory" />
        <div v-else>
            <!-- Section 1 -->
            <div class="bg-white shadow-xl lg:rounded-[35px]">
                <div @click="toggleSection(0)" class="cursor-pointer md:px-[80px] md:py-[20px] p-3 md:max-w-full line-clamp-1 flex items-center text-xs lg:text-sm text-black">
                    <Step class-name="md:text-[20px]" class="flex-shrink-0" :step="1" stepName="Thông tin sản phẩm" />
                </div>
                <hr v-if="sections[0].open || (!sections[0].open && selectedProject && !isLoadingCheckConversation)" class="mb-4 border-gray-300 w-full border-b-[1px]">
                <div :class="sections[0].open ? 'flex-col rounded-[35px] items-start' : 'flex-row rounded-full items-center hidden'" class="md:px-[80px] md:py-[20px] p-3 flex justify-between text-black">
                    <div :class="sections[0].open && !isLoadingCheckConversation ? 'block w-full' : 'absolute opacity-0 invisible'">
                        <form @submit.prevent="handleFillBasicInformation">
                            <div>
                                <p class="text-sm md:text-[19px] font-bold">1. Nhập tên sản phẩm <span class="text-red-500">*</span></p>
                                <input v-model="formProject.title" type="text" placeholder="Nhập tên sản phẩm...." class="placeholder-italic text-xs md:text-[16px] w-full rounded-lg lg:rounded-2xl border-[#D4D4D4] mt-1.5 lg:py-[22px] lg:px-7" />
                                <div v-show="mode != null">
                                    <p class="text-sm md:text-[19px] font-bold mt-4">2. Mô tả ngắn ngọn về sản phẩm</p>
                                    <textarea v-model="formProject.description" placeholder="Mô tả các tính năng và lợi ích của sản phẩm..." rows="4" class="placeholder-italic resize-none mt-1.5 text-sm md:text-[16px] w-full rounded-lg lg:rounded-2xl border-[#D4D4D4] lg:py-[22px] lg:px-7"></textarea>
                                    <div class="md:w-[50%] w-full flex flex-col items-center mt-6 lg:m-0">
                                        <div class="w-full flex md:justify-start justify-between">
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

                                        <span class="text-sm md:text-[16px] text-gray-400 self-start inline-block mb-1 italic"> Tải lên file mô tả chi tiết sản phẩm (PDF, Word, Excel, PowerPoint). </span>
                                        <span class="text-sm text-white self-start inline-block mb-1">.</span>
                                        <div v-if="tempFormProject?.files?.length > 0" class="flex flex-col items-center border-gray-300 border-2 rounded-[20px] h-fit w-full p-4 overflow-hidden">
                                            <div class="flex flex-col gap-3 h-5/6 w-full overflow-y-scroll">
                                                <div class="flex items-center w-full gap-2" v-for="(file, index) in tempFormProject.files" :key="index">
                                                    <div class="size-6 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all" :class="file.active ? 'border-[#FFA411]' : 'border-[#D4D4D4]'" @click="toggleActiveFile(index)">
                                                        <div class="size-[15px] bg-[#FFA411] rounded-full" v-if="file.active"></div>
                                                    </div>
                                                    <div class="flex items-center gap-2 rounded-full text-black flex-1 px-[10px] py-[6px] transition-all" :class="file.active ? 'bg-[#FFF9D4]' : 'bg-[#F9F9F9]'">
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
                                </div>
                            </div>
                            <div class="pb-4 border-b-[3px] border-[#d6d6d6]">
                                <!-- Tạm ẩn -->

                                <div class="mt-4">
                                    <p class="text-sm md:text-[19px] font-bold mt-4">3. Link website/sản phẩm</p>
                                    <input id="product-link" v-model="formProject.productLink" type="text" placeholder="Link..." class="placeholder-italic text-sm md:text-[16px] w-full rounded-lg lg:rounded-2xl text-black border-[#D4D4D4] mt-1.5 lg:py-[8px] lg:px-[24px]" />
                                </div>
                                <p class="text-sm md:text-[19px] font-bold mt-4">4. Tải hình ảnh sản phẩm</p>
<!--                                <span class="text-sm text-gray-400 self-start inline-block mb-1 italic">Tải hình ảnh dựa trên 2 tiêu chí</span>-->
                                <div class="lg:grid grid-cols-2 gap-10 mt-6">
                                    <div class="flex flex-col items-center p-4 border-2 border-gray-300 rounded-3xl">
                                        <div class="w-full flex justify-between">
                                            <p class="text-sm md:text-[16px] font-bold leading-6">Ảnh sản phẩm</p>
                                            <div  class="rounded-lg justify-center flex items-center ">
                                                <label for="image-product" class="flex items-center rounded-lg justify-center bg-orion-orange cursor-pointer text-sm md:text-[19px] w-[26px] h-[26px] text-aiwow-sec">
                                                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M16.5 11.3359L16.5 14.3359C16.5 14.7338 16.342 15.1153 16.0607 15.3966C15.7794 15.6779 15.3978 15.8359 15 15.8359L4.5 15.8359C4.10218 15.8359 3.72064 15.6779 3.43934 15.3966C3.15804 15.1153 3 14.7338 3 14.3359L3 11.3359" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M13.5 6.08594L9.75 2.33594L6 6.08594" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M9.75 2.33594L9.75 11.3359" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
<!--                                                    <img src="/assets/svgs/upload_white.svg" alt="">-->
                                                    <input type="file" class="hidden" id="image-product" @change="uploadImage" />
                                                </label>
                                                <span class="flex items-center rounded-lg justify-center bg-orion-orange cursor-pointer text-sm md:text-[16px] w-[80px] h-[26px] text-black ml-2" @click="showImageList(2)">
                                                    Kho ảnh
                                                </span>
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-400 self-start inline-block mb-1 italic">Tải ảnh sản phẩm hoặc chọn ảnh tại kho ảnh sản phẩm.</span>
                                        <div v-if="tempFormProject.image_product.url" class="flex items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[16/9] w-full h-full overflow-hidden h-image">
                                            <img :src="tempFormProject.image_product.url" alt="" class="object-cover h-image" />
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center mt-6 lg:m-0 p-4 border-2 border-gray-300 rounded-3xl">
                                        <div class="w-full flex justify-between">
                                            <p class="text-sm md:text-[16px] font-bold leading-6">Ảnh người mẫu</p>
                                            <div  class="rounded-lg justify-center flex items-center ">
                                                <label for="image-model" class="flex items-center rounded-lg justify-center bg-orion-orange cursor-pointer text-sm md:text-[19px] w-[26px] h-[26px] text-aiwow-sec">
                                                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M16.5 11.3359L16.5 14.3359C16.5 14.7338 16.342 15.1153 16.0607 15.3966C15.7794 15.6779 15.3978 15.8359 15 15.8359L4.5 15.8359C4.10218 15.8359 3.72064 15.6779 3.43934 15.3966C3.15804 15.1153 3 14.7338 3 14.3359L3 11.3359" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M13.5 6.08594L9.75 2.33594L6 6.08594" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M9.75 2.33594L9.75 11.3359" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
<!--                                                    <img src="/assets/svgs/upload_white.svg" alt="">-->
                                                    <input type="file" class="hidden" id="image-model" @change="uploadModelImage" />
                                                </label>
                                                <span class="flex items-center rounded-lg justify-center bg-orion-orange cursor-pointer text-sm md:text-[16px] w-[80px] h-[26px] text-black ml-2" @click="showImageList(1)">
                                                    Kho ảnh
                                                </span>
                                            </div>
                                        </div>

                                        <span class="text-sm text-gray-400 self-start inline-block mb-1 italic">Tải ảnh người mẫu hoặc chọn ảnh tại kho ảnh avatar.</span>
                                        <div v-if="tempFormProject.model_product.url" class="flex items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden h-image">
                                            <img :src="tempFormProject.model_product.url" alt="" class="object-cover h-image" />                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-center gap-4 mt-4 mb-4">
                                <button type="submit" class="mg-b-20px h-[40px] md:h-[50px] text-sm md:text-[17px] px-4 bg-ai3goc-bgclr text-white rounded-lg lg:rounded-2xl font-bold" :disabled="loading">
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

                    <div @click="toggleSection(0)" :class="sections[0].open ? 'self-end' : ''" class="flex-shrink-0 text-ai3goc-primary font-medium flex items-center cursor-pointer text-sm md:text-sm icon-hidden-data">
                        <span class="hidden md:block">{{ sections[0].open ? "Thu gọn" : "" }}</span>
                        <svg v-if="sections[0].open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': sections[0].open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
                <LoadingCircle v-if="isLoadingCheckConversation" />
                <div :class="(!sections[0].open && selectedProject && !isLoadingCheckConversation) ? 'md:px-[80px] md:pb-[20px]' : ''">
                    <div v-if="!sections[0].open && selectedProject && !isLoadingCheckConversation" class="bg-orion-orange-hover rounded-full p-2 mx-auto mb-2">
                        <div class="flex items-center">
                            <img src="/assets/img/my_assistant/p_red.png" alt="Project Icon" class="lg:w-12 w-8 lg:h-12 h-8 rounded-full mr-4" />
                            <div>
                                <h2 class="lg:text-2xl text-base font-semibold text-black line-clamp-1">{{ selectedProject?.title }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow-xl lg:rounded-[35px]">
                <div @click="toggleSection(1)" class="cursor-pointer md:px-[80px] md:py-[20px] p-3 flex items-center gap-2 mb-2 text-sm lg:text-sm mt-2 text-black">
                    <Step class-name="md:text-[20px]" class="flex-shrink-0" :step="mode != 'basic' ? 2 : 2" step-name="Thiết lập thông tin và hiệu chỉnh" />
                </div>
                <hr v-if="sections[1].open" class="border-gray-300 w-full border-b-[1px]">
                <div ref="custom" :class="sections[1].open ? 'flex-col items-start' : 'hidden flex-row lg:rounded-full items-center p-3'" class="flex justify-between mt-4">
                    <div  class="w-full lg:w-4/5 self-center p-3" :class="sections[1].open ? '' : 'hidden'">
                        <div class="py-2">
                            <div class="flex flex-col">
                                <!-- Container cho editor với id cố định -->
                                <div id="editor-container" class="w-full border border-gray-200 rounded-lg mb-4"></div>
                            </div>
                        </div>
                        <div v-if="sections[1].open" class="w-full self-center">
                            <ContentProductAd v-if="messagesMap['content_product_ad']" :form-data="messagesMap['content_product_ad']?.formData" :handle-button-click="mode == 'basic' ? handleSubmitMediaCampaign : handleSubmitMediaCampaignV2" :selectedContent="analysisActive?.name" :show-title="false" :loadingSubmit="isLoadingSelectCreateContent"/>
                        </div>
                    </div>
                    <div  @click="toggleSection(1)" :class="sections[1].open ? 'self-end' : ''" class="md:px-[80px] md:py-[20px] p-3 flex-shrink-0 text-ai3goc-primary font-medium flex items-center cursor-pointer text-sm md:text-sm hidden-data-2">
                        <span class="hidden md:block">{{ sections[1].open ? "Thu gọn" : "" }}</span>
                        <svg v-if="sections[1].open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': sections[1].open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
             <!-- Section 3 -->
            <div v-if="mode != 'basic'" ref="category" :class="sections[2].open ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'" class="hidden bg-white shadow-xl flex justify-between mt-4">
                <div @click="toggleSection(2)" class="cursor-pointer w-full md:px-[80px] md:py-[20px] p-3 flex items-center text-sm lg:text-[19px] text-black">
                    <Step class-name="md:text-[20px]" class="flex-shrink-0" :step="mode != 'basic' ? 3 : 3" step-name="Chọn thể loại nội dung" />
                </div>
                <hr v-if="sections[2].open" class="border-gray-300 w-full border-b-[1px]">
                <div v-if="sections[2].open" class="w-full lg:w-4/5 self-center p-3">
                    <!-- Options -->
                    <LoadingCircle v-if="isLoadingAnalysis" />
                    <div class="flex flex-col gap-4 mt-6" v-if="messagesMap['analysis'] && !isLoadingAnalysis">
                        <template v-for="(item, index) in messagesMap['analysis']" :key="index">
                            <div class="flex items-center gap-5 lg:px-[90px]">
                                <div @click.stop="handleToggleAnalysis(index)" :class="item.isActive ? 'border-orion-orange' : 'border-gray-400'" class="flex-shrink-0 size-6 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all">
                                    <div class="size-[15px] bg-orion-orange rounded-full" v-if="item.isActive"></div>
                                </div>
                                <div class="relative w-full">
                                    <div @click.stop="handleToggleAnalysis(index)" :class="item.isActive ? 'border-orion-orange bg-[#FFF9D4]' : 'border-gray-400'" class="cursor-pointer relative flex items-center justify-between border border-gray-300 rounded-2xl text-black px-4 py-2 flex-1 gap-1 h-[40px] transition-all">
                                        <span class="select-none text-sm md:text-[19px] font-semibold">{{ showItemName(index, item) }}</span>
                                    </div>
                                    <div v-if="showDropdownCampaign && item.name == 'Chiến dịch Quảng cáo sản phẩm'"
                                         class="absolute right-0 border-2 border-[#E6E6E6] top-[40px] bg-white rounded-3xl shadow-lg w-[200px] lg:w-[280px] text-black z-50">
                                        <ul @click="handleChangeMaxPost(indexMenu)" v-for="(itemMenu, indexMenu) in formProject.menu" class="flex items-center cursor-pointer" @click.stop>
                                            <div :class="itemMenu.isActive ? 'border-orion-orange' : 'border-[#D4D4D4]'" class="ml-2 flex-shrink-0 size-6 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all">
                                                <div class="size-[15px] bg-orion-orange rounded-full" v-if="itemMenu.isActive"></div>
                                            </div>
                                            <div :class="itemMenu.isActive ? 'bg-[#FFF9D4]' : 'bg-[#F9F9F9]'" class="text-sm md:text-[19px] flex items-center cursor-pointer py-2 px-2 mx-2 rounded-2xl">{{ itemMenu.title }}</div>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div class="flex justify-center gap-1 items-center mt-5">
                            <div class="flex flex-col items-center justify-center gap-5">
                                <button class="text-sm md:text-[17px] relative flex items-center justify-center h-[40px] md:h-[50px] place-items-center w-full max-w-[180px] px-4 bg-ai3goc-bgclr text-white rounded-2xl font-bold mg-b-20px" v-if="['Bài viết Quảng cáo sản phẩm', 'Thơ Quảng cáo sản phẩm', 'Nhạc Doanh nghiệp', 'Chiến dịch Quảng cáo sản phẩm'].includes(analysisActive?.name) && mode != 'basic'" @click="handleStep2()">
                                    <span v-if="!isLoadingSelectCreateContent"> Xác nhận </span>
                                    <LoadingCircle v-else class="!size-6" />
                                    <div v-if="isLoadingSelectCreateContent" class="text-sm md:text-sm font-medium text-center p-0.5 leading-none rounded-full transition-all duration-300">{{ loadingPercent }} %</div>
                                </button>
                                <button v-else :disabled="isLoadingSelectCreateContent" type="button" @click="handleButtonSelectCreateContent" class="text-sm md:text-base relative flex gap-1 items-center justify-center h-[40px] md:h-[50px] place-items-center w-full max-w-[180px] px-4 bg-ai3goc-bgclr text-white rounded-2xl font-bold">
                                    <span v-if="!isLoadingSelectCreateContent" class="text-sm md:text-[19px]"> Xác nhận </span>
                                    <LoadingCircle v-else class="!size-6" />
                                    <div v-if="isLoadingSelectCreateContent" class="text-sm md:text-sm font-medium text-center p-0.5 leading-none rounded-full transition-all duration-300">{{ loadingPercent }} %</div>
                                </button>
                            </div>
                            <!-- <div class="flex justify-center items-center" v-if="!['Phân tích thị trường', 'Nhạc Doanh nghiệp'].includes(analysisActive?.name) && mode != 'basic'">
                                <div>hoặc</div>
                                <div>
                                    <button class="text-sm md:text-base relative flex items-center justify-center h-[40px] md:h-[50px] place-items-center w-full max-w-[280px] px-4 bg-ai3goc-bgclr text-white rounded-2xl font-bold" @click="openPostDetail(item?.content ?? '', null, true)">
                                        <span> AI AGENT tự động tạo nội dung và tự động đăng bài </span>
                                    </button>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div @click="toggleSection(2)" :class="sections[2].open ? 'self-end' : ''" class="md:px-[80px] md:py-[20px] p-3 text-ai3goc-primary mt-2 font-medium flex items-center cursor-pointer text-sm md:text-sm hidden-data-2">
                    <span class="hidden md:block">{{ sections[2].open ? "Thu gọn" : "" }}</span>
                    <svg v-if="sections[2].open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': sections[1].open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <div v-if="mode != 'basic' && analysisActive?.name === 'Nhạc Doanh nghiệp'">
                <div ref="custom" :class="sections[6].open ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'" class="bg-white shadow-xl flex justify-between mt-4">
                    <div @click="toggleSection(6)" class="cursor-pointer w-full md:px-[80px] md:py-[20px] p-3 flex items-center text-sm lg:text-sm text-black">
                        <Step class-name="md:text-[20px]" class="flex-shrink-0" :step="3" step-name="Tạo ý tưởng tổng quan" />
                    </div>
                    <hr v-if="sections[6].open" class="border-gray-300 w-full border-b-[1px]">
                    <div class="md:px-[80px] md:py-[20px] p-3 w-full self-center" v-if="sections[6].open">
                        <MusicStep v-if="messagesMap['music']" :data="messagesMap['music']" :handle-button-click="handleSubmitMusic" :toggleBigIdeaMusic="toggleBigIdeaMusic" :loading="messagesMap['music']?.isLoading" />
                    </div>
                    <div v-if="sections[6].open" @click="toggleSection(6)" :class="sections[6].open ? 'self-end' : ''" class="md:px-[80px] md:py-[20px] p-3 flex-shrink-0 text-ai3goc-primary font-medium flex items-center cursor-pointer text-sm md:text-sm">
                        <span class="hidden md:block">{{ sections[6].open ? "Thu gọn" : "Hiển thị" }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': sections[6].open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div v-if="mode != 'basic' && analysisActive?.name === 'Chiến dịch Quảng cáo sản phẩm'">
                <div ref="custom" :class="sections[7].open ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'" class="bg-white shadow-xl flex justify-between mt-4">
                    <div @click="toggleSection(7)" class="cursor-pointer md:px-[80px] md:py-[20px] p-3 w-full flex items-center text-sm lg:text-sm text-black">
                        <Step class-name="md:text-[20px]" class="flex-shrink-0" :step="3" step-name="Chọn mục tiêu Chiến dịch truyền thông" />
                    </div>
                    <hr v-if="sections[7].open" class="border-gray-300 w-full border-b-[1px]">
                    <div class="w-full self-center md:px-[80px] md:pb-[20px] p-3" v-if="sections[7].open">
                        <ContentProductAd v-if="messagesMap['content_product_ad_v2']" :form-data="messagesMap['content_product_ad_v2']?.formData" :handle-button-click="handleButtonSelectCreateContent" :loading-submit="messagesMap['content_product_ad_v2']?.isLoading" :selectedContent="analysisActive?.name" />
                    </div>
                    <div v-if="sections[7].open" @click="toggleSection(7)" :class="sections[7].open ? 'self-end' : ''" class="md:px-[80px] md:py-[20px] p-3 flex-shrink-0 text-ai3goc-primary font-medium flex items-center cursor-pointer text-sm md:text-sm">
                        <span class="hidden md:block">{{ sections[7].open ? "Thu gọn" : "" }}</span>
                        <svg v-if="sections[7].open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': sections[7].open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>


            <div v-if="isGenAIStatus == false">
                <AnalysisStep :mode="mode" v-if="analysisActive?.name === 'Phân tích thị trường'" :open="sections[3].open" :list="messagesMap['resultsAnalysis']" :handleSubmit="handleButtonClick" :loadingSubmit="eventLoading" :toggleItem="toggleItemResultAnalysis" :toggleSection="() => toggleSection(3)" :refScroll="resultsAnalysis" />
                <div v-if="analysisActive?.name !== 'Chiến dịch Quảng cáo sản phẩm'" :class="analysisActive?.name === 'Phân tích thị trường' && 'hidden'">
                    <!-- Section 3 -->
                    <LoadingCircle v-if="resultTargetLoading" loading-title="Hệ thống đang xử lý. Xin vui lòng đợi." />
                    <!-- Result 1 -->
                    <div ref="resultTarget" class="">
                        <div :class="true ? 'flex-col lg:rounded-[35px]' : 'flex-row lg:rounded-full'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex items-center justify-between mt-4" id="box-content">
                            <div class="flex items-center self-start justify-between w-full text-sm lg:text-sm text-black">
                                <Step class-name="md:text-[20px]" class="flex-shrink-0" :step="analysisActive?.name === 'Nhạc Doanh nghiệp' ? '4' : ['Bài viết Quảng cáo sản phẩm', 'Thơ Quảng cáo sản phẩm'].includes(analysisActive?.name) && mode != 'basic' ? 3 : mode != 'basic' ? 3 : 3" step-name="Xem kết quả và hiệu chỉnh" />
                            </div>
                            <div v-for="(item, index) in messagesMap['resultTarget']" :key="index" class="w-full lg:w-4/5 mt-10 md:mt-5 text-black text-sm lg:text-[15px] relative">
                                <div class="lg:items-center justify-between mb-4" v-if="index == 0">
                                    <div class="grid grid-cols-2 md:grid-cols-4 lg:items-center gap-2 w-full">
                                        <template v-for="(option, indexOptionRewrite) in item.options_rewrite" :key="indexOptionRewrite">
                                            <div class="w-full" v-if="option.name !== 'keyword'">
                                                <SelectRadix :disabled="item.isLoadingRewrite" :options="option.options" :selected="option.value" :placeholder="option.value || option.name" :handle-change="(value) => selectOption({ value: value, indexOptionRewrite: indexOptionRewrite, index: index })" />
                                            </div>
                                            <div v-if="option.name === 'keyword'" class="absolute -top-10 md:top-[57px] right-24 md:right-[8rem] flex items-center gap-2">
                                                <DropdowRadix :disabled="item.isLoadingRewrite" :open="option.isOpenKeyword" @update:open="option.isOpenKeyword = $event">
                                                    <template #trigger>
                                                        <button type="button" class="hidden md:flex items-center justify-between gap-2 w-full px-6 py-2.5 bg-white border border-gray-200 rounded-full shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ai3goc-primary relative">
                                                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g clip-path="url(#clip0_1641_27040)">
                                                                <path d="M12.3238 2.36588L9.34221 0.190202C9.13539 0.0414306 8.87696 -0.0220012 8.62263 0.0135743C8.3683 0.0491498 8.13848 0.180849 7.98282 0.380325L6.35156 2.51184L10.8829 5.83289L12.5141 3.70138C12.6699 3.49949 12.7374 3.24561 12.7017 2.9953C12.666 2.74499 12.5301 2.51863 12.3238 2.36588Z" fill="#207A91"/>
                                                                <path d="M0.38018 10.5858L0.0131191 13.5929C-0.0198275 13.8167 0.00955975 14.045 0.0981361 14.2539C0.186713 14.4629 0.331238 14.6447 0.516584 14.7804C0.70193 14.916 0.921264 15.0005 1.15158 15.025C1.3819 15.0495 1.61471 15.0131 1.82564 14.9195L4.67584 13.7211C4.83458 13.6581 4.977 13.5615 5.09274 13.4381L0.561443 10.1348C0.468507 10.2707 0.406674 10.4245 0.38018 10.5858Z" fill="#207A91"/>
                                                                <path d="M10.3026 6.59346L5.63083 12.696L1.08594 9.37942L5.7577 3.28125L10.3026 6.59346Z" fill="#207A91"/>
                                                                <path d="M14.9234 12.8828H8.43454C7.83643 12.8828 7.35156 13.356 7.35156 13.9397V13.9441C7.35156 14.5278 7.83643 15.001 8.43454 15.001H14.9234C15.5215 15.001 16.0063 14.5278 16.0063 13.9441V13.9397C16.0063 13.356 15.5215 12.8828 14.9234 12.8828Z" fill="#207A91"/>
                                                                <path d="M14.919 9.21094H10.6142C10.0161 9.21094 9.53125 9.68413 9.53125 10.2678V10.2723C9.53125 10.856 10.0161 11.3292 10.6142 11.3292H14.919C15.5171 11.3292 16.0019 10.856 16.0019 10.2723V10.2678C16.0019 9.68413 15.5171 9.21094 14.919 9.21094Z" fill="#207A91"/>
                                                                </g>
                                                                <defs>
                                                                <clipPath id="clip0_1641_27040">
                                                                <rect width="16" height="15" fill="white"/>
                                                                </clipPath>
                                                                </defs>
                                                            </svg>

                                                            <span class="text-sm md:text-[14px] text-ai3goc-primary font-bold line-clamp-1">Từ khóa</span>
                                                        </button>
                                                    </template>
                                                    <template #content>
                                                        <div class="flex flex-col lg:mt-0 w-[270px] bg-white border border-gray-200 rounded-lg shadow-xl z-50 p-2 px-3">
                                                            <textarea placeholder="Nhập từ khóa" v-model="option.value" class="text-sm border-none rounded-[10px] bg-[#F5F5F5] h-[66px] w-[243px]"></textarea>
                                                            <div class="flex items-center justify-between h-[30px] mt-4">
                                                                <button
                                                                    type="button"
                                                                    class="text-[#8A8A8A] text-sm border border-gray-300 rounded-lg h-full px-3"
                                                                    @click="
                                                                        option.isOpenKeyword = !option.isOpenKeyword;
                                                                        option.value = '';
                                                                    "
                                                                >
                                                                    Huỷ
                                                                </button>
                                                                <button
                                                                    type="button"
                                                                    class="text-sm bg-ai3goc-bgclr rounded-lg text-white h-full px-3"
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
                                        </template>
                                    </div>
                                </div>
                                <div class="w-full" v-if="index == 0">
                                    <div class="flex flex-col gap-4">
                                        <div class="flex justify-between items-center">
                                            <p class="text-ai3goc-bgclr text-[15px] font-bold">{{ analysisActive?.name === 'Nhạc Doanh nghiệp' ? 'Lời bài hát đã tạo:' : 'Nội dung đã tạo:' }}</p>
                                            <button @click="handleChangeContent"
                                            :class="activeCorrectionConfirmBtn ? 'bg-ai3goc-primary' : 'bg-[#B2B2B2]'"
                                            class="text-white py-2.5 text-sm lg:text-sm rounded-lg lg:rounded-2xl px-6 hover:bg-cyan-600 flex items-center gap-2">
                                                Xác nhận
                                            </button>
                                        </div>
                                        <div class="relative" :class="loadingContent ? 'hidden' : ''" >
                                            <div class="absolute top-1 right-4 p-1 rounded-full w-fit text-white cursor-pointer z-50 mt-2" @click="textToSpeechWithGoogle(item)">
                                                <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="11.3428" cy="11.0928" r="11.0928" fill="#F49A23"/>
                                                <g clip-path="url(#clip0_805_19876)">
                                                <path d="M10.7354 6.28125L7.23003 9.0855H4.42578V13.2919H7.23003L10.7354 16.0961V6.28125Z" stroke="white" stroke-width="1.91442" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M16.3927 6.23242C17.707 7.54711 18.4453 9.32997 18.4453 11.1889C18.4453 13.0479 17.707 14.8308 16.3927 16.1455M13.918 8.70718C14.5751 9.36452 14.9443 10.2559 14.9443 11.1854C14.9443 12.1149 14.5751 13.0063 13.918 13.6637" stroke="white" stroke-width="1.91442" stroke-linecap="round" stroke-linejoin="round"/>
                                                </g>
                                                <defs>
                                                <clipPath id="clip0_805_19876">
                                                <rect width="16.8255" height="16.8255" fill="white" transform="translate(3.02344 2.77344)"/>
                                                </clipPath>
                                                </defs>
                                                </svg>
                                            </div>
                                            <div v-if="resultContent" class="absolute inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
                                                <div role="status" class="flex items-center flex-col gap-2">
                                                <svg aria-hidden="true" class="w-6 h-6 text-gray-200 dark:text-gray-600 fill-blue-600 h-10 mx-auto text-gray-200 animate-spin dark:text-gray-600 fill-orion-sec" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"></path>
                                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"></path>
                                                </svg>
                                                <span class="text-black"> Nội dung đang tạo...</span>
                                                </div>
                                            </div>
                                            <textarea v-if="analysisActive?.name === 'Nhạc Doanh nghiệp'" :class="!resultContent ? '' : 'hidden'" rows="10" v-model="item.content" class="w-full text-black whitespace-pre-line border border-gray-400 rounded-xl text-base p-4 max-h-96 overflow-y-auto"> </textarea>
                                            <div v-else id="editor-container-2" class="w-full text-base border border-gray-200 rounded-lg mb-4 relative max-h-96 overflow-y-auto" @input="changeContent"></div>
                                        </div>
                                        <div :class="!loadingContent ? 'hidden' : ''" class="w-full">
                                            <LoadingCircle loading-title="Nội dung đang tạo..." />
                                        </div>
                                    </div>
                                </div>
                                <div v-if="index === 0 && analysisActive?.name !== 'Nhạc Doanh nghiệp' && !loadingContent" class="flex flex-col lg:flex-row gap-4 mt-4">
                                    <div v-if="!hiddenImage" class="w-full">
                                        <!-- Upload Buttons -->
                                        <div class="flex justify-between items-center  mb-5">
                                            <p class="text-ai3goc-bgclr text-[15px] font-bold">Hình ảnh:</p>
                                            <span class="py-2.5 text-sm lg:text-sm  px-6  flex items-center gap-2 hidden"><span class="w-[120px] font-bold">Dịch sang:</span>  <SelectRadix :options="languages" selected="" :placeholder="langValue ? langValue : 'Tiếng Việt'" :handle-change="(value) => selectOptionLang(value)"/></span>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 items-center justify-center w-full gap-2">
                                            <div class="flex flex-col justify-center items-center h-full w-full rounded-xl overflow-hidden min-h-[120px]" v-for="(image, index_image) in item.images" :key="index_image">
                                                <div v-if="!image.isLoading && (image?.imageRef?.s3_url || image?.previewImageUpload)" class="relative m-auto">
                                                    <img :src="checkImage(image) ? '/assets/images/icon_check.png' : '/assets/images/icon_un_check.png'"  @click="selectImage(image)" class="ml-0 top-2 left-2 absolute rounded-full checked:bg-orion-orange hover:checked:bg-orion-orange checked:text-orion-orange checked:ring-orion-orange z-50 cursor-pointer" >
                                                    <img  v-if="image.imageRef" :src="image.imageRef.s3_url" class="rounded-lg lg:rounded-2xl bg-gray-200 aspect-[16/9] w-full" :class="image.className" />
                                                    <div class="absolute top-2 right-2  text-white rounded-full flex justify-center items-center md:gap-2">
                                                        <svg class="cursor-pointer z-50" width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg" @click="toggleShowOption(index_image)">
                                                            <rect width="29" height="29" rx="7" fill="black" fill-opacity="0.3"/>
                                                            <path d="M14.5013 15.7082C15.1686 15.7082 15.7096 15.1672 15.7096 14.4998C15.7096 13.8325 15.1686 13.2915 14.5013 13.2915C13.834 13.2915 13.293 13.8325 13.293 14.4998C13.293 15.1672 13.834 15.7082 14.5013 15.7082Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M14.5013 7.25016C15.1686 7.25016 15.7096 6.70917 15.7096 6.04183C15.7096 5.37449 15.1686 4.8335 14.5013 4.8335C13.834 4.8335 13.293 5.37449 13.293 6.04183C13.293 6.70917 13.834 7.25016 14.5013 7.25016Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M14.5013 24.1667C15.1686 24.1667 15.7096 23.6257 15.7096 22.9583C15.7096 22.291 15.1686 21.75 14.5013 21.75C13.834 21.75 13.293 22.291 13.293 22.9583C13.293 23.6257 13.834 24.1667 14.5013 24.1667Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                        <div class="absolute right-2 top-8 w-[172px] md:w-[200px] p-2 rounded-md lg:rounded-2xl bg-black bg-opacity-30 font-bold z-50" v-if="image.isShowOptionImage">
                                                           <ul class="flex flex-col gap-[2px] m-0">
                                                                <li class="flex flex-row hover:bg-black hover:bg-opacity-40 items-center px-1 rounded-md cursor-pointer" @click="generateCustomeImage(index, index_image)">
                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M12.6667 2H3.33333C2.59695 2 2 2.59695 2 3.33333V12.6667C2 13.403 2.59695 14 3.33333 14H12.6667C13.403 14 14 13.403 14 12.6667V3.33333C14 2.59695 13.403 2 12.6667 2Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                                                        <path d="M5.66797 6.6665C6.22025 6.6665 6.66797 6.21879 6.66797 5.6665C6.66797 5.11422 6.22025 4.6665 5.66797 4.6665C5.11568 4.6665 4.66797 5.11422 4.66797 5.6665C4.66797 6.21879 5.11568 6.6665 5.66797 6.6665Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                                                        <path d="M13.9987 9.99984L10.6654 6.6665L3.33203 13.9998" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    </svg>
                                                                    <span class="ml-2">Tạo lại ảnh</span>
                                                                </li>
                                                               <li class="flex flex-row hover:bg-black hover:bg-opacity-40 items-center px-1 rounded-md cursor-pointer" @click="upscaleImage(index, index_image)">
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
                                                    <button type="button" @click="removeImage(index, index_image)" class="absolute bottom-2 left-2 bg-red-500 text-white w-4 h-4 rounded-full flex justify-center items-center z-50">
                                                        <img src="/assets/svgs/remove-icon.svg"/>
                                                    </button>
                                                    <button type="button" @click="showFullImage(image.imageRef.s3_url)" class="absolute bottom-2 right-2 text-white w-8 h-8 rounded-full flex justify-center items-center cursor-pointer z-50">
                                                        <img src="/assets/svgs/show-full.svg"/>
                                                    </button>
                                                </div>
                                                <div v-else-if="!image.isLoading && !(image?.imageRef || image?.previewImageUpload)" class="bg-[#E6E6E6] flex h-full items-center justify-center w-full rounded-xl">
                                                    <img src="/assets/svgs/aiwow/image.svg" alt="loading" class="mx-auto w-16" />
                                                </div>
                                                <div v-else-if="image.isLoading" class="bg-[#E6E6E6] flex h-full items-center justify-center w-full rounded-xl">
                                                    <div class="flex flex-col justify-center items-center rounded-lg lg:rounded-2xl bg-gray-200 object-cover aspect-[16/9] w-full">
                                                        <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                                        <h3 class="text-white text-center">Hệ thống đang xử lý, xin đợi một chút</h3>
                                                    </div>
                                                </div>
                                                <div v-else class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[16/9] w-full h-full overflow-hidden">
<!--                                                    <input type="radio" disabled class="bg-gray-200 ml-0 top-2 left-2 absolute rounded-full" />-->
                                                    <img src="/assets/svgs/aiwow/image.svg" class="mx-auto w-24" />
                                                    <div class="absolute top-2 right-2  text-white rounded-full flex justify-center items-center md:gap-2">
                                                        <label class="cursor-pointer">
                                                            <input
                                                                type="file"
                                                                accept="image/*"
                                                                @change="handleImageUpload($event, index, index_image)"
                                                                class="hidden"
                                                            />
                                                            <img src="/assets/svgs/upload-icon-v2.svg" />
                                                        </label>
                                                        <button type="button" @click="openSearchModal(index, index_image)" >
                                                            <img src="/assets/svgs/find-icon.svg"/>
                                                        </button>
                                                        <button type="button" class="flex flex-row bg-[#a0a2a4] hover:bg-black hover:bg-opacity-40 items-center px-1 md:py-[2px] rounded-md cursor-pointer" @click="generateCustomeImage(index, index_image)">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M12.6667 2H3.33333C2.59695 2 2 2.59695 2 3.33333V12.6667C2 13.403 2.59695 14 3.33333 14H12.6667C13.403 14 14 13.403 14 12.6667V3.33333C14 2.59695 13.403 2 12.6667 2Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                                                <path d="M5.66797 6.6665C6.22025 6.6665 6.66797 6.21879 6.66797 5.6665C6.66797 5.11422 6.22025 4.6665 5.66797 4.6665C5.11568 4.6665 4.66797 5.11422 4.66797 5.6665C4.66797 6.21879 5.11568 6.6665 5.66797 6.6665Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                                                <path d="M13.9987 9.99984L10.6654 6.6665L3.33203 13.9998" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                            <span class="ml-2">Tạo ảnh</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <h2 class="md:text-base text-sm italic mt-1">{{ label_images[index_image] }}</h2>
                                            </div>
                                            <div v-for="(i, fakeIndex) in getFakeNumberImage(item)">
                                                <label>
                                                    <div v-if="autoCreaeImageLoading" class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[16/9] w-full h-full overflow-hidden">
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
                                                    <div v-else class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[16/9] w-full h-full overflow-hidden">
<!--                                                        <input type="radio" disabled class="bg-gray-200 ml-0 top-2 left-2 absolute rounded-full" />-->
                                                        <img src="/assets/svgs/aiwow/image.svg" class="mx-auto w-24" />
                                                        <div class="absolute top-2 right-2  text-white rounded-full flex justify-center items-center md:gap-2">
                                                            <label class="cursor-pointer">
                                                                <input
                                                                    type="file"
                                                                    accept="image/*"
                                                                    @change="handleImageUpload($event, index, fakeIndex + item.images.length)"
                                                                    class="hidden"
                                                                />
                                                                <img src="/assets/svgs/upload-icon-v2.svg" />
                                                            </label>
                                                            <button type="button" @click="openSearchModal(index, fakeIndex + item.images.length)" >
                                                                <img src="/assets/svgs/find-icon.svg"/>
                                                            </button>
                                                            <button disabled type="button" @click="generateCustomeImage(index, fakeIndex  + item.images.length + 1)" class="bg-opacity-30 text-white font-bold bg-black p-[2px] rounded-[4px]">
                                                                Tạo ảnh
                                                            </button>
                                                        </div>
                                                        <!-- <button class="absolute bottom-2 right-2 rounded-[7.4px] text-white bg-black bg-opacity-30 flex justify-center items-center px-1 py-[2px]" type="button">
                                                            <img class="w-4 h-4" src="/assets/svgs/star-icon.svg"/>
                                                            Làm nét</button> -->
                                                    </div>
                                                    <h2 class="md:text-base text-sm italic mt-1 text-center">{{ label_images[fakeIndex + item.images.length] }}</h2>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="!hiddenVideo" class="lg:w-1/2">
                                        <!-- Video Section -->
                                        <div class="flex flex-col">
                                            <div class="flex justify-center gap-4 mb-4 h-[40px]">
                                                <label :id="`video_${index}`" class="bg-ai3goc-bgclr text-white px-6 py-2 text-sm lg:text-sm rounded-lg lg:rounded-2xl flex items-center gap-2">
                                                    <div class="p-1 rounded-full">
                                                        <Upload size="16" color="#ffffff" />
                                                    </div>
                                                    Tải video
                                                    <input type="file" accept="video/mp4,video/x-m4v,video/webm,video/ogg,video/*,.flv,.3gp" class="inputMedia hidden" @change="handleFileChange($event, index)" />
                                                </label>
                                                <button @click="scrollToCreateFormRef(index)" class="bg-ai3goc-bgclr text-white px-4 py-2 text-sm lg:text-sm rounded-2xl flex items-center gap-2">
                                                    <img src="/assets/img/my_assistant/generate_video.png" alt="" />
                                                    Tạo video
                                                </button>
                                            </div>
                                            <div class="flex items-center justify-center h-[256px] w-full">
                                                <label class="text-sm lg:text-sm flex gap-1 items-center mb-1 w-full">
                                                    <div class="flex gap-1 w-full items-center cursor-pointer">
                                                        <input type="radio" v-model="uploadType" value="video" :checked="uploadType == 'video'" @change="uploadType = 'video'" class="ml-0 rounded-full" />
                                                        <div class="w-full">
                                                            <div v-if="item.videoRef || item.previewVideoUpload" class="bg-[#E6E6E6] flex h-[256px] items-center justify-center rounded-xl w-full object-cover">
                                                                <video controls v-if="item.videoRef" :src="item.videoRef.s3_url" alt="image generated by AI" class="w-full h-full object-contain cursor-pointer rounded-xl" />
                                                                <video controls v-else :src="item.previewVideoUpload" alt="image generated by AI" class="w-full h-full object-contain cursor-pointer rounded-xl" />
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


                                <div class="flex flex-col lg:flex-row gap-4 mt-4" v-if="index == 0 && analysisActive?.name !== 'Nhạc Doanh nghiệp' && !loadingContent">
                                    <div class="w-full">
                                        <!-- Upload Buttons -->
                                        <div class="flex justify-between items-center mb-5">
                                            <p class="text-ai3goc-bgclr text-[15px] font-bold">Hình ảnh thành video:</p>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 items-center justify-center w-full gap-2">
                                            <div class="flex flex-col relative justify-center items-center w-full overflow-hidden max-h-[240px] rounded-[20px]" v-for="(video, index_video) in videos" :key="index_video">
                                               <img  v-if="video?.s3_url" :src="video.is_checked ? '/assets/images/icon_check.png' : '/assets/images/icon_un_check.png'"  @click="selectVideoItem(index_video)" class="ml-0 top-2 left-2 absolute rounded-full checked:bg-orion-orange hover:checked:bg-orion-orange checked:text-orion-orange checked:ring-orion-orange z-50 cursor-pointer" >
                                                <div v-if="video?.s3_url" class="video-player cursor-pointer" @click="showFullVideo(video?.s3_url)">
                                                    <div class="video-inner" :class="video.ratio === '16:9' ? 'ratio-16-9':'ratio-9-16'">
                                                        <img :src="video.s3_thumbnail" alt="Video Thumbnail" class="video-thumbnail">
                                                    </div>

                                                    <div class="play-button">
                                                        <img src="/assets/img/icon/icon_play.png" alt="Video Thumbnail">
                                                    </div>
                                                    <div class="controls">
                                                        <span>▶ 0:00 / 0:{{video.duraion < 10 ? '0'+video.duraion : video.duraion}}</span>
                                                        <div class="progress-bar"></div>
                                                          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect x="8.36523" y="0.396484" width="5.01927" height="1.67309" fill="white"/>
                                                                <rect x="11.7129" y="5.41797" width="5.01927" height="1.67309" transform="rotate(-90 11.7129 5.41797)" fill="white"/>
                                                                <rect width="5.01927" height="1.67309" transform="matrix(1 0 0 -1 8.36523 13.7871)" fill="white"/>
                                                                <rect width="5.01927" height="1.67309" transform="matrix(-4.37114e-08 1 1 4.37114e-08 11.7129 8.76562)" fill="white"/>
                                                                <rect width="5.01927" height="1.67309" transform="matrix(-1 0 0 1 5.01758 0.396484)" fill="white"/>
                                                                <rect width="5.01927" height="1.67309" transform="matrix(4.37114e-08 -1 -1 -4.37114e-08 1.67188 5.41797)" fill="white"/>
                                                                <rect x="5.01758" y="13.7871" width="5.01927" height="1.67309" transform="rotate(180 5.01758 13.7871)" fill="white"/>
                                                                <rect x="1.67188" y="8.76562" width="5.01927" height="1.67309" transform="rotate(90 1.67188 8.76562)" fill="white"/>
                                                            </svg>
                                                    </div>
                                                </div>
                                                <div v-if="!video.isLoading && !video.s3_url" class="bg-[#E6E6E6] flex aspect-video items-center justify-center w-full rounded-xl">
                                                    <img src="/assets/img/aiwow/homepage/play_button.png" class="w-16" alt="" />
                                                </div>
                                                <div v-if="video.isLoading && !video.s3_url" class="bg-[#E6E6E6] flex aspect-video items-center justify-center w-full rounded-xl">
                                                    <div class="flex flex-col justify-center items-center w-full min-h-[260px]">
                                                        <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                                        <h3 class="text-white text-center">Hệ thống đang xử lý, xin đợi một chút</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="!hiddenVideo" class="lg:w-1/2">
                                        <!-- Video Section -->
                                        <div class="flex flex-col">
                                            <div class="flex justify-center gap-4 mb-4 h-[40px]">
                                                <label :id="`video_${index}`" class="bg-ai3goc-bgclr text-white px-6 py-2 text-sm lg:text-sm rounded-lg lg:rounded-2xl flex items-center gap-2">
                                                    <div class="p-1 rounded-full">
                                                        <Upload size="16" color="#ffffff" />
                                                    </div>
                                                    Tải video
                                                    <input type="file" accept="video/mp4,video/x-m4v,video/webm,video/ogg,video/*,.flv,.3gp" class="inputMedia hidden" @change="handleFileChange($event, index)" />
                                                </label>
                                                <button @click="scrollToCreateFormRef(index)" class="bg-ai3goc-bgclr text-white px-4 py-2 text-sm lg:text-sm rounded-2xl flex items-center gap-2">
                                                    <img src="/assets/img/my_assistant/generate_video.png" alt="" />
                                                    Tạo video
                                                </button>
                                            </div>
                                            <div class="flex items-center justify-center h-[256px] w-full">
                                                <label class="text-sm lg:text-sm flex gap-1 items-center mb-1 w-full">
                                                    <div class="flex gap-1 w-full items-center cursor-pointer">
                                                        <input type="radio" v-model="uploadType" value="video" :checked="uploadType == 'video'" @change="uploadType = 'video'" class="ml-0 rounded-full" />
                                                        <div class="w-full">
                                                            <div v-if="item.videoRef || item.previewVideoUpload" class="bg-[#E6E6E6] flex h-[256px] items-center justify-center rounded-xl w-full object-cover">
                                                                <video controls v-if="item.videoRef" :src="item.videoRef.s3_url" alt="image generated by AI" class="w-full h-full object-contain cursor-pointer rounded-xl" />
                                                                <video controls v-else :src="item.previewVideoUpload" alt="image generated by AI" class="w-full h-full object-contain cursor-pointer rounded-xl" />
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
                                <div v-if="analysisActive?.name !== 'Nhạc Doanh nghiệp' && index == 0 && !loadingContent">
                                    <!-- Video Section -->
                                    <div class="flex flex-col">
                                       <div class="relative">
                                            <div class="flex justify-center gap-2 md:gap-4 mb-4 h-[40px] mt-10">
                                                <button @click="confirmShowMergeVideo" class="relative h-[40px] md:h-[50px] min-w-[80%] lg:min-w-[360px] text-sm lg:text-base px-4 bg-ai3goc-bgclr text-white rounded-lg lg:rounded-2xl font-bold">
                                                    <svg class="absolute" width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M20.57 2H4.93C3.72602 2 2.75 2.97602 2.75 4.18V19.82C2.75 21.024 3.72602 22 4.93 22H20.57C21.774 22 22.75 21.024 22.75 19.82V4.18C22.75 2.97602 21.774 2 20.57 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M7.75 2V22" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M17.75 2V22" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M2.75 12H22.75" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M2.75 7H7.75" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M2.75 17H7.75" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M17.75 17H22.75" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M17.75 7H22.75" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    Ghép video
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4" v-if="analysisActive?.name !== 'Nhạc Doanh nghiệp' && index == 0 && !loadingContent">
                                    <!-- Video Section -->
                                    <div class="flex flex-col">
                                       <div class="relative">
                                            <p class="text-ai3goc-bgclr text-[15px] font-bold absolute top-2">Video:</p>
                                            <div class="flex justify-center gap-2 md:gap-4 mb-4 h-[40px]">
                                                <button @click="scrollToCreateFormRef(index)" class="w-[30%] sm:w-1/3 bg-orion-orange hover:bg-cyan-600 font-bold text-white text-center py-2 text-sm lg:text-sm rounded-2xl flex items-center gap-2 justify-center">
                                                    <img src="/assets/img/my_assistant/generate_video.png" alt="" class="hidden md:show" />
                                                    Tạo video mới
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-center w-full">
                                        <label class="text-sm lg:text-sm flex gap-1 items-center mb-1 w-full">
                                            <div class="flex gap-1 w-full items-center cursor-pointer">
                                                <div class="relative w-full">
                                                    <div v-if="item.videoRef || item.previewVideoUpload" class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[16/9] w-full h-full overflow-hidden">
                                                        <label class="ml-0 w-16 h-16 cursor-pointer top-0 left-0 p-2 z-10 text-right absolute">
                                                            <img :src="checkVideo(item) ? '/assets/images/icon_check.png' : '/assets/images/icon_un_check.png'"  @click="selectVideo(item)" class="ml-0 top-2 left-2 absolute rounded-full checked:bg-orion-orange hover:checked:bg-orion-orange checked:text-orion-orange checked:ring-orion-orange z-50 cursor-pointer" >
                                                        </label>
                                                        <video controls v-if="item.videoRef" :src="item.videoRef.s3_url" alt="image generated by AI" class="w-full h-auto rounded-md " />
                                                        <video controls v-else :src="item.previewVideoUpload" alt="image generated by AI" class="w-full h-auto rounded-md" />

                                                        <button type="button" @click="removeVideo(index)" class="absolute bottom-2 right-2 bg-red-500 text-white w-6 h-6 rounded-full">x</button>
                                                    </div>
                                                    <div v-else class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[16/9] w-full h-full overflow-hidden">
<!--                                                        <input type="radio" disabled class="bg-gray-200 ml-0 top-2 left-2 absolute rounded-full checked:bg-orion-orange hover:checked:bg-orion-orange checked:text-orion-orange checked:ring-orion-orange" />-->
                                                        <div class="flex flex-col items-center" v-if="isLoadingVideo">
                                                            <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                                            <h3 class="text-white text-center">Hệ thống đang xử lý, xin đợi một chút</h3>
                                                            <span class="color-greeen">{{percenLoadingVideo}}%</span>
                                                        </div>
                                                    </div>
                                                    <div class="absolute top-2 right-2  text-white rounded-full flex justify-center items-center md:gap-2">
                                                        <label class="cursor-pointer">
                                                            <input
                                                                type="file"
                                                                accept="video/mp4,video/x-m4v,video/webm,video/ogg,video/*,.flv,.3gp"
                                                                @change="handleFileChange($event, index)"
                                                                class="hidden"
                                                            />
                                                            <img class="md:w-[30px] w-[29px]" src="/assets/svgs/upload-icon-v2.svg" />
                                                        </label>
                                                        <button type="button" @click="autoCreateVideo" :disabled="showVideoGenerate == true" class="md:ml-0 ml-2 md:text-[19px] text-sm bg-opacity-30 text-white font-bold bg-black p-[4px] rounded-[4px]">
                                                            Tự động tạo video
                                                        </button>
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
                                            }
                                        "
                                        class="flex place-items-center gap-2 px-8 py-2 lg:py-3 bg-[#1E405A] text-white rounded-lg lg:rounded-2xl font-bold"
                                    >
                                        <img src="/assets/img/my_assistant/music.png" class="w-5" alt="" />
                                        Tạo nhạc
                                    </button>
                                </div>
                                <div class="flex flex-col justify-center gap-3 lg:gap-4 mt-6 mb-4" v-else>
                                    <div v-if="!loadingContent" class="text-center md:text-base text-sm text-gray-400 inline-block mb-1 italic border-t-[3px] border-[#d6d6d6]">Vui lòng lựa chọn Hình ảnh/Video đăng bài</div>
                                    <div v-if="!loadingContent" class="flex  justify-center">
                                        <button @click="openPostDetail(item?.content ?? '', index)" class="min-w-[100px] md:min-w-[150px] p-4 lg bg-ai3goc-bgclr rounded-lg lg:rounded-2xl border border-[#C5C5C5] text-white font-bold place-items-center gap-2 md:text-[17px] text-sm">Xác nhận</button>
                                    </div>
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
                                    <CreateMusic :is-show-button-confirm="true" @saveGenerationResult="(song) => handleSaveGenerationResultMusic(song, index)" :content="messagesMap['resultTarget'][0].content ?? ''" :titleMusic="formProject.title">
                                        <div class="flex  justify-center mt-[15px]">
                                            <button @click="openPostDetail(messagesMap['resultTarget'][0].content ?? '', 0)" class="min-w-[100px] md:min-w-[150px] p-4 lg bg-ai3goc-bgclr rounded-lg lg:rounded-2xl border border-[#C5C5C5] text-white font-bold place-items-center gap-2 md:text-[19px] text-sm">Xác nhận</button>
                                        </div>
                                    </CreateMusic>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="analysisActive?.name !== 'Chiến dịch Quảng cáo sản phẩm'" ref="stepPostFormRef" :class="analysisActive?.name === 'Phân tích thị trường' && 'hidden'">
                        <div :class="true ? 'flex-col lg:rounded-[35px]' : 'flex-row lg:rounded-full'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex items-center justify-between mt-4">
                        <div class="flex items-center self-start justify-between w-full text-sm lg:text-sm text-black" v-if="isGenAIStatus == false">
                            <Step class-name="md:text-[20px]" class="flex-shrink-0" :step="analysisActive?.name === 'Nhạc Doanh nghiệp' ? '5' : ['Bài viết Quảng cáo sản phẩm', 'Thơ Quảng cáo sản phẩm'].includes(analysisActive?.name) && mode != 'basic' ? 4 : mode != 'basic' ? 4 : 4" step-name="Đăng bài" />
                        </div>
                        <div class="w-full">
                            <PostForm ref="postFormRef" :templatePostForm="isGenAIStatus ? 'AutoPostAIForm' : 'BasicForm'" @beforeSubmit="beforeSubmitPostForm" @onSuccess="onSuccessPostForm" />
                        </div>
                    </div>
                </div>
            </div>
            <!--project type campaign-->
            <FacebookContentListStep
                ref="fbContentRef"
                :key="analysisActive?.name"
                v-if="analysisActive?.name === 'Chiến dịch Quảng cáo sản phẩm'"
                :projectId="businessProjectId"
                :showFBContent="showFBContent"
                @showSection="handleShowSection"
                @toggleShowFBContent="toggleShowFBContent"
                :open="openFaceBookContent"
                :loading="loading"
                :contents="{ content: 'Nội dung từ component cha' }"
                :step="{step: 4, stepName: 'Xem kết quả và hiệu chỉnh'}"
                :max-post="selectedProjectType?.maxPost"
                @updateFaceBookContent="handleUpdateFaceBookContent"
            />

            <PopupFacebookContent
                ref="popupFBRef"
                v-if="analysisActive?.name === 'Chiến dịch Quảng cáo sản phẩm' && openPoupFaceBookContent"
                :projectId="businessProjectId"
                :project-type="'campaign'"
                :selectedProject="selectedProject"
                @showSection="handleShowSection"
                :facebookContentDetail="facebookContentDetail"
                :open="openPoupFaceBookContent"
                :loading="loading"
                :contents="{ content: 'Nội dung từ component cha' }"
            />
    </div>

    <Modal :show="showGradeModal" @close="closeGradeModal">
        <div class="p-4 md:p-12 max-h-[90vh] overflow-y-scroll">
            <div>
                <div class="flex gap-1">
                    <div class="flex justify-start items-center gap-2">
                        <div class="w-[52px] overflow-hidden rounded-full border-[1px] border-[#d4d4d4]">
                            <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
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
                        <button @click="replaceWithImprovedContent" class="bg-ai3goc-primary text-white px-4 py-2 rounded-full my-4 flex justify-center items-center gap-2">Thay thế nội dung</button>
                    </div>
                </div>
            </div>
            <div class="flex justify-center items-center">
                <button @click="closeGradeModal" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-full mt-4">Đóng</button>
            </div>
        </div>
    </Modal>

    <Loading v-if="isLoading" />

    <MaintenanceModal v-if="maintenanceMode" />
    <PopupConfirmVideo @confirmVideo="confirmVideo" :message="message" @close="showConfirmVideo=false" v-if="showConfirmVideo"></PopupConfirmVideo>
    <div v-if="showSearchModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center" style="z-index: 9999999;">
        <div class="bg-white w-[80vw] md:w-[50vw] p-4 rounded-xl">
            <div class="flex items-center justify-between">
                <div class="flex justify-start items-center gap-4">
                    <span class="text-[15px] lg:text-[18px] leading-none font-bold text-black">Tìm kiếm hình ảnh</span>
                </div>
                <button type="button" @click="showSearchModal = false" class="p-2">
                    <img src="/assets/img/orion/icon/close_yellow.svg"/>
                </button>
            </div>
            <div class="border-t border-gray-300 my-2"></div>
            <div class="overflow-y-auto  max-h-[60vh]">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div
                        v-for="(img, i) in searchImages"
                        :key="i"
                        @click="selectedSearchImage = img.link"
                        :class="[
                            'cursor-pointer border-2 rounded-lg overflow-hidden relative',
                            selectedSearchImage === img.link ? 'border-orion-orange border-4' : 'border-transparent'
                        ]"
                    >
                        <img v-if="selectedSearchImage === img.link" src="/assets/images/icon_tick.png" class="absolute top-2 right-2">
                        <img :src="img.link" class="w-full aspect-[4/3] object-cover" />
                    </div>
                </div>
                <div class="flex justify-center">
                    <button type="button" @click="loadMoreImages" :disabled="isLoadingMore" :class="isLoadingMore ? 'bg-opacity-60' : 'bg-opacity-100'" class="mg-b-20px h-[40px] md:h-[50px] md:min-w-[150px] text-sm md:text-[17px] px-4 bg-ai3goc-bgclr text-white rounded-lg lg:rounded-2xl font-bold btn-loadmore">{{ searchImages.length == 0 ? 'Đang tải thêm ảnh...' : 'Xem thêm...' }} </button>
                </div>
            </div>
            <div class="flex justify-center items-center my-4">
                <button @click="confirmSelectedImage" class="mg-b-20px h-[40px] md:h-[50px] md:min-w-[150px] text-sm md:text-[17px] px-4 bg-ai3goc-bgclr text-white rounded-lg lg:rounded-2xl font-bold">Xác nhận</button>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" v-if="showImage">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-full m-2 h-[95vh] md:w-[700px] xl:w-[1000px] rounded-[20px] relative" >
            <div class="">
                <div class="flex flex-col lg:flex-row md:justify-end md:items-end lg:items-center mt-4 flex-wrap lg:flex-nowrap lg:justify-between">
                    <h2 class="text-black font-bold text-[18px] mb-4"> Kho ảnh {{typeImage != 1 ? 'sản phẩm' : 'avatar'}} </h2>
                    <img src="/assets/img/orion/icon/close_yellow.svg" class="cursor-pointer absolute top-2 right-5" @click="closePopup" />
                    <label v-if="typeImage != 1" class="relative h-[40px] mb-5 cursor-pointer flex flex-row text-sm lg:text-base px-4 rounded-lg lg:rounded-2xl font-bold line-h-40">
                        <button @click="isShowOption = !isShowOption" class="w-full h-ful border-[#d6d6d6] px-4 border-2  font-bold py-2.5 text-sm text-black lg:text-sm rounded-2xl lg:rounded-2xl text-center flex items-center gap-2 justify-center">
                             {{typeImage == 2 ? 'Ảnh sản phẩm đã tải' : 'Ảnh bối cảnh'}}
                             <svg data-v-8e5f379b="" class="rotate-0" width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path data-v-8e5f379b="" d="M1 1L5 5L9 1" stroke="#C5C5C5"></path></svg>
                        </button>
                        <div class="absolute z-10 right-0 w-52 bg-white shadow-xl rounded-2xl p-2 py-4 space-y-2 mt-10" v-if="isShowOption">
                            <div @click="showImageList(2)" class="flex items-center cursor-pointer text-sm md:text-sm px-4 py-2 rounded-md text-black">
                                <p>Ảnh sản phẩm đã tải</p>
                            </div>
                            <div @click="showImageList(3)" class="flex items-center cursor-pointer text-sm md:text-sm px-4 py-2 rounded-md text-black">
                                <p>Ảnh bối cảnh</p>
                            </div>
                        </div>
                    </label>
                    <label v-if="typeImage != 1" @click="redirectHistory()" class="h-[40px] mb-5 cursor-pointer flex flex-row text-sm lg:text-base px-4 bg-[#149CBE] text-white rounded-lg lg:rounded-2xl font-bold line-h-40">
                        <svg class="mt-3" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.2222 1H2.77778C1.79594 1 1 1.79594 1 2.77778V15.2222C1 16.2041 1.79594 17 2.77778 17H15.2222C16.2041 17 17 16.2041 17 15.2222V2.77778C17 1.79594 16.2041 1 15.2222 1Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5.889 7.22233C6.62538 7.22233 7.22233 6.62538 7.22233 5.889C7.22233 5.15262 6.62538 4.55566 5.889 4.55566C5.15262 4.55566 4.55566 5.15262 4.55566 5.889C4.55566 6.62538 5.15262 7.22233 5.889 7.22233Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.0001 11.6666L12.5556 7.22217L2.77783 16.9999" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                        <span class="ml-2">Quản lý kho ảnh sản phẩm</span>
                    </label>
                    <input id="upload-image" type="file" class="hidden">
                </div>
                 <div class="flex w-full border-b-[3px] border-[#d6d6d6] mb-5 px-5"></div>
            </div>

            <div class="relative">
                <div class="w-fit overflow-y-auto h-[52vh] lg:h-[62vh] mb-2 relative text-center">
                    <div class="grid grid-cols-3 lg:grid-cols-4 gap-2 lg:gap-4 w-full">
                        <div @click="selectedImage(image.s3_url)" v-for="image in images" :key="image.id" class="relative rounded-xl cursor-pointer">
                            <img :src="image.s3_url" :alt="image.id" class="w-full h-full rounded-lg object-cover" />
                            <input type="radio" :value="image.s3_url" :checked="imageSelect == image.s3_url ? true : false" class="absolute top-2 checked:bg-[#0e68ef] checked:bg-orion-orange hover:checked:bg-orion-orange checked:text-orion-orange checked:ring-orion-orange lg:top-4 right-2 lg:right-4 cursor-pointer outline-none md:size-6 size-4" />
                        </div>
                    </div>
                    <button v-if="nextPage" @click="loadMore" class="h-[40px] md:h-[50px] min-w-[80%] lg:min-w-[200px] text-sm md:text-[19px] px-4 bg-ai3goc-bgclr text-white rounded-lg lg:rounded-2xl font-bold">Xem thêm</button>
                </div>
            </div>
            <div class="flex items-center gap-4 md:gap-8 justify-center md:mt-6">
                <button @click="confirmImage" class="h-[40px] md:h-[50px] text-sm md:text-[19px] px-8 md:px-12 bg-ai3goc-bgclr text-white rounded-lg lg:rounded-2xl font-bold">
                   Xác nhận
                </button>
            </div>
        </div>
    </div>
     <input type="file" ref="uploadImage1" accept="image/*" @change="handleImageUploadV2" class="hidden" />
     <!-- <PopupCreateImage @close="isShowCreateImage=false" :is_show="isShowCreateImage" @showImageList="showModalImageList" ref="createImageRef" :productImage="createProductImage" @updateImage="handleUpdateImage" /> -->
     <PopupCreateVideo @close="isShowCreateVideo=false" :is_show="isShowCreateVideo" ref="createVideoRef" @createShortVideo="createShortVideo" />
     <PopupImageList @close="isShowImage=false" :is_show="isShowImage" @showCreateImage="showModelCreateImage" />
     <PopupMergeVideo @close="isShowMergeVideo=false" :is_show="isShowMergeVideo" @confirmMergeVideo="confirmMergeVideo" ref="mergeVideoRef" />
     <div>
        <div v-if="isShowFullVideo" class="overlay z-9999" @click="isShowFullVideo = false">
            <button class="close-btn" @click="isShowFullVideo = false">✖</button>
            <video controls v-if="videoFullUrl" :src="videoFullUrl" alt="image generated by AI" class="max-h-[800px]" />
        </div>
        <div v-if="isShowFullImage" class="overlay z-9999" @click="isShowFullImage = false">
            <button class="close-btn" @click="isShowFullImage = false">✖</button>
            <img :src="thumbnail" class="full-image" />
        </div>
    </div>
    <div v-if="isShowCreateImage" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[500px] rounded-[40px] relative">
            <div class="">
                <div class="flex flex-col lg:flex-row md:justify-end md:items-end lg:items-center mt-4 flex-wrap lg:flex-nowrap lg:justify-between">
                    <h2 class="text-black font-bold text-2xl mb-4 md:text-[25px]">Tạo ảnh</h2>
                    <img src="/assets/img/orion/icon/close_yellow.svg" class="cursor-pointer absolute top-2 right-5" @click="closePopupRegenImage(regenIndex, regenImageIndex)" />
                </div>
                <div class="flex w-full border-b-[3px] border-[#d6d6d6] mb-5 px-5"></div>
            </div>

            <div class="relative">
                <div class="mb-2 relative">
                    <div class="flex flex-col lg:justify-between gap-8 flex-wrap md:flex-nowrap">
                        <section ref="resultBox" class="bg-white w-full h-fit">
                            <form @submit.prevent="generateImage" class="">
                                <div class="relative">
                                    <div class="text-black">
                                        <h1 class="font-semibold text-sm">1. Mô tả hình ảnh</h1>
                                        <div class="relative">
                                            <SuggestionPrompt v-model="imageDescription" :type="'image'" :screenId="2" />
                                            <textarea id="image-description" v-model="imageDescription" type="text" placeholder="Nhập mô tả ảnh bạn muốn tạo..." class="w-full p-3 mt-1 h-[200px] border text-black border-[#D4D4D4] rounded-[10px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder:font-light md:text-[14px]"></textarea>

                                            <div class="object-mic relative ml-auto">
                                                <div v-if="isRecording" class="outline-mic right-3 bottom-3 flex items-center justify-center"></div>
                                                <div v-if="isRecording" class="outline-mic right-3 bottom-3 flex items-center justify-center" id="delayed"></div>
                                                <div v-if="isRecording" class="button-mic right-3 bottom-3 flex items-center justify-center"></div>
                                                <button class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center" @click="startRecording" :disabled="disabledRecord" type="button">
                                                    <img class="w-6 h-6" src="/assets/img/ai3goc/icon/mic.svg" alt="mic" />
                                                </button>
                                            </div>
                                        </div>

                                        <span v-if="errors.description" class="text-red-500 text-sm">{{ errors.description }}</span>
                                    </div>
                                    <div class="mt-4">
                                        <div class="flex flex-row md:flex-col xl:flex-row xl:items-center xl:gap-8 gap-1 justify-between xl:justify-between">
                                            <h1 class="font-semibold text-sm">2. Kích thước</h1>

                                            <select v-model="imageDimension" :class="{ 'bg-gray-200 border-gray-200': !imageDimension, 'bg-white border-[#D4D4D4]': imageDimension }" class="block text-[14px] w-1/3 sm:w-1/2 md:w-full xl:w-3/5 appearance-none col-span-2 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500  md:text-[15px]">
                                                <option v-for="dimension in validDimensions" :value="dimension" :key="dimension">
                                                    {{ dimension }}
                                                </option>
                                            </select>
                                        </div>
                                        <span v-if="errors.dimensions" class="text-red-500 text-sm">{{ errors.dimensions }}</span>
                                    </div>
                                    <div class="flex w-full border-b-[3px] border-[#d6d6d6] my-5 px-5"></div>
                                    <div class="flex text-black items-center justify-center mt-4 w-full">
                                        <button id="create-image" type="submit" class="md:px-2 py-2 bg-ai3goc-sec text-white rounded-[10px] text-[12px] xl:text-[14px] disabled:opacity-50 disabled:pointer-events-none hover:scale-105 font-bold w-1/2 lg:w-1/3 md:text-[15px]" @click="handleGenImage(regenImageIndex)">Tạo lại ảnh</button>
                                    </div>
                                </div>
                            </form>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="showOptions" class="overlay-option" @click="hideOption()"></div>
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
import PopupConfirmVideo from '@/Components/PopupConfirmCreateVideo.vue';
import PostForm from "@/Components/ShareAiText/PostForm.vue";
import { convertToSnakeCase } from "@/lib/utils";
import { inject } from "vue";
const helpers = inject("helpers");
import Step from "@/Components/Step.vue";
import MaintenanceModal from "@/Components/MaintenanceModal.vue";
import ProjectExpert from "./ProjectExpert/ProjectExpert.vue";
import { eventBus } from "@/lib/eventBus.js";
import PopupFacebookContent from "@/Pages/Client/MyAssistants/ProjectAdvance/TypeCampaign/PopupFacebookContent.vue";
import FacebookContentListStep from "@/Pages/Client/MyAssistants/ProjectAdvance/TypeCampaign/FacebookContentListStep.vue";
import { useTextToSpeech } from "@/helper/useTextToSpeech";
import PopupCreateImage from "./Components/PopupCreateImage.vue";
import PopupCreateVideo from "./Components/PopupCreateVideo.vue";
import PopupImageList from "./Components/PopupImageList.vue";
import PopupMergeVideo from "./Components/PopupMergeVideo.vue";
import ProjectBusinessHistory from "./ProjectBusinessHistory.vue";

import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
const { listVoice,handleTextToSpeech } = useTextToSpeech()
//Thêm mới phần ảnh
const isShowImage = ref(false)
const isShowCreateImage = ref(false)
const isShowCreateVideo = ref(false)
const videoKey = ref(0)
const uploadImage1 = ref(null)
const imageModel = ref("")
const createVideoRef = ref(null)
const mergeVideoRef = ref(null)
const createImageRef = ref(null)
const imageCreateVideo = ref("")
const imageKey = ref(0)
const isMergerVideo = ref(false)
const isShowMergeVideo = ref(false)
const isShowFullImage = ref(false)
const thumbnail = ref("")
const isShowFullVideo = ref(false)
const videoFullUrl = ref("")
const showOptions = ref(false)
const createProductImage = ref("https://adbox-staging.s3.ap-northeast-1.amazonaws.com/images/image_682d879e8c2587.25347104.png?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIA6LFOESVFRUY54N6D%2F20250521%2Fap-northeast-1%2Fs3%2Faws4_request&X-Amz-Date=20250521T075823Z&X-Amz-SignedHeaders=host&X-Amz-Expires=604800&X-Amz-Signature=84d6564ea7eb8504e038e64149dfbaaf96c36411371d4d197bbb374376785022")
const videos = ref([
    {
        isLoading:false,
        s3_url:"",
        duraion:0,
        ratio:''
    },
    {
        isLoading:false,
        s3_url:"",
        duraion:0,
        ratio:''
    },
    {
        isLoading:false,
        s3_url:"",
        duraion:0,
        ratio:''
    },
    {
        isLoading:false,
        s3_url:"",
        duraion:0,
        ratio:''
    },
])

const showFullVideo = (videoUrl) => {
    videoFullUrl.value = videoUrl
    isShowFullVideo.value = true
}

const showFullImage = (imageUrl) => {
    thumbnail.value = imageUrl
    isShowFullImage.value = true
}

const trigUploadImage = (index) => {
    imageKey.value = index
    if (uploadImage1.value instanceof HTMLInputElement) {
        uploadImage1.value.click()
    } else {
        console.error('uploadImage1 is not a valid input element', uploadImage1.value)
    }
    showOptions.value = false
    messagesMap["resultTarget"][0].images[index].isShowOptionImage = false;
}

const hideOption = () => {
    if(messagesMap["resultTarget"][0].images) {
        for(var i = 0; i < messagesMap["resultTarget"][0].images.length; i++) {
            messagesMap["resultTarget"][0].images[i].isShowOptionImage = false
        }
    }
    showOptions.value = false
}

const createShortVideo = async (data) => {
    const formData = new FormData();
    formData.append("duration", data.duration);
    formData.append("number_result", data.number_result);
    formData.append("resolution", data.resolution);
    formData.append("s3_url", data.s3_url);
    formData.append("video_description", data.video_description);
    videos.value[videoKey.value].isLoading = true
    videos.value[videoKey.value].s3_url = ""
    isShowCreateVideo.value = false
    try {
        const response = await axios.post(route("short-video.create-video-ai-v3"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        if (response.data.success) {
            videos.value[videoKey.value].isLoading = true
            videos.value[videoKey.value].s3_thumbnail = response.data.s3_thumbnail
            videos.value[videoKey.value].s3_url = response.data.s3_url
            videos.value[videoKey.value].isLoading = false
            videos.value[videoKey.value].ratio = data.resolution
            videos.value[videoKey.value].duraion = data.duration
        } else {
            videos.value[videoKey.value].isLoading = false
            toast.error('Có lỗi xảy ra trong quá trình tạo video')
        }
    } catch (err) {
        videos.value[videoKey.value].isLoading = false
        toast.error('Có lỗi xảy ra trong quá trình tạo video')
    }
}

const confirmShowMergeVideo = async () => {
    var videoUrls = [];
    var totalDuration = 0
    for(var i = 0 ; i < videos.value.length; i++) {
        if(videos.value[i].s3_url && videos.value[i].is_checked) {
            videoUrls.push(videos.value[i].s3_url)
            totalDuration = videos.value[i].duraion + totalDuration
        }
    }
    if(videoUrls.length < 2) {
        toast.error("Số lượng video cần từ 2 video trở lên")
        return
    }
    const content = messagesMap["resultTarget"][0].content;
    if(mergeVideoRef.value) {
        mergeVideoRef.value.updateAudioDes(content, totalDuration)
    }
    isShowMergeVideo.value = true
}

const changeContent = () => {
    var content = sunEditorContentInstance.value.getContents();
    messagesMap["resultTarget"][0].content = content
}

const convertTextToAudio = async (voiceType, audioDes) => {
    const result = await handleTextToSpeech({
        text: audioDes,
        voice: voiceType,
        cloned: null,
        screen_id:4
    })
    return result
}

const confirmMergeVideo = async (data) => {
    if(isLoadingVideo.value) {
        return
    }
    isShowMergeVideo.value = false
    isLoadingVideo.value = true;
    isLoadingPercent.value = true
    const formData = new FormData();
    var videoUrls = [];
    var totalDuration = 0
    for(var i = 0 ; i < videos.value.length; i++) {
        if(videos.value[i].s3_url && videos.value[i].is_checked) {
            videoUrls.push(videos.value[i].s3_url)
            totalDuration = videos.value[i].duraion + totalDuration
        }
    }
    var audio_s3 = data.audio_s3
    formData.append("videoUrls", JSON.stringify(videoUrls));
    formData.append("audio1", data.audio1);
    if(!audio_s3) {
        if(data.voiceType) {
            audio_s3 = await convertTextToAudio(data.voiceType, data.audioDes)
        }
    }
    formData.append("audio_s3", audio_s3);
    formData.append("totalDuration", totalDuration);
    formData.append("enableCaption", data.enableCaption);
    resetVideoUpload(0);
    percenLoadingVideo.value = 0;
    try {
        const response = await axios.post(route("short-video.merge-video-auto-v2"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        isLoadingPercent.value = false
        if (response.data.success) {
            await updateProject({
                id: formProject.id,
                title: formProject.title,
                content: formProject.content,
                video_url: response.data.video_path,
            });
            await slowlyIncreasePercent();
            handleSaveGenerationResultVideo(response.data.s3_url, 0);
        } else {
            toast.error("Có lỗi xảy ra trong quá trình merge video")
        }
        isLoadingVideo.value = false
    } catch (err) {
        isLoadingVideo.value = false
        toast.error("Có lỗi xảy ra trong quá trình merge video")
    }
}

const toggleShowOption = (index) => {
    if(messagesMap["resultTarget"][0].images) {
        for(var i = 0; i < messagesMap["resultTarget"][0].images.length; i++) {
            if(index == i && !messagesMap["resultTarget"][0].images[i].isShowOptionImage) {
                messagesMap["resultTarget"][0].images[i].isShowOptionImage = true
                showOptions.value = true
            } else {
                messagesMap["resultTarget"][0].images[i].isShowOptionImage = false
            }
        }
    }
}

const showPopupCreateVideo = (index, s3_url) => {
    for(var i = 0; i < videos.value.length; i++) {
        if(videos.value[i].isLoading) {
            return false;
        }
    }
    isShowCreateVideo.value = true
    imageCreateVideo.value = s3_url
    videoKey.value = index
    if(createVideoRef.value) {
        createVideoRef.value.updateImageUrl(s3_url)
    }
    showOptions.value = false
    messagesMap["resultTarget"][0].images[index].isShowOptionImage = false;
}

const showModalImageList = () => {
    isShowImage.value = true
    isShowCreateImage.value = false
}

const showModelCreateImage = (s3_url) => {
    imageModel.value = s3_url
    isShowImage.value = false
    isShowCreateImage.value = true
    if(createImageRef.value) {
        createImageRef.value.updateImageUrl(s3_url)
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
  for (const image of messagesMap["resultTarget"][0].images) {
    try {
      const { width, height } = await getImageRatioFromURL(image.imageRef.s3_url);
      const ratio = width / height;

      image.className = ratio >= 1 ? 'object-cover' : 'object-contain';
    } catch (error) {
      console.error('Lỗi khi lấy kích thước ảnh:', error);
      image.className = 'object-cover';
    }
  }
}

const downloadImage = (image) => {
    var url = route(("ai-video.downloadFile"), {url:image, name:"image.png"})
    window.open(url, '_blank');
};

const handleUpdateImage = async (s3_url, index) => {
    let image = {
        previewImageUpload: null,
        fileImageUpload: null,
        imageRef: {
            s3_url: s3_url
        },
        isLoading: false
    };
    if(!messagesMap["resultTarget"][0].images) {
        messagesMap["resultTarget"][0].images = {}
    }
    messagesMap["resultTarget"][0].images[index] = image;
    updateClassImage()
    referUrl.value = null;
    if (imageKey.value === index) {
        isShowCreateImage.value = false
    }
}
const showSectionMusic = ref(false)
const showDropdownCampaign = ref(false);
const openFaceBookContent = ref(false);
const openPoupFaceBookContent = ref(false);
const showFBContent = ref(true);
const facebookContentDetail = ref(null)
const fbContentRef = ref(null)
const userId = ref(null)
const toggleShowFBContent = (value = null) => {
    if (value == null) {
        showFBContent.value = true;
        return;
    }
    showFBContent.value = !showFBContent.value;
}
const handleShowSection = (index, isOpen = true) => {
    openPoupFaceBookContent.value = isOpen;
    if (index === 6 && fbContentRef.value && isOpen === false) {
        document.body.className = document.body.className.replace(/ no-scroll/g, '')
        openPoupFaceBookContent.value = false
        fbContentRef.value.callApiGetContent(formProject.id);
    }
}
const handleUpdateFaceBookContent = (data) => {
    console.log(data, 'handleUpdateFaceBookContent');
    facebookContentDetail.value = data
}
const toggleDropdownCampaign = (value) => {
    if ([true, false].includes(value)) {
        showDropdownCampaign.value = value;
        return;
    }
    showDropdownCampaign.value = !showDropdownCampaign.value;
}
const pageData = reactive({
    image_select_files: [],
    video_select_files: [],
});
const breadcrumbsData = [{ text: "A.I bán hàng", link: "ai-business.index" }];
const page = usePage();
const stepPostFormRef = ref(null);
const postFormRef = ref(null);
const businessProjectId = ref(null);
const createFormRef = ref(null);
const uploadType = ref("image");
const showAffKeyPopup = ref(false);
const createContentWithImage = ref(false);
const autoCreaeImageLoading = ref(false);
const MAX_IMAGE_FILES = 4;
const editor = ref(null);
const { props: pageProps } = usePage();
let urlParams = new URLSearchParams(window.location.search);
const mode = ref(urlParams.get("mode") || "basic");
const refProjectExpert = ref(null)

const activeCorrectionConfirmBtn = computed(() => {
    let result = false;
    messagesMap['resultTarget'].forEach((target) => {
        target.options_rewrite.forEach((item) => {
            if(item.value) {
                result = true
            }
        });
    });
    return result;
});

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
    { open: false },
    { open: false },
]);

const label_images = ref(['Ảnh phối cảnh của sản phẩm', 'Ảnh ghép người mẫu với sản phẩm', 'Ảnh ghép tự sướng của bạn với sản phẩm', 'Ảnh ghép tự sướng của bạn với sản phẩm']);

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
let sampleAnalysis = [
    {
        name: "Bài viết Quảng cáo sản phẩm",
        isActive: true,
    },
    {
        name: "Chiến dịch Quảng cáo sản phẩm",
        isActive: false,
    },
    {
        name: "Thơ Quảng cáo sản phẩm",
        isActive: false,
    },
    {
        name: "Nhạc Doanh nghiệp",
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
};

const selectOption = ({ index = 0, value, indexOptionRewrite }) => {
    messagesMap["resultTarget"][index].options_rewrite[indexOptionRewrite].value = value;
    messagesMap["resultTarget"][index].options_rewrite[indexOptionRewrite].isOpen = false;
    // rewriteContentProductAd(index);
};

// Khai báo refs
const editorElement = ref(null);
const sunEditorInstance = ref(null);
const sunEditorContentInstance = ref(null);

const initEditorContent = () => {
    if(sunEditorContentInstance.value) {
        sunEditorContentInstance.value.setContents(resultContent.value);
        return true
    }
    nextTick(() => {
        // Đảm bảo container tồn tại
        const container = document.getElementById("editor-container-2");
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
        if (!sunEditorContentInstance.value) {
            sunEditorContentInstance.value = suneditor.create(textarea, {
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
        if (resultContent.value) {
            sunEditorContentInstance.value.setContents(resultContent.value);
        }
    });
};

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
const setOptionStep3 = () => {
    const url = new URL(window.location.href);
    const type = url.searchParams.get('type');
    const day = url.searchParams.get('day');

    const typeToIndex = {
        'bvqc': 0,
        'cdqc': 1,
        'tqc': 2,
        'anqc': 3
    };

    const dayToIndex = {
        7: 0,
        14: 1,
        21: 2,
        28: 3
    }

    sampleAnalysis.forEach(item => item.isActive = false);

    if (typeToIndex[type] !== undefined) {
        sampleAnalysis[typeToIndex[type]].isActive = true;
    }

    if (dayToIndex[day] !== undefined) {
        selectedProjectType.value = formProject.menu[dayToIndex[day]];
    }
}
onMounted(() => {
    setOptionStep3(sampleAnalysis);
    messagesMap["analysis"] = sampleAnalysis;
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".dropdown")) {
            options.forEach((option) => (option.isDropdownOpen = false));
        }
        toggleDropdownCampaign(false)
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
const startChat = async (key, init = false) => {
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
            project_id: businessProjectId.value || selectedProject.value.id
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
                                    if (init) {
                                        loading.value = false;
                                        sections[0].open = false;
                                        sections[1].open = true;
                                    }
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
                            // if (init) {
                            //     loading.value = false;
                            //     sections[0].open = false;
                            //     sections[1].open = true;
                            // }
                        }
                        if (data.event === "workflow_finished") {
                            // Đảm bảo đã nhận được toàn bộ content
                            await new Promise((resolve) => setTimeout(resolve, 100));
                            // Gọi API tính credit
                            await calculateStreamCredit(messages, conversationId.value);
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
const continueChat = async (key, query) => {

    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        return; // Dừng nếu không đủ credit
    }
    if (mode.value == "" || mode.value == null) {
        toast.error("Vui lòng chọn chế độ để sử dụng tính năng.");
        return;
    }

    if (!key) return;

    if (key === "analysis") {
        return (messagesMap[key] = [
            { name: "Bài viết Quảng cáo sản phẩm", isActive: true },
            { name: "Thơ Quảng cáo sản phẩm", isActive: false },
            { name: "Chiến dịch Quảng cáo sản phẩm", isActive: false },
            { name: "Phân tích thị trường", isActive: false },
            { name: "Nhạc Doanh nghiệp", isActive: false },
        ]);
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
                project_id: businessProjectId.value || selectedProject.value.id,
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
                case "poetry":
                    messageData = response.data.answer;
                    break;
                case "analysis":
                    messageData = response.data.answer.split("\n").map((item) => {
                        return { name: item, isActive: false };
                    });
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
            await calculateStreamCredit(messageData, conversationId.value);
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

    // Validate file size (max 10MB)
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if (file.size > maxSize) {
        toast.error("Kích thước hình ảnh tối đa là 10MB");
        e.target.value = "";
        return;
    }

    const imageUrl = await getS3URL(file, 2);
    if(imageUrl) {
        tempFormProject.value.image_product.url = imageUrl;
        if(createImageRef.value) {
            createImageRef.value.updateImageUrl(s3_url, imageUrl)
        }
    } else {
        toast.error('Có lỗi xảy ra trong quá trình upload')
    }
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

    const imageUrl = await getS3URL(file, 1);
    if(imageUrl) {
        tempFormProject.value.model_product.url = imageUrl
        if(createImageRef.value) {
            createImageRef.value.updateImageUrl(imageUrl)
        }
    } else {
        toast.error('Có lỗi xảy ra trong quá trình upload')
    }
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

    formData.append("image_model", modelImage);
    formData.append("image_product", productImage);
    formData.append("ratio", imageDimension.value);
    formData.append("prompt_kh", imageDescription.value);

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

const genImageByReplicate = async () => {
    combineImagesReplicate(tempFormProject.value.model_product?.url, tempFormProject?.value?.image_product?.url)
    .then((s3Url) => {
        if (s3Url) {
            tempFormProject.value.image_product.url = s3Url;
            if (createImageRef.value) {
                createImageRef.value.updateImageUrl(s3Url);
            }
        }
    })
    .catch((error) => {
        console.error("Lỗi khi kết hợp ảnh:", error);
        toast.error("Không thể kết hợp ảnh.");
    });
}

const combineImagesReplicate = async (modelImage, productImage) => {
    let formData = new FormData();

    formData.append("image_model", modelImage);
    formData.append("image_product", productImage);
    formData.append("ratio", imageDimension.value);
    formData.append("prompt_kh", imageDescription.value);

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

const generateSelfImage = async () => {
    const latestModelResponse = await axios.get(route('ai-image.get-latest-my-ai-image'));
    const latestModel = latestModelResponse.data?.data;

    if (!latestModel || !latestModel.id) {
        console.error("Không tìm thấy model_id gần nhất!");
        return tempFormProject.value.model_product.url;
    }

    const modelId = latestModel.id;

    let formData = new FormData();

    formData.append("aspect_ratio", "16:9");
    formData.append("model_id", modelId);
    formData.append("image_description", "Hình ảnh một người mẫu giống hệt với phong cách và giới tính của các ảnh trong tập dữ liệu training, ăn mặc thời trang phù hợp với đặc trưng giới tính đã được training, đang đứng tạo dáng chuyên nghiệp với một tay đặt phía trước ở vị trí như thể đang giới thiệu hoặc tương tác với một vật thể. Bối cảnh xung quanh được thiết kế hài hòa với tư thế, ánh sáng đẹp, phong cách chụp ảnh tạp chí chất lượng cao, toàn thân. Duy trì nghiêm ngặt đặc điểm giới tính và phong cách từ dữ liệu training, không thay đổi sang giới tính khác.");

    try {
        const response = await axios.post(route('ai-image.generate-my-ai-image'), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        if (response.data?.generated_images?.s3_url) {
            return response.data.generated_images.s3_url;
        } else {
            toast.error(response.data.message || "Không thể tạo ảnh.");
        }
    } catch (error) {
        console.error(error);
        toast.error("Lỗi khi gọi API tạo ảnh!");
    }
};
const generateImageBackgroundWithReplicate = async (image) => {
    let formData = new FormData();

    formData.append("image", tempFormProject.value.image_product.url);
    formData.append("ratio", imageDimension.value);
    formData.append("prompt_kh", imageDescription.value);

    try {
        const response = await axios.post(route('ai-business.create-image-background-by-replicate'), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        if (response.data.success == true) {
            return response.data.s3_url;
        } else {
            return await generateImageBackgroundWithFile(image);
        }
    } catch (error) {
        toast.error("Lỗi khi tạo ảnh!");
    }
};
const generateImageBackgroundWithFile = async (inputImageFile) => {
    let formData = new FormData();

    formData.append("image", inputImageFile);
    formData.append("prompt_kh", imageDescription.value);

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

const generateImageBackground = async (imageUrl) => {
    let formData = new FormData();

    formData.append("image_url", imageUrl);

    try {
        const response = await axios.post(route('ai-business.generate-image-background'), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        if (response.data.status === 'success') {
            return response.data.s3_url;
        } else {
            console.warn("Tạo ảnh nền không thành công. Chuyển sang generatePromptPerspective.");
            return await generatePromptPerspective(imageUrl);
        }
    } catch (error) {
        console.error(error);
        let s3url = await generatePromptPerspective(imageUrl);
        if (s3url) {
            return s3url;
        }
        toast.error("Lỗi khi gọi API tạo ảnh nền!");
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
    menu: [
        {
            title: 'Chiến dịch 7 ngày',
            isActive: false,
            maxPost: 7
        },
        {
            title: 'Chiến dịch 14 ngày',
            isActive: false,
            maxPost: 14
        },
        {
            title: 'Chiến dịch 21 ngày',
            isActive: false,
            maxPost: 21
        },
        {
            title: 'Chiến dịch 28 ngày',
            isActive: false,
            maxPost: 28
        }
    ]
});
const formProject = reactive({
    id: null,
    title: "",
    description: "",
    image_product: "",
    model_product: "",
    productLink: "",
    files: [],
    userId: null,
    menu: [
        {
            title: 'Chiến dịch 7 ngày',
            isActive: false,
            maxPost: 7
        },
        {
            title: 'Chiến dịch 14 ngày',
            isActive: false,
            maxPost: 14
        },
        {
            title: 'Chiến dịch 21 ngày',
            isActive: false,
            maxPost: 21
        },
        {
            title: 'Chiến dịch 28 ngày',
            isActive: false,
            maxPost: 28
        }
    ]
});

const handleFillBasicInformation = () => {
    if (formProject.title == "") {
        toast.error("Vui lòng nhập tên sản phẩm");
        return;
    }
    if (mode.value == "basic") {
        sections[0].open = false;
        sections[1].open = true
    }
    handleFillBasicInformationSubmit()
}

const handleFillBasicInformationSubmit = async () => {
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
            // sections[0].open = false;

            // if (tempFormProject.value.image_product.file == null && tempFormProject.value.files?.length <= 0) {
            //     toast.error('Bạn vui lòng tải ảnh sản phẩm hoặc tài liệu sản phẩm.')
            //     return;
            // }
            await handleProjectSubmit(true, true);
        }
    } catch (error) {
        console.error("Lỗi:", error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại.");
    } finally {
        loading.value = false;
    }
};
const handleProjectSubmit = async (isStartChat = true, init = false) => {
    try {
        for(var i = 0; i < sections.length; i++) {
            if(i > 0) {
                sections[i].open = false
            }
        }

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
        if (tempFormProject.value.image_product.url) {
            formData.append("image_product", tempFormProject.value.image_product.url);
            if(createImageRef.value) {
                createImageRef.value.updateImageUrl("",tempFormProject.value.image_product.url)
            }
        }
        if (tempFormProject.value.model_product.url) {
            formData.append("image_model", tempFormProject.value.model_product.url);
            if(createImageRef.value) {
                createImageRef.value.updateImageUrl(tempFormProject.value.model_product.url,"")
            }
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
            let project_type = ''
            let total_campaign_days = 0;
            if (selectedAnalysis.value === 'Chiến dịch Quảng cáo sản phẩm') {
                project_type = 'campaign'
                total_campaign_days = selectedProjectType.value?.maxPost || 0
            }
            formData.append("project_type", project_type);
            formData.append("total_campaign_days", total_campaign_days);
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
                await startChat("information_project", init);
            }
            if(mode.value == 'basic') {
                showSectionInfoV2()
            } else {
                showSectionInfo()
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

const callApiChatStream = async (payload) => {
     // Try primary endpoint
    payload.conversation_id = conversationId.value
    let response = await fetch(route("ai-business.sendChatStreaming"), {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify(payload),
    });
    return response
}
const deleteFaceBookContent = async (projectId) => {
    const payload = {
        project_id: projectId
    }
    let response = await fetch(route("ai-business.delete-facebook-content"), {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify(payload),
    });
    return response
}
const domain = import.meta.env.VITE_API_URL
const dataInfo = ref(null)
const submitInfo = async (muc_tieus = []) => {
    let data = {...(dataInfo.value || {})};
    if (fbContentRef.value) {
        showFBContent.value = true;
        openFaceBookContent.value = true
        await deleteFaceBookContent(formProject.id || 0);
        showFBContent.value = false;
        fbContentRef.value.callApiGetContentInterval(formProject.id);
        fbContentRef.value.setOpenMainSection();
    }
    sections[7].open = false;
    await updateMetaData(muc_tieus);
    data.currentStep = "buoc4"
    data.conversation_id = conversationId.value
    const payloadContinue = {
        query: JSON.stringify(data),
        project_id: formProject.id
    };
    const content = sunEditorInstance.value.getContents();
    data.projectId = formProject.id;
    data.maxPost = selectedProjectType.value.maxPost;
    data.userId = formProject.userId;
    data.domain = domain;
    data.muc_tieu = muc_tieus;
    data.answer = content;
    data.the_loai = 'Chiến dịch Quảng cáo sản phẩm'
    payloadContinue.query = JSON.stringify(data);
    payloadContinue.projectType = 'campaign';
    await callApiChatStream(payloadContinue)
}

const setValue = (project, isUpdate) => {
    formProject.id = project.id;
    formProject.userId = project.user_id;
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
    // const messageMapJson = project?.data_json;
    // if (messageMapJson?.information_project) {
    //     conversationId.value = messageMapJson?.information_project?.conversation_id;
    // }
};
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
    sections[2].open = true;
    isLoadingAnalysis.value = true;
    handleButtonClick("analysis", event, category, 2);
    handleProjectUpdateStep02();
};

const handleProjectUpdateStep02 = async () => {
    try {
        if (formProject.id && sunEditorInstance.value) {
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

            formData.append("title", formProject.title);
            formData.append("description", formProject.description);
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
    const maxSize = 15 * 1024 * 1024; // Giới hạn 15MB

    if (file.size > maxSize) {
        toast.error("File quá lớn! Vui lòng chọn file nhỏ hơn 15MB.");
        return;
    }
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
const projectExpert = ref(null)
const getDataProjectCampaign = () => {
    sections[2].open = true;
    sections[7].open = true;
    openFaceBookContent.value = true;
    showFBContent.value = true;
    messagesMap.content_product_ad_v2 = {
        formData: mokCampaign.value
    }
    if (fbContentRef.value) {
        fbContentRef.value.callApiGetContentInterval(formProject.id);
        fbContentRef.value.setOpenMainSection();
    }
}
const selectProject = async (item) => {
    showModalHistory.value = false;
    if(mode.value == "expert") {
        if(refProjectExpert.value) {
            refProjectExpert.value.handleDetailProject(item)
        }
        return
    }

    await nextTick();
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
    if(mode.value == 'basic') {
        showSectionInfoV2()
    }
    businessProjectId.value = item.id;
    formProject.id = item.id;
    formProject.userId = item.user_id;
    formProject.title = item.title;
    formProject.description = item.description;
    formProject.image_product = item.image_product;
    formProject.files = item.files;
    formProject.productLink = item.url;
    tempFormProject.value.image_product.url = item.image_product;
    tempFormProject.value.model_product.url = item.image_model;
    tempFormProject.value.files = item.files;
    activeListProject.value = false;
    if(createImageRef.value) {
        createImageRef.value.updateImageUrl(item.image_model, item.image_product)
    }
    imageModel.value =  item.image_model;
    //get data json
    const messageMapJson = item.data_json;
    const metaData = item.meta_data;
    formProject.data_json = messageMapJson;
    let formInfo = null
    if(messageMapJson.information_project.form_data) {
        formInfo = messageMapJson.information_project.form_data
    }
    if(messageMapJson.information_project.data_info) {
        dataInfo.value = messageMapJson.information_project.data_info
    } else {
        dataInfo.value = {
            "cta":"",
            "key_message":"",
            "maxLength":"500",
            "promotion_gift":"",
            "trending_context":""
        }
    }
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

            //scroll to section
            if (category.value) {
                category.value.scrollIntoView({ behavior: "smooth", block: "center", inline: "center" });
                messagesMap["analysis"] = sampleAnalysis;
            }
        }
    } else {
        sections[0].open = true;
    }
    if(metaData && mode.value != 'basic') {
        var metaDataJson = JSON.parse(metaData)
        console.log(metaDataJson, 'metaDataJson');
        for(var i = 0; i < messagesMap["analysis"].length; i++) {
            messagesMap["analysis"][i].isActive = false
            if(metaDataJson.the_loai == messagesMap["analysis"][i].name) {
                messagesMap["analysis"][i].isActive = true
            }
        }
        selectedProjectType.value = null;
        toggleDropdownCampaign(false);
        formProject.menu = formProject.menu.map(menu => {
            return { ...menu, isActive: false };
        });
        if (metaDataJson.the_loai == 'Chiến dịch Quảng cáo sản phẩm') {
            formProject.menu = formProject.menu.map(menu => {
                if (menu.maxPost == item.total_campaign_days) {
                    return { ...menu, isActive: true };
                }
                return menu;
            });
            const menu = formProject.menu.filter(menu => menu.maxPost == item.total_campaign_days);
            selectedProjectType.value = menu.length ? menu[0] : null;
            const muc_tieus = metaDataJson?.muc_tieu || [];
            mokCampaign.value[0].options.forEach(item => {
                item.isActive = muc_tieus.includes(item.label);
            });
            await nextTick();
            getDataProjectCampaign();
        }
        showSectionInfo(metaDataJson.the_loai, formInfo, true)
    }
    isLoadingCheckConversation.value = false;

};
const projectExpertKey = ref(0);
const newProject = async () => {
    showModalHistory.value = false;
    tempFormProject.value = {
        image_product: {
            url: "",
            file: null,
        },
        model_product: {
            url: "",
            file: null,
        },
        files: [],
        menu: [
            {
                title: 'Chiến dịch 7 ngày',
                isActive: false,
                maxPost: 7
            },
            {
                title: 'Chiến dịch 14 ngày',
                isActive: false,
                maxPost: 14
            },
            {
                title: 'Chiến dịch 21 ngày',
                isActive: false,
                maxPost: 21
            },
            {
                title: 'Chiến dịch 28 ngày',
                isActive: false,
                maxPost: 28
            }
        ]
    };
    if (mode.value == "" || mode.value == null) {
        toast.error("Vui lòng chọn chế độ để sử dụng tính năng.");
        return;
    }
    if (isLoadingSelectCreateContent.value || isLoadingCheckConversation.value || isLoadingAnalysis.value || isLoading.value || loading.value || isSending.value || documentLoading.value || autoCreaeImageLoading.value) {
        toast.error("Vui lòng chờ cho đến khi hoàn thành tác vụ hiện tại.");
        return;
    }
    projectExpertKey.value += 1;
    selectedProject.value = null;
    conversationId.value = null;
    hiddenSocialMedia.value = false;
    hiddenImage.value = false;
    hiddenVideo.value = true;
    Object.keys(messagesMap).forEach((key) => {
        delete messagesMap[key];
    });
    formProject.id = null;
    formProject.title = "";
    formProject.description = "";
    formProject.image_product = "";
    formProject.files = [];
    tempFormProject.value.image_product.url = "";
    tempFormProject.value.files = [];
    activeListProject.value = false;
    sections[0].open = true;
    sections[1].open = false;
    sections[2].open = false;
    isLoadingCheckConversation.value = false;
}

const selectedAnalysis = ref("");
const handleChangeMaxPost = (index) => {
    formProject.menu.forEach((item, i) => {
    if (i !== index) {
            item.isActive = false;
        }
    });
    formProject.menu[index].isActive = true;
    selectedProjectType.value = formProject.menu[index];
    toggleDropdownCampaign(false);
}
const showItemName = (index, item) => {
    if (messagesMap["analysis"][index].name !== 'Chiến dịch Quảng cáo sản phẩm') {
       return item.name;
    }
    const selectedMenu = formProject.menu.find(item => item.maxPost === selectedProjectType.value?.maxPost);

    return selectedMenu ? selectedMenu.title : 'Chiến dịch Quảng cáo sản phẩm';
}
const selectedProjectType = ref(null);
const handleToggleAnalysis = (index) => {
    if (messagesMap["analysis"][index].name === 'Chiến dịch Quảng cáo sản phẩm') {
        toggleDropdownCampaign();
        if (!selectedProjectType.value) {
            // formProject.menu[0].isActive = true;
            // selectedProjectType.value = formProject.menu[0]
        }
    } else {
        toggleDropdownCampaign(false);
    }
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
    // resetTargetOptions();
    // messagesMap.content_product_ad = null;
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
        if(analysisActive.value) {
            const result = await continueChat(
                key,
                JSON.stringify({
                    currentStep: `buoc3`,
                    the_loai: analysisActive.value.name,
                })
            );
        }

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
                currentStep: `buoc3`,
                the_loai: analysisActive.value.name === "Chiến dịch Quảng cáo sản phẩm" ? 'Chiến dịch Truyền thông' : analysisActive.value.name,
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
                currentStep: `buoc3`,
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

const handleSubmitMediaCampaignV2 = async () => {
    // sections[1].open = false;
    // sections[2].open = true;
    const key = "content_product_ad";
    dataInfo.value = {
                        currentStep: `buoc4`,
                        ...Object.fromEntries(messagesMap[key].formData.map((item) => [item.name, item.value])),
                    }
    const content = sunEditorInstance.value.getContents();
    messagesMap["information_project"] = content
    const updatedDataJson = {
            information_project: {
                answer:content,
                data_info: dataInfo.value,
                form_data:messagesMap[key].formData
            },
        };
    const formData = new FormData();
    formData.append("id", formProject.id);
    formData.append("data_json", JSON.stringify(updatedDataJson));
    await updateProject(formData);
    handleStep2();
}

const handleSubmitMediaCampaign = async () => {
    // const analysisActive = messagesMap["analysis"].find((item) => item.isActive);
    if (analysisActive.value.name === "Chiến dịch Quảng cáo sản phẩm") {
        toggleDropdownCampaign();
        submitInfo();
        return;
    } else {
        toggleDropdownCampaign(false);
    }
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
        if(messagesMap["analysis"]) {
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
            } else {
                result = await continueChat(
                    key,
                    JSON.stringify({
                        currentStep: `buoc4`,
                        ...Object.fromEntries(messagesMap[key].formData.map((item) => [item.name, item.value])),
                    })
                );
            }
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
            messagesMap[key].isLoading = false;
            isSending.value = false;
            await callSearchImage();
        }
    } catch (error) {
        console.error("handleSubmitContentProductAd ~ error:", error);
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
            messagesMap[key].isLoading = false;
            isSending.value = false;

            await callSearchImage();
        }
    } catch (error) {
        console.error("Lỗi khi lấy form content product ad:", error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại.");
    }
};
const handleChangeContent = async () => {
    stopAudio();
    await rewriteContentProductAd(0)
}
const rewriteContentProductAd = async (index) => {
    const key = "content_product_ad";
    try {
        messagesMap["resultTarget"][index].isLoadingRewrite = true;
        let allRewrite = {};
        messagesMap["resultTarget"][index].options_rewrite.forEach((item) => {
            allRewrite[convertToSnakeCase(item.name)] = item.value;
        });
        var query = {
                currentStep: `buoc5`,
                text: messagesMap["resultTarget"][0].content,
                ...allRewrite,
            }
        loadingContent.value = true
        resultContent.value = ""
        initEditorContent()
        const result = await sendDataV2(query, 2)
        if (result) {
            messagesMap["resultTarget"][index].content = marked(result);
            resultContent.value = ""
        }
        loadingContent.value = false
        const formData = new FormData();
        formData.append("id", formProject.id);
        formData.append("title", formProject.title);
        formData.append("content", result);

        await updateProject(formData);
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
                const goal = messagesMap[key]?.overview?.formData[0].value;
                const so_luong = messagesMap[key]?.overview?.formData[1].value;
                if (!goal) {
                    alert('Vui lòng chọn một mục tiêu chiến dịch');
                    return;
                }
                if (!so_luong) {
                    alert('Vui lòng chọn số lượng ý tưởng');
                    return;
                }
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
                    goal: goal,
                    so_luong: String(so_luong),
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

                let body = dataInfo.value
                const content = sunEditorInstance.value.getContents();
                body.answer = content
                body.y_tuong = bigIdeaActive.content
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
                        ...body,
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
    const key = "music";
    try {
        messagesMap[key] = {
            overview: {
                isLoading: true,
            },
        };
        const result = await continueChat(
            key,
            JSON.stringify({
                currentStep: `buoc3`,
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
        // messagesMap[key] = {
        //     overview: {
        //         isLoading: false,
        //     },
        // };
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
            case "Thơ Quảng cáo sản phẩm":
                await getFormPoetry();
                break;
            default:
                break;
        }

        // Tạm sửa để demo, demo xong tìm nguyên nhân rồi fix sau
        isChoosedCategory.value = true;

        // sections[2].open = false;
        sections[3].open = true;
    } catch (error) {
    } finally {
        isLoadingSelectCreateContent.value = false;
    }
};
const loadingPercent = ref(0);
const getDataStep2 = async () => {
    await getFormContentProductAd()
    sections[1].open = false;
    sections[2].open = true;
    messagesMap.resultTarget = null;
    messagesMap.resultsAnalysis = null;
    messagesMap.content_product_ad = null;
    messagesMap.poetry = null;
    messagesMap.music = null
    switch (analysisActive.value?.name) {
        case "Bài viết Quảng cáo sản phẩm":
            messagesMap.content_product_ad =  {
                formData:mockFormDataContentProductAd
            }
            break;
        case "Thơ Quảng cáo sản phẩm":
            messagesMap.content_product_ad =  {
                formData:mockFormDataPoetry
            }
            break;
        case "Nhạc Doanh nghiệp":
            await getFormContentMusic();
            break;
        case "Chiến dịch Quảng cáo sản phẩm":
            messagesMap.content_product_ad =  {
                formData:mokCampaign.value
            }
            break;
        default:
            break;
    }
}
const resetTargetOptions = () => {
    mokCampaign.value[0].options.forEach(item => {
        item.isActive = false;
    });
}
const handleProjectCampaignStep2 = async () => {
    if (!selectedProjectType.value) {
        toast.error('Vui lòng chọn số ngày chiến dịch');
        return;
    }
    toggleDropdownCampaign(false);
    messagesMap.content_product_ad_v2 = null;
    isLoadingSelectCreateContent.value = true;
    loadingPercent.value = 0;
    simulateLoading();
    const formData = new FormData();
    formData.append("id", formProject.id);
    formData.append("title", formProject.title);
    formData.append("project_type", 'campaign');
    formData.append("total_campaign_days", selectedProjectType.value?.maxPost);
    await updateProject(formData);
    await updateMetaData();
    sections[7].open = true;
    sections[2].open = false;
    sections[1].open = false;
    resetTargetOptions();
    messagesMap.content_product_ad_v2 =  {
        formData:mokCampaign.value
    }
    isLoadingSelectCreateContent.value = false;
}
const resultContent = ref("")
const handleStep2 = async () => {
    // const analysisActive = messagesMap["analysis"].find((item) => item.isActive);
    if(analysisActive?.value?.name == 'Chiến dịch Quảng cáo sản phẩm') {
        handleProjectCampaignStep2();
        return;
    }
    const key = "content_product_ad";
    isLoadingSelectCreateContent.value = true;
    loadingPercent.value = 0;
    simulateLoadingV2();
    await continueChat(
        key,
        JSON.stringify({
            currentStep: `buoc3`,
            the_loai: analysisActive?.value?.name,
        })
    );
    if(analysisActive?.value?.name == 'Nhạc Doanh nghiệp') {
        isLoadingSelectCreateContent.value = true;
        sections[6].open = true
        await getFormContentMusic();
        sections[1].open = false;
        sections[2].open = false
        isLoadingSelectCreateContent.value = false;
        return false
    }
    try {
        if (!messagesMap[key]) {
            messagesMap[key] = {
                formData: [],
                isLoading: false,
            };
        }

        messagesMap[key].isLoading = true;
        const content = sunEditorInstance.value.getContents();
        let query = dataInfo.value
        query.currentStep = `buoc4`
        query.answer = content
        loadingContent.value = true
        sunEditorContentInstance.value = null;
        const result = await sendDataV2(query)
        sections[1].open = false;
        // messagesMap["resultTarget"] = [];
        if (result) {
            const jsonStartIndex = result.indexOf("{");
            const jsonEndIndex = result.indexOf("}") + 1;

            const jsonPart = result.substring(jsonStartIndex, jsonEndIndex);
            const content = result.substring(jsonEndIndex).trim();

            const jsonData = JSON.parse(jsonPart);
            if (!messagesMap["resultTarget"]) {
                messagesMap["resultTarget"] = [];
            }
            // messagesMap["resultTarget"][0] = {
            //     content: marked(helpers.convertHtmlToText(content)),
            //     options_rewrite: [
            //         ...Object.keys(jsonData).map((key) => ({
            //             name: key,
            //             value: "",
            //             options: jsonData[key] || [],
            //             isOpen: false,
            //         })),
            //     ],
            //     images: [],
            // };
            sections.forEach((item) => {
                item.open = false;
            });
            resultContent.value = ""
            await nextTick();
            if (resultTarget.value) {
                resultTarget.value.scrollIntoView({ behavior: "smooth", block: "center", inline: "center" });
            }

            const formData = new FormData();
            formData.append("id", formProject.id);
            formData.append("title", formProject.title);
            formData.append("content", content);

            await updateProject(formData);
            messagesMap[key].isLoading = false;
            isSending.value = false;
            await callSearchImage();
            isLoadingSelectCreateContent.value = false;
        }
    } catch (error) {
        isLoadingSelectCreateContent.value = false;
        console.error("handleSubmitContentProductAd ~ error:", error);
    }
}
const loadingContent = ref(false)
const updateContent = async (result) => {
    if (result) {
        const jsonStartIndex = result.indexOf("{");
        const jsonEndIndex = result.indexOf("}") + 1;

        const jsonPart = result.substring(jsonStartIndex, jsonEndIndex);
        const content = result.substring(jsonEndIndex).trim();
        resultContent.value = marked(content)
        if(content) {
            loadingContent.value = false
        }
        initEditorContent()
    }
}

const updateResult = async (result) => {
    if (result) {
        const jsonStartIndex = result.indexOf("{");
        const jsonEndIndex = result.indexOf("}") + 1;

        const jsonPart = result.substring(jsonStartIndex, jsonEndIndex);
        const content = result.substring(jsonEndIndex).trim();
        resultContent.value = marked(content)
        if(content) {
            loadingContent.value = false
        }
        const targetSection = document.getElementById('editor-container-2');
        if (targetSection && content.length < 200) {
            targetSection.scrollIntoView({ behavior: 'smooth' });
        } else {
            const targetSection2 = document.getElementById('box-content');
            if (targetSection2) {
                targetSection2.scrollIntoView({ behavior: 'smooth' });
            }
        }
        const jsonData = JSON.parse(jsonPart);
        messagesMap["resultTarget"] = []
        messagesMap["resultTarget"][0] = {
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
        };
        sections[2].open = false
        initEditorContent()
    }
}

const sendDataV2 = async (query, type = 1) => {
    try {

        const payload = {
            query: JSON.stringify(query),
            project_id: businessProjectId.value || selectedProject.value.id
        };

        // Try primary endpoint
        let response = await callApiChatStream(payload)

        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let buffer = "";
        let messages = "";
        while (true) {
            const { value, done } = await reader.read();
            if (done) break;

            buffer += decoder.decode(value, { stream: true });
            const lines = buffer.split("\n");
            buffer = lines.pop() || "";

            for (const line of lines) {
                if (line.trim().startsWith("data: ")) {
                    try {
                        const jsonStr = line.trim().slice(5);
                        const data = JSON.parse(jsonStr);

                        if (data.event === "message" && data.answer) {
                            messages += data.answer;
                            if(type == 2) {
                                await updateContent(messages)
                            } else {
                                await updateResult(messages)
                            }
                        }

                        if (data.event === "workflow_started" && data.conversation_id) {
                            conversationId.value = data.conversation_id;
                        }

                        if (data.event === "workflow_finished") {
                            await new Promise(resolve => setTimeout(resolve, 100));
                            await calculateStreamCredit(messages, conversationId.value);
                        }
                    } catch (e) {
                        console.error("JSON parse error:", e);
                        continue;
                    }
                }
            }
        }

        // Process remaining buffer
        if (buffer.trim()) {
            try {
                const data = JSON.parse(buffer.trim().slice(5));
                if (data.event === "message" && data.answer) {
                    messages += data.answer;
                    information_project.value = messages.trim();
                    if(type == 2) {
                        await updateContent(messages)
                    } else {
                        await updateResult(messages)
                    }
                }
            } catch (e) {
                console.warn("Final buffer parse error:", e);
            }
        }
        return messages
    } catch (error) {
        console.error("Chat error:", error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại.");
    } finally {
        loading.value = false;
    }
}

const callApiChatStreamV2 = async (payload) => {
     // Try primary endpoint
    payload.conversation_id = conversationId.value
    let response = await fetch(route("ai-business.sendChatStreaming"), {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify(payload),
    });
    return response
}

const showSectionInfo = async (the_loai = "", formData = "", is_edit = false) => {
    if(the_loai) {
        analysisActive.value.name = the_loai
    }
    sections[1].open = !is_edit;
    var mockProAdData = mockFormDataContentProductAd
    if(formData) {
        mockProAdData = formData
    }
    messagesMap.content_product_ad =  {
        formData:mockProAdData
    }
}

const showSectionInfoV2 = async () => {
    sections[1].open = true;
    messagesMap["analysis"] = sampleAnalysis;
    analysisActive.value = {
        name:'Chiến dịch Quảng cáo sản phẩm'
    }
    await getFormContentProductAd()
    messagesMap.resultTarget = null;
    messagesMap.resultsAnalysis = null;
    messagesMap.content_product_ad = null;
    messagesMap.poetry = null;
    messagesMap.music = null
    messagesMap.content_product_ad =  {
        formData:mockFormDataContentProductAd
    }
}

const mokCampaign = ref([
    {
        name:"",
        options: [
            {
                "label": "Biết - Tăng Nhận Thức Thương Hiệu",
                "isActive": false
            },
            {
                "label": "Biết - Quảng Bá Sản Phẩm/Dịch Vụ",
                "isActive": false
            },
            {
                "label": "Biết - Tạo Nội Dung Viral",
                "isActive": false
            },
            {
                "label": "Biết - Tối Ưu Hóa Cho SEO",
                "isActive": false
            },
            {
                "label": "Biết - Tạo Dẫn Đường Cho Trang Đích",
                "isActive": false
            },
            {
                "label": "Biết - Phát Triển Thương Hiệu Cá Nhân",
                "isActive": false
            },
            {
                "label": "Hiểu - Giáo Dục Khách Hàng",
                "isActive": false
            },
            {
                "label": "Hiểu - Hỗ Trợ Chiến Dịch Marketing",
                "isActive": false
            },
            {
                "label": "Hiểu - Phá vỡ rào cản khách hàng",
                "isActive": false
            },
            {
                "label": "Hiểu - Tạo Uy Tín và Chứng Minh Chuyên Môn",
                "isActive": false
            },
            {
                "label": "Hiểu - Thúc Đẩy Tương Tác Khách Hàng",
                "isActive": false
            },
            {
                "label": "Tin - Xây Dựng và Nuôi Dưỡng Mối Quan Hệ Khách Hàng",
                "isActive": false
            },
            {
                "label": "Tin - Phản Hồi và Giải Quyết Vấn Đề của Khách Hàng",
                "isActive": false
            },
            {
                "label": "Tin - Tối Ưu Hóa Trải Nghiệm Khách Hàng",
                "isActive": false
            },
            {
                "label": "Tin - Tạo Lead và Chuyển Đổi",
                "isActive": false
            },
            {
                "label": "Tin - Thúc Đẩy Bán Hàng và Tạo Doanh Thu",
                "isActive": false
            },
            {
                "label": "Yêu - Xây Dựng Cộng Đồng",
                "isActive": false
            },
            {
                "label": "Yêu - Thúc Đẩy Văn Hóa Doanh Nghiệp",
                "isActive": false
            },
            {
                "label": "Yêu - Tác Động Xã Hội và Trách Nhiệm Cộng Đồng",
                "isActive": false
            }
        ],
        type: "checkbox",
        label: "",
        value: "",
    }
])

const mockFormDataPoetry = [
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

const mockFormDataContentProductAd = [
    {
        name: "trending_context",
        type: "text",
        label: "Bối cảnh",
        value: "",
        placeholder: "Bối cảnh là xu hướng hoặc sự kiện để bài viết chạm được tới tâm trí đám đông nhanh nhất và dễ nhất."
    },
    {
        name: "key_message",
        type: "textarea",
        label: "Thông điệp chính",
        value: "",
        placeholder: "Thông điệp chính là nội dung trọng tâm bạn muốn khách hàng ghi nhớ."
    },
    {
        name: "promotion_gift",
        type: "text",
        label: "Ưu đãi và quà tặng",
        value: "",
        placeholder: "Ưu đãi/quà tặng giúp tăng sức hút và thúc đẩy hành động."
    },
    {
        name: "cta",
        type: "select",
        label: "Thêm CTA (Lời Kêu Gọi Hành Động)",
        value: "",
        options: [
            { label: "Mua Ngay", value: "Mua Ngay" },
            { label: "Khuyến khích liên kết và tương tác", value: "Khuyến khích liên kết và tương tác" },
            { label: "Đăng Ký tham gia", value: "Đăng Ký tham gia" },
            { label: "Xem Chi Tiết", value: "Xem Chi Tiết" },
            { label: "Liên Hệ Chúng Tôi", value: "Liên Hệ Chúng Tôi" },
            { label: "Thêm Chia Sẻ", value: "Thêm Chia Sẻ" },
            { label: "Tăng bình luận", value: "Tăng bình luận" },
        ],
    },
     {
        name: "maxLength",
        type: "select",
        label: "Độ dài bài viết",
        value: "500",
        options: [
            { label: "200 từ", value: "200" },
            { label: "300 từ", value: "300" },
            { label: "400 từ", value: "400" },
            { label: "500 từ", value: "500" },
            { label: "1000 từ", value: "1000" },
            { label: "1500 từ", value: "1500" },
        ],
    },
];
const simulateLoading = () => {
    if (loadingPercent.value < 99) {
        const increment = Math.floor(Math.random() * 8) + 5; // Random increment between 2-5%
        loadingPercent.value = Math.min(loadingPercent.value + increment, 99); // Cap at 99%
        setTimeout(simulateLoading, 300); // Update every 10 seconds
    }
};

const simulateLoadingV2 = () => {
    if (loadingPercent.value < 99) {
        const increment = Math.floor(Math.random() * 8) + 5; // Random increment between 2-5%
        loadingPercent.value = Math.min(loadingPercent.value + increment, 99); // Cap at 99%
        setTimeout(simulateLoadingV2, 3000); // Update every 10 seconds
    }
};
const updateMetaData = async (muc_tieu) => {
    const payload = {
        inputs: {},
        query: JSON.stringify({
            currentStep: `buoc3`,
            the_loai: analysisActive.value.name,
            muc_tieu: muc_tieu
        }),
        conversation_id: conversationId.value,
        project_id: businessProjectId.value || selectedProject.value.id,
    };

    await axios.post(route("ai-business.sendChat"), payload, {
        headers: {
            "Content-Type": "application/json;charset=UTF-8",
        },
    });
}
const handleButtonSelectCreateContent = async (event) => {
    let muc_tieus = []
    let key = "content_product_ad";

    if (analysisActive.value?.name === 'Chiến dịch Quảng cáo sản phẩm') {
        key = "content_product_ad_v2";
        for(var i = 0 ; i < messagesMap[key].formData[0].options.length; i++) {
            if(messagesMap[key].formData[0].options[i].isActive) {
                muc_tieus.push(messagesMap[key].formData[0].options[i].label)
            }
        }
        if (!muc_tieus.length) {
            toast.error('Vui lòng chọn 1 mục tiêu');
            return;
        }
        await submitInfo(muc_tieus);
        return;
    }
    try {
        isGenAIStatus.value = false;
        loadingPercent.value = 0; // Initialize loading percentage
        // Function to simulate loading progress
        const simulateLoading = () => {
            if (loadingPercent.value < 99) {
                const increment = Math.floor(Math.random() * 8) + 5; // Random increment between 2-5%
                loadingPercent.value = Math.min(loadingPercent.value + increment, 99); // Cap at 99%
                setTimeout(simulateLoading, 300); // Update every 10 seconds
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
            case "Chiến dịch Quảng cáo sản phẩm":
                // Tạm sửa để demo, demo xong tìm nguyên nhân rồi fix sau
                // if (isChoosedCategory.value) {
                //     console.log(selectedProject.value.id);
                //     const currentUrl = new URL(window.location.href);
                //     currentUrl.searchParams.set("projectId", selectedProject.value.id);
                //     window.location.replace(currentUrl.toString());
                //     return;
                // } else {
                //     isChoosedCategory.value = true;
                // }
                isChoosedCategory.value = true;
                await getFormMediaCampaign();
                break;
            case "Nhạc Doanh nghiệp":
                // Tạm sửa để demo, demo xong tìm nguyên nhân rồi fix sau
                isChoosedCategory.value = true;

                await getFormContentMusic();
                    sections[2].open = true;

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

const promptBackground = ref("");
const genBackgroundProductImage = ref("");
const isLoadingSearchImage = ref(false);
const callSearchImage = async (targetIndex = 0) => {

    for (let i = 0; i < 4; i++) {
        const image = {
            imageRef: {
                s3_url: "", // hoặc null nếu bạn muốn
            },
            previewImageUpload: null,
            fileImageUpload: null,
            isShowOptionImage: false,
            isLoading: false,
        };
        messagesMap["resultTarget"][targetIndex].images.push(image);
    }
    return;


    try {
        autoCreaeImageLoading.value = true
        let savedCount = 0;
        let imageUrls = [];
        let productImage = null;
        let additionalImages = [];

        if (tempFormProject.value.image_product.url) {
            // Convert URL to File
            const proxyUrl = `/proxy-image?url=${encodeURIComponent(tempFormProject.value.image_product.url)}`;
            const response = await axios.get(proxyUrl, { responseType: "blob" });
            const blob = new Blob([response.data], { type: "image/jpeg" });
            const file = new File([blob], "image_product.jpg", { type: "image/jpeg" });
            tempFormProject.value.image_product.file = file;

            let combinedImage, selfImage1, selfImage2;
            // Ảnh sản phẩm gốc
            // await saveGenerationResult(tempFormProject.value.image_product.url, "", "image", targetIndex);

            if (tempFormProject.value.model_product?.url) {
                // Model product URL exists, proceed with conversion to blob and image combining
                const proxyUrlModel = `/proxy-image?url=${encodeURIComponent(tempFormProject.value.model_product.url)}`;
                const responseModel = await axios.get(proxyUrlModel, { responseType: "blob" });
                const blobModel = new Blob([responseModel.data], { type: "image/jpeg" });
                const fileModel = new File([blobModel], "model_product.jpg", { type: "image/jpeg" });
                tempFormProject.value.model_product.file = fileModel;

                // Ảnh phối cảnh
                const resizedFile = await resizeAndPadImage(tempFormProject.value.image_product.file);
                const resizedFile2 = await resizeAndPadImage(tempFormProject?.value?.model_product?.file);
                genBackgroundProductImage.value = await generateImageBackgroundWithFile(resizedFile);
                await saveGenerationResult(genBackgroundProductImage.value, "", "image", targetIndex);

                // Ảnh combine từ AI
                combinedImage = await combineImages(resizedFile, resizedFile2);
                await saveGenerationResult(combinedImage, "", "image", targetIndex);

                // Ảnh tạo từ model tự Sướng
                let model_link = await generateSelfImage();
                const defaultModelBlob = await urlToBlob(model_link);
                if (!defaultModelBlob) {
                    toast.error("Không thể tải ảnh mẫu mặc định!");
                    return;
                }
                selfImage1 = await combineImages(resizedFile, defaultModelBlob);
                await saveGenerationResult(selfImage1, "", "image", targetIndex);

                let model_link2 = await generateSelfImage();
                const defaultModelBlob2 = await urlToBlob(model_link2);
                if (!defaultModelBlob2) {
                    toast.error("Không thể tải ảnh mẫu mặc định!");
                    return;
                }
                selfImage2 = await combineImages(resizedFile, defaultModelBlob2);
                await saveGenerationResult(selfImage2, "", "image", targetIndex);
            } else {
                const latestModelResponse = await axios.get(route('ai-image.get-latest-my-ai-image'));
                const latestModel = latestModelResponse.data?.data;
                if (latestModel?.id) {
                    // Up ảnh sản phẩm + k up ảnh người mẫu nhưng đã training ảnh tự sướng -> mong muốn tạo 4 ảnh sau : Ảnh 1 : phối cảnh Ảnh 2+3+4 : tự sướng cầm sản phẩm**

                    label_images.value = ['Ảnh phối cảnh của sản phẩm', 'Ảnh tự sướng của bạn ghép với sản phẩm', 'Ảnh tự sướng của bạn ghép với sản phẩm', 'Ảnh tự sướng của bạn ghép với sản phẩm'];
                    const resizedFile = await resizeAndPadImage(tempFormProject.value.image_product.file);
                    genBackgroundProductImage.value = await generateImageBackgroundWithFile(resizedFile);
                    await saveGenerationResult(genBackgroundProductImage.value, "", "image", targetIndex);

                    // Tạo ảnh tự sướng ghép ảnh sản phẩm
                    let model_link3 = await generateSelfImage();
                    const defaultModelBlob3 = await urlToBlob(model_link3);
                    if (!model_link3) {
                        toast.error("Không thể tải ảnh mẫu tự sướng! Chuyển sang chế độ tạo ảnh phối cảnh.");
                        combinedImage = await generatePromptPerspectiveWithFile(resizedFile);
                        await saveGenerationResult(combinedImage, "", "image", targetIndex);
                    } else {
                        combinedImage = await combineImages(resizedFile, defaultModelBlob3);
                        await saveGenerationResult(combinedImage, "", "image", targetIndex);
                    }

                    let model_link = await generateSelfImage();
                    const defaultModelBlob = await urlToBlob(model_link);
                    if (!defaultModelBlob) {
                        toast.error("Không thể tải ảnh mẫu tự sướng! Chuyển sang chế độ tạo ảnh phối cảnh.");
                        selfImage1 = await generatePromptPerspectiveWithFile(resizedFile);
                        await saveGenerationResult(selfImage1, "", "image", targetIndex);
                    } else {
                        selfImage1 = await combineImages(resizedFile, defaultModelBlob);
                        await saveGenerationResult(selfImage1, "", "image", targetIndex);
                    }

                    let model_link2 = await generateSelfImage();
                    const defaultModelBlob2 = await urlToBlob(model_link2);
                    if (!defaultModelBlob2) {
                        toast.error("Không thể tải ảnh mẫu tự sướng! Chuyển sang chế độ tạo ảnh phối cảnh.");
                        selfImage2 = await generatePromptPerspectiveWithFile(resizedFile);
                        await saveGenerationResult(selfImage2, "", "image", targetIndex);
                    } else {
                        selfImage2 = await combineImages(resizedFile, defaultModelBlob2);
                        await saveGenerationResult(selfImage2, "", "image", targetIndex);
                    }
                }
                else {
                    // Up ảnh sản phẩm + k up ảnh người mẫu + chưa training ảnh tự sướng
                    const resizedFile = await resizeAndPadImage(tempFormProject.value.image_product.file);
                    label_images.value = ['Ảnh phối cảnh của sản phẩm', 'Ảnh phối cảnh của sản phẩm', 'Ảnh phối cảnh của sản phẩm', 'Ảnh phối cảnh của sản phẩm'];

                    genBackgroundProductImage.value = await generateImageBackgroundWithFile(resizedFile);
                    await saveGenerationResult(genBackgroundProductImage.value, "", "image", targetIndex);
                    combinedImage = await generatePromptPerspectiveWithFile(resizedFile);
                    await saveGenerationResult(combinedImage, "", "image", targetIndex);
                    selfImage1 = await generatePromptPerspectiveWithFile(resizedFile);
                    await saveGenerationResult(selfImage1, "", "image", targetIndex);
                    selfImage2 = await generatePromptPerspectiveWithFile(resizedFile);
                    await saveGenerationResult(selfImage2, "", "image", targetIndex);
                }
            }

            imageUrls = [
                genBackgroundProductImage.value,
                combinedImage,
                selfImage1,
                selfImage2
            ];

        } else {
            const latestModelResponse = await axios.get(route('ai-image.get-latest-my-ai-image'));
            const latestModel = latestModelResponse.data?.data;
            console.log("debug: ", latestModel);
            if (latestModel?.id) {
                console.log(latestModel);
                label_images.value = ['Ảnh tự sướng của bạn', 'Ảnh tự sướng của bạn', 'Ảnh tự sướng của bạn', 'Ảnh tự sướng của bạn'];
                for (let i = 0; i < 4; i++) {
                    let model_link = await generateSelfImage();
                    await saveGenerationResult(model_link, "", "image", targetIndex);
                    imageUrls.push(model_link);
                }

                await updateProject({
                    id: formProject.id,
                    title: formProject.title,
                    content: formProject.content,
                    image_urls: imageUrls,
                });

                return;
            } else {
                label_images.value = ['Ảnh tìm kiếm từ internet', 'Ảnh tìm kiếm từ internet', 'Ảnh tìm kiếm từ internet', 'Ảnh tìm kiếm từ internet'];
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
                imageUrls = additionalImages.slice(0, 5);
                for (let i = 0; i < imageUrls.length; i++) {
                    await saveGenerationResult(imageUrls[i], "", "image", targetIndex);
                }
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
        autoCreaeImageLoading.value = false
    }
};

const generatePromptPerspective = async (imageUrl) => {
    try {
        let formData = new FormData();
        formData.append("image_url", imageUrl);

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

const generateAiBackground = async () => {
    try {
        if (!promptBackground.value || !tempFormProject.value.image_product.file) {
            toast.error("Thiếu prompt hoặc ảnh sản phẩm!");
            return null; // Return null if missing prompt or image
        }

        let formData = new FormData();
        formData.append("prompt_background", promptBackground.value);
        formData.append("imageFile", tempFormProject.value.image_product.file);

        const response = await axios.post(route("ai-background.generate-ai-background-v2-with-file"), formData, {
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
    "Hãy tạo bức ảnh truyền tải cảm xúc sâu sắc từ bài viết, Nhất quyết không thêm mô tả bằng chữ, không được thêm chữ dưới mọi hình thức vào ảnh. không thêm chữ vào ảnh, giúp người xem không chỉ nhìn thấy sản phẩm/dịch vụ mà còn cảm nhận được giá trị tinh thần, lợi ích cảm xúc khi sở hữu nó.  - Chủ đề chính của ảnh: Không tập trung vào sản phẩm, mà khai thác trải nghiệm, cảm giác, giá trị tinh thần mà sản phẩm mang lại.  Hãy đi từ Đối tượng tiềm năng của sản phẩm - sau đó mô tả cảm giác của \"đối tượng\" đó khi sở hữu hoặc sử dụng sản phẩm. Ví dụ:  + Nếu là bất động sản → Cảm giác bình yên, an cư, gắn kết gia đình, sự thành công  + Nếu là sản phẩm công nghệ → Cảm giác hiện đại, tiện lợi, an tâm, kiểm soát tốt hơn cuộc sống + Nếu là sản phẩm sức khỏe → Cảm giác hạnh phúc, tràn đầy năng lượng, an tâm cho gia đình  - Bối cảnh phù hợp với cảm xúc: (VD: ngôi nhà ấm áp với gia đình hạnh phúc, doanh nhân thành đạt tự tin, một người thư giãn tận hưởng thành quả...) - Đảm bảo rằng Không thêm bất kỳ ký tự nào vào ảnh theo bất cứ trường hợp nào 🔥 Hãy đảm bảo hình ảnh không chỉ minh họa sản phẩm/dịch vụ, mà còn đánh trúng vào cảm xúc, giá trị tinh thần mà khách hàng sẽ có được khi sở hữu nó.  Hình ảnh phải trông thực tế nhất có thể",

    "Hãy tạo bức ảnh thể hiện trực quan, chủ đề dựa trên Nội dung chính của bài viết, Nhất quyết không thêm mô tả bằng chữ, không được thêm chữ dưới mọi hình thức vào ảnh. không thêm chữ vào ảnh. - Đối tượng chính của ảnh: Khai thác từ phần [Nội dung bài viết] - Các yếu tố quan trọng cần minh họa: Hãy tìm kiếm các yếu tố liên quan tới Sản Phẩm / Dịch vụ mà bài viết quảng cáo. Hoặc nếu là bài viết không tập trung vào Sản phẩm/Dịch vụ thì tìm kiếm các yếu tố xung quanh bài viết. - Cảm xúc cần thể hiện: Cần truyền tải được [Cảm xúc mong muốn truyền tải] - Phong cách hình ảnh: Cần truyền tải được [Phong cách viết] - Đảm bảo rằng Không thêm bất kỳ ký tự nào vào ảnh theo bất cứ trường hợp nào - Tông màu và hiệu ứng phù hợp với nội dung: (VD: màu sắc thiên nhiên nếu sản phẩm là hữu cơ, màu sắc công nghệ nếu sản phẩm số...) Hãy đảm bảo mỗi ảnh thể hiện một khía cạnh khác nhau của bài viết, tạo nên sự đồng nhất nhưng không lặp lại. Hình ảnh phải trông thực tế nhất có thể",

    "Hãy tạo bức ảnh giúp truyền tải mục tiêu chính của bài viết, Nhất quyết không thêm mô tả bằng chữ, không được thêm chữ dưới mọi hình thức vào ảnh. không thêm chữ vào ảnh. Sử dụng [Nội dung bài viết] để nắm được đối tượng của bức ảnh. Nhưng Chủ đề Chính sẽ khai thác từ phần [Mục tiêu bài viết] (VD: tăng nhận diện thương hiệu, kích thích mua hàng, xây dựng niềm tin, tạo sự tương tác...) Dựa trên Chủ đề Chính sẽ tạo ra 1 bức ảnh tập trung vào yếu tố mục tiêu bài viết. Và Cảm xúc cần thể hiện: Cần truyền tải được [Cảm xúc mong muốn truyền tải] mà người dùng đã điền. - Đảm bảo rằng Không thêm bất kỳ ký tự nào vào ảnh theo bất cứ trường hợp nào Hãy đảm bảo hình ảnh thể hiện kết quả rõ ràng, giúp người xem dễ dàng cảm nhận được mục tiêu của bài viết. Hình ảnh phải trông thực tế nhất có thể",

    "Hãy tạo bức ảnh với  biểu tượng mạnh mẽ, không minh họa trực tiếp sản phẩm hay nội dung bài viết, Nhất quyết không thêm mô tả bằng chữ, không được thêm chữ dưới mọi hình thức vào ảnh. không thêm chữ vào ảnh, mà tập trung vào 1 key cụ thể về giá trị cốt lõi và thông điệp sâu xa của bài viết. - Key chính của bài viết: (Không phải sản phẩm, mà là ý nghĩa sâu xa mà sản phẩm mang lại. VD: \"Thành công\", \"Cân bằng\", \"Hồi sinh\", \"Sự bảo vệ\", \"Phát triển bền vững\"...)  - Hình ảnh biểu tượng để minh họa Key chính: (VD: Kim tự tháp thể hiện sự bền vững, Ngọn lửa tượng trưng cho đam mê, Dòng sông chảy mãi đại diện cho sự phát triển...) - Đảm bảo rằng Không thêm bất kỳ ký tự nào vào ảnh theo bất cứ trường hợp nào Hãy đảm bảo hình ảnh thể hiện kết quả rõ ràng, giúp người xem dễ dàng hiểu được tác động của sản phẩm/dịch vụ. Chú ý hình ảnh thực sự đơn giản, có tính biểu tượng cao, dễ nhớ, gây tò mò và thể hiện rõ thông điệp sâu xa của bài viết. Hình ảnh phải trông thực tế nhất có thể",
]);
const getReplacedPrompts = () => {
    const formDataRaw = messagesMap["content_product_ad"]?.formData || messagesMap["poetry"]?.formData;

const formData = Array.isArray(formDataRaw) ? formDataRaw : [];
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
        const promptObj = prompts[index];
        let descriptionToSend = `${content} ${promptObj}`;
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
                    currentStep: `buoc2`,
                    content: content,
                });
                await continueChat("analysis", query);
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

const handleFileChange = async (event, index, index_image = null) => {
    if (event.target?.files?.length === 0) {
        return;
    }

    const file = event.target.files[0];

    if (file.type.includes("image")) {
        handleImageStyle(file, index, index_image);
        return;
    }

    if (file.type.includes("video")) {
        handleVideoStyle(file, index);
        return;
    }
};

const handleImageStyle = async (file, index, index_image = null) => {
    if (allowedExtension.indexOf(file.type) < 0) {
        toast.error("Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg");
        return;
    }

    if (index_image == null && !validateLimitImage(index)) {
        return;
    }

    if (file) {
        if (index_image !== null) {
            if(!messagesMap["resultTarget"][index].images[index_image]) {
                let image = {
                    previewImageUpload: null,
                    fileImageUpload: null,
                    imageRef: {
                        s3_url: null
                    },
                    isLoading: true
                };
                messagesMap["resultTarget"][index].images[index_image] = image
                updateImages()
            } else {
                messagesMap["resultTarget"][index].images[index_image].isLoading = true;
            }
        }
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
                previewImageUpload: value,
                fileImageUpload: file,
                imageRef: {
                    s3_url: s3_url
                },
                isLoading: false
            };

            if (index_image !== null) {
                messagesMap["resultTarget"][index].images[index_image] = image;
                messagesMap["resultTarget"][index].images[index_image].isLoading = false;
            } else {
                messagesMap["resultTarget"][index].images.push(image);
            }
            updateImages()
            selectImage(image);
            updateClassImage()
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
                    options_rewrite: mockFormDataContentProductAd
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

            if(musicUrl.value) {
                 postForm.files = [
                     {
                         s3_url: musicUrl.value,
                         type: "video",
                     },
                 ];
             }
            postFormRef.value.openPostDetail(postForm);
        };
    }

    const getTarget = () => {
        if(analysisActive.value.name !== 'Chiến dịch Quảng cáo sản phẩm')
            return '';

        const key = 'content_product_ad';
        const activeOptions = messagesMap[key].formData[0].options
            .filter((option) => option.isActive)
            .map((option) => option.value);

        return activeOptions[0]
    };

    const beforeSubmitPostForm = () => {


    }

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
const slowlyIncreasePercent = async () => {
    for (let i = percenLoadingVideo.value; i < 100; i++) {
        if (percenLoadingVideo.value < 99) {
            await sleep(500);
            percenLoadingVideo.value++;
        }
    }
};
const autoCreateVideo = async () => {
    if (!isConfirm.value) {
        showConfirmVideo.value = true;
        return;
    }

    if (showVideoGenerate.value || autoCreaeImageLoading.value) {
        return;
    }

    isLoadingVideo.value = true;
    showVideoGenerate.value = true;
    percenLoadingVideo.value = 0;
    isMergeVideo.value = false;

    const video_des = messagesMap["resultTarget"][0].content;
    const images = messagesMap["resultTarget"][0]["images"];
    const imageVideos = [];

    resetVideoUpload(0);

    try {
        if (images.length === 0) {
            await createImage();
            return;
        }

        let isError = false;

        for (let i = 0; i < images.length; i++) {
            if(images[i].imageRef.s3_url) {
                imageVideos.push(images[i].imageRef.s3_url);
            }
        }

        if (isError) {
            toast.error("Có lỗi xảy ra trong quá trình tạo video");
            return;
        }

        let duration = 5;
        if (imageVideos.length > 2) {
            isMergeVideo.value = true;
            duration = 10;
        }

        const hasEnoughCredit = await checkCreditVideo(duration);
        if (!hasEnoughCredit) {
            return;
        }

        isLoadingPercent.value = true;

        let image2 = "";
        let video2 = "";
        if (imageVideos.length > 1) {
            image2 = imageVideos[1];
        }

        let is_credit = imageVideos.length <= 2 ? 1 : 0;

        const video1 = await createVideoScene(imageVideos[0], image2, video_des, is_credit);
        if (!video1) {
            toast.error("Có lỗi xảy ra trong quá trình tạo video");
            return;
        }

        if (imageVideos.length > 2) {
            image2 = "";
            if (imageVideos.length >= 4) {
                image2 = imageVideos[3];
            }

            video2 = await createVideoScene(imageVideos[2], image2, video_des, is_credit);
            if (!video2) {
                toast.error("Có lỗi xảy ra trong quá trình tạo video");
                return;
            }
        }

        if (video2) {
            const audioUrl = await textToSpeechGoogle(video_des, 10);
            if (!audioUrl) {
                toast.error("Có lỗi khi tạo âm thanh");
                return;
            }

            const videoMerge = await mergeVideo(video1, video2, audioUrl);
            await slowlyIncreasePercent();
            handleSaveGenerationResultVideo(videoMerge, 0);
        } else {
            await slowlyIncreasePercent();
            handleSaveGenerationResultVideo(video1, 0);
        }
    } catch (err) {
        console.error("❌ Lỗi không mong muốn:", err);
        toast.error("Đã có lỗi xảy ra trong quá trình xử lý");
    } finally {
        isLoadingVideo.value = false;
        showVideoGenerate.value = false;
        isConfirm.value = false;
        percenLoadingVideo.value = 0;
        isLoadingPercent.value = false;
    }
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

async function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
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

            await updateProject({
                id: formProject.id,
                title: formProject.title,
                content: formProject.content,
                video_url: response.data.video_path,
            });

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
const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};

const createAIImage = async (prompt, imageDimension) => {
    try {
        let width = 1024;
        let height = 1024;
        let aspect_ratio = "1:1";

        if (imageDimension.includes(":")) {
            aspect_ratio = imageDimension;

            const [w, h] = imageDimension.split(":").map(Number);
            width = 1024;
            height = Math.round((1024 * h) / w);
        } else if (imageDimension.includes("x")) {
            const [w, h] = imageDimension.split("x").map(Number);
            width = w;
            height = h;
            aspect_ratio = `${w}:${h}`;
        }

        const response = await axios.post(route("ai-image.generate"), {
            description: prompt,
            style: "",
            artist: "",
            height,
            width,
            model: "Flux Schnell",
            aspect_ratio: aspect_ratio,
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

const musicUrl = ref(null)
const handleSaveGenerationResultMusic = (videoUrl, index) => {
    try {
        musicUrl.value = videoUrl;
        saveGenerationResult(videoUrl, messagesMap["resultTarget"][index]?.content, "music_video", index);
        openPostDetail(messagesMap["resultTarget"][index]?.content ?? '', index)
    } catch (err) {
        console.error("handleSaveGenerationResultMusic", err);
        return null;
    }
};

const saveGenerationResult = async (dataParam, content, type = "image", index) => {
    const url = dataParam.url || dataParam.generate_file?.s3_url || dataParam;
    try {
        if (type == "video" || type == 'music_video') {
            videoUrl.value = dataParam;
            if (videoUrl.value) {
                handleVideoUrl(videoUrl.value, index);
            }
        }

        if (type == "image") {
            alert(1)
            if (!messagesMap["resultTarget"][index].images) {
                messagesMap["resultTarget"][index].images = [];
            }

            if (!validateLimitImage(index)) {
                return;
            }
            var imageUrls = [];
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
            updateImages()
        }
        if ((type != "music" && type != 'music_video')) {
            targetActiveIndex.value = -1;
            resultTarget.value.scrollIntoView({ behavior: "smooth" });
        }
    } catch (error) {
        console.error("Lỗi khi lưu kết quả:", error);
        toast.error("Không thể lưu kết quả. Vui lòng thử lại sau.");
        throw error;
    }
};

const showSearchModal = ref(false);
const selectedSearchImage = ref(null);

const currentSearchIndex = ref(null);
const currentSearchIndexImage = ref(null);

const openSearchModal = (index, index_image) => {
    currentSearchIndex.value = index;
    currentSearchIndexImage.value = index_image;
    selectedSearchImage.value = null;
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

const searchImageByKeyword = async () => {
    try {
        keyword.value = formProject.title || selectedProject?.value?.title;
        const response = await axios.get(route("ai-business.search-image"), {
            params: {
                keyword: keyword.value,
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
    console.log(selectedSearchImage.value);
    const index = currentSearchIndex.value;
    const index_image = currentSearchIndexImage.value;
    console.log("messagesMap[resultTarget]:", messagesMap["resultTarget"]);
    console.log("index:", index, "=> target:", messagesMap["resultTarget"][index]);

    if ( !selectedSearchImage.value || index === null || index === undefined || index_image === null || index_image === undefined ) { return; }

    const image = {
        imageRef: {
            s3_url: selectedSearchImage.value,
        },
        previewImageUpload: null,
        fileImageUpload: null,
    };

    messagesMap["resultTarget"][index].images[index_image] = image;
    console.log(messagesMap["resultTarget"][index]);
    console.log(messagesMap["resultTarget"][index].images[index_image]);
    selectImage(image);
    showSearchModal.value = false;
    updateImages()
};

const handleImageUploadV2 = async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    var imageUrl = await getS3URL(file);
    const image = {
        imageRef: {
            s3_url: imageUrl,
        },
        previewImageUpload: null,
        fileImageUpload: null,
    };
    messagesMap["resultTarget"][0].images[imageKey.value] = image
    updateImages()
    updateClassImage()
};

const handleImageUpload = (e, index, index_image = null) => {
    const file = e.target.files[0];
    if (!file) return;
    handleFileChange(e, index, index_image);
};

const upscaleImage = async (index, index_image) => {
    let imageUrl = messagesMap["resultTarget"][index].images[index_image].imageRef.s3_url;

        const image = {
            imageRef: { s3_url: imageUrl },
            previewImageUpload: null,
            fileImageUpload: null,
            isLoading: true
        };
    if (!messagesMap["resultTarget"][index].images) {
        messagesMap["resultTarget"][index].images = {};
    }
    messagesMap["resultTarget"][index].images[index_image] = image;

    let fileBlob = await urlToBlob(imageUrl);
    let formData = new FormData();

    formData.append("image", fileBlob);

    try {
        const response = await axios.post(route('ai-business.upscale-image'), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        if (response.data.status === 'success') {
            messagesMap["resultTarget"][index].images[index_image].imageRef.s3_url = response.data.s3_url;
        } else {
            toast.error("Làm nét ảnh không thành công. Vui lòng thử lại.");
        }
        messagesMap["resultTarget"][index].images[index_image].isLoading = false;
    } catch (error) {
        messagesMap["resultTarget"][index].images[index_image].isLoading = false;
        console.error(error);
        toast.error("Làm nét ảnh không thành công. Vui lòng thử lại.");
    }
    updateImages()
}

const updateImages = async () =>  {
    var imageUrls = [];
    for(var idx = 0; idx < messagesMap["resultTarget"][0].images.length; idx++) {
        imageUrls.push(messagesMap["resultTarget"][0].images[idx].imageRef.s3_url)
    }
    await updateProject({
        id: formProject.id,
        image_urls: imageUrls,
    });
}

const regenImageIndex = ref(1);
const regenIndex = ref(0);
const referUrl = ref(null);
const arrayImageIndex = ref([]);
const generateCustomeImage = async (index, index_image) => {
    imageKey.value = index_image
    arrayImageIndex.value[index_image] = index_image;
    isShowCreateImage.value = true
    showOptions.value = false
    messagesMap["resultTarget"][index].images[index_image].isShowOptionImage = false
    // messagesMap["resultTarget"][index].images[index_image].isLoading = true;
    regenImageIndex.value = index_image + 1
    regenIndex.value = index;
    referUrl.value = messagesMap["resultTarget"][index].images[index_image]?.imageRef?.s3_url;
    updateImages()
    return false
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
                project_id: businessProjectId.value || selectedProject.value.id
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
                            if (data.event === "workflow_finished") {
                                // Đảm bảo đã nhận được toàn bộ content
                                await new Promise((resolve) => setTimeout(resolve, 100));
                                // Gọi API tính credit
                                await calculateStreamCredit(fullAnswer, conversationId.value);
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
        removedImage.url = removedImage.imageRef.s3_url;
    } else {
        removedImage.url = removedImage.previewImageUpload;
    }

    pageData.image_select_files = pageData.image_select_files.filter((file) => file.url !== removedImage.url);
    if (removedImage.hasOwnProperty("imageRef")) {
        messagesMap["resultTarget"][index]["images"][index_image].imageRef.s3_url = '';
    }
    if (removedImage.hasOwnProperty("previewImageUpload")) {
        messagesMap["resultTarget"][index]["images"][index_image].previewImageUpload = '';
    }
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
    updateClassImage()
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

const selectVideoItem = (index) => {
    videos.value[index].is_checked = !videos.value[index].is_checked
}

const selectVideo = (item) => {
    let video = {
        url: item.videoRef?.s3_url ? item.videoRef?.s3_url : item.previewVideoUpload,
    };
    pageData.video_select_files = [video];
    pageData.image_select_files = [];
};

const removeVideo = (index) => {
    pageData.video_select_files = [];
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
    let numberImage = MAX_IMAGE_FILES - item?.images?.length;
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
const oldTextToSpeechWithGoogle = ref('');
const oldUrlAudioTextToSpeechWithGoogle = ref('');
const audioTextToSpeechWithGooglePaused = ref(true);
const audioTextToSpeechWithGoogle = ref(new Audio());

const textToSpeechWithGoogle = async (item) => {
    async function playAudio(audio, urlAudio) {
        if (!audio.paused) {
            audioTextToSpeechWithGooglePaused.value = false;
            console.log('Audio đang phát, không thể phát tiếp!');
            return;
        }
        audio.src = urlAudio;

        const waitForAudioEnd = new Promise((resolve) => {
            audio.addEventListener('ended', () => {
            audioTextToSpeechWithGooglePaused.value = true;
            resolve('Audio đã phát xong');
            }, { once: true });
        });

        try {
            audioTextToSpeechWithGooglePaused.value = false;
            await audio.play();
            const result = await waitForAudioEnd;
            console.log(result);
        } catch (error) {
            audioTextToSpeechWithGooglePaused.value = true;
            console.error('Lỗi khi phát audio:', error);
        }
    }


    if (item.content) {
        let urlAudio = ''
        if (oldTextToSpeechWithGoogle.value.trim() != item.content.trim()) {
            const result = await axios.post(route("ai-audio.text-to-speech-google"), {
                text: item.content,
            })
            if (result.data.success) {
                urlAudio = result.data.data;
                oldUrlAudioTextToSpeechWithGoogle.value = urlAudio;
            }
        }else{
            urlAudio = oldUrlAudioTextToSpeechWithGoogle.value;
        }
        oldUrlAudioTextToSpeechWithGoogle.value = urlAudio;
        oldTextToSpeechWithGoogle.value = item.content;
        playAudio(audioTextToSpeechWithGoogle.value, urlAudio);
    }
}

const stopAudio = () => {
    if (!audioTextToSpeechWithGoogle.value.paused) {
        audioTextToSpeechWithGoogle.value.pause();
        audioTextToSpeechWithGoogle.value.currentTime = 0; // Đặt lại thời gian về đầu
        audioTextToSpeechWithGooglePaused.value = true;
        console.log('Audio đã dừng');
    }
};

const calculateStreamCredit = async (fullAnswer, conversationId) => {
    try {
        const response = await fetch(route("ai-business.credit.calculate-stream"), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            body: JSON.stringify({
                content: fullAnswer,
                conversation_id: conversationId,
            }),
        });
        const result = await response.json();
        if (!result.success) {

        }
        return result;
    } catch (error) {
        console.error("Error calculating stream credit:", error);
        throw error;
    }
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

const languages = ref([
    "Tiếng việt",
    "Tiếng Anh",
    "Tiếng Trung",
    "Tiếng Nhật",
    "Tiếng Hàn",
]);

const redirectHistory = () => {
    location.href = route('product-image-history')
}
const confirmImage = () => {
    if(typeImage.value != 1) {
        tempFormProject.value.image_product.url = imageSelect.value;
    } else {
        tempFormProject.value.model_product.url = imageSelect.value;
    }
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

const loadMore = async () => {
    if(nextPage.value) {
        showImageList(0, nextPage.value)
    }
}

const closePopup = () => {
    showImage.value = false
    isShowCreateImage.value = false
}

const closePopupRegenImage = (index, index_image) => {
    isShowCreateImage.value = false
    messagesMap["resultTarget"][index].images[index_image-1].isLoading = false;
    console.log("closePopupRegenImage", index, index_image-1);
    if (referUrl.value) {
        messagesMap["resultTarget"][index].images[index_image-1].imageRef.s3_url = referUrl.value;
        referUrl.value = null;
        updateImages()
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

// RE_GENERATE_IMAGE
const imageDescription = ref("");
const imageS3Url = ref([]);
const errors = ref({});
const validDimensions = computed(() => ["16:9", "9:16"]);
const imageDimension = ref("16:9");
const handleGenImage = async (index_image) => {
    isShowCreateImage.value = false;
    messagesMap["resultTarget"][0].images[arrayImageIndex.value[index_image - 1]].isLoading = true;
    if (tempFormProject.value.image_product.url && tempFormProject?.value?.model_product?.url) {
        const proxyProductUrl = `/proxy-image?url=${encodeURIComponent(tempFormProject.value.image_product.url)}`;
        const response = await axios.get(proxyProductUrl, { responseType: "blob" });
        const productImageBlob = new Blob([response.data], { type: "image/jpeg" });
        const productImage = await resizeAndPadImage(productImageBlob);

        const proxyModelUrl = `/proxy-image?url=${encodeURIComponent(tempFormProject.value.model_product.url)}`;
        const responseModel = await axios.get(proxyModelUrl, { responseType: "blob" });
        const modelImageBlob = new Blob([responseModel.data], { type: "image/jpeg" });
        const modelImage = await resizeAndPadImage(modelImageBlob);

        switch (index_image) {
            case 1:
                imageS3Url.value[index_image] = await generateImageBackgroundWithReplicate(productImage);
                break;
            case 2:
                imageS3Url.value[index_image] = await combineImagesReplicate(
                    tempFormProject.value.model_product.url,
                    tempFormProject.value.image_product.url
                );
                break;
            case 3:
            case 4:
                let model_link = await generateSelfImage();
                imageS3Url.value[index_image] = await combineImagesReplicate(
                    model_link,
                    tempFormProject.value.image_product.url
                );
                break;
            default:
                imageS3Url.value[index_image] = await generateImageBackgroundWithReplicate(productImage);
                break;
        }
    } else if (tempFormProject.value.image_product.url) {
        const proxyProductUrl = `/proxy-image?url=${encodeURIComponent(tempFormProject.value.image_product.url)}`;
        const response = await axios.get(proxyProductUrl, { responseType: "blob" });
        const productImageBlob = new Blob([response.data], { type: "image/jpeg" });
        const productImage = await resizeAndPadImage(productImageBlob);

        const latestModelResponse = await axios.get(route('ai-image.get-latest-my-ai-image'));
        const latestModel = latestModelResponse.data?.data;

        if (!latestModel || !latestModel.id) {
            switch (index_image) {
                case 0:
                case 1:
                case 2:
                case 3:
                    imageS3Url.value[index_image] = await generateImageBackgroundWithReplicate(productImage);
                    break;
                default:
                    imageS3Url.value[index_image] = tempFormProject.value.image_product.url;
                    break;
            }
        } else {
            switch (index_image) {
                case 1:
                    imageS3Url.value[index_image] = await generateImageBackgroundWithReplicate(productImage);
                    break;
                case 2:
                case 3:
                case 4:
                    let model_link = await generateSelfImage();
                    imageS3Url.value[index_image] = await combineImagesReplicate(
                        model_link,
                        tempFormProject.value.image_product.url
                    );
                    break;
                default:
                    imageS3Url.value[index_image] = await generateImageBackgroundWithReplicate(productImage);
                    break;
            }
        }
    }
    else {
        imageS3Url.value[index_image] = await createAIImage(imageDescription.value, imageDimension.value);
    }
    if (!imageS3Url.value[index_image]) {
        keyword.value = formProject.title || selectedProject?.value?.title;
        if (!keyword.value) {
            return;
        }

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

        for (const img of searchImages.value) {
            const imageUrl = img.link;
            const canLoad = await canLoadImage(imageUrl);

            if (canLoad) {
                imageS3Url.value[index_image] = imageUrl;
                break;
            }
        }
    }
    referUrl.value = imageS3Url.value[index_image];
    handleUpdateImage(imageS3Url.value[index_image], index_image - 1);
    updateImages()
}

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

#editor-container .sun-editor-editable p,
#editor-container .sun-editor-editable h1,
#editor-container .sun-editor-editable h2,
#editor-container .sun-editor-editable h3,
#editor-container .sun-editor-editable h4,
#editor-container .sun-editor-editable h5,
#editor-container .sun-editor-editable h6,
#editor-container .sun-editor-editable ul,
#editor-container .sun-editor-editable ol,
#editor-container .sun-editor-editable li,
#editor-container .sun-editor-editable blockquote,
#editor-container .sun-editor-editable pre,
#editor-container .sun-editor-editable code,
#editor-container .sun-editor-editable table,
#editor-container .sun-editor-editable tr,
#editor-container .sun-editor-editable th,
#editor-container .sun-editor-editable td {
    font-size: 19px;
}
@media (max-width: 640px) {
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
        font-size: 14px;
    }
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
.line-h-40 {
    line-height:40px;
}
.option-image {
    background: rgba(0, 0, 0, 0.4);
    margin-top: 298px;
}

.option-image li {
    cursor: pointer;
    line-height: 30px;
    padding-left: 5px;
    margin-top: 15px;
    margin-top: 5px;
}
.option-image li:hover {
    cursor: pointer;
    background:#0000004D;
    border-radius:20px;
    line-height: 30px;
    padding-left: 5px;
    margin-top: 5px;
    padding-right: 5px;
}
.box-image {
    width: 100%;
    height: 245px;
    overflow: hidden;
    position: relative;
}

.blurred-image {
  position: absolute;
  top: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: blur(20px);
  opacity: 0.7;
  z-index: 0;
}

.blurred-image.left {
  left: -30%; /* kéo ảnh ra bên trái */
}

.blurred-image.right {
  right: -30%; /* kéo ảnh ra bên phải */
}

.center-image {
  position: relative;
  height: 100%;
  object-fit: cover;
  z-index: 1;
}
.z-9999 {
    z-index:9999
}

@media only screen and (max-width: 600px) {
    .option-image {
        margin-left: -80px;
        margin-top: 318px;
    }
}

.video-player {
  position: relative;
  width: 100%;
  aspect-ratio: 16 / 9;
  border-radius: 20px;
  background-color: #ddd; /* nền xám hai bên */
  overflow: hidden;
}

.ratio-16-9 {
    aspect-ratio: 16 / 9;
}

.ratio-9-16 {
    aspect-ratio: 9 / 16;
}

.video-inner {
  position: absolute;
  top: 0;
  left: 50%;
  height: 100%;
  transform: translateX(-50%);
  background-color: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
}

.video-inner img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 0;
}

.video-thumbnail {
  width: 100%;
  display: block;
}

.play-button {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.circle-indicator {
  position: absolute;
  top: 15px;
  left: 15px;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
}

.circle-indicator::after {
  content: '';
  width: 20px;
  height: 20px;
  background: orange;
  border-radius: 50%;
}

.controls {
  position: absolute;
  bottom: 10px;
  left: 10px;
  right: 10px;
  color: white;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-family: sans-serif;
  font-size: 14px;
}

.progress-bar {
  flex: 1;
  height: 5px;
  background: white;
  border-radius: 10px;
  margin-left: 10px;
  margin-right: 10px;
}
.overlay-option {
  position: fixed;
  inset: 0;
  z-index: 10;
}
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
}
.full-image {
  max-width: 90%;
  max-height: 90%;
}
.close-btn {
  position: absolute;
  top: 20px;
  right: 25px;
  font-size: 28px;
  color: white;
  background: transparent;
  border: none;
  cursor: pointer;
  z-index: 10000;
}
.close-btn:hover {
  color: red;
}
.h-image {
    height: 400px;
}
.or-text {
    margin-left: 335px;
    margin-top: 10px;
}
@media only screen and (max-width: 1400px) {
    .h-image {
        height: 200px;
    }
}
@media only screen and (max-width: 1290px) {
    .or-text {
        margin-left: 190px;
    }
}

@media only screen and (max-width: 800px) {
    .h-image {
        height: 300px;
    }
    .or-text {
        margin-left: 95px;
        margin-top: 5px;
    }
    .button1 {
       width: 100px;
        margin-left: -35px;
    }
    .button2 {
        width: 135px;
        margin-left: 0px;
    }
}

@media only screen and (max-width: 768px) {
    .h-image {
        height: 180px;
    }
    .max-h-mobile {
        max-height:600px;
    }
    .or-text {
        margin-left: 95px;
        margin-top: 5px;
    }
    .button1 {
       width: 100px;
        margin-left: -35px;
    }
    .button2 {
        width: 135px;
        margin-left: 0px;
    }
}
.icon-hidden-data {
    margin-top: -30px;
    padding-bottom: 20px;
}

.hidden-data-2 {
    margin-top: -40px;
    padding-bottom: 30px;
}
.mg-b-20px {
    margin-bottom: -20px;
}
.border-4 {
    border-width: 4px;
}
.btn-loadmore {
    margin-bottom: 20px;
    margin-top: 20px;
}
.placeholder-italic::placeholder {
    font-style: italic;
}
</style>
