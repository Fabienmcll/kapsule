<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
         <h2 class="text-2xl mt-4 uppercase underline decoration-blue-600 decoration-2 ml-6 text-white font-bold">
            {{ $t("your_kapsules") }}
        </h2>

        <div class="flex justify-between items-end p-6 gap-4">
            <!-- Barre de recherche et Date (Gauche) -->
            <div class="flex gap-6 items-center flex-1">
                <div class="flex items-center gap-2 flex-1 max-w-md">
                    <label for="search" class="text-white font-bold whitespace-nowrap">{{ $t("search") }} :</label>
                    <input
                        id="search"
                        type="text"
                        :placeholder="$t('search_placeholder')"
                        class="bg-blue-900 border-gray-700 text-white w-full rounded-md shadow-sm px-4 py-2"
                        v-model="searchQuery"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <label for="order" class="text-white font-bold whitespace-nowrap">{{ $t("sort_by") }} :</label>
                    <select
                        id="order"
                        v-model="order"
                        @change="changeDateOrder(order)"
                        class="bg-blue-900 border-gray-700 text-white rounded-md shadow-sm px-4 py-2"
                    >
                        <option value="asc">{{ $t("date_asc") }}</option>
                        <option value="desc">{{ $t("date_desc") }}</option>
                    </select>
                </div>
            </div>

            <!-- Boutons d'action (Droite) -->
            <div class="flex flex-col w-68">
                <button
                    @click="showJoinModal = true"
                    class="px-4 flex items-center gap-2 hover:bg-blue-700 hover:cursor-pointer py-2 bg-blue-600 text-white rounded"
                >
                    <UserGroupIcon class="h-5 w-5" />
                    {{ $t("join_kapsule") }}
                </button>
                <button
                    @click="showModal = true"
                    class="px-4 mt-4 flex items-center gap-2 hover:bg-green-700 hover:cursor-pointer py-2 bg-green-600 text-white rounded"
                >
                    <PlusIcon class="h-5 w-5" />
                    {{ $t("create_kapsule") }}
                </button>
            </div>
        </div>

        <!-- Modal pour rejoindre une kapsule -->
        <JoinKapsuleModal
            :show="showJoinModal"
            @close="showJoinModal = false"
            @success="handleJoinSuccess"
        />

        <!-- Modal de confirmation pour rejoindre la kapsule -->
        <ConfirmJoinKapsuleModal
            :show="showAreYouSureModal"
            :kapsule-with-code="kapsuleWithCode"
            :user-of-the-kapsule-with-code="userOfTheKapsuleWithCode"
            @close="showAreYouSureModal = false"
        />

        <!-- Modal de création de la kapsule -->
        <CreateKapsuleModal :show="showModal" @close="showModal = false" />

        <!-- Kapsules list -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="kapsule in kapsules" :key="kapsule.id" class="p-6">
            <Link
                    :href="route('kapsule.load', kapsule.id)"
                    class="block h-full">
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
                                @click.stop.prevent="copyToClipboard(kapsule.share_code)"
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

            </Link>
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
import { Head, Link, usePage } from "@inertiajs/vue3";
import { trans } from "laravel-vue-i18n";
import Modal from "@/Components/Modal.vue";
import { ref, watch, computed } from "vue";
import { debounce } from "lodash";
import {
    PlusIcon,
    FolderIcon,
    ClipboardDocumentIcon,
    UserGroupIcon
} from "@heroicons/vue/24/solid";
import CreateKapsuleModal from "./Partials/CreateKapsuleModal.vue";
import JoinKapsuleModal from "./Partials/JoinKapsuleModal.vue";
import ConfirmJoinKapsuleModal from "./Partials/ConfirmJoinKapsuleModal.vue";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";

const page = usePage();

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
const userOfTheKapsuleWithCode = ref(props.userOfTheKapsuleWithCode || null);

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

const showAreYouSureModal = ref(false);
const handleJoinSuccess = (kapsule) => {
    kapsuleWithCode.value = kapsule;
    showAreYouSureModal.value = true;
    showJoinModal.value = false;
};
</script>
