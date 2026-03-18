<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Roadtrip $roadtrip
 * @var bool $isEditMode
 * @var array $existingTrips
 * @var string $userDefaultCity
 */

$this->assign('title', $isEditMode ? 'Edit RoadTrip' : 'Create RoadTrip');
$this->assign('mainClass', '');
?>

<script>
    const USER_DEFAULT_CITY = "<?= h($userDefaultCity) ?>";
    const MODE_EDITION = <?= json_encode($isEditMode) ?>;
    const URL_GET_FAVORIS = "<?= $this->Url->build(['controller' => 'Roadtrips', 'action' => 'getFavoritePlaces']) ?>";
    const UPLOAD_IMAGE_URL = "<?= $this->Url->build(['controller' => 'Roadtrips', 'action' => 'uploadStepImage']) ?>";

    // Keys are kept in French so it doesn't break your existing JS file
    const EXISTING_ROADTRIP = <?= json_encode([
        'id' => $roadtrip->id,
        'titre' => $roadtrip->title,
        'description' => $roadtrip->description,
        'statut' => $roadtrip->status ?? 'draft',
        'visibilite' => $roadtrip->visibility,
        'photo' => $roadtrip->photo_url
    ]) ?>;

    const EXISTING_TRAJETS = <?= json_encode($existingTrips) ?>;

    const SAVE_URL = "<?= $this->Url->build(['action' => $isEditMode ? 'edit' : 'add', $isEditMode ? $roadtrip->id : null]) ?>";
    const CSRF_TOKEN = "<?= $this->request->getAttribute('csrfToken') ?>";
</script>

<h1 class="roadtrip-main-title"><?= $isEditMode ? "Edit my RoadTrip" : "Create a RoadTrip" ?></h1>

<div class="main-container">
    <div class="sidebar">
        <div class="search-container">
            <div class="region-selector-container">
                <label for="regionSelect">🌍 Search area:</label>
                <small class="help-text-small">Centers the map and filters suggested cities.</small>
                <select id="regionSelect" class="full-width-input margin-bottom">
                    <option value="europe" <?= (isset($roadtrip->place) && $roadtrip->place === 'europe') ? 'selected' : '' ?>>Europe</option>
                    <option value="america" <?= (isset($roadtrip->place) && $roadtrip->place === 'america') ? 'selected' : '' ?>>North America (USA, Canada, Mexico)</option>
                </select>
            </div>

            <div id="legend" class="block-display">
                <h3>Itinerary:</h3>
                <ul id="legendList" class="no-bullets padding-zero"></ul>
                <div id="newBlockForm" class="hidden"></div>
            </div>

            <div id="actionsContainer" class="margin-top-small">
                <button type="button" id="btnAddSegment" class="full-width-btn">+ Add a trip</button>
            </div>

            <hr>
        </div>

        <div id="saveContainer">
            <h3>Save & Settings</h3>

            <?= $this->Form->create($roadtrip, ['type' => 'file', 'id' => 'roadtripForm']) ?>

            <?= $this->Form->control('title', [
                'id' => 'roadtripTitle',
                'placeholder' => 'RoadTrip Title',
                'label' => false,
                'class' => 'full-width-input margin-bottom'
            ]) ?>

            <?= $this->Form->control('description', [
                'id' => 'roadtripDescription',
                'type' => 'textarea',
                'placeholder' => 'Description (optional)',
                'label' => false,
                'class' => 'full-width-input margin-bottom'
            ]) ?>

            <div class="status-selector-container margin-bottom">
                <?= $this->Form->control('status', [
                    'id' => 'roadtripStatut',
                    'type' => 'select',
                    'label' => ['text' => 'Project progress:', 'class' => 'bold-label'],
                    'class' => 'full-width-input',
                    'options' => [
                        'draft' => '📝 Work in progress (Draft)',
                        'completed' => '✅ Completed project'
                    ],
                    'default' => $roadtrip->status ?? 'draft'
                ]) ?>
            </div>

            <div class="visibility-selector-container margin-bottom">
                <?= $this->Form->control('visibility', [
                    'id' => 'roadtripVisibilite',
                    'type' => 'select',
                    'label' => ['text' => 'Who can see this RoadTrip?', 'class' => 'bold-label'],
                    'class' => 'full-width-input',
                    'options' => [
                        'private' => '🔒 Private (Only me)',
                        'friends' => '👥 Friends',
                        'public' => '🌍 Public (Everyone)'
                    ],
                    'default' => $roadtrip->visibility ?? 'private'
                ]) ?>
                <small class="help-text">
                    * You can share a draft in "Friends" or "Public" mode.
                </small>
            </div>

            <div class="image-upload-container margin-bottom">
                <label class="bold-label">Road Trip cover:</label>
                <?php if ($isEditMode && !empty($roadtrip->photo_url)): ?>
                    <div class="current-image-wrapper">
                        <?= $this->Html->image('roadtrips/' . $roadtrip->photo_url, ['class' => 'preview-thumbnail', 'alt' => 'Current cover']) ?>
                        <br><small>Current image</small>
                    </div>
                <?php endif; ?>

                <?= $this->Form->control('photo_cover', [
                    'type' => 'file',
                    'id' => 'roadtripPhoto',
                    'accept' => 'image/*',
                    'label' => false
                ]) ?>
            </div>

            <?= $this->Form->hidden('trajets', ['id' => 'trajetsJsonData']) ?>

            <?= $this->Form->button($isEditMode ? "Update" : "Save", [
                'type' => 'submit',
                'id' => 'saveRoadtripBtn',
                'class' => 'submit-btn full-width-btn margin-top-small'
            ]) ?>

            <?= $this->Form->end() ?>
        </div>
    </div>

    <div class="segment-form-container hidden" id="segmentFormContainer">
        <span id="closeSegmentForm" class="close-segment-btn" title="Close">✖</span>
        <h3 id="segmentTitle">Plan stops</h3>
        <div id="subEtapesContainer"></div>

        <div class="subEtape-buttons">
            <button type="button" id="addSubEtape">+ Add a sub-step</button>
            <button type="button" id="saveSegment">Validate sub-steps</button>
        </div>
    </div>

    <div class="map-container">
        <div id="map"></div>
    </div>
</div>

<template id="template-legend-item">
    <li class="legend-li">
        <div class="segment-header legend-segment-item">
            <span class="legend-color-indicator"></span>

            <div class="transport-options">
                <button type="button" class="transport-btn active" data-mode="Voiture" title="By Car">🚗</button>
                <button type="button" class="transport-btn" data-mode="Velo" title="By Bike">🚲</button>
                <button type="button" class="transport-btn" data-mode="Marche" title="On Foot">🚶</button>
            </div>
            <button type="button" class="settings-btn" title="Trip options">⚙️</button>
            <button type="button" class="toggleSousEtapes legend-toggle-btn">▼</button>
            <button type="button" class="remove-segment-btn" title="Delete this trip">✖</button>
        </div>

        <div class="legend-date-container">
            <label>Departure on:</label>
            <input type="date" class="legend-date-input" required>
            <label>at:</label>
            <input type="time" class="legend-time-input" value="08:00" required>
        </div>

        <div class="route-preferences hidden">
            <label class="pref-item">
                <input type="checkbox" class="pref-checkbox" data-pref="exclude-tolls">
                <span>No tolls</span>
            </label>
            <label class="pref-item">
                <input type="checkbox" class="pref-checkbox" data-pref="exclude-motorways">
                <span>No motorways</span>
            </label>
        </div>

        <button type="button" class="modifierSousEtapes">Add/Edit Sub-steps</button>
        <ul class="sousEtapesList block-display"></ul>
    </li>
</template>

<template id="template-sub-etape">
    <div class="subEtape sub-etape-form">
        <input type="text" placeholder="Place or city name" class="subEtapeNom">

        <div class="subEtapeEditorContainer"></div>

        <label class="small-bold-label">Time spent on site (estimation)</label>
        <input type="time" class="subEtapeHeure" required>

        <button type="button" class="removeSubEtapeBtn sub-etape-remove-btn">✖</button>
    </div>
</template>

<div id="imageModal" class="image-modal">
    <?= $this->Html->image('', ['id' => 'imageModalContent', 'class' => 'image-modal-content', 'alt' => 'Enlarged photo']) ?>
</div>
