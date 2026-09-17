/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        'bloom-purple': '#9333ea',
        'bloom-purple-dark': '#7e22ce',
        'bloom-purple-deep': '#581c87',
        'bloom-purple-light': '#f3e8ff',
        'bloom-indigo': '#6366f1',
        'bloom-indigo-dark': '#4f46e5',
        'bloom-rose': '#f43f5e',
        'bloom-rose-light': '#ffe4e6',
        'bloom-gold': '#f59e0b',
        'bloom-cream': '#fbfbf9',
        'bloom-cream-alt': '#f4f2ed',
        'bloom-slate': '#64748b',
        'bloom-slate-dark': '#475569',
        'bloom-dark-bg': '#0f1016',
        'bloom-dark-card': '#161822',
        'bloom-dark-border': '#232738',
      },
    },
  },
  plugins: [],
}
