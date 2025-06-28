<?php require page('includes/user-header') ?>

<?php
if (user('role') !== 'user') {
    message("Only regular users can access this section.");
    redirect('');
}

$user_id      = user('id');
$new_name     = $_POST['new_playlist'] ?? null;
$playlist_id  = $_GET['id'] ?? null;
$remove_song  = $_GET['remove_song'] ?? null;
$remove_pl    = $_GET['remove_pl'] ?? null;

// Buat playlist baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $new_name) {
    db_query("INSERT INTO playlists (user_id, name) VALUES (:uid, :name)", [
        'uid' => $user_id,
        'name' => trim($new_name)
    ]);
    message("Playlist \"$new_name\" created.");
    redirect('user/playlists');
}

// Hapus playlist
if ($remove_pl) {
    db_query("DELETE FROM playlist_items WHERE playlist_id = :pid", ['pid' => $remove_pl]);
    db_query("DELETE FROM playlists WHERE id = :pid", ['pid' => $remove_pl]);
    message("Playlist deleted.");
    redirect('user/playlists');
}

// Hapus lagu dari playlist
if ($remove_song && $playlist_id) {
    db_query("DELETE FROM playlist_items WHERE playlist_id = :pid AND song_id = :sid", [
        'pid' => $playlist_id,
        'sid' => $remove_song
    ]);
    message("Song removed.");
    redirect("user/playlists?id=$playlist_id");
}

// Ambil semua playlist user
$playlists = db_query("SELECT * FROM playlists WHERE user_id = :uid", ['uid' => $user_id]);
if (!is_array($playlists)) $playlists = [];

// Jika ada id playlist, ambil lagu
$songs = [];
$current = null;
if ($playlist_id) {
    $current = db_query_one("SELECT * FROM playlists WHERE id = :pid AND user_id = :uid", [
        'pid' => $playlist_id, 'uid' => $user_id
    ]);
    if ($current) {
        $songs = db_query("SELECT songs.* FROM playlist_items
            JOIN songs ON playlist_items.song_id = songs.id
            WHERE playlist_items.playlist_id = :pid", ['pid' => $playlist_id]);
        if (!is_array($songs)) $songs = [];
    }
}
?>

<section class="user-content" style="min-height:200px">
    <h3>🎵 My Playlists</h3>

    <form method="post" class="playlist-form">
        <label for="new_playlist">Create New Playlist</label>
        <input type="text" name="new_playlist" placeholder="Playlist name" class="form-control my-1" required>
        <button type="submit" class="btn bg-purple">Add Playlist</button>
    </form>

    <div class="playlist-grid">
        <?php foreach ($playlists as $pl): ?>
            <div class="playlist-card">
                <a href="<?=ROOT?>/user/playlists?id=<?=$pl['id']?>"><?=esc($pl['name'])?></a>
                <a class="playlist-delete" href="<?=ROOT?>/user/playlists?remove_pl=<?=$pl['id']?>" onclick="return confirm('Delete playlist?')">
                    <img class="bi" src="<?=ROOT?>/assets/icons/trash3.svg" alt="Delete" width="16">
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <hr style="margin-top: 30px;">

    <?php if ($current): ?>
        <h4>📂 Songs in "<?=esc($current['name'])?>"</h4>

        <?php if (!empty($songs)): ?>
            <table class="table">
                <tr><th>Image</th><th>Title</th><th>Artist</th><th>Category</th><th>Action</th></tr>
                <?php foreach ($songs as $row): ?>
                    <tr>
                        <td style="width:80px;">
                            <a href="<?=ROOT?>/song/<?=$row['slug']?>">
                                <img src="<?=ROOT?>/<?=$row['image']?>" style="width:60px; height:60px; object-fit:cover; border-radius:5px;">
                            </a>
                        </td>
                        <td>
                            <a href="<?=ROOT?>/song/<?=$row['slug']?>"><?=esc($row['title'])?></a>
                        </td>
                        <td>
                            <a href="<?=ROOT?>/artist/<?=$row['artist_id']?>">
                                <?=esc(get_artist($row['artist_id']))?>
                            </a>
                        </td>
                        <td>
                            <a href="<?=ROOT?>/category/<?=urlencode(get_category($row['category_id']))?>">
                                <?=esc(get_category($row['category_id']))?>
                            </a>
                        </td>
                        <td>
                            <a href="<?=ROOT?>/user/playlists?id=<?=$current['id']?>&remove_song=<?=$row['id']?>" onclick="return confirm('Remove this song?')">
                                <img class="bi" src="<?=ROOT?>/assets/icons/trash3.svg" width="18">
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <div class="m-2">No songs in this playlist yet.</div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php require page('includes/user-footer') ?>
