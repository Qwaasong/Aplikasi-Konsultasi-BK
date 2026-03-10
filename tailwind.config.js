import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                "brand-teal": "#086375",
                "brand-teal-light": "#E0F7FA",
                "brand-dark": "#064e5c",
                primary: "#044B5F",
                "primary-hover": "#033948",
                "bg-light": "#F4F9FA",
                "border-light": "#E2E8F0",
                "icon-bg": "#7C9EA6",
            },
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
            },
            transitionProperty: {
                width: "width",
                spacing: "margin, padding",
            },
        },
    },
    plugins: [forms],
};
