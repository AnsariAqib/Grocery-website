<?php

/*
|------------------------------------------------------------------------------
| TEMPORARY DIAGNOSTIC — delete this file once the upload issue is fixed.
|------------------------------------------------------------------------------
| Reproduces exactly what Livewire's /livewire/upload-file endpoint does when it
| parks a file in the temporary upload directory, and reports the surrounding
| environment. Open it in a browser and read the output top to bottom.
*/

header('Content-Type: text/plain; charset=utf-8');

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;

function line(string $label, $value): void
{
    echo str_pad($label, 34).': '.(is_bool($value) ? ($value ? 'yes' : 'NO') : $value)."\n";
}

echo "=== PHP ===\n";
line('version', PHP_VERSION);
line('sapi', PHP_SAPI);
line('gd loaded', extension_loaded('gd'));
line('fileinfo loaded', extension_loaded('fileinfo'));
line('memory_limit', ini_get('memory_limit'));
line('max_execution_time', ini_get('max_execution_time'));
line('post_max_size', ini_get('post_max_size'));
line('upload_max_filesize', ini_get('upload_max_filesize'));
$tmpDir = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();
line('upload tmp dir', $tmpDir);
line('upload tmp dir writable', is_writable($tmpDir));
line('running as user', function_exists('posix_getpwuid') ? (posix_getpwuid(posix_geteuid())['name'] ?? '?') : get_current_user());

echo "\n=== APP ===\n";
line('APP_ENV', config('app.env'));
line('APP_DEBUG', config('app.debug') ? 'true' : 'false');
line('APP_URL', config('app.url'));
line('config cached', file_exists(base_path('bootstrap/cache/config.php')));
line('public/storage symlink', is_link(public_path('storage'))
    ? 'link -> '.readlink(public_path('storage'))
    : (is_dir(public_path('storage')) ? 'plain directory' : 'MISSING'));

echo "\n=== DISKS ===\n";
$default = config('filesystems.default');
$livewireDisk = FileUploadConfiguration::disk();
line('filesystems.default', $default);
line('livewire temp disk (effective)', $livewireDisk);
line('livewire temp directory', FileUploadConfiguration::path());
line('disk driver', config("filesystems.disks.{$livewireDisk}.driver") ?: 'NOT CONFIGURED');

$root = config("filesystems.disks.{$livewireDisk}.root");
line('disk root', $root ?: '(none)');

if ($root) {
    line('root exists', is_dir($root));
    line('root writable', is_dir($root) && is_writable($root));
    line('root owner', is_dir($root) && function_exists('posix_getpwuid')
        ? (posix_getpwuid(fileowner($root))['name'] ?? '?').' / '.substr(sprintf('%o', fileperms($root)), -4)
        : '?');
    $free = @disk_free_space($root);
    line('free space', $free ? round($free / 1024 / 1024).' MB' : '?');
}

echo "\n=== WRITE TEST (what Livewire actually does) ===\n";
$probe = FileUploadConfiguration::path('/').'upload-probe-'.time().'.txt';

try {
    $ok = Storage::disk($livewireDisk)->put($probe, 'probe');
    line('Storage::put returned', $ok ? 'true' : 'FALSE (write refused, throw is off)');

    if ($ok) {
        line('file exists after write', Storage::disk($livewireDisk)->exists($probe));
        Storage::disk($livewireDisk)->delete($probe);
        line('cleaned up', true);
    }
} catch (\Throwable $e) {
    echo "EXCEPTION: ".get_class($e)."\n";
    echo "MESSAGE  : ".$e->getMessage()."\n";
    echo "AT       : ".$e->getFile().':'.$e->getLine()."\n";
}

echo "\n=== RAW FILESYSTEM TEST ===\n";
if ($root) {
    $dir = rtrim($root, '/\\').'/'.FileUploadConfiguration::path();

    if (! is_dir($dir)) {
        line('temp dir exists', false);
        line('mkdir succeeded', @mkdir($dir, 0755, true) ? 'yes' : 'NO — '.(error_get_last()['message'] ?? '?'));
    } else {
        line('temp dir exists', true);
        line('temp dir writable', is_writable($dir));
    }

    if (is_dir($dir)) {
        $file = $dir.'/raw-probe.txt';
        $wrote = @file_put_contents($file, 'probe');
        line('raw write', $wrote !== false ? 'yes' : 'NO — '.(error_get_last()['message'] ?? '?'));
        @unlink($file);
    }
}

echo "\n=== LAST LOGGED ERROR ===\n";
$log = storage_path('logs/laravel.log');
if (is_file($log)) {
    line('log writable', is_writable($log));
    $tail = array_slice(file($log), -40);
    // Only the exception headline lines, so nothing sensitive is dumped here.
    foreach ($tail as $row) {
        if (preg_match('/^\[[\d\-: ]+\].*(ERROR|CRITICAL|EMERGENCY)/i', $row)) {
            echo substr(trim($row), 0, 400)."\n";
        }
    }
} else {
    line('laravel.log', 'MISSING');
}

echo "\nDone.\n";
