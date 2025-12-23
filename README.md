# retro-templates

Ce projet propose une galerie de templates pour rétrospectives agiles, générée automatiquement à partir des images présentes dans différents dossiers. Chaque template est accompagné d'une miniature et, si disponible, d'un lien de téléchargement d'assets au format ZIP.

## Fonctionnement

- Les images sont organisées par dossier, chaque dossier représentant un type de rétrospective.
- Le script [`generate_index.php`](generate_index.php) génère automatiquement la page [`index.html`](index.html) :
  - Création de miniatures pour chaque image.
  - Affichage des images par catégorie.
  - Ajout d'un lien de téléchargement ZIP si le fichier existe.
  - Visualisation des images en grand via une lightbox.

## Utilisation

1. Pour ajouter un nouveau template de rétrospective, il suffit de placer une image dans le dossier correspondant à la rétrospective (ex : `Speedboat/`, `Foot/`, etc.).
2. Ensuite, lancer le script PHP pour générer la galerie :
   ```sh
   php generate_index.php
   ```

## Liens utiles

- 🌐 [Le site des templates est hébergé et accessible ici](https://agileanddevopstoolkit.github.io/retro-templates/)
- 🎥 [Chaîne Youtube Agile Toolkit](https://www.youtube.com/@AgileToolkit)
- 📚 [Agile Toolkit Hub](https://agileanddevopstoolkit.github.io/agile-toolkit-hub)
- ❤️ [Soutenir sur Tipeee](https://fr.tipeee.com/agile-toolkit)

## Structure du projet

```
generate_index.php
index.html
assets/
<dossiers de templates>
```

