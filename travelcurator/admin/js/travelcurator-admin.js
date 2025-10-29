/**
 * TravelCurator Admin JavaScript
 * Funcionalidades interativas do painel administrativo
 */

(function($) {
    'use strict';

    // Variáveis globais
    const TravelCurator = {
        init: function() {
            this.bindEvents();
            this.initComponents();
            this.loadDashboardData();
        },

        bindEvents: function() {
            // Tabs
            $(document).on('click', '.travelcurator-tab-nav a', this.handleTabClick);
            
            // Repetidores
            $(document).on('click', '#add-highlight', this.addHighlight);
            $(document).on('click', '.remove-highlight', this.removeHighlight);
            $(document).on('click', '#add-itinerary-day', this.addItineraryDay);
            $(document).on('click', '.remove-day', this.removeItineraryDay);
            $(document).on('click', '#add-available-date', this.addAvailableDate);
            $(document).on('click', '.remove-date', this.removeAvailableDate);
            
            // Galeria
            $(document).on('click', '#add-gallery-images', this.openMediaLibrary);
            $(document).on('click', '.remove-image', this.removeGalleryImage);
            $(document).on('click', '#clear-gallery', this.clearGallery);
            
            // Leads
            $(document).on('click', '.tc-lead-detail', this.showLeadDetail);
            $(document).on('click', '.tc-update-lead-status', this.updateLeadStatus);
            $(document).on('click', '.tc-add-lead-note', this.showAddNoteModal);
            $(document).on('submit', '#tc-lead-note-form', this.addLeadNote);
            
            // Notificações
            $(document).on('click', '.tc-notification-item', this.markNotificationRead);
            $(document).on('click', '#tc-mark-all-read', this.markAllNotificationsRead);
            
            // Filtros
            $(document).on('change', '.tc-filter-select', this.applyFilters);
            $(document).on('input', '.tc-search-input', this.debounce(this.performSearch, 500));
            
            // Modais
            $(document).on('click', '.tc-modal-close, .tc-modal-overlay', this.closeModal);
            $(document).on('click', '.tc-modal', function(e) { e.stopPropagation(); });
            
            // Formulários
            $(document).on('submit', '.tc-ajax-form', this.handleAjaxForm);
            
            // WhatsApp
            $(document).on('click', '.tc-whatsapp-btn', this.openWhatsApp);
        },

        initComponents: function() {
            // Inicializar datepickers
            if ($.fn.datepicker) {
                $('.tc-datepicker').datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true
                });
            }
            
            // Inicializar tooltips
            this.initTooltips();
            
            // Inicializar sortables
            if ($.fn.sortable) {
                $('.tc-sortable').sortable({
                    handle: '.tc-drag-handle',
                    placeholder: 'tc-sortable-placeholder',
                    update: this.updateSortOrder
                });
            }
            
            // Auto-resize textareas
            this.autoResizeTextareas();
            
            // Carregar estatísticas
            this.loadStats();
        },

        // === TABS ===
        handleTabClick: function(e) {
            e.preventDefault();
            const $tab = $(this);
            const target = $tab.attr('href');
            
            // Update tab navigation
            $tab.closest('.travelcurator-tab-nav').find('.nav-tab').removeClass('nav-tab-active');
            $tab.addClass('nav-tab-active');
            
            // Update tab content
            $tab.closest('.travelcurator-tabs').find('.travelcurator-tab-content').removeClass('active');
            $(target).addClass('active');
        },

        // === REPETIDORES ===
        addHighlight: function(e) {
            e.preventDefault();
            const container = $('#highlights-list');
            const index = container.children().length;
            
            const html = `
                <div class="highlight-item tc-fade-in-up" data-index="${index}">
                    <div class="highlight-fields">
                        <input type="text" name="package_highlights[${index}][icon]" 
                               placeholder="Ícone (ex: fas fa-star)" style="width: 200px;" />
                        <input type="text" name="package_highlights[${index}][text]" 
                               placeholder="Texto do destaque" style="width: 300px;" />
                        <button type="button" class="button remove-highlight">Remover</button>
                    </div>
                </div>
            `;
            
            container.append(html);
        },

        removeHighlight: function(e) {
            e.preventDefault();
            $(this).closest('.highlight-item').fadeOut(300, function() {
                $(this).remove();
            });
        },

        addItineraryDay: function(e) {
            e.preventDefault();
            const container = $('#itinerary-list');
            const index = container.children().length;
            const dayNumber = index + 1;
            
            const html = `
                <div class="itinerary-day tc-fade-in-up" data-index="${index}">
                    <div class="day-header">
                        <h5>Dia <input type="number" name="package_itinerary[${index}][day_number]" 
                                     value="${dayNumber}" min="1" style="width: 60px;" /></h5>
                        <button type="button" class="button remove-day">Remover Dia</button>
                    </div>
                    <table class="form-table">
                        <tr>
                            <th>Título do Dia</th>
                            <td><input type="text" name="package_itinerary[${index}][day_title]" style="width: 100%;" /></td>
                        </tr>
                        <tr>
                            <th>Descrição</th>
                            <td><textarea name="package_itinerary[${index}][day_description]" rows="3" style="width: 100%;"></textarea></td>
                        </tr>
                        <tr>
                            <th>Atividades</th>
                            <td><textarea name="package_itinerary[${index}][day_activities]" rows="2" style="width: 100%;"></textarea></td>
                        </tr>
                    </table>
                </div>
            `;
            
            container.append(html);
            TravelCurator.autoResizeTextareas();
        },

        removeItineraryDay: function(e) {
            e.preventDefault();
            $(this).closest('.itinerary-day').fadeOut(300, function() {
                $(this).remove();
            });
        },

        addAvailableDate: function(e) {
            e.preventDefault();
            const container = $('#dates-list');
            const index = container.children().length;
            
            const html = `
                <div class="date-item tc-fade-in-up" data-index="${index}">
                    <div class="date-fields">
                        <div class="field-group">
                            <label>Data de Saída</label>
                            <input type="date" name="available_dates[${index}][departure_date]" />
                        </div>
                        <div class="field-group">
                            <label>Data de Retorno</label>
                            <input type="date" name="available_dates[${index}][return_date]" />
                        </div>
                        <div class="field-group">
                            <label>Vagas</label>
                            <input type="number" name="available_dates[${index}][available_spots]" min="0" style="width: 80px;" />
                        </div>
                        <div class="field-group">
                            <label>Preço Especial</label>
                            <input type="number" name="available_dates[${index}][special_price]" min="0" step="0.01" style="width: 120px;" />
                        </div>
                        <div class="field-group">
                            <button type="button" class="button remove-date">Remover</button>
                        </div>
                    </div>
                </div>
            `;
            
            container.append(html);
        },

        removeAvailableDate: function(e) {
            e.preventDefault();
            $(this).closest('.date-item').fadeOut(300, function() {
                $(this).remove();
            });
        },

        // === GALERIA ===
        openMediaLibrary: function(e) {
            e.preventDefault();
            
            if (typeof wp !== 'undefined' && wp.media) {
                const frame = wp.media({
                    title: 'Selecionar Imagens da Galeria',
                    button: { text: 'Adicionar à Galeria' },
                    multiple: true
                });
                
                frame.on('select', function() {
                    const selection = frame.state().get('selection');
                    const container = $('#gallery-preview');
                    const currentIds = $('#package_gallery').val() ? $('#package_gallery').val().split(',') : [];
                    
                    selection.map(function(attachment) {
                        attachment = attachment.toJSON();
                        if (currentIds.indexOf(attachment.id.toString()) === -1) {
                            currentIds.push(attachment.id);
                            
                            const html = `
                                <div class="tc-gallery-item tc-fade-in-up" data-id="${attachment.id}">
                                    <img src="${attachment.sizes.thumbnail.url}" />
                                    <button type="button" class="remove-image">×</button>
                                </div>
                            `;
                            container.append(html);
                        }
                    });
                    
                    $('#package_gallery').val(currentIds.join(','));
                });
                
                frame.open();
            }
        },

        removeGalleryImage: function(e) {
            e.preventDefault();
            const item = $(this).closest('.tc-gallery-item');
            const id = item.data('id');
            const currentIds = $('#package_gallery').val().split(',');
            const newIds = currentIds.filter(itemId => itemId != id);
            
            $('#package_gallery').val(newIds.join(','));
            item.fadeOut(300, function() {
                $(this).remove();
            });
        },

        clearGallery: function(e) {
            e.preventDefault();
            if (confirm('Tem certeza que deseja limpar toda a galeria?')) {
                $('#package_gallery').val('');
                $('#gallery-preview').fadeOut(300, function() {
                    $(this).html('').fadeIn(300);
                });
            }
        },

        // === LEADS ===
        showLeadDetail: function(e) {
            e.preventDefault();
            const leadId = $(this).data('lead-id');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'travelcurator_get_lead_detail',
                    lead_id: leadId,
                    nonce: travelcurator_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        TravelCurator.showModal('lead-detail', response.data);
                    } else {
                        TravelCurator.showAlert('error', response.data.message);
                    }
                },
                error: function() {
                    TravelCurator.showAlert('error', 'Erro ao carregar detalhes do lead.');
                }
            });
        },

        updateLeadStatus: function(e) {
            e.preventDefault();
            const leadId = $(this).data('lead-id');
            const newStatus = $(this).val();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'travelcurator_update_lead_status',
                    lead_id: leadId,
                    status: newStatus,
                    nonce: travelcurator_admin.nonce
                },
                beforeSend: function() {
                    $(e.target).addClass('tc-loading');
                },
                success: function(response) {
                    if (response.success) {
                        TravelCurator.showAlert('success', 'Status atualizado com sucesso!');
                        location.reload(); // Refresh to show updated data
                    } else {
                        TravelCurator.showAlert('error', 'Erro ao atualizar status.');
                    }
                },
                complete: function() {
                    $(e.target).removeClass('tc-loading');
                }
            });
        },

        showAddNoteModal: function(e) {
            e.preventDefault();
            const leadId = $(this).data('lead-id');
            const modalHtml = `
                <div class="tc-modal-overlay active" id="add-note-modal">
                    <div class="tc-modal">
                        <div class="tc-modal-header">
                            <h3 class="tc-modal-title">Adicionar Nota</h3>
                            <button class="tc-modal-close">&times;</button>
                        </div>
                        <div class="tc-modal-content">
                            <form id="tc-lead-note-form">
                                <input type="hidden" name="lead_id" value="${leadId}" />
                                <div class="tc-form-group">
                                    <label for="note">Nota:</label>
                                    <textarea id="note" name="note" rows="4" required></textarea>
                                </div>
                                <div style="text-align: right; margin-top: 20px;">
                                    <button type="button" class="button tc-modal-close">Cancelar</button>
                                    <button type="submit" class="tc-btn tc-btn-success">Adicionar Nota</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(modalHtml);
            $('#note').focus();
        },

        addLeadNote: function(e) {
            e.preventDefault();
            const form = $(this);
            const formData = form.serialize();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData + '&action=travelcurator_add_lead_note&nonce=' + travelcurator_admin.nonce,
                beforeSend: function() {
                    form.addClass('tc-loading');
                },
                success: function(response) {
                    if (response.success) {
                        TravelCurator.showAlert('success', 'Nota adicionada com sucesso!');
                        TravelCurator.closeModal();
                        location.reload();
                    } else {
                        TravelCurator.showAlert('error', response.data.message);
                    }
                },
                complete: function() {
                    form.removeClass('tc-loading');
                }
            });
        },

        // === NOTIFICAÇÕES ===
        markNotificationRead: function(e) {
            const notificationId = $(this).data('notification-id');
            
            if ($(this).hasClass('unread')) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'travelcurator_mark_notification_read',
                        notification_id: notificationId,
                        nonce: travelcurator_admin.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $(e.target).removeClass('unread');
                            TravelCurator.updateNotificationCount();
                        }
                    }
                });
            }
        },

        markAllNotificationsRead: function(e) {
            e.preventDefault();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'travelcurator_mark_all_notifications_read',
                    nonce: travelcurator_admin.nonce
                },
                beforeSend: function() {
                    $(e.target).addClass('tc-loading');
                },
                success: function(response) {
                    if (response.success) {
                        $('.tc-notification-item').removeClass('unread');
                        TravelCurator.updateNotificationCount();
                        TravelCurator.showAlert('success', 'Todas as notificações foram marcadas como lidas.');
                    }
                },
                complete: function() {
                    $(e.target).removeClass('tc-loading');
                }
            });
        },

        updateNotificationCount: function() {
            const unreadCount = $('.tc-notification-item.unread').length;
            $('.tc-notification-count').text(unreadCount);
            
            if (unreadCount === 0) {
                $('.tc-notification-badge').hide();
            }
        },

        // === FILTROS E BUSCA ===
        applyFilters: function() {
            const filters = {};
            
            $('.tc-filter-select').each(function() {
                const name = $(this).attr('name');
                const value = $(this).val();
                if (value) {
                    filters[name] = value;
                }
            });
            
            TravelCurator.loadFilteredData(filters);
        },

        performSearch: function() {
            const query = $('.tc-search-input').val();
            const filters = { search: query };
            
            $('.tc-filter-select').each(function() {
                const name = $(this).attr('name');
                const value = $(this).val();
                if (value) {
                    filters[name] = value;
                }
            });
            
            TravelCurator.loadFilteredData(filters);
        },

        loadFilteredData: function(filters) {
            const table = $('.tc-data-table tbody');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'travelcurator_filter_data',
                    filters: filters,
                    nonce: travelcurator_admin.nonce
                },
                beforeSend: function() {
                    table.addClass('tc-loading');
                },
                success: function(response) {
                    if (response.success) {
                        table.html(response.data.html);
                        $('.tc-results-count').text(response.data.count + ' resultados encontrados');
                    }
                },
                complete: function() {
                    table.removeClass('tc-loading');
                }
            });
        },

        // === MODAIS ===
        showModal: function(type, data) {
            let modalHtml = '';
            
            switch (type) {
                case 'lead-detail':
                    modalHtml = TravelCurator.getLeadDetailModal(data);
                    break;
                default:
                    return;
            }
            
            $('body').append(modalHtml);
        },

        closeModal: function(e) {
            if (e.target === e.currentTarget) {
                $('.tc-modal-overlay').fadeOut(300, function() {
                    $(this).remove();
                });
            }
        },

        getLeadDetailModal: function(lead) {
            return `
                <div class="tc-modal-overlay active">
                    <div class="tc-modal">
                        <div class="tc-modal-header">
                            <h3 class="tc-modal-title">Detalhes do Lead #${lead.id}</h3>
                            <button class="tc-modal-close">&times;</button>
                        </div>
                        <div class="tc-modal-content">
                            <div class="tc-form-grid">
                                <div class="tc-form-group">
                                    <label>Nome:</label>
                                    <input type="text" value="${lead.name}" readonly />
                                </div>
                                <div class="tc-form-group">
                                    <label>Email:</label>
                                    <input type="email" value="${lead.email}" readonly />
                                </div>
                                <div class="tc-form-group">
                                    <label>Telefone:</label>
                                    <input type="text" value="${lead.phone || 'Não informado'}" readonly />
                                </div>
                                <div class="tc-form-group">
                                    <label>Status:</label>
                                    <select class="tc-update-lead-status" data-lead-id="${lead.id}">
                                        <option value="new" ${lead.status === 'new' ? 'selected' : ''}>Novo</option>
                                        <option value="contacted" ${lead.status === 'contacted' ? 'selected' : ''}>Contatado</option>
                                        <option value="qualified" ${lead.status === 'qualified' ? 'selected' : ''}>Qualificado</option>
                                        <option value="converted" ${lead.status === 'converted' ? 'selected' : ''}>Convertido</option>
                                        <option value="lost" ${lead.status === 'lost' ? 'selected' : ''}>Perdido</option>
                                    </select>
                                </div>
                            </div>
                            <div class="tc-form-group">
                                <label>Mensagem:</label>
                                <textarea readonly rows="4">${lead.message || 'Nenhuma mensagem'}</textarea>
                            </div>
                            <div class="tc-form-group">
                                <label>Notas:</label>
                                <textarea readonly rows="6">${lead.notes || 'Nenhuma nota'}</textarea>
                            </div>
                            <div style="text-align: right; margin-top: 20px;">
                                <button class="tc-btn tc-add-lead-note" data-lead-id="${lead.id}">Adicionar Nota</button>
                                <a href="mailto:${lead.email}" class="tc-btn tc-btn-secondary">Enviar Email</a>
                                <button class="tc-btn tc-btn-success" onclick="window.open('https://wa.me/55${lead.phone?.replace(/\D/g, '')}', '_blank')">WhatsApp</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
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

        showAlert: function(type, message) {
            const alertClass = 'tc-alert-' + type;
            const iconMap = {
                success: '✓',
                error: '⚠',
                warning: '⚠',
                info: 'ℹ'
            };
            
            const alertHtml = `
                <div class="tc-alert ${alertClass} tc-fade-in-up" style="position: fixed; top: 20px; right: 20px; z-index: 999999; max-width: 400px;">
                    <span class="tc-alert-icon">${iconMap[type] || 'ℹ'}</span>
                    ${message}
                </div>
            `;
            
            const $alert = $(alertHtml);
            $('body').append($alert);
            
            setTimeout(() => {
                $alert.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        },

        autoResizeTextareas: function() {
            $('textarea[data-autoresize]').each(function() {
                $(this).on('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            });
        },

        initTooltips: function() {
            $('.tc-tooltip').each(function() {
                const tooltip = $(this).attr('data-tooltip');
                if (tooltip) {
                    $(this).attr('title', tooltip);
                }
            });
        },

        updateSortOrder: function(event, ui) {
            const items = $(this).children();
            const order = [];
            
            items.each(function(index) {
                order.push({
                    id: $(this).data('id'),
                    order: index
                });
            });
            
            // Salvar nova ordem via AJAX
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'travelcurator_update_sort_order',
                    order: order,
                    nonce: travelcurator_admin.nonce
                }
            });
        },

        // === WHATSAPP ===
        openWhatsApp: function(e) {
            e.preventDefault();
            const packageId = $(this).data('package-id');
            const packageTitle = $(this).data('package-title');
            const whatsappNumber = travelcurator_admin.whatsapp_number;
            
            if (!whatsappNumber) {
                TravelCurator.showAlert('warning', 'Número do WhatsApp não configurado.');
                return;
            }
            
            // Track click
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'travelcurator_whatsapp_click',
                    package_id: packageId,
                    nonce: travelcurator_admin.nonce
                }
            });
            
            // Open WhatsApp
            const message = encodeURIComponent(`Olá! Tenho interesse no pacote "${packageTitle}". Pode me dar mais informações?`);
            const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${message}`;
            window.open(whatsappUrl, '_blank');
        },

        // === DASHBOARD E ESTATÍSTICAS ===
        loadDashboardData: function() {
            if ($('.travelcurator-dashboard').length) {
                this.loadStats();
                this.loadCharts();
                this.loadRecentLeads();
            }
        },

        loadStats: function() {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'travelcurator_get_stats',
                    nonce: travelcurator_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        TravelCurator.updateStatsCards(response.data);
                    }
                }
            });
        },

        updateStatsCards: function(stats) {
            $('.tc-stat-card[data-stat="total_packages"] .tc-stat-number').text(stats.total_packages || 0);
            $('.tc-stat-card[data-stat="today_leads"] .tc-stat-number').text(stats.today_leads || 0);
            $('.tc-stat-card[data-stat="week_leads"] .tc-stat-number').text(stats.week_leads || 0);
            $('.tc-stat-card[data-stat="month_leads"] .tc-stat-number').text(stats.month_leads || 0);
            $('.tc-stat-card[data-stat="whatsapp_clicks"] .tc-stat-number').text(stats.whatsapp_clicks || 0);
            $('.tc-stat-card[data-stat="conversion_rate"] .tc-stat-number').text((stats.conversion_rate || 0) + '%');
        },

        loadCharts: function() {
            // Carrega dados para gráficos (Chart.js)
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'travelcurator_get_chart_data',
                    nonce: travelcurator_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        TravelCurator.renderCharts(response.data);
                    }
                }
            });
        },

        renderCharts: function(data) {
            // Gráfico de leads por mês
            if ($('#leads-chart').length && typeof Chart !== 'undefined') {
                const ctx = document.getElementById('leads-chart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.monthly_labels,
                        datasets: [{
                            label: 'Leads por Mês',
                            data: data.monthly_leads,
                            borderColor: '#1A3A5F',
                            backgroundColor: 'rgba(26, 58, 95, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
            
            // Gráfico de propósitos emocionais
            if ($('#purposes-chart').length && typeof Chart !== 'undefined') {
                const ctx2 = document.getElementById('purposes-chart').getContext('2d');
                new Chart(ctx2, {
                    type: 'doughnut',
                    data: {
                        labels: data.purpose_labels,
                        datasets: [{
                            data: data.purpose_data,
                            backgroundColor: [
                                '#1A3A5F',
                                '#D4B254',
                                '#C85F4D',
                                '#8A8582',
                                '#46b450'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });
            }
        },

        loadRecentLeads: function() {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'travelcurator_get_recent_leads',
                    nonce: travelcurator_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.tc-recent-leads').html(response.data.html);
                    }
                }
            });
        },

        // === FORMULÁRIOS AJAX ===
        handleAjaxForm: function(e) {
            e.preventDefault();
            const form = $(this);
            const formData = form.serialize();
            
            $.ajax({
                url: form.attr('action') || ajaxurl,
                type: form.attr('method') || 'POST',
                data: formData,
                beforeSend: function() {
                    form.addClass('tc-loading');
                    form.find('button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        TravelCurator.showAlert('success', response.data.message || 'Operação realizada com sucesso!');
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        }
                        if (response.data.reload) {
                            location.reload();
                        }
                    } else {
                        TravelCurator.showAlert('error', response.data.message || 'Erro ao processar solicitação.');
                    }
                },
                error: function() {
                    TravelCurator.showAlert('error', 'Erro de conexão. Tente novamente.');
                },
                complete: function() {
                    form.removeClass('tc-loading');
                    form.find('button[type="submit"]').prop('disabled', false);
                }
            });
        }
    };

    // Inicializar quando o documento estiver pronto
    $(document).ready(function() {
        TravelCurator.init();
        
        // Adicionar classe para animações CSS
        setTimeout(() => {
            $('.tc-stat-card, .tc-chart-container, .tc-notification-item').addClass('tc-fade-in-up');
        }, 100);
    });

    // Expor para escopo global se necessário
    window.TravelCurator = TravelCurator;

})(jQuery);