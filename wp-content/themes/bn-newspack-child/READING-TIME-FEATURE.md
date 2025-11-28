# Reading Time Feature

## Overview
Added a reading time calculator that displays "Reading Time: X minutes" on single posts and articles.

## Implementation Details

### Location
The reading time appears:
- Below the byline (entry-meta)
- Above the social sharing icons (sharedaddy/Jetpack sharing)
- Within the `.entry-subhead` container

### Calculation Formula
- **Formula**: `(number of words) / 250`
- The calculation:
  - Counts all words in the post content
  - Strips HTML tags and shortcodes
  - Divides by 250 (average reading speed)
  - Rounds up to nearest minute
  - Minimum display is 1 minute

### Files Modified/Created
1. **`functions.php`** - Added two new functions:
   - `bn_get_reading_time()` - Calculates reading time based on word count
   - `bn_reading_time_styles()` - Adds inline CSS styling

2. **`template-parts/header/entry-header.php`** (NEW - copied from parent theme)
   - Overrides the parent theme's entry-header template
   - Inserts reading time directly in PHP between entry-meta and sharing_display()
   - Lines 103-124: Reading time display code

### Technical Approach
- **Server-side PHP rendering** (proper WordPress approach)
- Template override pattern: Child theme overrides parent theme template
- Reading time is rendered server-side, in HTML from the start
- Benefits:
  - SEO-friendly (content visible to search engines)
  - No JavaScript dependency
  - Works with JavaScript disabled
  - Better accessibility for screen readers
  - Faster initial page load (no DOM manipulation needed)

### Styling
The reading time is styled to match the theme's design:
- Centered text alignment (mobile and desktop)
- Italic font style
- Gray color (#666) for normal layouts
- White color with transparency for "featured image behind" layouts
- Responsive design for different screen sizes

### Post Types Supported
- Standard posts (`post`)
- Articles (`article`)

## Testing
To test the feature:
1. View any single post or article page
2. The reading time should appear between the byline and social sharing icons
3. Verify it displays correctly on different layouts:
   - Default layout
   - Featured image behind
   - Featured image beside
   - Featured image above

## Future Enhancements
Potential improvements:
- Add reading time to post listings/archives
- Make the words-per-minute rate configurable
- Add admin option to show/hide reading time
- Include estimated time for embedded videos

