# Formules – Feuille Salariés

## Résultat de l'analyse

Aucune formule Excel n'a été détectée dans la feuille **Salariés**.

Toutes les cellules observées contiennent :
- des valeurs numériques fixes (ex : salaires, cotisations, totaux)
- des données texte (identité des salariés)

---

## Interprétation

Les calculs (CNSS, AMO, IGR, Net à payer...) semblent :
- soit saisis manuellement
- soit calculés en amont (macro VBA ou autre feuille non visible)

---

## Recommandation (très important)

Pour un système professionnel, il est conseillé d'utiliser des formules comme :

- Salaire net :
```excel
=SalaireBrut - (CNSS + AMO + IGR)
```

- CNSS :
```excel
=SalairePlafonne * TauxCNSS
```

- AMO :
```excel
=SalaireBrut * TauxAMO
```

- IR :
```excel
=MAX(0; (SalaireImposable * TauxIR) - DeductionIR)
```

---

Document d'analyse des formules de la feuille Salariés.
