# 📡 Offline POS - API Documentation

## Base URL

```
https://{tenant}.yourdomain.com/api/offline-pos
```

**Authentication:** Bearer Token (Sanctum)

---

## 📋 **Table of Contents**

1. [Init Data API](#init-data-api)
2. [Sync API](#sync-api)
3. [Reports API](#reports-api)
4. [Return Invoice API](#return-invoice-api)

---

## 🔄 **Init Data API**

### 1. Get Initial Data

جلب جميع البيانات المطلوبة للعمل offline.

**Endpoint:** `GET /init-data`

**Headers:**
```http
Authorization: Bearer {token}
X-Branch-ID: {branch_id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "items": [...],
    "customers": [...],
    "stores": [...],
    "employees": [...],
    "cash_boxes": [...],
    "user": {...},
    "settings": {...},
    "categories": [...],
    "price_types": [...]
  },
  "metadata": {
    "total_items": 1500,
    "total_customers": 300,
    "branch_id": 1,
    "timestamp": "2026-01-20T12:00:00.000000Z",
    "version": "1.0.0",
    "execution_time_ms": 1250.5
  }
}
```

### 2. Check for Updates

التحقق من وجود تحديثات دون تحميل كل البيانات.

**Endpoint:** `GET /init-data/check-updates`

**Query Parameters:**
- `last_sync` (optional): timestamp of last sync

**Response:**
```json
{
  "success": true,
  "has_updates": true,
  "updated_sections": ["items", "customers"],
  "message": "Updates available."
}
```

### 3. Get Specific Section

تحميل قسم محدد فقط.

**Endpoint:** `GET /init-data/section/{section}`

**Sections:** `items`, `customers`, `stores`, `employees`, `cash_boxes`, `categories`

**Response:**
```json
{
  "success": true,
  "section": "items",
  "data": [...],
  "message": "Items data loaded successfully."
}
```

---

## 🔁 **Sync API**

### 1. Sync Single Transaction

مزامنة معاملة واحدة.

**Endpoint:** `POST /sync-transaction`

**Request Body:**
```json
{
  "local_id": "uuid-xxxx-xxxx-xxxx",
  "transaction": {
    "transaction_type": "sale",
    "date": "2026-01-20 14:30:00",
    "customer_id": 61,
    "store_id": 62,
    "employee_id": 65,
    "cash_box_id": 59,
    "items": [
      {
        "item_id": 100,
        "unit_id": 1,
        "quantity": 2,
        "price": 50,
        "discount": 5
      }
    ],
    "subtotal": 95,
    "discount_value": 10,
    "total": 85,
    "payment_method": "cash",
    "paid_amount": 100,
    "change_amount": 15
  }
}
```

**Response Success:**
```json
{
  "success": true,
  "message": "Transaction synced successfully.",
  "data": {
    "server_transaction_id": 1234,
    "invoice_number": "SALE-2026-001234",
    "created_at": "2026-01-20T14:30:15.000000Z"
  }
}
```

**Response Error:**
```json
{
  "success": false,
  "message": "Failed to sync transaction.",
  "error": "Insufficient stock for item #100"
}
```

### 2. Batch Sync

مزامنة جماعية (حتى 50 معاملة).

**Endpoint:** `POST /batch-sync`

**Request Body:**
```json
{
  "transactions": [
    {
      "local_id": "uuid-1",
      "transaction": {...}
    },
    {
      "local_id": "uuid-2",
      "transaction": {...}
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Batch sync completed.",
  "results": [
    {
      "local_id": "uuid-1",
      "status": "synced",
      "server_id": 1234,
      "error": null
    },
    {
      "local_id": "uuid-2",
      "status": "failed",
      "server_id": null,
      "error": "Insufficient stock"
    }
  ],
  "summary": {
    "total": 2,
    "synced": 1,
    "failed": 1,
    "already_synced": 0
  }
}
```

### 3. Check Sync Status

التحقق من حالة معاملة معينة.

**Endpoint:** `GET /sync-status/{localId}`

**Response:**
```json
{
  "success": true,
  "data": {
    "local_id": "uuid-xxxx",
    "server_id": 1234,
    "status": "synced",
    "sync_attempts": 1,
    "last_sync_attempt": "2026-01-20T14:30:00Z",
    "synced_at": "2026-01-20T14:30:15Z",
    "error_message": null,
    "can_retry": false
  }
}
```

### 4. Retry Failed Sync

إعادة محاولة مزامنة معاملة فاشلة.

**Endpoint:** `POST /retry-sync/{localId}`

**Response:**
```json
{
  "success": true,
  "message": "Transaction synced successfully on retry.",
  "data": {
    "server_transaction_id": 1234
  }
}
```

### 5. Get Pending Transactions

جلب قائمة المعاملات المعلقة.

**Endpoint:** `GET /pending-transactions`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "local_transaction_id": "uuid-1",
      "created_at": "2026-01-20T10:00:00Z",
      "sync_attempts": 0
    }
  ],
  "count": 1
}
```

---

## 📊 **Reports API**

### 1. Best Sellers

أكثر الأصناف مبيعاً.

**Endpoint:** `GET /reports/best-sellers`

**Query Parameters:**
- `from_date` (optional): YYYY-MM-DD (default: start of month)
- `to_date` (optional): YYYY-MM-DD (default: today)
- `limit` (optional): number (default: 10)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 100,
      "name": "Product A",
      "code": "PRD-001",
      "total_quantity": 150,
      "total_value": 7500
    }
  ]
}
```

### 2. Top Customers

أفضل العملاء.

**Endpoint:** `GET /reports/top-customers`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 61,
      "name": "Customer A",
      "code": "CUST-001",
      "transaction_count": 25,
      "total_purchases": 15000
    }
  ]
}
```

### 3. Daily Sales

مبيعات يوم محدد.

**Endpoint:** `GET /reports/daily-sales`

**Query Parameters:**
- `date` (optional): YYYY-MM-DD (default: today)

**Response:**
```json
{
  "success": true,
  "data": {
    "date": "2026-01-20",
    "transaction_count": 45,
    "subtotal": 25000,
    "total_discount": 1000,
    "total_sales": 24000,
    "total_paid": 20000,
    "items_sold": 120
  }
}
```

### 4. Sales Summary

ملخص المبيعات لفترة.

**Endpoint:** `GET /reports/sales-summary`

**Query Parameters:**
- `from_date` (optional)
- `to_date` (optional)

**Response:**
```json
{
  "success": true,
  "data": {
    "period": {
      "from": "2026-01-01",
      "to": "2026-01-20"
    },
    "total_transactions": 500,
    "total_subtotal": 250000,
    "total_discount": 10000,
    "total_sales": 240000,
    "total_paid": 200000,
    "average_transaction": 480
  }
}
```

---

## 🔙 **Return Invoice API**

### Create Return Invoice

إنشاء فاتورة مرتجعة.

**Endpoint:** `POST /return-invoice`

**Request Body:**
```json
{
  "original_invoice_id": 1234,
  "return_items": [
    {
      "item_id": 100,
      "quantity": 1,
      "price": 50,
      "reason": "تالف"
    }
  ],
  "notes": "إرجاع بسبب عيب في المنتج"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Return invoice created successfully.",
  "data": {
    "return_invoice_id": 1235,
    "invoice_number": "RET-2026-001235"
  }
}
```

---

## 🔐 **Authentication**

### Required Headers

```http
Authorization: Bearer {sanctum_token}
X-Branch-ID: {branch_id}
Content-Type: application/json
Accept: application/json
```

### Getting Token

```javascript
// Login first
const response = await fetch('/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'user@example.com',
    password: 'password'
  })
});

const { token } = await response.json();

// Use token in subsequent requests
const data = await fetch('/api/offline-pos/init-data', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'X-Branch-ID': '1'
  }
});
```

---

## ⚠️ **Error Responses**

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "You do not have permission to perform this action.",
  "required_permission": "sync offline pos transactions"
}
```

### 422 Validation Error
```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "transaction.total": ["The total field is required."]
  }
}
```

### 500 Server Error
```json
{
  "success": false,
  "message": "Failed to sync transaction.",
  "error": "Internal server error"
}
```

---

## 🚀 **Rate Limiting**

- **InitData:** 10 requests/minute
- **Sync:** 60 requests/minute
- **Reports:** 30 requests/minute

---

## 📝 **Best Practices**

1. **Always include X-Branch-ID** في جميع الطلبات
2. **Cache InitData locally** لتقليل الطلبات
3. **Use batch sync** للمزامنة الجماعية
4. **Check sync status** قبل إعادة المحاولة
5. **Handle errors gracefully** وأعد المحاولة مع backoff

---

## 🔧 **Testing**

### Using cURL

```bash
# Get init data
curl -X GET "https://tenant1.yourdomain.com/api/offline-pos/init-data" \
  -H "Authorization: Bearer {token}" \
  -H "X-Branch-ID: 1"

# Sync transaction
curl -X POST "https://tenant1.yourdomain.com/api/offline-pos/sync-transaction" \
  -H "Authorization: Bearer {token}" \
  -H "X-Branch-ID: 1" \
  -H "Content-Type: application/json" \
  -d '{
    "local_id": "test-uuid-123",
    "transaction": {
      "transaction_type": "sale",
      ...
    }
  }'
```

### Using JavaScript

```javascript
const API_BASE = 'https://tenant1.yourdomain.com/api/offline-pos';
const TOKEN = 'your-sanctum-token';
const BRANCH_ID = 1;

async function getInitData() {
  const response = await fetch(`${API_BASE}/init-data`, {
    headers: {
      'Authorization': `Bearer ${TOKEN}`,
      'X-Branch-ID': BRANCH_ID
    }
  });
  
  return await response.json();
}

async function syncTransaction(localId, transaction) {
  const response = await fetch(`${API_BASE}/sync-transaction`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${TOKEN}`,
      'X-Branch-ID': BRANCH_ID,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      local_id: localId,
      transaction: transaction
    })
  });
  
  return await response.json();
}
```

---

**Version:** 1.0.0  
**Last Updated:** 2026-01-20
