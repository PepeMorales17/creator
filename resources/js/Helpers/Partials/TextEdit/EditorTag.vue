<template>
    <div class="relative m-auto">
        <div class="absolute rounded-lg shadow-2xl bg-blue-600 text-white p-4 overflow-auto max-h-48" :class="{ hidden: !selectedTag, width: true }" :id="uId" style="z-index: 1000" :ref="'div-tags-' + uId">
            <ul :id="uId + 'Ul'">
                <li
                    :id="uId + 'li' + index"
                    tag-list
                    :tabIndex="index + 100"
                    v-for="(v, index) in tagsFromServer"
                    :key="index"
                    @blur="whereIsIt"
                    @click="replaceTagWith(v)"
                    @keyup="handleOption($event, v, index)"
                    class="cursor-pointer p-2 focus:bg-yellow-900 hover:bg-blue-500"
                >
                    {{ v }}
                </li>
            </ul>
        </div>
        <textarea maxlength="1000" :id="'textarea' + uId" rows="1" @keydown.enter="handleEnter" @blur="whereIsIt" @keyup="searching" class="w-full rounded-xl form-control text-base" @click="searching" v-model="value" :placeholder="placeholder" @paste="pasteFile"></textarea>
        <div class="flex justify-end items-end">
            <label :for="'assetsFieldHandle' + uId" class="block cursor-pointer px-5">
                <PaperclipIcon />
                <input type="file" multiple :name="'fields[assetsFieldHandle' + uId + '][]'" :id="'assetsFieldHandle' + uId" class="w-px h-px opacity-0 overflow-hidden absolute" @change="onChange" ref="file" />
            </label>
            <SendIcon class="cursor-pointer" @click="sendNoteToServer" v-if="!!value" />
        </div>
        <template v-if="!!filesToUpload.length">
            <div class="grid grid-cols-6">
                <FileView :file="file" v-for="(file, index) in filesToUpload" :key="index" @click="filesToUpload.splice(index, 1)"></FileView>
            </div>
        </template>
    </div>
</template>

<script>
export default {
    methods: {
        whereIsIt(e) {
            this.$nextTick(() => {
                setTimeout(() => {
                    var hasFocus = false;
                    const tags = document.querySelectorAll("[tag-list]").forEach((x) => {
                        if (x === document.activeElement) {
                            hasFocus = true;
                        }
                    });
                    if (!hasFocus) {
                        this.selectedTag = null;
                    }
                }, 200);
            });
        },
        handleEnter(e) {
            if (e.shiftKey) {
                e.preventDefault();
                this.sendNoteToServer();
            } // console.log("New line", e);
        },
        sendNoteToServer() {
            var item = this.$page.props.item;
            if (!!item) {
                if (!this.withItem) {
                    if (!confirm("Quieres asociar esta nota?")) item = null;
                }
                var url = this.$page.url;
                if (!!url) {
                    url = url.match(/^\/+[a-z]+\//);
                    if (url) url = url[0].replaceAll("/", "");
                    if (url) url = url.replaceAll("/", "");
                }
            }
            var formData = new FormData();
            this.filesToUpload.map((x) => formData.append("files[]", x));
            this.tags.map((x) => formData.append("tags[]", x));

            //console.log(new RegExp("==[[0-9][a-zA-z_0-9\\s]+\\]", "gm"));

            (this.value.match(new RegExp("==[[0-9][a-zA-z_0-9\\s]+\\]", "gm")) ?? []).map((x) => formData.append("filesNames[]", x));
            formData.append("note", this.value);
            formData.append("noteable_id", !!item ? item.id : "");
            formData.append("noteable_type", !!url ? url : "");
            //formData.append("tags", this.tags);

            axios
                .post(route("note.store"), formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((x) => {
                    this.$emit("created");
                    this.filesToUpload = [];
                    this.$nextTick(() => (this.value = null));
                });
            //console.log(this.$page.props, url);
        },
        onChange() {
            this.filesToUpload = [...this.$refs.file.files];
        },
        pasteFile(event) {
            const items = [];
            for (const item of event.clipboardData.items) {
                if (!item.kind) continue;
                if (item.kind.indexOf("file") === -1) continue;
                items.push(item.getAsFile());
            }

            this.filesToUpload = items; //[item.getAsFile()];
        },
    },
};
</script>
<script setup>
import FileView from "@/Pages/Creator/utils/FileView.vue";
import { getCaretCoordinates } from "./getCaretCoordinates.js";
import { PaperclipIcon, SendIcon } from "@/Helpers/Partials/Icons/AppIcons.js";
import { ref, getCurrentInstance, computed } from "vue";

const props = defineProps({
    modelValue: {
        type: String,
    },
    width: {
        default: "max-w-xs",
    },
    placeholder: {
        default: "Que estas pensando",
    },
    withItem: Object,
});

const value = computed({
    get: () => props.modelValue,
    set: (val) => {
        emit("update:modelValue", val);
    },
});

defineEmits(["update:modelValue", "created"]);

const { emit } = getCurrentInstance();

const selectedTag = ref(null);
const tags = ref([]);
const filesToUpload = ref([]);
const tagsFromServer = ref([]);
const lastPos = ref(0);
const uId = Date.now();
//const pero = ref(null);

//const value = value.value;

//const textValue = ref(null);

const getSelectedTag = (val, pos, sym = "#") => {
    //const reg = new RegExp("#[a-zA-Z0-9-_]+$");
    const reg = new RegExp(`${sym}[a-zA-Z0-9-_]+$`, "gm");
    //console.log(reg);
    if (!val) return null;
    var find = val.substring(0, pos).search(reg);
    if (find === -1) return null;
    var space = val.substring(pos).indexOf(" ");
    const tag = val.substring(find, pos + space + 1).trim();
    if (!!tag) searchInServer(tag);

    return tag;
};

function isArrow(key) {
    return key === "ArrowDown" || key === "ArrowUp"; // || key === "ArrowRight" || key === "ArrowLeft";
}

function replaceTagWith(val) {
    //console.log(val);
    const reg = new RegExp(`\\${selectedTag.value}`, "gm");
    const ocurrencias = value.value.matchAll(reg);
    var theOne = null;
    var atIndex = null;
    var toIndex = null;
    for (const ocurrencia of ocurrencias) {
        if (lastPos.value > ocurrencia.index - 1 && lastPos.value < ocurrencia.index + ocurrencia[0].length + 1) {
            theOne = ocurrencia[0];
            atIndex = ocurrencia.index - 1;
            toIndex = ocurrencia.index + ocurrencia[0].length + 1;
        }
    }
    if (theOne && toIndex && atIndex) {
        const newVal = value.value.substring(0, atIndex) + value.value.substring(atIndex).replace(theOne, val);
        //console.log(val, theOne, toIndex, atIndex, newVal, '-', value.value.substring(0, atIndex), '-', value.value.substring(atIndex).replace(theOne, val));
        //value.value = newVal;
        //emit("update:modelValue", newVal);
        value.value = newVal;
        //modelValue = modelValue.substring(0, atIndex) + modelValue.substring(atIndex).replace(theOne, val);
    }
    const textarea = document.getElementById("textarea" + uId);
    const pos = atIndex + val.length + 1;
    setTimeout(() => {
        textarea.setSelectionRange(pos, pos);
    }, 50);
    textarea.focus();
}

const searchInServer = _.debounce(function (tag) {
    axios
        .get(
            route("tag.index", {
                tag: tag,
            })
        )
        .then((x) => {
            const ts = x.data.tags;
            tagsFromServer.value = ts;
        });
}, 500);

function searching(e) {
    //|\\/
    const sym = "(\\#|\\$|\\*|\\@|\\!|\\?|\\+)";
    if (focusDiv(e)) return;
    var val = value.value;
    if (!val) return;
    resizeRows(e.target);
    //if (val.length > 250 && window.innerWidth < 1900) e.target.rows = 20;
    var pos = e.target.selectionStart;
    lastPos.value = pos;
    var match = new RegExp(`(${sym}[a-zA-Z0-9(_)]{1,})`, "g");
    match = val.match(match);
    tags.value = !!match ? match : [];
    selectedTag.value = getSelectedTag(val, pos, sym);
    const div = document.getElementById(uId);
    const subs = val.substr(0, pos);
    const lines = subs.split("\n");
    setTimeout(() => {
        const coor = getCaretCoordinates(e.target, pos);
        div.style.top = coor.top + coor.height + "px";
        var left = div.offsetWidth * 1.35 + coor.left;
        left = left > window.innerWidth ? window.innerWidth - div.offsetWidth * 1.35 : coor.left;
        div.style.left = left + "px";

        // console.log(coor, left, div.offsetWidth * 1.25);
    }, 10);
}

function resizeRows(ta) {
    const letterPerRow = ta.clientWidth / 16;
    var len = value.value.length;
    len = len > 250 ? len * 0.7 : len > 400 ? len * 0.5 : len > 750 ? len * 0.4 : len;
    const val = parseInt(len / letterPerRow);
    ta.rows = val === 0 ? 1 : val;
}

function focusDiv(e) {
    if (isArrow(e.key) && !!selectedTag.value && tags.value.length) {
        const ul = document.getElementById(uId + "li" + "0").focus();
        return true;
    }
}

function handleOption(e, v, i) {
    e.preventDefault();
    if (e.key === "Enter") replaceTagWith(v);
    const len = tagsFromServer.value.length;
    const isEnd = len - 1 === i;
    const isStart = 0 === i;
    //console.log(e.key === "ArrowUp" && !isStart, e.key === "ArrowDown" && !isEnd, "pepe" + (i + 1), "pepe" + (i - 1));

    if (e.key === "ArrowUp" && !isStart) {
        document.getElementById(uId + "li" + (i - 1)).focus();
        return;
    }
    if (e.key === "ArrowDown" && !isEnd) {
        document.getElementById(uId + "li" + (i + 1)).focus();
        //console.log(document.getElementById(uId + 'li' + (i + 1)).focus());
        return;
    }
    if (e.key === "ArrowDown" && isEnd) {
        document.getElementById(uId + "li" + "0").focus();
        return;
    }
    if (e.key === "ArrowUp" && isStart) {
        //console.log(uId + (tags.value.length - 1));
        document.getElementById(uId + "li" + (len - 1)).focus();
        return;
    }
}

// function extractImage(e) {
//     const file = e.target.files[0];
//     if (file.type.search("image") < 0) {
//         alert("No es una imagen");
//         this.form[this.target] = "";
//         e.target.value = "";
//         return;
//     }
//     const url = (window.URL ? URL : webkitURL).createObjectURL(file);
// }
</script>
