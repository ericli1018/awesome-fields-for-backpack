{{-- Date Range Backpack CRUD filter --}}

@php
    $filter->options['date_range_options'] = array_replace_recursive([
		'timePicker' => false,
    	'alwaysShowCalendars' => true,
        'autoUpdateInput' => true,
        'startDate' => \Carbon\Carbon::now()->toDateTimeString(),
        'endDate' => \Carbon\Carbon::now()->toDateTimeString(),
        'ranges' => [
            trans('backpack::crud.today') =>  [\Carbon\Carbon::now()->startOfDay()->toDateTimeString(), \Carbon\Carbon::now()->endOfDay()->toDateTimeString()],
            trans('backpack::crud.yesterday') => [\Carbon\Carbon::now()->subDay()->startOfDay()->toDateTimeString(), \Carbon\Carbon::now()->subDay()->endOfDay()->toDateTimeString()],
            trans('backpack::crud.last_7_days') => [\Carbon\Carbon::now()->subDays(6)->startOfDay()->toDateTimeString(), \Carbon\Carbon::now()->toDateTimeString()],
            trans('backpack::crud.last_30_days') => [\Carbon\Carbon::now()->subDays(29)->startOfDay()->toDateTimeString(), \Carbon\Carbon::now()->toDateTimeString()],
            trans('backpack::crud.this_month') => [\Carbon\Carbon::now()->startOfMonth()->toDateTimeString(), \Carbon\Carbon::now()->endOfMonth()->toDateTimeString()],
            trans('backpack::crud.last_month') => [\Carbon\Carbon::now()->subMonth()->startOfMonth()->toDateTimeString(), \Carbon\Carbon::now()->subMonth()->endOfMonth()->toDateTimeString()]
        ],
        'locale' => [
            'firstDay' => 0,
            'format' => config('backpack.base.default_date_format'),
            'applyLabel'=> trans('backpack::crud.apply'),
            'cancelLabel'=> trans('backpack::crud.cancel'),
            'customRangeLabel' => trans('backpack::crud.custom_range')
        ],


    ], $filter->options['date_range_options'] ?? []);

    //if filter is active we override developer init values
    if($filter->currentValue) {
	    $dates = (array)json_decode($filter->currentValue);
        $filter->options['date_range_options']['startDate'] = $dates['from'];
        $filter->options['date_range_options']['endDate'] = $dates['to'];
    }

@endphp


<li filter-name="{{ $filter->name }}"
    filter-type="{{ $filter->type }}"
    filter-key="{{ $filter->key }}"
	class="nav-item dropdown {{ Request::get($filter->name)?'active':'' }}">
	<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $filter->label }} <span class="caret"></span></a>
	<div class="dropdown-menu p-0">
		<div class="form-group backpack-filter mb-0">
			<div class="input-group date">
		        <span class="input-group-text"><i class="la la-calendar"></i></span>
		        <input class="form-control pull-right"
		        		id="daterangepicker-{{ $filter->key }}"
		        		type="text"
                        data-bs-daterangepicker="{{ json_encode($filter->options['date_range_options'] ?? []) }}"
		        		>
		        <a class="input-group-text daterangepicker-{{ $filter->key }}-clear-button" href=""><i class="la la-times"></i></a>
		    </div>
		</div>
	</div>
</li>

{{-- ########################################### --}}
{{-- Extra CSS and JS for this particular filter --}}

{{-- FILTERS EXTRA CSS  --}}
{{-- push things in the after_styles section --}}

@push('crud_list_styles')
    <link rel="stylesheet" href="{{ basset('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.css') }}">
	<style>
		.input-group.date {
			width: 320px;
			max-width: 100%; }
		.daterangepicker.dropdown-menu {
			z-index: 3001!important;
		}
	</style>
@endpush


{{-- FILTERS EXTRA JS --}}
{{-- push things in the after_scripts section --}}

@push('crud_list_scripts')
    <script src="{{ basset('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/moment.min.js') }}"></script>
	<script src="{{ basset('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js') }}"></script>
	
	@bassetBlock('backpack/crud/filters/date_range.js')
  	<script>
		function applyDateRangeFilter(_self, filter_name, start, end) {

  			if (start && end) {
  				var dates = {
					'from': start.format('YYYY-MM-DD HH:mm:ss'),
					'to': end.format('YYYY-MM-DD HH:mm:ss')
                };

                var value = JSON.stringify(dates);
  			} else {
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
			} else {
				$(_self).trigger('filter:clear');
			}
  		}

		jQuery(document).ready(function($) {
			if ($('html')[0].lang.length > 0)
			{
				moment.locale($('html')[0].lang);
			}
			//moment.locale('{{app()->getLocale()}}');

			$('li[filter-type=date_range]').not('[filter-enabled]').each(function () {
				$(this).attr('filter-enabled', '');
				var filter_name = $(this).attr('filter-name');
                var filter_key = $(this).attr('filter-key');
				var _self = $(this);

				var dateRangeInput = $('#daterangepicker-' + filter_key);

				$config = dateRangeInput.data('bs-daterangepicker');

				$ranges = $config.ranges;
				$config.ranges = {};

				//if developer configured ranges we convert it to moment() dates.
				for (var key in $ranges) {
					if ($ranges.hasOwnProperty(key)) {
						$config.ranges[key] = $.map($ranges[key], function($val) {
							return moment($val);
						});
					}
				}

				$config.startDate = moment($config.startDate);
				$config.endDate = moment($config.endDate);

				dateRangeInput.daterangepicker($config);

				dateRangeInput.on('apply.daterangepicker', function(ev, picker) {
					applyDateRangeFilter(_self, filter_name, picker.startDate, picker.endDate);
				});
				$(_self).on('hide.bs.dropdown', function () {
					if($('.daterangepicker').is(':visible'))
					return false;
				});
				$(_self).on('filter:clear', function(e) {
					//if triggered by remove filters click just remove active class,no need to send ajax
					$(_self).removeClass('active');
				});
				// datepicker clear button
				$(_self).find(".daterangepicker-clear-button").click(function(e) {
					e.preventDefault();
					applyDateRangeFilter(_self, filter_name, null, null);
				});
			});
		});
	</script>
	@endBassetBlock
@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}