# AGENTS.md

## Cursor Cloud specific instructions

### Overview

This repository contains a single WordPress child theme (`bn-newspack-child`) for the Bay Nature magazine site, built on top of the Newspack parent theme (`newspack-theme`) by Automattic. The theme includes custom Gutenberg blocks, a paywall system, and a three-menu navigation structure.

### Development Environment

- **Theme directory**: `wp-content/themes/bn-newspack-child/`
- **Dev commands**: See `package.json` in theme dir — `npm start` (watch), `npm run build` (production)
- **Lint**: `npx wp-scripts lint-js src/` (JS), `npx wp-scripts lint-style assets/css/*.css` (CSS)
- **Local WordPress**: Uses `@wordpress/env` (wp-env) with Docker. Config is in `.wp-env.json` in the theme directory.

### Starting Services

1. **Docker must be running** before wp-env can start. In this environment, start dockerd manually: `sudo dockerd &>/tmp/dockerd.log &` then wait a few seconds.
2. **Fix Docker socket permissions** if needed: `sudo chmod 666 /var/run/docker.sock`
3. **Start WordPress**: `cd wp-content/themes/bn-newspack-child && npx wp-env start` — serves at http://localhost:8888.
4. **Start asset watcher**: `cd wp-content/themes/bn-newspack-child && npm start` — rebuilds JS blocks on file changes.
5. **wp-admin credentials**: username `admin`, password `password` (wp-env defaults).

### Gotchas

- The theme declares `Template: newspack-theme` in `style.css` (not `newspack-sacha`). WordPress doesn't support grandchild themes, so the Sacha styles are inlined — see `SACHA-INTEGRATION.md`.
- The `.wp-env.json` downloads the `newspack-theme` parent from GitHub releases (`v1.89.0`). First `wp-env start` pulls Docker images and the theme zip, which takes ~90 seconds.
- After `wp-env start`, activate the child theme if not already active: `npx wp-env run cli wp theme activate bn-newspack-child`.
- Set pretty permalinks: `npx wp-env run cli wp rewrite structure '/%postname%/'`.
- Pre-existing lint errors in CSS and JS are formatting-related (prettier/stylelint) and are not caused by setup.
- The admin sidebar shows custom post types registered by the theme: "Articles", "Biodiversity Series", plus custom taxonomies "Picks" and "Features".
