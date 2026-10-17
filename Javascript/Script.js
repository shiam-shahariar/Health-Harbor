/* HealthHarbor client-side validation and AJAX search. Server validation
   remains authoritative for all submitted data. */

console.log("HealthHarbor JS connected");

/* ---------- helper ---------- */
function setError(id, msg) {
    var el = document.getElementById(id);
    if (el) el.innerHTML = msg;
}
function clearErrors(ids) {
    ids.forEach(function (id) { setError(id, ""); });
}

/* ---------- Register ---------- */
function validateRegister() {
    clearErrors(["nameError","emailError","phoneError","roleError","passwordError","confirmError","termsError"]);
    var name = document.getElementById("name").value.trim();
    var email = document.getElementById("email").value.trim();
    var phone = document.getElementById("phone").value.trim();
    var role = document.getElementById("role").value;
    var pass = document.getElementById("password").value;
    var confirm = document.getElementById("confirm").value;
    var terms = document.getElementById("terms").checked;
    var valid = true;

    if (name.length < 5) { setError("nameError", "Name must be at least 5 characters"); valid = false; }
    if (email.indexOf("@") === -1) { setError("emailError", "Enter a valid email"); valid = false; }
    if (!/^[0-9]{11}$/.test(phone)) { setError("phoneError", "Phone must be exactly 11 digits"); valid = false; }
    if (role === "") { setError("roleError", "Please choose a role"); valid = false; }
    if (pass.length < 5) { setError("passwordError", "Password must be at least 5 characters"); valid = false; }
    if (confirm !== pass) { setError("confirmError", "Passwords do not match"); valid = false; }
    if (!terms) { setError("termsError", "You must accept the Terms & Conditions"); valid = false; }

    return valid; // when valid, the form submits to RegisterValidation.php
}

/* ---------- Login ---------- */
function validateLogin() {
    clearErrors(["emailError","passwordError"]);
    var email = document.getElementById("email").value.trim();
    var pass = document.getElementById("password").value;
    var valid = true;

    if (email.indexOf("@") === -1) { setError("emailError", "Enter a valid email"); valid = false; }
    if (pass.length < 5) { setError("passwordError", "Password must be at least 5 characters"); valid = false; }

    return valid; // when valid, the form submits to LoginValidation.php
}

/* ---------- Change password ---------- */
function validatePassword() {
    clearErrors(["currentError","newpassError","confirmpassError"]);
    var cur = document.getElementById("current").value;
    var np = document.getElementById("newpass").value;
    var cp = document.getElementById("confirmpass").value;
    var valid = true;

    if (cur.length < 1) { setError("currentError", "Enter your current password"); valid = false; }
    if (np.length < 5) { setError("newpassError", "New password must be at least 5 characters"); valid = false; }
    if (cp !== np) { setError("confirmpassError", "Passwords do not match"); valid = false; }

    return valid;
}

function validateProfile() {
    clearErrors(["pnameError"]);
    var name = document.getElementById("pname").value.trim();
    var valid = true;
    if (name.length < 5) { setError("pnameError", "Name must be at least 5 characters"); valid = false; }
    return valid;
}
function validateBooking() {
    clearErrors(["doctorError","dateError","slotError"]);
    var doctor = document.getElementById("doctor_id").value;
    var date   = document.getElementById("date").value;
    var slot   = document.getElementById("slot").value;
    var valid = true;
    if (doctor === "") { setError("doctorError", "Please choose a doctor"); valid = false; }
    if (date === "")   { setError("dateError", "Please pick a date"); valid = false; }
    if (slot === "")   { setError("slotError", "Please choose a time slot"); valid = false; }
    return valid;
}
function validateReview() {
    clearErrors(["rdoctorError","ratingError"]);
    var doc = document.getElementById("rdoctor").value;
    var rating = document.getElementById("rating").value;
    var valid = true;
    if (doc === "")    { setError("rdoctorError", "Please choose a doctor"); valid = false; }
    if (rating === "") { setError("ratingError", "Please choose a rating"); valid = false; }
    return valid;
}
function validateAvailability() {
    clearErrors(["adateError","startError","endError"]);
    var date  = document.getElementById("adate").value;
    var start = document.getElementById("start").value;
    var end   = document.getElementById("end").value;
    var valid = true;
    if (date === "")  { setError("adateError", "Pick a date"); valid = false; }
    if (start === "") { setError("startError", "Pick a start time"); valid = false; }
    if (end === "")   { setError("endError", "Pick an end time"); valid = false; }
    if (start !== "" && end !== "" && end <= start) { setError("endError", "End must be after start"); valid = false; }
    return valid;
}
function validateNotes() {
    clearErrors(["apptError","notesError"]);
    var appt = document.getElementById("appt").value;
    var notes = document.getElementById("notes").value.trim();
    var valid = true;
    if (appt === "")     { setError("apptError", "Select an appointment"); valid = false; }
    if (notes.length < 3){ setError("notesError", "Please write some notes"); valid = false; }
    return valid;
}
function validateSpecialty() {
    clearErrors(["specnameError"]);
    var name = document.getElementById("specname").value.trim();
    var valid = true;
    if (name.length < 2) { setError("specnameError", "Enter a specialty name"); valid = false; }
    return valid;
}

/* ---------- AJAX doctor search ----------
   Sends the search term to SearchController.php, receives a JSON list
   of doctors, and rebuilds the #searchResults table without reloading. */
function esc(str) {
    return String(str == null ? "" : str)
        .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}
function searchDoctors() {
    var q   = document.getElementById("q").value;
    var box = document.getElementById("searchResults");
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status !== 200) {
                box.innerHTML = "<p class='error'>Doctor search is temporarily unavailable. Please try again.</p>";
                return;
            }
            var doctors;
            try {
                doctors = JSON.parse(this.responseText);
            } catch (error) {
                box.innerHTML = "<p class='error'>The search response was invalid. Please try again.</p>";
                return;
            }
            if (!Array.isArray(doctors)) {
                box.innerHTML = "<p class='error'>Doctor search is temporarily unavailable. Please try again.</p>";
                return;
            }
            if (doctors.length === 0) {
                box.innerHTML = "<p class='empty'>No doctors found.</p>";
                return;
            }
            var html = "<table class='data'><tr><th>Doctor</th><th>Specialty</th><th>Rating</th><th>Action</th></tr>";
            for (var i = 0; i < doctors.length; i++) {
                var rating = doctors[i].avg_rating
                    ? (doctors[i].avg_rating + " ⭐ (" + doctors[i].review_count + ")")
                    : "No ratings yet";
                html += "<tr><td>" + esc(doctors[i].name) + "</td>"
                      + "<td>" + esc(doctors[i].specialty) + "</td>"
                      + "<td>" + esc(rating) + "</td>"
                      + "<td><a class='btn small' href='book_appointment.php?doctor_id="
                      + encodeURIComponent(doctors[i].id) + "'>Book</a></td></tr>";
            }
            html += "</table>";
            box.innerHTML = html;
        }
    };
    xhttp.onerror = function () {
        box.innerHTML = "<p class='error'>Doctor search is temporarily unavailable. Please try again.</p>";
    };
    xhttp.open("POST", "../Controller/SearchController.php", true);
    xhttp.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    xhttp.send("q=" + encodeURIComponent(q));
}
