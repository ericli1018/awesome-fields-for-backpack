{{-- browse field --}}
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')

    <div class="input-group">
		<input
			type="text"
			name="{{ $field['name'] }}"
			value="{{ old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? '' }}"
			data-init-function="bpFieldInitBrowseElement"
			data-elfinder-trigger-url="{{ url(config('elfinder.route.prefix').'/popup') }}"
			@include('crud::fields.inc.attributes')

			@if(!isset($field['readonly']) || $field['readonly']) readonly @endif
		>

		<span class="input-group-append">
			<button type="button" data-inputid="{{ $field['name'] }}-filemanager" class="btn btn-light btn-sm popup_selector"><i class="la la-cloud-upload"></i> {{ trans('backpack::crud.browse_uploads') }}</button>
			<button type="button" data-inputid="{{ $field['name'] }}-filemanager" class="btn btn-light btn-sm clear_elfinder_picker"><i class="la la-eraser"></i> {{ trans('backpack::crud.clear') }}</button>
		</span>
	</div>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM CSS --}}
@push('crud_fields_styles')
    <!-- include browse css -->
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
    <!-- include browse js -->
    <script src="{{ basset('https://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.4/jquery.colorbox-min.js') }}" crossorigin="anonymous"></script>
    @if (app()->getLocale() !== 'en')
    <script src="{{ basset('https://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.4/i18n/jquery.colorbox-' . str_replace('_', '-', app()->getLocale()) . '.js') }}" crossorigin="anonymous"></script>
    @endif
    @bassetBlock('backpack/crud/fields/browse.js')
    <script>
        // this global variable is used to remember what input to update with the file path
        // because elfinder is actually loaded in an iframe by colorbox
        var elfinderTarget = false;

        // function to update the file selected by elfinder
        function processSelectedFile(filePath, requestingField) {
            elfinderTarget.val(filePath.replace(/\\/g,"/"));
            elfinderTarget = false;
        }

        function bpFieldInitBrowseElement(element) {
            var triggerUrl = element.data('elfinder-trigger-url')
            var name = element.attr('name');

            element.siblings('.input-group-append').children('button.popup_selector').click(function (event) {
                event.preventDefault();

                elfinderTarget = element;

                // trigger the reveal modal with elfinder inside
                $.colorbox({
                    href: triggerUrl + '/' + name,
                    fastIframe: true,
                    iframe: true,
                    width: '80%',
                    height: '80%'
                });
            });

            element.siblings('.input-group-append').children('button.clear_elfinder_picker').click(function (event) {
                event.preventDefault();
                element.val("");
            });
        }
    </script>
    @endBassetBlock
@endpush
