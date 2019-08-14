<?php
namespace ElementorLightXWidgets\Widgets\SignInButton;

class SignInButton extends \Elementor\Widget_Base {
	
    public function get_name() {
		return 'SignInButton';
	}

	public function get_title() {
		return __( 'SignInButton', 'elementor-lightx-widgets' );
	}

	public function get_icon() {
		return 'fa fa-user';
	}

	public function get_categories() {
		return [ 'elementor-lightx-widgets-category' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'style_variation_section',
			[
				'label' => __( 'כפתור', 'elementor-lightx-widgets' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'signin-page-url',
			[
				'label' => __( 'קישור לעמוד התחברות / הרשמה', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::URL,
			]
		);

		$this->add_control(
			'username-text',
			[
				'label' => __( 'טקסט לפני שם המשתמש', 'elementor-lightx-widgets' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$current_user = wp_get_current_user();
		// printf( __( 'User first name: %s', 'textdomain' ), esc_html( $current_user->user_firstname ) ) . '<br />';
		// printf( __( 'User last name: %s', 'textdomain' ), esc_html( $current_user->user_lastname ) ) . '

		if(!$current_user->exists()) {
			printf( __( '
				<a href="%s" class="signin-button" role="button" style="text-align: left; color: #58585a">
					<span class="signin-button-content-wrapper">
						<span class="signin-button-text">כניסה</span>
					</span>
				</a>
				', 'textdomain'), $settings['signin-page-url']['url'] );
		} else {
			printf(__( '
				<span class="username-content-wrapper">
					<span class="username-text">%s %s - </span>
				</span>
				<a href="/wp-login.php?action=logout" class="signout-button" role="button" style="color: #58585a">
					<span class="signout-button-content-wrapper">
						<span class="signout-button-text">התנתקות</span>
					</span>
				</a>
			', 'textdomain' ), esc_html($settings['username-text']), esc_html($this->km_get_users_name(null, $current_user))) ;
		}
		?>
		<?php
	}

	protected function _content_template() {

	}

	function km_get_users_name( $user_id = null, $user = null ) {
		$user_info = $user_id ? new WP_User( $user_id ) : ($user ? $user : wp_get_current_user());
		if ( $user_info->first_name ) {
			if ( $user_info->last_name ) {
				return $user_info->first_name . ' ' . $user_info->last_name;
			}
			return $user_info->first_name;
		}
		return $user_info->display_name;
	}
}