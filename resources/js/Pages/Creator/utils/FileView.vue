<template>
    <div class="intro-y col-span-6 sm:col-span-4 md:col-span-3 2xl:col-span-2">
        <div class="file box rounded-md px-5 pt-8 pb-5 sm:px-5 relative zoom-in">
            <a v-if="type == 'empty'" href="javascript:;" @dblclick="folderSelected" class="w-3/5 file__icon file__icon--empty-directory mx-auto"></a>
            <a v-else-if="type == 'folder'" href="javascript:;" @dblclick="folderSelected" class="text-yellow-900 w-3/5 file__icon file__icon--directory mx-auto"></a>
            <a v-else-if="type == 'image'" href="javascript:;" class="w-3/5 file__icon file__icon--image mx-auto">
                <div class="file__icon--image__preview image-fit">
                    <img :alt="name()" :src="url" data-action="zoom"/>
                </div>
            </a>
            <a v-else :href="type === 'pdf' && actions ? url : 'javascript:;'" :target="type === 'pdf' && actions ? '_blank' : ''" class="w-3/5 file__icon file__icon--file mx-auto">
                <div class="file__icon__file-name">
                    {{ type }}
                </div>
            </a>
            <a href="javascript:;" class="block font-medium mt-4 text-center truncate" v-if="!isUpdatingName" @dblclick="updatingName">{{ name() }}</a>
            <input type="text" class="block font-medium mt-4 text-center" @keydown.enter="changeName" @keydown.esc="cancelChgName" @blur="cancelChgName" style="max-width: -webkit-fill-available" v-if="isUpdatingName" placeholder="name" v-model="newName" ref="inputName" />
            <div class="text-slate-500 text-xs text-center mt-0.5">
                {{ file.size }}
            </div>
            <div class="absolute top-0 right-0 mr-2 mt-3 dropdown ml-auto" v-if="actions">
                <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown">
                    <MoreVerticalIcon class="w-5 h-5 text-slate-500" />
                </a>
                <div class="dropdown-menu w-40">
                    <ul class="dropdown-content">
                        <!-- <li>
                            <a href="" class="dropdown-item"> <UsersIcon class="w-4 h-4 mr-2" /> Share File </a>
                        </li> -->
                        <li>
                            <a href="javascript:;" class="dropdown-item" @click="updatingName"> <Edit3Icon class="w-4 h-4 mr-2" /> Cambiar nombre </a>
                            <a :href="route('folder.download', file.id)" class="dropdown-item" v-if="canDownload"> <DownloadCloudIcon class="w-4 h-4 mr-2" /> Descargar </a>
                            <a href="javascript:;" class="dropdown-item" @click="emitDelete()" v-if="!folderHasChildren"> <TrashIcon class="w-4 h-4 mr-2" /> Borrar </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
//import { MoreVerticalIcon, TrashIcon, DownloadCloudIcon, Edit3Icon } from "@zhuowenli/vue-feather-icons/dist/vue-feather-icons.cjs";
import {MoreVerticalIcon, TrashIcon, DownloadCloudIcon, Edit3Icon} from "@/Helpers/Partials/Icons/AppIcons.js";
import { files, folder } from "./Finder.js";

const originalName = ref(null);
const newName = ref(null);
const isUpdatingName = ref(false);
</script>
<script>
import { defineComponent, ref } from "vue";

export default defineComponent({
    emits: ["deleted", "folderSelected"],
    props: {
        file: Object,
        actions: {
            default: false,
        },
        forceDelete: {
            default: false
        }
    },
    // mounted() {
    //     this.originalName = this.name();
    // },
    methods: {
        emitDelete() {
            this.cApp();
            if (this.forceDelete) {
                files.delete(this.file);
            }
            this.$emit("deleted");
        },
        folderSelected() {
            this.$emit("folderSelected", this.file);
        },
        async changeName() {
            if (this.canDownload) {
                await files.updateName(this.file, this.newName.split(".")[0]).then((x) => {
                    this.file.file_name = x.data;
                    this.originalName = x.data;
                });
            } else {//folder
                await folder.updateName(this.file, this.newName).then((x) => {
                    this.file.name = x.data.name;
                    this.originalName = x.data.name;
                });
            }
            this.cApp();
            this.newName = null;
            this.isUpdatingName = false;
        },
        updatingName() {
            if (!this.actions) return;
            this.originalName = this.name();
            this.newName = this.originalName;
            this.isUpdatingName = true;
            this.cApp();
            this.$nextTick(() => this.$refs.inputName.focus());
        },
        cApp() {
            document.getElementById("app").click();
        },
        name() {
            return this.file.file_name ?? this.file.name;
        },
        cancelChgName() {
            this.isUpdatingName = false;
            this.file.file_name = this.originalName;
            //console.log(this.originalName);
        },
    },
    computed: {
        type() {
            const mime = this.file.type ?? this.file.mime_type;
            if (!mime) {
                if (this.folderHasChildren) return "folder";
                return "empty";
            }
            if (mime.indexOf("image") > -1) return "image";
            if (mime.indexOf("pdf") > -1) return "pdf";
            if (mime.indexOf("excel") > -1) return "xls";
            if (mime.indexOf("csv") > -1) return "csv";
            if (mime.indexOf("spreadsheet") > -1) return "xls";
            if (mime.indexOf("word") > -1) return "doc";
            if (mime.indexOf("zip") > -1) return "zip";
            if (mime.indexOf("text/plain") > -1) return "txt";
        },
        folderHasChildren() {
            const childs = !!this.file.children ? this.file.children.length > 0 : false;
            return this.file.file_count > 0 || this.file.children_count > 0 || childs;
        },
        canDownload() {
            return ["empty", "folder"].indexOf(this.type) === -1;
        },
        url() {
            if (["image", "pdf"].indexOf(this.type) == -1) return;
            return this.file.url ?? (this.file instanceof File ? URL.createObjectURL(this.file) : null);
        },
    },
});
</script>
