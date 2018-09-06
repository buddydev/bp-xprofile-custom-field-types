=== BuddyPress Xprofile Custom Field Types ===
Contributors: buddydev, sbrajesh
Tags: buddypress, xprofile, fields, buddypress-profile-field-types
Requires at least: 4.5
Tested up to: 4.9.8
Stable tag: 1.0.5
License: GLPv2 or later

Buddypress Xprofile Custom Field Types adds extra custom profile fields to BuddyPress. Field types are: Birthdate, Email, Url etc.

== Description ==
BuddyPress Xprofile Custom Field Types plugin adds some essential field types to BuddyPress Profile.

BuddyPress Xprofile Custom Field Types is 100% compatible with [BP Profile Search plugin](https://wordpress.org/plugins/bp-profile-search/).

The newly added BuddyPress field types are:-
* Birthdate.
* Image.
* File.
* Checkbox acceptance.
* From/To field(can be used to show 2 numbers or text strings).
* [Email](http://www.w3.org/TR/html-markup/input.email.html "Input type email - HTML5").
* [Web](http://www.w3.org/TR/html-markup/input.url.html "Input type url - HTML5").
* [Datepicker](http://www.w3.org/TR/2013/NOTE-html-markup-20130528/input.date.html "Input type date - HTML5").
* Custom post type selector.
* Custom post type multiselector.
* [Colorpicker](http://www.w3.org/TR/2013/NOTE-html-markup-20130528/input.color.html "Input type color - HTML5").
* Decimal number.
* Number within min/max values.
* Custom taxonomy selector.
* Custom taxonomy multiselector.
* Range input (slider)
* [Select2 javascript plugin](https://select2.github.io/) for select boxes.

The plugin is opensource and currently developed on github. We welcome you to be part of its future development at [https://github.com/buddydev/bp-xprofile-custom-field-types](https://github.com/buddydev/bp-xprofile-custom-field-types).

Discuss the plugin on our [release post](https://buddydev.com/add-extra-buddypress-profile-fields-with-buddypress-xprofile-custom-field-types-plugin/) or view the plugin's [detailed documentation here](https://buddydev.com/plugins/bp-xprofile-custom-field-types/).
The idea is based on @donmik's plugin. This plugin is a complete rewrite. Some field type do share code with the original plugin. My guess, we are using 20-30% of the code for field types from the original.

In future, we hope to add more fields.

**Note: This plugin is not 100% backward compatible**
It is very easy to migrate. Should take less than 5 minute. If you are looking to move from the older plugin to this one, please read our [migration guide](https://buddydev.com/plugins/bp-xprofile-custom-field-types/#migrate).

= Credit =
 [@donmik](http://donmik.com) for the [BuddyPress Xprofile Custom Fields Type](https://github.com/donmik/buddypress-xprofile-custom-fields-type) from where we adopted the field types in our first version.
 In the first version, te plugin brought all the profile fields offered by the currently abandoned The ["BuddyPress Xprofile Custom Fields Type"](https://github.com/donmik/buddypress-xprofile-custom-fields-type) plugin.

== Installation ==

1. Upload the plugin to your 'wp-content/plugins' directory
2. Activate the plugin
3. Go to Dashboard > Users > Profile Fields
4. Create or Edit a field.
5. In Field Type select, you can see new field types under the "Custom Fields" group.
6. For select2, you can see a new box below submit button only with selectbox, multiselectbox,
custom post type selector, custom post type multiselector, custom taxonomy selector and
custom taxonomy multiselector.
6. Enjoy!

== Frequently Asked Questions ==

= Can I replace BuddyPress Xprofile Custom Fields Type with this? =
Yes, but you will need to follow our [migration guide](https://buddydev.com/plugins/bp-xprofile-custom-field-types/#migrate). We have changed the internal architecture and the admin field settings need to be updated.

= Is the upgrade from BuddyPress Xprofile Custom Fields Type safe? =
Yes, 100%. For better performance, we have changed the way field settings were stored in admin. The user data will be preserved and they won't notice the difference.

= What is the supported BuddyPress Version? =
2.9+, Tested with 3.1.

= Where do I get support? =
Please use [BuddyDev support](https://buddydev.com/support/forums/) forums.


== Screenshots ==

1. Admin field types dropdown screenshot-1.png
2. front end edit profile field types screenshot-2.png
3. profile view screenshot-3.png

== Changelog ==

= 1.0.5 =
 * Fix the date format for birthdate checking. Now it validates correctly(Earlier only year and month was significant).
 * Fix a validation issue with registration page when birthdate is not selected correctly and min age is required.
 * Improve from/to field display filter.
 * Fix translation for the birthdate field.

= 1.0.4 =
 * Fix the signup validation. Thank you Thank you @carsten-lund.

= 1.0.3 =
* Add support for BP Profile Search plugin by Andrea.

= 1.0.2 =
* Fix the custom post type selection in admin. Thank you @jcdeckard.

= 1.0.1 =
* Added From/To field type. Thank you @carsten-lund for the suggestion.
* Added option for birthdate field to show/hide months while showing age.
* Updated text domain to use the plugin slug.

= 1.0.0 =
Initial release. Includes all field types supported by the older BuddyPress Xprofile Fields Type plugin.