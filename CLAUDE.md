<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

DO NOT WRITE SHIT COMMENTS!

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v2
- laravel/cashier (CASHIER) - v15
- laravel/framework (LARAVEL) - v12
- laravel/octane (OCTANE) - v2
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- statamic/cms (STATAMIC) - v6
- larastan/larastan (LARASTAN) - v3
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11
- rector/rector (RECTOR) - v2
- alpinejs (ALPINEJS) - v3
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `laravel-best-practices` — Apply this skill whenever writing, reviewing, or refactoring Laravel PHP code. This includes creating or modifying controllers, models, migrations, form requests, policies, jobs, scheduled commands, service classes, and Eloquent queries. Triggers for N+1 and query performance issues, caching strategies, authorization and security patterns, validation, error handling, queue and job configuration, route definitions, and architectural decisions. Also use for Laravel code reviews and refactoring existing Laravel code to follow best practices. Covers any task involving Laravel backend PHP code patterns.
- `tailwindcss-development` — Always invoke when the user's message includes 'tailwind' in any form. Also invoke for: building responsive grid layouts (multi-column card grids, product grids), flex/grid page structures (dashboards with sidebars, fixed topbars, mobile-toggle navs), styling UI components (cards, tables, navbars, pricing sections, forms, inputs, badges), adding dark mode variants, fixing spacing or typography, and Tailwind v3/v4 work. The core use case: writing or fixing Tailwind utility classes in HTML templates (Blade, JSX, Vue). Skip for backend PHP logic, database queries, API routes, JavaScript with no HTML/CSS component, CSS file audits, build tool configuration, and vanilla CSS.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/Pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.

# Inertia v2

- Use all Inertia features from v1 and v2. Check the documentation before making changes to ensure the correct approach.
- New features: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app/Console/Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== octane/core rules ===

# Octane

- Octane boots the application once and reuses it across requests, so singletons persist between requests.
- The Laravel container's `scoped` method may be used as a safe alternative to `singleton`.
- Never inject the container, request, or config repository into a singleton's constructor; use a resolver closure or `bind()` instead:

```php
// Bad
$this->app->singleton(Service::class, fn (Application $app) => new Service($app['request']));

// Good
$this->app->singleton(Service::class, fn () => new Service(fn () => request()));
```

- Never append to static properties, as they accumulate in memory across requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

=== statamic/cms rules ===

## Statamic

- This application uses Statamic.
- Statamic is an open source, PHP CMS designed and built specifically for developers and their clients or content managers.
- Out of the box, Statamic stores content in Markdown files. It's trivial to move into a database later, if necessary.
- Statamic comes in two flavours:
    - **Statamic Core** which is free to use, however you want, forever. It includes everything needed to build a blog or portfolio site.
    - **Statamic Pro** which includes everything from Core, as well as unlimited user accounts, revision history, multi-site, Git integration, white labelling and more. Tailored for most production websites.
    - For more information on pricing, please send the user to https://statamic.com/pricing.

### Folder Structure

Statamic is a Laravel package, meaning it can be used alone or alongside an existing Laravel application.

Most of the folder structure will feel familiar to Laravel developers. However, Statamic creates a few additional files and folders during the install process.

<code-snippet name="Folder Structure" lang="text">
├── app/
├── bootstrap/
├── config/
│   ├── statamic/         # Statamic-specific configs

├── content/
│   ├── assets/           # Asset containers

│   ├── collections/      # Collections and entries

│   ├── globals/          # Global sets

│   ├── navigation/       # Navigations

│   ├── trees/            # Collection and navigation trees

├── database/
├── lang/
├── public/
│   ├── assets/           # Default location for assets

│   ├── ...
├── resources/
│   ├── addons/
│   ├── blueprints/       # Blueprints

│   ├── fieldsets/        # Fieldsets

│   ├── users/            # User roles & groups

│   ├── preferences.yaml  # Default preferences

│   ├── sites.yaml        # Sites config

│   ├── ...
├── routes/
├── storage/
├── tests/
├── users/
├── please                # Statamic's CLI tool

├── ...
</code-snippet>

### Statamic's CLI

- Statamic ships with its own `please` CLI tool, useful for creating tags or fieldtypes, updating search indexes, enabling multi-site and much more.
- You may run `php please` to get the list of available commands. You may use the `--help` option on a command to inspect its required parameters.

### Statamic's Core Concepts

- **Assets:** Files managed by Statamic and made available to your writers and developers with tags and fieldtypes. They can be images, videos, PDFs, or any other type of file.
- **Collections:** Collections are containers that hold groups of related entries. Each entry in a collection can represent a blog post, product, recipe or page.
- **Globals:** Global variables store content that belongs to your whole site, not just a single page or URL. They're available everywhere, in all of your views, all the time.
- **Navigations:** A navigation is a hierarchy of links and text nodes that are used to build navs and menus on the frontend of your site.
- **Taxonomies:** A taxonomy is a system of classifying data around a set of unique characteristics. Think things like tags, categories, etc.
- **Users:** Users are the member accounts to your site or application. What a user can do with their account is up to you. They could have limited or full access to the Control Panel, a login-only area of the front-end, or even something more custom by tapping into Laravel.
- **Blueprints:** Blueprints determine the fields shown in your publish forms. You can configure the field's order, each field's width and group them into sections and tabs. Blueprints are attached to collections, taxonomies, globals, assets, users and even forms, all of which help to determine their content schema.
- **Fieldsets:** Fieldsets are used to store and organize reusable fields. Blueprints can reference fields or entire fieldsets, helping you keep your configurations nice and DRY.

### Templating

- Statamic supports two templating languages:
    - **Antlers** is tightly integrated and simple to learn. Uses the `.antlers.html` file extension.
    - **Laravel Blade** ships with Laravel and is familiar to most Laravel developers. Uses the `.blade.php` file extension.
- When creating views, you should familiarize yourself with the project and determine which templating language is already in use.
- When using Laravel Blade, you may want to use the "Antlers Blade Components" feature which lets you use a Blade-component-esque syntax with Statamic's tags feature:

<code-snippet name="Antlers Blade Components example" lang="blade">
    <s:collection from="pages" limit="2" sort="title:desc">
        {{ $title }}
    </s:collection>
</code-snippet>

### Control Panel

- The Control Panel is the primary way to create and manage content.
- Unless disabled or overridden, the Control Panel is usually accessible from `https://your-website.com/cp`.
- Super users can do and see everything, while non-super users can only do & see what their roles allow for.

### Extending Statamic

- You can either extend Statamic in the context of an application, or in the context of an addon.
- Addons are Composer packages, meaning they can be reused, distributed, or even sold to others later.
- There are a variety of ways you can extend Statamic: creating tags, fieldtypes, modifiers, etc. A lot of these things can be bootstrapped with `php please make:` commands.

#### Extending the Control Panel

- The Control Panel is built with Inertia.js and Vue 3.
- When running the `make:fieldtype` or `make:widget` commands, Statamic will install the necessary npm packages and configure Vite.
- Running `setup-cp-vite` in an application context will also do this for you. You'll be able to use `npm run cp:dev` and `npm run cp:build` to run Vite.
- You should use Statamic's UI Components where possible. It includes components for buttons, cards, inputs, etc.
    - UI Components can be imported from `@statamic/cms/ui`.
    - For more information on Statamic's UI Components, please visit our Storybook docs: https://ui.statamic.dev

### Additional Context

- Statamic Documentation: https://statamic.dev/llms.txt
- GitHub Issues: https://github.com/statamic/cms/issues

</laravel-boost-guidelines>
