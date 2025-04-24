const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require("path");

/**
 * @see https://stackoverflow.com/a/38132106/11409930
 * @see https://stackoverflow.com/a/45278943/11409930
 */

var indexConfig = Object.assign({}, defaultConfig, {
  name: "index",
  entry: {
    "scripts/main/index": "./src/scripts/main/index.js",
  },
  output: {
    path: path.resolve(__dirname, "build"),
    filename: "[name].js",
  },
});

module.exports = [defaultConfig, indexConfig];