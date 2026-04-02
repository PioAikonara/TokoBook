<?php
session_start();
require_once "includes/config.php";
$page_title = "Tentang Kami";
include "includes/header.php";
?>

<style>
    :root {
        --brand-blue: #4834d4;
        --brand-dark: #130f40;
        --brand-accent: #686de0;
        --soft-bg: #f8f9fc;
        --white: #ffffff;
    }

    .about-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 80px 20px;
    }

    .editorial-layout {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 80px;
        align-items: center;
    }

    .headline-wrap {
        position: relative;
    }

    .label-accent {
        display: inline-block;
        color: var(--brand-blue);
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 3px;
        font-size: 0.8rem;
        margin-bottom: 20px;
    }

    .main-headline {
        font-size: clamp(3rem, 8vw, 5rem);
        line-height: 0.95;
        font-weight: 800;
        color: var(--brand-dark);
        letter-spacing: -2px;
        margin-bottom: 40px;
    }

    .main-headline span {
        color: var(--brand-blue);
    }

    .editorial-subtext {
        font-size: 1.25rem;
        line-height: 1.8;
        color: #535c68;
        margin-bottom: 50px;
        max-width: 600px;
    }

    .stats-tray {
        display: flex;
        gap: 60px;
    }

    .stat-item h3 {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--brand-blue);
        margin-bottom: 5px;
    }

    .stat-item p {
        font-size: 0.9rem;
        color: #999;
        font-weight: 600;
        text-transform: uppercase;
    }

    .visual-sculpture {
        position: relative;
    }

    .canvas-box {
        width: 100%;
        aspect-ratio: 1/1.2;
        background: var(--brand-dark);
        border-radius: 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 40px 100px rgba(19, 15, 64, 0.2);
    }

    .shape {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: 0.6;
    }

    .shape-1 {
        width: 300px;
        height: 300px;
        background: var(--brand-blue);
        top: -50px;
        right: -50px;
    }

    .shape-2 {
        width: 250px;
        height: 250px;
        background: var(--brand-accent);
        bottom: -30px;
        left: -30px;
    }

    .canvas-content {
        position: relative;
        z-index: 2;
        padding: 60px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }

    .canvas-title {
        color: #fff;
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.1;
    }

    .manifesto-section {
        margin-top: 150px;
        padding: 100px;
        background: var(--white);
        border-radius: 60px;
        box-shadow: 0 20px 80px rgba(0,0,0,0.03);
        text-align: center;
    }

    .manifesto-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--brand-blue);
        margin-bottom: 30px;
    }

    .manifesto-text {
        font-size: 2rem;
        font-weight: 700;
        color: var(--brand-dark);
        line-height: 1.4;
        max-width: 900px;
        margin: 0 auto;
    }

    @media (max-width: 992px) {
        .editorial-layout { grid-template-columns: 1fr; gap: 60px; text-align: center; }
        .headline-wrap { order: 2; }
        .visual-sculpture { order: 1; }
        .editorial-subtext { margin: 0 auto 50px; }
        .stats-tray { justify-content: center; }
        .manifesto-section { padding: 60px 30px; }
        .manifesto-text { font-size: 1.5rem; }
    }
</style>

<div class="about-wrapper">
    <div class="editorial-layout">
        <div class="headline-wrap">
            <span class="label-accent">Napas Literasi Kami</span>
            <h1 class="main-headline">Lebih dari sekadar <span>tumpukan kertas.</span></h1>
            <p class="editorial-subtext">Kami percaya bahwa setiap buku memiliki takdirnya sendiri untuk mengubah hidup seseorang. Di TokoBook, kami menghubungkan pemikiran besar dengan para pencari inspirasi melalui kurasi yang penuh cinta.</p>
            
            <div class="stats-tray">
                <div class="stat-item">
                    <h3>12k+</h3>
                    <p>Pembaca Setia</p>
                </div>
                <div class="stat-item">
                    <h3>450+</h3>
                    <p>Penulis Lokal</p>
                </div>
                <div class="stat-item">
                    <h3>100%</h3>
                    <p>Cinta Literasi</p>
                </div>
            </div>
        </div>

        <div class="visual-sculpture">
            <div class="canvas-box">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="canvas-content">
                    <h2 class="canvas-title">Membangun Jembatan<br>Imajinasi Dunia.</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="manifesto-section">
        <p class="manifesto-title">MANIFESTO KAMI</p>
        <h3 class="manifesto-text">"Kami tidak hanya menjual buku, kami menyediakan 'kendaraan' bagi pikiran Anda untuk berkeliling dunia tanpa harus berpindah satu langkah pun."</h3>
    </div>
</div>

<?php include "includes/footer.php"; ?>