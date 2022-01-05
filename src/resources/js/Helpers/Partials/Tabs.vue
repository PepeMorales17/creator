<template>
    <div class="w-full px-2 py-8 sm:px-0">
        <TabGroup>
            <TabList class="flex p-1 space-x-1 bg-blue-900/20 rounded-xl">
                <!-- <slot name="nav" :selected="selected"> -->
                    <Tab v-for="k in keys" as="template" :key="k" v-slot="{ selected }">
                        <button
                            :class="[
                                'w-full py-1 text-sm leading-5 font-medium text-blue-700 rounded-lg',
                                'focus:outline-none focus:ring-2 ring-offset-2 ring-offset-blue-400 ring-white ring-opacity-60',
                                selected ? 'bg-white shadow' : 'text-blue-100 hover:bg-white/[0.12] hover:text-white',
                            ]"
                        >
                            {{ k }}
                        </button>
                    </Tab>
                <!-- </slot> -->
            </TabList>

            <TabPanels class="mt-2">
                <TabPanel v-for="(value, idx) in data" :key="idx" :class="['bg-white rounded-xl p-3', 'focus:outline-none focus:ring-2 ring-offset-2 ring-offset-blue-400 ring-white ring-opacity-60']">
                    <slot name="body" :value="value">
                        <slot :name="idx" :value="value">{{ value }}</slot>
                    </slot>
                </TabPanel>
            </TabPanels>
        </TabGroup>
    </div>
</template>

<script>
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";

export default {
    components: {
        TabGroup,
        TabList,
        Tab,
        TabPanels,
        TabPanel,
    },
    props: {
        keys: {
            type: Array,
            required: true,
        },
        values: {
            type: Array,
            required: false,
        },
    },
    data() {
        return {
            data: !!this.values ? this.values : this.keys,
        };
    },
};
</script>
