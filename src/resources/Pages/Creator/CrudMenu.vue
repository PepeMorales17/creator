<template>
    <app-layout>
        <crud :menu="menu.crud" :dataTable="dataTable" :inputs="inputs" :item="item" :value="item">
        </crud>
    </app-layout>
</template>

<script>
import Crud from "@/Helpers/Crud.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineComponent } from "vue";

export default defineComponent({

    components: { Crud, AppLayout },

    props: ["menu", "dataTable", "inputs", "item"],

});
</script>
