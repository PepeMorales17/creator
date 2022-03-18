<script>
import { nestedMenu } from "@/utils/MenuUtils";
import { defineComponent, ref } from "vue";
import { helper as $h } from "@/utils/helper";
import { Link } from "@inertiajs/inertia-vue3";

// import ChevronDownIcon from "@zhuowenli/vue-feather-icons/icons/ChevronDownIcon";
// import BarChart2Icon from "@zhuowenli/vue-feather-icons/icons/BarChart2Icon";
import { ChevronDownIcon, BarChart2Icon } from "@/Helpers/Partials/Icons/AppIcons.js";
import dom from "@left4code/tw-starter/dist/js/dom";
import SwitchDark from "@/Helpers/Partials/Utils/DarkMode/SwitchDark.vue";
const ChildMenu = defineComponent({
    name: "ChildMenu",
    components: {
        ChevronDownIcon,
        Link,
    },
    props: ["menus", "activeMobileMenu", "linkTo", "enter", "leave"],

    template: `
    <ul>
        <li
            v-for="(subMenu, subMenuKey) in menus"
            :key="subMenuKey"
        >
            <a
                href="javascript:;"
                class="menu"
                :class="{ 'menu--active': subMenu.active }"

            >
                <div class="menu__title">
                    <Link :href="route(subMenu.namespace)">
                    {{ subMenu.name }}
                    </Link>
                    <div
                        v-if="!!subMenu.children.length"
                        class="menu__sub-icon"
                        :class="{
                            'transform rotate-180':
                                subMenu.activeDropdown,
                        }"
                        @click.stop="linkTo(subMenu)"
                    >
                        <ChevronDownIcon />
                    </div>
                </div>
            </a>
            <!-- BEGIN: Third Child -->
            <transition @enter="enter" @leave="leave">
                <ChildMenu
                :menu="subMenu.children"
                :leave="leave"
                :enter="enter"
                :linkTo="linkTo"
                :activeMobileMenu="activeMobileMenu"
                v-if="!!subMenu.children.length && subMenu.activeDropdown"></ChildMenu>
            </transition>
            <!-- END: Third Child -->
        </li>
    </ul>
    `,
});

export default defineComponent({
    components: {
        ChevronDownIcon,
        Link,
        ChildMenu,
        BarChart2Icon,
        SwitchDark
    },
    props: {
        menus: {
            type: Array,
        },
        top: {
            default: true,
        },
    },
    data() {
        return {
            formattedMenu: [],
            activeMobileMenu: false,
        };
    },
    computed: {
        topMenu() {
            return nestedMenu(this.menus);
        },
    },
    methods: {
        toggleMobileMenu() {
            this.activeMobileMenu = !this.activeMobileMenu;
        },
        linkTo(menu) {
            if (!!menu.children.length) {
                menu.activeDropdown = !menu.activeDropdown;
            } else {
                this.activeMobileMenu = false;
                // router.push({
                //     name: menu.pageName,
                // });
            }
        },
        enter(el, done) {
            dom(el).slideDown(300);
        },
        leave(el, done) {
            dom(el).slideUp(300);
        },
    },
    mounted() {
        this.formattedMenu = $h.toRaw(this.topMenu);
    },
});
</script>
<template>
    <!-- <Link as="div" class="menu__icon" :href="route(subMenu.namespace)">
                    <ActivityIcon />
                </Link> -->
    <!-- BEGIN: Mobile Menu -->
    <div class="mobile-menu md:hidden">
        <div class="mobile-menu-bar">
            <a href="javascript:;" class="flex mr-auto">
                <img alt="Rubick Tailwind HTML Admin Template" class="w-6" src="/storage/images/logo.svg" />
            </a>
            <a href="javascript:;" id="mobile-menu-toggler">
                <BarChart2Icon class="w-8 h-8 text-white transform -rotate-90" @click="toggleMobileMenu" />
            </a>
        </div>
        <transition @enter="enter" @leave="leave">
            <ul v-if="activeMobileMenu" class="border-t border-white/[0.08] py-5 hidden">
                <li>
                    <SwitchDark />
                </li>
                <!-- BEGIN: First Child -->
                <template v-for="(menu, menuKey) in formattedMenu">
                    <li v-if="menu == 'devider'" :key="menu + menuKey" class="menu__devider my-6"></li>
                    <li v-else :key="menu + menuKey">
                        <a
                            href="javascript:;"
                            class="menu"
                            :class="{
                                'menu--active': menu.active,
                                'menu--open': menu.activeDropdown,
                            }"
                        >
                            <div class="menu__icon">
                                <component :is="menu.icon" v-if="!!menu.icon" />
                            </div>
                            <div class="menu__title">
                                <Link :href="route(menu.namespace)">
                                    {{ menu.name }}
                                </Link>
                                <div
                                    v-if="!!menu.children.length"
                                    class="menu__sub-icon"
                                    :class="{
                                        'transform rotate-180': menu.activeDropdown,
                                    }"
                                    @click.stop="linkTo(menu)"
                                >
                                    <ChevronDownIcon />
                                </div>
                            </div>
                        </a>
                        <!-- BEGIN: Second Child -->
                        <transition @enter="enter" @leave="leave">
                            <ChildMenu :menus="menu.children" :leave="leave" :enter="enter" :linkTo="linkTo" :activeMobileMenu="activeMobileMenu" v-if="!!menu.children.length && menu.activeDropdown"> </ChildMenu>
                        </transition>
                        <!-- END: Second Child -->
                    </li>
                </template>
                <!-- END: First Child -->
            </ul>
        </transition>
    </div>
    <!-- END: Mobile Menu -->
</template>
