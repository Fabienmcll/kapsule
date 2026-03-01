<template>
    <Head title="Kapsule" />

    <AuthenticatedLayout>
        <div class="m-8">
            <h3 class="text-xl">{{ kapsule.name }}</h3>

            <p>{{ kapsule.description }} par {{ owner.username }}</p>

            <p>Membres : {{ members.length + 1 }}</p>
            <div v-for="member in members" :key="member.id">
                - {{ member.username }}
                <!-- Si invite du membre en attente -->
                <span v-if="member.is_pending">(en attente)</span>
                <!-- Si on est proprio et que le membre est en attente, on affiche les boutons d'acceptation/rejet -->
                <button
                    v-if="amIOwner && member.is_pending"
                    @click="showAcceptModal = true"
                >
                    Accepter la demande
                </button>
                <button
                    v-if="amIOwner && member.is_pending"
                    @click="showRejectModal = true"
                >
                    Rejeter la demande
                </button>
            </div>
            - {{ owner.username }} (propriétaire)
        </div>
        <!-- Si vous voyez ce message, c'est que vous n'avez pas encore été accepté dans la kapsule. -->
        <p v-if="amIPending">
            Votre demande d'adhésion à la kapsule est en attente.
        </p>

        <!-- Si vous voyez ça c'est que soit proprio soir membre ET accepté -->
        <div v-if="amIOwner || (amIMember && amIAccepted)" class="m-8">
            <FileUpload :kapsule-id="kapsule.id" />

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                <div
                    v-for="item in media"
                    :key="item.id"
                    class="rounded-lg overflow-hidden bg-gray-800 border border-gray-700"
                >
                    <img
                        v-if="item.mime_type.startsWith('image/')"
                        :src="item.thumb || item.url"
                        class="w-full h-48 object-cover shadow-sm"
                    />

                    <video
                        v-else-if="item.mime_type.startsWith('video/')"
                        class="w-full h-48 object-cover"
                    >
                        <source :src="item.url" :type="item.mime_type" />
                    </video>
                </div>
            </div>
        </div>
        <AcceptKapsuleModal
            :show="showAcceptModal"
            :member="members.find((m) => m.is_pending)"
            :kapsule="kapsule"
            :allMembers="members"
            @close="showAcceptModal = false"
            @accepted="
                members = members.map((m) =>
                    m.id === $event
                        ? { ...m, accepted: true, is_pending: false }
                        : m,
                )
            "
        />
        <!-- rejected : Supprimer la l'utilisateur de la liste des membres après rejet -->
        <RejectKapsuleModal
            :show="showRejectModal"
            :member="members.find((m) => m.is_pending)"
            :kapsule="kapsule"
            :allMembers="members"
            @close="showRejectModal = false"
            @rejected="members = members.filter((m) => m.id !== $event)"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FileUpload from "@/Components/FileUpload.vue";
import AcceptKapsuleModal from "./Partials/AcceptKapsuleModal.vue";
import RejectKapsuleModal from "./Partials/RejectKapsuleModal.vue";

const props = defineProps({
    kapsule: Object,
    owner: Object,
    members: Array,
    media: Array,
    isAccepted: Boolean,
    isPending: Boolean,
});

const page = usePage();

const owner = ref(props.owner);
const kapsule = ref(props.kapsule);
const members = ref(props.members);
const amIAccepted = ref(props.isAccepted);
const amIPending = ref(props.isPending);

const showAcceptModal = ref(false);
const showRejectModal = ref(false);

const amIOwner = ref(owner.value.id === page.props.auth.user.id);

const amIMember = ref(
    members.value.some((member) => member.id === page.props.auth.user.id),
);
</script>
