<template>
    <div class="bg-white rounded-lg relative">
        <div class="w-full lg:m-auto lg:w-10/12">
            <nav-links :menu="menu" />
            <h1 class="text-lg font-bold p-5" v-if="!!title">{{ title }}</h1>

            <slot name="header"></slot>

            <div class="mt-4 w-full shadow-lg border-2 border-blue-50 rounded-lg">
                <template v-if="route().current() === initUrl + '.index'">
                    <h1 class="text-lg font-bold p-5">{{ titles.index ?? "" }}</h1>
                    <slot name="index">
                        <s-table
                            :data="dataTable"
                            @view="$inertia.visit(route(initUrl + '.show', uris.show ? uris.show($event.id) : $event.id), { preserveState: false })"
                            @edit="$inertia.visit(route(initUrl + '.edit', uris.edit ? uris.edit($event.id) : $event.id), { preserveState: false })"
                            @trash="trash($event.id)"
                        />
                    </slot>
                </template>
                <template v-else-if="route().current() === initUrl + '.create'">
                    <h1 class="text-lg font-bold p-5">{{ titles.create ?? "" }}</h1>
                    <slot name="create" :form="form">
                        <input-group :input="input" :as="input.is" v-for="(input, index) in inputs.inputs" :key="index" v-model="form[input.key]" :errors="form.errors" />
                    </slot>
                    <div class="flex justify-end p-4">
                        <button class="btn-pri" :disabled="form.processig" @click="store">Guardar</button>
                    </div>
                </template>
                <template v-else-if="route().current() === initUrl + '.edit'">
                    <h1 class="text-lg font-bold p-5">{{ !!titles.edit ? titles.edit + "(ID " + item.id + ")" : "Editar id " + item.id }}</h1>
                    <slot name="edit" :form="form">
                        <slot name="create" :form="form">
                            <input-group :input="input" :as="input.is" v-for="(input, index) in inputs.inputs" :key="index" v-model="form[input.key]" :errors="form.errors" />
                        </slot>
                    </slot>
                    <div class="flex justify-end p-4">
                        <button class="btn-pri" :disabled="form.processig" @click="edit">Editar</button>
                    </div>
                </template>
                <template v-else-if="route().current() === initUrl + '.show' && !!item">
                    <h1 class="text-lg font-bold p-5">{{ !!titles.show ? titles.show + "(ID" + item.id + ")" : "Ver id " + item.id }}</h1>
                    <slot name="show">
                        <slot name="showHeader"></slot>
                        <ul class="flex flex-col just">
                            <li class="flex" v-for="key in Object.keys(item)">
                                <div v-if="Array.isArray(item[key])">
                                    <s-table :data="item[key]" :thOnInit="false" />
                                </div>
                                <div v-else>
                                    <span class="font-bold mr-4"> {{ key }}: </span> <span class=""> {{ $filters.formatByKey(key, item[key]) }} </span>
                                </div>
                            </li>
                        </ul>

                        <div class="flex justify-end p-4">
                            <Link class="btn-pri" as="button" :href="route(initUrl + '.edit', uris.edit ? uris.edit(item.id) : item.id)">Editar</Link>
                            <a :href="route('pdf', { model: initUrl, id: item.id })" class="btn-pri" v-if="canPrint">Imprimir</a>
                            <Link class="btn-pri" as="button" :href="route(initUrl + '.create', uris.edit ? uris.edit(item.id) : { id: item.id })" v-if="canCopy">Copiar</Link>
                        </div>
                    </slot>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
import { defineAsyncComponent, defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import { useForm } from "@inertiajs/inertia-vue3";
import NavLinks from "./Partials/Navs/NavLinks.vue";

export default defineComponent({
    setup(props) {
        var form = null;
        if (!!props.inputs) {
            form = useForm(!!props.value ? props.value : props.inputs.emptyValue);
            console.log(props.inputs, form, props.inputs.emptyValue, props.value);
        }
        return { form };
    },

    components: {
        Link,
        STable: defineAsyncComponent(() => import("./Partials/Tables/STable.vue")),
        InputGroup: defineAsyncComponent(() => import("./Partials/Inputs/InputGroup.vue")),
        NavLinks,
    },
    props: {
        dataTable: Object,
        inputs: Object,
        item: Object,
        value: Object,
        menu: {
            default: [],
        },
        titles: {
            default() {
                return {};
            },
        },
        uris: {
            default() {
                return {};
            },
        },
        canPrint: {
            default: false,
        },
        canCopy: {
            default: false,
        },

        valid: {
            default() {
                const f = () => true;
                return f;
            },
        },
    },

    computed: {
        initUrl() {
            const route = JSON.parse(JSON.stringify(Object.keys(this.menu)[0])).split(".");
            route.splice(-1, 1);
            return route.join(".");
        },
        title() {
            return this.menu[route().current()] ? this.menu[route().current()].title : null;
        },
    },

    methods: {
        trash(id) {
            if (!confirm("¿Estas seguro que quieres borrar este elemento?")) return;
            this.$inertia.delete(route(this.initUrl + ".destroy", this.uris.destroy ? this.uris.destroy(id) : id));
        },
        store() {
            if (this.valid(this.form)) {
                this.form.post(this.uris.store ?? route(this.initUrl + ".store"));
            }
        },
        edit() {
            if (this.valid(this.form)) {
                this.form.put(this.uris.update ?? route(this.initUrl + ".update", this.item.id));
                // form.put(uris.update ?? route(initUrl + '.update', item.id))
            }
        },
    },
});
</script>
