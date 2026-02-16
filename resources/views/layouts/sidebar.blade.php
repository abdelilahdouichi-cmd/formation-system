<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <i class="fas fa-graduation-cap brand-image img-circle elevation-3" style="opacity: .8"></i>
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    @php
        $isCoreSystemOpen = request()->routeIs('admin.dashboard');
        $isTrainingManagementOpen = request()->routeIs('admin.formations.*')
            || request()->routeIs('admin.participants.*');
        $isPersonnelOpen = request()->routeIs('admin.users.*');
        $isCertificationOpen = request()->routeIs('admin.qualifications.*')
            || request()->routeIs('admin.diplomes.*')
            || request()->routeIs('admin.licences.*')
            || request()->routeIs('admin.justificatifs.*');
        $isAdministrationOpen = request()->routeIs('admin.users.*');
        $isReportsOpen = request()->routeIs('admin.reports.*');
    @endphp

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-header">Core System</li>
                <li class="nav-item {{ $isCoreSystemOpen ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isCoreSystemOpen ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Core System
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('view dashboard')
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>
                        @endcan

                        @can('view notifications')
                            <li class="nav-item">
                                {{-- MISSING ROUTE: suggested route name admin.notifications.index, controller App\Http\Controllers\Admin\NotificationController@index --}}
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Notifications</p>
                                </a>
                            </li>
                        @endcan

                        @can('view activity logs')
                            <li class="nav-item">
                                {{-- MISSING ROUTE: suggested route name admin.activity-logs.index, controller App\Http\Controllers\Admin\ActivityLogController@index --}}
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Activity Logs</p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>

                <li class="nav-header">Training Management</li>
                @canany(['view formations', 'create formations', 'manage training'])
                    <li class="nav-item {{ $isTrainingManagementOpen ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $isTrainingManagementOpen ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chalkboard-teacher"></i>
                            <p>
                                Training Management
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @canany(['view formations', 'create formations'])
                                <li class="nav-item {{ request()->routeIs('admin.formations.*') ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ request()->routeIs('admin.formations.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>
                                            Formations
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @can('view formations')
                                            <li class="nav-item">
                                                <a href="{{ route('admin.formations.index') }}" class="nav-link {{ request()->routeIs('admin.formations.index') ? 'active' : '' }}">
                                                    <i class="far fa-dot-circle nav-icon"></i>
                                                    <p>List formations</p>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('create formations')
                                            <li class="nav-item">
                                                <a href="{{ route('admin.formations.create') }}" class="nav-link {{ request()->routeIs('admin.formations.create') ? 'active' : '' }}">
                                                    <i class="far fa-dot-circle nav-icon"></i>
                                                    <p>Planifier formation</p>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('execute formations')
                                            <li class="nav-item">
                                                {{-- MISSING ROUTE: suggested route name admin.formations.execute, controller App\Http\Controllers\Formation\FormationController@execute (requires formation parameter) --}}
                                                <a href="#" class="nav-link">
                                                    <i class="far fa-dot-circle nav-icon"></i>
                                                    <p>Exécuter formation</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany

                            @can('view classes')
                                <li class="nav-item">
                                    {{-- MISSING ROUTE: suggested route name admin.formations.classes.index, controller App\Http\Controllers\Formation\ClasseController@index (requires formation parameter) --}}
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Classes</p>
                                    </a>
                                </li>
                            @endcan

                            @can('view levels')
                                <li class="nav-item">
                                    {{-- MISSING ROUTE: suggested route name admin.levels.index, controller App\Http\Controllers\Formation\LevelController@index --}}
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Niveaux</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <li class="nav-header">Personnel</li>
                @canany(['view users', 'view instructors', 'view examiners', 'view participants'])
                    <li class="nav-item {{ $isPersonnelOpen ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $isPersonnelOpen ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Personnel
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('view users')
                                <li class="nav-item">
                                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Personnel list</p>
                                    </a>
                                </li>
                            @endcan

                            @can('view instructors')
                                <li class="nav-item">
                                    {{-- MISSING ROUTE: suggested route name admin.instructors.index, controller App\Http\Controllers\Formation\InstructorController@index --}}
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Instructeurs</p>
                                    </a>
                                </li>
                            @endcan

                            @can('view examiners')
                                <li class="nav-item">
                                    {{-- MISSING ROUTE: suggested route name admin.examiners.index, controller App\Http\Controllers\Formation\ExaminerController@index --}}
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Examinateurs</p>
                                    </a>
                                </li>
                            @endcan

                            @can('view participants')
                                <li class="nav-item">
                                    {{-- MISSING ROUTE: suggested route name admin.participants.index, controller App\Http\Controllers\Formation\ParticipantController@index (existing nested route requires formation parameter) --}}
                                    <a href="#" class="nav-link {{ request()->routeIs('admin.participants.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Participants</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <li class="nav-header">Certification</li>
                @canany(['view qualifications', 'view diplomes', 'view licences', 'view justificatifs'])
                    <li class="nav-item {{ $isCertificationOpen ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $isCertificationOpen ? 'active' : '' }}">
                            <i class="nav-icon fas fa-certificate"></i>
                            <p>
                                Certification
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('view qualifications')
                                <li class="nav-item">
                                    {{-- MISSING ROUTE: suggested route name admin.qualifications.index, controller App\Http\Controllers\Formation\QualificationController@index (existing nested route requires formation parameter) --}}
                                    <a href="#" class="nav-link {{ request()->routeIs('admin.qualifications.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Qualifications</p>
                                    </a>
                                </li>
                            @endcan

                            @can('view diplomes')
                                <li class="nav-item">
                                    {{-- MISSING ROUTE: suggested route name admin.diplomes.index, controller App\Http\Controllers\Formation\DiplomeController@index (existing nested route requires formation parameter) --}}
                                    <a href="#" class="nav-link {{ request()->routeIs('admin.diplomes.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Diplômes</p>
                                    </a>
                                </li>
                            @endcan

                            @can('view licences')
                                <li class="nav-item">
                                    {{-- MISSING ROUTE: suggested route name admin.licences.index, controller App\Http\Controllers\Formation\LicenceController@index (existing nested route requires formation parameter) --}}
                                    <a href="#" class="nav-link {{ request()->routeIs('admin.licences.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Licences</p>
                                    </a>
                                </li>
                            @endcan

                            @can('view justificatifs')
                                <li class="nav-item">
                                    {{-- MISSING ROUTE: suggested route name admin.justificatifs.index, controller App\Http\Controllers\Formation\JustificatifController@index (existing nested route requires formation parameter) --}}
                                    <a href="#" class="nav-link {{ request()->routeIs('admin.justificatifs.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Documents justificatifs</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <li class="nav-header">Administration</li>
                @canany(['view users', 'view roles', 'view permissions', 'manage system settings'])
                    <li class="nav-item {{ $isAdministrationOpen ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $isAdministrationOpen ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>
                                Administration
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('view users')
                                <li class="nav-item">
                                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Users</p>
                                    </a>
                                </li>
                            @endcan

                            @can('view roles')
                                <li class="nav-item">
                                    {{-- MISSING ROUTE: suggested route name admin.roles.index, controller App\Http\Controllers\Admin\RoleController@index --}}
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Roles</p>
                                    </a>
                                </li>
                            @endcan

                            @can('view permissions')
                                <li class="nav-item">
                                    {{-- MISSING ROUTE: suggested route name admin.permissions.index, controller App\Http\Controllers\Admin\PermissionController@index --}}
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Permissions</p>
                                    </a>
                                </li>
                            @endcan

                            @can('manage system settings')
                                <li class="nav-item">
                                    {{-- MISSING ROUTE: suggested route name admin.settings.index, controller App\Http\Controllers\Admin\SystemSettingController@index --}}
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>System settings</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <li class="nav-header">Reports</li>
                @canany(['view reports', 'export reports'])
                    <li class="nav-item {{ $isReportsOpen ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $isReportsOpen ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>
                                Reports
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('view formation reports')
                                <li class="nav-item">
                                    <a href="{{ route('admin.reports.dashboard') }}" class="nav-link {{ request()->routeIs('admin.reports.dashboard') || request()->routeIs('admin.reports.formation') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Formation reports</p>
                                    </a>
                                </li>
                            @endcan

                            @can('view licence reports')
                                <li class="nav-item">
                                    <a href="{{ route('admin.reports.licence-rate') }}" class="nav-link {{ request()->routeIs('admin.reports.licence-rate') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Licence reports</p>
                                    </a>
                                </li>
                            @endcan

                            @can('export reports')
                                <li class="nav-item">
                                    {{-- MISSING ROUTE: suggested route name admin.reports.export, controller App\Http\Controllers\Formation\ReportController@exportFormationReport (requires formation parameter) --}}
                                    <a href="#" class="nav-link {{ request()->routeIs('admin.reports.export') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Export PDF/Excel</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
            </ul>
        </nav>
    </div>
</aside>
