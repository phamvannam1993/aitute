<template>
    <!--    <div class="absolute w-[100%] h-[100%] top-[50%] left-[50%] -translate-x-1/2 -translate-y-1/2 border border-[blue]">-->
    <!--    </div>-->
    <vue-draggable-resizable
        :active="false"
        v-bind="style.width ? {
            w: style.width,
            h: style.height,
        }: { w: 'auto', h: 'auto' }"
        :x="style.left"
        :y="style.top"
        :class-name="'border-[1px] border-transparent'"
        :class-name-handle="'my-handle-class'"
        ref="resizableRef"
        @dragging="onDrag"
        @dragStop="onDragStop"
        @resizing="onResize"
        @resizeStop="onResizeStop"
        :key="key"
    >
        <template #tl>
            <span
                class="bg-[blue] w-[8px] h-[8px] block top-[-14px] left-[-14px] absolute rounded-[50%] cursor-nwse-resize"></span>
        </template>
        <template #tm>
            <span
                class="bg-[blue] w-[8px] h-[8px] block top-[-14px] left-[50%] absolute rounded-[50%] -translate-x-1/2 cursor-ns-resize"></span>
        </template>
        <template #tr>
            <span
                class="bg-[blue] w-[8px] h-[8px] block top-[-14px] right-[-14px] absolute rounded-[50%] cursor-nesw-resize"></span>
        </template>
        <template #mr>
            <span
                class="bg-[blue] w-[8px] h-[8px] block top-[50%] right-[-14px] absolute rounded-[50%] -translate-y-1/2 cursor-ew-resize"></span>
        </template>
        <template #br>
            <span
                class="bg-[blue] w-[8px] h-[8px] block bottom-[-14px] right-[-14px] absolute rounded-[50%] cursor-nwse-resize"></span>
        </template>
        <template #bm>
            <span
                class="bg-[blue] w-[8px] h-[8px] block bottom-[-14px] left-[50%] absolute rounded-[50%] -translate-x-1/2 cursor-ns-resize"></span>
        </template>
        <template #bl>
            <span
                class="bg-[blue] w-[8px] h-[8px] block bottom-[-14px] left-[-14px] absolute rounded-[50%] cursor-nesw-resize"></span>
        </template>
        <template #ml>
            <span
                class="bg-[blue] w-[8px] h-[8px] block top-[50%] left-[-14px] absolute rounded-[50%] -translate-y-1/2 cursor-ew-resize"></span>
        </template>
        <div class="flex h-full w-full">
            <slot></slot>
        </div>
    </vue-draggable-resizable>
</template>

<script setup>
import VueDraggableResizable from "vue-draggable-resizable";
import {ref} from "vue";

const props = defineProps({
    width: {
        type: [Number, String],
        default: 'auto'
    },
    height: {
        type: [Number, String],
        default: 'auto'
    },
    style: {
        type: Object,
        default: () => ({})
    }
})

const emit = defineEmits(['onDragStop', 'onResizeStop'])

const resizableRef = ref(null)

const onDrag = (x, y) => {
    props.style.left = x
    props.style.top = y
}

const onDragStop = () => {
    console.log('onDragStop')
    emit('onDragStop', {left: props.style.left, top: props.style.top})
}

const onResize = (x, y, width, height) => {
    console.log('onResize', x, y, width, height)
    props.style.left = x
    props.style.top = y
    props.style.width = width
    props.style.height = height
}

const onResizeStop = () => {
    console.log('onResizeStop')
    emit('onResizeStop', {
        left: props.style.left,
        top: props.style.top,
        width: props.style.width,
        height: props.style.height,
    })
}
</script>

<style>
@import "vue-draggable-resizable/style.css";
</style>
