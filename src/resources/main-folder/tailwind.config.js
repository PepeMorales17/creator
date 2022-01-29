const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                orange: {
                    900: "#ff4800ff",
                    800: "#ff5400ff",
                    700: "#ff6000ff",
                    600: "#ff6d00ff",
                    500: "#ff7900ff",
                    400: "#ff8500ff",
                    300: "#ff9100ff",
                    200: "#ff9e00ff",
                    100: "#ffaa00ff",
                    50: "#ffb600ff"
                },
            },
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
