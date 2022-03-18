<template>
    <div >
        <div class="relative before:hidden before:lg:block before:absolute before:w-[69%] before:h-[3px] before:top-0 before:bottom-0 before:mt-4 before:bg-slate-100 before:dark:bg-darkmode-400 flex flex-col lg:flex-row justify-center px-5 sm:px-20">
            <tab :isActive="active === index" :title="item.title" :description="item.description" v-for="(item, index) in tabs" :key="index" @click="active = index" />
        </div>
        <div class="px-5 sm:px-20 mt-10 pt-10 border-t border-slate-200/60 dark:border-darkmode-400">
            <!-- <slot /> -->
            <content :index="active"/>
            {{active}}
        </div>
    </div>
</template>
<script setup>
import { useSlots, ref, h, defineComponent } from "vue";

const slots = useSlots();

const active = ref(0);

const tabs = ref(
    slots.default().map((tab, index) => {
        return {
            title: tab.props.title,
            description: tab.props.description,
        };
    })
);
const content = defineComponent({
    props: ['index'],
    render() {
        if (!slots.default) {
            return undefined;
        }
        return slots.default().map((vnode, index) => h(vnode, {class:{['hidden ' + index]: this.index !== index}}));
    },
});
const tab = defineComponent({
    template: `
    <div :class="isActive ? active.div : deactive.div">
        <button :class="isActive ? active.btn : deactive.btn">{{title}}</button>
        <div :class="isActive ? active.desc : deactive.desc">{{description}}</div>
    </div>
    `,
    props: ["isActive", "title", "description"],
    data() {
        return {
            active: {
                div: "intro-x lg:text-center flex items-center lg:block flex-1 z-10",
                btn: "w-10 h-10 rounded-full btn btn-primary",
                desc: "lg:w-32 font-medium text-base lg:mt-3 ml-3 lg:mx-auto",
            },
            deactive: {
                div: "intro-x lg:text-center flex items-center mt-5 lg:mt-0 lg:block flex-1 z-10",
                btn: "w-10 h-10 rounded-full btn text-slate-500 bg-slate-100 dark:bg-darkmode-400 dark:border-darkmode-400",
                desc: "lg:w-32 text-base lg:mt-3 ml-3 lg:mx-auto text-slate-600 dark:text-slate-400",
            },
        };
    },
});
</script>
