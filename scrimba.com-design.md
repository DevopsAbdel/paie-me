---
version: alpha
name: Scrimba Learning — Dark
description: A clean, education-first UI in dark mode with soft blue accents and compact editorial hierarchy. Dark adaptation of the Scrimba Learning design system.
colors:
  primary: "#1E90FF"
  primary-60: "rgba(30,144,255,0.15)"
  primary-70: "rgba(30,144,255,0.25)"
  secondary: "#6B7280"
  tertiary: "#A78BFA"
  neutral: "#0F1725"
  surface: "#1B2437"
  on-surface: "#E6EDF7"
  on-surface-muted: "#8FA3BD"
  border: "#2C3A52"
  border-strong: "#3A4A66"
  success: "#22C55E"
  warning: "#F59E0B"
  error: "#EF4444"
typography:
  headline-display:
    fontFamily: system-ui
    fontSize: 36px
    fontWeight: 800
    lineHeight: 54px
    letterSpacing: 0px
  headline-lg:
    fontFamily: system-ui
    fontSize: 24px
    fontWeight: 300
    lineHeight: 36px
    letterSpacing: 0px
  headline-md:
    fontFamily: system-ui
    fontSize: 20px
    fontWeight: 300
    lineHeight: 24px
    letterSpacing: 0px
  headline-sm:
    fontFamily: system-ui
    fontSize: 18px
    fontWeight: 300
    lineHeight: 22px
    letterSpacing: 0px
  body-lg:
    fontFamily: system-ui
    fontSize: 16px
    fontWeight: 300
    lineHeight: 24px
    letterSpacing: 0px
  body-md:
    fontFamily: system-ui
    fontSize: 14px
    fontWeight: 300
    lineHeight: 22px
    letterSpacing: 0px
  body-sm:
    fontFamily: system-ui
    fontSize: 12px
    fontWeight: 300
    lineHeight: 18px
    letterSpacing: 0px
  label-lg:
    fontFamily: system-ui
    fontSize: 14px
    fontWeight: 500
    lineHeight: 20px
    letterSpacing: 0px
  label-md:
    fontFamily: system-ui
    fontSize: 12px
    fontWeight: 500
    lineHeight: 16px
    letterSpacing: 0px
  label-sm:
    fontFamily: system-ui
    fontSize: 11px
    fontWeight: 500
    lineHeight: 14px
    letterSpacing: 0.02em
  nav-item:
    fontFamily: system-ui
    fontSize: 14px
    fontWeight: 400
    lineHeight: 20px
    letterSpacing: 0px
  button-md:
    fontFamily: system-ui
    fontSize: 14px
    fontWeight: 500
    lineHeight: 18px
    letterSpacing: 0px
rounded:
  none: 0px
  sm: 4px
  md: 5px
  lg: 8px
  xl: 12px
  full: 9999px
spacing:
  xs: 2px
  sm: 10px
  md: 18px
  lg: 32px
  xl: 46px
  gutter: 24px
  section: 48px
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "#FFFFFF"
    typography: "{typography.button-md}"
    rounded: "{rounded.md}"
    padding: 0px 16px
    height: 36px
  button-primary-hover:
    backgroundColor: "{colors.primary-70}"
    textColor: "#FFFFFF"
    typography: "{typography.button-md}"
    rounded: "{rounded.md}"
    padding: 0px 16px
    height: 36px
  button-secondary:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.primary}"
    typography: "{typography.button-md}"
    rounded: "{rounded.md}"
    padding: 0px 16px
    height: 36px
  button-tertiary:
    backgroundColor: "transparent"
    textColor: "{colors.on-surface}"
    typography: "{typography.button-md}"
    rounded: "{rounded.md}"
    padding: 0px 12px
    height: 36px
  card:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.on-surface}"
    rounded: "{rounded.lg}"
    padding: 16px
    border: "1px solid {colors.border}"
  input:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.on-surface}"
    typography: "{typography.body-md}"
    rounded: "{rounded.md}"
    padding: 0px 12px
    height: 36px
    border: "1px solid {colors.border}"
  chip:
    backgroundColor: "{colors.primary-60}"
    textColor: "{colors.primary}"
    typography: "{typography.label-md}"
    rounded: "{rounded.full}"
    padding: 0px 8px
    height: 20px
---

# Scrimba Learning — Dark

## Overview
Adaptation **dark mode** du thème Scrimba Learning : la même clarté
éditoriale et hiérarchie compacte, sur un canvas sombre (navy profond)
avec un bleu primaire vif (`#1E90FF`) réservé à l'action, la sélection et
l'accent. Pas de light mode : le thème sombre est le thème unique du projet.

## Colors
- **Primary (#1E90FF):** bleu vif pour CTA, états actifs, liens, sélection.
- **Primary tint (rgba 30,144,255,0.15) et hover tint (0.25):** lavis bleu
  sombre pour la navigation sélectionnée, chips, surlignages discrets.
- **Secondary (#6B7280):** ardoise pour textes de support, méta, nav secondaire.
- **Tertiary (#A78BFA):** accent complémentaire pour badges/statuts spéciaux.
- **Neutral (#0F1725):** fond de page navy profond, canvas ouvert.
- **Surface (#1B2437):** cartes, panneaux et contrôles sur surface sombre.
- **On-surface (#E6EDF7):** texte principal, un blanc bleuté très lisible.
- **On-surface muted (#8FA3BD):** descriptions, indications, libellés discrets.
- **Border (#2C3A52) / border-strong (#3A4A66):** lignes structurelles fines
  qui définissent inputs, cards et séparateurs.
- **Success (#22C55E), warning (#F59E0B), error (#EF4444):** statuts réservés
  aux tags, alertes, états de validation.

## Typography
system-ui partout. Titres grands et confiants : `headline-display` gras pour
le hero, `headline-lg/md/sm` légers et éditoriaux pour cartes et titres de
section. Texte compact et lisible à 14px / 22px. Labels en graisse moyenne
pour navigation, boutons et métadonnées.

## Layout & Spacing
Sidebar persistante à gauche + canvas principal large. Rythme d'espacement
compact mais aéré : 2, 10, 18, 32, 46px. Cartes et contrôles avec padding
interne modéré, sections majeures séparées verticalement.

## Elevation & Depth
Profondeur volontairement subtile : bordures, contraste tonal et séparation
des cartes plutôt que des ombres lourdes. Résultat flat et net : les cartes
se détachent du fond sombre par des lignes fines et l'espacement.

## Shapes
Contrôles interactifs à 5px, cartes à 8px, pills et chips entièrement
arrondies quand nécessaire. Moderne, accessible, discipliné.

## Components
- `button-primary` : fond bleu plein `#1E90FF`, texte blanc, 36px, 5px.
- `button-secondary` : surface sombre + texte bleu (actions secondaires).
- `button-tertiary` : transparent, texte clair (actions calmes).
- Hover : le bleu s'éclaircit légèrement, reste subtil.
- Cards : surface sombre, bordure 1px, 8px, padding 16px, pas de densité.
- Inputs : surface sombre, 5px, 36px, placeholder gris ardoise.
- Chips/badges : petits marqueurs contrastés, radius plein, hauteur 20px.
- Sidebar : text-forward, petites icônes, libellés discrets, état sélectionné
  avec fond teinté + bordure gauche bleue.
- Labels de statut (validé, en attente, refusé, etc.) : petites chips aux
  couleurs contrôlées (vert, ambre, rouge).

## Do's and Don'ts
- Do keep the UI airy and readable with strong spacing between major zones.
- Do use the blue primary sparingly for action, selection, and emphasis.
- Do rely on borders and whitespace more than shadows for hierarchy.
- Do keep typography lightweight and compact in body areas.
- Don't introduce heavy gradients, glass effects, or ornate shadows.
- Don't make cards visually dense.
- Don't replace the system-ui feel with decorative display fonts.
- Don't use overly rounded shapes except for pills, badges, and chips.
- **Do always use dark mode — never a light theme.**
