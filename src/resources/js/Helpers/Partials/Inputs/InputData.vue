<template>
    <input :value="modelValue" v-bind="$attrs" @input="$emit('update:modelValue', $event.target.value)" class="capitalize" :id="id" :list="id + '-datalist'" @change="setData($event.target.value)" :class="{ 'bg-red-400': !inList }" />
    <datalist :id="id + '-datalist'">
        <option :value="d[primaryKey]" v-for="(d, ind) in data" :key="ind" class="capitalize">
            {{ d[display] }}
        </option>
    </datalist>
</template>

<script>
import { defineComponent } from "vue";
export default defineComponent({
    props: ["modelValue", "data", "display", "form", "id", "primaryKey", "set"],
    emits: ["update:modelValue", "selected"],
    inheritAttrs: false,
    mounted() {
        //console.log(this.form, 'la form');
        this.setData(this.modelValue);
    },
    data() {
        return {
            select: null,
        };
    },
    computed: {
        inList() {
            return !!this.select;
        },
    },
    methods: {
        setData(val) {
            this.select = this.data[val];
            if (this.form) {
                if (this.select) {
                    if (!!!this.form[this.set.on]) {
                        this.form[this.set.on] = this.select[this.set.key];
                    }
                }
            }
        },
    },
});
</script>
