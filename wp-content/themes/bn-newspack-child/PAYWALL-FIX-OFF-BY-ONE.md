# Paywall Free Views Off-by-One Bug Fix

## Issue Description

**Bug**: Users were getting N-1 free views instead of N free views due to an off-by-one error.

**Example**: With `free_views = 3` (default), users could only view 2 articles before being gated, not 3 as intended.

## Root Cause

The view tracking happens BEFORE the access check:

```php
// In gate.php (line 68-76)
bn_track_paywall_view();  // Increment counter FIRST
if ( bn_is_subscriber() ) {  // THEN check access
    return $content;
}
```

The access check used a strict less-than comparison:

```php
// In membership.php (original line 213)
return $views_count < $free_views;  // Wrong: allows only N-1 views
```

## Flow Diagram (BEFORE Fix)

With `free_views = 3`:

| Visit # | Before Increment | After Increment | Check (count < 3) | Result                             |
| ------- | ---------------- | --------------- | ----------------- | ---------------------------------- |
| 1st     | 0                | 1               | 1 < 3 = TRUE      | ✓ Access granted                   |
| 2nd     | 1                | 2               | 2 < 3 = TRUE      | ✓ Access granted                   |
| 3rd     | 2                | 3               | 3 < 3 = **FALSE** | ✗ **BLOCKED** (should be allowed!) |

**Result**: User only got 2 free views, not 3!

## Flow Diagram (AFTER Fix)

With `free_views = 3`:

| Visit # | Before Increment | After Increment | Check (count <= 3) | Result               |
| ------- | ---------------- | --------------- | ------------------ | -------------------- |
| 1st     | 0                | 1               | 1 <= 3 = TRUE      | ✓ Access granted     |
| 2nd     | 1                | 2               | 2 <= 3 = TRUE      | ✓ Access granted     |
| 3rd     | 2                | 3               | 3 <= 3 = TRUE      | ✓ Access granted     |
| 4th     | 3                | 4               | 4 <= 3 = FALSE     | ✗ BLOCKED (correct!) |

**Result**: User gets exactly 3 free views as intended!

## The Fix

Changed the comparison operator from `<` to `<=` in `bn_has_free_views_remaining()`:

```php
// File: inc/paywall/membership.php (line 215)
// OLD: return $views_count < $free_views;
// NEW:
return $views_count <= $free_views;
```

## Alternative Solutions Considered

### Option 1: Track AFTER access check ❌

```php
if ( bn_is_subscriber() ) {
    return $content;
}
bn_track_paywall_view();  // Track only if blocked
```

**Rejected because**:

- This would only track views where the user was actually blocked
- Semantically incorrect: we want to count all attempts to access gated content
- More complex logic required

### Option 2: Change comparison to <= ✅ **CHOSEN**

```php
return $views_count <= $free_views;
```

**Advantages**:

- Simple one-character fix
- Maintains "track before check" semantics
- Clear intent with inline comment
- No behavioral changes elsewhere

## Testing

### Manual Test

1. Clear cookies: `document.cookie = "bn_paywall_views=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"`
2. Visit 3 gated articles in sequence
3. Verify all 3 are fully accessible
4. Visit 4th gated article
5. Verify paywall CTA is shown

### Code Test

```php
// Test with free_views = 3
$_COOKIE['bn_paywall_views'] = 1;
assert( bn_has_free_views_remaining() === true );  // 1 <= 3

$_COOKIE['bn_paywall_views'] = 2;
assert( bn_has_free_views_remaining() === true );  // 2 <= 3

$_COOKIE['bn_paywall_views'] = 3;
assert( bn_has_free_views_remaining() === true );  // 3 <= 3 ✓ FIX

$_COOKIE['bn_paywall_views'] = 4;
assert( bn_has_free_views_remaining() === false ); // 4 <= 3
```

## Files Changed

1. **`inc/paywall/membership.php`** (line 215)

   - Changed `<` to `<=`
   - Added explanatory comment

2. **`inc/paywall/gate.php`** (lines 68-72)
   - Enhanced comment explaining the "track before check" pattern
   - Added example showing the <= logic

## Impact

- ✅ Users now get the correct number of free views (N instead of N-1)
- ✅ No performance impact
- ✅ No breaking changes to API or filters
- ✅ Backward compatible (users with existing cookies will simply get one more view)

## Deployment Notes

- No database changes required
- No cookie migration needed
- Users with existing view counts will benefit immediately
- Safe to deploy without cache clearing

## Related Documentation

- See `README.md` section "Membership System" for overall architecture
- See `inc/paywall/membership.php` for full membership logic
- See `inc/paywall/gate.php` for content gating implementation
