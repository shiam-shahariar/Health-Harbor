Health Harbor — Doctor Appointment & Clinic Booking System


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