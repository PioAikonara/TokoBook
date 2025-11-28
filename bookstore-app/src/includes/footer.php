    </main>
    <?php if (!isset($_SESSION['admin_id'])): ?>
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>TokoBook</h3>
                    <p>Toko Buku Online Terpercaya</p>
                </div>
                <div class="footer-section">
                    <h4>Menu</h4>
                    <ul>
                        <li><a href="<?php echo BASE_URL; ?>index.php">Beranda</a></li>
                        <li><a href="<?php echo BASE_URL; ?>user/browse-books.php">Buku</a></li>
                        <li><a href="<?php echo BASE_URL; ?>user/about.php">Tentang Kami</a></li>
                        <li><a href="<?php echo BASE_URL; ?>user/contact.php">Kontak</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Kontak</h4>
                    <p>Email: info@tokobook.com</p>
                    <p>Telp: +62 812-3456-7890</p>
                    <p>Alamat: Jakarta, Indonesia</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> TokoBook. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>