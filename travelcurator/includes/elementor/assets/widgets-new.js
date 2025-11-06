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
                $('.no-packages').hide();
            } else {
                $('.package-card').hide();
                var $filteredPackages = $('.package-card[data-purpose="' + filter + '"]');

                if ($filteredPackages.length > 0) {
                    $filteredPackages.fadeIn(300);
                    $('.no-packages').hide();
                } else {
                    // Show "no results" message
                    if ($('.no-packages').length === 0) {
                        var whatsappNumber = typeof travelcuratorData !== 'undefined' ? travelcuratorData.whatsappNumber : '';
                        var whatsappMessage = encodeURIComponent('Olá! Gostaria de uma experiência personalizada. Podem me ajudar?');

                        $('.packages-grid').append(
                            '<div class="no-packages">' +
                                '<div class="no-packages-icon">😔</div>' +
                                '<h3 class="no-packages-title">Nenhuma experiência encontrada</h3>' +
                                '<p class="no-packages-text">Não encontramos pacotes para este propósito emocional no momento.</p>' +
                                '<p class="no-packages-cta">Mas podemos criar uma experiência personalizada para você!</p>' +
                                '<a href="https://wa.me/' + whatsappNumber + '?text=' + whatsappMessage + '" class="btn-whatsapp-cta" target="_blank" rel="noopener">' +
                                    '💬 Fale Conosco no WhatsApp' +
                                '</a>' +
                            '</div>'
                        );
                    } else {
                        $('.no-packages').fadeIn(300);
                    }
                }
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

    // AJAX Pagination functionality
    var paginationInitialized = false;

    function initAjaxPagination() {
        // Prevent multiple initializations
        if (paginationInitialized) {
            return;
        }
        paginationInitialized = true;

        // Use event delegation on document to ensure it works even after AJAX loads
        $(document).off('click.tcPagination').on('click.tcPagination', '.packages-pagination .page-numbers', function(e) {
            // Skip if current page or dots
            if ($(this).hasClass('current') || $(this).hasClass('dots')) {
                e.preventDefault();
                return false;
            }

            e.preventDefault();
            e.stopPropagation();

            var $link = $(this);
            var url = $link.attr('href');

            if (!url) {
                return false;
            }

            // Extract page number from URL
            var pageMatch = url.match(/[?&]paged=(\d+)/);
            var page = pageMatch ? pageMatch[1] : 1;

            // Get current filter
            var currentFilter = $('.tc-purpose-pill.active').data('purpose') || 'all';

            // Find the widget container
            var $widget = $link.closest('.elementor-widget-travelcurator-packages-grid');
            var $grid = $widget.find('.packages-grid');
            var $pagination = $widget.find('.packages-pagination');

            // Show loading state
            $pagination.addClass('loading');
            $grid.css('opacity', '0.5');

            // Scroll to grid top
            $('html, body').animate({
                scrollTop: $grid.offset().top - 100
            }, 400);

            // Build AJAX URL
            var ajaxUrl = window.location.href.split('?')[0];
            ajaxUrl += '?paged=' + page;
            if (currentFilter !== 'all') {
                ajaxUrl += '&purpose=' + currentFilter;
            }

            // Perform AJAX request
            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    // Create a temporary container to parse the response
                    var $tempContainer = $('<div>').html(response);

                    // Find the widget in the response
                    var $responseWidget = $tempContainer.find('.elementor-widget-travelcurator-packages-grid').first();

                    if ($responseWidget.length) {
                        var $newGrid = $responseWidget.find('.packages-grid').first();
                        var $newPagination = $responseWidget.find('.packages-pagination').first();

                        if ($newGrid.length) {
                            // Replace grid content
                            $grid.html($newGrid.html());

                            // Replace pagination
                            if ($newPagination.length) {
                                $pagination.replaceWith($newPagination.clone());
                            } else {
                                $pagination.remove();
                            }

                            // Update URL without reload
                            window.history.pushState({page: page}, '', ajaxUrl);

                            // Restore visibility
                            $grid.css('opacity', '1');

                            // Reinitialize modal for new content
                            initModal();

                            // Reinitialize filters if needed
                            if (currentFilter !== 'all') {
                                setTimeout(function() {
                                    $grid.find('.package-card').hide();
                                    $grid.find('.package-card[data-purpose="' + currentFilter + '"]').fadeIn(300);
                                }, 100);
                            }
                        }
                    }

                    // Clean up
                    $tempContainer.remove();
                },
                error: function(xhr, status, error) {
                    console.error('Pagination AJAX error:', error);
                    // Fallback to normal navigation
                    window.location.href = url;
                },
                complete: function() {
                    // Find pagination again in case it was replaced
                    var $currentPagination = $widget.find('.packages-pagination');
                    if ($currentPagination.length) {
                        $currentPagination.removeClass('loading');
                    }
                    $grid.css('opacity', '1');
                }
            });

            return false;
        });

        // Handle browser back/forward buttons
        $(window).off('popstate.tcPagination').on('popstate.tcPagination', function(event) {
            if (event.state && event.state.page) {
                location.reload();
            }
        });
    }

    // Initialize on document ready
    $(document).ready(function() {
        initPurposeFilters();
        initModal();
        initWhatsApp();
        initSmoothScroll();
        initLazyLoad();
        initAjaxPagination();
    });

    // Initialize on Elementor frontend load
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/travelcurator-packages-grid.default', function($scope) {
            initPurposeFilters();
            initModal();
            initWhatsApp();
            initAjaxPagination();
        });
    });

})(jQuery);
