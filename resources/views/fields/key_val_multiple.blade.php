{{-- key_val_multiple_field field --}}
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')

    <input type="hidden"
        name="{{ $field['name'] }}"
        data-init-function="bpFieldInitKeyValMultipleElement"
        value="{{ $field['value'] }}"
        @include('crud::fields.inc.attributes')>
    
    <div id="key_val_multiple_{{ $field['name'] }}" class="key_val_multiple_area form-control">
        <div class="row header">
            <lable class="col-4">{{ trans('ericli1018.awesome-fields-for-backpack::key-val-multiple.title') }}</lable>
            <lable class="col-7">{{ trans('ericli1018.awesome-fields-for-backpack::key-val-multiple.content') }}</lable>
            <lable class="col-1"></lable>
        </div>
        <div class="row item">
            <lable class="col-4"><input class="col-12 key" /></lable>
            <lable class="col-7"><input class="col-12 val" /></lable>
            <lable class="col-1"><a class="btn btn-sm btn-danger col-12" title="{{ trans('ericli1018.awesome-fields-for-backpack::key-val-multiple.button_remove') }}">-</a></lable>
        </div>
        <div class="row bottom">
            <lable class="col-12">&nbsp;</lable>
            <lable class="col-11"><a class="btn btn-sm btn-primary form-control btnAddRow" title="{{ trans('ericli1018.awesome-fields-for-backpack::key-val-multiple.button_add') }}">+</a></lable>
            <lable class="col-1"></lable>
        </div>
    </div>
    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM CSS --}}
@push('crud_fields_styles')
    <!-- include key_val_multiple css -->
    @bassetBlock('backpack/crud/fields/key_val_multiple_field.css')
        <style>
            .key_val_multiple_area {
                border: 1px solid rgba(0,40,100,.12);
                border-radius: 5px;
                padding: 10px;
                vertical-align: middle;
                display:flow-root;
            }
            .key_val_multiple_area label {
                
            }
        </style>
    @endBassetBlock
@endpush

{{-- CUSTOM JS --}}
@push('crud_fields_scripts')
    <!-- include key_val_multiple js -->
    @bassetBlock('backpack/crud/fields/key_val_multiple_field.js')
    <script>
        function bpFieldInitKeyValMultipleElement(element) {
            //console.log(element.attr('name') + '=>' + element.val());
            var elemVal = element.val();
            var elemValObj = [];
            var elemName = element.attr('name');
            var keyValMultiArea = $('#key_val_multiple_' + elemName);
            var bottomArea = $(keyValMultiArea).find('.bottom');
            var elemAdd = $(keyValMultiArea).find('.btnAddRow');
            var elemRowStr =   '<div class="row item">';
            elemRowStr +=          '<lable class="col-4"><input class="form-control key" /></lable>';
            elemRowStr +=          '<lable class="col-7"><input class="form-control val" /></lable>';
            elemRowStr +=          '<lable class="col-1"><a class="btn btn-sm btn-danger form-control btnDelRow" title="{{ trans('ericli1018.awesome-fields-for-backpack::key-val-multiple.button_remove') }}">-</a></lable>';
            elemRowStr +=      '</div>';

            $(keyValMultiArea).find('.item').remove();
            try 
            {
                elemValObj = JSON.parse(elemVal);
            } 
            catch (e) 
            {
            }

            var updateVal = function() 
            {
                var vals = [];
                $(keyValMultiArea).find('.item').each(function()
                {
                    var obj = {};
                    var key = $(this).find('.key').val();
                    var val = $(this).find('.val').val();
                    
                    obj[key] = val;
                    vals.push(obj);
                });
                //console.log(JSON.stringify(vals));
                element.val(JSON.stringify(vals));
            };

            var addRowItem = function(key = null, val = null)
            {
                var rowElem = $(elemRowStr);
                var btnDelRow = $(rowElem).find('.btnDelRow');

                if (key) 
                {
                    $(rowElem).find('.key').val(key);
                }

                if (val) 
                {
                    $(rowElem).find('.val').val(val);
                }

                $(rowElem).find('input').change(function()
                {
                    updateVal();
                });

                btnDelRow.click(function()
                {
                    $(rowElem).remove();
                    updateVal();
                });

                $(rowElem).insertBefore(bottomArea);
            };

            elemAdd.click(function()
            {
                addRowItem();
            });

            elemValObj.forEach(function(obj)
            {
                Object.getOwnPropertyNames(obj).forEach((val, idx, array) => 
                {
                    //console.log(`${val} -> ${obj[val]}`);
                    addRowItem(val, obj[val]);
                });
            });
        }
    </script>
    @endBassetBlock
@endpush
