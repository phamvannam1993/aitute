<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[103]" :class="props.is_show ? '':'hidden'">
        <div class="bg-white py-6 px-4 md:p-8 m-2 shadow-lg w-full md:w-[500px] xl:w-[800px] md:rounded-[40px] rounded-[20px] relative" >
            <div class="">
                <div class="flex flex-col lg:flex-row md:justify-end md:items-end lg:items-center mt-4 flex-wrap lg:flex-nowrap lg:justify-between">
                    <h2 class="text-black font-bold text-2xl mb-4">Thiết lập video</h2>
                    <svg @click="closePopup" class="cursor-pointer absolute top-2 right-5" width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="29" height="29" rx="14.5" fill="#F49A23"/>
                    <path d="M19.7294 9.27002L9.26953 19.7299" stroke="white" stroke-width="3" stroke-linecap="square" stroke-linejoin="round"/>
                    <path d="M9.26953 9.27002L19.7294 19.7299" stroke="white" stroke-width="3" stroke-linecap="square" stroke-linejoin="round"/>
                    </svg>
                </div>
                 <div class="flex w-full border-b-[3px] border-[#d6d6d6] mb-5 px-5"></div>
            </div>

            <div class="relative">
                <div class="overflow-y-auto  mb-2 relative text-center">
                    <div class="relative">
                        <div class="grid grid-cols-2 w-full mb-2">
                            <input type="file" ref="fileInput1" accept="audio/mpeg,audio/wav" @change="handleAudioFileChange1" class="hidden" />
                            <div class="flex flex-col gap-1">
                                <label class="text-black font-semibold text-[10px] lg:text-[14px] cursor-pointer">
                                    <div class="flex items-center gap-1">
                                        <span class="text-sm lg:text-base leading-none">1. Nhạc nền</span>
                                    </div>
                                </label>
                                <p class="text-[#A4A4A4] text-sm font-thin italic w-5/6 max-sm:hidden text-left">
                                    (Vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.)
                                </p>
                            </div>
                            <div class="flex flex-col gap-1">
                                <button type="button" @click="handleUploadClick1"
                                    class="flex items-center border-[2px] border-[#149CBE] justify-center gap-2 p-1 bg-white hover:bg-black/10 text-[#149CBE] rounded-lg backdrop-blur-sm transition-colors w-full h-fit">
                                    <svg class="size-6 md:size-5 xl:size-6" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.75 11.25L15.75 14.25C15.75 14.6478 15.592 15.0294 15.3107 15.3107C15.0294 15.592 14.6478 15.75 14.25 15.75L3.75 15.75C3.35218 15.75 2.97064 15.592 2.68934 15.3107C2.40804 15.0294 2.25 14.6478 2.25 14.25L2.25 11.25" stroke="#149CBE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12.75 6L9 2.25L5.25 6" stroke="#149CBE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9 2.25L9 11.25" stroke="#149CBE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>

<!--                                    <img src="/assets/img/ai3goc/icon/upload.svg" class="size-6 md:size-5 xl:size-6" />-->
                                    <span class="text-[15px] font-semibold">Tải nhạc nền</span>
                                </button>
                                <p class="text-[#1E405A] text-sm font-thin italic w-5/6 max-sm:hidden text-left">{{ audioName1 }}
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 w-full mb-2">
                            <input type="file" ref="fileInput2" accept="audio/mpeg,audio/wav" @change="handleAudioFileChange2" class="hidden" />
                            <div class="flex flex-col gap-1">
                                <label class="text-black font-semibold text-[10px] lg:text-[14px] cursor-pointer">
                                    <div class="flex items-center gap-1">
                                        <span class="text-sm lg:text-base leading-none">2. Thuyết minh</span>
                                    </div>
                                </label>
                                <div class="relative mt-2">
                                    <SuggestionPrompt v-model="audioDescription" :duraion="totalDuration" />
                                    <textarea rows="6" id="audio-description" v-model="audioDescription" type="text" placeholder="Nội dung thuyết minh" @input="changeDes"
                                        class="text-[10px] md:text-sm w-full mt-1 p-3 border text-black border-gray-300 rounded-[13px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                    <div class="object-input-mic relative ml-auto">
                                        <div v-if="isInputRecording" class="outline-input-mic right-3 bottom-3 flex items-center justify-center"></div>
                                        <div v-if="isInputRecording" class="outline-input-mic right-3 bottom-3 flex items-center justify-center" id="delayed"></div>
                                        <div v-if="isInputRecording" class="button-input-mic right-3 bottom-3 flex items-center justify-center"></div>
                                        <button class="button-input-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center" @click="startInputRecording" :disabled="disabledRecord"
                                            type="button">
                                            <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="10.027" cy="10.8298" r="9.8454" fill="#207A91"/>
                                                <path d="M13.7846 6.3686C13.7846 4.17135 12.0664 2.39014 9.94698 2.39014C7.82753 2.39014 6.10938 4.17135 6.10938 6.3686V8.64529C6.10938 10.8425 7.82753 12.6238 9.94698 12.6238C12.0664 12.6238 13.7846 10.8425 13.7846 8.64529V6.3686Z" fill="white"/>
                                                <path d="M14.6736 10.8257C14.5334 10.8244 14.3953 10.8606 14.2731 10.9308C14.1509 11.0009 14.0489 11.1025 13.9772 11.2255C13.5694 11.9482 12.9823 12.5485 12.2748 12.9659C11.5674 13.3833 10.7647 13.603 9.9475 13.603C9.13034 13.603 8.32762 13.3833 7.62018 12.9659C6.91274 12.5485 6.32557 11.9482 5.91783 11.2255C5.84628 11.1024 5.74427 11.0007 5.62204 10.9305C5.49981 10.8603 5.36166 10.8242 5.22142 10.8257C5.07861 10.8255 4.93826 10.8636 4.81437 10.9361C4.69049 11.0086 4.58742 11.113 4.51547 11.2389C4.44351 11.3648 4.40518 11.5078 4.40431 11.6535C4.40344 11.7993 4.44007 11.9427 4.51052 12.0695C4.99338 12.9199 5.6611 13.6458 6.46225 14.1913C7.2634 14.7368 8.17658 15.0873 9.13141 15.2158V16.8352H7.23989C7.02345 16.8352 6.81587 16.9229 6.66282 17.0791C6.50978 17.2353 6.4238 17.4472 6.4238 17.668C6.4238 17.8889 6.50978 18.1008 6.66282 18.257C6.81587 18.4131 7.02345 18.5009 7.23989 18.5009H12.666C12.8824 18.5009 13.09 18.4131 13.2431 18.257C13.3961 18.1008 13.4821 17.8889 13.4821 17.668C13.4821 17.4472 13.3961 17.2353 13.2431 17.0791C13.09 16.9229 12.8824 16.8352 12.666 16.8352H10.7636V15.2158C11.7183 15.0869 12.6313 14.7362 13.4324 14.1908C14.2335 13.6453 14.9013 12.9196 15.3845 12.0695C15.4549 11.9427 15.4916 11.7993 15.4907 11.6535C15.4898 11.5078 15.4515 11.3648 15.3795 11.2389C15.3076 11.113 15.2045 11.0086 15.0806 10.9361C14.9568 10.8636 14.8164 10.8255 14.6736 10.8257Z" fill="white"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <span class="text-[#A4A4A4] text-sm font-thin italic  max-sm:hidden text-right mr-2">
                                    Nội dung không quá {{totalWord}} từ
                                </span>
                                <label class="text-black font-semibold text-[10px] lg:text-[14px] cursor-pointer">
                                    <div class="flex items-center gap-1">
                                        <span class="text-sm lg:text-base leading-none">Chọn chức năng chuyển đổi âm thanh</span>
                                    </div>
                                </label>
                                <div class="grid grid-cols-2 gap-4 md:gap-14 mt-1 relative">
                                    <div class="flex flex-col items-center gap-2">
                                        <button @click="textToSpeech" :disabled="isLoadingClonedVoices" type="button"
                                            class="px-1 py-2 h-fit bg-[#FF9011] text-white rounded-[10px] w-full text-sm md:text-base font-bold button1">
                                            {{ isLoadingClonedVoices ? 'Đang chuyển đổi...' : 'Nhái giọng' }}
                                        </button>
                                        <p class="text-center italic text-xs text-ai3goc-primary">(Chỉ áp dụng cho tài khoản được cấp quyền)</p>
                                    </div>
                                    <span class="px-3 left-1/2 md:mt-[10px] mt-[5px] transform -translate-x-1/2 text-black  italic absolute">hoặc</span>
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="md:relative md:w-full">
                                            <div @click="toggleDropdown"
                                                class="w-full p-2 text-[10px] md:text-sm md:p-3 border text-black border-[#D4D4D4] rounded-xl shadow-sm cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <span v-if="selectedVoice">{{ getVoiceName(selectedVoice) }}</span>
                                                <span v-if="!selectedVoice">Bạn chưa chọn giọng nói</span>
                                            </div>
                                            <ul v-if="isDropdownOpen" class="absolute left-0 right-0 mt-1 bg-white border border-gray-300 text-black rounded-lg shadow-lg max-h-60 overflow-y-auto z-10">
                                                <li v-for="(voice, index) in listVoices" :key="index" @click="selectVoice(voice)"
                                                    class="flex justify-between items-center px-2 py-1 text-[10px] md:text-sm md:px-4 md:py-2 hover:bg-gray-100 cursor-pointer">
                                                    <span class="inline-block w-[200px] text-left">{{ voice.name }}</span>
                                                    <div class="flex items-center">
                                                        <div @click.stop="playSampleVoice(voice.demo)" class="text-white px-3 py-1 rounded-lg focus:outline-none w-[50px]">
                                                            <img src="/assets/img/ai_audio/speaker.png" alt="">
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class=" flex items-center gap-2 " v-if="isShowUploadFileOrRecord && clonedVoices.length === 0">
                                    <button type="button" :disabled="isLoadingClonedVoices" @click="triggerAudioUpload"
                                        class="flex-1 flex items-center gap-2 px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg backdrop-blur-sm transition-colors justify-center">
                                        <img src="/assets/img/upload.png" class="size-5" />
                                        <span class="hidden xl:block">Tải giọng nói của bạn lên</span>
                                    </button>
                                    <input type="file" ref="audioInput" accept="audio/*" @change="handleAudioUpload" class="hidden" @click="$event.target.value = null" />
                                    <div class="text-center">
                                        <span class="px-3 text-black italic">hoặc</span>
                                    </div>
                                    <button type="button" @click="isRecordVoice ? stopRecorCloneVoice() : startRecordingCloneVoice()"
                                        class="flex-1 flex items-center gap-2 px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg backdrop-blur-sm transition-colors justify-center"
                                        :class="{ 'bg-red-500 hover:bg-red-600': isRecordVoice }">
                                        <img :src="isRecordVoice ? '/assets/img/ai_audio/pause.png' : '/assets/img/mic.png'" class="size-5" />
                                        <span v-if="isRecordVoice">{{
                                            formatTime(recordingTime) }}</span>
                                        <span v-else class="hidden xl:block">Ghi âm (30s>)</span>
                                    </button>
                                </div>
                                <label class="text-black font-semibold text-[10px] lg:text-[14px] cursor-pointer">
                                    <div class="flex items-center gap-1">
                                        <span class="text-sm lg:text-base leading-none">Bạn có thể sử dụng âm thanh trực tiếp</span>
                                    </div>
                                </label>
                                <div class="flex flex-col">
                                    <div class="mt-2 flex items-center gap-2 ">
                                        <button type="button" @click="triggerAudioUploadV2"
                                            class="flex items-center  border-[#149CBE] border-[2px] flex-1 rounded-[10px] py-1 gap-2 justify-center">
                                            <div class="rounded-full w-[25px] h-[25px] bg-[#149CBE] flex items-center justify-center">
                                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M10.0206 11.7197C9.2411 12.1489 8.61553 12.8239 8.24869 13.6413L8.24073 13.659L8.24072 13.6591C8.14329 13.8762 8.05836 14.0654 7.98346 14.2125C7.91305 14.3507 7.80984 14.539 7.65214 14.6908C7.49161 14.8453 7.35212 14.9356 7.14544 15.0188C6.95837 15.0941 6.77523 15.1155 6.62697 15.1246C6.4863 15.1333 6.3179 15.1333 6.13927 15.1333H6.13926L6.11886 15.1333C4.93211 15.1333 3.97005 16.0953 3.97005 17.2821C3.97005 18.4688 4.93211 19.4309 6.11886 19.4309H11.9998H17.8808C19.0675 19.4309 20.0296 18.4688 20.0296 17.2821C20.0296 16.0953 19.0675 15.1333 17.8808 15.1333L17.8604 15.1333H17.8604C17.6817 15.1333 17.5133 15.1333 17.3727 15.1246C17.2244 15.1155 17.0413 15.0941 16.8542 15.0188C16.6475 14.9356 16.508 14.8453 16.3475 14.6908C16.1898 14.539 16.0866 14.3507 16.0162 14.2125C15.9413 14.0654 15.8563 13.8761 15.7589 13.659L15.7509 13.6413C15.3841 12.8239 14.7585 12.1489 13.979 11.7197V9.98283C15.4128 10.5146 16.5736 11.6074 17.1955 12.993C17.3032 13.233 17.372 13.3859 17.427 13.4937C17.4373 13.5139 17.446 13.5302 17.4531 13.5431L17.4703 13.5443C17.556 13.5496 17.6745 13.5499 17.8808 13.5499C19.942 13.5499 21.6129 15.2209 21.6129 17.2821C21.6129 19.3433 19.942 21.0142 17.8808 21.0142H11.9998H6.11886C4.05766 21.0142 2.38672 19.3433 2.38672 17.2821C2.38672 15.2209 4.05766 13.5499 6.11886 13.5499C6.32513 13.5499 6.44359 13.5496 6.52937 13.5443L6.54657 13.5431C6.55367 13.5302 6.56235 13.5139 6.57264 13.4937C6.62758 13.3859 6.69644 13.233 6.80417 12.993C7.42605 11.6074 8.58683 10.5146 10.0206 9.98283V11.7197Z"
                                                        fill="white" />
                                                    <path
                                                        d="M12.0007 3.55952L11.4409 2.99973L12.0007 2.43994L12.5605 2.99973L12.0007 3.55952ZM12.7924 13.3611C12.7924 13.7983 12.4379 14.1528 12.0007 14.1528C11.5635 14.1528 11.209 13.7983 11.209 13.3611L12.7924 13.3611ZM7.52029 6.92037L11.4409 2.99973L12.5605 4.11932L8.63987 8.03995L7.52029 6.92037ZM12.5605 2.99973L16.4811 6.92037L15.3616 8.03995L11.4409 4.11932L12.5605 2.99973ZM12.7924 3.55952L12.7924 13.3611L11.209 13.3611L11.209 3.55952L12.7924 3.55952Z"
                                                        fill="white" />
                                                </svg>
                                            </div>
                                            <span class="font-bold text-xs md:text-[15px] text-[#149CBE]">
                                                Tải âm thanh
                                            </span>
                                        </button>
                                        <input type="file" ref="audioInput2" accept=".aac,.mp3,.m4a,.wav,audio/*" @change="handleAudioFileChange" class="hidden" @click="$event.target.value = null" />
                                        <div class="text-center">
                                            <span class="text-black italic">hoặc</span>
                                        </div>
                                        <button type="button" @click="isRecordVoice ? stopRecorCloneVoice() : startRecordingCloneVoice()"
                                            class="flex items-center justify-center gap-1 border-[#149CBE] border-[2px] flex-1 px-4 py-2 rounded-[10px] text-[10px] md:text-[15px] font-bold"
                                            :class="{
                                                'bg-red-500': isRecordVoice
                                            }">
                                            <div class="mr-3 flex items-center justify-center w-[16px] md:w-[22px] h-[16px] md:h-[22px] rounded-full ">

                                                <svg v-if="isRecordVoice" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6 stroke-white">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" />
                                                </svg>
                                                <div  v-else class="w-[16px] md:w-[22px] h-[16px] md:h-[22px] rounded-full bg-[#149CBE] flex items-center justify-center">
                                                    <svg class="w-2 h-4 md:w-[13px] md:h-[20px]" viewBox="0 0 13 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_1396_3273)">
                                                            <path
                                                                d="M6.13049 13.22C4.96527 13.22 3.84758 12.7578 3.02271 11.9348C2.19783 11.1118 1.73312 9.99524 1.73047 8.83002V4.39996C1.73047 3.23301 2.19407 2.11386 3.01923 1.2887C3.84439 0.463536 4.96354 0 6.13049 0C7.29745 0 8.4166 0.463536 9.24176 1.2887C10.0669 2.11386 10.5305 3.23301 10.5305 4.39996V8.83002C10.5278 9.99524 10.0631 11.1118 9.23822 11.9348C8.41335 12.7578 7.29572 13.22 6.13049 13.22ZM6.13049 1.63C5.39758 1.63265 4.69554 1.92563 4.17822 2.44482C3.66091 2.96401 3.37048 3.66704 3.37048 4.39996V8.83002C3.41287 9.53459 3.72263 10.1964 4.23651 10.6803C4.75039 11.1642 5.42965 11.4337 6.1355 11.4337C6.84135 11.4337 7.52054 11.1642 8.03442 10.6803C8.54831 10.1964 8.85806 9.53459 8.90045 8.83002V4.39996C8.90045 3.66531 8.60865 2.96076 8.08917 2.44128C7.5697 1.92181 6.86514 1.63 6.13049 1.63Z"
                                                                fill="#ffffff" />
                                                            <path
                                                                d="M11.3591 11.07C11.2041 11.0666 11.0509 11.1046 10.9155 11.1802C10.7802 11.2558 10.6674 11.3662 10.5891 11.5C10.1364 12.2806 9.48659 12.9285 8.70473 13.3789C7.92286 13.8293 7.03642 14.0663 6.13411 14.0663C5.2318 14.0663 4.34529 13.8293 3.56343 13.3789C2.78157 12.9285 2.13177 12.2806 1.67909 11.5C1.55842 11.2931 1.36053 11.1427 1.12892 11.0817C0.897313 11.0208 0.650975 11.0543 0.444106 11.175C0.237237 11.2957 0.0867813 11.4936 0.0258319 11.7252C-0.0351174 11.9568 -0.00157984 12.2031 0.119094 12.41C0.655485 13.3295 1.39455 14.1146 2.28004 14.7055C3.16553 15.2964 4.1741 15.6776 5.22908 15.82V17.57H3.13905C2.90036 17.57 2.67148 17.6648 2.5027 17.8336C2.33392 18.0024 2.23909 18.2313 2.23909 18.47C2.23909 18.7087 2.33392 18.9376 2.5027 19.1064C2.67148 19.2752 2.90036 19.37 3.13905 19.37H9.13905C9.37775 19.37 9.60668 19.2752 9.77547 19.1064C9.94425 18.9376 10.0391 18.7087 10.0391 18.47C10.0391 18.2313 9.94425 18.0024 9.77547 17.8336C9.60668 17.6648 9.37775 17.57 9.13905 17.57H7.03908V15.82C8.09228 15.6762 9.0989 15.2943 9.98256 14.7035C10.8662 14.1127 11.6037 13.3283 12.1391 12.41C12.2188 12.2739 12.2611 12.119 12.2613 11.9613C12.2615 11.8035 12.2198 11.6485 12.1405 11.5122C12.0611 11.3758 11.9469 11.263 11.8096 11.1853C11.6723 11.1076 11.5168 11.0679 11.3591 11.07Z"
                                                                fill="#ffffff" />
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_1396_3273">
                                                                <rect width="12.26" height="19.38" fill="white" />
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>

                                            </div>
                                            <span class="leading-none" :class="{
                                                'text-[#149CBE]': !isRecordVoice,
                                                'text-white': isRecordVoice
                                            }">
                                                {{ isRecordVoice ? formatTime(recordingTime) : 'Thu âm' }}
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="rounded-lg">
                            <div class=" bg-gray-100 rounded-lg flex-1 mt-3 mb-3" v-if="uploadedAudioPreview">
                                <audio controls :src="uploadedAudioPreview" class="w-full"></audio>
                            </div>
                        </div>
                        <div class="flex items-center mb-2 justify-between">
                            <label class="text-black font-semibold text-[10px] lg:text-[14px] cursor-pointer">
                                <div class="flex items-center gap-1">
                                    <span class="text-sm lg:text-base leading-none">3. Phụ đề</span>
                                </div>
                            </label>
                            <div class="grid grid-cols-2 w-1/2 gap-2">
                            <div @click="changeCap(true)" class="flex items-center cursor-pointer">
                                <div class="flex items-center">
                                    <input type="radio" id="enableCaptionTrue" :checked="enableCaption" class="hidden peer" />
                                    <div class="w-[24px] h-[24px] flex items-center justify-center rounded-full border-2 border-[#D4D4D4] peer-checked:border-[#FFA411] mr-2">
                                        <div v-if="enableCaption" class="bg-[#FFA411] rounded-full w-[16px] h-[16px]"></div>
                                    </div>
                                    <label for="enableCaptionTrue" class="text-sm lg:text-base">Bật</label>
                                </div>
                            </div>
                            <div @click="changeCap(false)" class="flex items-center cursor-pointer">
                                <div class="flex items-center">
                                    <input type="radio" id="enableCaptionFalse" :checked="!enableCaption" class="hidden peer" />
                                    <div class="w-[24px] h-[24px] flex items-center justify-center rounded-full border-2 border-[#D4D4D4] peer-checked:border-[#FFA411] mr-2">
                                        <div v-if="!enableCaption" class="bg-[#FFA411] rounded-full w-[16px] h-[16px]"></div>
                                    </div>
                                    <label for="enableCaptionFalse" class="text-sm lg:text-base">Tắt</label>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="flex w-full border-b-[3px] border-[#d6d6d6] mt-5 px-5"></div>
            <div class="flex items-center gap-4 md:gap-8 justify-center mt-3">
                <button @click="confirmMergeVideo" class="flex items-center relative h-[40px] md:h-[50px] text-sm lg:text-base px-8 min-w-[180px] md:min-w-[200px] bg-ai3goc-bgclr text-white rounded-lg lg:rounded-2xl font-bold">
                <svg class="mr-2" width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
       <Popup
        v-if="isOpenWarrningUseVoice"
        title="Cảnh báo !"
        :message="'Tính năng này có thể sử dụng vào mục đích xấu nên để sử dụng tính năng này cần phải liên hệ với quản trị viên để ký cam kết sử dụng theo đúng pháp luật hiện hành. Liên hệ quản trị viên theo sđt <strong>098 878 6699</strong> (Mr. Hùng)'"
        cancelButtonText="Bỏ qua"
        @cancel="isOpenWarrningUseVoice = false"
    />
</template>

<script setup>
import { ref, watch, computed, onMounted, defineEmits, nextTick } from 'vue';
import Step from "@/Components/Step/Index.vue";
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { useTextToSpeech } from "@/helper/useTextToSpeech";
import { toast } from "vue3-toastify";
import SuggestionPrompt from './SuggestionPrompt.vue';
import Popup from '@/Components/Popup/Index.vue'

const isLoading = ref(false);
const errors = ref({});
const audioDescription = ref("")
const props = defineProps({
    message: String,
    isError:Boolean,
    is_show:Boolean,
    type: String,
    user: Object,
});
const { listVoice,handleTextToSpeech } = useTextToSpeech()
const { props: pageProps } = usePage();
const user = computed(() => pageProps.auth.user);
const updateImageUrl = async (s3_url) => {
    imageUrl.value = s3_url;
}
const text = ref("")
const changeDes = (event) => {
    const input = event.target.value;
    var countWord = countWords(input)
    if(countWord <= totalWord.value) {
        text.value = input
    } else {
        event.target.value = text.value
    }
}

const emit  = defineEmits();

const closePopup = () => {
    emit('close');
};
const fileInput1 = ref(null);
const audio1 = ref(null)
const audio2 = ref(null)
const enableCaption = ref(false)
const fileInput2 = ref(null);
const audioName1 = ref(null)
const audioName2 = ref(null)
const formatTime = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};
const changeCap = async (val) => {
   enableCaption.value = val
}

const clonedVoices = ref([]);
const isLoadingClonedVoices = ref(false);
const isLoadingClonedVoices_google = ref(false);

const handleAudioFileChange1 = (event) => {
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav"];
    if (!allowedAudioTypes.includes(type)) {
        toast.error( "Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.")
        return;
    }
    audio1.value = event.target.files[0]
    audioName1.value = audio1.value.name
    fileInput1.value.value = ""
};

const handleAudioFileChange2 = (event) => {
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav"];
    if (!allowedAudioTypes.includes(type)) {
        toast.error( "Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.")
        return;
    }
    audio2.value = event.target.files[0]
    audioName2.value = audio2.value.name
    fileInput2.value.value = ""
};

const handleUploadClick1 = (index) => {
    fileInput1.value?.click();
};

const handleUploadClick2 = (index) => {
    fileInput2.value?.click();
};

const confirmMergeVideo = async () => {
    if(!uploadedAudioPreview.value) {
        if(!audioDescription.value) {
            toast.error("Nội dung thuyết minh không được để trống")
            return
        }
        if(!selectedClonedVoice.value) {
            toast.error("Vui lòng chọn giọng nói")
            return
        }
        var countWord = countWords(audioDescription.value)
        if(countWord > totalWord.value) {
            toast.error("Nội dung thuyết minh không được vượt quá "+countWord+" từ.")
            return
        }
    }
    const data = {
        audio1:audio1.value,
        audio_s3: uploadedAudioPreview.value,
        voiceType:selectedClonedVoice.value,
        audioDes:audioDescription.value,
        enableCaption:enableCaption.value,
    }
    emit('confirmMergeVideo', data)
}

const isInputRecording = ref(false);
const audioInputBlob = ref(null);
const audioInputUrl = ref(null);
let mediaInputRecorder = null;
let audioInputChunks = [];
let deviceInput = ref(null);
const startInputRecording = async () => {
    if (!isInputRecording.value) {
        // Nếu chưa ghi âm
        try {
            isInputRecording.value = true; // Bắt đầu ghi âm
            deviceInput = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaInputRecorder = new MediaRecorder(deviceInput);

            // Khi có dữ liệu âm thanh
            mediaInputRecorder.addEventListener("dataavailable", (event) => {
                audioInputChunks.value.push(event.data);
            });

            // Khi dừng ghi âm
            mediaInputRecorder.addEventListener("stop", async () => {
                audioInputBlob.value = new Blob(audioInputChunks.value, { type: "audio/mp3" });
                audioInputUrl.value = URL.createObjectURL(audioInputBlob.value);
                console.log(audioInputUrl)

                // Tạo FormData và gửi yêu cầu API
                const formData = new FormData();

                // Sửa lại: Gói Blob thành File object trước khi thêm vào FormData
                const file = new File([audioInputBlob.value], "audio.mp3", { type: "audio/mp3" });
                formData.append("audio", file);

                // isTranscribing.value = true;
                try {
                    const response = await axios.post('/speech-to-text', formData, {
                        headers: { "Content-Type": "multipart/form-data" },
                    });
                    // Xử lý văn bản trả về từ API
                    console.log("Transcription Text:", response);
                    audioDescription.value = response?.data?.text || 'Vui lòng thử lại';
                } catch (error) {
                    console.error("Error in Speech-to-Text:", error);
                } finally {
                    // isTranscribing.value = false;
                }

                isInputRecording.value = false;
            });

            // Bắt đầu ghi âm
            mediaInputRecorder.onstart = () => {
                audioInputChunks.value = []; // Xóa các đoạn âm thanh trước đó
            };

            mediaInputRecorder.start(); // Bắt đầu ghi
        } catch (error) {
            console.error("Lỗi khi bắt đầu ghi âm:", error);
            isInputRecording.value = false; // Trở lại trạng thái chưa ghi âm nếu có lỗi
        }
    } else {
        // Nếu đang ghi âm
        isInputRecording.value = false; // Dừng ghi âm
        mediaInputRecorder.stop(); // Kết thúc ghi âm
        deviceInput.getTracks().forEach((track) => track.stop()); // Dừng tất cả các tracks
    }
};

const audioUrl = ref(null)
const audioPreview = ref(null);
const audioPreviewKey = ref(0);
const uploadedAudioPreview = ref(null);
const isRecordVoice_clonevoice = ref(false);
const recordingInterval_clonevoice = ref(null);
let recordedAudioChunks_clonevoice = [];
let recordingMediaRecorder_clonevoice = null;
const audioFile_clonevoice = ref(null);
const uploadedAudioPreview_clonevoice = ref(null);
const recordingTime_clonevoice = ref(0);
const audioInput_clonevoice = ref(null);
const fileInputName_clonevoice = ref(null);
const audioFile = ref(null);
const mediaRecorder = ref(null);
const audioChunks = ref([]);
const isRecording = ref(false);
const recordingTime = ref(0);
const audioS3 = ref(null)
const isTranscription = ref(false)
const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)
const isOpenWarrningUseVoice = ref(false);
const triggerAudioUpload = () => {
    audioInput.value.click();
};
const totalWord = ref(10)
const triggerAudioUploadV2 = () => {
    audioInput2.value.click();
};
const audioInput = ref(null);
const audioInput2 = ref(null);
const fileName2 = ref('');
const audioFile2 = ref(null);
const isLoadDetail = ref(false)
const handleAudioFileChange = async (event) => {
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav"];
    if (!allowedAudioTypes.includes(type)) {
        alert("Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.");
        audioInput.value.value = "";
        return;
    }
    await uploadToS3(event.target.files[0])
};

const uploadToS3 = async (file) => {
    const formData = new FormData();
    formData.append("audio_file", file);
    const response = await axios.post(route("ai-audio.upload-s3"), formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        }
    })
    if(response.data.success) {
        const urlAudio = response.data.s3_url;
        audioUrl.value = urlAudio;
        uploadedAudioPreview.value = urlAudio;
        audioS3.value = urlAudio;
        audioFile.value = null;
    } else {
        toast.error(response.data.message)
    }
}

const handleVoiceClone = async () => {
    if (!audioDescription.value) {
        toast.error("Vui lòng nhập lời thoại trước khi nhái giọng");
        return;
    }
    var countWord = countWords(audioDescription.value)
    if(countWord > totalWord.value) {
        toast.error("Nội dung thuyết minh không được vượt quá "+countWord+" từ.")
        return
    }
    try {
        const formData = new FormData();
        if (!audioFile.value) {
            toast.warning("Vui lòng tải audio hoặc ghi âm giọng của bạn")
            return;
        }
        isLoadingClonedVoices.value = true;
        formData.append("name", window.location.hostname + "_" + user.value.id)
        formData.append("description", "Giọng nói thuyết trình mạnh mẽ")
        formData.append("files", audioFile.value);
        const response = await axios.post(route("ai-audio.clone-voice"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            }
        }
        );

        if (response.status === 200) {
            toast.success("Clone giọng thành công.");
            clonedVoices.value = [response.data.data]
            selectedClonedVoice.value = response.data.data.type;

            credits.value = response.data.data.credits;
            audioFile.value = ""
            // Sau khi voice đã được clone thì chuyển sang bước nhái giọng dựa theo lời thoại
            console.log('clonedVoices1', clonedVoices.value)
            await textToSpeech();
        } else {
            console.error("Phản hồi không thành công:", response.data);
        }
        message.value = "";
    } catch (error) {
        console.error("Đã xảy ra lỗi khi gửi tin nhắn:", error.message);
    } finally {
        isLoadingClonedVoices.value = false;
    }
}

const textToSpeech = async () => {
    if (!user.value.clone_voice) {
        isOpenWarrningUseVoice.value = true;
        return;
    }
    const hasEnoughCredit = await checkCreditV2({
        type: 'elevenlab',
        model: "Elevenlab",
        number_result: audioDescription.value?.length || null,
    });
     if (clonedVoices.value.length === 0) {
        handleShowUploadFileOrRecord()
        return;
    }
    if (!hasEnoughCredit) {
        return; // Dừng nếu không đủ credit
    }
    // không có voice clone thì bắt buộc phải nhập nội dung thuyết minh hoặc ghi âm
    if (!clonedVoices.value[0]?.type) {
        if (!audioDescription.value || !audioFile_clonevoice.value) {
            alert('Hãy nhập nội dung thuyết minh cần nhái giọng. Sau đó bấm vào Tải âm thanh hoặc Ghi âm giọng nói của bạn trong 30s')
            return;
        }
    }
    if (!audioDescription.value) {
        alert('Hãy nhập nội dung nhái giọng')
        return;
    }
    try {
        isLoadingClonedVoices.value = true;
        if (clonedVoices.value.length === 0) {
            await handleVoiceClone();
        }
        if (clonedVoices.value.length === 0) {
            isLoadingClonedVoices.value = false;
            toast.error("Có lỗi xảy ra khi nhái giọng, vui lòng thử lại sau");
            return;
        }
        const result = await axios.post(route("ai-audio.text-to-speech"), {
            voice_id: clonedVoices.value[0].type,
            language: 'vi',
            text: audioDescription.value,
            isSaveDb: false,
            screen_id: 16
        })
        if (result.data.success) {
            const urlAudio = result.data.data;
            audioUrl.value = urlAudio;
            uploadedAudioPreview.value = urlAudio;
            audioS3.value = urlAudio;
            audioFile.value = null;
        }
    } catch (error) {
        console.log(error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại sau");
    } finally {
        isLoadingClonedVoices.value = false;
    }
};
const textToSpeechGoogle = async () => {
    if (!audioDescription.value.trim()) {
        alert('Hãy nhập nội dung để chuyển thành giọng nói');
        return;
    }
    try {
        isLoadingClonedVoices_google.value = true;
        const result = await axios.post(route("ai-audio.text-to-speech-google"), {
            text: audioDescription.value,
        })
        if (result.data.success) {
            const urlAudio = result.data.data;
            audioUrl.value = urlAudio;
            uploadedAudioPreview.value = urlAudio;
            audioS3.value = urlAudio;
            audioFile.value = null;
        }
    } catch (error) {
        console.log(error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại sau");
    } finally {
        isLoadingClonedVoices_google.value = false;
    }
};
const listVoices = ref("")
const fetchListVoices = async () => {
    try {
        const response = await axios.get(route('ai-audio.list-voices'));
        var listVoiceArr = [];
        for(var i = 0; i < response.data.data.length; i++) {
            if(response.data.data[i].model != 'cloned') {
                listVoiceArr.push(response.data.data[i])
            }
        }
        listVoices.value = listVoiceArr;
    } catch (error) {
        console.error("Error fetching voice types:", error);
    }
};

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

// Add new refs for recording
const isRecordVoice = ref(false);
const recordingInterval = ref(null);
let recordedAudioChunks = [];
let recordingMediaRecorder = null;
// Start recording function
const startRecordingCloneVoice = async () => {
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
            uploadToS3(file)
        };
        recordingMediaRecorder.start();
        isRecordVoice.value = true;
        recordingTime.value = 0;
        // Start timer
        recordingInterval.value = setInterval(() => {
            recordingTime.value++;
            // Automatically stop after 5 minutes
            if (recordingTime.value >= 300) {
                stopRecorCloneVoice();
            }
        }, 1000);
    } catch (err) {
        console.error("Error accessing microphone:", err);
        toast.error("Không thể truy cập microphone");
    }
};
// Stop recording function
const stopRecorCloneVoice = () => {
    if (recordingMediaRecorder && isRecordVoice.value) {
        recordingMediaRecorder.stop();
        recordingMediaRecorder.stream.getTracks().forEach(track => track.stop());
        clearInterval(recordingInterval.value);
        isRecordVoice.value = false;
        recordingTime.value = 0;
    }
};
const isDropdownOpen = ref(false);
const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};
const isShowUploadFileOrRecord = ref(false);
const selectedLanguage = ref("vi-VN");
const selectedVoice = ref("");
const selectedClonedVoice = ref("")
const selectedContentType = ref("talk");
const availableVoices = ref([]);
const selectVoice = (voice) => {
    console.log("selectVoice ~ voice:", voice)
    selectedVoice.value = voice.type;
    selectedClonedVoice.value = voice.type;
    uploadedAudioPreview.value = "";
    isDropdownOpen.value = false;
};
function countWords(str) {
    const words = str.trim().split(/\s+/);
    return words.filter(word => word.length > 0).length;
}
const getVoiceName = (type) => {
    const voice = listVoices.value.find(v => v.type === type);
    return voice ? voice.name : 'Bạn chưa tạo giọng nói';
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
onMounted(() => {
    fetchVoiceTypes();
    fetchListVoices();
});
const handleShowUploadFileOrRecord = () => {
    console.log("isShowUploadFileOrRecord", isShowUploadFileOrRecord.value)
    isShowUploadFileOrRecord.value = !isShowUploadFileOrRecord.value;
}

const fetchVoiceTypes = async () => {
    try {
        const response = await axios.get(route('voice-type.list'));
        clonedVoices.value = response.data.data;
    } catch (error) {
        console.error("Error fetching voice types:", error);
    }
};

const handleAudioUpload = (event) => {
    if (!audioDescription.value) {
        toast.error("Vui lòng nhập lời thoại trước khi nhái giọng");
        return;
    }
    const file = event.target.files[0];

    if (file) {
        // Clear previous audio data
        if (uploadedAudioPreview.value) {
            URL.revokeObjectURL(uploadedAudioPreview.value);
            uploadedAudioPreview.value = null;
            audioFile.value = null;
            audioPreviewKey.value++;
        }

        // Rest of your existing validation and handling code
        const allowedAudioTypes = ["audio/mpeg", "audio/wav", "audio/x-m4a"];
        if (!allowedAudioTypes.includes(file.type)) {
            alert("Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav. hoặc .m4a");
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
                audioFile.value = file;
                uploadedAudioPreview.value = URL.createObjectURL(file);
                // Force audio preview to reload
                audioPreviewKey.value += 1;
                // Ensure audio is loaded with new source
                nextTick(() => {
                    if (audioPreview.value) {
                        audioPreview.value.load();
                    }
                    handleVoiceClone();
                });
            }
            // Dọn dẹp URL đối tượng
            URL.revokeObjectURL(objectUrl);
        });
    }
};
const totalDuration = ref(10)
const updateAudioDes = async (content, duration) => {
    totalDuration.value = duration
    if(!audioDescription.value) {
        const response = await axios.post(route("short-video.getAudioDes"), {
            video_des: content,
            duration:duration
        });
        audioDescription.value = response.data.audio_des
    }
    totalWord.value = duration*4
}
defineExpose({ updateImageUrl, updateAudioDes });
</script>
