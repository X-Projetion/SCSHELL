<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCSHELL</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        center {
            font-size: 24px;
            font-weight: bold;
            margin: 20px;
            display: block;
        }

        a {
            display: block;
            text-align: center;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px auto;
            width: 150px;
        }

        a:hover {
            background-color: #2980b9;
        }

        #scanResults {
            width: 500px;
            height: 500px;
            margin: 20px auto;
            overflow: auto; /* Add overflow property for scrollbars if needed */
        }

        #scanResults div {
            margin: 0;
        }

        #scanResults p, #scanResults hr {
            margin: 0;
            white-space: pre-wrap; /* Preserve whitespace and wrap lines */
        }

        .safe {
            color: green;
        }

        .found {
            color: red;
        }

        hr {
            border: 0;
            height: 1px;
            background: #333;
            margin: 10px 0;
        }

        p.success {
            color: blue;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <center>SCSHELL</center>
    <a href="?scan">Scanner</a>

    <?php
    if (isset($_GET['scan'])) {
        set_time_limit(0);
        error_reporting(0);
        @ini_set('zlib.output_compression', 0);
        header("Content-Encoding: none");
        ob_start();

        function ngelist($dir, &$dirs = array()) {
            $scan = scandir($dir);
            foreach ($scan as $key => $value) {
                $path = $dir . DIRECTORY_SEPARATOR . $value;
                if (!is_dir($path)) {
                    $dirs[] = $path;
                } else if ($value != "." && $value != "..") {
                    $dirs[] = $path;
                    ngelist($path, $dirs);
                }
            }
            return $dirs;
        }

        function baca($filenya) {
            $filesize = filesize($filenya);
            $filesize = round($filesize / 1024 / 1024, 1);

            if ($filesize > 2) {
                $pesan = "Skipped--";
                echo $pesan;
                $fp = fopen('result-scanner.html', 'a');
                fwrite($fp, $pesan . "\n");
                fclose($fp);
            } else {
                $php_file = file_get_contents($filenya);
                $tokens   = token_get_all($php_file);
                $dirs = array();
                $batas    = count($tokens);

                if ($batas > 0) {
                    for ($i = 0; $i < $batas; $i++) {
                        if (isset($tokens[$i][1])) {
                            $dirs[] .= $tokens[$i][1];
                        }
                    }
                }

                $dirs = array_values(array_unique(array_filter(array_map('trim', $dirs))));
                return ($dirs);
            }
        }

        function ngecek($string) {
            $query   = array(
                'base64_encode',
                'base64_decode',
                'FATHURFREAKZ',
                'eval',
                'system',
                'gzinflate',
                'str_rot13',
                'convert_uu',
                'shell_data',
                'getimagesize',
                'magicboom',
                'mysql_connect',
                'mysqli_connect',
                'basename',
                'getimagesize',
                'exec',
                'shell_exec',
                'fwrite',
                'str_replace',
                'mail',
                'file_get_contents',
                'url_get_contents',
                'move_uploaded_file',
                'symlink',
                'substr',
                'pathinfo',
                '__file__',
                '__halt_compiler'
            );

            $dirs = "";
            foreach ($query as $value) {
                if (in_array($value, $string)) {
                    $dirs .= $value . ", ";
                }
            }

            if ($dirs != "") {
                $dirs = substr($dirs, 0, -2);
            }

            return $dirs;
        }

        $list = ngelist(".");
        echo '<div align="center" id="scanResults">';

        foreach ($list as $value) {
            if (is_file($value)) {
                $string = baca($value);
                $cek    = ngecek($string);

                if (empty($cek)) {
                    echo '<div class="safe">' . $value . ' => Safe</div><hr>';
                } else if (preg_match("/, /", $cek)) {
                    echo '<div class="found">' . $value . ' => Found (' . $cek . ')</div><hr>';
                    $fp = fopen('result-scanner.html', 'a');
                    fwrite($fp, $pesan . "\n");
                    fclose($fp);
                } else {
                    echo '<div class="found">' . $value . ' => Found (' . $cek . ')</div><hr>';
                }

                ob_flush();
                flush();
                sleep(1);
            }
        }

        echo '</div>';
        ob_end_flush();
    }
    ?>
    <p class="success"><a href="result-scanner.html">Success, open result here</a></p>
</body>
</html>