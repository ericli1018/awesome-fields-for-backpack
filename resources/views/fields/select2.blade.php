{{-- select2 field --}}
@php
    //$field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
    $current_value = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
    
    if (is_object($current_value) && is_subclass_of(get_class($current_value), 'Illuminate\Database\Eloquent\Model') ) {
        $current_value = $current_value->getKey();
    }
    if (!isset($field['options'])) {
        $options = $field['model']::all();
    } else {
        $options = call_user_func($field['options'], $field['model']::query());
    }
    $field['allows_null'] = $field['allows_null'] ?? $crud->model::isColumnNullable($field['name']);
@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')

    <select type="hidden"
        name="{{ $field['name'] }}"
        style="width: 100%"
        data-field-is-inline="{{var_export($inlineCreate ?? false)}}"
        data-init-function="bpFieldInitSelect2Element"
        data-language="{{ str_replace('_', '-', app()->getLocale()) }}"
        value="{{ $field['value'] }}"
        @include('crud::fields.inc.attributes', ['default_class' => 'form-control select2_field'])
        >

        @if ($field['allows_null'])
            <option value="">-</option>
        @endif

        @if (count($options))
            @foreach ($options as $option)
                @if($current_value == $option->getKey())
                    <option value="{{ $option->getKey() }}" selected>{{ $option->{$field['attribute']} }}</option>
                @else
                    <option value="{{ $option->getKey() }}">{{ $option->{$field['attribute']} }}</option>
                @endif
            @endforeach
        @endif
    
    </select>
    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM CSS --}}
@push('crud_fields_styles')
    <!-- include select2 css-->
    <link href="{{ basset(base_path('vendor/select2/select2/dist/css/select2.min.css')) }}" rel="stylesheet" type="text/css" />
    <link href="{{ basset('https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

{{-- CUSTOM JS --}}
@push('crud_fields_scripts')
    <!-- include select2 js-->
    <script src="{{ basset(base_path('vendor/select2/select2/dist/js/select2.full.min.js')) }}" crossorigin="anonymous"></script>
    @if (app()->getLocale() !== 'en')
    <script src="{{ basset(base_path('vendor/select2/select2/dist/js/i18n/' . str_replace('_', '-', app()->getLocale()) . '.js')) }}" crossorigin="anonymous"></script>
    @endif
    @bassetBlock('backpack/crud/fields/select2.js')
    <script>
        function bpFieldInitSelect2Element(element) {
            if (!element.hasClass("select2-hidden-accessible")) 
            {
                let $isFieldInline = element.data('field-is-inline');
                
                element.select2({
                    theme: "bootstrap",
                    dropdownParent: $isFieldInline ? $('#inline-create-dialog .modal-content') : document.body
                });
            }
        }
    </script>
    @endBassetBlock
@endpush
