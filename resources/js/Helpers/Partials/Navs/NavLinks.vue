<template>
    <ul class="flex justify-around py-4">
        <template v-for="(item, index) in menu" :key="index">
            <Link
                :href="item.routeWith ?? route(item.route)"
                as="button"
                :class="[
                    'w-full py-1 text-sm leading-5 font-medium text-blue-700 rounded-lg',
                    'focus:outline-none focus:ring-2 ring-offset-2 ring-offset-blue-400 ring-white ring-opacity-60',
                    selected(item) ? color + ' shadow' : 'text-blue-400 hover:bg-blue-50 hover:text-blue-500',
                ]"
                >{{ item.name }}
            </Link>
        </template>
    </ul>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

export default defineComponent({
    props: {
        color: {
            default: "bg-orange-200",
        },
        menu: Object,
    },

    components: { Link },

    methods: {
        selected(item) {
            //console.log((item.routeWith,  route(),  route().current(item.route)), 'quepedo', item, window.location.href);
            return item.routeWith ? window.location.href === item.routeWith : route().current(item.route);
        },
    },
});
</script>

<style lang="scss" scoped></style>
