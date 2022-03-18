<template>
    <!-- BEGIN: Show Modal Toggle -->
    <button id="programmatically-show-modal" class="btn btn-primary shadow-md mr-2" @click="showProgrammaticallyShowModal()">Subir archivos</button>
    <!-- END: Show Modal Toggle -->
    <!-- BEGIN: Modal Content -->

    <div id="modal-upload-finder" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" @dragover="files.dragAnddrop.dragover" @dragleave="files.dragAnddrop.dragleave" @drop="filesToUpload = files.dragAnddrop.drop($event)">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Subir Archivos</h2>
                    <button class="btn btn-outline-secondary hidden sm:flex">
                        <Explore @change="addFiles" />
                    </button>
                    <div class="dropdown sm:hidden">
                        <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown">
                            <MoreHorizontalIcon class="w-5 h-5 text-slate-500" />
                        </a>
                        <div class="dropdown-menu w-40">
                            <ul class="dropdown-content">
                                <li>
                                    <a href="javascript:;" class="dropdown-item">
                                        <Explore @change="addFiles" />
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body intro-y grid grid-cols-12 gap-3 sm:gap-6 mt-5">
                    <FileView :file="file"
                    v-for="(file, index) in filesToUpload" :key="index" @click="filesToUpload.splice(index, 1)"></FileView>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Cancelar</button>
                    <button type="button" class="btn btn-primary w-20" @click="send" :disabled="sending">Enviar</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>
    <!-- END: Modal Content -->
</template>

<script>
export default defineComponent({
    data() {
        return {
            filesToUpload: [],
            sending: false,
        };
    },
    props: ["selectedFolder"],
    emits: ["freshFiles"],
    methods: {
        async send() {
            this.sending = true;
            await files.upload(this.filesToUpload, this.selectedFolder).then((x) => {
                this.$emit("freshFiles", x.data);
                this.filesToUpload = [];
                this.modal().hide();
            });
            this.sending = false;
        },
        addFiles(e) {
            this.filesToUpload = [...e.target.files];
        },
    },
});
</script>

<script setup>
//import { MoreHorizontalIcon, FileIcon } from "@zhuowenli/vue-feather-icons/dist/vue-feather-icons.cjs";
import {MoreHorizontalIcon, FileIcon } from "@/Helpers/Partials/Icons/AppIcons.js";
import { files, Explore } from "./Finder.js";
import FileView from "./FileView.vue";
import { defineComponent } from "vue";

const modal = () => {
    const el = document.querySelector("#modal-upload-finder");
    return tailwind.Modal.getOrCreateInstance(el);
};
function showProgrammaticallyShowModal() {
    modal().show();
}
</script>
