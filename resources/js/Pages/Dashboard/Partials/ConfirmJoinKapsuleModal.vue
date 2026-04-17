<template>
    <Modal :show="show" @close="emit('close')">
            <div class="p-6 bg-gray-900 text-white z-0">
                <h2 class="text-xl font-bold">
                    {{ trans("are_you_sure", { kapsulename: kapsuleWithCode.name, name: userOfTheKapsuleWithCode.username }) }}
                </h2>
                <p class="mt-4">
                    {{ $t("with_joining_this") }}
                </p>
                <div class="mt-6 flex justify-end gap-4">
                    <button
                        @click="emit('close')"
                        class="px-4 py-2 bg-gray-700 text-white rounded"
                    >
                        {{ $t("cancel") }}
                    </button>
                    <button
                        @click="confirmJoin()"
                        class="px-4 py-2 bg-blue-600 text-white rounded"
                    >
                        {{ $t("join") }}
                    </button>
                </div>
            </div>
        </Modal> 
</template>

<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { trans } from "laravel-vue-i18n";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    show: Boolean,
    kapsuleWithCode: Object,
    userOfTheKapsuleWithCode: Object,
});

const emit = defineEmits(["close"]);

const toast = useToast();
const page = usePage();

const confirmJoin = () => {
    if (!props.kapsuleWithCode?.id) {
        toast.error("Aucune Kapsule sélectionnée !");
        return;
    }
    router.post(
        route("kapsules.join", props.kapsuleWithCode.id),
        {},
        {
            onFinish: () => {
                const flash = page.props.flash;
                
                if (flash.success) {
                    toast.success(flash.success);
                    emit("close");
                }
                
                if (flash.error) {
                    toast.error(flash.error);
                    emit("close");
                }
            },
        },
    );
};
</script>
