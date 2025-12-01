const defaultConfig = require('@wordpress/scripts/config/webpack.config');

// Use the default @wordpress/scripts configuration:
// - Entry: src/index.js
// - Output: build/index.js and build/index.asset.php
// Our src/index.js file imports and registers all custom blocks.
module.exports = defaultConfig;

