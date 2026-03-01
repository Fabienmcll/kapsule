<template>
    <Head title="Kapsule" />
    
    <AuthenticatedLayout>
        <div class=" m-8">
            <h3 class="text-xl">{{ kapsule.name }}</h3>

        <p>{{ kapsule.description }} par {{ owner.username }}</p>

        <p>Membres : {{ members.length + 1}}</p>
        <div v-for="member in members" :key="member.id">
            - {{ member.username }}
        </div>
        - {{ owner.username }} (propriétaire)
        </div>

        <FileUpload :kapsule-id="kapsule.id" />

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
    <div v-for="item in media" :key="item.id" class="rounded-lg overflow-hidden bg-gray-800 border border-gray-700">
        
        <img v-if="item.mime_type.startsWith('image/')" 
             :src="item.thumb || item.url" 
             class="w-full h-48 object-cover shadow-sm" />

        <video v-else-if="item.mime_type.startsWith('video/')" 
               class="w-full h-48 object-cover">
            <source :src="item.url" :type="item.mime_type">
        </video>
        
    </div>
</div>

    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FileUpload from '@/Components/FileUpload.vue';

const props = defineProps({
    kapsule: Object,
    owner: Object,
    members: Array,
    media: Array,
});

const owner = ref(props.owner);
const kapsule = ref(props.kapsule);
const members = ref(props.members);

</script>