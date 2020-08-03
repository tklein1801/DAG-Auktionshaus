<p align="center">
    <img src="https://files.dulliag.de/web/images/logo.jpg" width="240px" height="auto">
</p>

<h1 align="center"><strong>DAG-Auktionshaus</strong></h1>

### About

Das DAG-Auktionshaus ist ein online basiertes Auktionshaus für den Arma 3 RP Server [ReallifeRPG](https://realliferpg.de). Das ganze steht in keinen offiziellen Zusammenhang und gehört offiziell nicht zu [ReallifeRPG](https://realliferpg.de).

### Work in progress

Das DAG-Auktionshaus befindet sich kurz vor der Veröffentlichung. Wir haben noch ein paar Features erarbeitet welche wir vor der finalen Veröffentlichung einbauen und testen wollen. Dennoch ist es dir möglich die aktuelle Version auf unserer [Webseite](https://dulliag.de/Auktionen/) zu testen.

### Road to v1.0

- [x] API überarbeiten(und an Real-time Datenbank anpassen)
- [x] Real-time Datenbank verwenden
- [ ] Datensicherheit überarbeiten
  - Firebase Authentication verwenden
- [ ] CSS überarbeiten => Fokus auf mobiles Design
- [ ] Avatare hochladen
- [ ] Verkäufer kontaktieren
- [x] Dateiupload verbessern
- [x] jQuery entfernen
  - Soweit es Bootstrap 4 zulässt

### Rules

**Firebase Database(Real-time)**

```
{
  /* Visit https://firebase.google.com/docs/database/security to learn more about security rules. */
  "rules": {
    ".read": true,
    ".write": true
  }
}
```

**Firebase Storage**

```
rules_version = '2';
service firebase.storage {
  match /b/{bucket}/o {
    match /{allPaths=**} {
      allow read, write: if true;
    }
  }
}
```

### Ressourcen

- [Firebase](https://firebase.google.com)
- [Bootstrap & dazugehörige Ressourcen](https://getbootstrap.com/)
