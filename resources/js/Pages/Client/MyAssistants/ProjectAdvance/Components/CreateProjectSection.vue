<script setup>
    import {ref, defineProps, defineEmits, nextTick, onMounted, watch} from 'vue'
    import Step from '@/Components/Step.vue'
    import LoadingCircle from '@/Components/LoadingCircle.vue'
    import { FileText, Trash2, Upload } from 'lucide-vue-next'
    import suneditor from "suneditor";
    import { toast } from "vue3-toastify";

    const props = defineProps({
        open: {
            type: Boolean,
            default: false
        },
        projectId: {
            type: Number,
            default: 0
        },
        mode: {
            type: String
        },
        projectType: {
            type: String,
            default: ""
        },
    });
    const loading = ref(false);
    const mainSectionOpen = ref(true)
    const loadingPercent = ref(0)
    const selectedProject = ref(null);
    const emit = defineEmits(['toggle', 'submit']);
    const isEditorInitialized = ref(false);
    const sunEditorInstance = ref(null);
    const pendingEditorContent = ref('');
    const existsProductDocument = ref(true);

    const formData = ref({
        title: '',
        description: '',
        productLink: '',
        image_product: {
            url: "",
            file: null,
        },
        image_model: {
            url: "",
            file: null,
        },
        files: [],
    });
    const updateBasicInfo = (basicInfo) => {
        formData.value = basicInfo
    }
    const showImage = ref(false)
    const errors = ref({});
    const validateForm = () => {
        errors.value = {};
        let isValid = true;
        if (!formData.value.title.trim()) {
            errors.value.title = 'Vui lòng nhập tên sản phẩm';
            isValid = false;
        }
        if (!formData.value.description.trim()) {
            errors.value.description = 'Vui lòng nhập mô tả sản phẩm';
            isValid = false;
        }
        return isValid;
    };
    // Function to get image dimensions
    const getImageDimensions = (file) => {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => {
                resolve({
                    width: img.width,
                    height: img.height
                });
            };
            img.onerror = reject;
            img.src = URL.createObjectURL(file);
        });
    };
    const documentLoading = ref(false)
    const toggleMainSection = () => {
        mainSectionOpen.value = !mainSectionOpen.value;
    };
    setInterval(async () => {
        if(loading.value) {
            if(loadingPercent.value < 99) {
                loadingPercent.value =  loadingPercent.value + 1
            }
        } else if(loadingPercent.value < 99 && loadingPercent.value > 0) {
            for(var idx = 1; idx < 10; idx++) {
                loadingPercent.value =  loadingPercent.value + 1
            }
            if(loadingPercent.value >= 99) {
                mainSectionOpen.value = false
            }
        }
    }, 300)

    const updateIsLoading = () => {
        loading.value = false
        mainSectionOpen.value = false
    }

    const redirectHistory = () => {
        location.href = route('product-image-history')
    }

    const uploadImage = async (e) => {
        const file = e.target.files[0];
        if (!file) return;
        // Validate file type
        const validImageTypes = ["image/jpeg", "image/png", "image/jpg", "image/gif"];
        if (!validImageTypes.includes(file.type)) {
            toast.error("File phải là hình ảnh có định dạng: jpeg, png, jpg, gif");
            e.target.value = "";
            return;
        }
        // Validate file size (max 2MB)
        const maxSize = 10 * 1024 * 1024; // 2MB in bytes
        if (file.size > maxSize) {
            toast.error("Kích thước hình ảnh tối đa là 2MB");
            e.target.value = "";
            return;
        }
        // Validate image dimensions (optional)
        try {
            const dimensions = await getImageDimensions(file);
        } catch (error) {
            toast.error("Không thể đọc file ảnh");
            e.target.value = "";
            return;
        }
        const imageUrl = await getS3URL(file);
        if(imageUrl) {
            formData.value.image_product.url = imageUrl;
        } else {
            toast.error('Có lỗi xảy ra trong quá trình upload')
        }
    };

    const getS3URL = async (file, type = 1) => {
        const formData = new FormData();
        formData.append("image_file", file);
        var url = route("ai-business.product-image-upload")
        if(type == 1) {
            url = route("short-video.uploadImageToS3")
        }
        try {
            const response = await axios.post(url, formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
            if (response.data.success) {
                return response.data.s3_url
            } else {
                return ""
            }
        } catch (err) {
            //return ""
        }
    }

    const toggleActiveFile = (index) => {
        formData.value.files[index].active = !formData.value.files[index].active;
    }
    const uploadDoc = ref(null)
    const handleUploadDocument = async (e) => {
        const file = e.target.files[0];
        //only accept pdf, doc, docx, xls, xlsx, ppt, pptx, jpg, jpeg, png, gif
        // const allowedExtensions = /(\.pdf|\.doc|\.docx|\.xls|\.xlsx|\.ppt|\.pptx|\.jpg|\.jpeg|\.png|\.gif)$/i;
        // Chỉ cho phép các file văn bản
        const allowedMimeTypes = [
            // Text documents
            "text/plain", // .txt
            // Microsoft Word
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document", // .docx
            // PDF
            "application/pdf", // .pdf
        ];
        if (!allowedMimeTypes.includes(file.type)) {
            toast.error("Chỉ chấp nhận các định dạng tài liệu: TXT, DOCX, PDF");
            return;
        }
        if (!file) return;
        try {
            documentLoading.value = true;
            const formDataApi = new FormData();
            formDataApi.append("file", file);
            const response = await axios.post(route("ai-business.upload-document"), formDataApi, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
            if (response.data) {
                const data = response.data.data;
                const url = data.url;
                const name = data.name;
                const type = data.type;
                const size = data.size;
                const path = data.path;
                const file = {
                    url: url,
                    name: name,
                    type: type,
                    size: size,
                    active: false,
                    path: path,
                };
                formData.value.files.push(file);
            }
        } catch (error) {
            console.error("Lỗi khi upload tài liệu:", error.response?.data || error.message);
            toast.error("Upload tài liệu thất bại!");
        } finally {
            documentLoading.value = false;
        }
        uploadDoc.value.value = ""
    };
    const handleRemoveDocument = (index) => {
        formData.value.files.splice(index, 1);
    };
    const selectALlDocument = (e) => {
        formData.value.files.forEach((item) => {
            item.active = true;
        });
    };
    const unSelectALlDocument = (e) => {
        formData.value.files.forEach((item) => {
            item.active = false;
        });
    };
    const handleSubmit = () => {
        if (validateForm()) {
            console.log("formData BasicInfoSection", formData.value)
            // Parse thành plain object trước khi emit
            const files = [
                ...formData.value.files
                    .filter((item) => item.active)
                    .map((item) => {
                        return {
                            type: "document",
                            transfer_method: "remote_url",
                            url: item.url,
                            name:item.name,
                            active:item.active
                        };
                    }),
            ];

            const data = {
                title: formData.value.title,
                description: formData.value.description,
                productLink: formData.value.productLink,
                image_product: formData.value.image_product,
                image_model: formData.value.image_model,
                files: files
            };
            console.log(data)
            loading.value = true
            loadingPercent.value = 0
            emit('submit', data, 'step1');
        }
    };
    //Upload ảnh phần mới
    const uploadModelImage = async (e) => {
        const file = e.target.files[0];
        console.log(file);
        if (!file) return;
        // Validate file type
        const validImageTypes = ["image/jpeg", "image/png", "image/jpg", "image/gif"];
        if (!validImageTypes.includes(file.type)) {
            toast.error("File phải là hình ảnh có định dạng: jpeg, png, jpg, gif");
            e.target.value = "";
            return;
        }
        // Validate file size (max 10MB)
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if (file.size > maxSize) {
            toast.error("Kích thước hình ảnh tối đa là 10MB");
            e.target.value = "";
            return;
        }
        // Validate image dimensions (optional)
        try {
            const dimensions = await getImageDimensions(file);
        } catch (error) {
            toast.error("Không thể đọc file ảnh");
            e.target.value = "";
            return;
        }
        const imageUrl = await getS3URL(file, 1);
        if(imageUrl) {
            formData.value.image_model.url = imageUrl
        } else {
            toast.error('Có lỗi xảy ra trong quá trình upload')
        }

    };
    const nextPage = ref(null)
    const images = ref([])
    const typeImage = ref(1)
    const imageSelect = ref(null)
    const isShowOption = ref(false)

    const selectedImage = (imageUrl) => {
        imageSelect.value = imageUrl
    }
    const confirmImage = () => {
        if(typeImage.value != 1) {
            formData.value.image_product.url = imageSelect.value;
        } else {
            formData.value.image_model.url = imageSelect.value;
        }
        showImage.value = false
    }
    const showImageList = async (type = 2, isLoadMore = false) => {
        if(isLoadMore) {
            const response = await axios.get(nextPage.value, {});
            const dataImage = response.data.data.data
            for(var i = 0; i < dataImage.length; i++) {
                images.value.push(dataImage[i])
            }
            nextPage.value = response.data.data.next_page_url
            return
        }
        var url = route("ai-image.api-ai-image-history")
        nextPage.value = ""
        if(type == 2) {
            url = route("ai-business.product-image-list")
        }
        if(type == 3) {
            url = route("ai-background.api-history")
        }
        const response = await axios.get(url, {});
        images.value = response.data.data.data
        nextPage.value = response.data.data.next_page_url
        showImage.value = true
        typeImage.value = type
        isShowOption.value = false
    }

    const loadMore = async () => {
        if(nextPage.value) {
            showImageList(0, nextPage.value)
        }
    }

    const closePopup = () => {
        showImage.value = false
    }

    const toggleExistsProductDocument = () => {
        existsProductDocument.value = !existsProductDocument.value
    }
    defineExpose({ updateBasicInfo, updateIsLoading });
</script>
<template>
    <div class="bg-white shadow-xl lg:rounded-[35px] md:px-[80px] md:py-[20px] p-3"
         :class="mainSectionOpen ? 'lg:rounded-[35px]' : ''">
        <div :class="mainSectionOpen ? 'flex-col rounded-[35px] items-start' : 'flex-row rounded-full items-center'"
             class="flex justify-between text-black">
            <!-- Header -->
            <div :class="mainSectionOpen ? 'max-w-full' : 'max-w-[300px]'"
                 class="md:max-w-full line-clamp-1 flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
                <Step class="flex-shrink-0" :step="1" stepName="Tạo dự án" />
            </div>
            <!-- Main Form -->
            <div :class="mainSectionOpen ? 'block w-full' : 'hidden'">
                <form @submit.prevent="handleSubmit">
                    <!-- Basic Product Info -->
                     <div class="mt-5">
                        <p class="text-xs md:text-[15px] font-bold">
                            1. Nhập tên sản phẩm <span class="text-red-500">*</span>
                        </p>
                        <input
                            v-model="formData.title"
                            type="text"
                            placeholder="Nhập tên sản phẩm...."
                            :class="{'border-red-500': errors.title}"
                            class="text-xs md:text-sm w-full rounded-lg lg:rounded-2xl border-[#D4D4D4] mt-1.5 lg:py-[22px] lg:px-7"
                        />
                        <span v-if="errors.title" class="text-red-500 text-xs mt-1">{{ errors.title }}</span>

                        <div>
                            <p class="text-xs md:text-[17px] font-bold mt-4">
                                2. Mô tả ngắn ngọn về sản phẩm <span class="text-red-500">*</span>
                            </p>
                            <textarea
                                v-model="formData.description"
                                placeholder="Mô tả các tính năng và lợi ích của sản phẩm..."
                                rows="4"
                                :class="{'border-red-500': errors.description}"
                                class="resize-none mt-1.5 text-xs md:text-sm w-full rounded-lg lg:rounded-2xl border-[#D4D4D4] lg:py-[22px] lg:px-7"
                            ></textarea>
                            <span v-if="errors.description" class="text-red-500 text-xs mt-1">{{ errors.description }}</span>
                        </div>
                        </div>
                        <div class="">
                            <!-- Tạm ẩn -->

                            <div class="mt-4">
                                <p class="text-xs md:text-[17px] font-bold mt-4">3. Link website/sản phẩm</p>
                                <input id="product-link" v-model="formData.productLink" type="text" placeholder="Link..." class="text-xs md:text-sm w-full rounded-lg lg:rounded-2xl text-black border-[#D4D4D4] mt-1.5 lg:py-[8px] lg:px-[24px]" />
                            </div>
                            <p class="text-xs md:text-[17px] font-bold mt-4">4. Tải tài liệu sản phẩm</p>
                            <div v-if="existsProductDocument" class="pb-4 border-b-[3px] border-[#d6d6d6]">
                                <div class="text-business-primary mt-4 px-5">
                                    <span class="text-sm text-gray-400 self-start inline-block mb-1 italic">Tải hình ảnh dựa trên 2 tiêu chí</span>
                                </div>
                                <div class="lg:grid grid-cols-2 gap-10 mt-6">
                                    <div class="flex flex-col items-center p-4 border-2 border-gray-300 rounded-3xl">
                                        <div class="w-full flex justify-between">
                                            <p class="text-xs md:text-[15px] font-bold leading-6">Tiêu chí 1: Ảnh sản phẩm</p>
                                            <div  class="rounded-lg justify-center flex items-center ">
                                                <label for="image-product" class="flex items-center rounded-lg justify-center bg-orion-orange cursor-pointer text-xs md:text-[15px] w-[26px] h-[26px] text-aiwow-sec">
                                                    <img src="/assets/svgs/upload_white.svg" alt="">
                                                    <input type="file" class="hidden" id="image-product" @change="uploadImage" />
                                                </label>
                                                <span class="flex items-center rounded-lg justify-center bg-orion-orange cursor-pointer text-xs md:text-[15px] w-[80px] h-[26px] text-white ml-2" @click="showImageList(2)">
                                                    Kho ảnh
                                                </span>
                                            </div>

                                        </div>
                                        <span class="text-sm text-gray-400 self-start inline-block mb-1 italic"></span>
                                        <div class="flex items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden">
                                            <img v-if="formData.image_product.url" :src="formData.image_product.url" alt="" class="object-cover max-h-[500px]" />
                                            <img v-else src="\assets\img\aiwow\image_placeholder.png" alt="" class="size-32 object-contain" />
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center mt-6 lg:m-0 p-4 border-2 border-gray-300 rounded-3xl">
                                        <div class="w-full flex justify-between">
                                            <p class="text-xs md:text-[15px] font-bold leading-6">Tiêu chí 2: Ảnh người mẫu</p>
                                            <div  class="rounded-lg justify-center flex items-center ">
                                                <label for="image-model" class="flex items-center rounded-lg justify-center bg-orion-orange cursor-pointer text-xs md:text-[15px] w-[26px] h-[26px] text-aiwow-sec">
                                                    <img src="/assets/svgs/upload_white.svg" alt="">
                                                    <input type="file" class="hidden" id="image-model" @change="uploadModelImage" />
                                                </label>
                                                <span class="flex items-center rounded-lg justify-center bg-orion-orange cursor-pointer text-xs md:text-[15px] w-[80px] h-[26px] text-white ml-2" @click="showImageList(1)">
                                                    Kho ảnh
                                                </span>
                                            </div>
                                        </div>

                                        <span class="text-sm text-gray-400 self-start inline-block mb-1 italic">Tải ảnh người mẫu hoặc chọn ảnh tại kho ảnh avatar.</span>
                                        <div v-if="formData?.image_model" class="flex items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden">
                                            <img v-if="formData.image_model.url" :src="formData.image_model.url" alt="" class="object-cover max-h-[500px]" />
                                            <img v-else src="\assets\img\aiwow\image_placeholder.png" alt="" class="size-32 object-contain" />
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center mt-6 lg:m-0">
                                        <div class="w-full flex justify-between">
                                            <p class="text-xs md:text-[15px] text-business-primary font-bold leading-6">Tải tài liệu sản phẩm:</p>
                                            <label :class="documentLoading ? 'cursor-not-allowed !text-gray-400' : 'cursor-pointer'" for="file-product" class="flex items-center rounded-lg justify-center bg-orion-orange cursor-pointer text-xs md:text-[15px] w-[26px] h-[26px]">
                                                <img src="/assets/svgs/upload_white.svg" alt="">
                                                <input ref="uploadDoc" type="file" class="hidden" id="file-product" @change="handleUploadDocument" accept=".docx, .pdf, .xls, .xlsx, .ppt, .pptx" />
                                            </label>
                                        </div>

                                        <span class="text-sm text-gray-400 self-start inline-block mb-1 italic"> Tải lên file mô tả chi tiết sản phẩm (PDF, Word, Excel, PowerPoint). </span>
                                        <span class="text-sm text-white self-start inline-block mb-1">.</span>
                                        <div v-if="formData?.files?.length > 0" class="flex flex-col items-center border-gray-300 border-2 rounded-[20px] h-fit w-full p-4 overflow-hidden">
                                            <div class="flex flex-col gap-3 h-5/6 w-full overflow-y-scroll">
                                                <div class="flex items-center w-full gap-2" v-for="(file, index) in formData.files" :key="index">
                                                    <div class="size-6 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all" :class="file.active ? 'border-orion-orange' : 'border-gray-400'" @click="toggleActiveFile(index)">
                                                        <div class="size-[15px] bg-orion-orange rounded-full" v-if="file.active"></div>
                                                    </div>
                                                    <div class="flex items-center gap-2 rounded-full text-black flex-1 px-[10px] py-[6px] transition-all" :class="file.active ? 'bg-orion-orange-hover' : 'bg-gray-100'">
                                                        <FileText class="size-5 cursor-pointer" />
                                                        <span class="font-semibold line-clamp-1">{{ file.name }}</span>
                                                        <Trash2 @click="handleRemoveDocument(index)" class="stroke-red-500 ml-auto size-5 cursor-pointer flex-shrink-0" />
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="bg-white flex items-center justify-center gap-[20px] mt-[20px]">
                                                <button type="button" class="text-xs md:text-[15px] font-bold w-fit px-3 h-[36px] flex items-center justify-center gap-2 border-2 border-business-primary text-business-primary rounded-lg lg:rounded-2xl mb-3" @click="selectALlDocument">
                                                    <span>Chọn tất cả</span>
                                                </button>
                                                <button type="button" class="text-xs md:text-[15px] font-bold w-fit px-3 h-[36px] flex items-center justify-center gap-2 border-2 border-gray-400 text-gray-400 rounded-lg lg:rounded-2xl mb-3" @click="unSelectALlDocument">
                                                    <span>Bỏ chọn</span>
                                                </button>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center gap-4 mt-5 mb-4">
                            <button type="submit" class="h-[40px] md:h-[50px] min-w-[80%] lg:min-w-[170px] text-sm lg:text-base px-4 bg-ai3goc-bgclr text-white rounded-lg lg:rounded-2xl font-bold" :disabled="loading">
                                <div role="status" v-if="loading">
                                    <svg aria-hidden="true" class="inline w-6 h-6 text-gray-200 animate-spin dark:text-gray-600 fill-gray-600 dark:fill-gray-300" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                                    </svg>
                                    <span class="ml-2" v-if="loadingPercent < 98 && loadingPercent > 0">{{ loadingPercent }} %</span>
                                </div>
                                <span v-else >Xác nhận</span>
                            </button>
                        </div>
                </form>
            </div>
            <!-- Toggle Button -->
            <div @click="toggleMainSection"
                 :class="open ? 'self-end' : ''"
                 class="flex-shrink-0 text-business-sec font-medium flex items-center cursor-pointer text-xs md:text-sm">
                <span class="hidden md:block">{{ mainSectionOpen ? "Thu gọn" : "Hiển thị" }}</span>
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5 ml-1 transform"
                     :class="{ 'rotate-180': open }"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
        <LoadingCircle v-if="isLoadingCheck" />
        <div v-if="!open && props.selectedProject && !isLoadingCheck"
             class="bg-orion-orange-hover rounded-full p-2 mx-auto mb-2">
            <div class="flex items-center">
                <img src="/assets/img/my_assistant/p_red.png"
                     alt="Project Icon"
                     class="lg:w-12 w-8 lg:h-12 h-8 rounded-full mr-4" />
                <div>
                    <h2 class="lg:text-2xl text-base font-semibold text-black line-clamp-1">
                        {{ props.selectedProject?.title }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" v-if="showImage">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[700px] xl:w-[1000px] rounded-[40px] relative" >
            <div class="">
                <div class="flex flex-col lg:flex-row md:justify-end md:items-end lg:items-center mt-4 flex-wrap lg:flex-nowrap lg:justify-between">
                    <h2 class="text-black font-bold text-2xl mb-4"> Kho ảnh {{typeImage != 1 ? 'sản phẩm' : 'avatar'}} </h2>
                    <img src="/assets/img/close2.png" class="cursor-pointer absolute top-2 right-5" @click="closePopup" />
                    <label v-if="typeImage != 1" class="relative h-[40px] mb-5 cursor-pointer flex flex-row text-sm lg:text-base px-4 rounded-lg lg:rounded-2xl font-bold line-h-40">
                        <button @click="isShowOption = !isShowOption" class="w-full h-ful border-[#d6d6d6] px-4 border-2  font-bold py-2.5 text-xs text-black lg:text-sm rounded-2xl lg:rounded-2xl text-center flex items-center gap-2 justify-center">
                             {{typeImage == 2 ? 'Ảnh sản phẩm đã tải' : 'Ảnh bối cảnh'}}
                             <svg data-v-8e5f379b="" class="rotate-0" width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path data-v-8e5f379b="" d="M1 1L5 5L9 1" stroke="#C5C5C5"></path></svg>
                        </button>
                        <div class="absolute z-10 right-0 w-52 bg-white shadow-xl rounded-2xl p-2 py-4 space-y-2 mt-10" v-if="isShowOption">
                            <div @click="showImageList(2)" class="flex items-center cursor-pointer text-xs md:text-sm px-4 py-2 rounded-md text-black">
                                <p>Ảnh sản phẩm đã tải</p>
                            </div>
                            <div @click="showImageList(3)" class="flex items-center cursor-pointer text-xs md:text-sm px-4 py-2 rounded-md text-black">
                                <p>Ảnh bối cảnh</p>
                            </div>
                        </div>
                    </label>
                    <label v-if="typeImage != 1" @click="redirectHistory()" for="upload-image" class="h-[40px] mb-5 cursor-pointer flex flex-row text-sm lg:text-base px-4 bg-business-sec text-white rounded-lg lg:rounded-2xl font-bold line-h-40">
                        <svg class="mt-3" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.2222 1H2.77778C1.79594 1 1 1.79594 1 2.77778V15.2222C1 16.2041 1.79594 17 2.77778 17H15.2222C16.2041 17 17 16.2041 17 15.2222V2.77778C17 1.79594 16.2041 1 15.2222 1Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5.889 7.22233C6.62538 7.22233 7.22233 6.62538 7.22233 5.889C7.22233 5.15262 6.62538 4.55566 5.889 4.55566C5.15262 4.55566 4.55566 5.15262 4.55566 5.889C4.55566 6.62538 5.15262 7.22233 5.889 7.22233Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.0001 11.6666L12.5556 7.22217L2.77783 16.9999" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input id="upload-image" type="file" class="hidden">
                        <span class="ml-2">Quản lý kho ảnh sản phẩm</span>
                    </label>
                </div>
                 <div class="flex w-full border-b-[3px] border-[#d6d6d6] mb-5 px-5"></div>
            </div>

            <div class="relative">
                <div class="w-fit overflow-y-auto h-[60vh] lg:h-[70vh] mb-2 relative text-center">
                    <div class="grid grid-cols-3 lg:grid-cols-5 gap-2 lg:gap-5 w-full">
                        <div @click="selectedImage(image.s3_url)" v-for="image in images" :key="image.id" class="relative rounded-xl cursor-pointer">
                            <img :src="image.s3_url" :alt="image.id" class="w-full h-full rounded-lg object-cover" />
                            <input type="radio" :value="image.s3_url" :checked="imageSelect == image.s3_url ? true : false" class="absolute top-2 checked:bg-orion-orange hover:checked:bg-orion-orange checked:text-orion-orange checked:ring-orion-orange lg:top-4 right-2 lg:right-4 cursor-pointer outline-none size-6" />
                        </div>
                    </div>
                    <button v-if="nextPage" @click="loadMore" class="h-[40px] md:h-[50px] min-w-[80%] lg:min-w-[170px] text-sm lg:text-base px-4 bg-business-primary text-white rounded-lg lg:rounded-2xl font-bold mt-5">Xem thêm</button>
                </div>
            </div>
            <div class="flex items-center gap-4 md:gap-8 justify-center mt-6">
                <button @click="confirmImage" class="h-[40px] md:h-[50px] min-w-[80%] lg:min-w-[170px] text-sm lg:text-base px-4 bg-business-primary text-white rounded-lg lg:rounded-2xl font-bold">
                   Xác nhận
                </button>
            </div>
        </div>
    </div>
</template>
<style scoped>
    .border-red-500 {
        border-color: rgb(239, 68, 68);
        border-width: 2px;
    }
    .line-h-40 {
       line-height: 40px;
    }

    .form-checkbox {
        @apply rounded;
        @apply border-gray-300;
        @apply text-yellow-500;
        @apply focus:ring-yellow-500;
    }
    .form-checkbox:checked {
        @apply bg-yellow-500;
        @apply border-yellow-500;
    }
</style>
