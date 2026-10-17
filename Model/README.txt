MODEL LAYER :

Model/db.php contains the mysqli connection and database operations.
HealthHarbor uses the healthharbor database created by healthharbor.sql.
Configure host, username, password, or database name in db.php if your
local XAMPP installation does not use the defaults.

Tables:
- users          Patients, doctors, and administrators
- specialties    Managed list used for doctor registration and profiles
- slots          Doctor availability, including whether each slot is booked
- appointments   Patient bookings linked to a single availability slot
- reviews        Patient ratings and feedback for doctors

Import healthharbor.sql through phpMyAdmin after starting MySQL in XAMPP.
