<script setup>
import { onMounted, ref } from "vue";
import NoteView from "./NoteView.vue";
import FileView from "@/Pages/Creator/utils/FileView.vue";
import { Link } from "@inertiajs/inertia-vue3";
import { MoreVerticalIcon, Edit3Icon, SearchIcon, ChevronDownIcon } from "@/Helpers/Partials/Icons/AppIcons.js";
import { Dropdown, DropdownToggle, DropdownMenu, DropdownContent } from "@/Helpers/Partials/Navs/Dropdown/Dropdown.js";
import NoteFilters from "@/utils/Filters/NoteFilters.json";
const props = defineProps({
    title: {
        default: "Historia",
    },
    filter: Object,
    notes: Array,
    id: {
        default: "history",
    },
    getBeforeMount: {
        default: false,
    },
});
const findTags = ref("");
const history = ref([]);
const showEditor = ref(false);
const showFilters = ref(false);

const showingFilters = ref([]);

const filters = ref(
    Object.keys(NoteFilters).reduce((pv, cv) => {
        pv[cv] = "";
        return pv;
    }, {})
);

const searchTags = () => {
    localStorage.setItem("findTags-" + props.id, findTags.value);
    localStorage.setItem("moreFilters-" + props.id, JSON.stringify(filters.value));
    localStorage.setItem("showingFilters-" + props.id, JSON.stringify(showingFilters.value));
    axios.get(route("note.withTags", { tags: findTags.value, filter: props.filter, moreFilters: filters.value })).then((x) => {
        localStorage.setItem("history-" + props.id, JSON.stringify(x.data));
        history.value = x.data;
    });
};

defineExpose({
    searchTags,
});

onMounted(() => {
    //console.log(filters);
    //document.body.addEventListener("keyup", (e) => eventL(e));
    if (props.getBeforeMount) {
        searchTags();
    } else {
        findTags.value = localStorage.getItem("findTags-" + props.id) != 'null' ? localStorage.getItem("findTags-" + props.id): '';
        if (localStorage.getItem("moreFilters-" + props.id)) {
            filters.value = JSON.parse(localStorage.getItem("moreFilters-" + props.id));
        }
        history.value = localStorage.getItem("history-" + props.id) ? JSON.parse(localStorage.getItem("history-" + props.id)) : [];
        showingFilters.value = localStorage.getItem("showingFilters-" + props.id) ? JSON.parse(localStorage.getItem("showingFilters-" + props.id)) : [];
        showFilters.value = !!showingFilters.value.length;
    }
});

function clearFilters() {
    showingFilters.value = [];
    Object.keys(filters.value).forEach((x) => (filters.value[x] = null));
    localStorage.setItem("moreFilters-" + props.id, JSON.stringify(filters.value));
}
</script>
<script>
export default {
    inheritAttrs: false,
};
</script>
<template>
    <div class="rounded-2xl">
        <span class="text-lg font-bold m-3 flex justify-between items-baseline"
            >{{ title }}
            <div class="w-full sm:w-auto relative mr-auto mt-3 sm:mt-0 flex-grow ml-2">
                <SearchIcon class="w-4 h-4 absolute my-auto inset-y-0 ml-3 left-0 z-10 text-slate-500" />
                <input type="text" class="form-control sm:w-64 box px-10 w-full" placeholder="Busca notas con etiquetas" v-model="findTags" @change="searchTags" style="width: 100%;" />
                <Dropdown class="inbox-filter absolute inset-y-0 mr-3 right-0 flex items-center" placement="bottom-start">
                    <DropdownToggle tag="a" role="button" class="w-4 h-4 block" href="javascript:;">
                        <ChevronDownIcon class="w-4 h-4 cursor-pointer text-slate-500" />
                    </DropdownToggle>
                    <DropdownMenu class="inbox-filter__dropdown-menu pt-2">
                        <DropdownContent tag="div">
                            <div class="mt-3">
                                <span @click="showFilters = !showFilters" class="cursor-pointer">Filtros disponibles</span>
                                <div class="grid md:grid-cols-5 grid-cols-2" v-if="showFilters">
                                    <div class="form-check mr-2" :class="{ 'mt-2 sm:mt-0': index > 0 }" v-for="(item, index) in Object.keys(NoteFilters)" :key="index">
                                        <input :id="'checkbox-switch-' + index" class="form-check-input" type="checkbox" :value="item" v-model="showingFilters" />
                                        <label class="form-check-label" :for="'checkbox-switch-' + index"> {{ NoteFilters[item].label }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-12 gap-4 gap-y-3 p-3">
                                <div class="md:col-span-6 col-span-12" v-for="(item, index) in showingFilters" :key="index">
                                    <label for="input-filter-1" class="form-label text-xs">{{ NoteFilters[item].label }}</label>
                                    <input id="input-filter-1" type="text" class="form-control flex-1" v-model="filters[item]" />
                                </div>
                                <div class="col-span-12 flex items-center mt-3">
                                    <button class="btn btn-secondary w-32 ml-auto" @click="clearFilters">Borrar Filtros</button>
                                    <button class="btn btn-primary w-32 ml-2" @click="searchTags">Search</button>
                                </div>
                            </div>
                        </DropdownContent>
                    </DropdownMenu>
                </Dropdown>
            </div>
        </span>

        <div :class="$attrs.class ? $attrs.class : '' + ' w-full overflow-auto h-screen'">
            <template v-for="(item, index) in history" :key="index">
                <div class="text-slate-400 dark:text-slate-500 text-xs text-center mb-10 mt-5" v-if="!!item.type && item.type === 'date'">{{ item.date }}</div>
                <div class="chat__box__text-box flex items-end mb-4">
                    <div class="bg-slate-100 dark:bg-darkmode-400 px-4 py-3 text-slate-500 rounded-r-md rounded-t-md" :class="{ '-mb-2': !!item.media.length }">
                        <note-view
                            :text="item.note"
                            @tag="
                                findTags = !!$event ? $event : '';
                                searchTags();
                            "
                        />
                        <div class="mt-1 text-xs text-slate-500">{{ item.date + " - " + item.date_human }} <Link :href="route('note.edit', item.id)" class="text-primary">e</Link></div>
                    </div>
                </div>
                <div :class="'grid mb-3 ' + (item.media.length > 1 ? 'grid-flow-col  overflow-x-auto p-4' : 'grid-col-12')">
                    <FileView :file="file" @deleted="item.media.splice(index, 1)" :forceDelete="true" :actions="true" v-for="(file, index) in item.media" :key="index"></FileView>
                </div>
            </template>
        </div>
    </div>
</template>
