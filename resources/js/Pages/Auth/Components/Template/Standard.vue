<template>
    <CreditPackageTemplate :credit_package="credit_package">
    </CreditPackageTemplate>
</template>
<script setup>
import CreditPackageTemplate from './CreditPackageTemplate.vue';

const { credit_package } = defineProps({
    credit_package: {
        type: Object,
    },
});

</script>
