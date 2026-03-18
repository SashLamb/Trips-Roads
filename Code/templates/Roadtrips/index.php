<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Roadtrip> $randomRoadtrips
 */
?>
<section class="hero-actions">
    <div class="hero-content">
        <h1>Ready for an adventure?</h1>
        <p>Create your own itinerary or discover those from the community.</p>
        <div class="hero-buttons">
            <?= $this->Html->link('<span>➕</span> Create a Road Trip', ['controller' => 'Roadtrips', 'action' => 'add'], ['escape' => false, 'class' => 'btn-action primary']) ?>
            <?= $this->Html->link('<span>🌍</span> View Public Road Trips', ['controller' => 'Roadtrips', 'action' => 'publicRoadtrips'], ['escape' => false, 'class' => 'btn-action secondary']) ?>
        </div>
    </div>
</section>

<section class="full-map-container">
    <div class="floating-search">
        <div class="search-input-group">
            <?= $this->Form->text('poi_search', ['id' => 'poiSearchIndex', 'placeholder' => 'Search for a place...']) ?>
            <button type="button" id="searchBtnIndex">🔍</button>
        </div>
        <ul id="searchResultsIndex" class="searching-results"></ul>
    </div>

    <div class="map-sidebar open" id="mapSidebar">
        <div class="sidebar-content">
            <div class="category-header">
                <h4>Filters</h4>
            </div>

            <div class="category-select-wrapper">
                <label for="categorySelect">Categories:</label>
                <?= $this->Form->select('category', [
                    'Dining' => ['restaurant' => '🍽️ Restaurants', 'fast_food' => '🍔 Fast-food', 'cafe' => '☕ Cafes', 'bar' => '🍺 Bars & Pubs'],
                    'Accommodation' => ['hotel' => '🏨 Hotels', 'camping' => '🏕️ Campsites'],
                    'Services' => ['fuel' => '⛽ Gas stations', 'parking' => '🅿️ Parking lots'],
                    'Leisure' => ['attraction' => '🎭 Attractions', 'museum' => '🏛️ Museums', 'park' => '🌳 Parks'],
                    'Emergencies' => ['hospital' => '🏥 Hospitals']
                ], ['id' => 'categorySelect', 'class' => 'category-select', 'empty' => '-- Show all --']) ?>
            </div>

            <button type="button" id="clearFilterBtn" class="clear-filter-btn d-none">
                ❌ Clear
            </button>

            <div class="category-info">
                <p class="info-text">💡 <strong>Tip:</strong> Click on the map to re-center the search.</p>
            </div>

            <div class="category-header">
                <h4>Filter radius</h4>
            </div>

            <div class="range-container mt-10">
                <label for="radiusSlider" class="flex-between-bold">
                    Radius: <span id="radiusValue">2</span> km
                </label>
                <?= $this->Form->control('radius', [
                    'type' => 'range', 'min' => 1, 'max' => 20, 'step' => 1, 'value' => 2,
                    'label' => false, 'id' => 'radiusSlider', 'class' => 'form-range w-100-pointer'
                ]) ?>
            </div>
        </div>
        <button type="button" class="sidebar-toggle" id="btnToggleSidebar">
            <span id="toggleIcon">◀</span>
        </button>
    </div>

    <div id="userMapIndex"></div>
</section>

<section class="featured-section">
    <h2>🌟 Featured</h2>
    <div class="roadtrips-grid">
        <?php if (isset($randomRoadtrips) && !$randomRoadtrips->isEmpty()): ?>
            <?php foreach ($randomRoadtrips as $rt): ?>
                <?= $this->Html->link(
                    '<article class="mini-card">' .
                    '<div class="card-img" style="background-image: url(\'' . $this->Url->build($rt->cover_image) . '\');"></div>' .
                    '<div class="card-info">' .
                    '<h3>' . h($rt->title) . '</h3>' .
                    '<span class="badge">Completed</span>' .
                    '</div>' .
                    '</article>',
                    ['controller' => 'Roadtrips', 'action' => 'view', $rt->id],
                    ['escape' => false, 'class' => 'link-no-decor']
                ) ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center-padded">
                <p class="text-muted-large">No featured road trips at the moment.</p>
                <p>Be the first to publish one!</p>
            </div>
        <?php endif; ?>
    </div>
</section>
