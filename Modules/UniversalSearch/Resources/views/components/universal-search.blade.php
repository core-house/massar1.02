<style>
    .massar-universal-search-btn:hover i { transform: scale(1.08); transition: transform .2s ease; }
    .massar-universal-search-item:hover { background: rgba(52, 211, 163, 0.05) !important; }
    .massar-universal-search-item.is-active { background: rgba(52, 211, 163, 0.12) !important; }
</style>

<li class="me-3">
    <button type="button"
            class="btn btn-lg transition-base massar-universal-search-btn"
            title="{{ __('بحث شامل') }} (Ctrl+K)"
            onclick="window.massarUniversalSearch && window.massarUniversalSearch.open && window.massarUniversalSearch.open()"
            style="background: none; border: none; color: #34d3a3; cursor: pointer; padding: 8px 12px;">
        <i class="fas fa-search fa-2x" style="color: #34d3a3;"></i>
    </button>
</li>

<div class="offcanvas offcanvas-top"
     tabindex="-1"
     id="massarUniversalSearchOffcanvas"
     aria-labelledby="massarUniversalSearchLabel"
     style="height: 360px; z-index: 1060;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="massarUniversalSearchLabel">
            <i class="fas fa-search me-2" style="color: #34d3a3;"></i>
            {{ __('بحث شامل') }}
        </h5>
        <div class="d-flex align-items-center gap-2">
            <small class="text-muted">Ctrl+K</small>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('إغلاق') }}"></button>
        </div>
    </div>

    <div class="offcanvas-body p-0">
        <div class="p-3 border-bottom">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text"
                       class="form-control"
                       id="massarUniversalSearchInput"
                       autocomplete="off"
                       placeholder="{{ __('ابحث باسم الشاشة أو الراوت...') }}"
                       aria-label="{{ __('بحث شامل') }}">
            </div>
            <small class="text-muted d-block mt-2" style="font-size:.75rem;">
                {{ __('ابدأ بكتابة حرفين على الأقل — Enter للفتح — Esc للإغلاق') }}
            </small>
        </div>

        <div id="massarUniversalSearchResults" style="max-height: 260px; overflow:auto;"></div>
    </div>
</div>

<script>
(function () {
    if (window.massarUniversalSearch) { return; }

    var offcanvasInstance = null;
    var debounceTimer = null;
    var lastRequestId = 0;
    var activeIndex = -1;
    var lastItems = [];

    function getEl(id) { return document.getElementById(id); }

    function ensureOffcanvas() {
        var el = getEl('massarUniversalSearchOffcanvas');
        if (!el) { return null; }
        if (!offcanvasInstance) {
            offcanvasInstance = new bootstrap.Offcanvas(el);
        }
        return offcanvasInstance;
    }

    function open() {
        var inst = ensureOffcanvas();
        if (!inst) { return; }
        inst.show();
        setTimeout(function () {
            var input = getEl('massarUniversalSearchInput');
            if (input) { input.focus(); input.select && input.select(); }
        }, 120);
    }

    function close() {
        var inst = ensureOffcanvas();
        if (!inst) { return; }
        inst.hide();
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function setActive(index) {
        activeIndex = index;
        var container = getEl('massarUniversalSearchResults');
        if (!container) { return; }
        var nodes = container.querySelectorAll('[data-massar-universal-search-item]');
        nodes.forEach(function (n) { n.classList.remove('is-active'); });
        if (activeIndex >= 0 && activeIndex < nodes.length) {
            nodes[activeIndex].classList.add('is-active');
            nodes[activeIndex].scrollIntoView({ block: 'nearest' });
        }
    }

    function render(items) {
        lastItems = Array.isArray(items) ? items : [];
        activeIndex = -1;

        var container = getEl('massarUniversalSearchResults');
        if (!container) { return; }

        if (!lastItems.length) {
            container.innerHTML = '<div class="p-3 text-muted small">{{ __("لا توجد نتائج") }}</div>';
            return;
        }

        var html = '';
        lastItems.forEach(function (item, idx) {
            var title = escapeHtml(item.title || item.name || '');
            var group = escapeHtml(item.group || '');
            var uri = escapeHtml(item.uri || '');
            var url = escapeHtml(item.url || '#');

            html += ''
                + '<a href="' + url + '" '
                + 'class="d-flex align-items-start px-3 py-2 text-decoration-none border-bottom massar-universal-search-item" '
                + 'data-massar-universal-search-item="1" data-index="' + idx + '"'
                + 'style="color: inherit;">'
                +   '<i class="fas fa-arrow-right me-2 text-muted small" style="margin-top:2px;"></i>'
                +   '<div style="min-width:0;">'
                +     '<div class="small fw-semibold text-dark" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + title + '</div>'
                +     '<div class="text-muted" style="font-size:.75rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">'
                +        (group ? ('<span class="me-2">' + group + '</span>') : '')
                +        '<span dir="ltr">' + uri + '</span>'
                +     '</div>'
                +   '</div>'
                + '</a>';
        });

        container.innerHTML = html;

        container.querySelectorAll('[data-massar-universal-search-item]').forEach(function (a) {
            a.addEventListener('mouseenter', function () {
                var idx = parseInt(this.getAttribute('data-index') || '-1', 10);
                if (!isNaN(idx)) { setActive(idx); }
            });
        });
    }

    function searchNow(query) {
        var minLen = 2;
        query = (query || '').trim();

        if (query.length < minLen) {
            render([]);
            return;
        }

        var requestId = ++lastRequestId;

        if (!window.jQuery) {
            // Fallback to fetch if jQuery isn't available
            fetch('{{ route("universalsearch.search") }}?q=' + encodeURIComponent(query), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (requestId !== lastRequestId) { return; }
                render(data);
            })
            .catch(function () { if (requestId === lastRequestId) { render([]); } });
            return;
        }

        window.jQuery.ajax({
            url: '{{ route("universalsearch.search") }}',
            method: 'GET',
            data: { q: query },
            dataType: 'json'
        }).done(function (data) {
            if (requestId !== lastRequestId) { return; }
            render(data);
        }).fail(function () {
            if (requestId !== lastRequestId) { return; }
            render([]);
        });
    }

    function wire() {
        var input = getEl('massarUniversalSearchInput');
        if (!input) { return; }

        input.addEventListener('input', function () {
            var value = input.value;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () { searchNow(value); }, 250);
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                input.value = '';
                render([]);
                close();
                return;
            }

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!lastItems.length) { return; }
                var next = Math.min(activeIndex + 1, lastItems.length - 1);
                setActive(next);
                return;
            }

            if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (!lastItems.length) { return; }
                var prev = Math.max(activeIndex - 1, 0);
                setActive(prev);
                return;
            }

            if (e.key === 'Enter') {
                if (activeIndex >= 0 && lastItems[activeIndex] && lastItems[activeIndex].url) {
                    e.preventDefault();
                    window.location.href = lastItems[activeIndex].url;
                }
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        // Ctrl+K
        if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
            e.preventDefault();
            open();
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', wire);
    } else {
        wire();
    }

    window.massarUniversalSearch = {
        open: open,
        close: close
    };
})();
</script>

