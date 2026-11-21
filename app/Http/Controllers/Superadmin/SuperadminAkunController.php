<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Akun;
use App\Models\ChatMobile;
use App\Models\Laporan;
use App\Models\Role;
use App\Support\ActivitySearchParser;
use App\Support\CreatedAtSearch;
use App\Support\SearchHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SuperadminAkunController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q'             => ['nullable', 'string', 'max:100'],
            'role'          => ['nullable', 'string', 'max:30'],
            'status'        => ['nullable', 'in:aktif,nonaktif'],
            'jenis_kelamin' => ['nullable', 'in:Pria,Wanita'],
            'instansi'      => ['nullable', 'string', 'max:30'],
            'dari'          => ['nullable', 'date'],
            'sampai'        => ['nullable', 'date'],
            'sort'          => ['nullable', 'in:terbaru,terlama,nama_az,nama_za'],
            'per_page'      => ['nullable', 'integer', 'in:15,25,50,100'],
        ]);

        // Jika rentang terbalik, tukar agar hasil tetap masuk akal.
        if (! empty($filters['dari']) && ! empty($filters['sampai'])
            && strtotime((string) $filters['dari']) > strtotime((string) $filters['sampai'])) {
            [$filters['dari'], $filters['sampai']] = [$filters['sampai'], $filters['dari']];
        }

        $query = Akun::query();

        $this->applyFilters($query, $filters);

        // Pencarian telepon harus dilakukan setelah filter SQL lain agar
        // jumlah kandidat yang didekripsi tetap kecil.
        $keyword = trim((string) ($filters['q'] ?? ''));
        if ($keyword !== '') {
            $phoneMatchedIds = SearchHelper::looksLikePhoneSearch($keyword)
                ? $this->phoneMatchedIds(clone $query, $keyword)
                : [];

            $this->applyKeyword($query, $keyword, $phoneMatchedIds);
        }

        $this->applySorting($query, $filters['sort'] ?? 'terbaru');

        $perPage = (int) ($filters['per_page'] ?? 15);
        $akunList = $query->paginate($perPage)->withQueryString();

        $options = $this->filterOptions();

        return view('backend.superadmin.akun.index', array_merge(
            compact('akunList', 'filters'),
            $options
        ));
    }

    public function create()
    {
        $roles = Role::all();
        return view('backend.superadmin.akun.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:akun,email',
            'notelp' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'role' => 'required|string|max:30|exists:roles,name',
            'jenis_kelamin' => 'nullable|in:Pria,Wanita',
            'alamat' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        // Checkbox tidak mengirim nilai saat tidak dicentang, jadi form
        // menyertakan hidden input value="0". Jangan beri default true.
        $validated['is_active'] = $request->boolean('is_active');

        $akun = Akun::create($validated);

        ActivityLog::record('data', "Membuat akun baru '{$akun->nama}' ({$akun->email}) dengan role {$akun->role}.", 'akun', "akun-{$akun->id_akun}");

        return redirect()->route('superadmin.akun.index')
            ->with('success', 'Akun berhasil dibuat.');
    }

    public function edit($id)
    {
        $akun = Akun::findOrFail($id);
        $roles = Role::all();
        return view('backend.superadmin.akun.edit', compact('akun', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $akun = Akun::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'email' => ['required', 'email', 'max:100', Rule::unique('akun', 'email')->ignore($akun->id_akun, 'id_akun')],
            'notelp' => 'required|string|max:20',
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|max:30|exists:roles,name',
            'jenis_kelamin' => 'nullable|in:Pria,Wanita',
            'alamat' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->boolean('is_active');

        // Proteksi: jangan sampai tidak ada superadmin aktif
        if ($akun->role === 'superadmin' && ($validated['role'] !== 'superadmin' || !$isActive)) {
            $totalSuperadmin = Akun::where('role', 'superadmin')
                ->where(function ($q) {
                    $q->where('is_active', true)->orWhereNull('is_active');
                })
                ->count();

            if ($totalSuperadmin <= 1) {
                return back()->with('error', 'Gagal: Sistem harus memiliki setidaknya satu Superadmin aktif.');
            }
        }

        $akun->nama = $validated['nama'];
        $akun->email = $validated['email'];
        $akun->notelp = $validated['notelp'];
        $akun->role = $validated['role'];
        $akun->jenis_kelamin = $validated['jenis_kelamin'] ?? null;
        $akun->alamat = $validated['alamat'] ?? null;
        $akun->is_active = $isActive;

        $oldRole = $akun->getOriginal('role');
        $oldActive = $akun->getOriginal('is_active');
        $passwordChanged = ! empty($validated['password']);

        if (!empty($validated['password'])) {
            $akun->password = $validated['password']; // Mutator akan hash otomatis
        }

        $akun->save();

        // Token lama tidak boleh tetap berlaku setelah role berubah,
        // akun dinonaktifkan, atau password diganti.
        $deactivated = $oldActive !== false && $akun->is_active === false;

        if ($oldRole !== $akun->role || $deactivated || $passwordChanged) {
            $akun->revokeAccessTokens();
        }

        ActivityLog::record('data', "Memperbarui data akun '{$akun->nama}' ({$akun->email}, role {$akun->role}).", 'akun', "akun-{$akun->id_akun}");

        return redirect()->route('superadmin.akun.index')
            ->with('success', 'Data akun berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $akun = Akun::findOrFail($id);

        // Proteksi 1: Tidak boleh menghapus akun sendiri
        if (Auth::id() == $akun->id_akun) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Proteksi 2: Tidak boleh menghapus satu-satunya superadmin
        if ($akun->role === 'superadmin') {
            $totalSuperadmin = Akun::where('role', 'superadmin')->count();
            if ($totalSuperadmin <= 1) {
                return back()->with('error', 'Superadmin terakhir tidak dapat dihapus.');
            }
        }

        // Proteksi 3: Cegah kehilangan data laporan/percakapan akibat cascade.
        $laporanCount = Laporan::where('id_akun', $akun->id_akun)->count();
        $chatCount = ChatMobile::where('sender_id', $akun->id_akun)
            ->orWhere('receiver_id', $akun->id_akun)
            ->count();

        if ($laporanCount > 0 || $chatCount > 0) {
            return back()->with(
                'error',
                "Akun tidak dapat dihapus karena masih terhubung dengan {$laporanCount} laporan dan {$chatCount} percakapan. Nonaktifkan akun atau hapus data terkait terlebih dahulu."
            );
        }

        $nama = $akun->nama;
        $email = $akun->email;
        $role = $akun->role;
        $akunId = $akun->id_akun;

        $akun->revokeAccessTokens();
        $akun->delete();

        ActivityLog::record('data', "Menghapus akun '{$nama}' ({$email}, role {$role}).", 'akun', "akun-{$akunId}");

        return redirect()->route('superadmin.akun.index')
            ->with('success', 'Akun berhasil dihapus.');
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $akun = Akun::findOrFail($id);
        $akun->password = $request->new_password; // Mutator otomatis hash
        $akun->save();
        $akun->revokeAccessTokens();

        ActivityLog::record('data', "Me-reset password akun '{$akun->nama}' ({$akun->email}).", 'akun', "akun-{$akun->id_akun}");

        return back()->with('success', "Password untuk akun {$akun->nama} berhasil direset.");
    }

    public function toggleStatus($id)
    {
        $akun = Akun::findOrFail($id);

        if (Auth::id() == $akun->id_akun) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        // null dianggap aktif agar konsisten dengan badge di tampilan.
        $currentlyActive = $akun->is_active !== false;

        if ($akun->role === 'superadmin' && $currentlyActive) {
            $totalSuperadmin = Akun::where('role', 'superadmin')
                ->where(function ($q) {
                    $q->where('is_active', true)->orWhereNull('is_active');
                })
                ->count();

            if ($totalSuperadmin <= 1) {
                return back()->with('error', 'Tidak dapat menonaktifkan Superadmin aktif terakhir.');
            }
        }

        $akun->is_active = !$currentlyActive;
        $akun->save();

        if ($akun->is_active === false) {
            $akun->revokeAccessTokens();
        }

        $statusText = $akun->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::record('data', "Akun '{$akun->nama}' ({$akun->email}) {$statusText}.", 'akun', "akun-{$akun->id_akun}");

        return back()->with('success', "Akun {$akun->nama} berhasil {$statusText}.");
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['role'])) {
            $role = (string) $filters['role'];

            // Role legacy 'admin' masih dipakai sebagian akun lama.
            $roleKeys = match ($role) {
                'admin_ppa' => ['admin_ppa', 'admin'],
                'guest'     => ['guest', 'Pelapor Cepat'],
                default     => [$role],
            };

            $query->whereIn('role', $roleKeys);
        }

        if (! empty($filters['status'])) {
            if ($filters['status'] === 'aktif') {
                $query->where(function ($q) {
                    $q->where('is_active', true)->orWhereNull('is_active');
                });
            } else {
                $query->where('is_active', false);
            }
        }

        if (! empty($filters['jenis_kelamin'])) {
            $query->where('jenis_kelamin', $filters['jenis_kelamin']);
        }

        if (! empty($filters['instansi'])) {
            $instansi = (string) $filters['instansi'];

            if ($instansi === 'lainnya') {
                $knownRoles = array_merge(
                    Akun::roleKeysForInstansi('superadmin'),
                    Akun::roleKeysForInstansi('uptd_ppa'),
                    Akun::roleKeysForInstansi('dinas_sosial'),
                    Akun::roleKeysForInstansi('masyarakat'),
                );

                $query->whereNotIn('role', array_values(array_unique($knownRoles)));
            } else {
                $keys = Akun::roleKeysForInstansi($instansi);

                if ($keys !== []) {
                    $query->whereIn('role', $keys);
                }
            }
        }

        if (! empty($filters['dari'])) {
            $query->whereDate('created_at', '>=', $filters['dari']);
        }

        if (! empty($filters['sampai'])) {
            $query->whereDate('created_at', '<=', $filters['sampai']);
        }
    }

    /**
     * @param  array<int, int>  $phoneMatchedIds
     */
    private function applyKeyword(Builder $query, string $keyword, array $phoneMatchedIds): void
    {
        $like = '%' . SearchHelper::escapeLike($keyword) . '%';
        $roleKeys = Akun::roleKeysForKeyword($keyword);
        $instansiKeys = $this->instansiKeysForKeyword($keyword);
        $isNumeric = ctype_digit($keyword);
        $statusConditions = $this->statusConditionsForKeyword($keyword);
        $parsed = ActivitySearchParser::parse($keyword);

        $months = [];
        $days = [];
        $year = null;
        $date = null;
        $time = null;

        switch ($parsed['type']) {
            case 'date':
                $date = $parsed['date'];
                $time = $parsed['time'] ?? null;
                break;

            case 'month':
                $months = [(int) $parsed['month']];
                $year = $parsed['year'] ?? null;
                $time = $parsed['time'] ?? null;
                break;

            case 'month_partial':
                $months = array_map('intval', $parsed['months']);
                $year = $parsed['year'] ?? null;
                $time = $parsed['time'] ?? null;
                break;

            case 'numeric':
                $value = (int) $parsed['value'];

                if ($value >= 1900 && $value <= 2100) {
                    $year = $value;
                } else {
                    $days = ActivitySearchParser::dayNumbersForKeyword($keyword);
                }
                break;

            case 'time':
                $time = $parsed['time'];
                break;

            case 'text':
                $months = ActivitySearchParser::monthNumbersForKeyword($keyword);
                break;
        }

        $query->where(function (Builder $sub) use (
            $like,
            $keyword,
            $isNumeric,
            $roleKeys,
            $instansiKeys,
            $phoneMatchedIds,
            $statusConditions,
            $months,
            $days,
            $year,
            $date,
            $time
        ) {
            $sub->where('nama', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('alamat', 'like', $like)
                ->orWhere('role', 'like', $like)
                ->orWhere('kode_akun', 'like', $like)
                ->orWhere('jenis_kelamin', 'like', $like);

            if ($isNumeric) {
                $sub->orWhere('id_akun', (int) $keyword)
                    ->orWhereRaw('CAST(id_akun AS CHAR) LIKE ?', [$like]);
            }

            if ($roleKeys !== []) {
                $sub->orWhereIn('role', $roleKeys);
            }

            if ($instansiKeys !== []) {
                $sub->orWhereIn('role', $instansiKeys);
            }

            if ($phoneMatchedIds !== []) {
                $sub->orWhereIn('id_akun', $phoneMatchedIds);
            }

            if ($statusConditions !== []) {
                $sub->orWhere(function (Builder $status) use ($statusConditions) {
                    foreach ($statusConditions as $index => $condition) {
                        if ($condition === 'aktif') {
                            $active = function (Builder $activeQuery) {
                                $activeQuery->where('is_active', true)->orWhereNull('is_active');
                            };

                            if ($index === 0) {
                                $status->where($active);
                            } else {
                                $status->orWhere($active);
                            }

                            continue;
                        }

                        if ($index === 0) {
                            $status->where('is_active', false);
                        } else {
                            $status->orWhere('is_active', false);
                        }
                    }
                });
            }

            if ($date !== null) {
                $sub->orWhere(function (Builder $dateQuery) use ($date, $time) {
                    CreatedAtSearch::applyDate($dateQuery, $date, $time);
                });
            } elseif ($months !== []) {
                $sub->orWhere(function (Builder $monthQuery) use ($months, $year, $time) {
                    CreatedAtSearch::applyMonths($monthQuery, $months, $year, $time);
                });
            } elseif ($year !== null) {
                $sub->orWhere(function (Builder $yearQuery) use ($year) {
                    CreatedAtSearch::applyYear($yearQuery, $year);
                });
            }

            if ($days !== []) {
                $sub->orWhere(function (Builder $dayQuery) use ($days) {
                    CreatedAtSearch::applyDays($dayQuery, $days);
                });
            }

            if ($time !== null && $date === null && $months === []) {
                $sub->orWhere(function (Builder $timeQuery) use ($time) {
                    CreatedAtSearch::applyTime($timeQuery, $time);
                });
            }
        });
    }

    /**
     * @return array<int, int>
     */
    private function phoneMatchedIds(Builder $query, string $keyword): array
    {
        $variants = SearchHelper::phoneSearchVariants($keyword);

        if ($variants === []) {
            return [];
        }

        $ids = [];

        $query->select(['id_akun', 'notelp'])
            ->chunkById(500, function ($rows) use ($variants, &$ids) {
                foreach ($rows as $row) {
                    try {
                        $phone = $row->notelp; // accessor mendekripsi otomatis
                    } catch (\Throwable $e) {
                        continue;
                    }

                    if (SearchHelper::phoneMatches($phone, $variants)) {
                        $ids[] = (int) $row->id_akun;
                    }
                }
            }, 'id_akun');

        return $ids;
    }

    private function applySorting(Builder $query, string $sort): void
    {
        match ($sort) {
            'terlama' => $query->orderBy('created_at', 'asc')->orderBy('id_akun', 'asc'),
            'nama_az' => $query->orderBy('nama', 'asc')->orderBy('id_akun', 'asc'),
            'nama_za' => $query->orderBy('nama', 'desc')->orderBy('id_akun', 'asc'),
            default   => $query->orderBy('created_at', 'desc')->orderBy('id_akun', 'desc'),
        };
    }

    /**
     * @return array{roleList: array<string, string>, instansiList: array<string, string>, statusList: array<string, string>, genderList: array<string, string>}
     */
    private function filterOptions(): array
    {
        $roleList = Akun::ROLE_LABELS;

        // 'admin' dan 'Pelapor Cepat' adalah alias legacy yang sudah
        // diwakili oleh opsi admin_ppa dan guest pada filter.
        unset($roleList['admin'], $roleList['Pelapor Cepat']);

        foreach (Role::query()->orderBy('display_name')->get() as $role) {
            $roleList[$role->name] = $role->display_name;
        }

        // Sertakan role legacy yang masih dipakai di tabel akun.
        $usedRoles = Akun::query()
            ->whereNotNull('role')
            ->distinct()
            ->pluck('role');

        foreach ($usedRoles as $role) {
            if (in_array($role, ['admin', 'Pelapor Cepat'], true)) {
                continue;
            }

            if (! isset($roleList[$role])) {
                $roleList[$role] = ucfirst(str_replace('_', ' ', $role));
            }
        }

        asort($roleList);

        $instansiList = Akun::INSTANSI_OPTIONS;

        $statusList = [
            'aktif'    => 'Aktif',
            'nonaktif' => 'Nonaktif',
        ];

        $genderList = [
            'Pria'   => 'Pria',
            'Wanita' => 'Wanita',
        ];

        return compact('roleList', 'instansiList', 'statusList', 'genderList');
    }

    /**
     * @return array<int, string>
     */
    private function instansiKeysForKeyword(string $keyword): array
    {
        $needle = mb_strtolower($keyword);
        $keys = [];

        if (str_contains('superadmin', $needle) || str_contains($needle, 'superadmin')) {
            $keys = array_merge($keys, Akun::roleKeysForInstansi('superadmin'));
        }

        if (str_contains($needle, 'uptd') || str_contains($needle, 'ppa')) {
            $keys = array_merge($keys, Akun::roleKeysForInstansi('uptd_ppa'));
        }

        if (str_contains($needle, 'dinas sosial') || str_contains($needle, 'dinsos')) {
            $keys = array_merge($keys, Akun::roleKeysForInstansi('dinas_sosial'));
        }

        if (str_contains($needle, 'masyarakat') || str_contains($needle, 'pelapor')) {
            $keys = array_merge($keys, Akun::roleKeysForInstansi('masyarakat'));
        }

        return array_values(array_unique($keys));
    }

    /**
     * Status yang cocok dengan keyword secara parsial.
     *
     * Awalan diprioritaskan agar "aktif" tidak ikut mencocokkan "nonaktif",
     * sementara "non" tetap bisa menemukan status nonaktif.
     *
     * @return array<int, string>
     */
    private function statusConditionsForKeyword(string $keyword): array
    {
        $needle = mb_strtolower(trim($keyword));

        if ($needle === '') {
            return [];
        }

        $groups = [
            'aktif' => ['aktif', 'active', 'aktip'],
            'nonaktif' => ['nonaktif', 'non aktif', 'non-active', 'inactive', 'tidak aktif'],
        ];

        $prefixMatches = [];
        $substringMatches = [];

        foreach ($groups as $condition => $aliases) {
            foreach ($aliases as $alias) {
                if (str_starts_with($alias, $needle)) {
                    $prefixMatches[$condition] = true;
                } elseif (str_contains($alias, $needle)) {
                    $substringMatches[$condition] = true;
                }
            }
        }

        $matches = $prefixMatches !== [] ? $prefixMatches : $substringMatches;

        return array_keys($matches);
    }
}
