<template>
    <Head title="Kapsule" />
    
    <AuthenticatedLayout>
        <div class=" m-8">
            <h3 class="text-xl">{{ kapsule.name }}</h3>

        <p>{{ kapsule.description }} par {{ owner.username }}</p>

        <p>Membres : {{ members.length }}</p>
        <div v-for="member in members" :key="member.id">
            - {{ member.username }}
        </div>
        - {{ owner.username }} (propriétaire)
        </div>

        

    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    kapsule: Object,
    owner: Object,
    members: Array,
});

const owner = ref(props.owner);
const kapsule = ref(props.kapsule);
const members = ref(props.members);

</script>