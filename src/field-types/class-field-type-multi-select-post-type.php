<?php
/**
 * Multi Select Post Type.
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Field_Types
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Field_Types;

use BPXProfileCFTR\Contracts\Field_Type_Multi_Valued;
use BPXProfileCFTR\Contracts\Field_Type_Selectable;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Multiselect Post Type Type
 */
class Field_Type_Multi_Select_Post_Type extends \BP_XProfile_Field_Type implements Field_Type_Selectable, Field_Type_Multi_Valued {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct();

		$this->name     = _x( 'Custom Post Type Multiselector', 'xprofile field type', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );

		$this->supports_multiple_defaults = true;
		$this->supports_options           = false;
		do_action( 'bp_xprofile_field_type_multiselect_custom_post_type', $this );
	}

	/**
	 * Edit field html.
	 *
	 * @param array $raw_properties properties.
	 */
	public function edit_field_html( array $raw_properties = array() ) {
		$user_id = bp_displayed_user_id();

		if ( isset( $raw_properties['user_id'] ) ) {
			$user_id = (int) $raw_properties['user_id'];
			unset( $raw_properties['user_id'] );
		}

		$html = $this->get_edit_field_html_elements(
			array_merge(
				array(
					'multiple' => 'multiple',
					'id'       => bp_get_the_profile_field_input_name() . '[]',
					'name'     => bp_get_the_profile_field_input_name() . '[]',
				),
				$raw_properties
			)
		);
		?>

		<legend id="<?php bp_the_profile_field_input_name(); ?>-1">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
		</legend>

		<?php do_action( bp_get_the_profile_field_errors_action() ); ?>

		<select <?php echo $html; ?>>
			<?php bp_the_profile_field_options( "user_id={$user_id}" ); ?>
		</select>

		<?php if ( bp_get_the_profile_field_description() ) : ?>
            <p class="description" id="<?php bp_the_profile_field_input_name(); ?>-3"><?php bp_the_profile_field_description(); ?></p>
		<?php endif; ?>

		<?php
	}

	/**
	 * Edit field options.
	 *
	 * @param array $args args.
	 */
	public function edit_field_options_html( array $args = array() ) {
	    global $field;

		$post_type_selected = self::get_selected_post_type( $field->id );
		$posts_selected     = maybe_unserialize( \BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] ) );

		$html = '';

		if ( ! empty( $_POST[ 'field_' . $this->field_obj->id ] ) ) {
			$new_posts_selected = $_POST[ 'field_' . $this->field_obj->id ];
			$posts_selected     = ( $posts_selected != $new_posts_selected ) ? $new_posts_selected : $posts_selected;
		}
		// Get posts of custom post type selected.
		$posts = new \WP_Query(
			array(
				'posts_per_page' => - 1,
				'post_type'      => $post_type_selected,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		if ( $posts ) {
			foreach ( $posts->posts as $post ) {
				$html .= sprintf(
					'<option value="%s"%s>%s</option>',
					$post->ID,
					( ! empty( $posts_selected ) && ( in_array( $post->ID, $posts_selected ) ) ) ? 'selected="selected"' : '',
					$post->post_title
				);
			}
		}

		echo apply_filters( 'bp_get_the_profile_field_multiselect_custom_post_type', $html, $args['type'], $post_type_selected, $this->field_obj->id );
	}

	/**
	 * Dashboard->Users->Profile Fields
	 *
	 * @param array $raw_properties atts.
	 */
	public function admin_field_html( array $raw_properties = array() ) {

		$html = $this->get_edit_field_html_elements(
			array_merge(
				array(
					'multiple' => 'multiple',
					'id'       => bp_get_the_profile_field_input_name() . '[]',
					'name'     => bp_get_the_profile_field_input_name() . '[]',
				),
				$raw_properties
			)
		);
		?>

		<select <?php echo $html; ?>>
			<?php bp_the_profile_field_options(); ?>
		</select>

		<?php
	}

	/**
	 * Dashboard->Users->Profile Fields->New|Edit entry.
	 *
	 * @param \BP_XProfile_Field $current_field object.
	 * @param string             $control_type type.
	 */
	public function admin_new_field_html( \BP_XProfile_Field $current_field, $control_type = '' ) {

		$type = array_search( get_class( $this ), bp_xprofile_get_field_types() );

		if ( false === $type ) {
			return;
		}

		$class = $current_field->type != $type ? 'display: none;' : '';

		$post_types = get_post_types( array( 'public' => true ) );

		$selected_post_type = self::get_selected_post_type( $current_field->id );
		?>
        <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
			<?php if ( ! $post_types ) : ?>
                <h3><?php _e( 'There is no custom post type. You need to create at least one to use this field.', 'bp-xprofile-custom-field-types' ); ?></h3>
			<?php else : ?>
                <h3><?php esc_html_e( 'Select a post type:', 'bp-xprofile-custom-field-types' ); ?></h3>
                <div class="inside">
                    <p>
						<?php _e( 'Select a post type:', 'bp-xprofile-custom-field-types' ); ?>
                        <select name="bpxcftr_multi_selected_post_type" id="bpxcftr_multi_selected_post_type">
                            <option value=""><?php _e( 'Select...', 'bp-xprofile-custom-field-types' ); ?></option>
							<?php foreach ( $post_types as $k => $v ) : ?>
                                <option value="<?php echo $k; ?>" <?php selected( $selected_post_type, $k, true );?>><?php echo $v; ?></option>
							<?php endforeach; ?>
                        </select>
                    </p>
                </div>
			<?php endif; ?>
        </div>
		<?php
	}

	/**
	 * Check if the values are valid.
	 *
	 * @param array $values values.
	 *
	 * @return bool
	 */
	public function is_valid( $values ) {

		if ( empty( $values ) ) {
			return true;
		}

		/**
		 * Can not test for post type as the field id is unknown at this moment.
		 */

		$values = (array) $values;

		_prime_post_caches( $values, false, false );
		$validated = true;

		foreach ( $values as $post_id ) {
			$post = get_post( $post_id );

			if ( ! $post ) {
				$validated = false;
				break;
			}
		}

		return $validated;
	}

	/**
	 * Modify the appearance of value.
	 *
	 * @param  string $field_value Original value of field.
	 * @param  int    $field_id Id of field.
	 *
	 * @return string   Value formatted
	 */
	public static function display_filter( $field_value, $field_id = 0 ) {

		if ( empty( $field_value ) ) {
			return '';
		}

		$post_ids = explode( ',', $field_value );
		$post_ids = wp_parse_id_list( $post_ids );
		_prime_post_caches( $post_ids, false, false );

		$list = '';
		foreach ( $post_ids as $post_id ) {

			if ( ! get_post( $post_id ) ) {
				continue;
			}

			$list .= sprintf( '<li><a href="%1$s">%2$s</a></li>', esc_url( get_permalink( $post_id ) ), get_the_title( $post_id ) );
		}

		if ( $list ) {
			return "<ul class='bpxcftr-multi-post-type-posts-list'>{$list}</ul>";
		}

		return '';
	}

	/**
	 * Get the terms content.
	 *
	 * @param int $field_id field id.
	 *
	 * @return string
	 */
	private static function get_selected_post_type( $field_id ) {

		if ( ! $field_id ) {
			return '';
		}

		return bp_xprofile_get_meta( $field_id, 'field', 'selected_post_type', true );
	}
}
