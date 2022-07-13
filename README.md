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

**api/auth/register**<br />
Method	: POST <br />
Body		: 

    username	: string
    password	: string
    
**api/auth/login** <br />
Method	: POST <br />
Body		:

    username	: string
    password	: string

**api/auth/refresh** <br />

Method	: POST <br />
Body		: None 

**api/auth/logout** <br />
Method	: POST <br />
Body		: None <br />

## Kendaraan
|Nama Halaman    |Routing             |Authorization Bearer Token |
|----------------|---------------------|-----------------------------|
|Cek Stok			|api/kendaraan/stok		|Yes| 
|Penjualan Stok     |api/kendaraan/penjualan        |Yes|
|Laporan Penjualan	|api/kendaraan/laporan	   |Yes|

**api/kendaraan/stok** <br />
Method	: GET <br />
Body		: None 

**api/kendaraan/penjualan** <br />

Method	: POST <br />
Body		: None 

    _id: string id dari id stok
    terjual: integer

**api/kendaraan/laporan** <br />
Method	: GET <br />
Body		: None
