<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_SERVER['APP_LOCALES']);

        parent::tearDown();
    }

    public function test_app_locales_limits_the_supported_locales_and_always_keeps_the_default()
    {
        $this->assertSame(['en', 'ja'], array_keys($this->localeConfig()['supported']));

        $_SERVER['APP_LOCALES'] = 'en';
        $this->assertSame(['en'], array_keys($this->localeConfig()['supported']));

        $_SERVER['APP_LOCALES'] = 'ja';
        $this->assertSame(['en', 'ja'], array_keys($this->localeConfig()['supported']));
    }

    public function test_the_browser_language_is_used_when_it_is_supported()
    {
        $this->withHeaders(['Accept-Language' => 'ja'])
            ->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('currentLocale', 'ja')
                ->has('supportedLocales', 2));
    }

    public function test_a_single_supported_locale_ignores_the_browser_cookie_and_switch_request()
    {
        config(['locale.supported' => ['en' => ['name' => 'English', 'native' => 'English']]]);

        $this->withHeaders(['Accept-Language' => 'ja'])
            ->withUnencryptedCookie('locale', 'ja')
            ->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('currentLocale', 'en')
                ->has('supportedLocales', 1));

        $this->put(route('locale.update'), ['locale' => 'ja'])
            ->assertSessionHasErrors('locale');
    }

    /**
     * @return array<string, mixed>
     */
    private function localeConfig(): array
    {
        return require config_path('locale.php');
    }
}
