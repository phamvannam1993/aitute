<template>
    <div class="inline-block font-normal" :class="wrapperClass" ref="target">
        <DraggableResizeable class="min-w-[150px]" ref="spanRef" :resizable="true" :style="style.dragStyle" @onDragStop="onDragStop" @onResizeStop="onResizeStop">
            <div class="w-full h-full overflow-hidden" :style="{ animationDelay: `${style.delayTime / 1000}s` }">
                <template v-if="isShow">
                    <div contenteditable="true" class="block h-full w-full whitespace-pre-wrap px-2 py-4 rounded-md leading-1" :class="[style.animation, isHeading ? 'heading' : 'paragraph']" @click="showEditPopup = true" @input="handleChange" :style="{ ...styleObject, backgroundColor: `${style.textBorder || 'rgba(255,255,255,0)'}` }">{{ isTyping ? displayedText : props.modelValue }}</div>
                </template>
            </div>
        </DraggableResizeable>
        <div v-if="showEditPopup" class="z-[9998] text-edit-menu absolute bg-white rounded-b-2xl p-2 h-[60px] w-[800px] top-[20px] left-[50%] translate-x-[-50%] shadow-xl font-normal">
            <div class="flex items-center">
                <select v-model="style.fontFamily" class="appearance-none rounded-lg bg-transparent text-sm focus:outline-none border-[#e2e2e2] py-1.5 mr-2">
                    <option value="Be Vietnam Pro">Be Vietnam Pro</option>
                    <option value="Arial">Arial</option>
                    <option value="Times New Roman">Times New Roman</option>
                    <option value="Georgia">Georgia</option>
                    <option value="Courier New">Courier New</option>
                    <option value="Roboto">Roboto</option>
                    <option value="Roboto Mono">Roboto Mono</option>
                    <option value="Roboto Condensed">Roboto Condensed</option>
                    <option value="Inter">Inter</option>
                    <option value="Oswald">Oswald</option>
                    <option value="Open Sans">Open Sans</option>
                    <option value="Nunito">Nunito</option>
                    <option value="Nunito Sans">Nunito Sans</option>
                    <option value="Quicksand">Quicksand</option>
                    <option value="Montserrat">Montserrat</option>
                </select>

                <NumberField class="max-w-[100px] mr-2" v-model="style.fontSize">
                    <NumberFieldContent>
                        <NumberFieldDecrement />
                        <NumberFieldInput />
                        <NumberFieldIncrement />
                    </NumberFieldContent>
                </NumberField>

                <div
                    class="mr-1 shrink-0 w-[36px] h-[36px] rounded hover:bg-[#e2e2e2] flex items-center justify-center cursor-pointer transition duration-150"
                    :class="{
                        'bg-[#e2e2e2] ': style.isBold,
                    }"
                    @click="style.isBold = !style.isBold"
                >
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-testid="font-weight-bold-svg">
                        <path d="M15.225 11.5391C16.195 10.8834 16.875 9.80681 16.875 8.80851C16.875 6.59659 15.125 4.89362 12.875 4.89362H6.625V18.5957H13.665C15.755 18.5957 17.375 16.9319 17.375 14.8864C17.375 13.3987 16.515 12.1264 15.225 11.5391ZM9.625 7.34042H12.625C13.455 7.34042 14.125 7.99617 14.125 8.80851C14.125 9.62085 13.455 10.2766 12.625 10.2766H9.625V7.34042ZM13.125 16.1489H9.625V13.2128H13.125C13.955 13.2128 14.625 13.8685 14.625 14.6808C14.625 15.4932 13.955 16.1489 13.125 16.1489Z" fill="#808080"></path>
                    </svg>
                </div>

                <div
                    class="mr-1 shrink-0 w-[36px] h-[36px] rounded hover:bg-[#e2e2e2] flex items-center justify-center cursor-pointer transition duration-150"
                    :class="{
                        'bg-[#e2e2e2]': style.isItalic,
                    }"
                    @click="style.isItalic = !style.isItalic"
                >
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-testid="font-italic-svg">
                        <path d="M10 4.89362V7.82979H12.21L8.79 15.6596H6V18.5957H14V15.6596H11.79L15.21 7.82979H18V4.89362H10Z" fill="#808080"></path>
                    </svg>
                </div>

                <div
                    class="mr-1 shrink-0 w-[36px] h-[36px] rounded hover:bg-[#e2e2e2] flex items-center justify-center cursor-pointer transition duration-150"
                    :class="{
                        'bg-[#e2e2e2] ': style.isUnderline,
                    }"
                    @click="style.isUnderline = !style.isUnderline"
                >
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-testid="font-underLine-svg">
                        <path d="M12 16.6383C15.31 16.6383 18 14.0055 18 10.7659V2.93616H15.5V10.7659C15.5 12.6549 13.93 14.1915 12 14.1915C10.07 14.1915 8.5 12.6549 8.5 10.7659V2.93616H6V10.7659C6 14.0055 8.69 16.6383 12 16.6383ZM5 18.5957V20.5532H19V18.5957H5Z" fill="#808080"></path>
                    </svg>
                </div>

                <ColorSelect v-model="style.color" />
                <BorderSelect v-model="style.textBorder" />
                <TextAlignTool v-model="style.textAlign" />
                <LineHeightTool v-model="style.lineHeight" />
                <button @click="promptRewriteInstructions" class="rounded hover:bg-[#e2e2e2] flex items-center justify-center">
                    <svg width="32" height="32" viewBox="0 0 90 90" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <rect width="90" height="90" fill="url(#pattern0_7992_94)" />
                        <defs>
                            <pattern id="pattern0_7992_94" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <use xlink:href="#image0_7992_94" transform="scale(0.0111111)" />
                            </pattern>
                            <image
                                id="image0_7992_94"
                                width="90"
                                height="90"
                                xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAABaCAYAAAA4qEECAAAACXBIWXMAAAsTAAALEwEAmpwYAAAFPUlEQVR4nO2cS2wcRRCGKwnvlxAKj4DxVM8uCRiBBLnwSgJCQgLEBckg8ToA4n0EGYSAE3DhAFwQSARDBBJC5MJDQuGAAjYJkAfhkBOXIEhCIIadqnGwEzfqtrPxZtdrz0x7emanfqmPW1v9bW31q7oBRCKRSCQSiUQikUgkEolEIlHVVIsuB0XPAfIXgLwLkPeB4oOFasYn5J+tj8bX/sYAlEZBdCMo2gKKdTkbfQ8Yr4XCaoU+DZA+8g+K3TTkDdCnT4VC6WK+EBRv8w5HuYZNP0I/rYBCyPzqSD94h6IWrW23/1bv6qV0oeaKbN7gGXK81jsElVMLopv8gS717IITNhr1OE/23XnOOaobl+YPWtHz3juucm4hPesBNH+ZylnknyCMbgakzd7BJff98/xBm6VrqqiIb7CfD6MrQNGUd3jJ2g4foPeniIivW23QpwWAl8T/ffmDVjyWzFH6vW2V1ffPOaDoV7dA6DCE/DgoehCQJhzDHis66O3QP67mXr7TqLuoo0+atpHXVwS0iVZ6CAb0SV1trdMngIruA8W7s4OO727aDem2CoCmcaj/dVYim4E+ZWavOG3aiGFAn9G0Z37gxCmufKCnIKBbk9mM12TLq7Sxg5/v9zjoZue/gSC+tqutgK4CxV9lBhFG93Tw846KgLYD1AQgP9LRjoFj0kxmCMQdU1Vdn5wtHZUJtLIgjkBIt7fYqMXXAdJ/biDQG3P6ivxKhUCzaXvtgGellzmbQyNFgHEwp68BXeBoUCwNaA2Kn5j+fHSvo0ieAhUPzuuv+TeZf1VlQCNFoHjYSd40aQf50QX7jPRAxvGgRKCVq0ZbIOBrEvtd49WA9K2AVl0B/22X1RivyxwoZiBW/A4g/ykRrWbgKv7AzofnW8qnkl5qt27NrEXxHxVMHTRlc6rZB8lNehlgfFeX1WkPgkb6Lf9ONfu2uzqgld1ofznXSqEwOg+QXuhy8tOjoJVNIUcA6TtAGoL6eM15P1Y2ltsUhfzZAja0ehk0t+Ztc0gQ8GOZChDt/oc9gdmccBFTFdA8O63sB6T7k/sdD9pjtXTfW0HQqhnlrwPoJQvy2W4uZTp5rzJoNjOUZxbg71MOvqvyoCdBRVfODfnQSkcn4qUHfRCQXgKkkfRRzR928fVtR36WHvSTs+o80u2uIU3aefDxMqcuTk5wyg4a+V8Ixs6eZfe9DLbat0yd7XuXH/SrLXaDxmWpN+iRN7X7SRsFNPIuOF+f3gYH6cV0cOgw1BvnNu2YGg9T61HpiEbaA7Xxemfjeqkd3NLBfrhpxlQtuYNcQtDIm1oir6P0Eru/kfSUfHb6cJs2SgIaadJeBU566aaPLwLk1wD5QIKoftN+xn39dQlAh/H1mb5v1YEzbVWqW3A9BhppInHBY8fvpI8F9PzAd9jyr9X6xMSA++lqQH63AFcxCh7RqiW699i94IXIPOtgBje/cEsKWh0Fzuu7nmqHfOdMsY2uNug0l4XU8Y3eWpw66d66LLTTTQfiwfZNIN7rHWphrr+lvdCp2qLkl5YTEqSnCwC0QBc6zRtEzjpAtxxbepvBsgBQO/s5lD9oMyNw1gkahfDQJYW/X+7l0r2ReejJd+dVbtE84geyBR2v8Q5A5dSO3mH3JvMMjm8IatHbMBTk8aqtBYChF6ltK8bjVUamENE8XeYfinbaTACZi0aFkons6YJx3SNt+NgNsiJqunp+tMRRPJJ57zxXmTmneYPIrKYU7yzsI7DTvpmS3SF/82SRSCQSiUQikUgkEolEIpFIBN70P7BrRY68kLeyAAAAAElFTkSuQmCC"
                            />
                        </defs>
                    </svg>
                    <h3 class="text-blue-600 font-bold">AI</h3>
                </button>
                <div v-if="showRewriteInput" class="absolute top-16 right-0 w-fit h-full z-30">
                    <div class="bg-white flex flex-col items-end justify-center p-4 rounded-lg w-fit">
                        <select v-model="selectedOptionAI" @change="updateRewriteInstructions" class="w-fit p-2 border border-gray-300 rounded-lg mb-3">
                            <option value="">-- Chọn một yêu cầu --</option>
                            <option value="Rút gọn văn bản">Viết ngắn hơn</option>
                            <option value="Mô tả chi tiết hơn">Viết chi tiết hơn</option>
                            <option value="Khác">Khác</option>
                        </select>
                        <div v-if="selectedOptionAI === 'Khác'" class="bg-white p-4 rounded-lg shadow-lg max-w-lg w-full">
                            <h3 class="text-lg font-semibold mb-2">Nhập yêu cầu:</h3>
                            <textarea v-model="rewriteInstructions" placeholder="Ví dụ: Làm cho nó trang trọng hơn..." class="w-full p-2 border border-gray-300 rounded-lg mb-3"></textarea>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button @click="cancelRewrite" class="px-3 py-1 bg-gray-300 text-black rounded-lg mr-2">Huỷ</button>
                            <button @click="rewriteTextWithAI" class="px-3 py-1 bg-blue-500 text-white rounded-lg">Viết lại</button>
                        </div>
                    </div>
                </div>
                <select v-model="style.animation" class="rounded-md px-2 py-2 ml-2">
                    <option value="">animation</option>
                    <option value="ani-left-to-right">trái phải</option>
                    <option value="ani-right-to-left">phải trái</option>
                    <option value="ani-top-to-bottom">trên xuống</option>
                    <option value="ani-bottom-to-top">dưới lên</option>
                </select>
                <input type="number" v-model="style.delayTime" class="w-28 px-2 py-2 ml-2 rounded-md" />
            </div>
        </div>
        <div v-if="loading" class="fixed inset-0 bg-black z-[9999] bg-opacity-60 flex flex-col gap-2 justify-center items-center">
            <div class="spinner"></div>
            <h3 class="text-white">Hệ thống đang xử lý, xin đợi một chút</h3>
        </div>
    </div>
</template>

<script setup>
import { computed, onBeforeMount, reactive, ref, watch } from "vue";
import { NumberField, NumberFieldContent, NumberFieldDecrement, NumberFieldIncrement, NumberFieldInput } from "@/Components/ui/number-field";
import { onClickOutside } from "@vueuse/core";
import ColorSelect from "@/Pages/Client/TextToVideo/Components/EditableContent/ColorSelect.vue";
import BorderSelect from "@/Pages/Client/TextToVideo/Components/EditableContent/BorderSelect.vue";
import TextAlignTool from "@/Pages/Client/TextToVideo/Components/EditableContent/TextAlignTool.vue";
import LineHeightTool from "@/Pages/Client/TextToVideo/Components/EditableContent/LineHeightTool.vue";
import { randomAnimation } from "@/Pages/Client/TextToVideo/Animation.js";
import DraggableResizeable from "@/Pages/Client/TextToVideo/Components/DraggableResizeable.vue";
import { onMounted } from "@vue/runtime-core";

const props = defineProps({
    modelValue: String,
    customStyle: Object,
    isHeading: Boolean,
    isTyping: Boolean,
    wrapperClass: String,
    resizable: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

const target = ref(null);
onClickOutside(target, (event) => {
    const classList = event.target.closest(".text-edit-menu")?.classList;
    if (!classList?.contains("text-edit-menu")) {
        showEditPopup.value = false;
    }
});

let displayedText = ref("");
const showEditPopup = ref(false);
const style = defineModel("customStyle");

const spanRef = ref(null);
const isShow = ref(false);

const styleObject = computed(() => {
    if (!style.value) {
        return {};
    }

    const value = {};

    if (style.value.fontFamily) {
        value.fontFamily = style.value.fontFamily;
    }

    if (style.value.fontSize) {
        value.fontSize = `${style.value.fontSize}px`;
    }

    if (style.value.isBold) {
        value.fontWeight = "bold";
    }

    if (style.value.isItalic) {
        value.fontStyle = "italic";
    }

    if (style.value.isUnderline) {
        value.textDecoration = "underline";
    }

    if (style.value.color) {
        value.color = style.value.color;
    }

    if (style.value.textAlign) {
        value.textAlign = style.value.textAlign;
    }

    if (style.value.lineHeight) {
        value.lineHeight = style.value.lineHeight;
    }

    return value;
});

const onDragStop = ({ left, top }) => {
    style.value.dragStyle.left = left;
    style.value.dragStyle.top = top;
};

const onResizeStop = ({ left, top, width, height }) => {
    if (style.value) {
        style.value.dragStyle.left = left;
        style.value.dragStyle.top = top;
        style.value.dragStyle.width = width;
        style.value.dragStyle.height = height;
    }
};

const handleChange = (event) => {
    const newText = event.target.innerText;
    emit("update:modelValue", newText);
    displayedText.value = newText;
};

const typeText = (text, delay = 5) => {
    displayedText.value = "";
    return new Promise((resolve) => {
        let i = 0;
        const timer = setInterval(() => {
            if (i < text.length) {
                displayedText.value += text.charAt(i);
                i++;
            } else {
                clearInterval(timer);
                resolve();
            }
        }, delay);
    });
};

watch(
    () => props.modelValue,
    async (newValue) => {
        if (newValue) {
            await typeText(newValue);
        }
    },
    { immediate: true }
);

const showRewriteInput = ref(false);
const rewriteInstructions = ref("");

const loading = ref(false);

const selectedOptionAI = ref("");

const promptRewriteInstructions = () => {
    showRewriteInput.value = true;
};

const rewriteTextWithAI = async () => {
    showRewriteInput.value = false;
    if (rewriteInstructions.value != "") {
        loading.value = true;
        console.log(rewriteInstructions.value);
        const aiRewrittenText = await callAIRewriteAPI(displayedText.value, rewriteInstructions.value);
        emit("update:modelValue", aiRewrittenText);
        rewriteInstructions.value = "";
        selectedOptionAI.value = "";
    } else {
        alert("Vui lòng nhập yêu cầu của bạn!");
    }
};

const updateRewriteInstructions = () => {
    rewriteInstructions.value = selectedOptionAI.value != "Khác" ? selectedOptionAI.value : "";
};

const callAIRewriteAPI = async (content, requirement) => {
    try {
        const response = await axios.post(route("slide-ai.rewrite"), {
            content: content,
            requirement: requirement,
        });
        loading.value = false;
        return response.data;
    } catch (error) {
        console.error("Error calling AI rewrite API:", error);
        if (error.code === "ERR_BAD_REQUEST") {
            alert("Bạn không có quyền sử dụng tính năng này. Vui lòng liên hệ quản trị viên.");
            loading.value = false;
            return;
        }
        return content;
    }
};

const cancelRewrite = () => {
    showRewriteInput.value = false;
};

onMounted(() => {
    if (!style.value.dragStyle) {
        style.value.dragStyle = {
            top: 0,
            left: 0,
            width: 250,
            height: 60,
        };
    }

    if (!style.value.delayTime) {
        style.value.delayTime = 0;
    }

    if (!style.value.animation) {
        style.value.animation = "";
    }

    if (!style.value.textBorder) {
        style.value.textBorder = "";
    }

    setTimeout(() => {
        isShow.value = true;
    }, Number(style.value.delayTime) + 100);
});
</script>

<style scoped>
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

.ani-left-to-right {
    animation: ani-left-to-right 1s ease-in-out;
}

.ani-right-to-left {
    animation: ani-right-to-left 1s ease-in-out;
}

.ani-top-to-bottom {
    animation: ani-top-to-bottom 1s ease-in-out;
}

.ani-bottom-to-top {
    animation: ani-bottom-to-top 1s ease-in-out;
}

@keyframes ani-left-to-right {
    0% {
        transform: translateX(-300px);
    }
    100% {
        transform: none;
    }
}

@keyframes ani-right-to-left {
    0% {
        transform: translateX(300px);
    }
    100% {
        transform: none;
    }
}

@keyframes ani-top-to-bottom {
    0% {
        transform: translateY(-300px);
    }
    100% {
        transform: none;
    }
}

@keyframes ani-bottom-to-top {
    0% {
        transform: translateY(300px);
    }
    100% {
        transform: none;
    }
}
</style>
