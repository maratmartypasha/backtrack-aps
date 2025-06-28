<?php 
// Tambah view count
db_query("UPDATE songs SET views = views + 1 WHERE id = :id LIMIT 1", ['id' => $row['id']]);

// Ambil semua playlist user jika login
$user_playlists = [];
if (logged_in()) {
	$user_playlists = db_query("SELECT * FROM playlists WHERE user_id = :uid", ['uid' => user('id')]);
}

// Proses submit add to playlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['song_id'], $_POST['playlist_id']) && logged_in()) {
	$song_id = (int) $_POST['song_id'];
	$playlist_id = (int) $_POST['playlist_id'];

	// Cek apakah lagu sudah ada
	$exists = db_query_one("SELECT * FROM playlist_items WHERE playlist_id = :pid AND song_id = :sid", [
		'pid' => $playlist_id,
		'sid' => $song_id
	]);

	if (!$exists) {
		db_query("INSERT INTO playlist_items (playlist_id, song_id) VALUES (:pid, :sid)", [
			'pid' => $playlist_id,
			'sid' => $song_id
		]);
		message("Song added to selected playlist.");
	} else {
		message("Song already exists in the selected playlist.");
	}
	redirect("song/" . $row['slug']);
}
?>

<!--start music card-->
<div class="music-card-full" style="max-width: 800px;">
	<h2 class="card-title"><?= esc($row['title']) ?></h2>
	<div class="card-subtitle">by: <?= esc(get_artist($row['artist_id'])) ?></div>

	<div style="overflow: hidden;">
		<a href="<?= ROOT ?>/song/<?= $row['slug'] ?>"><img src="<?= ROOT ?>/<?= $row['image'] ?>"></a>
	</div>

	<div class="card-content">
		<?php if (!empty($row['youtube_url'])): ?>
			<?php $video_id = get_youtube_id($row['youtube_url']); ?>
			<?php if ($video_id): ?>
				<div class="video-container">
					<iframe width="100%" height="300" 
							src="https://www.youtube.com/embed/<?= $video_id ?>?autoplay=0&controls=1" 
							frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
							allowfullscreen></iframe>
				</div>
			<?php else: ?>
				<div class="alert">Invalid YouTube link</div>
			<?php endif; ?>
		<?php else: ?>
			<audio controls style="width: 100%">
				<source src="<?= ROOT ?>/<?= $row['file'] ?>" type="audio/mpeg">
			</audio>
		<?php endif; ?>

		<?php if (!empty($row['lyrics'])): ?>
			<div class="lyrics-box" style="margin-top: 2em;">
				<h4>Lyrics</h4>
				<div id="lyrics-preview" style="white-space: pre-wrap; overflow: hidden; max-height: 6em;">
					<?= nl2br(esc(substr($row['lyrics'], 0, 600))) ?>...
				</div>
				<div id="lyrics-full" style="display: none; white-space: pre-wrap;">
					<?= nl2br(esc($row['lyrics'])) ?>
				</div>
				<button onclick="toggleLyrics()" class="btn bg-orange mt-2" id="toggle-lyrics-btn">Full lyrics</button>
			</div>

			<script>
				function toggleLyrics() {
					const preview = document.getElementById('lyrics-preview');
					const full = document.getElementById('lyrics-full');
					const button = document.getElementById('toggle-lyrics-btn');

					if (full.style.display === 'none') {
						preview.style.display = 'none';
						full.style.display = 'block';
						button.textContent = 'Hide lyrics';
					} else {
						preview.style.display = 'block';
						full.style.display = 'none';
						button.textContent = 'Full lyrics';
					}
				}
			</script>
		<?php endif; ?>

		<?php if (logged_in() && user('role') === 'user'): ?>
			<?php if (!empty($user_playlists)): ?>
				<form method="post" style="margin-top: 1em;">
					<input type="hidden" name="song_id" value="<?= $row['id'] ?>">
					<select name="playlist_id" class="form-control my-1" required>
						<option value="">-- Select Playlist --</option>
						<?php foreach ($user_playlists as $pl): ?>
							<option value="<?= $pl['id'] ?>"><?= esc($pl['name']) ?></option>
						<?php endforeach; ?>
					</select>
					<button class="btn bg-orange">Add to Playlist</button>
				</form>
			<?php else: ?>
				<div class="alert">You don't have any playlists yet. <a href="<?=ROOT?>/user/playlists">Create one here</a>.</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
<!--end music card-->
