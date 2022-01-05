<template>
    <!-- <my-select :data="taxInformation"></my-select> -->
    <div v-if="true">
        <input-group
            :input="{
                id: 'tax_id',
                key: 'tax_id',
                label: 'RFC',
                type: 'text',
            }"
            @input="search($event.target.value)"
            @change="setName"
            v-model="form['tax_id']"
            as="MyInput"
            list="datalist"
            autocomplete="off"
        />
        <datalist id="datalist">
            <option :value="item.tax_id" v-for="(item, index) in data" :key="index">{{ item.tax_id }}</option>
        </datalist>
        <span v-if="loading">Cargando...</span>
        <input-group
            :input="{
                id: 'tax_name',
                key: 'tax_name',
                label: 'Razon Social',
                type: 'text',
            }"
            :disabled="loading"
            v-model="form['tax_name']"
            as="MyInput"
        />
        <input-group
            :input="{
                id: 'represented_by',
                key: 'represented_by',
                label: 'Representada por:',
                type: 'text',
            }"
            :disabled="loading"
            v-model="form['represented_by']"
            as="MyInput"
        />
    </div>
</template>

<script>
import { defineComponent } from "vue";
import InputGroup from "../InputGroup.vue";
// import MySelect from "../Select.vue";
import { $axios } from "@/Plugins/axios.js";

export default defineComponent({
    props: ["form"],
    components: { InputGroup },
    data() {
        return {
            data: [],
            loading: false,
        };
    },
    methods: {
        search: _.debounce(function (val) {
            if (val.length < 6) {
                this.loading = true;
                $axios.get(route("wapi.taxinfo", val)).then((res) => {
                    this.data = res.data;
                    this.loading = false;
                });
            }
        }, 500),
        setName() {
            const index = this.data.findIndex((x) => x.tax_id == this.form.tax_id);
            if (index > -1) {
                this.form.tax_name = this.data[index].tax_name;
                this.form.represented_by = this.data[index].represented_by;
            }
        },
    },
});
</script>
