# Adding the page treeview to the sidebar

To add the page treeview, simply include the provided template in `templates/navigation/sidebar-menu.html.twig` :

```
{% include 'navigation/markdownPages.html.twig' %}
```

See [UserFrosting documentation](https://learn.userfrosting.com/recipes/extending-template#adding-custom-menu-entries) for more details on how to add custom entries to the sidebar menu.