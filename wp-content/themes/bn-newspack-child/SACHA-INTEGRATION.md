# Newspack Sacha Styling Integration

This document describes the Newspack Sacha styles that have been integrated into the Bay Nature (Newspack Child) theme.

## What Was Changed

### 1. Theme Parent Updated
- **File**: `style.css`
- **Change**: Updated `Template: newspack-sacha` to `Template: newspack-theme`
- **Reason**: WordPress doesn't support grandchild themes (child of a child), so we now inherit directly from `newspack-theme` instead of `newspack-sacha`.

### 2. Fonts Added
- **Directory**: `fonts/ibm-plex-serif/`
- **Files**: IBM Plex Serif font family (Regular, Italic, Bold, Bold Italic)
- **Usage**: These fonts are used for headings throughout the site

### 3. Sacha Styles
- **File**: `assets/css/sacha-styles.css`
- **Description**: Complete CSS implementation of Newspack Sacha's styling features

### 4. PHP Helper Functions
- **Directory**: `inc/sacha/`
- **Files**:
  - `child-color-patterns.php` - Handles custom color customizations
  - `child-typography.php` - Handles typography customizations
  - `index.php` - Security file

### 5. Functions Updated
- **File**: `functions.php`
- **Changes**:
  - Enqueues Sacha styles after parent theme styles
  - Loads Sacha customization helper functions
  - Adds editor style support
  - Hooks for color and typography customization

## Key Style Features from Newspack Sacha

### Typography
- **IBM Plex Serif** font for headings
- Normal font weight for entry titles (not bold)
- Bold captions for images
- Italic blockquotes with serif font
- Styled drop caps with background color

### Layout & Design
- **Centered elements**:
  - Article titles and subtitles
  - Section headers
  - Author bios
  - Archive headers
  
- **Decorative section headers**: Headers with lines extending on both sides (e.g., "— Latest News —")

### Content Elements
- **Pullquotes**: Centered with larger font sizes on tablet+
- **Tags**: Styled with background colors instead of plain text
- **Category links**: Centered with custom styling

### Header & Footer
- **Header**: Support for solid background header with contrast text
- **Footer**: Light gray background (#f1f1f1) with custom styling
- **Site info**: Dark background in footer bottom

### Editor Support
- All frontend styles are also available in the WordPress block editor
- Custom color and typography options work in both frontend and editor

## Testing Your Theme

1. **Activate the Theme**:
   - Go to `wp-admin > Appearance > Themes`
   - You should now see "Bay Nature (Newspack Child)" as available (not broken)
   - Activate it

2. **Check Typography**:
   - Headings should use IBM Plex Serif font
   - Entry titles should have normal weight (not bold)

3. **Check Section Headers**:
   - Widget titles and section headers should have decorative lines on both sides

4. **Test Customizer**:
   - Go to `Appearance > Customize`
   - Changes to colors and fonts will work with Sacha's customization system

## Customization Options

The following WordPress Customizer options are supported:

- **Theme Colors**: Custom color schemes
- **Header Color**: Custom header background and text colors
- **Footer Color**: Custom footer colors
- **Font Body**: Body font selection
- **Font Header**: Heading font selection
- **Accent All Caps**: Toggle uppercase for accent headers
- **Header Solid Background**: Enable solid color header background

## File Structure

```
bn-newspack-child/
├── style.css (updated Template reference)
├── functions.php (updated with Sacha integration)
├── fonts/
│   └── ibm-plex-serif/
│       ├── IBMPlexSerif-Regular.ttf
│       ├── IBMPlexSerif-Italic.ttf
│       ├── IBMPlexSerif-Bold.ttf
│       └── IBMPlexSerif-BoldItalic.ttf
├── assets/
│   └── css/
│       └── sacha-styles.css
└── inc/
    └── sacha/
        ├── child-color-patterns.php
        ├── child-typography.php
        └── index.php
```

## Notes

- All Sacha styles are applied in addition to your existing custom styles
- The Sacha styles load after the parent theme but before your theme.json and block-specific styles
- If any Sacha style conflicts with your custom styling, you can override it in your theme.json or block styles
- The customization functions require the parent theme's `newspack_get_color_contrast()` and `newspack_adjust_brightness()` functions

## Troubleshooting

**If fonts don't load:**
- Check that font files are in `/fonts/ibm-plex-serif/` directory
- Check browser console for 404 errors
- Verify file paths in `sacha-styles.css`

**If styles don't apply:**
- Clear WordPress cache
- Clear browser cache
- Check that `sacha-styles.css` is being enqueued (view page source)
- Verify in browser dev tools that styles are loading after parent theme styles

**If customizer options don't work:**
- Check that parent theme functions are available
- Verify PHP helper files are loading (check for PHP errors)

## Support

These styles are adapted from the official Newspack Sacha theme by Automattic.
For Bay Nature specific customizations, refer to the main theme documentation.

