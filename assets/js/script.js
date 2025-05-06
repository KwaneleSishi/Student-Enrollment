
document.addEventListener('DOMContentLoaded', function() {
    // Department filter click handler
    document.querySelectorAll('#department-menu a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const deptId = this.getAttribute('href').split('=')[1];
            window.location.href = `?dept=${deptId}`;
        });
    });

    // Credit filter functionality
    document.getElementById('credits-filter').addEventListener('change', function() {
        const creditValue = this.value;
        const courseCards = document.querySelectorAll('.course-card');
        
        courseCards.forEach(card => {
            const credits = card.querySelector('.course-content p').textContent.match(/\d+/)[0];
            if (creditValue === 'all' || credits === creditValue) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// Role selector functionality
document.addEventListener('DOMContentLoaded', function() {
    const roleBtns = document.querySelectorAll('.role-btn');
    const roleInput = document.getElementById('role-input');
    
    // Initialize from PHP value
    let currentRole = roleInput.value.toLowerCase(); // Ensure lowercase
    roleBtns.forEach(btn => {
        btn.classList.toggle('active', btn.dataset.role === currentRole);
    });

    roleBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const selectedRole = this.dataset.role;
            
            roleBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            roleInput.value = selectedRole;
        });
    });
});

//form submission handler
document.getElementById('login-form').addEventListener('submit', function(e) {
    // Basic client-side validation
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (!email || !password) {
        e.preventDefault();
        alert('Please fill in all fields');
    }
});