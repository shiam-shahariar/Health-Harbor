# Health Harbor — Doctor Appointment & Clinic Booking System


# PROJECT OVERVIEW :

HealthHarbor is a web-based clinic appointment booking system. Patients
can find approved doctors, book available appointment times, and manage
their bookings. Doctors manage their availability and appointment
requests. Administrators manage doctors, specialties, user accounts, and
appointments.

The project uses PHP for its server-side pages and controllers, MySQL for
application data, and HTML, CSS, and JavaScript for the user interface.
Doctor search uses AJAX to display matching results without reloading the
page. The application is designed to run locally through XAMPP.

# FEATURES :

* Patient features:
1. Register and sign in as a patient.
2. Search approved doctors by name or specialty and view their average
   rating and review count.
3. View future availability and book an open time slot with an optional
   reason for the appointment.
4. View appointment dates, times, and statuses. Cancel pending or accepted
   appointments.
5. Submit a rating from 1 to 5 and an optional written review after
   completing an appointment with the doctor.
6. Update profile details, change the password, or delete the account.

* Doctor features:
1. Register with a specialty from the administrator-managed list. New
   doctor accounts must be approved before the doctor can sign in.
2. Add future availability slots and delete unbooked slots.
3. View appointment requests and accept or reject pending appointments.
   Accepting changes the status to Accepted. Rejecting releases the slot.
4. Complete an accepted appointment, or save consultation notes to mark it
   Completed.
5. View patient reviews and the average rating.
6. Update profile details, change the password, or delete the account.

* Administrator features:
1. Approve or reject doctors who are waiting for verification.
2. Add or delete specialties. A specialty assigned to a doctor cannot be
   deleted.
3. Activate or deactivate patient and doctor accounts, or remove them.
   View all appointments and summary counts for appointments, doctors,
   patients, and specialties.
4. Update the administrator profile and password.

* Account and booking rules:
1. Registration accepts patient and doctor roles. Administrator accounts
   are supplied by the sample database; they cannot be created through the
   registration form.
2. Registration requires a name of at least 5 characters, a valid email,
   an 11-digit phone number, a password of at least 5 characters, matching
   password confirmation, and acceptance of the Terms & Conditions.
3. Doctor registration requires a specialty from the current list.
   New patient accounts are active immediately. New doctor accounts are
   pending until an administrator approves them.
4. A booking must use an available future slot belonging to an active
   doctor. The database transaction prevents two patients from booking the
   same slot.
5. Patients can cancel pending or accepted appointments. Doctors can
   accept or reject pending appointments, and complete accepted
   appointments. Cancellation and rejection make the slot available again.
6. Reviews require at least one completed appointment with the doctor.

# FOLDER & FILE RESPONSIBILITIES :

1. View/ contains the public landing page, forms, and role-specific pages.
2. Controller/ contains form handling, validation, authentication,
   authorization, appointment actions, and the doctor-search endpoint.
3. Model/db.php connects to MySQL and contains the application's database
   operations.
4. DataBase/healthharbor.sql creates the database and tables, and inserts sample
   accounts, specialties, and a completed sample appointment.
5. Design/Style.css contains the page styling.
6. Javascript/Script.js contains client-side form checks and AJAX doctor
   search. PHP performs the server-side checks as well.
7. Model/users.json is a standalone sample data file. The application
   stores and reads its account data from the MySQL users table.

# DATABASE :

* The SQL import creates the healthharbor database with these tables:
- users          Patient, doctor, and administrator accounts.
- specialties    Doctor specialty choices maintained by administrators.
- slots          Doctor availability and whether a slot is booked.
- appointments   Patient bookings, statuses, and consultation notes.
- reviews        Patient ratings and written reviews.

* The database connection is configured in Model/db.php:
- Host: localhost
- Username: root
- Password: blank
- Database: healthharbor

If your local MySQL settings are different, update these values in
Model/db.php before opening the application.

# REQUIREMENTS :

- XAMPP with Apache, MySQL or MariaDB, and PHP.
- PHP's mysqli extension enabled.
- A browser with JavaScript enabled for live doctor search and
  client-side form feedback.
- The supplied healthharbor.sql file.

# RUNNING WITH XAMPP :

1. Copy the Health-Harbor project folder into XAMPP's htdocs directory.
   Keep the folder name Health-Harbor.

   Common locations:
   - Windows: C:\xampp\htdocs\Health-Harbor
   - macOS: /Applications/XAMPP/xamppfiles/htdocs/Health-Harbor
   - Linux: /opt/lampp/htdocs/Health-Harbor

2. Open the XAMPP Control Panel (or XAMPP Manager on macOS) and start
   Apache and MySQL.

3. Open phpMyAdmin at http://localhost/phpmyadmin.

4. Select the Import tab, choose healthharbor.sql from the project folder,
   and run the import. The SQL file creates the healthharbor database,
   tables, and sample data.

5. If your MySQL username, password, host, or database name differs from
   the defaults, update the connection settings in Model/db.php.

6. Open the application at:
   http://localhost/Health-Harbor/View/index.php

Run the application through Apache. Opening a PHP file directly from the
filesystem will not execute its PHP code.

# SAMPLE TEST ACCOUNTS :

* Every sample account seeded by healthharbor.sql uses this password:
 Password: admin123

* Administrator:
- Email: admin@healthharbor.com
- Password: admin123

* Approved doctor:
- Email: rahman@healthharbor.com
- Password: admin123
- Specialty: Cardiology

* Doctor awaiting approval:
- Email: sultana@healthharbor.com
- Password: admin123
- Specialty: Neurology
- Status: Pending. This account cannot sign in until an administrator
  approves it. Use the administrator account to test doctor approval.

* Patient:
- Email: karim@mail.com
- Password: admin123
- The sample data includes a completed appointment with Dr. Rahman, so
  this account can also be used to test the review feature.

These are shared sample credentials for local testing only. Change the
passwords before using the application in any environment with real users.

# SAMPLE DATA AND RESETTING THE DATABASE :

The sample data includes five specialties, four user accounts, and one
completed historical appointment between Karim Ahmed and Dr. Rahman. The
sample appointment enables patient review testing. Its date is in the
past, so it is not an available booking slot.

The SQL seed statements use INSERT IGNORE for sample accounts,
specialties, and the sample appointment. Importing the file again will
not overwrite changes to existing sample accounts or duplicate those
seeded records.

To reset local test data, first back up anything you want to keep. In
phpMyAdmin, drop the healthharbor database and then import
healthharbor.sql again. Dropping the database permanently deletes its
accounts, appointments, and other application data.

# SECURITY AND DATA NOTES :

1. Passwords created through registration are stored as password hashes.
2. Protected pages check the signed-in user's role. Form actions use
   CSRF tokens and validate submitted values on the server.
3. The sample accounts use a public, shared password and are intended for
   local testing only. Do not use these credentials for real accounts.
4. This project is intended for local demonstration and testing. Review
   the application and secure the server and database before exposing it
   to a public network or storing real patient information.
