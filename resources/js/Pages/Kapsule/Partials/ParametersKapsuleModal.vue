<template>
    <Modal :show="show" @close="emit('close')">
        <div class="p-8 bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-800">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <Cog6ToothIcon class="h-6 w-6 text-blue-400" />
                    {{ $t('parameters') }}
                </h3>
                <button @click="emit('close')" class="text-gray-500 hover:text-white transition-colors">
                    <XMarkIcon class="h-6 w-6" />
                </button>
            </div>

            <!-- Edit Kapsule Section -->
            <section class="mb-10">
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-4">
                    {{ $t('edit_kapsule') }}
                </h4>
                <form @submit.prevent="submitUpdate" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1" for="name">
                            {{ $t('name_kapsule') }}
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            :placeholder="$t('name_placeholder')"
                            class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all placeholder:text-gray-600"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1" for="description">
                            {{ $t('description_kapsule') }}
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            :placeholder="$t('description_placeholder')"
                            class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all placeholder:text-gray-600 resize-none"
                        ></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-800 disabled:opacity-50 text-white rounded-xl font-bold transition-all shadow-lg shadow-blue-900/20"
                        >
                            <CheckIcon v-if="!form.processing" class="h-4 w-4" />
                            <div v-else class="h-4 w-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                            {{ $t('save_changes') }}
                        </button>
                    </div>
                </form>
            </section>

            <!-- Banned Users Section -->
            <section>
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-4">
                    {{ $t('banned_users') }}
                </h4>

                <div v-if="bannedUsers.length === 0" class="py-8 text-center bg-gray-800/30 rounded-xl border border-dashed border-gray-700">
                    <UsersIcon class="h-8 w-8 text-gray-700 mx-auto mb-2" />
                    <p class="text-gray-500 text-sm">
                        {{ $t('no_banned_users') }}
                    </p>
                </div>

                <ul v-else class="space-y-2">
                    <li v-for="user in bannedUsers" :key="user.id"
                        class="flex items-center justify-between p-3 bg-gray-800/50 rounded-xl border border-gray-800 hover:border-gray-700 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-red-900/30 flex items-center justify-center text-red-400 text-xs font-bold border border-red-500/10">
                                {{ user.username.charAt(0).toUpperCase() }}
                            </div>
                            <span class="text-gray-300 font-medium text-sm">{{ user.username }}</span>
                        </div>
                        <button
                            @click="promptUnban(user.id)"
                            class="px-3 py-1.5 bg-blue-600/10 hover:bg-blue-600 text-blue-400 hover:text-white rounded-lg text-xs font-bold transition-all border border-blue-600/20"
                        >
                            {{ $t('unban') }}
                        </button>
                    </li>
                </ul>
            </section>
        </div>

        <AreYouSureModal
            :show="showAreYouSureModal"
            :text="$t('confirm_unban')"
            :actionText="$t('unban')"
            @close="showAreYouSureModal = false"
            @accepted="unbanUser(selectedUserId)"
        />
    </Modal>
</template>

<script setup>
import { ref, watch } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import Modal from "@/Components/Modal.vue";
import AreYouSureModal from "@/Pages/Kapsule/Partials/AreYouSureModal.vue";
import {
    Cog6ToothIcon,
    XMarkIcon,
    UsersIcon,
    CheckIcon,
    NoSymbolIcon
} from "@heroicons/vue/24/solid";

const props = defineProps({
    show: Boolean,
    kapsule: Object,
    bannedUsers: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(["close", "unbanned"]);

const toast = useToast();
const showAreYouSureModal = ref(false);
const selectedUserId = ref(null);

const form = useForm({
    name: props.kapsule?.name || "",
    description: props.kapsule?.description || "",
});

watch(() => props.kapsule, (newKapsule) => {
    if (newKapsule) {
        form.name = newKapsule.name;
        form.description = newKapsule.description;
    }
}, { immediate: true });

const submitUpdate = () => {
    if (!form.name.trim()) {
        toast.error("Le nom de la Kapsule est obligatoire.");
        return;
    }

    form.post(route("kapsules.modify", { kapsule: props.kapsule.id }), {
        preserveScroll: true,
        onSuccess: () => {
             toast.success("Kapsule mise à jour !");
             emit("close");
        },
        onError: () => {
            toast.error("Une erreur est survenue lors de la mise à jour.");
        }
    });
};

const promptUnban = (userId) => {
    selectedUserId.value = userId;
    showAreYouSureModal.value = true;
};

const unbanUser = async (userId) => {
    router.post(
        route("kapsules.unban", { kapsule: props.kapsule.id, member: userId }),
        {},
        {
            onSuccess: (page) => {
                const flash = page.props.flash;
                if (flash.success) {
                    toast.success(flash.success);
                    showAreYouSureModal.value = false;
                    emit("unbanned", userId);
                } else if (flash.error) {
                    toast.error(flash.error);
                }
            },
            onError: () => {
                toast.error("Une erreur est survenue.");
            }
        }
    );
};
</script>
