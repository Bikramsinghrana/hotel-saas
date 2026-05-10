<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
    Dropzone.autoDiscover = false;

    // Toast and Error Utilities
    function showToast(message, icon = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({ icon, title: message });
    }

    function showError(message) {
        Swal.fire({ icon: 'error', title: 'Oops...', text: message });
    }

    // Hotel Wizard Logic
    function hotelWizard(hotel, step) {
        return {
            currentStep: step,
            hotelId: hotel ? hotel.id : null,
            loading: false,
            formData: {
                name: hotel?.name || '',
                description: hotel?.description || '',
                rating: hotel?.rating || 3,
                status: hotel?.status || 'draft',
                address_line: hotel?.address?.address_line || '',
                city: hotel?.address?.city || '',
                state: hotel?.address?.state || '',
                country: hotel?.address?.country || '',
                pincode: hotel?.address?.pincode || '',
                nearby_places: Array.isArray(hotel?.nearby) ? hotel.nearby : [],
                price_per_night: hotel?.base_price || 0,
                discount_percentage: hotel?.discount || 0,
                tax_percentage: hotel?.tax || 0,
                facilities: Array.isArray(hotel?.facilities) ? hotel.facilities : [],
                cancellation_type: hotel?.policies?.cancellation_type || 'free',
                cancel_before_days: hotel?.policies?.cancel_before_days || 0,
            },
            roomData: {
                room_type: '',
                total_rooms: 1,
                max_adults: 2,
                max_children: 0,
                price_per_day: 0,
            },
            rooms: Array.isArray(hotel?.rooms) ? hotel.rooms : [],
            uploadedMedia: Array.isArray(hotel?.media) ? hotel.media : [],
            dropzone: null,

            init() {
                this.initDropzone();
            },

            getStepTitle(i) {
                return ['General Info', 'Address', 'Pricing', 'Media', 'Policies'][i-1];
            },

            goToStep(i) {
                if (this.hotelId || i == 1) {
                    this.currentStep = i;
                    if (this.currentStep == 4 && !this.dropzone) {
                        this.$nextTick(() => this.initDropzone());
                    }
                } else {
                    showToast('Please complete Step 1 first', 'warning');
                }
            },

            addNearbyPlace() { this.formData.nearby_places.push(''); },
            removeNearbyPlace(index) { this.formData.nearby_places.splice(index, 1); },
            addFacility() { this.formData.facilities.push(''); },
            removeFacility(index) { this.formData.facilities.splice(index, 1); },

            initDropzone() {
                if (this.hotelId) {
                    this.dropzone = new Dropzone("#dropzone-media", {
                        url: `/admin/hotels/wizard/${this.hotelId}/media`,
                        params: { _token: '{{ csrf_token() }}', type: 'gallery' },
                        maxFilesize: 5,
                        acceptedFiles: "image/*",
                        success: (file, response) => {
                            if (response.success) {
                                this.uploadedMedia.push(response.media);
                                showToast('Media uploaded!');
                                this.dropzone.removeFile(file);
                            }
                        },
                        error: (file, message) => {
                            showToast(message.message || 'Upload failed', 'error');
                            this.dropzone.removeFile(file);
                        }
                    });
                }
            },

            async submitStep(step) {
                this.loading = true;
                const data = { ...this.formData, step, hotel_id: this.hotelId, _token: '{{ csrf_token() }}' };

                try {
                    const response = await fetch('{{ route("admin.hotels.wizard.store") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (result.success) {
                        showToast(result.message);
                        if (result.hotel_id) this.hotelId = result.hotel_id;
                        
                        if (step < 5) {
                            this.currentStep++;
                            if (this.currentStep == 4 && !this.dropzone) {
                                this.$nextTick(() => this.initDropzone());
                            }
                        } else {
                            showToast('Hotel activated successfully!');
                            setTimeout(() => { window.location.href = '{{ route("admin.hotels.index") }}'; }, 1500);
                        }
                    } else {
                        if (result.errors) {
                            let errorMsg = Object.values(result.errors).flat().join('<br>');
                            showError(errorMsg);
                        } else {
                            showError(result.message || 'Validation failed');
                        }
                    }
                } catch (e) {
                    showError('Something went wrong!');
                } finally {
                    this.loading = false;
                }
            },

            async deleteMedia(mediaId) {
                if (!confirm('Are you sure?')) return;
                try {
                    const response = await fetch(`/admin/hotels/wizard/${this.hotelId}/media/${mediaId}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.uploadedMedia = this.uploadedMedia.filter(m => m.id != mediaId);
                        showToast('Media deleted');
                    }
                } catch (e) {
                    showToast('Failed to delete media', 'error');
                }
            },

            async saveRoom() {
                if (!this.roomData.room_type || !this.roomData.price_per_day) {
                    showToast('Please fill room details', 'error');
                    return;
                }
                this.loading = true;
                try {
                    const response = await fetch(`/admin/hotels/wizard/${this.hotelId}/rooms`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(this.roomData)
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.rooms.push(result.room);
                        this.roomData = { room_type: '', total_rooms: 1, max_adults: 2, max_children: 0, price_per_day: 0 };
                        showToast('Room added!');
                    }
                } catch (e) {
                    showToast('Failed to add room', 'error');
                } finally {
                    this.loading = false;
                }
            },

            removeRoom(index) {
                this.rooms.splice(index, 1);
                showToast('Room removed from list');
            }
        };
    }

    // Room Wizard Logic
    function roomWizard(room, step) {
        return {
            currentStep: step,
            roomId: room ? room.id : null,
            loading: false,
            formData: {
                post_title: room?.post_title || '',
                room_slug: room?.room_slug || '',
                room_type_id: room?.room_type_id || '',
                status: room?.status || 'draft',
                post_content: room?.post_content || '',
                
                total_rooms: room?.total_rooms || 1,
                day: room?.day || 1,
                max_adults: room?.max_adults || 2,
                max_children: room?.max_children || 0,
                base_price: room?.base_price || 0,
                member_price: room?.member_price || 0,
                price_per_day: room?.price_per_day || 0,
                discount: room?.discount || 0,
                tax: room?.tax || 0,
                check_in: room?.check_in ? new Date(room.check_in).toISOString().split('T')[0] : '',
                check_out: room?.check_out ? new Date(room.check_out).toISOString().split('T')[0] : '',
                coupon: room?.coupon || '',
                accept_terms: room?.accept_terms || false,

                facilities: Array.isArray(room?.facilities) ? room.facilities : [],
                extra_services: Array.isArray(room?.extra_services) ? room.extra_services : [],
            },
            
            // For AJAX Terms Creation
            newTerm: {
                title: '',
                price: 0,
                price_type: '$',
                description: ''
            },

            uploadedMedia: Array.isArray(room?.gallery) ? room.gallery : [],
            dropzone: null,

            init() {
                this.initDropzone();
            },

            getStepTitle(i) {
                return ['General Info', 'Pricing & Capacity', 'Services & Media', 'Review'][i-1];
            },

            goToStep(i) {
                if (this.roomId || i == 1) {
                    this.currentStep = i;
                    if (this.currentStep == 3 && !this.dropzone) {
                        this.$nextTick(() => this.initDropzone());
                    }
                } else {
                    showToast('Please complete Step 1 first', 'warning');
                }
            },

            initDropzone() {
                if (this.roomId) {
                    this.dropzone = new Dropzone("#dropzone-room-media", {
                        url: `/admin/rooms/wizard/${this.roomId}/media`,
                        params: { _token: '{{ csrf_token() }}', type: 'gallery' },
                        maxFilesize: 5,
                        acceptedFiles: "image/*",
                        success: (file, response) => {
                            if (response.success) {
                                this.uploadedMedia.push(response.media);
                                showToast('Image uploaded!');
                                this.dropzone.removeFile(file);
                            }
                        },
                        error: (file, message) => {
                            showToast(message.message || 'Upload failed', 'error');
                            this.dropzone.removeFile(file);
                        }
                    });
                }
            },

            async saveTerm(type) {
                if (!this.newTerm.title) return;
                this.loading = true;
                try {
                    const response = await fetch('{{ route("admin.terms.store") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ ...this.newTerm, type })
                    });
                    const result = await response.json();
                    if (result.success) {
                        showToast('Created successfully!');
                        // Depending on UI, we might need to refresh the list or append
                        // For now, let's assume we just need to add the ID to selected array
                        if (type == 'facility') {
                            this.formData.facilities.push(result.term.id.toString());
                        } else if (type == 'extra_service') {
                            this.formData.extra_services.push(result.term.id.toString());
                        }
                        
                        // Reset modal data
                        this.newTerm = { title: '', price: 0, price_type: '$', description: '' };
                        
                        // Close modal (Bootstrap)
                        const modalEl = document.querySelector('.modal.show');
                        if (modalEl) {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }

                        // Trigger a reload or dynamic append of the checkbox list would be better
                        // But for simplicity in this wizard, we'll suggest refreshing or just append to state
                        window.location.reload(); // Simple way to refresh lists from DB
                    }
                } catch (e) {
                    showToast('Failed to create', 'error');
                } finally {
                    this.loading = false;
                }
            },

            async deleteMedia(mediaId) {
                if (!confirm('Are you sure?')) return;
                try {
                    const response = await fetch(`/admin/rooms/wizard/${this.roomId}/media/${mediaId}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.uploadedMedia = this.uploadedMedia.filter(m => m.id != mediaId);
                        showToast('Media deleted');
                    }
                } catch (e) {
                    showToast('Failed to delete media', 'error');
                }
            },

            async submitStep(step) {
                this.loading = true;
                // Ensure prices are numbers
                this.formData.price_per_day = this.formData.base_price; 
                
                const data = { ...this.formData, step, room_id: this.roomId, _token: '{{ csrf_token() }}' };

                try {
                    const response = await fetch('{{ route("admin.rooms.wizard.store") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (result.success) {
                        showToast(result.message);
                        if (result.room_id) this.roomId = result.room_id;
                        
                        if (step < 4) {
                            this.currentStep++;
                            if (this.currentStep == 3 && !this.dropzone) {
                                this.$nextTick(() => this.initDropzone());
                            }
                        } else {
                            showToast('Room saved successfully!');
                            setTimeout(() => { window.location.href = '{{ route("admin.rooms.index") }}'; }, 1500);
                        }
                    } else {
                        if (result.errors) {
                            let errorMsg = Object.values(result.errors).flat().join('<br>');
                            showError(errorMsg);
                        } else {
                            showError(result.message || 'Validation failed');
                        }
                    }
                } catch (e) {
                    showError('Something went wrong!');
                } finally {
                    this.loading = false;
                }
            }
        };
    }
</script>
