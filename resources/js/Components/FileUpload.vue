<template>
    <div class="p-4">
        <file-pond
            name="media"
            ref="pond"
            label-idle="Glissez vos photos/vidéos ici..."
            accepted-file-types="image/*,video/*,image/heic,image/heif"

            :allow-multiple="true"
            :allow-directory-upload="false"
            :item-insert-interval="200"
            :max-parallel-uploads="1"
            :allow-revert="false"

            @processfile="onProcessFile"

            :server="{
        process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
            const formData = new FormData();
            formData.append(fieldName, file, file.name);
            formData.append('kapsule_id', props.kapsuleId);

            const controller = new AbortController();

            axios.post(route('media.upload'), formData, {
                signal: controller.signal,
                onUploadProgress: (e) => {
                    progress(e.lengthComputable, e.loaded, e.total);
                }
            }).then(response => {
                load(response.data);
            }).catch(thrown => {
                if (axios.isCancel(thrown)) {
                    abort();
                } else if (thrown.response && thrown.response.status === 419) {
                    window.location.reload();
                } else {
                    error(thrown.response?.data?.message || thrown.message);
                }
            });

            return {
                abort: () => {
                    controller.abort();
                    abort();
                }
            };
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
import axios from "axios";

const FilePond = vueFilePond(FilePondPluginImagePreview, FilePondPluginFileValidateType);
const pond = ref(null);

const props = defineProps({
    kapsuleId: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['upload-success']);

const onProcessFile = (error, file) => {
    if (!error) {
        // On attend 2 secondes Puis on retire le fichier de la liste FilePond
        setTimeout(() => {
            pond.value.removeFile(file.id);
            emit('upload-success');
        }, 2000);
    }
};

</script>
