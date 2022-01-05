<template>
    <div class="w-1/2 m-auto p-4 bg-red-500 text-white rounded-lg font-bold text-center">
        <slot>
            <p>{{msg}}</p>
        </slot>
    </div>
</template>

<script>
import { defineComponent } from "vue";
export default defineComponent({
    props: ["msg"]
});
</script>

<style lang="scss" scoped></style>
