<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useCart } from '@/composables/useCart';

const page = usePage();
const { totalItems } = useCart();

const isScrolled = ref(false);
const isMobileNavOpen = ref(false);

function handleScroll() {
    isScrolled.value = window.scrollY > 8;
}

onMounted(() => window.addEventListener('scroll', handleScroll));
onUnmounted(() => window.removeEventListener('scroll', handleScroll));

function toggleMobileNav() {
    isMobileNavOpen.value = !isMobileNavOpen.value;
}
</script>

<template>
    <div>
        <!-- NAVBAR -->
        <nav class="site-nav" :class="{ 'is-scrolled': isScrolled }">
            <div class="nav-wrap">

            <Link href="/" class="nav-brand">
                    <div class="nav-brand-mark"><i class="bi bi-shop"></i></div>
                    <div class="nav-brand-name">E-Commerce RSON</div>
                </Link>

                <div class="nav-menu">
                    <Link href="/beranda" class="nav-item" :class="{ active: $page.url.startsWith('/beranda') }">Beranda</Link>
                    <Link href="/produk" class="nav-item" :class="{ active: $page.url.startsWith('/produk') }">Produk</Link>
                    <Link href="/tentang">Tentang</Link>
                    <Link href="/kontak">Kontak</Link>
                </div>

                <div class="nav-actions">
                    <Link href="/keranjang" class="nav-cart-icon">
                        <i class="bi bi-cart3"></i>
                        <span v-if="totalItems > 0" class="nav-cart-badge">{{ totalItems }}</span>
                    </Link>

                    <template v-if="page.props.auth.user">
                        <Link href="/pesanan-saya" class="btn-nav-login">
                            <i class="bi bi-bag-check"></i> Pesanan Saya
                        </Link>
                        <Link href="/logout" method="post" as="button" class="btn-nav-cta">
                            <i class="bi bi-box-arrow-right"></i> Keluar
                        </Link>
                    </template>
                    <template v-else>
                        <Link href="/login" class="btn-nav-login">Masuk</Link>
                        <Link href="/register" class="btn-nav-cta">
                            Daftar <i class="bi bi-arrow-right"></i>
                        </Link>
                    </template>
                </div>

                <button class="nav-hamburger" @click="toggleMobileNav">
                    <i class="bi" :class="isMobileNavOpen ? 'bi-x-lg' : 'bi-list'"></i>
                </button>

            </div>

            <!-- Mobile Nav -->
            <div v-if="isMobileNavOpen" class="nav-mobile is-open">
                <Link href="/"><i class="bi bi-house"></i> Beranda</Link>
                <Link href="/produk"><i class="bi bi-grid"></i> Produk</Link>
                <Link href="/tentang"><i class="bi bi-info-circle"></i> Tentang</Link>
                <Link href="/kontak"><i class="bi bi-telephone"></i> Kontak</Link>
                <Link href="/keranjang">
                    <i class="bi bi-cart3"></i> Keranjang
                    <span v-if="totalItems > 0" style="margin-left:auto;background:var(--primary);color:#fff;font-size:10px;padding:1px 7px;border-radius:20px;">{{ totalItems }}</span>
                </Link>
                <hr>
                <hr>
                <template v-if="page.props.auth.user">
                    <Link href="/pesanan-saya"><i class="bi bi-bag-check"></i> Pesanan Saya</Link>
                    <Link href="/logout" method="post" as="button"><i class="bi bi-box-arrow-right"></i> Keluar</Link>
                </template>
                <template v-else>
                    <Link href="/login"><i class="bi bi-box-arrow-in-right"></i> Masuk</Link>
                    <Link href="/register"><i class="bi bi-person-plus"></i> Daftar Sekarang</Link>
                </template>
            </div>
        </nav>

        <!-- KONTEN HALAMAN -->
        <slot />

        <!-- FOOTER -->
        <footer class="site-footer">
            <div class="wrap" style="max-width:1180px;margin:0 auto;padding:0 28px;">
                <div class="row g-5">

                    <div class="col-lg-4">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                            <div class="footer-brand-mark"><i class="bi bi-shop"></i></div>
                            <div style="color:var(--white);font-weight:800;font-size:14px;">E-Commerce RSON</div>
                        </div>
                        <p style="font-size:13px;line-height:1.75;margin-bottom:22px;max-width:300px;">
                            Menghadirkan kekayaan produk asli daerah — kopi, batik, kerajinan tangan — langsung dari pengrajin lokal ke rumah Anda.
                        </p>
                        <div class="footer-socials">
                            <a href="#"><i class="bi bi-facebook"></i></a>
                            <a href="#"><i class="bi bi-instagram"></i></a>
                            <a href="#"><i class="bi bi-whatsapp"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-2 col-6">
                        <div class="footer-col-title">Navigasi</div>
                        <ul class="footer-nav-list">
                            <li><Link href="/">Beranda</Link></li>
                            <li><Link href="/produk">Produk</Link></li>
                            <li><Link href="/tentang">Tentang Kami</Link></li>
                            <li><Link href="/kontak">Kontak</Link></li>
                        </ul>
                    </div>

                    <div class="col-lg-2 col-6">
                        <div class="footer-col-title">Akun</div>
                        <ul class="footer-nav-list">
                            <li><Link href="/login">Masuk</Link></li>
                            <li><Link href="/register">Daftar</Link></li>
                            <li><Link href="/pesanan-saya">Pesanan Saya</Link></li>
                        </ul>
                    </div>

                    <div class="col-lg-4">
                        <div class="footer-col-title">Kontak</div>
                        <div style="display:flex;gap:10px;margin-bottom:11px;font-size:13px;">
                            <i class="bi bi-geo-alt-fill" style="color:var(--primary-light);"></i>
                            <span>Jl. Nusantara Raya No. 88, Jakarta</span>
                        </div>
                        <div style="display:flex;gap:10px;margin-bottom:11px;font-size:13px;">
                            <i class="bi bi-whatsapp" style="color:var(--primary-light);"></i>
                            <span>0812-0000-0000</span>
                        </div>
                        <div style="display:flex;gap:10px;font-size:13px;">
                            <i class="bi bi-envelope-fill" style="color:var(--primary-light);"></i>
                            <span>https://lynk.id/rson.dev</span>
                        </div>
                    </div>

                </div>

                <div class="footer-bottom-bar">
                    <p>&copy; {{ new Date().getFullYear() }} E-Commerce RSON. Semua hak cipta dilindungi.</p>
                </div>
            </div>
        </footer>
    </div>
</template>