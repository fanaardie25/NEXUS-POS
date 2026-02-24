<div style="display: flex; flex-wrap: wrap; gap: 20px;">
    @foreach($records as $record)
        @if ($record->barcode)
            <div style="text-align: center; width: 180px; border: 1px solid #eee; padding: 10px;">
            <p style="font-size: 10px;">{{ $record->name }}</p>
            
            @php
                $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                $barcode = base64_encode($generator->getBarcode($record->barcode, $generator::TYPE_CODE_128));
            @endphp

            <img src="data:image/png;base64,{{ $barcode }}" style="width: 100%;">
            <p style="font-size: 9px;">{{ $record->barcode }}</p>
        </div>
        @endif
    @endforeach
</div>

<script>
    window.onload = function() { window.print(); }
</script>