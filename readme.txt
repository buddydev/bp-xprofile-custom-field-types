=== Buddypress Xprofile Custom Field Types ===
Contributors: buddydev, sbrajesh
Tags: buddypress, xprofile, fields
Requires at least: 4.5
Tested up to: 4.9.6
Stable tag: 1.0.0
License: GLPv2 or later

Buddypress Xprofile Custom Field Types adds extra custom field types to BuddyPress. Field types are: Birthdate, Email, Url etc.

== Description ==
Buddypress Xprofile Custom Field Types plugin adds some essential field types to BuddyPress Porifle Field.

In the first version, It brings all the profile fields offered by the currently abandoned The ["Buddypress Xprofile Custom Fields Type"]("https://github.com/donmik/buddypress-xprofile-custom-fields-type") plugin.

The supported profile field types are:-
* Birthdate.
* Image.
* File.
* Checkbox acceptance.
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

This plugin is a new plugin with a better and manageable codebase. We do re-0use 20-30% of the code for field types from
the @donmik's plugin.

In future, we hope to add more fields.
Please do note that this plugin is not 100% backward compatible.
If you are looking to move from the older plugin to this one, please read our release post(Link to be added on release).


=== Credit ===
 [@donmik](http://donmik.com) for the [BuddyPress Xprofile Custom Fields Type]("https://github.com/donmik/buddypress-xprofile-custom-fields-type") from where we adopted the field types in our first version.

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
Yes, but you will need to follow our migration guide. We have changed the internal architecture and the fiels need to be updated.


= What is the supported BuddyPress Version? =
2.9+, Tested with 3.0.

= Where do I get support? =
Please use [BuddyDev support](https://buddydev.com/support/forums/) forums.


== Changelog ==

= 1.0.0 =
Initial release. Includes all field types supported by the older BuddyPress Xprofile Fields Type plugin.
