<template>
  <div class="fixed bottom-4 right-4 z-50">
    <!-- Chat Icon Button -->
    <button @click="toggleChat" class="bg-orion-primary p-3 rounded-full shadow-lg hover:bg-green-600 transition-colors"
      v-if="!isOpen">
      <img src="/assets/svgs/footer-head-robo.svg" alt="chat" class="w-8 h-8" />
    </button>

    <!-- Chat Box -->
    <div v-show="isOpen" class="bg-white bg-[url('/assets/img/numerology/background.png')] bg-no-repeat bg-cover rounded-lg shadow-xl w-[800px] max-h-[90vh] flex flex-col">
      <!-- Header -->
      <div class="bg-[#092A99] text-white p-4 rounded-t-lg flex justify-between items-center">
        <div class="flex items-center gap-2">
          <img src="/assets/img/numerology/icon.png" alt="">
          <h3 class="font-semibold text-lg">Thần Số Học</h3>
        </div>
        <button @click="toggleChat" class="hover:opacity-80">
          <img src="/assets/img/numerology/close.png" alt="">
        </button>
      </div>

      <!-- Chat Content -->
      <div class="p-4 bg-white bg-opacity-40 mx-4 md:mx-10 mt-4 rounded-md max-h-[500px] overflow-y-auto">
          <div class="flex-1 space-y-6" ref="chatContent">
          <!-- Messages -->
          <div v-for="(message, index) in messages" :key="index" :class="[
            'max-w-[90%] rounded-lg p-4',
            message.role === 'assistant' ? 'bg-gray-100 mr-auto prose prose-sm max-w-none' : 'bg-green-100 ml-auto'
          ]" v-html="message.role === 'assistant' ? marked(message.content) : message.content">
          </div>

          <!-- Typing Indicator -->
          <div v-if="loading" class="flex space-x-2 p-4 bg-gray-100 max-w-[90%] rounded-lg">
            <div class="w-2.5 h-2.5 bg-gray-500 rounded-full animate-bounce"></div>
            <div class="w-2.5 h-2.5 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            <div class="w-2.5 h-2.5 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
          </div>
        </div>
      </div>

      <!-- Input Area -->
      <div class="p-4 bg-white bg-opacity-40 mx-4 md:mx-10 mt-4 rounded-md mb-6">
          <div class="">
          <!-- Initial Form -->
          <form @submit.prevent="handleSubmit" v-if="!hasInitialResult">
            <div class="space-y-4">
              <div class="space-y-2">
                <label for="fullName" class="text-xs lg:text-sm flex gap-1 items-center mb-1 font-semibold">
                    <div class="flex items-center gap-2 py-1 px-2 rounded-2xl text-white bg-[#092A99]">
                        <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                        <p class="font-bold text-[16px]">Họ và tên</p>
                    </div>
                </label>
                <div class="relative">
                  <input id="fullName" v-model="form.fullName" type="text" placeholder="Nhập họ và tên của bạn"
                  class="w-full p-3 border rounded-lg focus:outline-none focus:border-orion-primary pl-14 text-xs md:text-base" required />
                  <img src="/assets/img/numerology/name.png" class="absolute inset-y-0 m-auto left-4" alt="">
                </div>
              </div>
              <div class="space-y-2">
                <label for="date" class="text-xs lg:text-sm flex gap-1 items-center mb-1 font-semibold">
                    <div class="flex items-center gap-2 py-1 px-2 rounded-2xl text-white bg-[#092A99]">
                        <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                        <p class="font-bold text-[16px]">Ngày sinh</p>
                    </div>
                </label>
                <div class="relative">
                  <input id="date" v-model="form.birthDate" type="date"
                  class="w-full p-3 border rounded-lg focus:outline-none focus:border-orion-primary pl-14 text-xs md:text-base" required />
                  <img src="/assets/img/numerology/date.png" class="absolute inset-y-0 m-auto left-4" alt="">
                </div>
              </div>
              <div class="flex flex-col md:flex-row items-center gap-2">
                <button type="submit"
                  class="w-full bg-[#092A99] text-white flex items-center gap-2 py-1 px-2 justify-center md:p-2 rounded-lg hover:bg-green-600 transition-colors text-xs md:text-lg font-medium"
                  :disabled="loading">
                  <img src="/assets/img/numerology/result.png" class="w-8" alt="">
                  {{ loading ? 'Đang phân tích...' : 'Xem kết quả' }}
                </button>
                <button type="submit"
                  class="w-full bg-white text-[#671697] flex items-center gap-2 py-1 px-2 justify-center md:p-2 rounded-lg hover:bg-green-600 transition-colors text-xs md:text-lg font-medium"
                  :disabled="loading">
                  <img src="/assets/img/numerology/new_result.png" class="w-8" alt="">
                  {{ loading ? 'Đang phân tích...' : 'Xem kết quả mới' }}
                </button>
              </div>
            </div>
          </form>

          <!-- Chat Input -->
          <div v-else class="flex space-x-2">
            <input v-model="chatInput" type="text" placeholder="Nhập câu hỏi của bạn..."
              class="flex-1 text-xs md:text-base p-3 border rounded-lg focus:outline-none focus:border-orion-primary"
              @keyup.enter="sendMessage" />
            <button @click="sendMessage"
              class="bg-[#092A99] flex items-center gap-2 text-white px-2 py-1 md:px-6 md:py-3 rounded-lg transition-colors"
              :disabled="loading">
              <img src="/assets/img/numerology/send.png" class="w-4 md:w-8" alt="">
              <p class="text-sm md:text-[20px] font-bold">Gửi</p>
            </button>
          </div>

          <!-- New Analysis Button -->
          <button v-if="hasInitialResult" @click="startNewAnalysis"
            class="mt-4 flex items-center gap-2 justify-center w-full md:w-3/5 mx-auto bg-[#092A99] p-2 rounded-lg transition-colors">
            <img src="/assets/img/numerology/result.png" class="w-8" alt="">

            <p class="text-white text-base md:text-[20px] font-bold">Phân tích mới</p>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';
import { marked } from 'marked';

const isOpen = ref(false);
const loading = ref(false);
const hasInitialResult = ref(false);
const chatInput = ref('');
const messages = ref([
  {
    role: 'assistant',
    content: 'Xin chào! Hãy nhập họ tên và ngày sinh để xem kết quả thần số học của bạn.'
  }
]);

const form = ref({
  fullName: '',
  birthDate: ''
});

// Lưu context của phiên chat
const chatContext = ref({
  fullName: '',
  birthDate: '',
  numerologyData: null
});

const handleSubmit = async () => {
  try {
    loading.value = true;
    const formattedDate = new Date(form.value.birthDate)
      .toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });

    messages.value.push({
      role: 'user',
      content: `Thông tin của tôi: ${form.value.fullName} - ${formattedDate}`
    });

    // Thêm message assistant trống để cập nhật
    messages.value.push({
      role: 'assistant',
      content: ''
    });

    const response = await fetch(route('than-so-hoc'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'text/event-stream',
      },
      body: JSON.stringify({
        fullname: form.value.fullName,
        birthdate: formattedDate
      })
    });

    const reader = response.body.getReader();
    const decoder = new TextDecoder();

    let fullContent = '';

    while (true) {
      const { value, done } = await reader.read();
      if (done) break;

      const chunk = decoder.decode(value);
      const lines = chunk.split('\n');

      for (const line of lines) {
        if (line.trim() && line.startsWith('data: ')) {
          try {
            const data = JSON.parse(line.slice(5));
            if (data.type === 'content') {
              fullContent += data.content;
              // Cập nhật message cuối cùng
              const lastMessage = messages.value[messages.value.length - 1];
              if (lastMessage.role === 'assistant') {
                lastMessage.content = fullContent;
              }
              await scrollToBottom();
            }
          } catch (e) {
            console.error('Error parsing SSE data:', e);
          }
        }
      }
    }

    if (fullContent) {
      hasInitialResult.value = true;
      chatContext.value = {
        fullName: form.value.fullName,
        birthDate: formattedDate,
        numerologyData: fullContent
      };
    }

  } catch (error) {
    console.error('Error:', error);
    messages.value.push({
      role: 'assistant',
      content: error.message || 'Có lỗi xảy ra, vui lòng thử lại sau.'
    });
  } finally {
    loading.value = false;
    await scrollToBottom();
  }
};

const sendMessage = async () => {
  if (!chatInput.value.trim() || loading.value) return;

  const userMessage = chatInput.value;
  chatInput.value = '';

  messages.value.push({
    role: 'user',
    content: userMessage
  });

  loading.value = true;
  let responseContent = '';

  try {
    const response = await fetch(route('than-so-hoc-chat'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'text/event-stream',
      },
      body: JSON.stringify({
        message: userMessage,
        context: chatContext.value
      })
    });

    const reader = response.body.getReader();
    const decoder = new TextDecoder();

    // Thêm message assistant trống để cập nhật
    messages.value.push({
      role: 'assistant',
      content: ''
    });

    while (true) {
      const { value, done } = await reader.read();
      if (done) break;

      const chunk = decoder.decode(value);
      const lines = chunk.split('\n');

      for (const line of lines) {
        if (line.trim() && line.startsWith('data: ')) {
          try {
            const data = JSON.parse(line.slice(5));
            if (data.type === 'content') {
              responseContent += data.content;
              // Cập nhật message cuối cùng
              const lastMessage = messages.value[messages.value.length - 1];
              if (lastMessage.role === 'assistant') {
                lastMessage.content = responseContent;
              }
              await scrollToBottom();
            }
          } catch (e) {
            console.error('Error parsing SSE data:', e);
          }
        }
      }
    }
  } catch (error) {
    messages.value.push({
      role: 'assistant',
      content: error.message || 'Có lỗi xảy ra, vui lòng thử lại sau.'
    });
  } finally {
    loading.value = false;
    await scrollToBottom();
  }
};

const startNewAnalysis = () => {
  hasInitialResult.value = false;
  messages.value = [
    {
      role: 'assistant',
      content: 'Xin chào! Hãy nhập họ tên và ngày sinh để xem kết quả thần số học của bạn.'
    }
  ];
  form.value = {
    fullName: '',
    birthDate: ''
  };
  chatContext.value = {
    fullName: '',
    birthDate: '',
    numerologyData: null
  };
};

const scrollToBottom = async () => {
  await nextTick();
  const chatContent = document.querySelector('.overflow-y-auto');
  if (chatContent) {
    chatContent.scrollTop = chatContent.scrollHeight;
  }
};

const toggleChat = () => {
  isOpen.value = !isOpen.value;
};

onMounted(() => {
  scrollToBottom();
});
</script>

<style>
/* Add required styles for markdown rendering */
.prose {
  max-width: none;
  font-size: 1rem;
}

.prose h1 {
  font-size: 1.5em;
  font-weight: bold;
  margin-top: 1.5em;
  margin-bottom: 0.8em;
  color: #1a56db;
}

.prose ul {
  list-style-type: disc;
  padding-left: 1.5em;
  margin: 1em 0;
}

.prose li {
  margin: 0.5em 0;
}

.prose p {
  margin-bottom: 1em;
  line-height: 1.6;
}

/* Custom scrollbar */
.overflow-y-auto {
  scrollbar-width: thin;
  scrollbar-color: rgba(155, 155, 155, 0.5) transparent;
}

.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: rgba(155, 155, 155, 0.5);
  border-radius: 20px;
}

/* Message animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.message {
  animation: fadeIn 0.3s ease-out;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .w-\[600px\] {
    width: 90vw;
  }

  .max-h-\[80vh\] {
    max-height: 90vh;
  }
}

/* Add responsive styles */
@media (max-width: 900px) {
  .w-\[800px\] {
    width: 90vw;
    margin: 0 auto;
  }
}

/* Add animation for chat box */
.chat-box-enter-active,
.chat-box-leave-active {
  transition: all 0.3s ease;
}

.chat-box-enter-from,
.chat-box-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>