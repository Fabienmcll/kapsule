<template>
    <Modal :show="show" @close="emit('close')">
        <div class="p-6 bg-gray-900 text-white">
            <h2 class="text-lg font-medium">Paramètres de la Kapsule</h2>
            <Link
                v-if="kapsule?.id"
                :href="route('kapsules.banned-users', kapsule.id)"
                class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                Voir les utilisateurs bannis
            </Link>
            <Link
                v-if="kapsule?.id"
                :href="route('kapsules.edit', kapsule.id)"
                class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                Modifier la Kapsule
            </Link>
        </div>
    </Modal>
</template>

<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { trans } from "laravel-vue-i18n";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    show: Boolean,
    kapsule: Object,
});

const emit = defineEmits(["close"]);

const toast = useToast();
const page = usePage();
</script>
