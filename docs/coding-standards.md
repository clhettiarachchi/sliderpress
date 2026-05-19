# SliderPress — Coding Standards

Bundler: @wordpress/scripts. Standard: WordPress Coding Standards (WPCS).

---

## PHP

### General rules

- Tabs for indentation, always
- Full `<?php` opening tags -- never short tags
- `declare( strict_types=1 )` at the top of every class file
- `if ( ! defined( 'ABSPATH' ) ) { exit; }` in every directly-accessible file
- Opening brace on a new line for class and function declarations
- Opening brace on the same line for control structures
- One blank line between logical blocks inside functions
- Hook everything -- no standalone logic at file root
- Named callbacks only -- `[ $this, 'method' ]`; no anonymous hook callbacks
- All classes loaded via explicit `require_once` in `sliderpress.php`
- Load order: dependencies before dependants
- Never call `get_option()` directly for plugin settings -- use `Settings::get()`

### Naming

- Classes: PascalCase -- `SlideshowRenderer`
- Methods and functions: snake_case -- `register_hooks()`
- Variables: snake_case -- `$slide_id`
- Constants: SCREAMING_SNAKE_CASE -- `SLIDERPRESS_VERSION`
- File names: `class-` + lowercase hyphenated class name -- `class-slideshow-renderer.php`

### Sanitisation and escaping

Sanitise on input, escape on output. No exceptions.

| Context      | Sanitise                | Escape             |
|--------------|-------------------------|--------------------|
| Plain text   | `sanitize_text_field()` | `esc_html()`       |
| HTML attr    | `sanitize_text_field()` | `esc_attr()`       |
| URL          | `esc_url_raw()`         | `esc_url()`        |
| Integer      | `absint()`              | `(int)` cast       |
| Post content | `wp_kses_post()`        | `wp_kses_post()`   |
| SQL          | `$wpdb->prepare()`      | (handled by prepare) |

Always `wp_unslash()` before sanitising `$_POST` values.

### i18n

All user-facing strings wrapped with text domain `sliderpress`.
Never concatenate translated strings -- always use `sprintf()`.

```php
/* translators: 1: current slide number, 2: total slides */
sprintf( __( 'Slide %1$d of %2$d', 'sliderpress' ), $current, $total )
```

### Comments

File header docblock:
```php
/**
 * SliderPress -- Brief description of this file's purpose.
 *
 * @package SliderPress
 */
```

Section dividers -- never use ASCII separator lines (`// ----`).
Use a short docblock instead:
```php
/**
 * Slide resolution
 *
 * Reads and sanitises the slides array from block attributes.
 */
```

Inline comments: `//` only, one space after `//`, comment the why not the what.

---

## JavaScript

- File header: short docblock with role and `@package SliderPress`
  Never describe current development scope in the header.
- All user-facing strings via `__()` from `@wordpress/i18n`
- Named functions for event listeners -- no anonymous functions that can't be removed
- Section dividers: JSDoc block, never `// ----` banners

```js
/**
 * SliderPress -- Block edit component.
 *
 * @package SliderPress
 */
```

```js
/**
 * Image management
 *
 * Sets or clears the slide image from the Media Library.
 */
```

---

## SCSS

- BEM naming: `.sp-block__element--modifier`
- `@use` / `@forward` only -- never `@import`
- Mobile-first -- base styles for small screens, scale up with mixins
- CSS custom properties (`--sp-*`) for runtime values (colours, transitions)
- SCSS variables (`$spacing-*`) for compile-time constants
- State classes (`.is-active`, `.is-loading`) are flat rules, never nested inside BEM blocks
- No `!important` except in `.u-*` utility classes
- No inline styles in PHP templates -- use BEM modifier classes

### Dark mode

Override CSS custom properties only -- never duplicate component rules:
```scss
@media (prefers-color-scheme: dark) {
  :root {
    --sp-color-accent: #2ea2cc;
  }
}
```

### File headers

```scss
// block/slideshow/src/style.scss
// Slideshow block -- styles loaded in both the editor and the front end.
```

Inline comments: `//` only, never `/* */` inside partials.