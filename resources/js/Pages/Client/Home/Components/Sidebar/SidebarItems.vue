<template>
  <a
    href="#"
    class="flex items-center space-x-4 font-semibold p-3 w-full rounded-lg cursor-pointer hover:text-white hover:bg-[#2C75E3]"
    :class="active ? 'text-white bg-[#2C75E3]' : 'text-black bg-white'"
    @click="handleClick"
    @mouseover="isHovered = true"
    @mouseout="isHovered = false"
  >
    <img
      :src="(active || isHovered) ? icon.replace('.svg', '-active.svg') : icon"
      :alt="text"
      class="h-5"
    />
    <span>{{ text }}</span>
  </a>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  icon: String,
  text: String,
  active: Boolean,
});

const emit = defineEmits(['click']);

const isHovered = ref(false);

const handleClick = () => {
  emit('click');
};
</script>
