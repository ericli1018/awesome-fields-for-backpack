{{-- Date Range Backpack CRUD filter --}}
<li filter-name="{{ $filter->name }}"
    filter-type="{{ $filter->type }}"
    filter-key="{{ $filter->key }}"
	class="nav-item dropdown {{ Request::get($filter->name)?'active':'' }}">
	<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $filter->label }} <span class="caret"></span></a>
	<div class="dropdown-menu p-0">
		<div class="form-group backpack-filter mb-0">
			<div class="input-group date">
		        <div class="input-group-prepend">
		          <span class="input-group-text"><i class="la la-calendar"></i></span>
		        </div>
		        <input class="form-control pull-right"
		        		id="datepicker-{{ $filter->key }}"
		        		type="text"
						@if ($filter->currentValue)
							value="{{ $filter->currentValue }}"
						@endif
		        		>
		        <div class="input-group-append datepicker-clear-button">
		          <a class="input-group-text" href=""><i class="la la-times"></i></a>
		        </div>
		    </div>
		</div>
	</div>
</li>

{{-- ########################################### --}}
{{-- Extra CSS and JS for this particular filter --}}

{{-- FILTERS EXTRA CSS  --}}
{{-- push things in the after_styles section --}}

@push('crud_list_styles')
    <link rel="stylesheet" href="{{ basset('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker3.min.css') }}">
	<style>
		.input-group.date {
			width: 320px;
			max-width: 100%;
		}
	</style>
@endpush


{{-- FILTERS EXTRA JS --}}
{{-- push things in the after_scripts section --}}

@push('crud_list_scripts')
	<!-- include select2 js-->
	<script src="{{ basset('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js') }}"></script>
	@php $language = $filter->options['language'] ?? \App::getLocale(); @endphp
	@if ($language !== 'en')
	<script charset="UTF-8" src="{{ basset('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/locales/bootstrap-datepicker.'.$language.'.min.js') }}"></script>
	@endif
    @bassetBlock('backpack/crud/filters/date.js')
    <script>
		jQuery(document).ready(function($) {
			$('li[filter-type=date]').not('[filter-enabled]').each(function () {
				$(this).attr('filter-enabled', '');
				var filter_name = $(this).attr('filter-name');
                var filter_key = $(this).attr('filter-key');
				var _self = $(this);

				var dateInput = $('#datepicker-' + filter_key).datepicker({
					autoclose: true,
					format: 'yyyy-mm-dd',
					todayHighlight: true,
					language: '{{ $language }}',
				})
				.on('changeDate', function(e) {
					var d = new Date(e.date);
					if (isNaN(d.getFullYear())) {
						var value = '';
					} else {
						var value = d.getFullYear() + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);
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
					// console.log('date filter cleared');
					$(_self).removeClass('active');
					$('#datepicker-' + filter_key).datepicker('update', '');
					$('#datepicker-' + filter_key).trigger('changeDate');
				});

				// datepicker clear button
				$(_self).find(".datepicker-clear-button").click(function(e) {
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