<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Akun;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SystemSetting;
use App\Models\Laporan;
use Illuminate\Support\Facades\Hash;

echo "--- RUNNING SUPERADMIN & MULTI-ADMIN VERIFICATION ---\n";

// Run seeder to ensure 3 admin accounts exist
(new Database\Seeders\AdminInstansiSeeder())->run();
echo "[SEED] AdminInstansiSeeder executed successfully\n";

// 1. Akun Superadmin check
$superadmin = Akun::where('email', 'superadmin@sentra.com')->first();
assert($superadmin !== null, "Superadmin account not found");
assert($superadmin->isSuperAdmin() === true, "isSuperAdmin() should be true");
assert($superadmin->hasRole('superadmin') === true, "hasRole('superadmin') should be true");
assert($superadmin->hasRole(['admin', 'superadmin']) === true, "hasRole(['admin', 'superadmin']) should be true");
assert(Hash::check('Superadmin123!', $superadmin->password), "Password hash should match");
echo "[PASS] 1. Akun Superadmin & password check\n";

// 2. Akun Admin PPA check
$adminPPA = Akun::where('email', 'admin.ppa@sentra.com')->first();
assert($adminPPA !== null, "Admin PPA account not found");
assert($adminPPA->isAdminPPA() === true, "isAdminPPA() should be true");
assert($adminPPA->isSuperAdmin() === false, "Admin PPA should not be superadmin");
assert(Hash::check('AdminPPA123!', $adminPPA->password), "Admin PPA password hash should match");
echo "[PASS] 2. Akun Admin PPA check\n";

// 3. Akun Admin Dinsos check
$adminDinsos = Akun::where('email', 'admin.dinsos@sentra.com')->first();
assert($adminDinsos !== null, "Admin Dinsos account not found");
assert($adminDinsos->isAdminDinsos() === true, "isAdminDinsos() should be true");
assert($adminDinsos->isSuperAdmin() === false, "Admin Dinsos should not be superadmin");
assert(Hash::check('Dinsos123!', $adminDinsos->password), "Admin Dinsos password hash should match");
echo "[PASS] 3. Akun Admin Dinsos check\n";

// 4. Verification total 3 admin accounts
$adminCount = Akun::whereIn('role', ['superadmin', 'admin_ppa', 'admin_dinsos', 'admin'])->count();
assert($adminCount >= 3, "Total admin accounts should be at least 3");
echo "[PASS] 4. Total 3 admin accounts verified (Count: $adminCount)\n";

// 5. Scoping Laporan check
$querySuperadmin = Laporan::forUser($superadmin)->toSql();
$queryPPA = Laporan::forUser($adminPPA)->toSql();
$queryDinsos = Laporan::forUser($adminDinsos)->toSql();

assert(!str_contains($querySuperadmin, '`kategori` in'), "Superadmin query must not constrain categories");
assert(str_contains($queryPPA, '`kategori` in'), "Admin PPA query must constrain categories");
assert(str_contains($queryDinsos, '`kategori` in') || str_contains($queryDinsos, '`status` ='), "Admin Dinsos query must constrain categories / status");
echo "[PASS] 5. Laporan::forUser() scoping checks passed\n";

// 6. Phone number encryption check
assert(!empty($superadmin->notelp), "Decrypted notelp should not be empty");
$rawNotelp = DB::table('akun')->where('email', 'superadmin@sentra.com')->value('notelp');
assert($rawNotelp !== $superadmin->notelp, "Raw notelp in DB must be encrypted string");
echo "[PASS] 6. Phone number encryption check\n";

// 7. SystemSetting check
$appName = SystemSetting::get('app_name');
assert($appName === 'SENTRA', "SystemSetting app_name should be SENTRA");
SystemSetting::set('test_key', 'test_val');
assert(SystemSetting::get('test_key') === 'test_val', "SystemSetting set/get cache check");
SystemSetting::where('key', 'test_key')->delete();
echo "[PASS] 7. SystemSetting getter, setter, and cache checks\n";

// 8. Superadmin Dedicated Layout & Routes check
$routes = [
    'superadmin.dashboard',
    'superadmin.akun.index',
    'superadmin.akun.create',
    'superadmin.roles.index',
    'superadmin.settings.index',
    'superadmin.laporan.index',
];
foreach ($routes as $r) {
    assert(Route::has($r), "Route {$r} must exist");
}
echo "[PASS] 8. Superadmin routes verified\n";

echo "--- ALL CHECKS PASSED SUCCESSFULLY! ---\n";
