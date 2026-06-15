<template>

    <Head title="TÔI LÀ AI - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData" isBig>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="w-full bg-white rounded-[10px] md:rounded-[25px] p-[12px] md:p-[25px] drop-shadow-xl">
                <div class="flex flex-col mb-2">
                        <div class="flex gap-1">
                            <div class="flex justify-start items-center gap-2">
                                <div class="size-10 overflow-hidden rounded-full border-[1px] border-[#d4d4d4]">
                                    <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
                                </div>

                                <h2 class="text-black font-bold text-xl lg:text-xl 2xl:text-3xl leading-none">
                                    Video Avatar
                                </h2>
                            </div>
                        </div>
                        <p class="text-ai3goc-sec text-sm md:text-base font-bold my-1 md:my-4">Thực hiện theo các bước sau:</p>
                    </div>
                <form @submit.prevent="handleSubmit" class="">
                    <!-- Video Section -->
                    <div class="flex flex-col gap-4 my-4 w-full">
                        <div class="rounded-lg ">
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <Step :step="1" stepName="Tải video"/>
                                <button v-if="!isLoadConvert" type="button" @click="triggerVideoUpload"
                                    class="p-1 h-fit border-[2px] border-ai3goc-primary text-ai3goc-primary rounded-[10px] w-2/5 lg:w-1/2 text-[10px] md:text-[15px] font-bold flex items-center justify-center gap-2">
                                    <div class="rounded-full w-[25px] h-[25px] bg-ai3goc-primary flex items-center justify-center">
                                        <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M10.0206 11.7197C9.2411 12.1489 8.61553 12.8239 8.24869 13.6413L8.24073 13.659L8.24072 13.6591C8.14329 13.8762 8.05836 14.0654 7.98346 14.2125C7.91305 14.3507 7.80984 14.539 7.65214 14.6908C7.49161 14.8453 7.35212 14.9356 7.14544 15.0188C6.95837 15.0941 6.77523 15.1155 6.62697 15.1246C6.4863 15.1333 6.3179 15.1333 6.13927 15.1333H6.13926L6.11886 15.1333C4.93211 15.1333 3.97005 16.0953 3.97005 17.2821C3.97005 18.4688 4.93211 19.4309 6.11886 19.4309H11.9998H17.8808C19.0675 19.4309 20.0296 18.4688 20.0296 17.2821C20.0296 16.0953 19.0675 15.1333 17.8808 15.1333L17.8604 15.1333H17.8604C17.6817 15.1333 17.5133 15.1333 17.3727 15.1246C17.2244 15.1155 17.0413 15.0941 16.8542 15.0188C16.6475 14.9356 16.508 14.8453 16.3475 14.6908C16.1898 14.539 16.0866 14.3507 16.0162 14.2125C15.9413 14.0654 15.8563 13.8761 15.7589 13.659L15.7509 13.6413C15.3841 12.8239 14.7585 12.1489 13.979 11.7197V9.98283C15.4128 10.5146 16.5736 11.6074 17.1955 12.993C17.3032 13.233 17.372 13.3859 17.427 13.4937C17.4373 13.5139 17.446 13.5302 17.4531 13.5431L17.4703 13.5443C17.556 13.5496 17.6745 13.5499 17.8808 13.5499C19.942 13.5499 21.6129 15.2209 21.6129 17.2821C21.6129 19.3433 19.942 21.0142 17.8808 21.0142H11.9998H6.11886C4.05766 21.0142 2.38672 19.3433 2.38672 17.2821C2.38672 15.2209 4.05766 13.5499 6.11886 13.5499C6.32513 13.5499 6.44359 13.5496 6.52937 13.5443L6.54657 13.5431C6.55367 13.5302 6.56235 13.5139 6.57264 13.4937C6.62758 13.3859 6.69644 13.233 6.80417 12.993C7.42605 11.6074 8.58683 10.5146 10.0206 9.98283V11.7197Z"
                                                fill="white" />
                                            <path
                                                d="M12.0007 3.55952L11.4409 2.99973L12.0007 2.43994L12.5605 2.99973L12.0007 3.55952ZM12.7924 13.3611C12.7924 13.7983 12.4379 14.1528 12.0007 14.1528C11.5635 14.1528 11.209 13.7983 11.209 13.3611L12.7924 13.3611ZM7.52029 6.92037L11.4409 2.99973L12.5605 4.11932L8.63987 8.03995L7.52029 6.92037ZM12.5605 2.99973L16.4811 6.92037L15.3616 8.03995L11.4409 4.11932L12.5605 2.99973ZM12.7924 3.55952L12.7924 13.3611L11.209 13.3611L11.209 3.55952L12.7924 3.55952Z"
                                                fill="white" />
                                        </svg>
                                    </div>
                                    <span>Chọn tệp</span>
                                </button>
                                <span v-if="isLoadConvert" class="spinner">
                                    <div class="loaderVideo loadVideoS3"></div>
                                </span>
                            </div>
                            <!-- Video Preview Section -->
                            <div class="rounded overflow-hidden h-[320px] md:h-[400px]">
                                <video v-if="uploadedVideoPreview" controls class="w-full h-full object-fit" ref="videoPreview" :key="videoPreviewKey" :src="uploadedVideoPreview">
                                    Trình duyệt không hỗ trợ phát video.
                                </video>
                                <div v-else class="w-full h-full flex items-center justify-center bg-[#E6E6E6]">
                                    <img src="/assets/img/aiwow/play_button.png" class="w-20" />
                                </div>
                            </div>
                        </div>
                        <input type="file" ref="videoInput" accept="video/*" @change="handleVideoUpload" class="hidden" />
                    </div>
                    <div class="relative">
                        <!-- Audio Section -->
                        <div class="flex flex-col gap-4 my-4 w-full">
                            <div class=" rounded-lg">
                                <Step :step="2" stepName="Nhập nội dung nhái giọng hoặc nội dung để chuyển đổi âm thanh"/>
                                <div class="relative my-2">
                                    <SuggestionPrompt v-model="videoDescription" :type="'voice'" :screenId="16" />
                                    <textarea rows="6" id="video-description" v-model="videoDescription" type="text" placeholder="Nhập nội dung nhái giọng hoặc nội dung để chuyển đổi âm thanh"
                                        class="text-[10px] md:text-sm w-full mt-1 p-3 border text-black border-gray-300 rounded-[13px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                    <div class="object-input-mic relative ml-auto">
                                        <div v-if="isInputRecording" class="outline-input-mic right-3 bottom-3 flex items-center justify-center"></div>
                                        <div v-if="isInputRecording" class="outline-input-mic right-3 bottom-3 flex items-center justify-center" id="delayed"></div>
                                        <div v-if="isInputRecording" class="button-input-mic right-3 bottom-3 flex items-center justify-center"></div>
                                        <button class="button-input-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center" @click="startInputRecording" :disabled="disabledRecord"
                                            type="button">
                                            <svg class="size-4" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000">
                                                <g>
                                                    <path
                                                        d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z" />
                                                </g>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <Step :step="3" stepName="Chọn chức năng chuyển đổi âm thanh"/>
                                <div class="grid grid-cols-2 gap-4 md:gap-14 mt-1">
                                    <div class="flex flex-col items-center gap-2">

                                        <button @click="textToSpeech" :disabled="isLoadingClonedVoices" type="button"
                                            class="px-1 py-2 h-fit bg-[#FF9011] text-white rounded-[10px] w-full text-sm md:text-base font-bold">
                                            {{ isLoadingClonedVoices ? 'Đang chuyển đổi...' : 'Nhái giọng' }}
                                        </button>
                                        <p class="text-center italic text-xs text-ai3goc-primary">(Chỉ áp dụng cho tài khoản được cấp quyền)</p>
                                    </div>
                                    <div class="flex flex-col items-center gap-2">

                                        <button @click="textToSpeechGoogle" :disabled="isLoadingClonedVoices_google" type="button"
                                            class="px-1 py-2 h-fit bg-[#FF9011] text-white rounded-[10px] w-full text-sm md:text-base font-bold">
                                            {{ isLoadingClonedVoices_google ? 'Đang chuyển đổi...' : 'Chuyển đổi âm thanh' }}
                                        </button>
                                        <p class="text-center italic text-xs text-ai3goc-primary">(Chuyển đổi âm thanh từ nội dung trên)</p>
                                    </div>


                                </div>
                                <!-- Audio Upload/Record Buttons -->

                            </div>
                            <div class="rounded-lg">
                                <!-- Audio Upload/Record Buttons -->
                                <label class="text-black font-bold text-xs md:text-[14px] mt-2 inline-block"><strong>Hoặc:</strong> Nếu muốn sử dụng âm thanh trực tiếp thì bấm vào đây để tải âm
                                    thanh lên hoặc ghi âm
                                </label>
                                <div class="flex flex-col">
                                    <div class="mt-2 flex items-center gap-2 ">
                                        <button type="button" :disabled="isLoadingClonedVoices" @click="triggerAudioUpload"
                                            class="flex items-center  border-ai3goc-primary border-[2px] flex-1 rounded-[10px] py-1 gap-2 justify-center">
                                            <div class="rounded-full w-[25px] h-[25px] bg-ai3goc-primary flex items-center justify-center">
                                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M10.0206 11.7197C9.2411 12.1489 8.61553 12.8239 8.24869 13.6413L8.24073 13.659L8.24072 13.6591C8.14329 13.8762 8.05836 14.0654 7.98346 14.2125C7.91305 14.3507 7.80984 14.539 7.65214 14.6908C7.49161 14.8453 7.35212 14.9356 7.14544 15.0188C6.95837 15.0941 6.77523 15.1155 6.62697 15.1246C6.4863 15.1333 6.3179 15.1333 6.13927 15.1333H6.13926L6.11886 15.1333C4.93211 15.1333 3.97005 16.0953 3.97005 17.2821C3.97005 18.4688 4.93211 19.4309 6.11886 19.4309H11.9998H17.8808C19.0675 19.4309 20.0296 18.4688 20.0296 17.2821C20.0296 16.0953 19.0675 15.1333 17.8808 15.1333L17.8604 15.1333H17.8604C17.6817 15.1333 17.5133 15.1333 17.3727 15.1246C17.2244 15.1155 17.0413 15.0941 16.8542 15.0188C16.6475 14.9356 16.508 14.8453 16.3475 14.6908C16.1898 14.539 16.0866 14.3507 16.0162 14.2125C15.9413 14.0654 15.8563 13.8761 15.7589 13.659L15.7509 13.6413C15.3841 12.8239 14.7585 12.1489 13.979 11.7197V9.98283C15.4128 10.5146 16.5736 11.6074 17.1955 12.993C17.3032 13.233 17.372 13.3859 17.427 13.4937C17.4373 13.5139 17.446 13.5302 17.4531 13.5431L17.4703 13.5443C17.556 13.5496 17.6745 13.5499 17.8808 13.5499C19.942 13.5499 21.6129 15.2209 21.6129 17.2821C21.6129 19.3433 19.942 21.0142 17.8808 21.0142H11.9998H6.11886C4.05766 21.0142 2.38672 19.3433 2.38672 17.2821C2.38672 15.2209 4.05766 13.5499 6.11886 13.5499C6.32513 13.5499 6.44359 13.5496 6.52937 13.5443L6.54657 13.5431C6.55367 13.5302 6.56235 13.5139 6.57264 13.4937C6.62758 13.3859 6.69644 13.233 6.80417 12.993C7.42605 11.6074 8.58683 10.5146 10.0206 9.98283V11.7197Z"
                                                        fill="white" />
                                                    <path
                                                        d="M12.0007 3.55952L11.4409 2.99973L12.0007 2.43994L12.5605 2.99973L12.0007 3.55952ZM12.7924 13.3611C12.7924 13.7983 12.4379 14.1528 12.0007 14.1528C11.5635 14.1528 11.209 13.7983 11.209 13.3611L12.7924 13.3611ZM7.52029 6.92037L11.4409 2.99973L12.5605 4.11932L8.63987 8.03995L7.52029 6.92037ZM12.5605 2.99973L16.4811 6.92037L15.3616 8.03995L11.4409 4.11932L12.5605 2.99973ZM12.7924 3.55952L12.7924 13.3611L11.209 13.3611L11.209 3.55952L12.7924 3.55952Z"
                                                        fill="white" />
                                                </svg>
                                            </div>
                                            <span class="font-bold text-xs md:text-[15px] text-ai3goc-primary">
                                                Tải âm thanh
                                            </span>
                                        </button>
                                        <input type="file" ref="audioInput" accept=".aac,.mp3,.m4a,.wav,audio/*" @change="handleAudioUpload" class="hidden" @click="$event.target.value = null" />
                                        <div class="text-center">
                                            <span class="text-gray-500">hoặc</span>
                                        </div>
                                        <button type="button" @click="isRecordVoice ? stopRecorCloneVoice() : startRecordingCloneVoice()"
                                            class="flex items-center justify-center gap-1 border-ai3goc-primary border-[2px] flex-1 px-4 py-2 rounded-[10px] text-[10px] md:text-[15px] font-bold"
                                            :class="{
                                                'bg-red-500': isRecordVoice
                                            }">
                                            <div class="flex items-center justify-center w-[16px] md:w-[22px] h-[16px] md:h-[22px] rounded-full ">

                                                <svg v-if="isRecordVoice" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6 stroke-white">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" />
                                                </svg>
                                                <div  v-else class="w-[16px] md:w-[22px] h-[16px] md:h-[22px] rounded-full bg-ai3goc-primary flex items-center justify-center">
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
                                                'text-ai3goc-primary': !isRecordVoice,
                                                'text-white': isRecordVoice
                                            }">
                                                {{ isRecordVoice ? formatTime(recordingTime) : 'Thu âm' }}
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-lg">
                                <Step :step="4" stepName="Kiểm tra lại âm thanh sau khi thực hiện bước 3"/>
                                <div class=" bg-gray-100 rounded-lg flex-1 mt-3 " v-if="uploadedAudioPreview">
                                    <audio controls :src="uploadedAudioPreview" class="w-full"></audio>
                                </div>
                            </div>
                            <div class="rounded-lg">
                                <!-- Audio Upload/Record Buttons -->
                                <label class="flex items-center justify-between flex-1 gap-1 text-black font-semibold text-[14px] mt-2">
                                    <Step :step="5" stepName="Tải nhạc nền"/>
                                    <a @click="$refs.fileInput.click()" href="javascript:void(0)"
                                        class="px-1 py-2 h-fit bg-ai3goc-primary text-white rounded-[10px] w-2/5 lg:w-1/2 text-sm md:text-base font-bold flex items-center justify-center">Chọn nhạc
                                        nền</a>
                                </label>
                                <p class="line-clamp-1 text-black font-bold">{{ fileName2 }}</p>
                                <div>
                                    <span class="text-[10px] md:text-xs text-ai3goc-primary italic">(Nhạc nền chỉ dùng cho trường hợp video có thuyết minh)</span>
                                </div>
                                <div class="relative lg:w-1/2">
                                    <input type="file" id="audio-file" @change="handleAudioFileChange" accept="audio/*" class="hidden" ref="fileInput" />
                                </div>
                            </div>

                            <div class="rounded-lg">
                                <label for="image-description" class="text-xs lg:text-sm flex gap-1 items-center mb-1 font-semibold justify-between">
                                    <Step :step="6" />
                                    <div class="text-xs md:text-sm flex gap-1 items-center mb-1 font-semibold w-1/2">
                                        <input type="checkbox" id="caption" v-model="enableCaption" class="ml-0 rounded-full text-ai3goc-primary ring-ai3goc-primary focus:ring-2 focus:ring-ai3goc-primary">
                                        <p class="text-black text-sm md:text-base">Tạo phụ đề</p>
                                    </div>
                                </label>
                            </div>
                            <input type="file" ref="audioInput" accept=".aac,.mp3,.m4a,.wav,audio/*" @change="handleAudioUpload" class="hidden" />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-5 flex justify-between">
                        <Step :step="7" stepName="Bấm vào đây"/>
                        <button type="submit" :class="{
                            'bg-ai3goc-primary': !videoFile || !audioFile,
                            'bg-ai3goc-primary hover:bg-ai3goc-primary/50': videoFile && audioFile
                        }" :disabled="isLoading" class="px-1 py-2 h-fit bg-ai3goc-sec text-white rounded-[10px] w-2/5 lg:w-1/2 text-sm md:text-base font-bold flex items-center justify-center">
                            {{ isLoading ? 'Đang xử lý...' : 'Gửi yêu cầu' }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="w-full flex flex-col items-center bg-white rounded-[10px] md:rounded-[25px] p-[12px] md:p-[25px] drop-shadow-xl">
                <p class="text-[10px] text-sm text-left text-ai3goc-primary w-full italic mb-4">Hiển thị kết quả</p>
                <video v-if="resultVideoUrl" :src="resultVideoUrl.s3_url" alt="video-generate-by-AI" controls="" preload="auto"
                    class="md:max-h-[500px] object-cover rounded-lg mb-2" />
                <div v-else class="flex items-center justify-center bg-gray-300 w-full h-[300px] md:h-[400px] xl:h-[500px] rounded-xl  showLoaderVideo mb-4">
                    <div v-if="isLoading" class="flex flex-col items-center justify-center">
                        <div class="loaderVideo"></div>
                        <p class="text-loading">Video đang được tạo. Vui lòng chờ !</p>
                    </div>
                    <div v-else class="w-full h-full flex items-center justify-center bg-[#E6E6E6]">
                        <img src="/assets/img/aiwow/play_button.png" class="w-20" />
                    </div>
                </div>
                <TaskBar :isActive="resultVideoUrl"  :isCallUploadAudio="isCallUploadAudio" @handle-audio-file-change="handleAudioFileChangeV2" :selectedImage="resultVideoUrl" :shareLinkableType="'Lipsync'" :features="['upload-audio']" type="video" />

                <!-- <div :class="{ 'pointer-events-none': !resultVideoUrl }" class="flex items-center flex-wrap w-full py-2 justify-end gap-2">
                    <div @click="createPost(resultVideoUrl)" class="flex bg-[#149CBE] px-2 py-2 rounded-full lg:flex-row flex-col justify-start items-center gap-1 transition-colors duration-300  ">
                        <img src="/assets/img/ai3goc/icon/post.svg" class="" alt="Create Post" />
                        <span class="flex items-center justify-center font-medium text-white text-xs">Đăng
                            bài</span>
                    </div>
                    <div @click="downloadVideo()" class="flex bg-[#149CBE] px-2 py-2 rounded-full lg:flex-row flex-col justify-start items-center gap-1 transition-colors duration-300  ">
                        <img src="/assets/img/ai3goc/icon/download.svg" class="" alt="Create Post" />
                        <span class="flex items-center justify-center font-medium text-white text-xs">Tải
                            xuống</span>
                    </div>
                </div>
                 -->
                 <div class="flex justify-end mt-4 w-full">
                     <a :href="route('lipsync.historyLipSync')"
                         class="px-4 w-[180px] text-center py-2 bg-ai3goc-primary text-white font-bold text-[15px] rounded-[10px] hover:bg-ai3goc-sec transition-colors duration-300">
                         Lịch sử
                     </a>
                 </div>
            </div>
        </div>
    </Layout>
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade" />
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel" :currentCredit="pageProps.auth.user.credit || 0"
        :additionalCredit="additionalCredit" />
    <div v-if="isShowAlert" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="z-index:9999">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-bold text-[#092A99]">Xác nhận thoát màn hình</h3>
            <p class="mt-4 text-gray-600">
                Video đang được tạo. Bạn có chắc chắn muốn thoát không?
            </p>
            <div class="mt-6 flex justify-end space-x-4">
                <button @click="cancelExit" class="px-4 py-2 bg-gray-300 text-black rounded">Hủy</button>
                <button @click="confirmExit" class="px-4 py-2 bg-red-500 text-white rounded">Đồng ý</button>
            </div>
        </div>
    </div>
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
    <Popup
        v-if="showConfirmModal"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa video này không?"
        cancelButtonText="Huỷ"
        acceptButtonText="Xoá"
        @cancel="cancelDelete"
        @accept="confirmDelete"
    />

    <Popup
        v-if="showPopupConfirm"
        title="Xin chào !"
        :message="messageSubmit"
        cancelButtonText="Bỏ qua"
        acceptButtonText="Đồng ý"
        @cancel="closePopup"
        @accept="confirmSubmit"
    />

    <Popup
        v-if="isOpenWarrningUseVoice"
        title="Cảnh báo !"
        :message="'Tính năng này có thể sử dụng vào mục đích xấu nên để sử dụng tính năng này cần phải liên hệ với quản trị viên để ký cam kết sử dụng theo đúng pháp luật hiện hành. Liên hệ quản trị viên theo sđt <strong>098 878 6699</strong> (Mr. Hùng)'"
        cancelButtonText="Bỏ qua"
        @cancel="isOpenWarrningUseVoice = false"
    />

</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, computed, onBeforeUnmount } from 'vue';
import Layout from "@/Layouts/Client/Layout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import Footer from '../Home/Components/Footer.vue';
import MainMenu from '@/Components/MainMenu.vue';
import Credit from '@/Components/Credit.vue';
import axios from 'axios';
import firebase from "../../../firebase/firebase";
import PopupAff from '@/Components/PopupAff.vue';
import CreditBuyModal from '../../../Components/ModalBuyCredit/BuyCredit.vue';
import { toast } from "vue3-toastify";
import FormShareLink from '@/Components/FormShareLink.vue';
import Modal from '@/Components/Modal.vue';
import SuggestionPrompt from '@/Components/AIVirtual/SuggestionPrompt.vue';
import LabelStep from '@/Components/LabelStep.vue';
import Popup from '@/Components/Popup/Index.vue'
import Step from "@/Components/Step/Index.vue";
import TaskBar from "@/Components/TaskBar.vue";


const props = defineProps({
    videoUrl: String
});

const { props: pageProps } = usePage();
const user = computed(() => pageProps.auth.user);

const breadcrumbsData = [
    { text: "Tôi là A.I", link: "lipsync.lipsync" },
];

const showAffKeyPopup = ref(false);
const messageSubmit = ref("File âm thanh dài quá 1 phút thời gian tạo sẽ rất lâu. Bạn có muốn tiếp tục không?")
const credits = ref(0);
const isConfirm = ref(false)
const showPopupConfirm = ref(false)
const isLoading = ref(false);
const videoInput = ref(null);
const audioInput = ref(null);
const audioInputVoice = ref(null);
const videoFile = ref(null);
const audioFile = ref(null);
const uploadedVideoPreview = ref(null);
const uploadedAudioPreview = ref(null);
const resultVideoUrl = ref(null);
const isShowDes = ref(true);
const numberResult = ref(0);
const additionalCredit = ref(0);
const isShowAlert = ref(false);
const enableCaption = ref(false);
const isCallUploadAudio = ref(false)
// Thêm các ref mới cho recording
const mediaRecorder = ref(null);
const audioChunks = ref([]);
const isRecording = ref(false);
const recordingTime = ref(0);
const audioS3 = ref(null)
const isTranscription = ref(false)
const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)
const isOpenWarrningUseVoice = ref(false);
const videoDescription = ref(null);

const triggerVideoUpload = () => {
    videoInput.value.click();
};
const audioUrl = ref(null)

const confirmSubmit = async () => {
    isConfirm.value = true;
    showPopupConfirm.value = false
    await handleSubmit()
}

const closePopup = () => {
    isConfirm.value = false;
    showPopupConfirm.value = false
}

const triggerAudioUpload = () => {
    audioInput.value.click();
};
let showBuyCreditPopup = ref(false);
const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};
const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};


const toggleTranscription = async (lipSyncId, duration) => {
    if (isTranscription.value) {
        return
    }
    try {
        const response = await axios.post(route('ai-video.create-video-with-transcription'), {
            lip_sync_id: lipSyncId,
            duration: duration,
            screen_id: 16
        });
        const transcription = response?.data?.video;
        resultVideoUrl.value = {s3_url: transcription, id: lipSyncId};
        isTranscription.value = true
    } catch (error) {
        console.error('Error creating transcription:', error);
        toast.error('Có lỗi xảy ra khi tạo video. Vui lòng thử lại.');
        isTranscription.value = true
    } finally {
        isTranscription.value = true
    }
};


const getDuration = async (videoUrl, audioUrl) => {
    try {
        const formData = new FormData();
        formData.append('videoUrl', videoUrl);
        formData.append('audioUrl', audioUrl);
        formData.append('number_result', numberResult.value);
        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("lipsync.getDuration"), formData);
        if (response.data.success) {
            numberResult.value = response.data.duration
            audioS3.value = response.data.audioS3
            if (response.data.audioDuration > 60) {
                isLoading.value = false;
                showPopupConfirm.value = true
                return false
            }
            isConfirm.value = true
            return true
        } else {
            alert(response.data.message)
            return false
        }
    } catch (error) {
        alert(error.message)
        isLoading.value = false;
        return false;
    }
}

const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append('model', 'lipsync');
        formData.append('type', 'lipsync');
        formData.append('number_result', numberResult.value);
        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
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

const fileName2 = ref('');
const audioFile2 = ref(null);
const isLoadDetail = ref(false)
const handleAudioFileChange = (event) => {
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav"];
    if (!allowedAudioTypes.includes(type)) {
        alert("Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.");
        audioInput.value.value = "";
        return;
    }
    audioFile2.value = event.target.files[0];
    isUploadAudio.value = true
    fileName2.value = audioFile2.value ? audioFile2.value.name : 'Chưa có tệp nào được chọn';
};
const lipSyncIdRes = ref("")
const handleAudioFileChangeV2 = async () => {
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav"];
    if (!allowedAudioTypes.includes(type)) {
        alert("Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.");
        return;
    }
    isCallUploadAudio.value = true;
    const file = event.target.files[0];
    const formData = new FormData();
    formData.append('lip_sync_id', lipSyncIdRes.value);
    formData.append('audio_file', file);
    let response = await axios.post(
        route("lipsync.mergeAudioVideoLipsyncQueue"),
        formData,
        {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        }
    );

    response = await axios.get(route("lipsync.detailLipsync", { id: lipSyncIdRes.value }));
    if (response.data.url && response.data.is_upload_audio) {
        resultVideoUrl.value = {s3_url: response.data.url, id: lipSyncIdRes.value};
        isLoading.value = false;
        isLoadDetail.value = false
    } else if (response.data.code === 500) {
        isLoading.value = false;
        isLoadDetail.value = false
        alert(response.data.message)
    }
    isCallUploadAudio.value = false;
}

const getVideoDuration = async (file) =>
    new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => {
            const media = new Audio(reader.result);
            media.onloadedmetadata = () => resolve(media.duration);
        };
        reader.readAsDataURL(file);
        reader.onerror = (error) => reject(error);
    });

const formatTime = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};
const videoPreviewKey = ref(0);
const audioPreviewKey = ref(0);
const videoPreview = ref(null);
const audioPreview = ref(null);
const DEFAULT_VIDEO_URL = props.videoUrl;
const DEFAULT_AUDIO_URL = "";
const filteredSongs = ref([]);
const filteredVideos = ref([]);
const currentAudio = ref(null);
const currentAudioUrl = ref(null);
const videoRefs = ref([]);
const currentPageVideos = ref(1);
const hasNextPageVideos = ref(false);
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const videoDeleteId = ref(null);
const lipSyncResult = ref(null);
const clonedVoices = ref([]);
const isLoadingClonedVoices = ref(false);
const isLoadingClonedVoices_google = ref(false);
const cancelExit = () => {
    history.pushState({}, '');
    isShowAlert.value = false;
}

const confirmExit = () => {
    var redirect = localStorage.getItem("referrer_lip");
    isLoading.value = true;
    window.location.href = redirect
}

const referrer = document.referrer;
if (!referrer.includes("lipsync")) {
    localStorage.setItem("referrer_lip", referrer);
}
for (var i = 0; i < 100; i++) {
    history.pushState({}, '');
}
window.onpopstate = function () {
    if (isLoading.value) {
        isShowAlert.value = true;
        isHistory.value = false;
    }
};

const fetchVideo = async () => {
    try {
        const response = await axios.get(
            route("ai-video.getListImgToVideo", {
                page: currentPageVideos.value
            })
        );

        hasNextPageVideos.value = response?.data?.data?.next_page_url || null;
        let list = response.data.data.data || [];
        list = list.map((item) => ({
            ...item,
            isCheck: false,
        }));
        if (list.length > 0) {
            filteredVideos.value = [...list, ...filteredVideos.value];
            console.log(hasNextPageVideos.value)
        }
    } catch (error) {
        console.error("Error generating AI content:", error);
    } finally {
        isLoading.value = false;
        currentPageVideos.value++;
    }
};

const fetchSong = async () => {
    const response = await axios.get(route('text-to-song.getAllMusic'));
    filteredSongs.value = response.data.data;
    console.log(filteredSongs.value);
}
var intervalS3 = null
const handleVideoUpload = async (event) => {
    const file = event.target.files[0];
    if (file) {
        const allowedVideoTypes = ["video/mp4", "video/quicktime", "video/webm"];
        if (!allowedVideoTypes.includes(file.type)) {
            alert("Xin vui lòng tải lên các tệp video có định dạng .mp4, .webm, .mov");
            videoInput.value.value = "";
            return;
        }
        if (file.size > 300000000) {
            alert("File video của bạn quá lớn. Vui lòng upload file video nhỏ hơn 300MB")
            isLoading.value = false
            return false
        }
        isLoadConvert.value = true
        const formData = new FormData();
        formData.append('video_file', file);
        if (file.type == "video/mp4") {
            formData.append('is_mp4', true);
        }
        const response = await axios.post(
            route("lipsync.convertFileToMp4"),
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        if (response.data.success) {
            if (response.data.video_s3) {
                uploadedVideoPreview.value = response.data.video_s3;
                isLoadConvert.value = false
            } else {
                videoConvert.value = response.data.video_convert
                uuidJob.value = response.data.uuid
                intervalS3 = setInterval(async () => {
                    await getS3URLConvert()
                }, 10000);
            }
        } else {
            alert(response.data.message);
            return false
        }

        // Update with new file
        videoFile.value = file;
    }
};
const videoConvert = ref(null)
const uuidJob = ref(null)
const isLoadConvert = ref(false)
const getS3URLConvert = async () => {
    var url = route("lipsync.getFileConvert");
    url = url + "?video_convert=" + videoConvert.value + "&uuid_job=" + uuidJob.value
    const response = await axios.get(url);
    if (response.data.success) {
        if (response.data.video_s3) {
            uploadedVideoPreview.value = response.data.video_s3
            clearInterval(intervalS3)
            isLoadConvert.value = false
        }
    } else {
        alert(response.data.message)
        clearInterval(intervalS3)
        isLoadConvert.value = false
    }
}

const handleAudioUpload = async (event) => {
    const file = event.target.files[0];

    if (file) {
        const allowedAudioTypes = ["audio/mpeg", "audio/wav", "audio/x-m4a"];
        if (!allowedAudioTypes.includes(file.type)) {
            alert("Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav. hoặc .m4a");
            audioInput.value.value = "";
            return;
        }
        var duration = await getVideoDuration(file)
        if (duration > 5 * 60) {
            alert("Vui lòng upload file audio có độ dài không quá 5 phút");
            audioInput.value.value = "";
            return;
        }
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
        });
    }
};
const checkInterval = ref(null);
const handleSubmit = async () => {
    // Default URLs for when files are not provided
    const defaultVideoUrl = props.videoUrl;
    console.log("handleSubmit ~ defaultVideoUrl:", defaultVideoUrl)
    const defaultAudioUrl = "";

    isLoading.value = true;

    try {
        let videoUrl = uploadedVideoPreview.value || defaultVideoUrl;
        let audioUrl = audioS3.value || defaultAudioUrl;
        // Xử lý upload video nếu có file
        // if (videoFile.value) {
        //     const videoMetadata = {
        //         contentType: videoFile.value.type,
        //         customMetadata: {
        //             fileName: videoFile.value.name,
        //             fileSize: videoFile.value.size.toString(),
        //             lastModified: videoFile.value.lastModified.toString()
        //         }
        //     };
        //     videoUrl = uploadedVideoPreview.value
        //     if (!videoUrl) {
        //         alert("Đang tải file video. Vui lòng chờ")
        //         isLoading.value = false;
        //         return false
        //     }
        // }
        // else {
        //     alert("Vui lòng tải lên file video")
        //     isLoading.value = false
        //     return false
        // }
        // Xử lý upload audio nếu có file
        if (audioFile.value) {
            const audioMetadata = {
                contentType: 'audio/wav',
                customMetadata: {
                    fileName: audioFile.value.name,
                    fileSize: audioFile.value.size.toString(),
                    lastModified: audioFile.value.lastModified.toString(),
                    sampleRate: '44100',
                    channels: '1',
                    duration: recordingTime.value.toString()
                }
            };
            audioUrl = await firebase.uploadImage(audioFile.value, "audios/", audioMetadata);
        }
        if (!audioUrl) {
            alert("Vui lòng tải lên file âm thanh")
            isLoading.value = false
            return false
        }

        if (!isConfirm.value) {
            const hasDuration = await getDuration(videoUrl, audioUrl);
            if (!hasDuration) {
                isLoading.value = false;
                return false;
            }
        }
        const hasEnoughCredit = await checkCredit();
        if (!hasEnoughCredit) {
            isLoading.value = false;
            return; // Dừng nếu không đủ credit
        }
        resultVideoUrl.value = null;
        // Gửi request với URL (có thể là default hoặc đã upload)
        const response = await axios.post(route("lipsync.generate"), {
            duration: numberResult.value,
            videoUrl,
            audioUrl: audioS3.value
        });
        if (response.status === 200) {
            const lipSyncId = response.data.id;
            lipSyncIdRes.value = lipSyncId
            lipSyncResult.value = response.data.lipSync;
            startCheckingStatus(lipSyncId);
        } else {
            showBuyCreditModal();
            return false;
        }
        isConfirm.value = false
    } catch (error) {
        console.error("Lỗi:", error);
        alert("Có lỗi xảy ra. Vui lòng thử lại.");
        isLoading.value = false;
        isConfirm.value = false
    }
};
// api clone-voices api
const handleVoiceClone = async () => {
    try {
        const formData = new FormData();
        formData.append("name", window.location.hostname + "_" + user.value.id)
        formData.append("description", "Giọng nói thuyết trình mạnh mẽ")
        formData.append("files", audioFile_clonevoice.value);
        const response = await axios.post(route("ai-audio.clone-voice"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            }
        }
        );
        if (response.status === 200) {
            clonedVoices.value = [response.data.data]
            credits.value = response.data.data.credits;
            audioFile_clonevoice.value = ""
        }
        message.value = "";
    } catch (error) {
        console.error("Đã xảy ra lỗi khi gửi tin nhắn:", error.message);
    }
}
const isUploadAudio = ref(false)
const textToSpeech = async () => {
    if (!user.value.clone_voice) {
        isOpenWarrningUseVoice.value = true;
        return;
    }
    const hasEnoughCredit = await checkCreditV2({
        type: 'elevenlab',
        model: "Elevenlab",
        number_result: videoDescription.value?.length || null,
    });
    if (!hasEnoughCredit) {
        return; // Dừng nếu không đủ credit
    }
    // không có voice clone thì bắt buộc phải nhập nội dung thuyết minh hoặc ghi âm
    if (!clonedVoices.value[0]?.type) {
        if (!videoDescription.value || !audioFile_clonevoice.value) {
            alert('Hãy nhập nội dung thuyết minh cần nhái giọng. Sau đó bấm vào Tải âm thanh hoặc Ghi âm giọng nói của bạn trong 30s')
            return;
        }
    }
    if (!videoDescription.value) {
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
            text: videoDescription.value,
            isSaveDb: false,
            screen_id: 16
        })
        if (result.data.success) {
            const urlAudio = result.data.data;
            audioUrl.value = urlAudio;
            uploadedAudioPreview.value = urlAudio;
            audioS3.value = urlAudio;
            credits.value = result.data.credit;
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
    // const hasEnoughCredit = await checkCreditV2({
    //     type: 'elevenlab',
    //     model: "Elevenlab",
    //     number_result: videoDescription.value?.length || null,
    // });
    // if (!hasEnoughCredit) {
    //     return; // Dừng nếu không đủ credit
    // }
    if (!videoDescription.value.trim()) {
        alert('Hãy nhập nội dung để chuyển thành giọng nói');
        return;
    }
    try {
        isLoadingClonedVoices_google.value = true;
        const result = await axios.post(route("ai-audio.text-to-speech-google"), {
            text: videoDescription.value,
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
const fetchVoiceTypes = async () => {
    try {
        const response = await axios.get(route('voice-type.list'));
        clonedVoices.value = response.data.data;
    } catch (error) {
        console.error("Error fetching voice types:", error);
    }
};
const isCallQueue = ref(false)
const isCallTran = ref(false)
const startCheckingStatus = async (lipSyncId) => {
    // Clear any existing interval
    if (checkInterval.value) {
        clearInterval(checkInterval.value);
    }

    // Start new interval to check status every 5 seconds
    checkInterval.value = setInterval(async () => {
        if (isCallQueue.value || isCallTran.value) {
            return
        }
        if (isLoadDetail.value) {
            try {
                const response = await axios.get(route("lipsync.detailLipsync", { id: lipSyncId }));
                if (response.data.url && response.data.is_upload_audio) {
                    resultVideoUrl.value = {s3_url: response.data.url, id: lipSyncId};
                    isLoading.value = false;
                    isLoadDetail.value = false
                } else if (response.data.code === 500) {
                    isLoading.value = false;
                    isLoadDetail.value = false
                    alert(response.data.message)
                }
            } catch (error) {
                console.error("Lỗi khi kiểm tra trạng thái:", error);
                isLoading.value = false;
                isLoadDetail.value = false
            }
        } else if (!resultVideoUrl.value) {
            try {
                const statusResponse = await axios.post(route("lipsync.check-status", { id: lipSyncId }));
                if (statusResponse.data.status === 'COMPLETED') {
                    // Clear interval when completed
                    if (!isLoadDetail.value) {
                        resultVideoUrl.value = {s3_url: statusResponse.data.url, id: lipSyncId};
                        isLoading.value = false;
                        if (enableCaption.value) {
                            resultVideoUrl.value = "";
                            isLoading.value = true;
                            isCallTran.value = true
                            await toggleTranscription(lipSyncId, statusResponse.data.duration)
                            isCallTran.value = false
                            isLoading.value = false;
                        }
                        if (isUploadAudio.value) {
                            isCallQueue.value = true
                            resultVideoUrl.value = "";
                            isLoading.value = true;
                            const formData = new FormData();
                            formData.append('lip_sync_id', lipSyncId);
                            if (audioFile2.value) {
                                formData.append('audio_file', audioFile2.value);
                            }
                            const response = await axios.post(
                                route("lipsync.mergeAudioVideoLipsyncQueue"),
                                formData,
                                {
                                    headers: {
                                        "Content-Type": "multipart/form-data",
                                    },
                                }
                            );
                            isCallQueue.value = false
                            if (response.data.success) {
                                isLoadDetail.value = true
                            }
                        }
                    }
                } else if (statusResponse.data.code === 500) {
                    isLoading.value = false;
                    alert(statusResponse.data.message)
                }
            } catch (error) {
                console.error("Lỗi khi kiểm tra trạng thái:", error);
                isLoading.value = false;
            }
        }
    }, 20000)
};

const downloadVideo = () => {
    if (!resultVideoUrl.value) {
        alert("Chưa có video");
        return;
    }
    var url = route(("ai-video.downloadFile"), { url: resultVideoUrl.value, name: "video.mp4" })
    window.location.href = url
};

const handleCheckSieze = () => {
    if (window.innerWidth >= 1024) {
        isShowDes.value = true;
    } else {
        isShowDes.value = false;
    }
};
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
                    videoDescription.value = response?.data?.text || 'Vui lòng thử lại';
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
onMounted(() => {
    fetchSong();
    fetchVideo();
    handleCheckSieze();
    fetchVoiceTypes();
    window.addEventListener("resize", handleCheckSieze);

    // Reset về URL mặc định khi load trang
    uploadedVideoPreview.value = DEFAULT_VIDEO_URL;
    // uploadedAudioPreview.value = DEFAULT_AUDIO_URL;

    // Reset các file đã chọn
    videoFile.value = null;
    audioFile.value = null;

    // Reset recording state
    isRecording.value = false;
    recordingTime.value = 0;
    audioChunks.value = [];

    // Force media elements to reload
    nextTick(() => {
        if (videoPreview.value) {
            videoPreview.value.load();
        }
        if (audioPreview.value) {
            audioPreview.value.load();
        }
    });
});
onUnmounted(() => {
    if (uploadedVideoPreview.value && uploadedVideoPreview.value !== DEFAULT_VIDEO_URL) {
        URL.revokeObjectURL(uploadedVideoPreview.value);
    }
    if (uploadedAudioPreview.value && uploadedAudioPreview.value !== DEFAULT_AUDIO_URL) {
        URL.revokeObjectURL(uploadedAudioPreview.value);
    }
    if (mediaRecorder.value && mediaRecorder.value.state !== 'inactive') {
        mediaRecorder.value.stop();
    }
    if (checkInterval.value) {
        clearInterval(checkInterval.value);
    }
    window.removeEventListener("resize", handleCheckSieze);
});

const createPost = (imageUrl) => {
    if (imageUrl) {
        let image = {
            s3_url: imageUrl,
            type: 'video',
        };
        localStorage.setItem("aiImage", JSON.stringify(image));
        window.location.href = route('calendar');
    }
};

const prepareDeleteFile = (id) => {
    videoDeleteId.value = id;
    showConfirmModal.value = true;
};


const confirmDelete = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post(route("lipsync.delete", { id: videoDeleteId.value }));
        if (response.status === 200) window.location.reload();
    } catch (error) {
        console.error("Đã xảy ra lỗi khi xóa:", error.message);
    } finally {
        isLoading.value = false;
    }
};

const cancelDelete = () => {
    showConfirmModal.value = false;
};
const closeShareLink = () => {
    isShowShareLinkModal.value = false;
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
            audioFile.value = file;
            uploadedAudioPreview.value = URL.createObjectURL(file);
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
// Clean up on component unmount
onBeforeUnmount(() => {
    if (recordingInterval.value) {
        clearInterval(recordingInterval.value);
    }
    if (recordingMediaRecorder) {
        recordingMediaRecorder.stream.getTracks().forEach(track => track.stop());
    }
});

// Add new refs for recording clone voice
const isRecordVoice_clonevoice = ref(false);
const recordingInterval_clonevoice = ref(null);
let recordedAudioChunks_clonevoice = [];
let recordingMediaRecorder_clonevoice = null;
const audioFile_clonevoice = ref(null);
const uploadedAudioPreview_clonevoice = ref(null);
const recordingTime_clonevoice = ref(0);
const audioInput_clonevoice = ref(null);
const fileInputName_clonevoice = ref(null);
// Start recording function for clone voice
const startRecordingCloneVoice_clonevoice = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        recordingMediaRecorder_clonevoice = new MediaRecorder(stream);
        recordedAudioChunks_clonevoice = [];
        recordingMediaRecorder_clonevoice.ondataavailable = (e) => {
            if (e.data.size > 0) {
                recordedAudioChunks_clonevoice.push(e.data);
            }
        };
        recordingMediaRecorder_clonevoice.onstop = async () => {
            const audioBlob = new Blob(recordedAudioChunks_clonevoice, { type: 'audio/wav' });
            const file = new File([audioBlob], "recorded_audio.wav", { type: "audio/wav" });
            audioFile_clonevoice.value = file;
            uploadedAudioPreview_clonevoice.value = URL.createObjectURL(file);
        };
        recordingMediaRecorder_clonevoice.start();
        isRecordVoice_clonevoice.value = true;
        recordingTime_clonevoice.value = 0;
        // Start timer
        recordingInterval_clonevoice.value = setInterval(() => {
            recordingTime_clonevoice.value++;
            // Automatically stop after 5 minutes
            if (recordingTime_clonevoice.value >= 300) {
                stopRecorCloneVoice_clonevoice();
            }
        }, 1000);
    } catch (err) {
        console.error("Error accessing microphone:", err);
        toast.error("Không thể truy cập microphone");
    }
};

// Stop recording function for clone voice
const stopRecorCloneVoice_clonevoice = () => {
    if (recordingMediaRecorder_clonevoice && isRecordVoice_clonevoice.value) {
        recordingMediaRecorder_clonevoice.stop();
        recordingMediaRecorder_clonevoice.stream.getTracks().forEach(track => track.stop());
        clearInterval(recordingInterval_clonevoice.value);
        isRecordVoice_clonevoice.value = false;
        recordingTime_clonevoice.value = 0;
    }
};

// Handle audio upload for clone voice
const handleAudioUpload_clonevoice = async (event) => {
    const file = event.target.files[0];
    console.log("file name", file.name)
    if (file) {
        const allowedAudioTypes = ["audio/mpeg", "audio/wav", "audio/x-m4a"];
        if (!allowedAudioTypes.includes(file.type)) {
            alert("Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav. hoặc .m4a");
            audioInput_clonevoice.value.value = "";
            return;
        }
        var duration = await getVideoDuration(file)
        if (duration > 5 * 60) {
            alert("Vui lòng upload file audio có độ dài không quá 5 phút");
            audioInput_clonevoice.value.value = "";
            return;
        }
        // Revoke old URL if exists
        if (uploadedAudioPreview_clonevoice.value) {
            URL.revokeObjectURL(uploadedAudioPreview_clonevoice.value);
        }

        // Update with new file
        audioFile_clonevoice.value = file;
        uploadedAudioPreview_clonevoice.value = URL.createObjectURL(file);
    }
};

// Trigger audio upload for clone voice
const triggerAudioUpload_clonevoice = () => {
    audioInput_clonevoice.value.click();
};

</script>

<style scoped>
.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24AA69;
    border-radius: 50%;
    width: 120px;
    height: 120px;
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

.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24AA69;
    border-radius: 50%;
    width: 120px;
    height: 120px;
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
    width: 60px;
    height: 60px;
    border-radius: 50%;
    /* background: #50CDDD; */
    box-shadow: 0px 0px 80px #0084f9;
    position: absolute;
}

@keyframes pulseMic {
    0% {
        transform: scale(0);
        opacity: 0;
        border: 65px solid #0b3082;
    }

    50% {
        border: solid #a3ffc2;
        opacity: 0.8;
    }

    90% {
        transform: scale(3.2);
        opacity: 0.2;
        border: 3px solid #2e3cff;
    }

    100% {
        transform: scale(3.3);
        opacity: 0;
        border: 1px solid #7a89ff;
    }
}

.object-input-mic {
    display: flex;
    flex: 0 1 100%;
    justify-content: center;
    align-items: center;
    align-content: stretch;
}

.outline-input-mic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 8px solid #b5a4a4;
    animation: pulseMic 3s;
    animation-timing-function: ease-out;
    animation-iteration-count: infinite;
    position: absolute;
}

.button-input-mic {
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

.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #0084f9;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
}

.loaderVideo {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #0084f9;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
}

.showLoaderVideo {
    display: flex;
    justify-content: center;
    flex-direction: column-reverse;
    justify-content: center;
    margin-left: 50;
}

.loadVideoS3 {
    width: 50px;
    height: 50px;
    border: 10px solid #f3f3f3;
    border-top: 10px solid #0084f9
}

@media only screen and (max-width: 599px) {
    .desktop {
        display: none
    }
}

@media only screen and (min-width: 600px) and (max-width: 770px) {
    .mobile {
        display: none
    }
}

@media only screen and (min-width: 1400px) {
    .mobile {
        display: none
    }
}

@media only screen and (min-width: 770px) and (max-width: 1399px) {
    .desktop {
        display: none
    }
}

.text-name-audio {
    max-width: 100px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
</style>
