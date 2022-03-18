<script setup>
//import { ChevronLeftIcon, FolderPlusIcon, FolderIcon, FileIcon, UserIcon, UsersIcon, TrashIcon, PlusIcon, SearchIcon, ChevronDownIcon, SettingsIcon, ChevronsLeftIcon } from "@zhuowenli/vue-feather-icons/dist/vue-feather-icons.cjs";
import AppLayout from "@/Layouts/Authenticated.vue";
import { files, folder } from "./utils/Finder.js";
import ModalUpload from "./utils/ModalUpload.vue";
import Modal from "@/Helpers/Partials/Modal/Modal.vue";
import { defineComponent } from "vue";
import FileView from "@/Pages/Creator/utils/FileView.vue";
import dom from "@left4code/tw-starter/dist/js/dom";
import { FinderIcon, ChevronDownIcon, FolderIcon } from "@/Helpers/Partials/Icons/AppIcons.js";

const FolderCo = {
    name: "FolderCo",
    props: ["folder", "fn", "selected"],
    components: { ChevronDownIcon, FolderIcon },
    template: `
     <div class="flex items-baseline pl-1"  :class="{
                    'font-bold underline': folder.activeDropdown,
                    'bg-blue-700 text-white': !!selected && selected.id === folder.id,
                }">
            <div
                v-if="!!folder.children.length"
                class="transition ease-in duration-100 ml-auto w-5 h-5 cursor-pointer"
                :class="{
                    'transform rotate-180': folder.activeDropdown,
                }"
                @click.stop="linkTo(folder)"
            >
                <ChevronDownIcon />
            </div>
            <button class="flex flex-grow items-center px-3 py-2 mt-2 rounded-md" @click="fn(folder)"><FolderIcon class="w-4 h-4 mr-2" /> {{ folder.name }}</button>
    </div>
    <transition @enter="enter" @leave="leave">
        <div class="pl-2 border-l">
            <FolderCo :selected="selected" :folder="fo" :fn="fn" v-if="folder.children && folder.activeDropdown" v-for="(fo, index) in folder.children" :key="index"></FolderCo>
        </div>
    </transition>
    `,
    methods: {
        linkTo(menu) {
            if (!!menu.children.length) {
                menu.activeDropdown = !menu.activeDropdown;
            }
        },
        enter(el, done) {
            dom(el).slideDown(300);
        },
        leave(el, done) {
            dom(el).slideUp(300);
        },
    },
};
</script>
<script>
export default defineComponent({
    mounted() {
        folder.all().then((x) => {
            this.folders = x.data;
            this.showingFiles = x.data;
        });
    },
    data() {
        return {
            folders: [],
            showingFiles: [],
            selectedFolder: null,
            newFolderName: null,
            hideFiles: false,
        };
    },
    methods: {
        back() {
            if (this.selectedFolder) {
                // this.hideFiles = true;
                if (!isNaN(this.selectedFolder.root_index)) {
                    //console.log("estoy aqui");
                    this.showingFiles = this.folders;
                    this.selectedFolder = null;
                    this.hideFiles = false;
                    return;
                }
                var lastI = this.selectedFolder.root_index.lastIndexOf(".");
                const root = lastI > -1 ? this.selectedFolder.root_index.substring(0, lastI).replace(/\.children(?!.*\.children)/i, "") : this.selectedFolder.root_index;
                //console.log(root, _.get(this.folders, root), this.selectedFolder, !Number.isNaN(root));

                this.getFilesFromFolder(_.get(this.folders, root));
                // this.hideFiles = false;
            }
        },
        async getFilesFromFolder(f) {
            if (f.id === (!!this.selectedFolder ? this.selectedFolder.id : null)) return;
            const selF = JSON.parse(JSON.stringify(f));
            if (!f.root) {
                if (!!this.selectedFolder) {
                    f = this.selectedFolder.children[this.selectedFolder.children.findIndex((x) => f.id == x.id)];
                }
            }
            // const reg = new RegExp("\\/" + selF.name + "(?!.*\/" + selF.name + ")"); // /\.children(?!.*\.children)/i;
            // if (f.root.search(reg) === -1) {
            //     const bla = new RegExp("\\/" + f.name + "(?!.*\/" + f.name + ")");
            //     f.root.replace(f.name, selF.name);
            //     console.log(JSON.parse(JSON.stringify(f.root)), selF, bla, reg, f.root.search(bla));
            //     f.name = selF.name;
            // }
            this.selectedFolder = f;
            //this.hideFiles = true;
            await folder.files(f).then((x) => (this.showingFiles = x.data));
            //this.hideFiles = false;
        },
        updateFiles(files) {
            var dif = _.differenceWith(files, this.showingFiles, _.isEqual);
            dif.map((x) => this.showingFiles.push(x));
        },
        async deleted(file, index) {
            if (!!file.name) {
                await folder.delete(file).then((x) => {
                    const root = this.selectedFolder ? this.selectedFolder.root_index : !!file.root_index ? file.root_index : null;
                    if (root) {
                        const parentFolder = _.get(this.folders, root);
                        if (parentFolder) {
                            if (parentFolder.children) {
                                const ind = parentFolder.children.findIndex((x) => x.id == file.id);
                                if (ind > -1) parentFolder.children.splice(ind, 1);
                            }
                        }
                    }
                });
            } else {
                await files
                    .delete(file)
                    .then((x) => {
                        //this.showingFiles.splice(index, 1);
                        //this.hideFiles = false;
                    })
                    .catch((x) => console.error(x));
            }
            this.showingFiles.splice(index, 1);
        },
        newFolder() {
            this.folder.new(this.selectedFolder, this.newFolderName).then((x) => {
                this.newFolderName = null;
                if (!!this.selectedFolder) {
                    var newFolder = x.data;
                    const ind = this.selectedFolder.children.push(newFolder);
                    newFolder.root = this.selectedFolder.root + "/" + newFolder.name;
                    newFolder.root_index = this.selectedFolder.root_index + "." + ind;
                    !!this.showingFiles ? this.showingFiles.push(newFolder) : null;
                } else {
                    this.folders = x.data;
                    this.showingFiles = x.data;
                }
            });
        },
        focusInput() {
            this.$nextTick(() => {
                this.$refs["inputNameFolder"].focus();
            });
        },
    },
});
</script>
<template>
    <AppLayout>
        <div class="grid grid-cols-12 gap-6 mt-8">
            <div class="col-span-12 lg:col-span-3 2xl:col-span-2">
                <h2 class="intro-y text-lg font-medium mr-auto mt-2 flex justify-between">
                    Finder
                    <div class="w-full sm:w-auto text-blue-900 cursor-pointer" v-if="!!selectedFolder" @click="back">{{ "<-" }} Atras</div>
                </h2>
                <!-- BEGIN: File Manager Menu -->
                <div class="intro-y box p-5 mt-6">
                    <div class="mt-1">
                        <FolderCo :selected="selectedFolder" :folder="f" :fn="getFilesFromFolder" v-for="(f, index) in folders" :key="index"></FolderCo>
                    </div>
                </div>
                <!-- END: File Manager Menu -->
            </div>
            <div class="col-span-12 lg:col-span-9 2xl:col-span-10">
                <!-- BEGIN: File Manager Filter -->
                <div class="intro-y flex flex-col-reverse sm:flex-row items-center">
                    <div class="w-full sm:w-auto relative mr-auto mt-3 sm:mt-0">
                        <FinderIcon.SearchIcon class="w-4 h-4 absolute my-auto inset-y-0 ml-3 left-0 z-10 text-slate-500" />
                        <input type="text" class="form-control w-full sm:w-64 box px-10" placeholder="Search files" />
                        <div class="inbox-filter dropdown absolute inset-y-0 mr-3 right-0 flex items-center" data-tw-placement="bottom-start">
                            <FinderIcon.ChevronDownIcon class="dropdown-toggle w-4 h-4 cursor-pointer text-slate-500" role="button" aria-expanded="false" data-tw-toggle="dropdown" />
                            <div class="inbox-filter__dropdown-menu dropdown-menu pt-2">
                                <div class="dropdown-content">
                                    <div class="grid grid-cols-12 gap-4 gap-y-3 p-3">
                                        <div class="col-span-6">
                                            <label for="input-filter-1" class="form-label text-xs">File Name</label>
                                            <input id="input-filter-1" type="text" class="form-control flex-1" placeholder="Type the file name" />
                                        </div>
                                        <div class="col-span-6">
                                            <label for="input-filter-2" class="form-label text-xs">Shared With</label>
                                            <input id="input-filter-2" type="text" class="form-control flex-1" placeholder="example@gmail.com" />
                                        </div>
                                        <div class="col-span-6">
                                            <label for="input-filter-3" class="form-label text-xs">Created At</label>
                                            <input id="input-filter-3" type="text" class="form-control flex-1" placeholder="Important Meeting" />
                                        </div>
                                        <div class="col-span-6">
                                            <label for="input-filter-4" class="form-label text-xs">Size</label>
                                            <select id="input-filter-4" class="form-select flex-1">
                                                <option>10</option>
                                                <option>25</option>
                                                <option>35</option>
                                                <option>50</option>
                                            </select>
                                        </div>
                                        <div class="col-span-12 flex items-center mt-3">
                                            <button class="btn btn-secondary w-32 ml-auto">Create Filter</button>
                                            <button class="btn btn-primary w-32 ml-2">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full sm:w-auto flex-grow p-4 font-bold">Folder: {{ !!selectedFolder ? selectedFolder.root : null }}</div>
                    <div class="w-full sm:w-auto flex">
                        <ModalUpload @freshFiles="updateFiles" :selectedFolder="selectedFolder"></ModalUpload>
                        <div class="dropdown">
                            <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                                <span class="w-5 h-5 flex items-center justify-center">
                                    <FinderIcon.PlusIcon class="w-4 h-4" />
                                </span>
                            </button>
                            <div class="dropdown-menu w-40">
                                <ul class="dropdown-content">
                                    <li>
                                        <!-- <a href="javascript:;" class="dropdown-item"> <FileIcon class="w-4 h-4 mr-2" /> Nueva carpeta </a> -->
                                        <Modal
                                            id="new-folder-modal"
                                            :toggle="{
                                                is: 'a',
                                                class: 'dropdown-item cursor-pointer',
                                            }"
                                            @afterShow="focusInput"
                                        >
                                            <template #toggle><FinderIcon.FolderPlusIcon class="w-4 h-4 mr-2" /> Nueva carpeta</template>
                                            <template #content="{ modal }">
                                                <label for="crud-form-1" class="form-label">Nombre del folder</label>
                                                <input
                                                    id="crud-form-1"
                                                    v-model="newFolderName"
                                                    type="text"
                                                    @keydown.enter="
                                                        newFolder();
                                                        modal().hide();
                                                    "
                                                    class="form-control w-full"
                                                    placeholder="Nombre del folder"
                                                    ref="inputNameFolder"
                                                />
                                                <button
                                                    class="btn btn-primary shadow-md mr-2 w-full"
                                                    @click="
                                                        newFolder();
                                                        modal().hide();
                                                    "
                                                >
                                                    Guardar
                                                </button>
                                            </template>
                                        </Modal>
                                    </li>
                                    <!-- <li>
                                        <a href="" class="dropdown-item"> <SettingsIcon class="w-4 h-4 mr-2" /> Settings </a>
                                    </li> -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: File Manager Filter -->
                <!-- BEGIN: Directory & Files -->
                <div class="intro-y grid grid-cols-12 gap-3 sm:gap-6 mt-5">
                    <template v-if="!hideFiles">
                        <FileView :file="file" @deleted="deleted(file, index)" @folderSelected="getFilesFromFolder($event)" v-for="(file, index) in showingFiles" :key="index" :actions="true"></FileView>
                    </template>
                </div>
                <!-- END: Directory & Files -->
            </div>
        </div>
    </AppLayout>
</template>
