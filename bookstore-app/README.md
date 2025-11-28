# 📚 TokoBook - Aplikasi Toko Buku Online# Bookstore Application



Aplikasi toko buku online berbasis web menggunakan PHP Native, MySQL, HTML, dan CSS.## Overview

This is a simple bookstore application designed to manage books, users, and orders. The application features two main actors: Admin and User. Each actor has specific functionalities to interact with the bookstore.

## ✨ Fitur Aplikasi

## Features

### 👨‍💼 Admin

- Login Admin### Admin

- Dashboard statistik- **Dashboard**: Overview of the bookstore's data.

- **Kelola Kategori Buku** (Create, Read, Update, Delete)- **Manage Books**: Create, update, and delete book entries.

- **Kelola Data Buku** (Create, Read, Update, Delete)- **Manage Users**: View and manage registered users, including deletion.

- Lihat daftar user terdaftar- **Manage Orders**: View and manage user orders, including status updates.

- Kelola pesanan dari user

- Lihat pesan dari user### User

- **Dashboard**: Access to browse books, view the cart, and check orders.

### 👤 User- **Browse Books**: Display a list of available books for browsing and searching.

- Registrasi akun baru- **Cart**: View selected books and proceed to checkout.

- Login user- **Orders**: View past orders and their statuses.

- Browse/Jelajahi buku

- Pencarian buku (by judul, penulis, deskripsi)## Installation

- Filter buku berdasarkan kategori

- Lihat detail buku1. Clone the repository:

- Tambah buku ke keranjang   ```

- Checkout & pembayaran   git clone <repository-url>

- Upload bukti pembayaran   ```

- Lihat status pesanan

- Kirim pesan ke admin2. Navigate to the project directory:

- Halaman About Us   ```

   cd bookstore-app

## 🛠️ Teknologi   ```



- **Backend**: PHP (Native)3. Import the SQL database:

- **Database**: MySQL   - Open your MySQL client and run the SQL script located in `database/bookstore.sql` to create the necessary database structure and initial data.

- **Frontend**: HTML, CSS, JavaScript

- **Server**: Apache (XAMPP)4. Configure the database connection:

   - Edit the `src/includes/config.php` file to set your database connection details.

## 📋 Requirement

5. Start a local server:

- XAMPP (PHP 7.4+ & MySQL)   - You can use tools like XAMPP, WAMP, or any PHP server to run the application.

- Web Browser (Chrome, Firefox, etc.)

6. Access the application:

## 🚀 Cara Instalasi   - Open your web browser and go to `http://localhost/bookstore-app/src/index.php`.



### 1. Clone atau Download Project## Technologies Used

```bash- HTML

git clone [repository-url]- CSS

# atau download dan extract ke folder htdocs XAMPP- PHP

```- MySQL



### 2. Pindahkan ke XAMPP## Contributing

Letakkan folder project di:Feel free to submit issues or pull requests for improvements or bug fixes. 

```

C:\xampp\htdocs\TokoBook\## License

```This project is open-source and available under the MIT License.

### 3. Buat Database
1. Buka **phpMyAdmin** (http://localhost/phpmyadmin)
2. Buat database baru bernama `tokobook`
3. Import file SQL:
   - Klik database `tokobook`
   - Pilih tab **Import**
   - Pilih file `database/bookstore.sql`
   - Klik **Go**

### 4. Konfigurasi Database (Opsional)
Jika username/password MySQL berbeda, edit file:
```
src/includes/database.php
```

Ubah:
```php
private $username = "root";  // sesuaikan
private $password = "";      // sesuaikan
```

### 5. Jalankan Aplikasi
1. Start **Apache** dan **MySQL** di XAMPP Control Panel
2. Buka browser dan akses:
```
http://localhost/TokoBook/bookstore-app/src/
```

## 🔑 Login Credentials

### Admin
- **URL**: `http://localhost/TokoBook/bookstore-app/src/auth/login-admin.php`
- **Username**: `admin`
- **Password**: `admin123`

### User (Test Account)
- Daftar akun baru melalui halaman register
- Atau buat manual di database

## 📁 Struktur Folder

```
bookstore-app/
├── database/
│   └── bookstore.sql           # File SQL database
├── src/
│   ├── admin/                  # Halaman admin
│   │   ├── index.php          # Dashboard
│   │   ├── manage-books.php   # Kelola buku
│   │   ├── manage-orders.php  # Kelola pesanan
│   │   └── manage-users.php   # Kelola user
│   ├── auth/                   # Autentikasi
│   │   ├── login.php          # Login user
│   │   ├── login-admin.php    # Login admin
│   │   ├── register.php       # Register user
│   │   └── logout.php         # Logout
│   ├── user/                   # Halaman user
│   │   ├── index.php          # Dashboard user
│   │   ├── browse-books.php   # Browse buku
│   │   ├── cart.php           # Keranjang
│   │   ├── orders.php         # Pesanan
│   │   ├── about.php          # Tentang kami
│   │   └── contact.php        # Kontak
│   ├── includes/               # File include
│   │   ├── config.php         # Konfigurasi
│   │   ├── database.php       # Koneksi database
│   │   ├── header.php         # Header
│   │   └── footer.php         # Footer
│   ├── assets/                 # Asset (CSS, JS, Images)
│   │   ├── css/
│   │   └── js/
│   ├── uploads/                # Upload files
│   │   ├── books/             # Gambar buku
│   │   └── payments/          # Bukti pembayaran
│   └── index.php              # Homepage
└── README.md
```

## 📊 Database Schema

### Tables
1. **admin** - Data administrator
2. **users** - Data pengguna/customer
3. **categories** - Kategori buku
4. **books** - Data buku
5. **cart** - Keranjang belanja
6. **orders** - Data pesanan
7. **order_details** - Detail item pesanan
8. **messages** - Pesan dari user

## 🎨 Fitur Utama

### Untuk Admin:
✅ CRUD Kategori Buku
✅ CRUD Data Buku (dengan upload gambar)
✅ Manajemen User
✅ Manajemen Pesanan
✅ Lihat pesan dari customer

### Untuk User:
✅ Registrasi & Login
✅ Browse & Search Buku
✅ Filter berdasarkan kategori
✅ Keranjang Belanja
✅ Checkout & Upload Bukti Bayar
✅ Tracking Pesanan
✅ Kirim Pesan ke Admin
✅ Halaman About Us

## 🔧 Troubleshooting

### Error "Connection failed"
- Pastikan MySQL sudah running
- Cek username/password di `database.php`
- Pastikan database `tokobook` sudah dibuat

### Gambar tidak muncul
- Pastikan folder `uploads/books/` sudah dibuat
- Cek permission folder uploads (777)

### Halaman blank/error
- Aktifkan error reporting di PHP
- Cek error log di XAMPP

## 📝 Catatan

- Aplikasi ini dibuat untuk keperluan pembelajaran
- Gunakan password yang kuat untuk production
- Backup database secara berkala
- Validasi input untuk keamanan

## 👨‍💻 Developer

Dibuat dengan ❤️ menggunakan PHP Native

## 📄 License

MIT License - Free to use for educational purposes
