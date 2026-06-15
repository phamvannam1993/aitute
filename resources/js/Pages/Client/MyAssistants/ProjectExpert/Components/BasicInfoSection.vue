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
        isLoadingCheck: {
            type: Boolean,
            default: false
        },
        projectId: {
            type: Number,
            default: 0
        },
        mode: {
            type: String,
            default: null
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

    const formData = ref({
        title: '',
        description: '',
        productLink: '',
        image_product: {
            url: "",
            file: null,
        },
        model_product: {
            url: "",
            file: null,
        },
        files: [],
        answers: {
            '5.1': 'Đây là lần đầu tiên ra mắt sản phẩm này.',
            '5.2': 'Chưa ai biết đến sản phẩm.',
            '5.3': ['Nhận diện: Khách hàng chưa biết đến sản phẩm, cần nội dung để giới thiệu.'], // Default with first option
            '5.4': 'Họ chưa biết đến sản phẩm nên chưa có định kiến.',
            '5.5': 'Tăng nhận diện thương hiệu (Brand Awareness)'
        }
    });
    const updateBasicInfo = (basicInfo) => {
        formData.value = basicInfo
    }
    // Định nghĩa thứ tự câu hỏi
    const questionOrder = ['5.1', '5.2', '5.3', '5.4', '5.5'];
    const errors = ref({});
    const documentLoading = ref(false);
    const productDetail = ref(null)
    // Định nghĩa câu hỏi và các options
    const questions = {
        '5.1': {
            text: 'Sản phẩm/Dịch vụ này đã từng được ra mắt trước đây chưa?',
            options: [
                'Đây là lần đầu tiên ra mắt sản phẩm này.',
                'Sản phẩm đã được ra mắt trước đây.'
            ]
        },
        '5.2': {
            text: 'Độ nhận diện thương hiệu của sản phẩm hiện nay như thế nào?',
            options: [
                'Chưa ai biết đến sản phẩm.',
                'Một số người đã biết đến sản phẩm.',
                'Nhiều người đã biết đến sản phẩm.'
            ]
        },
        '5.3': {
            text: 'Khách hàng hiện tại đang ở giai đoạn nào trong phễu chuyển đổi?',
            options: [
                'Nhận diện: Khách hàng chưa biết đến sản phẩm, cần nội dung để giới thiệu.',
                'Khách hàng đã nghe nói về sản phẩm, đang tìm hiểu và so sánh.',
                'Khách hàng đã có ý định mua, chỉ cần có ưu đãi hoặc động lực để ra quyết định.',
                'Khách hàng đã sử dụng, cần nội dung chăm sóc và upsell'
            ]
        },
        '5.4': {
            text: 'Hiện tại, khách hàng có suy nghĩ hoặc định kiến gì về sản phẩm không?',
            options: [
                'Họ chưa biết đến sản phẩm nên chưa có định kiến.',
                'Họ có một số định kiến về sản phẩm.',
                'Họ có nhiều định kiến tiêu cực về sản phẩm.'
            ]
        },
        '5.5': {
            text: 'Định hướng tiếp theo của bạn cho SP/DV này là gì?',
            options: [
                'Tăng nhận diện thương hiệu (Brand Awareness)',
                'Tăng tương tác với khách hàng (Engagement)',
                'Tăng doanh số bán hàng (Sales)',
                'Tăng độ trung thành của khách hàng (Loyalty)'
            ]
        }
    };

    const handleQuestionContentChange = (option) => {
        const currentAnswers = formData.value.answers['5.3'];
        const index = currentAnswers.indexOf(option);

        if (index === -1) {
            currentAnswers.push(option);
        } else {
            currentAnswers.splice(index, 1);
        }

        // If all options are unselected, select the first one
        if (currentAnswers.length === 0) {
            formData.value.answers['5.3'] = [questions['5.3'].options[0]];
        }
    };

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

    const goToStepSelectCategory = async  (event) => {
        const editorContent = sunEditorInstance.value.getContents();
        if (editorContent == "") {
            toast.error("Thiết lập thông tin và hiệu chỉnh nội dung trước khi chuyển bước");
            return;
        }
        var content = await extractTagsFromContent(editorContent)
        if(!content) {
            return
        }

        const formData = new FormData()
        formData.append("id", props.projectId)
        formData.append("expert_info", editorContent)
        emit("updateProject", formData)
        emit("submitInfo", content)
        emit("showSection", 1)
    };

    const toggleMainSection = () => {
        mainSectionOpen.value = !mainSectionOpen.value;
    };
    const extractTagsFromContent = async (content) => {
        const formData = new FormData()
        formData.append("content", content)
        try {
            const response = await axios.post(
                route("ai-business.extractTagsFromContent"),
                formData,
                {
                    headers: { "Content-Type": "multipart/form-data" }
                }
            );
            if(response.data.success) {
                return response.data.contents;
            } else {
                toast.error(response.data.message)
                return ""
            }

        } catch(error) {
            toast.error("Cập nhật dự án thất bại")
            return ""
        }
    }

    setInterval(async () => {
        if(loading.value) {
            if(loadingPercent.value < 99) {
                loadingPercent.value =  loadingPercent.value + 1
            }
        } else if(loadingPercent.value < 99 && loadingPercent.value > 0) {
            for(var idx = 1; idx < 10; idx++) {
                loadingPercent.value =  loadingPercent.value + 1
            }
        }
    }, 500)

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

        // If all validations pass
        formData.value.image_product.file = file;
        formData.value.image_product.url = URL.createObjectURL(file);
    };

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
    };

    const toggleActiveFile = (index) => {
        formData.value.files[index].active = !formData.value.files[index].active
    }

    const handleRemoveDocument = (index) => {
        formData.value.files.splice(index, 1)
    }

    const selectAllDocument = () => {
        formData.value.files = formData.value.files.map(file => ({
            ...file,
            active: true
        }))
    }

    const unSelectAllDocument = () => {
        formData.value.files = formData.value.files.map(file => ({
            ...file,
            active: false
        }))
    }

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
                model_product: formData.value.model_product,
                files: files,
                answers: formData.value.answers || {}
            };

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

        // If all validations pass
        formData.value.model_product.file = file;
        formData.value.model_product.url = URL.createObjectURL(file);
    };


    const initEditor = (information_project = "", isSuccess = false) => {
        if(isSuccess) {
            loading.value = false
        }
        // Nếu editor đã được khởi tạo thì không khởi tạo lại
        if (isEditorInitialized.value) {
            sunEditorInstance.value.setContents(information_project);
            return;
        }

        nextTick(() => {
            // Đảm bảo container tồn tại
            const containers = document.querySelectorAll("#editor-container-sub");
            if (containers.length === 0) {
                console.log("Editor container not found");
                return;
            }

            // Nếu có nhiều hơn 1 container, xóa các container thừa
            if (containers.length > 1) {
                for (let i = 1; i < containers.length; i++) {
                    containers[i].remove();
                }
            }

            const container = containers[0];

            // Cleanup editor cũ nếu có
            if (sunEditorInstance.value) {
                sunEditorInstance.value.destroy();
                sunEditorInstance.value = null;
                container.innerHTML = '';
            }

            // Xóa hết nội dung container
            container.innerHTML = '';

            // Tạo textarea mới
            const textarea = document.createElement("textarea");
            container.appendChild(textarea);

            // Khởi tạo SunEditor mới
            sunEditorInstance.value = suneditor.create(textarea, {
                height: "auto",
                minHeight: "300px",
                width: "100%",
                buttonList: [],
                defaultStyle: `
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                font-size: 15px;
                line-height: 1.6;
                color: #333;
                padding: 20px;
            `,
                hideToolbar: true,
            });
            // Set nội dung
            sunEditorInstance.value.setContents(information_project);

            // Đánh dấu đã khởi tạo
            isEditorInitialized.value = true;
        });
    };

    onMounted(() => {
        initEditor(); // Khởi tạo editor

    });

    defineExpose({ initEditor, updateBasicInfo, toggleMainSection });
</script>

<template>
    <div class="bg-white shadow-xl lg:rounded-[35px] md:px-[80px] md:py-[20px] p-3"
         :class="mainSectionOpen ? 'lg:rounded-[35px]' : ''">
        <div :class="mainSectionOpen ? 'flex-col rounded-[35px] items-start' : 'flex-row rounded-full items-center'"
             class="flex justify-between text-black">

            <!-- Header -->
            <div :class="mainSectionOpen ? 'max-w-full' : 'max-w-[300px]'"
                 class="md:max-w-full line-clamp-1 flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
                <Step class="flex-shrink-0" :step="1" stepName="Thông tin sản phẩm" />
            </div>

            <!-- Main Form -->
            <div :class="mainSectionOpen ? 'block w-full' : 'hidden'" >
                <form @submit.prevent="handleSubmit">
                    <!-- Basic Product Info -->
                    <div>
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

                        <div v-show="mode != null">
                            <p class="text-xs md:text-[15px] font-bold mt-4">
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

                    <div class="pb-4 border-b-[3px] border-[#d6d6d6]">
                        <div class="mt-4">
                            <p class="text-xs md:text-[15px] font-bold mt-4">3. Link website/sản phẩm</p>
                            <input
                                id="product-link"
                                v-model="formData.productLink"
                                type="text"
                                placeholder="Link..."
                                class="text-xs md:text-sm w-full rounded-lg lg:rounded-2xl text-black border-[#D4D4D4] mt-1.5 lg:py-[8px] lg:px-[24px]"
                            />
                        </div>

                        <p class="text-xs md:text-[15px] font-bold mt-4">4. Tải tài liệu sản phẩm</p>
                        <div class="lg:grid grid-cols-2 gap-10 mt-6">
                            <!-- Image Upload Section -->
                            <div class="flex flex-col items-center">
                                <label for="image-product" class="flex-shrink-0 cursor-pointer text-xs md:text-[15px] font-bold w-[200px] h-[36px] flex items-center justify-center gap-2 border-2 border-ai3goc-primary text-ai3goc-primary rounded-xl mb-3">
                                    <div class="bg-ai3goc-primary p-1 rounded-full">
                                        <Upload size="16" color="#ffffff" />
                                    </div>
                                    <span>Tải ảnh sản phẩm</span>
                                    <input type="file" class="hidden" id="image-product" @change="uploadImage" />
                                </label>
                                <span class="text-sm text-gray-400 self-start inline-block mb-1 italic">
                    Tải lên 01 hình ảnh đẹp nhất của sản phẩm.
                </span>
                                <div v-if="formData?.image_product?.url" class="flex items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden">
                                    <img v-if="formData.image_product.url"
                                        :src="formData.image_product.url"
                                        alt=""
                                        class="object-cover size-full"
                                    />
                                    <img v-else src="\assets\img\aiwow\image_placeholder.png" alt="" class="size-32 object-contain" />
                                </div>
                            </div>
                            <div class="flex flex-col items-center mt-6 lg:m-0">
                                <label for="image-model" class="flex-shrink-0 cursor-pointer text-xs md:text-[15px] font-bold w-[220px] h-[36px] flex items-center justify-center gap-2 border-2 border-ai3goc-primary text-ai3goc-primary rounded-xl mb-3">
                                    <div class="bg-ai3goc-primary p-1 rounded-full">
                                        <Upload size="16" color="#ffffff" />
                                    </div>
                                    <span>Tải lên ảnh người mẫu</span>
                                    <input type="file" class="hidden" id="image-model" @change="uploadModelImage" />
                                </label>
                                <span class="text-sm text-gray-400 self-start inline-block mb-1 italic">Tải lên 01 hình ảnh người mẫu sử dụng sản phẩm.</span>
                                <div v-if="formData?.model_product?.url" class="flex items-center justify-center bg-gray-200 rounded-lg lg:rounded-2xl aspect-[4/3] w-full h-full overflow-hidden">
                                    <img v-if="formData.model_product.url" :src="formData.model_product.url" alt="" class="object-cover size-full" />
                                    <img v-else src="\assets\img\aiwow\image_placeholder.png" alt="" class="size-32 object-contain" />
                                </div>
                            </div>
                            <!-- Document Upload Section -->
                            <div class="flex flex-col items-center mt-6 lg:m-0">
                                <label
                                    :class="documentLoading ? 'cursor-not-allowed !text-gray-400' : 'cursor-pointer'"
                                    for="file-product"
                                    class="flex-shrink-0 cursor-pointer text-xs md:text-[15px] font-bold w-[200px] h-[36px] flex items-center justify-center gap-2 border-2 border-ai3goc-primary text-ai3goc-primary rounded-xl mb-3"
                                >
                                    <div class="bg-ai3goc-primary p-1 rounded-full">
                                        <Upload size="16" color="#ffffff" />
                                    </div>
                                    <span>
                        {{ documentLoading ? "Đang tải tài liệu..." : "Tải tài liệu sản phẩm" }}
                    </span>
                                    <input
                                        type="file"
                                        class="hidden"
                                        id="file-product"
                                        @change="handleUploadDocument"
                                        accept=".docx, .pdf, .xls, .xlsx, .ppt, .pptx"
                                    />
                                </label>
                                <span class="text-sm text-gray-400 self-start inline-block mb-1 italic">
                    Tải lên file mô tả chi tiết sản phẩm (PDF, Word, Excel, PowerPoint).
                </span>
                                <span class="text-sm text-white self-start inline-block mb-1">.</span>

                                <!-- Document List -->
                                <div v-if="formData.files?.length > 0" class="flex flex-col items-center border-gray-300 border-2 rounded-[20px] aspect-[4/3] h-fit w-full p-4 overflow-hidden">
                                    <div class="flex flex-col gap-3 h-5/6 w-full overflow-y-scroll">
                                        <div
                                            class="flex items-center w-full gap-2"
                                            v-for="(file, index) in formData.files"
                                            :key="index"
                                        >
                                            <div
                                                class="size-6 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all"
                                                :class="file.active ? 'border-orion-orange' : 'border-gray-400'"
                                                @click="toggleActiveFile(index)"
                                            >
                                                <div class="size-[15px] bg-orion-orange rounded-full" v-if="file.active"></div>
                                            </div>
                                            <div class="flex items-center gap-2 rounded-full text-black flex-1 px-[10px] py-[6px] transition-all"
                                                 :class="file.active ? 'bg-orion-orange-hover' : 'bg-gray-100'"
                                            >
                                                <FileText class="size-5 cursor-pointer" />
                                                <span class="font-semibold line-clamp-1">{{ file.name }}</span>
                                                <Trash2
                                                    @click="handleRemoveDocument(index)"
                                                    class="stroke-red-500 ml-auto size-5 cursor-pointer flex-shrink-0"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white flex items-center justify-center gap-[20px] mt-[20px]">
                                        <button
                                            type="button"
                                            class="text-xs md:text-[15px] font-bold w-fit px-3 h-[36px] flex items-center justify-center gap-2 border-2 border-orion-sec text-orion-sec rounded-lg lg:rounded-2xl mb-3"
                                            @click="selectAllDocument"
                                        >
                                            <span>Chọn tất cả</span>
                                        </button>
                                        <button
                                            type="button"
                                            class="text-xs md:text-[15px] font-bold w-fit px-3 h-[36px] flex items-center justify-center gap-2 border-2 border-gray-400 text-gray-400 rounded-lg lg:rounded-2xl mb-3"
                                            @click="unSelectAllDocument"
                                        >
                                            <span>Bỏ chọn</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Info Section -->
                    <div class="mt-4">
                        <p class="text-xs md:text-[15px] font-bold">
                            5. Cung cấp thông tin chuyên sâu
                        </p>
                        <div class="px-5">
                            <!-- Iterate through questions in order -->
                            <template v-for="questionKey in questionOrder" :key="questionKey">
                                <!-- Regular select questions -->
                                <div v-if="questionKey !== '5.3'" class="mb-4">
                                    <p class="text-[10px] md:text-xs font-bold mt-4">
                                        {{ questionKey }}. {{ questions[questionKey].text }}
                                    </p>
                                    <select
                                        v-model="formData.answers[questionKey]"
                                        class="w-full border border-[#D4D4D4] rounded-2xl mt-2"
                                    >
                                        <option v-for="option in questions[questionKey].options"
                                                :key="option"
                                                :value="option">
                                            {{ option }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Special case for question 5.3 (multiple choice) -->
                                <div v-else class="mb-4">
                                    <p class="text-[10px] md:text-xs font-bold mt-4">
                                        5.3 {{ questions['5.3'].text }}
                                        <span class="text-[#999999]">(Có thể chọn nhiều mục nếu cần)</span>
                                    </p>
                                    <div class="space-y-3 mt-2">
                                        <label v-for="option in questions['5.3'].options"
                                               :key="option"
                                               class="flex items-center">
                                            <input type="checkbox"
                                                   :value="option"
                                                   :checked="formData.answers['5.3'].includes(option)"
                                                   @change="handleQuestionContentChange(option)"
                                                   class="form-checkbox h-5 w-5 text-yellow-500 rounded"
                                            />
                                            <span :class="formData.answers['5.3'].includes(option)
                                    ? 'bg-yellow-100 border-yellow-400'
                                    : 'border-gray-300'"
                                                  class="ml-3 text-gray-800 border rounded-2xl p-2 w-full cursor-pointer transition">
                            {{ option }}
                        </span>
                                        </label>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center mt-6">
                        <button type="submit"
                                :disabled="loading"
                                class="h-[40px] md:h-[50px] min-w-[80%] lg:min-w-[360px] text-sm lg:text-base px-4 bg-ai3goc-primary text-white rounded-lg lg:rounded-2xl font-bold">
                            <span v-if="!loading && (loadingPercent > 98 || loadingPercent == 0)">Xác nhận</span>
                            <div v-else class="text-xs md:text-sm font-medium text-center p-0.5 leading-none rounded-full transition-all duration-300">{{ loadingPercent }} %</div>
                        </button>
                    </div>
                </form>

                <div class="mt-8 border-t-2 border-gray-200 pt-8">
                    <div class="py-10">
                        <div class="flex items-center gap-2 mb-6">
                            <div class="flex-shrink-0">
                                <img src="/assets/img/orion-1.png" alt="Settings Icon" class="w-6 h-6" />
                            </div>
                            <h2 class="text-base md:text-lg font-bold text-black">
                                Thiết lập thông tin và hiệu chỉnh
                            </h2>
                        </div>
                        <div class="flex flex-col">
                            <!-- Container cho editor với id cố định -->
                            <div id="editor-container-sub" class="w-full border border-gray-200 rounded-lg mb-4"></div>
                            <div class="flex items-center justify-center gap-5 mt-7">
                                <button
                                    :disabled="loading"
                                    @click="goToStepSelectCategory($event)"
                                    :class="loading ? 'bg-gray-300 cursor-not-allowed' : 'bg-ai3goc-primary'"
                                    type="button"
                                    class="h-[40px] md:h-[50px] place-items-center w-full max-w-[180px] px-4 bg-ai3goc-primary text-white rounded-2xl font-bold"
                                >
                                    <span v-if="!loading && (loadingPercent > 98 || loadingPercent == 0)">Xác nhận</span>
                                    <div v-else class="text-xs md:text-sm font-medium text-center p-0.5 leading-none rounded-full transition-all duration-300">{{ loadingPercent }} %</div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Toggle Button -->
            <div @click="toggleMainSection"
                 :class="open ? 'self-end' : ''"
                 class="flex-shrink-0 text-ai3goc-sec font-medium flex items-center cursor-pointer text-xs md:text-sm">
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
</template>

<style scoped>
    .border-red-500 {
        border-color: rgb(239, 68, 68);
        border-width: 2px;
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
