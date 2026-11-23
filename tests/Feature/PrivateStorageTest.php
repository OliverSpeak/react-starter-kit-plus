<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrivateStorageTest extends TestCase
{
    public function test_private_disk_files_have_no_storage_url()
    {
        Storage::fake('local');
        Storage::disk('local')->put('private.txt', 'private');

        $response = $this->get('/storage/private.txt');

        $response->assertNotFound();
    }
}
