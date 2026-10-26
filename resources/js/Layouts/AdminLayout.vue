<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

defineProps({
    pageTitle: { type: String, default: 'Dashboard' },
    breadcrumb: { type: String, default: 'Dashboard' },
});

const page = usePage();
const isSidebarOpen = ref(false);
const showLogoutConfirm = ref(false);

function openSidebar() { isSidebarOpen.value = true; }
function closeSidebar() { isSidebarOpen.value = false; }

const userInitials = () => {
    const name = page.props.auth.user?.name ?? '';
    return name.substring(0, 2).toUpperCase();
};
</script>

<template>
    <div>
        <!-- OVERLAY (mobile) -->
        <div
            class="sidebar-overlay"
            :class="{ open: isSidebarOpen }"
            @click="closeSidebar"
        ></div>

        <!-- SIDEBAR -->
        <aside class="sidebar" :class="{ open: isSidebarOpen }">

            <Link href="/admin/dashboard" class="sidebar-logo">
                <div class="logo-mark"><i class="bi bi-shop"></i></div>
                <div class="logo-text">
                    E-Commerce System
                    <span>Admin Panel</span>
                </div>
            </Link>

            <nav class="sidebar-nav">

                <div class="nav-label">Main Menu</div>
                <Link href="/admin/dashboard" class="nav-item" :class="{ active: $page.url.startsWith('/admin/dashboard') }">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </Link>

                <div class="nav-label">Konten</div>
                <Link href="/admin/categories" class="nav-item" :class="{ active: $page.url.startsWith('/admin/categories') }">
                    <i class="bi bi-tags-fill"></i> Kategori
                </Link>
                <Link href="/admin/products" class="nav-item" :class="{ active: $page.url.startsWith('/admin/products') }">
                    <i class="bi bi-box-seam-fill"></i> Produk
                </Link>
                <Link href="/admin/banners" class="nav-item" :class="{ active: $page.url.startsWith('/admin/banners') }">
                    <i class="bi bi-image-fill"></i> Banner
                </Link>

                <div class="nav-label">Transaksi</div>
                <Link href="/admin/orders" class="nav-item" :class="{ active: $page.url.startsWith('/admin/orders') }">
                    <i class="bi bi-receipt"></i> Pesanan
                </Link>

            </nav>

            <div class="sidebar-bottom">
                <div class="user-card" style="cursor: pointer;" @click="showLogoutConfirm = true">
                    <div class="user-avatar">{{ userInitials() }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ page.props.auth.user?.name }}</div>
                        <div class="user-role">Administrator</div>
                    </div>
                    <i class="bi bi-box-arrow-right" style="font-size:14px;color:var(--muted);"></i>
                </div>
            </div>

        </aside>

        <!-- MAIN CONTENT -->
        <div class="main-content">

            <header class="topbar">
                <div style="display:flex;align-items:center;gap:16px;">
                    <button class="icon-btn" style="display:none;" :class="{ 'd-flex': true }" @click="openSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <div class="topbar-title">{{ pageTitle }}</div>
                        <div class="breadcrumb-mini">
                            Admin <i class="bi bi-chevron-right" style="font-size:10px;"></i>
                            <span>{{ breadcrumb }}</span>
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:8px;">
                    <a href="/" target="_blank" class="icon-btn" title="Lihat Website">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
            </header>

            <main class="page-content">
                <slot />
            </main>

        </div>

        <!-- MODAL KONFIRMASI LOGOUT -->
        <Teleport to="body">
            <Transition name="modal-fade">
                <div v-if="showLogoutConfirm" class="app-modal-backdrop" @click.self="showLogoutConfirm = false">
                    <div class="app-modal-box" style="max-width: 360px;">
                        <div class="app-modal-body" style="text-align:center; padding: 28px 20px 8px;">
                            <i class="bi bi-box-arrow-right" style="font-size:32px;color:var(--primary);"></i>
                            <h3 style="margin-top:12px;font-size:16px;">Keluar dari sistem?</h3>
                            <p style="font-size:13px;color:var(--muted);margin-top:4px;">Anda akan keluar dari panel admin.</p>
                        </div>
                        <div class="app-modal-footer" style="justify-content:center;">
                            <button class="btn-secondary-custom" @click="showLogoutConfirm = false">Batal</button>
                            <Link href="/logout" method="post" as="button" class="btn-primary-custom">Ya, Keluar</Link>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
.app-modal-backdrop {
    position: fixed; inset: 0; background: rgba(18, 20, 36, 0.45);
    display: flex; align-items: center; justify-content: center;
    z-index: 1000; padding: 20px;
}
.app-modal-box {
    background: var(--white); border-radius: var(--radius);
    width: 100%; overflow: hidden;
}
.app-modal-footer { padding: 16px 20px; display: flex; gap: 8px; }
.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity .2s; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
@media (max-width: 991px) {
    .icon-btn.d-flex { display: flex !important; }
}
</style>