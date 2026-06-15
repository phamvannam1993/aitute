<template>
    <div>
        <p>Chọn hiệu ứng</p>
        <select class="rounded-md w-56 px-4 py-2" v-model="style.animation">
            <option value="">Chọn hiệu ứng</option>
            <option value="ani-left-to-right">trái qua phải</option>
            <option value="ani-right-to-left">phải qua trái</option>
            <option value="ani-top-to-bottom">trên xuống dưới</option>
            <option value="ani-bottom-to-top">dưới lên trên</option>
        </select>
    </div>
    <div class="mt-3">
        <p>Chọn hình dạng</p>
        <select class="rounded-md w-56 px-4 py-2" v-model="style.sharp">
            <option value="">Chọn hình dạng</option>
            <option value="sharp-circle">tròn</option>
            <option value="sharp-square">vuông</option>
            <option value="sharp-video">16:9</option>
        </select>
    </div>
    <div class="mt-3">
        <p class="mt-6">Thiết lập thời gian xuất hiện</p>
        <p class="text-xs mb-1">(1000 tương ứng với 1s)</p>
        <input type="text" v-model="style.delayTime" class="px-4 py-2 w-56 rounded-md" />
    </div>
    <!-- <div class="mt-3">
        <p>Độ lớn</p>
        <input type="text" v-model="style.width" class="px-4 py-2 w-56 rounded-md" />
    </div> -->
</template>
<script setup>
import { ref } from "vue";

const style = defineModel();
</script>
