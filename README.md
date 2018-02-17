# Markdown Pages Sprinkle for [UserFrosting 4](https://www.userfrosting.com)
[![Build Status](https://travis-ci.org/lcharette/UF_MarkdownPages.svg?branch=master)](https://travis-ci.org/lcharette/UF_MarkdownPages)

Simple flat-file page manager for UserFrosting 4. Drop any markdown file in your sprinkle and access them directly inside your UserFrosting installation. Support custom templates and sprinkle priority. It's just like a mini [Grav](https://getgrav.org) site, directly inside UserFrosting.

> This sprinkles requires UserFrosting 4.1 or newer

# Help and Contributing

If you need help using this sprinkle or found any bug, feels free to open an issue or submit a pull request. You can also find me on the [UserFrosting Chat](https://chat.userfrosting.com/) most of the time for direct support. You can also contribute to this sprinkle by buying me coffee :

<a href='https://ko-fi.com/A7052ICP' target='_blank'><img height='36' style='border:0px;height:36px;' src='https://az743702.vo.msecnd.net/cdn/kofi4.png?v=0' border='0' alt='Buy Me a Coffee at ko-fi.com' /></a>

# Installation
Edit UserFrosting `app/sprinkles.json` file and add the following to the `require` list : `"lcharette/uf_markdownpages": "^1.0.0"`. Also add `MarkdownPages` to the `base` list. For example:

```json
{
    "require": {
        "lcharette/uf_markdownpages": "^1.0.0"
    },
    "base": [
        "core",
        "account",
        "admin",
        "MarkdownPages"
    ]
}
```

Run `composer update` and `php bakery bake` to install the sprinkle.

# Features and usage

TODO

# Running tests

This sprinkle comes supports automated testing. Before submitting a new Pull Request, you need to make sure all tests are a go. With the sprinkle added to your UserFrosting installation, simply execute the `php bakery test` command to run the tests.

# Licence

By [Louis Charette](https://github.com/lcharette). Copyright (c) 2018, free to use in personal and commercial software as per the MIT license.

# TODO
- Add basic permissions to pages
- Support multiple languages