<template>
    <Modal :show="show" @close="emit('close')">
        <div
            class="p-8 bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl"
        >
            <div class="flex items-center gap-4 mb-6">
                <div
                    class="h-12 w-12 rounded-full bg-blue-900/30 flex items-center justify-center text-blue-400 border border-blue-500/20"
                >
                    <ExclamationTriangleIcon class="h-6 w-6" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">
                        {{ text }}
                    </h3>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button
                    @click="emit('close')"
                    class="px-6 py-2.5 bg-gray-800 hover:bg-gray-700 text-white rounded-xl font-medium transition-colors border border-gray-700"
                >
                    {{ $t("cancel") }}
                </button>
                <button
                    @click="emit('accepted')"
                    class="px-6 py-2.5 text-white rounded-xl font-bold transition-colors shadow-lg"
                    :class="
                        isRedButton
                            ? 'bg-red-600 hover:bg-red-700 shadow-red-900/20'
                            : 'bg-blue-600 hover:bg-blue-700 shadow-blue-900/20'
                    "
                >
                    {{ actionText }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from "@/Components/Modal.vue";
import { ExclamationTriangleIcon } from "@heroicons/vue/24/solid";
import { computed } from "vue";

const props = defineProps({
    isRedButton: Boolean,
    show: Boolean,
    text: String,
    actionText: String,
});
const isRedButton = computed(() => props.isRedButton);
const emit = defineEmits(["close", "accepted"]);
</script>
