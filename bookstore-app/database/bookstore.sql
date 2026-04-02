CREATE DATABASE IF NOT EXISTS tokobook;
USE tokobook;

-- Tabel Admin
CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel User
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Kategori Buku
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Buku
CREATE TABLE books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100) NOT NULL,
    publisher VARCHAR(100),
    year INT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Tabel Pesanan
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid', 'shipped', 'completed', 'cancelled') DEFAULT 'pending',
    payment_proof VARCHAR(255),
    shipping_address TEXT,
    phone VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel Detail Pesanan
CREATE TABLE order_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    book_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- Tabel Keranjang
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    book_id INT,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- Tabel Pesan/Kontak
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin (password: admin123)
INSERT INTO admin (username, password, email) 
VALUES ('admin', MD5('admin123'), 'admin@tokobook.com');

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Novel', 'Buku novel dan fiksi'),
('Komik', 'Buku komik dan manga'),
('Pendidikan', 'Buku pelajaran dan pendidikan'),
('Bisnis', 'Buku bisnis dan ekonomi'),
('Teknologi', 'Buku teknologi dan programming');

-- Insert sample books
INSERT INTO books (category_id, title, author, publisher, year, price, stock, description, image) VALUES
(1, 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 85000, 20, 'Novel tentang perjuangan anak-anak Belitung untuk mendapatkan pendidikan', 'laskar-pelangi.jpg'),
(1, 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, 95000, 15, 'Novel sejarah Indonesia era kolonial', 'bumi-manusia.jpg'),
(2, 'Naruto Vol. 1', 'Masashi Kishimoto', 'Elex Media', 2000, 25000, 30, 'Komik manga tentang ninja muda bernama Naruto', 'naruto.jpg'),
(3, 'Matematika SMA Kelas 10', 'Tim Penulis', 'Erlangga', 2020, 120000, 50, 'Buku pelajaran matematika untuk SMA', 'matematika.jpg'),
(4, 'Rich Dad Poor Dad', 'Robert Kiyosaki', 'Gramedia', 1997, 110000, 25, 'Buku tentang literasi keuangan dan investasi', 'richdad.jpg'),
(5, 'Clean Code', 'Robert C. Martin', 'Prentice Hall', 2008, 450000, 10, 'Panduan menulis kode yang bersih dan maintainable', 'cleancode.jpg');