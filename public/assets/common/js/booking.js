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
        nights: 1,
        taxPercent: 0,
    },

    /**
     * Initialize booking system
     */
    initBooking(config = {}) {
        this.config = { ...this.config, ...config };
        console.log('Booking System Initialized');

        // Initial render if elements exist
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
        if (!code) return;

        try {
            const response = await fetch(`/api/coupons/validate?code=${code}`);
            const data = await response.json();

            if (data.success) {
                this.state.coupon = data.coupon;
                this.renderBookingSummary();
                return { success: true, message: 'Coupon applied!' };
            } else {
                this.state.coupon = null;
                this.renderBookingSummary();
                return { success: false, message: data.message || 'Invalid coupon' };
            }
        } catch (error) {
            console.error('Coupon Error:', error);
            return { success: false, message: 'Server error' };
        }
    },

    /**
     * Calculate all totals
     */
    calculateBookingTotal() {
        let roomSubtotal = 0;
        let extraSubtotal = 0;

        this.state.rooms.forEach(room => {
            const price = parseFloat(room.price) || 0;
            const discount = parseFloat(room.discount) || 0;
            const discountedPrice = price - (price * (discount / 100));
            
            roomSubtotal += discountedPrice * room.quantity * this.state.nights;

            room.extraServices.forEach(service => {
                extraSubtotal += (parseFloat(service.price) || 0) * room.quantity;
            });
        });

        const subtotal = roomSubtotal + extraSubtotal;
        let couponDiscount = 0;

        if (this.state.coupon) {
            if (this.state.coupon.discount_type === 'percentage') {
                couponDiscount = subtotal * (this.state.coupon.discount_value / 100);
            } else {
                couponDiscount = parseFloat(this.state.coupon.discount_value) || 0;
            }
        }

        const total = Math.max(0, subtotal - couponDiscount);

        return {
            roomSubtotal,
            extraSubtotal,
            subtotal,
            couponDiscount,
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

        if (!summaryEl) return;

        const totals = this.calculateBookingTotal();

        if (totals.itemCount <= 0) {
            summaryEl.innerHTML = '';
            formSection?.classList.add('d-none');
            return;
        }

        formSection?.classList.remove('d-none');

        let html = '<div class="summary-items">';

        this.state.rooms.forEach(room => {
            const price = parseFloat(room.price) || 0;
            const discount = parseFloat(room.discount) || 0;
            const discountedPrice = price - (price * (discount / 100));

            html += `
                <div class="summary-line">
                    <span>${room.name} x ${room.quantity}</span>
                    <span class="fw-bold">${this.formatCurrency(discountedPrice * room.quantity * this.state.nights)}</span>
                </div>
            `;
            room.extraServices.forEach(s => {
                html += `
                    <div class="summary-line extra-small text-muted ps-2">
                        <span>+ ${s.name}</span>
                        <span>${this.formatCurrency((parseFloat(s.price) || 0) * room.quantity)}</span>
                    </div>
                `;
            });
        });

        html += `
            </div>
            <div class="summary-line total">
                <span>Total Amount</span>
                <span>${this.formatCurrency(totals.total)}</span>
            </div>
            <button onclick="BookingSystem.proceedToCheckout()" class="btn-confirm-booking">
                PROCEED TO CHECKOUT
            </button>
        `;

        summaryEl.innerHTML = html;
    },

    /**
     * Handle checkout redirect
     */
    proceedToCheckout() {
        const data = {
            rooms: this.state.rooms,
            coupon: this.state.coupon?.code,
            nights: this.state.nights,
            total: this.calculateBookingTotal().total
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
