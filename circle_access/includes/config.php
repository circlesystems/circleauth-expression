<?php

//Check the table exp_actions to get the CircleAccess ID

define('CIRCLE_ACCESS_ACT', 46);

//API Keys
define('APP_NAME', 'ExpressionEngine');
define('APP_KEY', 'app2DvSsfdcyxGVdt9C7XEa6YoJ5QeUBuBb1');
define('READ_KEY', 'read9MM6UQMm9onURE13zaaBC3v8ErAjbyqiE');
define('WRITE_KEY', 'writeAqofyyPQ1gUnDURVFGsnurPoscqrA58eu');

define('CIRCLEAUTH_VERSION', '1.0');
define('CIRCLEAUTH_PATH', dirname(__FILE__));
define('CIRCLEAUTH_CONSOLE_URL', 'https://console.gocircle.ai/');
define('CIRCLEAUTH_LOGIN_URL', 'https://circleauth.gocircle.ai/login/');
define('CIRCLEAUTH_DOMAIN', 'https://circleauth.gocircle.ai/');
define('CIRCLEAUTH_EMAIL_INFO', 'info@circleauth.gocircle.ai');
define('CIRCLE_DASHBOARD_URL', 'https://console.unicauth.com/dashboard/login_email/index?appKey='.APP_KEY);

//Redirect URL after login
define('REDIRECT_URL', '/admin.php?/cp/members');

//Default ExpressionEngine members roles
define('SUPERADMIN', 1);
define('BANNED', 2);
define('GUESTS', 3);
define('PENDING', 4);
define('MEMBERS', 5);

//Default new member role.
define('NEW_MEMBER_DEFAULT_ROLE', SUPERADMIN);
