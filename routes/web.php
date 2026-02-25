<?php

use App\Exports\ProductTemplateExport;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

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

Route::get('/products-template', function () {
    return Excel::download(
        new ProductTemplateExport,
        'products-template.csv'
    );
})->name('products.template');