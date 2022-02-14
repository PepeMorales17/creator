<template>
    <!-- BEGIN: Show Modal Toggle -->
    <component :is="toggle.is" :id="'toggle' + id" :class="toggle.class" @click="modal().show()">
        <slot name="toggle" />
    </component>
    <!-- END: Show Modal Toggle -->
    <!-- BEGIN: Modal Content -->

    <div :id="id" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" :class="size">
            <div class="modal-content p-4">
                <slot name="content" :modal="modal"></slot>
            </div>
        </div>
    </div>
    <!-- END: Modal Content -->
</template>

<script>
import { defineComponent, ref } from "vue";
export default defineComponent({
    inheritAttrs: false,
    props: {
        size: {
            default: "modal-md",
        },
        id: {
            required: true,
        },
        toggle: {
            default() {
                return {
                    class: "btn btn-primary mr-1 mb-2",
                    is: "button",
                };
            },
        },
    },
    methods: {
        modal() {
            return tailwind.Modal.getOrCreateInstance(document.querySelector("#" + this.id));
        },
    },
});
</script>
