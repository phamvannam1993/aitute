<!-- resources/js/Components/DynamicChat.vue -->
<template>
    <div class="w-full max-w-7xl mx-auto p-4 h-[1000px] flex flex-col bg-gray-50 rounded-lg shadow">
        <!-- Header -->
        <div class="mb-4 p-4 bg-white rounded-t-lg shadow-sm">
            <h2 class="text-lg font-semibold text-gray-800">Chat Assistant</h2>
        </div>

        <!-- Topic Selection -->
        <div v-if="!started" class="p-4 bg-white rounded-lg shadow-sm mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Chọn chủ đề
            </label>
            <select
                v-model="selectedTopic"
                @change="startConversation"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black"
            >
                <option value="Status Fanpage">Trạng thái Fanpage</option>
                <option value="Status Profile">Trạng thái Profile</option>
                <option value="Chiến dịch">Chiến dịch</option>
            </select>
        </div>

        <!-- Chat Messages -->
        <div
            ref="messagesContainer"
            class="flex-1 overflow-y-auto px-4 py-2 space-y-4"
        >
            <template v-for="(message, index) in messages" :key="index">
                <!-- Bot Message -->
                <div v-if="!message.userResponse" class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>

                    <div class="flex-1 max-w-[80%]">
                        <div
                            class="bg-white rounded-lg shadow-sm p-4 text-black"
                            v-html="message.content"
                            @click="handleButtonClick"
                        ></div>
                    </div>
                </div>

                <!-- User Message -->
                <div v-else class="flex items-start justify-end space-x-3">
                    <div class="max-w-[80%]">
                        <!-- Tin nhắn text thường -->
                        <div
                            v-if="typeof message.userResponse === 'string' && !message.userResponse.includes('<div')"
                            class="bg-blue-500 text-white rounded-lg shadow-sm p-4"
                        >
                            {{ message.userResponse }}
                        </div>
                        <!-- Tin nhắn dạng object/form data -->
                        <div
                            v-else-if="typeof message.userResponse === 'string' && message.userResponse.includes('<div')"
                            class="w-[800px] mx-auto"
                        >
                            <div class="bg-white rounded-lg shadow-sm p-5">
                                <div class="bg-blue-50 p-5 rounded-lg space-y-3"
                                     v-html="message.userResponse"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </template>

            <!-- Loading Indicator -->
            <div v-if="loading" class="flex justify-center items-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="mt-4 bg-white rounded-b-lg shadow-sm p-4">
            <div class="flex space-x-4">
                <input
                    v-model="userInput"
                    type="text"
                    placeholder="Nhập câu hỏi của bạn..."
                    @keyup.enter="sendMessage"
                    :disabled="isSending"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black disabled:bg-gray-100 disabled:cursor-not-allowed"
                />
                <button
                    @click="sendMessage"
                    :disabled="isSending"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    {{ isSending ? 'Đang gửi...' : 'Gửi' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, watch } from 'vue'
    import { router } from '@inertiajs/vue3'

    const messages = ref([])
    const loading = ref(false)
    const started = ref(false)
    const selectedTopic = ref('Status Fanpage')
    const userInput = ref('')
    const conversationId = ref(null)
    const messagesContainer = ref(null)

    const parseHtmlContent = (content) => {
        const div = document.createElement('div')
        div.innerHTML = content

        // Style cho container chính
        div.className = 'prose max-w-none text-gray-800 leading-relaxed'

        // Style cho form và ngăn chặn submit
        const form = div.querySelector('form')
        if (form) {
            form.className = 'space-y-4 max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-sm'
            form.removeAttribute('action')
            form.removeAttribute('method')
        }

        // Style cho label
        const labels = div.querySelectorAll('label')
        labels.forEach(label => {
            label.className = 'block text-sm font-medium text-gray-700 mb-1'
        })

        // Style cho input
        const inputs = div.querySelectorAll('input')
        inputs.forEach(input => {
            input.className = 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
            if (input.readOnly) {
                input.className += ' bg-gray-50'
            }
        })

        // Style cho button và chuyển type từ submit sang button
        const buttons = div.querySelectorAll('button')
        buttons.forEach(button => {
            button.className = 'w-full mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2'
            button.type = 'button'
            button.setAttribute('data-action', 'submit-form')
        })

        // Style cho headings
        const headings = div.querySelectorAll('h1, h2, h3, h4, h5, h6')
        headings.forEach(heading => {
            heading.className = 'font-semibold text-gray-900 my-3'
        })

        // Style cho đoạn văn
        const paragraphs = div.querySelectorAll('p')
        paragraphs.forEach(p => {
            p.className = 'my-3 text-gray-700'
        })

        // Style cho danh sách
        const lists = div.querySelectorAll('ol, ul')
        lists.forEach(list => {
            list.className = 'space-y-3 my-4 ml-4'
            if (list.tagName === 'UL') {
                list.classList.add('list-none')
            } else {
                list.classList.add('list-decimal')
            }
        })

        // Style cho list items
        const listItems = div.querySelectorAll('li')
        listItems.forEach(item => {
            item.className = 'flex items-start gap-2'
            if (item.parentElement.tagName === 'UL') {
                item.innerHTML = `<span class="text-blue-500 mt-1">•</span> <span>${item.innerHTML}</span>`
            }
        })

        // Style cho text được bold
        const boldTexts = div.querySelectorAll('strong')
        boldTexts.forEach(text => {
            text.className = 'font-semibold text-gray-900'
        })

        // Style cho links
        const links = div.querySelectorAll('a')
        links.forEach(link => {
            link.className = 'text-blue-600 hover:underline'
        })

        // Style cho blockquotes
        const quotes = div.querySelectorAll('blockquote')
        quotes.forEach(quote => {
            quote.className = 'border-l-4 border-gray-300 pl-4 italic my-4'
        })

        // Style cho code blocks
        const codes = div.querySelectorAll('code')
        codes.forEach(code => {
            code.className = 'bg-gray-100 rounded px-1'
        })

        // Style cho pre blocks
        const pres = div.querySelectorAll('pre')
        pres.forEach(pre => {
            pre.className = 'bg-gray-100 p-4 rounded my-4'
        })

        // Style cho tables
        const tables = div.querySelectorAll('table')
        tables.forEach(table => {
            table.className = 'min-w-full divide-y divide-gray-200 my-4'
        })

        const ths = div.querySelectorAll('th')
        ths.forEach(th => {
            th.className = 'px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'
        })

        const tds = div.querySelectorAll('td')
        tds.forEach(td => {
            td.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-500'
        })

        // Style cho images
        const images = div.querySelectorAll('img')
        images.forEach(img => {
            img.className = 'rounded-lg shadow-sm my-4'
        })

        // Style cho textarea
        const textareas = div.querySelectorAll('textarea')
        textareas.forEach(textarea => {
            textarea.className = 'w-full min-h-[120px] px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y'
            if (textarea.readOnly) {
                textarea.className += ' bg-gray-50'
            }
        })

        // Style cho select boxes nếu cần
        const selects = div.querySelectorAll('select')
        selects.forEach(select => {
            select.className = 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
        })

        return div.innerHTML
    }

    const detectMessageType = (response) => {
        if (typeof response !== 'string') return { type: 'text', content: 'Invalid response' }

        try {
            const jsonData = JSON.parse(response)
            if (typeof jsonData === 'object') {
                const items = Object.entries(jsonData).map(([key, value]) =>
                    `<div class="flex items-start py-2 border-b border-blue-100 last:border-0">
                    <div class="w-[30%] text-gray-600 font-medium">${key}:</div>
                    <div class="w-[70%] text-gray-800">${value || '-'}</div>
                </div>`
                ).join('')

                return {
                    type: 'data-list',
                    content: `<div class="bg-blue-50 p-4 rounded-lg space-y-2">${items}</div>`
                }
            }
        } catch (e) {
            // Không phải JSON, tiếp tục xử lý các case khác
        }

        if (response.includes('<form')) {
            return {
                type: 'form',
                content: parseHtmlContent(response)
            }
        }

        if (response.includes('<button')) {
            return {
                type: 'buttons',
                content: parseHtmlContent(response)
            }
        }

        return {
            type: 'text',
            content: parseHtmlContent(response)
        }
    }

    const isSending = ref(false)
    const startConversation = async () => {
        if (!selectedTopic.value || !userInput.value.trim() || isSending.value) return

        isSending.value = true
        loading.value = true

        try {
            const response = await axios.post(route('ai-business.sendChat'), {
                inputs: {
                    chude: selectedTopic.value
                }
            })

            started.value = true
            conversationId.value = response.data.conversation_id
            const messageData = detectMessageType(response.data.answer)
            messages.value.push(messageData)

        } catch (error) {
            console.error('Error starting conversation:', error)
            messages.value.push({
                type: 'text',
                content: 'Có lỗi xảy ra, vui lòng thử lại.'
            })
        } finally {
            loading.value = false
            isSending.value = false
        }
    }

    // Cập nhật hàm sendMessage
    const sendMessage = async () => {
        if (!userInput.value.trim() || isSending.value) return

        const currentMessage = userInput.value
        messages.value.push({
            userResponse: currentMessage
        })

        isSending.value = true
        loading.value = true

        try {
            const requestData = {
                inputs: {
                    chude: selectedTopic.value,
                },
                query: currentMessage,
                conversation_id: conversationId.value
            }

            const response = await axios.post(route('ai-business.sendChat'), requestData)

            if (!conversationId.value && response.data.conversation_id) {
                conversationId.value = response.data.conversation_id
            }

            const messageData = detectMessageType(response.data.answer)
            messages.value.push(messageData)

            userInput.value = ''

        } catch (error) {
            console.error('Error sending message:', error)
            messages.value.push({
                type: 'text',
                content: 'Có lỗi xảy ra, vui lòng thử lại.'
            })
        } finally {
            loading.value = false
            isSending.value = false
        }
    }

    // Cập nhật hàm handleButtonClick
    const handleButtonClick = (e) => {
        if (isSending.value) return

        // Xử lý button trong form
        if (e.target.getAttribute('data-action') === 'submit-form') {
            e.preventDefault()
            const form = e.target.closest('form')
            if (form) {
                const formData = new FormData(form)
                const data = {}
                for (let [key, value] of formData.entries()) {
                    data[key] = value
                }

                const formContentItems = Object.entries(data).map(([key, value]) =>
                    `<div class="flex items-start py-2 border-b border-blue-100 last:border-0">
                <div class="w-[30%] text-gray-600 font-medium">${key}:</div>
                <div class="w-[70%] text-gray-800">${value || '-'}</div>
            </div>`
                ).join('')

                const formContent = `<div class="bg-blue-50 p-4 rounded-lg space-y-2">${formContentItems}</div>`

                messages.value.push({
                    userResponse: formContent
                })

                isSending.value = true
                loading.value = true

                const requestData = {
                    inputs: {
                        ...data,
                        chude: selectedTopic.value,
                    },
                    query: JSON.stringify(data),
                    conversation_id: conversationId.value
                }

                axios.post(route('ai-business.sendChat'), requestData)
                    .then(response => {
                        if (!conversationId.value && response.data.conversation_id) {
                            conversationId.value = response.data.conversation_id
                        }
                        const messageData = detectMessageType(response.data.answer)
                        messages.value.push(messageData)
                    })
                    .catch(error => {
                        console.error('Error:', error)
                        messages.value.push({
                            type: 'text',
                            content: 'Có lỗi xảy ra, vui lòng thử lại.'
                        })
                    })
                    .finally(() => {
                        loading.value = false
                        isSending.value = false
                    })
            }
        }

        // Xử lý button thông thường
        if (e.target.hasAttribute('data-message')) {
            const message = e.target.getAttribute('data-message')
            if (message) {
                userInput.value = message
                sendMessage()
            }
        }
    }

    watch(messages, () => {
        setTimeout(() => {
            if (messagesContainer.value) {
                messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
            }
        }, 100)
    }, { deep: true })
</script>

<style>
    .prose {
        @apply text-base leading-relaxed;
    }

    .prose h1 { @apply text-2xl font-bold mb-4; }
    .prose h2 { @apply text-xl font-bold mb-3; }
    .prose h3 { @apply text-lg font-bold mb-2; }
    .prose p { @apply mb-4 leading-relaxed; }
    .prose ul { @apply list-none space-y-2; }
    .prose ol { @apply list-decimal space-y-2; }
    .prose strong { @apply font-semibold text-gray-900; }
    .prose em { @apply italic; }
    .prose blockquote { @apply border-l-4 border-gray-300 pl-4 italic my-4; }
    .prose code { @apply bg-gray-100 rounded px-1; }
    .prose pre { @apply bg-gray-100 p-4 rounded my-4; }
    .prose a { @apply text-blue-600 hover:underline; }
    .prose img { @apply rounded-lg shadow-sm my-4; }

    /* Additional styles for better spacing */
    .prose > * + * {
        @apply mt-4;
    }

    .prose li + li {
        @apply mt-2;
    }

    .prose li > p {
        @apply mb-0;
    }

    /* Form data styles */
    .bg-blue-50 {
        @apply bg-opacity-50;
    }

    .bg-blue-50 .flex {
        @apply transition-colors duration-200;
    }

    .bg-blue-50 .flex:hover {
        @apply bg-blue-100 bg-opacity-50;
    }

    .bg-blue-50 .w-[30%] {
        @apply text-sm font-medium;
    }

    .bg-blue-50 .w-[70%] {
        @apply text-sm break-words;
    }

    .bg-blue-50 .border-blue-100 {
        @apply border-opacity-50;
    }

    /* Message container styles */
    .message-container {
        @apply w-full max-w-full overflow-x-hidden;
    }

    .message-content {
        @apply break-words;
    }
</style>
