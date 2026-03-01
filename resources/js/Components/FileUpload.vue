<template>
    <div class="p-4">
        <file-pond 
            name="media"
            ref="pond"
            label-idle="Glissez vos photos/vidéos ici..."
            :allow-multiple="true"
            :allow-directory-upload="true"
            :allow-revert="false"
    @processfile="onProcessFile"
            accepted-file-types="image/*,video/*"
            :server="{
                process: {
                    url: route('media.upload'),
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    ondata: (formData) => {
                        formData.append('kapsule_id', props.kapsuleId);
                        return formData;
                    }
                }
            }"
        />
    </div>
</template>

<script setup>
import { ref } from "vue";
import vueFilePond from "vue-filepond";
import "filepond/dist/filepond.min.css";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";

const FilePond = vueFilePond(FilePondPluginImagePreview, FilePondPluginFileValidateType);
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
const pond = ref(null);

const props = defineProps({
    kapsuleId: {
        type: Number,
        required: true
    }
});


const onProcessFile = (error, file) => {
    if (!error) {
        // On attend 2 secondes Puis on retire le fichier de la liste FilePond
        setTimeout(() => {
            pond.value.removeFile(file.id);
        }, 2000);
    }
};

</script>