<?php

namespace Tests;

use Inertia\Testing\AssertableInertia;

class InertiaStatamicTest extends TestCase
{
    public $data;

    public function setUp(): void
    {
        parent::setUp();

        $this->makeUser();

        $this->data = [
            'title' => 'The About Page',
            'content' => 'This is the about page.',
        ];
    }

    /** @test */
    public function uses_default_template()
    {
        $this->withStandardFakeViews();
        $this->viewShouldReturnRaw('default', '<h1>{{ title }}</h1> <p>{{ content }}</p>');

        $collection = $this->makeCollection('pages', 'Pages');
        $entry = $this->makeEntry('pages', $this->data);

        $this->get('/about')
            ->assertStatus(200)
            ->assertSee('<h1>The About Page</h1> <p><p>This is the about page.</p>', false);
    }

    /** @test */
    public function uses_alternative_template()
    {
        $this->withStandardFakeViews();
        $this->viewShouldReturnRaw('other_template', '<h1>{{ title }}</h1> <p>{{ content }}</p>');

        $collection = $this->makeCollection('pages', 'Pages', 'other_template');
        $entry = $this->makeEntry('pages', $this->data);

        $this->get('/about')
            ->assertStatus(200)
            ->assertSee('<h1>The About Page</h1> <p><p>This is the about page.</p>', false);
    }

    /** @test */
    public function uses_inertia_template()
    {
        $collection = $this->makeCollection('pages', 'Pages', 'app');
        $entry = $this->makeEntry('pages', $this->data);

        $this->get('/about')
            ->assertStatus(200)
            ->assertSee('data-page')
            ->assertDontSee('@inertia')
            ->assertInertia(function (AssertableInertia $page) {
                return $page
                    ->component('About')
                    ->where('collection.title', 'Pages')
                    ->where('collection.handle', 'pages')
                    ->where('content', '<p>This is the about page.</p>')
                    ->where('id', 'about')
                    ->where('permalink', 'http://localhost/about')
                    ->where('slug', 'about')
                    ->where('title', 'The About Page')
                    ->where('uri', '/about')
                    ->where('url', '/about');
            });
    }
}
