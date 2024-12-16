{{-- Dropdown Backpack CRUD filter --}}
<li filter-name="{{ $filter->name }}"
    filter-type="{{ $filter->type }}"
    filter-key="{{ $filter->key }}"
	class="nav-item dropdown {{ Request::get($filter->name)?'active':'' }}">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $filter->label }} <span class="caret"></span></a>
    <ul class="dropdown-menu">
		<a class="dropdown-item" parameter="{{ $filter->name }}" dropdownkey="" href="">-</a>
		<div role="separator" class="dropdown-divider"></div>
		@if (is_array($filter->values) && count($filter->values))
			@foreach($filter->values as $key => $value)
				@if ($key === 'dropdown-separator')
					<div role="separator" class="dropdown-divider"></div>
				@else
					<a  class="dropdown-item {{ ($filter->isActive() && $filter->currentValue == $key)?'active':'' }}"
						parameter="{{ $filter->name }}"
						href=""
						dropdownkey="{{ $key }}"
						>{{ $value }}</a>
				@endif
			@endforeach
		@endif
    </ul>
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

@push('crud_list_scripts')
	<script src="{{ basset('https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js') }}" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    @bassetBlock('backpack/crud/filters/dropdown.js')
	<script>
		jQuery(document).ready(function($) {
			$('li[filter-type=dropdown]').not('[data-filter-enabled]').each(function () {
				$(this).attr('filter-enabled', '');
				var filter_name = $(this).attr('filter-name');
                var filter_key = $(this).attr('filter-key');
				var _self = $(this);

				$(_self).find(".dropdown-menu a").click(function(e) {
					e.preventDefault();

					var value = $(this).attr('dropdownkey');
					var parameter = $(this).attr('parameter');

					// behaviour for ajax table
					var ajax_table = $("#crudTable").DataTable();
					var current_url = ajax_table.ajax.url();
					var new_url = addOrUpdateUriParameter(current_url, parameter, value);

					// replace the datatables ajax url with new_url and reload it
					new_url = normalizeAmpersand(new_url.toString());
					ajax_table.ajax.url(new_url).load();

					// add filter to URL
					crud.updateUrl(new_url);

					// mark this filter as active in the navbar-filters
					// mark dropdown items active accordingly
					if (URI(new_url).hasQuery(filter_name, true)) {
						$(_self).removeClass('active').addClass('active');
						$(_self).find(".dropdown-menu a").removeClass('active');
						$(this).addClass('active');
					}
					else
					{
						$(_self).trigger("filter:clear");
					}
				});

				// clear filter event (used here and by the Remove all filters button)
				$(_self).on('filter:clear', function(e) {
					// console.log('dropdown filter cleared');
					$(_self).removeClass('active');
					$(_self).find(".dropdown-menu a").removeClass('active');
				});
			});
		});
	</script>
	@endBassetBlock
@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}