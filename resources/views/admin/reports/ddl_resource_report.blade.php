@extends('admin.layout')
@section('admin.content')
    <style>
        .report-panel,
        .report-card {
            border-radius: 12px;
        }

        .report-card-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .report-card {
            flex: 0 1 160px;
            min-width: 140px;
            max-width: 180px;
            padding: 10px 12px;
            border: 1px solid #e3e6f0;
            background: #fff;
        }

        .report-card-label {
            color: #5a5c69;
            font-size: 13px;
            line-height: 1.2;
        }

        .report-card-value {
            margin-top: 2px;
            color: #4e73df;
            font-size: 17px;
            font-weight: 700;
        }

        .report-card-language {
            color: #858796;
            font-size: 12px;
        }

        .report-section {
            margin-bottom: 18px;
        }

        .report-card-scroll {
            max-height: 300px;
            padding-right: 5px;
            overflow-y: auto;
        }

        @media (max-width: 480px) {
            .report-card {
                flex: 1 1 calc(50% - 10px);
                min-width: 120px;
                max-width: none;
            }
        }
    </style>

    <div class="content-wrapper">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ URL::to('admin') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">DDL Resource Report</li>
            </ol>

            <div class="card report-panel mb-3">
                <div class="card-header">
                    <i class="fa fa-list"></i> DDL Resource Report
                </div>
                <div class="card-body py-2">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="report-type">Report type</label>
                            <select class="form-control" id="report-type" data-url="{{ route('ddl-report.data') }}">
                                <option value="unique_resources">Unique uploaded resources</option>
                                <option value="subject_coverage">Subject area coverage</option>
                                <option value="translated_adopted">Translated and adopted resources</option>
                                <option value="partner_contributions">Partner-contributed resources</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="report-language">Language</label>
                            <select class="form-control" id="report-language">
                                <option value="all">All languages</option>
                                @foreach (LaravelLocalization::getSupportedLocales() as $code => $properties)
                                    <option value="{{ $code }}">{{ $properties['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3 mb-0">
                            <label for="report-date-from">From date</label>
                            <input class="form-control" type="date" id="report-date-from">
                        </div>
                        <div class="form-group col-md-3 mb-0">
                            <label for="report-date-to">To date</label>
                            <input class="form-control" type="date" id="report-date-to">
                        </div>
                    </div>
                </div>
            </div>

            <div id="report-results"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const reportType = $('#report-type');
            const reportLanguage = $('#report-language');
            const reportDateFrom = $('#report-date-from');
            const reportDateTo = $('#report-date-to');
            const reportResults = $('#report-results');

            function loadReport() {
                const dateFrom = reportDateFrom.val();
                const dateTo = reportDateTo.val();

                if ((dateFrom && !dateTo) || (!dateFrom && dateTo)) {
                    reportResults.html(
                        '<div class="alert alert-warning">Please select both From date and To date.</div>');
                    return;
                }

                reportResults.html('<div class="card report-panel"><div class="card-body">Loading...</div></div>');

                $.get(reportType.data('url'), {
                        type: reportType.val(),
                        language: reportLanguage.val(),
                        date_from: dateFrom,
                        date_to: dateTo
                    })
                    .done(function(response) {
                        reportResults.html(response);
                    })
                    .fail(function() {
                        reportResults.html(
                            '<div class="alert alert-danger">The report could not be loaded.</div>');
                    });
            }

            reportDateFrom.on('change', function() {
                reportDateTo.attr('min', this.value);
            });

            reportDateTo.on('change', function() {
                reportDateFrom.attr('max', this.value);
            });

            reportType.add(reportLanguage).add(reportDateFrom).add(reportDateTo).on('change', loadReport);
            loadReport();
        });
    </script>
@endpush
