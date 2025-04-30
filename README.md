# Juni Twig Helper

Adds the following Twig filters and functions for use in the templates:

**inline**

Strip `<p>` tags from rich text field.

**lead**

Adds the class 'lead' to the paragraph elements.

**monthIndex**

Returns the number of the month in a year based on the month name.

**navTitle**

Returns the navigation title of an entry based on the custom field navigationTitle. When empty it defaults to the entry title.

**readingTime**

Estimates the reading time of a given text.

**scrub**

Removes empty `<p/>` tags from the html text. It also removes any non-breaking spaces.

**startsWith**

Returns whether a given string starts with the provided search string.

**timeAgo**

Formats a date as `7 days ago`, etc.

**typography**

Adds classes to `<ul>`, `<ol>` and `<table>` elements so they can be styled.
