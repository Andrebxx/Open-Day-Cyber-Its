<?php
include 'password.php';
session_start();

if (!isset($_GET['id'])) {
    die("ID livello non specificato.");
}

$id = intval($_GET['id']);

// Recupera il nome del file dal database
$stmt = $myDB->prepare("SELECT file_nome FROM Livelli WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($file_nome);
$stmt->fetch();
$stmt->close();

if (empty($file_nome)) {
    die("Nessun file associato a questo livello.");
}

// Gestione di più file separati da virgola
$fileList = array_map('trim', explode(',', $file_nome));

if (count($fileList) === 1) {
    // Un solo file: download diretto
    $percorso = __DIR__ . "/allegati/" . basename($fileList[0]);
    if (!file_exists($percorso)) {
        die("File non trovato.");
    }
    if (ob_get_level()) ob_end_clean();
    $mime = mime_content_type($percorso);
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . basename($fileList[0]) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($percorso));
    readfile($percorso);
    exit;
} else {
    // Più file: crea uno zip temporaneo
    $zip = new ZipArchive();
    $zipName = tempnam(sys_get_temp_dir(), 'allegati_') . '.zip';
    if ($zip->open($zipName, ZipArchive::CREATE) !== TRUE) {
        die("Impossibile creare l'archivio zip.");
    }
    $almenoUno = false;
    foreach ($fileList as $file) {
        $filePath = __DIR__ . "/allegati/" . basename($file);
        if (file_exists($filePath)) {
            $zip->addFile($filePath, basename($file));
            $almenoUno = true;
        }
    }
    $zip->close();

    if (!$almenoUno) {
        unlink($zipName);
        die("Nessun file trovato.");
    }

    // Pulisci eventuale output precedente
    if (ob_get_level()) ob_end_clean();
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="allegati_livello_' . $id . '.zip"');
    header('Content-Length: ' . filesize($zipName));
    flush();
    readfile($zipName);
    unlink($zipName);
    exit;
}
?>