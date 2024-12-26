{{-- regular object attribute --}}
@php
    $column['split'] = $column['split'] ?? ',';
    $column['value'] = $column['value'] ?? data_get($entry, $column['name']);
    $column['escaped'] = $column['escaped'] ?? true;
    $column['prefix'] = $column['prefix'] ?? '';
    $column['suffix'] = $column['suffix'] ?? '';
    $column['text'] = $column['default'] ?? '-';
    
    if($column['value'] instanceof \Closure) {
        $column['value'] = $column['value']($entry);
    }

    if(!empty($column['value'])) {
        $column['value'] = array_unique(explode($column['split'], $column['value']));
    }
@endphp

<span>
    @if(count($column['value']) < 1)
        @if($column['escaped'])
            {{ $column['text'] }}
        @else
            {!! $column['text'] !!}
        @endif
    @else
        @foreach($column['value'] as $value)
            @includeWhen(!empty($column['wrapper']), 'ericli1018.awesome-fields-for-backpack::columns.inc.wrapper_start', ['current_value' => $value])
                @if(!$loop->first)
                    ,
                @endif
                @if($column['escaped'])
                    {{  $column['prefix'].$value.$column['suffix'] }}
                @else
                    {!!  $column['prefix'].$value.$column['suffix'] !!}
                @endif
            @includeWhen(!empty($column['wrapper']), 'ericli1018.awesome-fields-for-backpack::columns.inc.wrapper_end', ['current_value' => $value])
        @endforeach
    @endif
</span>
