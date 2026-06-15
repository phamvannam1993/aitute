<template>
    <Head title="Âm Nhạc - AI 3 GỐC - Cộng đồng AI tử tế" />
    <form @submit.prevent="submit">
        <div class="flex flex-col justify-center items-start w-full gap-4 text-black">
            <div class="w-full bg-white lg:rounded-[35px] md:px-[80px] md:py-[20px] py-3 px-0 shadow-lg">
                <div class="flex flex-col p-1 justify-between min-h-[450px] rounded-lg w-full">
                    <div>
                        <div class="flex flex-col md:flex-row justify-between">
                            <div class="flex justify-start items-center gap-2 mb-2">
                                <h2 class="text-black font-bold text-[25px] md:text-[30px] mb-6">Tạo nhạc</h2>
                            </div>
                        </div>
                        <p class="text-xs lg:text-sm text-[#1E405A] font-bold mb-4">Thực hiện theo các bước sau:</p>
                        <div class="mt-6 mb-4"><Step :step="1" stepName="Tùy chọn phong cách âm nhạc" /></div>
                        <div class="flex flex-col lg:flex-row items-stretch gap-2">
                            <textarea v-model="musicStyle" rows="2" placeholder="Phong cách âm nhạc đã chọn ..." class="resize-none w-full border-[#D4D4D4] rounded-lg mt-3"> </textarea>
                            <div id="app" class="max-w-md mx-auto mt-5 flex justify-around relative">
                                <div>
                                    <div>
                                        <div class="flex flex-wrap items-center justify-between md:text-sm text-[11px] font-medium mb-2">
                                            <div class="flex gap-5">
                                                <span class="cursor-pointer" :class="{ 'text-[#FFA411]': activeTab === 'Instrument' && !showMusicOptions }" @click="changeTab('Instrument')">Nhạc cụ</span>
                                                <span class="cursor-pointer" :class="{ 'text-[#FFA411]': activeTab === 'Genre' && !showMusicOptions }" @click="changeTab('Genre')"> Thể loại </span>
                                                <span class="cursor-pointer" :class="{ 'text-[#FFA411]': activeTab === 'Music Type' && !showMusicOptions }" @click="changeTab('Music Type')"> Loại nhạc</span>
                                            </div>
                                            <div class="flex items-center px-2 py-1 bg-gray-200 rounded-lg hover:bg-gray-300 text-gray-700 gap-1">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3 7H4.77985C6.93172 7 8.00766 7 8.87921 7.45631C9.25172 7.65134 9.59114 7.90388 9.88499 8.20464C10.5725 8.90832 10.8817 9.93888 11.5 12C12.1183 14.0611 12.4275 15.0917 13.115 15.7954C13.4089 16.0961 13.7483 16.3487 14.1208 16.5437C14.9923 17 16.0683 17 18.2202 17H21M21 17L18 14M21 17L18 20" stroke="#33363F" stroke-width="2"/>
                                                <path d="M9.62793 14.4053C9.73735 14.7369 9.84537 15.0481 9.95898 15.333C10.1216 15.7409 10.3121 16.1471 10.5527 16.54C10.1963 16.8953 9.78883 17.1961 9.34277 17.4297C8.7464 17.7419 8.11525 17.8762 7.39746 17.9395C6.69663 18.0012 5.83848 18 4.78027 18H3V16H4.78027C5.87338 16 6.62889 15.9995 7.22168 15.9473C7.7973 15.8965 8.14001 15.8021 8.41504 15.6582C8.69441 15.5119 8.94955 15.3222 9.16992 15.0967C9.33125 14.9315 9.476 14.7223 9.62793 14.4053ZM22.4141 7L18.707 10.707L17.293 9.29297L18.5859 8H18.2197C17.1266 8 16.3711 8.00048 15.7783 8.05273C15.2027 8.10349 14.86 8.1979 14.585 8.3418C14.3056 8.48806 14.0505 8.67777 13.8301 8.90332C13.6688 9.06837 13.5229 9.27698 13.3711 9.59375C13.2618 9.26259 13.1545 8.95159 13.041 8.66699C12.8782 8.25864 12.6873 7.85232 12.4463 7.45898C12.8027 7.10373 13.2112 6.80384 13.6572 6.57031C14.2536 6.25814 14.8848 6.12383 15.6025 6.06055C16.3034 5.99876 17.1615 6 18.2197 6H18.5859L17.293 4.70703L18.707 3.29297L22.4141 7Z" fill="#33363F"/>
                                                </svg>
                                                <button @click="randomStyle">Ngẫu nhiên</button>
                                            </div>
                                        </div>
                                        <div v-if="activeTab === 'Instrument'" class="flex flex-wrap gap-2 mt-2 max-h-[240px] md:max-h-[340px] overflow-y-auto overscroll-contain p-3 border-[1px] rounded-lg border-[#D4D4D4]">
                                            <button class="px-2 py-1 md:px-4 md:py-2 rounded-lg hover:bg-ai3goc-sec hover:text-white md:text-sm text-xs" v-for="instrument in instruments" :key="instrument" @click="selectOption(instrument)"
                                                :class="selectedOptions.includes(instrument) ? 'bg-[#FFA411] text-white' : 'bg-[#DEDEDE] text-black'"
                                            >
                                                {{ instrument }}
                                            </button>
                                        </div>
                                        <div v-if="activeTab === 'Genre'" class="flex flex-wrap gap-2 mt-2 max-h-[240px] md:max-h-[340px] overflow-y-auto overscroll-contain p-3 border-[1px] rounded-lg border-[#D4D4D4]">
                                            <button class="px-4 py-2 rounded-lg hover:bg-ai3goc-sec hover:text-white md:text-sm text-xs" v-for="genre in genres" :key="genre" @click="selectOption(genre)"
                                                :class="selectedOptions.includes(genre) ? 'bg-[#FFA411] text-white' : 'bg-[#DEDEDE] text-black'"
                                            >
                                                {{ genre }}
                                            </button>
                                        </div>
                                        <div v-if="activeTab === 'Music Type'" class="flex flex-wrap gap-2 mt-2 max-h-[240px] md:max-h-[340px] overflow-y-auto overscroll-contain p-3 border-[1px] rounded-lg border-[#D4D4D4]">
                                            <button class="px-4 py-2 rounded-lg hover:bg-ai3goc-sec hover:text-white md:text-sm text-xs" v-for="type in musicTypes" :key="type" @click="selectOption(type)"
                                                :class="selectedOptions.includes(type) ? 'bg-[#FFA411] text-white' : 'bg-[#DEDEDE] text-black'"
                                            >
                                                {{ type }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Chọn File âm thanh - Hiển thị khi createFromExistingFile được chọn -->
                        <div class="mb-2 mt-3" v-if="createFromExistingFile">
                            <span class="ml-1 font-semibold my-2">Chọn File âm thanh</span>
                            <div class="flex my-3 items-center">
                                <div class="flex items-center space-x-4 rounded-lg">
                                    <label class="cursor-pointer inline-flex items-center p-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-blue-600 bg-white hover:bg-green-50">
                                        <img src="/assets/svgs/upload-icon.svg" class="pr-2" />
                                        <template v-if="file">
                                            {{ file.name || "" }}
                                        </template>
                                        <template v-else> Chọn tài liệu </template>
                                        <input type="file" class="hidden" @change="handleFileUpload" />
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full flex flex-row justify-between items-center icon px-0 gap-3 mt-[26px]">
                        <Step :step="2" stepName="Bấm vào đây" />
                        <button @click="sendData(step, index)" :disabled="isLoading" class="px-4 text-white py-[10px] rounded-xl text-center bg-[#1E405A] w-1/3 flex justify-center items-center">
                            <span v-if="!isLoading" class="text-xs lg:text-sm font-bold">Tạo bài hát</span>
                            <div v-else role="status" class="w-full flex items-center justify-center gap-2">
                                <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                                </svg>
                                <p>{{ loadingPercent }} %</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            <div class="w-full p-4 relative bg-white shadow-[0_3px_10px_rgb(0,0,0,0.2)] rounded-xl">
                <p class="text-black text-sm text-center italic font-thin mb-4">Hiển thị kết quả</p>

                <div class="bg-purple-100 p-6 rounded-xl min-h-[450px] shadow-lg max-w-xl mx-auto relative overflow-hidden">
                    <!-- Layer background với ảnh mờ -->
                    <div class="absolute inset-0 bg-center bg-cover opacity-60 filter blur-sm" :style="{ backgroundImage: `url(${resImg})` }"></div>

                    <!-- Nội dung chính -->
                    <div class="relative z-10">
                        <div v-if="songs.length !== 0" class="flex justify-center gap-4 items-center h-20">
                            <div
                                v-for="(song, index) in songs"
                                :key="index"
                                class="cursor-pointer transition-all duration-300"
                                :class="{
                                    'w-20 h-20 lg:w-24 lg:h-24 scale-110': selectedSong === song,
                                    'w-16 h-16 lg:w-20 lg:h-20 opacity-50': selectedSong !== song,
                                }"
                                @click="changePlayer(song)"
                            >
                                <img :src="song.imageUrl" alt="Album Art" class="w-full h-full object-cover rounded-lg" />
                            </div>
                        </div>
                        <img v-else :src="resImg" alt="Album Art" class="w-32 h-32 rounded-lg mx-auto mb-4" />
                        <div class="h-56 bg-slate-100 bg-opacity-70 rounded-lg p-4 my-6">
                            <p :class="['text-center font-semibold text-lg', { 'pr-[18px]': songs.length > 0 }]">Lyrics</p>
                            <p class="text-center text-sm text-gray-700 mb-4 h-40 overflow-y-auto" v-html="resLyric"></p>
                        </div>
                        <div class="mb-1">
                            <span class="text-white">{{ formatTime(currentTime) }} / {{ formatTime(duration) }}</span>
                        </div>
                        <div class="w-full h-2 bg-gray-300 rounded cursor-pointer mb-2" @click="resAudio && seekAudio($event)">
                            <div class="h-full bg-[#FFA411] rounded" :style="{ width: progress + '%' }"></div>
                        </div>
                        <div class="flex justify-center items-center">
                            <button
                                :disabled="!resAudio"
                                @click="togglePlay"
                                :class="{
                                    'bg-ai3goc-sec hover:bg-ai3goc-button-sec cursor-pointer': resAudio,
                                    'bg-gray-300 cursor-not-allowed': !resAudio,
                                }"
                                class="p-3 rounded-full text-white focus:outline-none"
                            >
                                <img class="w-8" v-if="isPlaying" src="/assets/img/ai_music/pause.svg" alt="pause" />
                                <img class="w-8" v-else src="/assets/img/ai_music/play.svg" alt="play" />
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="selectedSong" class="w-full flex flex-col md:flex-row justify-center gap-4 mt-4">
                    <div class="flex justify-center">
                        <button class="flex gap-2 items-center min-w-[100px] justify-center md:min-w-[150px] px-2 md:px-4 py-2 lg:py-3 bg-[#149CBE] text-white rounded-lg lg:rounded-2xl font-bold text-sm" @click="convertAudioToVideo(selectedSong.id)" :disabled="isLoadingVideo">
                            <svg width="30" height="25" viewBox="0 0 30 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.6705 2.45703H8.02025C4.9413 2.45703 2.44531 4.95302 2.44531 8.03197V16.9346C2.44531 20.0135 4.9413 22.5095 8.02025 22.5095H21.6705C24.7494 22.5095 27.2454 20.0135 27.2454 16.9346V8.03197C27.2454 4.95302 24.7494 2.45703 21.6705 2.45703Z" fill="#149BBE"/>
                            <path d="M21.6762 2.45102C23.1498 2.45102 24.5632 3.03643 25.6052 4.07846C26.6472 5.12049 27.2326 6.53378 27.2326 8.00743V16.91C27.2326 18.3837 26.6472 19.797 25.6052 20.839C24.5632 21.881 23.1498 22.4664 21.6762 22.4664H8.02593C6.55228 22.4664 5.13895 21.881 4.09692 20.839C3.05489 19.797 2.46952 18.3837 2.46952 16.91V8.02593C2.46952 6.55228 3.05489 5.13899 4.09692 4.09696C5.13895 3.05493 6.55228 2.46952 8.02593 2.46952H21.6762M21.6762 0H8.02593C5.89732 0 3.8559 0.84559 2.35074 2.35074C0.84559 3.8559 0 5.89732 0 8.02593V16.9286C0 19.0572 0.84559 21.0986 2.35074 22.6038C3.8559 24.1089 5.89732 24.9545 8.02593 24.9545H21.6762C23.8048 24.9545 25.8462 24.1089 27.3514 22.6038C28.8565 21.0986 29.7021 19.0572 29.7021 16.9286V8.02593C29.7021 5.89732 28.8565 3.8559 27.3514 2.35074C25.8462 0.84559 23.8048 0 21.6762 0Z" fill="white"/>
                            <path d="M19.8427 5.42578H9.85972C7.86505 5.42578 6.24805 7.04278 6.24805 9.03745V15.9151C6.24805 17.9097 7.86505 19.5267 9.85972 19.5267H19.8427C21.8374 19.5267 23.4544 17.9097 23.4544 15.9151V9.03745C23.4544 7.04278 21.8374 5.42578 19.8427 5.42578Z" fill="white"/>
                            <path d="M17.4178 13.5911L14.0531 15.5297C13.8735 15.6341 13.6695 15.6892 13.4618 15.6895C13.254 15.6897 13.0498 15.635 12.87 15.531C12.6902 15.427 12.541 15.2773 12.4376 15.0971C12.3342 14.9169 12.2803 14.7126 12.2813 14.5049V10.6215C12.2822 10.4145 12.3373 10.2113 12.4411 10.0322C12.5449 9.85312 12.6939 9.70435 12.8731 9.60069C13.0523 9.49704 13.2556 9.44211 13.4626 9.44141C13.6696 9.44071 13.8732 9.49426 14.0531 9.5967L17.4178 11.5414C17.5972 11.6457 17.7461 11.7952 17.8496 11.9751C17.9531 12.1549 18.0075 12.3588 18.0075 12.5663C18.0075 12.7738 17.9531 12.9776 17.8496 13.1575C17.7461 13.3373 17.5972 13.4869 17.4178 13.5911Z" fill="white"/>
                            <path d="M13.4654 9.43441C13.6823 9.42785 13.8965 9.48356 14.0828 9.59493L17.4475 11.5397C17.6269 11.6439 17.7758 11.7935 17.8793 11.9733C17.9828 12.1531 18.0372 12.357 18.0372 12.5645C18.0372 12.772 17.9828 12.9759 17.8793 13.1557C17.7758 13.3356 17.6269 13.4851 17.4475 13.5894L14.0828 15.5279C13.8965 15.6393 13.6823 15.695 13.4654 15.6885C13.3097 15.6885 13.1556 15.6578 13.0118 15.5982C12.868 15.5386 12.7373 15.4513 12.6273 15.3413C12.5172 15.2312 12.4298 15.1005 12.3703 14.9567C12.3107 14.8129 12.2801 14.6588 12.2801 14.5031V10.6198C12.2801 10.4641 12.3107 10.31 12.3703 10.1662C12.4298 10.0223 12.5172 9.89168 12.6273 9.78161C12.7373 9.67154 12.868 9.58419 13.0118 9.52462C13.1556 9.46505 13.3097 9.43441 13.4654 9.43441ZM13.4654 6.96489C12.4965 6.96653 11.5679 7.3521 10.8828 8.03717C10.1978 8.72224 9.81218 9.65093 9.81055 10.6198V14.5031C9.81083 15.1439 9.97956 15.7734 10.2999 16.3284C10.6202 16.8834 11.0808 17.3445 11.6356 17.6653C12.1903 17.9862 12.8197 18.1555 13.4605 18.1564C14.1013 18.1572 14.7311 17.9896 15.2867 17.6702L18.6514 15.7255C19.2065 15.4046 19.6673 14.9433 19.9877 14.3879C20.3081 13.8325 20.4768 13.2026 20.4768 12.5614C20.4768 11.9203 20.3081 11.2904 19.9877 10.735C19.6673 10.1796 19.2065 9.71831 18.6514 9.39737L15.2867 7.45879C14.7244 7.13203 14.0849 6.96152 13.4346 6.96489H13.4654Z" fill="#149BBE"/>
                            </svg>

                            <span class="font-medium text-xs md:text-sm">Chuyển bài hát thành Video</span>
                        </button>
                    </div>
                    <div class="flex gap-2 md:gap-4 justify-center">
                        <button @click="shareAIGeneratedMedia(musicId)" class="flex gap-2 items-center min-w-[100px] justify-center md:min-w-[150px] px-2 md:px-4 py-2 lg:py-3 bg-[#149CBE] text-white rounded-lg lg:rounded-2xl font-bold text-sm">
                            <svg width="22" height="24" viewBox="0 0 22 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.298 0.0713821C16.994 0.0253641 16.6871 0.00150444 16.3797 0H5.63141C4.13787 0 2.7055 0.588918 1.64941 1.63716C0.593327 2.68541 2.7227e-06 4.10714 2.7227e-06 5.58959V18.4104C-0.000724998 19.1446 0.144433 19.8718 0.427178 20.5503C0.709923 21.2287 1.12473 21.8453 1.64779 22.3644C2.17086 22.8836 2.79192 23.2953 3.47548 23.5759C4.15904 23.8566 4.89168 24.0007 5.63141 24H16.3797C17.1185 23.9993 17.8499 23.8541 18.5322 23.5728C19.2144 23.2915 19.8342 22.8796 20.3561 22.3606C20.878 21.8415 21.2918 21.2256 21.5739 20.5478C21.856 19.87 22.0007 19.1437 22 18.4104V5.57859C21.9997 4.25676 21.5271 2.97783 20.6661 1.96941C19.8051 0.960985 18.6116 0.288439 17.298 0.0713821ZM12.1202 7.05014C12.1207 6.8125 12.1915 6.58022 12.3238 6.38216C12.4561 6.1841 12.6441 6.02895 12.8644 5.93597C13.0847 5.843 13.3277 5.8163 13.5632 5.85916C13.7987 5.90202 14.0163 6.01255 14.1891 6.17706L18.1222 9.93277C18.2403 10.0459 18.3342 10.1815 18.3984 10.3315C18.4625 10.4815 18.4956 10.6428 18.4956 10.8058C18.4956 10.9687 18.4625 11.13 18.3984 11.28C18.3342 11.43 18.2403 11.5656 18.1222 11.6788L14.1891 15.4344C14.0163 15.5989 13.7987 15.7095 13.5632 15.7524C13.3277 15.7952 13.0847 15.7685 12.8644 15.6755C12.6441 15.5825 12.4561 15.4274 12.3238 15.2294C12.1915 15.0313 12.1207 14.799 12.1202 14.5614V7.05014ZM10.4607 7.79687C10.5646 7.79088 10.6686 7.80605 10.7664 7.84144C10.8642 7.87683 10.9537 7.93167 11.0293 8.00264C11.1049 8.0736 11.1651 8.15913 11.2062 8.25405C11.2473 8.34896 11.2685 8.45128 11.2683 8.55459V13.1009C11.2698 13.2976 11.1936 13.487 11.0561 13.6287C10.9186 13.7703 10.7307 13.8528 10.5326 13.8586C9.17175 13.8915 6.28969 14.2868 5.07269 16.8675C5.00277 17.0079 4.89037 17.1233 4.75126 17.1973C4.61215 17.2713 4.45329 17.3004 4.29674 17.2804C4.14018 17.2605 3.99376 17.1925 3.878 17.086C3.76225 16.9795 3.68288 16.8398 3.65098 16.6863C2.40079 10.5916 5.68117 8.04944 10.4441 7.79687H10.4607Z" fill="white"/>
                            </svg>

                            <span class="font-medium hover:scale-105 rounded-md text-xs md:text-sm">Chia sẻ</span>
                        </button>
                        <button class="flex gap-2 items-center min-w-[100px] justify-center md:min-w-[150px] px-2 md:px-4 py-2 lg:py-3 bg-[#149CBE] text-white rounded-lg lg:rounded-2xl font-bold text-sm" @click="downloadMusic()">
                            <svg width="22" height="25" viewBox="0 0 28 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M28 14.1283C28 14.8219 27.8612 15.5087 27.5916 16.1495C27.322 16.7903 26.9268 17.3726 26.4286 17.863C25.9304 18.3535 25.3389 18.7425 24.6879 19.008C24.037 19.2734 23.3394 19.41 22.6348 19.41H21.1379C21.1731 18.9175 21.0967 18.4233 20.9142 17.9634C20.7317 17.5034 20.4476 17.0892 20.0826 16.7506C19.7176 16.4119 19.2806 16.1574 18.8034 16.0053C18.3262 15.8532 17.8206 15.8074 17.3232 15.8713V11.4505C17.3232 10.568 16.9671 9.72167 16.3333 9.09765C15.6994 8.47363 14.8396 8.123 13.9431 8.123C13.0467 8.123 12.187 8.47363 11.5531 9.09765C10.9192 9.72167 10.5631 10.568 10.5631 11.4505V15.8713C10.0656 15.8053 9.55935 15.8496 9.08146 16.001C8.60357 16.1523 8.16612 16.4069 7.80101 16.7461C7.43589 17.0853 7.15239 17.5005 6.97121 17.9614C6.79003 18.4223 6.71577 18.9173 6.75384 19.41H5.25156C3.95396 19.383 2.71032 18.8937 1.75108 18.0331C0.79184 17.1724 0.182054 15.9985 0.0347015 14.7291C-0.112651 13.4597 0.212431 12.1807 0.949706 11.1292C1.68698 10.0777 2.78644 9.3249 4.04439 9.01036C3.68868 8.20802 3.54113 7.3312 3.61501 6.45881C3.6889 5.58641 3.98191 4.74582 4.46767 4.01255C4.95343 3.27928 5.61672 2.67635 6.39788 2.25802C7.17904 1.8397 8.05356 1.61907 8.94281 1.616C10.2864 1.61486 11.5804 2.115 12.5643 3.01566C13.4358 1.63632 14.808 0.635382 16.4022 0.216336C17.9964 -0.202711 19.6928 -0.00844379 21.1467 0.759656C22.6006 1.52776 23.7029 2.81206 24.2294 4.35149C24.756 5.89091 24.6673 7.56992 23.9815 9.0473C25.1266 9.33959 26.1415 9.99632 26.8683 10.9152C27.595 11.8341 27.9929 12.9638 28 14.1283Z" fill="white"/>
                            <path d="M19.0608 17.9756C18.9029 17.8076 18.7129 17.6721 18.5016 17.5766C18.2904 17.4812 18.062 17.4277 17.8297 17.4194C17.5974 17.411 17.3657 17.448 17.1479 17.528C16.9301 17.6081 16.7305 17.7296 16.5606 17.8858L15.7129 18.6569V11.4421C15.7129 10.9799 15.5264 10.5366 15.1944 10.2097C14.8623 9.88285 14.412 9.69922 13.9424 9.69922C13.4728 9.69922 13.0225 9.88285 12.6905 10.2097C12.3585 10.5366 12.1719 10.9799 12.1719 11.4421V18.6569L11.3242 17.8858C11.1562 17.7191 10.9557 17.5876 10.7348 17.4991C10.5139 17.4105 10.2771 17.3669 10.0386 17.3707C9.80006 17.3745 9.56484 17.4258 9.34695 17.5213C9.12907 17.6168 8.93301 17.7547 8.77061 17.9267C8.60821 18.0987 8.48281 18.3012 8.40189 18.5221C8.32096 18.743 8.28621 18.9777 8.29972 19.2121C8.31322 19.4466 8.3747 19.6759 8.48048 19.8863C8.58625 20.0968 8.73411 20.2841 8.91522 20.4369L12.7352 23.9386C12.7352 23.9386 12.7996 23.9809 12.8265 24.0073L12.9337 24.0971L12.9875 24.1288L13.1216 24.1974C13.158 24.2205 13.1955 24.2417 13.2342 24.2608H13.2932C13.3422 24.2798 13.3924 24.2957 13.4435 24.3083L13.5615 24.34H13.6259H13.7761H13.9102H13.9746H14.1088H14.259H14.3234L14.4414 24.3083C14.4925 24.2957 14.5427 24.2798 14.5916 24.2608H14.6506L14.7633 24.1974L14.8974 24.1288L14.9511 24.0971L15.0584 24.0073C15.0584 24.0073 15.1227 23.965 15.1496 23.9386L18.9696 20.4369C19.1402 20.2814 19.278 20.0944 19.3749 19.8864C19.4719 19.6784 19.5262 19.4536 19.5347 19.2249C19.5431 18.9962 19.5056 18.7681 19.4243 18.5537C19.343 18.3393 19.2195 18.1428 19.0608 17.9756Z" fill="white"/>
                            </svg>
                            <span class="font-medium hover:scale-105 rounded-md text-xs md:text-sm">Tải xuống</span>
                        </button>
                        <div class="cursor-pointer flex gap-2 items-center min-w-[100px] justify-center md:min-w-[150px] px-2 md:px-4 py-2 lg:py-3 bg-[#149CBE] text-white rounded-lg lg:rounded-2xl font-bold text-sm">
                            <a :href="route('text-to-song.history')" target="_blank" class="text-xs md:text-sm">Lịch sử</a>
                        </div>
                    </div>
                </div>
                <div v-if="isLoadingVideo" class="flex flex-col items-center justify-center mt-4">
                    <div class="w-10 h-10 border-4 border-t-blue-500 border-gray-300 rounded-full animate-spin"></div>
                    <p class="mt-2 text-lg font-medium">{{ loadingPercent }}%</p>
                </div>

                <!-- Hiển thị video vừa tạo -->
                <div v-if="currentVideoUrl" class="w-full max-w-xl mx-auto mt-4">
                    <video controls class="w-full rounded-lg" :src="currentVideoUrl"></video>
                </div>
                <div v-if="resAudio" class="flex flex-wrap items-center justify-center w-full gap-4 mt-4">
                    <div class="flex justify-end md:gap-4 gap-2">
                        <button v-if="currentVideoUrl" class="flex gap-2 items-center min-w-[100px] justify-center md:min-w-[150px] px-2 md:px-4 py-2 lg:py-3 bg-[#149CBE] text-white rounded-lg lg:rounded-2xl font-bold text-sm" @click="downloadVideo">
                           <svg width="22" height="25" viewBox="0 0 28 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M28 14.1283C28 14.8219 27.8612 15.5087 27.5916 16.1495C27.322 16.7903 26.9268 17.3726 26.4286 17.863C25.9304 18.3535 25.3389 18.7425 24.6879 19.008C24.037 19.2734 23.3394 19.41 22.6348 19.41H21.1379C21.1731 18.9175 21.0967 18.4233 20.9142 17.9634C20.7317 17.5034 20.4476 17.0892 20.0826 16.7506C19.7176 16.4119 19.2806 16.1574 18.8034 16.0053C18.3262 15.8532 17.8206 15.8074 17.3232 15.8713V11.4505C17.3232 10.568 16.9671 9.72167 16.3333 9.09765C15.6994 8.47363 14.8396 8.123 13.9431 8.123C13.0467 8.123 12.187 8.47363 11.5531 9.09765C10.9192 9.72167 10.5631 10.568 10.5631 11.4505V15.8713C10.0656 15.8053 9.55935 15.8496 9.08146 16.001C8.60357 16.1523 8.16612 16.4069 7.80101 16.7461C7.43589 17.0853 7.15239 17.5005 6.97121 17.9614C6.79003 18.4223 6.71577 18.9173 6.75384 19.41H5.25156C3.95396 19.383 2.71032 18.8937 1.75108 18.0331C0.79184 17.1724 0.182054 15.9985 0.0347015 14.7291C-0.112651 13.4597 0.212431 12.1807 0.949706 11.1292C1.68698 10.0777 2.78644 9.3249 4.04439 9.01036C3.68868 8.20802 3.54113 7.3312 3.61501 6.45881C3.6889 5.58641 3.98191 4.74582 4.46767 4.01255C4.95343 3.27928 5.61672 2.67635 6.39788 2.25802C7.17904 1.8397 8.05356 1.61907 8.94281 1.616C10.2864 1.61486 11.5804 2.115 12.5643 3.01566C13.4358 1.63632 14.808 0.635382 16.4022 0.216336C17.9964 -0.202711 19.6928 -0.00844379 21.1467 0.759656C22.6006 1.52776 23.7029 2.81206 24.2294 4.35149C24.756 5.89091 24.6673 7.56992 23.9815 9.0473C25.1266 9.33959 26.1415 9.99632 26.8683 10.9152C27.595 11.8341 27.9929 12.9638 28 14.1283Z" fill="white"/>
                            <path d="M19.0608 17.9756C18.9029 17.8076 18.7129 17.6721 18.5016 17.5766C18.2904 17.4812 18.062 17.4277 17.8297 17.4194C17.5974 17.411 17.3657 17.448 17.1479 17.528C16.9301 17.6081 16.7305 17.7296 16.5606 17.8858L15.7129 18.6569V11.4421C15.7129 10.9799 15.5264 10.5366 15.1944 10.2097C14.8623 9.88285 14.412 9.69922 13.9424 9.69922C13.4728 9.69922 13.0225 9.88285 12.6905 10.2097C12.3585 10.5366 12.1719 10.9799 12.1719 11.4421V18.6569L11.3242 17.8858C11.1562 17.7191 10.9557 17.5876 10.7348 17.4991C10.5139 17.4105 10.2771 17.3669 10.0386 17.3707C9.80006 17.3745 9.56484 17.4258 9.34695 17.5213C9.12907 17.6168 8.93301 17.7547 8.77061 17.9267C8.60821 18.0987 8.48281 18.3012 8.40189 18.5221C8.32096 18.743 8.28621 18.9777 8.29972 19.2121C8.31322 19.4466 8.3747 19.6759 8.48048 19.8863C8.58625 20.0968 8.73411 20.2841 8.91522 20.4369L12.7352 23.9386C12.7352 23.9386 12.7996 23.9809 12.8265 24.0073L12.9337 24.0971L12.9875 24.1288L13.1216 24.1974C13.158 24.2205 13.1955 24.2417 13.2342 24.2608H13.2932C13.3422 24.2798 13.3924 24.2957 13.4435 24.3083L13.5615 24.34H13.6259H13.7761H13.9102H13.9746H14.1088H14.259H14.3234L14.4414 24.3083C14.4925 24.2957 14.5427 24.2798 14.5916 24.2608H14.6506L14.7633 24.1974L14.8974 24.1288L14.9511 24.0971L15.0584 24.0073C15.0584 24.0073 15.1227 23.965 15.1496 23.9386L18.9696 20.4369C19.1402 20.2814 19.278 20.0944 19.3749 19.8864C19.4719 19.6784 19.5262 19.4536 19.5347 19.2249C19.5431 18.9962 19.5056 18.7681 19.4243 18.5537C19.343 18.3393 19.2195 18.1428 19.0608 17.9756Z" fill="white"/>
                            </svg>
                            <span class="font-medium hover:scale-105 rounded-md text-xs md:text-sm">Tải xuống</span>
                        </button>
                    </div>
                </div>
                <slot v-if="props.isShowButtonConfirm"></slot>
            </div>
        </div>
    </form>
    <div v-if="showConfirmModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-30">
        <div class="bg-[url('../../public/assets/img/bg-modal.png')] bg-cover bg-center rounded-lg p-6 shadow-lg absolute z-[51] md:w-[500px] md:h-[346px] md:rounded-[41px] flex flex-col justify-start items-center">
            <img class="w-40" src="/assets/img/model-delete-icon.png" />
            <h3 class="text-lg font-semibold text-black">Cảnh báo !</h3>
            <p class="text-black">Bạn có chắc chắn muốn xóa nội dung này không?</p>
            <div class="flex justify-center mt-4 p-2">
                <button @click="cancelDelete" :disabled="isDeleteLoading" class="px-4 md:px-11 py-2 border-[1px] border-ai3goc-primary text-black font-semibold rounded-[15px] mr-2">Huỷ</button>
                <button @click="confirmDelete" :disabled="isDeleteLoading" class="px-4 md:px-11 py-2 bg-ai3goc-sec text-white font-semibold rounded-[15px]">
                    <span v-if="!isDeleteLoading">Xoá</span>
                    <div v-else role="status">
                        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </button>
            </div>
        </div>
        <div class="fixed inset-0 bg-black opacity-50"></div>
    </div>

    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink" align-items="items-center">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade" />
    <CreditModal :showCreditPopup="showCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleCancel" />
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel" :currentCredit="pageProps.auth.user.credit || 0" :additionalCredit="additionalCredit" />
</template>

<script setup>
import DescriptionSuggestionMusic from "@/Components/AIVirtual/DescriptionSuggestionMusic.vue";
import FormShareLink from "@/Components/FormShareLink.vue";
import Modal from "@/Components/Modal.vue";
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import "suneditor/dist/css/suneditor.min.css"; // Import CSS
import { computed, onBeforeMount, onMounted, reactive, ref, watch } from "vue";
import { toast } from "vue3-toastify";
import CreditModal from "../../../Components/ModalBuyCredit/Index.vue";
import CreditBuyModal from "../../../Components/ModalBuyCredit/BuyCredit.vue";
import "1llest-waveform-vue/lib/style.css";
import PopupAff from "@/Components/PopupAff.vue";
import Step from "@/Components/Step.vue";

const emit = defineEmits(["saveGenerationResult"]);
const props = defineProps({
    credits: Number,
    content: String,
    titleMusic: String,
    isShowButtonConfirm: {
        type: Boolean,
        default: false,
    },
});

const breadcrumbsData = [{ text: "Bài hát", link: "text-to-song.index" }];
const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);
const isShowShareLinkModal = ref(false);
const showAffKeyPopup = ref(false);
const shareLink = ref(null);
const credits = ref(props.credits);
const ai_assistant = ref(props.ai_assistant);
let messageIdToDelete = ref(null);
const isLoading = ref(false);
const isDeleteLoading = ref(false);
const history = ref([]);
const content = ref("");
const musicStyle = ref("");
const file = ref(null);
const createFromExistingFile = ref(false);
const currentLyricIndex = ref(0);
const resAudio = ref(null);
const resLyric = ref('Đây là một công cụ AI tạo nhạc mạnh mẽ. Bạn có thể tạo một bài hát bằng cách nhập một câu mô tả, chẳng hạn như "hãy tạo cho tôi một bài hát rock về tình yêu." Hoặc nhập lời bài hát cụ thể. Sau đó, bấm vào nút soạn để tạo bài hát chỉ với một cú nhấp chuột.');
const lyricsData = ref("");
const resImg = ref('/assets/img/ai3goc/banner/login.svg');
const audio = new Audio(resAudio.value);
const share_audio1 = ref("");
const share_audio2 = ref("");
const musicIds = ref([]);
const musicId = ref("");
const isPlaying = ref(false);
const currentTime = ref(0);
const duration = ref(0);
const progress = ref(0);
const showMusicOptions = ref(false);
const instruments = ref(["Piano", "Guitar", "Electric guitar", "Violin", "Flute", "Drum", "Acoustic guitar", "Synth", "Drums", "Bass", "Saxophone", "Trumpet", "Cello", "Synthesizer", "Ukulele", "Harp", "Accordion", "Harmonica", "Tuba", "Trombone", "French Horn", "Recorder", "Xylophone", "Marimba", "Glockenspiel", "Vibraphone", "Steel Drums", "Conga", "Bongo", "Triangle", "Tambourine", "Maracas", "Cymbals", "Timpani", "Cajon", "Djembe", "Sitar", "Guzheng", "Pipa", "Erhu", "Dizi", "Sheng", "Hulusi", "Xun", "Banjo", "Mandolin", "Clarinet", "Oboe", "Viola", "Organ", "Electric Bass", "Electric Piano", "MIDI Keyboard", "Drum Machine", "Sequencer", "Mixer", "Audio Interface"]);
const genres = ref(["Pop", "Rock", "Rap", "Metal", "Electronic", "Hip Hop", "Trap", "Country", "Jazz", "Blues", "Classical", "Folk", "Soul", "Funk", "Reggae", "Punk", "R&B", "Disco", "Indie", "Alternative", "World Music", "Latin", "New Age", "Experimental", "Ambient", "Post-Rock", "EDM", "Hardcore", "Industrial", "Gothic", "Rap Rock", "Fusion Jazz", "Bluegrass", "Folk Rock", "Heavy Metal", "Death Metal", "Black Metal", "Progressive Metal", "Alternative Rock", "Garage Rock", "Punk Rock", "Grunge", "Blues Rock", "Hard Rock", "Psychedelic Rock", "Progressive Rock", "Glam Rock", "Electronic Rock", "Alternative Metal", "Nu Metal", "Industrial Metal", "Rap Metal", "Gangsta Rap", "Alternative Hip Hop", "Jazz Rap", "House", "Techno", "Drum and Bass", "Dubstep", "Ambient House", "Dub", "Acid House", "Cool Jazz", "Bebop", "Free Jazz", "Swing Jazz", "Blues Jazz", "Country Blues", "Chicago Blues", "Baroque", "Romantic", "Modern Classical", "Minimalism", "Neoclassical", "Opera", "Alternative Country", "Contemporary Folk", "Traditional Folk", "Celtic", "African", "Latin Jazz", "Salsa", "Samba", "Bossa Nova", "Reggae Rock", "Ska", "Funk Rock", "New Wave", "Post-Punk", "Gothic Rock", "Industrial Rock", "Noise Rock", "Dream Pop", "Shoegaze", "Post-Metal", "Post-Hardcore", "Emo", "Math Rock", "Atmospheric Black Metal", "Symphonic Metal", "Folk Metal", "Viking Metal", "Electronicore", "Trap Metal", "Post-Britpop", "New Psychedelia", "Space Rock", "Art Rock", "New Romanticism", "Synthpop", "Future Bass", "Vaporwave", "Retrowave", "Electropop", "Tropical House", "Deep House", "Tech House", "Minimal Techno", "Hard Techno", "Industrial Techno", "Liquid Drum and Bass", "Neurofunk", "Breakbeat", "Big Beat", "Trap (EDM)", "Future House", "Post-Dubstep", "Ambient Dubstep", "Experimental Electronic", "Hyperpop", "8-bit Music", "Synthwave", "Ballad", "Dance", "J-pop", "Orchestral", "Psychedelic", "Progressive", "K-pop", "Pop Rock", "Cantonese", "Gospel", "Phonk"]);
const musicTypes = ref(["Upbeat", "Melodic", "Dark", "Epic", "Bass", "Emotional", "Acoustic", "Cheerful", "Sad", "Passionate", "Calm", "Excited", "Melancholic", "Mysterious", "Tense", "Relaxed", "Anxious", "Angry", "Gentle", "Intense", "Dreamy", "Joyful", "Depressed", "Hopeful", "Fearful", "Humorous", "Solemn", "Energetic", "Gloomy", "Warm", "Cold", "Profound", "Sorrowful", "Comforting", "Lonely", "Nostalgic", "Uplifting", "Contemplative", "Thrilling", "Peaceful", "Frenzied", "Elegant", "Rugged", "Sweet", "Moody", "Exuberant", "Worried", "Content", "Lost", "Confident", "Sensitive", "Strong", "Vulnerable", "Enthusiastic", "Indifferent", "Sympathetic", "Doubtful", "Determined", "Confused", "Serene", "Restless", "Delightful", "Heavy", "Light", "Stirring", "Comfortable", "Uneasy", "Sacred", "Secular", "Transcendent", "Simple", "Elaborate", "Sunny", "Bright", "Hazy", "Clear", "Bewildered", "Cozy", "Distant", "Intimate", "Majestic", "Subtle", "Overwhelming", "Ethereal", "Grounded", "Radical", "Conservative", "Avant-garde", "Modern", "Futuristic"]);
const selectedOptions = ref([]);
const activeTab = ref("Instrument");
const activeTabOptions = ref("Instrument");

const popupMessage = ref("Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!");
const acceptUpgrade = ref(true);

const sunoTaskId = ref(null);

const formatLyrics = (lyrics) => {
    return lyrics.replace(/\[([A-Za-z]+)\]/g, "<p><strong>[$1]</strong></p>").replace(/\n/g, "<br>");
};
const parseLyrics = (lyricsString) => {
    const lines = lyricsString.split("\n").filter((line) => line.trim() !== "");

    let time;
    if (duration.value < 170) {
        time = 0;
    } else if (duration.value >= 170 && duration.value <= 190) {
        time = 5;
    } else if (duration.value > 190) {
        time = 10;
    } else {
        time = 0;
    }

    return lines.map((line) => {
        const isLyric = !line.match(/^\[.*\]$/);

        const parsedLine = {
            time: time,
            text: line.trim(),
        };

        if (isLyric) {
            time += 5;
        }

        return parsedLine;
    });
};
onMounted(() => {
    if (props.content) {
        content.value = props.content;
    }
});
watch(props.content, (newContent) => {
    content.value = newContent;
});

const changeTab = (tab) => {
    if (!showMusicOptions.value) {
        activeTab.value = tab;
    } else {
        activeTabOptions.value = tab;
    }
};

const selectOption = (option) => {
    if (selectedOptions.value.includes(option)) {
        return;
    }
    musicStyle.value += option + ",";
    const index = selectedOptions.value.indexOf(option);

    if (index === -1) {
        selectedOptions.value.push(option);
    }
};

const randomStyle = () => {
    // random each options
    const randomInstrument = instruments.value[Math.floor(Math.random() * instruments.value.length)];
    const randomGenre = genres.value[Math.floor(Math.random() * genres.value.length)];
    const randomMusicType = musicTypes.value[Math.floor(Math.random() * musicTypes.value.length)];
    selectedOptions.value = [
      randomInstrument,
      randomGenre,
      randomMusicType
    ];

    musicStyle.value = `${randomInstrument} , ${randomGenre} , ${randomMusicType} `;
};

const togglePlay = () => {
    if (isPlaying.value) {
        audio.pause();
    } else {
        audio.play();
    }
};

const videoUrls = ref({});
const selectedSong = ref(null);

const currentVideoUrl = computed(() => {
    return selectedSong.value && videoUrls.value[selectedSong.value.id] ? videoUrls.value[selectedSong.value.id] : null;
});

const changePlayer = (song) => {
    if (musicId.value === share_audio1.value) {
        musicId.value = share_audio2.value;
    } else if (musicId.value === share_audio2.value) {
        musicId.value = share_audio1.value;
    }
    emit("saveGenerationResult", song);
    selectedSong.value = song;
    resImg.value = song.imageUrl;
    resLyric.value = formattedText(song.prompt);
    resAudio.value = song.audioUrl;

    audio.src = resAudio.value;
    audio.load();

    isPlaying.value = false;

    audio.onplay = null;
    audio.onpause = null;
    progress.value = 0;
    audio.addEventListener("loadedmetadata", () => {
        duration.value = audio.duration;
    });
};

const videoUrl = ref(null);
const isLoadingVideo = ref(false);
const convertAudioToVideo = async (audioId) => {
    isLoadingVideo.value = true;
    try {
        const hasEnoughCredit = await checkCredit();
        if (!hasEnoughCredit) {
            isLoadingVideo.value = false;
            return;
        }

        if (!sunoTaskId.value || !audioId) {
            toast.warn("Thiếu thông tin audioId hoặc taskId của audio");
            isLoadingVideo.value = false;
            return;
        }

        loadingPercent.value = 0;
        const callBackUrl = route("text-to-song.convert-audio-to-video");

        const simulateLoading = () => {
            if (loadingPercent.value < 99) {
                const increment = Math.floor(Math.random() * 4) + 2;
                loadingPercent.value = Math.min(loadingPercent.value + increment, 99);
                setTimeout(simulateLoading, 8000);
            }
        };
        simulateLoading();

        const dataRequest = {
            audioId: audioId,
            taskId: sunoTaskId.value,
            callBackUrl: callBackUrl,
        };

        const response = await fetch(route("text-to-song.convert-audio-to-video"), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(dataRequest),
        });

        if (!response.ok) {
            const dataResponse = await response.json();
            if (response.status === 500) {
                toast.warning("Hệ thống đang quá tải, vui lòng thử lại sau.");
                console.error("Chi tiết lỗi => ", dataResponse.message);
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            videoUrls.value[selectedSong.value.id] = data.data.videoUrl;
            emit("saveGenerationResult", data.data.videoUrl);
            toast.success("Chuyển đổi video thành công!");
        } else {
            toast.warning("Hệ thống đang quá tải, vui lòng thử lại sau.");
        }
    } catch (error) {
        if (error.message.includes("504")) {
            toast.warning("Hệ thống đang bảo trì, vui lòng thử lại sau.");
        } else {
            console.error("Lỗi xảy ra:", error);
        }
    } finally {
        isLoadingVideo.value = false;
    }
};

const downloadVideo = () => {
    var url = route(("ai-video.downloadFile"), { url: currentVideoUrl.value, name: "video.mp4" })
    window.location.href = url
};

const formattedText = (originalText) => {
    return originalText
        .replace(/\*\*/g, "") // Thay thế tất cả dấu ** thành rỗng
        .replace(/(Verse \d+|Chorus|Outro):/g, "<br><strong>$1:</strong><br>") // Xuống dòng trước và sau các tiêu đề như Verse 1, Chorus, Outro
        .replace(/: /g, ":<br>") // Thêm xuống dòng sau dấu `: `
        .replace(/, /g, ",<br>") // Thêm xuống dòng sau dấu phẩy
        .replace(/\n/g, "<br>"); // Thay thế xuống dòng thành <br>
};

const getGeneratedPrompt = async (data) => {
    const dataRequest = {
        promt: data,
        musicTypes: musicStyle.value,
    };
    const response = await fetch(route("text-to-song.summary"), {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(dataRequest),
    });

    if (response.ok) {
        const data = await response.json();
        if (data.success) {
            return data.data;
        }
    } else {
        toast.error("Hệ thống đang quá tải vui lòng thử lại sau");
        return false;
    }
};

const seekAudio = (event) => {
    const progressBar = event.currentTarget;
    const clickX = event.offsetX;
    const progressBarWidth = progressBar.clientWidth;
    const seekTime = (clickX / progressBarWidth) * audio.duration;
    audio.currentTime = seekTime;
};
watch(resAudio, (newAudioSrc) => {
    audio.src = newAudioSrc;
    audio.load();
});

onMounted(() => {
    audio.addEventListener("loadedmetadata", () => {
        duration.value = audio.duration;
    });
    audio.addEventListener("timeupdate", () => {
        currentTime.value = audio.currentTime;
        progress.value = (audio.currentTime / audio.duration) * 100;

        //     lyricsData.value.forEach((line, index) => {
        //     if (audio.currentTime >= line.time) {
        //       currentLyricIndex.value = index;
        //     }
        //   });
    });
    audio.addEventListener("play", () => {
        isPlaying.value = true;
    });
    audio.addEventListener("pause", () => {
        isPlaying.value = false;
    });
});
const shareAIGeneratedMedia = async (id) => {
    try {
        console.log(musicId.value);
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: musicId.value,
            share_linkable_type: "Music",
        });
        shareLink.value = route("share-link.show", { token: response.data.data.link_token });
        openShareLink();
    } catch (error) {
        toast.error("Chia sẻ ảnh thất bại");
    }
};
const downloadMusic = async () => {
    if (resAudio.value) {
        try {
            // dow file from s3
            const response = await fetch(resAudio.value);
            if (!response.ok) throw new Error("Không thể tải file từ S3.");

            // Blob
            const blob = await response.blob();
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.href = url;
            link.download = "audio-file.mp3";
            link.click();

            // Hủy object URL sau khi tải xuống
            URL.revokeObjectURL(url);
        } catch (error) {
            console.error("Lỗi tải xuống:", error);
            toast.warning("Không thể tải tệp âm thanh xuống!");
        }
    } else {
        toast.warning("Không có tệp âm thanh để tải xuống!");
    }
};
const formatTime = (time) => {
    const minutes = Math.floor(time / 60);
    const seconds = Math.floor(time % 60)
        .toString()
        .padStart(2, "0");
    return `${minutes}:${seconds}`;
};

const openShareLink = () => {
    isShowShareLinkModal.value = true;
};

const closeShareLink = () => {
    isShowShareLinkModal.value = false;
};

const showConfirmModal = ref(false);
const confirmDelete = async () => {
    let response;
    isDeleteLoading.value = true;

    response = await axios.post(route("ai-text.delete-text", { id: messageIdToDelete.value }), { id: messageIdToDelete.value });
    if (response.status === 200) {
        const index = history.value.data.findIndex((item) => item.id === messageIdToDelete.value);
        if (index !== -1) {
            history.value.data.splice(index, 1);
        }
        toast.success("Xóa thành công");
    }
    isDeleteLoading.value = false;
    showConfirmModal.value = false;
};

const cancelDelete = () => {
    showConfirmModal.value = false;
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

let isUpdatingFromAPI = false;
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
        formData.append("type", "music_to_text");

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
const songs = ref([]);
const loadingPercent = ref(0);

const sendData = async () => {
    isLoading.value = true;
    try {
        isUpdatingFromAPI = true;
        const hasEnoughCredit = await checkCredit();
        if (!hasEnoughCredit) {
            isLoading.value = false;
            return;
        }

        if (!content.value) {
            toast.warn("Vui lòng mô tả nội dung bài hát");
            isLoading.value = false;
            return;
        }
        loadingPercent.value = 0;

        const callBackUrl = route("text-to-song.generate-song");
        const simulateLoading = () => {
            if (loadingPercent.value < 99) {
                const increment = Math.floor(Math.random() * 4) + 2;
                loadingPercent.value = Math.min(loadingPercent.value + increment, 99);
                setTimeout(simulateLoading, 8000);
            }
        };
        simulateLoading();
        resAudio.value = null;

        const dataRequest = {
            prompt: content.value, // Mô tả nội dung bài hát hoặc lời bài hát
            style: musicStyle.value, // Ví dụ: "Classical"
            title: props.titleMusic, // Ví dụ: "Peaceful Piano Meditation"
            customMode: true,
            instrumental: false, // false nếu muốn có lời; true nếu chỉ cần nhạc nền
            model: "V4",
            callBackUrl: callBackUrl,
        };

        const response = await fetch(route("text-to-song.generate-song"), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(dataRequest),
        });

        if (!response.ok) {
            const dataResponse = await response.json();
            if (response.status == 500) {
                toast.warning("Hệ thống đang quá tải, vui lòng thử lại sau.");
                console.log("Chi tiết lỗi => ", dataResponse.message);
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            sunoTaskId.value = data.data.taskId;
            songs.value = data.data.sunoData;
            changePlayer(songs.value[0]);
            setTimeout(async () => {
                saveData(data.data.sunoData);
            }, 1000);
        } else {
            toast.warning("Hệ thống đang quá tải, vui lòng thử lại sau.");
        }
    } catch (error) {
        if (error.message.includes("504")) {
            toast.warning("Hệ thống đang bảo trì, vui lòng thử lại sau.");
        } else {
            console.error("Lỗi xảy ra:", error);
        }
    } finally {
        isLoading.value = false;
        isUpdatingFromAPI = false;
    }
};

// const sendData = async () => {
//     isLoading.value = true;
//     try {
//         isUpdatingFromAPI = true;
//         const hasEnoughCredit = await checkCredit();
//         if (!hasEnoughCredit) {
//             isLoading.value = false;
//             return;
//         }

//         if (!content.value) {
//             toast.warn('Vui lòng mô tả nội dung bài hát');
//             isLoading.value = false;
//             return;
//         }
//         loadingPercent.value = 0;

//         const simulateLoading = () => {
//             if (loadingPercent.value < 99) {
//                 const increment = Math.floor(Math.random() * 4) + 2;
//                 loadingPercent.value = Math.min(loadingPercent.value + increment, 99);
//                 setTimeout(simulateLoading, 8000);
//             }
//         };
//         simulateLoading();
//         resAudio.value = null;
//         const lyricsData = content.value;
//         const summaryPromt = await getGeneratedPrompt(props.titleMusic)
//         const dataRequest = {
//             content : summaryPromt,
//             lyrics : lyricsData,
//             musicTitle : props.titleMusic,
//         }

//         const response = await fetch(route("text-to-song.song"), {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/json',
//                 },
//                 body: JSON.stringify(dataRequest),
//         });

//         if (!response.ok) {
//             const dataResponse = await response.json();
//                 if(response.status == 500) {
//                     toast.warning("Hệ thống đang quá tải, vui lòng thử lại sau.")
//                     console.log("Chi tiết lỗi => ", dataResponse.message)
//                 }
//                 throw new Error(`HTTP error! status: ${response.status}`);
//         }

//         const data = await response.json();

//         if (data.success){
//             songs.value = data.data;
//             changePlayer(songs.value[0]);
//             setTimeout(async() =>{
//                 saveData(data.data);
//             }, 1000);
//         } else {
//             toast.warning("Hệ thống đang quá tải, vui lòng thử lại sau.")
//         }
//     } catch (error) {
//         if (error.message.includes('504')) {
//             toast.warning("Hệ thống đang bảo trì, vui lòng thử lại sau.")
//         } else {
//             console.error("Lỗi xảy ra:", error);
//         }
//     } finally {
//         isLoading.value = false;
//         isUpdatingFromAPI = false;
//     }
// };

const saveData = async (data) => {
    try {
        console.log(data);
        const songTitle = data[0].title;
        const response = await axios.post(route("text-to-song.store"), {
            prompt: songTitle,
            data: data,
        });
        history.value.push(response.data.response);
        if (response.data.success) {
            toast.success("Lưu dữ liệu thành công");
            musicIds.value = response.data.response;
            share_audio1.value = response.data.response[0].id;
            share_audio2.value = response.data.response[1].id;
            musicId.value = response.data.response[0].id;
            // return response.data.response.data;
        } else {
            toast.success("Hệ thống đang quá tải, vui lòng thử lại sao.");
        }
        return {};
    } catch (error) {
        console.log(error);
        return {};
    }
};

const handleFileUpload = (event) => {
    const selectedFile = event.target.files[0];

    if (!selectedFile) {
        return;
    }

    // Chỉ định các định dạng file âm thanh hợp lệ
    const validFileTypes = ["audio/mpeg", "audio/wav", "audio/ogg"];

    if (!validFileTypes.includes(selectedFile.type)) {
        toast.error("File phải có định dạng: mp3, wav, ogg.");
        file.value = null;
        return;
    }

    // Giới hạn kích thước file không quá 10MB
    if (selectedFile.size > 10 * 1024 * 1024) {
        toast.error("File không được vượt quá 10MB.");
        file.value = null;
        return;
    }

    // Nếu tất cả điều kiện được đáp ứng, lưu file vào biến `file`
    file.value = selectedFile || null;
};

watch(createFromExistingFile, (newValue) => {
    if (!newValue) {
        file.value = null; // Đặt lại file về null khi checkbox bị bỏ chọn
    }
});

const isRecording = ref(false);
const audioBlob = ref(null);
const audioUrl = ref(null);
let mediaRecorder = null;
let audioChunks = [];
const audioResult = ref({});
let device = ref(null);
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
                    content.value = response?.data?.text || "Vui lòng thử lại";
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
</script>

<style scoped>
.p-editor {
    --p-editor-content-color: #000000;
    /* Đặt màu mặc định cho văn bản */
}

.fade-scale-enter-active,
.fade-scale-leave-active {
    transition: opacity 0.5s ease, transform 0.5s ease;
}

.fade-scale-enter-from,
.fade-scale-leave-to {
    opacity: 0;
    transform: scale(0.95);
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
</style>
