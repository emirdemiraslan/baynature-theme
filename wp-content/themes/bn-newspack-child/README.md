# Bay Nature (Newspack Child Theme)

A mobile-first WordPress child theme for Bay Nature, built on `newspack-sacha` with custom Gutenberg blocks, responsive navigation, and a flexible paywall system.

## Features

- **Mobile-first design** (≥70% mobile visitors): fluid typography, stack-first layouts, min-width media queries
- **Three-menu navigation system**:
  - Header Utility: Join/Donate buttons + hamburger toggle
  - Popup Overlay Menu: full-screen accessible overlay (primary mobile nav)
  - Topics Row: horizontal scrollable category chips
- **Custom Gutenberg blocks**:
  - Hero Header: full-width hero with manual post selection, content positioning (left/center/right on desktop, forced center on mobile), 60vh mobile / 80vh desktop
  - Paywall CTA: membership call-to-action component
  - Stubs: Featured Issue, Latest News Rail, Newsletter Signup, Events Teaser, Featured Trail, Author Box
- **Flexible paywall** (admin-managed):
  - Modes: Template-only, Automatic (latest N printed issues for Article CPT using ACF Issue field), Hybrid
  - Settings: latest N (default 3), preview paragraphs, free views, manual issue override, exceptions
  - Compatibility with existing "Member Only Content Default Template"
- **Block templates**: Home, Single, Archive, Member-Only
- **Accessibility**: WCAG AA targets, keyboard navigation, focus trap in overlay, reduced-motion support
- **Performance**: mobile CWV targets (LCP ≤ 2.5s, CLS ≤ 0.1, INP ≤ 200ms), lazy images, deferred JS

## Requirements

- WordPress 6.6+
- PHP 7.4+
- Parent theme: `newspack-sacha`
- Recommended: Advanced Custom Fields (ACF) for Issue field on Article CPT

## Installation

1. Upload `bn-newspack-child` to `/wp-content/themes/`
2. Ensure `newspack-sacha` parent theme is installed and active
3. Activate the Bay Nature child theme
4. Flush permalinks: Settings → Permalinks → Save Changes

## Development Setup

### Build assets

```bash
cd wp-content/themes/bn-newspack-child
npm install
npm start  # Watch mode
npm run build  # Production build
```

### File structure

```
bn-newspack-child/
├── style.css
├── functions.php
├── theme.json
├── package.json
├── inc/
│   ├── setup.php
│   ├── blocks.php
│   ├── navigation/
│   │   ├── menus.php
│   │   └── index.php
│   └── paywall/
│       ├── settings.php
│       ├── membership.php
│       ├── gate.php
│       └── index.php
├── blocks/
│   ├── hero-header/
│   ├── paywall-cta/
│   ├── featured-issue/
│   ├── latest-news-rail/
│   ├── newsletter-signup/
│   ├── events-teaser/
│   ├── featured-trail/
│   └── author-box/
├── assets/
│   ├── css/
│   │   └── navigation.css
│   └── js/
│       └── overlay.js
├── parts/
│   ├── header.html
│   └── header-overlay.html
└── templates/
    ├── home.html
    ├── single.html
    ├── archive.html
    └── member-only-content-default-template.html
```

## Configuration

### Navigation

**Appearance → Menus**
- Create three menus and assign to locations:
  - **Header Utility**: typically empty (Join/Donate URLs set in settings)
  - **Popup**: main navigation for overlay (supports 1 sublevel)
  - **Topics**: horizontal category chips (1 level only)

**Appearance → Bay Nature → Navigation**
- Set Join URL (default `/join`)
- Set Donate URL (default `/donate`)

### Paywall

**Appearance → Bay Nature → Paywall**
- **Mode**: Template-only | Latest N printed issues | Hybrid
- **Latest N printed issues**: number of recent printed issues to paywall (default 3)
- **Preview paragraphs**: number of paragraphs visible to non-members (default 3)
- **Anonymous free views**: cookie-based free views before paywall (default 3)

The paywall:
- Detects "printed issues" on the `Article` CPT using the ACF `issue` field (non-empty values)
- Caches the latest N issues via transient (auto-refreshed on Article publish/update)
- Respects the existing "Member Only Content Default Template" assignment

### Blocks

**Hero Header** (bn/hero-header)
- Select a post manually (fallback to latest if empty)
- Choose content position: left, center (default), right (desktop only; mobile forced center)
- Configure heights: 60vh mobile (default), 80vh desktop (default)
- Toggle excerpt and author display
- Customize overlay gradient for contrast

**Paywall CTA** (bn/paywall-cta)
- Customize heading, message, button text, and button URL
- Use in templates or inject programmatically

## Design Tokens

Managed in `theme.json`:

- **Colors**: base, contrast, primary (orange), secondary (green), cyan (Join), yellow (Donate), gray scale
- **Typography**: fluid type scale (xs–5xl), body (sans-serif), heading (serif)
- **Spacing**: xs–2xl (0.5rem–4rem)
- **Layout**: contentSize 680px, wideSize 1280px

## Deployment

1. Backup database and files
2. Zip the theme: `zip -r bn-newspack-child.zip bn-newspack-child`
3. Upload via Appearance → Themes → Add New → Upload
4. Activate child theme
5. Flush permalinks
6. Verify:
   - Navigation (all three menus render)
   - Paywall (test with an Article in a recent printed Issue)
   - Templates (Home, Archive, Single)
   - Mobile: overlay menu, topics scroll, CWV

## Membership System

The paywall uses a flexible, extensible membership system:

### How It Works

1. **Filter Override** (highest priority): allows plugins to integrate via `bn_is_subscriber_override` filter
2. **User Role Check**: logged-in users with these roles get access:
   - `subscriber`, `member`, `administrator`, `editor`, `author`
   - Customize via `bn_subscriber_roles` filter
3. **Cookie-Based Free Views**: anonymous users get N free views (default 3, configurable in settings)
   - Cookie: `bn_paywall_views` (30-day expiration)
   - View counter increments on gated content access
4. **Bot Detection**: search engine bots (Google, Bing, etc.) always get full content for SEO

### Integration with Membership Plugins

To integrate with **WooCommerce Subscriptions**, **Paid Memberships Pro**, or similar:

```php
// In your theme's functions.php or custom plugin
add_filter( 'bn_is_subscriber_override', function( $default ) {
    // WooCommerce Subscriptions example
    if ( function_exists( 'wcs_user_has_subscription' ) ) {
        return wcs_user_has_subscription( get_current_user_id(), '', 'active' );
    }
    
    // Paid Memberships Pro example
    if ( function_exists( 'pmpro_hasMembershipLevel' ) ) {
        return pmpro_hasMembershipLevel();
    }
    
    return $default; // Fall back to built-in logic
} );
```

### Custom Member Role

The theme registers a `member` role on activation, separate from WordPress's default `subscriber`. Assign this role to paying members.

### Anonymous User Tracking

- View tracking happens on first content access (before access check)
- Cookie persists for 30 days
- Respects `COOKIEPATH` and `COOKIE_DOMAIN`
- Secure flag enabled on HTTPS sites

## Support & Extension

- To customize membership logic: use the `bn_is_subscriber_override` or `bn_subscriber_roles` filters
- To add GA4 events: hook `wp_footer` or enqueue a tracking script that listens for `.bn-paywall-cta-button`, `.bn-hamburger`, `.bn-topics-menu a`, etc.
- To extend blocks: copy a block folder, update `block.json` name, register in `inc/blocks.php`
- To modify bot detection: filter `bn_is_bot` return value or add patterns to `inc/paywall/membership.php`

## License

Inherits parent theme license (GPL v2+).

