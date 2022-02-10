<script>
import { nestedMenu } from "@/utils/MenuUtils";
import { defineComponent } from "vue";
import { helper as $h } from "@/utils/helper";
import { Link } from "@inertiajs/inertia-vue3";

import ChevronDownIcon from "@zhuowenli/vue-feather-icons/icons/ChevronDownIcon";


export default defineComponent({
    name: 'TheMenu',
    components: {
        ChevronDownIcon,
        Link
    },
    props: {
        menus: {
            type: Array,
        },
        top: {
            default: true
        },
    },
    data() {
        return {
            formattedMenu: [],
        };
    },
    computed: {
        topMenu() {
            return nestedMenu(this.menus);
        },
    },
    mounted() {
        this.formattedMenu = $h.toRaw(this.topMenu);
    },
});
</script>
<template>
    <ul>
        <li v-for="(menu, menuKey) in formattedMenu" :key="menuKey">
            <Link
                :href="route(menu.namespace)"
                class="top-menu"
                :class="{
                    'top-menu--active': menu.active && top,
                }"
            >
                <div class="top-menu__icon">
                    <component :is="menu.icon" v-if="!!menu.icon" />
                </div>
                <div class="top-menu__title">
                    {{ menu.name }}
                    <ChevronDownIcon
                        v-if="!!menu.children.length"
                        class="top-menu__sub-icon"
                    />
                </div>
            </Link>
            <TheMenu :menus="menu.children" :top="false" v-if="!!menu.children.length"></TheMenu>
        </li>
    </ul>

</template>
