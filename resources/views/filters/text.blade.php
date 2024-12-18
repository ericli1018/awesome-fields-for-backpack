{{-- Text Backpack CRUD filter --}}

<li filter-name="{{ $filter->name }}"
    filter-type="{{ $filter->type }}"
    filter-key="{{ $filter->key }}"
	class="nav-item dropdown {{ Request::get($filter->name) ? 'active' : '' }}">
	<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $filter->label }} <span class="caret"></span></a>
	<div class="dropdown-menu p-0">
		<div class="form-group backpack-filter mb-0">
			<div class="input-group">
		        <input class="form-control pull-right"
		        		id="text-filter-{{ $filter->key }}"
						data-filter-key="{{ $filter->key }}"
						data-filter-name="{{ $filter->name }}"
						data-filter-type="text"
		        		type="text"
						@if ($filter->currentValue)
							value="{{ $filter->currentValue }}"
						@endif
		        		>
		    	<a class="input-group-text text-filter-{{ $filter->key }}-clear-button" href=""><i class="la la-times"></i></a>
		    </div>
		</div>
	</div>
</li>

{{-- ########################################### --}}
{{-- Extra CSS and JS for this particular filter --}}


{{-- FILTERS EXTRA JS --}}
{{-- push things in the after_scripts section --}}

@push('crud_list_scripts')
	@bassetBlock('backpack/crud/filters/text.js')
	<script>
		jQuery(document).ready(function($) {
			$('input[data-filter-type=text]').not('[data-filter-enabled]').each(function () {
				$(this).attr('data-filter-enabled', '');
				var filter_name = $(this).attr('data-filter-name');
                var filter_key = $(this).attr('data-filter-key');
				
				$(this).on('change', function(e) {
					var parameter = filter_name;
					var value = $(this).val();

					// behaviour for ajax table
					var ajax_table = $('#crudTable').DataTable();
					var current_url = ajax_table.ajax.url();
					var new_url = addOrUpdateUriParameter(current_url, parameter, value);

					// replace the datatables ajax url with new_url and reload it
					new_url = normalizeAmpersand(new_url.toString());
					ajax_table.ajax.url(new_url).load();

					// add filter to URL
					crud.updateUrl(new_url);

					// mark this filter as active in the navbar-filters
					if (URI(new_url).hasQuery(filter_name, true)) {
						$('li[filter-key=' + filter_key + ']').removeClass('active').addClass('active');
					} else {
						$('li[filter-key=' + filter_key + ']').trigger('filter:clear');
					}
				});

				$('li[filter-key=' + filter_key + ']').on('filter:clear', function(e) {
					$('li[filter-key=' + filter_key + ']').removeClass('active');
					$('#text-filter-' + filter_key).val('');
				});

				// datepicker clear button
				$('.text-filter-' + filter_key + '-clear-button').click(function(e) {
					e.preventDefault();

					$('li[filter-key=' + filter_key + ']').trigger('filter:clear');
					$('#text-filter-' + filter_key).val('');
					$('#text-filter-' + filter_key).trigger('change');
				})
			});
		});
	</script>
	@endBassetBlock
@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}