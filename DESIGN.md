---
name: Kite School of Kenpo
description: Design system for a martial arts school website. Dark, disciplined aesthetic with bold red accents — communicates strength, tradition, and community safety.

colors:
  # Backgrounds
  background:
    value: "#1e2123"
    description: Primary page background — deep charcoal, nearly black
  background-elevated:
    value: "#25292b"
    description: Slightly lighter surface for alternating sections
  # Brand accent
  accent:
    value: "#e31213"
    description: Primary red — used for CTAs, links on hover, icons, separators
  accent-dark:
    value: "#850808"
    description: Darkened red for button hover states and decorative elements
  # Text
  text-primary:
    value: "#ffffff"
    description: Headlines and high-emphasis text on dark backgrounds
  text-secondary:
    value: "#999999"
    description: Body copy and supporting text on dark surfaces
  text-muted:
    value: "#9ca3af"
    description: Placeholder text and low-emphasis elements (Tailwind gray-400)
  # Brand scale (red-based)
  brand-50:
    value: "#fcdede"
  brand-100:
    value: "#f7b5b5"
  brand-200:
    value: "#f28c8d"
  brand-300:
    value: "#ed6464"
  brand-400:
    value: "#e83b3c"
  brand-500:
    value: "#e31213"
  brand-600:
    value: "#ba0f10"
  brand-700:
    value: "#910c0c"
  brand-800:
    value: "#670809"
    description: Used in `.btn-dark` background
  brand-900:
    value: "#3e0505"
  # Utility
  border-default:
    value: "#e5e7eb"
    description: Default border color (Tailwind gray-200 compat layer)

typography:
  fonts:
    heading:
      family: "Montserrat"
      weights: [400, 700]
      source: "Google Fonts"
      usage: "All h1–h6 elements"
    body:
      family: "Roboto"
      weights: [400, 700]
      source: "Google Fonts"
      usage: "Paragraphs, articles, general prose"
  scale:
    2xs:
      value: "0.7rem"
    xs:
      value: "0.8rem"
    sm:
      value: "0.95rem"
    base:
      value: "1.1rem"
    lg:
      value: "1.25rem"
    xl:
      value: "1.3rem"
    2xl:
      value: "1.6rem"
    3xl:
      value: "1.953rem"
    4xl:
      value: "2.441rem"
    5xl:
      value: "3.052rem"
    6xl:
      value: "3.5rem"
    7xl:
      value: "4.375rem"
    10xl:
      value: "6rem"
  line-height:
    body: 1.6
    relaxed: "1.625"
    heading: "1.1"

spacing:
  section-y: "3.5rem"
  container-x: "1rem"
  container-x-md: "2rem"
  btn-x: "1rem"
  btn-y: "0.5rem"
  btn-x-sm: "2rem"
  btn-y-sm: "0.75rem"

radii:
  input: "0.25rem"
  card: "0"
  dropdown-item: "0.5rem"
  pill: "9999px"

elevation:
  nav: "z-50"
  hero-content: "z-10"
  section-content: "z-10"

motion:
  faq-expand:
    property: "max-height"
    duration: "300ms"
    easing: "ease-in-out"
  button-color:
    property: "background-color, border-color"
    duration: "150ms"
    easing: "ease-in-out"
  dropdown:
    type: "Alpine.js x-transition"
    origin: "top left"
  loader:
    duration: "750ms"
    easing: "linear"
    type: "bouncing dot on track"

breakpoints:
  sm: "640px"
  md: "768px"
  lg: "1024px"
  xl: "1280px"

layout:
  max-width: "1280px"
  hero-min-height-mobile: "400px"
  hero-min-height-sm: "500px"
  hero-min-height-md: "600px"
  hero-min-height-lg: "700px"

components:
  btn:
    background: "#e31213"
    background-hover: "#850808"
    border: "#e31213"
    border-hover: "#850808"
    text: "#ffffff"
    transform: "uppercase"
    padding: "0.5rem 1rem"
    padding-sm: "0.75rem 2rem"
    focus-ring: true
    transition: "150ms ease-in-out"
  btn-dark:
    background: "#670809"
    background-hover: "#850808"
    border: "#670809"
    border-hover: "#850808"
    text: "#ffffff"
    transform: "uppercase"
  separator:
    type: "two red X SVG icons"
    color: "#e31213"
    size: "2.5rem"
    padding-y: "1rem"
---

## Visual Identity

Kite School of Kenpo presents itself as a serious, community-rooted martial arts school. The design leans dark and disciplined — deep charcoal backgrounds create a sense of gravitas, while the bold crimson red signals energy, action, and passion for the art. There is no decorative softness; the palette is tight and intentional.

## Color Philosophy

Two background tones carry the entire layout: `#1e2123` (primary) and `#25292b` (elevated). Page sections alternate between these two to create visual rhythm without breaking the dark-room atmosphere. The red accent (`#e31213`) is reserved for calls to action, hover states, icon highlights, and the signature section separator — it never becomes wallpaper.

White (`#ffffff`) is used exclusively for headlines and high-importance text. Secondary text (`#999999`) is visibly subordinate, serving body copy and supporting content on dark surfaces.

## Typography

Montserrat owns the headings — geometric, confident, uppercase-friendly. It suits the martial arts school brand: clean geometry with inherent authority. Roboto handles body copy with a comfortable 1.6 line-height, ensuring readability over dark backgrounds at modest contrast ratios.

The type scale was custom-tuned beyond Tailwind defaults, with an extra `2xs` size and values that step up by a modular ratio roughly following a 1.25× major third scale.

## Layout Patterns

Sections are full-width containers that alternate between the two background tones. Internal content uses a centered `container mx-auto` with generous vertical padding (`py-14`). The hero is the single exception — full-viewport-width with a background image, overlay, and centered content stacked vertically.

Grid patterns:
- **Hero**: Single column, centered
- **Two-column content**: 50/50 flex split at `md`
- **Three-column features**: Card grid, 1 col → 3 col at `md`
- **Footer**: 4-column grid at `lg`, 2 col at `md`, 1 col on mobile

## Component Style

### Buttons
All primary buttons are solid red, uppercase lettered, with a darker red on hover. They carry a visible focus ring (accessibility-first) and smooth 150ms color transitions. Secondary buttons use the darkest brand-800 tone as a base.

### Navigation
Dark bar (`#1e2123`) with centered desktop links in uppercase small-to-base font weight. A standalone red CTA button sits at the right end for Sign In / Account. Mobile: hamburger toggles a vertical stacked menu. Dropdown submenus render as full-panel overlays with the same dark brand background.

### Section Separator
Between section titles and content, a pair of small red ✕ SVG icons serves as a visual break — a nod to martial arts scoring or an "X marks the spot" motif, rendered in `#e31213`.

### Content Blocks
Long-form content within sections uses `.content-block`: off-white (`#999999`) body text, white links with red hover, white bold headings (`text-2xl font-bold`), and centered images at 60% max-width.

### Testimonials
Displayed in a 3-column card layout. Each card leads with a large open-quote SVG in gray-400, followed by headline and body, then a short red horizontal rule (`bg-red-brand-dark`) as a divider before the author attribution.

### FAQ
Collapsible items use a smooth `max-height` transition (300ms ease-in-out) for expand/collapse — pure CSS, no JS height calculation.

### Loader
An animated bouncing dot on a striped track: track color `#524656` (dark muted purple-grey), dot color `#CF4647` (slightly desaturated red). Used as a loading indicator — the color choice keeps brand coherence without being visually loud.

## Design Intent

This site communicates trust and discipline above all else. The dark palette is not decorative; it communicates seriousness appropriate for a school that works with children and teaches a combat discipline. Red accents convey passion and energy without tipping into aggression. Uppercase lettering throughout navigation and buttons reinforces formality and directness.

The design avoids rounded cards, soft shadows, or gradient treatments that might suggest a softer brand — every component is flat, dark, and intentional.