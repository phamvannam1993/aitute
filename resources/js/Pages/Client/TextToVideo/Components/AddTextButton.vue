<template>
    <div class="flex items-center justify-center">
        <button @click="handleAddTextComponent" class="h-[28px] bg-[rgba(0,0,0,0.7)] text-white flex px-[6px] py-[4px] items-center rounded">
            <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="text" class="svg-inline--fa fa-text w-[18px] h-[18px]" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                <path fill="#FFF" d="M48 80v48c0 13.3-10.7 24-24 24s-24-10.7-24-24V72C0 49.9 17.9 32 40 32H408c22.1 0 40 17.9 40 40v56c0 13.3-10.7 24-24 24s-24-10.7-24-24V80H248l0 352h48c13.3 0 24 10.7 24 24s-10.7 24-24 24H152c-13.3 0-24-10.7-24-24s10.7-24 24-24h48l0-352H48z"></path>
            </svg>
        </button>
    </div>
</template>

<script setup>
const { activeComponent } = defineProps({
    activeComponent: Object,
});
const handleAddTextComponent = () => {
    if (!activeComponent.props) {
        activeComponent.props = {};
    }

    if (!activeComponent.props?.components) {
        activeComponent.props.components = [];
    }
    activeComponent?.props?.components.push({
        name: "EditableContent",
        title: "Nhập nội dung",
        title_style: {
            fontFamily: "Be Vietnam Pro",
            fontSize: 16,
            dragStyle: {
                top: 0,
                left: 0,
                width: 250,
                height: 60
            },
            delayTime: 0,
            animation: "",
        },
    });
};
</script>
