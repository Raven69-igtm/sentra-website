<style>
/* ============================================================
   MODAL RESET PASSWORD
   Warna mengikuti token tema panel instansi (--i-primary):
   hijau untuk Admin PPA, oranye untuk Admin Dinsos.
   ============================================================ */
.reset-pwd-modal {
    --rp-accent: var(--i-primary, #006039);
    --rp-accent-hover: var(--i-primary-hover, #004d2e);
    --rp-accent-soft: var(--i-primary-soft, #e8f7ee);
    --rp-accent-ring: var(--i-primary-ring, rgba(0, 96, 57, 0.12));

    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    overflow-y: auto;
    padding: 24px 16px;
    font-family: "Plus Jakarta Sans", "Poppins", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

/* Layout Dinsos lama (dinsos_layout.blade.php) memakai Poppins + aksen oranye. */
body.dinsos-body .reset-pwd-modal {
    --rp-accent: #e07a2e;
    --rp-accent-hover: #c9661f;
    --rp-accent-soft: #fff3e8;
    --rp-accent-ring: rgba(224, 122, 46, 0.16);
    font-family: "Poppins", sans-serif;
}

.reset-pwd-modal.show {
    display: block;
}

.reset-pwd-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(17, 24, 39, 0.48);
    -webkit-backdrop-filter: blur(2px);
    backdrop-filter: blur(2px);
    z-index: 10000;
}

.reset-pwd-container {
    position: relative;
    z-index: 10001;
    width: 100%;
    max-width: 480px;
    margin: clamp(16px, 8vh, 72px) auto;
    padding: 28px;
    background-color: #fff;
    border: 1px solid rgba(16, 24, 40, 0.05);
    border-radius: 20px;
    box-shadow: 0 24px 60px rgba(16, 24, 40, 0.18);
    animation: resetPwdIn 0.18s ease-out;
}

@keyframes resetPwdIn {
    from {
        opacity: 0;
        transform: translateY(8px) scale(0.985);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@media (prefers-reduced-motion: reduce) {
    .reset-pwd-container {
        animation: none;
    }
}

@media (max-width: 576px) {
    .reset-pwd-modal {
        padding: 16px;
    }

    .reset-pwd-container {
        margin: 16px auto;
        padding: 22px;
        border-radius: 16px;
    }
}

/* Tombol tutup */
.reset-pwd-close {
    position: absolute;
    top: 16px;
    right: 16px;
    display: grid;
    place-items: center;
    width: 36px;
    height: 36px;
    padding: 0;
    border: 0;
    border-radius: 10px;
    background-color: transparent;
    color: #8a94a6;
    font-size: 22px;
    font-weight: 600;
    line-height: 1;
    cursor: pointer;
    transition: background-color 0.18s ease, color 0.18s ease;
}

.reset-pwd-close:hover,
.reset-pwd-close:focus-visible {
    background-color: var(--rp-accent-soft);
    color: var(--rp-accent);
    outline: none;
}

/* Header */
.reset-pwd-header {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding-right: 36px;
    margin-bottom: 18px;
    padding-bottom: 18px;
    border-bottom: 1px solid #edf0f4;
}

.reset-pwd-icon {
    flex: 0 0 auto;
    display: grid;
    place-items: center;
    width: 44px;
    height: 44px;
    border-radius: 14px;
    background-color: var(--rp-accent-soft);
    color: var(--rp-accent);
    font-size: 18px;
}

.reset-pwd-header h2 {
    margin: 0;
    color: #111827;
    font-size: 1.15rem;
    font-weight: 700;
    line-height: 1.35;
}

.reset-pwd-subtitle {
    margin: 4px 0 0;
    color: #64748b;
    font-size: 0.8rem;
    line-height: 1.5;
}

.reset-pwd-content {
    padding: 0;
}

/* Form */
.reset-pwd-group {
    margin-bottom: 16px;
}

.reset-pwd-group label {
    display: block;
    margin-bottom: 7px;
    color: #374151;
    font-size: 0.8rem;
    font-weight: 600;
}

.reset-pwd-group input {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    background-color: #fff;
    color: #111827;
    font-family: inherit;
    font-size: 0.875rem;
    transition: border-color 0.18s ease, box-shadow 0.18s ease;
}

.reset-pwd-group input:focus {
    border-color: var(--rp-accent);
    outline: none;
    box-shadow: 0 0 0 4px var(--rp-accent-ring);
}

.reset-pwd-group input.invalid {
    border-color: #dc2626;
    box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
}

.reset-pwd-group input.valid {
    border-color: var(--rp-accent);
}

.reset-pwd-error {
    display: block;
    margin-top: 6px;
    color: #dc2626;
    font-size: 0.78rem;
}

.reset-pwd-button {
    width: 100%;
    padding: 12px 20px;
    border: 0;
    border-radius: 12px;
    background-color: var(--rp-accent);
    color: #fff;
    font-family: inherit;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
}

.reset-pwd-button:hover {
    background-color: var(--rp-accent-hover);
    box-shadow: 0 8px 20px var(--rp-accent-ring);
}

.reset-pwd-button:active {
    transform: translateY(1px);
}

.reset-pwd-rules {
    margin-bottom: 20px;
    padding: 14px 16px;
    border: 0;
    border-left: 3px solid var(--rp-accent);
    border-radius: 12px;
    background-color: var(--rp-accent-soft);
    color: #475569;
    font-size: 0.82rem;
}

.reset-pwd-rules p {
    margin: 0 0 6px;
}

.reset-pwd-rules strong {
    color: #111827;
}

.reset-pwd-rules ul {
    margin: 0;
    padding-left: 18px;
}

.reset-pwd-rules li {
    margin-bottom: 3px;
}

.reset-pwd-rules li:last-child {
    margin-bottom: 0;
}

/* Notifikasi sukses */
.reset-pwd-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10010;
    opacity: 0;
    transform: translateY(-20px);
    transition: opacity 0.3s, transform 0.3s;
}

.reset-pwd-notification.show {
    opacity: 1;
    transform: translateY(0);
}

.reset-pwd-notification-content {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    border-radius: 12px;
    background-color: var(--i-primary, #006039);
    color: #fff;
    font-size: 0.85rem;
    font-weight: 500;
    box-shadow: 0 10px 30px rgba(16, 24, 40, 0.18);
}

body.dinsos-body .reset-pwd-notification-content {
    background-color: #e07a2e;
}

.reset-pwd-notification i {
    font-size: 18px;
}
</style>
