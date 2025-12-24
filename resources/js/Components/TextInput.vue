<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});
</script>

<template>
    <input
        ref="input"
        v-model="model"
        class="block w-full
               border-gray-300 rounded-lg shadow-sm
               focus:border-indigo-500 focus:ring-indigo-500
               transition"
        v-bind="$attrs"
    />
</template>
