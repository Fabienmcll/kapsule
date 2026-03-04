<template>
    <Modal v-if="member && kapsule" :show="show" @close="emit('close')">
        <div class="p-6 bg-gray-800 rounded-lg">
            <h2 class="text-xl mb-4">
                {{ text }}
            </h2>
            <div class="mt-6 flex justify-end gap-4">
                <button
                    @click="emit('close')"
                    class="px-4 py-2 bg-gray-700 text-white rounded"
                >
                    Annuler
                </button>
                <button
                    @click="acceptRequest(member.id)"
                    class="px-4 py-2 bg-green-600 text-white rounded"
                >
                    {{ actionText }}
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
    text: String,
    actionText: String,
});

const page = usePage();

const kapsule = ref(props.kapsule);
const members = ref(props.allMembers);
const text = ref(props.text);
const actionText = ref(props.actionText);

const toast = useToast();

const emit = defineEmits(["close", "accepted"]);

const acceptRequest = (memberId) => {
    emit("accepted", memberId);
};
</script>
