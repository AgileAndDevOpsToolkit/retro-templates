<?php

// Script : generate_index.php
// Objectif : Générer un fichier index.html avec les images de chaque dossier

// Dossiers et fichiers à ignorer
$ignore = ['.', '..', 'generate_index.php', 'index.html', '.git', 'assets'];

// URL de la chaîne YouTube
$youtube_url = "https://www.youtube.com/@AgileToolkit";

// Récupérer les dossiers (types de rétro)
$dirs = array_filter(scandir(__DIR__), function($item) use ($ignore) {
    return is_dir($item) && !in_array($item, $ignore);
});

ob_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Templates de Rétrospectives Agiles</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; }
        h1 { text-align: center; }
        h2 { margin-top: 40px; color: #333; text-align: center; }
        .gallery { display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; }
        .gallery img { 
            max-width: 300px; 
            max-height: 300px;
            width: auto;
            height: auto;
            object-fit: contain;
            border: 2px solid #ddd; 
            border-radius: 8px; 
            transition: transform 0.3s; 
            cursor: pointer; 
            display: block;
        }
        .gallery img:hover { transform: scale(1.05); }
        
        /* Lightbox */
        .lightbox {
            display: none;
            position: fixed;
            z-index: 999;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }
        .lightbox img {
            margin: auto;
            display: block;
            max-width: 80%;
            max-height: 80%;
        }
    </style>
</head>
<body>
    <h1>Galerie de Templates de Rétrospectives Agiles</h1>
    
    <div style="text-align: center; margin: 20px 0; display: flex; justify-content: center; align-items: center;">
        <a href="<?= htmlspecialchars($youtube_url) ?>" target="_blank" style="display: flex; align-items: center;">
            <img src="./assets/Youtube_logo.png" alt="YouTube" style="margin-right: 5px; height: 18px; width: auto; display: block;">
            <span style="font-size: 18px;">Youtube Agile Toolkit</span>
        </a>
    </div>

<?php foreach ($dirs as $dir): ?>
    <h2><?= htmlspecialchars($dir) ?></h2>
    <div class="gallery">
        <?php 
        $images = glob("$dir/*.{png,jpg,jpeg,gif}", GLOB_BRACE);
        foreach ($images as $img): ?>
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= basename($img) ?>" onclick="openLightbox(this.src)">
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<div id="lightbox" class="lightbox" onclick="this.style.display='none'">
    <img id="lightbox-img" src="">
</div>

<script>
    function openLightbox(src) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox').style.display = 'block';
    }
</script>

</body>
</html>
<?php

$content = ob_get_clean();
file_put_contents('index.html', $content);
echo "Le fichier index.html a été généré avec succès !\n";
