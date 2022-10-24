<?php

namespace Tests;

use Hotmeteor\Inertia\ServiceProvider;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\ServiceProvider as InertiaServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Statamic\Entries\Entry;
use Statamic\Facades\Collection;
use Statamic\Facades\User;
use Statamic\Providers\StatamicServiceProvider;
use Statamic\Stache\Stache;
use Statamic\Statamic;

class TestCase extends OrchestraTestCase
{
    use FakesViews;
    use WithFaker;

    protected function getPackageProviders($app)
    {
        return [
            StatamicServiceProvider::class,
            InertiaServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return ['Statamic' => Statamic::class];
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $configs = [
            'assets', 'cp', 'forms', 'routes', 'static_caching',
            'sites', 'stache', 'system', 'users',
        ];

        foreach ($configs as $config) {
            $app['config']->set("statamic.$config", require(__DIR__."/../vendor/statamic/cms/config/{$config}.php"));
        }

        $app['config']->set('statamic.users.repository', 'file');
        $app['config']->set('statamic.stache', require(__DIR__.'/__fixtures__/config/statamic/stache.php'));
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('statamic.sites', [
            'default' => 'en',
            'sites' => [
                'en' => ['name' => 'English', 'locale' => 'en_US', 'url' => 'http://localhost/'],
            ],
        ]);

        $app['config']->set('auth.providers.users.driver', 'statamic');
        $app['config']->set('statamic.stache.watcher', false);
        $app['config']->set('statamic.users.repository', 'file');
        $app['config']->set('statamic.stache.stores.users', [
            'class' => \Statamic\Stache\Stores\UsersStore::class,
            'directory' => __DIR__.'/__fixtures__/users',
        ]);

        $app['config']->set('statamic.stache.stores.taxonomies.directory', __DIR__.'/__fixtures__/content/taxonomies');
        $app['config']->set('statamic.stache.stores.terms.directory', __DIR__.'/__fixtures__/content/taxonomies');
        $app['config']->set('statamic.stache.stores.collections.directory', __DIR__.'/__fixtures__/content/collections');
        $app['config']->set('statamic.stache.stores.entries.directory', __DIR__.'/__fixtures__/content/collections');
        $app['config']->set('statamic.stache.stores.navigation.directory', __DIR__.'/__fixtures__/content/navigation');
        $app['config']->set('statamic.stache.stores.globals.directory', __DIR__.'/__fixtures__/content/globals');
        $app['config']->set('statamic.stache.stores.asset-containers.directory', __DIR__.'/__fixtures__/content/assets');
        $app['config']->set('statamic.stache.stores.nav-trees.directory', __DIR__.'/__fixtures__/content/structures/navigation');
        $app['config']->set('statamic.stache.stores.collection-trees.directory', __DIR__.'/__fixtures__/content/structures/collections');

        $app['config']->set('cache.stores.outpost', [
            'driver' => 'file',
            'path' => storage_path('framework/cache/outpost-data'),
        ]);

        $app['config']->set('view.paths', [
            __DIR__.'/__fixtures__/resources/views',
        ]);

        $app['config']->set('inertia.testing.page_paths', [
            __DIR__.'/__fixtures__/resources/js/Pages',
        ]);
    }

    protected function makeUser()
    {
        return User::make()
            ->id((new Stache())->generateId())
            ->email($this->faker->email)
            ->save();
    }

    protected function makeCollection(string $handle, string $name, string $template = 'default')
    {
        Collection::make($handle)
            ->title($name)
            ->template($template)
            ->pastDateBehavior('public')
            ->futureDateBehavior('private')
            ->routes('{slug}')
            ->save();

        return Collection::findByHandle($handle);
    }

    protected function makeEntry(string $collection, array $data)
    {
        $slug = 'about';

        Entry::make()
            ->id($slug)
            ->collection($collection)
            ->published(true)
            ->slug($slug)
            ->data($data)
            ->set('updated_by', User::all()->first()->id())
            ->set('updated_at', now()->timestamp)
            ->save();

        return Entry::findBySlug($slug, $collection);
    }

    protected function tearDown(): void
    {
        foreach (['content', 'users'] as $directory) {
            $path = __DIR__.'/__fixtures__/'.$directory;

            app('files')->deleteDirectory($path);

            mkdir($path);
            touch($path.'/.gitkeep');
        }

        parent::tearDown();
    }
}
