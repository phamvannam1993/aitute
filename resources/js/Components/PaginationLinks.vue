<template>
    <div v-if="links?.length > 3">
        <div class="flex flex-wrap justify-center mb-2 text-black">
            <template v-for="(link, k) in links" :key="k">
                <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
                <a
                    v-show="isNaN(link.label) || Math.abs(page - Number(link.label)) < 3"
                    v-else
                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                    :class="{ 'bg-blue-700 text-white': link.active }"
                    :href="link.url"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>

<script setup>
const { links } = defineProps(['links'])
const params = route().params
const page = params.page || 1
</script>
