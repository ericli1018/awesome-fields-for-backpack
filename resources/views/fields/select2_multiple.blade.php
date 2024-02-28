{{-- select2 multiple field --}}
@php
    if (!isset($field['options'])) {
        $field['options'] = $field['model']::all();
    } else {
        $field['options'] = call_user_func($field['options'], $field['model']::query());
    }

    //build option keys array to use with Select All in javascript.
    $model_instance = new $field['model'];
    $options_ids_array = $field['options']->pluck($model_instance->getKeyName())->toArray();

    $field['multiple'] = $field['multiple'] ?? true;
    $field['allows_null'] = $field['allows_null'] ?? $crud->model::isColumnNullable($field['name']);
@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')

    <select
        name="{{ $field['name'] }}[]"
        style="width: 100%"
        data-init-function="bpFieldInitSelect2MultipleElement"
        data-field-is-inline="{{var_export($inlineCreate ?? false)}}"
        data-select-all="{{ var_export($field['select_all'] ?? false)}}"
        data-options-for-js="{{json_encode(array_values($options_ids_array))}}"
        data-language="{{ str_replace('_', '-', app()->getLocale()) }}"
        @include('crud::fields.inc.attributes', ['default_class' =>  'form-control select2_multiple'])
        {{ $field['multiple'] ? 'multiple' : '' }}>

        @if ($field['allows_null'])
            <option value="">-</option>
        @endif

        @if (isset($field['model']))
            @foreach ($field['options'] as $option)
                @if( (old(square_brackets_to_dots($field["name"])) && in_array($option->getKey(), old($field["name"]))) || (is_null(old(square_brackets_to_dots($field["name"]))) && isset($field['value']) && in_array($option->getKey(), $field['value']->pluck($option->getKeyName(), $option->getKeyName())->toArray())))
                    <option value="{{ $option->getKey() }}" selected>{{ $option->{$field['attribute']} }}</option>
                @else
                    <option value="{{ $option->getKey() }}">{{ $option->{$field['attribute']} }}</option>
                @endif
            @endforeach
        @endif
    </select>

    @if(isset($field['select_all']) && $field['select_all'])
        <a class="btn btn-xs btn-default select_all" style="margin-top: 5px;"><i class="la la-check-square-o"></i> {{ trans('backpack::crud.select_all') }}</a>
        <a class="btn btn-xs btn-default clear" style="margin-top: 5px;"><i class="la la-times"></i> {{ trans('backpack::crud.clear') }}</a>
    @endif

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM CSS --}}
@push('crud_fields_styles')
    <!-- include select2_multiple css-->
    <link href="{{ basset(base_path('vendor/select2/select2/dist/css/select2.min.css')) }}" rel="stylesheet" type="text/css" />
    <link href="{{ basset('https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

{{-- CUSTOM JS --}}
@push('crud_fields_scripts')
    <!-- include select2_multiple js-->
    <script src="{{ basset(base_path('vendor/select2/select2/dist/js/select2.full.min.js')) }}" crossorigin="anonymous"></script>
    @if (app()->getLocale() !== 'en')
    <script src="{{ basset(base_path('vendor/select2/select2/dist/js/i18n/' . str_replace('_', '-', app()->getLocale()) . '.js')) }}" crossorigin="anonymous"></script>
    @endif
    @bassetBlock('backpack/crud/fields/select2_multiple.js')
    <script>
        function bpFieldInitSelect2MultipleElement(element) {
            var $select_all = element.attr('data-select-all');
            if (!element.hasClass("select2-hidden-accessible"))
            {
                let $isFieldInline = element.data('field-is-inline');

                var $obj = element.select2({
                    theme: "bootstrap",
                    dropdownParent: $isFieldInline ? $('#inline-create-dialog .modal-content') : document.body
                });

                //get options ids stored in the field.
                var options = JSON.parse(element.attr('data-options-for-js'));

                if($select_all) {
                    element.parent().find('.clear').on("click", function () {
                        $obj.val([]).trigger("change");
                    });
                    element.parent().find('.select_all').on("click", function () {
                        $obj.val(options).trigger("change");
                    });
                }
            }
        }
    </script>
    @endBassetBlock
@endpush
