<style>
@font-face {
    font-family: 'Cairo';
    font-style: normal;
    font-weight: 300 700;
    font-display: swap;
    src: local('Cairo'), local('Cairo-Regular');
}
:root {
    --rpos-accent: #25b900ff;
    --rpos-accent-h: #0d8a1eff;
    --rpos-dark: #1a1a2e;
    --rpos-surface: #ffffff;
    --rpos-bg: #f4f5f7;
    --rpos-border: #e5e7eb;
    --rpos-text: #1f2937;
    --rpos-muted: #6b7280;
    --rpos-sidebar-w: 90px;
    --rpos-cart-w: 400px;
    --rpos-header-h: 56px;
    --rpos-tabs-h: 56px;
    --rpos-footer-h: 52px;
    --rpos-radius: 12px;
}
* , *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body.rpos-body {
    margin: 0 !important;
    padding: 0 !important;
    height: 100%;
}
body.rpos-body {
    font-family: 'Cairo', sans-serif;
    background: var(--rpos-bg);
    color: var(--rpos-text);
    direction: rtl;
    /* No overflow:hidden here — Bootstrap modals need body to be scrollable */
}

.rpos-app-layout {
    display: flex;
    flex-direction: column;
    height: 100vh;
    width: 100vw;
    max-width: 100%;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.rpos-body-row {
    flex: 1;
    display: flex;
    flex-direction: row;
    overflow: hidden;
    align-items: stretch;
    min-height: 0;
}
/* Allow Bootstrap modals to work with overflow:hidden body */
body.rpos-body.modal-open {
    overflow: hidden !important;
    padding-right: 0 !important;
}
.modal-backdrop { z-index: 1040; }
.modal { z-index: 1050; }

.rpos-main-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-width: 0;
}

.rpos-header { height: var(--rpos-header-h); background: var(--rpos-dark); color: #fff; display: flex; align-items: center; padding: 0 1rem; gap: 1rem; flex-shrink: 0; z-index: 100; }
.rpos-header__brand { display: flex; align-items: center; gap: .5rem; flex-shrink: 0; }
.rpos-header__logo { width: 34px; height: 34px; background: var(--rpos-accent); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .85rem; }
.rpos-header__name { font-weight: 700; font-size: 1rem; white-space: nowrap; }
.rpos-header__search { flex: 1; max-width: 340px; position: relative; }
.rpos-header__search i { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: .85rem; }
.rpos-header__search input { width: 100%; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15); border-radius: 20px; padding: .4rem 2.2rem .4rem 1rem; color: #fff; font-family: 'Cairo', sans-serif; font-size: .85rem; outline: none; }
.rpos-header__search input::placeholder { color: #9ca3af; }
.rpos-header__search input:focus { border-color: var(--rpos-accent); }
.rpos-header__meta { display: flex; align-items: center; gap: .75rem; margin-right: auto; }
.rpos-table-badge { display: flex; align-items: center; gap: .4rem; font-size: .8rem; color: #d1d5db; background: rgba(255,255,255,.08); padding: .3rem .75rem; border-radius: 20px; cursor: pointer; transition: background .2s; }
.rpos-table-badge:hover { background: rgba(255,255,255,.15); }
.rpos-table-badge__dot { width: 8px; height: 8px; border-radius: 50%; }
.rpos-table-badge__dot--free { background: #6b7280; }
.rpos-table-badge__dot--busy { background: #10b981; }
.rpos-header__user { display: flex; flex-direction: column; align-items: flex-end; line-height: 1.3; }
.rpos-header__user strong { font-size: .85rem; }
.rpos-header__user small { font-size: .7rem; color: #9ca3af; }
.rpos-header__actions { display: flex; align-items: center; gap: .4rem; }
.rpos-online-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.rpos-online-dot--on { background: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,.25); }
.rpos-online-dot--off { background: #ef4444; }
.rpos-hdr-btn { position: relative; background: rgba(255,255,255,.08); border: none; border-radius: 8px; color: #d1d5db; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: .9rem; transition: background .2s, color .2s; text-decoration: none; }
.rpos-hdr-btn:hover { background: rgba(255,255,255,.18); color: #fff; }
.rpos-hdr-btn--active { background: var(--rpos-accent) !important; color: #fff !important; }
.rpos-hdr-btn--labeled { width: auto; padding: 0 .65rem; gap: .35rem; font-size: .75rem; font-family: 'Cairo', sans-serif; }
.rpos-hdr-btn--labeled span:not(.rpos-badge) { font-size: .72rem; white-space: nowrap; }
.rpos-badge { position: absolute; top: -4px; right: -4px; background: var(--rpos-accent); color: #fff; border-radius: 10px; font-size: .6rem; padding: 1px 4px; min-width: 16px; text-align: center; }

.rpos-order-types {
    min-height: var(--rpos-tabs-h);
    background: var(--rpos-surface);
    border-bottom: 1px solid var(--rpos-border);
    display: flex;
    align-items: center;
    padding: 0 1rem;
    gap: .5rem;
    flex-shrink: 0;
    flex-wrap: wrap;
}
.rpos-order-type { padding: .45rem 1.25rem; border-radius: 20px; border: 2px solid var(--rpos-border); background: transparent; color: var(--rpos-muted); font-family: 'Cairo', sans-serif; font-size: .9rem; font-weight: 600; cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: .4rem; white-space: nowrap; }
.rpos-order-type:hover { border-color: var(--rpos-accent); color: var(--rpos-accent); }
.rpos-order-type--active { background: var(--rpos-accent); border-color: var(--rpos-accent); color: #fff; }

.rpos-price-group-bar {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-right: auto;
    background: #f9fafb;
    border: 1px solid var(--rpos-border);
    border-radius: 24px;
    padding: .3rem .75rem;
}
.rpos-price-group-bar__label {
    font-size: .78rem;
    font-weight: 700;
    color: var(--rpos-muted);
    white-space: nowrap;
}
.rpos-price-group-bar__btns {
    display: flex;
    gap: .3rem;
    flex-wrap: wrap;
}
.rpos-pg-btn {
    padding: .25rem .85rem;
    border-radius: 16px;
    border: 1.5px solid var(--rpos-border);
    background: #fff;
    font-family: 'Cairo', sans-serif;
    font-size: .78rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
    color: var(--rpos-muted);
    white-space: nowrap;
}
.rpos-pg-btn:hover { border-color: var(--rpos-accent); color: var(--rpos-accent); }
.rpos-pg-btn--active { background: var(--rpos-accent); border-color: var(--rpos-accent); color: #fff; }

.rpos-main { flex: 1; display: flex; overflow: hidden; }

.rpos-categories {
    width: var(--rpos-sidebar-w);
    background: var(--rpos-surface);
    border-left: 1px solid var(--rpos-border);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 3px;
    padding: .5rem .3rem;
    flex-shrink: 0;
}
.rpos-cat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: .65rem .25rem;
    border: none;
    border-radius: 10px;
    background: transparent;
    color: var(--rpos-muted);
    font-family: 'Cairo', sans-serif;
    font-size: .68rem;
    cursor: pointer;
    transition: all .2s;
    text-align: center;
    line-height: 1.2;
}
.rpos-cat-item i { font-size: 1.2rem; }
.rpos-cat-item:hover { background: #f3f4f6; color: var(--rpos-accent); }
.rpos-cat-item--active { background: var(--rpos-accent); color: #fff; }

/* PRODUCTS */
.rpos-products { flex: 1; overflow-y: auto; padding: 1rem; background: var(--rpos-bg); }
.rpos-products__grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: .75rem; }
.rpos-product-card { background: var(--rpos-surface); border-radius: var(--rpos-radius); overflow: hidden; cursor: pointer; transition: transform .15s, box-shadow .15s; box-shadow: 0 1px 3px rgba(0,0,0,.06); border: 2px solid transparent; }
.rpos-product-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); border-color: var(--rpos-accent); }
.rpos-product-card__icon { height: 120px; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); display: flex; align-items: center; justify-content: center; position: relative; font-size: 2.5rem; color: var(--rpos-accent); overflow: hidden; border-radius: var(--rpos-radius) var(--rpos-radius) 0 0; }
.rpos-product-card__scale-badge { position: absolute; top: 6px; right: 6px; background: #f59e0b; color: #fff; border-radius: 6px; padding: 2px 5px; font-size: .6rem; }
.rpos-product-card__body { padding: .6rem .75rem .75rem; }
.rpos-product-card__name { font-size: .82rem; font-weight: 600; color: var(--rpos-text); margin-bottom: 2px; line-height: 1.3; }
.rpos-product-card__desc { font-size: .7rem; color: var(--rpos-muted); margin-bottom: .5rem; line-height: 1.3; }
.rpos-product-card__footer { display: flex; align-items: center; justify-content: space-between; }
.rpos-product-card__price { font-size: .9rem; font-weight: 700; color: var(--rpos-accent); }
.rpos-product-card__add { width: 26px; height: 26px; border-radius: 50%; border: none; background: var(--rpos-accent); color: #fff; display: flex; align-items: center; justify-content: center; font-size: .75rem; cursor: pointer; transition: background .2s, transform .1s; }
.rpos-product-card__add:hover { background: var(--rpos-accent-h); transform: scale(1.1); }

/* CART */
.rpos-cart { width: var(--rpos-cart-w); background: var(--rpos-surface); border-right: 1px solid var(--rpos-border); display: flex; flex-direction: column; flex-shrink: 0; overflow: hidden; max-height: 100%; }
.rpos-cart__header { padding: .75rem 1rem; border-bottom: 1px solid var(--rpos-border); display: flex; align-items: center; gap: .5rem; background: #f9fafb; }
.rpos-cart__title { font-size: .72rem; font-weight: 600; color: var(--rpos-muted); text-transform: uppercase; letter-spacing: .5px; }
.rpos-cart__order-num { font-size: .95rem; font-weight: 700; color: var(--rpos-text); flex: 1; }
.rpos-cart__table-tag { font-size: .7rem; background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 10px; font-weight: 600; display: none; }
.rpos-cart__void-icon { background: none; border: none; color: #ef4444; cursor: pointer; font-size: .9rem; padding: 4px; border-radius: 6px; transition: background .2s; }
.rpos-cart__void-icon:hover { background: #fee2e2; }
.rpos-cart__items { flex: 1; overflow-y: auto; padding: .5rem; }
.rpos-cart__empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; padding: 2rem; color: var(--rpos-muted); text-align: center; gap: .5rem; }
.rpos-cart__empty i { font-size: 2rem; opacity: .4; }
.rpos-cart__empty p { font-size: .85rem; margin: 0; }
.rpos-cart__empty small { font-size: .75rem; }
.rpos-cart-item { border-radius: 10px; margin-bottom: 6px; background: #f9fafb; border: 1px solid var(--rpos-border); overflow: hidden; }
.rpos-cart-item__main { display: flex; align-items: center; gap: .5rem; padding: .5rem; }
.rpos-cart-item__icon { width: 34px; height: 34px; background: #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: .9rem; color: var(--rpos-accent); flex-shrink: 0; }
.rpos-cart-item__info { flex: 1; min-width: 0; }
.rpos-cart-item__name { font-size: .8rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.rpos-cart-item__price { font-size: .75rem; color: var(--rpos-accent); font-weight: 600; }
.rpos-cart-item__qty { display: flex; align-items: center; gap: 4px; }
.rpos-cart-item__qty-btn { width: 24px; height: 24px; border-radius: 50%; border: 1px solid var(--rpos-border); background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: .7rem; color: var(--rpos-text); transition: all .15s; }
.rpos-cart-item__qty-btn:hover { background: var(--rpos-accent); color: #fff; border-color: var(--rpos-accent); }
.rpos-cart-item__qty-val { font-size: 1.25rem; font-weight: 700; min-width: 30px; text-align: center; }
.rpos-cart-item__note-btn { background: none; border: none; color: #9ca3af; cursor: pointer; font-size: .75rem; padding: 2px 4px; border-radius: 4px; transition: color .2s; }
.rpos-cart-item__note-btn:hover { color: var(--rpos-accent); }
.rpos-cart-item__remove-btn { background: none; border: none; color: #d1d5db; cursor: pointer; font-size: .8rem; padding: 2px 5px; border-radius: 4px; transition: color .2s, background .2s; flex-shrink: 0; }
.rpos-cart-item__remove-btn:hover { color: #ef4444; background: #fee2e2; }
.rpos-cart-item__note-row { padding: 0 .5rem .5rem; display: none; }
.rpos-cart-item__note-row.visible { display: block; }
.rpos-cart-item__note-input { width: 100%; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 8px; font-size: .72rem; font-family: 'Cairo', sans-serif; color: var(--rpos-text); background: #fff; outline: none; resize: none; }
.rpos-cart-item__note-input:focus { border-color: var(--rpos-accent); }
.rpos-cart__totals { padding: .75rem 1rem; border-top: 1px solid var(--rpos-border); display: flex; flex-direction: column; gap: 4px; background: #f9fafb; }
.rpos-cart__total-row { display: flex; justify-content: space-between; font-size: .82rem; color: var(--rpos-muted); }
.rpos-cart__total-row--total { font-size: 1rem; font-weight: 700; color: var(--rpos-text); margin-top: 4px; padding-top: 4px; border-top: 1px dashed var(--rpos-border); }
.rpos-cart__total-amount { color: var(--rpos-accent); font-size: 1.1rem; }
.rpos-cart__actions { display: flex; gap: 4px; padding: .5rem .75rem; border-top: 1px solid var(--rpos-border); }
.rpos-cart__action-btn { flex: 1; padding: .45rem 0; border-radius: 8px; border: 1px solid var(--rpos-border); background: #fff; font-family: 'Cairo', sans-serif; font-size: .72rem; font-weight: 600; cursor: pointer; transition: all .2s; color: var(--rpos-text); display: flex; align-items: center; justify-content: center; gap: 3px; }
.rpos-cart__action-btn:hover { background: #f3f4f6; }
.rpos-cart__action-btn--void { color: #a1db00ff; border-color: #fecaca; }
.rpos-cart__action-btn--void:hover { background: #fee2e2; }
.rpos-cart__action-btn--print { color: #3b82f6; border-color: #bfdbfe; }
.rpos-cart__action-btn--print:hover { background: #eff6ff; }
.rpos-checkout-btn { margin: .5rem .75rem; padding: .85rem; background: linear-gradient(135deg, var(--rpos-accent) 0%, #16f9b5ff 100%); color: #fff; border: none; border-radius: var(--rpos-radius); font-family: 'Cairo', sans-serif; font-size: 1rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: .5rem; transition: opacity .2s, transform .1s; box-shadow: 0 4px 12px rgba(232,98,42,.3); }
.rpos-checkout-btn:hover { opacity: .92; }
.rpos-checkout-btn:active { transform: scale(.98); }
.rpos-checkout-btn:disabled { background: #d1d5db; cursor: not-allowed; box-shadow: none; }

.rpos-footer { height: var(--rpos-footer-h); background: var(--rpos-dark); color: #6b7280; display: flex; align-items: center; padding: 0 1.25rem; gap: 1rem; font-size: .72rem; flex-shrink: 0; }
.rpos-footer__status { display: flex; align-items: center; gap: .35rem; white-space: nowrap; }
.rpos-footer__version { white-space: nowrap; }
.rpos-footer__dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; }
.rpos-footer__dot--on { background: #10b981; }
.rpos-footer__dot--off { background: #ef4444; }
.rpos-footer__links { margin-right: auto; display: flex; gap: .25rem; }
.rpos-footer__links a { color: #9ca3af; text-decoration: none; transition: color .2s, background .2s; display: flex; flex-direction: column; align-items: center; gap: 2px; padding: .3rem .65rem; border-radius: 8px; font-size: .62rem; line-height: 1.2; }
.rpos-footer__links a i { font-size: 1rem; }
.rpos-footer__links a:hover { color: #fff; background: rgba(255,255,255,.1); }

.rpos-modal-table-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: .6rem;
    max-height: 340px;
    overflow-y: auto;
    padding: .25rem;
}
.rpos-table-card { border-radius: 10px; border: 2px solid var(--rpos-border); padding: .65rem .5rem; cursor: pointer; transition: all .2s; text-align: center; position: relative; background: #fff; }
.rpos-table-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }
.rpos-table-card--free { border-color: #10b981; background: #f0fdf4; }
.rpos-table-card--occupied { border-color: #ef4444; background: #fef2f2; opacity: .7; cursor: not-allowed; }
.rpos-table-card--reserved { border-color: #f59e0b; background: #fffbeb; }
.rpos-table-card--selected { border-color: var(--rpos-accent) !important; background: #fff7f4 !important; box-shadow: 0 0 0 3px rgba(232,98,42,.2); }
.rpos-table-card__icon { font-size: 1.5rem; margin-bottom: 4px; }
.rpos-table-card--free .rpos-table-card__icon { color: #10b981; }
.rpos-table-card--occupied .rpos-table-card__icon { color: #ef4444; }
.rpos-table-card--reserved .rpos-table-card__icon { color: #f59e0b; }
.rpos-table-card--selected .rpos-table-card__icon { color: var(--rpos-accent); }
.rpos-table-card__name { font-size: .78rem; font-weight: 700; color: var(--rpos-text); }
.rpos-table-card__cap { font-size: .65rem; color: var(--rpos-muted); margin-top: 2px; }
.rpos-table-card__status-dot { position: absolute; top: 5px; left: 5px; width: 8px; height: 8px; border-radius: 50%; }
.rpos-table-card--free .rpos-table-card__status-dot { background: #10b981; }
.rpos-table-card--occupied .rpos-table-card__status-dot { background: #ef4444; }
.rpos-table-card--reserved .rpos-table-card__status-dot { background: #f59e0b; }

.rpos-modal-legend { display: flex; gap: 1rem; margin-bottom: .75rem; }
.rpos-legend-item { display: flex; align-items: center; gap: 5px; font-size: .75rem; color: var(--rpos-muted); }
.rpos-legend-dot { width: 10px; height: 10px; border-radius: 3px; }
.rpos-legend-dot--free { background: #10b981; }
.rpos-legend-dot--occupied { background: #ef4444; }
.rpos-legend-dot--reserved { background: #f59e0b; }

.rpos-customer-search-result {
    border: 1px solid var(--rpos-border);
    border-radius: 8px;
    padding: .6rem .85rem;
    cursor: pointer;
    transition: background .15s;
    font-size: .88rem;
}
.rpos-customer-search-result:hover { background: #f3f4f6; }
.rpos-customer-search-result .name { font-weight: 600; }
.rpos-customer-search-result .phone { font-size: .75rem; color: var(--rpos-muted); }

.rpos-quick-amounts { display: flex; gap: .5rem; flex-wrap: wrap; margin-top: .5rem; }
.rpos-quick-amt { padding: .35rem .85rem; border-radius: 20px; border: 2px solid var(--rpos-border); background: #fff; font-family: 'Cairo', sans-serif; font-size: .85rem; font-weight: 600; cursor: pointer; transition: all .2s; color: var(--rpos-text); }
.rpos-quick-amt:hover { border-color: var(--rpos-accent); color: var(--rpos-accent); background: #fff7f4; }
.rpos-quick-amt--exact { border-color: #10b981; color: #10b981; }
.rpos-quick-amt--exact:hover { background: #f0fdf4; }

.rpos-products::-webkit-scrollbar,
.rpos-cart__items::-webkit-scrollbar,
.rpos-categories::-webkit-scrollbar,
.rpos-modal-table-grid::-webkit-scrollbar { width: 4px; }
.rpos-products::-webkit-scrollbar-thumb,
.rpos-cart__items::-webkit-scrollbar-thumb,
.rpos-categories::-webkit-scrollbar-thumb,
.rpos-modal-table-grid::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

.rpos-delivery-panel { border-top: 1px solid var(--rpos-border); }
.rpos-delivery-panel__inner { background: #fffbf5; border-color: #fed7aa !important; }
.rpos-delivery-search-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    left: 0;
    z-index: 100;
    background: #fff;
    border: 1px solid var(--rpos-border);
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
    max-height: 160px;   
    overflow-y: auto;   
}
.rpos-delivery-result-item {
    padding: .5rem .75rem;
    cursor: pointer;
    font-size: .82rem;
    border-bottom: 1px solid #f3f4f6;
    transition: background .15s;
}
.rpos-delivery-result-item:last-child { border-bottom: none; }
.rpos-delivery-result-item:hover { background: #f9fafb; }

/* ===== MODAL FIX ===== */
.modal-backdrop {
    position: fixed !important;
    top: 0; left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 1050;
}
.modal {
    position: fixed !important;
    z-index: 1055;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    outline: 0;
}
</style>
