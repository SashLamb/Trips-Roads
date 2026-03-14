/**
 * @file Optimisation de la gestion du profil utilisateur
 * @description Fonctions de prévisualisation d'image et validation de formulaire.
 */

/**
 * Gère l'affichage de la barre latérale sur mobile.
 * Placée hors du DOMContentLoaded pour être accessible via onclick.
 * @function toggleSidebar
 */
function toggleSidebar() {
    const sidebar = document.querySelector('.profil-sidebar');
    if (sidebar) sidebar.classList.toggle('active');
}

/**
 * Prépare l'aperçu de l'image de profil.
 * @function previewImage
 * @param {HTMLInputElement} input - L'élément input type file
 * @param {HTMLElement} previewElement - L'élément qui recevra l'image de fond
 */
function previewImage(input, previewElement) {
    const file = input.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewElement.style.backgroundImage = `url('${e.target.result}')`;
        };
        reader.readAsDataURL(file);
    }
}


document.addEventListener('DOMContentLoaded', () => {

    const profilForm = document.getElementById('profilForm');
    const fileInput = document.getElementById('image');
    const avatarPreview = document.querySelector('.avatar-circle.large');
    const passField = document.getElementById('password');
    const confirmField = document.getElementById('confirm-password');

    /**
     * Listener pour le changement de photo de profil
     */
    if (fileInput && avatarPreview) {
        fileInput.addEventListener('change', () => previewImage(fileInput, avatarPreview));
    }

    /**
     * Listener pour la validation du formulaire
     */
    if (profilForm) {
        profilForm.addEventListener('submit', (e) => {
            if (passField && confirmField && passField.value !== "") {
                if (passField.value !== confirmField.value) {
                    e.preventDefault();
                    alert("Les mots de passe ne correspondent pas.");
                    confirmField.focus();
                }
            }
        });
    }

    /**
     * Gestion automatique de la classe active sur la navigation
     */
    const currentPath = window.location.pathname;
    document.querySelectorAll('.profil-nav a').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });
});
