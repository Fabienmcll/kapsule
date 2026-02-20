<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                       {{ $t('welcome') }}
                    </div>
                </div>
            </div>
        </div>
        <form @submit.prevent="submit" class="p-6 bg-white rounded shadow">
        <input 
            v-model="form.name" 
            type="text" 
            placeholder="Nom de la Kapsule" 
            class="border-gray-300 rounded-md shadow-sm"
        />
        <button type="submit" :disabled="form.processing" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded">
            Créer
        </button>
    </form>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
});

const submit = () => {
    form.post(route('kapsules.store'), {
        onSuccess: () => {
            form.reset();
            alert('Kapsule créée !');
        },
    });
};
</script>

