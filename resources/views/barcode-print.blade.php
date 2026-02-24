<div style="text-align: center; width: 200px; padding: 10px; border: 1px solid #ccc;">
    <p style="font-size: 12px; margin-bottom: 5px;">{{ $record->name }}</p>
    
    @php
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($record->barcode, $generator::TYPE_CODE_128));
    @endphp

    <img src="data:image/png;base64,{{ $barcode }}" style="width: 100%;">
    
    <p style="font-size: 10px; margin-top: 5px;">{{ $record->barcode }}</p>
</div>

<script>
    window.print(); // Biar pas dibuka langsung muncul dialog print
</script>