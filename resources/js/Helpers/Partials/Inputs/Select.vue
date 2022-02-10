<template>
    <select
        :value="modelValue"
        @input="
            $emit('update:modelValue', $event.target.value);
            $emit('selected', data[$event.target.value]);
        "
        class="capitalize"
    >
        <option :value="null"></option>
        <option :value="d.id" v-for="(d, ind) in data" :key="ind" class="capitalize">
            {{ d[display] }}
        </option>
    </select>
</template>

<script>
import { defineComponent } from "vue";
export default defineComponent({
    props: ["modelValue", "data", "display"],
    emits: ["update:modelValue", "selected"],
});
</script>
<style>
select,
option, [input-in='table'] {
    width: 200px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
</style>
