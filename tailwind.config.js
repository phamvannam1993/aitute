import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        screens: {
            'xs': '480px',
            // Custom screen sizes
            'sm': '640px',
            'md': '768px',
            'lg': '1024px',
            'xl': '1280px',
            '2xl': '1536px',
            // Additional custom screen sizes
            '3xl': '1920px',
            '4xl': '2560px',
        },
        extend: {
            fontFamily: {
                lexend: ['"Gilroy"', 'sans-serif'], // Add Lexend Deca font
                sans: [
                    "Gilroy",
                    "Figtree",
                    ...defaultTheme.fontFamily.sans,
                ],
            },
            colors: {
                "custom-gray": "#666666",
                "primary-color": "#0E68EF",
                "brightRed": "hsl(12, 88%, 59%)",
                "brightRedLight": "hsl(12, 88%, 69%)",
                "brightRedSupLight": "hsl(12, 88%, 95%)",
                "darkBlue": "hsl(228, 39%, 23%)",
                "darkGrayishBlue": "hsl(227, 12%, 61%)",
                "veryDarkBlue": "hsl(233, 12%, 13%)",
                "veryPaleRed": "hsl(13, 100%, 96%)",
                "veryLightGray": "hsl(0, 0%, 98%)",
                "orion-primary": "#005F49",
                "orion-orange": "#FFA411",
                "orion-orange-hover": "#FFF2D7",
                "orion-sec": "#24AA69",
                "orion-orange-bg": "#FFFBE2",
                "orion-button-sec": "#7CCE63",
                "ai3goc-bgclr": "#1E405A",
                "ai3goc-primary": "#207A91",
                "ai3goc-sec": "#1E405A",
                "ai3goc-bg-sec": "#042638",
            },
            boxShadow: {
                "custom-shadow": "0px 4px 5.3px 0px rgba(0, 0, 0, 0.26)",
            },
            keyframes: {
                "accordion-down": {
                    from: { height: 0 },
                    to: { height: "var(--radix-accordion-content-height)" },
                },
                "accordion-up": {
                    from: { height: "var(--radix-accordion-content-height)" },
                    to: { height: 0 },
                },
            },
            animation: {
                "accordion-down": "accordion-down 0.2s ease-out",
                "accordion-up": "accordion-up 0.2s ease-out",
            },
            backgroundImage: {
                "layout-background": "assets/img/aiwow/layout_background.png"
            },
            fontSize: {
                '14': '14px'
            },
            height: {
                '40vh': '40vh'
            }
        },
    },

    plugins: [
        forms,
    ],
};
