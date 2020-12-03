=== BuddyPress Xprofile Custom Field Types ===
Contributors: buddydev, sbrajesh
Tags: buddypress, xprofile, fields, buddypress-profile-field-types
Requires at least: 4.5
Tested up to: 5.6
Stable tag: 1.1.8
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
* Country field.
* From/To field(can be used to show 2 numbers or text strings).
* Token (can be used to set a list of predefined approved codes for registration etc).
* oEmbed ( allow your users to use youtube/facebook, vimeo and other oembed supporting urls to embed in their profile).
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

**Note 2: The Custom taxonomy field does not allow you to categorize users. They allow you to let users select some terms and display the terms on their profile.
           It is not intended for classifying user**

= Credit =
 [@donmik](http://donmik.com) for the [BuddyPress Xprofile Custom Fields Type](https://github.com/donmik/buddypress-xprofile-custom-fields-type) from where we adopted the field types in our first version.
 In the first version, te plugin brought all the profile fields offered by the currently abandoned The ["BuddyPress Xprofile Custom Fields Type"](https://github.com/donmik/buddypress-xprofile-custom-fields-type) plugin.

= More Plugins =
We love BuddyPress and we have created 100+ BuddyPress plugins.
Please take a look at our
 1. [Free BuddyPress Plugins](https://buddydev.com/plugins/  "Best BuddyPress Plugins")
 1. [Premium BuddyPress plugins](https://buddydev.com/plugins/category/buddypress-premium-plugins/ "Best BuddyPress Premium Plugins")
 We hope that it will help you take your BuddyPress network to the next level.


= BuddyPress Custom development & Maintenance Service =
If you need any assistance with setting up or adding new features to BuddyPress or this plugin, Our team is available for hire.
Please use our [BuddyPress Development Services](https://buddydev.com/buddypress-custom-plugin-development-service/) for any custom development needs.

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
2.9+, Tested with 7.0-RC1.

= Where do I get support? =
Please use [BuddyDev support](https://buddydev.com/support/forums/) forums.

= Can I hire you for BuddyPress development? =
We will love to work with you. Please let us know if you need any of our [services](https://buddydev.com/services/).

== Screenshots ==

1. Admin field types dropdown screenshot-1.png
2. front end edit profile field types screenshot-2.png
3. profile view screenshot-3.png

== Changelog ==

= 1.1.8 =
 * Added country field.
 * Added option for admins to choose if web links would open in new window/tab.
 * Fixed the issue with From/To value deletion. Thank you @johan_walter
 * Fixed web field always using http schema instead of the specified one.

= 1.1.7 =
 * Fix image/file not getting deleted when a user was deleted or marked spam.
 * Fix issue with the default values of from/to field not being shown.
 * Add option to select value separator in the from/to field.
 * Fix deletion of value of From/To field when using numeric values.
 * Prop @johan_walter for the suggestions.

= 1.1.6 =
 * Fix conflict of bitrhdate settings with date field settings.
 * Fix terms checkbox issue in the dashboard.
 * Cleanup files/images on user being marked spam or user deletion.
 * Added French translations. Props @johan_walter

= 1.1.5 =
 * Fix script loading on dashboard profile edit page.

= 1.1.4 =
 * Update to avoid registering our script handles on the pages not relavan to us. Fixes select2 conflict for some.

= 1.1.3 =
 * Added an action to notify file deletion.

= 1.1.2=
 * Update to include select2 full version.
 * Pass more data with the age display filter.

= 1.1.1 =
 * Fix the signup token validation.
 * New: Include German translations by Thorsten Wollenhöfer

= 1.1.0 =
 * New: Added oEmbed field type. allows using facebook, youtube, vimeo and other urls and embed them in BuddyPress user profile.
 * New: Token field type. Define a set of codes and ask your users to enter the codes. Could be used to simulate invite only registration.
 * Updated: TOS field type to allow adding more html tags and not filtering the attributes like traget etc.
 * Fix:- Options loading in admin.
 * New:- Add extra label for Age(Birthdate field type). You can use different label for view/edit field name.
 * For More, please see our [release post](https://buddydev.com/more-power-to-the-buddypress-custom-profile-fields/).

= 1.0.9 =
 * Fix a required file/image fields asking to reupload on edit profile, even when user has already added files.
 * Tested with WordPress 5.2.1 & BuddyPress 4.3

= 1.0.8 =
 * Fix a upload error on signup page when a file is not selected.

= 1.0.7 =
 * Fix a bug with multi post type selector.
 * Fix upload issue in dashboard profile update screen. Thank you @laudag.
 * Fix the loading of locale file for select2 js.

= 1.0.6 =
 * Update included select2 javascript to latest stable version(4.0.5).
 * Enable select2 in dashboard edit profile if enabled.
 * Fix a notice.

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