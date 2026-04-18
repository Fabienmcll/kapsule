<template>
    <Modal v-if="member && kapsule" :show="show" @close="emit('close')">
        <div class="p-8 bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl">
            <div class="flex items-center gap-4 mb-6">
                <div class="h-12 w-12 rounded-full bg-red-900/30 flex items-center justify-center text-red-500 border border-red-500/20">
                    <NoSymbolIcon class="h-6 w-6" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">
                        {{ $t('ban_user_title', { name: member.username }) }}
                    </h3>
                    <p class="text-gray-400 text-sm mt-1">
                        {{ kapsule.name }}
                    </p>
                </div>
            </div>

            <p class="text-gray-300">
                L'utilisateur n'aura plus accès à cette Kapsule et son contenu.
            </p>

            <div class="flex justify-end gap-3 mt-8">
                <button
                    @click="emit('close')"
                    class="px-6 py-2.5 bg-gray-800 hover:bg-gray-700 text-white rounded-xl font-medium transition-colors border border-gray-700"
                >
                    {{ $t('cancel') }}
                </button>
                <button
                    @click="banUser(member.id)"
                    class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-colors shadow-lg shadow-red-900/20"
                >
                    {{ $t('ban') }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from "@/Components/Modal.vue";
import { router, usePage } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { NoSymbolIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    show: Boolean,
    member: Object,
    kapsule: Object,
    allMembers: Array,
});

const toast = useToast();
const emit = defineEmits(["close", "rejected"]);

const banUser = (memberId) => {
    router.post(
        `/kapsules/${props.kapsule.id}/ban/${memberId}`,
        {},
        {
            onSuccess: (page) => {
                const flash = page.props.flash;
                if (flash.success) {
                    toast.success(flash.success);
                    emit("close");
                    emit("rejected", memberId);
                } else if (flash.error) {
                    toast.error(flash.error);
                }
            },
            onError: () => {
                toast.error("Une erreur est survenue.");
            }
        },
    );
};
</script>
