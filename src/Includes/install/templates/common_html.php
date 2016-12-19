<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/*
 * Output the common HTML blocks
 */

if (!defined('XLITE_INSTALL_MODE')) {
    die('Incorrect call of the script. Stopping.');
}


function show_install_html_header() {

    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
    }
?>
<head>
  <title>X-Cart v.<?php echo LC_VERSION; ?> <?php echo xtr('Installation Wizard'); ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Content-Script-Type" content="type/javascript" />
  <meta name="ROBOTS" content="NOINDEX" />
  <meta name="ROBOTS" content="NOFOLLOW" />
  <link rel="shortcut icon" href="public/favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="skins/common/css/font-awesome/font-awesome.min.css">
  <link rel="stylesheet" href="skins/common/bootstrap/css/bootstrap.min.css">
<?php

}

function show_install_css() {

    global $skinsDir;

?>

  <style type="text/css">

/**
  * Clear styles
  */

html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, font, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, label, legend, caption, input, textarea
{
  margin: 0;
  padding: 0;
  border: 0;
  outline: 0;
}

ol, ul
{
  list-style: none;
}

blockquote, q
{
  quotes:none;
}

blockquote:before,
blockquote:after,
q:before,
q:after
{
  content: '';
  content: none;
}

:focus
{
  outline:0;
}

a
{
  text-decoration: underline;
}


/**
  * Common styles
  */

body,
p,
div,
th,
td,
p,
input,
span,
textarea,
button
{
  color: #333333;
  font-size: 14px;
  font-family: Helvetica, Arial, Sans-serif;
}

body
{
  background-color: #ffffff;
}

a
{
  color: #154e9c;
}

h1,
h2,
h3
{
  font-family: "Trebuchet MS", Helvetica, Sans-serif;
  color: #69a4c9;
  font-weight: normal;
}

h1
{
  font-size: 30px;
  line-height: 36px;
  margin-bottom: 20px;
  margin: 10px 0 20px;
}

h2
{
  font-size: 24px;
  margin: 18px 0;
}

h3
{
  font-size: 18px;
  margin: 12px 0;
}

code {
  font-family: Arial, Helvetica, Sans-serif;
  font-size: 14px;
  color: #106cb1;
  background-color: transparent;
}

/**
  * Form elements styles
  */

input[type=text],
input[type=password]
{
  height: 32px;
  line-height: 16px;
  font-size: 14px;
}

input[type=text]:focus,
input[type=password]:focus,
select:focus,
textarea:focus
{
  border: solid 1px #999;
  font-size: 14px;
}

select
{
  line-height: 24px;
  font-family: Arial, Helvetica, Sans-serif !important;
  font-size: 12px !important;
}

select option {
  font-family: Arial, Helvetica, Sans-serif !important;
  font-size: 12px !important;
}

input[type="submit"].next-button,
input[type="button"].next-button,
input[type="reset"].next-button,
button.next-button {
  margin-left: -20px;
}

input[type="submit"].disabled-button,
input[type="button"].disabled-button,
input[type="reset"].disabled-button,
button.disabled-button {
  background: #dfdfdf;
  border: 1px solid #dfdfdf;
  color: white;
  cursor: default;
}

input.error,
select.error,
input.error:focus,
select.error:focus {
  border-color: #ff0000;
}

button span
{
  color: #0e55a6;
  font-family: "Trebuchet MS",Helvetica,sans-serif;
  font-size: 15px;
  vertical-align: middle;
}

button:hover
{
  border-color: #b1c9e0;
}

button.main
{
  padding-left: 10px;
  padding-right: 10px;
}

button.main span
{
  font-size: 18px;
  line-height: 18px;
}

button.invert
{
  background: url(<?php echo $skinsDir; ?>images/button_bg_blue.png) repeat 0 0;
  border-color: transparent;
}

button.invert span
{
  color: #fff;
}

button.invert:hover
{
  background-color: transparent;
  background-image: url(<?php echo $skinsDir; ?>images/button_bg_blue_hover.png);
}

td.next-button-layer {
  width: 151px;
  height: 114px;
}


/**
  * Layout
  */

html,
body
{
  min-width: 800px;
  height: 100%;
}

#content,
#sub-section,
#footer
{
  overflow: hidden;
}

#page-container {
  min-height: 100%;
  position: relative;
}

#page-container
{
  vertical-align: top;
  width: 100%;
}

#header {
  width: 100%;
  position: absolute;
  top: 0;
  left: 0;
}

#header, #menu
{
/*
  background: #0C263D url(<?php echo $skinsDir; ?>images/admin_header_bg.png) repeat-x left top;
  height: 76px;
*/
}

#header .logo
{
  background: url(<?php echo $skinsDir; ?>images/logo_admin.png) no-repeat 0 0;
  height:  80px;
  width: 82px;
  float: left;
}

.sw-version
{
  position: absolute;
  left: 90px;
  top: 12px;
  width: 870px;
}

.current
{
  color: #7f90A0;
  margin-right: 10px;
  font-size: 12px;
  display: inline;
}

.upgrade-note
{
  margin-right: 10px;
  text-align: right;
  float: right;
  white-space: nowrap;
}

.upgrade-note a {
  text-decoration: none;
  padding-left: 5px;
}

/**
  * Page content styles
  */
div.install-page
{
  width: 960px !important;
  margin: 0 auto;
}

div.install-page #header .logo {
  height: 80px;
  width: 82px;
}

div.install-page #header, #menu {
  background: transparent none;
}

div.install-page #header .sw-version {
  left: 100px;
}

div.install-page h1 {
  margin: 30px 0 0 100px;
  font-family: Arial,Verdana,sans-serif;
  font-size: 36px;
}

div.install-page #content {
  background: transparent none;
  border-top: 0 none;
}

div.content
{
  position: absolute;
  top: 160px;
  width: 100%;
  text-align: center;
}

#email {
  margin: 20px 0 20px;
  text-align: left;
}

#email input {
  display: inline-block;
  max-width: 300px;
}

#copyright_notice {
  border: 1px solid #999999;
  font-family: "Courier New", monospace;
  font-size: 14px;
  height: 300px;
  margin-bottom: 10px;
  margin-top: 10px;
  padding: 10px;
  overflow: auto;
  text-align: left;
  width: 938px;
}

.status-report-box .permissions-list {
  border: 1px solid #999999;
  font-family: "Courier New", monospace;
  font-size: 11px;
  font-style: italic;
  max-height: 250px;
  margin-bottom: 10px;
  padding: 5px;
  padding-bottom: 20px;
  overflow: auto;
  text-align: left;
  width: 320px;
}

.status-report-box .copy2clipboard {
  cursor: pointer;
  float: right;
  margin-bottom: 5px;
}

.error-text.lc_file_permissions .copy2clipboard-alert {
  position: absolute;
  padding: 6px;
  margin-top: 24px;
  width: 290px;
  margin-left: 6px;
}

.field-label {
  font-size: 14px;
  font-weight: bold;
  text-align: left;
  margin-right: 10px;
  vertical-align: baseline;
}

.field-label .required {
  padding-left: 3px;
  color: red;
}

.checkbox-field {
  font-size: 16px;
  text-align: left;
  color: #53769d;
  line-height: 1px;
  vertical-align: baseline;
}

.checkbox-field label
{
  display: inline;
  padding-left: 4px;
  white-space: nowrap;
}

.field-notice {
  font-size: 12px;
  font-style: italic;
  text-align: left;
  color: #8f8f8f;
}

td.field-notice {
  padding-left: 10px;
  text-align: left;
}

/**
  * Common styles
  */

.status-ok {
  color: green;
}

.status-failed {
  color: #c11600;
}

.status-failed-link {
  color: #c11600;
  text-decoration: underline;
}

.status-failed-link-active {
  color: #c11600;
  text-decoration: none;
  cursor: default;
}

.status-skipped {
  color: #145d8f;
}

.status-already-exists {
  color: #145d8f;
}


/**
  * Requirements checking page styles
  */

.clear {
  clear: both;
}

div.requirements-report {
}

div.requirements-list {
  float: left;
  width: 60%;
}

div.requirements-notes {
  float: right;
  width: 40%;
}

div.section-title {
  color: #68a3c8;
  padding-bottom: 5px;
  font-size: 16px;
  text-align: left;
}

div.list-row {
  text-align: left;
  font-size: 14px;
  padding-top: 4px;
  padding-left: 5px;
  padding-right: 15px;
  height: 24px;
}

.color-1 {
  background: #eeeeee;
}

.color-2 {
  background: white;
}

div.field-left {
  float: left;
  text-align: left;
  width: 70%;
}

div.field-right {
  float: right;
  text-align: right;
  width: 30%;
  white-space: nowrap;
}

.error-title {
  text-align: left;
  font-size: 16px;
  color: #c11600;
  padding-top: 15px;
  margin-left: 24px;
}

.error-text {
  text-align: left;
  padding-top: 10px;
  margin-left: 24px;
}

p {
  padding-bottom: 5px;
  padding-top: 5px;
}

div.requirements-warning-text {
  padding-top: 25px;
  padding-bottom: 25px;
  font-size: 12px;
  color: #333333;
}

div.status-report-box {
  border-radius: 10px;
  -moz-border-radius: 10px;
  -webkit-border-radius: 10px;
  background-color: #faf2f0;
  padding: 15px 20px;
  margin-top: 15px;
  margin-left: 24px;
  text-align: left;
}

div.status-report-box-text {
  text-align: left;
  padding-bottom: 10px;
}

div.status-report-box-text em,
.fatal-error .note em,
.fatal-error .additional-note em {
  text-decoration: none;
  font-style: normal;
  font-weight: bold;
}

input[type="button"].active-button {
  background: -moz-linear-gradient(top, #f59f57 0%, #f3923c 100%);
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f59f57), color-stop(100%,#f3923c));
  background: -webkit-linear-gradient(top, #f59f57 0%, #f3923c 100%);
  background: -o-linear-gradient(top, #f59f57 0%, #f3923c 100%);
  background: -ms-linear-gradient(top, #f59f57 0%, #f3923c 100%);
  background: linear-gradient(to bottom, #f59f57 0%, #f3923c 100%);
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  border-radius: 5px;
  padding: 6px 10px;
  border: 1px solid #e88d42;
  color: white;
  font-size: 14px;
  font-weight: normal;
  margin: 0;
}

.status-report-box .error-text {
  padding: 14px 0 14px 0;
  margin: 0;
  border-top: solid 1px #dedede;
  line-height: 18px;
}

.status-report-box .error-text:first-child {
  border-top: none;
  padding-top: 0;
}

.status-report-box .error-text.lc_php_version b {
  color: #c11600;
}

.status-report-box #re-check-button {
  margin-right: 14px;
}

.cloud-box {
  margin-top: 10px;
  margin-left: 24px;
  text-align: center;
}

.cloud-box .grey-line {
  display: inline-block;
  border-bottom: solid 1px #dedede;
  width: 360px;
}

.cloud-box .or-cloud {
  display: inline-block;
  position: relative;
  top: 12px;
  background-color: white;
  padding: 2px;
}

.cloud-box .or-cloud span {
  color: white;
  font-size: 12px;
  padding: 10px 8px;
  background: #dedede;
  -moz-border-radius: 17px;
  border-radius: 17px;
  -webkit-border-radius: 17px;
  width: 34px;
  height: 34px;
}

.cloud-box .cloud-header {
  font-size: 22px;
  margin-top: 30px;
  margin-bottom: 10px;
}

.cloud-box .cloud-text {
  font-size: 14px;
  margin: auto;
  padding-bottom: 10px;
}

.link-expanded {
  margin-top: -3px;
  margin-right: -9px;
}

.requirements-success {
  padding-top: 45px;
  padding-left: 30px;
  text-align: center;
  font-family: Arial,Helvetica,sans-serif;
  font-size: 36px;
  color: #51924a;
}

.requirements-success-image {
  padding-left: 35px;
}

/**
  * Step bar styles definition
  */

div.steps-bar {
  position: absolute;
  top: 98px;
}

.steps {
  border-style: none;
  margin: 0;
  padding: 0;
}

.step-row {
  background: #666;
  float: left;
  list-style: none outside none;
  height: 40px;
  font-family: Arial,Verdana,sans-serif;
  font-size: 18px;
  color: white;
  line-height: 40px;
  padding-left: 10px;
  padding-right: 10px;
}

.first {
  border-radius: 6px 0 0 6px;
  -moz-border-radius: 6px 0 0 6px;
  -webkit-border-radius: 6px 0 0 6px;
  padding-left: 20px;
}

.last {
  border-radius: 0 6px 6px 0;
  -moz-border-radius: 0 6px 6px 0;
  -webkit-border-radius: 0 6px 6px 0;
  padding-right: 20px;
}

.next {
  background: #dfdfdf;
}

.prev-prev {
  background: url(<?php echo $skinsDir; ?>images/arrow_dark.png) no-repeat scroll center center transparent;
}

.prev-next {
  background: url(<?php echo $skinsDir; ?>images/arrow_dark_grey.png) no-repeat scroll center center transparent;
}

.next-next {
  background: url(<?php echo $skinsDir; ?>images/arrow_grey.png) no-repeat scroll center center transparent;
}

/**
  * /end of step bar styles definition
  */

.full-width {
  width: 100%;
}

#process_iframe {
  padding-left: 15px;
  border: 1px solid black;
}

.cache-error {
  font-size: 16px;
  text-align: left;
  margin-bottom: 20px;
}

.cache-error span {
  font-size: 16px;
  color: #c11600;
}

.keyhole-icon {
  margin-right: 50px;
}

a.final-link {
  font-size: 22px;
  text-decoration: none;
  color: #144b9d;
}

a.final-link:hover {
  text-decoration: underline;
}

.report-layer {
  background: url(<?php echo $skinsDir; ?>../customer/images/popup_overlay.png) repeat scroll 50% 50% transparent;
  left: 0;
  position: absolute;
  top: 0;
  width: 900px;
  z-index: 1003;
  height: 100%;
  width: 100%;
}

.report-window {
  -moz-border-radius: 11px;
  border-radius: 11px;
  -webkit-border-radius: 11px;
  border: 10px solid #7a7a7a;
  background: white;
  width: 830px;
  margin: 60px auto;
  padding: 30px;
  z-index: 1004;
}

.report-title {
  font-size: 28px;
  color: #68a3c8;
}

textarea.report-details {
  font-family: "Courier New", monospace;
  font-size: 14px;
  height: 140px;
  width: 100%;
}

textarea.report-notes {
  height: 60px;
  width: 90%;
}

a.report-close {
  -moz-border-radius: 0 11px 11px 0;
  border-radius: 0 11px 11px 0;
  -webkit-border-radius: 0 11px 11px 0;
  background: url(<?php echo $skinsDir; ?>../../admin/images/icon_window_close.png) no-repeat scroll 10px 10px #7A7A7A;
  display: block;
  height: 41px;
  margin-left: 780px;
  margin-top: -40px;
  outline-style: none;
  right: 0;
  top: 0;
  width: 40px;
  z-index: 1005;
}

.hidden {
  display: none;
}

.fatal-error,
.warning-text {
  -moz-border-radius: 9px;
  border-radius: 9px;
  -webkit-border-radius: 9px;
  border: 10px solid #7a7a7a;
  background: white;
  margin: 10px auto;
  padding: 20px;
  width: 500px;
  text-align: center;
}

.warning-text {
  font-size: 16px;
  color: #0e55a6;
  text-align: left;
}

.fatal-error > div {
  font-size: 16px;
  color: #c11600;
  text-align: left;
}

.fatal-error > div.additional-note {
  margin-top: 14px;
  font-size: 14px;
  color: #333;
}

.fatal-error .note {
  margin-top: 20px;
  margin-bottom: 20px;
  color: #333333;
  font-size: 14px;
}

.fatal-error input.active-button {
  padding: 12px 20px;
  font-size: 16px;
}

td.table-left-column {
  text-align: left;
  width: 60%;
  padding: 6px;
  border: 2px #fff solid;
}

td.table-right-column {
  text-align: left;
  width: 40%;
  border: 2px #fff solid;
  padding: 6px;
}

table tr.section-title td {
  text-align: left;
}

table tr.section-title td span{
  display: inline-block;
  font-size: 14px;
  cursor: pointer;
  margin: 20px 0px 10px;
  border-bottom: dotted 1px black;
}

table tr.section {
  display: none;
}

.buttons-bar td {
    padding: 0 10px;
}

.buttons-bar td:first-child {
  padding-left: 0;
}

.section-pdo-error {
  color: #999999;
  font-size: 12px;
  margin-top: 10px;
}

.pdo-details {
  font-size: 16px;
  cursor: pointer;
  border-bottom: dotted 1px black;
  margin-left: 5px;
}

.section.section-pdo-error {
  display: none;
}

.btn.disabled,
.btn[disabled],
fieldset[disabled] .btn {
  opacity: 0.15;
}

.fatal-error.extended {
  border: none;
  margin: 0;
  padding: 0;
  margin-top: 20px;
  width: 530px;
}

.fatal-error .header {
  font-size: 36px;
  margin-bottom: 20px;
}

.fatal-error.extended .cloud-box {
  border: 1px solid #dbdfe2;
  -moz-border-radius: 5px;
  border-radius: 5px;
  -webkit-border-radius: 5px;
  position: absolute;
  top: 0;
  right: 0;
  margin-top: 30px;
  padding: 40px 30px;
  text-align: center;
}

.cloud-box .svg-icon {
  display: none;
}

.cloud-box .svg-icon img {
  width: 64px;
  height: 64px;
}

.fatal-error.extended .cloud-box .svg-icon {
  display: block;
  margin-top: 0;
}

.fatal-error.extended .cloud-box .grey-line {
  display: none;
}

.fatal-error.extended .cloud-box .cloud-header {
  margin-top: 0;
  font-size: 20px;
}

.fatal-error.extended .cloud-box .cloud-text {
  font-size: 15px;
  padding-bottom: 15px;
}

table.display-help {
 float: left;
}

.btn-lg {
  font-size: 16px;
}

.install-help-box {
  display: table;
  position: absolute;
  top: 99px;
  right: 0;
}

.install-help-box img {
  display: table-cell;
  vertical-align: middle;
  margin-top: 3px;
  width: 32px;
  height: 32px;
}

.install-help-box div {
  display: table-cell;
  vertical-align: middle;
  padding-left: 8px;
}

.install_done ul.permissions-list {
  border: none;
  border-radius: 5px;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  background-color: #f8f8f8;
  font-size: 14px;
  padding: 16px 24px;
  margin: 0;
  margin-top: 15px;
  display: inline-block;
}

.install_done ul.permissions-list li:before {
  content: "$";
  padding-right: 6px;
  color: #cccccc;
}

.install_done .clipbrd {
  margin-top: 15px;
  margin-bottom: 22px;
}

input[type="button"].copy2clipboard {
  padding: 9px 17px;
  color: #144b9d;
}

input[type="button"].copy2clipboard:hover {
  background-color: #ffffff;
  border-color: #cccccc;
}

.install_done .copy2clipboard-alert {
  display: inline-block;
  border-radius: 3px;
  -moz-border-radius: 3px;
  -webkit-border-radius: 3px;
  padding: 6px;
  width: 290px;
  margin-left: 10px;
}

.install_done .second-title {
  margin-bottom: 13px;
}

.install_done p {
  padding-bottom: 17px;
  padding-top: 0;
}

.install_done p.customer-link {
  padding-bottom: 9px;
}

</style>

<?php

}
