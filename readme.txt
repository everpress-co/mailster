=== Mailster - Email Newsletter Plugin for WordPress ===
Contributors: EverPress
Tags: email newsletter, newsletter, newsletter signup, email signup, email marketing
Requires at least: 3.8
Tested up to: 5.2.1
Stable tag: 2.4
Author: EverPress
Author URI: https://everpress.co
Requires PHP: 5.3.6

== Description ==

= Mailster is an easy to use Email Newsletter Plugin for WordPress. Create, send and track your Newsletter Campaigns without hassle. =

**Track Opens, Clicks, Unsubscriptions and Bounces**
Now it’s easy to keep track of your customers. Who does opened when and where your Newsletter? Track undeliverable mails (bounces), Countries, Cities** and know exactly who opened your mails.

**Auto Responders**
Send welcome messages to new subscribers or special offers to your loyal customers. Limit receivers with conditions or send messages only to certain lists.

**Unlimited**
Mailster has no limit for storing subscribers. Send campaigns to thousands of users with a single Mailster installation.

**Schedule your Campaigns**
Let your subscribers receive your latest news when they have time to read it, not when you have time to create it.

**Simple Newsletter Creation**
Creating Newsletters has never been so easy. If you familiar with WordPress Posts you can create your next campaign as easy as you publish a new blog entry. All options are easy accessible via the edit campaign screen.

**Campaign Analytics**
Track your campaigns **in real-time**. Get in depth statistics and track opens, clicks, unsubscritions and bounces.

**Segmentation**
Target your audience with custom **conditions** and segmentations.

**Preflight your Newsletter**
Don’t send unfinished Newsletters to your Customers which possible end up in there SPAM folders and are never been seen. Use built in Spam check to get your spam score.

= Feature List =

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

Xaver Birsak – https://everpress.co


= Linus =
[!(https://static.mailster.co/preview/linus.jpg)](https://evp.to/linus?utm_source=Plugin+Info+Page)
= Metro =
[!(https://static.mailster.co/preview/metro.jpg)](https://evp.to/metro?utm_source=Plugin+Info+Page)
= My Business =
[!(https://static.mailster.co/preview/business.jpg)](https://evp.to/business?utm_source=Plugin+Info+Page)
= Loose Leaf =
[!(https://static.mailster.co/preview/looseleaf.jpg)](https://evp.to/looseleaf?utm_source=Plugin+Info+Page)
= Market =
[!(https://static.mailster.co/preview/market.jpg)](https://evp.to/market?utm_source=Plugin+Info+Page)
= Skyline =
[!(https://static.mailster.co/preview/skyline.jpg)](https://evp.to/skyline?utm_source=Plugin+Info+Page)
= Letterpress =
[!(https://static.mailster.co/preview/letterpress.jpg)](https://evp.to/letterpress?utm_source=Plugin+Info+Page)


== Changelog ==

= Version 2.4 =

* new: Use over 900K photos from Unsplash
* new: RSS to Email Campaigns.
* new: Random Post Tags
* new: Campaign-Subscriber related tags
* new: Custom Dynamic Post Types
* new: additional Form shortcode attributes
* new: Fresh UI
* new: Translation Dashboard Info
* improved: Preserved stats from deleted subscribers
* improved: Mailster now stores your email address when you send a test
* improved: Import Export

= Version 2.3.19 =

* fixed: custom 'mailster_auto_post' output is working again.
* added: `mailster_is_email` filter to add custom email validation checks.
* improved: Mailster now better respects third party email with content type text/html
* removed: deprecated template functionality

= Version 2.3.18 =

* updated included template
* added: option to use TLS on bounce servers
* fixed: missing module buttons after code edit.
* fixed: priority order in queue.
* fixed: single quote in subject now correctly encoded
* improved: handling of link mapping for multi byte characters.
* improved: handling if notification.html file is missing
* improved: better file sanitation on template uploads

= Version 2.3.17 =

* added: `mailster_add_embeded_style` method to add custom embeded styles.
* added: option to enable sending usage statistics.
* fixed: issue with double quotes in background-image property
* fixed: correct saving of form option
* fixed: delivery issue with some third party apps
* improved: editor behavior when adding content in multiple areas.
* improved: styles added via `mailster_add(_embeded)_style` are now visible in the editor.
* improved: inline styles can now be skipped with an optional `data-embed` attribute
* improved: block comments now removed if present in the email
* improved: better action handling for mails opened on Yahoo
* improved: editor behavior
* improved: handling of options
* improved: removed some variables from the global space

= Version 2.3.16 =

* new: option to choose original image in editbar (for animated gifs)
* fixed: wrong subscriber count in dashboard widget if assigned to multiple lists
* fixed: auto expanding of chart on dashboard
* fixed: title with quotes now escaped correctly
* fixed: calculation of images if height is set to "auto"
* fixes: template file selection on system mails are now respected correctly
* improved: wp_mail wrapper now supports to address in format name <email>
* improved: wp_mail handling of reply-to, BCC and CC fields
* improved: DNS checks on settings page now asynchronous
* improved: subscribers now get removed from the queue if a bounce happens
* improved: better warning on import for pending subscribers
* improved: auto responder data now stored if campaign is saved as draft
* improved: handling of script tags during sanitation
* added: `mailster_allowed_script_domains` and `mailster_allowed_script_types` filter hooks
* added: condition "is in list" for better segmentation
* added: option to re test a test

= Version 2.3.15 =

* fully tested on WordPress 5.0
* fixed: missing icons in WordPress 5.0
* fixed: prevent tracking on test mails
* fixed: relative width attributes now preserved in the editor
* fixed: issues with wp_mail if reply_to is an array
* improved: better checks if `wp_mail` is defined by another plugin

= Version 2.3.14 =

* fixed: missing fallback on custom fields
* fixed: undefined bodyElement in editor.
* added: 'sub_query_limit' to process subscriber query in chunks for very large subscriber base
* added: display count of selected subscribers on delete page
* added: test for wp_mail
* added: more date form options on export
* improved: Gravatar as source is no longer shown if the source is not Gravatar
* improved: wp_mail handling for third party plugins
* improved: raw header parser for wp_mail
* improved: external forms now embedded via dedicate URL
* improved: query on subscriber overview page
* improved: translated roles in conditions view
* improved: searching subscribers now highlights search term.

= Version 2.3.13 =

* fixed: Max execution time error message pops up randomly on some servers.
* fixed: issue with certain post types and multiple underscores.
* fixed: PHP error on form duplication.
* fixed: issue with nested embed styles.
* fixed: smaller issues.
* improved: query for location based segmentation.
* added: 'mailster_get_post_list_args' filter for static posts.
* added: 'mailster_autoresponder_grace_period' filter.

= Version 2.3.12 =

* fixed: height attribute of image tags were not always respected.
* improved: tag replacement handling
* improved: list order in overview
* improved: queue handling of time based auto responders
* improved: query for dashboard widget
* improved: sql query

= Version 2.3.11 =

* fixed: added "source" tag in allowed tags
* fixed: smaller issues
* fixed: sql query issue on "(didn't) clicked link" condition
* fixed: unsubscribe issue on single opt out if user is logged in
* fixed: subscriber export on sites with CloudFlare
* fixed: smaller issues
* improved: custom tags are now replaced in the final campaign and no longer when created
* improved: privacy policy link gets updated if the address changes
* improved: subscriber query now has the campaign id as second argument.
* improved: nonce form handle
* added: `wp_include` and `wp_exclude` for subscriber query to handle WP user ID's
* added: condition "(didn't) clicked link" now allows to choose a certain campaign
* added: additional aggregated campaigns

= Version 2.3.10 =

* new: you can now use `[newsletter_profile]` and `[newsletter_unsubscribe]` everywhere where short codes are accepted
* fixed: array_map warning in wp_mail wrapper
* fixed: honeypot was pre-filled on Google Chrome with autofill
* fixed: Some tags where not displayed on notifications
* fixed: Gravatar changes on third party apps were not respected
* fixed: error if location database is missing
* fixed: tags in links causes a protocol removal
* fixed: smaller issues
* improved: better support for mailster_subscriber of third party apps with wrong data type
* improved: show stats on campaign overview if heartbeat API is disabled (no live reload)
* improved: better handling of inline styles for subscriber buttons
* disabled: honeypot mechanism to prevent Chrome browsers to fill out the honeypot field

= Version 2.3.9 =

* fixed: manage subscribers with no list assigned included users within a list
* fixed: some JS issues on IE 11
* fixed: IP addressed not stored on form submission
* fixed: not able to remove attachments
* fixed: wp_mail not working if receivers is not an array
* fixed: webversion tag was not displayed if campaign hasn't been saved yet
* fixed: redirection issue if baseurl contains query arguments
* fixed: button is no longer available on the unsubscribe form with single opt out
* added: `get_last_post` now includes subscriber and campaign id
* added: option to enable custom tags on web version

= Version 2.3.8 =

* fixed: caching issue on tags in subject line
* fixed: subscriber based autoresponder if "lists do not matter"
* new: Condition: GDPR Consent given
* added: meta data can now get exported
* added: `mailster_subscriber_rating` filter
* change: ratings now updated via cron to reduce server load on large databases

= Version 2.3.7 =

* new: option to add GDPR compliance forms on the privacy settings page.
* added: search field for modules
* added: `mailster_profile_form` and `mailster_unsubscribe_form` filter
* added: information to privacy policy text in WordPress 4.9.6
* added: added Mailster data to Export Personal Data option in WordPress 4.9.6
* added: added Mailster data to Erase Personal Data option in WordPress 4.9.6
* fixes: various small bugs

= Version 2.3.6 =

* new: Location based Segmentations
* new filter: `mailster_form_field_label_[field_id]` to alter the label of form fields
* improved: simplified location based tracking with auto update
* improved: Export page now offers conditional export and saves defined settings.
* improved: Delete page now offers conditional deletion.
* change: active campaigns are now included in aggregated items in conditions
* fixed: odd offset issue on hover in editor
* fixed: importing emails with single quotes
* fixed: JS error when switching back from codeview with no head section
* fixed: do not redirect after unsubscribe
* fixed: removing a user from a blog on a multi site now correctly removes subscriber

= Version 2.3.5 =

* fixed: list assignments for some third party add ons
* fixed: small bug fixes
* fixed: changes were not saved if only modules were rearranged
* fixed: ajax requests not working in some browser environments
* fixed: improved display of subscribers overview page with many custom fields
* fixes: export of subscribers not working on some servers
* added: more tests
* change: display Self Test menu entry if `WP_DEBUG` is enabled

= Version 2.3.4 =

* fixed: prevent style blocks moved to body tag
* fixed: buttons no longer get removed after click on cancel
* fixed: Outlook conditional tags were removed
* fixed: body attributes added via codeview are now preserved
* fixed: small bug fixes
* improved: better error handling on export
* improved: more info for list confirmations
* added: bulk option to confirm subscriptions
* added: `{lists}` tag is now working in confirmation messages

= Version 2.3.3 =

* fixed: pages were not editable
* fixed: error if `wp_get_attachment_metadata` returns false
* fixed: autoresponder query issue
* fixed: small bug fixes

= Version 2.3.2 =

* fixed: pagination on subscribers overview page
* fixed: profile for logged in users working again
* fixed: confirmation message was sent on single opt in
* fixed: subscribers detail page sometimes empty
* fixed: missing images on some third party templates

= Version 2.3.1 =

* fixed: error: Can't use function return value in write context
* improved: display info if module has no label

= Version 2.3 =

* new: option to hide the Webversion Bar
* new: option to disable tracking on campaign based basis
* new: option to disable user avatars
* new: time frame based delivery for campaigns
* new: Mailster test suite to test compatibility
* new: option to crop images in the picpicker
* new: elements can now expect fields in templates with `<single expect="title"></single>`
* new: option to disable Webversion bar
* new: option for list based subscription
* new: subscriber query class for better list segmentation
* new: cron command page
* new: `{lists}` tag to display campaign related lists
* new: `mailster_option` and `mailster_option_[option]` filter
* new: Export format: xls
* new: Option to duplicate forms
* new: Option to disable Webversion
* new: privacy settings page
* change: `mailster_replace_link` now targets the output link
* improved: list segmentation
* improved: campaign editor for faster campaign creation with inline editing
* Improved: modules with tags where the post not exists will get removed
* improved: image procession to support more third party plugins
* improved: info message on form submission now placed on after the form depending on scroll position.
* improved: background images behavior in editor
* improved: faster editor behavior
* improved: batch action on subscribers
* improved: multiple cron processes
* improved: image creation process to better support third party plugins
* improved: cron mechanism
* improved: export column selection
* improved: handling of placeholder images on td, th and v:fill
* added: copy-to-clipboard functionality
* added: subscriber crows indicator on dashboard widget
* added: Additional mail headers
* added: option to release cron lock
* added: option to reset cron last hit
* updated: PHPMailer to version 5.2.26
* deprecated MyMail methods


For further details please visit [the change log on the Mailster Homepage](https://mailster.co/changelog/)


