{{-- Example Backpack CRUD filter --}}
<li filter-name="{{ $filter->name }}"
    filter-type="{{ $filter->type }}"
    filter-key="{{ $filter->key }}"
	class="nav-item dropdown {{ Request::get($filter->name)?'active':'' }}">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $filter->label }} <span class="caret"></span></a>
    <div class="dropdown-menu p-0">
		<div class="form-group backpack-filter mb-0">
			<?php
				$from = '';
				$to = '';
				if ($filter->currentValue) {
					$range = (array) json_decode($filter->currentValue);
					$from = $range['from'];
					$to = $range['to'];
				}
			?>
			<div class="input-group">
				<input class="form-control pull-right from"
						type="number"
							@if($from)
								value = "{{ $from }}"
							@endif
							@if(array_key_exists('label_from', $filter->options))
								placeholder = "{{ $filter->options['label_from'] }}"
							@else
								placeholder = "min value"
							@endif
						>
						<input class="form-control pull-right to"
								type="number"
							@if($to)
								value = "{{ $to }}"
							@endif
							@if(array_key_exists('label_to', $filter->options))
								placeholder = "{{ $filter->options['label_to'] }}"
							@else
								placeholder = "max value"
							@endif
						>
				<a class="input-group-text range-filter-clear-button" href=""><i class="la la-times"></i></a>
			</div>
		</div>
    </div>
  </li>


{{-- ########################################### --}}
{{-- Extra CSS and JS for this particular filter --}}

{{-- FILTERS EXTRA CSS  --}}
{{-- push things in the after_styles section --}}

    {{-- @push('crud_list_styles')
        <!-- no css -->
    @endpush --}}


{{-- FILTERS EXTRA JS --}}
{{-- push things in the after_scripts section --}}


{{-- FILTER JAVASCRIPT CHECKLIST

- redirects to a new URL for standard DataTables
- replaces the search URL for ajax DataTables
- users have a way to clear this filter (and only this filter)
- filter:clear event on li[filter-name], which is called by the "Remove all filters" button, clears this filter;

END OF FILTER JAVSCRIPT CHECKLIST --}}

@push('crud_list_scripts')
    @bassetBlock('backpack/crud/filters/range.js')
	<script>
		jQuery(document).ready(function($) {
			$('li[filter-type=range]').not('[filter-enabled]').each(function () {
				$(this).attr('filter-enabled', '');
				var filter_name = $(this).attr('filter-name');
                var filter_key = $(this).attr('filter-key');
				var _self = $(this);

				$(_self).find(".from, .to").change(function(e) {
					e.preventDefault();
					var from = $(_self).find(".from").val();
					var to = $(_self).find(".to").val();
					if (from || to) {
						var range = {
							'from': from,
							'to': to
						};
						var value = JSON.stringify(range);
					} else {
						//this change to empty string,because addOrUpdateUriParameter method just judgment string
						var value = '';
					}
					var parameter = filter_name;

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
						$(_self).removeClass('active').addClass('active');
					}
				});

				$(_self).on('filter:clear', function(e) {
					$(_self).removeClass('active');
					$(_self).find(".from").val("");
					$(_self).find(".to").val("");
					$(_self).find(".to").trigger('change');
				});

				// range clear button
				$(_self).find(".range-filter-clear-button").click(function(e) {
					e.preventDefault();

					$(_self).trigger('filter:clear');
				});
			});
		});
	</script>
    @endBassetBlock
@endpush

{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}