# Intro

## Description

The Menu Multilingual module provides multilingual features for menu blocks,
filter out menu items that aren't multilingual.

Out of the box integrations provided for the next menu block types:
`systemMenuBlock`, `menuBlock`.
Supported menu item types: `MenuLinkContent`.<br>
Supported menu item links: any that extend a `ContentEntityBase` class.

## Features

- Only display menu links with translated labels<br>
   Filter out menu items that does not have a translated label.
    
- Only display menu links with translated content<br>
    Filter out menu items when menu item is a translatable entity without translation..

## Notices

General rule of thumb to show a menu item:
- entity language equals current language
- or language is undefined
- or entity has translations
- or translations are disabled for this entity type

Options are combined using "AND" operator. This means that if you enabled both options,
than link will be visible only if certain item meets both conditions:
- menu item label is translated,
- menu item link is translatable entity that has translations.

When menu item has a submenu and menu item does not meet enabled conditions than submenu will be filtered out too.

**Don't forget** to clear a cache when add a new menu item or change a block settings.

# Requirements

Required modules:
- menu_link_content
- content_translation

# Installation

1. Install the module as normal, note that there are two dependencies.
2. Add one or more language if you haven't yet enabled a content_translation module.
3. Go to `admin/config/regional/content-language` and tick at least `Custom menu link` option, 
you can also enable translations for `Content` entity per bundle.
4. Configure a particular menu block at `admin/structure/block`.
5. Clear a cache.
