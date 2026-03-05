<template>
    <AuthenticatedLayout>
        <Head title="Modifier la kapsule" />

        <div class="p-10">
            <h1 class="text-2xl font-bold mb-4">Modifier la Kapsule</h1>
            <!-- Formulaire de modification de la kapsule -->
            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700"
                        >Nom de la Kapsule</label
                    >
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2"
                    />
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-gray-700"
                        >Description</label
                    >
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="4"
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2"
                    ></textarea>
                </div>

                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Enregistrer les modifications
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";

const props = defineProps({
    kapsule: Object,
});
const page = usePage();
const toast = useToast();
const form = useForm({
    name: props.kapsule.name,
    description: props.kapsule.description,
});

function submit() {
    if (form.name.trim() === "") {
        toast.error("Le nom de la Kapsule ne peut pas être vide.");
        return;
    }
    form.post(route("kapsules.modify", props.kapsule.id), {
        onSuccess: () => {
            const flash = page.props.flash;
            if (flash && flash.success) {
                toast.success(flash.success);
            }
        },
    });
}
</script>
