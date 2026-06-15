<!-- components/AnalysisSection.vue -->
<template>
    <div class="w-full bg-[#F5F5F5] px-4 rounded-xl">
        <div class="flex items-center gap-2 my-4">
            <img src="/assets/img/orion-1.png" class="w-4" alt="">
            <p class="text-[10px] md:text-xs font-bold">{{ title }}</p>
        </div>
        <textarea
            v-if="textarea"
            rows="6"
            class="w-full text-base border border-[#D4D4D4] rounded-2xl resize-none"
            v-model="localContent"
            @input="$emit('update:content', $event.target.value)"
        >{{ content }}</textarea>
    </div>
</template>

<script setup>
    import { ref, watch } from 'vue';

    const props = defineProps({
        title: {
            type: String,
            required: true
        },
        content: {
            type: String,
            default: ''
        },
        textarea: {
            type: Boolean,
            default: false
        }
    });

    const emit = defineEmits(['update:content']);

    const localContent = ref(props.content);

    watch(() => props.content, (newValue) => {
        localContent.value = newValue;
    });
</script>
