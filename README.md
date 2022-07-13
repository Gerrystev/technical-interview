# Technical Interview
Untuk menggunakan file laravel ini, dapat melakukan hal-hal berikut:

## Install Dependencies
Pertama-tama lakukan instalasi dependecies dengan menjalankan perintah sebagai berikut:

    php composer.phar update

## Migrasi Database
Lakukan migrasi database terlebih dahulu dengan menjalankan perintah

    php artisan serve

Setelah melakukan migrasi, selanjutnya melakukan seeding database untuk menambahkan data dummy dengan menjalankan perintah sebagai berikut:

    php artisan db:seed

## Testing

Selanjutnya lakukan testing dengan menjalankan kode berikut

    php artisan test

# API
Dalam bab berikut terdapat beberapa api yang digunakan:

## User
|Nama Halaman    |Routing             |Authorization Bearer Token |
|----------------|---------------------|-----------------------------|
|Register		|api/auth/register     |No | 
|Login     		|api/auth/login        |No |
|Refresh Token	|api/auth/refresh	   |Yes|
|Logout         |api/auth/logout	   |Yes|

**api/auth/register**
Method	: POST
Body		: 

    username	: string
    password	: string
    
**api/auth/login**
Method	: POST
Body		: 

    username	: string
    password	: string

**api/auth/refresh**

Method	: POST
Body		: None

**api/auth/logout**
Method	: POST
Body		: None

## Kendaraan
|Nama Halaman    |Routing             |Authorization Bearer Token |
|----------------|---------------------|-----------------------------|
|Cek Stok			|api/kendaraan/stok		|Yes| 
|Penjualan Stok     |api/kendaraan/penjualan        |Yes|
|Laporan Penjualan	|api/kendaraan/laporan	   |Yes|

**api/kendaraan/stok**
Method	: GET
Body		: None

**api/kendaraan/penjualan**

Method	: POST
Body		: None

    _id: string id dari id stok
    banyak_penjualan: integer

**api/kendaraan/laporan**
Method	: GET
Body		: None
