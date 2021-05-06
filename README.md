# Inertia.js adapter for Statamic

[Statamic](https://statamic.com) server side adapter for [Inertia.js](https://inertiajs.com) to build single-page apps, without building an API.

[![Latest Stable Version](https://poser.pugx.org/hotmeteor/inertia-statamic/v)](//packagist.org/packages/hotmeteor/inertia-statamic)

## Installation

You can install the package through Composer.

```bash
composer require hotmeteor/inertia-statamic
```

## Usage

### Setup

The Inertia adapter works for any page or entry content available through Statamic Collections.

By default, all Inertia-enabled pages will be expecting an `app` template, which should be located at `resources/views/app.blade.php`. This is the base page that any Inertia app is looking for, and should contain the `@inertia` directive. The template can be defined either at the collection or page level, but it must be `app`.

```blade
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet" />
    <script src="{{ mix('/js/app.js') }}" defer></script>
  </head>
  <body>
    @inertia
  </body>
</html>
```

In your `app.js` file you must set up your Inertia app and reference where your Vue pages will live:
```js
// app.js

import { createApp, h } from 'vue'
import { App, plugin } from '@inertiajs/inertia-vue3'

const el = document.getElementById('app')

createApp({
  render: () => h(App, {
    initialPage: JSON.parse(el.dataset.page),
    resolveComponent: name => require(`./Pages/${name}`).default,
  })
}).use(plugin).mount(el)
```

Finally, you need to create a `Pages` folder in `resources/js`. This is where your app will be looking for Vue components that match the resolved naming of your Statamic pages.

```sh
|_ resources
    |_ js
        |_ Pages
            |_ About
                |_ Team.vue
            |_ Home.vue             
```

Both [server-side setup](https://inertiajs.com/server-side-setup) and [client-side setup](https://inertiajs.com/client-side-setup) full instructions are available on Inertia's website.

### Component Naming

As you can see in the folder structure above, your Vue component naming and location must match the Statamic collection hierarchy + page slug combo for any Inertia-enabled pages. The adapter will automatically build these paths based on the page's URL and slug.

Here are some examples of what this looks like:

Statamic Collection | Statamic Page | Slug | URL | Component Name
------------ | ------------- | ------------- | ------------- | -------------
Home | Home | `home` | / | `Home.vue`
Marketing | Overview | `overview` | /marketing/ | `Marketing/Overview.vue`
Marketing | Logos and Colors | `logos-and-colors` | /marketing/logos | `Marketing/LogosAndColors.vue`

### Props

All the typical data passed to a Statamic page as objects will now be available to your page as `props`. The `props` will contain all of the expected attributes and data. For example, the Inertia response's `props` object could look like:

```php
Inertia\Response {#2587 ▼
  #component: "Marketing/Overview"
  #props: array:22 [▼
    "amp_url" => null
    "api_url" => null
    "collection" => array:3 [▶]
    "content" => array:4 [▶]
    "date" => Illuminate\Support\Carbon @1617827556 {#2478 ▶}
    "edit_url" => "http://mysite.test/cp/collections/marketing/entries/f854a1cf-0dcf-404b-8418-a74662ba77e7/overview"
    "id" => "f854a1cf-0dcf-404b-8418-a74662ba77e7"
    "is_entry" => true
    "last_modified" => Illuminate\Support\Carbon @1617827556 {#2477 ▶}
    "mount" => null
    "order" => null
    "parent" => null
    "permalink" => "http://mysite.test/marketing"
    "private" => false
    "published" => true
    "slug" => "overview"
    "template" => "app"
    "title" => "Overview"
    "updated_at" => Illuminate\Support\Carbon @1617827556 {#2523 ▶}
    "updated_by" => array:4 [▶]
    "uri" => "/marketing"
    "url" => "/marketing"
  ]
  #rootView: "app"
  #version: ""
  #viewData: []
}
```

## Credits

- [Adam Campbell](https://github.com/hotmeteor)
- [Statamic](https://statamic.com)
- [The amazing Inertia.js libraries](https://github.com/inertiajs)
- [All Contributors](../../contributors)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
