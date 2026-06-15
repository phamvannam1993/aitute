<template>

    <Head title="Lịch sử tạo video - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="show-box">
            <div class="flex justify-between items-center my-5 flex-col-mobile">
                <h2 class="text-orion-primary font-bold text-2xl">Lịch sử tạo video từ hình ảnh</h2>
                <div class="flex justify-between items-center my-5">
                    <a href="javascript:void(0)"
                        class="bt-yellow px-4 md:px-11 py-2 text-white font-semibold rounded-[15px] flex justify-center items-center gap-1 mx-5"
                        @click="loadDataVideo">
                        Chọn video</a>
                    <a :href="route('ai-video.img2Video')"
                        class="px-4 md:px-11 py-2 bg-[#149CBE] text-white font-semibold rounded-[15px] flex justify-center items-center gap-1"><svg
                            width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1.93691 5.73778H5.70135V1.63111C5.70135 1.25778 5.8258 0.946666 6.07469 0.697776C6.32358 0.448888 6.63469 0.324444 7.00802 0.324444C7.40209 0.324444 7.71321 0.448888 7.94135 0.697776C8.19024 0.946666 8.31469 1.25778 8.31469 1.63111V5.73778H12.048C12.4214 5.73778 12.7221 5.85185 12.9502 6.08C13.1991 6.30815 13.3236 6.60889 13.3236 6.98222C13.3236 7.35555 13.1991 7.6563 12.9502 7.88444C12.7221 8.11259 12.4214 8.22667 12.048 8.22667H8.31469V12.3333C8.31469 12.7067 8.19024 13.0178 7.94135 13.2667C7.71321 13.5156 7.40209 13.64 7.00802 13.64C6.63469 13.64 6.32358 13.5156 6.07469 13.2667C5.8258 13.0178 5.70135 12.7067 5.70135 12.3333V8.22667H1.93691C1.58432 8.22667 1.28358 8.11259 1.03469 7.88444C0.806539 7.6563 0.692465 7.35555 0.692465 6.98222C0.692465 6.60889 0.806539 6.30815 1.03469 6.08C1.28358 5.85185 1.58432 5.73778 1.93691 5.73778Z"
                                fill="white" />
                        </svg>
                        Tạo video mới</a>
                </div>
            </div>
            <div v-if="isLoadVideo" class="flex-col justify-between hidden lg:flex box-video">
                <div v-if="isLoadVideo">
                    <div class="box-left">
                        <p class="text-des">Chọn video để ghép (tối thiểu 2 video)</p>
                    </div>
                    <div class="box-right text-center">
                        <div class="content-left" style="float:left">
                            <a href="javascript:void(0)"
                                class="md:px-8 py-2 bg-orion-sec text-white font-semibold flex justify-center items-center gap-1 mx-5 mb-2"
                                @click="handleUploadImageClick">
                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <rect width="30" height="30" fill="url(#pattern0_1977_2309)" />
                                    <defs>
                                        <pattern id="pattern0_1977_2309" patternContentUnits="objectBoundingBox"
                                            width="1" height="1">
                                            <use xlink:href="#image0_1977_2309" transform="scale(0.0111111)" />
                                        </pattern>
                                        <image id="image0_1977_2309" width="90" height="90"
                                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAABaCAYAAAA4qEECAAAACXBIWXMAAAsTAAALEwEAmpwYAAACu0lEQVR4nO3cTW4TMRjG8XdF2SOBgK64AlcBNuwqVqWFA7BpWAWp5+AWLAkUBC0nYIuADWpXgPgjixFEKE5sz4wdj5+fFKlS6onfJ65n6vkwExERERERERERA3aAR8Br4IIA5rHNbbvaXI0HwKWsXz1wEzgL7OggBZdq+59TV7tlHMnRIfctuFTbFd5nGdnddJHEehRcqq3HQxsbcJLaO+tRcKm2Hq9sbMB5au+sR8Gl2nqc29hii6kdpepV0H8o6IHhMd0PLkRBZ6KgM1HQmSjoTBR0Jq3t/JMBl4HH3dLBxdKyp1uz2UnfsvwF7AIffKOyW4Xc/ddCUkfyupCXw9bITtVNF6EOkz+odcQt546/7DlVxC3njr/sGcvXU6s76G9rtvOrSL0VBX0yxNShoIc9t3mwZjsa0QOdrT9dd1ZbQYf/w3LW5zoNBR03sg/dPNztIN1rEXrlkYLOREFnoqCnHnQq4BrwFHgLfO5e7ueZe690/yYB2Ntwaa97b690P6sGHBFuVrq/1YoIOTps4CrwBHgJfAW+AG+66ei6tYQ0s8A53wXr446XH1grEoMODfse8IMpTke+amJ/P9A8oD93gO8btvOsR711LCrR31FAn+4CP8cY2S0FHRr2nM2iw24t6I3TCHCLgaaj1oNeO7KBKwz4F9J60N6QgPvECQq75aCd4+W1ZOA28Ilx5v6mg3Y+As+BFwHH0slhK+hhzatfJqUSVjsqYbWjElY7KmG1oxJWOyph26aajg6kmuPo2qGg81DQmSjoTBR0Jgo6EwWdiYJuIGg9jg3/7XJDBu2eDJDE/Nvc2rYeCxtbdz9IEutRcKm2HvuW6eYbdzdTNOtRcKm2K7zL9njj7rHG0WFbj4JLtV0R8g3LyX2r3S1ki9AdpPUouFTbpdvl9rM/qFtERERERERExLbTb3j5asunMnuNAAAAAElFTkSuQmCC" />
                                    </defs>
                                </svg>
                                Chọn ảnh
                            </a>
                            <p class="text-name-audio mx-5" v-if="isLoadVideo">{{ imageName }}</p>
                        </div>
                        <div class="content-left" style="float:left">
                            <a href="javascript:void(0)"
                                class="md:px-8 py-2 bg-orion-sec text-white font-semibold flex justify-center items-center gap-1 mx-5 mb-2"
                                @click="handleUploadClick">
                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <rect width="30" height="30" fill="url(#pattern0_1817_1275)" />
                                    <defs>
                                        <pattern id="pattern0_1817_1275" patternContentUnits="objectBoundingBox"
                                            width="1" height="1">
                                            <use xlink:href="#image0_1817_1275" transform="scale(0.0111111)" />
                                        </pattern>
                                        <image id="image0_1817_1275" width="90" height="90"
                                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAABaCAYAAAA4qEECAAAACXBIWXMAAAsTAAALEwEAmpwYAAADaUlEQVR4nO2cTYhOURyH3xnDMEwTZSFDPjcjSpiNlViapFiysFDKsBFZKJlio2ShyMKwIaWsLMiKfJQaymx87RRNimbyNV6PTp0aZt55nXvPuee9772/p2Z7/v/f857O3I9zbqUihBBCCCGEEEIIISrAbOAw8BQYo7iMAU+AQ0B71J8e6AZeUD6em+wxZ3IZJf8tO/uZbZeLstMfQ7RZk8vO4xiiRxudMgeMxhAtAImOhERHQqIjIdGRkOhISPQEP4Bh4BZwGtgL9AJd9hHCHTwoo+gR4AFwGTgC9AGrgbb/5FjiU7SooseBt8A94DywH9gGLGpUlnBGM2jOgc/AM+AmcBLYDWwA5uQtSxb9BGuuDneBlZk3HzBLrpurw9LMGw+cJdfN1aE188YDZ8l1c3WQaImujWZ0AvCgkufm6qClo1lFA63mkhHYDhwFroTMErrfWgFyJRpoA1bYW+9jwCXgYa29JiGzeIt0CNYQ0cDMSUKv2bvIr2nl+DScmeAQzbmKtrfde4AzwG3gFfALTySaKaKrvlIlujYS3aClo5pFES0dSLRm9DRkc6mhpWMKEl0HrdFojS7S0jGuGU0U0R8lmiiir5ZddBVoCSzaPBf5ALwE7gPHgY6yix6pUSOp6G/ABbupZkGMLMHFhmxuGm54ih5OuycED4LIzKo5izkD884cuAEGgE4P0Z/MHrpGZPEWGbi5n8AgsANYbM4oOtZwFX0iYpZ/8Kkburk3wJqUNaqONdZ65DBvbFKTtm6SBl03K6beS4e76A6PGsuLIHrAs0bVsc5cjxr7iiB6fSTRvSnHb7EvdlPjk8+1SRc6PcafnyDvuZQ1DuBJ2nxJmnRhnsf4WxPk/Q6sSzh+nz3f4kXafEkadWGTx/iDCTObW+3NDuN22e0LQZ6jpM2XRIQLZ1OO3Zty/8Zvu//DnLzqARbavzXATuCivbkJRnizU2W4YHYP9SQcdxXwniYhO8MTQlwx0jY6jDfDzkRz7d005Ek0dj28DuwClpkbDHPCyp7x2wKcAl7ThORNdGGpSHQcJDoSEh0JiY6ERBdItD7HBl9iiNYHBonzgUHz1dmyczCG6Hb7IdSyMgTMyly0ld1dUtlD5m1+FMmTZna/3Z9R5H+Qo8Ajs1xEm8lCCCGEEEIIIYQQlXzzB+24jU8R5YSoAAAAAElFTkSuQmCC" />
                                    </defs>
                                </svg>
                                Chọn âm thanh
                            </a>
                            <p class="text-name-audio mx-5" v-if="isLoadVideo">{{ audioName }}</p>
                        </div>
                        <div class="content-right" style="float:right">
                            <a href="javascript:void(0)"
                                class="md:px-8 py-2 bg-orion-sec text-white font-semibold flex justify-center items-center gap-1 mx-5 mb-2"
                                @click="mergeData()">
                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <rect width="30" height="30" fill="url(#pattern0_1640_2935)" />
                                    <defs>
                                        <pattern id="pattern0_1640_2935" patternContentUnits="objectBoundingBox"
                                            width="1" height="1">
                                            <use xlink:href="#image0_1640_2935" transform="scale(0.0111111)" />
                                        </pattern>
                                        <image id="image0_1640_2935" width="90" height="90"
                                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAABaCAYAAAA4qEECAAAACXBIWXMAAAsTAAALEwEAmpwYAAADyElEQVR4nO2cz28OQRyHpwklIXFAojigLhIhcVLEr4SDOElIHMRFOGslDg5KgvoDmuLkIpwc/ai4ECfiIlVN3CihDWnR+hEemXSavGl2Z9+dfd/ZmX2/T/ImPUw/u/u88+7OfGd3lRIEQRAEQRAEQRAEQRAEQRCSANqBq8AHhCxGgT7tLHdvMv8o5KPPRbT05Px8dBEtOCCiPSGiYxOdO6iiIKL9IKI9IaI9IaI9IaKrLlrazyCiS+4o0qMNItqTCBEdqDg5dVRddKuBiPaDiPaEiPaEiPaEiPaEiPaEiPaEiPaEiPaEiPZE8KKBg0C3ipygRQNrgR/AX+CwipjQRV+viZoGtqlICV30uzlxY8B6FSGhi/6XEDkCLFWREbroLymxT4EFKiJCF303LRe4DbSpSAhd9HYz4kjjsoqEoEVrgEvYOaEiIAbRbcAti+jfwH4VOMGL1gALgWcW2ZPAZhUwUYjW6CGdGdql8R5YrQIlGtEaoBP4bJH9ElisAqQ00a4bBnaY6Xga94B5Dvt/tpn7H51oDXAkZdY4yw3lQJLslhatAc5hp0c1QLZqddEaYMAiWk90DqmCsi1tWkr0fOChRfYU0FVEdqP2P2rRGmAJ8Moi+xOwTjkA9EYvupEAqxJq17W8BZb52JdKi9YAW4BvFtlPyiytVka0BjgA/LHIvlNWabVSojXASexcVCVQRdFHMyYzmlMl7Fd1RAO7gJ9ko0ur+yolOifnCxzIBssaYxITwKYC20usjeTFZcNlSl5uhnB5KVRabYRsl40WIXVikAWwCHhuyf6a0dNf6IwC2+8tcuAuG9QvzWuKZGCPrsgBw8B38xkC+oH7luxfwF5gt/k7jQcma6gmf9jcKbW7ibJHfb1gsLeOQv9jx4PQI4/jNVnH6hiNpPFI3wfYBNlXXF+Z2ZejZ2dJ3gaM486FhMzzBfLGsgpUOWS7vzKzkQBrMpasski8ucasqN8skKu/+E5VFXA/XcyOJNozfnm2AlQWg6oKmItXEfR5eKMlf2OBc/UsmRfI4DGjiyT0guxpYAXQoZ8CsCzS9jusyuTJv6ZiB3iTcnCnE9r2pLQdtuSPNCD/tYodS015RUJb3fuSmLLkTzcgf1LFjrm9q14RKxNbwkRZ+VU4dXQntD2T96fd7PxomPOAUC3T5gLVYT49lpLoQFn50WDqGkXZWVZ+VACDBSQ8KDs/GnCfgo/Xcw9Hs/OjAugyhZx60eK2hpIfFcw8olzPz1zfFrYmtPzoYGbR9ZoeVpkJjf7oIr2eThe+MDU734X/S5Suj2PIBnEAAAAASUVORK5CYII=" />
                                    </defs>
                                </svg>
                                Ghép video</a>
                            <p style="color:#000000" v-if="isLoadVideo">Đã chọn {{ videoIds.length }}
                                video
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col lg:flex-row justify-between gap-4">
                    <div v-if="isCropping"
                        class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                        <div class="bg-white p-4 rounded-md w-full md:w-[80%] lg:w-[40%]">
                            <VueCropper ref="cropper" :src="imageUrl" :aspect-ratio="aspectRatio" :zoomable="true"
                                class="max-w-full max-h-[60vh] mx-auto overflow-hidden" />
                            <div class="flex justify-between mt-4">
                                <button @click="cancelCropping"
                                    class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">Hủy</button>
                                <button @click="cropImage"
                                    class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Cắt
                                    ảnh</button>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="file" ref="fileInput" accept="audio/*" @change="handleAudioFileChange" class="hidden" />
                <input type="file" ref="fileInputImage" accept="image/*" @change="handleImageFileChange"
                    class="hidden" />
                <div class=" all-video clear">
                    <p v-if="histories.length == 0" class="no-data">Không có video nào</p>
                    <div class="mb-0.5 relative h-240 ml-1 group justify-center flex left w-380"
                        v-for="(item, index) in histories" :key="index">
                        <img v-if="item.thumbnail && item.thumbnail !== ''" :src="item.thumbnail" alt="img"
                            class="h-240 h-auto object-contain" />
                        <div v-else class="h-[167px] flex items-center justify-center bg-gray-200">
                            <p class="text-gray-600">Film đang được tạo</p>
                        </div>
                        <div data-v-623ab925="" class="text-white absolute w-full icon-tick" v-if="isLoadVideo">
                            <svg v-if="!item.is_tick" width="34" height="34" viewBox="0 0 34 34" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g filter="url(#filter0_d_1967_1791)">
                                    <circle cx="15" cy="15" r="14" stroke="white" stroke-width="2"
                                        shape-rendering="crispEdges" />
                                </g>
                                <defs>
                                    <filter id="filter0_d_1967_1791" x="0" y="0" width="34" height="34"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dx="2" dy="2" />
                                        <feGaussianBlur stdDeviation="1" />
                                        <feComposite in2="hardAlpha" operator="out" />
                                        <feColorMatrix type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
                                        <feBlend mode="normal" in2="BackgroundImageFix"
                                            result="effect1_dropShadow_1967_1791" />
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1967_1791"
                                            result="shape" />
                                    </filter>
                                </defs>
                            </svg>

                            <svg v-if="item.is_tick" width="34" height="34" viewBox="0 0 34 34" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g filter="url(#filter0_d_1967_1766)">
                                    <circle cx="15" cy="15" r="15" fill="#00AFFF" />
                                    <circle cx="15" cy="15" r="14" stroke="white" stroke-width="2" />
                                </g>
                                <defs>
                                    <filter id="filter0_d_1967_1766" x="0" y="0" width="34" height="34"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dx="2" dy="2" />
                                        <feGaussianBlur stdDeviation="1" />
                                        <feComposite in2="hardAlpha" operator="out" />
                                        <feColorMatrix type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
                                        <feBlend mode="normal" in2="BackgroundImageFix"
                                            result="effect1_dropShadow_1967_1766" />
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1967_1766"
                                            result="shape" />
                                    </filter>
                                </defs>
                            </svg>
                            <span v-if="item.is_tick" class="text-number">{{ item.text_number
                                ? item.text_number : "" }}</span>
                        </div>
                        <div @click="!isLoadVideo ? fetchData(route('ai-video.historyDetail', { id: item.id, })) : tickVideo(item.id, item.type)"
                            class="absolute cursor-pointer inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-0">
                        </div>
                        <div v-if="!isLoadVideo"
                            class="text-white absolute bottom-2 px-2 hidden group-hover:flex w-full text-left items-center justify-end">
                            <button class="p-2 rounded-full cursor-pointer hover:bg-white/30"
                                @click.stop="createPostYoutube(item.id)">
                                <img src="/assets/svgs/icon-youtube.svg" class="w-5 h-5" />
                            </button>
                            <button class="p-2 rounded-full cursor-pointer hover:bg-white/30"
                                @click.stop="createPost(item.s3_url)">
                                <img src="/assets/svgs/edit-icon-white.svg" class="w-5 h-5" />
                            </button>
                            <button @click.stop="prepareDeleteFile(item.id)"
                                class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                                <img src="/assets/svgs/trash-icon-white.svg" class="w-5 h-5" />
                            </button>
                            <button class="p-2 rounded-full cursor-pointer hover:bg-white/30"
                                @click.stop="shareAIGeneratedMedia(item)">
                                <img src="/assets/svgs/share-icon-white.svg" class="w-5 h-5" />
                            </button>
                            <button @click.stop="downloadVideo(item.s3_url)"
                                class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                                <img src="/assets/svgs/download-icon-white.svg" class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="!isLoadVideo" class="desktop">
                <p v-if="histories.length == 0" class="no-data">Không có video nào</p>
                <div class="mb-0.5 relative h-240 ml-1 group justify-center flex left w-380"
                    v-for="(item, index) in histories" :key="index">
                    <img v-if="item.thumbnail && item.thumbnail !== ''" :src="item.thumbnail" alt="img"
                        class="h-240 h-auto object-contain" />
                    <div v-else class="h-[167px] flex items-center justify-center bg-gray-200">
                        <p class="text-gray-600">Film đang được tạo</p>
                    </div>
                    <div data-v-623ab925="" class="text-white absolute w-full icon-tick" v-if="isLoadVideo">
                        <svg v-if="!item.is_tick" width="34" height="34" viewBox="0 0 34 34" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g filter="url(#filter0_d_1967_1791)">
                                <circle cx="15" cy="15" r="14" stroke="white" stroke-width="2"
                                    shape-rendering="crispEdges" />
                            </g>
                            <defs>
                                <filter id="filter0_d_1967_1791" x="0" y="0" width="34" height="34"
                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dx="2" dy="2" />
                                    <feGaussianBlur stdDeviation="1" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                        result="effect1_dropShadow_1967_1791" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1967_1791"
                                        result="shape" />
                                </filter>
                            </defs>
                        </svg>

                        <svg v-if="item.is_tick" width="34" height="34" viewBox="0 0 34 34" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g filter="url(#filter0_d_1967_1766)">
                                <circle cx="15" cy="15" r="15" fill="#00AFFF" />
                                <circle cx="15" cy="15" r="14" stroke="white" stroke-width="2" />
                            </g>
                            <defs>
                                <filter id="filter0_d_1967_1766" x="0" y="0" width="34" height="34"
                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dx="2" dy="2" />
                                    <feGaussianBlur stdDeviation="1" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                        result="effect1_dropShadow_1967_1766" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1967_1766"
                                        result="shape" />
                                </filter>
                            </defs>
                        </svg>
                        <span v-if="item.is_tick" class="text-number">{{ item.text_number ?
                            item.text_number :
                            "" }}</span>
                    </div>
                    <div @click="!isLoadVideo ? fetchData(route('ai-video.historyDetail', { id: item.id, })) : tickVideo(item.id, item.type)"
                        class="absolute cursor-pointer inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-0">
                    </div>
                    <div v-if="!isLoadVideo"
                        class="text-white absolute bottom-2 px-2 hidden group-hover:flex w-full text-left items-center justify-end">
                        <button class="p-2 rounded-full cursor-pointer hover:bg-white/30"
                            @click.stop="createPostYoutube(item.id)">
                            <img src="/assets/svgs/icon-youtube.svg" class="w-5 h-5" />
                        </button>
                        <button class="p-2 rounded-full cursor-pointer hover:bg-white/30"
                            @click.stop="createPost(item.s3_url)">
                            <img src="/assets/svgs/edit-icon-white.svg" class="w-5 h-5" />
                        </button>
                        <button @click.stop="prepareDeleteFile(item.id)"
                            class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                            <img src="/assets/svgs/trash-icon-white.svg" class="w-5 h-5" />
                        </button>
                        <button class="p-2 rounded-full cursor-pointer hover:bg-white/30"
                            @click.stop="shareAIGeneratedMedia(item)">
                            <img src="/assets/svgs/share-icon-white.svg" class="w-5 h-5" />
                        </button>
                        <button @click.stop="downloadVideo(item.s3_url)"
                            class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                            <img src="/assets/svgs/download-icon-white.svg" class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>
            <div v-if="isLoadingMerge"
                class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
                <div class="show-load">
                    <p>Đang ghép video ...</p>
                    <div class="loader"></div>
                </div>
            </div>
            <div v-if="isSuccess" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="relative bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[480px] xl:w-[400px]"
                    style="background-image: url('/assets/svgs/bg-home.svg');">
                    <button class="absolute top-4 right-4 text-black font-bold text-xl" @click="closePopup">x</button>
                    <div class="text-black text-center">
                        <p class="text-[12px] md:text-[16px] font-medium mb-2">
                            Hoàn thành
                        </p>
                        <svg class="icon-success" width="26" height="25" viewBox="0 0 26 25" fill="none"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <rect x="0.626953" y="0.302246" width="24.5333" height="24.5333"
                                fill="url(#pattern0_1737_6464)" />
                            <defs>
                                <pattern id="pattern0_1737_6464" patternContentUnits="objectBoundingBox" width="1"
                                    height="1">
                                    <use xlink:href="#image0_1737_6464" transform="scale(0.00390625)" />
                                </pattern>
                                <image id="image0_1737_6464" width="256" height="256"
                                    xlink:href="data:image/png;base64,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" />
                            </defs>
                        </svg>
                        <a href="javascript:void(0)" @click="showVideo()"
                            class="px-4 py-2 bg-orion-sec text-white rounded-[10px] min-w-36 md:w-[180px] text-[15px] font-bold disabled:opacity-50 disabled:pointer-events-none">
                            Xem video
                        </a>
                    </div>
                </div>
            </div>
            <div class="lg:hidden flex-col justify-between flex">
                <div v-if="isLoadVideo">
                    <div class="text-center mb-2">
                        <p class="text-des">Chọn video để ghép</p>
                        <p class="text-des">(tối thiểu 2 video)</p>
                    </div>
                    <div class="text-center">
                        <div class="flex-col" style="display: flex;justify-content: center;">
                            <div class="content-left">
                                <a href="javascript:void(0)"
                                    class="md:px-8 py-2 bg-orion-sec text-white font-semibold flex justify-center items-center gap-1 mx-5 mb-2  pd-10"
                                    @click="handleUploadImageClick">
                                    Chọn ảnh
                                </a>
                                <p class="text-name-audio" v-if="isLoadVideo">{{ imageName }}</p>
                            </div>
                            <a href="javascript:void(0)"
                                class="md:px-8 py-2 bg-orion-sec text-white font-semibold flex justify-center items-center gap-1 mx-5 mb-2 pd-10"
                                @click="handleUploadClick">
                                Chọn âm thanh
                            </a>
                            <p class="text-name-audio" v-if="isLoadVideo">{{ audioName }}</p>
                        </div>
                        <div>
                            <a href="javascript:void(0)"
                                class="md:px-8 py-2 bg-orion-sec text-white font-semibold flex justify-center items-center gap-1 mx-5 mb-2 pd-10"
                                @click="mergeData()">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <rect width="16.3761" height="16.3761" fill="url(#pattern0_1736_6108)" />
                                    <defs>
                                        <pattern id="pattern0_1736_6108" patternContentUnits="objectBoundingBox"
                                            width="1" height="1">
                                            <use xlink:href="#image0_1736_6108" transform="scale(0.0111111)" />
                                        </pattern>
                                        <image id="image0_1736_6108" width="90" height="90"
                                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAABaCAYAAAA4qEECAAAACXBIWXMAAAsTAAALEwEAmpwYAAADyElEQVR4nO2cz28OQRyHpwklIXFAojigLhIhcVLEr4SDOElIHMRFOGslDg5KgvoDmuLkIpwc/ai4ECfiIlVN3CihDWnR+hEemXSavGl2Z9+dfd/ZmX2/T/ImPUw/u/u88+7OfGd3lRIEQRAEQRAEQRAEQRAEQRCSANqBq8AHhCxGgT7tLHdvMv8o5KPPRbT05Px8dBEtOCCiPSGiYxOdO6iiIKL9IKI9IaI9IaI9IaKrLlrazyCiS+4o0qMNItqTCBEdqDg5dVRddKuBiPaDiPaEiPaEiPaEiPaEiPaEiPaEiPaEiPaEiPZE8KKBg0C3ipygRQNrgR/AX+CwipjQRV+viZoGtqlICV30uzlxY8B6FSGhi/6XEDkCLFWREbroLymxT4EFKiJCF303LRe4DbSpSAhd9HYz4kjjsoqEoEVrgEvYOaEiIAbRbcAti+jfwH4VOMGL1gALgWcW2ZPAZhUwUYjW6CGdGdql8R5YrQIlGtEaoBP4bJH9ElisAqQ00a4bBnaY6Xga94B5Dvt/tpn7H51oDXAkZdY4yw3lQJLslhatAc5hp0c1QLZqddEaYMAiWk90DqmCsi1tWkr0fOChRfYU0FVEdqP2P2rRGmAJ8Moi+xOwTjkA9EYvupEAqxJq17W8BZb52JdKi9YAW4BvFtlPyiytVka0BjgA/LHIvlNWabVSojXASexcVCVQRdFHMyYzmlMl7Fd1RAO7gJ9ko0ur+yolOifnCxzIBssaYxITwKYC20usjeTFZcNlSl5uhnB5KVRabYRsl40WIXVikAWwCHhuyf6a0dNf6IwC2+8tcuAuG9QvzWuKZGCPrsgBw8B38xkC+oH7luxfwF5gt/k7jQcma6gmf9jcKbW7ibJHfb1gsLeOQv9jx4PQI4/jNVnH6hiNpPFI3wfYBNlXXF+Z2ZejZ2dJ3gaM486FhMzzBfLGsgpUOWS7vzKzkQBrMpasski8ucasqN8skKu/+E5VFXA/XcyOJNozfnm2AlQWg6oKmItXEfR5eKMlf2OBc/UsmRfI4DGjiyT0guxpYAXQoZ8CsCzS9jusyuTJv6ZiB3iTcnCnE9r2pLQdtuSPNCD/tYodS015RUJb3fuSmLLkTzcgf1LFjrm9q14RKxNbwkRZ+VU4dXQntD2T96fd7PxomPOAUC3T5gLVYT49lpLoQFn50WDqGkXZWVZ+VACDBSQ8KDs/GnCfgo/Xcw9Hs/OjAugyhZx60eK2hpIfFcw8olzPz1zfFrYmtPzoYGbR9ZoeVpkJjf7oIr2eThe+MDU734X/S5Suj2PIBnEAAAAASUVORK5CYII=" />
                                    </defs>
                                </svg>
                                Ghép video</a>
                            <p style="color:#000000" v-if="isLoadVideo">Đã chọn {{ videoIds.length }}
                                video
                            </p>
                        </div>
                    </div>
                </div>
                <div
                    class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4 w-full h-[70vh] overflow-y-auto my-3 lg:my-0">
                    <div class="" v-for="(item, index) in histories" :key="index">
                        <div class="w-full relative h-[252px] pb-2 aspect-video bg-slate-400 overflow-hidden rounded-xl"
                            @click="!isLoadVideo ? fetchData(route('ai-video.historyDetail', { id: item.id, })) : tickVideo(item.id, item.type)">
                            <div class="rounded-xl h-[200px] overflow-hidden">
                                <template v-if="item.thumbnail && item.thumbnail !== ''">
                                    <img :src="item.thumbnail" class="cursor-pointer" />
                                    <img src="/assets/img/icon_play.png"
                                        class="w-20 cursor-pointer absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-50"
                                        alt="like" />
                                </template>
                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                                    <p class="text-gray-600">Film đang được tạo</p>
                                </div>
                                <div data-v-623ab925="" class="text-white absolute w-full icon-tick" v-if="isLoadVideo">
                                    <svg v-if="!item.is_tick" width="34" height="34" viewBox="0 0 34 34" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g filter="url(#filter0_d_1967_1791)">
                                            <circle cx="15" cy="15" r="14" stroke="white" stroke-width="2"
                                                shape-rendering="crispEdges" />
                                        </g>
                                        <defs>
                                            <filter id="filter0_d_1967_1791" x="0" y="0" width="34" height="34"
                                                filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix"
                                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                    result="hardAlpha" />
                                                <feOffset dx="2" dy="2" />
                                                <feGaussianBlur stdDeviation="1" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix"
                                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix"
                                                    result="effect1_dropShadow_1967_1791" />
                                                <feBlend mode="normal" in="SourceGraphic"
                                                    in2="effect1_dropShadow_1967_1791" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>

                                    <svg v-if="item.is_tick" width="34" height="34" viewBox="0 0 34 34" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g filter="url(#filter0_d_1967_1766)">
                                            <circle cx="15" cy="15" r="15" fill="#00AFFF" />
                                            <circle cx="15" cy="15" r="14" stroke="white" stroke-width="2" />
                                        </g>
                                        <defs>
                                            <filter id="filter0_d_1967_1766" x="0" y="0" width="34" height="34"
                                                filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix"
                                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                    result="hardAlpha" />
                                                <feOffset dx="2" dy="2" />
                                                <feGaussianBlur stdDeviation="1" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix"
                                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix"
                                                    result="effect1_dropShadow_1967_1766" />
                                                <feBlend mode="normal" in="SourceGraphic"
                                                    in2="effect1_dropShadow_1967_1766" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                    <span v-if="item.is_tick" class="text-number">{{ item.text_number
                                        ? item.text_number : "" }}</span>
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 mt-2 mr-2">
                                <button class="p-2 bg-black bg-opacity-50 rounded-full"
                                    @click.stop="createPostYoutube(item.id)">
                                    <img src="/assets/svgs/icon-youtube.svg" class="w-5 h-5" />
                                </button>
                                <button class="p-2 bg-black bg-opacity-50 rounded-full"
                                    @click.stop="createPost(item.s3_url)">
                                    <img src="/assets/svgs/edit-icon-white.svg" class="w-5 h-5" />
                                </button>
                                <button @click.stop="prepareDeleteFile(item.id)"
                                    class="p-2 bg-black bg-opacity-50 rounded-full">
                                    <img src="/assets/svgs/trash-icon-white.svg" class="w-5 h-5" />
                                </button>
                                <button class="p-2 bg-black bg-opacity-50 rounded-full"
                                    @click.stop="shareAIGeneratedMedia(item)">
                                    <img src="/assets/svgs/share-icon-white.svg" class="w-5 h-5" />
                                </button>
                                <button @click.stop="downloadVideo(item.s3_url)"
                                    class="p-2 bg-black bg-opacity-50 rounded-full">
                                    <img src="/assets/svgs/download-icon-white.svg" class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="!isLoadVideo" class="show-box clear">
            <div class="flex justify-between justify-center-mobile items-center my-5">
                <h2 class="text-orion-primary font-bold text-2xl">Lịch sử ghép video</h2>
            </div>
            <div class="desktop">
                <p v-if="mergeHistories.length == 0" class="no-data">Không có video nào</p>
                <div class="mb-0.5 relative h-240 ml-1 group justify-center left w-380 flex"
                    v-for="(item, index) in mergeHistories" :key="index">
                    <img v-if="item.thumbnail && item.thumbnail !== ''" :src="item.thumbnail" alt="img"
                        class="h-240 h-auto object-contain" />
                    <div v-else class="h-[167px] flex items-center justify-center bg-gray-200">
                        <p class="text-gray-600">Film đang được tạo</p>
                    </div>
                    <div data-v-623ab925="" class="text-white absolute w-full icon-tick" v-if="isLoadVideo">
                        <svg v-if="!item.is_tick" width="34" height="34" viewBox="0 0 34 34" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g filter="url(#filter0_d_1967_1791)">
                                <circle cx="15" cy="15" r="14" stroke="white" stroke-width="2"
                                    shape-rendering="crispEdges" />
                            </g>
                            <defs>
                                <filter id="filter0_d_1967_1791" x="0" y="0" width="34" height="34"
                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dx="2" dy="2" />
                                    <feGaussianBlur stdDeviation="1" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                        result="effect1_dropShadow_1967_1791" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1967_1791"
                                        result="shape" />
                                </filter>
                            </defs>
                        </svg>

                        <svg v-if="item.is_tick" width="34" height="34" viewBox="0 0 34 34" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g filter="url(#filter0_d_1967_1766)">
                                <circle cx="15" cy="15" r="15" fill="#00AFFF" />
                                <circle cx="15" cy="15" r="14" stroke="white" stroke-width="2" />
                            </g>
                            <defs>
                                <filter id="filter0_d_1967_1766" x="0" y="0" width="34" height="34"
                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dx="2" dy="2" />
                                    <feGaussianBlur stdDeviation="1" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                        result="effect1_dropShadow_1967_1766" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1967_1766"
                                        result="shape" />
                                </filter>
                            </defs>
                        </svg>
                        <span v-if="item.is_tick" class="text-number">{{ item.text_number ?
                            item.text_number :
                            "" }}</span>
                    </div>
                    <div @click="!isLoadVideo ? fetchData(route('ai-video.historyDetail', { id: item.id, })) : tickVideo(item.id, item.type)"
                        class="absolute cursor-pointer inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-0">
                    </div>
                    <div v-if="!isLoadVideo"
                        class="text-white absolute bottom-2 px-2 hidden group-hover:flex w-full text-left items-center justify-end">
                        <button class="p-2 rounded-full cursor-pointer hover:bg-white/30"
                            @click.stop="createPostYoutube(item.id)">
                            <img src="/assets/svgs/icon-youtube.svg" class="w-5 h-5" />
                        </button>
                        <button class="p-2 rounded-full cursor-pointer hover:bg-white/30"
                            @click.stop="createPost(item.s3_url)">
                            <img src="/assets/svgs/edit-icon-white.svg" class="w-5 h-5" />
                        </button>
                        <button @click.stop="prepareDeleteFile(item.id)"
                            class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                            <img src="/assets/svgs/trash-icon-white.svg" class="w-5 h-5" />
                        </button>
                        <button class="p-2 rounded-full cursor-pointer hover:bg-white/30"
                            @click.stop="shareAIGeneratedMedia(item)">
                            <img src="/assets/svgs/share-icon-white.svg" class="w-5 h-5" />
                        </button>
                        <button @click.stop="downloadVideo(item.s3_url)"
                            class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                            <img src="/assets/svgs/download-icon-white.svg" class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>

            <div class="lg:hidden flex-col justify-between flex">
                <div
                    class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4 w-full h-[70vh] overflow-y-auto my-3 lg:my-0">
                    <p v-if="mergeHistories.length == 0" class="no-data">Không có video nào</p>
                    <div class="" v-for="(item, index) in mergeHistories" :key="index">
                        <div class="w-full relative h-[252px] pb-2 aspect-video bg-slate-400 overflow-hidden rounded-xl"
                            @click="
                                fetchData(
                                    route('ai-video.historyDetail', {
                                        id: item.id,
                                    })
                                )
                                ">
                            <div class="rounded-xl h-[200px] overflow-hidden">
                                <template v-if="item.thumbnail && item.thumbnail !== ''">
                                    <img :src="item.thumbnail" class="cursor-pointer" />
                                    <img src="/assets/img/icon_play.png"
                                        class="w-20 cursor-pointer absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-50"
                                        alt="like" />
                                </template>
                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                                    <p class="text-gray-600">Film đang được tạo</p>
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 mt-2 mr-2">
                                <button class="p-2 bg-black bg-opacity-50 rounded-full"
                                    @click.stop="createPostYoutube(item.id)">
                                    <img src="/assets/svgs/icon-youtube.svg" class="w-5 h-5" />
                                </button>
                                <button class="p-2 bg-black bg-opacity-50 rounded-full"
                                    @click.stop="createPost(item.s3_url)">
                                    <img src="/assets/svgs/edit-icon-white.svg" class="w-5 h-5" />
                                </button>
                                <button @click.stop="prepareDeleteFile(item.id)"
                                    class="p-2 bg-black bg-opacity-50 rounded-full">
                                    <img src="/assets/svgs/trash-icon-white.svg" class="w-5 h-5" />
                                </button>
                                <button class="p-2 bg-black bg-opacity-50 rounded-full"
                                    @click.stop="shareAIGeneratedMedia(item)">
                                    <img src="/assets/svgs/share-icon-white.svg" class="w-5 h-5" />
                                </button>
                                <button @click.stop="downloadVideo(item.s3_url)"
                                    class="p-2 bg-black bg-opacity-50 rounded-full">
                                    <img src="/assets/svgs/download-icon-white.svg" class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-center my-4 clear">

                <Pagination v-if="mergeHistories.length > 0" :totalPage="totalPageMerge" :initialPage="currentPageMerge"
                    @updatePage="fetchPageMerge" />
                <!-- <button
                                <PaginationCustom :links="paginationLinks" @paginate="fetchPageMerge" />
                                v-for="link in paginationLinks"
                                :key="link.label"
                                :disabled="!link.url || link.active"
                                @click="fetchData(link.url)"
                                :class="[
                                    'px-3 py-1 border',
                                    link.active
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-white text-blue-500',
                                ]"
                            >
                                <span v-html="link.label"></span>
                            </button> -->
            </div>
        </div>
    </Layout>
    <!-- Details Modal & Other Parts Same as Before -->
    <!-- Modal Xác Nhận Xóa -->
    <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-bold text-[#092A99]">Xác nhận xóa video</h3>
            <p class="mt-4 text-gray-600">Bạn có chắc chắn muốn xóa video này không?</p>
            <div class="mt-6 flex justify-end space-x-4">
                <button @click="cancelDelete" class="px-4 py-2 bg-gray-300 text-black rounded">Hủy</button>
                <button @click="confirmDelete" class="px-4 py-2 bg-red-500 text-white rounded">Xóa</button>
            </div>
        </div>
    </div>

    <div v-if="showConfirmModalVideo"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg" style="width: 600px">
            <h3 class="text-xl font-bold text-[#092A99]">Nhập thông tin Video</h3>
            <p class="mt-4 text-gray-600">Vui lòng nhập thông tin đăng bài</p>
            <div class="mb-4 px-2 w-full">
                <label class="block mb-1 text-sm" for="input1">Tiêu đề:</label>
                <input id="input1" style="color: #6b7280" v-on:keyup="handleInputTitle($event.target.value)"
                    class="w-full border px-4 py-2 rounded focus:border-blue-500 focus:shadow-outline outline-none"
                    type="text" autofocus placeholder="Tiêu đề..." />
            </div>
            <div class="mb-4 px-2 w-full">
                <input style="color: #6b7280" id="input1" v-on:keyup="handleInputDes($event.target.value)"
                    class="w-full border px-4 py-2 rounded focus:border-blue-500 focus:shadow-outline outline-none"
                    type="text" autofocus placeholder="Mô tả..." />
            </div>
            <div class="mt-6 flex justify-end space-x-4">
                <button @click="cancelPostVideo" class="px-4 py-2 bg-gray-300 text-black rounded">Hủy</button>
                <button v-if="!isDisableButton" @click="confirmPostVideo"
                    class="px-4 py-2 bg-blue-500 text-white rounded">Xác nhận</button>
                <button v-else disabled class="px-4 py-2 bg-blue-500 text-white rounded">Xác nhận</button>
            </div>
        </div>
    </div>

    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import { Head } from "@inertiajs/vue3";
import FormShareLink from "@/Components/FormShareLink.vue";
import Footer from "../Home/Components/Footer.vue";
import Modal from "@/Components/Modal.vue";
import Credit from "@/Components/Credit.vue";
import { toast } from "vue3-toastify";
import PaginationCustom from "@/Components/PaginationCustom.vue";
import MainMenu from "@/Components/MainMenu.vue";
import Pagination from '@/Components/Pagination.vue';
import VueCropper from "vue-cropperjs";
import "cropperjs/dist/cropper.css";
import Layout from "@/Layouts/Client/Layout.vue";

const props = defineProps({
    list: Array,
});

const breadcrumbsData = [
    { text: "Lịch sử tạo video từ hình ảnh", link: "ai-video.ImgToVideoHistory" },
];

const goBack = () => {
    window.history.back(); // Điều hướng về trang trước đó
};

// Thêm thuộc tính `isPlaying` vào từng video để quản lý việc phát video
const histories = ref(
    props.list?.data.map((item) => ({
        ...item,
        isPlaying: false, // Ban đầu, tất cả video đều không được phát
    })) || []
);

const mergeHistories = ref(
    props.list?.data.map((item) => ({
        ...item,
        isPlaying: false, // Ban đầu, tất cả video đều không được phát
    })) || []
);

const allVideo = ref(null)

const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const showConfirmModalVideo = ref(false);
const isDisableButton = ref(false);
const paginationLinks = ref(props.list?.links || []); // Pagination links
const isLoading = ref(false);
const isLoadingMerge = ref(false);
const isSuccess = ref(false);
const aiVideoId = ref(0);
const selectedVideo = ref(null);
const videoDeleteId = ref(null);
const videoPostId = ref(null);
const title = ref(null);
const fileInput = ref(null);
const description = ref(null);
const isLoadVideo = ref(false)
// Lazy load for videos
const openDetail = (item) => {
    selectedVideo.value = item;
};
const fileInputImage = ref(null)
const closeDetail = () => {
    selectedVideo.value = null;
};
const handleUploadImageClick = () => {
    fileInputImage.value?.click();
};
const audioFile = ref(null);
const handleUploadClick = () => {
    fileInput.value?.click();
};
// Handle video deletion without page reload
const prepareDeleteFile = (id) => {
    videoDeleteId.value = id;
    showConfirmModal.value = true;
};

const createPostYoutube = (id) => {
    videoPostId.value = id;
    showConfirmModalVideo.value = true;
};

const cancelPostVideo = () => {
    videoPostId.value = null;
    showConfirmModalVideo.value = false;
};

const handleInputTitle = (value) => {
    title.value = value;
};

const handleInputDes = (value) => {
    description.value = value;
};
const videoIds = ref([])
const loadDataVideo = async () => {
    if (!isLoadVideo.value) {
        isLoadVideo.value = true
        var url = route("ai-video.getAllVideo", { is_image: true })
        const response = await axios.get(url)
        if (response.data) {
            histories.value = response.data.data
        }
    } else {
        isLoadVideo.value = false
        fetchPage(1)
    }
}

const mergeData = async () => {
    if (videoIds.value.length < 2) {
        return false;
    }
    const formData = new FormData();
    if (audioFile.value) {
        formData.append('audio_file', audioFile.value);
    }
    if (imageInput.value) {
        formData.append('image_file', imageInput.value);
    }
    var url = route("ai-video.mergeVideo", { ids: videoIds.value, type_query: typeQuery.value })
    const response = await axios.post(url, formData,
        {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        }
    );
    if (response.data.success) {
        isLoadingMerge.value = true;
        aiVideoId.value = response.data.id
    } else {
        alert(response.data.message)
    }
}

function checkImageTransparency(imageUrl, callback) {
    const img = new Image();
    img.src = imageUrl;

    img.onload = function () {
        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");

        canvas.width = img.width;
        canvas.height = img.height;
        ctx.drawImage(img, 0, 0);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imageData.data;

        let hasTransparentPixel = false;
        for (let i = 0; i < data.length; i += 4) {
            if (data[i + 3] < 255) {
                // Kiểm tra alpha channel (pixel trong suốt)
                hasTransparentPixel = true;
                break;
            }
        }

        callback(hasTransparentPixel);
    };

    img.onerror = function () {
        callback(false);
    };
}

const closePopup = () => {
    isSuccess.value = false
}

const showVideo = () => {
    var url = route("ai-video.historyDetail", { id: aiVideoId.value })
    location.href = url
}


const tickVideo = (id) => {
    const ids = [];
    var isCheck = false
    for (var i = 0; i < videoIds.value.length; i++) {
        if (id != videoIds.value[i]) {
            ids.push(videoIds.value[i])
        } else {
            isCheck = true
        }
    }
    if (!isCheck) {
        ids.push(id)
    }
    var lists = []
    for (var j = 0; j < histories.value.length; j++) {
        var list = histories.value[j]
        list["is_tick"] = false
        if (checkExist(histories.value[j].id, ids)) {
            list["is_tick"] = true
            list["text_number"] = getNumber(histories.value[j].id, ids)
        }
        lists.push(list)
    }
    videoIds.value = ids
}

function getNumber(id, ids) {
    for (var i = 0; i < ids.length; i++) {
        if (ids[i] == id) {
            return i + 1;
        }
    }
}

var intervalid1 = setInterval(async () => {
    if (aiVideoId.value) {
        var url = route("ai-video.detail", { id: aiVideoId.value })
        try {
            const response = await axios.get(url)
            if (response.data.s3_url) {
                isSuccess.value = true
                isLoadingMerge.value = false;
            }
        } catch (error) {

        }
    }
}, 20000);


function checkExist(id, ids) {
    for (var i = 0; i < ids.length; i++) {
        if (ids[i] == id) {
            return true
        }
    }
    return false
}

const confirmPostVideo = async () => {
    if (!title.value) {
        alert("Tiêu đề không được để trống");
        return;
    }
    if (!description.value) {
        alert("Mô tả không được để trống");
        return;
    }
    if (isDisableButton.value) {
        return
    }
    var url = "/youtube/upload?title=" + title.value + "&description=" + description.value + "&id=" + videoPostId.value;
    try {
        isDisableButton.value = true
        const response = await fetch(url);
        isDisableButton.value = false
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }

        const json = await response.json();
        if (json["success"] == false && json["code"] == 400) {
            window.location.href = "/youtube/login";
        } else {
            alert(json["message"]);
            videoPostId.value = null;
            showConfirmModalVideo.value = false;
        }
    } catch (error) {
        isDisableButton.value = false
        console.error(error.message);
    }
};

const cancelDelete = () => {
    showConfirmModal.value = false; // Đóng modal xác nhận xóa
    videoDeleteId.value = null; // Xóa ID video cần xóa
};

const confirmDelete = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post(route("ai-video.delete", { id: videoDeleteId.value }));
        if (response.status === 200) {
            window.location.reload()
            showConfirmModal.value = false;
            // Remove the deleted video from the list instead of reloading the page
            // histories.value = histories.value.filter((item) => item.id !== videoDeleteId.value);
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi xóa:", error.message);
    } finally {
        isLoading.value = false;
        videoDeleteId.value = null; // Reset ID video sau khi xóa
    }
};

// Pagination fetch without full page reload
const totalPage = ref(props.list?.last_page || 1);
const currentPage = ref(props.list?.current_page || 1);
const typeQuery = ref(props.typeQuery || "");
const totalPageMerge = ref(props.list?.last_page || 1);
const currentPageMerge = ref(props.list?.current_page || 1);

const fetchPage = async (page) => {
    const url = route(("ai-video.ApiGetHistory"), { page: page, is_merge: false, is_image: true })
    const response = await axios.get(url)
    currentPage.value = response.data.list?.current_page;
    totalPage.value = response.data.list?.last_page;
    histories.value = response.data.list.data
};

const fetchPageIsMerge = async (page) => {
    const url = route(("ai-video.ApiGetHistory"), { page: page, is_merge: true, is_image: true })
    const response = await axios.get(url)
    currentPageMerge.value = response.data.list?.current_page;
    totalPageMerge.value = response.data.list?.totalPage;
    mergeHistories.value = response.data.list.data
};
fetchPage(1)
fetchPageIsMerge(1)
const fetchData = (url) => {
    window.location.href = url;
};

const downloadVideo = (video) => {
    var url = route(("ai-video.downloadFile"), { url: video, name: "video.mp4" })
    window.location.href = url
};

const createPost = (imageUrl) => {
    if (imageUrl) {
        let image = {
            s3_url: imageUrl,
            type: "video",
        };
        localStorage.setItem("aiImage", JSON.stringify(image));
        window.location.href = route("calendar");
    }
};
const openShareLink = () => {
    isShowShareLinkModal.value = true;
};

const closeShareLink = () => {
    isShowShareLinkModal.value = false;
};

const audioName = ref('');
const handleAudioFileChange = (event) => {
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav"];
    if (!allowedAudioTypes.includes(type)) {
        alert("Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.");
        audioInput.value.value = "";
        return;
    }
    audioFile.value = event.target.files[0];
    audioName.value = audioFile.value ? audioFile.value.name : 'Chưa có tệp nào được chọn';
};


const croppedImageUrl = ref(null);
const cropper = ref(null);
const isCropping = ref(false);
const imageInput = ref(null)
const imageName = ref('');
const imageGenerate = ref(null);
const imageUrl = ref(null)
const isTransparentImage = ref(null);
const handleImageFileChange = (event) => {
    var type = event.target.files[0].type;
    const alloweImageTypes = ["image/jpeg", "image/png", "image/jpg"];
    if (!alloweImageTypes.includes(type)) {
        alert("Xin vui lòng tải lên các ảnh có định dạng .png, .jpeg, .jpg");
        imageInput.value.value = "";
        return;
    }
    if (type) {
        isCropping.value = true;
        imageGenerate.value = null;
        imageInput.value = event.target.files[0];
        imageUrl.value = URL.createObjectURL(event.target.files[0]);
        checkImageTransparency(imageUrl.value, function (isTransparent) {
            isTransparentImage.value = isTransparent;
        });
    }
    imageInput.value = event.target.files[0];
    imageName.value = imageInput.value ? imageInput.value.name : 'Chưa có tệp nào được chọn';
};

const cancelCropping = (key) => {
    if (imageUrl.value) {
        const img = new Image();
        img.src = imageUrl.value;
        img.onload = function () {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            const targetSize = 1080;
            const aspectRatio = img.width / img.height;
            if (aspectRatio > 1) {
                canvas.width = targetSize;
                canvas.height = targetSize / aspectRatio;
            } else {
                canvas.height = targetSize;
                canvas.width = targetSize * aspectRatio;
            }
            ctx.drawImage(img, 0, 0, img.width, img.height, 0, 0, canvas.width, canvas.height);
            const resizedImageUrl = canvas.toDataURL();
            imageUrl.value = resizedImageUrl;
            const file = dataURLtoFile(resizedImageUrl, "resized-image.png");
            imageInput.value = file;
            isCropping.value = false;
        };
    }
    fileInputImage.value.value = "";
};

const aspectRatio = computed(() => {
    const resolution = "1:1";
    const [width, height] = resolution.split(":");
    return parseFloat(width) / parseFloat(height);
});

const cropImage = () => {
    if (cropper.value) {
        const croppedCanvas = cropper.value.getCroppedCanvas();
        let width = croppedCanvas.width;
        let height = croppedCanvas.height;
        if (width > 2048 || height > 2048) {
            const resizedCanvas = document.createElement("canvas");
            const ctx = resizedCanvas.getContext("2d");

            const maxDimension = 1080;
            if (width > height) {
                resizedCanvas.width = maxDimension;
                resizedCanvas.height = (height * maxDimension) / width;
            } else {
                resizedCanvas.height = maxDimension;
                resizedCanvas.width = (width * maxDimension) / height;
            }

            ctx.drawImage(croppedCanvas, 0, 0, width, height, 0, 0, resizedCanvas.width, resizedCanvas.height);

            croppedCanvas.width = resizedCanvas.width;
            croppedCanvas.height = resizedCanvas.height;
            croppedCanvas.getContext("2d").drawImage(resizedCanvas, 0, 0);

            width = croppedCanvas.width;
            height = croppedCanvas.height;
        }
        const croppedImage = croppedCanvas.toDataURL();
        imageUrl.value = croppedImage;
        const file = dataURLtoFile(croppedImage, "cropped-image.png");
        console.log("Image Dimensions:", width, "x", height);
        imageInput.value = file;
        isCropping.value = false;
    }
    fileInputImage.value.value = "";
};

const dataURLtoFile = (dataUrl, fileName) => {
    const [header, base64Data] = dataUrl.split(";base64,");
    const mime = header.split(":")[1];
    const binary = atob(base64Data);
    const array = new Uint8Array(binary.length);

    for (let i = 0; i < binary.length; i++) {
        array[i] = binary.charCodeAt(i);
    }

    return new File([array], fileName, { type: mime });
};

const shareAIGeneratedMedia = async (item) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: item.id,
            share_linkable_type: "AIGeneratedMedia",
        });

        shareLink.value = route("share-link.show", { token: response.data.data.link_token });
        openShareLink();
    } catch (error) {
        toast.error("Chia sẻ tin thất bại");
    }
};
</script>

<style scoped>
/* CSS for loader and other styles */
.bt-yellow {
    background: #ff7b00;
}

.all-video {
    max-height: 720px;
    overflow: auto;
}

.icon-tick {
    top: 10px;
    right: -90%;
}

/* HTML: <div class="loader"></div> */
.loader {
    width: 50px;
    aspect-ratio: 1;
    display: grid;
    border-radius: 50%;
    background:
        linear-gradient(0deg, rgb(0 71 171/50%) 30%, #0000 0 70%, rgb(0 71 171/100%) 0) 50%/8% 100%,
        linear-gradient(90deg, rgb(0 71 171/25%) 30%, #0000 0 70%, rgb(0 71 171/75%) 0) 50%/100% 8%;
    background-repeat: no-repeat;
    animation: l23 1s infinite steps(12);
}

.loader::before,
.loader::after {
    content: "";
    grid-area: 1/1;
    border-radius: 50%;
    background: inherit;
    opacity: 0.915;
    transform: rotate(30deg);
}

.loader::after {
    opacity: 0.83;
    transform: rotate(60deg);
}

@keyframes l23 {
    100% {
        transform: rotate(1turn)
    }
}

.show-load {
    width: 360px;
    text-align: center;
    height: 120px;
    background: rgb(255, 255, 255);
}

.loader {
    position: relative;
    margin-left: 165px;
    margin-top: 10px;
}

.show-load p {
    color: #000;
    margin-top: 20px;
}

@keyframes l27 {
    100% {
        transform: rotate(1turn)
    }
}

.box-video {
    padding: 20px;
    border: 2px solid #24aa69;
}

.text-des {
    color: #24aa69;
    font-weight: bold;
    font-size: 20px;
    margin-top: 10px;
}

.box-left {
    float: left;
}

.box-right {
    float: right;
}

.h-240 {
    height: 240px;
}

.icon-success {
    margin-left: 45%;
    margin-bottom: 10px;
}

@media only screen and (max-width: 770px) {
    .desktop {
        display: none;
    }
}

@media only screen and (max-width: 600px) {
    .flex-col-mobile {
        flex-direction: column;
    }

    .text-des[data-v-623ab925] {
        color: #2d75e3;
        font-weight: bold;
        font-size: 15px;
        margin-top: 10px;
    }

    .pd-10 {
        padding: 10px;
    }

    .icon-tick {
        right: -90%;
    }

    .justify-center-mobile {
        justify-content: center;
    }

    .text-des {
        font-size: 15px;
    }
}

.text-name-audio {
    max-width: 170px;
    overflow: hidden;
    color: #000;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.no-data {
    color: #000;
    font-size: 15px;
    font-weight: bold;
}

.text-number {
    position: absolute;
    margin-top: -32px;
    margin-left: 0px;
    font-size: 18px;
    font-weight: bold;
    text-align: center;
    width: 30px;
}

.left {
    float: left;
}

.w-380 {
    width: 380px
}

.clear {
    clear: both
}
</style>
