<?php

// Script : generate_index.php
// Objectif : Générer un fichier index.html avec les images de chaque dossier + miniatures + lien de téléchargement zip associé

// Configuration
$youtubeUrl = "https://www.youtube.com/@AgileToolkit";
$hubUrl = "https://agileanddevopstoolkit.github.io/agile-toolkit-hub";
$aboutUrl = "https://github.com/AgileAndDevOpsToolkit/retro-templates";

// Dossiers et fichiers à ignorer
$ignore = ['.', '..', 'generate_index.php', 'index.html', '.git', 'assets'];

// Récupérer les dossiers (types de rétro)
$dirs = array_filter(scandir(__DIR__), function($item) use ($ignore) {
    return is_dir($item) && !in_array($item, $ignore);
});

function createThumbnail($sourcePath, $thumbPath, $maxDim = 400) {
    $info = getimagesize($sourcePath);
    $type = $info[2];

    switch ($type) {
        case IMAGETYPE_JPEG:
            $img = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $img = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $img = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }

    $width = imagesx($img);
    $height = imagesy($img);

    if ($width > $height) {
        $newWidth = $maxDim;
        $newHeight = floor($height * ($maxDim / $width));
    } else {
        $newHeight = $maxDim;
        $newWidth = floor($width * ($maxDim / $height));
    }

    $tmpImg = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($tmpImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($tmpImg, $thumbPath);
            break;
        case IMAGETYPE_PNG:
            imagepng($tmpImg, $thumbPath);
            break;
        case IMAGETYPE_GIF:
            imagegif($tmpImg, $thumbPath);
            break;
    }

    imagedestroy($img);
    imagedestroy($tmpImg);
    return true;
}

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
        .gallery { display: flex; flex-wrap: wrap; justify-content: center; gap: 25px; margin-bottom: 40px; }
        .card {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 300px;
        }
        .card img {
            max-width: 300px;
            max-height: 300px;
            width: auto;
            height: auto;
            border: 2px solid #ddd;
            border-radius: 8px;
            transition: transform 0.3s;
            cursor: pointer;
        }
        .card img:hover { transform: scale(1.05); }
        .download-link {
            margin-top: 8px;
            text-decoration: none;
            color: #007BFF;
            font-size: 0.9em;
        }
        .download-link:hover {
            text-decoration: underline;
        }
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
        .external-links {
            text-align: center;
            margin: 20px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
        }
        .external-links a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #000;
        }
        .external-links img {
            margin-right: 5px;
            display: block;
        }
        .about-link {
            margin-top: 80px;
            text-align: center;
            font-size: 16px;
        }
        .about-link a {
            color: #007BFF;
            text-decoration: none;
        }
        .about-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Galerie de Templates de Rétrospectives Agiles</h1>
    <div class="external-links">
        <a href="<?= $youtubeUrl ?>" target="_blank">
            <img src="./assets/Youtube_logo.png" alt="YouTube" style="height: 18px; width: auto;">
            <span style="font-size: 18px;">Youtube Agile Toolkit</span>
        </a>
        <a href="<?= $hubUrl ?>" target="_blank">
            <img src="./assets/hub.png" alt="Hub" style="height: 32px; width: auto;">
            <span style="font-size: 18px;">Agile Toolkit Hub</span>
        </a>
    </div>

<?php foreach ($dirs as $dir): ?>
    <h2><?= htmlspecialchars($dir) ?></h2>
    <div class="gallery">
        <?php 
        $images = glob("$dir/*.{png,jpg,jpeg,gif}", GLOB_BRACE);
        foreach ($images as $img): 
            // Ignorer les images dont le nom commence par un underscore
            if (strpos(basename($img), '_') === 0) continue;

            $basename = pathinfo($img, PATHINFO_FILENAME);
            $ext = pathinfo($img, PATHINFO_EXTENSION);

            // ignorer les miniatures existantes
            if (str_starts_with(basename($img), 'miniature_')) continue;

            $miniature = "$dir/miniature_{$basename}.{$ext}";
            $zipPath = "$dir/{$basename}.zip";
            $hasZip = file_exists($zipPath);

            if (!file_exists($miniature)) {
                createThumbnail($img, $miniature);
            }
        ?>
            <div class="card">
                <img src="<?= htmlspecialchars($miniature) ?>" alt="<?= basename($img) ?>" onclick="openLightbox('<?= htmlspecialchars($img) ?>')">
                <?php if ($hasZip): ?>
                    <a class="download-link" href="<?= htmlspecialchars($zipPath) ?>" download>Télécharger les images</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<div class="about-link">
    <a href="<?= $aboutUrl ?>" target="_blank">À propos</a>
</div>

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
