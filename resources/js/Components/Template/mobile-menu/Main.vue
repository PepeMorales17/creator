<script setup>
import { computed, onMounted, ref, watch } from "vue";
//import { useRoute, useRouter } from "vue-router";
import { helper as $h } from "@/utils/helper";
import BarChart2Icon from "@zhuowenli/vue-feather-icons/icons//BarChart2Icon";
//import { useSideMenuStore } from "@/stores/side-menu";
import Logo from "@/Helpers/Logo";

import {
  activeMobileMenu,
  toggleMobileMenu,
  linkTo,
  enter,
  leave,
} from "./index";
import { nestedMenu } from "@/utils/MenuUtils";

const formattedMenu = ref([]);
//const sideMenuStore = useSideMenuStore();
//const mobileMenu = computed(() => nestedMenu(sideMenuStore.menu, route));

// watch(
//   computed(() => route.path),
//   () => {
//     formattedMenu.value = $h.toRaw(mobileMenu.value);
//   }
// );

// onMounted(() => {
//   formattedMenu.value = $h.toRaw(mobileMenu.value);
// });
</script>


<template>
  <!-- BEGIN: Mobile Menu -->
  <div class="mobile-menu md:hidden">
    <div class="mobile-menu-bar">
     <Logo :withText="false" class="flex mr-auto"></Logo>
      <a href="javascript:;" id="mobile-menu-toggler">
        <BarChart2Icon
          class="w-8 h-8 text-white transform -rotate-90"
          @click="toggleMobileMenu"
        />
      </a>
    </div>
    <transition @enter="enter" @leave="leave">
      <ul
        v-if="activeMobileMenu"
        class="border-t border-white/[0.08] py-5 hidden"
      >
        <!-- BEGIN: First Child -->

        <!-- END: First Child -->
      </ul>
    </transition>
  </div>
  <!-- END: Mobile Menu -->
</template>
