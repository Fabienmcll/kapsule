<template>
    <Head title="Kapsule" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            <div class="relative overflow-hidden rounded-2xl bg-gray-900 p-8 shadow-xl border border-gray-800">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                             <span class="text-gray-400 text-sm flex items-center gap-1">
                                <InformationCircleIcon class="h-4 w-4" />
                                {{ $t('owned_by') }} {{ owner.username }}
                             </span>
                        </div>
                        <h1 class="text-4xl font-extrabold text-white tracking-tight">{{ kapsule.name }}</h1>

                        <div class="mt-2 flex items-center gap-4">
                            <p class="text-lg text-gray-400 max-w-2xl leading-relaxed">
                                {{ kapsule.description || $t('no_description') }}
                            </p>
                            <div v-if="kapsule.share_code" class="flex items-center gap-2 px-3 py-1 bg-gray-800 border border-gray-700 rounded-lg group hover:border-blue-500/50 transition-colors">
                                <span class="text-xs font-mono text-gray-400 select-all">{{ kapsule.share_code }}</span>
                                <button
                                    @click="copyToClipboard(kapsule.share_code)"
                                    class="text-gray-500 hover:text-blue-400 transition-colors"
                                    :title="$t('copy_code')"
                                >
                                    <ClipboardDocumentIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button
                            v-if="amIOwner"
                            @click="showParametersModal = true"
                            class="flex items-center gap-2 px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white rounded-xl font-medium transition-all border border-gray-700 shadow-md group"
                        >
                            <Cog6ToothIcon class="h-5 w-5 text-blue-400" />
                            {{ $t('parameters') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-gray-900/80 rounded-2xl p-6 border border-gray-800">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <UsersIcon class="h-5 w-5 text-blue-400" />
                                {{ $t('members') }}
                            </h3>
                            <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded-lg text-xs font-bold border border-blue-500/30">
                                {{ members.length + 1 }}
                            </span>
                        </div>

                        <div class="space-y-4">
                            <!-- Owner Entry -->
                            <div class="flex items-center justify-between p-3 rounded-xl bg-blue-500/10 border border-blue-500/20">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold">
                                        {{ owner.username.charAt(0).toUpperCase() }}
                                    </div>
                                    <span class="text-white font-medium">{{ owner.username }}</span>
                                </div>
                                <span class="text-[10px] uppercase tracking-wider bg-blue-500/30 text-blue-200 px-2 py-0.5 rounded-md border border-blue-500/50">
                                    {{ $t('owner') }}
                                </span>
                            </div>

                            <div v-for="member in members" :key="member.id"
                                class="flex flex-col gap-3 p-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-300 text-xs font-bold">
                                            {{ member.username.charAt(0).toUpperCase() }}
                                        </div>
                                        <span class="text-gray-200 font-medium">{{ member.username }}</span>
                                    </div>
                                    <div v-if="member.is_pending" class="flex items-center gap-1 text-[10px] text-yellow-500 font-bold uppercase italic">
                                        <div class="h-1.5 w-1.5 rounded-full bg-yellow-500 animate-pulse"></div>
                                        {{ $t('pending') }}
                                    </div>
                                </div>

                                <div v-if="amIOwner" class="flex items-center gap-2 mt-1">
                                    <template v-if="member.is_pending">
                                        <button
                                            @click="memberToAcceptOrReject = member; showAcceptModal = true;"
                                            class="flex-1 flex items-center justify-center gap-1 py-1.5 bg-green-600/20 hover:bg-green-600 text-green-400 hover:text-white rounded-lg text-xs font-bold transition-all border border-green-600/30"
                                        >
                                            <CheckIcon class="h-3.5 w-3.5" />
                                            {{ $t('accept') }}
                                        </button>
                                        <button
                                            @click="memberToAcceptOrReject = member; showRejectModal = true;"
                                            class="flex-1 flex items-center justify-center gap-1 py-1.5 bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white rounded-lg text-xs font-bold transition-all border border-red-600/30"
                                        >
                                            <XMarkIcon class="h-3.5 w-3.5" />
                                            {{ $t('reject') }}
                                        </button>
                                    </template>
                                    <button
                                        v-if="!member.is_pending"
                                        @click="memberToBanId = member.id; showBanModal = true;"
                                        class="flex-1 flex items-center justify-center gap-1 py-1.5 bg-gray-700/50 hover:bg-red-600 text-gray-400 hover:text-white rounded-lg text-xs font-bold transition-all border border-white/10"
                                    >
                                        <NoSymbolIcon class="h-3.5 w-3.5" />
                                        {{ $t('ban') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="amIPending" class="p-6 bg-yellow-900/20 border border-yellow-700/50 rounded-2xl">
                        <div class="flex gap-4">
                            <InformationCircleIcon class="h-6 w-6 text-yellow-500 flex-shrink-0" />
                            <div>
                                <h4 class="text-sm font-bold text-yellow-500 uppercase tracking-wider">{{ $t('access_pending') }}</h4>
                                <p class="mt-1 text-sm text-yellow-600/80 leading-relaxed font-medium">
                                    {{ $t('pending_message') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div v-if="amIOwner || (amIMember && amIAccepted)" class="space-y-8">
                        <div class="bg-gray-900/80 rounded-2xl p-6 border border-gray-800">
                            <FileUpload :kapsule-id="kapsule.id" />
                        </div>

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
                </div>
            </div>
        </div>
        <AcceptKapsuleModal
            :show="showAcceptModal"
            :member="memberToAcceptOrReject"
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
            :member="memberToAcceptOrReject"
            :kapsule="kapsule"
            :allMembers="members"
            @close="showRejectModal = false"
            @rejected="members = members.filter((m) => m.id !== $event)"
        />
        <BanKapsuleModal
            :show="showBanModal"
            :member="members.find((m) => m.id === memberToBanId)"
            :kapsule="kapsule"
            :allMembers="members"
            @close="showBanModal = false"
            @rejected="members = members.filter((m) => m.id !== $event)"
        />
        <ParametersKapsuleModal
            :show="showParametersModal"
            :kapsule="kapsule"
            @close="showParametersModal = false"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FileUpload from "@/Components/FileUpload.vue";
import AcceptKapsuleModal from "./Partials/AcceptKapsuleModal.vue";
import RejectKapsuleModal from "./Partials/RejectKapsuleModal.vue";
import BanKapsuleModal from "./Partials/BanKapsuleModal.vue";
import ParametersKapsuleModal from "../Dashboard/Partials/ParametersKapsuleModal.vue";
import { trans } from "laravel-vue-i18n";
import { useToast } from "vue-toastification";
import {
    UsersIcon,
    Cog6ToothIcon,
    InformationCircleIcon,
    CheckIcon,
    XMarkIcon,
    NoSymbolIcon,
    ClipboardDocumentIcon
} from "@heroicons/vue/24/solid";

const props = defineProps({
    kapsule: Object,
    owner: Object,
    members: Array,
    media: Array,
    isAccepted: Boolean,
    isPending: Boolean,
});

const toast = useToast();

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text);
    toast.success(trans("code_copied"));
}

const page = usePage();

const owner = ref(props.owner);
const kapsule = ref(props.kapsule);
const members = ref(props.members);
const amIAccepted = ref(props.isAccepted);
const amIPending = ref(props.isPending);

const showAcceptModal = ref(false);
const showRejectModal = ref(false);
const showBanModal = ref(false);
const showParametersModal = ref(false);

const memberToAcceptOrReject = ref(null);
const memberToBanId = ref(null);

const amIOwner = ref(owner.value.id === page.props.auth.user.id);

const amIMember = ref(
    members.value.some((member) => member.id === page.props.auth.user.id),
);
</script>
