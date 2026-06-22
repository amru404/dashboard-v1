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
            colors: {
                madani: {
                    green: '#1B8B4B',
                    deep: '#013317',
                    depth: '#00200F',
                    success: '#2EAF63',
                    offwhite: '#F7FAF8',
                    text: '#1A1A1A',
                    muted: '#555555',
                    pale: '#EDF2EE',
                    ghost: '#FAFDF9',
                    border: 'rgba(0, 0, 0, 0.06)',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                madani: '0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 16px rgba(0, 0, 0, 0.04)',
                'madani-lg': '0 20px 60px rgba(0, 0, 0, 0.12)',
            },
        },
    },

    plugins: [forms],
};
