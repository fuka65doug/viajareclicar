<?php
/**
 * The template for displaying single travel packages
 *
 * @package TravelCurator
 */

get_header(); ?>

<div class="travelcurator-single-package">
    <?php while (have_posts()) : the_post(); 
        $package_id = get_the_ID();
        $price = get_post_meta($package_id, '_travelcurator_price', true);
        $duration = get_post_meta($package_id, '_travelcurator_duration', true);
        $difficulty = get_post_meta($package_id, '_travelcurator_difficulty', true);
        $start_date = get_post_meta($package_id, '_travelcurator_start_date', true);
        $end_date = get_post_meta($package_id, '_travelcurator_end_date', true);
        $location = get_post_meta($package_id, '_travelcurator_location', true);
        $include_items = get_post_meta($package_id, '_travelcurator_include_items', true);
        $exclude_items = get_post_meta($package_id, '_travelcurator_exclude_items', true);
        $gallery = get_post_meta($package_id, '_travelcurator_gallery', true);
        $itinerary = get_post_meta($package_id, '_travelcurator_itinerary', true);
        $hotel_name = get_post_meta($package_id, '_travelcurator_hotel_name', true);
        $hotel_stars = get_post_meta($package_id, '_travelcurator_hotel_stars', true);
        $hotel_desc = get_post_meta($package_id, '_travelcurator_hotel_description', true);
    ?>

    <!-- Hero Section -->
    <section class="package-hero">
        <div class="hero-image">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('full', array('class' => 'hero-img')); ?>
            <?php endif; ?>
            <div class="hero-overlay">
                <div class="container">
                    <div class="hero-content">
                        <h1 class="package-title"><?php the_title(); ?></h1>
                        <div class="package-meta">
                            <span class="location">
                                <i class="fas fa-map-marker-alt"></i> <?php echo esc_html($location); ?>
                            </span>
                            <span class="duration">
                                <i class="fas fa-clock"></i> <?php echo esc_html($duration); ?>
                            </span>
                            <span class="difficulty difficulty-<?php echo esc_attr($difficulty); ?>">
                                <i class="fas fa-signal"></i> <?php echo ucfirst($difficulty); ?>
                            </span>
                        </div>
                        <div class="price-section">
                            <span class="price-label">A partir de</span>
                            <span class="price">R$ <?php echo number_format($price, 2, ',', '.'); ?></span>
                            <span class="price-per">por pessoa</span>
                        </div>
                        <button class="btn btn-primary btn-cta" onclick="openLeadModal()">
                            <i class="fas fa-paper-plane"></i> Solicitar Orçamento
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container package-content">
        <div class="row">
            <div class="col-lg-8">
                <!-- Package Description -->
                <section class="package-section">
                    <h2><i class="fas fa-info-circle"></i> Sobre este Pacote</h2>
                    <div class="package-description">
                        <?php the_content(); ?>
                    </div>
                </section>

                <!-- Gallery -->
                <?php if (!empty($gallery)) : ?>
                <section class="package-section">
                    <h2><i class="fas fa-images"></i> Galeria de Fotos</h2>
                    <div class="package-gallery">
                        <?php foreach ($gallery as $image_id) : 
                            $image = wp_get_attachment_image_src($image_id, 'large');
                            $thumb = wp_get_attachment_image_src($image_id, 'medium');
                        ?>
                        <div class="gallery-item" onclick="openLightbox('<?php echo esc_url($image[0]); ?>')">
                            <img src="<?php echo esc_url($thumb[0]); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Itinerary -->
                <?php if (!empty($itinerary)) : ?>
                <section class="package-section">
                    <h2><i class="fas fa-route"></i> Roteiro Detalhado</h2>
                    <div class="itinerary-timeline">
                        <?php foreach ($itinerary as $day) : ?>
                        <div class="itinerary-day">
                            <div class="day-number">Dia <?php echo esc_html($day['day']); ?></div>
                            <div class="day-content">
                                <h4><?php echo esc_html($day['title']); ?></h4>
                                <p><?php echo wp_kses_post($day['description']); ?></p>
                                <?php if (!empty($day['activities'])) : ?>
                                <ul class="activities-list">
                                    <?php foreach ($day['activities'] as $activity) : ?>
                                    <li><i class="fas fa-check"></i> <?php echo esc_html($activity); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Accommodation -->
                <?php if (!empty($hotel_name)) : ?>
                <section class="package-section">
                    <h2><i class="fas fa-bed"></i> Hospedagem</h2>
                    <div class="accommodation-card">
                        <div class="hotel-header">
                            <h4><?php echo esc_html($hotel_name); ?></h4>
                            <div class="hotel-stars">
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <i class="fas fa-star <?php echo $i <= $hotel_stars ? 'active' : 'inactive'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p><?php echo wp_kses_post($hotel_desc); ?></p>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Includes/Excludes -->
                <section class="package-section">
                    <div class="row">
                        <?php if (!empty($include_items)) : ?>
                        <div class="col-md-6">
                            <h3><i class="fas fa-check-circle text-success"></i> O que está incluso</h3>
                            <ul class="include-list">
                                <?php foreach ($include_items as $item) : ?>
                                <li><i class="fas fa-check"></i> <?php echo esc_html($item); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($exclude_items)) : ?>
                        <div class="col-md-6">
                            <h3><i class="fas fa-times-circle text-warning"></i> O que não está incluso</h3>
                            <ul class="exclude-list">
                                <?php foreach ($exclude_items as $item) : ?>
                                <li><i class="fas fa-times"></i> <?php echo esc_html($item); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="package-sidebar sticky-sidebar">
                    <!-- Quick Info Card -->
                    <div class="info-card">
                        <h3>Informações Rápidas</h3>
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <div>
                                <strong>Período</strong>
                                <span><?php echo date('d/m/Y', strtotime($start_date)) . ' a ' . date('d/m/Y', strtotime($end_date)); ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Duração</strong>
                                <span><?php echo esc_html($duration); ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-signal"></i>
                            <div>
                                <strong>Dificuldade</strong>
                                <span class="difficulty-badge difficulty-<?php echo esc_attr($difficulty); ?>">
                                    <?php echo ucfirst($difficulty); ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Taxonomies -->
                        <?php 
                        $purposes = get_the_terms($package_id, 'travel_purpose');
                        if ($purposes && !is_wp_error($purposes)) :
                        ?>
                        <div class="info-item">
                            <i class="fas fa-heart"></i>
                            <div>
                                <strong>Propósito Emocional</strong>
                                <?php foreach ($purposes as $purpose) : ?>
                                <span class="purpose-tag"><?php echo esc_html($purpose->name); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php 
                        $amenities = get_the_terms($package_id, 'travel_amenity');
                        if ($amenities && !is_wp_error($amenities)) :
                        ?>
                        <div class="info-item">
                            <i class="fas fa-star"></i>
                            <div>
                                <strong>Comodidades</strong>
                                <div class="amenities-list">
                                    <?php foreach ($amenities as $amenity) : ?>
                                    <span class="amenity-tag"><?php echo esc_html($amenity->name); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Price Card -->
                    <div class="price-card">
                        <div class="price-header">
                            <span class="price-value">R$ <?php echo number_format($price, 2, ',', '.'); ?></span>
                            <span class="price-per">por pessoa</span>
                        </div>
                        <button class="btn btn-primary btn-block btn-lg" onclick="openLeadModal()">
                            <i class="fas fa-paper-plane"></i> Solicitar Orçamento
                        </button>
                        <button class="btn btn-outline-success btn-block" onclick="shareOnWhatsApp()">
                            <i class="fab fa-whatsapp"></i> Compartilhar no WhatsApp
                        </button>
                    </div>

                    <!-- Related Packages -->
                    <div class="related-card">
                        <h3>Pacotes Relacionados</h3>
                        <?php
                        $related_args = array(
                            'post_type' => 'travel_package',
                            'posts_per_page' => 3,
                            'post__not_in' => array($package_id),
                            'meta_query' => array(
                                array(
                                    'key' => '_travelcurator_status',
                                    'value' => 'active',
                                    'compare' => '='
                                )
                            )
                        );
                        $related_query = new WP_Query($related_args);
                        
                        if ($related_query->have_posts()) :
                            while ($related_query->have_posts()) : $related_query->the_post();
                                $related_price = get_post_meta(get_the_ID(), '_travelcurator_price', true);
                        ?>
                        <div class="related-item">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                <div class="related-thumb">
                                    <?php the_post_thumbnail('medium'); ?>
                                </div>
                                <?php endif; ?>
                                <div class="related-content">
                                    <h5><?php the_title(); ?></h5>
                                    <span class="related-price">R$ <?php echo number_format($related_price, 2, ',', '.'); ?></span>
                                </div>
                            </a>
                        </div>
                        <?php 
                            endwhile;
                            wp_reset_postdata();
                        endif; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php endwhile; ?>
</div>

<!-- Lead Modal -->
<div id="leadModal" class="travelcurator-modal">
    <div class="modal-content">
        <span class="close" onclick="closeLeadModal()">&times;</span>
        <h2>Solicitar Orçamento</h2>
        <form id="leadForm" class="lead-form">
            <input type="hidden" name="package_id" value="<?php echo get_the_ID(); ?>">
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
                <textarea id="lead_message" name="message" rows="4" placeholder="Conte-nos mais sobre suas expectativas para esta viagem..."></textarea>
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

<!-- Lightbox -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <img id="lightboxImage" class="lightbox-content">
</div>

<?php get_footer(); ?>

<script>
// Lead Modal Functions
function openLeadModal() {
    document.getElementById('leadModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeLeadModal() {
    document.getElementById('leadModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Lightbox Functions
function openLightbox(imageSrc) {
    document.getElementById('lightbox').style.display = 'block';
    document.getElementById('lightboxImage').src = imageSrc;
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// WhatsApp Share
function shareOnWhatsApp() {
    const title = '<?php echo esc_js(get_the_title()); ?>';
    const url = '<?php echo esc_url(get_permalink()); ?>';
    const text = `Olha que viagem incrível eu encontrei: ${title} - ${url}`;
    const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(text)}`;
    window.open(whatsappUrl, '_blank');
}

// Close modal on outside click
window.onclick = function(event) {
    const modal = document.getElementById('leadModal');
    const lightbox = document.getElementById('lightbox');
    if (event.target === modal) {
        closeLeadModal();
    }
    if (event.target === lightbox) {
        closeLightbox();
    }
}

// Form submission
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
/* Package Single Styles */
.travelcurator-single-package {
    font-family: 'Open Sans', sans-serif;
}

.package-hero {
    position: relative;
    height: 60vh;
    min-height: 500px;
    overflow: hidden;
}

.hero-image {
    position: relative;
    width: 100%;
    height: 100%;
}

.hero-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, rgba(26, 58, 95, 0.8), rgba(242, 183, 5, 0.6));
    display: flex;
    align-items: center;
}

.hero-content {
    text-align: center;
    color: white;
}

.package-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.package-meta {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.package-meta span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.1rem;
}

.price-section {
    margin-bottom: 2rem;
}

.price-label {
    display: block;
    font-size: 1rem;
    opacity: 0.9;
}

.price {
    display: block;
    font-size: 3rem;
    font-weight: 700;
    color: #F2B705;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.price-per {
    display: block;
    font-size: 1rem;
    opacity: 0.9;
}

.btn-cta {
    font-size: 1.2rem;
    padding: 1rem 2rem;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.btn-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

.package-content {
    padding: 4rem 0;
}

.package-section {
    margin-bottom: 3rem;
    padding: 2rem;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.package-section h2 {
    color: #1A3A5F;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #F2B705;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.package-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.gallery-item {
    cursor: pointer;
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.gallery-item:hover {
    transform: scale(1.05);
}

.gallery-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.itinerary-timeline {
    position: relative;
}

.itinerary-day {
    display: flex;
    margin-bottom: 2rem;
    position: relative;
}

.day-number {
    background: #1A3A5F;
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
    margin-right: 1.5rem;
}

.day-content {
    flex: 1;
    padding-top: 0.5rem;
}

.activities-list {
    list-style: none;
    padding: 0;
    margin-top: 1rem;
}

.activities-list li {
    padding: 0.25rem 0;
    color: #666;
}

.activities-list li i {
    color: #F2B705;
    margin-right: 0.5rem;
}

.accommodation-card {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    border-left: 4px solid #F2B705;
}

.hotel-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1rem;
}

.hotel-stars .fa-star.active {
    color: #F2B705;
}

.hotel-stars .fa-star.inactive {
    color: #ddd;
}

.include-list, .exclude-list {
    list-style: none;
    padding: 0;
}

.include-list li, .exclude-list li {
    padding: 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.include-list li i {
    color: #28a745;
}

.exclude-list li i {
    color: #ffc107;
}

/* Sidebar */
.package-sidebar {
    padding-left: 2rem;
}

.sticky-sidebar {
    position: sticky;
    top: 2rem;
}

.info-card, .price-card, .related-card {
    background: #fff;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 1.5rem;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.info-item i {
    color: #F2B705;
    font-size: 1.2rem;
    margin-top: 0.2rem;
}

.info-item div {
    flex: 1;
}

.info-item strong {
    display: block;
    color: #1A3A5F;
    margin-bottom: 0.25rem;
}

.difficulty-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
}

.difficulty-easy {
    background: #d4edda;
    color: #155724;
}

.difficulty-moderate {
    background: #fff3cd;
    color: #856404;
}

.difficulty-hard {
    background: #f8d7da;
    color: #721c24;
}

.purpose-tag, .amenity-tag {
    display: inline-block;
    background: #e9ecef;
    color: #495057;
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.8rem;
    margin: 0.2rem 0.2rem 0.2rem 0;
}

.amenities-list {
    margin-top: 0.5rem;
}

.price-card {
    text-align: center;
    background: linear-gradient(135deg, #1A3A5F, #2C5F84);
    color: white;
}

.price-header {
    margin-bottom: 1.5rem;
}

.price-value {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    color: #F2B705;
}

.related-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.related-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.related-thumb {
    width: 80px;
    height: 60px;
    border-radius: 5px;
    overflow: hidden;
    flex-shrink: 0;
}

.related-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-content h5 {
    margin: 0 0 0.25rem 0;
    font-size: 0.9rem;
    line-height: 1.3;
}

.related-price {
    color: #F2B705;
    font-weight: bold;
    font-size: 0.9rem;
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

/* Lightbox */
.lightbox {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.9);
}

.lightbox-content {
    margin: auto;
    display: block;
    max-width: 90%;
    max-height: 90%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.lightbox-close {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
}

.lightbox-close:hover {
    color: #F2B705;
}

/* Responsive */
@media (max-width: 768px) {
    .package-title {
        font-size: 2rem;
    }
    
    .price {
        font-size: 2rem;
    }
    
    .package-meta {
        gap: 1rem;
    }
    
    .hero-content {
        padding: 0 1rem;
    }
    
    .package-sidebar {
        padding-left: 0;
        margin-top: 2rem;
    }
    
    .lead-form .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        justify-content: center;
    }
    
    .modal-content {
        width: 95%;
        margin: 10% auto;
        padding: 1.5rem;
    }
}