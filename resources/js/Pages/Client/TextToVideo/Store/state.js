import { defineStore } from 'pinia'

export const useSlideAIStore = defineStore('slideAI', {
    state: () => {
        return { data: {}, currentSlide: {} };
    }
})
