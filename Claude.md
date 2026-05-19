# SliderPress — Claude Code Instructions

## Behaviour

- Make minimal, focused changes. Only touch files directly related to the task.
- Do not refactor, reformat, or reorganise code outside the scope of the request.
- Do not add comments, rename variables, or change code style unless explicitly asked.
- Ask before making any structural or architectural change.

## Coding standards

All coding conventions are in `docs/coding-standards.md`. Read it before
writing or editing any PHP, JS, or SCSS file.

## Project overview

SliderPress is a WordPress Gutenberg plugin that provides an accessible,
responsive slideshow block. It uses a parent/child block architecture:
`sliderpress/slideshow` (dynamic, PHP-rendered) contains one or more
`sliderpress/slide` child blocks (static save).

---

## Key identity tokens

| Token            | Value                    |
|------------------|--------------------------|
| Plugin slug      | `sliderpress`            |
| PHP namespace    | `SliderPress`            |
| Text domain      | `sliderpress`            |
| Meta key prefix  | `_sp_`                   |
| CSS class prefix | `sp-`                    |
| Constant prefix  | `SLIDERPRESS_`           |
| JS global        | `sliderpressAdmin`       |

---

## Block architecture

### sliderpress/slideshow (parent)
- Dynamic block -- `render.php` delegates to `SlideshowRenderer::render()`
- `save()` returns `<InnerBlocks.Content />` wrapped in `useBlockProps.save()`
  so WordPress serialises child slide blocks into post content
- Inspector controls: transition, aspect ratio, arrows, dots, autoplay, interval
- `allowedBlocks`: `[ 'sliderpress/slide' ]`
- Registered from `blocks/build/slideshow/block.json`

### sliderpress/slide (child)
- Static block -- `save.js` serialises image markup to post content
- `parent`: `[ 'sliderpress/slideshow' ]` -- only insertable inside slideshow
- Attributes: `imageId`, `imageUrl`, `altText`
- Reorder via native block drag handles
- Delete via native block toolbar
- Registered from `blocks/build/slide/block.json`

---

## Build

```
npm run build   # production build -> blocks/build/
npm run start   # watch mode
```

`wp-scripts` auto-discovers both `block.json` files from `blocks/src/`.

---

## CPT slugs (use constants, never raw strings)

| Constant        | Value               |
|-----------------|---------------------|
| `CPT::SHOW`     | `sliderpress_show`  |
| `CPT::SLIDE`    | `sliderpress_slide` |

---

## Settings keys (via Settings::get())

`image_size`, `default_transition`, `default_interval`, `default_ratio`,
`show_arrows`, `show_dots`, `footer_scripts`, `shortcode_support`, `uninstall_cleanup`

---

## What is not yet built

- Front-end JS (`assets/src/js/slideshow.js`)
- Front-end SCSS (`assets/src/scss/`)
- Slide title, caption, and CTA fields
- Named slideshow admin management (reference mode)
- `[sliderpress]` shortcode wired to renderer