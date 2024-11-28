import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
        "./node_modules/flowbite/**/*.js"
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
                roboto:['Roboto','sans-serif'],
                robotoBold: ['Roboto-Medium', 'sans-serif']
            },
            colors: {
                "my-gray": "#F2F4F8",
                "my-nav-text": "#21272A",
                "side-gray-text": "#697077",
                "selected-blue":"#001D6C",
                "input-gray" : "#F3F3F8",
                'violet':"#644DED14",
                'table-gray': "#F9F9FC",
                'violet-full':"#644DED",
                'red-warning': "#DA1E28",
                'gray-inactive':'#687182',
                'my-black': "#121619",
                'table-heading':'#484964',


                'danger': '#DA1E28',
                'text'  : '#21272A'
            },
            screens: {
                sm: "370px",
                // => @media (min-width: 640px) { ... }

                md: "768px",
                // => @media (min-width: 768px) { ... }

                lg: "1024px",
                // => @media (min-width: 1024px) { ... }

                xl: "1280px",
                // => @media (min-width: 1280px) { ... }

                "2xl": "1440px",
                // => @media (min-width: 1536px) { ... }

                "3xl": "1536px",
                // => @media (min-width: 1536px) { ... }
            },
        },
    },

    /*plugins: [
        forms,
        require('flowbite/plugin')
    ],*/
};
