import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

// Theme colors that can be selected per team
const themeColors = ['emerald', 'blue', 'indigo', 'violet', 'rose', 'amber', 'cyan', 'lime', 'teal', 'sky', 'purple', 'pink', 'orange', 'green'];
const colorShades = ['100', '300', '400', '500', '600', '700', '800', '900'];

// Generate safelist for dynamic theme colors
const themeSafelist = themeColors.flatMap(color => [
    // Background colors
    ...colorShades.map(shade => `bg-${color}-${shade}`),
    ...colorShades.map(shade => `dark:bg-${color}-${shade}`),
    // Text colors
    ...colorShades.map(shade => `text-${color}-${shade}`),
    ...colorShades.map(shade => `dark:text-${color}-${shade}`),
    // Hover states
    ...colorShades.map(shade => `hover:bg-${color}-${shade}`),
    ...colorShades.map(shade => `hover:text-${color}-${shade}`),
    ...colorShades.map(shade => `dark:hover:bg-${color}-${shade}`),
    ...colorShades.map(shade => `dark:hover:text-${color}-${shade}`),
    // Focus states
    ...colorShades.map(shade => `focus:bg-${color}-${shade}`),
    ...colorShades.map(shade => `focus:ring-${color}-${shade}`),
    ...colorShades.map(shade => `focus:border-${color}-${shade}`),
    // Active states
    ...colorShades.map(shade => `active:bg-${color}-${shade}`),
    // Border colors
    ...colorShades.map(shade => `border-${color}-${shade}`),
    ...colorShades.map(shade => `dark:border-${color}-${shade}`),
    // Ring colors
    ...colorShades.map(shade => `ring-${color}-${shade}`),
]);

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: themeSafelist,

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],
};
