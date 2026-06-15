<template>
    <div class="flex justify-between items-center mb-4">
    <div class="flex justify-start items-center gap-2 lg:gap-4">
        <div class="flex gap-1">
            <div class="flex justify-start items-center gap-2">
                <div class="w-[52px] overflow-hidden rounded-[10px] border-[1px] border-[#d4d4d4]">
                    <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
                </div>

                <h2 class="text-black font-bold text-[20px] lg:text-[30px] line-clamp-1">Lịch sử Chatbot</h2>
            </div>
        </div>
    </div>
    <div class="flex justify-end">
        <button @click="checkAllMessagesStatus" :disabled="isChecking"
        class="px-3 lg:px-4 py-3 bg-ai3goc-primary hover:bg-ai3goc-primary/50 text-white rounded-[16px] transition-all duration-200 flex items-center gap-2"
        :class="{ 'opacity-50 cursor-not-allowed': isChecking }">
        <span v-if="isChecking">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
            </svg>
        </span>
        <span class="text-[12px] lg:text-[16px] font-bold">{{ isChecking ? 'Đang kiểm tra...' : 'Kiểm tra trạng thái' }}</span>
        </button>
    </div>
    </div>
    <div style="overflow-x:auto;" class="mb-4">
    <div class="w-full font-bold text-black flex items-center mb-4 ">
        <div class="lg:w-[60px] hidden lg:flex justify-center items-center">
        <span class="text-[12px] lg:text-[14px]">STT</span>
        </div>
        <div class="w-[50px] lg:w-[150px] flex justify-center items-center">
        <span class="text-[12px] lg:text-[14px]">Chat ID</span>
        </div>
        <div class="w-[50px] lg:w-[150px] flex justify-center items-center">
        <span class="text-[12px] lg:text-[14px]">Fanpage</span>
        </div>
        <div class="w-[60px] lg:w-[120px] flex justify-center items-center">
        <span class="text-[12px] lg:text-[14px]">Trạng thái</span>
        </div>
        <div class="flex-1 flex justify-center items-center">
        <span class="text-[12px] lg:text-[14px]">Ghi chú</span>
        </div>
        <div class="hidden lg:flex lg:w-[75px] justify-center items-center">
        <span class="text-[12px] lg:text-[14px]">Chi tiết</span>
        </div>
    </div>
    <div v-for="(messages, user_page_id, index) in conversations" :key="user_page_id"
    :class="openChats[user_page_id] 
        ? 'mb-3 shadow-sm overflow-hidden border-[2px] border-[#d4d4d4] bg-white rounded-[18px]' 
        : 'mb-3 shadow-sm overflow-hidden bg-white rounded-[18px]'">
        <div :class="openChats[user_page_id] 
        ? 'flex items-center bg-white hover:bg-gray-200 transition-all duration-200 cursor-pointer h-[124px] lg:h-[68px] border-b-[2px] border-[#d4d4d4]' 
        : 'flex items-center border-[2px] border-[#D4D4D4] bg-white hover:bg-gray-200 transition-all duration-200 cursor-pointer rounded-[18px] h-[124px] lg:h-[68px]'" @click="toggleChat(user_page_id)">

        <div class="lg:w-[60px] hidden lg:flex justify-center items-center p-1 lg:p-4 h-full">
            <span class="text-[12px] lg:text-[14px] text-gray-700">{{ index + 1 }}</span>
        </div>

        <div class="w-[50px] lg:w-[150px] flex justify-center items-center border-r-[2px] lg:border-l-[2px] border-[#D4D4D4] p-1 lg:p-4 h-full">
            <span class="text-[12px] lg:text-[14px] text-gray-700 font-medium truncate">{{ user_page_id }}</span>
        </div>

        <div class="w-[50px] lg:w-[150px] flex justify-center items-center border-l-[2px] border-[#D4D4D4] p-1 lg:p-4 h-full">
            <span class="text-[12px] lg:text-[14px] text-gray-700 truncate">{{ messages[0]?.page_name || '' }}</span>
        </div>

        <div class="w-[60px] lg:w-[120px] flex justify-center items-center h-full">
            <span class="text-[12px] lg:text-[14px] text-black" >
            <img v-if="messages[0]?.status !== null" 
                :src="messages[0]?.status ? '/assets/svgs/hot.svg' : '/assets/svgs/cold.svg'" 
                alt="status icon" 
                class="h-6 lg:h-8" />
            <img v-else src="/assets/svgs/cold.svg" alt="status icon" class="h-6 lg:h-8" />
            </span>
        </div>

        <div class="flex-1 flex items-center border-l-[2px] lg:border-r-[2px] border-[#D4D4D4] p-1 lg:p-4 h-full gap-2">
            <span class="text-[12px] lg:text-[14px] text-gray-600 italic max-h-[116px] lg:max-h-[48px] overflow-y-auto flex-1">{{ messages[0]?.comment || 'Chưa có ghi chú' }}             
            </span>
            <button @click.stop="openPromotionDialog(user_page_id)"
            class="px-2 py-2 lg:px-3 lg:py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition-all duration-2 flex justify-center items-center">
            <span class="hidden lg:block">Gửi tin nhắn</span>
            <img class="block lg:hidden size-4" src="/assets/img/send.png" />
            </button>
            <span class="block lg:hidden transition-transform duration-300 ease-in-out"
                :class="openChats[user_page_id] ? 'rotate-180 text-green-500' : 'text-gray-500'">
            ▼
            </span>
        </div>

        <div class="hidden lg:w-[75px] lg:flex justify-center items-center h-full">
            <span class="transition-transform duration-300 ease-in-out"
                :class="openChats[user_page_id] ? 'rotate-180 text-green-500' : 'text-gray-500'">
            ▼
            </span>
        </div>
        </div>

        <!-- Chi tiết tin nhắn -->
        <div v-show="openChats[user_page_id]" class="p-4 text-black overflow-x-auto">
        <table class="w-full border border-gray-300 rounded-[16px] overflow-hidden border-collapse">
            <!-- Header -->
            <thead class="bg-[#002F76] text-white text-[14px] md:text-[16px]">
            <tr>
                <th class="w-[5%] px-3 py-2 text-center border border-gray-300">STT</th>
                <th class="w-[30%] lg:w-[10%] px-3 py-2 text-left border border-gray-300">Vai trò</th>
                <th class="w-[65%] px-3 py-2 text-left border border-gray-300">Message</th>
                <th class="w-[10%] px-3 py-2 text-center border border-gray-300 hidden md:table-cell">Price</th>
                <th class="w-[10%] px-3 py-2 text-center border border-gray-300 hidden md:table-cell">Facebook Page</th>
                <th class="w-[10%] px-3 py-2 text-center border border-gray-300 hidden md:table-cell">Thời gian tạo</th>
            </tr>
            </thead>
            
            <!-- Body -->
            <tbody class="text-[14px] md:text-[16px]">
            <tr v-for="(item, index) in messages" :key="index"
                :class="index % 2 === 0 ? 'bg-white' : 'bg-[#F3F3F3]'">
                <td class="px-3 py-2 text-center border border-gray-300">{{ index + 1 }}</td>
                <td class="px-3 py-2 border border-gray-300">{{ item.role === 'user' ? 'Người dùng' : 'AI' }}</td>
                <td class="px-3 py-2 break-words border border-gray-300">{{ item.text_content }}</td>
                <td class="px-3 py-2 text-right border border-gray-300 hidden md:table-cell">{{ item.price || '' }}</td>
                <td class="px-3 py-2 text-center border border-gray-300 hidden md:table-cell">{{ item.page_name || '' }}</td>
                <td class="px-3 py-2 whitespace-nowrap border border-gray-300 hidden md:table-cell">{{ formatDate(item.created_at) }}</td>
            </tr>
            </tbody>
        </table>
        </div>

    </div>
    </div>
    <div class="flex justify-center mt-12 w-full">
    <Pagination :totalPage="totalPage" :initialPage="currentPage" @updatePage="genData" />
    </div>

    <Modal :show="showPromotionDialog" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Gửi tin nhắn</h2>
        <div class="p-4">
          <div class="relative">
            <textarea 
              v-model="promotionMessage"
              :disabled="isLoadingMessage"
              class="w-full h-32 p-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
              :class="{ 'opacity-50': isLoadingMessage }"
              placeholder="Nhập nội dung tin nhắn ..."
            ></textarea>
            
            <!-- Loading Overlay -->
            <div v-if="isLoadingMessage" 
              class="absolute inset-0 flex items-center justify-center bg-gray-100 bg-opacity-50 rounded-lg">
              <div class="flex flex-col items-center">
                <svg class="animate-spin h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="mt-2 text-sm text-gray-600">Đang tạo tin nhắn...</span>
              </div>
            </div>
          </div>

          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh đính kèm</label>
            
            <div class="flex items-center space-x-2">
              <label 
                class="px-4 py-2 bg-gray-200 text-gray-600 rounded-lg hover:bg-gray-300 cursor-pointer transition-all duration-200"
                :class="{ 'opacity-50 cursor-not-allowed': isLoadingMessage }"
              >
                <span>Chọn hình ảnh</span>
                <input 
                  type="file" 
                  class="hidden" 
                  accept="image/*"
                  @change="handleFileUpload"
                  :disabled="isLoadingMessage"
                />
              </label>
              
              <button 
                v-if="imageFile" 
                @click="removeImage" 
                :disabled="isLoadingMessage"
                class="px-2 py-1 text-red-500 rounded-lg hover:bg-red-100"
              >
                Xóa
              </button>
            </div>
            
            <!-- Preview section -->
            <div v-if="imagePreview" class="mt-3">
              <div class="relative inline-block">
                <img 
                  :src="imagePreview" 
                  class="h-32 object-contain rounded-md border border-gray-300" 
                  alt="Preview"
                />
              </div>
            </div>
          </div>
  
          <div class="flex justify-end mt-4 space-x-2">
            <button 
              @click="closeModal" 
              :disabled="isLoadingMessage"
              class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50"
            >
              Hủy
            </button>
            <button 
              @click="confirmSendPromotion" 
              :disabled="sendingPromotion[selectedUserPageId] || isLoadingMessage || (!promotionMessage.trim() && !imageFile)"
              class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50"
            >
              {{ sendingPromotion[selectedUserPageId] ? 'Đang gửi...' : 'Gửi tin nhắn' }}
            </button>
          </div>
        </div>
      </div>
    </Modal>
</template>
<script setup>
import Modal from '@/Components/Modal.vue';
import { ref, onMounted } from "vue";
import { toast } from "vue3-toastify";
import axios from "axios";
import Pagination from '@/Components/Pagination.vue';
import { format } from 'date-fns';

const props = defineProps({
data: Object,
});

const breadcrumbData = [
{ text: 'Lịch sử chatbot', link: 'ai-chat.histories' },
];
const conversations = ref(props.data.conversations);
const pagination = ref(props.data.pagination);

const currentPage = ref(pagination.value.current_page);
const totalPage = ref(pagination.value.last_page);

const openChats = ref({});
const messageStatuses = ref({});
const isChecking = ref(false);
const sendingPromotion = ref({});
const showPromotionDialog = ref(false);
const promotionMessage = ref('');
const selectedUserPageId = ref(null);
const isLoadingMessage = ref(false);
const imageFile = ref(null);
const imagePreview = ref(null);

// Now using page_name and page_url directly from the message history data
// since it's included in the backend response

const toggleChat = (user_page_id) => {
openChats.value[user_page_id] = !openChats.value[user_page_id];
};

const formatDate = (dateString) => {
if (!dateString) return '';
const date = new Date(dateString);
return format(date, 'dd-MM-yyyy HH:mm:ss');
};

const checkAllMessagesStatus = async () => {
isChecking.value = true;
try {
    const userPageIds = Object.keys(conversations.value);
    const response = await axios.post(route('ai-chat.check-all-messages-status'), {
    user_page_ids: userPageIds
    });
    messageStatuses.value = response.data.statuses;
    // Update comments for each conversation
    Object.entries(conversations.value).forEach(([user_page_id, messages]) => {
    if (messages.length > 0 && messageStatuses.value[user_page_id]) {
        messages[0].comment = messageStatuses.value[user_page_id].comment;
        messages[0].status = messageStatuses.value[user_page_id].status;
    }
    });

    toast.success('Đã kiểm tra xong tất cả tin nhắn', {
    autoClose: 2000
    });
} catch (error) {
    toast.error('Không thể kiểm tra trạng thái tin nhắn', {
    autoClose: 2000
    });
} finally {
    isChecking.value = false;
}
};

const openPromotionDialog = async(user_page_id) => {
selectedUserPageId.value = user_page_id;
isLoadingMessage.value = true;
showPromotionDialog.value = true;

try {
    const response = await axios.post(route('api.get-promotion-message', user_page_id), {
    user_page_id: user_page_id,
    });
    
    if (response.data.message) {
    promotionMessage.value = response.data.message;
    } else {
    promotionMessage.value = "Chào bạn, tôi là bot của cửa hàng. Tôi có thể giúp gì cho bạn?"; // Default message
    }
} catch (error) {
    promotionMessage.value = "Chào bạn, tôi là bot của cửa hàng. Tôi có thể giúp gì cho bạn?"; // Default message
} finally {
    isLoadingMessage.value = false;
}
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    if (!file.type.match('image.*')) {
      toast.error('Vui lòng chọn tệp hình ảnh hợp lệ', {
        autoClose: 2000
      });
      return;
    }
    
    imageFile.value = file;
    
    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  };
  
  const removeImage = () => {
    imageFile.value = null;
    imagePreview.value = null;
  };

  const confirmSendPromotion = async () => {
    if (!selectedUserPageId.value || sendingPromotion.value[selectedUserPageId.value]) return;
    if (!promotionMessage.value.trim() && !imageFile.value) return;
  
    sendingPromotion.value[selectedUserPageId.value] = true;
    try {
      const formData = new FormData();
      formData.append('user_page_id', selectedUserPageId.value);
      formData.append('message', promotionMessage.value);
      
      if (imageFile.value) {
        formData.append('attachment', imageFile.value);
      }
      
      const response = await axios.post(route('api.send-promotion-message', selectedUserPageId.value), 
        formData,
        {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        }
      );
      
      if (response.data.success) {
        toast.success('Đã gửi tin nhắn thành công', {
          autoClose: 2000
        });
        showPromotionDialog.value = false;
      }
    } catch (error) {
      toast.error('Không thể gửi tin nhắn: ' + (error.response?.data?.message || error.message), {
        autoClose: 3000
      });
    } finally {
      sendingPromotion.value[selectedUserPageId.value] = false;
    }
  };

const closeModal = () => {
showPromotionDialog.value = false;
promotionMessage.value = '';
selectedUserPageId.value = null;
imageFile.value = null;
imagePreview.value = null;
};
</script>
<style>
#customers {
font-family: Arial, Helvetica, sans-serif;
border-collapse: collapse;
width: 100%;
table-layout: fixed;
}

#customers td,
#customers th {
border: 1px solid #e5e7eb;
padding: 8px 12px;
color: #374151;
overflow: hidden;
text-overflow: ellipsis;
}

/* Thêm styles cho mobile */
@media screen and (max-width: 640px) {
#customers {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
}

#customers td {
    padding: 6px 8px;
    font-size: 0.875rem;
}

#customers th {
    padding: 6px 8px;
    font-size: 0.875rem;
}

/* Đảm bảo cột Message có thể wrap text trên mobile */
#customers td:nth-child(3) {
    max-width: 200px;
    white-space: normal;
}
}

#customers tr:nth-child(even) {
background-color: #f9fafb;
}

#customers tr:hover {
background-color: #f3f4f6;
}

#customers th {
padding: 12px;
text-align: left;
background-color: #10B981;
color: white;
font-weight: 500;
}

/* Thêm style cho container của bảng */
.table-container {
width: 100%;
overflow-x: auto;
-webkit-overflow-scrolling: touch;
margin-bottom: 1rem;
border-radius: 0.5rem;
box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.rotate-180 {
transform: rotate(180deg);
}

/* Thêm style cho header của mỗi nhóm chat */
.chat-group-header {
background-color: #f3f4f6;
border-bottom: 1px solid #e5e7eb;
transition: all 0.2s ease;
}

.chat-group-header:hover {
background-color: #e5e7eb;
}

button:disabled {
cursor: not-allowed;
}
</style>