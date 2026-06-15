<template>
    <SelectRoot v-model="props.selected" :class="props.rootClass" @update:model-value="props.handleChange">
        <SelectTrigger :disabled="props.disabled" :class="[
            'flex w-full items-center gap-2 px-2 md:px-4 py-2 bg-white border border-gray-300 rounded-full shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ai3goc-sec',
            props.triggerClass
        ]">
            <SelectValue :placeholder="props.placeholder" :class="['text-black font-bold flex-1 text-left line-clamp-1', props.valueClass]" />
            <ChevronDown :class="['size-4 md:size-6 flex', props.chevronClass]" />
        </SelectTrigger>

        <SelectPortal>
            <SelectContent :class="[
                'min-w-[160px] flex flex-col lg:mt-0 bg-white border border-gray-200 rounded-lg shadow-custom-shadow p-2 px-3 z-[100]',
                props.contentClass
            ]">
                <SelectViewport :class="props.viewportClass">
                    <SelectGroup :class="props.groupClass">
                        <SelectItem v-for="(option, index) in props.options" :key="index" :class="[
                            'focus-visible:border-none focus-visible:outline-none px-4 py-2 cursor-pointer hover:bg-blue-50 hover:text-ai3goc-sec rounded-full',
                            { '!text-ai3goc-sec bg-green-100': option === props.selected },
                            props.itemClass
                        ]" :value="option">
                            <SelectItemText :class="[

                                props.itemTextClass
                            ]">
                                {{ option }}
                            </SelectItemText>
                        </SelectItem>
                    </SelectGroup>
                </SelectViewport>
            </SelectContent>
        </SelectPortal>
    </SelectRoot>
</template>

<script setup>
import { ChevronDown } from 'lucide-vue-next';
import {
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectItemText,
    SelectPortal,
    SelectRoot,
    SelectTrigger,
    SelectValue,
    SelectViewport
} from 'radix-vue';

const props = defineProps({
    options: {
        type: Array,
        required: true,
    },
    selected: {
        type: String,
        required: true,
    },
    placeholder: {
        type: String,
        default: 'Select...',
    },
    handleChange: {
        type: Function,
        required: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    // New class customization props
    rootClass: {
        type: String,
        default: '',
    },
    triggerClass: {
        type: String,
        default: '',
    },
    iconClass: {
        type: String,
        default: '',
    },
    valueClass: {
        type: String,
        default: '',
    },
    chevronClass: {
        type: String,
        default: '',
    },
    contentClass: {
        type: String,
        default: '',
    },
    viewportClass: {
        type: String,
        default: '',
    },
    groupClass: {
        type: String,
        default: '',
    },
    itemClass: {
        type: String,
        default: '',
    },
    itemTextClass: {
        type: String,
        default: '',
    },
})
</script>

<style></style>
