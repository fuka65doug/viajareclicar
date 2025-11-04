/**
 * TravelCurator Widgets JavaScript
 * Modal and interaction functionality
 */

(function($) {
    'use strict';

    // Purpose filter functionality
    function initPurposeFilters() {
        // Function to apply filter
        function applyFilter(filter) {
            // Update active state
            $('.tc-purpose-pill').removeClass('active');
            $('.tc-purpose-pill[data-purpose="' + filter + '"]').addClass('active');

            // Filter packages
            if (filter === 'all') {
                $('.package-card').fadeIn(300);
            } else {
                $('.package-card').hide();
                $('.package-card[data-purpose="' + filter + '"]').fadeIn(300);
            }
        }

        // Handle click events
        $('.tc-purpose-pill').on('click', function() {
            const filter = $(this).data('purpose');
            applyFilter(filter);

            // Update URL without reload
            const url = new URL(window.location.href);
            if (filter === 'all') {
                url.searchParams.delete('purpose');
            } else {
                url.searchParams.set('purpose', filter);
            }
            window.history.pushState({}, '', url);
        });

        // Check URL parameters on load
        const urlParams = new URLSearchParams(window.location.search);
        const purposeParam = urlParams.get('purpose');

        if (purposeParam) {
            // Check if pill exists for this purpose
            const $pill = $('.tc-purpose-pill[data-purpose="' + purposeParam + '"]');
            if ($pill.length) {
                applyFilter(purposeParam);

                // Scroll to packages section
                setTimeout(function() {
                    const $grid = $('.packages-grid');
                    if ($grid.length) {
                        $('html, body').animate({
                            scrollTop: $grid.offset().top - 100
                        }, 500);
                    }
                }, 300);
            }
        }
    }

    // Modal functionality
    function initModal() {
        // Open modal
        window.openPackageModal = function(packageId) {
            const $modal = $('#tc-modal-' + packageId);
            if ($modal.length) {
                $modal.addClass('active');
                $('body').css('overflow', 'hidden');
            }
        };

        // Close modal
        $('.tc-modal-close, .btn-modal-close').on('click', function(e) {
            e.preventDefault();
            $(this).closest('.tc-modal-overlay').removeClass('active');
            $('body').css('overflow', '');
        });

        // Close modal on overlay click
        $('.tc-modal-overlay').on('click', function(e) {
            if (e.target === this) {
                $(this).removeClass('active');
                $('body').css('overflow', '');
            }
        });

        // Close modal on ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.tc-modal-overlay.active').removeClass('active');
                $('body').css('overflow', '');
            }
        });
    }

    // WhatsApp integration
    function initWhatsApp() {
        window.openWhatsAppInterest = function(packageId, packageTitle) {
            // Get WhatsApp number from WordPress settings
            const whatsappNumber = travelcuratorData.whatsappNumber || '';

            if (!whatsappNumber) {
                alert('Número do WhatsApp não configurado. Entre em contato com o administrador.');
                return;
            }

            // Build message
            const message = encodeURIComponent(
                'Olá! Tenho interesse no pacote: ' + packageTitle + '. ' +
                'Gostaria de receber mais informações.'
            );

            // Open WhatsApp
            const url = 'https://wa.me/' + whatsappNumber + '?text=' + message;
            window.open(url, '_blank');
        };
    }

    // Smooth scroll to packages
    function initSmoothScroll() {
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 800);
            }
        });
    }

    // Lazy load images
    function initLazyLoad() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img.lazy').forEach(function(img) {
                imageObserver.observe(img);
            });
        }
    }

    // Get purpose icon based on purpose slug
    function getPurposeIcon(purpose) {
        const icons = {
            'reconexao': '🍷',
            'celebracao': '🎉',
            'descoberta': '🧭',
            'transformacao': '🦋',
            'descanso': '🌴',
            'aventura': '🏔️',
            'romance': '💕',
            'cultura': '🎭',
            'gastronomia': '🍽️',
            'natureza': '🌿',
            'bem-estar': '🧘',
            'familia': '👨‍👩‍👧‍👦'
        };
        return icons[purpose] || '✨';
    }

    // Initialize on document ready
    $(document).ready(function() {
        initPurposeFilters();
        initModal();
        initWhatsApp();
        initSmoothScroll();
        initLazyLoad();
    });

    // Initialize on Elementor frontend load
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/travelcurator-packages-grid.default', function($scope) {
            initPurposeFilters();
            initModal();
            initWhatsApp();
        });
    });

})(jQuery);
