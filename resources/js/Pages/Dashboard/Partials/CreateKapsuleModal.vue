<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6 bg-gray-900 text-white">
            <h2 class="text-xl font-bold">{{ $t("create_kapsule") }}</h2>
            <form @submit.prevent="submit" class="mt-6">
                <label for="name" class="block text-sm font-bold"
                    >{{ $t("name_kapsule") }}
                    <span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.name"
                    type="text"
                    placeholder="Nom de la Kapsule"
                    class="border-gray-300 bg-gray-900 text-white w-full rounded-md shadow-sm"
                />
                <div class="mt-4">
                    <label for="description" class="block text-sm font-bold">
                        {{$t("description_kapsule")}}
                    </label>
                    <textarea
                        v-model="form.description"
                        type="text"
                        placeholder="Description"
                        class="border-gray-300 bg-gray-900 text-white w-full h-40 rounded-md shadow-sm"
                    ></textarea>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="mt-4 w-full hover:cursor-pointer hover flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded"
                >
                    <FolderPlusIcon class="h-5 w-5" />
                    {{ $t("create") }}
                </button>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { trans } from "laravel-vue-i18n";
import Modal from "@/Components/Modal.vue";
import { FolderPlusIcon } from "@heroicons/vue/24/solid";

defineProps({
    show: Boolean,
});

const emit = defineEmits(["close"]);

const toast = useToast();

const form = useForm({
    name: "",
    description: "",
});

const submit = () => {
    if (form.name.trim() === "") {
        toast.error(trans("name_required"));
        return;
    }
    form.post(route("kapsules.store"), {
        onSuccess: () => {
            form.reset();
            toast.success(trans("kapsule_created"));
            emit("close");
        },
    });
};
</script>