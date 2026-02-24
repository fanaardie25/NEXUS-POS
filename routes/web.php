<?php

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::livewire('/cashier', 'pages::cashier.index')->middleware('auth');

Route::get('/print-barcode/{product}', function (Product $product) {
    return view('barcode-print', ['record' => $product]);
})->name('print.barcode');

Route::get('/print-barcodes', function (Request $request) {
    $ids = explode(',', $request->query('ids'));
    $products = Product::whereIn('id', $ids)->get();

    return view('barcode-print-bulk', ['records' => $products]);
})->name('print.barcodes.bulk');