// Authentication and authorization functions

// Check if user is authenticated and has the correct role
export function checkAuth(requiredRole) {
  const user = getCurrentUser()

  if (!user) {
    // Redirect to login page
    window.location.href = "../index.html"
    return false
  }

  if (user.role !== requiredRole) {
    // Redirect to appropriate dashboard
    if (user.role === "student") {
      window.location.href = "../student/dashboard.html"
    } else if (user.role === "instructor") {
      window.location.href = "../instructor/dashboard.html"
    } else if (user.role === "admin") {
      window.location.href = "../admin/dashboard.html"
    }
    return false
  }

  return true
}

// Get current user from localStorage
export function getCurrentUser() {
  const userJson = localStorage.getItem("currentUser")
  return userJson ? JSON.parse(userJson) : null
}

// Logout function
export function logout() {
  localStorage.removeItem("currentUser")
  window.location.href = "../index.html"
}
