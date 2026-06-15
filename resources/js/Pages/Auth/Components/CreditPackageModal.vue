<template>
    <Modal :show="pageData.is_show_post_detail_modal" alignItems="items-center"
        customClass="!max-w-[100%] !bg-transparent " minWidth="w-full" @close="closeModal">

        <Carousel :items-to-show="1.5" v-bind="config">
            <Slide v-for="(credit_package, index) in pageData.credit_packages" :key="index">
                <div>
                    <Trial v-if='credit_package.template == `Trial`' :credit_package="credit_package" />
                    <Standard v-if='credit_package.template == `Standard`' :credit_package="credit_package" />
                    <Premium v-if='credit_package.template == `Premium`' :credit_package="credit_package" />
                    <Vip v-if='credit_package.template == `Vip`' :credit_package="credit_package" />
                    <!-- <component :is="credit_package.template" :credit_package="credit_package" /> -->
                </div>
            </Slide>

            <template #addons>
                <Navigation>
                    <template #prev>
                        <span> <img src="/assets/img/pages/auth/CreditPackageModal/btn-pre-orion.png"> </span>
                    </template>
                    <template #next>
                        <span> <img src="/assets/img/pages/auth/CreditPackageModal/btn-next-orion.png"> </span>
                    </template>
                </Navigation>
            </template>
        </Carousel>
    </Modal>
</template>
<script setup>
import Modal from '@/Components/Modal.vue';
import { reactive } from 'vue';
import { Carousel, Navigation, Slide } from 'vue3-carousel';
import 'vue3-carousel/dist/carousel.css';
import Premium from './Template/Premium.vue';
import Standard from './Template/Standard.vue';
import Trial from './Template/Trial.vue';
import Vip from './Template/Vip.vue';

const config = {
    autoplay: 0,
    itemsToShow: 1,
    wrapAround: true,
    snapAlign: 'center',
    mouseDrag: false,
    touchDrag: false,
    breakpointMode: 'carousel',
    breakpoints: {
        // 100px and up
        100: {
            itemsToShow: 1,
            snapAlign: 'center',
        },
        // 768px and up
        768: {
            itemsToShow: 2,
            snapAlign: 'start',
        },
        // 1280px and up
        1280: {
            itemsToShow: 3,
            snapAlign: 'start',
        },
    },
};

const pageData = reactive({
    is_show_post_detail_modal: false,
    credit_packages: []
});

const openModal = () => {
    getCreditPackages();
    pageData.is_show_post_detail_modal = true;
};

const closeModal = () => {
    pageData.is_show_post_detail_modal = false;
};

const getCreditPackages = async () => {
    try {
        const response = await axios.get(route("api.credit-packages.index"));
        // pageData.credit_packages = response.data.data
        const rawData = response.data.data;

        const grouped = {};

        rawData.forEach(pkg => {
            const key = `${pkg.name}_${pkg.template}`;
            if (!grouped[key]) {
                grouped[key] = {
                    name: pkg.name,
                    template: pkg.template,
                    title: pkg.title,
                    content: pkg.content,
                    link: pkg.link,
                    durations: {}
                };
            }

            // Clean duration: '12 tháng' -> '12'
            const durationNumber = pkg.duration.replace(/[^\d]/g, '');
            grouped[key].durations[durationNumber] = {
                price: parseInt(pkg.price),
                credit: parseInt(pkg.credit),
                duration: pkg.duration
            };
        });

        // Convert object to array
        pageData.credit_packages = Object.values(grouped);
    } catch (error) {

    }
};

defineExpose({
    openModal,
});

</script>