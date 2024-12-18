{{-- browse field --}}
@php
    $multiple = Arr::get($field, 'multiple', true);
    $sortable = Arr::get($field, 'sortable', false);
    $value = old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? '';

    if (!$multiple && is_array($value)) {
        $value = Arr::first($value);
    }

    $field['wrapper'] = $field['wrapper'] ?? $field['wrapperAttributes'] ?? [];
    $field['wrapper']['data-init-function'] = $field['wrapper']['data-init-function'] ?? 'bpFieldInitBrowseMultipleElement';
    $field['wrapper']['data-elfinder-trigger-url'] = $field['wrapper']['data-elfinder-trigger-url'] ?? url(config('elfinder.route.prefix').'/popup/'.$field['name'].'?multiple=1');

    if (isset($field['mime_types'])) {
        $field['wrapper']['data-elfinder-trigger-url'] .= '&mimes='.urlencode(serialize($field['mime_types']));
    }

    if ($multiple) {
        $field['wrapper']['data-multiple'] = "true";
    } else {
        $field['wrapper']['data-multiple'] = "false";
    }

    if($sortable){
        $field['wrapper']['sortable'] = "true";
    }
@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')

    <div class="list" data-field-name="{{ $field['name'] }}">
    @if ($multiple)
        <input type="hidden" data-marker="multipleBrowseInput" name="{{ $field['name'] }}" value="{{ json_encode($value) }}">
    @else
        <input type="text" data-marker="multipleBrowseInput" name="{{ $field['name'] }}" value="{{ $value }}" @include('crud::fields.inc.attributes') readonly>
    @endif
    </div>

    <div class="btn-group" role="group" aria-label="..." style="margin-top: 0px;">
        <button type="button" class="browse popup btn btn-light">
            <i class="la la-cloud-upload"></i>
            {{ trans('backpack::crud.browse_uploads') }}
        </button>
        <button type="button" class="browse clear btn btn-light">
            <i class="la la-eraser"></i>
            {{ trans('backpack::crud.clear') }}
        </button>
    </div>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif

    <script type="text/html" data-marker="browse_multiple_template">
        <div class="input-group">
            <input type="text" @include('crud::fields.inc.attributes') readonly>
            <button type="button" class="browse remove input-group-text">
                <i class="la la-trash"></i>
            </button>
            @if($sortable)
            <button type="button" class="browse move input-group-text"><span class="la la-sort"></span></button>
            @endif
        </div>
    </script>

@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM CSS --}}
@push('crud_fields_styles')
    <!-- include browse_multiple css -->
    @bassetBlock('backpack/crud/fields/jquery-colorbox-example2-colorbox.css')
        <style>
            /*
            Colorbox Core Style:
            The following CSS is consistent between example themes and should not be altered.
            */
            #colorbox, #cboxOverlay, #cboxWrapper{position:absolute; top:0; left:0; z-index:9999; overflow:hidden; -webkit-transform: translate3d(0,0,0);}
            #cboxWrapper {max-width:none;}
            #cboxOverlay{position:fixed; width:100%; height:100%;}
            #cboxMiddleLeft, #cboxBottomLeft{clear:left;}
            #cboxContent{position:relative;}
            #cboxLoadedContent{overflow:auto; -webkit-overflow-scrolling: touch;}
            #cboxTitle{margin:0;}
            #cboxLoadingOverlay, #cboxLoadingGraphic{position:absolute; top:0; left:0; width:100%; height:100%;}
            #cboxPrevious, #cboxNext, #cboxClose, #cboxSlideshow{cursor:pointer;}
            .cboxPhoto{float:left; margin:auto; border:0; display:block; max-width:none; -ms-interpolation-mode:bicubic;}
            .cboxIframe{width:100%; height:100%; display:block; border:0; padding:0; margin:0;}
            #colorbox, #cboxContent, #cboxLoadedContent{box-sizing:content-box; -moz-box-sizing:content-box; -webkit-box-sizing:content-box;}

            /* 
                User Style:
                Change the following styles to modify the appearance of Colorbox.  They are
                ordered & tabbed in a way that represents the nesting of the generated HTML.
            */
            #cboxOverlay{background:#fff; opacity: 0.9; filter: alpha(opacity = 90);}
            #colorbox{outline:0;}
                #cboxContent{margin-top:32px; overflow:visible; background:#000;}
                    .cboxIframe{background:#fff;}
                    #cboxError{padding:50px; border:1px solid #ccc;}
                    #cboxLoadedContent{background:#000; padding:1px;}
                    #cboxLoadingGraphic{background:url(images/loading.gif) no-repeat center center;}
                    #cboxLoadingOverlay{background:#000;}
                    #cboxTitle{position:absolute; top:-22px; left:0; color:#000;}
                    #cboxCurrent{position:absolute; top:-22px; right:205px; text-indent:-9999px;}

                    /* these elements are buttons, and may need to have additional styles reset to avoid unwanted base styles */
                    #cboxPrevious, #cboxNext, #cboxSlideshow, #cboxClose {border:0; padding:0; margin:0; overflow:visible; text-indent:-9999px; width:20px; height:20px; position:absolute; top:-20px; background:url(images/controls.png) no-repeat 0 0;}
                    
                    /* avoid outlines on :active (mouseclick), but preserve outlines on :focus (tabbed navigating) */
                    #cboxPrevious:active, #cboxNext:active, #cboxSlideshow:active, #cboxClose:active {outline:0;}

                    #cboxPrevious{background-position:0px 0px; right:44px;}
                    #cboxPrevious:hover{background-position:0px -25px;}
                    #cboxNext{background-position:-25px 0px; right:22px;}
                    #cboxNext:hover{background-position:-25px -25px;}
                    #cboxClose{background-position:-50px 0px; right:0;}
                    #cboxClose:hover{background-position:-50px -25px;}
                    .cboxSlideshow_on #cboxPrevious, .cboxSlideshow_off #cboxPrevious{right:66px;}
                    .cboxSlideshow_on #cboxSlideshow{background-position:-75px -25px; right:44px;}
                    .cboxSlideshow_on #cboxSlideshow:hover{background-position:-100px -25px;}
                    .cboxSlideshow_off #cboxSlideshow{background-position:-100px 0px; right:44px;}
                    .cboxSlideshow_off #cboxSlideshow:hover{background-position:-75px -25px;}

            #cboxContent, #cboxLoadedContent, .cboxIframe {
				background: transparent;
			}
        </style>
    @endBassetBlock
@endpush

{{-- CUSTOM JS --}}
@push('crud_fields_scripts')
    <!-- include browse_multiple js -->
    <script src="{{ basset('https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js') }}" crossorigin="anonymous"></script>
    <script src="{{ basset('https://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.4/jquery.colorbox-min.js') }}" crossorigin="anonymous"></script>
    @if (app()->getLocale() !== 'en')
    <script src="{{ basset('https://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.4/i18n/jquery.colorbox-' . str_replace('_', '-', app()->getLocale()) . '.js') }}" crossorigin="anonymous"></script>
    @endif
    @bassetBlock('backpack/crud/fields/browse_multiple.js')
    <script>
        // this global variable is used to remember what input to update with the file path
        // because elfinder is actually loaded in an iframe by colorbox
        var elfinderTarget = false;

        // function to use the files selected inside elfinder
        function processSelectedMultipleFiles(files, requestingField) {
            elfinderTarget.trigger('createInputsForItemsSelectedWithElfinder', [files]);                
            elfinderTarget = false;
        }

        function bpFieldInitBrowseMultipleElement(element) {
            var $triggerUrl = element.data('elfinder-trigger-url');
            var $template = element.find("[data-marker=browse_multiple_template]").html();
            var $list = element.find(".list");
            var $input = element.find('input[data-marker=multipleBrowseInput]');
            var $multiple = element.attr('data-multiple');
            var $sortable = element.attr('sortable');

            // show existing items - display visible inputs for each stored path  
            if ($input.val() != '' && $input.val() != null && $multiple === 'true') {
                $paths = JSON.parse($input.val());
                if (Array.isArray($paths) && $paths.length) {
                    // remove any already visible inputs
                    $list.find('.input-group').remove();

                    // add visible inputs for each item inside the hidden input array
                    $paths.forEach(function (path) {
                        var newInput = $($template);
                        newInput.find('input').val(path);
                        $list.append(newInput);
                    });
                }
            }

            // make the items sortable, if configurations says so
            if($sortable){
                $list.sortable({
                    handle: 'button.move',
                    cancel: '',
                    update: function (event, ui) {
                        element.trigger('saveToJson');
                    }
                });
            }

            element.on('click', 'button.popup', function (event) {
                event.preventDefault();

                // remember which element the elFinder was triggered by
                elfinderTarget = element;

                // trigger the elFinder modal
                $.colorbox({
                    href: $triggerUrl,
                    fastIframe: true,
                    iframe: true,
                    width: '80%',
                    height: '80%'
                });
            });

            // turn non-hidden inputs into a JSON
            // and save them inside the hidden input that ACTUALLY holds all paths
            element.on('saveToJson', function(event) {
                var $paths = element.find('input').not('[type=hidden]').map(function (idx, item) {
                    return $(item).val();
                }).toArray();

                // save the JSON inside the hidden input
                $input.val(JSON.stringify($paths));
            });

            if ($multiple === 'true') {
                // remote item button
                element.on('click', 'button.remove', function (event) {
                    event.preventDefault();
                    $(this).closest('.input-group').remove();
                    element.trigger('saveToJson');
                });

                // clear button
                element.on('click', 'button.clear', function (event) {
                    event.preventDefault();

                    $('.input-group', $list).remove();
                    element.trigger('saveToJson');
                });

                // called after one or more items are selected in the elFinder window
                element.on('createInputsForItemsSelectedWithElfinder', element, function(event, files) {
                    files.forEach(function (file) {
                        var newInput = $($template);
                        newInput.find('input').val(file.path);
                        $list.append(newInput);
                    });

                    if($sortable){
                        $list.sortable("refresh")
                    }

                    element.trigger('saveToJson');
                });

            } else {
                // clear button
                element.on('click', 'button.clear', function (event) {
                    $input.val('');
                });

                // called after an item has been selected in the elFinder window
                element.on('createInputsForItemsSelectedWithElfinder', element, function(event, files) {
                    $input.val(files[0].path);
                });
            }
        }
    </script>
    @endBassetBlock
@endpush
