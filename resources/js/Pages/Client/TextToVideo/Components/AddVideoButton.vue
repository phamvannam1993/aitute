<template>
    <div class="flex items-center justify-center">
        <button @click="handleAddImageComponent" class="h-[28px] bg-[rgba(0,0,0,0.7)] text-white flex px-[6px] py-[4px] items-center rounded">
            <img src="/assets/img/video-icon.png" alt="add" class="size-[24px] mr-[4px] text-white">
        </button>
    </div>
</template>

<script setup>
const { activeComponent } = defineProps({
    activeComponent: Object,
});
const handleAddImageComponent = () => {
    if (!activeComponent.props) {
        activeComponent.props = {};
    }

    if (!activeComponent.props?.components) {
        activeComponent.props.components = [];
    }
    activeComponent?.props?.components.push({
        name: "EditVideo",
        style: {
            src: "",
            width: 250,
            x: 0,
            y: 0,
            delayTime: 100,
            animation: "",
        },
    });
};
</script>
