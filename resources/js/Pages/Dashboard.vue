<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="flex justify-end p-6">
            <button
                @click="showJoinModal = true"
                class="px-4 flex items-center gap-2 hover:bg-blue-700 hover:cursor-pointer py-2 bg-blue-600 text-white rounded"
            >
                <UserGroupIcon class="h-5 w-5" />
                Rejoindre une Kapsule
            </button>
        </div>

        <Modal :show="showJoinModal" @close="showJoinModal = false">
            <div class="p-6 bg-gray-900 text-white">
                <input
                    v-model="codeToJoin"
                    type="text"
                    placeholder="Code de partage"
                    class="border-gray-300 bg-gray-900 text-white w-full rounded-md shadow-sm"
                />
            </div>
            <button
                @click="searchWithCode(codeToJoin)"
                class="w-full bg-blue-600 text-white py-2 rounded-b-md"
            >
                Rejoindre
            </button>
        </Modal>

        <Modal :show="showAreYouSureModal" @close="showAreYouSureModal = false">
            <div class="p-6 bg-gray-900 text-white">
                <h2 class="text-xl font-bold">
                    Êtes-vous sûr de vouloir rejoindre la kapsule "{{
                        kapsuleWithCode.name
                    }}" de {{ userOfTheKapsuleWithCode.username }} ?
                </h2>
                <p class="mt-4">
                    En rejoignant cette Kapsule, vous aurez accès à son contenu
                    et pourrez collaborer avec les autres membres.
                </p>
                <div class="mt-6 flex justify-end gap-4">
                    <button
                        @click="showAreYouSureModal = false"
                        class="px-4 py-2 bg-gray-700 text-white rounded"
                    >
                        Annuler
                    </button>
                    <button
                        @click="confirmJoin()"
                        class="px-4 py-2 bg-blue-600 text-white rounded"
                    >
                        Rejoindre
                    </button>
                </div>
            </div>
        </Modal>

        <div class="flex justify-end p-6">
            <button
                @click="showModal = true"
                class="px-4 flex items-center gap-2 hover:bg-blue-700 hover:cursor-pointer py-2 bg-blue-600 text-white rounded"
            >
                <PlusIcon class="h-5 w-5" />
                {{ $t("create_kapsule") }}
            </button>
        </div>
        <Modal :show="showModal" @close="showModal = false">
            <div class="p-6 bg-gray-900 text-white">
                <h2 class="text-xl font-bold">{{ $t("create_kapsule") }}</h2>
                <form @submit.prevent="submit" class="mt-6">
                    <label for="name" class="block text-sm font-bold">{{
                        $t("name_kapsule")
                    }}</label>
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Nom de la Kapsule"
                        class="border-gray-300 bg-gray-900 text-white w-full rounded-md shadow-sm"
                    />
                    <div class="mt-4">
                        <label
                            for="description"
                            class="block text-sm font-bold"
                            >{{ $t("description_kapsule") }}</label
                        >
                        <textarea
                            v-model="form.description"
                            type="text"
                            placeholder="Description"
                            class="border-gray-300 bg-gray-900 text-white w-full h-40 rounded-md shadow-sm"
                        ></textarea>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-4 w-full hover:cursor-pointer hover flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded"
                    >
                        <FolderPlusIcon class="h-5 w-5" />
                        {{ $t("create") }}
                    </button>
                </form>
            </div>
        </Modal>

        <h2
            class="text-2xl uppercase underline decoration-blue-600 decoration-2 ml-6 text-white font-bold"
        >
            {{ $t("your_kapsules") }}
        </h2>

        <!-- Barre de recherche -->

        <div class="w-full mt-6 mb-4 px-6 flex gap-4 items-center">
            <input
                type="text"
                :placeholder="$t('search_placeholder')"
                class="bg-blue-900 border-gray-700 text-white w-full rounded-md shadow-sm px-4 py-2"
                v-model="searchQuery"
            />

            <select
                v-model="order"
                @change="changeDateOrder(order)"
                class="bg-blue-900 border-gray-700 text-white rounded-md shadow-sm px-4 py-2"
            >
                <option value="asc">{{ $t("date_asc") }}</option>
                <option value="desc">{{ $t("date_desc") }}</option>
            </select>
        </div>

        <!-- Kapsules list -->

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="kapsule in kapsules" :key="kapsule.id" class="p-6">
                <div
                    class="border border-gray-700 w-full p-5 rounded-xl dark:bg-gray-800 text-white shadow-sm hover:shadow-md transition-shadow"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="p-3 rounded-lg bg-blue-600/20 flex items-center justify-center"
                        >
                            <FolderIcon class="h-6 w-6 text-blue-400" />
                        </div>
                        <h3
                            class="font-bold text-xl tracking-tight uppercase truncate"
                        >
                            {{ kapsule.name }}
                        </h3>
                    </div>

                    <div class="mt-5">
                        <p
                            class="text-sm text-gray-400 leading-relaxed min-h-[60px] line-clamp-2 italic"
                        >
                            {{ kapsule.description || "Aucune description" }}
                        </p>

                        <hr class="my-5 border-gray-700" />

                        <div class="flex items-center justify-between mt-4">
                            <span
                                class="text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                {{ formatDate(kapsule.created_at) }}
                            </span>

                            <div
                                @click="copyToClipboard(kapsule.share_code)"
                                class="flex cursor-pointer items-center justify-between gap-3 bg-gray-900/50 border border-gray-600 px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors group"
                            >
                                <code
                                    class="font-mono text-sm font-bold text-blue-400"
                                    >{{ kapsule.share_code }}</code
                                >
                                <ClipboardDocumentIcon
                                    class="h-4 w-4 text-gray-500 group-hover:text-white"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="flex justify-center items-center gap-3 mt-6">
                <div
                    v-if="totalPages != 1"
                    v-for="pageNumber in pagesToShow"
                    :key="pageNumber"
                    :class="[
                        'flex items-center justify-center w-8 h-8 rounded-full text-white hover:bg-gray-600 cursor-pointer',
                        pageNumber === currentPage
                            ? 'bg-blue-600'
                            : 'bg-gray-700',
                    ]"
                >
                    <Link
                        :href="
                            route('dashboard', {
                                page: pageNumber,
                                q: searchQuery,
                                dateOrder: dateOrder,
                            })
                        "
                        class="w-full h-full flex items-center justify-center"
                    >
                        {{ pageNumber }}
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { trans } from "laravel-vue-i18n";
import { useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import { ref, watch, computed } from "vue";
import { debounce } from "lodash";
import {
    PlusIcon,
    FolderPlusIcon,
    FolderIcon,
    ClipboardDocumentIcon,
} from "@heroicons/vue/24/solid";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";

const showModal = ref(false);
const toast = useToast();
const showJoinModal = ref(false);

const props = defineProps({
    kapsules: Array,
    searchQuery: String,
    totalPages: Number,
    totalKapsules: Number,
    currentPage: Number,
    dateOrder: String,
    kapsuleWithCode: Object,
    userOfTheKapsuleWithCode: Object,
});

const totalKapsules = ref(props.totalKapsules);
const totaPagesNumber = ref(props.totalPages);
const currentPage = ref(props.currentPage);

const searchQuery = ref(props.searchQuery || "");
const dateOrder = ref(props.dateOrder || "desc"); // Valeur par défaut si non fournie

const order = ref(dateOrder.value);
const kapsuleWithCode = ref(props.kapsuleWithCode || {});

const form = useForm({
    name: "",
    description: "",
});

const submit = () => {
    form.post(route("kapsules.store"), {
        onSuccess: () => {
            form.reset();
            toast.success(trans("kapsule_created"));
            showModal.value = false;
        },
    });
};

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text);
    toast.success(trans("code_copied"));
}

// Fonctions de recherche avec debounce pour éviter les requêtes à chaque frappe
const searchKapsules = debounce((q) => {
    router.get(
        route("dashboard"),
        { q, page: 1, dateOrder: dateOrder.value },
        {
            preserveState: true,
            replace: true,
            onSuccess: (page) => {
                // Mettre à jour les kapsules et la pagination avec les nouvelles données
                updateFilters(page);
            },
        },
    );
}, 300);

// Watch pour déclencher la recherche à chaque changement de la requête de recherche
watch(searchQuery, (q) => {
    searchKapsules(q);
});

//Recupérer les numéros de pages à afficher dans la pagination
const pagesToShow = computed(() => {
    const side = 3;
    console.log("Current page:", currentPage.value);
    console.log("Total pages:", totaPagesNumber.value);
    const start = Math.max(currentPage.value - side, 1);
    const end = Math.min(currentPage.value + side, totaPagesNumber.value);
    let pages = [];
    for (let i = start; i <= end; i++) pages.push(i);
    console.log(pages);
    return pages;
});

// Fonction pour changer l'ordre de tri des dates
const changeDateOrder = (order) => {
    dateOrder.value = order;
    router.get(
        route("dashboard"),
        { q: searchQuery.value, dateOrder: order, page: currentPage.value },
        {
            preserveState: true,
            replace: true,
            onSuccess: (page) => {
                updateFilters(page);
            },
        },
    );
};

//Fonctyion pour mettre à jour les filtres
const updateFilters = (page) => {
    totalKapsules.value = page.props.totalKapsules;
    totaPagesNumber.value = page.props.totalPages;
    currentPage.value = page.props.currentPage;
};

const codeToJoin = ref("");
const showAreYouSureModal = ref(false);

const searchWithCode = (code) => {
    if (code.trim() === "") {
        toast.error("Veuillez entrer un code de partage valide.");
        return;
    }
    router.get(
        route("dashboard"),
        { codeToJoin: code },
        {
            preserveState: true,
            replace: true,
            onSuccess: (page) => {
                if (
                    page.props.kapsuleWithCode &&
                    Object.keys(page.props.kapsuleWithCode).length > 0
                ) {
                    kapsuleWithCode.value = page.props.kapsuleWithCode;

                    toast.success("Kapsule trouvée !");
                    showAreYouSureModal.value = true;
                    showJoinModal.value = false;
                    //TODO: Rediriger vers la page de la Kapsule trouvée
                } else {
                    toast.error("Aucune Kapsule trouvée avec ce code.");
                }
            },
        },
    );
};
const confirmJoin = () => {
    if (!kapsuleWithCode.value.id) {
        toast.error("Aucune Kapsule sélectionnée !");
        return;
    }
    router.post(
        route("kapsules.join", kapsuleWithCode.value.id),
        {},
        {
            onSuccess: () => {
                toast.success(
                    `Vous avez rejoint la Kapsule "${kapsuleWithCode.name}" !`,
                );
                showAreYouSureModal.value = false;
            },
            onError: (errors) => {
                toast.error("Impossible de rejoindre cette Kapsule.");
            },
        },
    );
};
</script>
