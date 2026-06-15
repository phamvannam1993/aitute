<template>

    <Head title="MC Ảo - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData" isBig>
        <div class="flex gap-4 w-full flex-wrap md:flex-nowrap">
            <div class="flex justify-center w-full bg-white  rounded-[10px] md:rounded-[25px] lg:w-2/4 p-[12px] md:p-[20px] h-fit drop-shadow-xl">
                <div class="bg-white rounded-[10px] md:rounded-[25px] w-full">
                <div class="flex gap-1">
                    <div class="flex justify-start items-center gap-2">
                        <div class="w-[52px] overflow-hidden rounded-full border-[1px] border-[#d4d4d4]">
                            <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
                        </div>

                        <h2 class="text-black font-bold text-xl lg:text-xl 2xl:text-3xl leading-none">
                            Video MC ảo
                        </h2>
                    </div>
                </div>
                <p class="text-sm md:text-base text-ai3goc-primary font-bold my-4 ">Thực hiện theo các bước sau:</p>
                    <form @submit.prevent="handleSubmit">
                        <div class="relative">
                            <Step :step="1" stepName="Tải hình ảnh" />
                            <div class="ml-2 md:ml-4">
                                <p class="text-[#909090] text-sm md:text-base italic">Sử dụng hình ảnh của bạn hoặc dùng ảnh AI</p>
                                <div class="my-4 flex items-start md:items-center flex-row gap-2 justify-between w-full">
                                    <div class="flex items-center gap-2 flex-1 w-full">
                                        <input id="select-avatar" type="checkbox" @input="handleChangeTypeOfImageHeyGen" :checked="typeOfImageHeyGen === 'custom'" value="custom"
                                            class="border-[2px] border-gray-300 cursor-pointer rounded-full w-[15px] h-[15px] md:w-[18px] md:h-[18px] flex-shrink-0">
                                        <button type="button" class="flex items-center border-gray-300 border-[2px] flex-1 rounded-[10px] py-1 gap-2 justify-center" :class="{
                                            'pointer-events-none': typeOfImageHeyGen !== 'custom',
                                            'border-ai3goc-primary': typeOfImageHeyGen === 'custom'
                                        }" @click="$refs.avatarInput.click()">
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
                                            <span class="font-bold text-[10px] md:text-[15px] text-ai3goc-primary">
                                                Tải ảnh lên
                                            </span>
                                        </button>
                                        <input type="file" id="avatar" @change="handleAvatarChange" accept=".jpg,.png,.jpeg" class="hidden" ref="avatarInput" />
                                    </div>
                                    <div class="flex items-center gap-2 flex-1 w-full">
                                        <input id="select-avatar2" type="checkbox" @input="handleChangeTypeOfImageHeyGen" :checked="typeOfImageHeyGen === 'ai'" value="ai"
                                            class="border-[2px] border-gray-300 cursor-pointer rounded-full w-[15px] h-[15px] md:w-[18px] md:h-[18px] flex-shrink-0">
                                        <button type="button" class="flex items-center border-gray-300  border-[2px] flex-1 rounded-[10px] py-1 gap-2 justify-center" :class="{
                                            'pointer-events-none': typeOfImageHeyGen !== 'ai',
                                            'border-ai3goc-primary': typeOfImageHeyGen === 'ai'
                                        }">
                                            <div class="rounded-full w-[25px] h-[25px] bg-ai3goc-primary flex items-center justify-center">
                                                <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M1 6.8541C1 5.83011 1.83011 5 2.8541 5C3.55638 5 4.19839 4.60322 4.51246 3.97508L5.33333 2.33333C5.50271 1.99458 5.5874 1.8252 5.69554 1.68619C5.95744 1.34953 6.33166 1.11825 6.7499 1.03456C6.9226 1 7.11197 1 7.49071 1H12.5093C12.888 1 13.0774 1 13.2501 1.03456C13.6683 1.11825 14.0426 1.34953 14.3045 1.68619C14.4126 1.8252 14.4973 1.99458 14.6667 2.33333L15.4875 3.97508C15.8016 4.60322 16.4436 5 17.1459 5C18.1699 5 19 5.83011 19 6.8541V11.8571C19 14.1866 19 15.3513 18.3217 16.1103C18.2552 16.1846 18.1846 16.2552 18.1103 16.3217C17.3513 17 16.1866 17 13.8571 17H6.14286C3.81345 17 2.64875 17 1.88974 16.3217C1.81536 16.2552 1.74476 16.1846 1.67829 16.1103C1 15.3513 1 14.1865 1 11.8571V6.8541Z"
                                                        stroke="white" stroke-width="1.5" />
                                                    <path
                                                        d="M12.063 14H10.113L9.56703 12.362H5.94003L5.39403 14H3.45703L6.64203 4.89999H8.86503L12.063 14ZM7.76003 6.96699L6.51203 10.685H9.00803L7.76003 6.96699Z"
                                                        fill="white" />
                                                    <path d="M12.9466 14V4.89999H14.7406V14H12.9466Z" fill="white" />
                                                </svg>
                                            </div>
                                            <span class="font-bold text-[10px] md:text-[15px] text-ai3goc-primary">
                                                Chọn ảnh AI
                                            </span>
                                        </button>
                                        <input type="file" id="avatar" @change="handleAvatarChange" accept=".jpg,.png,.jpeg" class="hidden" ref="avatarInput" />
                                    </div>
                                </div>
                                <img v-if="avatarPreview" :src="avatarPreview" class="md:w-[200px] h-[200px] object-cover rounded-md" />
                                <span v-if="errors?.avatar" class="text-red-500 text-sm py-2">{{ errors?.avatar.join(",") }}</span>
                                <div v-if="typeOfImageHeyGen === 'ai'">
                                    <div class="flex flex-col mb-6 p-2 md:p-[15px] border border-ai3goc-primary rounded-md">
                                        <div class="grid grid-cols-3 gap-2 overflow-y-auto max-h-[330px] md:max-h-[360px] ">
                                            <div v-for="(avatar, index) in avatars_heygen" :key="index" class="avatar-item relative border border-gray-200 rounded-md cursor-pointer"
                                                @click="selectAvatarHeyGen(avatar)">
                                                <img class="w-full h-[160px] md:h-[200px] object-cover transition-all rounded-md" :class="{
                                                    'border-ai3goc-primary border-[2px] ': (selectedAvatar_heygen?.avatar_id && selectedAvatar_heygen?.avatar_id === avatar?.avatar_id) || (selectedAvatar_heygen?.id && selectedAvatar_heygen?.id === avatar?.id)
                                                }" :src="avatar.preview_image_url || avatar.image_url" :alt="avatar.preview_image_url || avatar.image_url" />
                                            </div>
                                        </div>
                                        <button type="button" @click="loadMoreAvatars"
                                            class="ml-auto w-fit text-[10px] md:text-sm flex items-center justify-center mt-[10px] p-[6px] min-w-[92px] bg-ai3goc-primary text-white rounded-xl">
                                            Xem thêm
                                        </button>
                                    </div>
                                    <span v-if="errors?.avatar_id || errors?.talking_photo_id || errors?.presenter_url" class="text-red-500 text-sm">
                                        Xin vui lòng chọn mẫu trình bày
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col mt-2">
                                <Step :step="2" stepName="Màu nền" />
                                <div class="ml-2 mb-4 flex flex-wrap justify-between flex-col md:flex-row gap-2 md:gap-0 items-start">
                                    <div class="text-black flex items-center">
                                        <input id="background-color" type="checkbox" name="background" @input="handleChangeStyleBackground" :checked="styleBackground === 'color'" value="color"
                                            class="border-[2px] border-gray-300 cursor-pointer rounded-full w-[15px] h-[15px] md:w-[18px] md:h-[18px] flex-shrink-0">
                                        <label for="background-color" class="ml-[0.5rem] text-black text-[14px] flex flex-col md:flex-row md:items-center">
                                            <div class="flex items-center gap-1">
                                                <strong class="inline-block flex-shrink-0 leading-none"></strong>
                                                <span class="font-bold text-[10px] md:text-sm">Chọn màu nền </span>
                                                <input v-model="styleBackgroundColor" type="color" id="head" name="head" value="#e66465" class="bg-[white] w-7 h-7 rounded-full border-none " />
                                            </div>
                                            <span class="text-ai3goc-primary text-[10px] md:text-sm italic">(Chỉ áp dụng cho hình ảnh có nền trong suốt)</span>
                                        </label>
                                    </div>
                                    <div class=" flex flex-col items-center flex-wrap" v-if="typeOfImageHeyGen === 'ai'">
                                        <div class="flex items-center">
                                            <input id="background-image" type="checkbox" name="background" @input="handleChangeStyleBackground" :checked="styleBackground === 'custom'" value="custom"
                                                class="border-[2px] border-gray-300 cursor-pointer rounded-full w-[15px] h-[15px] md:w-[18px] md:h-[18px] flex-shrink-0">
                                            <label :class="{
                                                'border-ai3goc-primary': backgroundName
                                            }" class="cursor-pointer ml-2 flex items-center border-gray-300 border-[2px] min-w-[140px] md:min-w-[200px] rounded-[10px] py-1 gap-2 justify-center">
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
                                                <span class="font-bold text-[10px] md:text-[15px] text-ai3goc-primary line-clamp-1 max-w-28">
                                                    {{ backgroundName || 'Tải ảnh nền' }}
                                                </span>
                                                <input :disabled="styleBackground !== 'custom'" type="file" id="background" @change="handleBackgroundChange" class="d-none hidden"
                                                    accept=".jpg,.png,.jpeg" />
                                            </label>
                                        </div>
                                        <span v-if="errors?.background_url" class="text-red-500 text-sm">{{ errors?.background_url[0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class=" grid grid-cols-2 items-center mb-4" >
                                <Step :step="3" stepName="Ngôn ngữ" />
                                <!-- <LabelStep stepLabel="Bước 3" stepName="Ngôn ngữ" /> -->

                                <select v-model="selectedLanguage" @change="handleLanguageChange"
                                    class="w-full p-1 text-[10px] md:text-sm md:p-3 border text-black border-ai3goc-primary rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="vi-VN">
                                        Tiếng Việt
                                    </option>
                                    <option value="en-US">Tiếng Anh</option>
                                    <option value="ja-JP">
                                        Tiếng Nhật
                                    </option>
                                    <option value="yue-CN">
                                        Tiếng Trung
                                    </option>
                                    <option value="ko-KR">Tiếng Hàn</option>
                                </select>
                            </div>
                            <div class=" grid grid-cols-2 items-center mb-4">
                                <!-- <LabelStep stepLabel="Bước 4" stepName="Giọng nói" /> -->
                                <Step :step="4" stepName="Giọng nói" />
                                <div class="relative w-full">
                                    <div @click="toggleDropdown"
                                        class="w-full p-2 text-[10px] md:text-sm md:p-3 border text-black border-ai3goc-primary rounded-xl shadow-sm cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <span v-if="selectedVoice">{{ getVoiceName(selectedVoice) }}</span>
                                        <span v-if="!selectedVoice">Bạn chưa chọn giọng nói</span>
                                    </div>
                                    <ul v-if="isDropdownOpen" class="absolute left-0 right-0 mt-1 bg-white border border-gray-300 text-black rounded-lg shadow-lg max-h-60 overflow-y-auto z-10">
                                        <li v-for="(voice, index) in availableVoices" :key="index" @click="selectVoice(voice)"
                                            class="flex justify-between items-center px-2 py-1 text-[10px] md:text-sm md:px-4 md:py-2 hover:bg-gray-100 cursor-pointer">
                                            <span class="inline-block w-[200px]">{{ voice.text }}</span>
                                            <div class="flex items-center">
                                                <div @click.stop="playSampleVoice(voice.demo)" class="text-white px-3 py-1 rounded-lg focus:outline-none w-[50px]">
                                                    <img src="/assets/img/ai_audio/speaker.png" alt="">
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <span v-if="errors?.voice" class="text-red-500 text-sm">{{ errors?.voice.join(",") }}</span>
                            </div>

                            <div class="flex flex-col gap-2">
                                <!-- <LabelStep stepLabel="Bước 5" stepName="Nhập Lời thoại" /> -->
                                <Step :step="5" stepName="Nhập Lời thoại" />

                                <div class="relative">
                                    <SuggestionPrompt v-model="videoDescription" :type="'mc-virtual'" :screenId="5" />
                                    <textarea rows="4" id="video-description" v-model="videoDescription" type="text" placeholder="Nhập nội dung mà MC ảo sẽ trình bày..."
                                        class="w-full p-2 text-[10px] md:text-sm md:p-3 border text-black border-ai3goc-primary rounded-[13px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                    <div class="object-input-mic relative ml-auto">
                                        <div v-if="isInputRecording" class="outline-input-mic right-3 bottom-3 flex items-center justify-center"></div>
                                        <div v-if="isInputRecording" class="outline-input-mic right-3 bottom-3 flex items-center justify-center" id="delayed"></div>
                                        <div v-if="isInputRecording" class="button-input-mic right-3 bottom-3 flex items-center justify-center"></div>
                                        <button class="button-input-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center bg-ai3goc-primary" @click="startInputRecording" :disabled="disabledRecord"
                                            type="button">
                                            <svg width="11" height="16" viewBox="0 0 11 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M9.43217 3.92249C9.43217 1.91391 7.86156 0.285645 5.9241 0.285645C3.98664 0.285645 2.41602 1.91391 2.41602 3.92249V6.00368C2.41602 8.01225 3.98664 9.64052 5.9241 9.64052C7.86156 9.64052 9.43217 8.01225 9.43217 6.00368V3.92249Z"
                                                    fill="white" />
                                                <path
                                                    d="M10.2449 7.99492C10.1168 7.99369 9.99052 8.02679 9.87882 8.09092C9.76711 8.15504 9.67384 8.24795 9.60832 8.36036C9.23558 9.021 8.69883 9.56974 8.05213 9.9513C7.40544 10.3329 6.67164 10.5337 5.92465 10.5337C5.17766 10.5337 4.44386 10.3329 3.79716 9.9513C3.15047 9.56974 2.61372 9.021 2.24099 8.36036C2.17559 8.24783 2.08234 8.15483 1.9706 8.09069C1.85887 8.02655 1.73257 7.99352 1.60438 7.99492C1.47384 7.99475 1.34553 8.02954 1.23229 8.09582C1.11904 8.16211 1.02482 8.25755 0.959046 8.37263C0.893266 8.48771 0.858229 8.6184 0.857436 8.75162C0.856642 8.88485 0.890119 9.01595 0.954523 9.13184C1.39592 9.90923 2.00631 10.5728 2.73867 11.0715C3.47102 11.5701 4.30579 11.8905 5.17863 12.008V13.4884H3.44953C3.25168 13.4884 3.06192 13.5686 2.92201 13.7114C2.78211 13.8541 2.70352 14.0478 2.70352 14.2497C2.70352 14.4516 2.78211 14.6453 2.92201 14.788C3.06192 14.9308 3.25168 15.011 3.44953 15.011H8.40971C8.60757 15.011 8.79732 14.9308 8.93723 14.788C9.07713 14.6453 9.15573 14.4516 9.15573 14.2497C9.15573 14.0478 9.07713 13.8541 8.93723 13.7114C8.79732 13.5686 8.60757 13.4884 8.40971 13.4884H6.67066V12.008C7.54339 11.8902 8.378 11.5696 9.1103 11.071C9.84261 10.5724 10.4531 9.90898 10.8948 9.13184C10.9592 9.01595 10.9927 8.88485 10.9919 8.75162C10.9911 8.6184 10.956 8.48771 10.8903 8.37263C10.8245 8.25755 10.7303 8.16211 10.617 8.09582C10.5038 8.02954 10.3755 7.99475 10.2449 7.99492Z"
                                                    fill="white" />
                                            </svg>

                                        </button>
                                    </div>
                                </div>
                                <span v-if="errors?.content" class="text-red-500 text-sm">{{ errors?.content.join(', ') }}</span>
                                <span v-if="errors?.script" class="text-red-500 text-sm">{{ errors?.script.join(', ') }}</span>
                            </div>

                            <div v-if="selectedModel === 'heygen'" class="mt-2 md:mt-4 flex flex-col gap-2 ">
                                <div class="grid grid-cols-2 items-center ">
                                    <!-- <LabelStep stepLabel="Bước 6" stepName="Nhái giọng" /> -->
                                    <Step :step="6" stepName="Nhái giọng" />

                                    <button :disabled="isLoadingClonedVoices || isLoading" type="button" @click="textToSpeech"
                                        class="flex-1 px-4 py-2 bg-ai3goc-primary text-white rounded-[10px] min-w-36 md:w-[180px] text-[10px] md:text-[15px] font-bold">
                                        {{ isLoadingClonedVoices ? 'Đang chuyển đổi...' : 'Nhái giọng' }}
                                    </button>
                                </div>
                                <p class="text-ai3goc-primary italic text-[10px] md:text-sm mb-5">(Chỉ áp dụng cho tài khoản được cấp quyền)</p>
                            </div>
                            <div class=" flex items-center gap-2 " v-if="isShowUploadFileOrRecord && clonedVoices.length === 0">
                                <button type="button" :disabled="isLoadingClonedVoices" @click="triggerAudioUpload"
                                    class="flex-1 flex items-center gap-2 px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg backdrop-blur-sm transition-colors justify-center">
                                    <img src="/assets/img/upload.png" class="size-5" />
                                    <span class="hidden xl:block">Tải giọng nói của bạn lên</span>
                                </button>
                                <input type="file" ref="audioInput" accept="audio/*" @change="handleAudioUpload" class="hidden" @click="$event.target.value = null" />
                                <div class="text-center">
                                    <span class="px-3 text-gray-500">hoặc</span>
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
                        </div>
                        <div class=" grid grid-cols-2 md:items-center flex-row mb-4 mt-2">
                            <!-- <LabelStep stepLabel="Bước 7" stepName="Kích thước" /> -->
                            <Step :step="7" stepName="Kích thước" />

                            <div class="flex flex-1 justify-between items-center">
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded-full" id="aspect_ratio_1_1" value="1:1" @input="handleChangeAspectRatio" :checked="selectedAspectRatio === '1:1'">
                                    <label class="text-black ml-2 text-[10px] md:text-sm" for="aspect_ratio_1_1">1:1</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded-full" id="aspect_ratio_16_9" value="16:9" @input="handleChangeAspectRatio" :checked="selectedAspectRatio === '16:9'">
                                    <label class="text-black ml-2 text-[10px] md:text-sm" for="aspect_ratio_16_9">16:9</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded-full" id="aspect_ratio_9_16" value="9:16" @input="handleChangeAspectRatio" :checked="selectedAspectRatio === '9:16'">
                                    <label class="text-black ml-2 text-[10px] md:text-sm" for="aspect_ratio_9_16">9:16</label>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 items-center mb-4">
                            <!-- <LabelStep stepLabel="Bước 8" stepName="Phụ đề" /> -->
                            <Step :step="8" stepName="Phụ đề" />

                            <div class="flex items-center flex-1 justify-between">
                                <div class="flex items-center gap-2">
                                    <input type="radio" id="captionOn" value="true" v-model="enableCaption">
                                    <label for="captionOn" class="text-black text-[10px] md:text-sm">Bật</label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="radio" id="captionOff" value="false" v-model="enableCaption">
                                    <label for="captionOff" class="text-black text-[10px] md:text-sm">Tắt</label>
                                </div>
                            </div>
                        </div>
                        <div class=" grid grid-cols-2 items-center mb-4 ">
                            <!-- <LabelStep stepLabel="Bước 9" stepName="Cung cấp lời thoại
                                    bằng giọng nói" /> -->
                            <Step :step="9" stepName="Cung cấp lời thoại
                                    bằng giọng nói" />

                            <button :disabled="isLoadingClonedVoices || isLoading" type="button" @click="startRecording"
                                class="flex items-center justify-center gap-1 flex-1 px-4 py-2 bg-ai3goc-primary text-white rounded-[10px] text-[10px] md:text-[15px] font-bold" :class="{
                                    'bg-red-500': isRecording
                                }">
                                <div class="flex items-center justify-center w-[16px] md:w-[22px] h-[16px] md:h-[22px] rounded-full bg-white">

                                    <svg v-if="isRecording" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        class="size-6 stroke-ai3goc-primary">
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
                                <span>
                                    {{ isRecording ? 'Đang ghi âm...' : 'Thu âm' }}
                                </span>
                            </button>

                            <h3 class="mt-4 text-ai3goc-primary italic text-[10px] md:text-sm">Hoặc tải file âm thanh lên</h3>
                            <label class="mt-4 flex items-center justify-center gap-1 flex-1 px-4 py-2 bg-ai3goc-primary text-white rounded-[10px] text-[10px] md:text-[15px] font-bold">
                                <input type="file" accept="audio/*" class="hidden" @change="onAudioFileSelected" />
                                Tải audio
                            </label>
                        </div>
                        <div class="mb-4">
                            <span v-if="errors?.audio" class="text-red-500 text-sm">{{ errors?.audio.join(",") }}</span>
                            <div class="flex items-center mt-4 gap-2" v-if="audioUrl">
                                <audio :src="audioUrl" class="flex-1 h-[36px]" controls></audio>
                                <button  type="button" @click="audioUrl = null"
                                    class="text-[10px] md:text-sm h-[28px] md:h-[36px] text-center right-0 -top-4 bg-red-500 text-white font-semibold rounded-[10px] w-1/5">
                                    Xoá
                                </button>
                            </div>
                        </div>

                        <div class=" grid grid-cols-2 items-center">
                            <!-- <LabelStep stepLabel="Bước 10" stepName="Bấm vào đây" /> -->
                            <Step :step="10" stepName="Bấm vào đây" />

                            <button :disabled="isLoading" type="submit" class="w-full px-4 py-2 bg-ai3goc-primary text-white rounded-[10px] text-[10px] md:text-[15px] font-bold">
                                {{ isLoading ? "Đang tạo..." : "Tạo MC ảo" }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
            <div class="bg-white rounded-[10px] md:rounded-[25px] w-full lg:w-2/4 p-[12px] md:p-[25px] drop-shadow-xl">
                <!-- Phần hiển thị video/hình ảnh -->
                <div class="flex justify-center items-center flex-col">
                    <p class="text-[10px] mb-4 text-sm text-left text-ai3goc-primary w-full italic">Hiển thị kết quả</p>
                    <video v-if="resultValue?.result_url" :src="resultValue?.result_url" alt="video-generate-by-AI" controls="" preload="auto" :poster="resultValue?.avatar_url"
                        class="w-full h-full mb-4 md:min-w-[500px] md:min-h-[500px] object-cover" />
                    <div v-else class="flex items-center justify-center bg-gray-300 w-full h-[300px] md:h-[400px] xl:h-[500px] rounded-xl  showLoaderVideo">
                        <div v-if="isLoading || (resultValue && (resultValue?.status !== 'done' || resultValue.status !== 'fail' || resultValue.status !== 'error'))">
                            <div class="loaderVideo"></div>
                            <p class="text-loading">Video đang được tạo. Vui lòng chờ !</p>
                        </div>
                        <div v-else>
                            <svg width="91" height="73" viewBox="0 0 91 73" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_1396_4994)">
                                    <path
                                        d="M66.0304 32.26C74.9387 32.26 82.1604 25.0384 82.1604 16.13C82.1604 7.22165 74.9387 0 66.0304 0C57.122 0 49.9004 7.22165 49.9004 16.13C49.9004 25.0384 57.122 32.26 66.0304 32.26Z"
                                        fill="#B1B1B1" />
                                    <path
                                        d="M66.0309 58.2701C79.6447 58.2701 90.6809 52.9333 90.6809 46.3501C90.6809 39.7668 79.6447 34.4301 66.0309 34.4301C52.417 34.4301 41.3809 39.7668 41.3809 46.3501C41.3809 52.9333 52.417 58.2701 66.0309 58.2701Z"
                                        fill="#B1B1B1" />
                                    <path
                                        d="M24.6495 32.26C33.5579 32.26 40.7795 25.0384 40.7795 16.13C40.7795 7.22165 33.5579 0 24.6495 0C15.7412 0 8.51953 7.22165 8.51953 16.13C8.51953 25.0384 15.7412 32.26 24.6495 32.26Z"
                                        fill="#B1B1B1" />
                                    <path
                                        d="M24.65 58.2701C38.2638 58.2701 49.3 52.9333 49.3 46.3501C49.3 39.7668 38.2638 34.4301 24.65 34.4301C11.0362 34.4301 0 39.7668 0 46.3501C0 52.9333 11.0362 58.2701 24.65 58.2701Z"
                                        fill="#B1B1B1" />
                                    <path
                                        d="M45.3401 48.9901C50.1781 48.9901 54.1001 45.1442 54.1001 40.4001C54.1001 35.6559 50.1781 31.8101 45.3401 31.8101C40.5021 31.8101 36.5801 35.6559 36.5801 40.4001C36.5801 45.1442 40.5021 48.9901 45.3401 48.9901Z"
                                        fill="url(#paint0_linear_1396_4994)" />
                                    <path
                                        d="M45.3403 52.43C51.1503 52.43 55.8603 47.8096 55.8603 42.11C55.8603 36.4105 51.1503 31.79 45.3403 31.79C39.5303 31.79 34.8203 36.4105 34.8203 42.11C34.8203 47.8096 39.5303 52.43 45.3403 52.43Z"
                                        fill="white" />
                                    <path d="M45.3411 50.23V70.88H40.2811L39.1611 54.52L38.8711 50.23H45.3411Z" fill="white" />
                                    <path d="M51.8098 50.23L51.5198 54.52L50.3998 70.88H45.3398V50.23H51.8098Z" fill="white" />
                                    <path
                                        d="M36.4296 36.6C37.395 35.1164 38.7156 33.8972 40.2715 33.0533C41.8275 32.2094 43.5695 31.7673 45.3396 31.7673C47.1097 31.7673 48.8518 32.2094 50.4077 33.0533C51.9637 33.8972 53.2842 35.1164 54.2496 36.6C57.845 34.6228 60.6806 31.5058 62.3097 27.7399C63.9389 23.974 64.2691 19.7731 63.2484 15.7988C62.2277 11.8246 59.914 8.30285 56.6717 5.78809C53.4295 3.27332 49.4428 1.90845 45.3396 1.90845C41.2364 1.90845 37.2498 3.27332 34.0075 5.78809C30.7652 8.30285 28.4516 11.8246 27.4308 15.7988C26.4101 19.7731 26.7404 23.974 28.3695 27.7399C29.9987 31.5058 32.8342 34.6228 36.4296 36.6Z"
                                        fill="#C9C9C9" />
                                    <path
                                        d="M55.8499 42.34C55.8181 43.8696 55.442 45.3724 54.7495 46.7366C54.057 48.1009 53.0659 49.2915 51.8499 50.22H38.8698C37.643 49.2981 36.6412 48.1101 35.9397 46.7452C35.2382 45.3804 34.8553 43.8741 34.8198 42.34C24.4298 44.34 17.0898 49.27 17.0898 55.02C17.0898 61.7 27.0098 67.26 40.0898 68.44L39.0898 54.51H51.4498L50.4498 68.44C63.5498 67.26 73.4498 61.7 73.4498 55.02C73.5798 49.27 66.2399 44.35 55.8499 42.34Z"
                                        fill="#C9C9C9" />
                                    <path
                                        d="M40.1191 68.4401V70.4401C40.1191 71.0209 40.3499 71.5779 40.7606 71.9886C41.1713 72.3993 41.7283 72.6301 42.3091 72.6301H48.3091C48.8909 72.6301 49.4489 72.3996 49.8612 71.9892C50.2735 71.5788 50.5065 71.0218 50.5091 70.4401V68.4401C48.8158 68.6001 47.0758 68.6801 45.2892 68.6801C43.5025 68.6801 41.7791 68.6001 40.1191 68.4401Z"
                                        fill="#C9C9C9" />
                                </g>
                                <defs>
                                    <linearGradient id="paint0_linear_1396_4994" x1="43.3921" y1="56.0661" x2="42.9901" y2="39.3301" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#00BFFF" />
                                        <stop offset="1" stop-color="#00FFFF" />
                                    </linearGradient>
                                    <clipPath id="clip0_1396_4994">
                                        <rect width="90.68" height="72.63" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                    </div>
                    <div :class="{ 'pointer-events-none': !resultValue?.result_url }" class="flex items-center flex-wrap w-full py-2 justify-between gap-2">
                        <a :href="route('lipsync.lipsync', { videoUrl: resultValue?.result_url })"
                            class="relative group md:min-w-[120px] justify-center rounded-full bg-[#149CBE] flex gap-1 px-2 items-center">
                            <img src="/assets/svgs/aiwow/taskbar/lipsync.svg" alt="AI Video" class="max-lg:w-4" />
                            <span
                                class="font-medium hover:scale-105 text-white lg:px-1 xl:px-2 py-1.5 text-[10px] text-nowrap lg:text-sm">Lồng
                                tiếng</span>
                        </a>
                        <a @click="$refs.fileInput.click()"
                            href="javascript:void(0)" class="relative group md:min-w-[120px] justify-center rounded-full bg-[#149CBE] flex gap-1 px-2 items-center">
                            <div v-if="isCallUploadAudio" class="loaderVideo loaderAudio"></div>
                            <img v-if="!isCallUploadAudio" src="/assets/svgs/aiwow/taskbar/music.svg" alt="AI Video" class="max-lg:w-4"/>
                            <span
                                class="font-medium hover:scale-105 text-white lg:px-1 xl:px-2 py-1.5 text-[10px] text-nowrap lg:text-sm">Nhạc
                                nền</span>
                            <input type="file" id="audio-file" @change="handleAudioFileChange" accept="audio/*" class="hidden"
                                ref="fileInput" :disabled="isDisabled" />
                        </a>
                        <div class="relative text-nowrap lg:min-w-[20%]">
                            <button type="button" @click="toggleShowMore"
                                class="relative group md:min-w-[120px] justify-center flex bg-[#149CBE] w-full py-1.5 min-h-8 px-2 text-nowrap lg:px-2.5 rounded-full gap-1 items-center">
                                <img src="/assets/svgs/aiwow/taskbar/more.svg" alt="AI Video" class="w-4 h-4" />
                                <span class="font-medium max-lg:text-xs text-nowrap lg:text-sm hover:scale-105 rounded-md text-white">Khác</span>
                            </button>
                            <div v-if="isShowMore"
                                class="bg-white text-black menu-container absolute top-[120%] w-[10rem] right-0 z-[100] border-2 border-[#E6E6E6] gap-4 flex flex-col p-3 rounded-[20px]">
                                <button type="button" @click="createPost(resultValue?.result_url)"
                                    class="flex text-nowrap lg:min-w-[20%] py-1.5 px-2 lg:px-2.5 rounded-full hover:bg-[#FFFBE2] gap-1 items-center transition-colors duration-300">
                                    <img src="/assets/svgs/aiwow/taskbar/post.svg" class="max-sm:w-5 w-6"
                                        alt="Create Post" />
                                    <span class="font-medium max-lg:text-xs lg:text-sm hover:scale-105 rounded-md text-nowrap">Đăng bài</span>
                                </button>
                                <button type="button" @click="shareAIMcVirtual(resultValue)"
                                    class="flex text-nowrap lg:min-w-[20%] py-1.5 px-2 lg:px-2.5 rounded-full hover:bg-[#FFFBE2] gap-1 items-center">
                                    <img src="/assets/svgs/aiwow/taskbar/share2.svg" class="max-sm:w-5 w-6" />
                                    <span class="font-medium max-lg:text-xs lg:text-sm hover:scale-105 rounded-md text-nowrap">Chia sẻ</span>
                                </button>
                                <button type="button"
                                    class="flex text-nowrap lg:min-w-[20%] py-1.5 px-2 lg:px-2.5 rounded-full hover:bg-[#FFFBE2] gap-1 items-center"
                                    @click="downloadVideo(resultValue?.result_url)">
                                    <img src="/assets/svgs/aiwow/taskbar/download2.svg" alt="Download" class="max-sm:w-5 w-6" />
                                    <span class="font-medium max-lg:text-xs lg:text-sm hover:scale-105 rounded-md text-nowrap">Tải xuống</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-4 w-full btn-history">
                    <a v-if="isLoading" href="javascript:void(0)" @click="showAlert()"
                        class="px-4 w-[180px] text-center py-2 bg-ai3goc-primary text-white font-bold text-[10px] md:text-[15px] rounded-[10px]">Lịch
                        sử
                    </a>
                    <a v-else :href="route('mc-virtual.historyView')" class="px-4 w-[180px] text-center py-2 bg-ai3goc-primary text-white font-bold text-[10px] md:text-[15px] rounded-[10px]">Lịch
                        sử
                    </a>
                </div>

            </div>
        </div>
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-30 z-50 flex justify-center items-center w-screen h-screen">
            <div class="md:w-[20vw] bg-white rounded-lg relative">
                <button type="button" class="absolute right-2 top-1 z-50 text-black" @click="showSample">
                    X
                </button>
                <video :src="selectedAvatar.s3_url" :alt="selectedAvatar.presenter_id" class="rounded-lg" controls></video>
            </div>
        </div>

        <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold text-[#092A99]">Xác nhận xóa video</h3>
                <p class=" text-gray-600">
                    Bạn có chắc chắn muốn xóa video này không?
                </p>
                <div class="mt-6 flex justify-end space-x-4">
                    <button @click="cancelDelete" class="px-4 py-2 bg-gray-300 text-black rounded">Hủy</button>
                    <button @click="confirmDeleteVideo" class="px-4 py-2 bg-red-500 text-white rounded">Xóa</button>
                </div>
            </div>
        </div>
        <Modal :show="showListContentAI" :maxWidth="modalStep === 1 ? 'xl' : '5xl'" custom-class="mx-[20px]" @close="showListContentAI = false">
            <div v-if="modalStep === 1" class="bg-white p-5 rounded-[10px] md:rounded-[25px] min-h-[400px] flex flex-col items-center justify-center gap-[20px]">
                <div class="w-full">
                    <label class="text-black mr-auto">Nhập nội dung bạn muốn tạo</label>
                    <textarea class="w-full  text-black" rows="30" placeholder="Tạo cho tôi một bài giới thiệu về nước khoáng trong vòng 500 từ" v-model="userPrompt"></textarea>
                </div>
                <button class="text-center right-0 -top-4 py-2 bg-blue-500 text-white font-semibold rounded-[10px] w-1/5" @click="handleGetSuggest">Tạo</button>

                <div class="w-full">
                    <label class="text-black mr-auto mb-0">Nội dung gợi ý bởi AI</label>
                    <textarea class="w-full h-[200px] text-black" rows="30" placeholder="Nội dung gợi ý" v-model="suggestContent"></textarea>
                </div>
                <button :disabled="!suggestContent" class="text-center right-0 -top-4 py-2 text-white font-semibold rounded-[10px] w-1/5" :class="{
                    'bg-blue-500': suggestContent,
                    'bg-gray-500': !suggestContent
                }" @click="handleApplyContent">Áp dụng</button>
            </div>
        </Modal>
        <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
            <FormShareLink :shareLink="shareLink" />
        </Modal>
        <PopupN
        v-if="isOpenWarrningUseVoice"
        title="Cảnh báo !"
        :message="'Tính năng này có thể sử dụng vào mục đích xấu nên để sử dụng tính năng này cần phải liên hệ với quản trị viên để ký cam kết sử dụng theo đúng pháp luật hiện hành. Liên hệ quản trị viên theo sđt <strong>098 878 6699</strong> (Mr. Hùng)'"
        cancelButtonText="Bỏ qua"
        @cancel="isOpenWarrningUseVoice = false"
    />

        <CreditModal :showCreditPopup="showCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleCancel" />
        <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade" />
        <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel" :currentCredit="pageProps.auth.user.credit || 0"
            :additionalCredit="additionalCredit" />
        <div v-if="isShowAlert" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold text-[#092A99]">Xác nhận thoát màn hình</h3>
                <p class=" text-gray-600">
                    Video đang được tạo. Bạn có chắc chắn muốn thoát không?
                </p>
                <div class="mt-6 flex justify-end space-x-4">
                    <button @click="cancelExit" class="px-4 py-2 bg-gray-300 text-black rounded">Hủy</button>
                    <button @click="confirmExit" class="px-4 py-2 bg-red-500 text-white rounded">Đồng ý</button>
                </div>
            </div>
        </div>
    </Layout>
</template>
<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount, nextTick } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { toast } from "vue3-toastify";
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
import CreditModal from '../../../Components/ModalBuyCredit/Index.vue';
import CreditBuyModal from '../../../Components/ModalBuyCredit/BuyCredit.vue';
import PopupAff from '@/Components/PopupAff.vue';
import FormShareLink from "@/Components/FormShareLink.vue";
import { estimateDuration, getAudioDuration } from "@/lib/estimateDuration";
import Layout from "@/Layouts/Client/Layout.vue";
import LabelStep from '@/Components/LabelStep.vue';
import PopupN from '@/Components/Popup/Index.vue'
import Step from "@/Components/Step/Index.vue";


const props = defineProps({
    listVoiceClone: Array,
    user: Object,
    listAllVoice: Array,
});

const breadcrumbsData = [
    { text: "MC ảo", link: "mc-virtual.index" },
];
const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);
const credits = ref(0);
const showAffKeyPopup = ref(false);
const videoRef = ref("");
const videoDescription = ref("");
const selectedLanguage = ref("vi-VN");
const selectedVoice = ref("vi-VN-HoaiMyNeural");
const availableVoices = ref([]);
const selectedContentType = ref("talk");

const avatars = ref([]);
const avatars_heygen = ref([]);
const selectedAvatar = ref(null);
const selectedAvatar_heygen = ref(null);
const totalAvatars = ref();
const data = ref();
const dataHeyGen = ref();
const currentLength = ref(8);
const showModal = ref(false);
const selectedModel = ref('heygen');
const styleBackground = ref('color');
const styleBackgroundColor = ref('#2C75E3');
const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)

const enableCaption = ref(false);

const clonedVoices = ref([]);
const selectedClonedVoice = ref(null);
const isLoadingClonedVoices = ref(false);

const typeOfImageHeyGen = ref('custom');
const isShowAlert = ref(false);

const audioFile = ref(null);
const audioInput = ref(null);
const audioPreviewKey = ref(0);
const audioPreview = ref(null);
const uploadedAudioPreview = ref(null);
const isShowUploadFileOrRecord = ref(false);
const isHistory = ref(false);

const isOpenWarrningUseVoice = ref(false);

const resultValue = ref(null);
const isCloneVoice = ref(false);
const handleChangeTypeOfImageHeyGen = (type) => {
    const value = type.target.value;
    typeOfImageHeyGen.value = value;
    if (value === 'ai') {
        avatarName.value = ''
        avatarPreview.value = null
    }
}
const handleChangeStyleBackground = (style) => {
    const value = style.target.value;
    styleBackground.value = value;
}


const fileName = ref('');
const isCallApi = ref(false)
const isCallUploadAudio = ref(false)
const handleAudioFileChange = async (event) => {
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav", "audio/x-m4a"];
    if (!allowedAudioTypes.includes(type)) {
        alert("Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav. hoặc .m4a");
        audioInput.value.value = "";
        return;
    }
    audioFile.value = event.target.files[0];
    const formData = new FormData();
    formData.append('mc_id', resultValue.value.id);
    if (audioFile.value) {
        formData.append('audio_file', audioFile.value);
    }
    try {
        const response = await axios.post(
            route("mc-virtual.mergeAudioVideoQueue"),
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        if (response.data.success) {
            isCallUploadAudio.value = true
        }
    } catch (error) {
        isLoadingVideo.value = false
    } finally {
    }
};

var intervalid1 = setInterval(async () => {
    if (isCallUploadAudio.value) {
        var url = route("mc-virtual.detail", { id: resultValue.value.id })
        try {
            const response = await axios.get(url)
            if (response.data.data.is_upload_audio) {
                resultValue.value = response.data.data;
                isLoadingVideo.value = false
                isCallUploadAudio.value = false
            }
        } catch (error) {
            isCallUploadAudio.value = false
        }
    }
}, 10000);


for (var i = 0; i < 100; i++) {
    history.pushState({}, '');
}
window.onpopstate = function () {
    if (isLoading.value && !resultValue?.result_url) {
        isShowAlert.value = true;
        isHistory.value = false;
    }
};
const cancelExit = () => {
    history.pushState({}, '');
    isShowAlert.value = false;
}
const referrer = document.referrer;
if (!referrer.includes("mc-virtual") || referrer.includes("mc-virtual/history")) {
    localStorage.setItem("referrer", referrer);
}
const confirmExit = () => {
    if (isHistory.value) {
        window.location.href = "/mc-virtual/history"
    } else {
        var redirect = localStorage.getItem("referrer");
        window.location.href = redirect
    }
}

const showAlert = () => {
    isShowAlert.value = true;
    isHistory.value = true;
}

const handleShowUploadFileOrRecord = () => {
    console.log("isShowUploadFileOrRecord", isShowUploadFileOrRecord.value)
    isShowUploadFileOrRecord.value = !isShowUploadFileOrRecord.value;
}


const triggerAudioUpload = () => {
    if (!videoDescription.value) {
        toast.error("Vui lòng nhập lời thoại trước khi nhái giọng");
        return;
    }
    audioInput.value.click();
};
const handleAudioUpload = (event) => {
    if (!videoDescription.value) {
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
// api clone-voices api
const handleVoiceClone = async () => {
    if (!videoDescription.value) {
        toast.error("Vui lòng nhập lời thoại trước khi nhái giọng");
        return;
    }
    try {
        const formData = new FormData();
        if (!audioFile.value) {
            toast.warning("Vui lòng tải audio hoặc ghi âm giọng của bạn")
            return;
        }
        isLoadingClonedVoices.value = true;
        formData.append("name", window.location.hostname + "_" + props.user.id)
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
    if (!props.user.clone_voice) {
        isOpenWarrningUseVoice.value = true;
        return;
    }
    const hasEnoughCredit = await checkCredit({
        type: 'elevenlab',
        model: "Elevenlab",
        number_result: videoDescription.value.length || null,
    });
    if (!hasEnoughCredit) {
        return; // Dừng nếu không đủ credit
    }
    if (clonedVoices.value.length === 0) {
        handleShowUploadFileOrRecord()
        return;
    }
    if (!videoDescription.value) {
        toast.error("Vui lòng nhập lời thoại trước khi nhái giọng");
        return;
    }
    if (!selectedClonedVoice.value && clonedVoices.value.length === 0) {
        toast.error("Vui lòng chọn giọng nhái");
        return;
    }
    try {
        isLoadingClonedVoices.value = true;
        const result = await axios.post(route("ai-audio.text-to-speech"), {
            voice_id: selectedClonedVoice.value || clonedVoices.value[0].type,
            language: 'vi',
            text: videoDescription.value,
            isSaveDb: false
        })
        if (result.data.success) {
            audioUrl.value = result.data.data;
            credits.value = result.data.credit;
            selectedClonedVoice.value = clonedVoices.value[0].type
            isCloneVoice.value = true;
        }
    } catch (error) {
        console.log(error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại sau");
    } finally {
        isLoadingClonedVoices.value = false;
    }
};
const fetchAvatarsHeyGen = async () => {
    try {
        const [avatarsResponse, talkingPhotosResponse] = await Promise.all([
            axios.get(route('mc-virtual.heygen-avatars')),
            axios.get(route('mc-virtual.heygen-talking-photos'))
        ]);

        const resultAvatars = avatarsResponse.data.data;
        let resultTalkingPhotos = talkingPhotosResponse.data.data.filter(item => item.is_preset);

        dataHeyGen.value = {
            avatars: resultAvatars || [],
            talking_photos: resultTalkingPhotos || [],
        };

        avatars_heygen.value = [
            ...(dataHeyGen.value.avatars || []).slice(0, 4),
            ...(dataHeyGen.value.talking_photos || []).slice(0, 4),
        ];

        totalAvatars.value = (dataHeyGen.value.avatars?.length || 0) + (dataHeyGen.value.talking_photos?.length || 0);
    } catch (error) {
        console.error("Lỗi khi gọi API:", error);
    }
};
// Hàm này để load thêm avatars khi người dùng nhấn nút "Xem thêm"
const loadMoreAvatars = () => {
    if (currentLength.value >= totalAvatars.value) {
        return;
    }
    avatars_heygen.value = [
        ...dataHeyGen.value.avatars.slice(
            currentLength.value,
            currentLength.value + 4
        ),
        ...dataHeyGen.value.talking_photos.slice(
            currentLength.value,
            currentLength.value + 4
        ),
        ...avatars_heygen.value,
    ];
    currentLength.value += 4;
};

const selectAvatarHeyGen = (avatar) => {
    selectedAvatar_heygen.value = avatar;
    avatarFile.value = null;
    avatarName.value = '';
};
// Gọi API khi component được mount
onMounted(() => {
    fetchAvatarsHeyGen();
    handleLanguageChange();
    fetchVoiceTypes();
    getVideoProcessing();
});

const showSample = () => {
    showModal.value = !showModal.value;
};
const voiceIgnore = [''];

const voices = {
    "vi-VN": [
        { text: "Giọng nữ phổ thông", value_heyGen: "18d006d24dd84e438ae7ec886045c281", demo: "https://resource.heygen.ai/text_to_speech/ew6DRo8i2QE6BnXEhUWHQ3.mp3" },
        { text: "Giọng nam phổ thông", value_heyGen: "6861d3de6a904aeeb20feb8d4238cfd4", demo: "https://resource.heygen.ai/text_to_speech/G3noiNeCXTV8WVoxPzAQCG.mp3" },
        ...props.listAllVoice.filter((item) => (item.user_id !== props.user.id) && !voiceIgnore.includes(item.type)).map((item) => {
            return {
                text: item.user_id === props.user.id ? "Giọng của bạn" : item.name,
                value_heyGen: item.type,
                demo: item.demo,
            };
        }),
    ],
    "en-US": [
        { text: "Giọng nữ", value_heyGen: "26b2064088674c80b1e5fc5ab1a068eb" },
        { text: "Giọng nam", value_heyGen: "1985984feded457b9d013b4f6551ac94" },
    ],
    "ja-JP": [
        { text: "Giọng nữ", value_heyGen: "289430c137354573a3ab773c91f05094" },
        { text: "Giọng nam", value_heyGen: "107ec6a82944431ebb6dd0652ecb42d0" },
    ],
    "yue-CN": [
        { text: "Giọng nữ", value_heyGen: "4158cf2ef85d4ccc856aacb1c47dbb0c" },
        { text: "Giọng nam", value_heyGen: "35f6b6ac010849d38cfc99dc25e0e4b3" },
    ],
    "ko-KR": [
        { text: "Giọng nữ", value_heyGen: "bef4755ca1f442359c2fe6420690c8f7" },
        { text: "Giọng nam", value_heyGen: "9d81087c3f9a45df8c22ab91cf46ca89" },
    ],
};

const handleLanguageChange = () => {
    availableVoices.value = voices[selectedLanguage.value];
    selectedVoice.value = availableVoices.value[0].value_heyGen;
};

watch(selectedLanguage, handleLanguageChange);
watch(selectedModel, (newValue) => {
    handleLanguageChange();
});

availableVoices.value = voices[selectedLanguage.value];
const avatarFile = ref(null); // File ảnh
const backgroundFile = ref(null); // File ảnh
const backgroundName = ref('');
const errors = ref({});
const isLoading = ref(false);
const avatarPreview = ref(null);
// Xử lý khi chọn ảnh
const avatarName = ref('');
const handleAvatarChange = (event) => {
    avatarFile.value = event.target.files[0];
    //validate file image only jpg, png, jpeg
    const allowedImageTypes = ["image/jpeg", "image/png", "image/jpg"];
    if (!allowedImageTypes.includes(avatarFile.value.type)) {
        errors.value.avatar = ["Vui lòng chọn file ảnh có định dạng .jpg, .jpeg hoặc .png."]
        avatarFile.value = null;
        return;
    }
    errors.value.avatar = null;
    avatarName.value = avatarFile.value ? avatarFile.value.name : 'Chưa có ảnh nào được chọn';
    selectedAvatar_heygen.value = null;
    //create temp url for preview
    const reader = new FileReader();
    reader.onload = (e) => {
        avatarPreview.value = e.target.result;
    };
    reader.readAsDataURL(avatarFile.value);
};
const handleBackgroundChange = (event) => {
    styleBackground.value = 'custom';
    backgroundFile.value = event.target.files[0];
    backgroundName.value = backgroundFile.value ? backgroundFile.value.name : 'Chưa có ảnh nào được chọn';
};

const showCreditPopup = ref(false);
const handleBuyCredit = () => {
    window.location.href = '/pricing';
};

const handleCancel = () => {
    showCreditPopup.value = false;
};

const showCreditModal = () => {
    showCreditPopup.value = true;
};

const showBuyCreditPopup = ref(false);
const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};
const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
    console.log("🚀 ~ showBuyCreditModal ~ showBuyCreditPopup.value:", showBuyCreditPopup.value)
};
const checkCredit = async ({
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
                        toast.error("Đã xảy ra lỗi. Vui lòng thử lại");
                    });
                } else {
                    toast.error("Đã xảy ra lỗi. Vui lòng thử lại");
                }
            });
        } else {
            if (error.response) {
                toast.error(('Đã xảy ra lỗi.'));
            } else if (error.request) {
                toast.error('Không thể kết nối đến server.');
            } else {
                toast.error('Đã xảy ra lỗi');
            }
        }
        return false;
    }
};

// Submit form
const handleSubmit = async () => {
    if ((!selectedAvatar_heygen.value?.avatar_id
        && !selectedAvatar_heygen.value?.id
        && !avatarFile.value)) {
        toast.error("Vui lòng chọn hình ảnh để tạo MC");
        return;
    }
    if (!videoDescription.value && !audioUrl.value) {
        toast.error("Vui lòng nhập nội dung cho video hoặc ghi âm nội dung của bạn");
        return;
    }
    try {
        const model = selectedAvatar_heygen.value?.avatar_id ? 'heygen_video_avatar' : 'heygen_photo_avatar';
        const hasEnoughCredit = await checkCredit({
            type: model,
            model: model,
            number_result: videoDescription.value ? estimateDuration(videoDescription.value || "") : await getAudioDuration(audioUrl.value) || 0,
        });
        if (!hasEnoughCredit) {
            return; // Dừng nếu không đủ credit
        }
        // if (!validateForm()) return;
        const actions = {
            "heygen": {
                "talk": generateHeyGenClips,
                "clips": generateHeyGenClips
            }
        };

        const modelActions = actions[selectedModel.value];
        if (modelActions) {
            const action = modelActions[selectedContentType.value];
            if (action) {
                await action();
            }
        }
    } catch (error) {
        isLoading.value = false;
    } finally {
        isLoading.value = false;
    }
};

const generateHeyGenClips = async () => {
    errors.value = {};
    isLoading.value = true;
    const formData = new FormData();
    formData.append("script", videoDescription.value);
    if (selectedAvatar_heygen.value?.avatar_id) {
        formData.append("avatar_id", selectedAvatar_heygen.value?.avatar_id);
    } else {
        if (selectedAvatar_heygen.value?.id) {
            formData.append("talking_photo_id", selectedAvatar_heygen.value?.id);
        }
    }
    formData.append("voice", selectedVoice.value);
    if (backgroundFile.value && styleBackground.value === 'custom') {
        formData.append("background_url", backgroundFile.value);
    }
    formData.append("background_style", styleBackground.value);
    if (styleBackground.value === 'color') {
        formData.append("background_color", styleBackgroundColor.value);
    }
    formData.append("caption", enableCaption.value);
    if (audioUrl.value) {
        formData.append('audio_url', audioUrl.value);
        // if (selectedClonedVoice.value) {
        //     formData.append("voice_type_id", selectedClonedVoice.value);
        // }
    }
    formData.append("type_image", typeOfImageHeyGen.value);
    if (typeOfImageHeyGen.value === 'custom') {
        formData.append("avatar", avatarFile.value);
    }
    if (audioResult.value.id) {
        formData.append("audio_id", audioResult.value.id);
    }
    formData.append("aspect_ratio", selectedAspectRatio.value);
    try {
        const response = await axios.post(
            route("mc-virtual.generate-clips-heygen"),
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        if (response.status === 200) {
            getVideoProcessing()
        }
    } catch (error) {
        if (error.response && error.response.status === 403) {
            console.log("error.response ", error.response);
            showCreditModal();
        } else {
            if (error?.response?.data?.errors?.avatar) {
                toast.error(error?.response?.data?.errors?.avatar[0] || "Có lỗi xảy ra, vui lòng thử lại sau");
            }
            else {
                errors.value = error?.response?.data?.errors
                console.error("Có lỗi xảy ra:", error);
                if (Array.isArray(error?.response?.data?.errors)) {
                    errors.value = error?.response?.data?.errors
                } else {
                    toast.error("Có lỗi xảy ra, vui lòng thử lại sau");
                }
            }
        }
        isLoading.value = false;
    } finally {
        isLoading.value = false;
    }
};

const isRecording = ref(false);
const audioBlob = ref(null);
const audioUrl = ref(null);
let mediaRecorder = null;
let audioChunks = [];
const audioResult = ref({});
const disabledRecord = ref(false);
const isDisableRelearn = ref(true);
const isLoadingAudio = ref(false);
let device = ref(null);

const onAudioFileSelected = (event) => {
  const input = event.target;
  const file = input?.files?.[0];

  if (file && file.type.startsWith('audio/')) {
    const fileURL = URL.createObjectURL(file);
    audioUrl.value = fileURL;
    audioBlob.value = file; // Gán file làm blob để upload
    getContentFormAudio(); // Gọi hàm upload luôn sau khi chọn
  } else {
    toast.error("Vui lòng chọn một tệp audio hợp lệ.");
    audioUrl.value = null;
    audioBlob.value = null;
  }
};


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
                audioBlob.value = new Blob(audioChunks.value, {
                    type: 'audio/mp3',
                });
                // audioUrl.value = URL.createObjectURL(audioBlob.value);
                disabledRecord.value = true;
                isDisableRelearn.value = false;
                isCloneVoice.value = false;
                await getContentFormAudio();
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

const getContentFormAudio = async () => {
    if (!audioBlob.value) {
        toast.error("Chưa có audio để gửi. Vui lòng ghi âm trước.");
        return;
    }
    const audioFile = new File([audioBlob.value], "audio", {
        type: "audio/mp3",
    });
    isLoadingAudio.value = true;
    const formData = new FormData();
    formData.append("file", audioFile);

    try {
        const response = await axios.post(route("ai-audio.uploadV2"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        if (response.data.success) {
            audioUrl.value = response.data.data.s3_url;
        } else {
            console.error("Phản hồi không thành công:", response.data);
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi gửi file:", error.message);
    } finally {
        isLoadingAudio.value = false;
    }
};


const showListContentAI = ref(false);
const handleOpenListContentAI = () => {
    showListContentAI.value = true;
}
const modalStep = ref(1);
const userPrompt = ref("");
const suggestContent = ref('')
const handleGetSuggest = async () => {
    // Kiểm tra credit trước khi gửi
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        showBuyCreditPopup.value = true;
        return; // Dừng nếu không đủ credit
    }
    try {
        isLoading.value = true;
        const response = await axios.get(route('mc-virtual.suggest-content', { content: userPrompt.value }));
        suggestContent.value = response.data.data;
    } catch (e) {
        toast.error("Có lỗi xảy ra khi lấy nội dung gợi ý từ AI.");
    } finally {
        isLoading.value = false;
    }

}

const handleApplyContent = () => {
    if (suggestContent.value.length > 0) {
        videoDescription.value = suggestContent.value;
    }
    showListContentAI.value = false;
}
const checkInterval = ref(null);
const getVideoProcessing = async () => {
    try {
        const response = await axios.get(route('mc-virtual.get-video-process'));
        if (response?.data.id) {
            isLoading.value = true;
            resultValue.value = response.data;
            const res = await axios.get(`/mc-virtual/ajax-fetch-video/${response.data.id}`)
            if (res?.data?.result?.status === 'done') {
                clearInterval(checkInterval.value);
                checkInterval.value = null;
                isLoading.value = false;
                resultValue.value = res.data.result;
                //update credit
                res.data?.result?.credit && (credits.value = res.data.result.credit);
            } else if (res?.data?.result?.status === 'error') {
                isLoading.value = false;
                resultValue.value = null;
                checkInterval.value = null;
                toast.error("Có lỗi xảy ra khi tạo video của bạn. Vui lòng thử lại sau.");
            } else {
                checkInterval.value = setInterval(async () => {
                    const res = await axios.get(`/mc-virtual/ajax-fetch-video/${response.data.id}`)
                    isLoading.value = true;
                    if (res?.data?.result?.status === 'done') {
                        isLoading.value = false;
                        resultValue.value = res.data.result;
                        //update credit
                        res.data?.result?.credit && (credits.value = res.data.result.credit);
                        clearInterval(checkInterval.value);
                        checkInterval.value = null;
                    } else if (res?.data?.result?.status === 'error') {
                        isLoading.value = false;
                        resultValue.value = null;
                        clearInterval(checkInterval.value);
                        checkInterval.value = null;
                        toast.error("Có lỗi xảy ra khi tạo video của bạn. Vui lòng thử lại sau.");
                    }
                }, 8000);
            }
        }
    } catch (error) {
    }
}

const isShowMore = ref(false);
const toggleShowMore = (event) => {
    event.stopPropagation();
    isShowMore.value = !isShowMore.value;
};

const isShowShareLinkModal = ref(false);
const shareLink = ref(null);

const openShareLink = () => {
    isShowShareLinkModal.value = true;
};

const closeShareLink = () => {
    isShowShareLinkModal.value = false;
};
const shareAIMcVirtual = async (item) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: item.id,
            share_linkable_type: 'McVirtual',
        });

        shareLink.value = route("share-link.show", { 'token': response.data.data.link_token })
        openShareLink();

    } catch (error) {
        toast.error("Chia sẻ tin thất bại");
    }
};
const downloadVideo = async (video) => {
    if (!video) {
        alert("Chưa có video");
        return;
    }
    var url = route(("ai-video.downloadFile"), { url: video, name: "video.mp4" })
    window.location.href = url
};
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
// Add cleanup in onBeforeUnmount at component level
onBeforeUnmount(() => {
    if (checkInterval.value) {
        clearInterval(checkInterval.value);
        checkInterval.value = null;
    }
});

const videoIdToDelete = ref(null);
const showConfirmModal = ref(false);
const mergeVideo = async (item) => {
    isLoading.value = true;
    try {
        const response = await axios.post(route("mc-virtual.merge-voice-with-audio"), {
            id: item.id,
            ai_generated_media_id: item.ai_generated_media_id,
        });
        if (response.status === 200) {
            alert("Giọng thật đã được áp dụng thành công!");
            window.location.reload();
        }
    } catch (error) {
        console.error("Error applying real voice:", error.message);
        alert("Có lỗi xảy ra khi áp dụng giọng thật.");
    } finally {
        isLoading.value = false;
    }
};

const cancelDelete = () => {
    showConfirmModal.value = false;
    videoIdToDelete.value = null;
};

const confirmDeleteVideo = async () => {
    isLoading.value = true;
    try {
        const response = await axios.post(route("mc-virtual.delete", { id: videoIdToDelete.value }));
        if (response.status === 200) {
            resultValue.value = {};
            alert("Video đã được xóa thành công.");
        }
    } catch (error) {
        console.error("Error deleting video:", error.message);
        alert("Có lỗi xảy ra khi xóa video.");
    } finally {
        isLoading.value = false;
        showConfirmModal.value = false;
        videoIdToDelete.value = null;
    }
};

const isInputRecording = ref(false);
const audioInputBlob = ref(null);
const audioInputUrl = ref(null);
let mediaInputRecorder = null;
let audioInputChunks = [];
const audioInputResult = ref({});
let deviceInput = ref(null);
const isTranscribingInput = ref(false);
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

const stopInputRecording = () => {
    if (mediaInputRecorder) {
        mediaInputRecorder.stop();
        isInputRecording.value = false;
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

// Add new refs for recording
const isRecordVoice = ref(false);
const recordingTime = ref(0);
const recordingInterval = ref(null);
let recordedAudioChunks = [];
let recordingMediaRecorder = null;

// Format time function
const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

// Start recording function
const startRecordingCloneVoice = async () => {
    if (!videoDescription.value) {
        toast.error("Vui lòng nhập lời thoại trước khi nhái giọng");
        return;
    }
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
            if (recordingTime.value <= 60) {
                toast.error("Âm thanh không được ít hơn 60 giây");
                audioFile.value = null;
                recordedAudioChunks = [];
                return;
            }
            handleVoiceClone();
            recordingTime.value = 0;
        };

        recordingMediaRecorder.start();
        isRecordVoice.value = true;
        recordingTime.value = 0;

        // Start timer
        recordingInterval.value = setInterval(() => {
            recordingTime.value++;
            // Automatically stop after 5 minutes
            if (recordingTime.value >= 5 * 60) {
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

const selectedAspectRatio = ref('16:9');
const handleChangeAspectRatio = (value) => {
    selectedAspectRatio.value = value.target.value;
};

const isDropdownOpen = ref(false);
const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};
const selectVoice = (voice) => {
    console.log("selectVoice ~ voice:", voice)
    selectedVoice.value = voice.value_heyGen;
    isDropdownOpen.value = false;
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
const getVoiceName = (type) => {
    const voice = availableVoices.value.find(v => v.value_heyGen === type);
    return voice ? voice.text : 'Bạn chưa tạo giọng nói';
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
    margin-left: 25%;
    animation: spin 2s linear infinite;
}

.showLoaderVideo {
    display: flex;
    justify-content: center;
    flex-direction: column-reverse;
    justify-content: center;
    margin-left: 50;
}

.text-loading {
    color: black;
    text-align: center;
    margin-top: 20px;
}

input[type="checkbox"]:checked {
    background-color: #005F49;
    border-color: #005F49; /* Optional, if you want to change the border color as well */
}

input[type="radio"]:checked {
    background-color: #005F49;
    border-color: #005F49; /* Optional, if you want to change the border color as well */
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
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
    display: flex;
    justify-content: center;
    align-items: center;
    width: 20px;
    height: 20px;
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

.loaderAudio {
    width: 30px !important;
    height: 30px !important;
    margin-left: 0px !important;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #24AA69;
}
</style>
