<template>
    <Head title="Utilisateurs bannis - {{ kapsule.name }}" />
    <AuthenticatedLayout>
        <div class="m-8">
            <h3 class="text-xl">Utilisateurs bannis de {{ kapsule.name }}</h3>

            <p v-if="bannedUsers.length === 0">
                Aucun utilisateur banni pour le moment.
            </p>
            <ul v-else>
                <li v-for="user in bannedUsers" :key="user.id">
                    {{ user.username }}
                    <button
                        @click="
                            showAreYouSureModal = true;
                            selectedUserId = user.id;
                        "
                    >
                        Débannir
                    </button>
                </li>
            </ul>

            <Link
                :href="route('kapsule.load', { kapsule: kapsule.id })"
                class="mt-4 inline-block hover:underline"
            >
                Retour à la kapsule
            </Link>
        </div>
        <AreYouSureModal
            :show="showAreYouSureModal"
            :text="'Êtes-vous sûr de vouloir débannir cet utilisateur ?'"
            :actionText="'Débannir'"
            :member="{}"
            :kapsule="kapsule"
            :allMembers="[]"
            @close="showAreYouSureModal = false"
            @accepted="unbanUser(selectedUserId)"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import AreYouSureModal from "./Partials/AreYouSureModal.vue";
import { useToast } from "vue-toastification";

const props = defineProps({
    kapsule: Object,
    bannedUsers: Array,
});

const bannedUsers = ref(props.bannedUsers);
const kapsule = ref(props.kapsule);
const toast = useToast();

const showAreYouSureModal = ref(false);

const unbanUser = async (userId) => {
    router.post(
        route("kapsules.unban", { kapsule: kapsule.value.id, member: userId }),
        {},
        {
            onSuccess: () => {
                const flash = usePage().props.flash;
                if (flash.success) {
                    toast.success(flash.success);
                    bannedUsers.value = bannedUsers.value.filter(
                        (u) => u.id !== userId,
                    );
                    showAreYouSureModal.value = false;
                } else {
                    toast.error(
                        flash.error ||
                            "Une erreur est survenue lors du débannissement.",
                    );
                }
            },
        },
    );
};
</script>
