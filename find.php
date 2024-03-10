<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="NOINDEX, NOFOLLOW">
    <meta name="description" content="Scanner Shell Backdoor">
    <title>SCSHELL</title>
    <link rel="shortcut icon" href="https://avatars.githubusercontent.com/u/161194427?v=4">
    <link rel="icon" type="image/png" href="https://avatars.githubusercontent.com/u/161194427?v=4" sizes="16x16">
    <link rel="apple-touch-icon" href="https://avatars.githubusercontent.com/u/161194427?v=4" sizes="180x180">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:500,700&display=swap">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            background-color: grey;
            color: white;
            margin: 0;
            padding: 0;
            height: 100vh;
        }
        .bg {
            position: relative;
            background-color: grey;
            background-image: url("https://raw.githubusercontent.com/X-Projetion/SCSHELL/main/scshell-background.png");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            color: white;
            height: 500px;
            width: 600px;
            margin: auto;
        }
        .content {
            text-align: center;
            margin-top: 20px;
        }
        h1 {
    text-align: center;
        }
        .icon {
            text-decoration: none;
            color: white;
            margin-right: 10px;
        }
        .button {
            display: inline-block;
            text-align: center;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 10px;
            width: 150px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #2980b9;
        }
        #scanResults {
            width: auto;
            max-width: 500px;
            margin: auto;
            padding: 20px;
            overflow: auto;
            background-color: #fff;
            border: 2px solid #3498db;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .safe {
            color: green;
        }
        .found {
            color: red;
        }
        a {
            text-decoration: none;
            color: #fff;
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
        footer {
            background-color: transparent;
            color: white;
            padding: 20px;
            text-align: center;
            width: 100%;
        }
        @media only screen and (max-width: 600px) {
            .bg {
                height: 300px;
            }

            #scanResults {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="bg">
    <div class="content">
        <h1>-=SCSHELL=-</h1>
        <h4>Scanner Shell Backdoor</h4>
        <br><br><br><br><br><br>
        <a href="?scan" class="button">Scanner</a>
        <?php
        if (isset($_GET['scan'])) {
            set_time_limit(0);
            error_reporting(0);
            @ini_set('zlib.output_compression', 0);
            header("Content-Encoding: none");
            ob_start();

            function daf($dir, &$dirs = array()) {
                $scan = scandir($dir);
                foreach ($scan as $key => $value) {
                    $path = $dir . DIRECTORY_SEPARATOR . $value;
                    if (!is_dir($path)) {
                        $dirs[] = $path;
                    } else if ($value != "." && $value != "..") {
                        $dirs[] = $path;
                        daf($path, $dirs);
                    }
                }
                return $dirs;
            }

            function read($filenya) {
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
                    return $dirs;
                }
            }

            function cek($string) {
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

            $list = daf(".");
            echo "<div style='background-color: transparent;' id='scanResults'><div id='scanResults'>";
            foreach ($list as $value) {
                if (is_file($value)) {
                    $string = read($value);
                    $cek    = cek($string);

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
            echo '<p class="success"><a href="result-scanner.html" class="button">Success, open result here</a></p>';
            ob_end_flush();
        }
        ?>
</div>
<footer>
    <p>&copy; 2024 <span>&bull;</span> SCSHELL. All rights reserved. | <a href="https://github.com/X-Projetion/">X-Projetion.</a></p>
    <a href="https://github.com/X-Projetion/" target="_blank" class="icon"><i class="fab fa-github"></i></a>
    <a href="https://www.instagram.com/lutfifakee/" target="_blank" class="icon"><i class="fab fa-instagram"></i></a>
</footer>
</div>
</div>
</body>
</html>