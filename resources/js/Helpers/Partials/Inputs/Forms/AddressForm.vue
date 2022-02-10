<template>
    <!-- <input-group :input="input" v-for="(input, index) in inputs" :key="index" v-model="form[input.key]" :as="input.is" /> -->

    <InputGroup :input="inputs[n]" :as="inputs[n].is" v-for="n in [0, 1, 2, 3]" v-model="form[inputs[n].key]" :errors="form.errors" />
    <InputGroup :input="inputs[4]" :as="inputs[4].is" v-model="form[inputs[4].key]" :errors="form.errors" @change="findPostalCode($event.target.value)" />

    <InputGroup :input="inputs[5]" :as="inputs[5].is" v-model="form[inputs[5].key]" :errors="form.errors" />

    <InputGroup :input="inputs[6]" :as="inputs[6].is" v-model="form[inputs[6].key]" :errors="form.errors" v-if="!!!form[inputs[5].key]" />
    <InputGroup :input="inputs[n]" :as="inputs[n].is" v-for="n in [7, 8]" v-model="form[inputs[n].key]" :errors="form.errors" />

    <div v-if="!!inputs[9]">
        <InputGroup :input="inputs[n]" :as="inputs[n].is" v-for="n in [9, 10, 11]" v-model="form[inputs[n].key]" :errors="form.errors" />
    </div>
</template>

<script>
import { defineComponent } from "vue";
import InputGroup from "../InputGroup.vue";
// import MySelect from "../Select.vue";
import { $axios } from "@/Plugins/axios.js";

export default defineComponent({
    props: ["form", "inputs"],

    mounted() {
        if (!!this.form.postal_code_id) {
            this.findPostalCode(this.form.postal_code_id);
        }
    },
    components: { InputGroup },
    data() {
        return {
            data: [],
            loading: false,
        };
    },
    methods: {
        findPostalCode(e) {
            this.inputs[5].data = [];
            $axios
                .get(route("wapi.postal", e))
                .then((res) => {
                    this.inputs[5].data = res.data.colonies;
                    this.form.state = res.data.state;
                    this.form.city = res.data.city;
                })
                .catch(() => {
                    alert("El codigo postal no existe");
                    this.form.postal_code_id = null;
                });
        },
    },
});
</script>
