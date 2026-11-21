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
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
                display: ['Space Grotesk', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                vmt: {
                    ink: '#242034',
                    muted: '#665f74',
                    purple: '#6135bc',
                    'purple-hover': '#4c269c',
                    'purple-active': '#3e207e',
                    lilac: '#eee8fb',
                    paper: '#faf9fc',
                    line: '#e3deec',
                    'input-border': '#c5bed2',
                    orange: '#ffbe7b',
                },
            },
        },
    },

    plugins: [forms],
};
