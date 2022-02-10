<template>
    <BaseTable :data="data" :keys="keys" :thOnInit="thOnInit">
        <template #beforeTable v-if="!!multiple">
            <div class="m-4 p-4"><input type="checkbox" @change="selectAll" ref="check" /> Seleccionar todos</div>
        </template>
        <template #body="{ titles }">
            <tbody class="bg-white divide-y divide-gray-200">
                <tr
                    class="simple-tr"
                    :class="{ 'cursor-pointer hover:bg-gray-300': !!$attrs.onSelect, 'bg-gray-300': !!multiple && multiple[ind] }"
                    v-for="(d, ind) in data"
                    :key="d.id || ind"
                    @click="!!$attrs.onSelect ? $emit('select', d) : !!multiple ? addToMultiple(d, ind) : null"
                >
                    <BaseTd id="action-td" v-if="!!$attrs.onEdit || !!$attrs.onDelete || !!$attrs.onView || !!$slots.edit" class="px-6 py-4 text-right text-sm font-medium flex">
                        <slot name="edit" :item="d">
                            <EditIcon @click.stop="$emit('edit', d)" v-if="!!$attrs.onEdit" class="cursor-pointer w-5 h-5 m-auto" />
                        </slot>
                        <EyeIcon @click.stop="$emit('view', d)" v-if="!!$attrs.onView" class="cursor-pointer w-5 h-5 m-auto" />
                        <TrashIcon @click.stop="$emit('trash', d)" v-if="!!$attrs.onTrash" class="cursor-pointer w-5 h-5 m-auto" />
                    </BaseTd>
                    <template v-for="(key, index) in titles" :key="index">
                        <slot :name="key" :item="d" :value="d[key]" :k="key">
                            <BaseTd :id="key" :item="d" v-if="!!colsWithObject[key]">
                                {{ !!d[key] ? d[key][colsWithObject[key]] : null }}
                            </BaseTd>

                            <BaseTd :id="key" :item="d" v-else />
                        </slot>
                    </template>

                    <slot name="more" :item="d" />
                </tr>
            </tbody>
        </template>
    </BaseTable>
</template>

<script>
export const TableHelper = {
    data() {
        return {
            _dataTable: this.dataTable,
        };
    },
};
import { defineComponent, reactive } from "vue";

import EyeIcon from "@zhuowenli/vue-feather-icons/icons//EyeIcon";
import {EditIcon} from "@zhuowenli/vue-feather-icons/dist/vue-feather-icons.cjs";
import TrashIcon from "@zhuowenli/vue-feather-icons/icons//TrashIcon";
import BaseTable, { BaseTd } from "@/Helpers/Partials/Tables/BaseTable.vue";

export default defineComponent({
    name: "Stable",
    components: {
        EditIcon,
        EyeIcon,
        TrashIcon,
        BaseTable,
        BaseTd,
    },
    props: {
        data: {
            required: true,
            default() {
                return [];
            },
        },
        colsWithObject: {
            default() {
                return {};
            },
        },
        keys: Array,
        thOnInit: {
            default: true,
        },
        multiple: Object,
    },
    methods: {
        addToMultiple(d, ind) {
            if (!!this.multiple[ind]) {
                delete this.multiple[ind];
                return;
            }
            this.multiple[ind] = d;
        },
        selectAll() {
            this.data.map((x, i) => this.addToMultiple(x, i));
        },
    },
    watch: {
        multiple: {
            deep: true,
            handler(value, old) {
                const check = this.$refs.check,
                    len = Object.keys(value).length;
                if (len < this.data.length && len > 0) {
                    check.indeterminate = true;
                } else if (len === 0) {
                    check.indeterminate = false;
                    check.checked = false;
                } else if (len === this.data.length) {
                    check.indeterminate = false;
                    check.checked = true;
                }
            },
        },
    },
});
</script>
