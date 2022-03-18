<template>
    <loading :loading="loading"></loading>
</template>

<script>
import { defineComponent } from "vue";
import Loading from "./Loading.vue";

export default defineComponent({
    props: {
        modelValue: Object,
        term: String,
        action: {
            default: "get",
        },
        from: String,
        params: Object,
        urlData: {
            default() {
                return {};
            },
        },
    },
    emits: ["update:modelValue"],
    components: {
        Loading,
    },
    data() {
        return {
            loading: false,
        };
    },
    methods: {
        handleAxio() {
            this.loading = true;
            axios[this.action](this.getRoute(), { params: this.params })
                .then((response) => {
                    this.$emit("update:modelValue", response.data);
                })
                .finally(() => (this.loading = false));
        },
        getRoute() {

            if (this.from.search(".") > -1) {
                return route(this.from, this.urlData);
            }
            return this.from;
        },
        axioDebounce: _.debounce(function (e) {
            this.loading = true;
            axios[this.action](this.getRoute(), { params: this.params })
                .then((response) => {
                    this.$emit("update:modelValue", response.data);
                })
                .finally(() => (this.loading = false));
        }, 500),
    },
});
</script>
