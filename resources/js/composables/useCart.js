import { reactive, computed, watch } from 'vue';

const STORAGE_KEY = 'toko_nusantara_cart';

/**
 * Muat data keranjang dari localStorage saat pertama kali file ini di-import.
 * Dibungkus try-catch karena localStorage bisa saja berisi data rusak/korup
 * (misal jika user pernah edit manual lewat DevTools).
 */
function loadFromStorage() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        return raw ? JSON.parse(raw) : [];
    } catch (error) {
        console.warn('Gagal membaca keranjang dari localStorage, mulai dari kosong.', error);
        return [];
    }
}

/**
 * PENTING: `state` dideklarasikan di LEVEL MODUL (di luar function useCart),
 * bukan di dalamnya. Ini yang membuatnya jadi SINGLETON — dibuat sekali saja
 * saat file ini pertama di-import, lalu dipakai bersama oleh semua komponen
 * yang memanggil useCart() di manapun.
 */
const state = reactive({
    items: loadFromStorage(), // [{ id, name, slug, price, image, stock, qty }]
});

// Setiap kali isi keranjang berubah, otomatis simpan ke localStorage.
watch(
    () => state.items,
    (items) => {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    },
    { deep: true }
);

export function useCart() {
    /**
     * Tambah produk ke keranjang. Jika produk sudah ada, tambahkan qty-nya
     * (bukan duplikat baris) — dibatasi tidak boleh melebihi stok produk.
     */
    function addItem(product, qty = 1) {
        const existing = state.items.find((item) => item.id === product.id);

        if (existing) {
            existing.qty = Math.min(existing.qty + qty, product.stock);
        } else {
            state.items.push({
                id: product.id,
                name: product.name,
                slug: product.slug,
                price: product.price,
                image: product.image,
                stock: product.stock,
                qty: Math.min(qty, product.stock),
            });
        }
    }

    function removeItem(productId) {
        const index = state.items.findIndex((item) => item.id === productId);
        if (index !== -1) {
            state.items.splice(index, 1);
        }
    }

    function updateQty(productId, qty) {
        const item = state.items.find((item) => item.id === productId);
        if (!item) return;

        if (qty < 1) qty = 1;
        if (qty > item.stock) qty = item.stock;

        item.qty = qty;
    }

    function clearCart() {
        state.items.splice(0, state.items.length);
    }

    const totalItems = computed(() =>
        state.items.reduce((sum, item) => sum + item.qty, 0)
    );

    const totalPrice = computed(() =>
        state.items.reduce((sum, item) => sum + item.price * item.qty, 0)
    );

    const formattedTotalPrice = computed(
        () => 'Rp ' + totalPrice.value.toLocaleString('id-ID')
    );

    return {
        items: computed(() => state.items),
        addItem,
        removeItem,
        updateQty,
        clearCart,
        totalItems,
        totalPrice,
        formattedTotalPrice,
    };
}