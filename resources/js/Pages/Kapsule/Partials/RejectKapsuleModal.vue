<template>
    <Modal v-if="member && kapsule" :show="show" @close="emit('close')">
        <div class="p-6 bg-gray-800 rounded-lg">
            <h2 class="text-xl mb-4">
                Rejeter la demande d'adhésion de {{ member.username }} à
                {{ kapsule.name }} ?
            </h2>
            <div class="mt-6 flex justify-end gap-4">
                <button
                    @click="emit('close')"
                    class="px-4 py-2 bg-gray-700 text-white rounded"
                >
                    Annuler
                </button>
                <button
                    @click="rejectRequest(member.id)"
                    class="px-4 py-2 bg-red-600 text-white rounded"
                >
                    Rejeter
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from "@/Components/Modal.vue";
import { router, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import { useToast } from "vue-toastification";

const props = defineProps({
    show: Boolean,
    member: Object,
    kapsule: Object,
    allMembers: Array,
});

const page = usePage();

const kapsule = ref(props.kapsule);
const members = ref(props.allMembers);
const toast = useToast();

const emit = defineEmits(["close", "rejected"]);

const rejectRequest = (memberId) => {
    router.post(
        `/kapsules/${kapsule.value.id}/reject/${memberId}`,
        {},
        {
            onSuccess: () => {
                const member = members.value.find((m) => m.id === memberId);
                if (member) {
                    members.value = members.value.filter(
                        (m) => m.id !== memberId,
                    );
                }
            },
            onFinish: () => {
                const flash = usePage().props.flash;
                if (flash.success) {
                    toast.success(flash.success);
                }
                emit("close");
                emit("rejected", memberId);
            },
        },
    );
};
</script>
