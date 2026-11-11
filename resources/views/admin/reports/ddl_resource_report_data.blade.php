@if ($type == 'unique_resources')
    @include('admin.reports.partials.unique_resources')
@elseif ($type == 'subject_coverage')
    @include('admin.reports.partials.subject_coverage')
@elseif ($type == 'translated_adopted')
    @include('admin.reports.partials.translated_adopted')
@elseif ($type == 'partner_contributions')
    @include('admin.reports.partials.partner_contributions')
@endif
