/**
 * Booking Management System
 * public/assets/common/js/booking.js
 */

const BookingSystem = {
    config: {
        currencySymbol: '₹',
        decimalSeparator: '.',
        thousandSeparator: ',',
    },

    state: {
        rooms: [], // { id, name, price, discount, quantity, extraServices: [] }
        coupon: null,
        checkIn: '',
        checkOut: '',
        nights: 1,
        hotelId: null,
        taxPercent: 18,
        taxCalculationType: 'exclusive',
    },

    /**
     * Initialize booking system
     */
    initBooking(config = {}) {
        this.config = { ...this.config, ...config };
        this.state.rooms = [];
        this.state.coupon = null;
        if (config.checkIn) this.state.checkIn = config.checkIn;
        if (config.checkOut) this.state.checkOut = config.checkOut;
        if (config.nights) this.state.nights = parseInt(config.nights) || 1;
        if (config.hotelId) this.state.hotelId = config.hotelId;
        if (config.taxPercent !== undefined) this.state.taxPercent = parseFloat(config.taxPercent);
        if (config.cgstRate !== undefined) this.state.cgstRate = parseFloat(config.cgstRate);
        if (config.sgstRate !== undefined) this.state.sgstRate = parseFloat(config.sgstRate);
        if (config.taxCalculationType !== undefined) this.state.taxCalculationType = config.taxCalculationType;

        console.log('Booking System Initialized with stay:', this.state.checkIn, 'to', this.state.checkOut, '(', this.state.nights, 'nights)', 'GST:', this.state.taxPercent + '%');

        // Reset sidebar on initial load
        this.renderBookingSummary();
    },

    /**
     * Reset all booking selections
     */
    resetBooking() {
        this.state.rooms = [];
        this.state.coupon = null;
        this.renderBookingSummary();
    },

    /**
     * Update room selection (quantity)
     */
    updateRoomSelection(roomId, roomName, price, discount, quantity) {
        quantity = parseInt(quantity) || 0;
        const index = this.state.rooms.findIndex(r => r.id === roomId);

        if (quantity <= 0) {
            if (index !== -1) this.state.rooms.splice(index, 1);
        } else {
            const roomData = {
                id: roomId,
                name: roomName,
                price: parseFloat(price) || 0,
                discount: parseFloat(discount || 0) || 0,
                quantity: quantity,
                extraServices: this.state.rooms[index]?.extraServices || []
            };

            if (index === -1) {
                this.state.rooms.push(roomData);
            } else {
                this.state.rooms[index] = roomData;
            }
        }

        this.renderBookingSummary();
    },

    /**
     * Update extra services for a specific room
     */
    updateExtraServices(roomId, serviceId, serviceName, servicePrice, isChecked) {
        const room = this.state.rooms.find(r => r.id === roomId);
        if (!room) return;

        if (isChecked) {
            // Prevent duplicates
            if (!room.extraServices.find(s => s.id === serviceId)) {
                room.extraServices.push({ 
                    id: serviceId, 
                    name: serviceName, 
                    price: parseFloat(servicePrice) || 0 
                });
            }
        } else {
            room.extraServices = room.extraServices.filter(s => s.id !== serviceId);
        }

        this.renderBookingSummary();
    },

    /**
     * Apply coupon code via AJAX
     */
    async applyCoupon(code) {
        code = (code || '').trim();
        if (!code) {
            return { success: false, message: 'Please enter a coupon code.' };
        }

        try {
            const totals = this.calculateBookingTotal();
            const params = new URLSearchParams({
                code: code,
                check_in: this.state.checkIn || '',
                hotel_id: this.state.hotelId || '',
                amount: totals.subtotal || 0
            });

            if (this.state.rooms.length > 0) {
                params.set('room_id', this.state.rooms[0].id);
                params.set('quantity', this.state.rooms[0].quantity);
            }

            const response = await fetch(`/api/coupons/validate?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();

            if (data.success && data.coupon) {
                this.state.coupon = data.coupon;
                this.renderBookingSummary();
                return { success: true, message: `Coupon "${data.coupon.code}" applied!` };
            } else {
                this.state.coupon = null;
                this.renderBookingSummary();
                return { success: false, message: data.message || 'Invalid or inapplicable coupon code' };
            }
        } catch (error) {
            console.error('Coupon Error:', error);
            return { success: false, message: 'Server error while validating coupon.' };
        }
    },

    /**
     * Remove applied coupon
     */
    async removeCoupon() {
        this.state.coupon = null;
        try {
            await fetch('/api/coupons/validate?code=__NONE__', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
        } catch (e) {
            console.error('Error clearing coupon session:', e);
        }
        this.renderBookingSummary();
        return { success: true, message: 'Coupon removed.' };
    },

    /**
     * Calculate all totals
     */
    calculateBookingTotal() {
        let roomOriginalTotal = 0;
        let roomDiscountTotal = 0;
        let roomSubtotal = 0;
        let extraSubtotal = 0;

        this.state.rooms.forEach(room => {
            const basePrice = parseFloat(room.price) || 0;
            const discountPercent = parseFloat(room.discount || 0);
            const discountAmountPerNight = basePrice * (discountPercent / 100);
            const discountedPrice = Math.max(0, basePrice - discountAmountPerNight);

            roomOriginalTotal += basePrice * room.quantity * this.state.nights;
            roomDiscountTotal += discountAmountPerNight * room.quantity * this.state.nights;
            roomSubtotal += discountedPrice * room.quantity * this.state.nights;

            (room.extraServices || []).forEach(service => {
                extraSubtotal += (parseFloat(service.price) || 0) * room.quantity;
            });
        });

        const subtotal = roomSubtotal + extraSubtotal;
        let couponDiscount = 0;

        if (this.state.coupon) {
            if (this.state.coupon.discount_type === 'percentage') {
                couponDiscount = subtotal * (parseFloat(this.state.coupon.discount_value) / 100);
            } else {
                couponDiscount = Math.min(subtotal, parseFloat(this.state.coupon.discount_value) || 0);
            }
        }

        const netSubtotal = Math.max(0, subtotal - couponDiscount);
        const taxRate = (this.state.taxPercent !== undefined && this.state.taxPercent !== null) 
            ? parseFloat(this.state.taxPercent) 
            : 18;
        const cgstRate = (this.state.cgstRate !== undefined && this.state.cgstRate !== null)
            ? parseFloat(this.state.cgstRate)
            : (taxRate / 2);
        const sgstRate = (this.state.sgstRate !== undefined && this.state.sgstRate !== null)
            ? parseFloat(this.state.sgstRate)
            : (taxRate / 2);
        const taxCalcType = this.state.taxCalculationType || 'exclusive';

        let taxAmount = 0;
        let cgstAmount = 0;
        let sgstAmount = 0;
        let total = netSubtotal;

        if (taxRate > 0 && netSubtotal > 0) {
            if (taxCalcType === 'inclusive') {
                taxAmount = netSubtotal - (netSubtotal / (1 + (taxRate / 100)));
                cgstAmount = taxAmount / 2;
                sgstAmount = taxAmount / 2;
                total = netSubtotal;
            } else {
                taxAmount = (netSubtotal * taxRate) / 100;
                cgstAmount = (netSubtotal * cgstRate) / 100;
                sgstAmount = (netSubtotal * sgstRate) / 100;
                total = netSubtotal + taxAmount;
            }
        }

        const totalDiscount = roomDiscountTotal + couponDiscount;

        return {
            roomOriginalTotal,
            roomDiscountTotal,
            roomSubtotal,
            extraSubtotal,
            subtotal,
            netSubtotal,
            couponDiscount,
            taxRate,
            cgstRate,
            sgstRate,
            taxAmount,
            cgstAmount,
            sgstAmount,
            taxCalcType,
            totalDiscount,
            total,
            itemCount: this.state.rooms.reduce((acc, r) => acc + (parseInt(r.quantity) || 0), 0)
        };
    },

    /**
     * Format currency value
     */
    formatCurrency(value) {
        const val = parseFloat(value) || 0;
        return this.config.currencySymbol + val.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    },

    /**
     * Render the booking summary sidebar
     */
    renderBookingSummary() {
        const summaryEl = document.getElementById('booking-summary');
        const formSection = document.getElementById('booking-form-section');
        const couponInput = document.getElementById('coupon-code');
        const couponMsgEl = document.getElementById('coupon-message');

        if (!summaryEl) return;

        const totals = this.calculateBookingTotal();

        if (totals.itemCount <= 0) {
            summaryEl.innerHTML = '';
            formSection?.classList.add('d-none');
            return;
        }

        formSection?.classList.remove('d-none');

        // Update coupon message box in sidebar
        if (couponMsgEl) {
            if (this.state.coupon) {
                couponMsgEl.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between p-2 rounded bg-success-subtle text-success mt-2">
                        <span class="fw-bold small"><i class="fas fa-check-circle me-1"></i> ${this.state.coupon.code}</span>
                        <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none fw-bold" onclick="removeCouponFromUI()">Remove</button>
                    </div>
                `;
                if (couponInput) couponInput.value = this.state.coupon.code;
            } else {
                if (!couponMsgEl.dataset.customMsg) {
                    couponMsgEl.innerHTML = '';
                }
            }
        }

        let html = '<div class="summary-items mb-3">';

        this.state.rooms.forEach(room => {
            const basePrice = parseFloat(room.price) || 0;
            const discount = parseFloat(room.discount || 0);
            const discountedPrice = basePrice - (basePrice * (discount / 100));

            html += `
                <div class="summary-line py-1 border-bottom d-flex justify-content-between">
                    <div>
                        <strong class="text-dark">${room.name}</strong> 
                        <span class="text-muted">(${room.quantity} room${room.quantity > 1 ? 's' : ''} × ${this.state.nights} night${this.state.nights > 1 ? 's' : ''})</span>
                    </div>
                    <span class="fw-bold text-dark">${this.formatCurrency(discountedPrice * room.quantity * this.state.nights)}</span>
                </div>
            `;
            (room.extraServices || []).forEach(s => {
                html += `
                    <div class="summary-line extra-small text-muted ps-2 py-1 d-flex justify-content-between">
                        <span>+ ${s.name} (${room.quantity}x)</span>
                        <span>${this.formatCurrency((parseFloat(s.price) || 0) * room.quantity)}</span>
                    </div>
                `;
            });
        });

        html += '</div>';

        // Breakdown lines
        html += `
            <div class="summary-breakdown small text-secondary mb-3">
                <div class="d-flex justify-content-between py-1">
                    <span>Stay Duration:</span>
                    <strong class="text-dark">${this.state.nights} Night(s)</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span>Rooms Subtotal:</span>
                    <span class="text-dark fw-semibold">${this.formatCurrency(totals.roomSubtotal)}</span>
                </div>
        `;

        if (totals.extraSubtotal > 0) {
            html += `
                <div class="d-flex justify-content-between py-1 text-secondary">
                    <span>Extra Services:</span>
                    <span class="text-dark fw-semibold">+${this.formatCurrency(totals.extraSubtotal)}</span>
                </div>
            `;
        }

        if (this.state.coupon && totals.couponDiscount > 0) {
            html += `
                <div class="d-flex justify-content-between py-1 text-success fw-bold">
                    <span><i class="fas fa-tag me-1"></i> Coupon (${this.state.coupon.code}):</span>
                    <span>-${this.formatCurrency(totals.couponDiscount)}</span>
                </div>
            `;
        }

        if (totals.taxAmount > 0) {
            html += `
                <div class="d-flex justify-content-between py-1 text-dark fw-semibold">
                    <span><i class="fas fa-receipt me-1 text-primary"></i> GST & Taxes (${totals.taxRate}%):</span>
                    <span>+${this.formatCurrency(totals.taxAmount)}</span>
                </div>
                <div class="d-flex justify-content-between py-1 text-muted ps-3" style="font-size: 0.82rem;">
                    <span>↳ Central GST (CGST ${totals.cgstRate}%):</span>
                    <span class="text-dark">+${this.formatCurrency(totals.cgstAmount)}</span>
                </div>
                <div class="d-flex justify-content-between py-1 text-muted ps-3" style="font-size: 0.82rem;">
                    <span>↳ State GST (SGST ${totals.sgstRate}%):</span>
                    <span class="text-dark">+${this.formatCurrency(totals.sgstAmount)}</span>
                </div>
            `;
        }

        if (totals.roomDiscountTotal > 0) {
            html += `
                <div class="d-flex justify-content-between py-1 text-success small fw-semibold border-top mt-1 pt-1">
                    <span><i class="fas fa-gift me-1"></i> Room Offer Savings:</span>
                    <span>-${this.formatCurrency(totals.roomDiscountTotal)}</span>
                </div>
            `;
        }

        html += `
            </div>
            <div class="summary-line total d-flex justify-content-between align-items-center p-3 rounded bg-light border mb-3">
                <span class="fw-bold text-dark">Total Amount</span>
                <span class="h4 fw-bold text-success mb-0">${this.formatCurrency(totals.total)}</span>
            </div>
            <button type="button" onclick="BookingSystem.proceedToCheckout()" class="btn btn-success w-100 py-2 fw-bold text-uppercase shadow-sm">
                Proceed to Checkout <i class="fas fa-arrow-right ms-1"></i>
            </button>
        `;

        summaryEl.innerHTML = html;
    },

    /**
     * Handle checkout redirect
     */
    proceedToCheckout() {
        const totals = this.calculateBookingTotal();
        const data = {
            rooms: this.state.rooms,
            coupon: this.state.coupon?.code || null,
            check_in: this.state.checkIn,
            check_out: this.state.checkOut,
            nights: this.state.nights,
            total: totals.total
        };

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/rooms/checkout-init';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }

        const dataInput = document.createElement('input');
        dataInput.type = 'hidden';
        dataInput.name = 'booking_data';
        dataInput.value = JSON.stringify(data);
        form.appendChild(dataInput);

        document.body.appendChild(form);
        form.submit();
    }
};

window.BookingSystem = BookingSystem;
