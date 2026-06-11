<?php
mysqli_report(MYSQLI_REPORT_OFF);
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_port = getenv('DB_PORT') ?: '3306';
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_name = getenv('DB_NAME');
$db_prefix = getenv('DB_PREFIX') ?: 'pp_';

if (!$db_user || !$db_name) {
    echo "Database environment variables not fully set. Skipping auto-initialization.\n";
    exit(0);
}

echo "Checking database connection...\n";
$conn = mysqli_init();
if (!$conn) {
    echo "mysqli_init failed\n";
    exit(1);
}

// Enforce SSL if host contains tidbcloud.com
if (strpos($db_host, 'tidbcloud.com') !== false || getenv('DB_SSL') === 'true') {
    $conn->ssl_set(NULL, NULL, NULL, NULL, NULL);
    $connect_res = @$conn->real_connect($db_host, $db_user, $db_pass, $db_name, $db_port, NULL, MYSQLI_CLIENT_SSL);
} else {
    $connect_res = @$conn->real_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
}

if (!$connect_res) {
    echo "Database connection failed: " . mysqli_connect_error() . "\n";
    exit(1);
}

// Check if settings table exists
$result = $conn->query("SHOW TABLES LIKE '{$db_prefix}settings'");
if ($result && $result->num_rows > 0) {
    echo "Database tables already exist. Skipping initialization.\n";
    $conn->close();
    exit(0);
}

echo "Initializing database tables...\n";

// 1. Load database.sql
$sql = file_get_contents(__DIR__ . '/install/database.sql');
if ($sql === false) {
    echo "Failed to load install/database.sql\n";
    exit(1);
}
$sql = str_replace("__PREFIX__", $db_prefix, $sql);
$queries = array_filter(array_map('trim', explode(";", $sql)));
foreach ($queries as $query) {
    if (!empty($query)) {
        if (!$conn->query($query)) {
            echo "Error running query: " . $conn->error . "\n";
            echo "Failed Query: " . substr($query, 0, 200) . "...\n";
        }
    }
}

// 2. Load currency.sql
$sqlCurrency = file_get_contents(__DIR__ . '/install/currency.sql');
if ($sqlCurrency !== false) {
    $sqlCurrency = str_replace("INSERT INTO `currency`", "INSERT INTO `{$db_prefix}currency`", $sqlCurrency);
    if ($conn->multi_query($sqlCurrency)) {
        do {
            $conn->store_result();
        } while ($conn->more_results() && $conn->next_result());
    } else {
        echo "Error loading currency data: " . $conn->error . "\n";
    }
}

// 3. Load timezone.sql
$sqlTimezone = file_get_contents(__DIR__ . '/install/timezone.sql');
if ($sqlTimezone !== false) {
    $sqlTimezone = str_replace("INSERT INTO `timezone`", "INSERT INTO `{$db_prefix}timezone`", $sqlTimezone);
    if ($conn->multi_query($sqlTimezone)) {
        do {
            $conn->store_result();
        } while ($conn->more_results() && $conn->next_result());
    } else {
        echo "Error loading timezone data: " . $conn->error . "\n";
    }
}

// 4. Insert default admin
$adminName = getenv('ADMIN_NAME') ?: 'Admin';
$adminEmail = getenv('ADMIN_EMAIL') ?: 'admin@example.com';
$adminUser = getenv('ADMIN_USER') ?: 'admin';
$adminPass = getenv('ADMIN_PASS') ?: 'admin123456';

$hashedPass = password_hash($adminPass, PASSWORD_BCRYPT);
$insertAdmin = "INSERT INTO `{$db_prefix}admins` (name, email, username, password) VALUES 
               ('" . $conn->real_escape_string($adminName) . "', 
                '" . $conn->real_escape_string($adminEmail) . "', 
                '" . $conn->real_escape_string($adminUser) . "', 
                '" . $conn->real_escape_string($hashedPass) . "')";
if ($conn->query($insertAdmin)) {
    echo "Default administrator account created successfully.\n";
    echo "Username: {$adminUser}\n";
    echo "Password: (hidden)\n";
} else {
    echo "Failed to create administrator account: " . $conn->error . "\n";
}

// 5. Insert settings
$insertSettings = "INSERT INTO `{$db_prefix}settings` (site_name) VALUES ('--')";
$conn->query($insertSettings);

echo "Database initialization completed successfully!\n";
$conn->close();
?>