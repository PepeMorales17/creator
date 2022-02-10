<template>
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <slot name="beforeTable"></slot>
                    <table class="min-w-full divide-y divide-gray-200">
                        <slot name="head">
                            <thead class="bg-gray-50">
                                <tr>
                                    <slot name="theadInit"></slot>
                                    <th scope="col" v-if="thOnInit" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                    <template v-for="(title, index) in titles" :key="index">
                                        <slot name="th" :title="title">
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ title }}
                                            </th>
                                        </slot>
                                    </template>
                                    <slot name="theadEnd"></slot>
                                </tr>
                            </thead>
                        </slot>
                        <slot name="body" :titles="titles">
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr class="simple-tr" :class="{ 'cursor-pointer hover:bg-gray-300': !!$attrs.onSelect }" v-for="(d, ind) in data" :key="d.id || ind" @click="!!$attrs.onSelect ? $emit('select', d) : null">
                                    <template v-for="(key, index) in titles" :key="index">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $filters.formatByKey(key, d[key]) }}
                                        </td>
                                    </template>
                                </tr>
                            </tbody>
                        </slot>
                    </table>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import { defineComponent } from "vue";
import { EyeIcon, PencilIcon, TrashIcon } from "@heroicons/vue/solid";

export const BaseTd = defineComponent({
    template:  `
        <td class="px-6 py-4" :td-table="id">
            <slot>
                {{ $filters.formatByKey(id, item[id]) }}
            </slot>
        </td>
  `,
  props: ['item', 'id']
});
export default defineComponent({
    name: "BaseTable",
    components: {
        PencilIcon,
        EyeIcon,
        TrashIcon,
    },
    props: {
        data: {
            required: true,
            default() {
                return [];
            },
        },
        keys: Array,
        thOnInit: {
            default: false
        }
    },
    computed: {
        titles() {
            return this.keys ? this.keys : this.data[0] ? Object.keys(this.data[0]) : [];
        },
    },
});
</script>
