<?php
namespace ElementorLightXWidgets\Widgets;

class Utterances extends \Elementor\Widget_Base {
	
    public function get_name() {
		return 'Utterances';
	}

	public function get_title() {
		return __( 'Utterances', 'elementor-lightx-widgets' );
	}

	public function get_icon() {
		return 'fa fa-code';
	}

	public function get_categories() {
		return [ 'elementor-lightx-widgets-category' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'אמרות', 'elementor-lightx-widgets' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'utterances_amount',
			[
				'label' => __( 'כמות אמרות לדף', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'placeholder' => __( 'כמות', 'elementor-lightx-widgets' ),
				'default' => 5,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'עיצוב', 'elementor-lightx-widgets' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		
		$this->add_control(
			'utterances_text_alignment',
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

		$this->end_controls_section();
	}

	protected function generateUtterances($settings, $offset) {
		$totalOffet = (int)$offset * $settings['utterances_amount'] - $settings['utterances_amount'];
		if((int)$offset == 0)
			$totalOffet = 0;
		$args = array('offset' => $totalOffet , 'post_type' => "Utterance", 'order' => 'ASC', 'numberposts' => $settings['utterances_amount']);
		$posts_array = get_posts( $args );

		foreach ($posts_array as $key => $post) {
			$post->utteranceId = get_post_meta($post->ID)['utteranceId'][0];
		}

		return $posts_array;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();    	
		$url = explode('/', wp_parse_url( $_SERVER['REQUEST_URI'], -1 )['path']);
		$offset = isset($url[count($url)-1]) ? $url[count($url)-1] : 0;
		$utterances = $this->generateUtterances($settings, $offset);
		$userId = get_current_user_id(); ?>
		
			<span style="display: none;" class="userId"><?php echo $userId; ?></span>
			<div class="utterances items" data-total-utterances="<?php echo wp_count_posts('utterance')->publish; ?>"
				data-utterances-per-page="<?php echo $settings['utterances_amount']; ?>">
				<?php foreach ($utterances as $key => $utterance) { 
					if($offset == 0)
						$utteranceId = $key + 1;
					else
						$utteranceId = $offset * $settings['utterances_amount'] - $settings['utterances_amount'] + $key + 1;
					?>

					<div class="utterance item" data-utterance-id="<?php echo $utterance->utteranceId; ?>">
						<span class="utterance-number item-number">
							<?php echo $utteranceId; ?>
						</span>
						<span class="utterance-bookmark item-bookmark">
							<button onclick="bookmark(<?php echo $utterance->utteranceId; ?>)" class="bookmark-button">
								<i aria-hidden="true" class="far fa-bookmark"></i>
							</button>
						</span>
						<div class="utterance-text item-text" style="text-align: <?php echo $settings["utterances_text_alignment"]; ?>;">
							<?php echo $utterance->post_content; ?>
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

	function saveBookmark() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'utterances' ) )
			return wp_nonce_ays();

	}
}