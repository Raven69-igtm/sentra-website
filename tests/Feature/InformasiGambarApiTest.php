<?php

namespace Tests\Feature;

use App\Models\Informasi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class InformasiGambarApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_api_informasi_menyediakan_url_gambar_lewat_endpoint(): void
    {
        $namaFile = 'tmp-info-' . uniqid() . '.jpg';
        $path = public_path('uploads/informasi/' . $namaFile);

        File::ensureDirectoryExists(dirname($path));
        // JPEG 1x1 minimum agar Content-Type terdeteksi sebagai image/jpeg.
        File::put(
            $path,
            base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwcJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPDs0NDT/wAALCAABAAEBAREA/8QAFAABAQAAAAAAAAAAAAAAAAAAAAX/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8AmAA//9k=')
        );

        try {
            $informasi = Informasi::create([
                'judul' => 'Informasi uji gambar',
                'deskripsi' => 'Deskripsi uji',
                'gambar' => $namaFile,
            ]);

            $this->getJson('/api/informasi')
                ->assertOk()
                ->assertJsonFragment([
                    'gambar_url' => url('/api/informasi/' . $informasi->id_informasi . '/gambar'),
                ]);

            $this->get('/api/informasi/' . $informasi->id_informasi . '/gambar')
                ->assertOk()
                ->assertHeader('Access-Control-Allow-Origin', '*')
                ->assertHeader('Content-Type', 'image/jpeg');
        } finally {
            File::delete($path);
        }
    }
}
