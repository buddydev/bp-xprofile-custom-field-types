<?php
/**
 * Multi Select Taxonomy.
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
 * Multiselect Custom Post Type Type
 */
class Field_Type_Multi_Select_Taxonomy extends \BP_XProfile_Field_Type implements Field_Type_Selectable, Field_Type_Multi_Valued {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->name     = _x( 'Custom Taxonomy Multiselector', 'xprofile field type', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );

		$this->supports_multiple_defaults = false;
		$this->supports_options           = false;

		do_action( 'bp_xprofile_field_type_multiselect_custom_taxonomy', $this );
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
		$taxonomy_selected = self::get_selected_taxonomy( $field->id );
		$terms_selected    = maybe_unserialize( \BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] ) );

		$html = '';

		if ( ! empty( $_POST[ 'field_' . $this->field_obj->id ] ) ) {
			$new_terms_selected = $_POST[ 'field_' . $this->field_obj->id ];
			$terms_selected     = ( $terms_selected != $new_terms_selected ) ? $new_terms_selected : $terms_selected;
		}

		// Get terms of custom taxonomy selected.
		$terms = get_terms( $taxonomy_selected, array( 'hide_empty' => false ) );

		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$html .= sprintf(
					'<option value="%s" %s>%s</option>',
					$term->term_id,
					( ! empty( $terms_selected ) && ( in_array( $term->term_id, $terms_selected ) ) ) ? 'selected="selected"' : '',
					$term->name
				);
			}
		}

		echo apply_filters( 'bp_get_the_profile_field_multiselect_custom_taxonomy', $html, $args['type'], $terms_selected, $this->field_obj->id );
	}

	/**
	 * Dashboard->Users->Profile Fields entry.
	 *
	 * @param array $raw_properties properties.
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

		$taxonomies = get_taxonomies( array( 'public' => true ) );

		$selected_tax = self::get_selected_taxonomy( $current_field->id );
		?>
        <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
			<?php if ( ! $taxonomies ) : ?>
                <h3><?php _e( 'There is no custom taxonomy. You need to create at least one to use this field.', 'bp-xprofile-custom-field-types' ); ?></h3>
			<?php else : ?>
                <h3><?php esc_html_e( 'Select a custom taxonomy:', 'bp-xprofile-custom-field-types' ); ?></h3>
                <div class="inside">
                    <p>
						<?php _e( 'Select a custom taxonomy:', 'bp-xprofile-custom-field-types' ); ?>
                        <select name="bpxcftr_multi_selected_taxonomy" id="bpxcftr_multi_selected_taxonomy">
                            <option value=""><?php _e( 'Select...', 'bp-xprofile-custom-field-types' ); ?></option>
							<?php foreach ( $taxonomies as $k => $v ): ?>
                                <option value="<?php echo $k; ?>"<?php selected( $selected_tax,$k ) ?> ><?php echo $v; ?></option>
							<?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <label>
                            <input type="checkbox" name="bpxcftr_multi_tax_allow_new_terms" id="bpxcftr_multi_tax_allow_new_terms" value="1" <?php checked(true,  self::allow_new_terms( $current_field->id ) );?> />
		                    <?php _e( 'Allow users to add new terms:', 'bp-xprofile-custom-field-types' ); ?>
                        </label>

                    </p>
                </div>
			<?php endif; ?>
        </div>
		<?php
	}

	/**
	 * Check if the field is valid.
	 *
	 * @param array|string $values values.
	 *
	 * @return bool
	 */
	public function is_valid( $values ) {
		if ( empty( $values ) ) {
			return true;
		}

		$terms = get_terms(
			array(
				'include'    => $values,
				'hide_empty' => false,
				'fields'     => 'id=>name',
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Filter display.
	 *
	 * @param mixed  $field_value value.
	 * @param string $field_id field id.
	 *
	 * @return mixed
	 */
	public static function display_filter( $field_value, $field_id = '' ) {

		if ( empty( $field_value ) ) {
			return null;
		}

		$field_value = explode( ',', $field_value );
		$term_ids    = wp_parse_id_list( $field_value );

		$tax  = self::get_selected_taxonomy( $field_id );
		$list = '';

		foreach ( $term_ids as $term_id ) {

			$term = get_term( $term_id, $tax );
			if ( ! $term || is_wp_error( $term ) ) {
				continue;
			}

			$list .= sprintf( '<li><a href="%1$s">%2$s</a></li>', esc_url( get_term_link( $term, $tax ) ), esc_html( $term->name ) );
		}

		if ( $list ) {
			return "<ul class='bpxcftr-multi-taxonomy-terms-list'>{$list}</ul>";
		}

		return '';
	}

	/**
	 * Get the terms content.
	 *
	 * @param int $field_id field object.
	 *
	 * @return string
	 */
	public static function get_selected_taxonomy( $field_id ) {

		if ( ! $field_id ) {
			return '';
		}

		return bp_xprofile_get_meta( $field_id, 'field', 'selected_taxonomy', true );
	}

	/**
	 * I new term creation allowed?
	 *
	 * @param int $field_id field id.
	 *
	 * @return bool
	 */
	public static function allow_new_terms( $field_id ) {

		if ( ! $field_id ) {
			return false;
		}

		return (bool) bp_xprofile_get_meta( $field_id, 'field', 'allow_new_terms', true );
	}
}
