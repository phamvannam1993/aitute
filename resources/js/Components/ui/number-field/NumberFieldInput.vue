<script setup>
import { NumberFieldInput } from "radix-vue";
import { cn } from "@/lib/utils";
</script>

<template>
  <NumberFieldInput
    data-slot="input"
    :class="
      cn(
        'flex h-10 w-full rounded-md border border-slate-200 focus:border-slate-200 focus:outline-0 bg-white py-2 text-sm text-center ring-offset-white placeholder:text-slate-500 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-800 dark:bg-slate-950 dark:ring-offset-slate-950 dark:placeholder:text-slate-400 dark:focus-visible:ring-slate-300',
      )
    "
  />
</template>
