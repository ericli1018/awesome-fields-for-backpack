{{-- Simple Backpack CRUD filter --}}
<li filter-name="{{ $filter->name }}"
    filter-type="{{ $filter->type }}"
    filter-key="{{ $filter->key }}"
	class="nav-item {{ Request::get($filter->name)?'active':'' }}">
    <a class="nav-link" href=""
		parameter="{{ $filter->name }}"
    	>{{ $filter->label }}</a>
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
    @bassetBlock('backpack/crud/filters/simple.js')
    <script>
		jQuery(document).ready(function($) {
            $('li[filter-type=simple]').not('[data-filter-enabled]').each(function () {
				$(this).attr('data-filter-enabled', '');
				var filter_name = $(this).attr('filter-name');
                var filter_key = $(this).attr('filter-key');
                var _self = $(this);

                $(_self).find("a").click(function(e) {
                    e.preventDefault();

                    var parameter = $(this).attr('parameter');

                    // behaviour for ajax table
                    var ajax_table = $("#crudTable").DataTable();
                    var current_url = ajax_table.ajax.url();

                    if (URI(current_url).hasQuery(parameter)) {
                        var new_url = URI(current_url).removeQuery(parameter, true);
                    } else {
                        var new_url = URI(current_url).addQuery(parameter, true);
                    }

                    new_url = normalizeAmpersand(new_url.toString());

                    // replace the datatables ajax url with new_url and reload it
                    ajax_table.ajax.url(new_url).load();

                    // add filter to URL
                    crud.updateUrl(new_url);

                    // mark this filter as active in the navbar-filters
                    if (URI(new_url).hasQuery(parameter, true)) {
                        $(_self).removeClass('active').addClass('active');
                        $('#remove_filters_button').removeClass('invisible');
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
                });
            });
		});
	</script>
    @endBassetBlock
@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}