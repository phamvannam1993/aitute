<template>
    <Modal :show="pageData.is_show_post_detail_modal" alignItems="!items-start" maxWidth="xl-6xl" minWidth="w-full"
      @close="closePostDetail">
      <form @submit.prevent="submit" class="relative px-1 sm:px-10 py-2 text-xs lg:text-sm">
        <h2 class="text-ai3goc-sec font-bold text-2xl mb-4">Bài viết</h2>
        <div>

          <div class="flex flex-col lg:flex-row gap-2 mb-4 items-center">
            <div class="flex w-full lg:w-2/5">
              <Step :step="1" stepName="Chọn nền tảng" />
            </div>
            <div class="w-full">
              <div class="flex flex-wrap gap-1 justify-between w-full">
                <label v-for="(value, key) in SOCIAL_POSTABLE_TYPE" :key="key" class="flex items-center gap-1">
                  <input @change="handleChangeSocialPostableType" :disabled="!!form.id" type="radio"
                    v-model="form.social_postable_type" :value="value" :class="form.id ? 'bg-gray-200' : ''"
                    class="rounded-full text-orion-orange focus:ring-orion-orange ml-0" />
                  <span>{{ value.replace('Fanpage', '') }}</span>
                </label>
              </div>
              <div v-if="$page.props.errors?.social_postable_type" v-text="$page.props.errors?.social_postable_type"
                class="text-red-500 my-1">
              </div>
            </div>
          </div>
          <div class="mt-4 mb-7">
            <div v-if="form.social_postable_type === SOCIAL_POSTABLE_TYPE.FacebookFanpage">
              <div class="flex flex-wrap flex-1 items-center justify-center space-x-2">
                <div v-for="(fanpage) in pageData.fanpages" :key="fanpage.id" class="flex items-center">
                  <div @click="activeSocialPostableId(fanpage)"
                    :class="form.social_postable_id == fanpage.id ? 'border-[#FFA411]' : ''"
                    class="cursor-pointer relative  border-b-4 p-2">
                    <a class="flex flex-col items-center gap-1" :title="fanpage.page_name">
                      <img :src="fanpage.page_image ? fanpage.page_image : '/assets/img/login_icon/success.png'"
                        class="w-8 lg:w-10 aspect-square" alt="Avatar" />
                      <span class="text-xs lg:text-sm font-medium text-gray-700">{{
                        $helpers.truncateToTwoWords(fanpage.page_name) }}</span>
                    </a>
                  </div>

                </div>
              </div>
              <div v-if="$page.props.errors?.social_postable_id" v-text="$page.props.errors?.social_postable_id"
                class="text-red-500 my-1">
              </div>
            </div>
          </div>
          <div class="flex flex-col lg:flex-row gap-2 mb-4 items-center">
            <div class="flex w-full lg:w-2/5">
              <Step :step="2" stepName="Chọn ngày giờ đăng" />
            </div>
            <div class="w-full flex flex-col gap-3">
              <div class="flex flex-col sm:flex-row justify-start sm:justify-between gap-5 w-full items-center">
                <div class="flex gap-1 lg:gap-5  items-center justify-center">
                  <label class="flex items-center gap-1 lg:gap-2 h-full">
                    <input v-if="!form.id || (form.id && !form.published)" type="radio" v-model="form.published" :value="0"
                      class="rounded-full text-orion-orange focus:ring-orion-orange" />
                    <div class="date-picker  w-[160px] sm:w-[200px]">
                      <VueDatePicker format="dd/MM/yyyy" :ui="{ input: 'rounded-[10px] border-[#B5B5BE]' }" locale="vi"
                        :enable-time-picker="false" v-model="pageData.select_date" auto-apply :close-on-auto-apply="false"
                        :min-date="minDate" :max-date="maxDate" :disabled="form.published ? true : false"
                        :day-names="['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']" />
                    </div>
                    <div class="time-picker  w-[110px] sm:w-[130px]">
                      <VueDatePicker format="HH:mm" :ui="{ input: 'rounded-[10px] border-[#B5B5BE]' }" time-picker
                        :disabled="form.published ? true : false" v-model="pageData.select_time" auto-apply>
                        <template #input-icon>
                          <img class="w-5 ml-3" src="/assets/svgs/icon-clock.svg" />
                        </template>
                      </VueDatePicker>
                    </div>
                  </label>
                </div>
                <div v-if="!form.id || (form.id && !form.published)" class="flex  justify-start sm:justify-between gap-2 items-center ">
                  <span class="text-gray-500 italic">hoặc</span>
                  <label class="flex items-center gap-2">
                    <input type="radio" v-model="form.published" :value="1" class="text-orion-orange focus:ring-orion-orange ml-0 rounded-full" />
                    <span :class="form.published ? '' : 'bg-gray-100'"
                      class="py-1 px-4 border border-gray-500 rounded-full">Đăng bài
                      ngay</span>
                  </label>
                </div>
              </div>
              <div v-if="$page.props.errors?.scheduled_publish_time" v-text="$page.props.errors?.scheduled_publish_time"
                class="text-red-500 my-1">
              </div>
            </div>
          </div>
          <div>
            <div class="flex flex-col lg:flex-row gap-2 mb-4 items-center">
              <div class="flex w-full lg:w-2/5">
                <Step :step="3" stepName="Nội dung bài đăng" />
              </div>
            </div>
            <div class="w-full mb-4 relative">
              <SuggestionPrompt v-model="form.content"  />
                    <textarea id="description" v-model="form.content" type="text"
                        placeholder="Nhập nội dung..."
                        class="p-3 w-full  h-[160px] border text-black border-[#B5B5BE] rounded-[10px] shadow-sm "></textarea>
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
                      :disabled="disabledRecord"
                      type="button"
                  >
                      <img src="/assets/img/ai3goc/icon/mic.svg" alt="microphone" />
                  </button>
              </div>
              <div v-if="$page.props.errors?.content" v-text="$page.props.errors?.content" class="text-red-500 my-1">
              </div>
            </div>
          </div>

          <div>
            <div class="flex flex-col lg:flex-row gap-2 mb-4 items-center">
              <div class="flex w-full lg:w-2/5">
                <Step :step="4" stepName="Chọn ảnh / video đăng" />
              </div>
            </div>
            <div class="w-full mb-4">
              <div class="w-full flex flex-col lg:flex-row ">
                <div class="w-ful lg:w-1/2">
                  <div class="flex justify-center gap-16">
                    <label
                      class="bg-[#207A91] h-10 text-white py-2 text-xs lg:text-sm rounded-lg lg:rounded-2xl px-6 hover:bg-cyan-500 flex items-center gap-2">
                      <img class="hidden sm:block size-6" src="/assets/img/ai3goc/icon/upload.svg" alt="" />
                      Tải ảnh
                      <input type="file" multiple accept="image/*" @change="handleImageUpload($event)" class="hidden" />
                    </label>
                    <button type="button" @click="openTextToImage"
                      class="bg-[#149CBE] h-10 text-white py-2 text-xs lg:text-sm rounded-lg lg:rounded-2xl px-6 hover:bg-cyan-500 flex items-center gap-2">
                      <img class="hidden sm:block size-6" src="/assets/img/my_assistant/generate_image.png" alt="" />
                      Tạo ảnh
                    </button>
                  </div>
                  <div class="grid grid-cols-2 gap-4 p-8">
                    <div v-for="(file, index) in pageData.image_files" :key="index">
                      <label class="relative">
                        <input type="checkbox" :checked="checkImage(file)" @change="selectImage(file)"
                          class="ml-0 top-2 right-2 absolute rounded-full" />
                        <img :src="file.url"
                          class="rounded-lg lg:rounded-2xl  bg-gray-200 object-contain aspect-[4/3] w-full" />
                        <button type="button" @click="removeImage(index)"
                          class="absolute bottom-2 right-2 bg-red-500 text-white w-6 h-6 rounded-full">x
                        </button>
                      </label>
                    </div>
                    <div v-for="i in MAX_IMAGE_FILES - pageData.image_files.length">
                      <label>
                        <div
                          class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden">
                          <input type="radio" disabled class="bg-gray-200 ml-0 top-2 right-2 absolute rounded-full" />
                          <img src="/assets/svgs/aiwow/image.svg" class="mx-auto w-24" />
                        </div>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="w-ful lg:w-1/2">
                  <div class="flex justify-center gap-16">
                    <label
                      class="bg-ai3goc-sec h-10 text-white px-6 py-2 text-xs lg:text-sm rounded-lg lg:rounded-2xl hover:bg-blue-500 flex items-center gap-2">
                      <img class="hidden sm:block" src="/assets/img/my_assistant/upload_video.png" alt="" />
                      Tải video
                      <input type="file" accept="video/mp4,video/x-m4v,video/webm,video/ogg,video/*,.flv,.3gp"
                        class="inputMedia hidden" @change="handleVideoUpload($event)" />
                    </label>

                  </div>
                  <div class="grid grid-cols-1 gap-4 p-8">
                    <div v-for="(file, index) in pageData.video_files" :key="index">
                      <div class="relative">
                        <div
                          class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden">
                          <label class="ml-0 w-16 h-16 cursor-pointer top-0 right-0 p-2 z-10 text-right absolute">
                            <input type="checkbox" :checked="checkVideo(file)" @change="selectVideo(file)"
                              class=" rounded-full" />
                          </label>
                          <video :src="file.url" controls class="rounded overflow-hidden h-[320px] md:h-[380px] objet-fit"></video>
                          <button type="button" @click="removeVideo(index)"
                            class="absolute bottom-2 right-2 bg-red-500 text-white w-6 h-6 rounded-full">x
                          </button>
                        </div>
                      </div>
                    </div>

                    <div v-if="pageData.video_files.length < 1">
                      <label>
                        <div
                          class="flex relative items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden">
                          <input type="radio" disabled class="bg-gray-200 ml-0 top-2 right-2 absolute rounded-full" />
                          <img src="/assets/img/aiwow/homepage/play_button.png" alt="loading" class="mx-auto w-24" />
                        </div>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="$page.props.errors?.files" v-text="$page.props.errors?.files" class="text-red-500 my-1">
              </div>
            </div>
          </div>
          <div class="mb-8 border-b-2 border-gray-300"></div>
          <div v-if="$page.props.errors?.message" v-text="$page.props.errors?.message" class="text-red-500 mb-8 ">
          </div>
          <div class="text-center mb-8">
            <button type="submit"
              class="text-center inline-flex gap-2 items-center justify-center w-44 px-2 py-2 lg:py-3 bg-ai3goc-sec rounded-lg lg:rounded-2xl border border-[#C5C5C5] text-white font-bold">
              <img class="h-5 w-6" src="/assets/img/aiwow/btn-post.png" />
              Đăng bài
            </button>
          </div>
        </div>
      </form>

    </Modal>

    <Modal :show="pageData.is_text_to_image_modal" alignItems="items-start" maxWidth="xl-6xl" minWidth="w-full"
      @close="closeTextToImage">
      <TextToImage :showImage="false" @saveGenerationResult="saveGenerationResult" @onSubmit="onSubmitTextToImage"
        @onFinish="onFinishTextToImage" />
    </Modal>

    <Loading v-if="pageData.is_loading" />

  </template>
  <script setup>
  import Step from "@/Components/Step/Index.vue";
  import Loading from "@/Components/Loading.vue";
  import Modal from "@/Components/Modal.vue";
  import { defineEmits, inject, ref } from 'vue';
  import VueDatePicker from "@vuepic/vue-datepicker";
  import TextToImage from "@/Pages/Client/AIImage/TextToImageComponent.vue";
  import { SOCIAL_POSTABLE_TYPE } from "@/constants/social_postable";
  import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";

  const isRecording = ref(false);
  let mediaRecorder = null;
  let audioChunks = [];
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

  defineProps({
    pageData: {
      type: Object,
      required: true,

    },
    form: {
      type: Object,
      required: true,
    },
    minDate: {
      type: String,
      required: true,
    },
    maxDate: {
      type: String,
      required: true,
    },
    MAX_IMAGE_FILES: {
      type: Number,
      required: true,
    },
    handleChangeSocialPostableType: {
      type: Function,
      required: true,
    },
    handleImageUpload: {
      type: Function,
      required: true,
    },
    openTextToImage: {
      type: Function,
      required: true,
    },
    handleVideoUpload: {
      type: Function,
      required: true,
    },
    checkImage: {
      type: Function,
      required: true,
    },
    removeImage: {
      type: Function,
      required: true,
    },
    selectImage: {
      type: Function,
      required: true,
    },
    checkVideo: {
      type: Function,
      required: true,
    },
    removeVideo: {
      type: Function,
      required: true,
    },
    selectVideo: {
      type: Function,
      required: true,
    },
    saveGenerationResult: {
      type: Function,
      required: true,
    },
    closeTextToImage: {
      type: Function,
      required: true,
    },
    onSubmitTextToImage: {
      type: Function,
      required: true,
    },
    onFinishTextToImage: {
      type: Function,
      required: true,
    },
    closePostDetail: {
      type: Function,
      required: true,
    },
    activeSocialPostableId: {
      type: Function,
      required: true,
    },
    submit: {
      type: Function,
      required: true,
    },
  });

  </script>
