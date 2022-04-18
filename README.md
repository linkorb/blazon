<img src="doc/assets/blazon.png" />

Blazon
======

Blazon is a data-driven, plugin-powered, multi-target content publishing framework.

## Use-cases:

* Documentation, guides, manuals
* Contracts (with variables)
* Legal documents
* "Conditional text" content
* Wiki content rendering
* Marketing sites
* Policies, Procedures, Forms
* Knowledge graphs
* Digital gardens
* Code repository project documentation
* Complex data graph visualization (i.e. organizational structures)

## Definitions:

* Publication: Collection of "documents"
* Document: unique identifier (path), and a controller for rendering output
* Plugin: allows to modify every phase of the loading and publishing process
* Target: one of many outputs, produced by a Publisher (i.e. static html, pdf, etc).

## Features:

* Load content from local or remote sources
* Markdown, with frontmatter
* Xillion Resources
* Twig templating
* Supports both static site generation and dynamic rendering

## Examples

* https://github.com/linkorb/starwars-blazon graphql + twig example site


## Installation

```sh
git clone git@github.com:linkorb/blazon.git
cd blazon
composer install # install php dependencies into blazon's vendor/ directory
./bin/blazon -v # get version and help output
```

## Usage

Your Blazon project (i.e. your site, book, report, etc) should  be stored in it's own directory, preferably a Git repository.

From there, use the `blazon` cli tool to generate a static HTML site into the `build/` directory of your project:

### Publishing

```sh
cd path/to/my/blazon-project
path/to/blazon publish -vvv
```

This command:

1. Builds your Publication in-memory, using your custom `PublicationFactory`, or the default factory.
2. Loops over all registered `Document` instances
3. Renders the output of each `Document` using it's `handler` callback, and writes it to the Document's `path` into
`build/`
4. Recursively copies any public assets (images, css, js, pdfs, etc) from `public/` into `build/`

You can now serve the generated `build/` directory as a static (html) site on your favorite service:

* A simple nginx container
* Github pages
* Netlify
* ...etc

### Auto-Publishing with "watch-mode"

Pass the `-w` flag to the `blazon publish` command to tell blazon to keep watching your project's source directory for changes. After any detected change, it will automatically (re)publish to `build/`

### Preview server

Blazon includes a minimal preview server that you can use while authoring content and styling your publication.

Simply point your favorite PHP-enabled webserver (i.e. apache with mod-php, nginx with php-fpm, etc) to blazon's `public/` directory.

Make sure to setup `SOURCE_PATH=/path/to/your/blazon-project` in Blazon's `.env` file.

Features:

* Builds the publication on every HTTP request
* In-memory, no writes to `build/`
* Verbose error messages when setting `DEBUG=true` in your `.env` file.
* Supports serving static assets from your publication's `public/` directory.

It is not recommended to use the preview server in production. Use the `blazon publish` command instead for maximum security and performance.

## Inspiration

* Gatsby
* Hugo
* Sage
* Xillion
* Schemata

## License

MIT. Please refer to the [license file](LICENSE.md) for details.

## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).

Btw, we're hiring!
