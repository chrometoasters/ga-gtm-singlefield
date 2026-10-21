# GA/GTM single-field

Adds support for a single-field configuration of GA/GTM code within the CMS.  
Comes with Google snippets that need to be included in your theme's templates.

## Requirements

* Silverstripe CMS 6
* PHP 8.3 or later

For Silverstripe CMS 4 and 5, use the 2.x releases.

## Installation

### Module

```sh
$ composer require chrometoaster/ga-gtm-singlefield
```

Alternatively, you can also install the module manually by placing a copy to the root folder of your site.

Run dev/build either via opening the url `http://<your-host>/dev/build` in a browser or
by running the dev/build via a CLI.

### Theme integration

Place two template includes into your theme's main template, e.g. Page.ss.

#### Head

`<% include GA_GTM_head %>` as close to the opening `<head>` tag as possible:

```html
<head itemscope itemtype="http://schema.org/WebSite">
    <meta charset="utf-8">
    <title itemprop="name">$Title</title>

    <% include Chrometoaster\GA_GTM\GA_GTM_head %>
```

#### Body

`<% include Chrometoaster\GA_GTM\GA_GTM_body %>` directly after the opening `<body>` tag:

```html
</head>
<body>
    <% include Chrometoaster\GA_GTM\GA_GTM_body %>
```


## Usage

You will see a new 'GA & GTM' tab with a single field for a GA or GTM code under Settings in the CMS.  
Insert a valid GA or GTM code and check if it's getting output to the resulting markup correctly.

## Changelog

For details of updates, bugfixes, and features, please see the [changelog](CHANGELOG.md).

## TODO

* Replace template includes with TemplateGlobalProvider or Controller function.
* Investigate options for automatically inserting the necessary tags to the markup, e.g. extending the $MetaTags functionality or using Requirements backend.
