=== Mailster - Email Newsletter Plugin for WordPress ===
Contributors: EverPress, revaxarts
Tags: email newsletter, newsletter, newsletter signup, email signup, email marketing
Requires at least: 3.8
Tested up to: 4.9.3
Stable tag: 2.2.18
Author: EverPress
Author URI: https://mailster.co
Requires PHP: 5.3.6

== Description ==

= a super simple Email Newsletter Plugin for WordPress to create, send and track your Newsletter Campaigns =

**Track Opens, Clicks, Unsubscriptions and Bounces**
Now it’s easy to keep track of your customers. Who does opened when and where your Newsletter? Track undeliverable mails (bounces), Countries, Cities** and know exactly who opened your mails.

**Auto Responders**
Send welcome messages to new subscribers or special offers to your loyal customers. Limit receivers with conditions or send messages only to certain lists

**Schedule your Campaigns**
Let your subscribers receive your latest news when they have time to read it, not when you have time to create it

**Simple Newsletter Creation**
Creating Newsletters has never been so easy. If you familiar with WordPress Posts you can create your next campaign as easy as you publish a new blog entry. All options are easy accessible via the edit campaign page

**Unlimited Customization**
Use the Option panel to give your newsletter an unique branding, save your color schema and reuse it later. Choose one over 20 included backgrounds or upload your custom one.

**Preflight your Newsletter**
Don’t send unfinished Newsletters to your Customers which possible end up in there SPAM folders and are never been seen. Use built in Spam check to get your spam score

= Full Feature List =

* Track Opens, Clicks, Unsubscriptions and Bounces
* Track Countries and Cities*
* Schedule your Campaigns
* Auto responders
* Use dynamic and custom Tags (placeholders)
* Webversion for each Newsletter
* embed Newsletter with Shortcodes
* Forward via email
* Share with Social Media services
* Unlimited subscription forms
* Sidebar Widgets
* Single or Double-Opt-in support
* WYSIWYG Editor with code view
* Unlimited Color Variations
* Background Image support
* Quick Preview
* Email Spam check
* Multi language Support (over 10 languages included)
* SMTP support
* DomainKeys Identified Mail Support
* Import and Export for Subscribers
* Retina support

== Templates ==

These Templates are made for the Mailster Newsletter Plugin. They have been fully tested with all major email softwares and providers. They are all available exclusively on ThemeForest.

If you have further questions please visit our [knowledge base](https://kb.mailster.co)

Xaver Birsak – https://everpress.io


= Linus =
[!(https://mailster.github.io/preview/linus.jpg)](https://rxa.li/linus?utm_source=Plugin+Info+Page)
= Metro =
[!(https://mailster.github.io/preview/metro.jpg)](https://rxa.li/metro?utm_source=Plugin+Info+Page)
= My Business =
[!(https://mailster.github.io/preview/business.jpg)](https://rxa.li/business?utm_source=Plugin+Info+Page)
= Loose Leaf =
[!(https://mailster.github.io/preview/looseleaf.jpg)](https://rxa.li/looseleaf?utm_source=Plugin+Info+Page)
= Market =
[!(https://mailster.github.io/preview/market.jpg)](https://rxa.li/market?utm_source=Plugin+Info+Page)
= Skyline =
[!(https://mailster.github.io/preview/skyline.jpg)](https://rxa.li/skyline?utm_source=Plugin+Info+Page)
= Letterpress =
[!(https://mailster.github.io/preview/letterpress.jpg)](https://rxa.li/letterpress?utm_source=Plugin+Info+Page)


== Changelog ==

= Version 2.2.18 =

* fixed: problem with slashes in head section during test campaign
* fixed: problem on template save
* fixed: display issue on color pickers in editor

= Version 2.2.17 =

* added: optional caching parameter for forms
* change: sanitize_content no longer uses stripslashes

= Version 2.2.16 =

* fixed: Double id on settings page
* fixed: conflict with some third party delivery plugins
* fixed: editor did not expand to correct height on some third party templates
* improved: Excerpt and Content of posts look the same no matter which type of embedding were used

= Version 2.2.15 =

* fixed: visual issue with color picker on WP 4.9
* fixed: lists were always deleted from the manage subscribers tab
* fixed: issue with external image URLs not saving
* fixed: height not updated if image url is used in editor
* fixed: issue with Gravatar URLs
* improved: scrolling speed in editor
* improved: list query
* improved: Caching is now disabled on any page with a form
* improved: newsletter homepage selector on setting with sites over 100 pages
* improved: translation fetching

= Version 2.2.14 =

* fixed: ratings with '%' in segmentation causes not expected behavior in WordPress 4.8.3+
* fixed: missing escaping in segmentation rules where LIKE is used
* fixed: get next date on weekly autoresponder if weekday is not the current one
* fixed: missing form field on autoresponder overview
* fixed: updating third party templates via templates page
* updated: included template to 6.1

= Version 2.2.13 =

* fixed: issue with stripped tags on template save
* fixed: resending of transactional mails
* fixed: registration not stored if username was missing
* fixed: bounce test failed on some servers
* added: additional security steps on form submission
* added: option to recheck for updates on the dashboard

= Version 2.2.12 =

* fixed: editbar position issue on Chrome 61
* fixed: background image not editable if its the first of the template
* improved: placeholder image handling
* improved: loading of user met values
* removed: deprecated jQuery methods

= Version 2.2.11 =

* improved: translation checks and loading
* improved: loading of translations
* fixed: assigned unchecked lists on form submission if user choice is enabled
* fixed: PHP warning on bounce handler
* added: `remove_custom_value` method to remove meta values of subscribers
* added: Mailgun delivery option to setup

= Version 2.2.10 =

* fixed: Editor button now only available in the backend
* fixed: Excerpts were missing when view mode is Excerpt View
* fixed: missing `wp_get_raw_referer` on WP < 4.5
* fixed: choosing default values for dropdowns and radio custom fields
* fixed: multiple attached attachments
* fixed: plain text option wasn't respected during test campaigns
* improved: lists assigned to a form are now respected if form id is set explicitly on subscriber submission
* added: 'mailster_campaign_content' filter to alter the content of campaigns
* added: 'mailster_using_permalinks' filter

= Version 2.2.9 =

* added: support for the SparkPost add on
* fixed: some tags with alternative content were not replaced when sending a test campaign
* fixed: module screenshots returned error if more than 30 modules in template
* improved: unsubscribe action can now contain a status for more info
* change: send method now returns internal message ID
* change: test mails to unknown email addresses are no longer assigned to the current user to prevent false mailbox actions

= Version 2.2.8 =

* fixed: radio and dropdown values weren't populated on profile in some cases
* fixed: wp_mail now supports coma separated emails if used by Mailster
* fixed: PHP notice with autoresponders on PHP 7.1
* fixed: link for buttons were pre filled with the URL from the previous selected button
* fixed: PHP notices on Cron lock
* fixed: issue with defined constants if GEO library is loaded in a third party plugin
* fixed: display issue of emojis in tinymce of multi elements
* fixed: link of images wasn't populated correctly
* fixed: reading filesize on missing file during export
* improved: ever re-signup will respect the forms double-opt-in setting
* improved: using SQL_CALC_FOUND_ROWS on subscribers overview to speed up queries
* improved: form profile compatibility with certain themes
* improved: get referer on form signup
* improved: pre cache queries on autoresponder overview
* added: option for legacy POP3 method on bounce settings
* added: 'mailster_update_option_*' filter to alter option on save
* added: 'mailster_get_signups_sql', 'mailster_queue_campaign_subscriber_data' filters
* added: 'mailster_cookie_time' filter to adjust Mailster cookie expiration time
* added: 'mailster_get_current_user' and 'mailster_get_current_user_id' methods
* changed: 'mailster_unsubscribe_link' hook position and added campaign_id to arguments

= Version 2.2.7 =

* fully tested on WordPress 4.8
* fixed: exporting subscribers
* fixed: strip slashes on list descriptions
* fixed: SQL issue when unassign lists from subscribers
* fixed: encoding issue while saving campaigns on some servers
* improved: display Mailster username on Dashboard
* improved: removed usage of 'create_function' for PHP 7.2
* improved: better sanitation and checks on date fields
* added: 'mailster_keep_tags' filter to keep tags
* change: some default values on plugin activation

= Version 2.2.6 =

* fixed: checkboxes were always checked by default
* fixed: status info on user time based auto responder
* fixed: save settings button not enabled in some cases
* fixed: duplicating of other campaigns without capabilities
* fixed: spelling mistakes
* improved: html tags in custom field names
* improved: compatibility with caching plugins
* improved: excerpts are now generated if not defined via more tag or explicit
* improved: loading fall back if notification.html is missing
* improved: removed redundant white spaces in plain text versions
* improved: links in plain text version are now grouped together below the content
* improved: compatibility with third party templates
* improved: CSS rules for RTL languages
* added: 'mailster_get_last_post_args' filter to alter post arguments
* added: month to user time based autoresponder time frames
* updated: templates page

= Version 2.2.5 =

* change: Signup date checkbox on Mange Subscriber Page now checked by default
* fixed: Thickbox dimensions on form detail page
* fixed: selecting static posts working again
* fixed: issue on recipients detail page since last update
* fixed: Newsletter sign up widget didn't store empty title
* fixed: wrong excerpt on web version with dynamic tags in some cases
* updated: PHPMailer to version 5.2.23
* improved: Cron tab commands
* improved: Cron now supports secret via a header
* improved: URL rewrite support option on settings
* overall improvements

= Version 2.2.4 =

* fixed: adding attachments not possible on Firefox
* fixed: Subscriber ID was cached on custom dynamic tags in some cases
* fixed: converting links on single elements no longer pre filled if link is not set
* fixed: smaller bugs
* updated: order by "Clicks" is now "Click Date" in recipients details view
* improved: all widgets are now wrapped by a div with class "mailster-widget" for better targeting
* new option to get subscribers by md5 hash
* new "mailster_subscriber_hash" filter to change subscriber hash
* improved: added various filters to list and subscribers view
* improved: support of arrays in auto post tag filter
* improved: allowing anonymous functions in `mailster_add_tag`
* improved: allowing anonymous functions in `mailster_add_style`
* improved: subscriber caching
* improved: loading of widgets

= Version 2.2.3 =

* fixed: issue with custom editor buttons are not displayed
* fixed: assign subscribers to lists now correctly removes old assignments
* fixed: unescaped apostrophe on test mails
* fixed: small bugs
* improved: access to form.php and cron.php if location not default
* improved: third party templates support on PHP 7.1
* improved: cron mechanism
* improved: cron job settings page

= Version 2.2.2 =

* added: option to add link to your logo
* fixed: issue in segmentation if WP user meta field matches a reserved Mailster fields
* fixed: changing order of WP dashboard widgets wasn't stored
* improved: checks for path if plugin directory is not at it's default locations
* improved: dismiss option on dashboard notifications
* improved: update mechanism

= Version 2.2.1 =

* fixed: bounces weren't handled correctly in some cases
* fixed: styles were not applied correctly while sending tests
* fixed: Subscriber import with coma separated lists causes that new created lists were not assigned correctly
* fixed: wrong DKIM record on some installations
* fixed: error output on wrong formatted HTML in excerpts
* added: option to export ratings
* improved: URLs for some social services
* improved: backwards compatibility
* improved: datepicker is no longer triggered on datefields if operator is a regular expression

= Version 2.2 =

* new: Mailster Dashboard
* new: Setup Wizard
* new: Editor Button to quickly add common tags to your campaigns
* new: Editor Button to add forms to your post and pages
* new: Templates Settings allow you to define default logo and social media links
* new: add attachments to your campaigns
* new: Bounce servers now supports IMAP servers
* new: Bulk Actions for subscribers can now process all subscribers
* new: a screenshot.jpg file can now be used for the screenshot in templates folder
* new: Manage Settings to export and import your settings
* new: receivers email address now contains full name of subscribers
* new tag: 'author'. usage `{post_author:-1}`
* new: Subscriber Button Widget
* new: share service VK.com, Telegram, Whatsapp
* new: localization hub: translate.mailster.co
* new: 'mailster_excerpt_length' let you define the length of the excerpt used in your campaigns
* improved: sending queue
* improved: loading of language files
* improved: better ordering of lists
* updated: The included template has been updated
* change: background images are now located in the uploads directory
* change: settings are now in the plugins menu

For further details please visit [the changelog on the Mailster Homepage](https://mailster.co/changelog/)


