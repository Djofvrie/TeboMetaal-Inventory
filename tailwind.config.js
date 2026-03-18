import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                indigo: {
                    50: '#f0f7fa',
                    100: '#d9edf3',
                    200: '#b3dbe7',
                    300: '#80c3d6',
                    400: '#4ba5be',
                    500: '#227192',
                    600: '#1d6180',
                    700: '#18516b',
                    800: '#134157',
                    900: '#0e3143',
                    950: '#091f2b',
                },
            },
        },
    },

    plugins: [forms],
};
