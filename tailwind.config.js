const defaultTheme = require("tailwindcss/defaultTheme");
const { colors: defaultColors } = require("tailwindcss/defaultTheme");

module.exports = {
  content: ["./**/*.php", "./src/**/*.js"],
  plugins: [require("@tailwindcss/typography")],
  theme: {
    extend: {},
  },
};
