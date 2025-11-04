# Legacy Content Migration Summary

**Date:** November 4, 2025  
**Status:** ✅ Complete  
**Theme:** bn-newspack-child

## Overview

All legacy content from the `crate` theme has been successfully migrated to the `bn-newspack-child` theme. This migration ensures backward compatibility with existing posts, pages, and custom functionality.

---

## What Was Migrated

### 1. ✅ Custom Post Types

**File:** `inc/post-types.php`

- **biodiversity** - "Weird, Ugly, Rare" series posts
  - Custom fields: name, habitat, population, protection_status, subheading
  - Templates: `single-biodiversity.php`, `archive-biodiversity.php`
  - Assets: `bn-biodiversity/` folder (Bootstrap-based standalone pages)

- **article** - Legacy article post type (separate from standard posts)
  - Supports: categories, tags, picks taxonomy
  - Used in: related posts, staff picks, magazine issues

### 2. ✅ Custom Taxonomies

**File:** `inc/post-types.php`

- **picks** - Staff picks taxonomy
- **features** - Features taxonomy
- Both support: post, article post types

### 3. ✅ Shortcodes (20+ Preserved)

**File:** `inc/shortcodes.php`

#### Essential Shortcodes
- `[social-links]` - Social media icons
- `[button href="" target=""]` - Styled buttons
- `[ad id="" sizes=""]` - DoubleClick/DFP ads
- `[social_media_share_buttons]` - Share buttons

#### Newsletter Shortcodes
- `[subscribe]` - Header newsletter signup
- `[subscribe_sidebar]` - Sidebar newsletter signup
- `[subscribe_article]` - Article inline newsletter CTA
- `[subscribe_footer]` - Footer newsletter signup

#### Article Layout Shortcodes (Mutsun Series)
- `[article_text_block color="" background_color=""]`
- `[article_media_block color="" background_color="" align=""]`
- `[article_side_block color="" background_color="" float=""]`
- `[end_block]`

#### Sidebar Wrapper Shortcodes
- `[open_sidebar color="" background_color="" float=""]`
- `[close_sidebar]`

#### Audio/Interactive Shortcodes
- `[song_sparrow]` - Song Sparrow audio player
- `[compare_song_sparrow]` - Song comparison widget
- `[compare_stack_song_sparrow]` - Stacked comparison
- `[mutsun_event]` - Mutsun event widget
- `[mutsun_tura_story]` - Interactive Mutsun story

### 4. ✅ Page Templates (11 Templates)

**Location:** Child theme root

#### High Priority Templates
- `current_issue_template.php` - Magazine Issue Page
- `magazine_archive_page.php` - Magazine Archive
- `digital_edition_page.php` - Digital Edition Paywall
- `trail-finder-template.php` - Interactive Trail Finder
- `single-biodiversity.php` - Biodiversity series single
- `archive-biodiversity.php` - Biodiversity series archive

#### Additional Templates
- `donation_page.php` - Custom donation page
- `bay_nature_talks_paywall_template.php` - Event paywall
- `bay_nature_talks_parent_page.php` - Events parent
- `member_only_content_no_banner_template.php` - Member content variant
- `page-full-width.php` - Full-width page layout
- `page-mount-diablo.php` - Mount Diablo interactive page

### 5. ✅ Magazine Functions

**File:** `inc/magazine.php`

- `currentIssueRenderPosts($key)` - Render posts for specific issue
- `render_current_issue_content($querystr, $show_title, $key)` - Display issue content
- `render_issue_archive_issues()` - Display all magazine issues
- `is_in_magazine($post_id)` - Check if post is in magazine
- `current_week()` - Get current week dates
- `current_week_date_format()` - Format week date range

### 6. ✅ Digital Edition Functions

**File:** `inc/digital-edition.php`

- `isVistorActiveDigitalSubscriber()` - Check subscriber status (0/1/2)
  - 0 = Not logged in
  - 1 = Logged in but no subscription
  - 2 = Active digital subscriber
- `sfsAPI_isDES($email)` - Call SFS API to verify subscription
- `isWhiteListEmail($email)` - Check whitelist
- `isBayNatureEmail($email)` - Check Bay Nature staff
- `bn_get_utility_urls()` - Get join/login URLs

### 7. ✅ Filters & Hooks

**File:** `inc/filters.php`

- ACF relationship query filters (related posts, staff picks)
- FacetWP cache lifetime (1 day)
- Seasonal color theming via ACF options
- Co-authors Plus RSS feed integration
- Security: XML-RPC disabled, X-Pingback removed
- Gravity Forms editor access
- Tribe Events file upload limit

### 8. ✅ Special Pages & Assets

#### Biodiversity Series Assets
**Location:** `bn-biodiversity/`

- Standalone HTML/CSS/JS application
- Bootstrap 3.3.5, Font Awesome 4.4.0
- Leaflet maps, Howler.js audio, WaveSurfer
- Custom fonts: Jane Austen, Darwin
- Media: illustrations, audio files

#### Mount Diablo Interactive
**Location:** `pages/diablo/`

- HTML5 interactive project
- GSAP animations, ScrollMagic
- Custom fonts: Franklin Gothic, URW Gothic
- Images, interactive map highlights

---

## File Structure

```
wp-content/themes/bn-newspack-child/
├── inc/
│   ├── post-types.php         ✅ Custom post types & taxonomies
│   ├── shortcodes.php          ✅ All legacy shortcodes
│   ├── magazine.php            ✅ Magazine issue functions
│   ├── digital-edition.php     ✅ SFS API integration
│   ├── filters.php             ✅ ACF/FacetWP filters
│   └── setup.php               ✅ Updated to load all legacy files
├── bn-biodiversity/            ✅ Biodiversity series assets
├── pages/
│   └── diablo/                 ✅ Mount Diablo interactive
├── single-biodiversity.php     ✅ Biodiversity single template
├── archive-biodiversity.php    ✅ Biodiversity archive template
├── current_issue_template.php  ✅ Magazine issue page
├── magazine_archive_page.php   ✅ Magazine archive
├── digital_edition_page.php    ✅ Digital edition paywall
├── trail-finder-template.php   ✅ Trail finder
├── donation_page.php           ✅ Donation page
├── page-mount-diablo.php       ✅ Mount Diablo page
└── [additional templates]      ✅ Bay Nature talks, full-width, etc.
```

---

## Usage Guide

### Using Biodiversity Posts

1. **Create new biodiversity post:**
   - Go to "Biodiversity Series" in admin
   - Add custom fields: name, habitat, population, protection_status, subheading
   - Upload illustration image to `bn-biodiversity/media/illos/[post-slug].jpg`

2. **View biodiversity posts:**
   - Single: Uses `single-biodiversity.php` (Bootstrap standalone HTML)
   - Archive: Uses `archive-biodiversity.php` (grid layout)

### Using Magazine Templates

1. **Create issue page:**
   - Create new page
   - Select "Magazine Issue Page" template
   - Add ACF field `current_issue_key` (e.g., "v23n2")
   - Function `currentIssueRenderPosts()` will display all articles with that issue_key

2. **Magazine archive:**
   - Create page with "Magazine Archive" template
   - Set parent page ID in `render_issue_archive_issues()` (currently 222465)

### Using Digital Edition Paywall

1. **Configure SFS API:**
   - Define `SFS_API_URL` constant in `wp-config.php` or via admin
   - Whitelist emails can be added to `isWhiteListEmail()` function

2. **Create digital edition page:**
   - Use "Digital Edition Page" template
   - Returns 0/1/2 based on subscription status
   - Shows appropriate message/content

### Using Shortcodes

**Newsletter signup (most common):**
```
[subscribe_article]
```

**Custom button:**
```
[button href="/join" target="_blank"]Join Bay Nature[/button]
```

**Article layout blocks:**
```
[article_text_block color="#000" background_color="#fff"]
Content here
[end_block]
```

**Song Sparrow audio:**
```
[song_sparrow]
```

### Using Article Post Type

Articles are separate from standard posts and can be:
- Assigned to magazine issues via `issue_key` meta field
- Included in related posts alongside regular posts
- Featured as staff picks

---

## Important Notes

### Path Changes

All template files have been updated to use `get_stylesheet_directory_uri()` instead of `get_template_directory_uri()` to point to the child theme.

### Dependencies

These legacy features may require:

1. **Advanced Custom Fields (ACF) Pro**
   - Issue key fields
   - Biodiversity custom fields
   - Site options (seasonal colors)

2. **Co-Authors Plus** (optional)
   - Multi-author support in magazine issues

3. **FacetWP** (optional)
   - Filtered searches

4. **WooCommerce** (for digital subscriptions)
   - SFS API integration uses WooCommerce orders

5. **Gravity Forms** (optional)
   - Editor access granted via filters

### Biodiversity Isolation

The biodiversity templates load their own Bootstrap 3.3.5 and jQuery. These are isolated to biodiversity pages only and won't conflict with the main theme's styles.

### SFS API Configuration

The digital edition functions require SFS API configuration:
- Set `SFS_API_URL` constant
- Update whitelist emails in `inc/digital-edition.php` if needed
- Test subscription verification thoroughly

### Magazine Parent Page ID

The magazine archive function hardcodes parent page ID `222465`. Update this in `inc/magazine.php` line 233 if your magazine parent page has a different ID.

---

## Testing Checklist

Before going live, verify:

- [ ] Biodiversity posts display correctly with full HTML/JS functionality
- [ ] Article post type shows in archives and singles
- [ ] Magazine issue pages render correct posts by issue_key
- [ ] Digital edition paywall correctly identifies subscribers
- [ ] All shortcodes render (test with existing content)
- [ ] Mount Diablo interactive page functions
- [ ] Newsletter signup forms work
- [ ] Social share buttons appear
- [ ] Related posts include both article + post types
- [ ] Staff picks filter works
- [ ] Seasonal color theming applies (if using ACF options)

---

## Support

For issues with legacy content:

1. Check that required plugins are active (ACF, Co-Authors Plus, etc.)
2. Verify ACF field groups are imported for biodiversity and magazine
3. Check `SFS_API_URL` constant for digital edition
4. Review magazine parent page ID in `inc/magazine.php`
5. Test biodiversity media files exist in `bn-biodiversity/media/illos/`

---

## Future Considerations

While all legacy content is preserved, consider:

1. **Biodiversity Modernization**: Eventually rebuild as Gutenberg blocks instead of Bootstrap HTML
2. **SFS API Updates**: Test and update API endpoints/authentication if needed
3. **Shortcode Alternatives**: Gradually replace with Gutenberg blocks where appropriate
4. **Magazine System**: Consider custom post type instead of issue_key meta field
5. **Digital Edition**: Integrate with new paywall system when ready

---

**Migration completed successfully!** All legacy content should now work seamlessly in the bn-newspack-child theme.

