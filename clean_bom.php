<?php
function removeBOM($filepath) {
    $content = file_get_contents($filepath);
    if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
        file_put_contents($filepath, substr($content, 3));
        echo "BOM removed from: $filepath\n";
    }
    // Also remove spaces before <?php
    $trimmed = ltrim($content);
    if ($trimmed !== $content && substr($trimmed, 0, 5) === "<?php") {
        file_put_contents($filepath, $trimmed);
        echo "Whitespace removed from: $filepath\n";
    }
}

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        removeBOM($file->getRealPath());
    }
}
echo "Limpieza completada.\n";
