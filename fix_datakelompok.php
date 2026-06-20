<?php
$file = 'app/Controllers/DashboardController.php';
$content = file_get_contents($file);

// Read the read logic from buatKelompok
preg_match('/(\/\/ 1\. Fetch classes the staff member is plotted to.*?)(require_once __DIR__ \. \'\/\.\.\/Views\/buat_kelompok\.php\';)/s', $content, $matches);
$readLogic = $matches[1];

// Replace the read logic in dataKelompok
$pattern = '/(\$selectedClassId = isset\(\$_GET\[\'class_id\'\]\).*?)(require_once __DIR__ \. \'\/\.\.\/Views\/data_kelompok\.php\';)/s';
$replacement = $readLogic . "$2";
$newContent = preg_replace($pattern, $replacement, $content);

file_put_contents($file, $newContent);
echo "Fixed dataKelompok logic!";
