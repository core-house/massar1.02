    // Add Product to Cart
    $(document).on('click', '.product-card', function(e) {
        // تجاهل الضغط إذا كان على زر التفاصيل
        if ($(e.target).closest('.product-details-btn').length > 0) {
            return;
        }

        const itemId = $(this).data('item-id');
        addItemToCart(itemId);
    });

    function addItemToCart(itemId) {
        if (itemsCache[itemId]) {
            const response = itemsCache[itemId];
            addItemToCartFromData(response);
            return;
        }

        getItemData(itemId).then(function(response) {
            addItemToCartFromData(response);
        }).catch(function() {
            showToast(POS_TRANS.item_fetch_error, 'error');
        });
    }

    function addItemToCartFromData(response) {
        const existingItem = cart.find(item => item.id === response.id);

        // صنف عادي
        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({
                id: response.id,
                name: response.name,
                code: response.code,
                quantity: 1,
                price: response.prices[0]?.value || 0,
                unit_id: response.units[0]?.id || null,
                unit_name: response.units[0]?.name || 'قطعة',
                is_weight_scale: false
            });
        }
        updateCartDisplay();
    }

    // إضافة صنف ميزان مع الكمية المحددة
    function addScaleItemToCart(response, quantity) {
        if (!response || quantity <= 0) {
            showToast(POS_TRANS.invalid_quantity, 'error');
            return;
        }

        const existingItem = cart.find(item => item.id === response.id);

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({
                id: response.id,
                name: response.name,
                code: response.code,
                quantity: quantity,
                price: response.prices[0]?.value || 0,
                unit_id: response.units[0]?.id || null,
                unit_name: response.units[0]?.name || POS_TRANS.unit_kilo,
                is_weight_scale: true
            });
        }
        updateCartDisplay();
    }

    function updateCartDisplay() {
        const cartItems = $('#cartItems');
        cartItems.empty();

        if (cart.length === 0) {
            cartItems.html(`
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="text-muted">${POS_TRANS.cart_empty_msg}</p>
                    <small class="text-muted">${POS_TRANS.cart_add_hint}</small>
                </div>
            `);
            $('#cartItemsCount').text('0');
        } else {
            // عرض الأصناف بترتيب عكسي (الأحدث في الأعلى)
            [...cart].reverse().forEach(function(item, originalIndex) {
                const index = cart.length - 1 - originalIndex;
                const subtotal = (item.quantity * item.price).toFixed(2);
                const isWeightScale = item.is_weight_scale || false;
                const quantityLabel = isWeightScale ? POS_TRANS.weight_label : POS_TRANS.quantity_label;
                const quantityDisplay = isWeightScale ?
                    `${parseFloat(item.quantity).toFixed(3)} ${item.unit_name || POS_TRANS.unit_kilo}` :
                    item.quantity;

                const cartItem = `
                    <div class="card mb-2 shadow-sm" style="border: 1px solid #e0e0e0;">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold" style="font-size: 0.9rem; color: #333;">
                                        ${item.name}
                                        ${isWeightScale ? `<span class="badge bg-warning ms-1"><i class="fas fa-weight"></i> ${POS_TRANS.weight_scale}</span>` : ''}
                                    </h6>
                                    <small class="text-muted">${item.code || ''}</small>
                                </div>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger remove-item"
                                        data-index="${index}"
                                        style="border: none; padding: 0.25rem 0.5rem;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    ${!isWeightScale ? `
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary quantity-btn"
                                            data-index="${index}"
                                            data-action="decrease"
                                            style="width: 30px; height: 30px; padding: 0;">
                                        <i class="fas fa-minus" style="font-size: 0.7rem;"></i>
                                    </button>
                                    ` : ''}
                                    <div class="d-flex flex-column">
                                        <small class="text-muted" style="font-size: 0.7rem;">${quantityLabel}</small>
                                        ${isWeightScale ? `
                                        <input type="number"
                                               class="form-control form-control-sm cart-quantity text-center"
                                               data-index="${index}"
                                               value="${item.quantity}"
                                               step="0.001"
                                               style="width: 80px; height: 30px;"
                                               min="0.001">
                                        ` : `
                                        <input type="number"
                                               class="form-control form-control-sm cart-quantity text-center"
                                               data-index="${index}"
                                               value="${item.quantity}"
                                               style="width: 60px; height: 30px;"
                                               min="1">
                                        `}
                                    </div>
                                    ${!isWeightScale ? `
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary quantity-btn"
                                            data-index="${index}"
                                            data-action="increase"
                                            style="width: 30px; height: 30px; padding: 0;">
                                        <i class="fas fa-plus" style="font-size: 0.7rem;"></i>
                                    </button>
                                    ` : `
                                    <span class="badge bg-warning ms-1">
                                        <i class="fas fa-weight"></i> ${POS_TRANS.weight_scale}
                                    </span>
                                    `}
                                    <span class="text-muted ms-2">x ${parseFloat(item.price).toFixed(2)} ${POS_TRANS.currency}/${isWeightScale ? POS_TRANS.unit_kilo : POS_TRANS.unit_piece}</span>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success" style="font-size: 1rem;">${subtotal} ${POS_TRANS.currency}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                cartItems.prepend(cartItem);
            });
            $('#cartItemsCount').text(cart.length);
        }

        const subtotal = calculateTotal();
        const discount = 0;
        const additional = 0;
        const total = subtotal - discount + additional;

        $('#cartSubtotal').text(subtotal.toFixed(2) + ' ' + POS_TRANS.currency);
        $('#cartDiscount').text(discount.toFixed(2) + ' ' + POS_TRANS.currency);
        $('#cartAdditional').text(additional.toFixed(2) + ' ' + POS_TRANS.currency);
        $('#cartTotal').text(total.toFixed(2) + ' ' + POS_TRANS.currency);
    }

    function calculateTotal() {
        return cart.reduce((sum, item) => sum + (item.quantity * item.price), 0);
    }

    // Cart Quantity Update
    $(document).on('change', '.cart-quantity', function() {
        const index = $(this).data('index');
        const item = cart[index];
        const isWeightScale = item.is_weight_scale || false;
        const minValue = isWeightScale ? 0.001 : 1;

        let quantity = parseFloat($(this).val());
        if (isWeightScale) {
            quantity = parseFloat(quantity.toFixed(3));
        } else {
            quantity = parseInt(quantity);
        }

        if (quantity >= minValue) {
            cart[index].quantity = quantity;
            updateCartDisplay();
        } else {
            $(this).val(minValue);
            cart[index].quantity = minValue;
            updateCartDisplay();
        }
    });

    // Quantity Buttons
    $(document).on('click', '.quantity-btn', function() {
        const index = $(this).data('index');
        const action = $(this).data('action');
        const item = cart[index];

        // لا تعمل أزرار الكمية لأصناف الميزان
        if (item.is_weight_scale) {
            return;
        }

        if (action === 'increase') {
            cart[index].quantity++;
        } else if (action === 'decrease' && cart[index].quantity > 1) {
            cart[index].quantity--;
        }

        updateCartDisplay();
    });

    // Remove Item
    $(document).on('click', '.remove-item', function() {
        const index = $(this).data('index');
        if (confirm(POS_TRANS.confirm_remove_item)) {
            cart.splice(index, 1);
            updateCartDisplay();
        }
    });

    // إعادة تعيين السلة
    $('#resetBtn').on('click', function(e) {
        e.preventDefault();
        if (confirm(POS_TRANS.confirm_reset_cart)) {
            cart = [];
            selectedTable = null;
            invoiceNotes = '';
            updateCartDisplay();
            showToast(POS_TRANS.cart_reset_success, 'success');
        }
    });
