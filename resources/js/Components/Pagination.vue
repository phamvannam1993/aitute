<template>
    <Paginate
        v-model="currentPage"
        :page-count="totalPage"
        :click-handler="handlePageClick"
        :container-class="'pagination'"
        :prev-text="'<'"
        :next-text="'>'"
    />
</template>

<script setup>
import { ref, watch } from 'vue';
import Paginate from 'vuejs-paginate-next';

const props = defineProps({
    totalPage: {
        type: Number,
        required: true
    },
    initialPage: {
        type: Number,
        default: 1
    }
});

const emit = defineEmits(['updatePage']);

const currentPage = ref(props.initialPage);

const handlePageClick = (page) => {
    currentPage.value = page;
    emit('updatePage', page); 
};

watch(() => props.initialPage, (newPage) => {
    currentPage.value = newPage;
});
</script>

<style>
.pagination {
    color: black;
    display: flex;
    justify-content: center;
    list-style-type: none;
    flex-wrap: wrap;
}

.pagination li {
    margin: 5px !important;
    cursor: pointer;
    background-color: white;
    border: solid 1px #DEDEDE;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: 500;
    transition: background-color 0.3s, color 0.3s; 
    border-radius: .75rem;
}

.pagination li a {
    width: 40px;
    height: 40px;
    text-align: center;
    line-height: 40px;
}

.pagination li.active {
    font-weight: bold;
    color: white;
    background-color: #FFA411;
}

.pagination li:hover {
    background-color: #F5F5F5;
    color: #FFA411;
}

.pagination li.disabled {
    pointer-events: none;
    color: #BDBDBD;
}
@media (max-width: 768px) {
    .pagination li a {
        width: 30px;
        height: 30px;
        line-height: 30px;
        font-size: 14px;
    }

    .pagination li {
        margin: 3px;
    }
}

@media (max-width: 480px) {
    .pagination {
        justify-content: space-between;
    }

    .pagination li a {
        width: 24px;
        height: 24px;
        line-height: 24px;
        font-size: 16px;
    }

    .pagination li {
        margin: 4px;
    }
}
</style>