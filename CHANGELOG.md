# Changelog for Pilau Base

## 2.2.2 (????-??-??)
* Changed `pilau_content()` to not use `get_the_excerpt()` (which uses content if there's no excerpt)
* Added `pilau_get_roles_for_post_type()` (as part of fixing author meta box drop-down)
* Added (commented out) `embed_oembed_html` filter for `pilau_wmode_opaque()`
* Added functions for handling query args with multiple values
* Restricted 'Refresh' item in WP admin bar to editors only
* Fixed a number of minor errors

## 2.2.1 (2016-01-23)
* Changed `pilau_slug_stopwords()` so it can be used with a specified title
* Changed `pilau_gf_get_value()` as it didn't seem to be working; legacy code remains because it was tested with previous projects

## 2.2 (2015-11-09)
* `pilau_get_users_by_capability()`
* Adjusted `pilau_responsive_image` to work better with retina by default
* Filter to prevent non-admins from creating admin accounts
* Filter to prevent non-admins from editing admin accounts

## 2.1.3 (2015-08-19)
* Added slug stopwords removal to compensate for Yoast SEO not working
* Removed CMB2 symlink hack (handled in Pilau Starter)

## 2.1.2 (2015-08-18)
* Refresh button in admin toolbar for admin as well as front-end
* Fix for IE9 sources hack in `pilau_responsive_image()`
* Hack to handle symlinked CMB2 in local dev

## 2.1.1 (2015-06-21)
* `pilau_string_of_one_liners_to_array()`

## 2.1 (2015-06-14)
* Support for `<picture>` element art direction in `pilau_responsive_image()`

## 2.0 (2015-05-13)
* First version with changelog, ahem

## 1.0
* First version
