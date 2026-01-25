    // Modal Triggers
    $('#registerBtn').on('click', function() {
        if (cart.length === 0) {
            alert('السلة فارغة');
            return;
        }
        $('#paymentTotal').val(calculateTotal().toFixed(2) + ' ريال');
        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
    });

    $('#customerBtn').on('click', function() {
        const customerModal = new bootstrap.Modal(document.getElementById('customerModal'));
        customerModal.show();
        loadCustomerBalance();
    });

    $('#selectedCustomer').on('change', function() {
        selectedCustomer = $(this).val();
        loadCustomerBalance();
    });

    function loadCustomerBalance() {
        const customerId = $('#selectedCustomer').val();
        if (!customerId) {
            $('#balanceAmount').text('0.00');
            return;
        }

        if (isOnline) {
            $.ajax({
                url: '{{ route("pos.api.customer-balance", ":id") }}'.replace(':id', customerId),
                method: 'GET',
                success: function(response) {
                    $('#balanceAmount').text(parseFloat(response.balance || 0).toFixed(2));
                },
                error: function() {
                    $('#balanceAmount').text('0.00');
                }
            });
        } else {
            $('#balanceAmount').text('غير متاح (غير متصل)');
        }
    }

    $('#paymentBtn').on('click', function() {
        if (cart.length === 0) {
            alert('السلة فارغة');
            return;
        }
        $('#paymentTotal').val(calculateTotal().toFixed(2) + ' ريال');
        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
    });

    $('#notesBtn').on('click', function() {
        const notesModal = new bootstrap.Modal(document.getElementById('notesModal'));
        notesModal.show();
    });

    $('#tableBtn').on('click', function(e) {
        e.preventDefault();
        const tableModal = new bootstrap.Modal(document.getElementById('tableModal'));
        tableModal.show();
    });

    $('#pendingTransactionsBtn').on('click', function() {
        const pendingModal = new bootstrap.Modal(document.getElementById('pendingTransactionsModal'));
        pendingModal.show();
    });

    // Table Selection
    $('.table-btn').on('click', function() {
        selectedTable = $(this).data('table');
        $('.table-btn').removeClass('btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
    });

    // Payment Method Change
    $('#paymentMethod').on('change', function() {
        const method = $(this).val();
        if (method === 'cash') {
            $('#cashAmountDiv').show();
            $('#cardAmountDiv').hide();
        } else if (method === 'card') {
            $('#cashAmountDiv').hide();
            $('#cardAmountDiv').show();
        } else {
            $('#cashAmountDiv').show();
            $('#cardAmountDiv').show();
        }
    });

    // Calculate Change
    $('#cashAmount, #cardAmount').on('input', function() {
        calculateChange();
    });

    function calculateChange() {
        const total = calculateTotal();
        const cash = parseFloat($('#cashAmount').val() || 0);
        const card = parseFloat($('#cardAmount').val() || 0);
        const paid = cash + card;
        const change = paid - total;

        if (change > 0) {
            $('#changeAmount').text(change.toFixed(2));
            $('#changeAmountDiv').show();
        } else {
            $('#changeAmountDiv').hide();
        }
    }

    // Notes Save
    $('#notesModal .btn-primary').on('click', function() {
        invoiceNotes = $('#invoiceNotes').val();
    });
