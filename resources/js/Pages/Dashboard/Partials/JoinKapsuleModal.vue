<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6 bg-gray-900 text-white">
            <h2 class="text-xl font-bold mb-6">{{ $t("join_kapsule") }}</h2>
            <label for="codeToJoin" class="block text-sm font-bold mb-1">
                {{$t("code_placeholder")}}
            </label>
            <input
                id="codeToJoin"
                v-model="codeToJoin"
                type="text"
                :placeholder="$t('code_placeholder')"
                class="border-gray-300 bg-gray-900 text-white w-full rounded-md shadow-sm"
            />
        </div>
        <button
            @click="searchWithCode(codeToJoin)"
            class="w-full bg-blue-600 text-white py-2 rounded-b-md"
        >
            {{ $t("join") }}
        </button>
    </Modal>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { trans } from "laravel-vue-i18n";
import Modal from "@/Components/Modal.vue";

defineProps({
    show: Boolean,
});

const emit = defineEmits(["close", "success"]);

const toast = useToast();
const codeToJoin = ref("");

const searchWithCode = (code) => {
    if (code.trim() === "") {
        toast.error(trans("please_enter_confirm_code"));
        return;
    }
    router.get(
        route("dashboard"),
        { codeToJoin: code },
        {
            preserveState: true,
            replace: true,
            onSuccess: (page) => {
                if (
                    page.props.kapsuleWithCode &&
                    Object.keys(page.props.kapsuleWithCode).length > 0
                ) {
                    emit("success", page.props.kapsuleWithCode);
                } else {
                    toast.error(trans("kapsule_not_found"));
                }
            },
        },
    );
};
</script>