/**
 * TravelCurator Elementor Widgets JavaScript
 */

jQuery(document).ready(function($) {
    
    // Initialize all widgets
    initPackagesGrid();
    initPackageDetails();
    initSearchFilter();
    initLeadForm();
    initWishlist();
    
    /**
     * Packages Grid Widget Functions
     */
    function initPackagesGrid() {
        // Filter functionality
        $('.filter-tab').on('click', function() {
            var $this = $(this);
            var filter = $this.data('filter');
            var $container = $this.closest('.travelcurator-packages-grid');
            var $cards = $container.find('.package-card');
            
            // Update active tab
            $this.siblings().removeClass('active');
            $this.addClass('active');
            
            // Filter cards
            if (filter === 'all') {
                $cards.fadeIn(300);
            } else {
                $cards.each(function() {
                    var $card = $(this);
                    if ($card.hasClass('filter-' + filter)) {
                        $card.fadeIn(300);
                    } else {
                        $card.fadeOut(300);
                    }
                });
            }
        });
        
        // Card hover effects
        $('.package-card').hover(
            function() {
                $(this).find('.card-img').addClass('hover-scale');
            },
            function() {
                $(this).find('.card-img').removeClass('hover-scale');
            }
        );
    }
    
    /**
     * Package Details Widget Functions
     */
    function initPackageDetails() {
        // Lightbox functionality
        $(document).on('click', '.gallery-item', function() {
            var imageSrc = $(this).find('img').attr('src');
            openLightbox(imageSrc);
        });
        
        // Smooth scroll to sections
        $('.detail-navigation a').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            $('html, body').animate({
                scrollTop: $(target).offset().top - 100
            }, 800);
        });
    }
    
    /**
     * Search Filter Widget Functions
     */
    function initSearchFilter() {
        // Price range slider
        $('.price-slider input[type="range"]').on('input', function() {
            var value = parseInt($(this).val());
            var formattedValue = value.toLocaleString('pt-BR');
            $(this).siblings('.price-display').find('span').text(formattedValue);
        });
        
        // Difficulty radio buttons styling
        $('.difficulty-option input[type="radio"]').on('change', function() {
            var $container = $(this).closest('.difficulty-options');
            $container.find('.difficulty-label').removeClass('selected');
            if (this.checked) {
                $(this).siblings('.difficulty-label').addClass('selected');
            }
        });
        
        // Auto-complete for destinations (if implemented)
        $('.destination-autocomplete').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: travelcurator_ajax.ajax_url,
                    dataType: 'json',
                    data: {
                        action: 'travelcurator_search_destinations',
                        term: request.term,
                        nonce: travelcurator_ajax.nonce
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $(this).val(ui.item.value);
                return false;
            }
        });
        
        // Form validation
        $('.travel-search-form').on('submit', function(e) {
            var hasFilters = false;
            
            // Check if any filters are selected
            $(this).find('input, select').each(function() {
                if ($(this).val() && $(this).val() !== '') {
                    hasFilters = true;
                    return false;
                }
            });
            
            if (!hasFilters) {
                e.preventDefault();
                alert('Por favor, selecione pelo menos um filtro para buscar.');
                return false;
            }
        });
    }
    
    /**
     * Lead Form Widget Functions
     */
    function initLeadForm() {
        // Phone number formatting
        $('.phone-mask').on('input', function() {
            var value = this.value.replace(/\D/g, '');
            var formattedValue = '';
            
            if (value.length <= 11) {
                if (value.length <= 10) {
                    formattedValue = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
                } else {
                    formattedValue = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                }
            }
            
            this.value = formattedValue;
        });
        
        // CPF formatting (if needed)
        $('.cpf-mask').on('input', function() {
            var value = this.value.replace(/\D/g, '');
            var formattedValue = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            this.value = formattedValue;
        });
        
        // Form validation
        $('.travel-lead-form').on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $submitBtn = $form.find('.btn-submit');
            var $btnText = $submitBtn.find('.btn-text');
            var $btnLoading = $submitBtn.find('.btn-loading');
            
            // Basic validation
            var isValid = true;
            var errorMessages = [];
            
            $form.find('input[required], select[required], textarea[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    var label = $(this).siblings('label').text().replace('*', '').trim();
                    errorMessages.push(label + ' é obrigatório');
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });
            
            // Email validation
            var emailField = $form.find('input[type="email"]');
            if (emailField.val() && !isValidEmail(emailField.val())) {
                isValid = false;
                errorMessages.push('E-mail inválido');
                emailField.addClass('error');
            }
            
            // Phone validation
            var phoneField = $form.find('input[type="tel"]');
            if (phoneField.val() && phoneField.val().length < 14) {
                isValid = false;
                errorMessages.push('Telefone inválido');
                phoneField.addClass('error');
            }
            
            if (!isValid) {
                alert('Por favor, corrija os seguintes erros:\n\n• ' + errorMessages.join('\n• '));
                return false;
            }
            
            // Show loading state
            $btnText.hide();
            $btnLoading.show();
            $submitBtn.prop('disabled', true);
            
            // Submit form via AJAX
            $.ajax({
                url: travelcurator_ajax.ajax_url,
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSuccessMessage($form, response.data.message || 'Solicitação enviada com sucesso!');
                        $form[0].reset();
                        
                        // Track conversion events
                        trackConversion('lead_submitted', {
                            form_id: $form.find('input[name="form_id"]').val(),
                            package_id: $form.find('input[name="package_id"]').val()
                        });
                        
                    } else {
                        showErrorMessage($form, response.data || 'Erro ao enviar solicitação.');
                    }
                },
                error: function() {
                    showErrorMessage($form, 'Erro de conexão. Tente novamente.');
                },
                complete: function() {
                    // Reset button state
                    $btnText.show();
                    $btnLoading.hide();
                    $submitBtn.prop('disabled', false);
                }
            });
        });
        
        // Real-time validation feedback
        $('.travel-lead-form input, .travel-lead-form select, .travel-lead-form textarea').on('blur', function() {
            validateField($(this));
        });
    }
    
    /**
     * Wishlist Functions
     */
    function initWishlist() {
        // Load wishlist state from localStorage
        loadWishlistState();
        
        // Handle wishlist toggle
        $(document).on('click', '.wishlist-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $btn = $(this);
            var $icon = $btn.find('i');
                            var packageId = getPackageIdFromCard($btn);
                
                if (wishlist.indexOf(parseInt(packageId)) !== -1) {
                    var $icon = $btn.find('i');
                    $icon.removeClass('far').addClass('fas');
                    $btn.addClass('active');
                }
            });
        } catch (e) {
            console.warn('Error loading wishlist state:', e);
        }
    }
    
    function getPackageIdFromCard($element) {
        // Try to get package ID from various sources
        var $card = $element.closest('.package-card');
        
        // Check data attribute
        if ($card.data('package-id')) {
            return $card.data('package-id');
        }
        
        // Check hidden input
        var packageInput = $card.find('input[name="package_id"]');
        if (packageInput.length) {
            return packageInput.val();
        }
        
        // Check in nearby form or data
        var $form = $element.closest('form');
        if ($form.length) {
            var formPackageInput = $form.find('input[name="package_id"]');
            if (formPackageInput.length) {
                return formPackageInput.val();
            }
        }
        
        // Try to extract from URL or link
        var $link = $card.find('a[href*="/travel-package/"]');
        if ($link.length) {
            var href = $link.attr('href');
            var matches = href.match(/travel-package\/(\d+)/);
            if (matches) {
                return matches[1];
            }
        }
        
        return null;
    }
    
    function addToWishlist(packageId) {
        try {
            var wishlist = JSON.parse(localStorage.getItem('travelcurator_wishlist') || '[]');
            packageId = parseInt(packageId);
            
            if (wishlist.indexOf(packageId) === -1) {
                wishlist.push(packageId);
                localStorage.setItem('travelcurator_wishlist', JSON.stringify(wishlist));
            }
            
            // Send to server (optional)
            $.post(travelcurator_ajax.ajax_url, {
                action: 'travelcurator_add_to_wishlist',
                package_id: packageId,
                nonce: travelcurator_ajax.nonce
            });
            
        } catch (e) {
            console.warn('Error adding to wishlist:', e);
        }
    }
    
    function removeFromWishlist(packageId) {
        try {
            var wishlist = JSON.parse(localStorage.getItem('travelcurator_wishlist') || '[]');
            packageId = parseInt(packageId);
            
            var index = wishlist.indexOf(packageId);
            if (index !== -1) {
                wishlist.splice(index, 1);
                localStorage.setItem('travelcurator_wishlist', JSON.stringify(wishlist));
            }
            
            // Send to server (optional)
            $.post(travelcurator_ajax.ajax_url, {
                action: 'travelcurator_remove_from_wishlist',
                package_id: packageId,
                nonce: travelcurator_ajax.nonce
            });
            
        } catch (e) {
            console.warn('Error removing from wishlist:', e);
        }
    }
    
    function trackEvent(eventName, data) {
        // Google Analytics 4
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, {
                custom_parameter_1: data.package_id || '',
                custom_parameter_2: data.form_id || '',
                custom_parameter_3: data.action || ''
            });
        }
        
        // Facebook Pixel
        if (typeof fbq !== 'undefined') {
            fbq('trackCustom', eventName, data);
        }
        
        // Send to server for tracking
        $.post(travelcurator_ajax.ajax_url, {
            action: 'travelcurator_track_event',
            event_name: eventName,
            event_data: JSON.stringify(data),
            nonce: travelcurator_ajax.nonce
        });
    }
    
    function trackConversion(conversionType, data) {
        // Google Ads Conversion
        if (typeof gtag !== 'undefined') {
            gtag('event', 'conversion', {
                'send_to': 'AW-CONVERSION_ID/CONVERSION_LABEL', // Replace with actual conversion ID
                'value': 1.0,
                'currency': 'BRL',
                'transaction_id': data.form_id + '_' + Date.now()
            });
        }
        
        // Facebook Pixel Lead Event
        if (typeof fbq !== 'undefined') {
            fbq('track', 'Lead', {
                content_name: data.package_id || 'Travel Package',
                content_category: 'Travel',
                value: 1,
                currency: 'BRL'
            });
        }
        
        // Google Analytics Enhanced Ecommerce (optional)
        if (typeof gtag !== 'undefined') {
            gtag('event', 'generate_lead', {
                currency: 'BRL',
                value: 1,
                items: [{
                    item_id: data.package_id || 'unknown',
                    item_name: 'Travel Package Lead',
                    category: 'Travel',
                    quantity: 1,
                    price: 1
                }]
            });
        }
        
        trackEvent(conversionType, data);
    }
    
    // Initialize modal functionality
    window.openLeadModal = function(packageId) {
        var $modal = $('#leadModal, .travelcurator-modal:has(.lead-form)').first();
        
        if ($modal.length) {
            // Set package ID if provided
            if (packageId) {
                $modal.find('input[name="package_id"]').val(packageId);
                
                // Update modal title with package name (optional)
                $.post(travelcurator_ajax.ajax_url, {
                    action: 'travelcurator_get_package_name',
                    package_id: packageId,
                    nonce: travelcurator_ajax.nonce
                }, function(response) {
                    if (response.success) {
                        $modal.find('.form-title').text('Solicitar Orçamento - ' + response.data);
                    }
                });
            }
            
            $modal.fadeIn(300);
            $('body').css('overflow', 'hidden');
            
            // Track modal open
            trackEvent('lead_modal_opened', { package_id: packageId || '' });
        }
    };
    
    window.closeLeadModal = function() {
        var $modal = $('#leadModal, .travelcurator-modal:has(.lead-form)');
        $modal.fadeOut(300);
        $('body').css('overflow', 'auto');
    };
    
    // Close modal on outside click
    $(document).on('click', '.travelcurator-modal', function(e) {
        if (e.target === this) {
            $(this).fadeOut(300);
            $('body').css('overflow', 'auto');
        }
    });
    
    // Close modal on ESC key
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) { // ESC key
            $('.travelcurator-modal:visible').fadeOut(300);
            $('body').css('overflow', 'auto');
        }
    });
    
    // Lazy loading for images
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(function(img) {
                imageObserver.observe(img);
            });
        } else {
            // Fallback for older browsers
            $('img[data-src]').each(function() {
                $(this).attr('src', $(this).data('src')).removeClass('lazy');
            });
        }
    }
    
    // Initialize lazy loading
    initLazyLoading();
    
    // Smooth animations on scroll
    function initScrollAnimations() {
        if ('IntersectionObserver' in window) {
            var animationObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated', 'fadeInUp');
                        animationObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            document.querySelectorAll('.package-card, .detail-section').forEach(function(el) {
                animationObserver.observe(el);
            });
        }
    }
    
    // Initialize scroll animations
    initScrollAnimations();
    
    // Accessibility improvements
    function initAccessibility() {
        // Add ARIA labels to buttons without text
        $('.wishlist-btn').attr('aria-label', 'Adicionar aos favoritos');
        $('.filter-tab').each(function() {
            $(this).attr('aria-label', 'Filtrar por ' + $(this).text());
        });
        
        // Keyboard navigation for custom elements
        $('.wishlist-btn, .filter-tab').attr('tabindex', '0');
        
        $(document).on('keydown', '.wishlist-btn, .filter-tab', function(e) {
            if (e.keyCode === 13 || e.keyCode === 32) { // Enter or Space
                e.preventDefault();
                $(this).click();
            }
        });
        
        // Focus management for modals
        $(document).on('shown.modal', '.travelcurator-modal', function() {
            $(this).find('input:first').focus();
        });
    }
    
    // Initialize accessibility features
    initAccessibility();
    
    // Performance optimization: Debounce function
    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }
    
    // Debounced search function
    var debouncedSearch = debounce(function() {
        // Implement real-time search if needed
        console.log('Performing search...');
    }, 300);
    
    // Error handling for AJAX requests
    $(document).ajaxError(function(event, xhr, settings, error) {
        if (settings.url.indexOf(travelcurator_ajax.ajax_url) !== -1) {
            console.error('TravelCurator AJAX Error:', error);
            
            // Show user-friendly error message
            var errorMessage = 'Ocorreu um erro. Por favor, recarregue a página e tente novamente.';
            
            if (xhr.status === 0) {
                errorMessage = 'Erro de conexão. Verifique sua internet.';
            } else if (xhr.status === 404) {
                errorMessage = 'Recurso não encontrado.';
            } else if (xhr.status === 500) {
                errorMessage = 'Erro interno do servidor.';
            }
            
            // You could show a toast notification here instead of alert
            console.warn(errorMessage);
        }
    });
    
    // Initialize tooltips if using a tooltip library
    if ($.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    // Initialize any other third-party plugins
    if ($.fn.select2) {
        $('.select2-enable').select2({
            theme: 'bootstrap4',
            placeholder: 'Selecione...',
            allowClear: true
        });
    }
    
});

// Global functions that might be called from PHP/other scripts
window.TravelCuratorWidgets = {
    openLeadModal: window.openLeadModal,
    closeLeadModal: window.closeLeadModal,
    closeLightbox: window.closeLightbox,
    
    // Public API for other scripts
    addToWishlist: function(packageId) {
        // This would be called from external scripts
        var $btn = $('.wishlist-btn[data-package-id="' + packageId + '"]');
        if ($btn.length) {
            $btn.click();
        }
    },
    
    openPackageModal: function(packageId) {
        // Open a quick view modal for a package
        $.post(travelcurator_ajax.ajax_url, {
            action: 'travelcurator_get_package_quick_view',
            package_id: packageId,
            nonce: travelcurator_ajax.nonce
        }, function(response) {
            if (response.success) {
                var modalHtml = `
                    <div class="travelcurator-modal package-quick-view">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            ${response.data}
                        </div>
                    </div>
                `;
                
                $('body').append(modalHtml);
                $('.package-quick-view').fadeIn(300);
                $('body').css('overflow', 'hidden');
            }
        });
    }
};
            
            if (!packageId) return;
            
            // Toggle visual state
            if ($icon.hasClass('far')) {
                $icon.removeClass('far').addClass('fas');
                $btn.addClass('active');
                addToWishlist(packageId);
            } else {
                $icon.removeClass('fas').addClass('far');
                $btn.removeClass('active');
                removeFromWishlist(packageId);
            }
            
            // Add animation
            $btn.addClass('wishlist-animation');
            setTimeout(function() {
                $btn.removeClass('wishlist-animation');
            }, 600);
            
            // Track event
            trackEvent('wishlist_toggle', {
                package_id: packageId,
                action: $btn.hasClass('active') ? 'add' : 'remove'
            });
        });
    }
    
    /**
     * Utility Functions
     */
    
    function openLightbox(imageSrc) {
        var lightboxHtml = `
            <div class="travelcurator-lightbox" onclick="closeLightbox(event)">
                <span class="lightbox-close">&times;</span>
                <img class="lightbox-content" src="${imageSrc}" alt="Imagem ampliada">
            </div>
        `;
        
        $('body').append(lightboxHtml);
        $('body').css('overflow', 'hidden');
        
        // Animate in
        $('.travelcurator-lightbox').hide().fadeIn(300);
    }
    
    window.closeLightbox = function(event) {
        if (event && event.target !== event.currentTarget) return;
        
        $('.travelcurator-lightbox').fadeOut(300, function() {
            $(this).remove();
            $('body').css('overflow', 'auto');
        });
    };
    
    function isValidEmail(email) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function validateField($field) {
        var isValid = true;
        var value = $field.val();
        var fieldType = $field.attr('type') || $field.prop('tagName').toLowerCase();
        
        // Required validation
        if ($field.prop('required') && !value) {
            isValid = false;
        }
        
        // Type-specific validation
        switch (fieldType) {
            case 'email':
                if (value && !isValidEmail(value)) {
                    isValid = false;
                }
                break;
            case 'tel':
                if (value && value.length < 14) {
                    isValid = false;
                }
                break;
        }
        
        // Update field appearance
        if (isValid) {
            $field.removeClass('error').addClass('valid');
        } else {
            $field.removeClass('valid').addClass('error');
        }
        
        return isValid;
    }
    
    function showSuccessMessage($form, message) {
        var formId = $form.find('input[name="form_id"]').val();
        var $successDiv = $(`#success-message-${formId}`);
        
        if ($successDiv.length) {
            $successDiv.find('p').text(message);
            $form.hide();
            $successDiv.fadeIn();
            
            // Scroll to message
            $('html, body').animate({
                scrollTop: $successDiv.offset().top - 100
            }, 500);
        } else {
            alert(message);
        }
    }
    
    function showErrorMessage($form, message) {
        var formId = $form.find('input[name="form_id"]').val();
        var $errorDiv = $(`#error-message-${formId}`);
        
        if ($errorDiv.length) {
            $errorDiv.find('.error-content p').text(message);
            $errorDiv.fadeIn();
            
            // Scroll to message
            $('html, body').animate({
                scrollTop: $errorDiv.offset().top - 100
            }, 500);
            
            // Hide after 5 seconds
            setTimeout(function() {
                $errorDiv.fadeOut();
            }, 5000);
        } else {
            alert(message);
        }
    }
    
    function loadWishlistState() {
        try {
            var wishlist = JSON.parse(localStorage.getItem('travelcurator_wishlist') || '[]');
            
            $('.wishlist-btn').each(function() {
                var $btn = $(this);
                var packageId = getPackage