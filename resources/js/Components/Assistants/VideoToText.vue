<template>

    <Head title="Video To Text" />
    <div class="w-full h-full py-6 bg-gray-50 rounded-[30px]">
        <div class="max-w-8xl mx-auto px-4 h-full">
            <form @submit.prevent="submit" class="h-full flex flex-col space-y-6">
                <div class="relative bg-white shadow-md rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-lg">
                    <div class="p-6">
                        <h2 class="text-lg font-bold mb-4 flex items-center youtube_url">
                           <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_28_24780)">
                                <path d="M14.2691 1.76307C15.0516 1.77235 15.799 2.08972 16.3491 2.6464C16.8991 3.20307 17.2076 3.95417 17.2075 4.73677V14.2691C17.2061 15.0507 16.8969 15.8002 16.347 16.3557C15.7971 16.9111 15.0506 17.2276 14.2691 17.2369H4.73677C3.94912 17.2369 3.19363 16.9244 2.63612 16.368C2.07861 15.8116 1.76462 15.0567 1.76307 14.2691V4.73677C1.76462 3.94858 2.07842 3.19308 2.63576 2.63574C3.1931 2.0784 3.94858 1.76462 4.73677 1.76307H14.2691ZM14.2691 0H4.73677C3.4805 0 2.27569 0.499051 1.38737 1.38737C0.499051 2.27569 0 3.4805 0 4.73677V14.2691C0.00155739 15.5243 0.501282 16.7276 1.38943 17.6147C2.27758 18.5017 3.48152 19 4.73677 19H14.2691C15.5187 18.9922 16.7145 18.4904 17.5954 17.604C18.4762 16.7176 18.9706 15.5187 18.9706 14.2691V4.73677C18.9722 3.48612 18.4785 2.2857 17.5974 1.39806C16.7164 0.510413 15.5197 0.00776706 14.2691 0Z" fill="#207A91"/>
                                <path d="M10.2375 7.93376C10.0142 8.061 9.83093 8.2483 9.70863 8.47442L9.74977 8.55082C9.88912 8.79393 9.97343 9.06463 9.99679 9.34387C10.0201 9.62311 9.98196 9.90409 9.88493 10.167C9.75068 10.5286 9.51374 10.8431 9.20321 11.072L9.02102 11.1837L8.83884 11.2777C8.40117 11.4629 7.91086 11.4814 7.46046 11.3298C7.01006 11.1783 6.63072 10.867 6.39405 10.4549L5.17167 8.31573C4.92069 7.89048 4.84249 7.38513 4.95318 6.9039C5.06387 6.42268 5.35501 6.00228 5.76657 5.72942C6.17812 5.45656 6.6787 5.35206 7.16505 5.43744C7.65141 5.52282 8.08644 5.79157 8.38045 6.1883C8.56315 6.02805 8.75983 5.88448 8.96813 5.7593C9.17916 5.6379 9.40159 5.53751 9.63223 5.45957C9.14958 4.71333 8.39325 4.18638 7.52601 3.99214C6.65877 3.79791 5.74994 3.95192 4.9951 4.421C4.24025 4.89008 3.69972 5.63674 3.48984 6.50033C3.27996 7.36392 3.41751 8.27541 3.87288 9.03862L5.08352 11.1778C5.41837 11.7695 5.92393 12.2465 6.53414 12.5464C7.14435 12.8462 7.83084 12.955 8.50387 12.8586C8.85845 12.8153 9.20261 12.7099 9.52056 12.5471C9.58605 12.5217 9.64902 12.4902 9.70863 12.4531C9.77031 12.4207 9.82926 12.3833 9.88493 12.3414C10.1861 12.1489 10.4541 11.9087 10.6783 11.6303C11.1055 11.1007 11.3632 10.4546 11.4177 9.77628C11.4722 9.098 11.3211 8.41902 10.9839 7.82794L10.9428 7.75158C10.6958 7.74951 10.4526 7.81233 10.2375 7.93376Z" fill="#207A91"/>
                                <path d="M9.92608 5.92394C9.87814 5.93898 9.83106 5.95665 9.78504 5.97684C9.70588 5.99846 9.62908 6.02797 9.55584 6.06498C9.45504 6.10803 9.35691 6.15709 9.26199 6.2119L8.99166 6.38235L8.74482 6.57042L8.69781 6.61741C8.1129 7.13095 7.72284 7.83037 7.59331 8.59788C7.46379 9.36539 7.60273 10.1541 7.98672 10.8312L8.03372 10.9076C8.23538 10.9219 8.43742 10.8856 8.62141 10.8018C8.71751 10.7562 8.80987 10.7032 8.89763 10.6431C9.06034 10.5235 9.19642 10.3714 9.29725 10.1964L9.25024 10.1083C9.11311 9.86535 9.0305 9.59547 9.00818 9.31739C8.98585 9.03932 9.02433 8.75972 9.12095 8.49802C9.1413 8.43147 9.16685 8.36665 9.19735 8.30411L9.25024 8.22182C9.37302 7.98908 9.54802 7.78793 9.76153 7.63413C9.8283 7.5772 9.90123 7.52791 9.97897 7.48721C10.0544 7.44411 10.133 7.40678 10.214 7.37555C10.2944 7.33861 10.3794 7.3129 10.4668 7.29915C10.6372 7.25349 10.8135 7.23366 10.9898 7.24035H11.0838H11.2954C11.5689 7.29276 11.8278 7.40401 12.0541 7.56637C12.2804 7.72873 12.4687 7.93834 12.6059 8.18068L13.8225 10.3257C14.0677 10.7515 14.141 11.2549 14.0275 11.733C13.914 12.2111 13.6222 12.6277 13.2117 12.8979C12.8013 13.168 12.3032 13.2712 11.8192 13.1863C11.3352 13.1015 10.9019 12.8351 10.6078 12.4414C10.4818 12.5567 10.3462 12.6609 10.2023 12.7529C10.1349 12.8009 10.0642 12.8441 9.99074 12.8822C9.91947 12.9271 9.8448 12.9664 9.76741 12.9997C9.62918 13.0695 9.48574 13.1284 9.33839 13.176C9.83455 13.8892 10.5855 14.3847 11.4363 14.5604C12.2872 14.7361 13.1729 14.5784 13.911 14.1201C14.649 13.6617 15.183 12.9376 15.4027 12.0971C15.6224 11.2566 15.5112 10.3638 15.0919 9.60288L13.8812 7.46368C13.494 6.79685 12.8917 6.28134 12.1731 6.00159C11.4546 5.72185 10.6623 5.69446 9.92608 5.92394Z" fill="#207A91"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_28_24780">
                                <rect width="19" height="19" fill="white"/>
                                </clipPath>
                                </defs>
                            </svg>
                            <span class="ml-2">Nhập URL Link Youtube</span>
                        </h2>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <div class="relative flex-grow w-full">
                                <input
                                    v-model="url"
                                    type="text"
                                    placeholder="Dán liên kết Youtube vào đây..."
                                    class="w-full border border-gray-200 bg-gray-50 px-4 py-3 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                                />
                            </div>
                            <button
                                @click="sendChat()"
                                :disabled="isTranslating || isLoading"
                                class="flex items-center justify-center px-6 py-3 bg-ai3goc-bgclr text-white rounded-xl font-medium min-w-[140px] shadow-sm  focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-300 transform hover:-translate-y-1 disabled:opacity-50 disabled:pointer-events-none"
                            >
                                <span v-if="!isLoading">Gửi yêu cầu</span>
                                <span v-else class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex-grow flex flex-col bg-white shadow-md rounded-2xl transition-all duration-300 hover:shadow-lg">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-lg font-bold text-gray-700 text-center flex items-center">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_28_24767)">
                                <path d="M14.2456 1.76307C15.0332 1.76463 15.7881 2.07861 16.3445 2.63612C16.9009 3.19363 17.2134 3.94912 17.2134 4.73678V14.2926C17.2042 15.0741 16.8876 15.8206 16.3322 16.3705C15.7768 16.9204 15.0272 17.2296 14.2456 17.2311H4.71328C3.92508 17.2295 3.16966 16.9157 2.61232 16.3584C2.05498 15.801 1.74109 15.0456 1.73954 14.2574V4.73092C1.74109 3.94272 2.05498 3.18724 2.61232 2.6299C3.16966 2.07256 3.92508 1.75876 4.71328 1.75721H14.2456M14.2456 -0.00585938H4.71328C3.45701 -0.00585938 2.25223 0.493192 1.36391 1.38151C0.475594 2.26983 -0.0235291 3.47464 -0.0235291 4.73092V14.2926C-0.0235291 15.5489 0.475594 16.7537 1.36391 17.642C2.25223 18.5303 3.45701 19.0294 4.71328 19.0294H14.2456C14.8674 19.0294 15.4831 18.9068 16.0574 18.6687C16.6318 18.4306 17.1536 18.0816 17.5929 17.6417C18.0323 17.2017 18.3807 16.6795 18.6181 16.1049C18.8555 15.5302 18.9773 14.9144 18.9765 14.2926V4.76618C18.9773 4.14441 18.8555 3.52859 18.6181 2.95393C18.3807 2.37927 18.0323 1.85706 17.5929 1.41713C17.1536 0.977201 16.6318 0.628188 16.0574 0.390071C15.4831 0.151953 14.8674 0.0294001 14.2456 0.0294006V-0.00585938Z" fill="#207A91"/>
                                <path d="M10.8664 7.56934H4.55458C3.85675 7.56934 3.29105 8.13504 3.29105 8.83287V8.83874C3.29105 9.53657 3.85675 10.1023 4.55458 10.1023H10.8664C11.5642 10.1023 12.1299 9.53657 12.1299 8.83874V8.83287C12.1299 8.13504 11.5642 7.56934 10.8664 7.56934Z" fill="#207A91"/>
                                <path d="M14.4454 10.0964C15.1432 10.0964 15.7089 9.5307 15.7089 8.83287C15.7089 8.13504 15.1432 7.56934 14.4454 7.56934C13.7476 7.56934 13.1819 8.13504 13.1819 8.83287C13.1819 9.5307 13.7476 10.0964 14.4454 10.0964Z" fill="#207A91"/>
                                <path d="M8.13356 15.1624L14.4453 15.1624C15.1432 15.1624 15.709 14.5967 15.709 13.8988V13.8929C15.709 13.1951 15.1432 12.6294 14.4453 12.6294H8.13356C7.43573 12.6294 6.87008 13.1951 6.87008 13.8929V13.8988C6.87008 14.5967 7.43573 15.1624 8.13356 15.1624Z" fill="#207A91"/>
                                <path d="M4.55458 15.1623C5.25241 15.1623 5.81811 14.5966 5.81811 13.8988C5.81811 13.201 5.25241 12.6353 4.55458 12.6353C3.85675 12.6353 3.29105 13.201 3.29105 13.8988C3.29105 14.5966 3.85675 15.1623 4.55458 15.1623Z" fill="#207A91"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_28_24767">
                                <rect width="19" height="19" fill="white"/>
                                </clipPath>
                                </defs>
                            </svg>
                            <span class="ml-2"> Nội dung đã chuyển đổi</span>
                        </h2>
                    </div>

                    <div class="flex-grow p-6 flex flex-col">
                    <div id="editor-container" class="relative">
                    <svg @click="handleCopy" class="icon-copy" width="30px" height="30px" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="32" height="32" rx="7" fill="#B4B4B4"/>
                    <path d="M24 13H15C13.8954 13 13 13.8954 13 15V24C13 25.1046 13.8954 26 15 26H24C25.1046 26 26 25.1046 26 24V15C26 13.8954 25.1046 13 24 13Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 19H8C7.46957 19 6.96086 18.7893 6.58579 18.4142C6.21071 18.0391 6 17.5304 6 17V8C6 7.46957 6.21071 6.96086 6.58579 6.58579C6.96086 6.21071 7.46957 6 8 6H17C17.5304 6 18.0391 6.21071 18.4142 6.58579C18.7893 6.96086 19 7.46957 19 8V9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

<!--                        <svg @click="handleCopy" class="icon-copy" width="24px" height="24px" id="Layer_1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 105.02 122.88" xml:space="preserve"><g><path d="M5.32,14.64h20.51V5.32v0h0.01c0-1.47,0.6-2.8,1.56-3.76c0.95-0.95,2.28-1.55,3.75-1.55V0h0h39.61h1.22l0.88,0.88 l31.29,31.41l0.87,2.09v69.2v0h-0.01c0,1.47-0.59,2.8-1.55,3.76h-0.01c-0.95,0.96-2.28,1.55-3.75,1.55v0.01h0H79.19v8.65v0h-0.01 c0,1.47-0.59,2.8-1.55,3.76h-0.01c-0.96,0.95-2.28,1.55-3.75,1.55v0.01h0H5.32h0v-0.01c-1.47,0-2.8-0.6-3.76-1.56 c-0.95-0.96-1.55-2.28-1.55-3.75H0v0V19.97v0h0.01c0-1.47,0.6-2.8,1.56-3.76c0.95-0.95,2.28-1.55,3.75-1.55L5.32,14.64L5.32,14.64 L5.32,14.64z M31.76,14.64h13.17h1.22l0.88,0.88l31.29,31.41l0.87,2.09v53.95h19.89V36.24H74.73h0v0c-1.78,0-3.39-0.74-4.56-1.94 c-1.17-1.19-1.9-2.84-1.9-4.65h0v0V5.94H31.76V14.64L31.76,14.64z M68.39,2.97h2.37l31.29,31.41v1.74H74.73 c-3.49,0-6.35-2.92-6.35-6.48V2.97L68.39,2.97z M73.26,50.88H48.91h0v0c-1.78,0-3.39-0.74-4.56-1.94c-1.17-1.19-1.9-2.84-1.9-4.65 h0v0V20.58H25.83H5.94v96.36h67.32v-8.04v-2.97V50.88L73.26,50.88z"/></g></svg>-->
                     <textarea
                        ref="editor"
                        id="video_to_text_content_editor"
                        class="w-full flex-grow px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none min-h-[300px]"
                    ></textarea>
                    </div>
                        <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 md:space-x-2 mt-6">
                            <button
                                :disabled="isTranslating || isLoading"
                                id="viewButton"
                                class="flex items-center w-fit gap-1 px-4 md:px-4 justify-center bg-ai3goc-primary py-1 lg:py-3 rounded-2xl text-xs lg:text-[15px] font-bold text-white cursor-pointer">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 3H21V9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9 21H3V15" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 3L14 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M3 21L10 14" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="ml-2">Toàn màn hình</span>
                            </button>
                            <button
                                @click="getMp3Url"
                                :disabled="isTranslating || isLoading || isLoadingAudio"
                                class="flex items-center w-fit gap-1 px-4 md:px-4 justify-center bg-ai3goc-primary py-1 lg:py-3 rounded-2xl text-xs lg:text-[15px] font-bold text-white cursor-pointer"
                            >
                              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7 10L12 15L17 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 15V3" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Tải xuống file mp3</span>
                            </button>
                            <div class="flex items-center w-1/5">
                            </div>
                            <div class="flex items-center space-x-2">
                                <label for="language-select" class="text-sm font-medium text-gray-700">
                                    Dịch sang:
                                </label>
                                <select
                                    id="language-select"
                                    v-model="selectedLanguage"
                                    @change="handleLanguageChange"
                                    :disabled="isTranslating || isLoading"
                                    class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <option value=""></option>
                                    <option value="vi">Tiếng Việt</option>
                                    <option value="en">Tiếng Anh</option>
                                    <option value="zh">Tiếng Trung</option>
                                    <option value="ja">Tiếng Nhật</option>
                                    <option value="ko">Tiếng Hàn</option>
                                </select>

                                <!-- Hiển thị loading spinner khi đang dịch -->
                                <div v-if="isTranslating" class="flex items-center text-indigo-600">
                                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-sm">Đang dịch...</span>
                                </div>
                            </div>

<!--                            <button-->
<!--                                @click="translateContentStream('en')"-->
<!--                                :disabled="isTranslating || isLoading"-->
<!--                                class="px-5 py-2.5 bg-blue-500 text-white rounded-xl text-sm font-medium transition-all duration-300 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 transform hover:-translate-y-1 shadow-sm disabled:opacity-50 disabled:pointer-events-none flex items-center"-->
<!--                            >-->
<!--                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">-->
<!--                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />-->
<!--                                </svg>-->
<!--                                Dịch sang tiếng Anh-->
<!--                            </button>-->
<!--                            <button-->
<!--                                @click="translateContentStream('zh')"-->
<!--                                :disabled="isTranslating || isLoading"-->
<!--                                class="px-5 py-2.5 bg-red-500 text-white rounded-xl text-sm font-medium transition-all duration-300 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 transform hover:-translate-y-1 shadow-sm disabled:opacity-50 disabled:pointer-events-none flex items-center"-->
<!--                            >-->
<!--                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">-->
<!--                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />-->
<!--                                </svg>-->
<!--                                Dịch sang tiếng Trung-->
<!--                            </button>-->
                            <button
                                @click="exportToDoc"
                                :disabled="isTranslating || isLoading"
                                class="flex items-center w-fit gap-2 px-4 md:px-8 justify-center bg-ai3goc-bgclr py-1 lg:py-3 rounded-2xl text-xs lg:text-[15px] font-bold text-white cursor-pointer"
                            >
                              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7 10L12 15L17 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 15V3" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Tải xuống DOCX</span>
                            </button>
                            <button
                                @click="viewHistory"
                                :disabled="isTranslating || isLoading"
                                class="flex items-center w-fit gap-2 px-4 h-full md:px-8 justify-center bg-ai3goc-bgclr py-1 lg:py-4 rounded-2xl text-xs lg:text-[15px] font-bold text-white cursor-pointer"
                            >
                                Lịch sử
                            </button>
                        </div>
                    </div>
<!--                    <div class="border-b border-gray-100 px-6 py-4">-->
<!--                    <h2 class="mb-2 text-lg font-bold text-gray-700 text-center flex items-center">-->
<!--                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--                        <g clip-path="url(#clip0_104_658)">-->
<!--                        <path d="M14.2456 1.76307C15.0332 1.76463 15.7881 2.07861 16.3445 2.63612C16.9009 3.19363 17.2134 3.94912 17.2134 4.73678V14.2926C17.2042 15.0741 16.8876 15.8206 16.3322 16.3705C15.7768 16.9204 15.0272 17.2296 14.2456 17.2311H4.71328C3.92508 17.2295 3.16966 16.9157 2.61232 16.3584C2.05498 15.801 1.74109 15.0456 1.73954 14.2574V4.73092C1.74109 3.94272 2.05498 3.18724 2.61232 2.6299C3.16966 2.07256 3.92508 1.75876 4.71328 1.75721H14.2456M14.2456 -0.00585938H4.71328C3.45701 -0.00585938 2.25223 0.493192 1.36391 1.38151C0.475594 2.26983 -0.0235291 3.47464 -0.0235291 4.73092V14.2926C-0.0235291 15.5489 0.475594 16.7537 1.36391 17.642C2.25223 18.5303 3.45701 19.0294 4.71328 19.0294H14.2456C14.8674 19.0294 15.4831 18.9068 16.0574 18.6687C16.6318 18.4306 17.1536 18.0816 17.5929 17.6417C18.0323 17.2017 18.3807 16.6795 18.6181 16.1049C18.8555 15.5302 18.9773 14.9144 18.9765 14.2926V4.76618C18.9773 4.14441 18.8555 3.52859 18.6181 2.95393C18.3807 2.37927 18.0323 1.85706 17.5929 1.41713C17.1536 0.977201 16.6318 0.628188 16.0574 0.390071C15.4831 0.151953 14.8674 0.0294001 14.2456 0.0294006V-0.00585938Z" fill="#207A91"/>-->
<!--                        <path d="M9.87695 4.16404C10.1021 3.98394 10.411 3.94931 10.6709 4.0742C10.9307 4.19912 11.0957 4.46174 11.0957 4.74998V15.0107C11.0955 15.2988 10.9306 15.5617 10.6709 15.6865C10.4111 15.8114 10.1021 15.7758 9.87695 15.5957L6.41895 12.8291H3.75C3.33589 12.8291 3.00017 12.4931 3 12.0791V7.68162C3 7.26741 3.33579 6.93162 3.75 6.93162H6.41797L9.87695 4.16404ZM7.15039 8.26756C7.01744 8.37392 6.8519 8.43159 6.68164 8.43162H4.5V11.3291H6.68164L6.80762 11.3398C6.93224 11.3611 7.0506 11.4133 7.15039 11.4931L9.5957 13.4492V6.31053L7.15039 8.26756ZM13.9961 9.87596C13.996 9.19974 13.7608 8.54778 13.3359 8.02928L13.1426 7.81639L13.0908 7.75877C12.8509 7.46416 12.8681 7.0293 13.1426 6.75486C13.4172 6.48037 13.852 6.46381 14.1465 6.70408L14.2031 6.75584L14.3545 6.91404C15.0874 7.72365 15.496 8.77874 15.4961 9.87596C15.4961 11.0465 15.0307 12.1692 14.2031 12.997C13.9103 13.2899 13.4355 13.2908 13.1426 12.998C12.8496 12.7052 12.8497 12.2294 13.1426 11.9365C13.6889 11.39 13.9961 10.6487 13.9961 9.87596Z" fill="#207A91"/>-->
<!--                        </g>-->
<!--                        <defs>-->
<!--                        <clipPath id="clip0_104_658">-->
<!--                        <rect width="19" height="19" fill="white"/>-->
<!--                        </clipPath>-->
<!--                        </defs>-->
<!--                        </svg>-->
<!--                        <span class="ml-2"> Kết quả chuyển đổi thành âm thanh</span>-->
<!--                    </h2>-->
<!--                    <audio controls v-if="mp3Url" :key="mp3Url">-->
<!--                        <source :src="mp3Url" type="audio/mpeg">-->
<!--                        Your browser does not support the audio tag.-->
<!--                    </audio>-->
<!--                    <div v-if="isLoadingAudio" role="status" class="flex items-center justify-center w-full">-->
<!--                        <svg aria-hidden="true" class="h-10 mx-auto text-gray-200 animate-spin dark:text-gray-600 fill-orion-sec"-->
<!--                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--                            <path-->
<!--                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"-->
<!--                                fill="currentColor" />-->
<!--                            <path-->
<!--                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"-->
<!--                                fill="currentFill" />-->
<!--                        </svg>-->
<!--                    </div>-->
<!--                </div>-->
                </div>
            </form>
        </div>
        <div id="fullscreenContent" class="fixed inset-0 bg-white bg-opacity-100 flex items-center justify-center hidden">
        <div id="closeButton">
            <svg @click.stop="handleCopy" class="cursor-pointer absolute top-2 right-11" width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect y="-0.000488281" width="30.5661" height="30.5661" rx="15.2831" fill="#207A91"/>
            <g clip-path="url(#clip0_33_1908)">
            <path d="M21.2832 13.2454H14.1511C13.2758 13.2454 12.5662 13.9549 12.5662 14.8303V21.9624C12.5662 22.8377 13.2758 23.5473 14.1511 23.5473H21.2832C22.1585 23.5473 22.8681 22.8377 22.8681 21.9624V14.8303C22.8681 13.9549 22.1585 13.2454 21.2832 13.2454Z" stroke="white" stroke-width="2.32075" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9.39616 18.0002H8.60371C8.18336 18.0002 7.78024 17.8332 7.48301 17.5359C7.18578 17.2387 7.0188 16.8356 7.0188 16.4152V9.28315C7.0188 8.86281 7.18578 8.45968 7.48301 8.16245C7.78024 7.86522 8.18336 7.69824 8.60371 7.69824H15.7358C16.1561 7.69824 16.5593 7.86522 16.8565 8.16245C17.1537 8.45968 17.3207 8.86281 17.3207 9.28315V10.0756" stroke="white" stroke-width="2.32075" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
            <defs>
            <clipPath id="clip0_33_1908">
            <rect width="19.0189" height="19.0189" fill="white" transform="translate(5.43408 6.11328)"/>
            </clipPath>
            </defs>
            </svg>

            <svg class="cursor-pointer absolute top-2 right-2"  width="28" height="28" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="0.188721" y="0.0942383" width="34.8113" height="34.8113" rx="17.4057" fill="#1E405A"/>
            <path d="M25.0723 10.1509L10.2452 24.978" stroke="white" stroke-width="2.32075" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10.2455 10.1509L25.0726 24.978" stroke="white" stroke-width="2.32075" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <hr class="absolute top-[42px] border-gray-300 w-full">
        </div>



<!--            <button id="closeButton" class="absolute top-4 right-4 bg-red-600 text-white py-2 px-4 rounded">Đóng</button>-->
            <div id="displayText" class="p-4 overflow-auto"></div>
        </div>
        <VideoToTextHistory ref="videoToTextHistoryRef" v-if="isShowHistory" @view-detail="viewDetail" />
    </div>
</template>

<script setup>
    import Modal from '@/Components/Modal.vue';
    import PostToFacebook from '@/Components/ShareAiText/PostToFacebook.vue';
    import { Head, usePage } from "@inertiajs/vue3";
    import { reactive } from "@vue/reactivity";
    import axios from "axios";
    import suneditor from 'suneditor';
    import 'suneditor/dist/css/suneditor.min.css'; // Import CSS của Sun Editor
    import plugins from 'suneditor/src/plugins';
    import { computed, defineEmits, onBeforeMount, onBeforeUnmount, onMounted, onUnmounted, ref } from "vue";
    import { toast } from "vue3-toastify";
    import {marked} from "marked";
    import VideoToTextHistory from "./VideoToTextHistory.vue";
    const props = defineProps({
    });

    const { props: pageProps } = usePage();
    const auth = computed(() => pageProps.value.auth);

    const isShowHistory = ref(false);
    const videoToTextHistoryRef = ref(null);
    const activeForm = ref(true);
    const toggleForm = () => {
        activeForm.value = !activeForm.value;
    };
    const url = ref("");
    const mp3Url = ref('');
    const isLoadingAudio = ref(false);
    const editor = ref(null)
    let sunEditorInstance = ref(null);
    let videoTitle = ref('noi dung');

    const isLoading = ref(false);
    const isTranslating = ref(false);

    const user = pageProps.auth.user;
    const selectedLanguage = ref("");

    const languageNames = {
        'vi': 'tiếng Việt',
        'en': 'tiếng Anh',
        'zh': 'tiếng Trung',
        'ja': 'tiếng Nhật',
        'ko': 'tiếng Hàn'
    };

    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Cập nhật nội dung editor
    const updateEditor = debounce((newContent) => {
        sunEditorInstance.value.setContents(marked(newContent));
    }, 300);

    const replaceHtmlTags = (inputString) => {
        const tagReplacements = {
            "</strong> <strong>": "</strong>&nbsp;<strong>",
            "</strong> <em>": "</strong>&nbsp;<em>",
            "</em> <strong>": "</em>&nbsp;<strong>",
            "</em> <em>": "</em>&nbsp;<em>",
            "</strong><strong>": "</strong>&nbsp;<strong>",
            "</strong><em>": "</strong>&nbsp;<em>",
            "</em><strong>": "</em>&nbsp;<strong>",
            "</em><em>": "</em>&nbsp;<em>",
        };

        for (let oldTag in tagReplacements) {
            if (tagReplacements.hasOwnProperty(oldTag)) {
                let newTag = tagReplacements[oldTag];
                let regex = new RegExp(oldTag, 'g');
                inputString = inputString.replace(regex, newTag);
            }
        }

        return inputString;
    }
    const setVideoTitle = (htmlData) => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(htmlData, 'text/html');
        const item = doc.querySelectorAll('.se-video-title')[0];
        if (item) {
            videoTitle.value = item.innerHTML;
        }
    }
    const updateData = debounce(() => {
        const data = replaceHtmlTags(sunEditorInstance.value.getContents());
        sunEditorInstance.value.setContents(data);
        setVideoTitle(sunEditorInstance.value.getContents());
        saveHistory();
    }, 300)

    const openTab = () => {
        localStorage.setItem(
            'dataVideoToText',
             JSON.stringify({
                 url: url.value,
                 selectedLanguage: selectedLanguage.value,
                 videoTitle: videoTitle.value,
                 content: sunEditorInstance.value.getContents()
             })
        );
        window.open('/', '_blank');
        selectedLanguage.value = '';
    }
    const handleLanguageChange = async () => {
        if (!selectedLanguage.value) {
            // toast.info('Vui lòng chọn ngôn ngữ muốn dịch');
            return;
        }
        if (!sunEditorInstance.value) {
            toast.error('Editor chưa được khởi tạo');
            return;
        }
        // Lấy nội dung hiện tại từ editor
        const content = sunEditorInstance.value.getContents();

        // Kiểm tra nội dung văn bản thực sự
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = content;
        const textContent = tempDiv.textContent || tempDiv.innerText || '';
        const hasRealContent = textContent.trim().length > 0;

        if (!hasRealContent) {
            toast.warning('Không có nội dung để dịch');
            return;
        }
        openTab();
    };
    const getMp3Url = async () => {
        if (!url.value.trim()) {
            toast.error("Vui lòng nhập URL trước khi gửi yêu cầu.");
            return;
        }
        isLoadingAudio.value = true;
        toast.info('Đang tải xuống file mp3, vui lòng đợi!')
        const response = await axios.post(route("ai-video-to-text.convert-mp3"), {
           url: url.value.trim()
        });
        const data = response.data;
        if (!data.url) {
            toast.error('Tải xuống file mp3 thất bại')
            isLoadingAudio.value = false;
            return;
        }
        fetch(route("ai-video-to-text.download-mp3", { 'downloadUrl': data.url, 'title': data.title }))
            .then(response => response.blob())
            .then(blob => {
                const blobUrl = URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = blobUrl;
                a.download = data.title + '.mp3';
                document.body.appendChild(a);
                a.click();
                a.remove();
            })
        .catch(() => {
            toast.error('Tải xuống file mp3 thất bại')
        })
        .finally(() => {
            isLoadingAudio.value = false;
        });
    }
    const sendChat = async () => {

        if(!user) {
            window.location = route('login');
            return true;
        }
        if (!url.value.trim()) {
            toast.error("Vui lòng nhập URL trước khi gửi yêu cầu.");
            return;
        }
        if(!isYouTubeURL(url.value.trim())) {
            toast.error("Vui lòng nhập đúng URL youtute");
            return;
        }
        if(!isValidYoutubeURL(url.value.trim())) {
            toast.error("Không thể xử lý link YouTube Live, play list,... Bạn vui lòng kiểm tra lại hoặc chọn một link khác.\n");
            return;
        }
        if (isLoading.value) return;
        isLoading.value = true;

        try {
            selectedLanguage.value = '';
            sunEditorInstance.value.setContents('');
            toast.info("Đang xử lý URL...");
            // Tạo FormData object
            const formData = new FormData();
            formData.append('query', url.value.trim());
            formData.append('conversation_id', '');
            formData.append('inputs', JSON.stringify([]));
            formData.append('files', []);
            // Gửi request đến API
            const response = await fetch(route("ai-video-to-text.sendChatStreaming"), {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'text/event-stream'
                },
                body: formData
            });

            if (!response.ok) {
                if (response.status === 401) {
                    // Redirect to login page if unauthorized
                    const currentUrl = encodeURIComponent(window.location.href);
                    window.location.href = `/login?return_url=${currentUrl}`;
                    return;
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';
            let contentHTML = "";
            toast.info("Đang phân tích nội dung từ URL...");
            while (true) {
                const { value, done } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, { stream: true });

                const lines = buffer.split('\n');
                buffer = lines.pop() || '';

                for (const line of lines) {
                    if (line.trim().startsWith('data: ')) {
                        try {
                            const jsonStr = line.trim().slice(5);
                            const data = JSON.parse(jsonStr);

                            if (data.event === 'message' && data.answer) {
                                contentHTML += data.answer;
                                updateEditor(contentHTML);
                            }

                            if (data.event === 'workflow_started' && data.conversation_id) {
                                console.log('Conversation ID:', data.conversation_id);
                            }

                            if (data.event === 'error') {
                                toast.error(data.message || "Có lỗi xảy ra khi xử lý URL");
                                sunEditorInstance.value.setContents('<p style="color:red">Có lỗi xảy ra: ' + (data.message || "Không thể xử lý URL này") + '</p>');
                            }

                            if (data.event === 'workflow_finished') {
                                toast.success("Đã xử lý URL thành công!");
                            }
                        } catch (e) {
                            console.error("Parse error:", e);
                            continue;
                        }
                    }
                }
            }
            updateData();
        } catch (error) {
            console.error("Đã xảy ra lỗi khi xử lý URL:", error);
            sunEditorInstance.value.setContents('<p style="color:red">Đã xảy ra lỗi khi xử lý URL: ' + error.message + '</p>');
            toast.error("Đã xảy ra lỗi khi xử lý URL");
        } finally {
            isLoading.value = false;
        }
    };
    const translateContent = async (language) => {
        if(!user) {
            window.location = route('login');
            return true;
        }
        try {
            if (!sunEditorInstance.value) {
                toast.error('Editor chưa được khởi tạo');
                return;
            }

            const content = sunEditorInstance.value.getContents();

            if (!content) {
                toast.warning('Không có nội dung để dịch');
                return;
            }

            isTranslating.value = true;
            toast.info("Đang xử lý Dịch...");
            // Gửi yêu cầu dịch
            const response = await axios.post('/video-to-text/translate-content', {
                content: content,
                language: language
            });

            if (response.data) {
                sunEditorInstance.value.setContents(marked(response.data));
                const languageNames = {
                    'en': 'tiếng Anh',
                    'vi': 'tiếng Việt',
                    'zh': 'tiếng Trung'
                };
                toast.success(`Đã dịch sang ${languageNames[language] || language}`);
            }
        } catch (error) {
            console.error('Error translating content:', error);
            toast.error('Có lỗi xảy ra khi dịch nội dung');
        } finally {
            isTranslating.value = false;
        }
    };

    const translateContentStream = async(dataVideoToText) =>{
        if(!user) {
            window.location = route('login');
            return true;
        }
        try {
            isTranslating.value = true;
            const language = dataVideoToText.selectedLanguage;
            // Hiển thị thông báo đang dịch
            toast.info('Đang dịch nội dung...');

            // Tạo đối tượng để theo dõi dữ liệu stream
            const response = await fetch('/video-to-text/translate-content-stream', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    content: dataVideoToText.content,
                    language: language
                })
            });

            // Tạo biến để lưu kết quả tích lũy
            let translatedContent = '';

            // Đọc stream
            const reader = response.body.getReader();
            const decoder = new TextDecoder();

            // Xóa nội dung hiện tại của editor
            sunEditorInstance.value.setContents('');

            // Đọc dữ liệu từng chunk
            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                // Giải mã chunk
                const chunk = decoder.decode(value);
                translatedContent += chunk;

                // Cập nhật nội dung tức thì vào editor
                sunEditorInstance.value.setContents(marked(translatedContent));
            }

            // translate video title
            const dataTranslate = await axios.post(route('ai-video-to-text.translate-content'), {
                content: dataVideoToText.videoTitle,
                language: language
            });
            // set video title
            if (dataTranslate.data) {
                videoTitle.value  = dataTranslate.data;
            }
            await saveHistory();
            toast.success(`Đã dịch sang ${languageNames[language] || language}`);

        } catch (error) {
            console.error('Error translating content:', error);
            toast.error('Có lỗi xảy ra khi dịch nội dung');
        } finally {
           isTranslating.value = false;
        }
    }

    const exportToDoc = async () => {
        if (!user) {
            window.location = route('login');
            return true;
        }
        if (!sunEditorInstance.value) {
            toast.error('Editor chưa được khởi tạo');
            return;
        }
        const content = sunEditorInstance.value.getContents();
        const textContent = sunEditorInstance.value.getText() || '';
        const hasRealContent = textContent.trim().length > 0;

        if (!hasRealContent) {
            toast.warning('Không có nội dung để xuất ra file DOCX');
            return;
        }
        const formData = new FormData();
        formData.append('content', content);
        const response = await fetch(route("ai-video-to-text.export-to-doc"), {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'text/event-stream'
            },
            body: formData
        });
        if (!response.ok) {
            console.error("Đã xảy ra lỗi khi xuất ra file DOCX", response);
            toast.error("Đã xảy ra lỗi khi xuất ra file DOCX");
            return;
        }
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = videoTitle.value + '.docx';
        document.body.appendChild(a);
        a.click();
        a.remove();
    }

    const saveHistory = async () => {
         await fetch(route("ai-video-to-text.save-history"), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                youtube_url: url.value,
                content:  sunEditorInstance.value.getContents(),
                video_title: videoTitle.value,
                audio_url: mp3Url.value
            })
         });
    }

    const viewHistory = () => {
        isShowHistory.value = true;
        if (videoToTextHistoryRef.value) {
            videoToTextHistoryRef.value.fetchData(1);
        }
    }

    const viewDetail = (item) => {
        url.value = item.youtube_url;
        sunEditorInstance.value.setContents(item.content);
        videoTitle.value = item.video_title || 'noi dung';
        mp3Url.value = item.audio_url;
        const youtubeUrlElement = document.querySelector('.youtube_url');
        if (youtubeUrlElement) {
            youtubeUrlElement.scrollIntoView({ behavior: 'smooth' });
        }
    }
    const getDataVideoToTextFromLocalStorage = () => {
        const data = localStorage.getItem('dataVideoToText');

        return data ? JSON.parse(data) : null;
    }

    const scrollToBottom = debounce(() => {
        const youtubeUrlElement = document.querySelector('.youtube_url');
        if (youtubeUrlElement) {
            youtubeUrlElement.scrollIntoView({ behavior: 'smooth' });
        }
    }, 500);

    const handleCopy = () => {
        if (sunEditorInstance.value && (sunEditorInstance.value.getText() || '').trim().length) {
            navigator.clipboard.write([
                new ClipboardItem({
                    'text/html': new Blob([sunEditorInstance.value.getContents()], { type: 'text/html' }),
                    'text/plain': new Blob([sunEditorInstance.value.getText()], { type: 'text/plain' })
                })
            ]).then(() => {
                toast.success('Đã copy thành công')
            }).catch(err => {
                toast.error("Lỗi khi sao chép:", err);
            });
        }
    }

    function isYouTubeURL(url) {
        try {
            const u = new URL(url);
            const hostname = u.hostname.toLowerCase();
            const cleanedHost = hostname.replace(/^www\./, '');
            const isYouTube = cleanedHost === 'youtube.com' || cleanedHost === 'youtu.be';

            return isYouTube;

        } catch (e) {
            return false;
        }
    }

    function isValidYoutubeURL(url) {
        const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]+)(\?.*)?$/;
        const shortYoutubeRegex =  /^https?:\/\/(www\.)?youtu\.be\/([a-zA-Z0-9_-]+)(\?.*)?$/;

        return youtubeRegex.test(url) || shortYoutubeRegex.test(url);
    }

    onMounted(() => {
        sunEditorInstance.value = suneditor.create(editor.value, {
            height: '400',
            width: '100%',
            plugins: plugins,
            buttonList: [
                ['undo', 'redo'],
                ['font', 'fontSize', 'formatBlock'],
                ['paragraphStyle', 'blockquote'],
                ['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript'],
                ['fontColor', 'hiliteColor', 'textStyle'],
                ['removeFormat'],
                '/', // Line break
                ['outdent', 'indent'],
                ['align', 'horizontalRule', 'list', 'lineHeight'],
                ['table', 'link', 'image', 'video', 'audio' /** ,'math' */], // You must add the 'katex' library at options to use the 'math' plugin.
                /** ['imageGallery'] */ // You must add the "imageGalleryUrl".
                ['fullScreen', 'showBlocks', 'codeView'],
                ['preview', 'print'],
                ['save', 'template'],
                /** ['dir', 'dir_ltr', 'dir_rtl'] */ // "dir": Toggle text direction, "dir_ltr": Right to Left, "dir_rtl": Left to Right
            ]
        });
        document.getElementById('viewButton').addEventListener('click', function() {
            if (!user) {
                window.location = route('login');
                return true;
            }
            const text = sunEditorInstance.value.getContents();
            if (sunEditorInstance.value && !((sunEditorInstance.value.getText() || "").trim()).length) {
                return;
            }
            document.getElementById('displayText').innerHTML = text;
            document.getElementById('editor-container').classList.add('hidden');
            document.body.className += ' no-scroll';
            document.getElementById('fullscreenContent').classList.remove('hidden');
            document.getElementById('displayText').scrollTop = 0;
        });

        document.getElementById('closeButton').addEventListener('click', function() {
            document.getElementById('displayText').innerHTML = '';
            document.getElementById('fullscreenContent').classList.add('hidden');
            document.getElementById('editor-container').classList.remove('hidden');
            document.body.className = document.body.className.replace(/ no-scroll/g, '')
        });
        const dataVideoToText = getDataVideoToTextFromLocalStorage();
        if (dataVideoToText) {
            scrollToBottom();
            url.value = dataVideoToText.url;
            selectedLanguage.value = dataVideoToText.selectedLanguage;
            videoTitle.value = videoTitle.value;
            translateContentStream(dataVideoToText);
            localStorage.removeItem('dataVideoToText')
        }
    });
</script>

<style>
    .no-scroll {
        overflow: hidden; /* Ẩn cuộn */
    }
    #displayText {
        max-height: 87vh; /* Chiều cao tối đa */
        overflow-y: auto; /* Cuộn dọc */
        width: 100%; /* Chiều rộng 100% */
    }
    .p-editor {
        --p-editor-content-color: #000000;
        /* Đặt màu mặc định cho văn bản */
    }

    .sun-editor-editable h1 {
        color: #FF5733 !important;
        font-weight: bold;
    }

    @media(min-width: 768px) {
        .sun-editor {
            flex: 1 !important;
        }

        .sun-editor .se-container {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .sun-editor .se-wrapper {
            flex: 1;
        }

        .sun-editor .se-resizing-bar {
            display: none;
        }

        .sun-editor .se-wrapper .se-wrapper-inner {
            min-height: 100%;
        }

        .se-container {
            height: 100%;
        }

        .se-wrapper {
            height: 100%;
        }
    }



    @keyframes rotation {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .loader {
        animation: rotation 1s linear infinite;
    }


    .loader {
        border: 16px solid #f3f3f3;
        border-top: 16px solid #BA66F0;
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
        /*flex: 0 1 100%;*/
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

    .outline-mic_small {
        width: 15px;
        height: 15px;
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

    .blinking {
        animation: blinkingEffect 1s infinite;
    }

    @keyframes blinkingEffect {
        0% { opacity: 1; }
        50% { opacity: 0; }
        100% { opacity: 1; }
    }
    .icon-copy {
        top: 6px;
        right: 12px;
        position: absolute;
        z-index: 999;
        cursor: pointer;
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
            font-size: 15px !important;
        }
    }
</style>
