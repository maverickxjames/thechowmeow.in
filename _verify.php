<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Menu;
\Illuminate\Support\Facades\Cache::forget('menu.header');

$menu = Menu::active()->byLocation('header')
    ->with(['items.linkable', 'items.children.linkable', 'items.children.children.linkable'])
    ->first();

foreach ($menu->items as $item) {
    $c = $item->children->count();
    echo "{$item->title} ({$c} children)" . ($c ? ' ▼' : '') . "\n";
    foreach ($item->children as $child) {
        echo "  ├─ {$child->title}\n";
    }
}
