/*!
 * css-filters-polyfill-parser.js
 *
 * Author: Christian Schepp Schaefer
 * Summary: A polyfill for CSS filter effects
 * License: MIT
 * Version: 0.3.0
 *
 * URL:
 * https://github.com/Schepp/
 *
 */
importScripts("cssParser.js");var parser=new CSSParser;self.addEventListener("message",function(e){var s={content:parser.parse(e.data.content,!1,!0),media:e.data.media};self.postMessage(s)},!1);