<?php
/**
 * The template for displaying travel packages archive
 *
 * @package TravelCurator
 */

get_header(); ?>

<div class="travelcurator-archive">
    <!-- Hero Section -->
    <section class="archive-hero">
        <div class="hero-overlay">
            <div class="container">
                <div class="hero-content">
                    <h1 class="archive-title">
                        <?php
                        if (is_tax()) {
                            single_term_title();
                        } else {
                            echo 'Nossos Pacotes de Viagem';
                        }
                        ?>
                    </h1>
                    <p class="archive-subtitle">
                        <?php
                        if (is_tax()) {
                            echo term_description();
                        } else {
                            echo 'Descubra experiências únicas e transformadoras ao redor do mundo';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="container archive-content">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3">
                <div class="filters-sidebar">
                    <h3><i class="fas fa-filter"></i> Filtros</h3>
                    
                    <form id="packageFilters" class="package-filters">
                        <!-- Price Range -->
                        <div class="filter-group">
                            <h4>Faixa de Preço</h4>
                            <div class="price-range">
                                <input type="range" id="minPrice" name="min_price" min="500" max="10000" value="500" class="price-slider">
                                <input type="range" id="maxPrice" name="max_price" min="500" max="10000" value="10000" class="price-slider">
                                <div class="price-labels">
                                    <span>R$ <span id="minPriceLabel">500</span></span>
                                    <span>R$ <span id="maxPriceLabel">10.000</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="filter-group">
                            <h4>Duração</h4>
                            <div class="checkbox-group">
                                <label><input type="checkbox" name="duration[]" value="1-3"> 1-3 dias</label>
                                <label><input type="checkbox" name="duration[]" value="4-7"> 4-7 dias</label>
                                <label><input type="checkbox" name="duration[]" value="8-14"> 8-14 dias</label>
                                <label><input type="checkbox" name="duration[]" value="15+"> Mais de 15 dias</label>
                            </div>
                        </div>

                        <!-- Difficulty -->
                        <div class="filter-group">
                            <h4>Dificuldade</h4>
                            <div class="checkbox-group">
                                <label><input type="checkbox" name="difficulty[]" value="easy"> Fácil</label>
                                <label><input type="checkbox" name="difficulty[]" value="moderate"> Moderado</label>
                                <label><input type="checkbox" name="difficulty[]" value="hard"> Difícil</label>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="filter-group">
                            <h4>Categorias</h4>
                            <div class="checkbox-group">
                                <?php
                                $categories = get_terms(array(
                                    'taxonomy' => 'travel_category',
                                    'hide_empty' => true,
                                ));
                                foreach ($categories as $category) :
                                ?>
                                <label>
                                    <input type="checkbox" name="categories[]" value="<?php echo esc_attr($category->term_id); ?>">
                                    <?php echo esc_html($category->name); ?> (<?php echo $category->count; ?>)
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Destinations -->
                        <div class="filter-group">
                            <h4>Destinos</h4>
                            <div class="checkbox-group">
                                <?php
                                $destinations = get_terms(array(
                                    'taxonomy' => 'travel_destination',
                                    'hide_empty' => true,
                                ));
                                foreach ($destinations as $destination) :
                                ?>
                                <label>
                                    <input type="checkbox" name="destinations[]" value="<?php echo esc_attr($destination->term_id); ?>">
                                    <?php echo esc_html($destination->name); ?> (<?php echo $destination->count; ?>)
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Emotional Purpose -->
                        <div class="filter-group">
                            <h4>Propósito Emocional</h4>
                            <div class="checkbox-group">
                                <?php
                                $purposes = get_terms(array(
                                    'taxonomy' => 'travel_purpose',
                                    'hide_empty' => true,
                                ));
                                foreach ($purposes as $purpose) :
                                ?>
                                <label>
                                    <input type="checkbox" name="purposes[]" value="<?php echo esc_attr($purpose->term_id); ?>">
                                    <?php echo esc_html($purpose->name); ?> (<?php echo $purpose->count; ?>)
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                                <i class="fas fa-times"></i> Limpar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Packages Grid -->
            <div class="col-lg-9">
                <div class="packages-header">
                    <div class="results-info">
                        <span class="results-count">
                            <?php
                            global $wp_query;
                            echo $wp_query->found_posts . ' pacote(s) encontrado(s)';
                            ?>
                        </span>
                    </div>
                    <div class="sort-options">
                        <select id="sortBy" name="sort">
                            <option value="date">Mais Recentes</option>
                            <option value="price_asc">Menor Preço</option>
                            <option value="price_desc">Maior Preço</option>
                            <option value="title">Nome A-Z</option>
                            <option value="popularity">Mais Populares</option>
                        </select>
                    </div>
                    <div class="view-toggle">
                        <button class="view-btn active" data-view="grid" title="Visualização em Grade">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="view-btn" data-view="list" title="Visualização em Lista">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>

                <div id="packagesGrid" class="packages-grid view-grid">
                    <?php if (have_posts()) : ?>
                        <?php while (have_posts()) : the_post(); 
                            $package_id = get_the_ID();
                            $price = get_post_meta($package_id, '_travelcurator_price', true);
                            $duration = get_post_meta($package_id, '_travelcurator_duration', true);
                            $difficulty = get_post_meta($package_id, '_travelcurator_difficulty', true);
                            $location = get_post_meta($package_id, '_travelcurator_location', true);
                            $start_date = get_post_meta($package_id, '_travelcurator_start_date', true);
                            
                            // Get taxonomies
                            $categories = get_the_terms($package_id, 'travel_category');
                            $purposes = get_the_terms($package_id, 'travel_purpose');
                        ?>
                        <article class="package-card" data-price="<?php echo esc_attr($price); ?>" data-difficulty="<?php echo esc_attr($difficulty); ?>">
                            <div class="card-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('large', array('class' => 'card-img')); ?>
                                    <?php else : ?>
                                        <div class="placeholder-image">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </a>
                                
                                <!-- Package Labels -->
                                <div class="package-labels">
                                    <?php if ($categories && !is_wp_error($categories)) : ?>
                                        <span class="category-label"><?php echo esc_html($categories[0]->name); ?></span>
                                    <?php endif; ?>
                                    <span class="difficulty-label difficulty-<?php echo esc_attr($difficulty); ?>">
                                        <?php echo ucfirst($difficulty); ?>
                                    </span>
                                </div>

                                <!-- Wishlist Button -->
                                <button class="wishlist-btn" onclick="toggleWishlist(<?php echo $package_id; ?>)" title="Adicionar aos Favoritos">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>

                            <div class="card-content">
                                <div class="card-header">
                                    <h3 class="package-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <div class="package-location">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo esc_html($location); ?>
                                    </div>
                                </div>

                                <div class="package-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                </div>

                                <div class="package-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo esc_html($duration); ?></span>
                                    </div>
                                    <?php if ($start_date) : ?>
                                    <div class="meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <span><?php echo date('d/m/Y', strtotime($start_date)); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Emotional Purpose Tags -->
                                <?php if ($purposes && !is_wp_error($purposes)) : ?>
                                <div class="purpose-tags">
                                    <?php foreach (array_slice($purposes, 0, 2) as $purpose) : ?>
                                        <span class="purpose-tag"><?php echo esc_html($purpose->name); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>

                                <div class="card-footer">
                                    <div class="package-price">
                                        <span class="price-label">A partir de</span>
                                        <span class="price-value">R$ <?php echo number_format($price, 2, ',', '.'); ?></span>
                                        <span class="price-per">por pessoa</span>
                                    </div>
                                    <div class="card-actions">
                                        <button class="btn btn-outline-primary btn-sm" onclick="openQuickView(<?php echo $package_id; ?>)">
                                            <i class="fas fa-eye"></i> Ver Detalhes
                                        </button>
                                        <button class="btn btn-primary btn-sm" onclick="openLeadModal(<?php echo $package_id; ?>)">
                                            <i class="fas fa-paper-plane"></i> Solicitar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <div class="no-packages">
                            <div class="no-packages-content">
                                <i class="fas fa-search fa-3x"></i>
                                <h3>Nenhum pacote encontrado</h3>
                                <p>Tente ajustar os filtros ou fazer uma nova busca.</p>
                                <button class="btn btn-primary" onclick="clearFilters()">
                                    <i class="fas fa-refresh"></i> Limpar Filtros
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($wp_query->max_num_pages > 1) : ?>
                <div class="packages-pagination">
                    <?php
                    echo paginate_links(array(
                        'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                        'total' => $wp_query->max_num_pages,
                        'current' => max(1, get_query_var('paged')),
                        'format' => '?paged=%#%',
                        'show_all' => false,
                        'type' => 'plain',
                        'end_size' => 2,
                        'mid_size' => 1,
                        'prev_next' => true,
                        'prev_text' => '<i class="fas fa-chevron-left"></i> Anterior',
                        'next_text' => 'Próximo <i class="fas fa-chevron-right"></i>',
                        'add_args' => false,
                        'add_fragment' => '',
                    ));
                    ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Lead Modal -->
<div id="leadModal" class="travelcurator-modal">
    <div class="modal-content">
        <span class="close" onclick="closeLeadModal()">&times;</span>
        <h2>Solicitar Orçamento</h2>
        <form id="leadForm" class="lead-form">
            <input type="hidden" name="package_id" id="modalPackageId">
            <input type="hidden" name="action" value="travelcurator_submit_lead">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('travelcurator_lead_nonce'); ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="lead_name">Nome Completo *</label>
                    <input type="text" id="lead_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="lead_email">E-mail *</label>
                    <input type="email" id="lead_email" name="email" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="lead_phone">Telefone/WhatsApp *</label>
                    <input type="tel" id="lead_phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="lead_travelers">Número de Viajantes</label>
                    <select id="lead_travelers" name="travelers">
                        <option value="1">1 pessoa</option>
                        <option value="2">2 pessoas</option>
                        <option value="3">3 pessoas</option>
                        <option value="4">4 pessoas</option>
                        <option value="5+">5+ pessoas</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="lead_message">Mensagem Adicional</label>
                <textarea id="lead_message" name="message" rows="4" placeholder="Conte-nos mais sobre suas expectativas..."></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeLeadModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Enviar Solicitação
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Quick View Modal -->
<div id="quickViewModal" class="travelcurator-modal">
    <div class="modal-content modal-large">
        <span class="close" onclick="closeQuickView()">&times;</span>
        <div id="quickViewContent">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<?php get_footer(); ?>

<script>
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    // Price range sliders
    const minPrice = document.getElementById('minPrice');
    const maxPrice = document.getElementById('maxPrice');
    const minLabel = document.getElementById('minPriceLabel');
    const maxLabel = document.getElementById('maxPriceLabel');
    
    function updatePriceLabels() {
        minLabel.textContent = parseInt(minPrice.value).toLocaleString('pt-BR');
        maxLabel.textContent = parseInt(maxPrice.value).toLocaleString('pt-BR');
        
        if (parseInt(minPrice.value) >= parseInt(maxPrice.value)) {
            minPrice.value = parseInt(maxPrice.value) - 500;
        }
    }
    
    minPrice.addEventListener('input', updatePriceLabels);
    maxPrice.addEventListener('input', updatePriceLabels);
    
    // View toggle
    const viewBtns = document.querySelectorAll('.view-btn');
    const packagesGrid = document.getElementById('packagesGrid');
    
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const view = this.getAttribute('data-view');
            packagesGrid.className = `packages-grid view-${view}`;
        });
    });
    
    // Sort functionality
    document.getElementById('sortBy').addEventListener('change', function() {
        // Implement sorting via AJAX or page reload
        const sortValue = this.value;
        const url = new URL(window.location);
        url.searchParams.set('sort', sortValue);
        window.location.href = url.toString();
    });
});

// Filter form submission
document.getElementById('packageFilters').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const params = new URLSearchParams();
    
    for (let [key, value] of formData.entries()) {
        if (params.has(key)) {
            params.append(key, value);
        } else {
            params.set(key, value);
        }
    }
    
    // Add current page URL parameters
    const currentUrl = new URL(window.location);
    params.set('filtered', '1');
    
    // Reload page with filters
    window.location.href = currentUrl.pathname + '?' + params.toString();
});

function clearFilters() {
    document.getElementById('packageFilters').reset();
    const url = window.location.pathname;
    window.location.href = url;
}

// Modal functions
function openLeadModal(packageId = null) {
    if (packageId) {
        document.getElementById('modalPackageId').value = packageId;
    }
    document.getElementById('leadModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeLeadModal() {
    document.getElementById('leadModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function openQuickView(packageId) {
    const modal = document.getElementById('quickViewModal');
    const content = document.getElementById('quickViewContent');
    
    modal.style.display = 'block';
    content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Carregando...</div>';
    document.body.style.overflow = 'hidden';
    
    // Load package details via AJAX
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=travelcurator_quick_view&package_id=${packageId}&nonce=<?php echo wp_create_nonce('travelcurator_quick_view_nonce'); ?>`
    })
    .then(response => response.text())
    .then(data => {
        content.innerHTML = data;
    })
    .catch(error => {
        content.innerHTML = '<div class="error">Erro ao carregar informações do pacote.</div>';
    });
}

function closeQuickView() {
    document.getElementById('quickViewModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function toggleWishlist(packageId) {
    const btn = event.target.closest('.wishlist-btn');
    const icon = btn.querySelector('i');
    
    // Toggle visual state
    if (icon.classList.contains('far')) {
        icon.classList.remove('far');
        icon.classList.add('fas');
        btn.classList.add('active');
    } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
        btn.classList.remove('active');
    }
    
    // Save to localStorage (or send to server)
    let wishlist = JSON.parse(localStorage.getItem('travelcurator_wishlist') || '[]');
    const index = wishlist.indexOf(packageId);
    
    if (index === -1) {
        wishlist.push(packageId);
    } else {
        wishlist.splice(index, 1);
    }
    
    localStorage.setItem('travelcurator_wishlist', JSON.stringify(wishlist));
}

// Load wishlist state on page load
document.addEventListener('DOMContentLoaded', function() {
    const wishlist = JSON.parse(localStorage.getItem('travelcurator_wishlist') || '[]');
    
    document.querySelectorAll('.wishlist-btn').forEach(btn => {
        const packageCard = btn.closest('.package-card');
        const packageId = parseInt(packageCard.dataset.packageId || 0);
        
        if (wishlist.includes(packageId)) {
            const icon = btn.querySelector('i');
            icon.classList.remove('far');
            icon.classList.add('fas');
            btn.classList.add('active');
        }
    });
});

// Close modals on outside click
window.onclick = function(event) {
    const leadModal = document.getElementById('leadModal');
    const quickModal = document.getElementById('quickViewModal');
    
    if (event.target === leadModal) {
        closeLeadModal();
    }
    if (event.target === quickModal) {
        closeQuickView();
    }
}

// Lead form submission
document.getElementById('leadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    submitBtn.disabled = true;
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Solicitação enviada com sucesso! Entraremos em contato em breve.');
            closeLeadModal();
            this.reset();
        } else {
            alert('Erro ao enviar solicitação: ' + data.data);
        }
    })
    .catch(error => {
        alert('Erro ao enviar solicitação. Tente novamente.');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>

<style>
/* Archive Styles */
.travelcurator-archive {
    font-family: 'Open Sans', sans-serif;
}

.archive-hero {
    background: linear-gradient(135deg, #1A3A5F 0%, #2C5F84 100%);
    color: white;
    padding: 4rem 0;
    text-align: center;
}

.archive-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.archive-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto;
}

.archive-content {
    padding: 3rem 0;
}

/* Filters Sidebar */
.filters-sidebar {
    background: #fff;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: sticky;
    top: 2rem;
    max-height: calc(100vh - 4rem);
    overflow-y: auto;
}

.filters-sidebar h3 {
    color: #1A3A5F;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #F2B705;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-group {
    margin-bottom: 2rem;
}

.filter-group h4 {
    color: #1A3A5F;
    margin-bottom: 1rem;
    font-size: 1rem;
}

.price-range {
    position: relative;
}

.price-slider {
    width: 100%;
    margin: 0.5rem 0;
    -webkit-appearance: none;
    appearance: none;
    height: 5px;
    border-radius: 5px;
    background: #ddd;
    outline: none;
}

.price-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #F2B705;
    cursor: pointer;
}

.price-labels {
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
    color: #666;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.checkbox-group label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 5px;
    transition: background 0.2s;
}

.checkbox-group label:hover {
    background: #f8f9fa;
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1.5rem;
}

.filter-actions .btn {
    flex: 1;
    padding: 0.75rem;
    font-size: 0.9rem;
}

/* Packages Header */
.packages-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
    background: #fff;
    padding: 1rem 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.results-count {
    font-weight: bold;
    color: #1A3A5F;
}

.sort-options select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    background: white;
    cursor: pointer;
}

.view-toggle {
    display: flex;
    gap: 0.5rem;
}

.view-btn {
    background: #f8f9fa;
    border: 1px solid #ddd;
    color: #666;
    padding: 0.5rem 0.75rem;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.2s;
}

.view-btn.active,
.view-btn:hover {
    background: #F2B705;
    border-color: #F2B705;
    color: white;
}

/* Packages Grid */
.packages-grid {
    display: grid;
    gap: 1.5rem;
}

.view-grid {
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
}

.view-list {
    grid-template-columns: 1fr;
}

.view-list .package-card {
    display: flex;
    align-items: stretch;
}

.view-list .card-image {
    width: 300px;
    flex-shrink: 0;
}

.view-list .card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Package Card */
.package-card {
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
}

.package-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.card-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.package-card:hover .card-img {
    transform: scale(1.05);
}

.placeholder-image {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #e9ecef, #dee2e6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 3rem;
}

.package-labels {
    position: absolute;
    top: 1rem;
    left: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.category-label {
    background: rgba(26, 58, 95, 0.9);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
}

.difficulty-label {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
}

.difficulty-easy {
    background: rgba(40, 167, 69, 0.9);
    color: white;
}

.difficulty-moderate {
    background: rgba(255, 193, 7, 0.9);
    color: #856404;
}

.difficulty-hard {
    background: rgba(220, 53, 69, 0.9);
    color: white;
}

.wishlist-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    color: #666;
}

.wishlist-btn:hover,
.wishlist-btn.active {
    background: #fff;
    color: #e74c3c;
    transform: scale(1.1);
}

.card-content {
    padding: 1.5rem;
}

.package-title {
    margin: 0 0 0.5rem 0;
    font-size: 1.3rem;
    font-weight: 700;
}

.package-title a {
    color: #1A3A5F;
    text-decoration: none;
    transition: color 0.2s;
}

.package-title a:hover {
    color: #F2B705;
}

.package-location {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.package-location i {
    color: #F2B705;
}

.package-excerpt {
    color: #666;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.package-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.9rem;
    color: #666;
}

.meta-item i {
    color: #F2B705;
}

.purpose-tags {
    margin-bottom: 1rem;
}

.purpose-tag {
    display: inline-block;
    background: #e9ecef;
    color: #495057;
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.8rem;
    margin: 0.2rem 0.2rem 0.2rem 0;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: auto;
}

.package-price {
    text-align: left;
}

.price-label {
    display: block;
    font-size: 0.8rem;
    color: #666;
    margin-bottom: 0.25rem;
}

.price-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #F2B705;
}

.price-per {
    display: block;
    font-size: 0.8rem;
    color: #666;
}

.card-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.card-actions .btn {
    font-size: 0.8rem;
    padding: 0.5rem 1rem;
    white-space: nowrap;
}

/* No packages */
.no-packages {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.no-packages-content i {
    color: #dee2e6;
    margin-bottom: 1rem;
}

.no-packages-content h3 {
    color: #1A3A5F;
    margin-bottom: 1rem;
}

.no-packages-content p {
    color: #666;
    margin-bottom: 2rem;
}

/* Pagination */
.packages-pagination {
    margin-top: 3rem;
    text-align: center;
}

.packages-pagination .page-numbers {
    display: inline-block;
    padding: 0.5rem 1rem;
    margin: 0 0.25rem;
    background: #fff;
    border: 1px solid #ddd;
    color: #666;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.2s;
}

.packages-pagination .page-numbers:hover,
.packages-pagination .page-numbers.current {
    background: #F2B705;
    border-color: #F2B705;
    color: white;
}

.packages-pagination .page-numbers.prev,
.packages-pagination .page-numbers.next {
    font-weight: bold;
}

/* Modal Styles */
.travelcurator-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    backdrop-filter: blur(5px);
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 2rem;
    border-radius: 10px;
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.modal-large {
    max-width: 900px;
}

.close {
    position: absolute;
    right: 1rem;
    top: 1rem;
    font-size: 1.5rem;
    font-weight: bold;
    cursor: pointer;
    color: #aaa;
    z-index: 1;
}

.close:hover {
    color: #000;
}

.lead-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #1A3A5F;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #F2B705;
    box-shadow: 0 0 5px rgba(242, 183, 5, 0.3);
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
}

.loading, .error {
    text-align: center;
    padding: 2rem;
    font-size: 1.1rem;
}

.loading i {
    font-size: 2rem;
    color: #F2B705;
    margin-bottom: 1rem;
}

.error {
    color: #dc3545;
}

/* Responsive */
@media (max-width: 1200px) {
    .view-grid {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
}

@media (max-width: 768px) {
    .archive-title {
        font-size: 2rem;
    }
    
    .archive-content {
        padding: 2rem 0;
    }
    
    .packages-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .view-grid {
        grid-template-columns: 1fr;
    }
    
    .view-list .package-card {
        flex-direction: column;
    }
    
    .view-list .card-image {
        width: 100%;
        height: 200px;
    }
    
    .filters-sidebar {
        position: static;
        max-height: none;
        margin-bottom: 2rem;
    }
    
    .lead-form .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        justify-content: center;
        flex-direction: column;
    }
    
    .modal-content {
        width: 95%;
        margin: 10% auto;
        padding: 1.5rem;
    }
    
    .card-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .card-actions {
        flex-direction: row;
        width: 100%;
    }
    
    .card-actions .btn {
        flex: 1;
    }
}

@media (max-width: 480px) {
    .package-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .packages-pagination .page-numbers {
        padding: 0.4rem 0.6rem;
        font-size: 0.9rem;
    }
}