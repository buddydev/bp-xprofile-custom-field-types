<?php
/**
 * Select Taxonomy.
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Field_Types
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Field_Types;

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

use BPXProfileCFTR\Contracts\Field_Type_Selectable;

/**
 * Select Custom Taxonomy Type
 */

class Field_Type_Select_Taxonomy extends \BP_XProfile_Field_Type implements Field_Type_Selectable {

    public function __construct() {

	    parent::__construct();

	    $this->name     = _x( 'Custom Taxonomy Selector', 'xprofile field type', 'bp-xprofile-custom-field-types' );
	    $this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );

		$this->supports_options = false;

		do_action( 'bp_xprofile_field_type_select_custom_taxonomy', $this );
	}

	public function edit_field_html( array $raw_properties = array() ) {

	    $user_id = bp_displayed_user_id();

		if ( isset( $raw_properties['user_id'] ) ) {
			$user_id = (int) $raw_properties['user_id'];
			unset( $raw_properties['user_id'] );
		}

		$html = $this->get_edit_field_html_elements( $raw_properties );
		?>

        <legend id="<?php bp_the_profile_field_input_name(); ?>-1">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
        </legend>

		<?php do_action( bp_get_the_profile_field_errors_action() ); ?>
        <select <?php echo $html; ?>>
            <option value=""><?php _e( 'Select...', 'bp-xprofile-custom-field-types' ); ?></option>
			<?php bp_the_profile_field_options( "user_id={$user_id}" ); ?>
        </select>

		<?php  if ( bp_get_the_profile_field_description() ) : ?>
            <p class="description" id="<?php bp_the_profile_field_input_name(); ?>-3"><?php bp_the_profile_field_description(); ?></p>
		<?php endif;?>

		<?php
	}

	public function edit_field_options_html( array $args = array() ) {
		global $field;

		$taxonomy_selected = self::get_selected_taxonomy( $field->id );

		$term_selected = \BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] );

		$html = '';

		if ( ! empty( $_POST[ 'field_' . $this->field_obj->id ] ) ) {
			$new_term_selected = (int) $_POST[ 'field_' . $this->field_obj->id ];
			$term_selected     = ( $term_selected != $new_term_selected ) ? $new_term_selected : $term_selected;
		}

		// Get terms of custom taxonomy selected.
		$terms = get_terms( $taxonomy_selected, array(
			'hide_empty' => false
		) );

		if ( $terms && ! is_wp_error( $terms )) {

			foreach ( $terms as $term ) {
				$html .= sprintf( '<option value="%s"%s>%s</option>',
					$term->term_id,
					( $term_selected == $term->term_id ) ? ' selected="selected"' : '',
					$term->name );
			}
		}

		echo apply_filters( 'bp_get_the_profile_field_select_custom_taxonomy', $html, $args['type'], $term_selected, $this->field_obj->id );
	}


	public function admin_field_html( array $raw_properties = array() ) {
		$html = $this->get_edit_field_html_elements( $raw_properties );
		?>

        <select <?php echo $html; ?>>
			<?php bp_the_profile_field_options(); ?>
        </select>

		<?php
	}

	public function admin_new_field_html( \BP_XProfile_Field $current_field, $control_type = '' ) {

        $type = array_search( get_class( $this ), bp_xprofile_get_field_types() );

		if ( false === $type ) {
			return;
		}

		$class = $current_field->type != $type ? 'display: none;' : '';

		$taxonomies = get_taxonomies( array(
			'public'   => true,
		) );

		$selected_tax = self::get_selected_taxonomy( $current_field->id );
		?>
        <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box"
             style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
			<?php
			if ( ! $taxonomies ):
				?>
                <h3><?php _e( 'There is no custom taxonomy. You need to create at least one to use this field.', 'bp-xprofile-custom-field-types' ); ?></h3>
			<?php else : ?>
                <h3><?php esc_html_e( 'Select a custom taxonomy:', 'bp-xprofile-custom-field-types' ); ?></h3>
                <div class="inside">
                    <p>
						<?php _e( 'Select a custom taxonomy:', 'bp-xprofile-custom-field-types' ); ?>
                        <select name="bpxcftr_selected_taxonomy" id="bpxcftr_selected_taxonomy">
                            <option value=""><?php _e( 'Select...', 'bp-xprofile-custom-field-types' ); ?></option>
							<?php foreach ( $taxonomies as $k => $v ): ?>
                                <option value="<?php echo $k; ?>"<?php if ( $selected_tax == $k ): ?> selected="selected"<?php endif; ?>><?php echo $v; ?></option>
							<?php endforeach; ?>
                        </select>
                    </p>
                </div>
			<?php endif; ?>
        </div>
		<?php
	}

	public function is_valid( $values ) {

        if ( empty( $values ) ) {
			return true;
		}

		$term = get_term( $values );

		if ( is_wp_error( $term ) || ! $term ) {
			return false;
		}

		return true;
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
			return;
		}

		$term_id = absint( $field_value );
		$tax = self::get_selected_taxonomy( $field_id );

		$term = get_term( $term_id, $tax );
		if ( ! $term || is_wp_error( $term ) ) {
			return '';
		}

		return  sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_term_link( $term, $tax ) ), esc_html( $term->name ) );

	}

	/**
	 * Get the terms content.
	 *
	 * @param int $field_id field id.
	 *
	 * @return string
	 */
	public static function get_selected_taxonomy( $field_id ) {

		if ( ! $field_id ) {
			return '';
		}

		return bp_xprofile_get_meta( $field_id, 'field', 'selected_taxonomy',  true );
	}
}
