<template>
    <div class="overflow-auto max-w-screen-2xl lg:h-max h-auto">
        <table class="w-max table-fixed">
            <thead class="sticky top-0 z-10">
                <tr
                    class="text-md font-semibold tracking-wide text-left text-white bg-gray-900 uppercase h-6"
                >
                    <th
                        class="border-l-2 border-white sticky left-0 bg-gray-900"
                    >
                        <div class="flex items-center mx-2.5">
                            <div>Item</div>
                            <div v-if="!!canAdd">
                                <PlusCircleIcon
                                    class="cursor-pointer w-5 h-5"
                                    @click="add"
                                    v-if="canAdd"
                                />
                            </div>
                        </div>
                    </th>
                    <th
                        class="text-center p-3 border-l-2 border-white break-words"
                        :class="{ 'bg-gray-700': index === isEditing[1] }"
                        v-for="(col, colKey, index) in cols"
                        :key="index"
                        :data-col="col.key"
                    >
                        {{ col.header }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-gray-100">
                <tr v-for="(row, indexRow) in form" :key="indexRow">
                    <td
                        class="text-md text-white font-semibold w-0 p-3 text-center uppercase sticky left-0"
                        :class="{
                            'bg-gray-700': indexRow === isEditing[0],
                            'bg-gray-900': indexRow != isEditing[0],
                        }"
                    >
                        <div class="flex justify-evenly">
                            {{ indexRow }}

                            <TrashIcon
                                class="cursor-pointer w-5 h-5"
                                @click="form.splice(indexRow, 1)"
                                v-if="canDelete"
                            />
                        </div>
                    </td>
                    <td
                        :class="{
                            'hover:border-2 hover:border-blue-700 cursor-pointer bg-white':
                                !!col.is || !!col.modal,
                        }"
                        v-for="(col, colKey, indexCol) in cols"
                        :key="indexCol"
                        @dblclick="dbclick(indexRow, indexCol, colKey)"
                    >
                        <div
                            v-if="
                                !!!col.is ||
                                !(
                                    isEditing[0] === indexRow &&
                                    isEditing[1] === indexCol
                                )
                            "
                        >
                            {{ $filters.format(row[col.key], col.format) }}
                            <!-- {{ isNaN(row[col.key]) ? row[col.key] : $filters.format(col.format, row[col.key]) }} -->
                            <!-- {{ col.inputType === "Chbx" ? (row[col.key] ? col.valueTrue || true : col.valueFalse || false) : col.inputType === "MySelect" ? (!!col.bind.data[row[col.key]] ? col.bind.data[row[col.key]].name : row[col.key]) : row[col.key] }} -->
                        </div>
                        <!-- !!!col.inputType || !(isEditing[0] === indexRow && isEditing[1] === indexCol) -->

                        <!-- <component
                            :is="col.inputType"
                            class="w-full focus:outline-none"
                            :id="'input-' + indexRow + '-' + indexCol + (col.inputType === 'MyInputModal' ? '$$' : '')"
                            v-else
                            :row="row"
                            :value="row[col.key]"
                            v-model:checked="row[col.key]"
                            v-model="row[col.key]"
                            v-bind="col.bind"
                            @selected="selectionModal($event, col, row)"
                            @keydown.enter.prevent="applyNext(indexRow + 1, indexCol, row, col)"
                            @keydown.tab.prevent="applyNext(indexRow, indexCol + 1, row, col)"
                            @keydown.esc.prevent="isEditing = [null, null]"
                        /> -->
                        <input-group
                            :input="col"
                            :as="col.is"
                            v-model="form[indexRow][col.key]"
                            :form="form[indexRow]"
                            v-else
                            layout="table"
                            :id="'input-' + indexRow + '-' + indexCol"
                            @keydown.enter.prevent="
                                applyNext(
                                    indexRow + ($event.shiftKey ? -1 : 1),
                                    indexCol,
                                    row
                                )
                            "
                            @keydown.tab.prevent="
                                applyNext(
                                    indexRow,
                                    indexCol + ($event.shiftKey ? -1 : 1),
                                    row
                                )
                            "
                            @keydown.esc.prevent="isEditing = [null, null]"
                        />
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- <div v-if="!!cols[isEditing[1]]">
            <component :is="cols[isEditing[1]].modal" v-model:open="openModal" @select="selectionModal" v-model:data="cols[isEditing[1]].data" v-if="!!cols[isEditing[1]].url" :url="cols[isEditing[1]].url"></component>
        </div> -->
    </div>
</template>

<script>
import { defineComponent } from "vue";
import { TrashIcon, PlusCircleIcon } from "@heroicons/vue/solid";
import { toRefs } from "vue";
import InputGroup from "./InputGroup.vue";

export default defineComponent({
    // setup(props) {
    //     const { emptyValue } = toRefs(props);
    //     const indexes = Object.keys(emptyValue.value);
    //     return { indexes };
    // },
    //emits: ["update:modelValue"],
    props: {
        //modelValue: Object,
        cols: Object,
        form: Object,
        emptyValue: Object,
        canAdd: {
            default: true,
        },
        canDelete: {
            default: true,
        },
    },
    components: {
        TrashIcon,
        PlusCircleIcon,
        InputGroup,
    },
    data() {
        return {
            isEditing: [null, null],
            indexes: Object.keys(this.cols),
            valCols: Object.values(this.cols),
            openModal: false,
            selected: null,
        };
    },
    methods: {
        add() {
            this.form.push(JSON.parse(JSON.stringify(this.emptyValue)));
        },
        dbclick(indexRow, indexCol, colKey) {
            // Ok aqui ira la logica para abrir el portal y traer la informacion del servidor, esta funcion lo que hara es crear una tabla para buscar la informacion para el axios
            this.isEditing = [indexRow, indexCol];
            if (!!!this.cols[colKey].is) return;
            this.focus(indexRow, indexCol);
        },
        applyNext(indexRow, indexCol, row) {
            this.next(indexRow, indexCol);
            this.updateFormulas(row);
        },
        next(indexRow, indexCol) {
            const dataLen = this.form.length - 1,
                colLen = this.indexes.length - 1;
            if (indexCol > colLen) {
                indexCol = 0;
                indexRow = indexRow + 1;
            }
            if (indexRow > dataLen) {
                indexRow = 0;
            }
            if (!!this.cols[this.indexes[indexCol]].is) {
                this.isEditing = [indexRow, indexCol];
                this.focus(indexRow, indexCol);
            } else {
                this.next(indexRow, indexCol + 1);
            }
        },
        focus(indexRow, indexCol) {
            this.$nextTick(() => {
                const inp = document.getElementById(
                    "input-" + indexRow + "-" + indexCol
                );
                if (!!inp) {
                    inp.focus();
                    return true;
                }
            });
        },
        updateFormulas(row) {
            this.valCols.forEach((x) => {
                if (!!x.formula) {
                    if (typeof x.formula === "string") {
                        this.chooseFormula(x, row);
                    } else {
                        x.formula(row);
                    }
                }
            });
        },
        chooseFormula(col, row) {
            const name = /^(.*?)\(/.exec(col.formula)[1].toLowerCase();
            const values = /\(([^)]+)\)/.exec(col.formula)[1].split(",");
            this[name](values, row, col.key);
        },
        product(values, row, key) {
            const v = values.reduce(
                (pv, cv) =>
                    (/^\d+$/.test(cv) ? cv : parseFloat(row[cv] || 0)) * pv,
                1
            );
            row[key] = isNaN(v) ? 0 : v.toFixed(2);
        },
        sum(values, row, key) {
            const v = values.reduce(
                (pv, cv) =>
                    (/^\d+$/.test(cv) ? cv : parseFloat(row[cv] || 0)) + pv,
                0
            );
            row[key] = isNaN(v) ? 0 : v.toFixed(2);
        },
        subtraction(values, row, key) {
            const v = values.reduce((pv, cv) => {
                //console.log(pv, cv, row[cv]);
                return (/^\d+$/.test(cv) ? cv : parseFloat(row[cv] || 0)) - pv;
            }, 0);
            row[key] = isNaN(v) ? 0 : v.toFixed(2);
        },
        sumw(values, row, key) {
            var signs = values.splice(-1)[0];
            const v = values.reduce((pv, cv, ind) => {
                console.log(pv, cv, row[cv]);
                return (
                    (/^\d+$/.test(cv)
                        ? cv
                        : parseFloat(signs[ind] + row[cv] || 0)) + pv
                );
            }, 0);
            row[key] = isNaN(v) ? 0 : v.toFixed(2);
        },
        divisionper(values, row, key) {
            const v =
                ((/^\d+$/.test(values[0])
                    ? values[0]
                    : parseFloat(row[values[0]] || 0)) /
                    (/^\d+$/.test(values[1])
                        ? values[1]
                        : parseFloat(row[values[1]] || 0))) *
                100;
            row[key] = isNaN(v) ? 0 : v.toFixed(2);
        },
    },
});
</script>
<style scoped>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type="number"] {
    -moz-appearance: textfield;
}
</style>
