<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/';
    
    // Garantir que o diretório existe
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $file = $_FILES['test_file'];
    $path = $uploadDir . basename($file['name']);
    
    echo '<pre>';
    echo "File info:\n";
    print_r($file);
    echo "\nDestination: $path\n";
    
    if (move_uploaded_file($file['tmp_name'], $path)) {
        echo "Upload successful!";
    } else {
        echo "Upload failed!";
    }
    echo '</pre>';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teste de Upload</title>
</head>
<body>
    <h1>Teste de Upload</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="test_file">
        <button type="submit">Upload</button>
    </form>
</body>
</html>