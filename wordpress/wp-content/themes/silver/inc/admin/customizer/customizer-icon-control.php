<?php
/**
 * Customizer icon control: the Font Awesome picker of UX Builder in the
 * Customizer.
 *
 * Use it like any field:
 *   Silver_Option::add_field( 'option', array(
 *       'type'              => 'silver-icon',
 *       'settings'          => 'my_icon',
 *       'label'             => __( 'Icon', 'silver' ),
 *       'section'           => 'my-section',
 *       'default'           => '',
 *       'sanitize_callback' => 'silver_sanitize_icon_class',
 *   ) );
 * The value is the icon classes ("fa-solid fa-chevron-right"), or "" when no
 * icon is chosen. Print it with get_silver_icon().
 *
 * @package Silver
 */

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Silver_Customize_Icon_Control' ) ) {

	/**
	 * Icon picker control.
	 */
	class Silver_Customize_Icon_Control extends WP_Customize_Control {

		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'silver-icon';

		/**
		 * Text of the field when no icon is chosen.
		 *
		 * @var string
		 */
		public $empty_label = '';

		/**
		 * Sends the empty label to the control script.
		 */
		public function to_json() {
			parent::to_json();
			$this->json['emptyLabel'] = $this->empty_label ? $this->empty_label : __( 'None', 'silver' );
		}

		/**
		 * Renders the field: the current icon and its name, and a button that
		 * removes it. The popup is opened by assets/js/admin/customizer-icon.js.
		 */
		protected function render_content() {
			?>
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php endif; ?>
			<div class="silver-icon-field">
				<button type="button" class="silver-icon-field__select" title="<?php esc_attr_e( 'Choose icon', 'silver' ); ?>">
					<span class="silver-icon-field__preview"></span>
					<span class="silver-icon-field__label"></span>
				</button>
				<button type="button" class="silver-icon-field__clear" title="<?php esc_attr_e( 'Remove icon', 'silver' ); ?>">
					<span class="dashicons dashicons-no-alt"></span>
				</button>
			</div>
			<?php
		}
	}
}

/**
 * Lets Kirki fields use the "silver-icon" type.
 *
 * @param array $types Control type => class name.
 *
 * @return array
 */
function silver_customizer_icon_control_type( $types ) {
	$types['silver-icon'] = 'Silver_Customize_Icon_Control';

	return $types;
}

add_filter( 'kirki_control_types', 'silver_customizer_icon_control_type' );

/**
 * Loads the shared picker and the control script in the Customizer.
 */
function silver_customizer_icon_control_enqueue() {
	silver_icon_picker_enqueue_popup();

	silver_enqueue_asset( 'silver-customizer-icon', 'admin/customizer-icon', array( 'customize-controls', 'silver-icon-picker-popup' ) );
}

add_action( 'customize_controls_enqueue_scripts', 'silver_customizer_icon_control_enqueue' );
