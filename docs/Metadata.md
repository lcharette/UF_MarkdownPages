# Metadata

Markdown pages uses [MetaParsedown](https://github.com/pagerange/metaparsedown) and [Parsedown](https://github.com/erusev/parsedown) as the markdown parser. An example page file could look like this:

```
---
title: Page Title
description: Page description
---
## Page Title

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque porttitor eu
felis sed ornare. Sed a mauris venenatis, pulvinar velit vel, dictum enim. Phasellus
ac rutrum velit. **Nunc lorem** purus, hendrerit sit amet augue aliquet, iaculis
ultricies nisl. Suspendisse tincidunt euismod risus, _quis feugiat_ arcu tincidunt
eget. Nulla eros mi, commodo vel ipsum vel, aliquet congue odio. Class aptent taciti
sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Pellentesque
velit orci, laoreet at adipiscing eu, interdum quis nibh. Nunc a accumsan purus.
```

The settings between the pair of `---` markers is known as the YAML FrontMatter, and it is comprised of basic YAML metadata and settings for the page. In this example, we are explicitly setting the title, as well the page description. The content after the second `---` is the actual content that will be compiled and rendered as HTML.

## `title`

Set the page title. While technically optional, the title is required to show the page in the sidebar treeview. You should really define at least the title.

## `description`

_Optional_. Set the page description in the page sub header and metadata.

## `redirect`

_Optional_. Redirect to another page, another internal route or external URL. To redirect to another markdown static page, specify the base slug. For example, to redirect to `01.home/03.foo`, use `redirect: home/foo`. This will redirect the page to the correct place, eg `/p/home/foo`.

## `authGuard`

_Optional_. Set to `true` to required an authenticated user to display the page. The controller will throw an `AuthExpiredException` if the client is not authenticated which redirect to the login page by default.

## `hide_toc`

_Optional_. Set to `true` to hide the page table of content.