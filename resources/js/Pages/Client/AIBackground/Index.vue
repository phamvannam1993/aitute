<template>
    <Head title="Tạo hình bối cảnh - AI 3 GỐC - Cộng đồng AI tử tế" />

    <Layout :breadcrumbs="breadcrumbsData" :credits="credits">
        <body class="flex lg:flex-row flex-col gap-4">
            <!-- List Background -->
            <section class="w-full flex flex-col lg:w-5/12 bg-white p-3 lg:p-4 2xl:p-6 rounded-3xl text-black drop-shadow-xl">
                <div class="flex flex-col mb-2">
                    <div class="flex gap-1">
                        <div class="flex justify-start items-center gap-2">
                            <div class="w-[52px] h-[75%] overflow-hidden rounded-[10px] border-[1px] border-[#d4d4d4]">
                                <img class="w-full h-auto object-top" src="/assets/img/orion/big-robot.png" alt="Robot" />
                            </div>

                            <h2 class="text-black font-bold text-xl lg:text-xl 2xl:text-3xl leading-none">
                                Tạo hình bối cảnh
                            </h2>
                        </div>
                    </div>
                    <p class="text-orion-primary text-sm md:text-base font-bold">Thực hiện theo các bước sau:</p>
                </div>
                <div class="flex justify-between font-semibold mt-4">
                    <Step :step="1" stepName="Bấm vào đây để tải ảnh" forName="image-description" />
                    <button type="button" @click="handleUploadClick" class="w-[28%] xs:w-1/4 xl:w-1/3 justify-self-end flex flex-nowrap items-center font-bold px-1 py-1.5 justify-center gap-2 bg-transparent text-orion-sec rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-orion-sec">
                        <img src="/assets/img/orion/icon/upload.svg" class="size-6 md:size-5 xl:size-6" />
                        <span class="text-[12px] md:hidden xl:block xl:text-[14px] text-nowrap">Tải ảnh</span>
                    </button>
                </div>
                <Step :step="2" stepName="Chọn mẫu bối cảnh phù hợp" forName="image-description" />
                <div class="w-fit grid grid-cols-2 gap-2 overflow-y-scroll h-[40vh] lg:h-[55vh] my-2">
                    <div @click="handleSelectTheme(theme)" v-for="theme in themes" :key="theme" :class="themeActive.label === theme.label ? 'border-orion-sec' : 'border-transparent'" class="relative border-[4px] rounded-xl hover:border-orion-sec cursor-pointer">
                        <img :src="theme.thumbnail" alt="" class="w-full h-full rounded-lg" />
                        <h2 class="absolute text-center bottom-0 left-0 right-0 text-base lg:text-xl pt-4 z-10 text-white">{{ theme.label }}</h2>
                        <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-black to-transparent rounded-lg"></div>
                    </div>
                </div>
                <div class="grid grid-cols-2 mt-4">
                    <Step :step="3" stepName="Bấm vào đây" forName="image-description" />
                    <button type="button" :disabled="isLoading" @click="generateAiBackground" class="font-bold flex items-center w-full justify-center gap-2 px-3 py-2 bg-orion-sec text-white rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-orion-sec">
                        <span class="text-[12px] md:hidden xl:block xl:text-[14px]">Tạo bối cảnh</span>
                    </button>
                </div>
            </section>

            <section ref="resultBox" class="w-full lg:w-7/12 bg-white rounded-3xl text-black drop-shadow-xl p-3 lg:p-4 2xl:p-6">
                <p class="text-orion-sec italic font-thin text-center">Hiển thị kết quả</p>
                <div class=" flex flex-col gap-3">
                    <div class="flex flex-col lg:flex-row justify-between gap-4">
                        <div v-if="isCropping" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                            <div class="bg-white p-4 rounded-md w-full md:w-[80%] lg:w-[40%]">
                                <VueCropper ref="cropper" :src="imageUrl" :aspect-ratio="aspectRatio" :zoomable="true" class="max-w-full max-h-[60vh] mx-auto overflow-hidden" />
                                <div class="flex justify-between mt-4">
                                    <button @click="cancelCropping" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">Hủy</button>
                                    <button @click="cropImage" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Cắt ảnh</button>
                                </div>
                            </div>
                        </div>
                        <input type="file" ref="fileInput" accept="image/*" @change="handleFileChange" class="hidden" />
                    </div>

                    <div class="h-full">
                        <div class="bg-[#E6E6E6] flex items-center justify-center h-[50vh] rounded-lg">
                            <img @click="openDetail(imageGenerate)" v-if="imageGenerate?.s3_url || imageUrl" :src="imageGenerate?.s3_url || imageUrl" alt="uploaded image" class="mx-auto max-w-full max-h-full rounded-lg cursor-pointer" />
                            <img v-else src="/assets/svgs/aiwow/image.svg" alt="placeholder" class="mx-auto w-24" />
                        </div>
                    </div>
                    <TaskBar :isActive="imageGenerate" :selectedImage="imageGenerate" :shareLinkableType="'PebblelyVideo'" :features="['video']" />
                    <div class="flex justify-center">
                        <a :href="route('ai-background.history')" class="font-bold bg-orion-primary px-12 py-1.5 text-white rounded-xl w-full text-center lg:w-fit">Lịch sử</a>
                    </div>
                </div>

                <div class="p-2 flex flex-col gap-2">
                    <p class="font-light">Ảnh tạo gần đây</p>
                    <div ref="scrollContainer" class="flex gap-4 overflow-auto" style="scroll-behavior: smooth">
                        <button v-if="isLoading" class="h-32 w-32 bg-gray-300 rounded-md flex-shrink-0">
                            <div class="flex flex-col items-center">
                                <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                            </div>
                        </button>
                        <div v-for="(item, index) in listHistory" :key="index" class="h-32 w-32 bg-gray-300 rounded-md flex-shrink-0 cursor-pointer">
                            <img @click="openDetail(item)" :src="item.s3_url" alt="Generated Background" class="mx-auto w-full h-full rounded-lg" />
                        </div>
                    </div>
                </div>
            </section>
        </body>
    </Layout>

    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel" :currentCredit="pageProps.auth.user.credit || 0" :additionalCredit="additionalCredit" />
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade" />
</template>
<script setup>
import { computed, onMounted, ref, watch, onUnmounted, nextTick } from "vue";
import Layout from "@/Layouts/Client/Layout.vue";
import { usePage } from "@inertiajs/vue3";
import CreditBuyModal from "../../../Components/ModalBuyCredit/BuyCredit.vue";
import TaskBar from '@/Components/TaskBar.vue'

import VueCropper from "vue-cropperjs";
import "cropperjs/dist/cropper.css";
import PopupAff from "@/Components/PopupAff.vue";
import Step from "@/Components/Step/Index.vue";

const props = defineProps({
    history: Array,
    imageUrl: String,
});

const breadcrumbsData = [
    { text: "Tạo sản phẩm MEDIA", link: "ai-background.history" },
    { text: "Hình phối cảnh", link: "ai-background.indexV2" },
];

const { props: pageProps } = usePage();
const additionalCredit = ref(0);
const showAffKeyPopup = ref(false);
const popupMessage = ref("Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!");
const acceptUpgrade = ref(true);

// Handle Action
const isLoading = ref(false);

const selectedImage = ref(null);
const showConfirmModal = ref(false);
const imageDeleteId = ref(null);
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const isLoadingDelete = ref(false);

// Props
const listHistory = ref(props.history.data || []);
const imageGenerate = ref(null);
const credits = ref(0);

// Image
let allowedExtension = ["image/jpeg", "image/jpg", "image/png"];
const imageFile = ref(null);
const imageUrl = ref(props.imageUrl || null);
const fileInput = ref(null);
const isTransparentImage = ref(null);
const resultBox = ref(null);

const handleUploadClick = () => {
    fileInput.value?.click();
};

const scrollContainer = ref(null);

const handleWheel = (event) => {
    if (scrollContainer.value) {
        event.preventDefault();
        scrollContainer.value.scrollLeft += event.deltaY;
    }
};

onMounted(() => {
    if (scrollContainer.value) {
        scrollContainer.value.addEventListener("wheel", handleWheel);
    }
});

onUnmounted(() => {
    if (scrollContainer.value) {
        scrollContainer.value.removeEventListener("wheel", handleWheel);
    }
});

const handleFileChange = async (event) => {
    const file = event.target.files[0].type;
    if (allowedExtension.indexOf(file) < 0) {
        alert("Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg");
        return false;
    }
    if (file) {
        
        isCropping.value = true;
        imageGenerate.value = null;
        imageFile.value = event.target.files[0];
        imageUrl.value = URL.createObjectURL(event.target.files[0]);
        checkImageTransparency(imageUrl.value, function (isTransparent) {
            isTransparentImage.value = isTransparent;
        });
        await nextTick(() => {
            resultBox.value.scrollIntoView({ behavior: "smooth" });
        });
    }
};

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

const showBuyCreditPopup = ref(false);

const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};

const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};

const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append("model", "pebblely");
        formData.append("type", "pebblely");
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

const croppedImageUrl = ref(null);
const cropper = ref(null);
const isCropping = ref(false);

const aspectRatio = computed(() => {
    const resolution = "1:1";
    const [width, height] = resolution.split(":");
    return parseFloat(width) / parseFloat(height);
});

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
            imageFile.value = file;
            isCropping.value = false;
        };
    }
};

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
        imageFile.value = file;
        isCropping.value = false;
    }
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

const themes = ref([
    {
        label: "Ngẫu nhiên",
        thumbnail: "https://pebblely.b-cdn.net/samples/surprise-me.png",
        originalLabel: "Surprise me",
    },
    {
        label: "Studio chuyên nghiệp",
        thumbnail: "https://pebblely.b-cdn.net/samples/pebblely%20-%202023-03-17T164517.910.jpg",
        originalLabel: "Studio",
    },
    {
        label: "Vải lụa sang trọng",
        thumbnail: "https://pebblely.b-cdn.net/samples/pebblely%20-%202023-03-18T161959.621.jpg",
        originalLabel: "Silk",
    },
    {
        label: "Quán cà phê ấm cúng",
        thumbnail: "https://pebblely.b-cdn.net/samples/sample_20221226_015008_22.jpg",
        originalLabel: "Cafe",
    },
    {
        label: "Bàn ăn hiện đại",
        thumbnail: "https://pebblely.b-cdn.net/samples/pebblely%20-%202023-03-17T160622.039.jpg",
        originalLabel: "Tabletop",
    },
    {
        label: "Gỗ sáng tự nhiên",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-light-wood.jpg",
        originalLabel: "Light Wood",
    },
    {
        label: "Gỗ tối cổ điển",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-dark-wood.jpg",
        originalLabel: "Dark Wood",
    },
    {
        label: "Đá cẩm thạch",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-marble.jpg",
        originalLabel: "Marble",
    },
    {
        label: "Ghế công viên",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-park-bench.jpg",
        originalLabel: "Park Bench",
    },
    {
        label: "Ngoài trời tự nhiên",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-outdoors.jpg",
        originalLabel: "Outdoors",
    },
    {
        label: "Hoa cỏ mềm mại",
        thumbnail: "https://assets.pebblely.com/samples/thumbnail-flowers.jpg?width=150&quality=75",
        originalLabel: "Flowers",
    },
    {
        label: "Hoa hồng lãng mạn",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-roses.jpg",
        originalLabel: "Roses",
    },
    {
        label: "Hoa oải hương",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-lavender.jpg",
        originalLabel: "Lavender",
    },
    {
        label: "Cây xanh tươi mát",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-monstera.jpg",
        originalLabel: "Monstera",
    },
    {
        label: "Đồng cỏ xanh mướt",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-meadow.jpg",
        originalLabel: "Meadow",
    },
    {
        label: "Hoa khô vintage",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-dried-flowers.jpg",
        originalLabel: "Dried Flowers",
    },
    {
        label: "Hoa baby dịu dàng",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-babys-breath.jpg",
        originalLabel: "Baby's Breath",
    },
    {
        label: "Gian bếp gia đình",
        thumbnail: "https://pebblely.b-cdn.net/samples/sample_20230130_111500_1.jpg",
        originalLabel: "Kitchen",
    },
    {
        label: "Hoa quả tươi ngon",
        thumbnail: "https://pebblely.b-cdn.net/samples/sample_20221226_013404_15.jpg",
        originalLabel: "Fruits",
    },
    {
        label: "Thiên nhiên trong lành",
        thumbnail: "https://pebblely.b-cdn.net/samples/sample_20221226_013645_20.jpg",
        originalLabel: "Nature",
    },
    {
        label: "Bãi biển yên bình",
        thumbnail: "https://pebblely.b-cdn.net/samples/sample_20221226_015530_43.jpg",
        originalLabel: "Beach",
    },
    {
        label: "Phòng tắm hiện đại",
        thumbnail: "https://pebblely.b-cdn.net/samples/sample_20230204_1600_1.jpg",
        originalLabel: "Bathroom",
    },
    {
        label: "Tòa nhà chọc trời",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-skyscraper.jpg",
        originalLabel: "Skyscraper",
    },
    {
        label: "Quà tặng dễ thương",
        thumbnail: "https://pebblely.b-cdn.net/samples/gifts_sample.jpg",
        originalLabel: "Gifts",
    },
    {
        label: "Không khí Giáng sinh",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-christmas.jpg",
        originalLabel: "Christmas",
    },
    {
        label: "Ngày cưới hạnh phúc",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-wedding.jpg",
        originalLabel: "Wedding",
    },
    {
        label: "Lễ hội Halloween",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-halloween.jpg",
        originalLabel: "Halloween",
    },
    {
        label: "Lễ Phục sinh",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-easter-2.jpg",
        originalLabel: "Easter",
    },
    {
        label: "Hiệu ứng sơn màu",
        thumbnail: "https://pebblely.b-cdn.net/samples/sample_20221226_013926_38.jpg",
        originalLabel: "Paint",
    },
    {
        label: "Lửa ấm áp",
        thumbnail: "https://pebblely.b-cdn.net/samples/fire_sample.jpg",
        originalLabel: "Fire",
    },
    {
        label: "Hiệu ứng nước",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-water.jpg",
        originalLabel: "Water",
    },
    {
        label: "Hiệu ứng vàng lấp lánh",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-gold.jpg",
        originalLabel: "Gold",
    },
    {
        label: "Đá cuội tự nhiên",
        thumbnail: "https://pebblely.b-cdn.net/samples/sample_20221226_015811_32.jpg",
        originalLabel: "Pebbles",
    },
    {
        label: "Tuyết trắng tinh khôi",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-snow.jpg",
        originalLabel: "Snow",
    },
    {
        label: "Trang sức tinh tế",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-necklace.jpg",
        originalLabel: "Necklace",
    },
    {
        label: "Nội thất hiện đại",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-modern.jpg",
        originalLabel: "Furniture",
    },
    {
        label: "Nội thất tối giản",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-minimalist.jpg",
        originalLabel: "Minimalist Interior",
    },
    {
        label: "Phong cách Nhật",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-japanese.jpg",
        originalLabel: "Japanese Interior",
    },
    {
        label: "Phong cách Bắc Âu",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-scandi.jpg",
        originalLabel: "Scandi Interior",
    },
    {
        label: "Nội thất sang trọng",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-luxury.jpg",
        originalLabel: "Luxury Interior",
    },
    {
        label: "Nội thất ven biển",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-coastal.jpg",
        originalLabel: "Coastal Interior",
    },
    {
        label: "Phong cách Bohemian",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-bohemian.jpg",
        originalLabel: "Bohemian Interior",
    },
    {
        label: "Phong cách nông trại",
        thumbnail: "https://pebblely.b-cdn.net/samples/thumbnail-farmhouse.jpg",
        originalLabel: "Farmhouse Interior",
    },
]);

const themeActive = ref(themes.value[0]);

const handleSelectTheme = (theme) => {
    themeActive.value = theme;
};

const generateAiBackground = async () => {
    try {
        isLoading.value = true;
        const hasEnoughCredit = await checkCredit();
        if (!hasEnoughCredit) {
            isLoading.value = false;
            return;
        }
        const formData = new FormData();
        if (imageFile.value) {
            const img = new Image();
            img.src = URL.createObjectURL(imageFile.value);

            img.onload = async () => {
                if (img.width > 2048 || img.height > 2048) {
                    alert("Kích thước ảnh không được vượt quá 2048px.");
                    isLoading.value = false;
                    return;
                }
            };
            formData.append("file", imageFile.value);
        } else {
            formData.append("imageUrl", imageUrl.value);
        }
        formData.append("theme", themeActive.value.originalLabel);
        formData.append("isTransparentImage", isTransparentImage.value);

        const res = await axios.post(route("ai-background.generate-ai-background"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        console.log(res.data.s3_url);
        if (res.status === 200) {
            isLoading.value = false;
            imageGenerate.value = res.data;
            listHistory.value.unshift(res.data);
            console.log(listHistory.value);
            credits.value = res.data.credits;
        }
    } catch (error) {
        console.error("Error updating video before leaving the page:", error);
    } finally {
        isLoading.value = false;
    }
};

const openDetail = (item) => {
    selectedImage.value = item;
};

const closeDetail = () => {
    selectedImage.value = null;
};
</script>
