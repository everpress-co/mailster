=== Mailster - Email Newsletter Plugin for WordPress ===
Contributors: revaxarts
Tags: mailster, mymail, newsletter, email, revaxarts
Requires at least: 3.8
Tested up to: 4.8
Stable tag: 2.2.7
Author: revaxarts
Author URI: https://mailster.co

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
* Revisions support (native)
* Multi language Support (over 10 languages included)
* SMTP support
* DomainKeys Identified Mail Support
* Import and Export for Subscribers
* Retina support

== Templates ==

These Templates are made for the Mailster Newsletter Plugin. They have been fully tested with all major email softwares and providers. They are all available exclusively on ThemeForest.

If you have further questions please visit our [knowledge base](https://kb.mailster.co)

Xaver Birsak – https://revaxarts.com


= Linus =
[!(https://mailster.github.io/preview/linus.jpg)](http://rxa.li/linus?utm_source=Plugin+Info+Page)
= Metro =
[!(https://mailster.github.io/preview/metro.jpg)](http://rxa.li/metro?utm_source=Plugin+Info+Page)
= My Business =
[!(https://mailster.github.io/preview/business.jpg)](http://rxa.li/business?utm_source=Plugin+Info+Page)
= Loose Leaf =
[!(https://mailster.github.io/preview/looseleaf.jpg)](http://rxa.li/looseleaf?utm_source=Plugin+Info+Page)
= Market =
[!(https://mailster.github.io/preview/market.jpg)](http://rxa.li/market?utm_source=Plugin+Info+Page)
= Skyline =
[!(https://mailster.github.io/preview/skyline.jpg)](http://rxa.li/skyline?utm_source=Plugin+Info+Page)
= Letterpress =
[!(https://mailster.github.io/preview/letterpress.jpg)](http://rxa.li/letterpress?utm_source=Plugin+Info+Page)


== Changelog ==

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


