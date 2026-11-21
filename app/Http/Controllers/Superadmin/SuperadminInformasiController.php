<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Informasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Pengelolaan berita "Informasi Terkini" pada halaman utama.
 *
 * Sebelumnya fitur ini berada di panel Admin PPA & Admin Dinsos. Sekarang
 * seluruh konfigurasi konten publik dipusatkan di halaman Settings Superadmin,
 * sehingga tidak ada lagi pengelolaan ganda di tiap instansi.
 */
class SuperadminInformasiController extends Controller
{
    /** Folder gambar berita (relatif terhadap public/). */
    private const UPLOAD_DIR = 'uploads/informasi';

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());

        if ($validator->fails()) {
            return $this->backToSettings()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $data = [
            'judul'     => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
        ];

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $this->storeImage($request, $validated['judul']);
        }

        Informasi::create($data);

        if (isset($data['gambar'])) {
            ActivityLog::record(
                'dokumen',
                "Mengunggah gambar informasi \"{$data['judul']}\" ({$data['gambar']}).",
                'informasi',
                $data['gambar']
            );
        }

        ActivityLog::record('data', "Menambahkan informasi publik \"{$data['judul']}\".", 'informasi');

        return $this->backToSettings()->with('success', 'Informasi berhasil ditambahkan.');
    }

    public function update(Request $request, Informasi $informasi)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());

        if ($validator->fails()) {
            return $this->backToSettings()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $data = [
            'judul'     => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
        ];

        // Hapus gambar lama bila diganti atau dicentang untuk dihapus.
        if ($request->hasFile('gambar') || $request->boolean('hapus_gambar')) {
            $this->deleteImage($informasi->gambar);
            $data['gambar'] = null;
        }

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $this->storeImage($request, $validated['judul']);
        }

        $informasi->update($data);

        if (isset($data['gambar'])) {
            ActivityLog::record(
                'dokumen',
                "Mengunggah gambar informasi \"{$data['judul']}\" ({$data['gambar']}).",
                'informasi',
                $data['gambar']
            );
        }

        ActivityLog::record('data', "Memperbarui informasi publik \"{$data['judul']}\".", 'informasi');

        return $this->backToSettings()->with('success', 'Informasi berhasil diperbarui.');
    }

    public function destroy(Informasi $informasi)
    {
        $judul = $informasi->judul;

        $this->deleteImage($informasi->gambar);
        $informasi->delete();

        ActivityLog::record('data', "Menghapus informasi publik \"{$judul}\".", 'informasi');

        return $this->backToSettings()->with('success', 'Informasi berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'judul'        => ['required', 'string', 'max:100'],
            'deskripsi'    => ['required', 'string'],
            'gambar'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'hapus_gambar' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'gambar.image' => 'File yang dipilih harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus JPG, JPEG, PNG, GIF, atau WEBP.',
            'gambar.max'   => 'Ukuran gambar pendukung maksimal 2 MB.',
        ];
    }

    /**
     * Kembali ke halaman Settings dan langsung menuju kartu kelola berita.
     */
    private function backToSettings()
    {
        return redirect()->to(route('superadmin.settings.index') . '#kelola-berita');
    }

    private function storeImage(Request $request, string $judul): string
    {
        $directory = public_path(self::UPLOAD_DIR);

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file = $request->file('gambar');
        $fileName = Str::slug($judul) . '-' . time() . '-' . Str::lower(Str::random(6))
            . '.' . $file->getClientOriginalExtension();

        $file->move($directory, $fileName);

        return $fileName;
    }

    private function deleteImage(?string $fileName): void
    {
        if (! $fileName) {
            return;
        }

        $path = public_path(self::UPLOAD_DIR . '/' . $fileName);

        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
