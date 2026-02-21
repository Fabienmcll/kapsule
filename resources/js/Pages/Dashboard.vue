<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="flex justify-end p-6 ">
            <button @click="showModal = true" class="px-4 flex items-center gap-2 hover:bg-blue-700 hover:cursor-pointer py-2 bg-blue-600 text-white rounded">
                 <PlusIcon class="h-5 w-5" />
                 {{ $t('create_kapsule') }}
                 </button>
        </div>
    <Modal :show="showModal" @close="showModal = false">
        <div class="p-6 ">
            <h2 class="text-xl font-bold text-gray-900">{{ $t('create_kapsule') }}</h2>
            <form @submit.prevent="submit" class="mt-6">
                <label for="name" class="block text-sm font-bold text-gray-700">{{ $t('name_kapsule') }}</label>
                <input 
                    v-model="form.name" 
                    type="text" 
                    placeholder="Nom de la Kapsule" 
                    class="border-gray-300 w-full rounded-md shadow-sm"
                />
                <div class="mt-4">
                    <label for="description" class="block text-sm font-bold text-gray-700">{{ $t('description_kapsule') }}</label>
                    <textarea 
                        v-model="form.description" 
                        type="text" 
                        placeholder="Description" 
                        class="border-gray-300 w-full h-40 rounded-md shadow-sm"
                    ></textarea>
                </div>
                <button type="submit" :disabled="form.processing" class="mt-4 w-full hover:cursor-pointer hover flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded">
                  <FolderPlusIcon class="h-5 w-5" />
                {{ $t('create') }}
                </button>
            </form>
        </div>
    </Modal>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';
import { PlusIcon, FolderPlusIcon } from '@heroicons/vue/24/solid'
import { useToast } from "vue-toastification";

const showModal = ref(false);
const toast = useToast();

const form = useForm({
    name: '',
    description: '',
});

const submit = () => {
    form.post(route('kapsules.store'), {
        onSuccess: () => {
            form.reset();
            toast.success(trans('kapsule_created'));
            showModal.value = false;
        },
    });
};
</script>