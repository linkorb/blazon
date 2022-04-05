Starwars GraphQL API with Twig templates example
================================================

This directory contains a complete example for publishing a static
website based on data retrieved from the following GraphQL API:

* https://graphql.org/swapi-graphql/

The example defines queries (for retrieving films and characters) in the `graphql/` directory.

Twig Templates for visualising the query responses are stored in the `templates/` directory.

The `src/PublicationFactory.php` file defines a custom Publication factory that executes the queries and constructs the publication for publishing.

## Usage

Use the following command to generate a static website in the `build/` directory with verbose output:

    ../../bin/blazon publish -vv


