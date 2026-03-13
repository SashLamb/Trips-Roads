/**
 * @file Gestion des préférences d’accessibilité
 * @description
 * Ce script gère les préférences d’accessibilité utilisateur :
 * - Thème sombre / clair
 * - Mode malvoyant (taille/contraste adapté)
 * - Filtres pour différents types de daltonisme
 * 
 * Les préférences sont sauvegardées dans le localStorage
 * et réappliquées automatiquement au chargement de la page.
 */

/**
 * Applique les préférences enregistrées dans le localStorage.
 *
 * Cette fonction :
 * - Récupère les paramètres sauvegardés
 * - Met à jour les classes CSS du document
 * - Synchronise l’état des champs du formulaire
 *
 * @function appliquerPreferences
 * @returns {void}
 */
function appliquerPreferences() {

    /** @type {string|null} */
    const savedTheme = localStorage.getItem("theme");

    /** @type {string|null} */
    const savedMalvoyant = localStorage.getItem("Police");

    /** @type {string|null} */
    const savedDaltonienType = localStorage.getItem("typeDaltonien");

    /** @type {HTMLInputElement|null} */
    const checkboxSombre = document.getElementById("checkboxSombre");

    /** @type {HTMLInputElement|null} */
    const checkboxMalvoyant = document.getElementById("checkboxMalvoyant");

    /* =========================
       Gestion du thème sombre
    ========================== */
    if (savedTheme === "dark") {
        document.documentElement.classList.add("dark", "SombreBtn");
        if (checkboxSombre) checkboxSombre.checked = true;
    } else {
        document.documentElement.classList.remove("dark", "SombreBtn");
        if (checkboxSombre) checkboxSombre.checked = false;
    }

    /* =========================
       Gestion du mode malvoyant
    ========================== */
    if (savedMalvoyant === "malvoyant") {
        document.documentElement.classList.add("malvoyant", "MalvoyantBtn");
        if (checkboxMalvoyant) checkboxMalvoyant.checked = true;
    } else {
        document.documentElement.classList.remove("malvoyant", "MalvoyantBtn");
        if (checkboxMalvoyant) checkboxMalvoyant.checked = false;
    }

    /* =========================
       Gestion du daltonisme
    ========================== */

    // Supprime toutes les classes existantes
    document.documentElement.classList.remove(
        "daltonien",
        "protanopia",
        "deuteranopia",
        "tritanopia"
    );

    if (savedDaltonienType && savedDaltonienType !== "aucun") {

        document.documentElement.classList.add("daltonien", savedDaltonienType);

        /** @type {HTMLInputElement|null} */
        const radioToCheck = document.querySelector(
            `input[name="daltonism-type"][value="${savedDaltonienType}"]`
        );

        if (radioToCheck) radioToCheck.checked = true;

    } else {

        /** @type {HTMLInputElement|null} */
        const radioAucun = document.querySelector(
            'input[name="daltonism-type"][value="aucun"]'
        );

        if (radioAucun) radioAucun.checked = true;
    }
}

/**
 * Enregistre les préférences utilisateur lors de la soumission du formulaire.
 *
 * @function enregistrerPreferences
 * @param {SubmitEvent} event - Événement déclenché lors de la soumission
 * @returns {void}
 */
function enregistrerPreferences(event) {

    /** @type {HTMLInputElement|null} */
    const checkboxSombre = document.getElementById("checkboxSombre");

    /** @type {HTMLInputElement|null} */
    const checkboxMalvoyant = document.getElementById("checkboxMalvoyant");

    /** @type {HTMLInputElement|null} */
    const radioDaltonien = document.querySelector(
        'input[name="daltonism-type"]:checked'
    );

    /* =========================
       Sauvegarde du thème
    ========================== */
    if (checkboxSombre && checkboxSombre.checked) {
        localStorage.setItem("theme", "dark");
    } else {
        localStorage.setItem("theme", "light");
    }

    /* =========================
       Sauvegarde du mode malvoyant
    ========================== */
    if (checkboxMalvoyant && checkboxMalvoyant.checked) {
        localStorage.setItem("Police", "malvoyant");
    } else {
        localStorage.setItem("Police", "voyant");
    }

    /* =========================
       Sauvegarde du type de daltonisme
    ========================== */
    if (radioDaltonien && radioDaltonien.value) {
        localStorage.setItem("typeDaltonien", radioDaltonien.value);
    } else {
        localStorage.removeItem("typeDaltonien");
    }
}

/**
 * Initialise le script lorsque le DOM est entièrement chargé.
 *
 * @event DOMContentLoaded
 */
document.addEventListener("DOMContentLoaded", () => {

    // Application immédiate des préférences sauvegardées
    appliquerPreferences();

    /** @type {HTMLFormElement|null} */
    const form = document.getElementById("AccessForm");

    if (form) {
        form.addEventListener("submit", enregistrerPreferences);
    }
});