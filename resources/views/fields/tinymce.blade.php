{{-- tinymce field --}}
@php
    $defaultOptions = [
        'file_picker_callback' => 'elFinderBrowser',
        'selector' => 'textarea.tinymce',
        'plugins' => 'image,link,media,anchor',
        //these two options allow tinymce to save the path of images "/upload/image.jpg" instead of the relative server path "../../../uploads/image.jpg"
        'relative_urls' =>  false,
        'remove_script_host' => true,
    ];

    $field['options'] = array_merge($defaultOptions, $field['options'] ?? []);
@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')

    <textarea
        name="{{ $field['name'] }}"
        data-init-function="bpFieldInitTinyMceElement"
        data-options='{!! trim(json_encode($field['options'])) !!}'
        @include('crud::fields.inc.attributes', ['default_class' =>  'form-control tinymce'])
        >{{ old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? '' }}</textarea>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM CSS --}}
@push('crud_fields_styles')
    
@endpush

{{-- CUSTOM JS --}}
@push('crud_fields_scripts')
    <!-- include tinymce js-->
    @bassetDirectory(base_path('vendor/tinymce/tinymce/'), 'vendor/tinymce/tinymce/')
    <script src="{{ basset(base_path('vendor/tinymce/tinymce/tinymce.min.js')) }}" crossorigin="anonymous"></script>
    @if (app()->getLocale() !== 'en')
    <script src="{{ basset(base_path('vendor/mklkj/tinymce-i18n/langs5/' . str_replace('-', '_', app()->getLocale()) . '.js')) }}" crossorigin="anonymous"></script>
    @endif
    
    @bassetBlock('backpack/crud/fields/ckeditor.js')
    <script>
        function bpFieldInitTinyMceElement(element) {
            // grab the configuration defined in PHP
            var configuration = element.data('options');

            // the target should be the element the function has been called on
            configuration['target'] = element;
            configuration['file_picker_callback'] = eval(configuration['file_picker_callback']);

            // automatically update the textarea value on focusout
            configuration['setup'] = (function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            });

            // initialize the TinyMCE editor
            tinymce.init(element.data('options'));
        }

        function elFinderBrowser (callback, value, meta) {
            tinymce.activeEditor.windowManager.openUrl({
                title: 'elFinder 2.0',
                url: '{{ backpack_url('elfinder/tinymce5') }}',
                width: 900,
                height: 460,
                onMessage: function (dialogApi, details) {
                    if (details.mceAction === 'fileSelected') {
                        const file = details.data.file;

                        // Make file info
                        const info = file.name;

                        // Provide file and text for the link dialog
                        if (meta.filetype === 'file') {
                            callback(file.url, {text: info, title: info});
                        }

                        // Provide image and alt text for the image dialog
                        if (meta.filetype === 'image') {
                            callback(file.url, {alt: info});
                        }

                        // Provide alternative source and posted for the media dialog
                        if (meta.filetype === 'media') {
                            callback(file.url);
                        }

                        dialogApi.close();
                    }
                }
            });
        }
    </script>
    @endBassetBlock
@endpush
