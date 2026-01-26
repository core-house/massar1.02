    // Product Search
    let searchTimeout;
    $('#productSearch').on('input', function() {
        clearTimeout(searchTimeout);
        const term = $(this).val();
        
        if (term.length < 1) {
            loadAllProducts();
            return;
        }

        searchTimeout = setTimeout(async function() {
            if (isOnline) {
                $.ajax({
                    url: '{{ route("pos.api.search-items") }}',
                    method: 'GET',
                    data: { term: term },
                    success: function(response) {
                        displayProducts(response.items);
                        if (response.items && response.items.length > 0) {
                            db.saveItems(response.items);
                        }
                    },
                    error: function() {
                        searchLocal(term);
                    }
                });
            } else {
                searchLocal(term);
            }
        }, 300);
    });

    async function searchLocal(term) {
        try {
            const items = await db.searchItems(term);
            displayProducts(items);
        } catch (err) {
            console.error('Local search error:', err);
        }
    }

    // Barcode Search - البحث فقط عند الضغط على Enter
    $('#barcodeSearch').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            const barcode = $(this).val().trim();
            if (barcode.length > 0) {
                searchBarcode(barcode);
            }
        }
    });
    
    // دالة البحث بالباركود - دالة الميزان والبحث العادي
    async function searchBarcode(barcode) {
        if (!barcode || barcode.length === 0) return;
        
        // المحاولة الأولى: البحث في الميزان (إذا كان مفعّل)
        if (scaleSettings && scaleSettings.enable_scale_items && scaleSettings.scale_code_prefix) {
            const prefix = scaleSettings.scale_code_prefix.toString();
            const prefixLength = prefix.length;
            const codeDigits = parseInt(scaleSettings.scale_code_digits) || 5;
            const quantityDigits = parseInt(scaleSettings.scale_quantity_digits) || 5;
            const divisor = parseInt(scaleSettings.scale_quantity_divisor) || 100;
            
            // التحقق من أن الباركود يبدأ بـ prefix
            if (barcode.length >= prefixLength && barcode.startsWith(prefix)) {
                const totalDigits = prefixLength + codeDigits + quantityDigits;
                
                // التحقق من أن الباركود يحتوي على العدد الكافي من الأرقام
                if (barcode.length >= totalDigits) {
                    // استخراج كود الصنف (بعد prefix)
                    const itemCode = barcode.substring(prefixLength, prefixLength + codeDigits);
                    
                    // استخراج الكمية (بعد كود الصنف)
                    const quantityCode = barcode.substring(prefixLength + codeDigits, prefixLength + codeDigits + quantityDigits);
                    const quantity = parseInt(quantityCode) / divisor;
                    
                    if (quantity > 0 && itemCode) {
                        // البحث عن الصنف بالكود المستخرج (البحث في الباركودات)
                        let foundItem = null;
                        
                        // البحث في الـ cache أولاً - البحث في الباركودات
                        for (const itemId in itemsCache) {
                            const item = itemsCache[itemId];
                            if (item.barcodes && Array.isArray(item.barcodes)) {
                                const foundBarcode = item.barcodes.find(b => b.barcode === itemCode);
                                if (foundBarcode) {
                                    foundItem = item;
                                    break;
                                }
                            }
                            // البحث في رقم الصنف كـ fallback
                            if (item.code && item.code.toString() === itemCode) {
                                foundItem = item;
                                break;
                            }
                        }
                        
                        if (!foundItem) {
                            // البحث في IndexedDB أولاً (أسرع)
                            if (db) {
                                try {
                                    const localItems = await db.searchByBarcode(itemCode);
                                    if (localItems && localItems.length > 0) {
                                        foundItem = localItems[0];
                                    }
                                } catch (err) {
                                    console.error('Local search error:', err);
                                }
                            }
                            
                            // إذا لم يُوجد محلياً، البحث في الـ server
                            if (!foundItem && isOnline) {
                                try {
                                    const response = await $.ajax({
                                        url: '{{ route("pos.api.search-barcode") }}',
                                        method: 'GET',
                                        data: { barcode: itemCode },
                                        timeout: 2000 // تقليل timeout
                                    });
                                    
                                    if (response.items && response.items.length > 0) {
                                        const item = response.items[0];
                                        foundItem = await getItemData(item.id);
                                    }
                                } catch (err) {
                                    console.error('خطأ في البحث عن الصنف:', err);
                                }
                            }
                        }
                        
                        if (foundItem) {
                            // إضافة الصنف مع الكمية المحسوبة
                            addScaleItemToCart(foundItem, quantity);
                            $('#barcodeSearch').val('');
                            showToast(`تم إضافة ${quantity.toFixed(3)} كيلو`, 'success');
                            return;
                        } else {
                            // لم يُوجد الصنف بالكود المستخرج من الميزان
                            showToast('لم يتم العثور على صنف بالكود: ' + itemCode, 'error');
                            $('#barcodeSearch').val('');
                            return;
                        }
                    }
                }
            }
        }
        
        // المحاولة الثانية: البحث في الباركود العادي
        // البحث محلياً أولاً من الـ cache (فوري - الأسرع)
        // البحث في الباركودات أولاً
        let cachedItem = null;
        for (const itemId in itemsCache) {
            const item = itemsCache[itemId];
            if (item.barcodes && Array.isArray(item.barcodes)) {
                const foundBarcode = item.barcodes.find(b => b.barcode === barcode);
                if (foundBarcode) {
                    cachedItem = item;
                    break;
                }
            }
            // البحث في رقم الصنف كـ fallback
            if (item.code && item.code.toString() === barcode) {
                cachedItem = item;
                break;
            }
        }
        
        if (cachedItem) {
            // إضافة المنتج مباشرة للسلة
            addItemToCart(cachedItem.id);
            $('#barcodeSearch').val('');
            showToast('تم إضافة المنتج للشمنال', 'success');
            return;
        }
        
        // البحث في IndexedDB (سريع)
        let foundInIndexedDB = false;
        if (db) {
            try {
                const localItems = await db.searchByBarcode(barcode);
                if (localItems && localItems.length > 0) {
                    foundInIndexedDB = true;
                    if (localItems.length === 1) {
                        // إذا كان منتج واحد فقط، إضافته مباشرة
                        const itemId = localItems[0].id;
                        addItemToCart(itemId);
                        $('#barcodeSearch').val('');
                        showToast('تم إضافة المنتج للشمنال', 'success');
                    } else {
                        displayProducts(localItems);
                    }
                    return;
                }
            } catch (err) {
                console.error('Local barcode search error:', err);
            }
        }
        
        // البحث في الـ server (أبطأ - فقط إذا لم يُوجد محلياً)
        if (isOnline) {
            try {
                const response = await $.ajax({
                    url: '{{ route("pos.api.search-barcode") }}',
                    method: 'GET',
                    data: { barcode: barcode },
                    timeout: 2000 // تقليل timeout
                });
                
                if (response.items && response.items.length > 0) {
                    // حفظ في الـ cache
                    response.items.forEach(item => {
                        itemsCache[item.id] = item;
                    });
                    
                    if (response.exact_match && response.items.length === 1) {
                        const itemId = response.items[0].id;
                        addItemToCart(itemId);
                        $('#barcodeSearch').val('');
                        showToast('تم إضافة المنتج للشمنال', 'success');
                    } else {
                        displayProducts(response.items);
                    }
                    
                    // حفظ في IndexedDB
                    if (db && response.items && response.items.length > 0) {
                        db.saveItems(response.items).catch(err => console.error('Save to IndexedDB error:', err));
                    }
                    return;
                } else {
                    // لم يتم العثور على نتائج من الـ server
                    showToast('لم يتم العثور على صنف بالباركود: ' + barcode, 'error');
                    $('#barcodeSearch').val('');
                    return;
                }
            } catch (err) {
                // في حالة الخطأ، عرض رسالة الخطأ
                showToast('حدث خطأ أثناء البحث عن الباركود: ' + barcode, 'error');
                $('#barcodeSearch').val('');
                return;
            }
        } else {
            // غير متصل بالإنترنت ولم يُوجد محلياً
            showToast('لم يتم العثور على صنف بالباركود: ' + barcode, 'error');
            $('#barcodeSearch').val('');
            return;
        }
        
        // إذا وصلنا هنا، يعني لم يُوجد في أي مكان (fallback)
        showToast('لم يتم العثور على صنف بالباركود: ' + barcode, 'error');
        $('#barcodeSearch').val('');
    }

    // Category Selection
    $('.category-btn').on('click', function() {
        $('.category-btn').removeClass('active');
        $(this).addClass('active');
        selectedCategory = $(this).data('category');

        if (selectedCategory) {
            if (isOnline) {
                $.ajax({
                    url: '{{ route("pos.api.category-items", ":id") }}'.replace(':id', selectedCategory),
                    method: 'GET',
                    success: function(response) {
                        displayProducts(response.items);
                        if (response.items && response.items.length > 0) {
                            db.saveItems(response.items);
                        }
                    },
                    error: function() {
                        loadAllProducts();
                    }
                });
            } else {
                loadAllProducts();
            }
        } else {
            loadAllProducts();
        }
    });

    function loadAllProducts() {
        if (itemsCache && Object.keys(itemsCache).length > 0) {
            const items = Object.values(itemsCache);
            displayProducts(items);
        } else {
            displayProducts(initialProductsData);
        }
    }

    function displayProducts(items) {
        const grid = $('#productsGrid');
        grid.empty();
        
        items.forEach(function(item) {
            if (!itemsCache[item.id]) {
                getItemData(item.id).then(function(fullData) {
                    itemsCache[item.id] = fullData;
                });
            }
            
            const isWeightScale = itemsCache[item.id]?.is_weight_scale || false;
            const card = `
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="product-card card h-100" data-item-id="${item.id}" style="border: none; border-radius: 15px; overflow: hidden; cursor: pointer;">
                        <div class="product-image" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                            <i class="fas ${isWeightScale ? 'fa-weight' : 'fa-box'} fa-4x text-white opacity-50"></i>
                            ${isWeightScale ? '<span class="badge bg-warning position-absolute top-0 start-0 m-2"><i class="fas fa-weight"></i> ميزان</span>' : ''}
                        </div>
                        <div class="card-body p-3">
                            <h6 class="card-title mb-2" style="font-size: 0.95rem; font-weight: 600; color: #333;">${item.name}</h6>
                            <div class="product-footer" style="height: 4px; background: #FFD700; border-radius: 2px;"></div>
                        </div>
                    </div>
                </div>
            `;
            grid.append(card);
        });
    }
