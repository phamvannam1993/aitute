<template>
    <div class="flex bg-white items-center w-full pb-2 justify-center border-b shadow-[0px_0px_0px_1px_rgba(0,0,0,0.06),0px_1px_1px_-0.5px_rgba(0,0,0,0.06),0px_3px_3px_-1.5px_rgba(0,0,0,0.06),_0px_6px_6px_-3px_rgba(0,0,0,0.06),0px_12px_12px_-6px_rgba(0,0,0,0.06),0px_24px_24px_-12px_rgba(0,0,0,0.06)]">
        <div class="w-full flex items-center justify-center gap-1 xl:gap-3 " :class="isSidebarOpen ? 'lg:w-[90%]' : 'xl:w-3/4'">
            <!-- <a href="/"><img src="/assets/img/ai3goc/logo/image-text.svg" alt="Orion" class="h-7 md:h-[52px] w-auto" /></a> -->
            <div class="items-center py-5" :class="isHome ? 'relative' : ''">
                <!-- <a  href="/">
                    <svg class="size-6 md:size-12" width="40" height="41" viewBox="0 0 40 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 15.5L16.9303 6.22086C18.7359 4.81654 21.2642 4.81654 23.0697 6.22086L35 15.5V33.8333C35 34.7174 34.6488 35.5652 34.0237 36.1903C33.3986 36.8155 32.5507 37.1666 31.6667 37.1666H8.33333C7.44928 37.1666 6.60143 36.8155 5.97631 36.1903C5.35119 35.5652 5 34.7174 5 33.8333V15.5Z" stroke="black" stroke-width="3.33333" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15 37.1667V20.5H25V37.1667" stroke="black" stroke-width="3.33333" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a> -->
                <span v-if="isHome" class="absolute bottom-0 transform w-full h-2 bg-[#FFA328] rounded-full"/>
            </div>
            <div class="w-full flex md:grid grid-cols-5 items-center gap-1 md:gap-4 py-2">
                <a @click="showConfirmFineTune" :class="headerActiveElement === 'thcn' ? 'relative' : ''" class="bg-ai3goc-primary py-2 text-white flex items-center md:px-4  justify-center rounded-xl md:rounded-[38px] min-h-[75px] lg:min-h-[70px] cursor-pointer">
                    <p class="text-sm md:text-base lg:text-xl font-bold text-center h-fullcursor-pointer">THƯƠNG HIỆU <br/> CÁ NHÂN</p>
                    <span v-if="headerActiveElement === 'thcn'" class="absolute bottom-[-10px] w-3/4 transform h-2 bg-[#FFA328] rounded-full"/>
                </a>
<!--                <div :class="isSaleAi ? 'relative' : ''" class="bg-ai3goc-primary text-white flex items-center justify-center rounded-xl md:rounded-[38px] min-h-[75px] lg:min-h-[70px] w-14 md:w-full" @click="showMarketing">-->
<!--                    <p class="text-sm md:text-base lg:text-xl font-bold w-full text-center px-0.5 py-2 md:py-5 cursor-pointer">AI BÁN HÀNG</p>-->
<!--                    <span v-if="isSaleAi" class="absolute bottom-[-10px] w-3/4 transform h-2 bg-[#FFA328] rounded-full"/>-->
<!--                </div>-->
                <div  :class="isSaleAi ? 'relative' : ''" class="bg-ai3goc-primary text-white flex items-center justify-center rounded-xl md:rounded-[38px] min-h-[75px] lg:min-h-[70px] w-14 md:w-full" @click="toggleShowDrop(2)">
                    <p class="text-sm md:text-base lg:text-xl flex justify-center items-center gap-1 font-bold w-full text-center px-0.5 py-2 cursor-pointer">
                        <span>AI BÁN HÀNG</span>
<!--                        <svg :class="showOtherDropdown && 'rotate-180'" class="size-2" width="6" height="5" viewBox="0 0 6 5" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--                            <path d="M3 5L5.59808 0.5H0.401924L3 5Z" fill="white" />-->
<!--                        </svg>-->
                        <span v-if="isSaleAi" class="absolute bottom-[-10px] w-3/4 transform h-2 bg-[#FFA328] rounded-full"/>
                    </p>
                </div>
                <!-- <div :class="headerActiveElement === 'media' ? 'relative' : ''" class="bg-ai3goc-primary text-white flex items-center justify-center rounded-xl md:rounded-[38px] min-h-[75px] md:min-h-[70px]" @click.stop="showDropdown = !showDropdown">
                    <p class="text-sm md:text-base md:text-xl flex justify-center items-center gap-1 font-bold w-full text-center px-0.5 py-2 md:py-5 cursor-pointer">
                        <span>MEDIA</span>
                        <svg :class="showDropdown && 'rotate-180'" class="size-2" width="6" height="5" viewBox="0 0 6 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 5L5.59808 0.5H0.401924L3 5Z" fill="white" />
                        </svg>
                        <span v-if="headerActiveElement === 'media'" class="absolute bottom-[-10px] w-3/4 transform h-2 bg-[#FFA328] rounded-full"/>
                    </p>
                </div> -->
                <div :class="[
                    'bg-ai3goc-primary text-white flex relative items-center justify-center rounded-xl md:rounded-[38px] min-h-[75px] lg:min-h-[70px] z-50',
                    headerActiveElement === 'other' ? 'relative' : ''
                ]" @click="toggleShowDrop(1)">
                    <p class="text-sm md:text-base lg:text-xl flex justify-center items-center gap-1 font-bold w-full text-center px-0.5 py-2 cursor-pointer">
                        <span>CHĂM SÓC <br/> KHÁCH HÀNG</span>
                        <svg :class="showOtherDropdown && 'rotate-180'" class="size-2" width="6" height="5" viewBox="0 0 6 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 5L5.59808 0.5H0.401924L3 5Z" fill="white" />
                        </svg>
                        <span v-if="headerActiveElement === 'cskh'" class="absolute bottom-[-10px] w-3/4 transform h-2 bg-[#FFA328] rounded-full"/>
                    </p>
                </div>
                <a :href="route('google-search.index')" :class="headerActiveElement === 'google-search' ? 'relative' : ''" class="bg-ai3goc-primary text-white flex items-center md:px-4 px-1 justify-center rounded-xl md:rounded-[38px] min-h-[75px] lg:min-h-[70px] z-50">
                    <p class="text-sm md:text-base lg:text-xl font-bold text-center h-fullcursor-pointer">TÌM KIẾM <br/> KHÁCH HÀNG</p>
                    <span v-if="headerActiveElement === 'google-search'" class="absolute bottom-[-10px] w-3/4 transform h-2 bg-[#FFA328] rounded-full"/>
                </a>
                <div :class="[
                    'bg-ai3goc-primary text-white flex relative items-center justify-center rounded-xl md:rounded-[38px] min-h-[75px] lg:min-h-[70px] z-50',
                    headerActiveElement === 'other' ? 'relative' : ''
                ]" @click="toggleShowDrop(0)">
                    <p class="text-sm md:text-base lg:text-xl flex justify-center items-center gap-1 font-bold w-full text-center px-0.5 py-2 md:py-5 cursor-pointer">
                        <span>AI KHÁC</span>
                        <svg :class="showOtherDropdown && 'rotate-180'" class="size-2" width="6" height="5" viewBox="0 0 6 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 5L5.59808 0.5H0.401924L3 5Z" fill="white" />
                        </svg>
                        <span v-if="headerActiveElement === 'other'" class="absolute bottom-[-10px] w-3/4 transform h-2 bg-[#FFA328] rounded-full"/>
                    </p>
                    <div v-if="showOtherDropdown" ref="otherDropdown" class="absolute -left-[500%] md:left-0 border-2 border-[#E6E6E6] top-[65px] md:top-[80px] bg-white rounded-3xl shadow-lg md:w-[150%] text-black z-50">
                        <ul class="p-2" @click.stop>
                            <a @click.stop="showDropdown = !showDropdown; showOtherDropdown = !showOtherDropdown" class="flex items-center cursor-pointer gap-2 px-4 py-2 hover:bg-orion-orange/10 rounded-3xl">
                                <img src="/assets/img/orion/icon/menu/selfie.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                                <p>Media</p>
                            </a>
                            <!-- <a @click="showConfirmFineTune" class="flex items-center cursor-pointer gap-2 px-4 py-2 hover:bg-orion-orange/10 rounded-3xl">
                                <img src="/assets/img/orion/icon/menu/selfie.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                                <p>Xây dựng thương hiệu cá nhân</p>
                            </a> -->
                            <button @click="showDataDropdown = !showDataDropdown"  class="flex items-center cursor-pointer px-4 py-2 rounded-2xl">
                                <img src="/assets/img/orion/icon/menu/data.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                                <div>
                                    <div class="flex justify-between items-start gap-2">
                                        <p>Phễu data chủ động</p>
                                        <svg :class="showDataDropdown && 'rotate-180'" class="size-2" width="6" height="5" viewBox="0 0 6 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 5L5.59808 0.5H0.401924L3 5Z" fill="black" />
                                        </svg>
                                    </div>
                                    <div v-if="showDataDropdown" class="flex flex-col items-start mt-2">
                                        <a :href="route('ai-chat.index')" class="pl-4 py-3 w-full hover:bg-orion-orange/10 rounded-3xl text-left">Trải nghiệm</a>
                                        <a :href="route('customer-care.index')" class="pl-4 py-3 w-full hover:bg-orion-orange/10 rounded-3xl text-left">Liên kết Social</a>
                                    </div>
                                </div>
                            </button>
                            <a :href="route('home.others-ai')" class="flex items-center cursor-pointer px-4 py-2 hover:bg-orion-orange/10 rounded-2xl">
                                <img src="/assets/img/orion/icon/menu/aichat.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                                <p>Các trợ lý</p>
                            </a>
                        </ul>
                    </div>
                </div>

                <!-- <div class="flex w-1/4 items-center justify-center"  @click.stop="showDropdown = !showDropdown">
                    <p class="text-[10px] md:text-[16px] lg:text-[14px] xl:text-[16px] 2xl:text-[18px]  font-bold uppercase text-center hover:text-ai3goc-primary cursor-pointer" :class="showDropdownImage ? 'text-ai3goc-primary' : 'text-black'">
                        GIẢI PHÁP TRUYỀN THÔNG
                                </p>
                </div>
                <a class="flex w-1/4 items-center justify-center" :href="route('customer-care.index')">
                    <p class="text-[10px] md:text-[16px] lg:text-[14px] xl:text-[16px] 2xl:text-[18px] text-black font-bold uppercase text-center hover:text-ai3goc-primary cursor-pointer">
                        CHĂM SÓC KHÁCH HÀNG
                                </p>
                </a>
                <a class="flex w-1/4 items-center justify-center" :href="route('google-search.index')">
                    <p class="text-[10px] md:text-[16px] lg:text-[14px] xl:text-[16px] 2xl:text-[18px] text-black font-bold uppercase text-center hover:text-ai3goc-primary cursor-pointer">
                        TÌM KIẾM KH
                                </p>
                </a> -->
            </div>
        </div>
    </div>
    <div v-if="showDropdown" ref="dropdown" class="w-full max-sm:px-2 flex justify-center items-center text-center py-2">
        <div class="w-full lg:w-3/4 flex justify-between gap-0.5 md:gap-1.5 py-2 border-b-2 border-gray-300 z-50">
            <div @click="toggleDropdown('image', $event)" :class="showDropdownImage || isActiveTab[0] ? 'bg-[#FFA411] text-white' : 'text-white bg-[#E3E3E3]'" class="relative w-1/3 lg:w-[30%] py-1 lg:py-2 rounded-full text-xs sm:text-xl flex items-center justify-center cursor-pointer">
                <div class="flex items-center justify-between gap-1 md:gap-2 w-full px-2 py-2 md:py-0 md:px-4">
                    <span class="font-semibold text-black flex-1 text-center"><span class="md:inline hidden">AI</span> Hình ảnh</span>
                    <img v-if="showDropdownImage" src="/assets/img/ai3goc/icon/dropdown-open.svg" class="w-1.5 md:w-3" alt="" />
                    <img v-else src="/assets/img/ai3goc/icon/dropdown-close.svg" class="w-1.5 md:w-3" alt="" />
                </div>
                <div v-if="showDropdownImage" class="absolute left-0 border-2 border-[#E6E6E6] top-[50px] bg-white rounded-3xl shadow-lg w-[240%] lg:w-full text-black z-50">
                    <ul class="py-2" @click.stop>
                        <a :href="route('ai-image.index')" :class="isActiveChild[0] ? 'bg-orion-orange' : ''" class="flex items-center text-start cursor-pointer px-4 py-2 hover:bg-orion-orange/10 rounded-2xl">
                            <img src="/assets/img/orion/icon/menu/t2i.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                            <p>Tạo ảnh từ câu lệnh (prompt)</p>
                        </a>
                        <a :href="route('ai-background.indexV2')" :class="isActiveChild[1] ? 'bg-orion-orange' : ''" class="flex items-center text-start cursor-pointer px-4 py-2 hover:bg-orion-orange/10 rounded-2xl">
                            <img src="/assets/img/orion/icon/menu/bg-img.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                            <p>Tạo hình nền A.I sản phẩm</p>
                        </a>
                        <a :href="route('ai-image.faceSwap')" :class="isActiveChild[2] ? 'bg-orion-orange' : ''" class="flex items-center text-start cursor-pointer px-4 py-2 hover:bg-orion-orange/10 rounded-2xl">
                            <img src="/assets/img/orion/icon/menu/change-face.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                            <p>Đổi khuôn mặt</p>
                        </a>
                    </ul>
                </div>
            </div>
            <div @click="toggleDropdown('video', $event)" :class="showDropdownVideo || isActiveTab[1] ? 'bg-[#FFA411] text-white' : 'text-white bg-[#E3E3E3]'" class="relative w-1/3 lg:w-[30%] py-1 lg:py-2 rounded-full text-xs sm:text-xl flex items-center justify-center cursor-pointer">
                <div class="flex items-center justify-between gap-1 md:gap-2 w-full px-2 py-2 md:py-0 md:px-4">
                    <span class="font-semibold text-black flex-1 text-center"><span class="md:inline hidden">AI</span> Video</span>
                    <img v-if="showDropdownVideo" src="/assets/img/ai3goc/icon/dropdown-open.svg" class="w-1.5 md:w-3" alt="" />
                    <img v-else src="/assets/img/ai3goc/icon/dropdown-close.svg" class="w-1.5 md:w-3" alt="" />
                </div>
                <div v-if="showDropdownVideo" class="absolute lg:left-0 border-2 border-[#E6E6E6] top-[50px] bg-white rounded-3xl shadow-lg w-[240%] lg:w-full text-black z-50">
                    <ul class="py-2" @click.stop>
                        <a :href="route('mc-virtual.index')" :class="isActiveChild[3] ? 'bg-orion-orange' : ''" class="flex items-center cursor-pointer text-start px-4 py-2 hover:bg-orion-orange/10 rounded-2xl">
                            <img src="/assets/img/orion/icon/menu/mc.svg" alt="lipsync" class="w-[16px] md:w-[24px] md:h-[20px] mr-2" />
                            <p>Video MC ảo</p>
                        </a>
                        <a :href="route('lipsync.lipsync')" :class="isActiveChild[4] ? 'bg-orion-orange' : ''" class="flex items-center cursor-pointer text-start px-4 py-2 hover:bg-orion-orange/10 rounded-2xl">
                            <img src="/assets/img/orion/icon/menu/video-avatar.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                            <p>Video Avatar</p>
                        </a>
                        <a :href="route('ai-video.img2Video')" :class="isActiveChild[5] ? 'bg-orion-orange' : ''" class="flex items-center cursor-pointer text-start px-4 py-2 hover:bg-orion-orange/10 rounded-2xl">
                            <img src="/assets/img/orion/icon/menu/ai-image.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                            <p>Video từ hình ảnh</p>
                        </a>
                        <a :href="route('ai-video.url-to-video.create')" :class="isActiveChild[6] ? 'bg-orion-orange' : ''" class="flex items-center cursor-pointer text-start px-4 py-2 hover:bg-orion-orange/10 rounded-2xl">
                            <img src="/assets/img/orion/icon/menu/link2v.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                            <p>Video từ link sản phẩm</p>
                        </a>
                        <a :href="route('ai-video.index')" :class="isActiveChild[7] ? 'bg-orion-orange' : ''" class="flex items-center cursor-pointer text-start px-4 py-2 hover:bg-orion-orange/10 rounded-2xl">
                            <img src="/assets/img/orion/icon/menu/film.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                            <p>Video từ văn bản</p>
                        </a>
                        <!-- <a :href="route('ai-video.url-to-video.create')"
                            class="flex items-center cursor-pointer text-start px-4 py-2 hover:bg-orion-orange/10 rounded-2xl">
                            <img src="/assets/img/orion/icon/menu/link2v.svg" alt="lipsync"
                                class="w-[16px] md:w-[24px] mr-2" />
                            <p>Video từ link/text</p>
                        </a> -->
                        <a :href="route('text-to-video.index')" :class="isActiveChild[8] ? 'bg-orion-orange' : ''" class="flex items-center cursor-pointer text-start px-4 py-2 hover:bg-orion-orange/10 rounded-2xl">
                            <img src="/assets/img/orion/icon/menu/personalization-video.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                            <p>Video cá nhân hóa nội dung</p>
                        </a>
                        <a :href="route('ai-video.history', { type: 'all' })" :class="isActiveChild[9] ? 'bg-orion-orange' : ''" class="flex items-center cursor-pointer text-start px-4 py-2 hover:bg-orion-orange/10 rounded-2xl">
                            <img src="/assets/img/orion/icon/menu/merge-video.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                            <p>Ghép video</p>
                        </a>
                    </ul>
                </div>
            </div>
            <div @click="toggleDropdown('music', $event)" :class="showDropdownMusic || isActiveTab[2] ? 'bg-[#FFA411] text-white' : 'text-white bg-[#E3E3E3]'" class="relative w-1/3 lg:w-[30%] py-1 lg:py-2 rounded-full text-xs sm:text-xl flex items-center justify-center cursor-pointer">
                <div class="flex items-center justify-between gap-1 md:gap-2 w-full px-2 py-2 md:py-0 md:px-4">
                    <span class="font-semibold text-black flex-1 text-center"><span class="md:inline hidden">AI</span> Âm thanh</span>
                    <img v-if="showDropdownMusic" src="/assets/img/ai3goc/icon/dropdown-open.svg" class="w-1.5 md:w-3" alt="" />
                    <img v-else src="/assets/img/ai3goc/icon/dropdown-close.svg" class="w-1.5 md:w-3" alt="" />
                </div>
                <div v-if="showDropdownMusic" class="absolute right-0 border-2 border-[#E6E6E6] top-[50px] bg-white rounded-3xl shadow-lg w-[240%] lg:w-full text-black z-50">
                    <ul class="py-2" @click.stop>
                        <a :href="route('ai-business.music-for-business')" :class="isActiveChild[10] ? 'bg-orion-orange' : ''" class="flex items-center cursor-pointer text-start px-4 py-2 hover:bg-orion-orange/10 mx-4 2ounded-2xl">
                            <img src="/assets/img/orion/icon/menu/music.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                            <p>Tạo bài hát từ văn bản</p>
                        </a>
                        <a :href="route('ai-audio.index')" :class="isActiveChild[11] ? 'bg-orion-orange' : ''" class="flex items-center cursor-pointer text-start px-4 py-2 hover:bg-orion-orange/10 mx-4 2ounded-2xl">
                            <img src="/assets/img/orion/icon/menu/voice.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                            <p>Giọng nói A.I</p>
                        </a>
                    </ul>
                </div>
            </div>
            <div :class="showDropdownAssistant || isActiveTab[3] ? 'bg-[#FFA411] text-white' : 'text-white bg-[#E3E3E3]'" class="relative w-1/3 lg:w-[30%] py-1 lg:py-2 rounded-full text-xs sm:text-xl flex items-center justify-center cursor-pointer">
                <div class="flex items-center justify-between gap-1 md:gap-2 w-full px-2 py-2 md:py-0 md:px-4" @click="redirectUrl(route('home.others-ai'))">
                    <span class="font-semibold text-black flex-1 text-center">Trợ lý</span>
                </div>
                <div v-if="showDropdownAssistant" class="absolute right-0 border-2 border-[#E6E6E6] top-[50px] bg-white rounded-3xl shadow-lg w-[240%] lg:w-full text-black z-50">
                    <ul class="py-2" @click.stop>
                        <button @click="showDataDropdown = !showDataDropdown"  class="flex items-start cursor-pointer px-4 py-2 rounded-2xl">
                            <img src="/assets/img/orion/icon/menu/data.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2 mt-1.5" />
                            <div>
                                <div class="flex justify-between items-center gap-2 text-start">
                                    <p>Phễu data khách hàng</p>
                                    <svg :class="showDataDropdown && 'rotate-180'" class="size-2" width="6" height="5" viewBox="0 0 6 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 5L5.59808 0.5H0.401924L3 5Z" fill="black" />
                                    </svg>
                                </div>
                                <div v-if="showDataDropdown" class="flex flex-col items-start mt-2">
                                    <a :href="route('ai-chat.index')" class="pl-4 py-3 w-full hover:bg-orion-orange/10 rounded-3xl text-left">Trải nghiệm</a>
                                    <a :href="route('customer-care.index')" class="pl-4 py-3 w-full hover:bg-orion-orange/10 rounded-3xl text-left">Liên kết Social</a>
                                </div>
                            </div>
                        </button>
                        <a :href="route('home.others-ai')" class="flex items-center cursor-pointer px-4 py-2 hover:bg-orion-orange/10 rounded-2xl">
                            <img src="/assets/img/orion/icon/menu/aichat.svg" alt="lipsync" class="w-[16px] md:w-[24px] mr-2" />
                            <p>Các trợ lý</p>
                        </a>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div v-if="showDropdownCSKH" ref="dropdownCSKH" class="w-full max-sm:px-2 flex justify-center items-center text-center py-2">
        <div class="w-full lg:w-3/4 flex justify-between gap-0.5 md:gap-1.5 py-2 border-b-2 border-gray-300 z-50">
            <div :class="isActiveCSKHTab[0] ? 'bg-[#FFA411] text-white' : 'text-white bg-[#E3E3E3]'" class="relative w-1/3 lg:w-[30%] py-1 lg:py-2 rounded-full text-xs sm:text-xl flex items-center justify-center cursor-pointer" @click="redirectUrl(route('ai-chat.index'))">
                <div class="flex items-center justify-between gap-1 md:gap-2 w-full px-2 py-2 md:py-0 md:px-4">
                    <span class="font-semibold text-black flex-1 text-center">Trải nghiệm</span>
                </div>
            </div>
            <div :class="isActiveCSKHTab[1] ? 'bg-[#FFA411] text-white' : 'text-white bg-[#E3E3E3]'" class="relative w-1/3 lg:w-[30%] py-1 lg:py-2 rounded-full text-xs sm:text-xl flex items-center justify-center cursor-pointer" @click="redirectUrl(route('customer-care.index'))">
                <div class="flex items-center justify-between gap-1 md:gap-2 w-full px-2 py-2 md:py-0 md:px-4">
                    <span class="font-semibold text-black flex-1 text-center">Liên kết Social</span>
                </div>
            </div>
             <div :class="isActiveCSKHTab[2] ? 'bg-[#FFA411] text-white' : 'text-white bg-[#E3E3E3]'" class="relative w-1/3 lg:w-[30%] py-1 lg:py-2 rounded-full text-xs sm:text-xl flex items-center justify-center cursor-pointer" @click="redirectUrl(route('management.customer.index'))">
                <div class="flex items-center justify-between gap-1 md:gap-2 w-full px-2 py-2 md:py-0 md:px-4">
                    <span class="font-semibold text-black flex-1 text-center">Báo cáo</span>
                </div>
            </div>
        </div>
    </div>
    <div v-if="showDropdownAiBanHang" class="w-full max-sm:px-2 flex justify-center items-center text-center py-2">
        <div class="w-full lg:w-3/4 flex justify-between gap-0.5 md:gap-1.5 py-2 z-50">
            <div :class="isActiveAiBanHangTab[0] ? 'bg-[#FFA411] text-white' : 'text-white bg-[#E3E3E3]'" class="relative w-1/3 lg:w-[30%] py-1 lg:py-2 rounded-full text-xs sm:text-xl flex items-center justify-center cursor-pointer" @click="redirectUrl(route('home.index') + '?mode=advanced&view=project&type=bvqc')">
                <div class="flex items-center justify-between gap-1 md:gap-2 w-full px-2 py-2 md:py-0 md:px-4">
                    <span class="font-semibold text-black flex-1 text-center">Bài viết Quảng cáo</span>
                </div>
            </div>
            <div :class="isActiveAiBanHangTab[1] ? 'bg-[#FFA411] text-white' : 'text-white bg-[#E3E3E3]'" class="relative w-1/3 lg:w-[30%] py-1 lg:py-2 rounded-full text-xs sm:text-xl flex items-center justify-center cursor-pointer" @click="showDropdownCampaign = !showDropdownCampaign">
                <div class="flex items-center justify-between gap-1 md:gap-2 w-full px-2 py-2 md:py-0 md:px-4">
                    <span class="font-semibold text-black flex-1 text-center">Chiến dịch Quảng cáo</span>
                </div>
                <div v-if="showDropdownCampaign" class="absolute border-2 border-[#E6E6E6] top-[50px] bg-white rounded-3xl shadow-lg w-[180px] lg:w-[280px] text-black z-50">
                <ul class="py-2">
                    <a :href="route('home.index') + '?mode=advanced&view=project&type=cdqc&day=7'" class="flex items-center cursor-pointer px-4 py-2 hover:bg-[#FFA411] mx-2 rounded-2xl">
                    <p>Chiến dịch 7 ngày</p></a>
                    <a :href="route('home.index') + '?mode=advanced&view=project&type=cdqc&day=14'" class="flex items-center cursor-pointer px-4 py-2 hover:bg-[#FFA411] mx-2 rounded-2xl">
                    <p>Chiến dịch 14 ngày</p></a>
                    <a :href="route('home.index') + '?mode=advanced&view=project&type=cdqc&day=21'" class="flex items-center cursor-pointer px-4 py-2 hover:bg-[#FFA411] mx-2 rounded-2xl">
                    <p>Chiến dịch 21 ngày</p></a>
                    <a :href="route('home.index') + '?mode=advanced&view=project&type=cdqc&day=28'" class="flex items-center cursor-pointer px-4 py-2 hover:bg-[#FFA411] mx-2 rounded-2xl">
                    <p>Chiến dịch 28 ngày</p></a>
                </ul>
                </div>
            </div>
             <div :class="isActiveAiBanHangTab[2] ? 'bg-[#FFA411] text-white' : 'text-white bg-[#E3E3E3]'" class="relative w-1/3 lg:w-[30%] py-1 lg:py-2 rounded-full text-xs sm:text-xl flex items-center justify-center cursor-pointer" @click="redirectUrl(route('home.index') + '?mode=advanced&view=project&type=tqc')">
                <div class="flex items-center justify-between gap-1 md:gap-2 w-full px-2 py-2 md:py-0 md:px-4">
                    <span class="font-semibold text-black flex-1 text-center">Thơ Quảng cáo</span>
                </div>
            </div>
            <div :class="isActiveAiBanHangTab[3] ? 'bg-[#FFA411] text-white' : 'text-white bg-[#E3E3E3]'" class="relative w-1/3 lg:w-[30%] py-1 lg:py-2 rounded-full text-xs sm:text-xl flex items-center justify-center cursor-pointer" @click="redirectUrl(route('home.index') + '?mode=advanced&view=project&type=anqc')">
                <div class="flex items-center justify-between gap-1 md:gap-2 w-full px-2 py-2 md:py-0 md:px-4">
                    <span class="font-semibold text-black flex-1 text-center">Âm nhạc Quảng cáo</span>
                </div>
            </div>
        </div>
    </div>
    <ComingSoon v-if="isShowPopup" @close="close" />
    <ComingSoonNew v-if="isShowPopupNew" @closeNew="closeNew" />
    <ModelEnterUrl :isShow="showModelEnterUrl" :handleIsShowFunc="handleShowEnterUrl" />
    <PopupConfirmFineTune v-if="isShowConfirmModal" :message="'Để tạo phiên bản hình ảnh A.I của bạn thì bạn phải đào tạo ảnh mẫu. Chi phí đào tạo ảnh mẫu cho 1 lần với 1 mẫu là 250.000 VNĐ. Bạn có muốn tiếp tục không?'" @close="isShowConfirmModal = false" />
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade" />
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel" :currentCredit="pageProps?.auth?.user?.credit || 0" :additionalCredit="additionalCredit" />
    <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>
    <div v-if="showMaintenance" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[480px] xl:w-[580px] rounded-[40px]">
            <div class="w-full flex items-center justify-center">
                <img src="/assets/img/ai3goc/logo/circle.svg" alt="Thông báo" class="size-[160px] md:size-[200px]" />
            </div>
            <div class="text-black text-center">
                <h2 class="text-[16px] md:text-[24px] font-bold text-center mt-1 md:mt-2">Xin chào {{ pageProps.auth.user.name || "" }}</h2>
                <p class="text-[12px] md:text-[16px] font-medium mb-4 lg:w-10/12 mx-auto">Chức năng sẽ cập nhật lại trong thời gian tới. Xin vui lòng quay lại sau .</p>
            </div>
            <div class="flex items-center gap-4 md:gap-8 justify-center mt-6">
                <button @click="showMaintenance = false" class="rounded-[16px] w-[200px] font-bold bg-white text-orion-primary border-[2px] border-orion-primary py-4 text-[14px] md:text-[16px] cursor-pointer">Bỏ qua</button>
            </div>
        </div>
    </div>
    <div v-if="showOptions" class="overlay-option" @click="hideOption()"></div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, defineEmits } from "vue";
import ComingSoon from "@/Components/ComingSoon.vue";
import ModelEnterUrl from "@/Components/ModelEnterUrl.vue";

import PopupConfirmFineTune from "@/Components/PopupConfirmFineTune.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { toast } from "vue3-toastify";
import CreditBuyModal from "@/Components/ModalBuyCredit/BuyCredit.vue";
import PopupAff from "@/Components/PopupAff.vue";
import { eventBus } from "@/lib/eventBus.js";
import ComingSoonNew from "@/Components/ComingSoonNew.vue";

const { props: pageProps } = usePage();
const user = pageProps.auth.user;

const props = defineProps({
    isHome: {
        type: Boolean,
        default: false,
    },
    isSidebarOpen: {
        type: Boolean,
        default: false,
    },
});
const showDropdownCSKH = ref(false)
const showDropdownAiBanHang = ref(false)
const showDropdownCampaign = ref(false)
const showDropdown = ref(false);
const showOtherDropdown = ref(null);
const showDropdownImage = ref(false);
const showDropdownVideo = ref(false);
const showDropdownMusic = ref(false);
const showDropdownAssistant = ref(false);
const isShowPopup = ref(false);
const isShowPopupNew = ref(false);
const showModelEnterUrl = ref(false);
const isLoading = ref(false);
const showMaintenance = ref(false);
const showDataDropdown = ref(false);
const showOptions = ref(false)
const { url } = usePage();
const getRelativePath = (route) => {
    const url = new URL(route);
    return url.pathname;
};
const isActiveTab = ref([
    false,
    false,
    false,
    false
])
const isActiveCSKHTab = ref([
    false,
    false,
    false,
    false
])
const isActiveAiBanHangTab = ref([
    false,
    false,
    false,
    false
])
const isActiveChild = ref([
    false,false,false,false,false,false,false,false,
    false,false,false,false,false,false,false,false
])
const pages = [
    ["ai-image", "ai-background/v2", "ai-image/swap-face"],
    ["mc-virtual", "lipsync", "ai-video/img2Video", "ai-video/url-to-video/create", "ai-video", "create-video", "ai-video/history?type=all"],
    ["business/music-for-business", "ai-audio"],
    ["others-ai"],
    ["ai-chat", "customer-care", "management/customer"],
    ["?mode=advanced&type=bvqc&view=project", "mode=advanced&type=cdqc&view=project", "?mode=advanced&type=tqc&view=project", "?mode=advanced&type=anqc&view=project"]
]
var index = 0
for(var i = 0 ; i < pages.length; i++) {
    for(var j = 0; j < pages[i].length; j++) {
        if(url === "/"+pages[i][j] || (pages[i][j] === 'mode=advanced&type=cdqc&view=project' && url.includes(pages[i][j]))) {
            showOptions.value = true
            if(i < 4) {
                isActiveTab.value[i] = true
                showDropdown.value = true
            } else if (i === 4) {
                isActiveCSKHTab.value[j] = true
                showDropdownCSKH.value = true
            } else {
                isActiveAiBanHangTab.value[j] = true
                showDropdownAiBanHang.value = true
            }
            isActiveChild.value[index] = true
        }
        index++
    }
}
const redirectUrl = (url) => {
    location.href = url
}
const isAiOtherActive = computed(() => {
    return url.includes("ai-chat") || url.includes("ai-audio") || url.includes("music") || url.includes("song") || url.includes("ai-image") || url.includes("ai-background") || url.includes("video") || url.includes("creatomate") || url.includes("mc-virtual") || url.includes("create-video") || url.includes("lipsync");
});
const showConfirmFineTune = () => {
    // Bỏ popup đào tạo: vào thẳng trang Thương hiệu cá nhân
    window.location.href = route("ai-image.my-ai-image");
};

const handleShowMaintenance = () => {
    showMaintenance.value = true;
};

const hideOption = () => {
    showOptions.value = false
    showDropdown.value = false;
    showDropdownCSKH.value = false;
    showDropdownAiBanHang.value = false;
    showDropdownCampaign.value = false;
    showDropdownImage.value = false;
    showDropdownVideo.value = false;
    showDropdownMusic.value = false;
    showOtherDropdown.value = false;
}

const isSaleAi = ref(false);

const checkMediaActive = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const viewParam = urlParams.get("view");
    isSaleAi.value = viewParam === "project";
    isGuruActive.value = viewParam === "guru";
};
const isCustomerCareActive = computed(() => window.location.pathname === getRelativePath(route("customer-care.index")));
const isGuruActive = ref(false);

const checkGuruActive = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const viewParam = urlParams.get("view");
    isGuruActive.value = viewParam === "guru";
    isSaleAi.value = viewParam === "project";
};
const activeElement = ref(null);
const headerActiveElement = computed(() => {
    if (activeElement.value === 'other') {
        return 'other';
    }
    const routeList = [
        'ai-chat',
        'others-ai',
        'ai-audio',
        'business/music-for-business',
        'ai-video/img2Video',
        'create-video',
        'mc-virtual',
        'lipsync',
        'ai-image',
        'ai-image/swap-face',
        'ai-image/photo-beauty',
    ];
    const matched = routeList.some(route => url.includes(route));
    if (url.includes('google-search')) {
        return 'google-search'
    } else if (url.includes('ai-chat') || url.includes('customer-care') || url.includes('management/customer')) {
        return 'cskh'
    } else if (url.includes('ai-image/my-ai-image')) {
        return 'thcn'
    } else if (matched) {
        return 'other'
    }

    return null // fallback nếu không khớp điều kiện nào
})

window.addEventListener("popstate", checkMediaActive);
window.addEventListener("toggleProject", checkMediaActive);

checkMediaActive();

window.addEventListener("popstate", checkGuruActive);
window.addEventListener("toggleGuru", checkGuruActive);

checkGuruActive();

const showMarketing = async () => {
    if (!user) {
        window.location = route("login");
        return true;
    }
    const selectedMode = "advanced";
    const modes = { basic: 1, advanced: 2, expert: 3 };

    const packages = {
        aff_basic: 1,
        aff_account: 1,
        aff_xdthcn: 1,
        aff_advanced: 2,
        aff_expert: 3,
        aff_trial: 4,
        aff_business: 2,
        aff_vip: 2,
    };

    let message = "";

    if (user.is_vip_expired) {
        message = "Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!";
        if (user.package_code == "aff_trial") {
            // check for upgrade or by
        }
    } else if (!!!user.package_code) {
        message = "Bạn phải nâng cấp tài khoản!";
    } else if (user.package_code == "aff_trial") {
        const response = await axios.get(route("ai-business.trial-count"));
        if (response.data.check) {
            message = "Tài khoản thử nghiệm chỉ được thao tác 3 lần. Vui lòng nâng cấp tài khoản để sử dụng!";
        }
    }  else if (modes[selectedMode] > packages[user.package_code]) {
        message = "Rất tiếc, bạn không có quyền truy cập tính năng này. Vui lòng nâng cấp tài khoản để sử dụng đầy đủ các tính năng.";
    }

    if (message !== "") {
        eventBus.emit("popup-aff", { message });
        return true;
    }
    isSaleAi.value = true;
    activeElement.value = null;
    const currentUrl = new URL(window.location.href);

    if (currentUrl.pathname !== "/") {
        sessionStorage.setItem("redirectToProject", "true");
        window.location.href = "/?mode=advanced&view=project";
        return;
    }

    currentUrl.searchParams.set("view", "project");
    currentUrl.searchParams.set("mode", "advanced");
    window.history.pushState({}, "", currentUrl);
    window.dispatchEvent(new Event("toggleProject"));
};

if (sessionStorage.getItem("redirectToProject")) {
    sessionStorage.removeItem("redirectToProject");
    const url = new URL(window.location.href);
    url.searchParams.set("view", "project");
    window.history.pushState({}, "", url);
    window.dispatchEvent(new Event("toggleProject"));
}
if (sessionStorage.getItem("redirectToGuru")) {
    sessionStorage.removeItem("redirectToGuru");
    const url = new URL(window.location.href);
    url.searchParams.set("view", "guru");
    window.history.pushState({}, "", url);
    window.dispatchEvent(new Event("toggleGuru"));
}
const showGuru = () => {
    const currentUrl = new URL(window.location.href);

    if (currentUrl.pathname !== "/") {
        sessionStorage.setItem("redirectToGuru", "true");
        window.location.href = "/?view=guru";
        return;
    }

    currentUrl.searchParams.set("view", "guru");
    window.history.pushState({}, "", currentUrl);
    window.dispatchEvent(new Event("toggleGuru"));
};

async function createTokenAndRedirect(event) {
    event.preventDefault();

    const email = "giaovien1@gmail.com";
    const code = "aiwow";
    const secretKey = import.meta.env.VITE_SERCRET_KEY;

    try {
        const response = await fetch("https://slide.aiwow.com.vn/api/createToken", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ email, code, secretKey }),
        });

        if (!response.ok) {
            const errorData = await response.json();
            console.error("Error creating token:", errorData);
            alert("Lỗi tạo token: " + errorData.message);
            return;
        }

        const data = await response.json();
        const { userToken, token } = data;

        localStorage.setItem("userToken", userToken);
        localStorage.setItem("token", token);

        const redirectUrl = `https://slide.aiwow.com.vn/login-token?email=${encodeURIComponent(email)}&code=${encodeURIComponent(code)}&secretKey=${encodeURIComponent(secretKey)}&userToken=${encodeURIComponent(userToken)}&token=${encodeURIComponent(token)}`;

        window.location.href = redirectUrl;
    } catch (error) {
        console.error("Unexpected error:", error);
        alert("Có lỗi xảy ra: " + error.message);
    }
}

const handleShowEnterUrl = () => {
    showModelEnterUrl.value = !showModelEnterUrl.value;
};

const dropdown = ref(null);

const close = () => {
    isShowPopup.value = false;
};
const closeNew = () => {
    isShowPopupNew.value = false;
};

const toggleDropdown = (item, event) => {
    event.stopPropagation();

    if (item === "image") {
        showDropdownImage.value = !showDropdownImage.value;
        showDropdownVideo.value = false;
        showDropdownMusic.value = false;
        showDropdownAssistant.value = false;
    } else if (item === "video") {
        showDropdownImage.value = false;
        showDropdownVideo.value = !showDropdownVideo.value;
        showDropdownMusic.value = false;
        showDropdownAssistant.value = false;
    } else if (item === "assistant") {
        showDropdownImage.value = false;
        showDropdownVideo.value = false;
        showDropdownMusic.value = false;
        showDropdownAssistant.value = !showDropdownAssistant.value;
    } else {
        showDropdownImage.value = false;
        showDropdownVideo.value = false;
        showDropdownMusic.value = !showDropdownMusic.value;
        showDropdownAssistant.value = false;
    }
};

const toggleOtherDropdown = (item, event) => {
    event.stopPropagation();
    if (item === "image") {
        showDropdownImage.value = !showDropdownImage.value;
        showDropdownVideo.value = false;
        showDropdownMusic.value = false;
    } else if (item === "video") {
        showDropdownImage.value = false;
        showDropdownVideo.value = !showDropdownVideo.value;
        showDropdownMusic.value = false;
    } else {
        showDropdownImage.value = false;
        showDropdownVideo.value = false;
        showDropdownMusic.value = !showDropdownMusic.value;
    }
};
const dropdownRefs = {
    image: ref(null),
    video: ref(null),
    other: ref(null),
};

const otherDropdown = ref(null);
const toggleShowDrop = (type = 0) => {
    if (type == 2) {
        showDropdownAiBanHang.value = !showDropdownAiBanHang.value;
        showDropdown.value = false;
        showDropdownCSKH.value = false;
        showOptions.value = showDropdownAiBanHang.value === true
        return;
    }
    showDropdownAiBanHang.value = false
    if(type == 0) {
        isSaleAi.value = false;
        activeElement.value = 'other';
        showDropdown.value = !showDropdown.value
        showDropdownCSKH.value = false;
    } else {
        showDropdownCSKH.value = !showDropdownCSKH.value
        showDropdown.value = false;
    }
    if(showDropdown.value || showDropdownCSKH.value) {
        showOptions.value = true
    }
}
const handleClickOutsideNew = (event) => {

};

onMounted(() => {
    document.addEventListener("click", handleClickOutsideNew);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutsideNew);
});

const showAffKeyPopup = ref(false);
const isShowConfirmModal = ref(false);
const showBuyCreditPopup = ref(false);
const additionalCredit = ref(0);
const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};
const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};
const handleBuyCredit = () => {
    window.location.href = "/pricing";
};
const popupMessage = ref("Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!");
const acceptUpgrade = ref(true);
const checkCredit = async () => {
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
const showCS = () => {
    isShowPopupNew.value = true;
};
const showPopupComming = () => {
    isShowPopupNew.value = true;
};
</script>
<style>
.overlay-option {
  position: fixed;
  inset: 0;
  z-index: 10;
}
</style>
