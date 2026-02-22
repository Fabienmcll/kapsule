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
        <div class="p-6 bg-gray-900 text-white">
            <h2 class="text-xl font-bold">{{ $t('create_kapsule') }}</h2>
            <form @submit.prevent="submit" class="mt-6">
                <label for="name" class="block text-sm font-bold">{{ $t('name_kapsule') }}</label>
                <input 
                    v-model="form.name" 
                    type="text" 
                    placeholder="Nom de la Kapsule" 
                    class="border-gray-300 bg-gray-900 text-white w-full rounded-md shadow-sm"
                />
                <div class="mt-4">
                    <label for="description" class="block text-sm font-bold">{{ $t('description_kapsule') }}</label>
                    <textarea 
                        v-model="form.description" 
                        type="text" 
                        placeholder="Description" 
                        class="border-gray-300 bg-gray-900 text-white w-full h-40 rounded-md shadow-sm"
                    ></textarea>
                </div>
                <button type="submit" :disabled="form.processing" class="mt-4 w-full hover:cursor-pointer hover flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded">
                  <FolderPlusIcon class="h-5 w-5" />
                {{ $t('create') }}
                </button>
            </form>
        </div>
    </Modal>

    <h2 class="text-2xl uppercase  underline decoration-blue-600 decoration-2 ml-6 text-white font-bold">{{ $t('your_kapsules') }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="kapsule in kapsules" :key="kapsule.id" class="p-6 ">
            <div class="border border-gray-700 w-full p-5 rounded-xl dark:bg-gray-800 text-white shadow-sm hover:shadow-md transition-shadow">
    
    <div class="flex items-center gap-4">
        <div class="p-3 rounded-lg bg-blue-600/20 flex items-center justify-center">
            <FolderIcon class="h-6 w-6 text-blue-400" />
        </div>
        <h3 class="font-bold text-xl tracking-tight uppercase truncate">{{ kapsule.name }}</h3>
    </div>

    <div class="mt-5">
        <p class="text-sm text-gray-400 leading-relaxed min-h-[60px] line-clamp-2 italic">
            {{ kapsule.description || 'Aucune description' }}
        </p>

        <hr class="my-5 border-gray-700">

        <div class="flex items-center justify-between mt-4">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ formatDate(kapsule.created_at) }}
            </span>

            <div @click="copyToClipboard(kapsule.share_code)" 
                 class="flex cursor-pointer items-center justify-between gap-3 bg-gray-900/50 border border-gray-600 px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors group">
                <code class="font-mono text-sm font-bold text-blue-400">{{ kapsule.share_code }}</code>
                <ClipboardDocumentIcon class="h-4 w-4 text-gray-500 group-hover:text-white" />
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';
import { PlusIcon, FolderPlusIcon, FolderIcon, ClipboardDocumentIcon } from '@heroicons/vue/24/solid'
import { useToast } from "vue-toastification";

const showModal = ref(false);
const toast = useToast();

const form = useForm({
    name: '',
    description: '',
});

const props = defineProps({
    kapsules: Array,
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

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text);
    toast.success(trans('code_copied'));
}
</script>