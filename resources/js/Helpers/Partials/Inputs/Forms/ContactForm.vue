<template>
    <template v-if="only.indexOf('email') > -1">
        <h1 class="text-lg font-bold border-b-2 border-blue-200 py-4">Email</h1>
        <input-group :input="input" v-for="(input, index) in inputs.email" :key="index" v-model="form.email[input.key]" :as="input.is" />
    </template>
    <template v-if="only.indexOf('phone') > -1">
        <h1 class="text-lg font-bold border-b-2 border-blue-200 py-4">Telefono</h1>
        <input-group :input="input" v-for="(input, index) in inputs.phone" :key="index" v-model="form.phone[input.key]" :as="input.is" />
    </template>
    <template v-if="only.indexOf('address') > -1">
        <h1 class="text-lg font-bold border-b-2 border-blue-200 py-4" >Direccion</h1>
        <address-form :form="form.address" :inputs="inputs.address" />
    </template>
</template>

<script>
import { defineComponent } from "vue";
import AddressForm from "@/Helpers/Partials/Inputs/Forms/AddressForm.vue";

import InputGroup from "../InputGroup.vue";
import { $axios } from "@/Plugins/axios.js";

export default defineComponent({

    props: {
        only: {
            type: Array,
            default() {
                return  ['address', 'phone', 'email'];
            }
        },
        form: Object,
        inputs: undefined
    },

    setup() {},
    components: { InputGroup, AddressForm },
    data() {
        return {
            data: [],
            loading: false,
        };
    },
});
</script>
