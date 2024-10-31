<?php

class Postify_Public
{
	private $plugin_name;
	private $version;
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Postify shortcode
		add_shortcode('postify',  array($this, 'postify_shortcode'));
	}

	public function postify_shortcode($atts)
	{
		$atts = shortcode_atts(
			array(
				'posts_per_page' => -1,
				'excerpt_length' => 20,
				'layout' => 'grid',
				'show_button' => true,
				'show_category' => true,
				'theme' => 1,
				'image_width' => 600,
				'image_height' => 400,
				'slides_perview' => 3,
				'navigation' => true,
				'pagination' => true,
				'autoplay' => true,
				'background' => '#4F868F',
				'color' => '#ffffff',
			),
			$atts,
			'postify'
		);

		$args = array(
			'post_type'      => 'post',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => $atts['posts_per_page'],
		);
		$rand = rand();
		$theme = $atts['theme'];
		$image_width = $atts['image_width'];
		$image_height = $atts['image_height'];
		$layout = $atts['layout'];
		$show_button = $atts['show_button'];
		$show_category = $atts['show_category'];
		$navigation = $atts['navigation'];
		$pagination = $atts['pagination'];
		$autoplay = $atts['autoplay'];
		$slides_perview = $atts['slides_perview'];
		$background = $atts['background'];
		$color = $atts['color'];

		$background = sanitize_hex_color($atts['background']); // Sanitize the color value
		echo '<style type="text/css">
			.postify_' . esc_attr($rand) . ' .postify__btn a {
				background-color: ' . esc_attr($background) . ';
				color: ' . esc_attr($color) . ';
			}
			.postify_' . esc_attr($rand) . ' .postify__item--category a {
				background-color: ' . esc_attr($background) . ';
				color: ' . esc_attr($color) . ';
			}
			.postify_' . esc_attr($rand) . '.postify4 .postify__item__content--meta {
				background-color: ' . esc_attr($background) . ';
			}
			.postify_' . esc_attr($rand) . '.postify4 .postify__item__content--meta strong {
				color: ' . esc_attr($color) . ';
			}
			.postify_' . esc_attr($rand) . '.postify [class*="swiper_button__"] {
				background-color: ' . esc_attr($background) . ';
				color: ' . esc_attr($color) . ';
			}
			.postify_' . esc_attr($rand) . '.postify .swiper-pagination .swiper-pagination-bullet {
				background-color: ' . esc_attr($background) . ';
			}
			.postify_' . esc_attr($rand) . '.postify .postify__item--thumbnail img {
				width: ' . esc_attr($image_width) . 'px' . ';
				height: ' . esc_attr($image_height) . 'px' . ';
			}
		</style>';

		if ($theme == '1') {
			$query = new WP_Query($args);
?>
			<div class="postify postify_<?php echo esc_attr($rand); ?>">
				<?php
				if ($layout == 'grid') {
					echo '<div class="row">';
				} else {
				?>
					<div class="swiper swiper-container" data-swiper='{"initialSlide": 5, "slidesPerView": <?php echo esc_attr($slides_perview) ?>, "loop": true, "autoplay": <?php echo esc_attr($autoplay) ?>, "speed": 2000, "spaceBetween": 30, "pagination": <?php echo $pagination ? '{"el": ".swiper-pagination", "clickable": true}' : 'false' ?>, "navigation": <?php echo $navigation ? '{ "nextEl": ".swiper_button__next", "prevEl": ".swiper_button__prev"}' : 'false' ?> }'>

						<div class="swiper-wrapper">
						<?php
					}
						?>
						<?php
						if ($query->have_posts()) {
							while ($query->have_posts()) : $query->the_post();
								$thumbnail_id = get_post_thumbnail_id();
								$image_url = wp_get_attachment_image_src($thumbnail_id, 'large', true);
								$placeholder_img = plugin_dir_url(__FILE__) . 'img/placeholder.png';

								echo '<div class="' . ($layout == 'grid' ? 'col-lg-4 col-md-6 col-sm-6' : 'swiper-slide') . '">
							<article class="postify__item">
								<div class="postify__item--thumbnail">';
								if ($image_url) {
									echo '<img src="' . esc_url($image_url[0]) . '" alt="' . esc_attr(get_the_title()) . '">';
								} else {
									echo '<img src="' . esc_url($placeholder_img) . '" alt="' . esc_attr(get_the_title()) . '">';
								}
								echo '</div>
								<div class="postify__item--category">';
								if ($show_category == 'true') {
									$categories = get_the_category();
									if ($categories) {
										echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
									}
								}
								echo '</div>
								<div class="postify__item__content">
								<span class="postify__item__content--meta">
									<a href="' . esc_url(get_day_link(get_post_time('Y'), get_post_time('m'), get_post_time('j'))) . '">' . esc_html(get_the_date()) . '</a>
								</span>
								<div class="postify__item__content--title">';
								if (get_the_title()) {
									echo '<h3><a href="' . esc_url(get_the_permalink()) . '">' . esc_html(get_the_title()) . '</a></h3>';
								}
								echo '</div>
								<div class="postify__item__content--description">
								<p>' . esc_html(wp_trim_words(get_the_excerpt(), intval($atts['excerpt_length']))) . '</p>
								</div>';

								if ($show_button == 'true') {
									echo '<div class="postify__btn">
									<a href="' . esc_url(get_the_permalink()) . '">' . esc_html__('Read more', 'postify') . ' <i class="fa-solid fa-angle-right"></i></a>
									</div>';
								}
								echo '</div>
							</article>
							</div>';
							endwhile;
							wp_reset_query();
						}

						echo '</div>';
						if ($layout == 'carousel') {
							if ($pagination == 'true') {
								echo '<div class="swiper-pagination"></div>';
							}
							echo '</div>';
							if ($navigation == 'true') {
								echo '
							<div class="swiper_button__next">
								<i class="fa-solid fa-chevron-right"></i>
							</div> 
							<div class="swiper_button__prev">
								<i class="fa-solid fa-angle-left"></i>
							</div>';
							}
						}
						echo '</div>';
					} elseif ($theme == '2') {
						$query = new WP_Query($args);
						?>
						<div class="postify postify2 postify_<?php echo esc_attr($rand); ?>">
							<?php
							if ($layout == 'grid') {
								echo '<div class="row">';
							} else {
							?>
								<div class="swiper swiper-container" data-swiper='{"initialSlide": 5, "slidesPerView": <?php echo esc_attr($slides_perview) ?>, "loop": true, "autoplay": {"delay": 2000}, "speed": 1000, "spaceBetween": 30, "pagination": <?php echo $pagination ? '{"el": ".swiper-pagination", "clickable": true}' : false ?>, "navigation": <?php echo $navigation ? '{ "nextEl": ".swiper_button__next", "prevEl": ".swiper_button__prev"}' : false ?> }'>
									<div class="swiper-wrapper">
									<?php
								}
									?>
									<?php
									if ($query->have_posts()) {
										while ($query->have_posts()) : $query->the_post();
											$thumbnail_id = get_post_thumbnail_id();
											$image_url = wp_get_attachment_image_src($thumbnail_id, 'large');
											$placeholder_img = plugin_dir_url(__FILE__) . 'img/placeholder.png';
											echo '<div class="' . ($layout == 'grid' ? 'col-lg-4 col-md-6 col-sm-6' : 'swiper-slide') . '">
	
										<article class="postify__item">
											<div class="postify__item--thumbnail">';
											if ($image_url) {
												echo '<img src="' . esc_url($image_url[0]) . '" alt="' . esc_attr(get_the_title()) . '">';
											} else {
												echo '<img src="' . esc_url($placeholder_img) . '" alt="' . esc_attr(get_the_title()) . '">';
											}
											echo '</div>
											<div class="postify__item--category">';
											$categories = get_the_category();
											if ($categories) {
												echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
											}
											echo '</div>
											<div class="postify__item__content">
												<ul class="postify__item__content--user">
													<li><i class="fa-regular fa-user"></i><a href=' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '>' . esc_html(get_the_author()) . '</a></li>
													<li>
														<i class="fa-regular fa-calendar-days"></i><a href="' . esc_url(get_day_link(get_post_time('Y'), get_post_time('m'), get_post_time('j'))) . '">' . esc_html(get_the_date()) . '</a>
													</li>
												</ul>
												<div class="postify__item__content--title">
													<h3><a href="' . esc_url(get_the_permalink()) . '">' . esc_html(get_the_title()) . '</a></h3>
												</div>
											</div>
										</article>
										</div>';
										endwhile;
										wp_reset_query();
									}

									echo '</div>';
									if ($layout == 'carousel') {
										if ($pagination == 'true') {
											echo '<div class="swiper-pagination"></div>';
										}
										echo '</div>';
										if ($navigation == 'true') {
											echo '
									<div class="swiper_button__next">
										<i class="fa-solid fa-chevron-right"></i>
									</div> 
									<div class="swiper_button__prev">
										<i class="fa-solid fa-angle-left"></i>
									</div>';
										}
									}
									echo '</div>';
								} elseif ($theme == '3') {
									$query = new WP_Query($args);
									?>
									<div class="postify postify4 postify_<?php echo esc_attr($rand); ?>">
										<?php
										if ($layout == 'grid') {
											echo '<div class="row">';
										} else {
										?>
											<div class="swiper swiper-container" data-swiper='{"initialSlide": 5, "slidesPerView": <?php echo esc_attr($slides_perview) ?>, "loop": true, "autoplay": {"delay": 2000}, "speed": 1000, "spaceBetween": 30, "pagination": <?php echo $pagination ? '{"el": ".swiper-pagination", "clickable": true}' : false ?>, "navigation": <?php echo $navigation ? '{ "nextEl": ".swiper_button__next", "prevEl": ".swiper_button__prev"}' : false ?> }'>
												<div class="swiper-wrapper">
												<?php
											}
											?>
									<?php
									if ($query->have_posts()) {
										while ($query->have_posts()) : $query->the_post();
											$thumbnail_id = get_post_thumbnail_id();
											$image_url = wp_get_attachment_image_src($thumbnail_id, 'large');
											$placeholder_img = plugin_dir_url(__FILE__) . 'img/placeholder.png';
											echo '<div class="' . ($layout == 'grid' ? 'col-lg-4 col-md-6 col-sm-6' : 'swiper-slide') . '">
	
									<article class="postify__item">
										<div class="postify__item--thumbnail">';
											if ($image_url) {
												echo '<img src="' . esc_url($image_url[0]) . '" alt="' . esc_attr(get_the_title()) . '">
												<a class="postify__item__content--meta" href="' . esc_url(get_day_link(get_post_time('Y'), get_post_time('m'), get_post_time('j'))) . '"
											><strong>' . esc_html(get_the_date('d')) . '</strong><span>' . esc_html(get_the_date('M')) . '</span></a>
												';
											} else {
												echo '<img src="' . esc_url($placeholder_img) . '" alt="' . esc_attr(get_the_title()) . '">
												<a class="postify__item__content--meta" href="' . esc_url(get_day_link(get_post_time('Y'), get_post_time('m'), get_post_time('j'))) . '"
											><strong>' . esc_html(get_the_date('d')) . '</strong><span>' . esc_html(get_the_date('M')) . '</span></a>
												';
											}
											echo '
										</div>
									
										<div class="postify__item--category">';
											$categories = get_the_category();
											if ($categories) {
												echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>';
											}
											echo '</div>
										<div class="postify__item__content">
										
											<div class="postify__item__content--title">
												<h3><a href="' . esc_url(get_the_permalink()) . '">' . esc_html(get_the_title()) . '</a></h3>
											</div>
											<div class="postify__item__content--description">
												<p>' . esc_html(wp_trim_words(get_the_excerpt(), intval($atts['excerpt_length']))) . '</p>
											</div>';
											if ($show_button == 'true') {
												echo '<div class="postify__btn rounded">
										<a href="' . esc_url(get_the_permalink()) . '">' . esc_html__('Read more', 'postify') . ' <i class="fa-solid fa-angle-right"></i></a>
										</div>';
											}
											echo '</div>
									</article>
									</div>';
										endwhile;
										wp_reset_query();
									}

									echo '</div>';
									if ($layout == 'carousel') {
										if ($pagination == 'true') {
											echo '<div class="swiper-pagination"></div>';
										}
										echo '</div>';
										if ($navigation == 'true') {
											echo '
									<div class="swiper_button__next">
										<i class="fa-solid fa-chevron-right"></i>
									</div> 
									<div class="swiper_button__prev">
										<i class="fa-solid fa-angle-left"></i>
									</div>';
										}
									}
									echo '</div>';
								}
							}

							public function enqueue_styles()
							{
								wp_enqueue_style('fontawesome', plugin_dir_url(__FILE__) . 'css/fontawesome.min.css', array(), $this->version, 'all');
								wp_enqueue_style('grid', plugin_dir_url(__FILE__) . 'css/grid.css', array(), $this->version, 'all');
								wp_enqueue_style('swiper-bundle', plugin_dir_url(__FILE__) . 'css/swiper-bundle.min.css', array(), $this->version, 'all');
								wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/postify-public.css', array(), $this->version, 'all');
							}

							public function enqueue_scripts()
							{
								wp_enqueue_script('swiper-bundle', plugin_dir_url(__FILE__) . 'js/swiper-bundle.min.js', array('jquery'), $this->version, true);
								wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/postify-public.js', array('jquery'), $this->version, true);
							}
						}
