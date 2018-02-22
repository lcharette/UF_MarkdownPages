# Extending Parsedown

Markdown pages uses [Parsedown](https://github.com/erusev/parsedown) and [MetaParsedown](https://github.com/pagerange/metaparsedown) as the markdown parser. You can use Parsedown extension feature to add your own elements. Since Markdown Pages already provides some custom elements (notices), you can simply extends our class to add your own elements. Once this is done, you simply need to switch the `markdown` service so UserFrosting serves your own class instead of the base one.  

## Extends the base class

## Overwrite the class in your sprinkle services provider.

## Conflicts when dealing with multiple sprinkles

Conflict can emerge when multiple sprinkles wants to extends Parsedown as only one class can be defined as the service provider. In such cases, incompatible sprinkles should be dealt with manually. This means your sprinkle will be the one choosing which one gets to rule, or be responsible for merging both definitions in the same class.