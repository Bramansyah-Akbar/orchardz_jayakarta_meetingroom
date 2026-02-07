<?php
// Konfigurasi Folder Penyimpanan
$uploadDir = 'berkas/';

// Buat folder jika belum ada
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Logika Upload File (Dari HP ke PC)
$message = '';
if (isset($_POST['upload'])) {
    if (!empty($_FILES['file']['name'])) {
        $fileName = basename($_FILES['file']['name']);
        $targetFilePath = $uploadDir . $fileName;
        
        // Cek apakah file sudah ada
        if (file_exists($targetFilePath)) {
             $fileName = time() . '_' . $fileName; // Rename jika ada duplikat
             $targetFilePath = $uploadDir . $fileName;
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
            $message = "<div class='p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg'>Berhasil upload: <strong>$fileName</strong></div>";
        } else {
            $message = "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg'>Gagal mengupload file.</div>";
        }
    }
}

// Fungsi Format Ukuran File
function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) { $bytes = number_format($bytes / 1073741824, 2) . ' GB'; }
    elseif ($bytes >= 1048576) { $bytes = number_format($bytes / 1048576, 2) . ' MB'; }
    elseif ($bytes >= 1024) { $bytes = number_format($bytes / 1024, 2) . ' KB'; }
    elseif ($bytes > 1) { $bytes = $bytes . ' bytes'; }
    elseif ($bytes == 1) { $bytes = $bytes . ' byte'; }
    else { $bytes = '0 bytes'; }
    return $bytes;
}

// Ambil List File
$files = array_diff(scandir($uploadDir), array('.', '..'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiFi File Transfer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-4">

    <div class="max-w-md mx-auto bg-white shadow-lg rounded-xl overflow-hidden">
        
        <div class="bg-blue-600 p-6">
            <h1 class="text-white text-xl font-bold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                </svg>
                XAMPP File Transfer
            </h1>
            <p class="text-blue-100 text-sm mt-1">Transfer file via Local WiFi</p>
        </div>

        <div class="p-6">
            <?= $message ?>

            <form action="" method="post" enctype="multipart/form-data" class="mb-8 border-b pb-6">
                <label class="block mb-2 text-sm font-medium text-gray-900">Upload File (ke PC)</label>
                <div class="flex gap-2">
                    <input type="file" name="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2" required>
                    <button type="submit" name="upload" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                        Upload
                    </button>
                </div>
            </form>

            <h2 class="text-lg font-semibold text-gray-800 mb-4">File di PC (Siap Download)</h2>
            
            <?php if (empty($files)): ?>
                <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    Belum ada file di folder "berkas"
                </div>
            <?php else: ?>
                <ul class="space-y-2">
                    <?php foreach ($files as $file): 
                        $filePath = $uploadDir . $file;
                        $fileSize = formatSizeUnits(filesize($filePath));
                    ?>
                    <li>
                        <a href="<?= $filePath ?>" download class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-blue-50 border border-gray-200 transition group">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="bg-blue-100 p-2 rounded-full text-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="truncate">
                                    <p class="text-sm font-medium text-gray-700 group-hover:text-blue-700 truncate"><?= $file ?></p>
                                    <p class="text-xs text-gray-500"><?= $fileSize ?></p>
                                </div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            
            <div class="mt-6 text-xs text-center text-gray-400">
                Letakkan file di folder: <code>/transfer/berkas/</code>
            </div>
        </div>
    </div>
</body>
</html>