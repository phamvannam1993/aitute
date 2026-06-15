<template>
  <component :is="templates[pageData.template_post_form]" v-bind="formProps" />
  
  <FacebookFormModal ref="facebookFormModalRef" @onSuccess="onSuccessFacebookForm" />

  <Loading v-if="pageData.is_loading" />

</template>

<script setup>
import Loading from "@/Components/Loading.vue";
import dayjs from "dayjs";
import { defineEmits, onMounted, reactive, ref, watch } from "vue";
import { toast } from "vue3-toastify";
import { usePage } from "@inertiajs/vue3";
import { SOCIAL_POSTABLE_TYPE } from "@/constants/social_postable";
import BasicForm from "./TemplatePostForm/BasicForm.vue";
import ModelFullForm from "./TemplatePostForm/ModelFullForm.vue";
import AutoPostAIForm from "./TemplatePostForm/AutoPostAIForm.vue";
import FacebookFormModal from "./components/FacebookFormModal.vue";

import '@vuepic/vue-datepicker/dist/main.css';
import "./styles/PostForm.css";

const { templatePostForm } = defineProps({
  templatePostForm: {
    type: String,
    default: 'ModelFullForm',
  }
})

const MAX_IMAGE_FILES = 4;
const emit = defineEmits(['beforeSubmit', 'onSuccess']);
const page = usePage();
const minDate = dayjs().startOf("day").format("YYYY/MM/DD HH:mm:ss");
const maxDate = dayjs().startOf("day").add(29, "day").format("YYYY/MM/DD HH:mm:ss");
const templates = {
  BasicForm,
  ModelFullForm,
  AutoPostAIForm
};
const facebookFormModalRef = ref(null);

const pageData = reactive({
  is_show_post_detail_modal: false,
  is_text_to_image_modal: false,
  fanpages: [],
  image_files: [],
  video_files: [],
  image_select_files: [],
  video_select_files: [],
  select_date: new Date(),
  select_time: {
    hours: dayjs().add(1, "hour").hour(),
    minutes: dayjs().minute(),
    seconds: dayjs().second(),
  },
  is_loading: false,
  template_post_form: templatePostForm,
  autoPostAi: null,
  form_id: '',
  form_social_postable_id: '',
  onlyAutoPostAi: false
});

const form = reactive({
  id: '',
  title: '',
  content: '',
  scheduled_publish_time: '',
  social_postable_id: '',
  social_postable_type: SOCIAL_POSTABLE_TYPE.FacebookFanpage,
  published: 1,
  files: [],
});

watch(
  () => pageData.select_time,
  (newValue) => {
    if (!newValue) {
      //Check remove select_time
      //Set default 00:00:00
      pageData.select_time = {
        hours: 0,
        minutes: 0,
        seconds: 0,
      }
    }
  }
);

const openPostDetail = async (postForm = {}) => {
  resetForm();
  Object.assign(form, {
    id: postForm.id ? postForm.id : '',
    title: postForm.title ? postForm.title : '',
    content: postForm.content ? postForm.content : '',
    scheduled_publish_time: postForm.scheduled_publish_time ? postForm.scheduled_publish_time : '',
    social_postable_id: postForm.social_postable_id ? postForm.social_postable_id : form.social_postable_id,
    social_postable_type: postForm.social_postable_type ? postForm.social_postable_type : form.social_postable_type,
    published: postForm.hasOwnProperty('published') ? postForm.published : 1,
    files: [],
  });

  setPageDataForm(form);

  pageData.onlyAutoPostAi = postForm.onlyAutoPostAi ?? false;
  
  if (postForm.scheduled_publish_time) {
    handleScheduledPublishTime(postForm.scheduled_publish_time);
  }

  if (postForm.files) {
    handleFileChange(postForm.files);
  }

  showPostDetailModal(postForm)
};

const setPageDataForm = (form) => {
  pageData.form_id = form.id
  pageData.form_social_postable_id = form.social_postable_id
};


const showPostDetailModal = async (postForm) => {
  let status = await getSocialPostableType();
  if (status) {
    pageData.autoPostAi = postForm?.autoPostAi;
    pageData.is_show_post_detail_modal = true;
  }
};


const handleScheduledPublishTime = (scheduled_publish_time) => {
  const dateObject = new Date(scheduled_publish_time.replace(" ", "T"));
  pageData.select_date = dateObject
  pageData.select_time = {
    hours: dayjs(dateObject).hour(),
    minutes: dayjs(dateObject).minute(),
    seconds: dayjs(dateObject).second(),
  }
};

const handleFileChange = (files) => {
  let images = [];
  let videos = [];
  for (let i = 0; i < files.length; i++) {
    const file = files[i];

    if (file.type.includes('video')) {
      videos.push(file);
    } else {
      images.push(file);
    }
  }
  uploadImage(images)
  uploadVideo(videos)

};

const closePostDetail = () => {
  pageData.is_show_post_detail_modal = false;
};

const openTextToImage = () => {
  pageData.is_text_to_image_modal = true;
};

const closeTextToImage = () => {
  pageData.is_text_to_image_modal = false;
};

const uploadFilePreviewUrls = async (files) => {
  let previewUrls = [];
  const promises = Array.from(files).map(file => {
    return new Promise((resolve) => {
      if (file.s3_url) {
        previewUrls.push({
          'url': file.s3_url,
          'type': file.type,
          'file': file,
        });
        resolve();
      } else {
        const reader = new FileReader();
        reader.onload = () => {
          let objectURL = URL.createObjectURL(file);
          previewUrls.push({
            'url': objectURL,
            'type': file.type,
            'file': file,
          });
          resolve();
        };
        reader.readAsDataURL(file);
      }
    });
  });

  await Promise.all(promises);

  return previewUrls;
};

const resetForm = () => {
  pageData.image_files = [];
  pageData.image_select_files = [];
  pageData.video_files = [];
  pageData.video_select_files = [];
  pageData.select_date = new Date();
  pageData.select_time = {
    hours: dayjs().add(1, "hour").hour(),
    minutes: dayjs().minute(),
    seconds: dayjs().second(),
  }
  pageData.form_id = '';
  pageData.form_social_postable_id = '';
  pageData.onlyAutoPostAi = false
};

const handleImageUpload = async (event) => {
  const files = event.target.files;
  uploadImage(files)
  event.target.value = null;
};

const uploadImage = async (files) => {
  if (pageData.image_files.length + files.length > MAX_IMAGE_FILES) {
    toast.error("Chỉ được phép tải lên tối đa 4 ảnh.");
    return;
  }
  const newFiles = await uploadFilePreviewUrls(files);
  pageData.image_files = [...pageData.image_files, ...newFiles].slice(0, 4);
  newFiles.forEach(selectImage);
};

const removeImage = (index) => {
  const removedImage = pageData.image_files[index];
  pageData.image_select_files = pageData.image_select_files.filter(file => file.url !== removedImage.url);
  pageData.image_files = pageData.image_files.filter((_, i) => i !== index);
};

const selectImage = (file) => {
  const index = pageData.image_select_files.findIndex(i => i.url === file.url);
  if (index !== -1) {
    pageData.image_select_files = pageData.image_select_files.filter((_, i) => i !== index);
  } else {
    pageData.image_select_files = [...pageData.image_select_files, file];
  }
  pageData.video_select_files = [];
};

const checkImage = (item) => {
  let image_select_files = pageData.image_select_files.filter((i) => item.url === i.url);
  return image_select_files.length > 0;
};

const handleVideoUpload = async (event) => {
  const files = event.target.files;
  uploadVideo(files)
  event.target.value = null;
};

const uploadVideo = async (files) => {
  const newFiles = await uploadFilePreviewUrls(files);
  pageData.video_files = [...newFiles];
  newFiles.forEach(selectVideo);
};

const checkVideo = (item) => {
  let video_select_files = pageData.video_select_files.filter((i) => item.url === i.url);
  return video_select_files.length > 0;
};

const selectVideo = (file) => {
  pageData.video_select_files = [file];
  pageData.image_select_files = [];
};

const removeVideo = (index) => {
  pageData.video_files = pageData.video_files.filter((_, i) => i !== index);
};

const onSubmitTextToImage = () => {
  pageData.is_loading = true;
};

const saveGenerationResult = (response) => {
  uploadImage([response.data?.generate_file])
};

const onFinishTextToImage = () => {
  pageData.is_loading = false;
  closeTextToImage();
};

const getFormData = () => {
  const formData = new FormData();
  const files = [
    ...pageData.image_select_files.map((item) => item.file),
    ...pageData.video_select_files.map((item) => item.file),
  ];

  const appendToFormData = (key, value) => formData.append(key, value);

  appendToFormData('id', form.id);
  appendToFormData('title', form.title);
  appendToFormData('content', form.content);
  appendToFormData('social_postable_id', form.social_postable_id);
  appendToFormData('social_postable_type', form.social_postable_type);
  appendToFormData('published', form.published ? 1 : 0);

  if (!form.published && pageData.select_date) {
    const date = new Date(pageData.select_date);
    const formatter = new Intl.DateTimeFormat("en", { year: "numeric", month: "2-digit", day: "2-digit" });
    const [{ value: month }, , { value: day }, , { value: year }] = formatter.formatToParts(date);
    const scheduledPublishTime = `${year}/${month}/${day} ${pageData.select_time?.hours ?? '00'}:${pageData.select_time?.minutes ?? '00'}`;
    appendToFormData('scheduled_publish_time', scheduledPublishTime);
  }

  files.forEach((file, index) => {
    if ('s3_url' in file) {
      appendToFormData(`files[${index}][s3_url]`, file.s3_url);
      appendToFormData(`files[${index}][type]`, file.type);
    } else {
      appendToFormData(`files[${index}]`, file);
    }
  });

  return formData;
};

const activeSocialPostableId = (social) => {
  handleChangeSocialPostableIdUpdateForm(social);
  form.social_postable_id = social.id
}

const handleChangeSocialPostableIdUpdateForm = (social) => {
  if (!pageData.form_id)
    return;

  if (social.id == pageData.form_social_postable_id) {
    form.id = pageData.form_id
    form.published = 1;
    page.props.errors['social_postable_id'] = ''
  } else {
    form.id = ''
    page.props.errors['social_postable_id'] = 'Bài viết sẽ được sao chép sang fanpage mới.'
  }
}

const getSocialPostableType = async () => {
  try {
    const response = await axios.get(route('get-social-postable-type', { social_postable_type: form.social_postable_type }))
    if (form.social_postable_type === SOCIAL_POSTABLE_TYPE.FacebookFanpage) {
      pageData.fanpages = response.data.data
      if (response.data.data.length == 0) {
        toast.error(
          `Vui lòng <a href="/calendar" target="_blank" rel="noopener noreferrer" style="color: blue; text-decoration: underline">thêm fanpages</a> để tiếp tục.`
          , {
            autoClose: 5000,
            dangerouslyHTMLString: true
          });
      }

    }

    if (!form.social_postable_id) {
      activeSocialPostableId(response.data?.data[0])
    }
    return true;
  } catch (error) {
    console.error(error);
    if (error.response?.data?.errors?.message == 'Bạn cần cập nhật thông tin Facebook App trước khi sử dụng chức năng này') {
      facebookFormModalRef.value.openFacebookFormModal();
    }
    return false;
  }
};

const onSuccessFacebookForm = () => {
  console.log('onSuccessFacebookForm');
}

const handleChangeSocialPostableType = (event) => {
  form.social_postable_id = '';
  getSocialPostableType();
}

const handleChangeTemplatePostForm = (templatePostForm = 'BasicForm') => {
  page.props.errors = []
  pageData.template_post_form = templatePostForm;
}

const submit = async () => {
  try {
    emit('beforeSubmit');
    let formData = getFormData();
    pageData.is_loading = true;

    let url = route('social.ajax-store-social-post');
    if (form.id) {
      url = route('social.ajax-update-social-post', { id: form.id });
    }

    await axios.post(url, formData)

    toast.success("Đăng bài thành công");

    emit('onSuccess');
    closePostDetail();
  } catch (error) {
    console.error(error)
    page.props.errors = error.response.data.errors
    toast.error('Đăng bài thất bại!');
  } finally {
    pageData.is_loading = false;
  }
}

watch(
  () => templatePostForm,
  (newTemplate) => {
    handleChangeTemplatePostForm(newTemplate);
  },
  { immediate: true }
);

const formProps = {
  pageData,
  form,
  minDate,
  maxDate,
  MAX_IMAGE_FILES,
  handleChangeSocialPostableType,
  handleImageUpload,
  openTextToImage,
  checkImage,
  removeImage,
  selectImage,
  checkVideo,
  removeVideo,
  selectVideo,
  handleVideoUpload,
  saveGenerationResult,
  closeTextToImage,
  onSubmitTextToImage,
  onFinishTextToImage,
  closePostDetail,
  activeSocialPostableId,
  handleChangeTemplatePostForm,
  submit
};

onMounted(() => {
  // getSocialPostableType();
});

defineExpose({
  openPostDetail,
});
</script>