<?php
function cleanupFile($filePath) {
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

$error = '';
if (isset($_POST['url'])) {
    $url = trim($_POST['url']);
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $error = "Please enter a valid URL.";
    } else {
        $tempDir = __DIR__ . '/temp';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $uniquePrefix = uniqid('video_', true);
        $outputTemplate = $tempDir . '/' . $uniquePrefix . '.%(ext)s';

        // Path to yt-dlp - adjust if needed
        $ytDlpPath = 'yt-dlp';

        // Determine if URL is YouTube or Instagram (basic check)
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            // YouTube download: best video up to 720p + best audio, merge mp4
            $format = 'bestvideo[height<=720]+bestaudio/best[height<=720]';
        } elseif (strpos($url, 'instagram.com') !== false) {
            // Instagram download: best video + best audio, merge mp4
            // Instagram reels are usually video only but merging for safety
            $format = 'bestvideo+bestaudio/best';
        } else {
            $error = "Only YouTube or Instagram URLs are supported.";
        }

        if (!$error) {
            // Build command with merge output mp4
            $command = "$ytDlpPath -f \"$format\" --merge-output-format mp4 -o " . escapeshellarg($outputTemplate) . " " . escapeshellarg($url) . " 2>&1";

            exec($command, $outputLines, $returnCode);

            if ($returnCode !== 0) {
                $error = "Download failed:<br>" . implode("<br>", array_map('htmlspecialchars', $outputLines));
            } else {
                // Find downloaded file
                $files = glob($tempDir . '/' . $uniquePrefix . '*');
                if (count($files) === 0) {
                    $error = "Failed to locate the downloaded file.";
                } else {
                    $file = $files[0];
                    $basename = basename($file);

                    // Send headers for file download
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . $basename . '"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file));

                    ob_clean();
                    flush();
                    readfile($file);

                    cleanupFile($file);
                    exit;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>YT & Instagram Video Downloader (720p)</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        margin: 0;
        padding: 20px;
    }
    h1 {
        margin-bottom: 20px;
        text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
    }
    form {
        background: rgba(255,255,255,0.15);
        padding: 25px 35px;
        border-radius: 15px;
        box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        width: 350px;
        text-align: center;
    }
    input[type="text"] {
        width: 100%;
        padding: 10px 12px;
        border-radius: 8px;
        border: none;
        margin-bottom: 15px;
        font-size: 1rem;
        outline: none;
    }
    button {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 30px;
        font-size: 1.1rem;
        background: linear-gradient(90deg, #ff4b2b, #ff416c);
        color: white;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    button:hover {
        background: linear-gradient(90deg, #ff416c, #ff4b2b);
    }
    .error {
        margin-top: 15px;
        background: rgba(255, 69, 58, 0.3);
        padding: 10px;
        border-radius: 8px;
        color: #ff1e00;
    }
    footer {
        margin-top: 40px;
        font-size: 0.9rem;
        color: #ddd;
    }
</style>
</head>
<body>

<h1>YT & Instagram Video Downloader (720p)</h1>

<form method="POST" action="">
    <input type="text" name="url" placeholder="Enter YouTube or Instagram URL" required />
    <button type="submit">Download Video</button>
</form>

<?php if (!empty($error)): ?>
    <div class="error"><?= $error ?></div>
<?php endif; ?>

<footer>Powered by yt-dlp</footer>

</body>
</html>
