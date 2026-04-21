<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

<!-- Sidebar - Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ URL::to('admin') }}">
    <div class="sidebar-brand-icon">
    <i class="fas fa-tools"></i>
    </div>
    <div class="sidebar-brand-text mx-3">Admin Panel</div>
</a>

<!-- Divider -->
<hr class="sidebar-divider my-0">

<!-- Nav Item - Dashboard -->
<li class="nav-item active">
    <a class="nav-link" href="{{ URL::to('admin') }}">
    <i class="fas fa-fw fa-tachometer-alt"></i>
    <span>Dashboard</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
    <i class="fas fa-fw fa-cog"></i>
    <span>Resources</span>
    </a>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
    <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Resources</h6>
        <a class="collapse-item" href="{{ URL::to('admin/resources') }}">Resources</a>
        <a class="collapse-item" href="{{ URL::to('admin/comments') }}">Comments</a>
        <a class="collapse-item" href="{{ URL::to('admin/flags') }}">Flags</a>
    </div>
    </div>
</li>

<!-- Nav Item - Utilities Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
    <i class="fas fa-fw fa-wrench"></i>
    <span>Surveys</span>
    </a>
    <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Surveys</h6>
            <a class="collapse-item" href="{{ URL::to('admin/surveys') }}">Surveys</a>
            <a class="collapse-item" href="{{ URL::to('admin/survey_questions') }}">Survey Results</a>
            <a class="collapse-item" href="{{ URL::to('admin/survey_time') }}">Survey Settings</a>
        </div>
    </div>
</li>

<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
    <i class="fas fa-fw fa-folder"></i>
    <span>Pages</span>
    </a>
    <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
    <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Pages & News</h6>
        <a class="collapse-item" href="{{ URL::to('admin/pages') }}">Pages</a>
        <a class="collapse-item" href="{{ URL::to('admin/news') }}">News</a>
    </div>
    </div>
</li>

<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#glossary" aria-expanded="true" aria-controls="collapsePages">
        <i class="fas fa-fw fa-folder"></i>
        <span>Glossary</span>
    </a>
    <div id="glossary" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Glossary</h6>
            <a class="collapse-item" href="{{ URL::to('admin/glossary_subjects') }}">Subjects</a>
        </div>
    </div>
</li>

<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#reports" aria-expanded="true" aria-controls="collapsePages">
    <i class="fas fa-fw fa-folder"></i>
    <span>Reports</span>
    </a>
    <div id="reports" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
    <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Reports</h6>
        <a class="collapse-item" href="{{ URL::to('admin/reports/impact_report') }}">Impact Report</a>
        <a class="collapse-item" href="{{ URL::to('admin/reports/downloads') }}">Downloads</a>
        <a class="collapse-item" href="{{ URL::to('admin/analytics/reports/glossary') }}">Glossary View</a>
        <a class="collapse-item" href="{{ URL::to('admin/analytics/reports/sitewide') }}">Resource View</a>
        <div class="collapse-divider"></div>
        <h6 class="collapse-header">Analytics</h6>
        <a class="collapse-item" href="{{ URL::to('admin/analytics/sitewide?is_bot=2') }}">Sitewide Analytics</a>
        <a class="collapse-item" href="{{ URL::to('admin/analytics/resource?is_bot=2') }}">Resource Analytics</a>
        <a class="collapse-item" href="{{ URL::to('admin/analytics/glossary?is_bot=2') }}">Glossary Analytics</a>
        <a class="collapse-item" href="{{ URL::to('admin/analytics/user') }}">User Analytics</a>
        <a class="collapse-item" href="{{ URL::to('admin/analytics') }}">DDL Analytics</a>
    </div>
    </div>
</li>

<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#settings" aria-expanded="true" aria-controls="collapsePages">
    <i class="fas fa-fw fa-folder"></i>
    <span>Settings</span>
    </a>
    <div id="settings" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
    <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Settings</h6>
        <a class="collapse-item" href="{{ URL::to('admin/subscribers') }}">Subscribers</a>
        <a class="collapse-item" href="{{ URL::to('admin/contacts') }}">Contacts</a>
        <a class="collapse-item" href="{{ URL::to('admin/menu') }}">Menu</a>
        <a class="collapse-item" href="{{ URL::to('admin/taxonomy') }}">Taxonomys</a>
        <a class="collapse-item" href="{{ URL::to('admin/taxonomy/subject-areas') }}">Subject Area</a>
        <a class="collapse-item" href="{{ URL::to('admin/taxonomy/resource-types') }}">Resource Type</a>
        <a class="collapse-item" href="{{ URL::to('admin/taxonomy/literacy-levels') }}">Literacy Level</a>
        <a class="collapse-item" href="{{ URL::to('admin/vocabulary') }}">Vocabulary</a>
        <a class="collapse-item" href="{{ URL::to('admin/settings') }}">Configurations</a>
    </div>
    </div>
</li>

<!-- Nav Item - Charts -->
<li class="nav-item">
    <a class="nav-link" href="{{ URL::to('admin/users') }}">
    <i class="fas fa-fw fa-chart-area"></i>
    <span>Users</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider d-none d-md-block">

<!-- Nav Item - Translation -->
<li class="nav-item">
    <a class="nav-link" href="{{ URL::to('translations') }}">
        <i class="fas fa-language"></i>
        <span>Translation manager</span></a>
</li>

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>

</ul>
<!-- End of Sidebar -->
