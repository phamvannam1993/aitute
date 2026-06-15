<template>
    <div class="border-b-2 h-16 fixed z-[49] inset-0 bg-white flex px-8 items-center justify-between">
        <div class="flex gap-4 items-center">
            <a :href="route('home.index')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-9">
                    <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                    <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                </svg>
            </a>
            <a :href="route('text-to-video.index')">
                <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <rect width="39" height="39" fill="url(#pattern0_7909_445)" />
                    <defs>
                        <pattern id="pattern0_7909_445" patternContentUnits="objectBoundingBox" width="1" height="1">
                            <use xlink:href="#image0_7909_445" transform="scale(0.0111111)" />
                        </pattern>
                        <image id="image0_7909_445" width="90" height="90" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAABaCAYAAAA4qEECAAAACXBIWXMAAAsTAAALEwEAmpwYAAAEt0lEQVR4nO2dy29bRRTGR+JVVARLWkACBPwDBVaAQIiHukDAwiCBkak93zeSqRVMBQmrsOOxqirxb1AeiyL4A0AoqIRs2AAbyqstCStiieZWRx4gRHFkNfM49975SZ9kJXYy83k8d+bcM8fGFAqFQqFQKBQKhUJhPjqdzlUA7nfODQC8B+BDkt8C+B7AHyQnInksP/O/k+e8K68heZ/8jTn/Xbuw1t5G8lWSnwD4k2S1T20A+BjAgnPuVtNmxuPx9QBeIvk5gEsBzJ2lvwF8RrLb6/UOmLYwHA5vkJFG8lxEc2fpdwBvkbzJNBWS15B8neTFDAbvlLThhLTJNAlr7UMA1hQY/D8B+A7AY6buyJxI8n2SW7lN3UNbAE6NRqPrTB1xzt1B8ksFRlZz6uvBYHC3qRPycQy0TKsSa8Na+6ipA9baZ0n+pcC06golG6LnjWZIMvKauEoh6YO1FkYjJJ+RzUFukxjW7OeMJmReA7CZ2xzGmUaeMBpwzt1T0wtfNac2SN6V1WRZe8qySIEZVWStZF1n+81I1QYBOJVtW618x1dF2EE+nNTk5eXlq0muKuh8lXhUryUNRPkoXPaOM4/Z45Tx5AstNvqCeBDdaJKLuTvL/DoR/fYTyV8Tjp5NeWOHw+EhkX+TJwqM/jnqbTF/jy+ZyQCO7mwDySUFRlfW2hejGS03UhN1ZELyqd3aMBgMbs5tsh8IZ6KYLLftEwWNJrNMFvr9/i1KjL4UJZXB511kmS62A+DN3CZva+9xExpJblFg8lFlUcLTQU2WFCuS67mmC8Fa+6TCOzfrQdPPJBeujGTuara19t5gRpO0ZSRz1qg+Fsxoyeos0wVnGf12MKNJflTmZM4y+oOQRq+2fHVR7aGzwYwG8GMqk2ND8jCANwLGTH4I1riAYdFFo4SAUcjzIRsV5N0fDoeHjBJ8NDDIpzRYo5potJvGbnQZHXDqWDJKCBhuDTd1NOli2J9G/xZVXgzL8o5plndlw8I0G5ayBWeaLbg/kVpF0KSm4dF/Za19OZjRJUzKvcw+EszoEvhnmsC/IGerY34EUc9gU7gL4bZOLsRuOOYwW0teh9crsRb62dMNBkryOsSLaCEFXyWgyml2X09eR5wEGkFKMSTsyOZu04iivI4XYtfY+CWl2ZzOyYe9lpQkOZ6LXvujzUno/E+vmdh0u92DEhpU0Nmq0Ynogi8q0lajF0zKw0IAvmmhyWvJq9aQfLAcf0uEHHLMPcqYTidN5iPKKwpMqCLrq06nc63JiRxI9wfTq4Zq3Vp7p9GAc+4RzcF5XrkmzrnHjSacc083rTAKyY7RiJTHaUKpH04HjDWa8SV/ajuNANhUV+Jnr9I/Nb1Arsv1xtSJfr9/O8kvFJhXzamV7CV99rPO9puaLQVGzpK07WT2dXLA7fqqAlN3atU594BpEsvTQNRYSYj1vEThpE2mqXS73YPSSQA/pTYYwG9SqHs0Gt1o2kKv1zsgpRhIfhp5oyOl58/IPb5WlZ7f4wDPcTlb7b+ZYr8jV/7Gacm70HTKQBWd6bnzIwD6AN6RbCDJQfZfBXLxn68Hkcf+Z2flOf65x+S15etBCoVCoVAoFAqFQsHMz2UzXkt6XPsIAQAAAABJRU5ErkJggg==" />
                    </defs>
                </svg>
            </a>
        </div>
        <div class="">
            <div class="flex items-center gap-4 h-16">
                <div @click="toggleThemeMenu" ref="themeMenuRef" class="relative cursor-pointer flex gap-2 py-3 px-4 bg-[#009FFF] text-white rounded-md">
                    <span>
                        <svg class="w-6 h-6 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.872 9.687 20 6.56 17.44 4 4 17.44 6.56 20 16.873 9.687Zm0 0-2.56-2.56M6 7v2m0 0v2m0-2H4m2 0h2m7 7v2m0 0v2m0-2h-2m2 0h2M8 4h.01v.01H8V4Zm2 2h.01v.01H10V6Zm2-2h.01v.01H12V4Zm8 8h.01v.01H20V12Zm-2 2h.01v.01H18V14Zm2 2h.01v.01H20V16Z" />
                        </svg>
                    </span>
                    <p class="hidden md:block">Chủ đề</p>
                    <section v-if="isThemeMenuActive" class="absolute w-[24rem] top-[3.5rem] border border-gray-200 h-[32rem] right-0 bg-white rounded-xl drop-shadow p-4 flex flex-col justify-between">
                        <h1 class="text-2xl font-bold mb-2 text-black">Chọn chủ đề</h1>
                        <div class="grid grid-cols-2 gap-2 overflow-auto">
                            <button @click="changeActiveTheme(theme)" v-for="(theme, index) in themes" :class="clonedTheme.id == theme.id ? `bg-[#d5ceff]` : 'bg-transparent'" :key="index" class="rounded-lg text-left p-2">
                                <div class="px-4 py-5 rounded-lg" :style="{ backgroundColor: theme.backgroundColor }">
                                    <div class="rounded-lg p-4" :style="{ backgroundColor: theme.slideColor, color: theme.textColor }">
                                        <h3 class="text-2xl font-bold" :style="{ color: theme.headingColor }">Tiêu đề</h3>
                                        <h4>Nội dung & <b class="underline">liên kết</b></h4>
                                    </div>
                                </div>
                                <div class="flex gap-4 items-center text-black">
                                    <h2>{{ theme.name }}</h2>
                                </div>
                            </button>
                        </div>
                    </section>
                </div>
                <button @click="enterFullScreen" class="flex gap-2 py-3 px-4 bg-[#F05123] text-white rounded-md text-sm lg:text-xl">
                    <span>
                        <svg class="w-6 h-" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M8.6 5.2A1 1 0 0 0 7 6v12a1 1 0 0 0 1.6.8l8-6a1 1 0 0 0 0-1.6l-8-6Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <p class="hidden md:block">Xem video</p>
                </button>
            </div>
        </div>
    </div>
    <div class="flex lg:flex-row flex-col justify-start relative mt-16 gap-2 h-[calc(100vh-4rem)] lg:h-auto pt-8 lg:pt-0" :style="{ backgroundColor: clonedTheme?.backgroundColor }">
        <div class="h-[255px] md:h-[180px] w-full lg:min-h-screen lg:w-[22rem] lg:overflow-y-scroll overflow-x-auto lg:overflow-x-hidden relative flex lg:justify-center">
            <div class="flex flex-row lg:flex-col gap-4 justify-start items-center p-2 pr-6 border-r-2 over-flow-x-scoll max-w-[200px]">
                <div v-for="(slide, index) in slides" :class="activeComponent == slide ? ' bg-gray-300' : ''" :key="index" class="relative group">
                    <div class="w-[218px] h-[167px] overflow-hidden p-2 rounded-md cursor-pointer z-[5]" @click="activeSlide(slide)">
                        <div class="absolute transform-o w-[960px] h-[720px] pointer-events-none overflow-hidden">
                            <span class="absolute top-0 left-1 text-8xl">{{ index + 1 }}</span>
                            <component :is="slide.component" v-bind="slide.props" v-model:data="slide.props" :active="false" :style="slide.props.card_style" class="w-[960px] h-[720px]" />
                        </div>
                    </div>
                    <button @click.stop="deleteSlide(index)" class="absolute top-2 right-2 text-8xl m-1 py-1 px-1 bg-gray-300 text-white rounded-md">
                        <svg width="20" height="20" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <rect width="23" height="23" fill="url(#pattern0_7920_1008)" />
                            <defs>
                                <pattern id="pattern0_7920_1008" patternContentUnits="objectBoundingBox" width="1" height="1">
                                    <use xlink:href="#image0_7920_1008" transform="scale(0.0111111)" />
                                </pattern>
                                <image id="image0_7920_1008" width="90" height="90" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAABaCAYAAAA4qEECAAAACXBIWXMAAAsTAAALEwEAmpwYAAAC0ElEQVR4nO3cvU5UURQF4O1Po4Uv4F/s9QnUQku1pRA7cvY6MyaTODZi48QGa39G6cFWC3wCbNBufALstBGC0QQstjlREwtMBA/ctY/7S1ZCyV2QeydnwRUJIYQQQgihGymlM6o6LJmZmTkdP4e9Kfmiqn4GYCXl65zz+Si7MgBvf5X8W1ai6PpFb25T9GYUXb9o2y5RtIiUe6iqvgGw9aeiCFK+txW39/vyqQDABkGR9pfZcPlJBsBtgvJsh7kljAAcBTAGsE5Qku1R1lT1yXA4PNJl0fMERdg+Zb6Tkkej0UFV/UpQgO1TvpRrjqLRaNH/261DVZ9JV8oDAsDj8sBouOBPqvqo04dhCCGEEML+a/S8Y13YAHhHUIxVzkTYAHhFUIxVzpKwAfCUoBirnLGwUdW7BMVY5cwKm5TSDYJirHKmhfEPXAiKscq5IGwAnCIoxmqm3++fFDZTU1OHVPVb1+WgUsq1jEajw8IIwPuuC0K9rAorAK8JCrJKWRZWAJ4TFGSVsiisADwgKMgqZU5YqepNgoKsUvrCKqV0jaAgq5SrwgrAOYKCrEZ6vd5ZYTUYDI51XRAqpVyLMGtkAFgTdo0MABNh18gAsCTsGhkAxsKukQFgVtg1MgBMC7tyWE5QlP1LXPyHVgsDQJ/xwL+1AUCZD/wbGwBWxQvnA8CyeOF8AFgUL5wPAHPiRTk0JyjMdpmeeFEOzQkKs90k53xFvPA8APSYD/xbGgAG7Af+jQwAa+KN0wFgIt44HQCWxBunA8BYvCmH5wTF2Q5zR7wph+cExdlOknO+Lt54HACyhwP/FgaAvocDf+8DgHo68Hc+AKyKV84GgGXxSlUXHN06FsQrT6/NVNWheJVSOkH+pl37ma2c83HxDMB9B7/N98S78vZD/Hg3npHmoYgckFaklC4DeKmqHwnK/QDgBYBLXfcSQgghhBBCCCGEIA59B+xmjxjX34rTAAAAAElFTkSuQmCC" />
                            </defs>
                        </svg>
                    </button>
                    <div class="absolute flex flex-col px-4 gap-8 z-10 -right-3 top-1/2 -translate-y-1/2 translate-x-1/2 opacity-0 group-hover:opacity-100">
                        <button
                            class="rounded-full"
                            :style="{
                                color: clonedTheme?.textColor,
                                backgroundColor: clonedTheme?.slideColor,
                            }"
                            v-if="index >= 1"
                            @click.stop="swap(index, index - 1)"
                        >
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 15 7-7 7 7" />
                            </svg>
                        </button>
                        <button
                            class="rounded-full"
                            :style="{
                                color: clonedTheme?.textColor,
                                backgroundColor: clonedTheme?.slideColor,
                            }"
                            v-if="index < props.slideSuggestions.length - 1"
                            @click.stop="swap(index, index + 1)"
                        >
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                </div>
                <button @click="createNewSlide" class="mt-4 py-2 flex justify-center items-center px-4 bg-gray-300 text-white rounded w-[192px] h-[115px]">
                    <svg width="77" height="55" viewBox="0 0 77 55" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <rect width="77" height="55" fill="url(#pattern0_7920_999)" />
                        <defs>
                            <pattern id="pattern0_7920_999" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <use xlink:href="#image0_7920_999" transform="matrix(0.00793651 0 0 0.0111111 0.142857 0)" />
                            </pattern>
                            <image id="image0_7920_999" width="90" height="90" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAABaCAYAAAA4qEECAAAACXBIWXMAAAsTAAALEwEAmpwYAAAEpklEQVR4nO2dS4tcRRTHC3xFIro1KqioXyDqyogi6lZdtIK2tump/7+gTRPbIRl3487HKgh+DaNmEdEPoMhIjG7cqBujaBJnXJmBOCWH1IgZupuOt1637/nBgaGnH1X/W7cep06da4yiKIqiKIqiKIqyGL1e7zoADzvnVgC8B+Ajkt8C+AHAHyS3xeRveS38T97zrnyG5EPyHQv+XLew1t5F8nWSpwD8SdI3tC0AnwA46py703SZyWRyM4CXSX4O4O8I4s6yywA+I9kfDAb7TFcYjUa3SEsjeS6huLPsdwBvkbzNLCskbyB5jOTFAgLvNSnDqpTJLBPW2kcBfFeBwFcZgO8BPGnajvSJJD8guVNa1Dm2A+D98Xh8k2kjzrl7SH5ZgZB+Qft6ZWXlftMm5HaMNE3zmW3LWvuEaQPW2udI/lWBaP5/miyIXjA1Q5KJ58Q+h0kdrLUwNULyWVkclBaJccV+3tSE9GsALpUWh2m6kadNDTjnHmjpwOcXtC2S9xUVWeaeMi2qQAyf2DaKzrPDYiTnbXyM5AExAMfDa1l+XxY1RUQG8FjOFR+A41Mu9FrGCy11PZRV5PX19etJns1YSS+teG85RqPR7TnLEPw1+RxR4RbOKbKfU5as5QAwyelPvtBhoS+IBsmFztwv+tqEDraaw+35S9eFBvCrbMUlE9o590qhFuRrEjrYi8mElo1UFZq7rfp0EpFl276k08jMvvjFnE5JQhlC3EWp29TXJnQQ+0gKoU+p0Nwr9odRRZYQK5KbKjT3Cr0ZNfxMYuFKisxKuw4xa+2D0YQmaUtXyFQqNMnD0YSWqM7SFTL1Cv12NKFJfly6QqZeoeMNiAVcor5FQp+JJjSAn2LsjJjKiLRT82O0AjVxi07bGamNhh7J8zEL0uSKHzCV02SnRsIsqhB6OBzeYSon+HHKC91wR2XNVA7JN6voOiIMhms1tmwpU+if6xgMdXrHPNM7XbAwz4JFl+DMswQPJ1J9STMzKF0ua+2r0YRWNynniX0wmtDq+Gcex78gZ6u162C6gfA/QsuxYu2jeZUGr6Wa3Gu4Af8V+bL4SEwKQpYAnXUwYQBNEFrSPqjQTBwSFoIcz3V9Ho3UQY6lgtBZmdAk3zCp6ff7+8U12FWhkSsQPVRutcNCHzU5DwsB+CZnBYdT/NlNdkZacVhoN6tM5oQna5F3Rq7Vdpxzj5gSyCHHjBXd3t2pibQzcq12whQ+oryRsbK+kH3V6/VuNCWRA+nhYLpfUtu01t5rasA593jLs874GbbtnHvK1IRz7pllS4xCsmdqRNLjLEOqH15pMNbUTEj509puBMCl6lL8zEv909IBclPGG9MmhsPh3SS/qEA8v6BtFE/p02SeHRY1OxUIOcukbCeKz5NjQPJQDWFlU+xssWV1YkfUpISLdYqdFy+clMksK/1+f79UEsDPuQUG8Jsk6h6Px7earjAYDPZZa18i+WnihY6knj8te3ydSj0/59DOEZInw5MpmrZc+Y6TEneRLCSg7fSunDs/CGAI4B2JBpIY5PAokIu7jweRv8NrZ+Q94b2H5bP6eBBFURRFURRFURSzOP8AKdWkB4CG7BIAAAAASUVORK5CYII=" />
                        </defs>
                    </svg>
                </button>
            </div>
        </div>
        <div class="flex flex-col gap-2 relative w-full h-full mt-2 md:mt-8 items-center pb-12 lg:pb-0 overflow-hidden">
            <div class="slide-outer-wrapper w-full h-full md:w-[560px] md:h-[300px] lg:w-[740px] lg:h-[600px] xl:w-[960px] xl:h-[720px] relative lg:border lg:border-white rounded-2xl flex items-center justify-center">
                <CardStyleMenu :theme="theme" class="z-20" />
                <div class="absolute top-[8px] left-[75px] z-10 flex" ref="target">
                    <div class="flex items-center gap-3">
                        <button v-if="!hasFile" @click="$refs.fileInput.click()" class="text-blue-500 hover:text-blue-700 rounded w-8 h-8 flex items-center justify-center text-lg">
                            <img class="bg-[rgba(0,0,0,0.7)] rounded px-[6px] py-[4px]" src="/assets/svgs/image.svg" alt="" />
                        </button>

                        <!-- Nút xóa ảnh nền -->
                        <button v-else @click="resetBackground" class="text-black rounded-full w-8 h-8 flex items-center justify-center text-sm">
                            <img class="bg-[rgba(0,0,0,0.7)] rounded px-[6px] py-[4px]" src="/assets/svgs/delete.svg" alt="" />
                        </button>
                        <AddTextButton class="" :active-component="activeComponent" />
                        <AddImageButton class="" :active-component="activeComponent" />
                        <AddVideoButton class="" :active-component="activeComponent" />
                    </div>
                </div>
                <div class="flex items-center xl:w-full justify-end relative top-[-15px] md:top-0">
                    <div class="flex gap-0 items-center">
                        <!-- Ẩn input file -->
                        <input ref="fileInput" type="file" @change="handleChangeBackground" class="hidden" />
                    </div>
                </div>
                <!-- VIDEO HERE -->
                <div class="w-[373px] h-[265px] md:w-[720px] md:h-[280px] lg:w-full lg:h-full">
                    <!-- import background -->

                    <component v-if="activeComponent" :is="activeComponent.component" v-bind="activeComponent.props" v-model:data="activeComponent.props" :style="activeComponent.props.card_style" :active="true" class="transform-o-3 flex w-[960px] h-[720px]" />
                </div>
            </div>
        </div>
        <div class="fixed bottom-0 right-0 w-screen z-[55]"></div>
        <div
            v-if="isFullScreen"
            class="fixed inset-0 z-50 flex items-center justify-center"
            :style="{
                backgroundColor: activeComponent.props?.background_style?.backgroundColor ? activeComponent.props?.background_style?.backgroundColor : '#000',
            }"
        >
            <div class="fixed inset-0 z-50" style="pointer-events: all;"></div>
            <div class="md:scale-[70%] lg:flex items-center justify-center lg:w-full lg:h-full screen-o-4 overflow-hidden">
                <component :is="activeComponent.component" v-bind="activeComponent.props" :preview="true" v-model:data="activeComponent.props" :active="true" :isPaused="isPaused" :is-present="true" class="slide-content slide-presenting transform-o-4" :style="activeComponent.props.card_style" />
            </div>
            <!-- Exit Fullscreen Button -->
            <button @click="exitFullScreen" class="absolute top-0 left-0 px-4 py-2 z-[9999]">
                <img src="/assets/img/slide_ai/icon/out.png" alt="out" />
            </button>
            <div class="flex justify-center items-center gap-2 bg-slate-500 bg-opacity-70 border-white px-4 py-2 rounded-lg absolute bottom-0 z-[9999]">
                <button @click="prevSlide" class="text-black rounded-full">
                    <img src="/assets/img/icon/Artboard 3.png" class="w-12 h-12" alt="out" />
                </button>
                <button @click="togglePause" class="text-black rounded-full">
                    <img v-if="!isPaused" src="/assets/img/icon/Artboard 2.png" class="w-12 h-12" alt="out" />
                    <img v-else src="/assets/img/icon/Artboard 1.png" class="w-12 h-12" alt="out" />
                </button>
                <button @click="nextSlide" class="text-black rounded-full">
                    <img src="/assets/img/icon/Artboard 4.png" class="w-12 h-12" alt="out" />
                </button>
            </div>
        </div>
    </div>
    <div v-if="storeSuccess" class="fixed flex items-center gap-2 w-fit top-[6px] right-10 z-50 px-2 py-2 text-green-600">
        <div class="spinner"></div>
    </div>
    <div v-if="loading" class="fixed inset-0 bg-black z-[9999] bg-opacity-60 flex flex-col gap-2 justify-center items-center">
        <img src="/assets/img/dashboard/loading.gif" alt="" />
        <h3 class="text-white">Hệ thống đang xử lý, xin đợi một chút</h3>
    </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from "vue";
import { systemThemes as themes } from "./Components/CardStyleEditor/themes";
import CardStyleMenu from "@/Pages/Client/TextToVideo/Components/CardStyleEditor/CardStyleMenu.vue";
import { useSlideAIStore } from "@/Pages/Client/TextToVideo/Store/state.js";
import axios from "axios";
import theme from "tailwindcss/defaultTheme";
import Template10 from "./Components/VideoTemplate/Template10.vue";
import Template11 from "./Components/VideoTemplate/Template11.vue";
import Template12 from "./Components/VideoTemplate/Template12.vue";
import Template13 from "./Components/VideoTemplate/Template13.vue";
import AddTextButton from "./Components/AddTextButton.vue";
import AddImageButton from "./Components/AddImageButton.vue";
import AddVideoButton from "./Components/AddVideoButton.vue";

const props = defineProps({
    slideSuggestions: String,
    theme: String,
    topic: String,
    video_id: String,
});

console.log(props.slideSuggestions);

const topic = ref(props.topic);
const clonedTheme = ref(props.theme);
const slides = ref([]);
// const isTyping = ref(true);
const activeComponent = ref(null);
const miniMenuRef = ref(null);
const themeMenuRef = ref(null);
const isThemeMenuActive = ref(false);
const isListOpen = ref(false);
const listRef = ref(null);
const storeSuccess = ref(false);
const slideId = ref(null);
const successTitle = ref("");
const oneDriveSlide = ref(null);
const isMiniMenuActive = ref(false);
const slideAI = useSlideAIStore();
slideAI.data = props.slideSuggestions;
const isFirstLoad = ref(false);
const isShowConfirm = ref(false);
const isFirstSave = ref(0);
const hasFile = ref(false);

const isPaused = ref(false);
const backgroundImage = ref("");
const fileInput = ref("");
const activeSlide = (slide) => {
    // if (isFirstLoad.value) {
    //     isTyping.value = false;
    // }
    slideAI.currentSlide = slide;
    activeComponent.value = slide;
    isFirstLoad.value = true;
};
const swap = (i, j) => {
    const updatedData = slides.value;
    const temp = updatedData[i];
    updatedData[i] = updatedData[j];
    updatedData[j] = temp;
    slides.value = updatedData;
};

const handleSaveSlide = async () => {
    try {
        const res = await axios.post(route("slide-ai.update-ai-slide"), {
            id: slideId.value,
            slides: slides.value,
            theme: clonedTheme.value,
            title: topic.value,
        });
        if (res.status === 200) {
            handleCallApiSuccess("");
        }
    } catch (error) {
        console.log("🚀 ~ handleSaveSlide ~ error:", error);
    }
};

// const saveToDocument = async () => {
//     try {
//         const res = await axios.post(route("slide-ai.save-ai-slide"), {
//             slides: slides.value,
//             theme: clonedTheme.value,
//             title: topic.value,
//         });
//         if (res.status === 200) {
//             slideId.value = res?.data?.status?.id;
//             isShowConfirm.value = false;
//             isFirstSave.value += 1;
//             handleCallApiSuccess("Lưu vào kho tài liệu thành công");
//         }
//     } catch (error) {
//         console.log("🚀 ~ saveToDocument ~ error:", error);
//     }
// };

// handle user change background
const handleChangeBackground = (event) => {
    const file = event.target.files[0];
    // user Reader to red file
    const reader = new FileReader();
    reader.onload = function (e) {
        // update value background after load file image
        activeComponent.value.props.background = e.target.result;
        // re set input file for the next onchange
        event.target.value = null;
    };
    hasFile.value = !!file;
    if (file) {
        // read file
        reader.readAsDataURL(file);
    }
};
// reset background when click X
const resetBackground = () => {
    if (fileInput.value || activeComponent.value.props.background) {
        hasFile.value = false;
        activeComponent.value.props.background = "";
        fileInput.value.files = "";
    }
};

const backgrounds = [
    "/assets/images/gifs/bg_01.gif",
    "/assets/images/gifs/bg_02.gif",
    "/assets/images/gifs/bg_03.gif",
];

const randomBackground = () => {
    const randomIndex = Math.floor(Math.random() * backgrounds.length);
    console.log(backgrounds[randomIndex])
    return backgrounds[randomIndex];
}

watch(
    () => props.slideSuggestions,
    async (newVal) => {
        try {
            slides.value = props.slideSuggestions
                .map((suggestion) => {
                    console.log(suggestion);
                    return { 
                        component: Template11, 
                        props: { 
                            ...suggestion.props || suggestion,
                            background: randomBackground()
                        } 
                    };
                }) 
                .filter((slide) => slide !== null);

            if (slides.value.length > 0) {
                activeSlide(slides.value[0]);
            } else {
                activeSlide(null);
            }
            // saveToDocument();
            isFirstSave.value = 1;
        } catch (error) {
            console.error("Error parsing slide suggestions:", error);
        }
    },
    { immediate: true }
);

const loading = ref(false);

const onToggleListSlides = () => {
    isListOpen.value = !isListOpen.value;
};

const closeList = () => {
    isListOpen.value = false;
};

const handleClickOutside = (event) => {
    if (listRef.value && !listRef.value.contains(event.target)) {
        closeList();
    }
    if (miniMenuRef.value && !miniMenuRef.value.contains(event.target)) {
        isMiniMenuActive.value = false;
    }
    if (themeMenuRef.value && !themeMenuRef.value.contains(event.target)) {
        isThemeMenuActive.value = false;
    }
};

const toggleChangeSlide = (key) => {
    activeComponent.value.props.key = key;
    activeComponent.value.component = key;
};

watch(
    [slides.value, clonedTheme.value],
    async () => {
        if (isFirstSave.value !== 0 && slideId.value != null) {
            handleSaveSlide();
        }
    },
    { deep: true }
);

onMounted(async () => {
    document.addEventListener("click", handleClickOutside);
    slideAI.currentSlide = slides.value[0];
    activeComponent.value = slides.value[0];
    window.addEventListener("beforeunload", handleBeforeUnload);
});

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside);
    window.removeEventListener("beforeunload", handleBeforeUnload);
});

const handleBeforeUnload = (event) => {
    updateVideoBeforeUnload();
};

const updateVideoBeforeUnload = async () => {
    try {
        const res = await axios.post(route("text-to-video.update-video"), {
            video_id: props.video_id,
            slides: slides.value,
            theme: clonedTheme.value,
            title: topic.value,
        });
        if (res.status === 200) {
            console.log("Video updated successfully before leaving the page");
        }
    } catch (error) {
        console.error("Error updating video before leaving the page:", error);
    }
};

const isFullScreen = ref(false);
const enterFullScreen = () => {
    isFullScreen.value = true;
    activeComponent.value = slides.value[0];
    playAudioAndAdvance();
};

let audio;

const playAudioAndAdvance = () => {
    let hostname = window.location.origin;
    if (!activeComponent.value || !activeComponent.value.props.audio) return;

    audio = new Audio(hostname + activeComponent.value.props.audio);

    audio.play();

    audio.addEventListener("ended", () => {
        nextSlide();
    });
};

const exitFullScreen = () => {
    isFullScreen.value = false;
    if (audio) audio.pause();
    audio = null;
};

const nextSlide = () => {
    const currentIndex = slides.value.indexOf(activeComponent.value);
    if (currentIndex < slides.value.length - 1) {
        if (audio) {
            audio.pause();
            audio.currentTime = 0;
            audio = null;
        }

        activeComponent.value = slides.value[currentIndex + 1];
        isPaused.value = false;
        if (audio) audio = null; // Stop current audio
        playAudioAndAdvance(); // Play the next audio
    }
};

const prevSlide = () => {
    const currentIndex = slides.value.indexOf(activeComponent.value);
    if (currentIndex > 0) {
        if (audio) {
            audio.pause();
            audio.currentTime = 0;
            audio = null;
        }

        activeComponent.value = slides.value[currentIndex - 1];
        isPaused.value = false;

        playAudioAndAdvance();
    }
};

const pauseAudio = () => {
    if (audio) audio.pause();
};

const resumeAudio = () => {
    if (audio) audio.play();
};

const togglePause = () => {
    isPaused.value = !isPaused.value;
    if (audio && audio.paused) {
        resumeAudio();
    } else if (audio) {
        pauseAudio();
    }
};

const deleteSlide = (index) => {
    if (confirm("Bạn có chắc chắn muốn xóa slide này không?")) {
        slides.value.splice(index, 1);
        if (slides.value.length > 0) {
            activeComponent.value = slides.value[0];
        } else {
            activeComponent.value = null;
        }
    }
};

const createNewSlide = () => {
    let props = JSON.parse(JSON.stringify(slides.value[0].props));

    props.title = "Nhập tiêu đề";
    props.descriptions = [
        {
            sub_title: "Nhập tiêu đề phụ 1",
            description: "Nhập nội dung",
        },
        {
            sub_title: "Nhập tiêu đề phụ 2",
            description: "Nhập nội dung",
        },
    ];

    const newSlide = {
        component: Template11,
        props: props,
    };

    slides.value.push(newSlide);
};

const handleCallApiSuccess = (title) => {
    successTitle.value = title;
    storeSuccess.value = true;
    setTimeout(() => {
        storeSuccess.value = false;
    }, 2000);
};

const toggleThemeMenu = () => {
    isThemeMenuActive.value = !isThemeMenuActive.value;
};

const changeActiveTheme = (theme) => {
    clonedTheme.value = theme;
    props.slideSuggestions.map((suggestion) => {
        suggestion.theme = theme;
        suggestion.title_style.color = theme.headingColor;
        suggestion.subtitle_style.color = theme.textColor;
        suggestion.description_style.color = theme.textColor;
    });
};
</script>

<style scoped>
.transform-o {
    transform-origin: 8px 4px;
    transform: scale(0.2, 0.2);
}

.transform-o-2 {
    transform-origin: 15px 10px;
    transform: scale(0.2, 0.2);
    height: 720px !important;
}

.slide-container {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.slide-content {
    transform: scale(1.2);
    width: 960px;
    height: 720px;
}

.spinner {
    border: 4px solid rgba(0, 0, 0, 0.1);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border-left-color: #09f;
    animation: spin 1s ease infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.json-viewer {
    width: 800px;
    background-color: #f4f4f4;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    font-family: monospace;
    font-size: 16px;
    line-height: 1.4;
    overflow-x: auto;
}

.json-viewer pre {
    white-space: pre-wrap;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

@media (max-width: 1024px) {
    .transform-o-3 {
        transform-origin: 8px 4px;
        transform: scale(0.75, 0.75);
    }
    .screen-o-4 {
        width: 768px;
        height: 580px;
    }
    .transform-o-4 {
        transform-origin: 8px 4px;
        transform: scale(0.797, 0.797);
    }
}
@media (max-width: 767px) {
    .transform-o-3 {
        transform-origin: 8px 4px;
        transform: scale(0.377, 0.37);
    }
    .screen-o-4 {
        width: 372px;
        height: 274px;
    }
    .transform-o-4 {
        transform-origin: 8px 4px;
        transform: scale(0.377, 0.37);
    }
}
</style>
