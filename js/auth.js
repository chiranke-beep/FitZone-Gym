document.addEventListener('DOMContentLoaded', () => {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const authForms = document.querySelectorAll('.auth-form');
    const selectRoleMessage = document.getElementById('select-role-message');



    // Handle tab switching
    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            console.log('Tab clicked:', btn.dataset.tab);

            // Update tab button active state
            tabButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Hide all forms and select role message
            authForms.forEach(form => {
                form.style.display = 'none';
            });
            if (selectRoleMessage) {
                selectRoleMessage.style.display = 'none';
            }

            // Show the selected form
            const tab = btn.dataset.tab;
            const targetForm = document.getElementById(`${tab}-login`);
            if (targetForm) {
                targetForm.style.display = 'block';
                // Ensure login form is visible, forgot password is hidden
                const loginForm = targetForm.querySelector('.login-form');
                const forgotForm = targetForm.querySelector('.forgot-password-form');
                if (loginForm) {
                    loginForm.style.display = 'block';
                }
                if (forgotForm) {
                    forgotForm.style.display = 'none';
                }
                console.log('Showing form:', targetForm.id);
            } else {
                console.error('Form not found for tab:', tab);
            }
        });
    });

    // Forgot password toggle
    document.querySelectorAll('.forgot-password').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const formContainer = link.closest('.auth-form');
            const loginForm = formContainer.querySelector('.login-form');
            const forgotForm = formContainer.querySelector('.forgot-password-form');
            if (loginForm && forgotForm) {
                loginForm.style.display = 'none';
                forgotForm.style.display = 'block';
                console.log('Showing forgot password form');
            }
        });
    });

    // Back to login toggle
    document.querySelectorAll('.back-to-login').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const formContainer = link.closest('.auth-form');
            const loginForm = formContainer.querySelector('.login-form');
            const forgotForm = formContainer.querySelector('.forgot-password-form');
            if (loginForm && forgotForm) {
                forgotForm.style.display = 'none';
                loginForm.style.display = 'block';
                console.log('Showing login form');
            }
        });
    });

    // Initialize: Show customer form by default
    const customerButton = document.querySelector('.tab-btn[data-tab="customer"]');
    if (customerButton) {
        customerButton.click();
        console.log('Initialized with customer tab');
    } else {
        console.error('Customer tab button not found');
    }
});