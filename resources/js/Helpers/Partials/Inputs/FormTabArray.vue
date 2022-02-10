<template>
    <ul class="flex justify-around" v-if="form.length > 1">
        <button
            v-for="(item, index) in form"
            :key="index"
            :class="[
                'w-full py-1 text-sm leading-5 font-medium text-orange-700 rounded-lg',
                'focus:outline-none focus:ring-2 ring-offset-2 ring-offset-orange-400 ring-white ring-opacity-60',
                selected == index ? 'bg-blue-100 shadow' : 'text-orange-100 hover:bg-white/[0.12] hover:text-black',
            ]"
            @click="selected = index"
        >
            <div class="flex justify-around      ">
                <span>
                    <slot name="head" :item="item" :index="index">
                        <!-- {{ index + 1 + " " + (item ? (item.owner ?? '') : '') }} -->
                        {{ index + 1 }}
                    </slot>
                </span>
                <TrashIcon class="w-4 h-4" @click.stop="del" />
            </div>
        </button>
    </ul>
    <div class="flex justify-end p-3" v-if="form">
        <button class="btn-pri" @click="add"><PlusIcon class="w-5 h-5" /></button>
        <button class="btn-dan" @click="del" v-if="form.length === 1"><TrashIcon class="w-5 h-5" /></button>
    </div>
    <template v-if="form.length">
        {{form.errors}}
        <slot name="inputs" :selected="selected">
            <input-group :input="input" :as="input.is" v-for="(input, index) in inputs" :key="index" v-model="form[selected][input.key]" />
        </slot>
    </template>
</template>

<script>
import { defineComponent } from "vue";
import InputGroup from "./InputGroup.vue";
import TrashIcon from "@zhuowenli/vue-feather-icons/icons//TrashIcon";
import PlusIcon from "@zhuowenli/vue-feather-icons/icons//PlusIcon";

export default defineComponent({
    props: ["form", "inputs", "emptyValue"],

    components: { InputGroup, TrashIcon, PlusIcon },

    // mounted() {
    //     this.add(false);
    // },

    data() {
        return {
            selected: 0,
        };
    },

    methods: {
        add() {
            this.form.push(JSON.parse(JSON.stringify(this.emptyValue)));
            if (this.form.length > 1) {
                this.selected++;
            }
        },
        del() {
            const sel = this.selected;
            this.selected = this.selected === 0 ? 0 : this.selected - 1;
            this.form.splice(sel, 1);
        },
    },
});
</script>
