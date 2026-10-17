<?php
/*
 * Model/db.php
 * Same pattern as the Lab3 db class: connection() returns a mysqli
 * connection, and each query has its own method. Only the database
 * name is "healthharbor".
 */
class db {

    function connection() {
        $db_host     = "localhost";
        $db_user     = "root";
        $db_password = "";
        $db_name     = "healthharbor";
        $connection  = new mysqli($db_host, $db_user, $db_password, $db_name);
        if ($connection->connect_error) {
            throw new RuntimeException("Database connection failed: ".$connection->connect_error);
        }
        $connection->set_charset("utf8mb4");
        return $connection;
    }

    // fetch a single user by email (used for login and duplicate check)
    function getUserByEmail($connection, $email) {
        $email = $connection->real_escape_string($email);
        $sql   = "SELECT * FROM users WHERE email = '".$email."'";
        return $connection->query($sql);
    }
    function hasCompletedAppointment($connection, $patient_id, $doctor_id) {
        $patient_id = (int)$patient_id; $doctor_id = (int)$doctor_id;
        $sql = "SELECT id FROM appointments
                WHERE patient_id = ".$patient_id." AND doctor_id = ".$doctor_id." AND status = 'Completed' LIMIT 1";
        $result = $connection->query($sql);
        return $result && $result->num_rows > 0;
    }

    // insert a new user; $password must already be hashed
    function signup($connection, $name, $email, $password, $phone, $role, $specialty, $status) {
        $name     = $connection->real_escape_string($name);
        $email    = $connection->real_escape_string($email);
        $password = $connection->real_escape_string($password);
        $phone    = $connection->real_escape_string($phone);
        $role     = $connection->real_escape_string($role);
        $status   = $role === "doctor" ? "pending" : "active";
        $status   = $connection->real_escape_string($status);
        // specialty is NULL for patients, a quoted string for doctors
        $specialtySql = ($specialty === null || $specialty === "")
            ? "NULL"
            : "'".$connection->real_escape_string($specialty)."'";

        $sql = "INSERT INTO users (name, email, password, phone, role, specialty, status)
                VALUES ('".$name."', '".$email."', '".$password."', '".$phone."', '".$role."', ".$specialtySql.", '".$status."')";
        return $connection->query($sql);
    }

    // list all doctors still waiting for admin approval
    function getPendingDoctors($connection) {
        $sql = "SELECT id, name, email, specialty FROM users WHERE role = 'doctor' AND status = 'pending'";
        return $connection->query($sql);
    }

    // approve/reject a doctor by changing their status
    function updateUserStatus($connection, $id, $status) {
        $id     = (int)$id;
        $status = $connection->real_escape_string($status);
        $sql = "UPDATE users SET status = '".$status."' WHERE id = ".$id." AND role = 'doctor'";
        return $connection->query($sql);
    }

    // approved doctors a patient can book
    function getActiveDoctors($connection) {
        $sql = "SELECT id, name, specialty FROM users WHERE role = 'doctor' AND status = 'active' ORDER BY name";
        return $connection->query($sql);
    }

    function getSpecialtyByName($connection, $name) {
        $name = $connection->real_escape_string($name);
        return $connection->query("SELECT id FROM specialties WHERE name = '".$name."' LIMIT 1");
    }

    // Available slots are tied to an active doctor and exclude already-booked slots.
    function getAvailableSlots($connection, $doctor_id, $date) {
        $doctor_id = (int)$doctor_id;
        $date = $connection->real_escape_string($date);
        $sql = "SELECT s.id, s.start_time, s.end_time
                FROM slots s JOIN users d ON d.id = s.doctor_id
                WHERE s.doctor_id = ".$doctor_id." AND d.role = 'doctor' AND d.status = 'active'
                  AND s.slot_date = '".$date."' AND s.is_booked = 0
                  AND (s.slot_date > CURDATE() OR (s.slot_date = CURDATE() AND s.start_time > CURTIME()))
                ORDER BY s.start_time";
        return $connection->query($sql);
    }

    // Book an available slot atomically so concurrent requests cannot claim it twice.
    function insertAppointment($connection, $patient_id, $doctor_id, $slot_id, $date, $reason) {
        $patient_id = (int)$patient_id;
        $doctor_id  = (int)$doctor_id;
        $slot_id = (int)$slot_id;
        $date = $connection->real_escape_string($date);
        $reason = $connection->real_escape_string($reason);
        $connection->begin_transaction();
        $slot = $connection->query(
            "SELECT s.slot_date, s.start_time FROM slots s
             JOIN users d ON d.id = s.doctor_id
             WHERE s.id = ".$slot_id." AND s.doctor_id = ".$doctor_id." AND s.slot_date = '".$date."'"
             ." AND d.role = 'doctor' AND d.status = 'active' AND s.is_booked = 0
             AND (s.slot_date > CURDATE() OR (s.slot_date = CURDATE() AND s.start_time > CURTIME()))
             FOR UPDATE"
        );
        $patient = $connection->query(
            "SELECT id FROM users WHERE id = ".$patient_id." AND role = 'patient' AND status = 'active' LIMIT 1"
        );
        if (!$slot || $slot->num_rows !== 1 || !$patient || $patient->num_rows !== 1) {
            $connection->rollback();
            return false;
        }
        $details = $slot->fetch_assoc();
        $inserted = $connection->query(
            "INSERT INTO appointments (patient_id, doctor_id, slot_id, appt_date, appt_time, reason, status)
             VALUES (".$patient_id.", ".$doctor_id.", ".$slot_id.", '".$details["slot_date"]."',
                     '".$details["start_time"]."', '".$reason."', 'Pending')"
        );
        $booked = $inserted && $connection->query("UPDATE slots SET is_booked = 1 WHERE id = ".$slot_id." AND is_booked = 0");
        if (!$booked || $connection->affected_rows !== 1) {
            $connection->rollback();
            return false;
        }
        $connection->commit();
        return true;
    }

    // one patient's appointments, with the doctor's name/specialty
    function getPatientAppointments($connection, $patient_id) {
        $patient_id = (int)$patient_id;
        $sql = "SELECT a.id, a.appt_date, a.appt_time, a.status, u.name AS doctor_name, u.specialty
                FROM appointments a JOIN users u ON a.doctor_id = u.id
                WHERE a.patient_id = ".$patient_id." ORDER BY a.id DESC";
        return $connection->query($sql);
    }

    // one doctor's requests, with the patient's name
    function getDoctorAppointments($connection, $doctor_id) {
        $doctor_id = (int)$doctor_id;
        $sql = "SELECT a.id, a.appt_date, a.appt_time, a.status, a.reason, u.name AS patient_name
                FROM appointments a JOIN users u ON a.patient_id = u.id
                WHERE a.doctor_id = ".$doctor_id." ORDER BY a.id DESC";
        return $connection->query($sql);
    }

    // patient cancels their own appointment
    function cancelAppointment($connection, $id, $patient_id) {
        $id = (int)$id; $patient_id = (int)$patient_id;
        $connection->begin_transaction();
        $current = $connection->query(
            "SELECT slot_id FROM appointments WHERE id = ".$id." AND patient_id = ".$patient_id."
             AND status IN ('Pending','Accepted') FOR UPDATE"
        );
        if (!$current || $current->num_rows !== 1) {
            $connection->rollback();
            return false;
        }
        $slot_id = $current->fetch_assoc()["slot_id"];
        $updated = $connection->query(
            "UPDATE appointments SET status = 'Cancelled', slot_id = NULL
             WHERE id = ".$id." AND patient_id = ".$patient_id." AND status IN ('Pending','Accepted')"
        );
        $updatedRows = $connection->affected_rows;
        $released = $slot_id === null ? true
            : $connection->query("UPDATE slots SET is_booked = 0 WHERE id = ".(int)$slot_id);
        if (!$updated || $updatedRows !== 1 || !$released) {
            $connection->rollback();
            return false;
        }
        $connection->commit();
        return true;
    }

    // doctor accepts / rejects / completes an appointment
    function setAppointmentStatus($connection, $id, $doctor_id, $status) {
        $id = (int)$id; $doctor_id = (int)$doctor_id;
        if (!in_array($status, array("Accepted", "Rejected", "Completed"), true)) return false;
        $expected = $status === "Completed" ? "Accepted" : "Pending";
        $status = $connection->real_escape_string($status);
        $connection->begin_transaction();
        $current = $connection->query(
            "SELECT slot_id FROM appointments WHERE id = ".$id." AND doctor_id = ".$doctor_id."
             AND status = '".$expected."' FOR UPDATE"
        );
        if (!$current || $current->num_rows !== 1) {
            $connection->rollback();
            return false;
        }
        $slot_id = $current->fetch_assoc()["slot_id"];
        $release = $status === "Rejected";
        $updated = $connection->query(
            "UPDATE appointments SET status = '".$status."'".($release ? ", slot_id = NULL" : "")."
             WHERE id = ".$id." AND doctor_id = ".$doctor_id." AND status = '".$expected."'"
        );
        $updatedRows = $connection->affected_rows;
        $released = !$release || $slot_id === null
            || $connection->query("UPDATE slots SET is_booked = 0 WHERE id = ".(int)$slot_id);
        if (!$updated || $updatedRows !== 1 || !$released) {
            $connection->rollback();
            return false;
        }
        $connection->commit();
        return true;
    }

    // ----- profile / account -----
    function getUserById($connection, $id) {
        $id = (int)$id;
        return $connection->query("SELECT * FROM users WHERE id = ".$id);
    }
    function updateProfile($connection, $id, $name, $phone, $specialty) {
        $id    = (int)$id;
        $name  = $connection->real_escape_string($name);
        $phone = $connection->real_escape_string($phone);
        $specialtySql = ($specialty === "" || $specialty === null) ? "NULL" : "'".$connection->real_escape_string($specialty)."'";
        $sql = "UPDATE users SET name = '".$name."', phone = '".$phone."', specialty = ".$specialtySql." WHERE id = ".$id;
        return $connection->query($sql);
    }
    function updatePassword($connection, $id, $hash) {
        $id = (int)$id;
        $hash = $connection->real_escape_string($hash);
        return $connection->query("UPDATE users SET password = '".$hash."' WHERE id = ".$id);
    }
    function deleteUser($connection, $id) {
        $id = (int)$id;
        $connection->begin_transaction();
        $appointmentsDeleted = $connection->query(
            "DELETE FROM appointments WHERE patient_id = ".$id." OR doctor_id = ".$id
        );
        $userDeleted = $appointmentsDeleted
            && $connection->query("DELETE FROM users WHERE id = ".$id." AND role != 'admin'");
        if (!$userDeleted || $connection->affected_rows !== 1) {
            $connection->rollback();
            return false;
        }
        $connection->commit();
        return true;
    }

    // ----- admin: manage users -----
    function getAllUsers($connection) {
        return $connection->query("SELECT id, name, email, role, status FROM users WHERE role != 'admin' ORDER BY role, name");
    }
    function setUserStatusById($connection, $id, $status) {
        $id = (int)$id;
        $status = $connection->real_escape_string($status);
        return $connection->query("UPDATE users SET status = '".$status."' WHERE id = ".$id." AND role != 'admin'");
    }

    // ----- admin: appointments & stats -----
    function getAllAppointments($connection) {
        $sql = "SELECT a.id, a.appt_date, a.appt_time, a.status,
                       p.name AS patient_name, d.name AS doctor_name, d.specialty
                FROM appointments a
                JOIN users p ON a.patient_id = p.id
                JOIN users d ON a.doctor_id = d.id
                ORDER BY a.id DESC";
        return $connection->query($sql);
    }
    function getStats($connection) {
        $stats = array();
        $stats["appointments"] = $connection->query("SELECT COUNT(*) AS c FROM appointments")->fetch_assoc()["c"];
        $stats["doctors"]      = $connection->query("SELECT COUNT(*) AS c FROM users WHERE role = 'doctor'")->fetch_assoc()["c"];
        $stats["patients"]     = $connection->query("SELECT COUNT(*) AS c FROM users WHERE role = 'patient'")->fetch_assoc()["c"];
        $stats["specialties"]  = $connection->query("SELECT COUNT(*) AS c FROM specialties")->fetch_assoc()["c"];
        return $stats;
    }

    // ----- AJAX doctor search (approved doctors + their average rating) -----
    function searchDoctors($connection, $q) {
        $q = $connection->real_escape_string($q);
        $sql = "SELECT u.id, u.name, u.specialty,
                       ROUND(AVG(r.rating),1) AS avg_rating, COUNT(r.id) AS review_count
                FROM users u
                LEFT JOIN reviews r ON r.doctor_id = u.id
                WHERE u.role = 'doctor' AND u.status = 'active'
                  AND (u.name LIKE '%".$q."%' OR u.specialty LIKE '%".$q."%')
                GROUP BY u.id, u.name, u.specialty
                ORDER BY u.name";
        return $connection->query($sql);
    }

    // ----- doctor: prescription notes -----
    function getDoctorNotableAppointments($connection, $doctor_id) {
        $doctor_id = (int)$doctor_id;
        $sql = "SELECT a.id, a.appt_date, a.appt_time, a.status, p.name AS patient_name
                FROM appointments a JOIN users p ON a.patient_id = p.id
                WHERE a.doctor_id = ".$doctor_id." AND a.status = 'Accepted'
                ORDER BY a.id DESC";
        return $connection->query($sql);
    }
    function saveNotes($connection, $id, $doctor_id, $notes) {
        $id = (int)$id; $doctor_id = (int)$doctor_id;
        $notes = $connection->real_escape_string($notes);
        // saving notes also marks the visit Completed
        $sql = "UPDATE appointments SET notes = '".$notes."', status = 'Completed'
                WHERE id = ".$id." AND doctor_id = ".$doctor_id." AND status = 'Accepted'";
        $result = $connection->query($sql);
        return $result && $connection->affected_rows === 1;
    }

    // ----- patient: rate & review -----
    function getDoctorsForReview($connection, $patient_id) {
        $patient_id = (int)$patient_id;
        $sql = "SELECT DISTINCT d.id, d.name FROM appointments a
                JOIN users d ON a.doctor_id = d.id
                WHERE a.patient_id = ".$patient_id." AND a.status = 'Completed'
                ORDER BY d.name";
        return $connection->query($sql);
    }
    function insertReview($connection, $patient_id, $doctor_id, $rating, $review) {
        $patient_id = (int)$patient_id; $doctor_id = (int)$doctor_id; $rating = (int)$rating;
        $review = $connection->real_escape_string($review);
        $sql = "INSERT INTO reviews (patient_id, doctor_id, rating, review)
                VALUES (".$patient_id.", ".$doctor_id.", ".$rating.", '".$review."')";
        return $connection->query($sql);
    }

    // ----- admin: specialties -----
    function getSpecialties($connection) {
        return $connection->query("SELECT id, name FROM specialties ORDER BY name");
    }
    function addSpecialty($connection, $name) {
        $name = $connection->real_escape_string($name);
        return $connection->query("INSERT INTO specialties (name) VALUES ('".$name."')");
    }
    function deleteSpecialty($connection, $id) {
        $id = (int)$id;
        $result = $connection->query(
            "DELETE s FROM specialties s
             WHERE s.id = ".$id."
               AND NOT EXISTS (SELECT 1 FROM users u WHERE u.specialty = s.name)"
        );
        return $result && $connection->affected_rows === 1;
    }

    // ----- reviews (viewing) -----
    function getDoctorReviews($connection, $doctor_id) {
        $doctor_id = (int)$doctor_id;
        $sql = "SELECT r.rating, r.review, r.created_at, p.name AS patient_name
                FROM reviews r JOIN users p ON r.patient_id = p.id
                WHERE r.doctor_id = ".$doctor_id." ORDER BY r.id DESC";
        return $connection->query($sql);
    }

    // ----- doctor availability (slots) -----
    function addSlot($connection, $doctor_id, $date, $start, $end) {
        $doctor_id = (int)$doctor_id;
        $date  = $connection->real_escape_string($date);
        $start = $connection->real_escape_string($start);
        $end   = $connection->real_escape_string($end);
        $sql = "INSERT INTO slots (doctor_id, slot_date, start_time, end_time)
                VALUES (".$doctor_id.", '".$date."', '".$start."', '".$end."')";
        return $connection->query($sql);
    }
    function getDoctorSlots($connection, $doctor_id) {
        $doctor_id = (int)$doctor_id;
        return $connection->query("SELECT id, slot_date, start_time, end_time, is_booked FROM slots WHERE doctor_id = ".$doctor_id." ORDER BY slot_date, start_time");
    }
    function deleteSlot($connection, $id, $doctor_id) {
        $id = (int)$id; $doctor_id = (int)$doctor_id;
        $result = $connection->query("DELETE FROM slots WHERE id = ".$id." AND doctor_id = ".$doctor_id." AND is_booked = 0");
        return $result && $connection->affected_rows === 1;
    }
}
?>
