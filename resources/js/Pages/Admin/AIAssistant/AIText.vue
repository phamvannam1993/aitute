<template>

    <Head title="Bài viết - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div class="w-full pb-[20px] md:pb-0 px-[20px] xl:px-[44px]">
        <div class="w-full text-black">
            <div class="relative items-start gap-4 lg:px-[20px] xl:px-[52px]">
                <div id="scroll-target" class="flex gap-4 mb-3 items-center justify-between">
                    <div class="flex items-center gap-2 md:gap-4">
                        <img :src="ai_assistant?.thumbnail_url ? ai_assistant?.thumbnail_url
                            : '/assets/img/ai_text/ai_text.png'
                            " class="size-[52px] md:size-[72px] lg:size-[110px] rounded-[13px]" alt="" />
                        <h1 class="text-[#092A99] font-bold text-[14px] md:text-[18px] lg:text-[24px] line-clamp-2">
                            {{ ai_assistant?.name }}
                        </h1>
                    </div>
                </div>
            </div>
            <form @submit.prevent="submit">
                <div class="w-full max-w-screen">
                    <div class="flex flex-col md:flex-row gap-4 mt-6">
                        <div class="relative" :class="activeForm ? 'w-full md:w-[40%]' : 'w-0 mr-8'">
                            <div v-if="!activeForm" @click="toggleForm"
                                 class="absolute left-0 bg-white rounded-r-lg cursor-pointer z-[100] size-10 flex items-center justify-center">
                                <img src="/assets/img/open_menu.png" alt="Open Menu" />
                            </div>
                            <div v-if="activeForm" class="w-full ">
                                <div class="bg-white p-[16px]">
                                    <div @click="toggleForm"
                                        class="w-full cursor-pointer py-2 flex items-center justify-end">
                                        <img src="/assets/img/close_menu.png" alt="Close Menu" />
                                    </div>
                                    <div class="">
                                        <div class="mb-3">
                                            <span class="font-semibold text-[14px] md:text-[16px] xl:text-[20px]">Nội dung cần làm:</span>
                                            <textarea v-model="content" rows="3"
                                                placeholder="Tên sản phẩm, tính năng lợi ích, đặc điểm nổi bật..."
                                                class="resize-none w-full border-[#ccc] font-medium text-[12px] md:text-[14px] xl:text-[16px] rounded-lg mt-2">
                                            </textarea>
                                            <div class="object-mic relative ml-auto">
                                                <div
                                                    v-if="isRecording"
                                                    class="outline-mic right-3 bottom-3 flex items-center justify-center"
                                                ></div>
                                                <div
                                                    v-if="isRecording"
                                                    class="outline-mic right-3 bottom-3 flex items-center justify-center"
                                                    id="delayed"
                                                ></div>
                                                <div
                                                    v-if="isRecording"
                                                    class="button-mic right-3 bottom-3 flex items-center justify-center"
                                                ></div>
                                                <button
                                                    class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center"
                                                    @click="startRecording"
                                                    type="button"
                                                >
                                                    <svg class="h-6 w-6" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000">
                                                        <g>
                                                            <path
                                                                d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z"
                                                            />
                                                        </g>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div v-if="criteriaList.length > 0" class="mb-4">
                                            <span class="font-semibold mb-2 text-[14px] md:text-[16px] xl:text-[20px]">Tiêu chí:</span>
                                            <div v-for="(criteria, index) in criteriaList" :key="index" class="mb-4">
                                                <div v-if="criteria.type === 'input'" class="mb-2">
                                                    <label class="block text-gray-700 font-semibold mb-1 text-[12px] md:text-[14px] xl:text-[16px]">{{ criteria.label || 'Tiêu chí dạng text' }}</label>
                                                    <input
                                                        type="text"
                                                        v-model="criteria.value"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-black text-[10px] md:text-[12px] xl:text-[14px]"
                                                        :placeholder="criteria.placeholder || '' "
                                                    />
                                                    <div class="object-mic relative ml-auto">
                                                        <div
                                                            v-if="isRecordingInput && recordingIndex === index"
                                                            class="outline-mic right-3 bottom-1.5 flex items-center justify-center"
                                                        ></div>
                                                        <div
                                                            v-if="isRecordingInput && recordingIndex === index"
                                                            class="outline-mic right-3 bottom-1.5 flex items-center justify-center"
                                                            id="delayed"
                                                        ></div>
                                                        <div
                                                            v-if="isRecordingInput && recordingIndex === index"
                                                            class="button-mic right-3 bottom-1.5 flex items-center justify-center"
                                                        ></div>
                                                        <button
                                                            class="button-mic icon-mic absolute right-3 bottom-1.5 flex items-center justify-center"
                                                            @click="startRecordingInput(index)"
                                                            type="button"
                                                        >
                                                            <svg class="h-6 w-6" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000">
                                                                <g>
                                                                    <path
                                                                        d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z"
                                                                    />
                                                                </g>
                                                            </svg>
                                                        </button>

                                                    </div>
                                                </div>
                                                <div v-else-if="criteria.type === 'select' && !criteria.multiple" class="mb-2">
                                                    <label class="block text-gray-700 font-semibold mb-1 text-[12px] md:text-[14px] xl:text-[16px]">{{ criteria.label || 'Tiêu chí dạng select box' }}</label>
                                                    <select v-model="criteria.selectedValue" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-black text-[10px] md:text-[12px] xl:text-[14px]">
                                                        <option v-for="(option, optIndex) in getParsedOptions(criteria.value)" :key="optIndex" :value="option.value">
                                                            {{ option.text }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <div v-else-if="criteria.type === 'select' && criteria.multiple" class="mb-2">
                                                    <label class="block text-gray-700 font-semibold mb-1 text-[12px] md:text-[14px] xl:text-[16px]">{{ criteria.label || 'Tiêu chí dạng select box' }}</label>
                                                    <MultiSelectClient
                                                        :options="getParsedOptions(criteria.value)"
                                                        index="index"
                                                        @update:selectedOptions="(value) => handleSelectOptions(value, index)"
                                                        :selectedValues="criteria.selectedValues || []"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="flex flex-row gap-1 lg:w-[100%] justify-around items-end mb-4">
                                                <div class="w-full">
                                                    <div class="flex flex-col">
                                                        <span class="font-semibold mb-2 text-[14px] md:text-[16px] xl:text-[20px]">
                                                            Số từ bạn muốn tạo:
                                                        </span>
                                                        <div class="flex items-center space-x-2">
                                                            <button @click="decreaseLimit"
                                                                    class="size-[44px] rounded-[5px] border-[1px] border-[#2C75E3] flex items-center justify-center px-2">
                                                                <img src="/assets/img/ai_text/subtract.png"
                                                                     alt="Subtract"
                                                                     class="size-[20px] md:size-[24px] lg:size-[28px] xl:size-[32px]" />
                                                            </button>

                                                            <input v-model="selectedLimit" type="text"
                                                                   class="border-[#2C75E3] text-center h-[44px] w-[144px] flex-1 border-[1px] rounded-[5px] text-[14px] md:text-[20px] font-medium" />

                                                            <button @click="increaseLimit"
                                                                    class="size-[44px] rounded-[5px] border-[1px] border-[#2C75E3] flex items-center justify-center px-2">
                                                                <img src="/assets/img/ai_text/plus.png" alt="Plus"
                                                                     class="size-[20px] md:size-[24px] lg:size-[28px] xl:size-[32px]" />
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="w-full mb-4">
                                                <div class="relative flex flex-col w-[100%]">
                                                    <span class="font-semibold mb-2 text-[14px] md:text-[16px] xl:text-[20px]">Ngôn ngữ:</span>
                                                    <div class="relative w-full">
                                                        <div @click="toggleDropdown('language')"
                                                             class="cursor-pointer flex items-center border rounded-[5px] py-2 px-3 border-[#2C75E3]">
                                                            <div class="flex items-center flex-1">
                                                                <img :src="selectedLanguage.flag"
                                                                     class="w-auto h-[24px] md:h-[32px] mr-2 border-[1px]"
                                                                     alt="Flag" />
                                                                <span class="text-[14px] md:text-[20px] font-medium">{{
                                                                    selectedLanguage.name }}</span>
                                                            </div>
                                                            <img src="/assets/img/ai_text/expand.png"
                                                                 class="w-[20px] md:w-[26px] h-auto" alt="Expand" />
                                                        </div>

                                                        <ul v-if="dropdownOpen === 'language'"
                                                            class="absolute bg-white border border-[#2C75E3] rounded-md mt-1 w-full z-50">
                                                            <li v-for="(item, index) in language" :key="index"
                                                                @click="selectLanguage(item)"
                                                                class="cursor-pointer flex items-center p-2 hover:bg-gray-100">
                                                                <img :src="item.flag" class="w-6 h-4 mr-2" alt="Flag" />
                                                                <span>{{ item.name }}</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="relative flex flex-col mb-4">
                                                <span class="font-semibold mb-2 text-[14px] md:text-[16px] xl:text-[20px]">Mô hình AI:</span>
                                                <div class="relative w-full">
                                                    <div @click="toggleDropdown('model')"
                                                         class="cursor-pointer flex items-center border rounded-[5px] py-2 px-3 border-[#2C75E3]">
                                                        <div class="flex items-center flex-1">
                                                            <span class="text-[14px] md:text-[20px] font-medium">{{
                                                                selectedModel }}</span>
                                                        </div>
                                                        <img src="/assets/img/ai_text/expand.png"
                                                             class="w-[20px] md:w-[26px] h-auto" alt="Expand" />
                                                    </div>

                                                    <ul v-if="dropdownOpen === 'model'"
                                                        class="absolute bg-white border border-[#2C75E3] rounded-md mt-1 w-full z-50">
                                                        <li v-for="(item, index) in models" :key="index"
                                                            @click="selectModel(item)"
                                                            class="cursor-pointer p-2 hover:bg-gray-100">
                                                            {{ item }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="relative flex flex-col mb-4">
                                                <div class="flex flex-col xl:flex-row items-start xl:items-center gap-4 w-full">
                                                    <!-- Toggle Switch -->
                                                    <div class="flex items-center gap-3 w-full xl:w-[45%]">
                                                        <label class="relative inline-flex items-center cursor-pointer">
                                                            <input type="checkbox" v-model="isActiveGdthLeft" class="sr-only peer">
                                                            <div
                                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                                            </div>
                                                        </label>
                                                        <span class="text-sm text-gray-700">Giọng điệu thương hiệu</span>
                                                    </div>

                                                    <!-- Dropdown for tone selection -->
                                                    <div v-if="isActiveGdthLeft" class="flex items-center gap-3 w-full xl:w-[55%] dropdown-custom">
                                                        <div class="relative inline-block w-full">
                                                            <button
                                                                @click="isOpenLeft = !isOpenLeft"
                                                                class="flex justify-between items-center gap-2 w-full px-4 py-2 bg-white hover:bg-gray-50 rounded-md shadow-sm border border-gray-300 focus:ring focus:ring-blue-200"
                                                            >
                                                                <span class="text-sm text-gray-700">{{ selectedToneLeft }}</span>
                                                                <svg
                                                                    class="w-4 h-4 text-gray-500 transition-transform duration-200"
                                                                    :class="{ 'rotate-180': isOpenLeft }"
                                                                    fill="none"
                                                                    viewBox="0 0 24 24"
                                                                    stroke="currentColor"
                                                                >
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                                </svg>
                                                            </button>

                                                            <!-- Dropdown Items -->
                                                            <div
                                                                v-if="isOpenLeft"
                                                                class="absolute left-0 mt-2 w-full bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 max-h-40 overflow-y-auto"
                                                            >
                                                                <button
                                                                    v-for="tone in tones"
                                                                    :key="tone"
                                                                    @click="selectToneLeft(tone)"
                                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2"
                                                                    :class="{ 'bg-blue-50': selectedTone === tone }"
                                                                >
                                                                    <svg
                                                                        v-if="selectedToneLeft === tone"
                                                                        class="w-4 h-4 text-blue-600"
                                                                        fill="none"
                                                                        viewBox="0 0 24 24"
                                                                        stroke="currentColor"
                                                                    >
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                    <span :class="{ 'font-medium': selectedToneLeft === tone }">{{ tone }}</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-full flex flex-col">
                                            <span class="font-semibold mb-2 text-[14px] md:text-[16px] xl:text-[20px]">Chọn tài liệu để làm nội dung tham khảo:</span>
                                            <div class="flex flex-wrap items-center gap-4">
                                                <!-- Hiển thị danh sách file đã chọn -->
                                                <div
                                                    v-for="(file, index) in files"
                                                    :key="index"
                                                    class="relative flex items-center justify-center border border-gray-300 rounded-lg bg-white shadow-sm cursor-pointer"
                                                    style="width: 80px; height: 80px; position: relative;"
                                                >
                                                    <!-- Biểu tượng file -->
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24"
                                                        class="w-10 h-10 text-blue-600"
                                                        fill="currentColor"
                                                    >
                                                        <path d="M4 3h15a2 2 0 0 1 2 2v15a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm15 2H4v15h15V5zm-2 3H6v2h11V8zm-11 4h8v2H6v-2z"/>
                                                    </svg>
                                                    <div
                                                        class="absolute top-0 left-0 right-0 bg-black bg-opacity-75 text-white text-xs text-center py-1 opacity-0 hover:opacity-100 transition-opacity"
                                                    >
                                                        {{ file.name }}
                                                    </div>

                                                    <!-- Nút xóa -->
                                                    <button
                                                        @click="removeFile(index)"
                                                        class="absolute top-0 right-0 bg-black text-white w-5 h-5 flex items-center justify-center rounded-full"
                                                        style="transform: translate(50%, -50%);"
                                                    >
                                                        ×
                                                    </button>
                                                </div>

                                                <!-- Nút chọn tài liệu -->
                                                <label
                                                    class="cursor-pointer inline-flex items-center p-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-blue-600 bg-white hover:bg-blue-50"
                                                    v-if="files.length === 0"
                                                >
                                                    <img src="/assets/svgs/upload-icon.svg" class="pr-2" />
                                                    <span>Chọn tài liệu</span>
                                                    <input type="file" multiple class="hidden" @change="handleFileUpload" />
                                                </label>
                                            </div>

                                            <!-- Nút gửi -->
                                            <button
                                                @click="sendData(step, index)"
                                                :disabled="isLoading"
                                                class="h-[40px] md:h-[48px] text-white rounded-[10px] w-full md:w-[96px] text-center bg-[#2C75E3] mt-4"
                                            >
                                                <span v-if="!isLoading">Gửi</span>
                                                <div v-else role="status" class="w-full">
                                                    <svg
                                                        aria-hidden="true"
                                                        class="w-8 h-8 mx-auto text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                                                        viewBox="0 0 100 101"
                                                        fill="none"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                    >
                                                        <path
                                                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                            fill="currentColor"
                                                        />
                                                        <path
                                                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                            fill="currentFill"
                                                        />
                                                    </svg>
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                                <div
                                    class="flex flex-col gap-2 md:gap-4 bg-white p-[16px] mt-4 md:mt-8 max-h-60 overflow-y-auto">
                                    <div v-for="(step, index) in step_ais" :key="index" :class="dropDownStep === index
                                        ? 'cursor-pointer border rounded-[5px] py-2 px-3 border-[#2C75E3]'
                                        : 'cursor-pointer border rounded-[5px] py-2 px-3 border-transparent'">
                                        <div class="flex gap-2 items-center">
                                            <div
                                                class="size-6 md:size-8 flex items-center justify-center rounded-full bg-[#2c75e3] text-[12px] md:text-[20px] text-white">
                                                <span>{{ index + 1 }}</span>
                                            </div>
                                            <div class="flex flex-1 items-center p-1 rounded-lg w-full justify-between">
                                                <div class="flex-1">
                                                    <h2 class="text-[14px] md:text-[20px] font-medium line-clamp-1">{{
                                                        step.name }}</h2>
                                                </div>
                                                <div>
                                                    <div v-if="
                                                        dropDownStep ===
                                                        index && isLoading
                                                    ">
                                                        <svg aria-hidden="true"
                                                             class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                                                             viewBox="0 0 100 101" fill="none"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                                fill="currentColor" />
                                                            <path
                                                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                                fill="currentFill" />
                                                        </svg>
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                    <img v-else-if="step.success" class="w-8 max-w-8"
                                                         src="/assets/svgs/success.svg" alt="" />
                                                    <span v-else role="status">
                                                        <img src="/assets/img/ai_text/expand.png"
                                                             class="w-[20px] md:w-[26px] h-auto ml-2" alt="Expand" />
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="relative w-full flex flex-col bg-white mt-8 md:mt-0 p-[16px] transition-all duration-200"
                            :class="{ 'overflow-x-auto': !isOpen }"

                        >
                            <div class="flex justify-between mb-3 items-center w-full">
                                <span class="font-semibold text-[14px] md:text-[16px] text-center w-full">Nội
                                    dung</span>

                            </div>
                            <!--                            <Editor v-model="message" editorStyle="color: #000" class="border custom-editor" />-->
                            <textarea ref="editor" class="w-full flex-1" id="editorContainer"></textarea>
                            <div class="chat-container flex items-center justify-center p-2 rounded-lg border border-[#7A5AF8] bg-[#F6F3FE] w-full">
                                <textarea
                                    v-model="askMessage"
                                    type="text"
                                    placeholder="Chat với Aiwow..."
                                    class="w-10/12	 p-3 mt-1 h-[100px] border text-black border-[#2C75E3] rounded-[10px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                ></textarea>
                                <div class="flex items-center space-x-2">
                                    <div class="object-mic relative ml-auto">
                                        <div
                                            v-if="isRecordingAsk"
                                            class="outline-mic right-3 bottom-3 flex items-center justify-center"
                                        ></div>
                                        <div
                                            v-if="isRecordingAsk"
                                            class="outline-mic right-3 bottom-3 flex items-center justify-center"
                                            id="delayed"
                                        ></div>
                                        <div
                                            v-if="isRecordingAsk"
                                            class="button-mic right-3 bottom-3 flex items-center justify-center"
                                        ></div>
                                        <button
                                            class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center"
                                            @click="startRecordingAsk"
                                            type="button"
                                        >
                                            <svg class="h-6 w-6" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000">
                                                <g>
                                                    <path
                                                        d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z"
                                                    />
                                                </g>
                                            </svg>
                                        </button>

                                    </div>

                                    <button type="button" class="attachment-button w-8 h-8 border-2 border-dashed rounded flex items-center justify-center border-[#7A5AF8] w-56" @click="$refs.fileInput.click()">
                                        <div v-if="fileAsk" class="relative ml-2 flex items-center justify-center w-8 h-8 bg-white border border-gray-300 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                                                <!-- Top Left Lines -->
                                                <path d="M11 15H12M19 15H15" stroke="#FF5733" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <!-- Top Right Lines -->
                                                <path d="M19 11H18M15 11H11" stroke="#33FFBD" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <!-- Bottom Line -->
                                                <path d="M11 19H16" stroke="#3380FF" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <!-- Outer Frame -->
                                                <path d="M6 17V13C6 9.5 6 7.5 7.5 6C9 4.5 11 4.5 15 4.5H16C20 4.5 22 4.5 23.5 6C24.3 6.8 24.7 7.8 24.9 9M24 13V17C24 20.5 24 22.5 23 23.5C22 25 20 25 16 25H15C11 25 9 25 7.5 23.5C6.7 22.7 6.3 21.8 6.1 21" stroke="#9B33FF" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <span @click="removeFileAsk" class="absolute -top-1 -right-1 w-4 h-4 bg-black text-white rounded-full flex items-center justify-center text-xs cursor-pointer">×</span>
                                        </div>

                                        <svg  v-else xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" class="mdl-js" data-__embeded-gyazo-content-j-s-teams="5.6.1">
                                            <path d="M9.61053 15.58L16.2606 8.92999C16.9106 8.27999 16.9106 7.23 16.2606 6.58C15.6106 5.93 14.5605 5.93 13.9105 6.58L4.91055 15.58C3.61055 16.88 3.61055 18.98 4.91055 20.28C6.21055 21.58 8.31053 21.58 9.61053 20.28L18.5905 11.3C20.5505 9.34 20.5505 6.17 18.5905 4.22C16.6305 2.26 13.4606 2.26 11.5106 4.22L4.88055 10.85" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <input
                                            type="file"
                                            ref="fileInput"
                                            class="hidden"
                                            @change="handleFileUploadAsk"
                                            accept=".pdf,.doc,.docx,.txt,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain"
                                        />
                                    </button>

                                    </div>
                                <div class="min-w-[100px] min-h-[40px] ml-2 cursor-pointer">
                                    <button
                                        class="send-button bg-[#7A5AF8] text-white rounded px-4 py-2 w-full h-full flex items-center justify-center hover:bg-[#6347d9] transition-colors"
                                        @click="sendAskMessage()"
                                    >
                                        Gửi đi
                                    </button>
                                </div>
                            </div>

                            <div v-if="isLoadingThinking"
                                 class="absolute w-full h-full flex justify-center items-center bg-white bottom-0 opacity-50 z-50">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="loader w-12 h-12 border-4 border-[#5FB2FF] border-b-[#0747C8] rounded-full">
                                    </div>
                                    <p class="mt-2 text-lg text-black font-semibold">Thinking...</p>
                                </div>
                            </div>
                            <div class="flex items-center xl:items-center justify-between mt-4 gap-2">
                                <div
                                    class="flex flex-col justify-start xl:flex-row items-start xl:items-center gap-2 w-full">
                                    <div class="flex items-center gap-3 w-[40%]">
                                        <!-- Icon và text -->
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 4V20M8 8V16M16 8V16M4 10V14M20 10V14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                            <span class="text-sm text-gray-700">Giọng điệu thương hiệu</span>
                                        </div>

                                        <!-- Toggle switch -->
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" v-model="isActiveGdth" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    <div
                                        class="flex flex-col justify-start xl:flex-row items-start xl:items-center gap-2 w-full dropdown-custom">
                                        <div class="relative inline-block text-left">
                                            <button
                                                @click="isOpen = !isOpen"
                                                class="flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 rounded-full shadow-md"
                                            >
                                                <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 4V20M8 8V16M16 8V16M4 10V14M20 10V14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                                <span class="text-sm text-gray-700">{{ selectedTone }}</span>
                                                <svg
                                                    class="w-4 h-4 text-gray-500 transition-transform duration-200"
                                                    :class="{ 'rotate-180': isOpen }"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>

                                            <div
                                                v-if="isOpen"
                                                class="absolute left-0 mt-2 w-40 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5  max-h-[150px] overflow-y-auto"
                                                style="position: absolute; z-index: 999999;"
                                            >
                                                <div class="py-1 z-[9999]">
                                                    <button
                                                        v-for="tone in tones"
                                                        :key="tone"
                                                        @click="selectTone(tone)"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2"
                                                        :class="{ 'bg-blue-50': selectedTone === tone }"
                                                    >
                                                        <svg
                                                            v-if="selectedTone === tone"
                                                            class="w-4 h-4 text-blue-600"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke="currentColor"
                                                        >
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span :class="{ 'font-medium': selectedTone === tone }">{{ tone }}</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

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
import MultiSelectClient from '../../../Components/MultiSelectClient.vue';
    const props = defineProps({
        ai_assistant: Object,
        job: Object,
        operation: Object,
    });

    const additionalCredit = ref(0);
    const showAffKeyPopup = ref(false);
    const emit = defineEmits(['update-credits'])

    const activeForm = ref(true);
    const isActiveGdth = ref(false);
    const isActiveGdthLeft = ref(false);
    const toggleForm = () => {
        activeForm.value = !activeForm.value;
    };
    const isShowShareLinkModal = ref(false);
    const shareLink = ref(null);
    const selectedLimit = ref("50");
    const selectedModel = ref("GPT-4o");
    const selectedLanguage = ref({
        name: "Tiếng Việt",
        code:"vi-VN",
        flag: "https://flagcdn.com/w320/vn.png"
    });
    // const selectedLanguage = ref("Tiếng Việt");
    const file = ref(null);
    const fileAsk = ref(null);
    const files = ref([]); // Store multiple files
    const ai_assistant = ref(props.ai_assistant);
    const criteriaList = ref(props.ai_assistant.criteria || []);
    console.log('criteriaList', criteriaList)
    const job = ref(props.job);
    const operation = ref(props.operation);
    const step_ais = ref(
        props.ai_assistant.step_ais.map((step) => ({
            ...step,
            model: selectedModel.value,
            language: selectedLanguage.value.name,
            limit: selectedLimit.value,
            success: false,
        }))
    );
    const selectedVoice = ref("");
    const voice = ref([
        "Tương tác",
        "Hài Hước",
        "Thuyết phục",
        "Vui nhộn",
        "Lịch sự và tôn trọng",
        "Thoải mái",
        "Cuốn hút",
        "So sánh",
        "Tự tin",
    ]);
    const limit = ref(["50", "100", "200", "500", "1000", "2000", "3000"]);
    const models = ["Claude 3.5 Sonnet", "GPT-4o mini", "GPT-4o"];

    const dropdownOpen = ref(null);

    const language = [
        { name: "Tiếng Việt", flag: "https://flagcdn.com/w320/vn.png", code:"vi-VN" },
        { name: "Tiếng Anh", flag: "https://flagcdn.com/w320/gb.png", code:"en-US" },
        { name: "Tiếng Trung", flag: "https://flagcdn.com/w320/cn.png", code:"zh-CN"  },
        { name: "Tiếng Nhật", flag: "https://flagcdn.com/w320/jp.png", code:"ja-JP"  },
        { name: "Tiếng Hàn", flag: "https://flagcdn.com/w320/kr.png", code:"ko-KR"  },
        { name: "Tiếng Pháp", flag: "https://flagcdn.com/w320/fr.png" },
        { name: "Tiếng Đức", flag: "https://flagcdn.com/w320/de.png" },
    ];

    const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
    const acceptUpgrade = ref(true)

    const isOpen = ref(false);
    const selectedTone = ref('Thoải mái');
    const isOpenLeft = ref(false);
    const selectedToneLeft = ref('Thoải mái');
    const tones = [
        // Nhóm 1
        'Tương tác',
        'Thuyết phục',
        'Hài hước',
        'Vui nhộn',

        // Nhóm 2
        'Lịch sự và Tôn trọng',
        'Thoải mái',
        'Cực kỳ thoải mái',
        'Cuốn hút',
        'Truyền cảm hứng',

        // Nhóm 3
        'Tạo sự tò mò',
        'So sánh',
        'Quyền lực',
        'Tự tin',
        'Lãnh lùng',

        // Nhóm 4
        'Mỉa mai',
        'Xúc động',
        'Đồng cảm',
        'Chăm biếm',
        'Lạc quan',

        // Nhóm 5
        'Bi quan',
        'Nghiêm túc',
        'Ám ảnh'
    ];

    const selectTone = (tone) => {
        selectedTone.value = tone;
        isOpen.value = false;
    }

    const selectToneLeft = (tone) => {
        selectedToneLeft.value = tone;
        isOpenLeft.value = false;
    }

    const closeOnClickOutside = (e) => {
        if (!e.target.closest('.dropdown-custom')) {
            isOpen.value = false
            isOpenLeft.value = false;
        }
    }

    onMounted(() => {
        document.addEventListener('click', closeOnClickOutside)
    });

    onUnmounted(() => {
        document.removeEventListener('click', closeOnClickOutside)
    });


    function openPostToFacebook() {
        if(!props.fanpages.length){
            const message = `Vui lòng thêm fanpages để tiếp tục.</br><a href="`+route('calendar')+`" target="_blank" style="color: #24AA69; text-decoration: underline;">Thêm</a>`;
            toast.error(message,{
                dangerouslyHTMLString: true,
                autoClose: 5000,
            });
            return false;
        }

        console.log(step_ais)

        if (postToFacebookRef.value) {
            postToFacebookRef.value.openPostToFacebook();
        }
    }

    const toggleDropdown = (dropdownType) => {
        if (dropdownOpen.value === dropdownType) {
            dropdownOpen.value = null;
        } else {
            dropdownOpen.value = dropdownType;
        }
    };

    const selectLanguage = (language) => {
        selectedLanguage.value = language;
        dropdownOpen.value = null;
    };

    const selectModel = (model) => {
        selectedModel.value = model;
        dropdownOpen.value = null;
    };

    const handleClickOutside = (event) => {
        const dropdowns = document.querySelectorAll('.cursor-pointer');
        let isClickInside = false;

        dropdowns.forEach(dropdown => {
            if (dropdown.contains(event.target)) {
                isClickInside = true;
            }
        });

        if (!isClickInside) {
            dropdownOpen.value = null;
        }
    };

    onMounted(() => {
        document.addEventListener('click', handleClickOutside);
    });

    onBeforeUnmount(() => {
        document.removeEventListener('click', handleClickOutside);
    });

    let messageIdToDelete = ref(null);
    const message_id = ref("");
    const message = ref("");
    const css = ref("");
    const isLoading = ref(false);
    const isLoadingThinking = ref(false);
    const isLoadingMore = ref(false);
    const isDeleteLoading = ref(false);
    const history = ref([]);
    const assistant_id = ref(null);
    const dropDownStep = ref(0);
    const content = ref("");
    const media_link = ref([]);
    const activeHistory = ref(false);
    const isShowMedia = ref(false);
    const isImageTab = ref(true);

    const imagePrompt = ref("");
    const postContent = ref("");
    const multiSelected = ref([]);
    const isRecording = ref(false);
    const aiTextConversations = ref();
    const isRecordingAsk = ref(false);
    const audioBlob = ref(null);
    const audioUrl = ref(null);
    let mediaRecorder = null;
    let audioChunks = [];
    const audioResult = ref({});
    let device = ref(null);
    const isTranscribing = ref(false);
    const isRecordingInput = ref(false);
    const recordingIndex = ref(null);
    const startRecordingAsk = async () => {

        if (!isRecordingAsk.value) {
            // Nếu chưa ghi âm
            try {
                isRecordingAsk.value = true; // Bắt đầu ghi âm
                console.log('startRecordingAsk', isRecordingAsk.value)
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
                        const response = await axios.post('/admin/speech-to-text', formData, {
                            headers: { "Content-Type": "multipart/form-data" },
                        });
                        // Xử lý văn bản trả về từ API
                        console.log("Transcription Text:", response);
                        askMessage.value = response?.data?.text || 'Vui lòng thử lại';
                    } catch (error) {
                        console.error("Error in Speech-to-Text:", error);
                    } finally {
                        // isTranscribing.value = false;
                    }

                    isRecordingAsk.value = false;
                });

                // Bắt đầu ghi âm
                mediaRecorder.onstart = () => {
                    audioChunks.value = []; // Xóa các đoạn âm thanh trước đó
                };

                mediaRecorder.start(); // Bắt đầu ghi
            } catch (error) {
                console.error("Lỗi khi bắt đầu ghi âm:", error);
                isRecordingAsk.value = false; // Trở lại trạng thái chưa ghi âm nếu có lỗi
            }
        } else {
            // Nếu đang ghi âm
            isRecordingAsk.value = false; // Dừng ghi âm
            mediaRecorder.stop(); // Kết thúc ghi âm
            device.getTracks().forEach((track) => track.stop()); // Dừng tất cả các tracks
        }
    };

    const startRecordingInput = async (index) => {
        if (!isRecordingInput.value) {
            // Nếu chưa ghi âm
            try {
                isRecordingInput.value = true; // Bắt đầu ghi âm
                recordingIndex.value = index;
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
                        const response = await axios.post('/admin/speech-to-text', formData, {
                            headers: { "Content-Type": "multipart/form-data" },
                        });
                        // Xử lý văn bản trả về từ API
                        console.log("Transcription Text:", response);
                        criteriaList.value[index].value = response?.data?.text;
                    } catch (error) {
                        console.error("Error in Speech-to-Text:", error);
                    } finally {
                        // isTranscribing.value = false;
                    }

                    isRecordingInput.value = false;
                });

                // Bắt đầu ghi âm
                mediaRecorder.onstart = () => {
                    audioChunks.value = []; // Xóa các đoạn âm thanh trước đó
                };

                mediaRecorder.start(); // Bắt đầu ghi
            } catch (error) {
                console.error("Lỗi khi bắt đầu ghi âm:", error);
                isRecordingInput.value = false; // Trở lại trạng thái chưa ghi âm nếu có lỗi
            }
        } else {
            // Nếu đang ghi âm
            isRecordingInput.value = false; // Dừng ghi âm
            mediaRecorder.stop(); // Kết thúc ghi âm
            device.getTracks().forEach((track) => track.stop()); // Dừng tất cả các tracks
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
                    audioBlob.value = new Blob(audioChunks.value, { type: "audio/mp3" });
                    audioUrl.value = URL.createObjectURL(audioBlob.value);

                    // Tạo FormData và gửi yêu cầu API
                    const formData = new FormData();

                    // Sửa lại: Gói Blob thành File object trước khi thêm vào FormData
                    const file = new File([audioBlob.value], "audio.mp3", { type: "audio/mp3" });
                    formData.append("audio", file);

                    // isTranscribing.value = true;
                    try {
                        const response = await axios.post('/admin/speech-to-text', formData, {
                            headers: { "Content-Type": "multipart/form-data" },
                        });
                        // Xử lý văn bản trả về từ API
                        console.log("Transcription Text:", response);
                        content.value = response?.data?.text || 'Vui lòng thử lại';
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
    const buildCriteriaPrompt = () => {
        let prompt = '';

        criteriaList.value.forEach(criteria => {
            console.log("criteria", criteria)
            let name = criteria.name && criteria.name.trim() ? criteria.name.trim() : (criteria.label ? criteria.label.trim() : '');
            // Bỏ qua nếu 'name' bị trống hoặc không có
            if (!name) {
                return;
            }

            if (criteria.type === 'input') {
                const value = criteria.value ? criteria.value.trim() : '';

                // Chỉ thêm vào prompt nếu cả 'name' và 'value' đều có giá trị
                if (value) {
                    prompt += `${name}: ${value}<br>`;
                }
            } else if (criteria.type === 'select') {
                if (criteria.multiple && Array.isArray(criteria.selectedValues) && criteria.selectedValues.length > 0) {
                    // Xử lý trường hợp 'select' có thể chọn nhiều giá trị
                    const selectedTexts = criteria.selectedValues
                        .map(selectedValue => {
                            const option = getParsedOptions(criteria.value).find(opt => opt.value === selectedValue);
                            return option ? `  - ${option.text}` : '';
                        })
                        .filter(text => text)
                        .join('<br>');

                    // Chỉ thêm vào prompt nếu có giá trị đã chọn hợp lệ
                    if (selectedTexts) {
                        prompt += `${name}:<br>${selectedTexts}<br>`;
                    }
                } else if (!criteria.multiple) {
                    // Xử lý trường hợp 'select' chọn đơn
                    const selectedOption = getParsedOptions(criteria.value).find(option => option.value == criteria.selectedValue);
                    // Chỉ thêm vào prompt nếu có giá trị đã chọn
                    if (selectedOption) {
                        prompt += `${name}: ${selectedOption.text}<br>`;
                    }
                }
            }
        });
        return prompt.trim();
    };
    const filteredImages = computed(() =>
        Array.isArray(media_link.value)
            ? media_link.value.filter((item) => item.type === "image")
            : []
    );

    const filteredVideos = computed(() =>
        Array.isArray(media_link.value)
            ? media_link.value.filter((item) => item.type === "video")
            : []
    );

    const toggleTab = (tab) => {
        if (tab === "image") {
            isImageTab.value = true;
        } else {
            isImageTab.value = false;
        }
    };

    const updateMessage = (newInfo) => {
        message.value += (message.value ? "<br>" : "") + newInfo;
    };

    const updateCss = (newInfo) => {
        css.value += (css.value ? "<br>" : "") + newInfo;
    };

    const addToSocial = (url) => {
        message.value +=
            (message.value ? "<br>" : "") + `<img src=${url} alt='image'>`;
        sunEditorInstance.value.setContents(message.value);
        // saveData();
    };
    const addVideo = (url) => {
        message.value +=
            (message.value ? "<br>" : "") +
            `<video alt='video' src=${url} autoplay></video>`;
        sunEditorInstance.value.setContents(message.value);
        // saveData();
    };

    const prepareDeleteMessage = (id) => {
        messageIdToDelete.value = id;
        showConfirmModal.value = true;
    };

    const showConfirmModal = ref(false);


    const editor = ref(null); // Tham chiếu đến DOM của Sun Editor
    let sunEditorInstance = ref(null); // Tham chiếu đến instance của Sun Editor
        const appendContentWithDelay = async (text, delay) => {
        const currentContent = sunEditorInstance.value.getContents();
        // Nếu content hiện tại rỗng thì set mới, không thì append
        if (!currentContent || currentContent === '<p><br></p>') {
            sunEditorInstance.value.setContents(text);
        } else {
            sunEditorInstance.value.setContents(currentContent + text);
        }
        await new Promise(resolve => setTimeout(resolve, delay));
    };

    const isUpdatingFromAPI = ref(false);
    const conversationId = ref(null);
    const askMessage = ref(null);
    const state = reactive({
        stepAis: [],
    });
    const isFirstCall = ref(false);
    const sendData = async () => {
        try {
            if (!content.value) {
                toast.error('Vui lòng nội dung sản phẩm');
                isLoading.value = false;
                return;
            }
            isLoading.value = true;
            // Build và thêm chuỗi tiêu chí vào nội dung
            const criteriaPrompt = buildCriteriaPrompt();
            console.log('criteriaPrompt', criteriaPrompt)
            let baseContent = sunEditorInstance.value.getContents();
            // Tạo nội dung step 0 để mô tả mở bài
            const formDataStart = new FormData();
            if (!conversationId.value) {
                isFirstCall.value = true;
                if (isFirstCall.value) {
                    isLoadingThinking.value = true;
                }
                formDataStart.set('role', 'user');
                console.log("criteriaPrompt", criteriaPrompt)
                formDataStart.set('filter', criteriaPrompt);
                formDataStart.set('content', content.value);
                const response_docs = await axios.post(route("admin.ai-text.docs"), formDataStart);
                conversationId.value = response_docs.data.id;
                aiTextConversations.value = response_docs.data.messages;
            }
            if (selectedModel.value === "Claude 3.5 Sonnet") {
                    try {
                        sunEditorInstance.value.setContents(baseContent + `<h1>${ai_assistant.value.name} </h1> ${criteriaPrompt}`);
                        isLoadingThinking.value = false;
                        isFirstCall.value = false;
                        isLoading.value = true;
                        const formData = new FormData();
                        // formData.append('name', item.name);
                        formData.append('field', ai_assistant.value.name);
                        // formData.append('description', item.description);
                        formData.append('ai_assistant_id', props.ai_assistant.id);
                        formData.append('message', content.value || '');
                        formData.append('model', selectedModel.value);
                        formData.append('language', selectedLanguage.value);
                        formData.append('limit', selectedLimit.value);
                        formData.append('conversationId', conversationId.value || '');
                        formData.append('criteria', JSON.stringify(criteriaList.value));
                        formData.append('isActiveGdthLeft', isActiveGdthLeft.value);
                        formData.append('selectedToneLeft', selectedToneLeft.value);
                        const stepsData = step_ais.value.map(item => ({
                            name: item.name,
                            description: item.description,
                            comments: item.comments || content.value || item.description
                        }));
                        console.log("stepsData", stepsData)
                        formData.append('steps', JSON.stringify(stepsData));

                        // if (file.value) {
                        //     formData.append('file', file.value);
                        // }

                        files.value.forEach((file, index) => {
                            formData.append(`file[${index}]`, file);
                        });
                        // Gọi API multistep với EventSource
                        // Sử dụng fetch để stream response
                        const response = await fetch(route("admin.ai-text.askMultiClaudeStream"), {
                            method: 'POST',
                            body: formData
                        });
                        const reader = response.body.getReader();
                        const decoder = new TextDecoder();
                        let streamResponse = '';
                        baseContent = sunEditorInstance.value.getContents();
                        baseContent = baseContent === '<p><br></p>' ? '' : baseContent;
                        let currentStepIndex = -1;
                        while (true) {
                            const {value, done} = await reader.read();
                            if (done) {
                                // Sau khi stream kết thúc
                                // Goi Api get doc de lay step o client
                                try {
                                    const response_docs = await axios.get(route("admin.ai-text.docs"), {
                                        params: {
                                            id: conversationId.value
                                        }
                                    });
                                    aiTextConversations.value = response_docs.data.messages;
                                } catch (error) {
                                    console.error('Error fetching conversation:', error);
                                }
                                break;
                            }

                            const chunk = decoder.decode(value);
                            const lines = chunk.split('\n');

                            for (const line of lines) {
                                if (line.startsWith('event:')) {
                                    try {
                                        // Tìm data của event này
                                        const eventName = line.slice(7).trim(); // Lấy tên event
                                        const dataLine = lines.find(l => l.startsWith('data:'));
                                        if (eventName === 'stepStart' && dataLine) {
                                            console.log("Raw dataLine:", dataLine); // Log raw data
                                            console.log("Sliced dataLine:", dataLine.slice(6)); // Log after slice

                                            const eventData = JSON.parse(dataLine.slice(6));
                                            // Xử lý step event
                                            if (eventData.hasOwnProperty('stepIndex') && eventData.type === 'new') {
                                                const stepIndex = eventData.stepIndex;
                                                toggleDropdownStep(stepIndex);
                                                currentStepIndex = stepIndex;
                                                streamResponse += `<h2>${eventData.stepName}</h2>`;
                                                sunEditorInstance.value.setContents(baseContent + streamResponse);
                                                message.value = baseContent + streamResponse;
                                            }
                                            // Xử lý text event
                                            else if (eventData.hasOwnProperty('text')) {
                                                // Xử lý text ở đây
                                                streamResponse += eventData.text;
                                                sunEditorInstance.value.setContents(baseContent + streamResponse);
                                                message.value = baseContent + streamResponse;
                                            }
                                        }

                                        if (eventName === 'stepComplete' && dataLine) {
                                            const eventData = JSON.parse(dataLine.slice(6));
                                            const stepIndex = eventData.stepIndex;

                                            // Kiểm tra có tồn tại stepIndex không
                                            if (eventData.hasOwnProperty('stepIndex')) {
                                                const stepIndex = eventData.stepIndex;
                                                step_ais.value[stepIndex].success = true;

                                                // Nếu sau này cần thêm xử lý conversations
                                                // if (eventData.hasOwnProperty('conversations')) {
                                                //     step_ais.value[stepIndex].conversations = eventData.conversations;
                                                // }
                                            }
                                        }
                                    } catch (e) {
                                        console.error("JSON parse error:", error); // Log any parsing errors
                                        console.error("Problem data:", dataLine.slice(6)); // Log problematic data
                                    }
                                } else if (line.startsWith('data: ')) {
                                    const data = line.slice(6);

                                    if (data.trim() === "<END_STREAMING_SSE>") {
                                        return {success: true, text: streamResponse};
                                    }

                                    try {
                                        const jsonResponse = JSON.parse(data);
                                        if (jsonResponse.text) {
                                            streamResponse += jsonResponse.text;
                                            // Cập nhật editor ngay lập tức
                                            sunEditorInstance.value.setContents(baseContent + streamResponse);
                                            message.value = baseContent + streamResponse;
                                        }
                                        saveStepConversations(jsonResponse)
                                    } catch (e) {
                                        console.error('JSON parse error:', e);
                                    }
                                }
                            }
                        }
                    }  catch (error) {
                        toast.error(`${error.message}`);
                    } finally {
                        isLoading.value = false;
                    }

            } else {
                try {
                    sunEditorInstance.value.setContents(baseContent + `<h1>${ai_assistant.value.name} </h1> ${criteriaPrompt}`);
                    // if (isFirstCall.value) {
                    //     await sendMoBai();
                    // }
                    isLoadingThinking.value = false;
                    isFirstCall.value = false;
                    isLoading.value = true;
                    const formData = new FormData();
                    // formData.append('name', item.name);
                    formData.append('field', ai_assistant.value.name);
                    // formData.append('description', item.description);
                    formData.append('ai_assistant_id', props.ai_assistant.id);
                    formData.append('message', content.value || '');
                    formData.append('model', selectedModel.value);
                    formData.append('language', selectedLanguage.value);
                    formData.append('limit', selectedLimit.value);
                    formData.append('conversationId', conversationId.value || '');
                    formData.append('criteria', JSON.stringify(criteriaList.value));
                    formData.append('isActiveGdthLeft', isActiveGdthLeft.value);
                    formData.append('selectedToneLeft', selectedToneLeft.value);
                    const stepsData = step_ais.value.map(item => ({
                        name: item.name,
                        description: item.description,
                        comments: item.comments || content.value || item.description
                    }));
                    console.log("stepsData", stepsData)
                    formData.append('steps', JSON.stringify(stepsData));

                    files.value.forEach((file, index) => {
                        formData.append(`file[${index}]`, file);
                    });

                    // if (file.value) {
                    //     formData.append('file', file.value);
                    // }

                    // Gọi API multistep với EventSource
                    // Sử dụng fetch để stream response
                    const response = await fetch(route("admin.ai-text.askMultiGptStream"), {
                        method: 'POST',
                        body: formData
                    });
                    const reader = response.body.getReader();
                    const decoder = new TextDecoder();
                    let streamResponse = '';
                    baseContent = sunEditorInstance.value.getContents();
                    baseContent = baseContent === '<p><br></p>' ? '' : baseContent;
                    let currentStepIndex = -1;
                    while (true) {
                        const {value, done} = await reader.read();
                        if (done) {
                            // Sau khi stream kết thúc
                            // Goi Api get doc de lay step o client
                            try {
                                const response_docs = await axios.get(route("admin.ai-text.docs"), {
                                    params: {
                                        id: conversationId.value
                                    }
                                });
                                aiTextConversations.value = response_docs.data.messages;
                            } catch (error) {
                                console.error('Error fetching conversation:', error);
                            }
                            break;
                        }

                        const chunk = decoder.decode(value);
                        const lines = chunk.split('\n');

                        for (const line of lines) {
                            if (line.startsWith('event:')) {
                                try {
                                    // Tìm data của event này
                                    const eventName = line.slice(7).trim(); // Lấy tên event
                                    const dataLine = lines.find(l => l.startsWith('data:'));
                                    if (eventName === 'stepStart' && dataLine) {
                                        console.log("Raw dataLine:", dataLine); // Log raw data
                                        console.log("Sliced dataLine:", dataLine.slice(6)); // Log after slice

                                        const eventData = JSON.parse(dataLine.slice(6));
                                        // Xử lý step event
                                        if (eventData.hasOwnProperty('stepIndex') && eventData.type === 'new') {
                                            const stepIndex = eventData.stepIndex;
                                            toggleDropdownStep(stepIndex);
                                            currentStepIndex = stepIndex;
                                            streamResponse += `<h2>${eventData.stepName}</h2>`;
                                            sunEditorInstance.value.setContents(baseContent + streamResponse);
                                            message.value = baseContent + streamResponse;
                                        }
                                        // Xử lý text event
                                        else if (eventData.hasOwnProperty('text')) {
                                            // Xử lý text ở đây
                                            streamResponse += eventData.text;
                                            sunEditorInstance.value.setContents(baseContent + streamResponse);
                                            message.value = baseContent + streamResponse;
                                        }
                                    }

                                    if (eventName === 'stepComplete' && dataLine) {
                                        const eventData = JSON.parse(dataLine.slice(6));
                                        const stepIndex = eventData.stepIndex;

                                        // Kiểm tra có tồn tại stepIndex không
                                        if (eventData.hasOwnProperty('stepIndex')) {
                                            const stepIndex = eventData.stepIndex;
                                            step_ais.value[stepIndex].success = true;

                                            // Nếu sau này cần thêm xử lý conversations
                                            // if (eventData.hasOwnProperty('conversations')) {
                                            //     step_ais.value[stepIndex].conversations = eventData.conversations;
                                            // }
                                        }
                                    }
                                } catch (e) {
                                    console.error("JSON parse error:", error); // Log any parsing errors
                                    console.error("Problem data:", dataLine.slice(6)); // Log problematic data
                                }
                            } else if (line.startsWith('data: ')) {
                                const data = line.slice(6);

                                if (data.trim() === "<END_STREAMING_SSE>") {
                                    return {success: true, text: streamResponse};
                                }

                                try {
                                    const jsonResponse = JSON.parse(data);
                                    if (jsonResponse.text) {
                                        streamResponse += jsonResponse.text;
                                        // Cập nhật editor ngay lập tức
                                        sunEditorInstance.value.setContents(baseContent + streamResponse);
                                        message.value = baseContent + streamResponse;
                                    }
                                    saveStepConversations(jsonResponse)

                                } catch (e) {
                                    console.error('JSON parse error:', e);
                                }
                            }
                        }
                    }
                }  catch (error) {
                    toast.error(`${error.message}`);
                } finally {
                    isLoading.value = false;
                }
            }

            // Lưu dữ liệu sau khi tất cả hoàn thành
            toggleDropdownStep(-1);
            // saveData();
        } catch (error) {
            if (error.response && error.response.status === 403) {
                console.log("error.response ", error.response)
                showCreditModal();
            } else {
                console.error("Đã xảy ra lỗi khi gửi tin nhắn:", error);
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
            }

        } finally {
            // isLoading.value = false;
            // isUpdatingFromAPI.value = false;
        }
    };

    const sendAskMessage = async () => {
        try {
            if (!askMessage.value) {
                toast.error('Vui lòng nội dung chat');
                return;
            }
            isLoading.value = true;
            let streamResponse = '';
            const formData = new FormData();
            if (conversationId.value) {
                formData.set('role', 'user');
                formData.set('id', conversationId.value);
                formData.set('content', askMessage.value);
                formData.append('isActiveGdth', isActiveGdth.value);
                formData.append('selectedTone', selectedTone.value);
                const response_docs = await axios.post(route("admin.ai-text.docs"), formData);
                aiTextConversations.value = response_docs.data.messages;
            } else {
                // trường hợp user chat trước rồi mới gọi steps
                formData.set('role', 'user');
                formData.set('content', askMessage.value);
                formData.set('type', 'old_data');
                formData.append('isActiveGdth', isActiveGdth.value);
                formData.append('selectedTone', selectedTone.value);
                const response_docs = await axios.post(route("admin.ai-text.docs"), formData);
                conversationId.value = response_docs.data.id;
                aiTextConversations.value = response_docs.data.messages;
            }

            const formData2 = new FormData();
            formData2.append('messages', JSON.stringify(aiTextConversations.value));
            formData2.append('model', selectedModel.value);
            if (fileAsk.value) {
                formData2.append('file', fileAsk.value);
            }
            let response = '';
            if (selectedModel.value === "Claude 3.5 Sonnet") {
                 response = await fetch(route("admin.ai-text.askClaudeStream"), {
                    method: 'POST',
                    headers: {
                        'Accept': 'text/event-stream'
                    },
                    body: formData2
                });
            } else {
                 response = await fetch(route("admin.ai-text.askGptStream"), {
                    method: 'POST',
                    headers: {
                        'Accept': 'text/event-stream'
                    },
                    body: formData2
                });
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder();

            // Lấy nội dung hiện tại của editor
            const currentContent = sunEditorInstance.value.getContents();
            let baseContent = currentContent === '<p><br></p>' ? '' : currentContent;

            while (true) {
                const { value, done } = await reader.read();
                if (done) break;

                const chunk = decoder.decode(value);
                const lines = chunk.split('\n');

                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        const jsonStr = line.replace('data: ', '');
                        if (jsonStr.trim() === "<END_STREAMING_SSE>") {
                            break;
                        }
                        try {
                            const jsonObject = JSON.parse(jsonStr);
                            if (jsonObject.text) {
                                streamResponse += jsonObject.text;

                                if (!isUpdatingFromAPI.value) {
                                    isUpdatingFromAPI.value = true;
                                    const updatedContent = baseContent + streamResponse;
                                    sunEditorInstance.value.setContents(updatedContent);
                                    message.value = updatedContent;
                                    isUpdatingFromAPI.value = false;
                                }
                            }
                        } catch (jsonError) {
                            console.error("Lỗi parse JSON:", jsonError);
                        }
                    }
                }
                await new Promise(resolve => setTimeout(resolve, 30));
            }

            // Sau khi hoàn thành stream
            if (conversationId.value) {
                const formData = new FormData();
                formData.set('role', 'assistant');
                formData.set('id', conversationId.value);
                formData.set('content', streamResponse);
                const response_docs = await axios.post(route("admin.ai-text.docs"), formData);
                aiTextConversations.value = response_docs.data.messages;
            }
            askMessage.value = null;
            fileAsk.value = null;
            // await saveData(false);

        } catch (error) {
            console.error("Error:", error);
            toast.error("Có lỗi xảy ra khi gửi yêu cầu");
        } finally {
            isLoading.value = false;
        }
    };

    const handleSelectOptions = (valueSelected, index) => {
        criteriaList.value[index].selectedValues = valueSelected;
    };
    const getParsedOptions = (value) => {
        try {
            // Kiểm tra nếu `value` đã là một mảng, trả về nó trực tiếp
            if (Array.isArray(value)) {
                return value;
            }

            // Kiểm tra nếu `value` là một chuỗi JSON hợp lệ và không bị trống
            if (typeof value === 'string' && value.trim() !== '') {
                const parsed = JSON.parse(value);
                console.log("Array.isArray(parsed)", Array.isArray(parsed), parsed)
                // Đảm bảo rằng kết quả sau khi parse là mảng
                return Array.isArray(parsed) ? parsed : [];
            }

            // Nếu `value` là chuỗi trống, trả về mảng rỗng
            return [];
        } catch (error) {
            console.error("Lỗi khi parse JSON:", error);
            return []; // Trả về mảng rỗng nếu có lỗi
        }
    };
    const handleFileUpload = (event) => {
        files.value = Array.from(event.target.files); // Store all selected files

        // Validate each file
        const validFileTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        files.value.forEach((file, index) => {
            if (!validFileTypes.includes(file.type)) {
                toast.error('File phải có định dạng: doc, docx, pdf.');
                files.value.splice(index, 1); // Remove invalid file
            }

        });
    };

    const removeFile = (index) => {
        files.value.splice(index, 1);

    };
    const removeFileAsk = () => {
        fileAsk.value = null;
    };
    const handleFileUploadAsk = (event) => {
        const selectedFile = event.target.files[0];

        if (!selectedFile) {
            return;
        }

        const validFileTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain' // Thêm định dạng cho file .txt
        ];

        // Kiểm tra cả file type và extension
        const fileName = selectedFile.name.toLowerCase();
        const fileExtension = fileName.split('.').pop();
        const validExtensions = ['pdf', 'doc', 'docx', 'txt'];

        if (!validFileTypes.includes(selectedFile.type) && !validExtensions.includes(fileExtension)) {
            toast.error('File phải có định dạng: doc, docx, pdf, txt.');
            event.target.value = null; // Reset input file
            fileAsk.value = null;
            return;
        }

        fileAsk.value = selectedFile;
    };

    const toggleDropdownStep = (step) => {
        if (dropDownStep.value === step) {
            dropDownStep.value === -1;
        } else {
            dropDownStep.value = step;
        }
    };

    const decreaseLimit = () => {
        const currentIndex = limit.value.indexOf(String(selectedLimit.value));
        if (currentIndex > 0) {
            selectedLimit.value = limit.value[currentIndex - 1];
        }
    };

    const increaseLimit = () => {
        const currentIndex = limit.value.indexOf(String(selectedLimit.value));
        if (currentIndex < limit.value.length - 1) {
            selectedLimit.value = limit.value[currentIndex + 1];
        }
    };

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

        sunEditorInstance.value.onChange = function (contents) {
            // console.log(isUpdatingFromAPI);
            if (!isUpdatingFromAPI.value) { // Chỉ cập nhật khi không phải do API
                message.value = contents;
                // console.log("Nội dung đã thay đổi:", message.value);
            }
        };
    });

    function saveStepConversations(jsonResponse){
        if (jsonResponse.hasOwnProperty('conversations')) {
            step_ais.value[jsonResponse.stepIndex].conversations = jsonResponse.conversations.content;
        }
    }
</script>

<style>
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
</style>
