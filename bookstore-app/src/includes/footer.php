    </main>
    <footer class="site-footer">
        <style>
            :root {
                --brand-blue: #4834d4;
                --brand-dark: #130f40;
                --brand-accent: #686de0;
                --footer-bg: #130f40;
                --footer-text: #a4b0be;
            }

            .site-footer {
                background: var(--footer-bg);
                color: var(--footer-text);
                padding: 100px 0 40px;
                margin-top: 100px;
                border-top: 1px solid rgba(255, 255, 255, 0.05);
            }

            .footer-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
                display: grid;
                grid-template-columns: 2fr 1.2fr 1.2fr 1.8fr;
                gap: 50px;
            }

            .footer-brand .brand-name {
                font-size: 1.8rem;
                font-weight: 800;
                color: #fff;
                margin-bottom: 25px;
                display: block;
                letter-spacing: -1px;
            }

            .footer-brand p {
                line-height: 1.8;
                font-size: 0.95rem;
                max-width: 320px;
                color: #cbd5e1;
            }

            .footer-title {
                color: #fff;
                font-size: 1rem;
                font-weight: 700;
                margin-bottom: 30px;
                text-transform: uppercase;
                letter-spacing: 2px;
            }

            .footer-links {
                list-style: none;
            }

            .footer-links li {
                margin-bottom: 15px;
            }

            .footer-links li a {
                color: var(--footer-text);
                text-decoration: none;
                transition: all 0.3s ease;
                display: inline-block;
                font-size: 0.95rem;
            }

            .footer-links li a:hover {
                color: #fff;
                transform: translateX(8px);
            }

            .footer-subscribe p {
                font-size: 0.9rem;
                margin-bottom: 20px;
                color: #94a3b8;
            }

            .input-group {
                display: flex;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 12px;
                padding: 6px;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .input-group input {
                flex: 1;
                background: transparent;
                border: none;
                padding: 10px 15px;
                color: #fff;
                font-family: inherit;
                font-size: 0.9rem;
            }

            .input-group input:focus { outline: none; }

            .btn-join {
                background: var(--brand-blue);
                color: #fff;
                border: none;
                padding: 10px 20px;
                border-radius: 10px;
                font-weight: 700;
                cursor: pointer;
                transition: 0.3s;
            }

            .btn-join:hover {
                background: var(--brand-accent);
                transform: scale(1.05);
            }

            .footer-bottom {
                max-width: 1200px;
                margin: 80px auto 0;
                padding: 40px 20px 0;
                border-top: 1px solid rgba(255, 255, 255, 0.05);
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 0.85rem;
                color: #64748b;
            }

            .social-links {
                display: flex;
                gap: 15px;
            }

            .s-icon {
                width: 38px;
                height: 38px;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                text-decoration: none;
                transition: 0.3s;
                font-weight: 800;
                font-size: 0.75rem;
            }

            .s-icon:hover {
                background: var(--brand-blue);
                transform: translateY(-5px) rotate(8deg);
            }

            @media (max-width: 992px) {
                .footer-container { grid-template-columns: 1fr 1fr; gap: 60px; }
            }

            @media (max-width: 600px) {
                .footer-container { grid-template-columns: 1fr; text-align: center; }
                .footer-brand p { margin: 0 auto; }
                .footer-bottom { flex-direction: column; gap: 20px; }
            }
        </style>

        <div class="footer-container">
            <div class="footer-brand">
                <span class="brand-name">TokoBook</span>
                <p>Membangun jembatan literasi melalui kurasi buku pilihan yang menginspirasi perubahan dan pertumbuhan pikiran.</p>
            </div>

            <div class="footer-column">
                <h4 class="footer-title">Menu</h4>
                <ul class="footer-links">
                    <li><a href="<?= BASE_URL ?>index.php">Beranda</a></li>
                    <li><a href="<?= BASE_URL ?>user/browse-books.php">Katalog Buku</a></li>
                    <li><a href="<?= BASE_URL ?>about.php">Tentang Kami</a></li>
                    <li><a href="<?= BASE_URL ?>user/orders.php">Pesanan Saya</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4 class="footer-title">Bantuan</h4>
                <ul class="footer-links">
                    <li><a href="#">FAQ Pendana</a></li>
                    <li><a href="#">Syarat Penggunaan</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                    <li><a href="#">Hubungi Admin</a></li>
                </ul>
            </div>

            <div class="footer-column footer-subscribe">
                <h4 class="footer-title">Stay Updated</h4>
                <p>Dapatkan update promo dan buku best-seller mingguan langsung ke inbox Anda.</p>
                <div class="input-group">
                    <input type="email" placeholder="Email Anda...">
                    <button class="btn-join">Join</button>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date("Y") ?> TokoBook Indonesia. Hak Cipta Dilindungi.</p>
            <div class="social-links">
                <a href="#" class="s-icon">IG</a>
                <a href="#" class="s-icon">TW</a>
                <a href="#" class="s-icon">LN</a>
            </div>
        </div>
    </footer>
</body>
</html>