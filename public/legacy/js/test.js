'use strict';


document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('EmailInput');
    const passwordInput = document.getElementById('PasswordInput');
    const validatePasswordInput = document.getElementById('ValidatePasswordInput');
    const form = document.getElementById('form');
    const chauffeurRadio = document.getElementById('chauffeur');
    const chauffeurPassagerRadio = document.getElementById('chauffeurPassager');

    if (!emailInput || !passwordInput || !validatePasswordInput || !form) {
        return;
    }

    // Expression régulière pour valider l'email
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    // Expression régulière pour valider le mot de passe
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/;

    // Fonction pour valider l'email
    function validateEmail() {
        if (emailRegex.test(emailInput.value)) {
            emailInput.classList.add('is-valid');
            emailInput.classList.remove('is-invalid');
            return true;
        } else {
            emailInput.classList.add('is-invalid');
            emailInput.classList.remove('is-valid');
            return false;
        }
    }

    // Fonction pour valider le mot de passe
    function validatePassword() {
        if (passwordRegex.test(passwordInput.value)) {
            passwordInput.classList.add('is-valid');
            passwordInput.classList.remove('is-invalid');
            return true;
        } else {
            passwordInput.classList.add('is-invalid');
            passwordInput.classList.remove('is-valid');
            return false;
        }
    }

    // Fonction pour valider la confirmation du mot de passe
    function validatePasswordConfirm() {
        if (validatePassword() && passwordInput.value === validatePasswordInput.value && validatePasswordInput.value.length > 0) {
            validatePasswordInput.classList.add('is-valid');
            validatePasswordInput.classList.remove('is-invalid');
            return true;
        } else {
            validatePasswordInput.classList.add('is-invalid');
            validatePasswordInput.classList.remove('is-valid');
            return false;
        }
    }

    // Écouteurs d'événements pour la validation en temps réel
    emailInput.addEventListener('input', validateEmail);
    passwordInput.addEventListener('input', validatePassword);
    validatePasswordInput.addEventListener('input', validatePasswordConfirm);

    // Validation au moment de la soumission du formulaire
    form.addEventListener('submit', function(event) {
        const isEmailValid = validateEmail();
        const isPasswordValid = validatePassword();
        const isPasswordConfirmValid = validatePasswordConfirm();

        if (!isEmailValid || !isPasswordValid || !isPasswordConfirmValid) {
            event.preventDefault(); // Empêche si les champs ne sont pas valides
            event.stopPropagation();
            return;
        }

        const souhaiteDevenirChauffeur = (chauffeurRadio && chauffeurRadio.checked) ||
          (chauffeurPassagerRadio && chauffeurPassagerRadio.checked);

        if (souhaiteDevenirChauffeur) {
            event.preventDefault();
            window.location.href = 'form_chauffeur.html';
        }
    });
});




