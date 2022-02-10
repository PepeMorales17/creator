<template>
    <div class="">
        <input type="text" name="filter" id="filter" v-model="term" @input="getData" class="w-full border-2 rounded-lg p-2" ref="pp" placeholder="Buscar" />
    </div>
    <div class="shadow rounded-lg border-gray-200 bg-white" :class="{ 'overflow-scroll h-96 overla': listenScrollOf === 'parent' }">
        <slot></slot>
        <infinite-scroll v-model="localValue" :term="term" :listenScrollOf="listenScrollOf"></infinite-scroll>
        <axios-loading v-model="localValue" :term="term" :from="url" :params="{ term: term, ...params }" :urlData="urlData" ref="axio"></axios-loading>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import InfiniteScroll from "./InfiniteScroll.vue";
import AxiosLoading from "./AxiosLoading.vue";

export default defineComponent({
    components: {
        InfiniteScroll,
        AxiosLoading,
    },
    mounted() {
        if (!!!this.modelValue) {
            this.getData();
        }
    },
    data() {
        return {
            localValue: this.modelValue,
            term: "",
        };
    },
    props: {
        modelValue: Object,
        url: String,
        listenScrollOf: {
            default: "window",
        },
        params: {
            default() {
                return {};
            }
        },
        urlData: {
            default() {
                return {};
            }
        }
    },
    emits: ["update:modelValue"],
    watch: {
        localValue(newValue) {
            this.$emit("update:modelValue", newValue);
        },
        modelValue(newValue) {
            this.localValue = newValue;
        },
    },
    methods: {
        getData() {
            this.$refs.axio.axioDebounce();
        },
    },
});
</script>
