/**
 * POS Scale Integration
 * دعم الموازين الإلكترونية في نظام POS
 * يدعم Web Serial API و COM Port
 */

class POSScale {
    constructor() {
        this.port = null;
        this.reader = null;
        this.isConnected = false;
        this.weight = 0;
        this.unit = 'kg';
        this.onWeightReceived = null;
        this.config = {
            baudRate: 9600,
            dataBits: 8,
            stopBits: 1,
            parity: 'none',
            bufferSize: 64
        };
    }

    /**
     * الاتصال بالميزان عبر Web Serial API
     */
    async connect() {
        if (!('serial' in navigator)) {
            console.warn('Web Serial API غير مدعوم في هذا المتصفح');
            return false;
        }

        try {
            this.port = await navigator.serial.requestPort();
            await this.port.open(this.config);
            this.isConnected = true;
            console.log('تم الاتصال بالميزان');
            
            // بدء قراءة البيانات
            this.startReading();
            return true;
        } catch (error) {
            console.error('خطأ في الاتصال بالميزان:', error);
            this.isConnected = false;
            return false;
        }
    }

    /**
     * قطع الاتصال
     */
    async disconnect() {
        if (this.reader) {
            await this.reader.cancel();
            this.reader = null;
        }
        
        if (this.port) {
            await this.port.close();
            this.port = null;
        }
        
        this.isConnected = false;
        console.log('تم قطع الاتصال بالميزان');
    }

    /**
     * بدء قراءة البيانات من الميزان
     */
    async startReading() {
        if (!this.port || !this.isConnected) {
            return;
        }

        try {
            while (this.port.readable) {
                this.reader = this.port.readable.getReader();
                
                try {
                    while (true) {
                        const { value, done } = await this.reader.read();
                        
                        if (done) {
                            break;
                        }
                        
                        // تحويل البيانات إلى نص
                        const text = new TextDecoder().decode(value);
                        this.parseWeightData(text);
                    }
                } catch (error) {
                    console.error('خطأ في قراءة البيانات:', error);
                } finally {
                    this.reader.releaseLock();
                }
            }
        } catch (error) {
            console.error('خطأ في بدء القراءة:', error);
        }
    }

    /**
     * تحليل بيانات الوزن من الميزان
     * يدعم عدة تنسيقات شائعة للموازين
     */
    parseWeightData(data) {
        // إزالة المسافات والأحرف غير المرغوبة
        const cleanData = data.trim().replace(/[^\d.,-]/g, '');
        
        // محاولة استخراج الوزن من التنسيقات المختلفة
        // تنسيق 1: "1.250 kg" أو "1,250 kg"
        let match = cleanData.match(/(\d+[.,]\d+)\s*(kg|g|lb)/i);
        if (match) {
            this.weight = parseFloat(match[1].replace(',', '.'));
            this.unit = match[2].toLowerCase();
            this.notifyWeightReceived();
            return;
        }
        
        // تنسيق 2: "01250" (5 أرقام - الوزن بالجرام)
        match = cleanData.match(/^(\d{5})$/);
        if (match) {
            this.weight = parseFloat(match[1]) / 1000; // تحويل من جرام إلى كيلو
            this.unit = 'kg';
            this.notifyWeightReceived();
            return;
        }
        
        // تنسيق 3: "1.250" (رقم عشري مباشر)
        match = cleanData.match(/^(\d+[.,]\d+)$/);
        if (match) {
            this.weight = parseFloat(match[1].replace(',', '.'));
            this.unit = 'kg';
            this.notifyWeightReceived();
            return;
        }
        
        // تنسيق 4: ST,GS (EAN-13 مع الوزن)
        // مثال: 2001234567890 حيث 200 = الوزن (2.000 kg)
        match = cleanData.match(/^(\d{3})(\d{10})$/);
        if (match) {
            const weightCode = parseInt(match[1]);
            this.weight = weightCode / 100; // تحويل إلى كيلو
            this.unit = 'kg';
            this.notifyWeightReceived();
            return;
        }
    }

    /**
     * إشعار عند استلام الوزن
     */
    notifyWeightReceived() {
        if (this.onWeightReceived && typeof this.onWeightReceived === 'function') {
            this.onWeightReceived({
                weight: this.weight,
                unit: this.unit,
                formatted: this.formatWeight()
            });
        }
    }

    /**
     * تنسيق الوزن للعرض
     */
    formatWeight() {
        if (this.unit === 'g') {
            return this.weight.toFixed(0) + ' جرام';
        } else if (this.unit === 'kg') {
            return this.weight.toFixed(3) + ' كيلو';
        } else {
            return this.weight.toFixed(3) + ' ' + this.unit;
        }
    }

    /**
     * قراءة الوزن يدوياً (للموازين التي لا تدعم Serial)
     */
    readWeightManually() {
        return new Promise((resolve) => {
            const weight = prompt('أدخل الوزن بالكيلو:', '0.000');
            if (weight !== null) {
                this.weight = parseFloat(weight) || 0;
                this.unit = 'kg';
                resolve({
                    weight: this.weight,
                    unit: this.unit,
                    formatted: this.formatWeight()
                });
            } else {
                resolve(null);
            }
        });
    }

    /**
     * التحقق من دعم Web Serial API
     */
    static isSupported() {
        return 'serial' in navigator;
    }
}

// Export للاستخدام
window.POSScale = POSScale;
