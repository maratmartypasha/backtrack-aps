<?php require page('includes/header') ?>

<div class="section-title">Search for: <?= htmlspecialchars($_GET['find'] ?? '') ?></div>

<section class="content">

    <?php 
        $search = $_GET['find'] ?? null;
        $rows = [];

        if (!empty($search)) {
            $search_term = "%$search%";

			$query = "SELECT songs.* FROM songs 
					JOIN categories ON songs.category_id = categories.id 
					JOIN artists ON songs.artist_id = artists.id 
					WHERE songs.title LIKE :search 
						OR categories.category LIKE :search 
						OR artists.name LIKE :search 
						OR songs.lyrics LIKE :search 
					ORDER BY songs.views DESC 
					LIMIT 24";

            $rows = db_query($query, ['search' => $search_term]);
        }
    ?>

    <?php if (!empty($rows)): ?>
        <?php foreach ($rows as $row): ?>
            <?php include page('includes/song') ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="m-2">No songs found</div>
    <?php endif; ?>

</section>

<?php require page('includes/footer') ?>
