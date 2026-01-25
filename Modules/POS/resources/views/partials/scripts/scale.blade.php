    // Scale Integration
    function updateScaleStatus() {
        if (!scale) {
            $('#scaleStatus').hide();
            return;
        }
        
        $('#scaleStatus').show();
        if (scale.isConnected) {
            $('#scaleStatus').removeClass('alert-info').addClass('alert-success');
            $('#scaleStatusText').html('<i class="fas fa-check-circle"></i> الميزان متصل');
            $('#connectScaleBtn').html('<i class="fas fa-unlink"></i> قطع الاتصال');
        } else {
            $('#scaleStatus').removeClass('alert-success').addClass('alert-info');
            $('#scaleStatusText').html('<i class="fas fa-exclamation-circle"></i> الميزان غير متصل');
            $('#connectScaleBtn').html('<i class="fas fa-plug"></i> اتصال');
        }
    }
    
    // زر الاتصال/قطع الاتصال بالميزان
    $('#connectScaleBtn').on('click', async function() {
        if (!scale) {
            showToast('الميزان غير مدعوم في هذا المتصفح', 'error');
            return;
        }
        
        if (scale.isConnected) {
            await scale.disconnect();
            updateScaleStatus();
            showToast('تم قطع الاتصال بالميزان', 'info');
        } else {
            const connected = await scale.connect();
            if (connected) {
                updateScaleStatus();
                showToast('تم الاتصال بالميزان بنجاح', 'success');
            } else {
                showToast('فشل الاتصال بالميزان', 'error');
            }
        }
    });
    
    // تحديث حالة الميزان كل 5 ثواني
    setInterval(function() {
        if (scale && scale.isConnected) {
            updateScaleStatus();
        }
    }, 5000);
