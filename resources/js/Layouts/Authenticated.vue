<script setup>
import { onMounted } from "vue";
import { helper as $h } from "@/utils/helper";
import { Link } from "@inertiajs/inertia-vue3";
import TheMenu from "@/Helpers/Menu.vue";

import { AuthIcon } from "@/Helpers/Partials/Icons/AppIcons.js";
import MobileMenu from "@/Helpers/MobilMenu.vue";
import Logo from "@/Helpers/Logo";
import SwitchDark from "@/Helpers/Partials/Utils/DarkMode/SwitchDark.vue";
import FileView from "@/Pages/Creator/utils/FileView.vue";

import dom from "@left4code/tw-starter/dist/js/dom";

onMounted(() => {
  dom("body").removeClass("error-page").removeClass("login").addClass("main");
});
</script>

<template>
  <div class="py-2">
    <!-- <MainColorSwitcher /> -->
    <MobileMenu :menus="$page.props.main_menu" />
    <!-- BEGIN: Top Bar -->
    <div
      class="border-b border-white/[0.08] -mt-10 md:-mt-5 -mx-3 sm:-mx-8 px-3 sm:px-8 pt-3 md:pt-0 mb-10"
    >
      <div class="top-bar-boxed flex items-center">
        <!-- BEGIN: Logo -->
        <Logo class="-intro-x hidden md:flex"></Logo>
        <!-- END: Logo -->
        <!-- BEGIN: Breadcrumb -->
        <nav aria-label="breadcrumb" class="-intro-x h-full mr-auto">
          <ol class="breadcrumb breadcrumb-light">
            <li class="breadcrumb-item">
              <a href="#">App</a>
            </li>
          </ol>
        </nav>

        <div class="intro-x dropdown w-8 h-8">
          <div
            class="dropdown-toggle w-8 h-8 rounded-full overflow-hidden shadow-lg image-fit zoom-in scale-110"
            role="button"
            aria-expanded="false"
            data-tw-toggle="dropdown"
          >
            <img alt="avatar" src="/storage/images/avatar.svg" />
          </div>
          <div class="dropdown-menu w-56">
            <ul
              class="dropdown-content bg-primary/80 before:block before:absolute before:bg-black before:inset-0 before:rounded-md before:z-[-1] text-white"
            >
              <li class="p-2">
                <div class="font-medium">
                  {{ $page.props.auth.user.name }}
                </div>
                <!-- <div
                                    class="text-xs text-white/60 mt-0.5 dark:text-slate-500"
                                >
                                    {{ $f()[0].jobs[0] }}
                                </div> -->
              </li>
              <li>
                <hr class="dropdown-divider border-white/[0.08]" />
              </li>
              <li>
                <Link
                  :href="route('folder.index')"
                  as="button"
                  class="dropdown-item hover:bg-white/5"
                >
                  <AuthIcon.FolderIcon class="w-4 h-4 mr-2" />
                  Finder
                </Link>
              </li>
              <li>
                <SwitchDark />
              </li>
              <li>
                <Link
                  :href="route('logout')"
                  as="button"
                  method="post"
                  class="dropdown-item hover:bg-white/5"
                >
                  <AuthIcon.ToggleRightIcon class="w-4 h-4 mr-2" />
                  Logout
                </Link>
              </li>
            </ul>
          </div>
        </div>
        <!-- END: Account Menu -->
      </div>
    </div>
    <!-- END: Top Bar -->
    <!-- BEGIN: Top Menu -->
    <nav class="top-nav">
      <TheMenu :menus="$page.props.main_menu"></TheMenu>
    </nav>
    <!-- END: Top Menu -->
    <!-- BEGIN: Content -->
    <div class="content">
      <slot />
    </div>
    <!-- END: Content -->
  </div>
</template>
