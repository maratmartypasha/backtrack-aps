<?php 

if($action == 'add') {
	if($_SERVER['REQUEST_METHOD'] == "POST") {
		$errors = [];

		if(empty($_POST['title'])) {
			$errors['title'] = "a title is required";
		} else if(!preg_match("/^[a-zA-Z0-9 \.\&\-]+$/", $_POST['title'])){
			$errors['title'] = "a title can only have letters & spaces";
		}

		if(empty($_POST['category_id'])) {
			$errors['category_id'] = "a category is required";
		}

		if(empty($_POST['artist_id'])) {
			$errors['artist_id'] = "an artist is required";
		}

		$folder = "uploads/";
		if(!file_exists($folder)){
			mkdir($folder,0777,true);
			file_put_contents($folder."index.php", "");
		}

		if(!empty($_FILES['image']['name'])) {
			$allowed = ['image/jpeg','image/png'];
			if($_FILES['image']['error'] == 0 && in_array($_FILES['image']['type'], $allowed)) {
				$destination_image = $folder . $_FILES['image']['name'];
				move_uploaded_file($_FILES['image']['tmp_name'], $destination_image);
			} else {
				$errors['image'] = "image not valid. allowed types are " . implode(",", $allowed);
			}
		} else {
			$errors['image'] = "an image is required";
		}

		if(empty($errors)) {
			$values = [];
			$values['title'] = trim($_POST['title']);
			$values['category_id'] = trim($_POST['category_id']);
			$values['artist_id'] = trim($_POST['artist_id']);
			$values['youtube_url'] = trim($_POST['youtube_url']);
			$values['lyrics'] = trim($_POST['lyrics']);
			$values['image'] = $destination_image;
			$values['user_id'] = user('id');
			$values['date'] = date("Y-m-d H:i:s");
			$values['views'] = 0;
			$values['slug'] = str_to_url($values['title']);

			$query = "INSERT INTO songs (title,image,user_id,category_id,artist_id,date,views,slug,youtube_url,lyrics)
			          VALUES (:title,:image,:user_id,:category_id,:artist_id,:date,:views,:slug,:youtube_url,:lyrics)";
			db_query($query,$values);

			message("song created successfully");
			redirect('admin/songs');
		}
	}
} else if($action == 'edit') {
	$query = "SELECT * FROM songs WHERE id = :id LIMIT 1";
	$row = db_query_one($query,['id'=>$id]);

	if($_SERVER['REQUEST_METHOD'] == "POST" && $row) {
		$errors = [];

		if(empty($_POST['title'])) {
			$errors['title'] = "a title is required";
		} else if(!preg_match("/^[a-zA-Z0-9 \.\&\-]+$/", $_POST['title'])){
			$errors['title'] = "a title can only have letters & spaces";
		}

		if(empty($_POST['category_id'])) {
			$errors['category_id'] = "a category is required";
		}

		if(empty($_POST['artist_id'])) {
			$errors['artist_id'] = "an artist is required";
		}

		$folder = "uploads/";
		if(!file_exists($folder)){
			mkdir($folder,0777,true);
			file_put_contents($folder."index.php", "");
		}

		if(!empty($_FILES['image']['name'])) {
			$allowed = ['image/jpeg','image/png'];
			if($_FILES['image']['error'] == 0 && in_array($_FILES['image']['type'], $allowed)) {
				$destination_image = $folder . $_FILES['image']['name'];
				move_uploaded_file($_FILES['image']['tmp_name'], $destination_image);
				if(file_exists($row['image'])) unlink($row['image']);
			} else {
				$errors['image'] = "image not valid. allowed types are " . implode(",", $allowed);
			}
		}

		if(empty($errors)) {
			$values = [];
			$values['title'] = trim($_POST['title']);
			$values['category_id'] = trim($_POST['category_id']);
			$values['artist_id'] = trim($_POST['artist_id']);
			$values['youtube_url'] = trim($_POST['youtube_url']);
			$values['lyrics'] = trim($_POST['lyrics']);
			$values['user_id'] = user('id');
			$values['id'] = $id;

			$query = "UPDATE songs SET title = :title, user_id = :user_id, category_id = :category_id,
			          artist_id = :artist_id, youtube_url = :youtube_url, lyrics = :lyrics";
			if(!empty($destination_image)){
				$query .= ", image = :image";
				$values['image'] = $destination_image;
			}
			$query .= " WHERE id = :id LIMIT 1";

			db_query($query,$values);

			message("song edited successfully");
			redirect('admin/songs');
		}
	}
} else if($action == 'delete') {
	$query = "SELECT * FROM songs WHERE id = :id LIMIT 1";
	$row = db_query_one($query,['id'=>$id]);

	if($_SERVER['REQUEST_METHOD'] == "POST" && $row) {
		$values = ['id' => $id];

		db_query("DELETE FROM songs WHERE id = :id LIMIT 1", $values);
		if(file_exists($row['image'])) unlink($row['image']);

		message("song deleted successfully");
		redirect('admin/songs');
	}
}

require page('includes/admin-header');
?>

<section class="admin-content" style="min-height: 200px;">

<?php if($action == 'add' || $action == 'edit'): ?>
	<?php if($action == 'edit') $row = $row ?? []; ?>
	<div style="max-width: 600px;margin:auto;">
		<form method="post" enctype="multipart/form-data">
			<h3><?= $action == 'edit' ? 'Edit' : 'Add' ?> Song</h3>
			<input name="title" class="form-control my-1" value="<?=set_value('title',$row['title'] ?? '')?>" placeholder="Title">
			<select name="category_id" class="form-control my-1">
				<option value="">-- Select Category --</option>
				<?php foreach(db_query("SELECT * FROM categories ORDER BY category ASC") as $c): ?>
					<option value="<?=$c['id']?>" <?=set_select('category_id',$c['id'],$row['category_id'] ?? '')?>><?=$c['category']?></option>
				<?php endforeach; ?>
			</select>
			<select name="artist_id" class="form-control my-1">
				<option value="">-- Select Artist --</option>
				<?php foreach(db_query("SELECT * FROM artists ORDER BY name ASC") as $a): ?>
					<option value="<?=$a['id']?>" <?=set_select('artist_id',$a['id'],$row['artist_id'] ?? '')?>><?=$a['name']?></option>
				<?php endforeach; ?>
			</select>

			<input name="youtube_url" class="form-control my-1" value="<?=set_value('youtube_url',$row['youtube_url'] ?? '')?>" placeholder="YouTube URL">
			<textarea name="lyrics" class="form-control my-1" rows="5" placeholder="Lyrics"><?=set_value('lyrics',$row['lyrics'] ?? '')?></textarea>

			<?php if(!empty($row['image'])): ?>
				<img src="<?=ROOT?>/<?=$row['image']?>" style="width:100px;height:100px;object-fit:cover;">
			<?php endif; ?>
			<input type="file" name="image" class="form-control my-1">

			<button class="btn bg-orange">Save</button>
			<a href="<?=ROOT?>/admin/songs"><button type="button" class="float-end btn">Back</button></a>
		</form>
	</div>

<?php elseif($action == 'delete' && !empty($row)): ?>
	<form method="post" style="max-width:500px;margin:auto;">
		<h3>Delete Song</h3>
		<div class="form-control my-1"><?= $row['title'] ?></div>
		<button class="btn bg-red">Delete</button>
		<a href="<?=ROOT?>/admin/songs"><button type="button" class="float-end btn">Back</button></a>
	</form>

<?php else: ?>
	<h3>Songs <a href="<?=ROOT?>/admin/songs/add"><button class="float-end btn bg-purple">Add New</button></a></h3>
	<table class="table">
		<tr>
			<th>ID</th><th>Title</th><th>Image</th><th>Category</th><th>Artist</th><th>YouTube</th><th>Lyrics</th><th>Action</th>
		</tr>
		<?php foreach(db_query("SELECT * FROM songs ORDER BY id DESC") as $s): ?>
		<tr>
			<td><?=$s['id']?></td>
			<td><?=esc($s['title'])?></td>
			<td><img src="<?=ROOT?>/<?=$s['image']?>" style="width:60px;height:60px;object-fit:cover;"></td>
			<td><?=esc(get_category($s['category_id']))?></td>
			<td><?=esc(get_artist($s['artist_id']))?></td>
			<td><a href="<?=esc($s['youtube_url'])?>" target="_blank">Link</a></td>
			<td><?=substr(strip_tags($s['lyrics']),0,30)?>...</td>
			<td>
				<a href="<?=ROOT?>/admin/songs/edit/<?=$s['id']?>"><img class="bi" src="<?=ROOT?>/assets/icons/pencil-square.svg"></a>
				<a href="<?=ROOT?>/admin/songs/delete/<?=$s['id']?>"><img class="bi" src="<?=ROOT?>/assets/icons/trash3.svg"></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>

</section>

<?php require page('includes/admin-footer') ?>
