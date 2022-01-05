<template>
    <div class="group inline-block items-center px-1 pt-1" style="z-index: 1000">
        <Drop :menu="menu" />
    </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import { defineComponent } from "vue";

const Drop = defineComponent({
    name: 'Drop',
    template: `
        <Link as="button"
            :href="menu.routeWith ?? route(menu.namespace)"
            :class="buttonClass"
            target-menu="menu"
        >
            <span class="pr-1 font-semibold flex-1" target-menu="menu">{{ menu.name }}</span>
            <span :class="mrAuto" target-menu="menu">
                <svg target-menu="menu"
                    :class="svgClass"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                >
                    <path
                        d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                    />
                </svg>
            </span>
        </Link>
        <ul
            :class="ulClass"
            target-menu="menu"
        >
            <template v-for="(item, index) in menu.children" :key="index">
                <li
                    target-menu="menu"
                    class=" cursor-pointer block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition"
                    v-if="!!item.children && !!item.children.length"
                >
                    <Drop :menu="item"
                    buttonClass="w-full text-left flex items-center outline-none focus:outline-none"
                    svgClass="fill-current h-4 w-4 transition duration-150 ease-in-out"
                    ulClass="bg-white border rounded-md absolute top-0 right-0 transition duration-150 ease-in-out origin-top-left min-w-32"
                    mrAuto="mr-auto"
                    />
                </li>
                <Link as="li" :href="item.routeWith ?? route(item.namespace)"
                class="  cursor-pointer block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition" v-else target-menu="menu">
                    {{ item.name }}
                </Link>
            </template>
        </ul>
  `,
  components: {Link},
    props: {
        menu: {
            required: true,
        },
        buttonClass: {
            default:
                "inline-flex border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition",
        },
        mrAuto: {
             default:
                "",
        },
        ulClass: {
            default:
                "bg-white border rounded-sm transform scale-0 group-hover:scale-100 absolute transition duration-150 ease-in-out origin-top min-w-32",
        },
        svgClass: {
            default:
                "fill-current h-4 w-4 transform group-hover:-rotate-180 transition duration-150 ease-in-out",
        },
    },
});

export default defineComponent({
    name: "Dropdown",
    props: ["menu"],
    components: { Drop },
});
</script>
