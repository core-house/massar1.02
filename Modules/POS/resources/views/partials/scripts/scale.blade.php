    // Scale Integration
    function updateScaleStatus() {
        if (!scale) {
            $('#scaleStatus').hide();
            return;
        }
        
        $('#scaleStatus').show();
        if (scale.isConnected) {
            $('#scaleStatus').removeClass('alert-info').addClass('alert-success');
            $('#scaleStatusText').html('<i class="fas fa-check-circle"></i> ' + POS_TRANS.scale_status_connected);
            $('#connectScaleBtn').html('<i class="fas fa-unlink"></i> ' + POS_TRANS.scale_disconnect_btn);
        } else {
            $('#scaleStatus').removeClass('alert-success').addClass('alert-info');
            $('#scaleStatusText').html('<i class="fas fa-exclamation-circle"></i> ' + POS_TRANS.scale_status_disconnected);
            $('#connectScaleBtn').html('<i class="fas fa-plug"></i> ' + POS_TRANS.scale_connect_btn);
        }
    }
    
    $('#connectScaleBtn').on('click', async function() {
        if (!scale) {
            showToast(POS_TRANS.scale_not_supported, 'error');
            return;
        }
        
        if (scale.isConnected) {
            await scale.disconnect();
            updateScaleStatus();
            showToast(POS_TRANS.scale_disconnected, 'info');
        } else {
            const connected = await scale.connect();
            if (connected) {
                updateScaleStatus();
                showToast(POS_TRANS.scale_connected, 'success');
            } else {
                showToast(POS_TRANS.scale_connect_failed, 'error');
            }
        }
    });
    
    // تحديث حالة الميزان كل 5 ثواني
    setInterval(function() {
        if (scale && scale.isConnected) {
            updateScaleStatus();
        }
    }, 5000);
