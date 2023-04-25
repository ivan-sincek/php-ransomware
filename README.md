# PHP Ransomware

PHP ransomware that encrypts your files, as well as file and directory names.

Ransomware is set to start encrypting files and directories from the server's web root directory and only inside the server's web root directory.

**Ransomware will self-destruct upon running, which means you only have one chance at decrypting your data.**

**Keep also in mind that each decryption file has a uniquely generated salt used in encryption and as such cannot be replaced with another decryption file.**

Tested on XAMPP for Windows v7.4.3 (64-bit) with PHP v7.4.3.

Made for educational purposes. I hope it will help!

**IMPORTANT!: Please DO NOT use this ransomware for illegal purposes! I have no [liability](https://github.com/ivan-sincek/php-ransomware/blob/master/LICENSE) over your actions!**

## How to Run

Requires PHP v5.5.0 or greater because `openssl_pbkdf2()` is used.

**Care not to do any damage! Backup your server files before running ransomware! Script will crash on large files!**

Copy [\\src\\encrypt.php](https://github.com/ivan-sincek/php-ransomware/blob/master/src/encrypt.php) to your server's web root directory (e.g. to \\xampp\\htdocs\\ on XAMPP).

Navigate to the encryption file with your preferred web browser.

Decryption file will be created automatically after the encryption phase.

---

On web servers other than XAMPP (Apache) you might need to load `OpenSSL` and `Multibyte String` libraries in PHP.

In XAMPP it is as simple as uncommenting the following in `php.ini`:

```fundamental
extension=php_openssl.dll
extension=mbstring
```

## Images

<p align="center"><img src="https://github.com/ivan-sincek/php-ransomware/blob/master/img/ransomware.jpg" alt="Ransomware"></p>

<p align="center">Figure 1 - Ransomware</p>

<p align="center"><img src="https://github.com/ivan-sincek/php-ransomware/blob/master/img/encrypted_content.jpg" alt="Encrypted Content"></p>

<p align="center">Figure 2 - Encrypted Content</p>
