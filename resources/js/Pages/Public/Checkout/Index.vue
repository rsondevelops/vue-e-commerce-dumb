<script setup>
import { ref, computed } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import AppInput from '@/Components/AppInput.vue';
import { useCart } from '@/composables/useCart';

const props = defineProps({
    defaultCustomer: { type: Object, required: true },
});

const { items, totalPrice, formattedTotalPrice } = useCart();

function formatPrice(value) {
    return 'Rp ' + value.toLocaleString('id-ID');
}

const form = useForm({
    customer_name: props.defaultCustomer.name ?? '',
    customer_phone: props.defaultCustomer.phone ?? '',
    customer_address: props.defaultCustomer.address ?? '',
    payment_method: 'transfer',
    notes: '',
    items: [],
});

function submit() {
    // Susun ulang data items dari keranjang PERSIS sebelum submit,
    // supaya data terbaru (jika ada perubahan qty terakhir) ikut terkirim.
    form.items = items.value.map((item) => ({
        product_id: item.id,
        qty: item.qty,
    }));

    form.post('/checkout');
}

const isCartEmpty = computed(() => items.value.length === 0);
</script>

<template>
    <Head title="Checkout" />

    <PublicLayout>
        <div class="checkout-page">

            <h1>Checkout</h1>

            <!-- GUARD: KERANJANG KOSONG -->
            <div v-if="isCartEmpty" class="checkout-empty">
                <i class="bi bi-cart-x"></i>
                <h2>Keranjang Anda kosong</h2>
                <p>Tambahkan produk terlebih dahulu sebelum checkout.</p>
                <Link href="/produk" class="btn-primary-custom">Mulai Belanja</Link>
            </div>

            <form v-else @submit.prevent="submit" class="checkout-layout">

                <!-- FORM DATA PENGIRIMAN -->
                <div class="checkout-form-section">
                    <div class="card-box" style="padding: 20px; margin-bottom: 16px;">
                        <h3 class="checkout-section-title">Data Pengiriman</h3>

                        <div v-if="form.errors.items" class="checkout-error-banner">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            {{ form.errors.items }}
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            <AppInput
                                v-model="form.customer_name"
                                label="Nama Penerima"
                                placeholder="Nama lengkap"
                                :error="form.errors.customer_name"
                            />
                            <AppInput
                                v-model="form.customer_phone"
                                label="Nomor HP"
                                placeholder="0812xxxxxxxx"
                                :error="form.errors.customer_phone"
                            />
                            <div>
                                <label class="form-label-custom">Alamat Lengkap</label>
                                <textarea
                                    v-model="form.customer_address"
                                    rows="3"
                                    class="form-control-custom"
                                    :class="{ 'is-invalid': form.errors.customer_address }"
                                    placeholder="Jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota, kode pos"
                                ></textarea>
                                <div v-if="form.errors.customer_address" class="form-error">{{ form.errors.customer_address }}</div>
                            </div>
                            <div>
                                <label class="form-label-custom">Catatan (opsional)</label>
                                <textarea
                                    v-model="form.notes"
                                    rows="2"
                                    class="form-control-custom"
                                    placeholder="Contoh: titip di satpam jika tidak ada orang"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card-box" style="padding: 20px;">
                        <h3 class="checkout-section-title">Metode Pembayaran</h3>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <label class="payment-option" :class="{ selected: form.payment_method === 'transfer' }">
                                <input type="radio" value="transfer" v-model="form.payment_method" />
                                <i class="bi bi-bank"></i>
                                <div>
                                    <div class="payment-option-title">Transfer Manual</div>
                                    <div class="payment-option-desc">Transfer ke rekening kami, admin akan konfirmasi setelah pembayaran diterima.</div>
                                </div>
                            </label>
                            <label class="payment-option" :class="{ selected: form.payment_method === 'cod' }">
                                <input type="radio" value="cod" v-model="form.payment_method" />
                                <i class="bi bi-cash-coin"></i>
                                <div>
                                    <div class="payment-option-title">Bayar di Tempat (COD)</div>
                                    <div class="payment-option-desc">Bayar tunai langsung saat pesanan tiba di alamat Anda.</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- RINGKASAN -->
                <div class="checkout-summary">
                    <h3 class="checkout-section-title">Ringkasan Pesanan</h3>

                    <div class="checkout-summary-items">
                        <div v-for="item in items" :key="item.id" class="checkout-summary-item">
                            <span>{{ item.name }} <strong>×{{ item.qty }}</strong></span>
                            <span>{{ formatPrice(item.price * item.qty) }}</span>
                        </div>
                    </div>

                    <div class="checkout-summary-total">
                        <span>Total</span>
                        <strong>{{ formattedTotalPrice }}</strong>
                    </div>

                    <button type="submit" class="btn-primary-custom checkout-submit-btn" :disabled="form.processing">
                        <i class="bi bi-bag-check-fill"></i>
                        {{ form.processing ? 'Memproses...' : 'Buat Pesanan' }}
                    </button>
                </div>

            </form>
        </div>
    </PublicLayout>
</template>

<style scoped>
.checkout-page {
    max-width: 1000px;
    margin: 0 auto;
    padding: 32px 28px 64px;
}

.checkout-page h1 {
    font-size: 24px;
    margin-bottom: 24px;
}

.checkout-empty {
    text-align: center;
    padding: 64px 20px;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
}

.checkout-empty i {
    font-size: 48px;
    color: var(--muted);
    margin-bottom: 16px;
    display: block;
}

.checkout-empty h2 { font-size: 18px; margin-bottom: 6px; }
.checkout-empty p { color: var(--muted); font-size: 13px; margin-bottom: 20px; }

.checkout-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 24px;
    align-items: start;
}

.checkout-section-title {
    font-size: 14px;
    margin-bottom: 16px;
}

.checkout-error-banner {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--danger-bg);
    color: var(--danger);
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 13px;
    margin-bottom: 16px;
}

/* ================================================
   PAYMENT OPTION
================================================ */
.payment-option {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    cursor: pointer;
    transition: .15s;
}

.payment-option:hover {
    border-color: var(--primary-light);
}

.payment-option.selected {
    border-color: var(--primary);
    background: var(--primary-light);
}

.payment-option input {
    margin-top: 3px;
}

.payment-option i {
    font-size: 20px;
    color: var(--primary);
    margin-top: 2px;
}

.payment-option-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--dark);
}

.payment-option-desc {
    font-size: 12px;
    color: var(--muted);
    margin-top: 2px;
}

/* ================================================
   SUMMARY
================================================ */
.checkout-summary {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
    position: sticky;
    top: 80px;
}

.checkout-summary-items {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
}

.checkout-summary-item {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: var(--body-text);
}

.checkout-summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.checkout-summary-total strong {
    font-size: 18px;
    color: var(--primary);
}

.checkout-submit-btn {
    width: 100%;
    justify-content: center;
    padding: 14px;
}

/* ================================================
   RESPONSIVE
================================================ */
@media (max-width: 767px) {
    .checkout-layout {
        grid-template-columns: 1fr;
    }
}
</style>