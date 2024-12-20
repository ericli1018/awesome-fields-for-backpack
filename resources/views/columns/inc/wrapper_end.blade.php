@php
    // this is made available by columns like select and select_multiple
    $related_key = $related_key ?? null;
    $current_value = $current_value ?? null;

    // define the wrapper element
    $wrapperElement = $column['wrapper']['element'] ?? 'a';
    if(!is_string($wrapperElement) && $wrapperElement instanceof \Closure) {
        if ($current_value) 
        {
            $wrapperElement = $wrapperElement($crud, $column, $entry, $related_key, $current_value);
        }
        else
        {
            $wrapperElement = $wrapperElement($crud, $column, $entry, $related_key);
        }
    }
@endphp

</{{ $wrapperElement }}>
