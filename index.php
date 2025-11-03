<!--  
     |  ___|  __ \  |   |  _ \   _ \   
     | |      |   | |   | |   | |   |  
 \   | |      |   | |   | __ <  |   |  
\___/ \____| ____/ \___/ _| \_\\___/   
                                       
  ___|  _ \  __ \  ____|    _ )   _ _| __ \  ____|    \     ___|  
 |     |   | |   | __|     _ \ \   |  |   | __|     _ \  \___ \  
 |     |   | |   | |      ( `  <   |  |   | |      ___ \       | 
\____|\___/ ____/ _____| \___/\/ ___|____/ _____|_/    _\_____/  

  https://jcduro.bexartideas.com/index.php | 2025 | JC Duro Code & Ideas

-->

<?php
$img_convertida = '';
$info = '';

if (isset($_POST['convertir']) && isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['img']['tmp_name'];
    $tipo = mime_content_type($file);
    $destino = $_POST['destino']; // jpg, png, webp
    
    // Cargar imagen según tipo
    $img = null;
    if ($tipo == 'image/jpeg') {
        $img = imagecreatefromjpeg($file);
    } elseif ($tipo == 'image/png') {
        $img = imagecreatefrompng($file);
    } elseif ($tipo == 'image/webp') {
        $img = imagecreatefromwebp($file);
    } else {
        $info = "Formato no soportado. Usa JPG, PNG o WebP.";
    }
    
    if ($img) {
        if ($destino == 'webp') {
            if (function_exists('imagewebp')) {
                $archivo_temporal = tempnam(sys_get_temp_dir(), 'img') . '.webp';
                if (imagewebp($img, $archivo_temporal)) {
                    $img_data = file_get_contents($archivo_temporal);
                    unlink($archivo_temporal);
                    $img_convertida = base64_encode($img_data);
                    $info = "Conversión exitosa: $tipo → WebP";
                } else {
                    $info = "Error al convertir a WebP.";
                }
            } else {
                $info = "La función imagewebp() no está disponible.";
            }
        } elseif ($destino == 'png') {
            ob_start();
            imagepng($img);
            $img_data = ob_get_clean();
            $img_convertida = base64_encode($img_data);
            $info = "Conversión exitosa: $tipo → PNG";
        } elseif ($destino == 'jpg') {
            ob_start();
            imagejpeg($img, null, 90);
            $img_data = ob_get_clean();
            $img_convertida = base64_encode($img_data);
            $info = "Conversión exitosa: $tipo → JPG";
        }
        
        imagedestroy($img);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Convertidor de Imágenes PHP</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="box">
<h2>📸 Convertidor de Imágenes (JPG, PNG, WebP)</h2>
<form method="post" enctype="multipart/form-data">
<input type="file" name="img" accept=".jpg,.jpeg,.png,.webp" required />
<select name="destino" required>
    <option value="webp">Convertir a WebP</option>
    <option value="png">Convertir a PNG</option>
    <option value="jpg">Convertir a JPG</option>
</select>
<button type="submit" name="convertir">Convertir</button>
</form>
<div>
    <p><?= htmlspecialchars($info) ?></p>
    <?php if ($img_convertida): ?>
        <img src="data:image/<?= htmlspecialchars($destino) ?>;base64,<?= $img_convertida ?>" alt="Imagen convertida" />
        <br/>
        <a class="download" href="data:image/<?= htmlspecialchars($destino) ?>;base64,<?= $img_convertida ?>" download="convertida.<?= htmlspecialchars($destino) ?>">
          Descargar Imagen</a>
    <?php endif; ?>
</div>
</div>
</body>
</html>

