<template>
    <component :is="tagName" ref="editorRef" v-editor-directive="{ props, emit, cacheData }" class="select"></component>
</template>

<script setup>
import { inject, onMounted, ref, watch } from "vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

const init = async (el, editorBuild, { props, emit, cacheData }) => {
    // Initial data
    cacheData = props.modelValue;
    props.config.initialData = props.modelValue;

    // Init CKEditor
    const editor = await editorBuild.create(el, props.config);

    // Attach CKEditor instance
    el.CKEditor = editor;

    // Set initial disabled state
    editor.isReadOnly = props.disabled;

    // Set on change event
    editor.model.document.on("change:data", () => {
        const data = editor.getData();
        cacheData = data;
        emit("update:modelValue", data);
    });

    editor.editing.view.document.on("keyup", (e) => {
        const data = editor.getData();
        //cacheData = data
        //console.log(editor.editing.view.document.getBody().getText(), data);
        emit("keyup", { e: e, data: data });
    });

    // Set on focus event
    editor.editing.view.document.on("focus", (evt) => {
        emit("focus", evt, editor);
    });

    // Set on blur event
    editor.editing.view.document.on("blur", (evt) => {
        emit("blur", evt, editor);
    });

    // Set on ready event
    emit("ready", editor);

    // Watch model change
    watch(props, () => {
        if (cacheData !== props.modelValue) {
            el.CKEditor.setData(props.modelValue);
        }
    });
};

const vEditorDirective = {
    mounted(el, { value }) {
        init(el, ClassicEditor, value);
    },
};

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    config: {
        type: Object,
        default: () => ({}),
    },
    tagName: {
        type: String,
        default: "div",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    refKey: {
        type: String,
        default: null,
    },
});

const emit = defineEmits();

const editorRef = ref();
const cacheData = ref("");

const bindInstance = () => {
    if (props.refKey) {
        const bind = inject(`bind[${props.refKey}]`);
        if (bind) {
            bind(editorRef.value);
        }
    }
};

onMounted(() => {
    bindInstance();
});
</script>
