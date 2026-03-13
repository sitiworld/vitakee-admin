<?php
// Assume session_start() has already been called and $traducciones is available.

// 1. MENU CONFIGURATION
// Defines the entire navigation structure for all user roles.
$menuItems = [
    0 => [ // Administrator
        [
            'title' => $traducciones['navigation_section_title'],
            'items' => [
                [
                    "url" => "dashboard_administrator",
                    "title" => $traducciones['dashboard_title'],
                    "icon" => "mdi mdi-home-outline",
                ],
            ],
        ],
        [
            'title' => $traducciones['health_data_management_section_title'],
            'items' => [
                [
                    "url" => "biomarkers",
                    "title" => $traducciones['biomarkers_title'],
                    "icon" => "mdi mdi-flask-outline",
                ],
                [
                    "url" => "test_panels",
                    "title" => $traducciones['test_panels_title'],
                    "icon" => "mdi mdi-view-list",
                ],
            ],
        ],
        [
            'title' => $traducciones['user_management_section_title'],
            'items' => [
                [
                    "url" => "users_view",
                    "title" => $traducciones['users_view_title'],
                    "icon" => "mdi mdi-account",
                ],
                [
                    "url" => "specialists_view",
                    "title" => $traducciones['page_title_specialists'],
                    "icon" => "mdi mdi-doctor",
                ],
                [
                    "url" => "administrators_view",
                    "title" => $traducciones['page_title_admins'],
                    "icon" => "mdi mdi-chess-queen",
                ],
                [
                    "url" => "verification_requests_view",
                    "title" => $traducciones['verification_requests_title'] ?? 'Verification Requests',
                    "icon" => "mdi mdi-shield-check-outline",
                ],
            ],
        ],
        [
            'title' => $traducciones['admin_management_section_title'],
            'items' => [
                [
                    "url" => "backups_view",
                    "title" => $traducciones['backups_view_title'],
                    "icon" => "mdi mdi-backup-restore",
                ],
                [
                    "url" => "audit_log_view",
                    "title" => $traducciones['audit_log_view_title'],
                    "icon" => "mdi mdi-file-document-outline",
                ],
                [
                    "url" => "session_management_view",
                    "title" => $traducciones['session_log_view_title'],
                    "icon" => "mdi mdi-history",
                ],
                [
                    "url" => "countries_view",
                    "title" => $traducciones['csvFilenamePrefix_countries'],
                    "icon" => "mdi mdi-flag",
                ],
                [
                    "url" => "cities_view",
                    "title" => $traducciones['cities_view_view_title'],
                    "icon" => "mdi mdi-city", // ícono alusivo a ciudades
                ],
                [
                    "url" => "states_view",
                    "title" => $traducciones['states_view_view_title'],
                    "icon" => "mdi mdi-map-marker-radius", // ícono alusivo a estados/regiones
                ],
                [
                    "url" => "title_view",
                    "title" => $traducciones['title_view_view_title'],
                    "icon" => "mdi mdi-view-grid-outline",
                ],
                [
                    "url" => "specialty_view",
                    "title" => $traducciones['specialty_view_view_title'],
                    "icon" => "mdi mdi-feature-search-outline",
                ],
            ],
        ],
    ],
    1 => [ // Specialist
        [
            'title' => $traducciones['navigation_section_title'],
            'items' => [
                [
                    "url" => "dashboard_specialist",
                    "title" => $traducciones['dashboard_title'],
                    "icon" => "mdi mdi-home-outline",
                ],
                [
                    "url" => "service_requests",
                    "title" => $traducciones['service_requests_title'],
                    "icon" => "mdi mdi-bell-ring",
                ],
                // [
                //     "url" => "users_records",
                //     "title" => $traducciones['users_records_title'],
                //     "icon" => "mdi mdi-account-group",
                // ],
            ],
        ],
        [
            'title' => $traducciones['account_section_title'],
            'items' => [
                [
                    "url" => "my_profile",
                    "title" => $traducciones['profile_user_title'],
                    "icon" => "mdi mdi-account-circle",
                ],
            ],
        ],
    ],
    2 => [ // User
        [
            'title' => $traducciones['navigation_section_title'],
            'items' => [
                [
                    "url" => "dashboard",
                    "title" => $traducciones['dashboard_title'],
                    "icon" => "mdi mdi-home-outline",
                ],
            ],
        ],
        [
            'title' => $traducciones['user_record_section_title'],
            'items' => [
                [
                    "url" => "body_composition",
                    "title" => $traducciones['body_composition_title'],
                    "icon" => "mdi mdi-human-male-height",
                ],
                [
                    "url" => "lipid_profile",
                    "title" => $traducciones['lipid_profile_title'],
                    "icon" => "mdi mdi-water-outline",
                ],
                [
                    "url" => "energy_metabolism_view",
                    "title" => $traducciones['energy_metabolism_title'],
                    "icon" => "mdi mdi-flash-outline",
                ],
                [
                    "url" => "renal_function",
                    "title" => $traducciones['renal_function_title'],
                    "icon" => "mdi mdi-filter-variant",
                ],
            ],
        ],
        [
            'title' => $traducciones['user_services_section_title'],
            'items' => [
                [
                    "url" => "expert_review",
                    "title" => $traducciones['user_expert_review_title'],
                    "icon" => "mdi mdi-message-reply-text-outline",
                ],
                [
                    "url" => "user_request_panel",
                    "title" => $traducciones['requests_panel_title'],
                    "icon" => "mdi mdi-inbox-arrow-down-outline",
                ],
            ],
        ],
        [
            'title' => $traducciones['account_section_title'],
            'items' => [
                [
                    "url" => "my_profile",
                    "title" => $traducciones['profile_user_title'],
                    "icon" => "mdi mdi-account-circle",
                ],
            ],
        ],
    ],
];

// Maps role names from your system to the keys in the $menuItems array.
$roleMap = [
    'Administrator' => 0,
    'Specialist' => 1,
    'User' => 2,
];


// 2. NORMALIZE USER ROLE
// This handles different session keys and ensures the role is a lowercase string.
$rawRole = $_SESSION['role_user'] ?? $_SESSION['roles_user'] ?? '';
$user_role = strtolower((string) $rawRole);

?>

<div class="app-menu">

    <div class="logo-box">
        <a href="/dashboard" class="logo logo-dark text-center">
            <span class="logo-sm">
                <img src="public/assets/images/logo-sm2.svg" alt="" height="23">
            </span>
            <span class="logo-lg">
                <img src="public/assets/images/logo-index.png" alt="" height="35">
            </span>
        </a>
        <a href="/dashboard" class="logo logo-light text-center">
            <span class="logo-sm">
                <img src="public/assets/images/logo-sm2.svg" alt="" height="23">
            </span>
            <span class="logo-lg">
                <img src="public/assets/images/logo-index.png" alt="" height="35">
            </span>
        </a>
    </div>

    <div class="scrollbar">

        <div class="user-box text-center">
            <img src="public/assets/images/users/user-1.jpg" alt="user-img" title="User"
                class="rounded-circle avatar-md">
            <div class="dropdown">
                <a href="javascript:void(0);" class="dropdown-toggle h5 mb-1 d-block" data-bs-toggle="dropdown">
                    <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Guest'); ?>
                </a>
                <div class="dropdown-menu user-pro-dropdown">
                    <a href="profile_user" class="dropdown-item notify-item">
                        <i class="fe-user me-1"></i><span><?= $traducciones['my_account'] ?? 'My Account' ?></span>
                    </a>
                    <a href="logout" class="dropdown-item notify-item">
                        <i class="fe-log-out me-1"></i><span><?= $traducciones['logout'] ?? 'Logout' ?></span>
                    </a>
                </div>
            </div>
            <p class="text-muted mb-0"><?php echo htmlspecialchars(ucfirst($user_role)); ?></p>
        </div>

        <ul class="menu">

            <?php
            // 3. DYNAMICALLY GENERATE MENU
            
            // Capitalize the role name to match the keys in $roleMap (e.g., 'administrator' -> 'Administrator')
            $capitalizedRole = ucfirst($user_role);

            // Check if the current user's role is defined in our map
            if (isset($roleMap[$capitalizedRole])) {
                $roleKey = $roleMap[$capitalizedRole];

                // Check if a menu configuration exists for this role key
                if (isset($menuItems[$roleKey])) {
                    $userMenu = $menuItems[$roleKey];

                    // Loop through each section (e.g., 'Navigation', 'User Management')
                    foreach ($userMenu as $section) {
                        // Print the section title
                        echo '<li class="menu-title mt-2">' . htmlspecialchars($section['title']) . '</li>';

                        // Loop through each item in the section
                        foreach ($section['items'] as $item) {
                            echo '<li class="menu-item">';
                            echo '  <a href="' . htmlspecialchars($item['url']) . '" class="menu-link">';
                            echo '    <span class="menu-icon"><i class="' . htmlspecialchars($item['icon']) . ' font-20"></i></span>';
                            echo '    <span class="menu-text">' . htmlspecialchars($item['title']) . '</span>';
                            echo '  </a>';
                            echo '</li>';
                        }
                    }
                }
            }
            ?>

        </ul>
        <div class="clearfix"></div>

    </div>
</div>