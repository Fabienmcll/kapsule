<template>
    <Modal v-if="member && kapsule" :show="show" @close="emit('close')">
        <div class="p-8 bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl">
            <div class="flex items-center gap-4 mb-6">
                <div class="h-12 w-12 rounded-full bg-green-900/30 flex items-center justify-center text-green-400 border border-green-500/20">
                    <CheckIcon class="h-6 w-6" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">
                        {{ $t('accept_request_title', { name: member.username }) }}
                    </h3>
                    <p class="text-gray-400 text-sm mt-1">
                        {{ kapsule.name }}
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button
                    @click="emit('close')"
                    class="px-6 py-2.5 bg-gray-800 hover:bg-gray-700 text-white rounded-xl font-medium transition-colors border border-gray-700"
                >
                    {{ $t('cancel') }}
                </button>
                <button
                    @click="acceptRequest(member.id)"
                    class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-colors shadow-lg shadow-green-900/20"
                >
                    {{ $t('accept') }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from "@/Components/Modal.vue";
import { router, usePage } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { CheckIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    show: Boolean,
    member: Object,
    kapsule: Object,
    allMembers: Array,
});

const toast = useToast();
const emit = defineEmits(["close", "accepted"]);

const acceptRequest = (memberId) => {
    router.post(
        `/kapsules/${props.kapsule.id}/accept/${memberId}`,
        {},
        {
            onSuccess: (page) => {
                const flash = page.props.flash;
                if (flash.success) {
                    toast.success(flash.success);
                    emit("close");
                    emit("accepted", memberId);
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
