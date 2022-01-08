<template>
    <div class="text-center font-bold text-blue-900 underline" v-if="loading || label != ''" ref="i-scroll">
        {{ label }}
    </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        modelValue: Object,
        term: {
            type: String,
            default: "",
        },
        listenScrollOf: {
            default: "window",
        },
    },
    emits: ["update:modelValue"],
    components: {
        //Loading,
    },
    data() {
        return {
            loading: false,
            label: null,
        };
    },
    mounted() {
        this.$nextTick(() => {
            const parent = this.listenScrollOf === "parent" ? this.$refs["i-scroll"].parentNode : window;
            parent.addEventListener(
                "scroll",
                _.debounce((e) => {
                    const pixelFromBottom = this.listenScrollOf === "parent" ? parent.scrollHeight - parent.scrollTop - parent.offsetHeight : document.documentElement.offsetHeight - document.documentElement.scrollTop - window.innerHeight;

                    if (pixelFromBottom < 200) {
                        if (!!this.modelValue.next_page_url) {
                            this.label = "Cargando...";
                            this.loading = true;
                            this.getData();
                        } else {
                            this.label = "Ya son todos los resultados...";
                        }
                    }
                }, 100)
            );
        });
    },
    methods: {
        getData() {
            axios
                .get(this.modelValue.next_page_url, {
                    params: { term: this.term },
                })
                .then((response) => {
                    if (!!response) {
                        this.$emit("update:modelValue", {
                            ...response.data,
                            data: [...this.modelValue.data, ...response.data.data],
                        });
                        this.loading = false;
                    }
                });
        },
    },
});
</script>
