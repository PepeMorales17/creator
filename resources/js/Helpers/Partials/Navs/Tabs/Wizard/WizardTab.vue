<script>
import { defineComponent, h } from "vue";

export default defineComponent({
    props: {
        data: {
            dafault() {
                return [];
            },
        },
        active: {
            default: 0,
        },
    },
    data() {
        return {
            active_tab: {
                div: "intro-x lg:text-center flex items-center lg:block flex-1 z-10",
                btn: "w-10 h-10 rounded-full btn btn-primary",
                desc: "lg:w-32 font-medium text-base lg:mt-3 ml-3 lg:mx-auto",
            },
            deactive_tab: {
                div: "intro-x lg:text-center flex items-center mt-5 lg:mt-0 lg:block flex-1 z-10 hidden",
                btn: "w-10 h-10 rounded-full btn text-slate-500 bg-slate-100 dark:bg-darkmode-400 dark:border-darkmode-400",
                desc: "lg:w-32 text-base lg:mt-3 ml-3 lg:mx-auto text-slate-600 dark:text-slate-400",
            },
            boxClass: "intro-y box py-5 sm:py-10 mt-5",
            mainClass: "relative before:hidden before:lg:block before:absolute before:w-[69%] before:h-[3px] before:top-0 before:bottom-0 before:mt-4 before:bg-slate-100 before:dark:bg-darkmode-400 flex flex-col lg:flex-row justify-center px-5 sm:px-20",
        };
    },
    render() {
        return h(
            "div",
            { class: this.boxClass },
            h(
                "div",
                { class: this.mainClass },
                this.data.map((w, i) => {
                    const type = i == this.active ? "active_tab" : "deactive_tab";
                    return h("div", { class: this[type].div }, [h("button", { class: this[type].btn }, w.title), h("div", { class: this[type].desc }, w.description)]);
                })
            )
        );
    },
});
</script>
