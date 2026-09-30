<?php

require __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$db = $_ENV['DB_DATABASE'] ?? 'clinicflow';

try {
    $pdo = new PDO("mysql:host=$host:$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Seeding database...\n\n";
    
    // Hash a password
    $password_hash = password_hash('password', PASSWORD_BCRYPT);
    
    // Insert test users
    echo "Seeding users...\n";
    $users = [
        ['name' => 'Admin User', 'email' => 'admin@clinic.test', 'role' => 'admin', 'password' => $password_hash],
        ['name' => 'Dr. John Smith', 'email' => 'doctor1@clinic.test', 'role' => 'doctor', 'password' => $password_hash],
        ['name' => 'Dr. Sarah Johnson', 'email' => 'doctor2@clinic.test', 'role' => 'doctor', 'password' => $password_hash],
        ['name' => 'Reception Staff', 'email' => 'reception@clinic.test', 'role' => 'reception', 'password' => $password_hash],
    ];
    
    $user_ids = [];
    foreach ($users as $u) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, role, password, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$u['name'], $u['email'], $u['role'], $u['password']]);
        $user_ids[] = $pdo->lastInsertId();
    }
    echo "✓ Created " . count($users) . " users\n";
    
    // Insert doctor profiles
    echo "Seeding doctor profiles...\n";
    $doctor_profiles = [
        ['user_id' => $user_ids[1], 'specialization' => 'General Medicine', 'qualification' => 'MBBS', 'license_number' => 'LIC001', 'experience_years' => 10],
        ['user_id' => $user_ids[2], 'specialization' => 'Cardiology', 'qualification' => 'MBBS, DM', 'license_number' => 'LIC002', 'experience_years' => 15],
    ];
    
    foreach ($doctor_profiles as $profile) {
        $stmt = $pdo->prepare("
            INSERT INTO doctor_profiles (user_id, specialization, qualification, license_number, experience_years, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$profile['user_id'], $profile['specialization'], $profile['qualification'], $profile['license_number'], $profile['experience_years']]);
    }
    echo "✓ Created " . count($doctor_profiles) . " doctor profiles\n";
    
    // Insert doctor charges
    echo "Seeding doctor charges...\n";
    $charges = [
        ['doctor_id' => $user_ids[1], 'visit_type' => 'consultation', 'amount' => 500],
        ['doctor_id' => $user_ids[1], 'visit_type' => 'follow-up', 'amount' => 300],
        ['doctor_id' => $user_ids[2], 'visit_type' => 'consultation', 'amount' => 700],
        ['doctor_id' => $user_ids[2], 'visit_type' => 'follow-up', 'amount' => 400],
    ];
    
    foreach ($charges as $charge) {
        $stmt = $pdo->prepare("
            INSERT INTO doctor_charges (doctor_id, visit_type, amount, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$charge['doctor_id'], $charge['visit_type'], $charge['amount']]);
    }
    echo "✓ Created " . count($charges) . " doctor charges\n";
    
    // Insert patients
    echo "Seeding patients...\n";
    $patients = [
        ['mrn' => 'P001', 'name' => 'John Doe', 'phone' => '9876543210', 'date_of_birth' => '1990-01-15', 'gender' => 'male', 'blood_group' => 'O+'],
        ['mrn' => 'P002', 'name' => 'Jane Smith', 'phone' => '9876543211', 'date_of_birth' => '1985-05-20', 'gender' => 'female', 'blood_group' => 'A+'],
        ['mrn' => 'P003', 'name' => 'Robert Brown', 'phone' => '9876543212', 'date_of_birth' => '1978-11-30', 'gender' => 'male', 'blood_group' => 'B+'],
        ['mrn' => 'P004', 'name' => 'Emily Davis', 'phone' => '9876543213', 'date_of_birth' => '1992-08-25', 'gender' => 'female', 'blood_group' => 'AB+'],
        ['mrn' => 'P005', 'name' => 'Michael Wilson', 'phone' => '9876543214', 'date_of_birth' => '1988-03-10', 'gender' => 'male', 'blood_group' => 'O-'],
    ];
    
    $patient_ids = [];
    foreach ($patients as $p) {
        $stmt = $pdo->prepare("
            INSERT INTO patients (mrn, name, phone, date_of_birth, gender, blood_group, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$p['mrn'], $p['name'], $p['phone'], $p['date_of_birth'], $p['gender'], $p['blood_group']]);
        $patient_ids[] = $pdo->lastInsertId();
    }
    echo "✓ Created " . count($patients) . " patients\n";
    
    // Insert medicines
    echo "Seeding medicines...\n";
    $medicines = [
        ['name' => 'Aspirin', 'generic_name' => 'Acetylsalicylic Acid', 'category' => 'Pain Relief', 'strength' => '500mg', 'unit' => 'tablet', 'price' => 25],
        ['name' => 'Paracetamol', 'generic_name' => 'Paracetamol', 'category' => 'Pain Relief', 'strength' => '500mg', 'unit' => 'tablet', 'price' => 20],
        ['name' => 'Amoxicillin', 'generic_name' => 'Amoxicillin', 'category' => 'Antibiotic', 'strength' => '500mg', 'unit' => 'capsule', 'price' => 50],
        ['name' => 'Ibuprofen', 'generic_name' => 'Ibuprofen', 'category' => 'Anti-inflammatory', 'strength' => '400mg', 'unit' => 'tablet', 'price' => 30],
        ['name' => 'Vitamin B12', 'generic_name' => 'Cyanocobalamin', 'category' => 'Supplement', 'strength' => '1000mcg', 'unit' => 'injection', 'price' => 100],
        ['name' => 'Metformin', 'generic_name' => 'Metformin', 'category' => 'Diabetes', 'strength' => '500mg', 'unit' => 'tablet', 'price' => 60],
        ['name' => 'Lisinopril', 'generic_name' => 'Lisinopril', 'category' => 'Blood Pressure', 'strength' => '10mg', 'unit' => 'tablet', 'price' => 75],
        ['name' => 'Atorvastatin', 'generic_name' => 'Atorvastatin', 'category' => 'Cholesterol', 'strength' => '20mg', 'unit' => 'tablet', 'price' => 80],
        ['name' => 'Omeprazole', 'generic_name' => 'Omeprazole', 'category' => 'Digestive', 'strength' => '20mg', 'unit' => 'capsule', 'price' => 40],
        ['name' => 'Cetirizine', 'generic_name' => 'Cetirizine', 'category' => 'Allergy', 'strength' => '10mg', 'unit' => 'tablet', 'price' => 35],
    ];
    
    $medicine_ids = [];
    foreach ($medicines as $m) {
        $stmt = $pdo->prepare("
            INSERT INTO medicines (name, generic_name, category, strength, unit, price, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$m['name'], $m['generic_name'], $m['category'], $m['strength'], $m['unit'], $m['price']]);
        $medicine_ids[] = $pdo->lastInsertId();
    }
    echo "✓ Created " . count($medicines) . " medicines\n";
    
    // Insert medicine stock
    echo "Seeding medicine stock...\n";
    foreach ($medicine_ids as $mid) {
            $stmt = $pdo->prepare("
                INSERT INTO medicine_stock (medicine_id, qty, created_at, updated_at)
                VALUES (?, ?, NOW(), NOW())
            ");
            $stmt->execute([$mid, rand(20, 100)]);
    }
    echo "✓ Created medicine stock records\n";
    
    echo "\n✓ Database seeding complete!\n";
    
    // Summary
    echo "\n" . str_repeat("-", 60) . "\n";
    echo "📊 Summary:\n";
    echo "  • Users created: " . count($users) . "\n";
    echo "  • Doctor profiles: " . count($doctor_profiles) . "\n";
    echo "  • Doctor charges: " . count($charges) . "\n";
    echo "  • Patients: " . count($patients) . "\n";
    echo "  • Medicines: " . count($medicines) . "\n";
    echo str_repeat("-", 60) . "\n";
    echo "\n🔐 Default Login Credentials:\n";
    echo "   Email: admin@clinic.test\n";
    echo "   Password: password\n\n";
    echo "   Email: doctor1@clinic.test\n";
    echo "   Password: password\n\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
