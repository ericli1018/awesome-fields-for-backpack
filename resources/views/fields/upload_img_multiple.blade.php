@php
    $field['wrapper'] = $field['wrapper'] ?? $field['wrapperAttributes'] ?? [];
    $field['wrapper']['data-init-function'] = $field['wrapper']['data-init-function'] ?? 'bpFieldInitUploadImgMultipleElement';
    $field['wrapper']['data-field-name'] = $field['wrapper']['data-field-name'] ?? $field['name'];

	if (!isset($field['value'])) {
		$field['value'] = '[]';
	}

	if (!isset($field['qty'])) {
		$field['qty'] = 0;
	}

	if (!isset($field['showSingleChoise'])) {
		$field['showSingleChoise'] = 1;
	}

	if (!isset($field['showComment'])) {
		$field['showComment'] = 1;
	}
	
	$isProd = env('APP_ENV', 'local') == 'local' ? false : true;
	$total_qty = 0;
@endphp

{{-- upload multiple input --}}
@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')

	{{-- Show the file name and a "Clear" button on EDIT form. --}}
	@if (isset($field['value']))
		@php
			if (is_string($field['value'])) {
				$values = json_decode($field['value'], true) ?? [];
			} else {
				$values = $field['value'];
			}
		@endphp
		<input name="{{ $field['name'] }}[]" type="hidden" value="">
		<input name="meta_{{ $field['name'] }}" type="hidden" value="[]">
		<div class="backstrap-file mt-2">
			<input
				type="file"
				accept="image/png, image/jpeg, image/jpg"
				name="{{ $field['name'] }}[]"
				@include('crud::fields.inc.attributes', ['default_class' =>  isset($field['value']) && $field['value']!=null?'file_input backstrap-file-img-input':'file_input backstrap-file-img-input'])
				multiple
			>
			<label class="backstrap-file-img-label" for="customFile"></label>
		</div>
		
		<div class="well well-sm existing-img-file">
		@if (count($values))
			@foreach($values as $key => $item)
				@php
					if (is_array($item) && array_key_exists('file_path', $item)) {
						$is_selected = @$item['is_selected'];
						$comment = @$item['comment'];
						$file_path = @$item['file_path'];
						$file_name = @$item['fn'];
					} else {
						$file_path = $item;
					}
				@endphp
				<div class="file-preview float-left {{ $field['name'] }}-file-preview">
					@if (isset($field['temporary']))
						@php 
							$src = isset($field['disk'])?asset(\Storage::disk($field['disk'])->temporaryUrl($file_path, Carbon\Carbon::now()->addMinutes($field['temporary']))):asset($file_path);
						@endphp
						<a target="_blank" href="{{ $src }}" data-filepath="{{ $file_path }}" title="{{ $file_name }}">{{ $file_path }}</a>
					@else
						@php 
							$src = isset($field['disk'])?asset(\Storage::disk($field['disk'])->url($file_path)):asset($file_path);
						@endphp
						@if (preg_match("#\.(jpg|jpeg|gif|png)$# i", $src))
							<a target="_blank" href="{{ $src }}" data-filepath="{{ $file_path }}" title="{{ $file_name }}"><img src="{{ $src }}" /></a><br />
							@if ($field['showSingleChoise'] == '1')<div class="form-check form-check-inline"><input type="radio" id="{{ $field['name'] . ($total_qty) }}_selected"  class="is_selected form-check-input" name="{{ $field['name'] }}_selected" value="1" {{ $is_selected ? 'checked': '' }} /><label for="{{ $field['name'] . ($total_qty) }}_selected" class="form-check-label">主要顯示</label></div><br />@endif
							@if ($field['showComment'] == '1')<textarea class="comment form-control">{{ $comment }}</textarea>@endif
						@else
							<a target="_blank" href="{{ $src }}" data-filepath="{{ $file_path }}" title="{{ $file_name }}">{{ basename($file_path) }}</a>
						@endif
						@php 
							$total_qty++;
						@endphp
					@endif
					<br />
					<a href="#" class="btn btn-light btn-sm file-move-up-button" title="{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.button_move_up') }}"><i class="la la-arrow-circle-o-left"></i></a>
					<a href="#" class="btn btn-light btn-sm file-move-down-button" title="{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.button_move_down') }}"><i class="la la-arrow-circle-o-right"></i></a>
					<a href="#" class="btn btn-danger btn-sm file-clear-button" title="{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.button_remove') }}" data-filename="{{ $file_path }}"><i class="la la-trash"></i></a>
					<div class="clearfix"></div>
				</div>
			@endforeach
		@else
		@endif
    	</div>
    @endif
	<input type="hidden" class="qty" value="{{ $field['qty'] }}" />
	<input type="hidden" class="showSingleChoise" value="{{ $field['showSingleChoise'] }}" />
	<input type="hidden" class="showComment" value="{{ $field['showComment'] }}" />
	<input type="hidden" class="totalQty" value="{{ $total_qty }}" />
	<input type="hidden" class="orgValue" value="{{{json_encode($values??[])}}}" />
	{{-- Show the file picker on CREATE form. --}}

    {{-- HINT --}}
	<p class="help-block">
		<span style="color:#666;">{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.hint_gray_frame') }}</span>
		<span style="color:green;">{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.hint_green_frame') }}</span>
		<span style="color:red;">{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.hint_red_frame') }}</span>
	</p>
	@if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif

	@include('crud::fields.inc.wrapper_end')

{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
	@push('crud_fields_styles')
	<!-- include upload_img_multiple css -->
	@bassetBlock('backpack/crud/fields/upload-img-multiple.css')
	<style type="text/css">
		.existing-img-file {
			border: 1px solid #666;
			border-radius: 5px;
			vertical-align: middle;
			display:flow-root;
			min-height: 32px;
		}

		.existing-img-file .file-preview {
			text-align: center;
			display: inline-block;
			border: 1px solid #666;
			border-radius: 5px;
			padding: 5px;
    		margin: 5px;
		}

		.existing-img-file .tmp-file-preview {
			border: 2px solid green;
		}

		.existing-img-file .tmp-file-preview-name-dup {
			border: 2px solid red;
		}

		.existing-img-file .file-preview label {
			padding-top: inherit;
		}

		.existing-img-file .file-preview:first-child .file-move-up-button {
			display: none;
		}

		.existing-img-file .file-preview:last-child .file-move-down-button {
			display: none;
		}

		.existing-img-file a {
			padding-top: 5px;
			display: inline-block;
			font-size: 0.9em;
			margin: 3px;
			border: solid 1px #666;
			padding: 5px;
			border-radius: 5px;
		}

		.existing-img-file a img {
			height: 100px;
			display: inline-block;
		}

		.existing-img-file textarea {
			height: 32px;
			vertical-align: middle;
			display: inline-block;
		}

		.backstrap-file {
			position: relative;
			display: inline-block;
			width: 100%;
			height: calc(1.5em + 0.75rem + 2px);
			margin-bottom: 0;
		}

		.backstrap-file-img-input {
			position: relative;
			z-index: 2;
			width: 100%;
			height: calc(1.5em + 0.75rem + 2px);
			margin: 0;
			opacity: 0;
		}

		.backstrap-file-img-input:focus ~ .backstrap-file-img-label {
			border-color: #acc5ea;
			box-shadow: 0 0 0 0rem rgba(70, 127, 208, 0.25);
		}

		.backstrap-file-img-input:disabled ~ .backstrap-file-img-label {
			background-color: #e4e7ea;
		}

		.backstrap-file-img-input:lang(en) ~ .backstrap-file-img-label::after {
			content: "{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.button_browse') }}";
		}

		.backstrap-file-img-input ~ .backstrap-file-img-label[data-browse]::after {
			content: attr(data-browse);
		}

		.backstrap-file-img-label {
			position: absolute;
			top: 0;
			right: 0;
			left: 0;
			z-index: 1;
			height: calc(1.5em + 0.75rem + 2px);
			padding: 0.375rem 0.75rem;
			font-weight: 400;
			line-height: 1.5;
			color: #5c6873;
			background-color: #fff;
			border: 1px solid #e4e7ea;
			border-radius: 0.25rem;
			font-weight: 400!important;
		}

		.backstrap-file-img-label::after {
			position: absolute;
			top: 0;
			right: 0;
			bottom: 0;
			z-index: 3;
			display: block;
			height: calc(1.5em + 0.75rem);
			padding: 0.375rem 0.75rem;
			line-height: 1.5;
			color: #5c6873;
			content: "{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.button_browse') }}";
			background-color: #f0f3f9;
			border-left: inherit;
			border-radius: 0 0.25rem 0.25rem 0;
		}
	</style>
	@endBassetBlock
	@endpush

    @push('crud_fields_scripts')
		<!-- include upload_img_multiple js-->
    	@bassetBlock('backpack/crud/fields/upload-img-multiple.js')
        <script>
        	function bpFieldInitUploadImgMultipleElement(element) {
				//var imgDT = new DataTransfer();
				
				var fieldName = element.attr('data-field-name');

				var fileInput = element.find("input[type=file]").first();
				var existingFileDiv = element.find(".existing-img-file").first();
				var DTInput = element.find("input[name='" + fieldName + "']").first();
				var metaDTInput = element.find("input[name='meta_" + fieldName + "']").first();
				var comment = element.find(".comment");
				var isSelected = element.find(".is_selected");
				var fileMoveUpButton = element.find(".file-move-up-button");
				var fileMoveDownButton = element.find(".file-move-down-button");
        		var clearFileButton = element.find(".file-clear-button");
        		var inputLabel = element.find("label.backstrap-file-img-label");
				var uploadQtyLimit = element.find('.qty').val();
				var uploadShowSingleChoise = element.find('.showSingleChoise').val();
				var uploadShowComment = element.find('.showComment').val();
				var orgValues = element.find('.orgValue').val();
				var totalQty = element.find('.totalQty').val();

				orgValues = !!orgValues ? JSON.parse($("<textarea/>").html(orgValues).text()) : [];
				$(metaDTInput).val(JSON.stringify(orgValues)).trigger('change');
				
				var fileInputOnClick = function(e){
					existingFileDiv.find('.tmp-file-preview').remove();
					var filePreviews = $('.' + fieldName + '-file-preview');
	                if (uploadQtyLimit > 0 && uploadQtyLimit <= filePreviews.length) {
						alert('{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.qty_limit') }}');
						e.preventDefault();
						return;
					}
				};

				var fileInputOnChange = function(e) {
					let selectedFiles = orgValues;
					existingFileDiv.find('.tmp-file-preview').remove();
					if ($(this)[0].files.length == 0) 
					{
						inputLabel.html("{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.select_file') }}");
					}
					else
					{
						//let selectedFilesJsonStr = $(metaDTInput).val();
						let qty = totalQty;
						let errorFileNames = '';

						//selectedFiles = !!selectedFilesJsonStr ? JSON.parse(selectedFilesJsonStr) : [];
						

						var filePreviews = $('.' + fieldName + '-file-preview');
						if (uploadQtyLimit > 0 && uploadQtyLimit < (filePreviews.length + $(this)[0].files.length)) {
							alert('{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.qty_limit') }}');
							setTimeout(fileInputRebuild, 1);
							return;
						}

						inputLabel.html("{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.selected_file') }}");
					
						Array.from($(this)[0].files).forEach(file => {
							var isNameDup = false;
							if (selectedFiles.find(o => o.fn == file.name)) {
								errorFileNames += (errorFileNames != '\n' ? '\n' : '\n');
								errorFileNames += file.name;
								isNameDup = true;
								//return;
							}
							//imgDT.items.add(file);
							selectedFiles.push({name: file.name, type: file.type, comment: null, is_selected: false, fn: file.name});
							var elem = $(
								'<div class="file-preview tmp-file-preview ' + (isNameDup ? 'tmp-file-preview-name-dup' : '') + ' float-left {{ $field["name"] }}-file-preview">' + 
									'<a href="javascript:alert(\'{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.not_saved_yet') }}\')" target="">' +
										'<img />' + 
									'</a><br />' +
									( uploadShowSingleChoise == 1 ? '<div class="form-check form-check-inline"><input type="radio" id="{{ $field["name"] }}' + (qty) + '_selected" class="is_selected form-check-input" name="{{ $field["name"] }}_selected" value="1" /><label for="{{ $field["name"] }}' + (qty) + '_selected" class="form-check-label">主要顯示</label></div><br />' : '') +
									( uploadShowComment == 1 ? '<textarea class="comment form-control"></textarea>' : '') +
									'<br />' +
									'<a href="#" class="btn btn-light btn-sm file-move-up-button" title="排序往前"><i class="la la-arrow-circle-o-left"></i></a>' +
									'<a href="#" class="btn btn-light btn-sm file-move-down-button" title="排序往後"><i class="la la-arrow-circle-o-right"></i></a>' +
									//'<a href="#" class="btn btn-danger btn-sm tmp-file-clear-button" title="移除"><i class="la la-trash"></i></a>' +
									'<div class="clearfix"></div>' +
								'</div>');
							elem.find('a:first').attr('title', file.name);
							elem.find('a:first').attr('data-filepath', file.name);
							elem.find('img:first').attr('src', URL.createObjectURL(file));
							elem.find('.tmp-file-clear-button:first').attr('data-filename', file.name);
							existingFileDiv.append(elem);
							qty++;
						});
						totalQty = qty;

						if (!!errorFileNames) {
							alert('{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.hint_file_name_dup') }}' + errorFileNames);
						}

						var tmpFileClearButton = existingFileDiv.find('.tmp-file-clear-button');
						var tmpFileMoveUpButton = existingFileDiv.find(".file-move-up-button");
						var tmpFileMoveDownButton = existingFileDiv.find(".file-move-down-button");
						var tmpComment = existingFileDiv.find(".comment");
						var tmpIsSelected = existingFileDiv.find(".is_selected");

						if (tmpIsSelected)
						{
							tmpIsSelected.off();
							tmpIsSelected.change(function(){
								updateMetaDT();
							});
						}
						
						if (tmpComment) 
						{
							tmpComment.off();
							tmpComment.bind('input propertychange', function(){
								updateMetaDT();
							});
						}
						
						tmpFileMoveUpButton.off();
						tmpFileMoveUpButton.click(function(e) {
							e.preventDefault();
							var parent = $(this).parent();
							parent.prev().insertAfter(parent);
							updateMetaDT();
						});

						tmpFileMoveDownButton.off();
						tmpFileMoveDownButton.click(function(e) {
							e.preventDefault();
							var parent = $(this).parent();
							parent.next().insertBefore(parent);
							updateMetaDT();
						});
						/*
						tmpFileClearButton.off();
						tmpFileClearButton.click(function(e) {
							e.preventDefault();
							var container = $(this).parent().parent();
							var parent = $(this).parent();
							// remove the filename and button
							parent.remove();
							
							const rmFileName = $(this).data('filename');
							const dt = new DataTransfer();
							//const files = fileInput[0].files;
							const files = imgDT.files;
							for (let i = 0; i < files.length; i++) {
								const file = files[i];
								if (file.name != rmFileName)
								{
									dt.items.add(file); // here you exclude the file. thus removing it.
								}	
							}
							imgDT.files = dt.files;
							fileInput.files = imgDT.files;
							updateMetaDT();
						});
						
						fileInput.files = imgDT.files;
						*/
						// remove the hidden input, so that the setXAttribute method is no longer triggered
						$(this).next("input[type=hidden]:not([name='clear_" + fieldName + "[]'])").remove();

					}
					$(DTInput).val(JSON.stringify(selectedFiles)).trigger('change');
					updateMetaDT();
					
		        }

				var fileInputRebuild = function() {
					var newFileInput = $(fileInput).clone();
					
					newFileInput.click(fileInputOnClick);
		        	newFileInput.change(fileInputOnChange);
					newFileInput.insertAfter(fileInput);
					fileInput.remove();
					fileInput = newFileInput;
				};

				var updateMetaDT = function() {
					var filePreviews = $('.' + fieldName + '-file-preview');
					var metaDT = []; // ['file_path':'<file_path>','is_selected':false,'comment':'<comment>','fn':'<file name>']

					filePreviews.each(function(index, elem) {
						var comment = $(elem).find('.comment:first').val();
						var is_selected = $(elem).find(".is_selected:checked").length > 0 ? true : false;
						var file_path = $(elem).find('a:first').data('filepath');
						var fn = $(elem).find('a:first').attr('title');
						metaDT.push({'file_path': file_path, 'is_selected': is_selected, 'comment': comment, 'fn': fn});
					});

					$(metaDTInput).val(JSON.stringify(metaDT)).trigger('change');
				};

				isSelected.change(function(){
					updateMetaDT();
				});

				comment.bind('input propertychange',function(){
					updateMetaDT();
				});

				fileMoveUpButton.click(function(e) {
					e.preventDefault();
					var parent = $(this).parent();
					parent.prev().insertAfter(parent);
					updateMetaDT();
				});

				fileMoveDownButton.click(function(e) {
					e.preventDefault();
					var parent = $(this).parent();
					parent.next().insertBefore(parent);
					updateMetaDT();
				});

				clearFileButton.click(function(e) {
		        	e.preventDefault();
		        	var container = $(this).parent().parent();
		        	var parent = $(this).parent();
		        	// remove the filename and button
		        	parent.remove();
		        	var elemInputClear = $("<input type='hidden' name='clear_" + fieldName + "[]'>");
					elemInputClear.attr('value', $(this).data('filename'));
					elemInputClear.insertAfter(fileInput);
					
					let selectedFiles = orgValues;
					let delFilePath = $(this).data('filename');
					let newSelectedFiles = [];
					Array.from(selectedFiles).forEach(item => {
						if (item.file_path != delFilePath) 
						{
							newSelectedFiles.push(item);
						}
					});
					orgValues = newSelectedFiles;
					updateMetaDT();
		        });

				inputLabel.html("{{ trans('ericli1018.awesome-fields-for-backpack::upload-img-field.select_file') }}");
				fileInput.click(fileInputOnClick);
		        fileInput.change(fileInputOnChange);

				element.find('input').on('CrudField:disable', function(e) {
					element.children('.backstrap-file').find('input').prop('disabled', 'disabled');
					element.children('.existing-img-file').find('.file-preview').each(function(i, el) {
						let $deleteButton = $(el).find('a.file-clear-button');

						if(deleteButton.length > 0) {
							$deleteButton.on('click.prevent', function(e) {
								e.stopImmediatePropagation();
								return false;
							});
							// make the event we just registered, the first to be triggered
							$._data($deleteButton.get(0), "events").click.reverse();
						}
					});
				});

				element.on('CrudField:enable', function(e) {
					element.children('.backstrap-file').find('input').removeAttr('disabled');
					element.children('.existing-img-file').find('.file-preview').each(function(i, el) {
						$(el).find('a.file-clear-button').unbind('click.prevent');
					});
				});

				updateMetaDT();
        	}
        </script>
        @endBassetBlock
    @endpush
