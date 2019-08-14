<?php
namespace ElementorLightXWidgets\Widgets\VariationChooser;

class VariationsChooser extends \Elementor\Widget_Base {

    public function get_name() {
		return 'VariationsChooser';
	}

	public function get_title() {
		return __( 'VariationsChooser', 'elementor-lightx-widgets' );
	}

	public function get_icon() {
		return 'fa fa-plus';
	}

	public function get_categories() {
		return [ 'elementor-lightx-widgets-category' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'style_variation_section',
			[
				'label' => __( 'וריאציה', 'elementor-lightx-widgets' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'Variations_variation_alignment',
			[
				'label' => __( 'מיקום טקסט', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'right' => [
						'title' => __( 'ימין', 'elementor-lightx-widgets' ),
						'icon' => 'fa fa-align-right',
					],
					'center' => [
						'title' => __( 'מרכז', 'elementor-lightx-widgets' ),
						'icon' => 'fa fa-align-center',
					],
					'left' => [
						'title' => __( 'שמאל', 'elementor-lightx-widgets' ),
						'icon' => 'fa fa-align-left',
					],
				],
				'default' => 'center',
			]
		);

		$this->add_control(
			'Variations_variation_space',
			[
				'label' => __( 'מרווח', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .variation > *' => 'padding-top: {{SIZE}}{{UNIT}}',
				],
				
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_icon_section',
			[
				'label' => __( 'אייקון', 'elementor-lightx-widgets' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'Variation_icon_color',
			[
				'label' => __( 'צבע', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .variation .variation-icon i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'Variation_icon_size',
			[
				'label' => __( 'גודל האייקון', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
						'step' => 0.1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 14,
				],
				'selectors' => [
					'{{WRAPPER}} .variation .variation-icon i' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'Variation_icon_icon',
			[
				'label' => __( 'אייקון', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->end_controls_section();
		
		$this->start_controls_section(
			'style_background_section',
			[
				'label' => __( 'רקע', 'elementor-lightx-widgets' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'Variation_background_color',
			[
				'label' => __( 'צבע', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .variation .variation-icon' => 'background-color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			'Variation_background_selected_color',
			[
				'label' => __( 'צבע רקע נבחר', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .variation.selected .variation-icon' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .variation.hovered .variation-icon' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'Variation_background_radius',
			[
				'label' => __( 'עיגול דפנות', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .variation .variation-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'Variation_background_padding',
			[
				'label' => __( 'גודל עיגול', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'selectors' => [
					'{{WRAPPER}} .variation .variation-icon' => 'width: {{VALUE}}px; height: {{VALUE}}px',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_name_section',
			[
				'label' => __( 'שם וריאציה', 'elementor-lightx-widgets' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'Variation_name_color',
			[
				'label' => __( 'צבע', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .variation .variation-name' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'Variation_name_size',
			[
				'label' => __( 'גודל', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'pt', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'pt' => [
						'min' => 0,
						'max' => 100,
						'step' => 2,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'pt',
					'size' => 16,
				],
				'selectors' => [
					'{{WRAPPER}} .variation .variation-name' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_price_section',
			[
				'label' => __( 'מחיר', 'elementor-lightx-widgets' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'Variation_price_color',
			[
				'label' => __( 'צבע', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .variation .variation-price .amount' => 'color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'Variation_price_line_height',
			[
				'label' => __( 'גודל שורה (em)', 'elementor-lightx-widgets' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
				'selectors' => [
					'{{WRAPPER}} .variation .variation-price .amount' => 'line-height: {{VALUE}}',
                ],
                'min' => 0,
                'max' => 10,
				'default' => 1,
			]
		);

		$this->add_control(
			'Variation_price_font-weight',
			[
				'label' => __( 'עובי פונט', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'selectors' => [
					'{{WRAPPER}} .variation .variation-price .amount' => 'font-weight: {{VALUE}}',
                ],
                'options' => [
                    '100' => 'ExtraLight',
                    '200' => 'Light',
                    '300' => 'Regular',
                    '400' => 'SemiBold',
                    '500' => 'Bold',
                    '600' => 'ExtraBold',
                ]
			]
		);

		$this->add_control(
			'Variation_price_size',
			[
				'label' => __( 'גודל', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'pt', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'pt' => [
						'min' => 0,
						'max' => 100,
						'step' => 2,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'pt',
					'size' => 16,
				],
				'selectors' => [
					'{{WRAPPER}} .variation .variation-price' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		global $product;

		$variations = $this->getVariations();
		$settings = $this->get_settings_for_display();
		?>
			<div class="variations custom" data-total-variations="<?php echo count($variations); ?>" style="display: <?php echo (!is_bool($product) && $product->is_type( 'variable' ) ? 'flex' : 'none') ?>">
				<?php foreach ($variations as $key => $variation) : ?>
					<div class="variation <?php echo $variation['selected'] ? 'selected' : '' ?>" data-variation-id="<?php echo $variation['id']; ?>" onclick="<?php $this->updateProductPrice($variation) ?>">
						<span class="variation-icon">
							<?php \Elementor\Icons_Manager::render_icon( $settings['Variation_icon_icon'], [ 'aria-hidden' => 'true' ] ); ?>
						</span>
						<div class="variation-price" data-price="<?php echo $variation['price']; ?>">
							<div class="amount"><?php echo $variation['price']; ?> ש"ח</div>
						</div>
						<span class="variation-name">
							<?php echo $variation['name']; ?>
						</span>
					</div>
				<?php endforeach; ?>
			</div>

		<?php
	}

	protected function _content_template() {
		$variations = json_encode($this->getVariations());
		?>
			<div class="variations" style="display: flex;">
			<# _.each( <?php echo $variations; ?>, function( variation, key ) { #>
				<div class="variation">
					<span class="variation-icon">
						{{{ iconHTML.value }}}
					</span>
					<div class="variation-price">
						{{{variation.price_html}}}
					</div>
					<span class="variation-name">
						{{{variation.name}}}
					</span>
				</div>
			<# }); #>
		<?php
	}

	function updateProductPrice($variation) {
		global $product;
		$product->set_price($variation['price']);
		echo $product->get_price();
	}

	function getVariations($variationId = 0) {
		global $product;

		$variations = [];

		if ($product && !is_bool($product) && $product->is_type( 'variable' ))  {
			$available_variations = $product->get_available_variations();

			foreach ($available_variations as $key => $variation) {
				$variations[] = Array(
					'name' => $variation['attributes'][key($variation['attributes'])],
					'price_html' => $variation['price_html'],
					'price' => $variation['display_price'],
					'id' => $variation['variation_id'],
					'selected' => $key == 0,
				);

				if($variationId == $variation['variation_id'])
					return $variations[count($variations)-1];
			}
		} 

		return $variations;
	}

	function updatePrice($variation) {
		if(isset($_GET['v']))
			print_r($variation);
	}	 
}