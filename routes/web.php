<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('home', [
        'featured' => Product::inRandomOrder()->take(6)->get([
            'id',
            'name',
            'brand',
            'price',
            'url',
            'category'
        ])->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'brand' => $p->brand,
            'category' => $p->category,
            'url' => $p->url,
            'price' => (float) $p->price,
        ]),
    ]);
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get(
        '/chat',
        fn() => Inertia::render('chat/index')
    )->name('chat.index');
});



require __DIR__ . '/settings.php';
