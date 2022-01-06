<template>
    <div class="p-4" v-if="input.is === 'InputTable'">
        <div class="p-4 shadow-lg">
            <h1 class="font-bold text-xxl">{{ input.label }}</h1>
            <input-table
                :form="form[input.key]"
                :cols="input.cols"
                :emptyValue="input.emptyValue"
            />
        </div>
    </div>

    <input-group
        :input="input"
        :as="input.is"
        v-model="form[input.key]"
        v-else
        :form="form"
        :errors="form.errors"
    />
</template>

<script>
import { defineComponent, defineAsyncComponent } from "vue";

export default defineComponent({
    props: ["form", "input"],
    components: {
        InputGroup: defineAsyncComponent(() => import("./InputGroup.vue")),
        InputTable: defineAsyncComponent(() => import("./InputTable.vue")),
    },
});
</script>
