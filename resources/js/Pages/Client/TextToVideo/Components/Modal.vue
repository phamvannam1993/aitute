<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-75">
      <div class="bg-white p-4 rounded-lg shadow-lg w-1/3">
        <h2 class="text-xl font-bold mb-4">Create Slides</h2>
        <form @submit.prevent="submitForm">
          <div class="mb-4">
            <label for="topic" class="block text-sm font-medium text-gray-700">Topic</label>
            <input v-model="topic" type="text" id="topic" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          </div>
          <div class="mb-4">
            <label for="numberOfSlides" class="block text-sm font-medium text-gray-700">Number of Slides</label>
            <input v-model="numberOfSlides" type="number" id="numberOfSlides" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          </div>
          <div class="flex justify-end">
            <button type="button" @click="closeModal" class="mr-2 px-4 py-2 bg-gray-500 text-white rounded-md">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Create</button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, defineProps, defineEmits } from 'vue';
  import axios from 'axios';
  
  const props = defineProps({
    show: Boolean,
  });
  
  const emit = defineEmits(['close', 'create']);
  
  const topic = ref('');
  const numberOfSlides = ref(1);
  
  const closeModal = () => {
    emit('close');
  };
  
  const submitForm = async () => {
    try {
      const response = await axios.post(route('slide-ai.create'), {
        topic: topic.value,
        numberOfSlides: Number(numberOfSlides.value)
      });
  
      const slideSuggestions = response.data;
      emit('create', slideSuggestions);
      closeModal();
    } catch (error) {
      console.error('Error creating slides:', error);
      // Xử lý lỗi nếu có
    }
  };
  </script>
  
  <style scoped>
  /* Style cho modal */
  </style>
  