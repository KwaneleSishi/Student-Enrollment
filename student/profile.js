// Student Profile JavaScript

// Global variables
let currentStudent = null

// Import necessary functions (assuming they are in a separate module)
import { checkAuth, logout, getCurrentUser } from "../utils/auth.js"

// Initialize the page
document.addEventListener("DOMContentLoaded", () => {
  // Check authentication
  checkAuth("student")

  // Get current student info
  getCurrentStudent()

  // Setup logout button
  document.getElementById("logout-btn").addEventListener("click", logout)

  // Setup tab navigation
  setupTabs()

  // Setup form submissions
  setupForms()

  // Setup avatar upload
  setupAvatarUpload()

  // Setup modal close buttons
  document.querySelectorAll(".close-modal").forEach((btn) => {
    btn.addEventListener("click", () => {
      document.getElementById("success-modal").style.display = "none"
    })
  })

  // Check if password form exists
  const passwordForm = document.getElementById("password-form")
  if (passwordForm) {
    console.log("Password form found")
  } else {
    console.error("Password form not found!")
  }

  // Check if password tab exists
  const passwordTab = document.getElementById("password-tab")
  if (passwordTab) {
    console.log("Password tab found")
  } else {
    console.error("Password tab not found!")
  }
})

// Get current student information
function getCurrentStudent() {
  // In a real app, this would fetch from the server
  // For demo, we'll use the user from localStorage
  const user = getCurrentUser()

  if (!user || user.role !== "student") {
    window.location.href = "../index.html"
    return
  }

  currentStudent = user

  // Update UI with student name and avatar
  document.getElementById("student-name").textContent = `${currentStudent.firstName} ${currentStudent.lastName}`
  document.getElementById("user-avatar").textContent =
    currentStudent.firstName.charAt(0) + currentStudent.lastName.charAt(0)
  document.getElementById("profile-avatar").textContent =
    currentStudent.firstName.charAt(0) + currentStudent.lastName.charAt(0)

  // Populate form fields
  populatePersonalForm()

  // Load student stats
  loadStudentStats()
}

// Update the populatePersonalForm function to remove the fields we're no longer using
function populatePersonalForm() {
  document.getElementById("first-name").value = currentStudent.firstName || ""
  document.getElementById("last-name").value = currentStudent.lastName || ""
  document.getElementById("email").value = currentStudent.email || ""
}

// Update the populateAcademicForm function to include all the academic fields
// function populateAcademicForm() {
//   document.getElementById("student-id").value = currentStudent.id || ""
//   document.getElementById("major").value = currentStudent.major || ""
//   document.getElementById("enrollment-date").value = currentStudent.enrollmentDate || "2022-09-01"
//   document.getElementById("expected-graduation").value = currentStudent.expectedGraduation || "2026-05-15"
//   document.getElementById("academic-status").value = currentStudent.academicStatus || "Active"
//   document.getElementById("credits-completed").value = currentStudent.creditsCompleted || "24"
//   document.getElementById("advisor").value = currentStudent.advisor || "Dr. Michael Brown"
// }

// Load student statistics
function loadStudentStats() {
  // In a real app, this would fetch from the server
  // For demo, we'll use mock data
  const enrolledCount = 3
  const completedCount = 1
  const gpa = "3.75"

  document.getElementById("enrolled-count").textContent = enrolledCount
  document.getElementById("completed-count").textContent = completedCount
  document.getElementById("current-gpa").textContent = gpa
}

// Setup tab navigation
function setupTabs() {
  const tabButtons = document.querySelectorAll(".tab-btn")
  const tabContents = document.querySelectorAll(".tab-content")

  console.log("Tab buttons found:", tabButtons.length)
  console.log("Tab contents found:", tabContents.length)

  tabButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const tabName = button.getAttribute("data-tab")
      console.log("Tab clicked:", tabName)

      // Update active tab button
      tabButtons.forEach((btn) => btn.classList.remove("active"))
      button.classList.add("active")

      // Show selected tab content
      tabContents.forEach((content) => {
        content.classList.add("hidden")
        if (content.id === `${tabName}-tab`) {
          console.log("Showing tab content:", content.id)
          content.classList.remove("hidden")
        }
      })
    })
  })

  // Initialize the first tab to be visible
  const activeTab = document.querySelector(".tab-btn.active")
  if (activeTab) {
    const activeTabName = activeTab.getAttribute("data-tab")
    const activeTabContent = document.getElementById(`${activeTabName}-tab`)
    if (activeTabContent) {
      console.log("Initially showing tab:", activeTabName)
      tabContents.forEach((content) => content.classList.add("hidden"))
      activeTabContent.classList.remove("hidden")
    }
  }
}

// Setup form submissions
function setupForms() {
  // Personal information form
  const personalForm = document.getElementById("personal-form")
  if (personalForm) {
    personalForm.addEventListener("submit", (e) => {
      e.preventDefault()

      // Get form values
      const firstName = document.getElementById("first-name").value
      const lastName = document.getElementById("last-name").value
      const email = document.getElementById("email").value

      // Update current student object
      currentStudent.firstName = firstName
      currentStudent.lastName = lastName
      currentStudent.email = email

      // Save to localStorage (in a real app, this would be sent to the server)
      localStorage.setItem("currentUser", JSON.stringify(currentStudent))

      // Update UI
      document.getElementById("user-avatar").textContent = firstName.charAt(0) + lastName.charAt(0)
      document.getElementById("profile-avatar").textContent = firstName.charAt(0) + lastName.charAt(0)

      // Show success message
      showSuccessModal("Personal information updated successfully.")
    })
  } else {
    console.error("Personal form not found!")
  }

  // Password change form
  const passwordForm = document.getElementById("password-form")
  if (passwordForm) {
    console.log("Adding submit handler to password form")
    passwordForm.addEventListener("submit", (e) => {
      e.preventDefault()
      console.log("Password form submitted")

      // Get form values
      const currentPassword = document.getElementById("current-password").value
      const newPassword = document.getElementById("new-password").value
      const confirmPassword = document.getElementById("confirm-password").value

      // Validate current password (in a real app, this would be verified on the server)
      if (currentPassword !== "password") {
        showErrorModal("Current password is incorrect.")
        return
      }

      // Validate new password
      if (newPassword.length < 8) {
        showErrorModal("New password must be at least 8 characters long.")
        return
      }

      // Check for at least one uppercase letter
      if (!/[A-Z]/.test(newPassword)) {
        showErrorModal("New password must contain at least one uppercase letter.")
        return
      }

      // Check for at least one lowercase letter
      if (!/[a-z]/.test(newPassword)) {
        showErrorModal("New password must contain at least one lowercase letter.")
        return
      }

      // Check for at least one number
      if (!/[0-9]/.test(newPassword)) {
        showErrorModal("New password must contain at least one number.")
        return
      }

      // Check if passwords match
      if (newPassword !== confirmPassword) {
        showErrorModal("New passwords do not match.")
        return
      }

      // Check if new password is the same as the old password
      if (newPassword === currentPassword) {
        showErrorModal("New password must be different from your current password.")
        return
      }

      // In a real app, this would send the new password to the server
      // For demo, we'll simulate a server request with a timeout
      showLoadingModal("Updating your password...")

      setTimeout(() => {
        // Update the password in our mock data
        // In a real app, this would be done on the server

        // Show success message
        hideLoadingModal()
        showSuccessModal("Password changed successfully. Please use your new password the next time you log in.")

        // Reset form
        document.getElementById("password-form").reset()
      }, 1500)
    })
  } else {
    console.error("Password form not found!")
  }
}

// Setup avatar upload
function setupAvatarUpload() {
  const avatarUpload = document.getElementById("avatar-upload")
  const avatarPreview = document.getElementById("avatar-preview")

  avatarUpload.addEventListener("change", function () {
    const file = this.files[0]

    if (file) {
      const reader = new FileReader()

      reader.onload = (e) => {
        avatarPreview.src = e.target.result
        avatarPreview.style.display = "block"

        // In a real app, you would upload the image to the server here
        // For demo, we'll just update the local preview

        // Store the image in localStorage (not recommended for production)
        localStorage.setItem("userAvatar", e.target.result)
      }

      reader.readAsDataURL(file)
    }
  })

  // Check if we have a stored avatar
  const storedAvatar = localStorage.getItem("userAvatar")
  if (storedAvatar) {
    avatarPreview.src = storedAvatar
    avatarPreview.style.display = "block"
  }
}

// Add new modal functions for better user feedback
function showErrorModal(message) {
  document.getElementById("success-message").textContent = message
  const modal = document.getElementById("success-modal")
  modal.style.display = "block"

  // Change the header to "Error" and add an error class
  const header = modal.querySelector(".modal-header h3")
  header.textContent = "Error"
  header.classList.add("text-error")

  // Change the button color
  const button = modal.querySelector(".modal-footer button")
  button.classList.remove("btn-primary")
  button.classList.add("btn-error")
}

function showLoadingModal(message) {
  // Create a loading modal if it doesn't exist
  if (!document.getElementById("loading-modal")) {
    const loadingModal = document.createElement("div")
    loadingModal.id = "loading-modal"
    loadingModal.className = "modal"
    loadingModal.innerHTML = `
      <div class="modal-content">
        <div class="modal-body text-center">
          <div class="spinner"></div>
          <p id="loading-message">${message}</p>
        </div>
      </div>
    `
    document.body.appendChild(loadingModal)
  } else {
    document.getElementById("loading-message").textContent = message
  }

  document.getElementById("loading-modal").style.display = "block"
}

function hideLoadingModal() {
  const loadingModal = document.getElementById("loading-modal")
  if (loadingModal) {
    loadingModal.style.display = "none"
  }
}

// Update the showSuccessModal function to reset modal styling
function showSuccessModal(message) {
  document.getElementById("success-message").textContent = message
  const modal = document.getElementById("success-modal")

  // Reset header to "Success" and remove any error class
  const header = modal.querySelector(".modal-header h3")
  header.textContent = "Success"
  header.classList.remove("text-error")

  // Reset button color
  const button = modal.querySelector(".modal-footer button")
  button.classList.remove("btn-error")
  button.classList.add("btn-primary")

  modal.style.display = "block"
}
