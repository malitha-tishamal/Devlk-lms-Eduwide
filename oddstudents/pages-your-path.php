<?php
session_start();
require_once '../includes/db-conn.php';

if (!isset($_SESSION['former_student_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['former_student_id'];

$sql2 = "SELECT * FROM former_students WHERE id = ?";
$stmt = $conn->prepare($sql2);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc(); 

$user_id = $_SESSION['former_student_id'];
$summary = "";

// Fetch summary from the separate table
$sql = "SELECT summary FROM summaries WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($summary);
$stmt->fetch();
$stmt->close();

// Fetch about_text
$about_text = '';
$sql = "SELECT about_text FROM about WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($about_text);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Details - EduWide</title>
    <?php include_once("../includes/css-links-inc.php"); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script> 
    <style>
        .profile-header {
            background-color: #0073b1;
            color: white;
            padding: 30px;
        }
        .profile-header img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            margin-right: 20px;
        }
        .profile-header h1 {
            margin-top: 20px;
        }
        .section-header {
            font-size: 1.5em;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        .list-group-item {
            border: none;
            cursor: grab;
        }
        .card-body p {
            margin: 5px 0;
        }
        .btn-custom {
            background-color: #0073b1;
            color: white;
            border-radius: 20px;
        }
        .btn-custom:hover {
            background-color: #005f8c;
        }
        .experience-section, .education-section, .skills-section, .interests-section {
            margin-top: 30px;
        }
        #work-experience-list .list-group-item {
            user-select: none; 
           
        }
         .summary-card {
          border-radius: 10px;
          background-color: #fff;
        }
        .summary-icon {
          background-color: #e6f0ff;
          border-radius: 8px;
          display: inline-block;
    }

    </style>
</head>
<body>

    <?php include_once("../includes/header.php") ?>
    <?php include_once("../includes/formers-sidebar.php") ?>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Home</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item"><a href="">Details</a></li>
                    <li class="breadcrumb-item"><a href="">Your Path</a></li>
                </ol>
            </nav>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <div class="about-section">
                                    <h3 class="section-header d-flex justify-content-between align-items-center">
                                        About
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editAboutModal">Edit</button>
                                    </h3>
                                    <div id="about-text" class="shadow p-3 mb-3 bg-white rounded w-75">
                                        <?= !empty($about_text) ? nl2br(htmlspecialchars($about_text)) : 'Click edit to add your About section.' ?>
                                    </div>
                                </div>

                                <!-- About Modal -->
                                <div class="modal fade" id="editAboutModal" tabindex="-1" aria-labelledby="editAboutModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form class="modal-content" id="edit-about-form">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editAboutModalLabel">Edit About</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <textarea id="about-input" class="form-control w-100" rows="5"><?= htmlspecialchars($about_text) ?></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <script>
                                document.getElementById('edit-about-form').addEventListener('submit', function (e) {
                                    e.preventDefault();
                                    let updatedText = document.getElementById('about-input').value;

                                    fetch('update_about.php', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                        body: 'about=' + encodeURIComponent(updatedText)
                                    })
                                    .then(response => response.text())
                                    .then(data => {
                                        if (data.trim() === 'success') {
                                            document.getElementById('about-text').innerHTML = updatedText.replace(/\n/g, '<br>');

                                            let modalEl = document.getElementById('editAboutModal');
                                            let modalInstance = bootstrap.Modal.getInstance(modalEl);
                                            modalInstance.hide();
                                        } else {
                                           // alert("Error: " + data);
                                            location.reload();
                                        }
                                    })
                                    .catch(error => {
                                        console.error("Error updating About:", error);
                                        alert("Something went wrong.");
                                    });
                                });
                                </script>




                                <!-- Summary Section -->
                                <div class="container">
                                    <div class="summary-card shadow p-3 mb-3 bg-white rounded w-75">
                                        <!--p class="text-muted mb-2">Private to you</p-->
                                        <div class="d-flex align-items-start">
                                            <div class="summary-icon me-3">
                                                <?php 
                                            // Display profile picture with timestamp to force refresh
                                            echo "<img src='$profilePic?" . time() . "' alt='Profile Picture' class='img-thumbnail mb-1' style='width: 40px;'>";
                                            ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-bold">Write a summary about your personality or work experience</h6>
                                                <p class="text-muted"><?= !empty($summary) ? htmlentities($summary) : 'No summary added yet.' ?></p>
                                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSummaryModal">
                                                    <?= !empty($summary) ? 'Edit Summary' : 'Add Summary' ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary Modal -->
                                <div class="modal fade" id="addSummaryModal" tabindex="-1" aria-labelledby="addSummaryModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form id="summary-form">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Add Summary</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="text-muted mb-2">You can write about your experience, skills, or achievements.</p>
                                                    <textarea class="form-control" name="summary" rows="5" required><?= htmlentities($summary) ?></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary" id="save-summary">Save</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <script>
                                document.getElementById("summary-form").addEventListener("submit", function(event) {
                                    event.preventDefault();
                                    let summary = document.querySelector("textarea[name='summary']").value;

                                    fetch("save_summary.php", {
                                        method: "POST",
                                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                                        body: "summary=" + encodeURIComponent(summary)
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === "success") {
                                            alert("Summary updated successfully!");
                                            location.reload();
                                        } else {
                                            alert("Error: " + data.message);
                                        }
                                    })
                                    .catch(error => console.error("Fetch error:", error));
                                });
                                </script>
                                </div>


                                    <div class="experience-section">
                                        <h3 class="section-header">Experience</h3>
                                        <ul class="list-group" id="work-experience-list">
                                        </ul>
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#experienceModal">Add Work Experience</button>
                                    </div>
                                    <div class="education-section">
                                        <h3 class="section-header">Education</h3>
                                        <ul class="list-group" id="education-list">
                                        </ul>
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#educationModal">Add Education</button>
                                    </div>
                                    <div class="skills-section">
                                        <h3 class="section-header">Skills</h3>
                                        <ul class="list-group" id="skills-list">
                                        </ul>
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#skillsModal">Add Skill</button>
                                    </div>                                
                                </div>


                                    <div class="modal fade" id="experienceModal" tabindex="-1" aria-labelledby="experienceModalLabel" aria-hidden="true">
                                          <div class="modal-dialog modal-lg">
                                            <form class="modal-content" method="POST" action="save_experience.php" id="experience-form">
                                              <div class="modal-header">
                                                <h5 class="modal-title" id="experienceModalLabel">Add Experience</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                              </div>
                                              <div class="modal-body row g-3">

                                                <div class="col-md-6">
                                                  <label for="title" class="form-label">Title*</label>
                                                  <input type="text" class="form-control" id="title" name="title" required>
                                                </div>

                                                <div class="col-md-6">
                                                  <label for="employment_type" class="form-label">Employment type</label>
                                                  <select class="form-select" id="employment_type" name="employment_type">
                                                    <option value="">Please select</option>
                                                    <option value="Full-time">Full-time</option>
                                                    <option value="Part-time">Part-time</option>
                                                    <option value="Internship">Internship</option>
                                                    <option value="Freelance">Freelance</option>
                                                    <option value="Self-employed">Self-employed</option>
                                                  </select>
                                                </div>

                                                <div class="col-md-12">
                                                  <label for="company" class="form-label">Company or organization*</label>
                                                  <input type="text" class="form-control" id="company" name="company" required>
                                                </div>

                                                <div class="col-md-12 form-check">
                                                  <input class="form-check-input" type="checkbox" value="1" id="currentlyWorking" name="currently_working">
                                                  <label class="form-check-label" for="currentlyWorking">
                                                    I am currently working in this role
                                                  </label>
                                                </div>

                                                <div class="col-md-3">
                                                  <label for="start_month" class="form-label">Start Month*</label>
                                                  <select class="form-select" id="start_month" name="start_month" required>
                                                    <option value="">Month</option>
                                                    <option>January</option><option>February</option><option>March</option>
                                                    <option>April</option><option>May</option><option>June</option>
                                                    <option>July</option><option>August</option><option>September</option>
                                                    <option>October</option><option>November</option><option>December</option>
                                                  </select>
                                                </div>

                                                <div class="col-md-3">
                                                  <label for="start_year" class="form-label">Start Year*</label>
                                                  <input type="number" class="form-control" id="start_year" name="start_year" required>
                                                </div>

                                                <div class="col-md-3">
                                                  <label for="end_month" class="form-label">End Month</label>
                                                  <select class="form-select" id="end_month" name="end_month">
                                                    <option value="">Month</option>
                                                    <option>January</option><option>February</option><option>March</option>
                                                    <option>April</option><option>May</option><option>June</option>
                                                    <option>July</option><option>August</option><option>September</option>
                                                    <option>October</option><option>November</option><option>December</option>
                                                  </select>
                                                </div>

                                                <div class="col-md-3">
                                                  <label for="end_year" class="form-label">End Year</label>
                                                  <input type="number" class="form-control" id="end_year" name="end_year">
                                                </div>

                                                <div class="col-md-12">
                                                  <label for="location" class="form-label">Location</label>
                                                  <input type="text" class="form-control" id="location" name="location" placeholder="Ex: London, United Kingdom">
                                                </div>

                                                <div class="col-md-12">
                                                  <label for="location_type" class="form-label">Location type</label>
                                                  <select class="form-select" id="location_type" name="location_type">
                                                    <option value="">Please select</option>
                                                    <option value="On-site">On-site</option>
                                                    <option value="Hybrid">Hybrid</option>
                                                    <option value="Remote">Remote</option>
                                                  </select>
                                                </div>

                                                <div class="col-md-12">
                                                  <label for="description" class="form-label">Description</label>
                                                  <textarea class="form-control" id="description" name="description" rows="3" maxlength="2000"
                                                    placeholder="List your major duties and successes, highlighting specific projects"></textarea>
                                                  <div class="form-text">0/2000</div>
                                                </div>

                                                <div class="col-md-12">
                                                  <label for="profile_headline" class="form-label">Profile headline</label>
                                                  <input type="text" class="form-control" id="profile_headline" name="profile_headline" placeholder="Ex: Student at SLIATE / Full Stack Developer">
                                                </div>

                                                <div class="col-md-12">
                                                  <label for="job_source" class="form-label">Where did you find this job?</label>
                                                  <select class="form-select" id="job_source" name="job_source">
                                                    <option value="">Please select</option>
                                                    <option value="LinkedIn">LinkedIn</option>
                                                    <option value="Company website">Company website</option>
                                                    <option value="Referral">Referral</option>
                                                    <option value="Other">Other</option>
                                                  </select>
                                                </div>

                                                <!-- Skills -->
                                                <div class="col-md-12">
                                                  <label class="form-label">Skills</label>
                                                  <div id="exp-skill-list" class="d-flex flex-wrap gap-2 mb-2"></div>

                                                  <div class="input-group">
                                                    <input type="text" class="form-control" id="exp-skill-input" placeholder="Add a skill">
                                                    <button type="button" class="btn btn-outline-primary" id="add-exp-skill">+ Add Skill</button>
                                                  </div>
                                                  <div class="form-text">We recommend adding your top 5 used in this role.</div>
                                                </div>

                                              </div>
                                              <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Save</button>
                                              </div>
                                            </form>
                                          </div>
                                        </div>

                                        <script>
                                          const expSkillList = document.getElementById("exp-skill-list");
                                          const expSkillInput = document.getElementById("exp-skill-input");
                                          const addExpSkill = document.getElementById("add-exp-skill");

                                          addExpSkill.addEventListener("click", function (e) {
                                            e.preventDefault();
                                            const skill = expSkillInput.value.trim();
                                            if (skill !== "" && expSkillList.children.length < 5) {
                                              const skillBadge = document.createElement("span");
                                              skillBadge.className = "badge bg-secondary rounded-pill px-3 py-2";
                                              skillBadge.innerHTML = `${skill} <button type="button" class="btn-close btn-close-white btn-sm ms-2" aria-label="Remove"></button>`;
                                              expSkillList.appendChild(skillBadge);
                                              expSkillInput.value = "";

                                              // Remove skill on click
                                              skillBadge.querySelector("button").addEventListener("click", () => {
                                                expSkillList.removeChild(skillBadge);
                                              });
                                            }
                                          });
                                        </script>





                                        <div class="modal fade" id="educationModal" tabindex="-1" aria-labelledby="educationModalLabel" aria-hidden="true">
                                              <div class="modal-dialog modal-lg">
                                                <form class="modal-content" id="education-form" method="POST" action="save_education.php">
                                                  <div class="modal-header">
                                                    <h5 class="modal-title" id="educationModalLabel">Add Education</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                  </div>

                                                  <div class="modal-body row g-3">
                                                    <!-- Basic Education Inputs -->
                                                    <div class="col-md-6">
                                                      <label for="school" class="form-label">School*</label>
                                                      <input type="text" class="form-control" id="school" name="school" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                      <label for="degree" class="form-label">Degree</label>
                                                      <input type="text" class="form-control" id="degree" name="degree">
                                                    </div>
                                                    <div class="col-md-6">
                                                      <label for="field" class="form-label">Field of Study</label>
                                                      <input type="text" class="form-control" id="field" name="field">
                                                    </div>
                                                    <div class="col-md-3">
                                                      <label for="start-month" class="form-label">Start Month</label>
                                                      <select class="form-select" id="start-month" name="start_month">
                                                        <option value="">--Month--</option>
                                                        <option value="January">January</option>
                                                        <option value="February">February</option>
                                                        <option value="March">March</option>
                                                        <option value="April">April</option>
                                                        <option value="May">May</option>
                                                        <option value="June">June</option>
                                                        <option value="July">July</option>
                                                        <option value="August">August</option>
                                                        <option value="September">September</option>
                                                        <option value="October">October</option>
                                                        <option value="November">November</option>
                                                        <option value="December">December</option>
                                                      </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                      <label for="start-year" class="form-label">Start Year</label>
                                                      <input type="number" class="form-control" id="start-year" name="start_year">
                                                    </div>
                                                    <div class="col-md-3">
                                                      <label for="end-month" class="form-label">End Month</label>
                                                      <select class="form-select" id="end-month" name="end_month">
                                                        <option value="">--Month--</option>
                                                        <option value="January">January</option>
                                                        <option value="February">February</option>
                                                        <option value="March">March</option>
                                                        <option value="April">April</option>
                                                        <option value="May">May</option>
                                                        <option value="June">June</option>
                                                        <option value="July">July</option>
                                                        <option value="August">August</option>
                                                        <option value="September">September</option>
                                                        <option value="October">October</option>
                                                        <option value="November">November</option>
                                                        <option value="December">December</option>
                                                      </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                      <label for="end-year" class="form-label">End Year</label>
                                                      <input type="number" class="form-control" id="end-year" name="end_year">
                                                    </div>

                                                    <div class="col-md-6">
                                                      <label for="grade" class="form-label">Grade</label>
                                                      <input type="text" class="form-control" id="grade" name="grade">
                                                    </div>
                                                    <div class="col-md-6">
                                                      <label for="activities" class="form-label">Activities and Societies</label>
                                                      <input type="text" class="form-control" id="activities" name="activities" maxlength="500">
                                                    </div>
                                                    <div class="col-12">
                                                      <label for="edu-description" class="form-label">Description</label>
                                                      <textarea class="form-control" id="edu-description" name="description" rows="3" maxlength="1000"></textarea>
                                                    </div>

                                                    <!-- Skills Input -->
                                                    <div class="col-12">
                                                      <label class="form-label">Skills</label>
                                                      <div id="skill-list" class="d-flex flex-wrap gap-2 mb-2"></div>
                                                      <div class="input-group">
                                                        <input type="text" class="form-control" id="skill-input" placeholder="Add a skill">
                                                        <button class="btn btn-outline-primary" id="add-skill">+ Add Skill</button>
                                                      </div>
                                                      <div class="form-text">Maximum 5 skills allowed</div>
                                                    </div>
                                                  </div>

                                                  <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                  </div>
                                                </form>
                                              </div>
                                            </div>

                                            <script>
                                                  let skills = [];

                                                  document.getElementById("add-skill").addEventListener("click", function (e) {
                                                    e.preventDefault();
                                                    const input = document.getElementById("skill-input");
                                                    const skill = input.value.trim();

                                                    if (skill && skills.length < 5 && !skills.includes(skill)) {
                                                      skills.push(skill);
                                                      updateSkillList();
                                                      input.value = "";
                                                    }
                                                  });

                                                  function updateSkillList() {
                                                    const list = document.getElementById("skill-list");
                                                    list.innerHTML = "";

                                                    skills.forEach((skill, index) => {
                                                      const badge = document.createElement("span");
                                                      badge.className = "badge bg-secondary d-flex align-items-center";
                                                      badge.innerHTML = `
                                                        ${skill}
                                                        <button type="button" class="btn-close btn-close-white btn-sm ms-2 remove-skill" aria-label="Remove" data-index="${index}"></button>
                                                      `;
                                                      list.appendChild(badge);
                                                    });

                                                    document.querySelectorAll(".remove-skill").forEach(btn => {
                                                      btn.addEventListener("click", function () {
                                                        const index = this.getAttribute("data-index");
                                                        skills.splice(index, 1);
                                                        updateSkillList();
                                                      });
                                                    });
                                                  }

                                                  document.getElementById("education-form").addEventListener("submit", function () {
                                                    const hidden = document.createElement("input");
                                                    hidden.type = "hidden";
                                                    hidden.name = "skills";
                                                    hidden.value = JSON.stringify(skills);
                                                    this.appendChild(hidden);
                                                  });
                                            </script>



                                        <div class="modal fade" id="skillsModal" tabindex="-1" aria-labelledby="skillsModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="skillsModalLabel">Add Skill</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="skills-form">
                                                            <div class="mb-3">
                                                                <label for="skill" class="form-label">Skill</label>
                                                                <input type="text" class="form-control" id="skill" placeholder="e.g., JavaScript">
                                                            </div>
                                                             <div class="mb-3">
                                                                 <div class="mb-3">
                                                                    <label for="institution" class="form-label">Institution</label>
                                                                    <input type="text" class="form-control" id="institution2" placeholder="e.g., University of Colombo">
                                                            </div>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Add Skill</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <script type="text/javascript">
                                           document.getElementById('skills-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form from submitting normally

    const skill = document.getElementById('skill').value;
    const institution = document.getElementById('institution2').value;

    if (!skill || !institution) {
        alert("Please fill in both Skill and Institution fields.");
        return;
    }

    const formData = new FormData();
    formData.append('skill', skill);
    formData.append('institution', institution);

    // Send the data to the backend PHP file
    fetch('add_skill.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Parse the JSON response from PHP
    .then(data => {
        if (data.success) {
            alert(data.success); // Show success message
            const skillHTML = `
                <li class="list-group-item">
                    <div class="skillsbox">
                        <h4>${skill}</h4>
                        <p>${institution}</p>
                        <div class="d-flex">
                            <button class="btn btn-warning btn-sm edit-btn">Edit</button>
                            <button class="btn btn-danger btn-sm delete-btn">Delete</button>
                        </div>
                    </div>
                </li>
            `;
            document.getElementById('skills-list').innerHTML += skillHTML; // Add new skill to the list
            document.getElementById('skills-form').reset(); // Reset form
            initSortable(); // Reinitialize sortable functionality
            addSkillEventListeners(); // Reinitialize event listeners
        } else {
            alert(data.error); // Show error message
        }
    })
    .catch(error => {
        console.error('Error:', error); // Log errors in the console
        alert('Error adding skill. Please try again.');
    });
});

                                        </script>

                                        <script type="text/javascript">
                                            function initSortable() {
    const skillList = document.getElementById('skills-list');
    new Sortable(skillList, {
        animation: 150,
        handle: '.skillsbox', // Drag handle
    });
}

function addSkillEventListeners() {
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach((button) => {
        button.addEventListener('click', function() {
            const listItem = button.closest('li');
            const skillName = listItem.querySelector('h4').textContent;
            const institutionName = listItem.querySelector('p').textContent;

            const newSkill = prompt('Edit Skill', skillName);
            const newInstitution = prompt('Edit Institution', institutionName);

            if (newSkill && newInstitution) {
                listItem.querySelector('h4').textContent = newSkill;
                listItem.querySelector('p').textContent = newInstitution;
            }
        });
    });

    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach((button) => {
        button.addEventListener('click', function() {
            const listItem = button.closest('li');
            listItem.remove();
        });
    });
}

                                        </script>
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                fetchSkills();
                                            });

                                            function fetchSkills() {
                                                fetch('fetch_skills.php')
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        const skillsList = document.getElementById('skills-list');
                                                        skillsList.innerHTML = ''; // Clear current list

                                                        data.forEach(skill => {
                                                            const skillHTML = `
                                                                <li class="list-group-item">
                                                                    <div class="skillsbox">
                                                                        <h4>${skill.skill_name}</h4>
                                                                        <p>${skill.institution}</p>
                                                                        <div class="d-flex">
                                                                            <button class="btn btn-warning btn-sm edit-btn">Edit</button>
                                                                            <button class="btn btn-danger btn-sm delete-btn">Delete</button>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            `;
                                                            skillsList.innerHTML += skillHTML;
                                                        });

                                                        initSortable();
                                                        addSkillEventListeners();
                                                    })
                                                    .catch(error => {
                                                        console.error('Error fetching skills:', error);
                                                    });
                                            }
                                            </script>

                                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include_once("../includes/footer.php") ?>
    <?php include_once ("../includes/js-links-inc.php") ?>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
</body>
</html>
<?php
$conn->close();
?>
