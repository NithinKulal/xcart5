; <?php /*
; WARNING: Do not change the line above
;
; +-------------------------------------+
; |   X-Cart 5 configuration file   |
; +-------------------------------------+
;
; -----------------
;  About this file
; -----------------
;

;
; ----------------------
;  SQL Database details
; ----------------------
;
[database_details]
hostspec = ""
socket   = ""
port     = ""
database = ""
username = ""
password = ""
table_prefix = "xlite_"

;
; ----------------------
;  Cache settings
; ----------------------
;
[cache]
; Type of cache used. Can take auto, memcache, memcached, apc, xcache, file values.
type=file
; Cache namespace
namespace=XLite
; List of memcache servers. Semicolon is used as a delimiter.
; Each server is specified with a host name and port number, divided
; by a colon. If the port is not specified, the default
; port 11211 is used.
servers=

;
; -----------------------------------------------------------------------
;  X-Cart 5 HTTP & HTTPS host, web directory where cart installed
;  and allowed domains
; -----------------------------------------------------------------------
;
; NOTE:
; You should put here hostname ONLY without http:// or https:// prefixes
; Do not put slashes after the hostname
; Web dir is the directory in the URL, not the filesystem path
; Web dir must start with slash and have no slash at the end
; The only exception is when you configure for the root of the site,
; in which case you write single slash in it
; Domains should be listed separated by commas.
;
; WARNING: Do not set the "$" sign before the parameter names!
;
; EXAMPLE 1:
;
;   http_host = "www.yourhost.com"
;   https_host = "www.securedirectories.com/yourhost.com"
;   web_dir = "/shop"
;   domains = "www.yourhost2.com,yourhost3.com"
;
; will result in the following URLs:
;
;   http://www.yourhost.com/shop
;   https://www.securedirectories.com/yourhost.com/shop
;
;
; EXAMPLE 2:
;
;   http_host = "www.yourhost.com"
;   https_host = "www.yourhost.com"
;   web_dir = "" (don't use "/")
;
; will result in the following URLs:
;
;   http://www.yourhost.com
;   https://www.yourhost.com
;
[host_details]
http_host = ""
https_host = ""
web_dir = ""
domains = ""
admin_self = "admin.php"
cart_self = "cart.php"

[clean_urls]
; String with one or more chars.
; It will be used to autogenerate clean URLs.
; By default, only the "-" or "_" characters are allowed.
; Empty string is also allowed.
default_separator = "-"

; Get clean URLs capitalized for every starting letter of a word
capitalize_words = Off

; Use canonical URL for product page
use_canonical_urls_only = On

;
; -----------------
;  Logging details
; -----------------
;
[log_details]
type = file
name = "var/log/xlite.log.php"
level = LOG_WARNING
ident = "XLite"
suppress_errors = On
suppress_log_errors = Off

;
; Skin details
;
[skin_details]
skin = default
locale = en

;
; Default image settings
;
[images]
default_image = "images/no_image.png"
default_image_width = 110
default_image_height = 110
unsharp_mask_filter_on_resize = off

; Installation path of Image Magick executables:
; for example:
; image_magick_path = "C:\\Program Files\\ImageMagick-6.7.0-Q16\\"   (in Windows)
; image_magick_path = "/usr/local/imagemagick/" (in Unix/Linux )
; You should consult with your hosting provider to find where Image Magick is installed
; If you leave it empty then PHP GD library will be used.
;
image_magick_path =

;
; Installer authcode.
; A person who do not know the auth code can not access the installation script.
; Installation authcode is created authomatically and stored in this section.
;
[installer_details]
auth_code = ""
shared_secret_key = ""

;
; Some options to optimize the store
;
[performance]
developer_mode = Off
cache_namespace_hash = On
skins_cache = off

;
; Decorator options
;
[decorator]
time_limit = 600
use_tokenizer = Off
disable_software_reset = Off
use_output = Off
quick_data_rebuilding = Off

;
; Error handling options
;
[error_handling]
; Template for error pages
page = "public/error.html"
page_customer = "public/customer/error.html"
; Template for maintenance pages
maintenance = "public/maintenance.html"

;
; Marketplace
;
[marketplace]
url = "http://my.x-cart.com/index.php?q=api"
log_data = Off
upgrade_step_time_limit = 240
banner_url = "http://my.x-cart.com/xcinfo"

;
; Language options
;
[language]
default = en

;
; Installation parameters
;
[installation]
installation_lng = en

;
; AMQP server
;
[amqp]
host     = "localhost"
port     = 5672
user     = "guest"
password = "guest"
vhost    = "/"
exchange = "xlite"

;
; HTML Purifier options
; See http://htmlpurifier.org/live/configdoc/plain.html for more details on HTML Purifier options
;
[html_purifier]
; Allow link 'target' attribute
Attr.AllowedFrameTargets = On

; List of allowed values for 'target' attribute
Attr.AllowedFrameTargets[] = _blank
Attr.AllowedFrameTargets[] = _self
Attr.AllowedFrameTargets[] = _top
Attr.AllowedFrameTargets[] = _parent

; Allow 'id' attribute
Attr.EnableID = On

; Allow tricky css like 'display:block;' on images
CSS.AllowTricky = On

; Allow embed tags
HTML.SafeEmbed = On

; Allow object tags
HTML.SafeObject = On

; Allow iframe tags
HTML.SafeIframe = On

; List of allowed URI (without http:// or https:// part) for iframe tags
; If there are no allowed URIs specified then any src will be allowed for iframe tags
;
; Examples:
;
; URI.SafeIframeRegexp[] = "www.youtube.com/embed/"
; URI.SafeIframeRegexp[] = "www.youtube-nocookie.com/embed/"
; URI.SafeIframeRegexp[] = "player.vimeo.com/video/"

[storefront_options]
; Do not close target=callback for payments if storefront is closed
callback_opened = On

;
; Other options
;
[other]
; Translation drive code - auto / gettext / db
translation_driver = auto
; Event driver code - auto / db / amqp
event_driver = auto

; List of trusted domains.
; This option prevents redirecting to untrusted URLs passed via returnURL parameter.
; Examples:
; trusted_domains = "google.com"
; trusted_domains = "google.com, yahoo.com"
trusted_domains =

; X-Frame-Options value
; For possible values see https://developer.mozilla.org/en-US/docs/Web/HTTP/X-Frame-Options
; Examples:
; x_frame_options = 'disabled'
; x_frame_options = 'sameorigin'
x_frame_options = 'sameorigin'

; CSRF token strategy
; possible values: per-session, per-form
csrf_strategy = per-session


[export-import]

; Export/Import available encodings list
; This values should be valid iconv encoding alias and should be listed in iconv -l output
encodings_list[] = 'UTF-8'
encodings_list[] = 'ISO-8859-1'
encodings_list[] = 'WINDOWS-1251'
encodings_list[] = 'CSSHIFTJIS'
encodings_list[] = 'WINDOWS-1252'
encodings_list[] = 'GB2312'
encodings_list[] = 'EUC-KR'
encodings_list[] = 'EUC-JP'
encodings_list[] = 'GBK'
encodings_list[] = 'ISO-8859-2'
encodings_list[] = 'ISO-8859-15'
encodings_list[] = 'WINDOWS-1250'
encodings_list[] = 'WINDOWS-1256'
encodings_list[] = 'ISO-8859-9'
encodings_list[] = 'BIG5'
encodings_list[] = 'WINDOWS-1254'
encodings_list[] = 'WINDOWS-874'

; WARNING: Do not change the line below
; */ ?>
