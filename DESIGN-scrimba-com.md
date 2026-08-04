# Design System Inspired by Learn anything

> Auto-extracted from `https://scrimba.com/explain` on 2026-08-04

> **APPLIQUÉ (dark neutre) — Paie Me** : thème original LIGHT ci-dessous.
> L'app l'utilise en **dark neutre + violet** (changement total de palette) :
> fond graphite `#0b0d12`/`#14171f` (pas de navy/cyan/bleu), accent violet
> `#8b5cf6` (boutons primaires, liens, stat-values), icônes d'actions
> view=violet / edit=ambre / delete=rouge / info=magenta `#d946ef`,
> headings 800, radius 6px (boutons) / 12px (cards). Voir la skill
> `scrimba-design` et `assets/css/style.css`.

## 1. Visual Theme & Atmosphere

Friendly, approachable design with rounded shapes and generous whitespace.

The hero section leads with "Learn anything" followed by "Type a question and get a video explainer back. Share it with the community to help others learn.".

**Key Characteristics:**
- system-ui as the heading font
- system-ui as the body font for all running text
- Heading weight 800
- Light/white background (#ffffff) as the primary canvas
- Primary accent `#3abff8` used for CTAs and brand highlights
- 5 shadow level(s) detected — tinted shadows
- Rounded corners (3px+) creating a friendly, approachable feel
- Tags: light, rounded, colorful, sans-serif

## 2. Color Palette & Roles

### Primary
- **Primary Accent** (`#3abff8`) · `--color-primary`: Brand color, CTA backgrounds, link text, interactive highlights.
- **Secondary Accent** (`#ef4444`) · `--color-secondary`: Secondary brand, hover states, complementary highlights.
- **Background** (`#ffffff`) · `--color-bg`: Page background, primary canvas.

### Text
- **Text Primary** (`#000000`) · `--color-text`: Headings and body text.
- **Text Secondary** (`#afbccd`) · `--color-text-secondary`: Muted text, captions, placeholders.

### Borders & Surfaces
- **Border** (`#0b1118`) · `--color-border`: Dividers, outlines, input borders.

### Full Extracted Palette

| # | Hex | CSS Variable | Role | Area | Contrast |
|---|---|---|---|---|---|
| 1 | `#0b1118` | `--palette-1` | block | large | text-light |
| 2 | `#ffffff` | `--palette-2` | button | medium | text-dark |
| 3 | `#000000` | `--palette-3` | button | small | text-light |
| 4 | `#ef4444` | `--palette-4` | text-accent | small | text-light |
| 5 | `#3abff8` | `--palette-5` | text-accent | small | text-dark |
| 6 | `#61a6fa` | `--palette-6` | text-accent | small | text-dark |
| 7 | `#afbccd` | `--palette-7` | text-accent | small | text-dark |
| 8 | `#3b82f6` | `--palette-8` | badge | small | text-light |
| 9 | `#7a8ba3` | `--palette-9` | text-accent | small | text-dark |
| 10 | `#0369a1` | `--palette-10` | text-accent | small | text-light |

## 3. Typography Rules

- **Heading Font:** `system-ui`, sans-serif
- **Body Font:** `system-ui`, sans-serif

### Type Hierarchy

| Role | Font | Size | Weight | Line Height | Letter Spacing |
|---|---|---|---|---|---|
| H1 | system-ui | 48px | 800 | 50px | normal |
| H2 | system-ui | 13px | 500 | 30px | normal |
| Body | system-ui | 20px | 400 | 27px | normal |

### Type Scale

| Token | Size | Suggested Usage |
|---|---|---|
| Display | `48px` | headings |
| H1 | `24px` | headings |
| H2 | `20px` | headings |
| H3 | `18px` | headings |
| H4 | `17px` | headings |
| Body L | `16px` | body / supporting text |
| Body | `15px` | body / supporting text |
| Small | `14px` | body / supporting text |
| XS | `13px` | body / supporting text |
| Caption | `12px` | body / supporting text |

## 4. Component Stylings

### Primary Button

```css
.btn-primary {
  background: #ffffff;
  color: ;
  border-radius: 5px;
  padding: 0px 0px;
  font-size: 14px;
  font-weight: 500;
  border: none;
  cursor: pointer;
}
```

### Ghost Button

```css
.btn-ghost {
  background: transparent;
  color: #75869f;
  border-radius: 0px;
  padding: 6px 20px;
  font-size: 12px;
  font-weight: 400;
  border: none;
  cursor: pointer;
}
```

### Filled Button

```css
.btn-filled {
  background: #ffffff;
  color: #ffffff;
  border-radius: 8px;
  padding: 0px 0px;
  font-size: 14px;
  font-weight: 400;
  border: 0.8px solid rgba(255, 255, 255, 0.14);
  cursor: pointer;
}
```

### Filled Button 2

```css
.btn-filled-2 {
  background: #3b82f6;
  color: #ffffff;
  border-radius: 8px;
  padding: 0px 0px;
  font-size: 14px;
  font-weight: 400;
  border: none;
  cursor: pointer;
}
```

### Card

```css
.card {
  background: #000000;
  border-radius: 6px;
  padding: 0px;
}
```

## 5. Layout Principles

- **Base spacing unit:** `24px` — use multiples (48px, 72px, 96px, etc.)

### Spacing Scale (extracted from real elements)

| Token | Value | Role |
|---|---|---|
| spacing-1 | `24px` | card |
| spacing-2 | `3px` | element |
| spacing-3 | `6px` | element |
| spacing-4 | `7px` | element |
| spacing-5 | `18px` | element |
| spacing-6 | `36px` | card |
| spacing-7 | `8px` | element |
| spacing-8 | `12px` | element |

### Border Radius Scale

| Token | Value | Element |
|---|---|---|
| radius-subtle | `3px` | subtle |
| radius-button | `6px` | button |
| radius-card | `50px` | card |
| radius-subtle | `4px` | subtle |
| radius-card | `16px` | card |
| radius-button | `8px` | button |

## 6. Depth & Elevation

| Level | Shadow | Usage |
|---|---|---|
| Low | `rgba(0, 0, 0, 0.3) 0px 0px 2px 0px` | Cards, subtle elevation |
| Low | `lch(8.8 3.2 249) 0px 0px 0px 2px` | Cards, subtle elevation |
| Low | `rgba(0, 0, 0, 0.1) 0px 0px 0px 1px` | Cards, subtle elevation |
| Low | `lch(86.7 0 0 / 0.08) 1px 0px 0px 0px` | Cards, subtle elevation |
| High | `lch(0 0 0 / 0.25) 0px 6px 24px 0px, lch(22 5 270) 0px 0px 0px 1px` | Modals, floating elements |


## 7. Do's and Don'ts

### Do
- Use `#ffffff` as the primary background color
- Use `system-ui` for all headings and `system-ui` for body text
- Use `#3abff8` as the single dominant accent/CTA color
- Maintain `24px` as the base spacing unit — all gaps should be multiples
- Use rounded corners (`3px`+) consistently for all interactive elements
- Embrace bold color combinations — playful energy is the point
- Apply the shadow system for elevation — use the extracted shadow values
- Use weight 800 for headings to match the brand's typographic voice

### Don't
- Don't use colors outside the extracted palette without justification
- Don't substitute system-ui/system-ui with generic alternatives
- Don't use irregular spacing — stick to 24px grid
- Don't use dark/black backgrounds — this is a light-themed design
- Don't use sharp corners — they feel hostile in this rounded design language
- Don't use pure black (#000000) for text — use `#000000` instead
- Don't add decorative elements not present in the original design — no badges, ribbons, banners, or ornaments unless the source site uses them
- Don't invent UI patterns the source site doesn't have — if the original has no NEW badge, don't add one just because a red is in the palette

## 8. Responsive Behavior

| Breakpoint | Width | Notes |
|---|---|---|
| Mobile | < 640px | Single column, stack sections, reduce font sizes ~80% |
| Tablet | 640–1024px | 2-column where appropriate, maintain spacing ratios |
| Desktop | 1024–1440px | Full layout as designed |
| Wide | > 1440px | Max-width container, center content |

- Touch targets: minimum 44×44px on mobile
- Maintain 24px base unit across breakpoints — only scale multipliers

## 9. Agent Prompt Guide

### Quick Color Reference

```
Background:  #ffffff
Text:        #000000
Accent:      #3abff8
Secondary:   #ef4444
Border:      #0b1118
```

### Example Prompts

1. "Build a hero section with a `#ffffff` background, `system-ui` heading in `#000000`, and a `#3abff8` CTA button with 5px radius."
2. "Create a pricing card using background `#ffffff`, border `#0b1118`, `system-ui` for text, and 72px padding."
3. "Design a navigation bar — `#ffffff` background, `#000000` links, `#3abff8` for active state."
4. "Build a feature grid with 3 columns, 72px gap, each card using the card component style."
5. "Create a footer with `#000000` background, `#ffffff` text, and 48px padding."

### Iteration Guide

1. Start with layout structure (sections, grid, spacing)
2. Apply colors from the palette — background first, then text, then accents
3. Set typography — font families, sizes from the type scale, weights
4. Add components — buttons, cards, inputs using the specs above
5. Apply border-radius consistently across all elements
6. Add shadows for depth — use the extracted shadow values, not defaults
7. Check responsive behavior — test mobile and tablet layouts
8. Final pass — verify all colors match, spacing is consistent, fonts are correct

## 10. CSS Custom Properties

> 161 custom properties extracted from `:root` / `html` stylesheets.

### Color Variables

| Variable | Value |
|---|---|
| `--markour-selection-color` | `rgba(49, 139, 255, .25)` |
| `--markour-code-bg` | `#21252b` |
| `--markour-code-focus-bg` | `rgb(37, 42, 50)` |
| `--markour-code-caret` | `#ffe83d` |
| `--markour-code-header-bg` | `#313942` |
| `--markour-code-selection-color` | `rgba(0, 184, 255, .32)` |
| `--md-hr-color` | `hsla(215.29,25%,26.67%,30%)` |
| `--slider-track` | `hsla(0,0%,100%,20%)` |
| `--slider-fill` | `hsla(0,0%,100%,50%)` |
| `--slider-thumb` | `hsla(0,0%,100%,95%)` |

### Spacing Variables

| Variable | Value |
|---|---|
| `--u_gih` | `220px` |
| `--u_rd` | `6px` |
| `--u_keyboardheight` | `0px` |
| `--u_mipx` | `8px` |
| `--u_mih` | `32px` |
| `--u_mifs` | `13px` |
| `--u_mird` | `4px` |
| `--u_accentL` | `55` |
| `--u_accentC` | `66` |
| `--u_accentH` | `270` |
| `--u_accentA` | `1` |
| `--u_blueL` | `54.6` |
| `--u_blueC` | `66.4` |
| `--u_blueH` | `277.6` |
| `--u_blueA` | `1` |
| `--u_greenL` | `72.4` |
| `--u_greenC` | `56.2` |
| `--u_greenH` | `161.4` |
| `--u_greenA` | `1` |
| `--u_yellowL` | `81.8` |
| ... | *(74 more)* |

### Typography Variables

| Variable | Value |
|---|---|
| `--u_mi-disabled-textL` | `var(--u_op-fg-subtleL)` |
| `--u_mi-disabled-textC` | `var(--u_op-fg-subtleC)` |
| `--u_mi-disabled-textH` | `var(--u_op-fg-subtleH)` |
| `--u_mi-disabled-textA` | `var(--u_op-fg-subtleA,1)` |
| `--font-code` | `"Source Code Pro"` |
| `--font-monosans` | `"Helvetica Neue", Verdana` |

### Other Variables

| Variable | Value |
|---|---|
| `--u_fxd` | `.3s` |
| `--sait` | `env(safe-area-inset-top, 0px)` |
| `--sail` | `env(safe-area-inset-right, 0px)` |
| `--u_menu-bgL` | `var(--u_op-surface-raisedL)` |
| `--u_menu-bgC` | `var(--u_op-surface-raisedC)` |
| `--u_menu-bgH` | `var(--u_op-surface-raisedH)` |
| `--u_menu-bgA` | `var(--u_op-surface-raisedA,1)` |
| `--u_site-bgL` | `var(--u_op-bgL)` |
| `--u_site-bgC` | `var(--u_op-bgC)` |
| `--u_site-bgH` | `var(--u_op-bgH)` |
| `--u_site-bgA` | `var(--u_op-bgA,1)` |
| `--u_hueL` | `var(--u_blueL)` |
| `--u_hueC` | `var(--u_blueC)` |
| `--u_hueH` | `var(--u_blueH)` |
| `--u_hueA` | `var(--u_blueA,1)` |
| ... | *(36 more)* |
