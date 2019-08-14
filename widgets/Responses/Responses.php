<?php
namespace ElementorLightXWidgets\Widgets;

class Responses extends \Elementor\Widget_Base {
	
    public function get_name() {
		return 'Responses';
	}

	public function get_title() {
		return __( 'Responses', 'lightx-widget' );
	}

	public function get_icon() {
		return 'fa fa-code';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'תגובות', 'lightx-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'responses_amount',
			[
				'label' => __( 'כמות תגובות לדף', 'lightx-widget' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'placeholder' => __( 'כמות', 'lightx-widget' ),
				'default' => 5,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'עיצוב', 'lightx-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		
		$this->add_control(
			'responses_text_alignment',
			[
				'label' => __( 'מיקום טקסט', 'lightx-widget' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'right' => [
						'title' => __( 'ימין', 'lightx-widget' ),
						'icon' => 'fa fa-align-right',
					],
					'center' => [
						'title' => __( 'מרכז', 'lightx-widget' ),
						'icon' => 'fa fa-align-center',
					],
					'left' => [
						'title' => __( 'שמאל', 'lightx-widget' ),
						'icon' => 'fa fa-align-left',
					],
				],
				'default' => 'center',
			]
		);

		$this->end_controls_section();
	}

	protected function generateResponses($settings, $offset) {
		$totalOffet = (int)$offset * $settings['responses_amount'] - $settings['responses_amount'];
		if((int)$offset == 0)
			$totalOffet = 0;
		$args = array(
            'offset' => $totalOffet,
            'post_type' => "Response",
            'order' => 'ASC',
            'numberposts' => $settings['responses_amount'],
            'include' => '',
        );
		$posts_array = get_posts( $args );

		foreach ($posts_array as $key => $post) {
			$post->responseId = get_post_meta($post->ID)['responseId'][0];
		}

		return $posts_array;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();    	
		$url = explode('/', wp_parse_url( $_SERVER['REQUEST_URI'], -1 )['path']);
		$offset = isset($url[count($url)-1]) ? $url[count($url)-1] : 0;
		$responses = $this->generateResponses($settings, $offset);
		$userId = get_current_user_id(); ?>
			<span style="display: none;" class="userId"><?php echo $userId; ?></span>
			<div class="responses items" data-total-responses="<?php echo wp_count_posts('response')->publish; ?>"
				data-responses-per-page="<?php echo $settings['responses_amount']; ?>">
				<?php foreach ($responses as $key => $response) { 
					if($offset == 0)
						$responseId = $key + 1;
					else
						$responseId = $offset * $settings['responses_amount'] - $settings['responses_amount'] + $key + 1;
					?>

					<div class="response item" data-response-id="<?php echo $response->responseId; ?>">
						<span class="response-number item-number">
							<?php echo $responseId; ?>
						</span>
						<span class="response-bookmark item-bookmark">
							<button onclick="bookmark(<?php echo $response->responseId; ?>)" class="bookmark-button">
								<i aria-hidden="true" class="far fa-bookmark"></i>
							</button>
						</span>
						<div class="response-text item-text" style="text-align: <?php echo $settings["responses_text_alignment"]; ?>;">
							<?php echo $response->post_content; ?>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php
	}

	protected function _content_template() {
		?>
		<h3>{{{ settings.title }}}</h3>
		<?php
	}
}