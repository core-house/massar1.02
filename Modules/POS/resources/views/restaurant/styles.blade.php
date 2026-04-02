<style>
/* ============================================================
   RPOS — Restaurant POS Styles  (Redesigned)
   ============================================================ */

/* ── Fonts ── */
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap');

/* ── Design Tokens ── */
:root {
    --rpos-accent:      #22c55e;
    --rpos-accent-h:    #16a34a;
    --rpos-accent-soft: rgba(34,197,94,.12);
    --rpos-dark:        #0f172a;
    --rpos-dark-2:      #1e293b;
    --rpos-surface:     #ffffff;
    --rpos-bg:          #f1f5f9;
    --rpos-border:      #e2e8f0;
    --rpos-text:        #0f172a;
    --rpos-muted:       #64748b;
    --rpos-danger:      #ef4444;
    --rpos-warning:     #f59e0b;
    --rpos-info:        #3b82f6;
    --rpos-sidebar-w:   88px;
    --rpos-cart-w:      380px;
    --rpos-header-h:    58px;
    --rpos-tabs-h:      54px;
    --rpos-footer-h:    50px;
    --rpos-radius:      14px;
    --rpos-radius-sm:   8px;
    --rpos-shadow-sm:   0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --rpos-shadow:      0 4px 16px rgba(0,0,0,.08);
    --rpos-shadow-lg:   0 10px 40px rgba(0,0,0,.14);
    --rpos-transition:  .18s ease;
}

/* ── Reset ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body.rpos-body { margin: 0 !important; padding: 0 !important; height: 100%; }
body.rpos-body {
    font-family: 'Cairo', sans-serif;
    background: var(--rpos-bg);
    color: var(--rpos-text);
    direction: rtl;
}
body.rpos-body.modal-open { overflow: hidden !important; padding-right: 0 !important; }

/* ── Layout Shell ── */
.rpos-app-layout {
    display: flex;
    flex-direction: column;
    height: 100vh;
    width: 100vw;
    position: fixed;
    inset: 0;
}
.rpos-body-row {
    flex: 1;
    display: flex;
    overflow: hidden;
    align-items: stretch;
    min-height: 0;
}
.rpos-main-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-width: 0;
}

/* ── Header ── */
.rpos-header {
    height: var(--rpos-header-h);
    background: var(--rpos-dark);
    color: #fff;
    display: flex;
    align-items: center;
    padding: 0 1.1rem;
    gap: .85rem;
    flex-shrink: 0;
    z-index: 100;
    border-bottom: 1px solid rgba(255,255,255,.06);
}
.rpos-header__brand { display: flex; align-items: center; gap: .5rem; flex-shrink: 0; }
.rpos-header__logo {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, var(--rpos-accent), var(--rpos-accent-h));
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .8rem;
    box-shadow: 0 2px 8px rgba(34,197,94,.4);
}
.rpos-header__name { font-weight: 700; font-size: .95rem; white-space: nowrap; }

.rpos-header__search { flex: 1; max-width: 320px; position: relative; }
.rpos-header__search i {
    position: absolute; right: 11px; top: 50%;
    transform: translateY(-50%);
    color: #64748b; font-size: .8rem; pointer-events: none;
}
.rpos-header__search input {
    width: 100%;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 22px;
    padding: .42rem 2.1rem .42rem .9rem;
    color: #fff;
    font-family: 'Cairo', sans-serif;
    font-size: .82rem;
    outline: none;
    transition: border-color var(--rpos-transition), background var(--rpos-transition);
}
.rpos-header__search input::placeholder { color: #64748b; }
.rpos-header__search input:focus {
    border-color: var(--rpos-accent);
    background: rgba(255,255,255,.11);
}

.rpos-header__meta { display: flex; align-items: center; gap: .6rem; margin-right: auto; }

.rpos-table-badge {
    display: flex; align-items: center; gap: .4rem;
    font-size: .75rem; color: #cbd5e1;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.1);
    padding: .28rem .7rem;
    border-radius: 20px;
    cursor: pointer;
    transition: background var(--rpos-transition);
}
.rpos-table-badge:hover { background: rgba(255,255,255,.13); }
.rpos-table-badge__dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.rpos-table-badge__dot--free { background: #475569; }
.rpos-table-badge__dot--busy { background: var(--rpos-accent); box-shadow: 0 0 0 2px rgba(34,197,94,.3); }

.rpos-header__user { display: flex; flex-direction: column; align-items: flex-end; line-height: 1.35; }
.rpos-header__user strong { font-size: .82rem; color: #f1f5f9; }
.rpos-header__user small { font-size: .68rem; color: #64748b; }

.rpos-header__actions { display: flex; align-items: center; gap: .3rem; }

.rpos-online-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.rpos-online-dot--on  { background: var(--rpos-accent); box-shadow: 0 0 0 3px rgba(34,197,94,.25); }
.rpos-online-dot--off { background: var(--rpos-danger); }

.rpos-hdr-btn {
    position: relative;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: var(--rpos-radius-sm);
    color: #94a3b8;
    width: 34px; height: 34px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: .85rem;
    transition: background var(--rpos-transition), color var(--rpos-transition), border-color var(--rpos-transition);
    text-decoration: none;
}
.rpos-hdr-btn:hover { background: rgba(255,255,255,.15); color: #fff; border-color: rgba(255,255,255,.2); }
.rpos-hdr-btn--active { background: var(--rpos-accent) !important; color: #fff !important; border-color: var(--rpos-accent) !important; }
.rpos-hdr-btn--labeled {
    width: auto; padding: 0 .6rem;
    gap: .3rem; font-size: .72rem;
    font-family: 'Cairo', sans-serif;
}
.rpos-hdr-btn--labeled span:not(.rpos-badge) { font-size: .7rem; white-space: nowrap; }
.rpos-badge {
    position: absolute; top: -5px; right: -5px;
    background: var(--rpos-danger);
    color: #fff; border-radius: 10px;
    font-size: .58rem; padding: 1px 4px;
    min-width: 16px; text-align: center;
    border: 1.5px solid var(--rpos-dark);
}

/* ── Order Type Tabs ── */
.rpos-order-types {
    min-height: var(--rpos-tabs-h);
    background: var(--rpos-surface);
    border-bottom: 1px solid var(--rpos-border);
    display: flex;
    align-items: center;
    padding: 0 1rem;
    gap: .45rem;
    flex-shrink: 0;
    flex-wrap: wrap;
}
.rpos-order-type {
    padding: .4rem 1.1rem;
    border-radius: 22px;
    border: 1.5px solid var(--rpos-border);
    background: transparent;
    color: var(--rpos-muted);
    font-family: 'Cairo', sans-serif;
    font-size: .85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--rpos-transition);
    display: flex; align-items: center; gap: .35rem;
    white-space: nowrap;
}
.rpos-order-type:hover { border-color: var(--rpos-accent); color: var(--rpos-accent); background: var(--rpos-accent-soft); }
.rpos-order-type--active {
    background: var(--rpos-accent);
    border-color: var(--rpos-accent);
    color: #fff;
    box-shadow: 0 2px 8px rgba(34,197,94,.3);
}

/* ── Price Group Bar ── */
.rpos-price-group-bar {
    display: flex; align-items: center; gap: .4rem;
    margin-right: auto;
    background: #f8fafc;
    border: 1px solid var(--rpos-border);
    border-radius: 22px;
    padding: .25rem .65rem;
}
.rpos-price-group-bar__btns { display: flex; gap: .25rem; flex-wrap: wrap; }
.rpos-pg-btn {
    padding: .22rem .75rem;
    border-radius: 16px;
    border: 1.5px solid var(--rpos-border);
    background: #fff;
    font-family: 'Cairo', sans-serif;
    font-size: .75rem; font-weight: 600;
    cursor: pointer;
    transition: all var(--rpos-transition);
    color: var(--rpos-muted);
    white-space: nowrap;
}
.rpos-pg-btn:hover { border-color: var(--rpos-accent); color: var(--rpos-accent); }
.rpos-pg-btn--active { background: var(--rpos-accent); border-color: var(--rpos-accent); color: #fff; }

/* ── Main Area ── */
.rpos-main { flex: 1; display: flex; overflow: hidden; }

/* ── Categories Sidebar ── */
.rpos-categories {
    width: var(--rpos-sidebar-w);
    background: var(--rpos-surface);
    border-left: 1px solid var(--rpos-border);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: .5rem .3rem;
    flex-shrink: 0;
}
.rpos-cat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: .6rem .2rem;
    border: none;
    border-radius: 10px;
    background: transparent;
    color: var(--rpos-muted);
    font-family: 'Cairo', sans-serif;
    font-size: .65rem;
    cursor: pointer;
    transition: all var(--rpos-transition);
    text-align: center;
    line-height: 1.2;
}
.rpos-cat-item i { font-size: 1.15rem; }
.rpos-cat-item:hover { background: var(--rpos-accent-soft); color: var(--rpos-accent); }
.rpos-cat-item--active {
    background: var(--rpos-accent);
    color: #fff;
    box-shadow: 0 2px 8px rgba(34,197,94,.25);
}

/* ── Products Grid ── */
.rpos-products { flex: 1; overflow-y: auto; padding: .85rem; background: var(--rpos-bg); }
.rpos-products__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
    gap: .7rem;
}
.rpos-product-card {
    background: var(--rpos-surface);
    border-radius: var(--rpos-radius);
    overflow: hidden;
    cursor: pointer;
    transition: transform var(--rpos-transition), box-shadow var(--rpos-transition), border-color var(--rpos-transition);
    box-shadow: var(--rpos-shadow-sm);
    border: 2px solid transparent;
}
.rpos-product-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--rpos-shadow);
    border-color: var(--rpos-accent);
}
.rpos-product-card__icon {
    height: 110px;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    display: flex; align-items: center; justify-content: center;
    position: relative;
    font-size: 2.4rem;
    color: var(--rpos-accent);
    overflow: hidden;
}
.rpos-product-card__scale-badge {
    position: absolute; top: 6px; right: 6px;
    background: var(--rpos-warning); color: #fff;
    border-radius: 6px; padding: 2px 5px; font-size: .58rem; font-weight: 700;
}
.rpos-product-card__body { padding: .55rem .7rem .7rem; }
.rpos-product-card__name { font-size: .8rem; font-weight: 700; color: var(--rpos-text); margin-bottom: 2px; line-height: 1.3; }
.rpos-product-card__desc { font-size: .68rem; color: var(--rpos-muted); margin-bottom: .45rem; line-height: 1.3; }
.rpos-product-card__footer { display: flex; align-items: center; justify-content: space-between; }
.rpos-product-card__price { font-size: .88rem; font-weight: 800; color: var(--rpos-accent); }
.rpos-product-card__add {
    width: 26px; height: 26px;
    border-radius: 50%;
    border: none;
    background: var(--rpos-accent);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: .72rem;
    cursor: pointer;
    transition: background var(--rpos-transition), transform .1s;
    box-shadow: 0 2px 6px rgba(34,197,94,.35);
}
.rpos-product-card__add:hover { background: var(--rpos-accent-h); transform: scale(1.12); }

/* ── Cart Sidebar ── */
.rpos-cart {
    width: var(--rpos-cart-w);
    background: var(--rpos-surface);
    border-right: 1px solid var(--rpos-border);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    overflow: hidden;
    max-height: 100%;
    box-shadow: -2px 0 12px rgba(0,0,0,.04);
}

/* Cart Header */
.rpos-cart__header {
    padding: .7rem 1rem;
    border-bottom: 1px solid var(--rpos-border);
    display: flex; align-items: center; gap: .5rem;
    background: #f8fafc;
}
.rpos-cart__title {
    font-size: .68rem; font-weight: 700;
    color: var(--rpos-muted);
    text-transform: uppercase; letter-spacing: .6px;
}
.rpos-cart__order-num { font-size: .92rem; font-weight: 800; color: var(--rpos-text); flex: 1; }
.rpos-cart__table-tag {
    font-size: .68rem;
    background: #dbeafe; color: #1d4ed8;
    padding: 2px 8px; border-radius: 10px;
    font-weight: 700; display: none;
}
.rpos-cart__void-icon {
    background: none; border: none;
    color: #cbd5e1; cursor: pointer;
    font-size: .85rem; padding: 4px 5px;
    border-radius: 6px;
    transition: color var(--rpos-transition), background var(--rpos-transition);
}
.rpos-cart__void-icon:hover { color: var(--rpos-danger); background: #fee2e2; }

/* Cart Items */
.rpos-cart__items { flex: 1; overflow-y: auto; padding: .5rem .6rem; }
.rpos-cart__empty {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    height: 100%; padding: 2rem;
    color: var(--rpos-muted); text-align: center; gap: .5rem;
}
.rpos-cart__empty i { font-size: 2.2rem; opacity: .25; }
.rpos-cart__empty p { font-size: .82rem; margin: 0; font-weight: 600; }
.rpos-cart__empty small { font-size: .72rem; }

.rpos-cart-item {
    border-radius: 10px;
    margin-bottom: 5px;
    background: #f8fafc;
    border: 1px solid var(--rpos-border);
    overflow: hidden;
    transition: box-shadow var(--rpos-transition);
}
.rpos-cart-item:hover { box-shadow: var(--rpos-shadow-sm); }
.rpos-cart-item__main { display: flex; align-items: center; gap: .45rem; padding: .45rem .5rem; }
.rpos-cart-item__icon {
    width: 32px; height: 32px;
    background: var(--rpos-accent-soft);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; color: var(--rpos-accent); flex-shrink: 0;
}
.rpos-cart-item__info { flex: 1; min-width: 0; }
.rpos-cart-item__name { font-size: .78rem; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.rpos-cart-item__price { font-size: .72rem; color: var(--rpos-accent); font-weight: 700; }
.rpos-cart-item__qty { display: flex; align-items: center; gap: 3px; }
.rpos-cart-item__qty-btn {
    width: 22px; height: 22px;
    border-radius: 50%;
    border: 1px solid var(--rpos-border);
    background: #fff;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .65rem; color: var(--rpos-text);
    transition: all .12s;
}
.rpos-cart-item__qty-btn:hover { background: var(--rpos-accent); color: #fff; border-color: var(--rpos-accent); }
.rpos-cart-item__qty-val { font-size: 1.1rem; font-weight: 800; min-width: 28px; text-align: center; }
.rpos-cart-item__note-btn {
    background: none; border: none;
    color: #cbd5e1; cursor: pointer;
    font-size: .72rem; padding: 2px 4px;
    border-radius: 4px;
    transition: color var(--rpos-transition);
}
.rpos-cart-item__note-btn:hover { color: var(--rpos-accent); }
.rpos-cart-item__remove-btn {
    background: none; border: none;
    color: #cbd5e1; cursor: pointer;
    font-size: .78rem; padding: 2px 5px;
    border-radius: 4px;
    transition: color var(--rpos-transition), background var(--rpos-transition);
    flex-shrink: 0;
}
.rpos-cart-item__remove-btn:hover { color: var(--rpos-danger); background: #fee2e2; }
.rpos-cart-item__note-row { padding: 0 .5rem .45rem; display: none; }
.rpos-cart-item__note-row.visible { display: block; }
.rpos-cart-item__note-input {
    width: 100%;
    border: 1px solid var(--rpos-border);
    border-radius: 6px;
    padding: 4px 8px;
    font-size: .7rem;
    font-family: 'Cairo', sans-serif;
    color: var(--rpos-text);
    background: #fff;
    outline: none; resize: none;
}
.rpos-cart-item__note-input:focus { border-color: var(--rpos-accent); }

/* Cart Totals */
.rpos-cart__totals {
    padding: .65rem 1rem;
    border-top: 1px solid var(--rpos-border);
    display: flex; flex-direction: column; gap: 4px;
    background: #f8fafc;
}
.rpos-cart__total-row { display: flex; justify-content: space-between; font-size: .8rem; color: var(--rpos-muted); }
.rpos-cart__total-row--total {
    font-size: .95rem; font-weight: 800; color: var(--rpos-text);
    margin-top: 5px; padding-top: 5px;
    border-top: 1.5px dashed var(--rpos-border);
}
.rpos-cart__total-amount { color: var(--rpos-accent); font-size: 1.05rem; }

/* Cart Action Buttons */
.rpos-cart__actions { display: flex; gap: 4px; padding: .45rem .65rem; border-top: 1px solid var(--rpos-border); }
.rpos-cart__action-btn {
    flex: 1; padding: .42rem 0;
    border-radius: var(--rpos-radius-sm);
    border: 1px solid var(--rpos-border);
    background: #fff;
    font-family: 'Cairo', sans-serif;
    font-size: .7rem; font-weight: 700;
    cursor: pointer;
    transition: all var(--rpos-transition);
    color: var(--rpos-text);
    display: flex; align-items: center; justify-content: center; gap: 3px;
}
.rpos-cart__action-btn:hover { background: #f1f5f9; }
.rpos-cart__action-btn--void { color: var(--rpos-danger); border-color: #fecaca; }
.rpos-cart__action-btn--void:hover { background: #fff1f2; }

/* ── Print Toggles (below cart actions) ── */
.rpos-cart__print-opts {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: .4rem .75rem;
    border-top: 1px solid var(--rpos-border);
    background: #f8fafc;
}
.rpos-cart__print-opts .form-check-label {
    font-family: 'Cairo', sans-serif;
    font-size: .72rem;
    font-weight: 700;
    color: var(--rpos-muted);
    cursor: pointer;
    user-select: none;
}
.rpos-cart__print-opts .form-check-input { cursor: pointer; }
.rpos-cart__print-opts .form-check-input:checked {
    background-color: var(--rpos-accent);
    border-color: var(--rpos-accent);
}

/* Checkout Button */
.rpos-checkout-btn {
    margin: .5rem .65rem .65rem;
    padding: .82rem;
    background: linear-gradient(135deg, var(--rpos-accent) 0%, var(--rpos-accent-h) 100%);
    color: #fff;
    border: none;
    border-radius: var(--rpos-radius);
    font-family: 'Cairo', sans-serif;
    font-size: .95rem; font-weight: 800;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    transition: opacity var(--rpos-transition), transform .1s;
    box-shadow: 0 4px 14px rgba(34,197,94,.35);
    letter-spacing: .3px;
}
.rpos-checkout-btn:hover { opacity: .92; }
.rpos-checkout-btn:active { transform: scale(.98); }
.rpos-checkout-btn:disabled { background: #e2e8f0; color: #94a3b8; cursor: not-allowed; box-shadow: none; }

/* ── Delivery Panel ── */
.rpos-delivery-panel { border-top: 1px solid var(--rpos-border); }
.rpos-delivery-panel__inner { background: #fffbeb; border-color: #fde68a !important; }
.rpos-delivery-search-dropdown {
    position: absolute; top: 100%; right: 0; left: 0;
    z-index: 100;
    background: #fff;
    border: 1px solid var(--rpos-border);
    border-radius: var(--rpos-radius-sm);
    box-shadow: var(--rpos-shadow);
    max-height: 160px; overflow-y: auto;
}
.rpos-delivery-result-item {
    padding: .45rem .7rem;
    cursor: pointer; font-size: .8rem;
    border-bottom: 1px solid #f1f5f9;
    transition: background .12s;
}
.rpos-delivery-result-item:last-child { border-bottom: none; }
.rpos-delivery-result-item:hover { background: #f8fafc; }

/* ── Footer ── */
.rpos-footer {
    height: var(--rpos-footer-h);
    background: var(--rpos-dark);
    color: #475569;
    display: flex; align-items: center;
    padding: 0 1.1rem; gap: 1rem;
    font-size: .7rem; flex-shrink: 0;
    border-top: 1px solid rgba(255,255,255,.05);
}
.rpos-footer__status { display: flex; align-items: center; gap: .3rem; white-space: nowrap; }
.rpos-footer__version { white-space: nowrap; color: #334155; }
.rpos-footer__dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; }
.rpos-footer__dot--on  { background: var(--rpos-accent); }
.rpos-footer__dot--off { background: var(--rpos-danger); }
.rpos-footer__links { margin-right: auto; display: flex; gap: .15rem; }
.rpos-footer__links a {
    color: #475569; text-decoration: none;
    transition: color var(--rpos-transition), background var(--rpos-transition);
    display: flex; flex-direction: column; align-items: center; gap: 2px;
    padding: .28rem .6rem;
    border-radius: var(--rpos-radius-sm);
    font-size: .6rem; line-height: 1.2;
}
.rpos-footer__links a i { font-size: .95rem; }
.rpos-footer__links a:hover { color: #fff; background: rgba(255,255,255,.09); }

/* ── Table Selection Modal ── */
.rpos-modal-table-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(108px, 1fr));
    gap: .55rem;
    max-height: 340px; overflow-y: auto;
    padding: .2rem;
}
.rpos-table-card {
    border-radius: 10px;
    border: 2px solid var(--rpos-border);
    padding: .6rem .45rem;
    cursor: pointer;
    transition: all var(--rpos-transition);
    text-align: center; position: relative;
    background: #fff;
}
.rpos-table-card:hover { transform: translateY(-2px); box-shadow: var(--rpos-shadow); }
.rpos-table-card--free     { border-color: #86efac; background: #f0fdf4; }
.rpos-table-card--occupied { border-color: #fca5a5; background: #fff1f2; opacity: .7; cursor: not-allowed; }
.rpos-table-card--reserved { border-color: #fcd34d; background: #fffbeb; }
.rpos-table-card--selected { border-color: var(--rpos-accent) !important; background: #f0fdf4 !important; box-shadow: 0 0 0 3px rgba(34,197,94,.2); }
.rpos-table-card__icon { font-size: 1.4rem; margin-bottom: 4px; }
.rpos-table-card--free     .rpos-table-card__icon { color: #16a34a; }
.rpos-table-card--occupied .rpos-table-card__icon { color: var(--rpos-danger); }
.rpos-table-card--reserved .rpos-table-card__icon { color: var(--rpos-warning); }
.rpos-table-card--selected .rpos-table-card__icon { color: var(--rpos-accent); }
.rpos-table-card__name { font-size: .76rem; font-weight: 700; color: var(--rpos-text); }
.rpos-table-card__cap { font-size: .62rem; color: var(--rpos-muted); margin-top: 2px; }
.rpos-table-card__status-dot { position: absolute; top: 5px; left: 5px; width: 7px; height: 7px; border-radius: 50%; }
.rpos-table-card--free     .rpos-table-card__status-dot { background: #16a34a; }
.rpos-table-card--occupied .rpos-table-card__status-dot { background: var(--rpos-danger); }
.rpos-table-card--reserved .rpos-table-card__status-dot { background: var(--rpos-warning); }

.rpos-modal-legend { display: flex; gap: .85rem; margin-bottom: .65rem; }
.rpos-legend-item { display: flex; align-items: center; gap: 5px; font-size: .72rem; color: var(--rpos-muted); }
.rpos-legend-dot { width: 9px; height: 9px; border-radius: 3px; }
.rpos-legend-dot--free     { background: #16a34a; }
.rpos-legend-dot--occupied { background: var(--rpos-danger); }
.rpos-legend-dot--reserved { background: var(--rpos-warning); }

/* ── Customer Search ── */
.rpos-customer-search-result {
    border: 1px solid var(--rpos-border);
    border-radius: var(--rpos-radius-sm);
    padding: .55rem .8rem;
    cursor: pointer;
    transition: background .12s;
    font-size: .85rem;
}
.rpos-customer-search-result:hover { background: #f8fafc; }
.rpos-customer-search-result .name { font-weight: 700; }
.rpos-customer-search-result .phone { font-size: .72rem; color: var(--rpos-muted); }

/* ── Quick Amounts (Payment) ── */
.rpos-quick-amounts { display: flex; gap: .4rem; flex-wrap: wrap; margin-top: .45rem; }
.rpos-quick-amt {
    padding: .3rem .75rem;
    border-radius: 20px;
    border: 1.5px solid var(--rpos-border);
    background: #fff;
    font-family: 'Cairo', sans-serif;
    font-size: .82rem; font-weight: 700;
    cursor: pointer;
    transition: all var(--rpos-transition);
    color: var(--rpos-text);
}
.rpos-quick-amt:hover { border-color: var(--rpos-accent); color: var(--rpos-accent); background: var(--rpos-accent-soft); }
.rpos-quick-amt--exact { border-color: #86efac; color: #16a34a; }
.rpos-quick-amt--exact:hover { background: #f0fdf4; }

/* ── Scrollbars ── */
.rpos-products::-webkit-scrollbar,
.rpos-cart__items::-webkit-scrollbar,
.rpos-categories::-webkit-scrollbar,
.rpos-modal-table-grid::-webkit-scrollbar { width: 4px; }
.rpos-products::-webkit-scrollbar-thumb,
.rpos-cart__items::-webkit-scrollbar-thumb,
.rpos-categories::-webkit-scrollbar-thumb,
.rpos-modal-table-grid::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.rpos-products::-webkit-scrollbar-track,
.rpos-cart__items::-webkit-scrollbar-track,
.rpos-categories::-webkit-scrollbar-track { background: transparent; }

/* ── Bootstrap Modal Fix (fixed layout body) ── */
.modal-backdrop {
    position: fixed !important;
    inset: 0;
    width: 100vw; height: 100vh;
    z-index: 1050;
}
.modal {
    position: fixed !important;
    z-index: 1055;
    inset: 0;
    width: 100%; height: 100%;
    overflow-x: hidden; overflow-y: auto;
    outline: 0;
}
</style>
