<?php

namespace Tests\Feature;

use Tests\TestCase;

class HandleAppearanceTest extends TestCase
{
    public function test_unknown_appearance_cookie_falls_back_to_system_without_reaching_the_inline_script()
    {
        $response = $this->withUnencryptedCookie('appearance', "dark';alert(1);//")->get(route('login'));

        $response->assertOk();
        $response->assertSeeHtml("const appearance = 'system';");
        $response->assertDontSeeHtml('alert(1)');
    }

    public function test_dark_appearance_cookie_renders_the_dark_page()
    {
        $response = $this->withUnencryptedCookie('appearance', 'dark')->get(route('login'));

        $response->assertOk();
        $response->assertSeeHtml('<html lang="en" class="dark">');
        $response->assertSeeHtml("const appearance = 'dark';");
    }
}
