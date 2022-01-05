<script>
import { defineAsyncComponent, defineComponent, h, resolveComponent } from "vue";
//import MySelect from "./Select.vue";
import { useForm } from "@inertiajs/inertia-vue3";

export const FormHelper = {
    data() {
        return {
            form: useForm(!!this.value ? this.value : this.inputs.emptyValue),
        };
    },
    props: {
        value: Object,
        inputs: Object,
        //emptyValue: Object,
    },
};

export default defineComponent({
    setup(props) {
        //console.log('qeue peso aqui estou');
        const inputsNames = {
            MyInput: "input",
            input: "input",
            MySelect: resolveComponent("my-select"),
            InputData: resolveComponent("input-data"),
            //MySelectFrom: resolveComponent("my-select-from"),
            MyTextarea: "textarea",
            //MyTextarea: "textarea",
            //InputFetch: resolveComponent("InputFetch"),
        };
        const resolveName = inputsNames[props.as];
        return { resolveName };
    },

    components: {
        MySelect: defineAsyncComponent(() => import("./Select.vue")),
        InputData: defineAsyncComponent(() => import("./InputData.vue")),
        // MySelectFrom: defineAsyncComponent(() => import("./SelectFrom.vue")),
        // InputFetch: defineAsyncComponent(() => import("./InputFetch.vue")),
    },

    mounted() {
        //this.renderData = this.layouts()[this.layout];
    },

    data() {
        return {
            //renderData: null
        };
    },

    render() {
        return this.renderData;
    },
    inheritAttrs: false,
    props: {
        id: String,
        input: Object,
        modelValue: undefined,
        //classes: Object,
        form: Object,
        withErrors: {
            type: Boolean,
            default: true,
        },
        errors: {
            type: Object,
        },
        as: String,
        layout: {
            type: String,
            default: "lsi", //label-> span -> input
        },
    },
    methods: {
        getInput() {
            const input = JSON.parse(JSON.stringify(this.input));
            var id = this.id;
            if (!!this.id) {
                if (id.indexOf("...") > -1) {
                    id = input.id + this.id.replace("...", "");
                }
                delete input.id;
            }
            // if (["InputFetch"].indexOf(this.as) > -1) {
            //     input.form = this.form;
            // }
            return { input, id };
        },
        layouts() {
            const { input, id } = this.getInput();
            return {
                lsi: h("div", {}, [
                    h(
                        "label",
                        {
                            class: "flex border-b border-gray-200 h-12 py-3 items-center",
                        },
                        [
                            h(
                                "span",
                                {
                                    class: "flex-shrink-0 text-right px-2 ",
                                },
                                this.input.label
                            ),
                            h(this.resolveName, {
                                class: "focus:outline-none px-3 border-0 w-full",
                                placeholder: this.input.label,
                                value: this.modelValue,
                                id: id,
                                ...input,
                                ...this.$attrs,
                                oninput: ($event) => this.$emit("update:modelValue", $event.target.value),
                            }),
                        ]
                    ),
                    this.hasErrors
                        ? h(
                              "p",
                              {
                                  class: "text-sm text-red-600 ml-2",
                              },
                              this.localErrors
                          )
                        : null,
                ]),
                table: h(this.resolveName, {
                    class: " focus:outline-none border-0 w-full h-full",
                    'input-in': 'table',
                    placeholder: this.input.label,
                    value: this.modelValue,
                    id: id,
                    form: this.form,
                    ...input,
                    ...this.$attrs,
                    oninput: ($event) => this.$emit("update:modelValue", $event.target.value),
                }),
            };
        },
    },
    computed: {
        localErrors() {
            //console.log(this.errors);
            return !!this.$page.props.errors[this.input.key] ? this.$page.props.errors[this.input.key] : !!this.errors[this.input.key] ? (Array.isArray(this.errors[this.input.key]) ? this.errors[this.input.key].join(", ") : this.errors[this.input.key]) : null;
        },
        hasErrors() {
            return (this.withErrors && this.$page.props.errors[this.input.key]) || (this.errors && this.errors[this.input.key]);
        },
        needForm() {
            return ["InputFetch"].indexOf(this.as) > -1;
        },
        renderData() {
            return this.layouts()[this.layout];
        },
    },
    emits: ["update:modelValue"],
});
</script>
