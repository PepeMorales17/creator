<script setup>
import { onMounted } from "vue";
//import DarkModeSwitcher from "@/components/dark-mode-switcher/Main.vue";
import dom from "@left4code/tw-starter/dist/js/dom";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";

onMounted(() => {
    dom("body").removeClass("main").removeClass("error-page").addClass("login");
});

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <div>
        <Head title="Iniciar sesion" />
        <!-- <DarkModeSwitcher /> -->
        <div class="container sm:px-10">
            <div class="block xl:grid grid-cols-2 gap-4">
                <!-- BEGIN: Login Info -->
                <div class="hidden xl:flex flex-col min-h-screen">
                    <a href="" class="-intro-x flex items-center pt-5">
                        <img
                            alt="Rubick Tailwind HTML Admin Template"
                            class="w-6"
                            src="storage/images/logo.svg"
                        />
                        <span class="text-white text-lg ml-3">
                            GrupoTermo
                        </span>
                    </a>
                    <div class="my-auto">
                        <img
                            alt="Rubick Tailwind HTML Admin Template"
                            class="-intro-x w-1/2 -mt-16"
                            src="storage/images/illustration.svg"
                        />
                        <div
                            class="-intro-x text-white font-medium text-4xl leading-tight mt-10"
                        >
                            A few more clicks to <br />
                            sign in to your account.
                        </div>
                        <div
                            class="-intro-x mt-5 text-lg text-white text-opacity-70 dark:text-slate-400"
                        >
                            Manejo de las empresas
                        </div>
                    </div>
                </div>
                <!-- END: Login Info -->

                <BreezeValidationErrors class="mb-4" />

                <div
                    v-if="status"
                    class="mb-4 font-medium text-sm text-green-600"
                >
                    {{ status }}
                </div>

                <!-- BEGIN: Login Form -->
                <div class="h-screen xl:h-auto flex py-5 xl:py-0 my-10 xl:my-0">
                    <div
                        class="my-auto mx-auto xl:ml-20 bg-white dark:bg-darkmode-600 xl:bg-transparent px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto"
                    >
                        <h2
                            class="intro-x font-bold text-2xl xl:text-3xl text-center xl:text-left"
                        >
                            Iniciar sesion
                        </h2>
                        <div
                            class="intro-x mt-2 text-slate-400 xl:hidden text-center"
                        >
                            A few more clicks to sign in to your account. Manage
                            all your e-commerce accounts in one place
                        </div>
                        <div class="intro-x mt-8">
                            <input
                                type="text"
                                class="intro-x login__input form-control py-3 px-4 block"
                                placeholder="Email"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                            />
                            <input
                                type="password"
                                class="intro-x login__input form-control py-3 px-4 block mt-4"
                                placeholder="Password"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                            />
                        </div>
                        <div
                            class="intro-x flex text-slate-600 dark:text-slate-500 text-xs sm:text-sm mt-4"
                        >
                            <div class="flex items-center mr-auto">
                                <input
                                    id="remember-me"
                                    type="checkbox"
                                    class="form-check-input border mr-2"
                                    v-model="form.remember"
                                />
                                <label
                                    class="cursor-pointer select-none"
                                    for="remember-me"
                                    >Remember me</label
                                >
                            </div>
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="underline text-sm text-gray-600 hover:text-gray-900"
                            >
                                Forgot your password?
                            </Link>

                        </div>
                        <div
                            class="intro-x mt-5 xl:mt-8 text-center xl:text-left"
                        >
                            <button
                                class="btn btn-primary py-3 px-4 w-full xl:w-32 xl:mr-3 align-top"
                                :disabled="form.processing"
                                @click="submit"
                            >
                                Login
                            </button>
                            <!-- <button
                                class="btn btn-outline-secondary py-3 px-4 w-full xl:w-32 mt-3 xl:mt-0 align-top"
                            >
                                Register
                            </button> -->
                        </div>
                        <div
                            class="intro-x mt-10 xl:mt-24 text-slate-600 dark:text-slate-500 text-center xl:text-left"
                        >
                            By signin up, you agree to our
                            <a class="text-primary dark:text-slate-200" href=""
                                >Terms and Conditions</a
                            >
                            &
                            <a class="text-primary dark:text-slate-200" href=""
                                >Privacy Policy</a
                            >
                        </div>
                    </div>
                </div>
                <!-- END: Login Form -->
            </div>
        </div>
    </div>
</template>
