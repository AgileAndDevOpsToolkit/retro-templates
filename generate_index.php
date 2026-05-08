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

$galleryByCategory = [];

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
            inset: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .lightbox.is-open {
            display: flex;
        }
        .lightbox img {
            margin: auto;
            display: block;
            max-width: min(82vw, 1400px);
            max-height: 84vh;
            position: relative;
            z-index: 2;
        }
        .lightbox-nav-zone {
            position: absolute;
            top: 0;
            bottom: 0;
            width: max(88px, 12vw);
            border: none;
            background: transparent;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 3;
            padding: 0;
        }
        .lightbox-nav-prev {
            left: 0;
        }
        .lightbox-nav-next {
            right: 0;
        }
        .lightbox-arrow {
            font-size: 42px;
            line-height: 1;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.35);
            transition: background-color 0.2s ease, transform 0.2s ease;
            user-select: none;
        }
        .lightbox-nav-zone:hover .lightbox-arrow {
            background: rgba(0, 0, 0, 0.6);
            transform: scale(1.08);
        }
        @media (max-width: 700px) {
            .lightbox-nav-zone {
                width: max(76px, 16vw);
            }
            .lightbox-arrow {
                font-size: 34px;
                width: 50px;
                height: 50px;
            }
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
    <?php
    $images = glob("$dir/*.{png,jpg,jpeg,gif}", GLOB_BRACE);
    $cards = [];

    foreach ($images as $img) {
        // Ignorer les images dont le nom commence par un underscore
        if (strpos(basename($img), '_') === 0) {
            continue;
        }

        $basename = pathinfo($img, PATHINFO_FILENAME);
        $ext = pathinfo($img, PATHINFO_EXTENSION);

        // ignorer les miniatures existantes
        if (str_starts_with(basename($img), 'miniature_')) {
            continue;
        }

        $miniature = "$dir/miniature_{$basename}.{$ext}";
        $zipPath = "$dir/{$basename}.zip";

        if (!file_exists($miniature)) {
            createThumbnail($img, $miniature);
        }

        $cards[] = [
            'image' => $img,
            'miniature' => $miniature,
            'zipPath' => $zipPath,
            'hasZip' => file_exists($zipPath)
        ];
    }

    $galleryByCategory[$dir] = array_map(function($card) {
        return $card['image'];
    }, $cards);
    ?>
    <h2><?= htmlspecialchars($dir) ?></h2>
    <div class="gallery">
        <?php foreach ($cards as $index => $card): ?>
            <div class="card">
                <img
                    src="<?= htmlspecialchars($card['miniature']) ?>"
                    alt="<?= basename($card['image']) ?>"
                    onclick='openLightbox(<?= json_encode($dir, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>, <?= $index ?>)'>
                <?php if ($card['hasZip']): ?>
                    <a class="download-link" href="<?= htmlspecialchars($card['zipPath']) ?>" download>Télécharger les images</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<div class="about-link">
    <a href="<?= $aboutUrl ?>" target="_blank">À propos</a>
</div>

<div id="lightbox" class="lightbox" aria-hidden="true">
    <button id="lightbox-prev" class="lightbox-nav-zone lightbox-nav-prev" aria-label="Image précédente" type="button">
        <span class="lightbox-arrow">&#10094;</span>
    </button>
    <img id="lightbox-img" src="" alt="Aperçu en grand format">
    <button id="lightbox-next" class="lightbox-nav-zone lightbox-nav-next" aria-label="Image suivante" type="button">
        <span class="lightbox-arrow">&#10095;</span>
    </button>
</div>

<script>
    const galleryByCategory = <?= json_encode($galleryByCategory, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const prevButton = document.getElementById('lightbox-prev');
    const nextButton = document.getElementById('lightbox-next');

    let currentCategory = null;
    let currentIndex = -1;

    function updateLightboxView() {
        const images = galleryByCategory[currentCategory] || [];
        const currentImage = images[currentIndex];

        if (!currentImage) {
            closeLightbox();
            return;
        }

        lightboxImg.src = currentImage;
        prevButton.style.display = currentIndex > 0 ? 'flex' : 'none';
        nextButton.style.display = currentIndex < images.length - 1 ? 'flex' : 'none';
    }

    function closeLightbox() {
        lightbox.classList.remove('is-open');
        lightbox.setAttribute('aria-hidden', 'true');
        lightboxImg.src = '';
        currentCategory = null;
        currentIndex = -1;
    }

    function openLightbox(category, index) {
        currentCategory = category;
        currentIndex = index;

        updateLightboxView();
        lightbox.classList.add('is-open');
        lightbox.setAttribute('aria-hidden', 'false');
    }

    prevButton.addEventListener('click', function(event) {
        event.stopPropagation();
        if (currentIndex > 0) {
            currentIndex -= 1;
            updateLightboxView();
        }
    });

    nextButton.addEventListener('click', function(event) {
        event.stopPropagation();
        const images = galleryByCategory[currentCategory] || [];
        if (currentIndex < images.length - 1) {
            currentIndex += 1;
            updateLightboxView();
        }
    });

    lightboxImg.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    lightbox.addEventListener('click', function(event) {
        if (!event.target.closest('.lightbox-nav-zone') && event.target !== lightboxImg) {
            closeLightbox();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (!lightbox.classList.contains('is-open')) {
            return;
        }

        if (event.key === 'Escape') {
            closeLightbox();
        }

        if (event.key === 'ArrowLeft' && prevButton.style.display !== 'none') {
            prevButton.click();
        }

        if (event.key === 'ArrowRight' && nextButton.style.display !== 'none') {
            nextButton.click();
        }
    });
    
    window.openLightbox = openLightbox;
    window.closeLightbox = closeLightbox;

</script>

</body>
</html>
<?php

$content = ob_get_clean();
file_put_contents('index.html', $content);
echo "Le fichier index.html a été généré avec succès !\n";
