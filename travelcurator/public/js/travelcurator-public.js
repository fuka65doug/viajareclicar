/**
 * TravelCurator Public JavaScript
 * Funcionalidades interativas do frontend
 */

(function($) {
    'use strict';

    const TravelCuratorPublic = {
        init: function() {
            this.bindEvents();
            this.initComponents();
        },

        bindEvents: function() {
            // Tabs do single package
            $(document).on('click', '.tc-package-tab-nav a', this.handleTabClick);
            
            // Formulário de interesse
            $(document).on('submit', '.tc-interest-form', this.submitInterestForm);
            
            // Filtros de pacotes
            $(document).on('change', '.tc-filter-select, .tc-filter-input', this.applyFilters);
            $(document).on('click', '.tc-clear-filters', this.clearFilters);
            $(document).on('input', '.tc-search-input', this.debounce(this.performSearch, 500));
            
            // Ordenação
            $(document).on('change', '.tc-sort-select', this.handleSort);
            
            // WhatsApp
            $(document).on('click', '.tc-whatsapp-btn', this.handleWhatsAppClick);
            
            // Galeria
            $(document).on('click', '.tc-gallery-thumbnail', this.openGallery);
            
            // Lazy loading
            this.initLazyLoading();
            
            // Scroll animations
            this.initScrollAnimations();
        },

        initComponents: function() {
            // Inicializar máscara de telefone
            if ($.fn.mask) {
                $('.tc-phone-input').mask('(00) 00000-0000');
            }
            
            // Inicializar carousel se houver
            this.initCarousel();
            
            // Ajustar altura dos cards
            this.equalizeCardHeights();
            
            // Configurar range sliders
            this.initRangeSliders();
            
            // Inicializar tooltips
            this.initTooltips();
        },

        // === TABS ===
        handleTabClick: function(e) {
            e.preventDefault();
            const $tab = $(this);
            const target = $tab.attr('href');
            
            // Update tab navigation
            $tab.closest('.tc-package-tab-nav').find('a').removeClass('active');
            $tab.addClass('active');
            
            // Update tab content
            $tab.closest('.tc-package-tabs').find('.tc-package-tab-content').removeClass('active');
            $(target).addClass('active');
            
            // Smooth scroll to content
            if ($(window).width() <= 768) {
                $('html, body').animate({
                    scrollTop: $(target).offset().top - 100
                }, 300);
            }
        },

        // === FORMULÁRIO DE INTERESSE ===
        submitInterestForm: function(e) {
            e.preventDefault();
            const form = $(this);
            const formData = form.serialize();
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.text();
            
            // Validação básica
            if (!TravelCuratorPublic.validateForm(form)) {
                return;
            }
            
            $.ajax({
                url: travelcurator_public.ajax_url,
                type: 'POST',
                data: formData + '&action=travelcurator_submit_interest&nonce=' + travelcurator_public.nonce,
                beforeSend: function() {
                    form.addClass('tc-form-loading');
                    submitBtn.text('Enviando...');
                    $('.tc-form-message').remove();
                },
                success: function(response) {
                    if (response.success) {
                        TravelCuratorPublic.showFormMessage(form, 'success', response.data.message);
                        form[0].reset();
                        
                        // Track conversion
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'conversion', {
                                'send_to': 'AW-CONVERSION_ID/CONVERSION_LABEL',
                                'value': 1.0,
                                'currency': 'BRL'
                            });
                        }
                        
                        // Scroll to message
                        setTimeout(() => {
                            $('html, body').animate({
                                scrollTop: $('.tc-form-message').offset().top - 100
                            }, 500);
                        }, 100);
                        
                    } else {
                        TravelCuratorPublic.showFormMessage(form, 'error', response.data.message);
                    }
                },
                error: function() {
                    TravelCuratorPublic.showFormMessage(form, 'error', 'Erro de conexão. Tente novamente.');
                },
                complete: function() {
                    form.removeClass('tc-form-loading');
                    submitBtn.text(originalText);
                }
            });
        },

        validateForm: function(form) {
            let isValid = true;
            const requiredFields = form.find('[required]');
            
            requiredFields.each(function() {
                const field = $(this);
                const value = field.val().trim();
                
                field.removeClass('tc-field-error');
                
                if (!value) {
                    field.addClass('tc-field-error');
                    isValid = false;
                } else if (field.attr('type') === 'email' && !TravelCuratorPublic.isValidEmail(value)) {
                    field.addClass('tc-field-error');
                    isValid = false;
                }
            });
            
            if (!isValid) {
                TravelCuratorPublic.showFormMessage(form, 'error', 'Por favor, preencha todos os campos obrigatórios.');
            }
            
            return isValid;
        },

        isValidEmail: function(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        showFormMessage: function(form, type, message) {
            $('.tc-form-message').remove();
            
            const messageHtml = `<div class="tc-form-message ${type}">${message}</div>`;
            form.prepend(messageHtml);
            
            // Auto-remove success messages
            if (type === 'success') {
                setTimeout(() => {
                    $('.tc-form-message.success').fadeOut();
                }, 5000);
            }
        },

        // === FILTROS ===
        applyFilters: function() {
            const container = $('.tc-packages-container');
            if (!container.length) return;
            
            const filters = TravelCuratorPublic.getActiveFilters();
            
            $.ajax({
                url: travelcurator_public.ajax_url,
                type: 'POST',
                data: {
                    action: 'travelcurator_filter_packages',
                    filters: filters,
                    nonce: travelcurator_public.nonce
                },
                beforeSend: function() {
                    container.addClass('tc-loading');
                },
                success: function(response) {
                    if (response.success) {
                        container.html(response.data.html);
                        $('.tc-results-count').text(response.data.count + ' pacotes encontrados');
                        
                        // Update URL without reload
                        TravelCuratorPublic.updateURL(filters);
                        
                        // Reinitialize components
                        TravelCuratorPublic.equalizeCardHeights();
                        TravelCuratorPublic.initScrollAnimations();
                    }
                },
                complete: function() {
                    container.removeClass('tc-loading');
                }
            });
        },

        getActiveFilters: function() {
            const filters = {};
            
            $('.tc-filter-select').each(function() {
                const name = $(this).attr('name');
                const value = $(this).val();
                if (value) {
                    filters[name] = value;
                }
            });
            
            $('.tc-filter-input').each(function() {
                const name = $(this).attr('name');
                const value = $(this).val();
                if (value) {
                    filters[name] = value;
                }
            });
            
            return filters;
        },

        clearFilters: function(e) {
            e.preventDefault();
            
            $('.tc-filter-select').val('');
            $('.tc-filter-input').val('');
            $('.tc-range-slider').each(function() {
                const min = $(this).attr('min');
                const max = $(this).attr('max');
                $(this).val([min, max]);
            });
            
            TravelCuratorPublic.applyFilters();
        },

        performSearch: function() {
            TravelCuratorPublic.applyFilters();
        },

        updateURL: function(filters) {
            const url = new URL(window.location);
            
            // Clear existing parameters
            url.searchParams.delete('category');
            url.searchParams.delete('purpose');
            url.searchParams.delete('destination');
            url.searchParams.delete('min_price');
            url.searchParams.delete('max_price');
            url.searchParams.delete('search');
            
            // Add active filters
            Object.keys(filters).forEach(key => {
                if (filters[key]) {
                    url.searchParams.set(key, filters[key]);
                }
            });
            
            window.history.replaceState({}, '', url);
        },

        // === ORDENAÇÃO ===
        handleSort: function() {
            const sortBy = $(this).val();
            const container = $('.tc-packages-container');
            
            if (!container.length) return;
            
            const filters = TravelCuratorPublic.getActiveFilters();
            filters.orderby = sortBy;
            
            $.ajax({
                url: travelcurator_public.ajax_url,
                type: 'POST',
                data: {
                    action: 'travelcurator_filter_packages',
                    filters: filters,
                    nonce: travelcurator_public.nonce
                },
                beforeSend: function() {
                    container.addClass('tc-loading');
                },
                success: function(response) {
                    if (response.success) {
                        container.html(response.data.html);
                        TravelCuratorPublic.equalizeCardHeights();
                        TravelCuratorPublic.initScrollAnimations();
                    }
                },
                complete: function() {
                    container.removeClass('tc-loading');
                }
            });
        },

        // === WHATSAPP ===
        handleWhatsAppClick: function(e) {
            const packageId = $(this).data('package-id');
            const packageTitle = $(this).data('package-title') || 'este pacote';
            
            // Track click
            $.ajax({
                url: travelcurator_public.ajax_url,
                type: 'POST',
                data: {
                    action: 'travelcurator_whatsapp_click',
                    package_id: packageId,
                    nonce: travelcurator_public.nonce
                }
            });
            
            // Analytics tracking
            if (typeof gtag !== 'undefined') {
                gtag('event', 'whatsapp_click', {
                    'event_category': 'engagement',
                    'event_label': packageTitle,
                    'package_id': packageId
                });
            }
        },

        // === GALERIA ===
        openGallery: function(e) {
            e.preventDefault();
            
            const images = [];
            $('.tc-gallery-thumbnail').each(function() {
                images.push({
                    src: $(this).attr('href'),
                    title: $(this).attr('title') || ''
                });
            });
            
            const currentIndex = $('.tc-gallery-thumbnail').index(this);
            
            // Use Lightbox library if available
            if (typeof $.fancybox !== 'undefined') {
                $.fancybox.open(images, {
                    index: currentIndex,
                    loop: true,
                    toolbar: true,
                    smallBtn: true
                });
            } else {
                // Fallback: simple modal
                TravelCuratorPublic.showImageModal(images[currentIndex].src, images[currentIndex].title);
            }
        },

        showImageModal: function(src, title) {
            const modal = `
                <div class="tc-image-modal" onclick="this.remove()">
                    <div class="tc-image-modal-content" onclick="event.stopPropagation()">
                        <img src="${src}" alt="${title}" />
                        <div class="tc-image-modal-title">${title}</div>
                        <button class="tc-image-modal-close" onclick="this.closest('.tc-image-modal').remove()">&times;</button>
                    </div>
                </div>
            `;
            
            $('body').append(modal);
        },

        // === CAROUSEL ===
        initCarousel: function() {
            $('.tc-packages-carousel').each(function() {
                const carousel = $(this);
                const grid = carousel.find('.tc-packages-grid');
                const cards = grid.find('.tc-package-card');
                
                if (cards.length <= 1) return;
                
                let currentIndex = 0;
                const totalCards = cards.length;
                const cardWidth = cards.outerWidth(true);
                
                // Add navigation
                const nav = `
                    <div class="tc-carousel-nav">
                        <button class="tc-carousel-prev">&lsaquo;</button>
                        <button class="tc-carousel-next">&rsaquo;</button>
                    </div>
                `;
                carousel.append(nav);
                
                // Navigation events
                carousel.find('.tc-carousel-prev').click(function() {
                    currentIndex = Math.max(0, currentIndex - 1);
                    updateCarousel();
                });
                
                carousel.find('.tc-carousel-next').click(function() {
                    currentIndex = Math.min(totalCards - 1, currentIndex + 1);
                    updateCarousel();
                });
                
                function updateCarousel() {
                    const translateX = -currentIndex * cardWidth;
                    grid.css('transform', `translateX(${translateX}px)`);
                }
                
                // Auto-play
                if (carousel.data('autoplay')) {
                    setInterval(() => {
                        currentIndex = (currentIndex + 1) % totalCards;
                        updateCarousel();
                    }, 5000);
                }
            });
        },

        // === LAZY LOADING ===
        initLazyLoading: function() {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('tc-lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }
        },

        // === SCROLL ANIMATIONS ===
        initScrollAnimations: function() {
            if ('IntersectionObserver' in window) {
                const animationObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('tc-fade-in-up');
                            animationObserver.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });

                document.querySelectorAll('.tc-package-card, .tc-itinerary-day, .tc-package-highlight-item').forEach(el => {
                    animationObserver.observe(el);
                });
            }
        },

        // === UTILITÁRIOS ===
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        equalizeCardHeights: function() {
            $('.tc-packages-grid').each(function() {
                const cards = $(this).find('.tc-package-card');
                let maxHeight = 0;
                
                // Reset heights
                cards.css('height', 'auto');
                
                // Find max height
                cards.each(function() {
                    const height = $(this).outerHeight();
                    if (height > maxHeight) {
                        maxHeight = height;
                    }
                });
                
                // Apply max height
                cards.css('height', maxHeight + 'px');
            });
        },

        initRangeSliders: function() {
            $('.tc-range-slider').each(function() {
                const slider = $(this);
                const min = parseInt(slider.attr('min'));
                const max = parseInt(slider.attr('max'));
                const step = parseInt(slider.attr('step')) || 1;
                
                if (typeof noUiSlider !== 'undefined') {
                    noUiSlider.create(slider[0], {
                        start: [min, max],
                        connect: true,
                        range: {
                            'min': min,
                            'max': max
                        },
                        step: step,
                        format: {
                            to: function (value) {
                                return parseInt(value);
                            },
                            from: function (value) {
                                return parseInt(value);
                            }
                        }
                    });
                    
                    slider[0].noUiSlider.on('change', function() {
                        TravelCuratorPublic.applyFilters();
                    });
                }
            });
        },

        initTooltips: function() {
            $('.tc-tooltip').each(function() {
                const tooltip = $(this);
                const text = tooltip.attr('data-tooltip');
                
                if (!text) return;
                
                tooltip.hover(
                    function() {
                        const tooltipEl = $(`<div class="tc-tooltip-popup">${text}</div>`);
                        $('body').append(tooltipEl);
                        
                        const offset = tooltip.offset();
                        const tooltipWidth = tooltipEl.outerWidth();
                        const tooltipHeight = tooltipEl.outerHeight();
                        
                        tooltipEl.css({
                            position: 'absolute',
                            top: offset.top - tooltipHeight - 10,
                            left: offset.left + (tooltip.outerWidth() / 2) - (tooltipWidth / 2),
                            zIndex: 9999
                        });
                    },
                    function() {
                        $('.tc-tooltip-popup').remove();
                    }
                );
            });
        },

        // === ANALYTICS ===
        trackPackageView: function(packageId, packageTitle) {
            // Google Analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'view_item', {
                    'event_category': 'packages',
                    'event_label': packageTitle,
                    'package_id': packageId
                });
            }
            
            // Custom tracking
            $.ajax({
                url: travelcurator_public.ajax_url,
                type: 'POST',
                data: {
                    action: 'travelcurator_track_package_view',
                    package_id: packageId,
                    nonce: travelcurator_public.nonce
                }
            });
        },

        // === RESPONSIVE HELPERS ===
        handleResize: function() {
            TravelCuratorPublic.equalizeCardHeights();
            
            // Adjust carousel for mobile
            if ($(window).width() <= 768) {
                $('.tc-packages-carousel .tc-packages-grid').css('display', 'block');
                $('.tc-packages-carousel .tc-package-card').css('margin-bottom', '20px');
            } else {
                $('.tc-packages-carousel .tc-packages-grid').css('display', 'flex');
                $('.tc-packages-carousel .tc-package-card').css('margin-bottom', '0');
            }
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        TravelCuratorPublic.init();
        
        // Track page view for single packages
        if ($('.tc-single-package').length) {
            const packageId = $('.tc-single-package').data('package-id');
            const packageTitle = $('.tc-package-hero-title').text();
            TravelCuratorPublic.trackPackageView(packageId, packageTitle);
        }
    });

    // Handle window resize
    $(window).on('resize', TravelCuratorPublic.debounce(TravelCuratorPublic.handleResize, 250));

    // Expose to global scope
    window.TravelCuratorPublic = TravelCuratorPublic;

})(jQuery);

// CSS dinamico para elementos criados via JavaScript
const dynamicCSS = `
    .tc-field-error {
        border-color: var(--tc-danger) !important;
        box-shadow: 0 0 0 3px rgba(220, 50, 50, 0.1) !important;
    }
    
    .tc-image-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(26, 58, 95, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        cursor: pointer;
    }
    
    .tc-image-modal-content {
        position: relative;
        max-width: 90%;
        max-height: 90%;
        cursor: default;
    }
    
    .tc-image-modal-content img {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }
    
    .tc-image-modal-title {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        color: white;
        padding: 20px;
        border-radius: 0 0 8px 8px;
    }
    
    .tc-image-modal-close {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255,255,255,0.9);
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .tc-carousel-nav {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        pointer-events: none;
        z-index: 10;
    }
    
    .tc-carousel-prev,
    .tc-carousel-next {
        position: absolute;
        background: rgba(255,255,255,0.9);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: auto;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .tc-carousel-prev:hover,
    .tc-carousel-next:hover {
        background: var(--tc-secondary);
        color: white;
        transform: scale(1.1);
    }
    
    .tc-carousel-prev {
        left: 10px;
    }
    
    .tc-carousel-next {
        right: 10px;
    }
    
    .tc-tooltip-popup {
        background: var(--tc-text);
        color: var(--tc-white);
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        animation: tc-fadeInUp 0.3s ease;
    }
    
    .tc-lazy {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .tc-lazy.loaded {
        opacity: 1;
    }
`;

// Inject dynamic CSS
if (typeof document !== 'undefined') {
    const style = document.createElement('style');
    style.textContent = dynamicCSS;
    document.head.appendChild(style);
}