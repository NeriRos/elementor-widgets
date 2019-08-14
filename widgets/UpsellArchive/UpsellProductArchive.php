<?php
namespace ElementorLightXWidgets\Widgets\UpsellArchive;

class UpsellProductArchive extends \Elementor\Widget_Base {

    public function get_name() {
		return 'Custom product archive';
	}

	public function get_title() {
		return __( 'CustomProductArchive', 'plugin-name' );
	}

	public function get_icon() {
		return 'fa fa-plus';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'contents',
			[
				'label' => __( 'תכנים', 'plugin-name' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'product_tag',
			[
				'label' => __( 'טאג מוצר', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'upsell',
			]
		);

		$this->add_control(
			'products_list-columns',
			[
				'label' => __( 'עמודות', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 4,
			]
		);

		$this->add_control(
			'image_url',
			[
				'label' => __( 'אייקון טעינה', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::URL,
				'default' => [
					'url' => plugins_url( '' , __FILE__ ).'/assets/img/loader.gif',
					'is_external' => false,
					'nofollow' => false,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'selectors_product_archive',
			[
				'label' => __( 'סלקטורים של ארכיב מוצרים', 'plugin-name' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'product_archive-container',
			[
				'label' => __( 'מיכל ארכיב מוצרים', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '#upsell_product-archive',
			]
		);

		// $this->add_control(
		// 	'products_list-container',
		// 	[
		// 		'label' => __( 'מיכל רשימת מוצרים', 'plugin-name' ),
		// 		'type' => \Elementor\Controls_Manager::TEXT,
		// 		'default' => '.products_list-container',
		// 	]
		// );


		$this->add_control(
			'products_list',
			[
				'label' => __( 'רשימת מוצרים', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '.products_list',
			]
		);

		$this->add_control(
			'products_list-item',
			[
				'label' => __( 'פריט ארכיב מוצרים', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '.products_list-item',
			]
		);

		$this->add_control(
			'add_to_cart-button',
			[
				'label' => __( 'סלקטור כפתור הוסף לעגלה', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '.add_to_cart_button',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pagination',
			[
				'label' => __( 'סלקטורים של עימוד', 'plugin-name' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'pagination-nav',
			[
				'label' => __( 'סלקטור תפריט עימוד', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '.pagination',
			]
		);

		$this->add_control(
			'pagination-scroll',
			[
				'label' => __( 'סלקטור כפתור מעבר', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '.pagination-scroll_button',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_price_section',
			[
				'label' => __( 'מחיר', 'plugin-name' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
            global $product;

			$settings = $this->get_settings_for_display();
			$columns = $settings['products_list-columns'];
			$products = $this->get_products();
			$this->setJavascriptSelectorsSettings($settings);			
			?>
			<div id="upsell_product-archive">
				  <div class="products_list-container">
					<ul class="products_list columns-<?php echo $columns ?>">
						<?php foreach ($products as $key => $product) : $wc_product = wc_get_product( $product->ID ); ?>
							<li class="products_list-item product<?php echo $key < $columns ? ' product_shown' : ''; ?>" data-product-id="<?php echo $product->ID; ?>">
								<div class="add_to_cart_button-container">
									<button class="add_to_cart_button">
										<i aria-hidden="true" class="fa fa-plus"></i>
									</button>
								</div>
								<a href="<?php echo get_post_permalink($product); ?>" class="product_link">
									<?php $image_url = get_the_post_thumbnail_url( $product, array(125,125) );?>
									<span class="product_image">
										<img height="125" width="125" src="<?php echo $image_url; ?>" alt="teest">
									</span>
									<h2 class="product_title"><?php echo $product->post_title; ?></h2>
									<span class="product_price">
										<?php echo wc_price($wc_product->get_price()); ?>
									</span>    
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
	  			<div class="pagination-container">
                    <div class="pagination">
                        <span><button class="pagination-scroll_button" data-scroll="next"><i class="fa fa-caret-up"></i></button></span>
                        <span><span class="horizontal_seperator"></span></span>
                        <span><button class="pagination-scroll_button" data-scroll="prev"><i class="fa fa-caret-down"></i></button></span>
                    </div>
				</div>
			</div>
		<?php
	}

	protected function _content_template() {
		
			// add_action( 'woocommerce_before_shop_loop_item', 'add_to_cart_text', 1 );
			// function add_to_cart_text() {
			// 	global $woocommerce_loop;
			// 	if ( !is_admin() && is_single() && $woocommerce_loop['name'] == "products" ) {        
			// 		echo do_shortcode('[elementor-template id="2497"]');
			// 	}
			// }
	}
	
	function setJavascriptSelectorsSettings($settings) {
		?>
			<script type="text/javascript">   
				var container_selector = '<?php echo $settings["product_archive-container"]; ?>' ;
				var list_selector = '<?php echo $settings["products_list"]; ?>' ;
				var item_selector = '<?php echo $settings["products_list-item"]; ?>' ;
				var products_list_columns = '<?php echo $settings["products_list-columns"]; ?>' ;
				var add_to_cart_selector = '<?php echo $settings["add_to_cart-button"]; ?>' ;
				var nav_selector = '<?php echo $settings["pagination-nav"]; ?>' ;
				var scroll_selector = '<?php echo $settings["pagination-scroll"]; ?>' ;
                var image_loader = '<?php echo $settings["image_url"]; ?>' ;
                
                window.phpOptions = {
                    container_selector,
                    list_selector,
                    item_selector,
                    products_list_columns,
                    add_to_cart_selector,
                    nav_selector,
                    scroll_selector,
                    image_loader
                };
			</script>
		<?php
	}
    
    function get_products() {
        $products_args = array(
            'post_type' => 'product',
            'posts_per_page' => 12,
            // 'tag' => 'upsell',
			'found_posts' => 12,
			'total' => 12
          );
		  $products = new \WP_Query( $products_args );
		  wp_reset_postdata();
		return $products->posts;
    }
}