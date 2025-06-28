<?php require page('includes/user-header') ?>

<?php
$id = user('id');
$row = db_query_one("SELECT * FROM users WHERE id = :id LIMIT 1", ['id' => $id]);

if ($_SERVER['REQUEST_METHOD'] == "POST" && $row) {

	$errors = [];

	// Validasi input
	if (empty($_POST['username'])) {
		$errors['username'] = "A username is required";
	} elseif (!preg_match("/^[a-zA-Z]+$/", $_POST['username'])) {
		$errors['username'] = "Username can only contain letters with no spaces";
	}

	if (empty($_POST['email'])) {
		$errors['email'] = "An email is required";
	} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$errors['email'] = "Email is not valid";
	}

	if (!empty($_POST['password'])) {
		if ($_POST['password'] != $_POST['retype_password']) {
			$errors['password'] = "Passwords do not match";
		} elseif (strlen($_POST['password']) < 8) {
			$errors['password'] = "Password must be at least 8 characters";
		}
	}

	if (empty($errors)) {
		$values = [];
		$values['username'] = trim($_POST['username']);
		$values['email']    = trim($_POST['email']);
		$values['id']       = $id;

		$query = "UPDATE users SET username = :username, email = :email";

		if (!empty($_POST['password'])) {
			$query .= ", password = :password";
			$values['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
		}

		$query .= " WHERE id = :id LIMIT 1";

		db_query($query, $values);

		message("Profile updated successfully");
		redirect("user/profile");
	}
}
?>

<section class="user-content" style="min-height: 200px;">
	<div style="max-width: 500px;margin: auto;">
		<form method="post">
			<h3>Edit Profile</h3>

			<?php if (!empty($row)): ?>
				<input class="form-control my-1" value="<?= set_value('username', $row['username']) ?>" type="text" name="username" placeholder="Username">
				<?php if (!empty($errors['username'])): ?>
					<small class="error"><?= $errors['username'] ?></small>
				<?php endif; ?>

				<input class="form-control my-1" value="<?= set_value('email', $row['email']) ?>" type="email" name="email" placeholder="Email">
				<?php if (!empty($errors['email'])): ?>
					<small class="error"><?= $errors['email'] ?></small>
				<?php endif; ?>

				<input class="form-control my-1" value="<?= set_value('password') ?>" type="password" name="password" placeholder="New Password (optional)">
				<?php if (!empty($errors['password'])): ?>
					<small class="error"><?= $errors['password'] ?></small>
				<?php endif; ?>

				<input class="form-control my-1" value="<?= set_value('retype_password') ?>" type="password" name="retype_password" placeholder="Retype Password">

				<button class="btn bg-orange">Save</button>
			<?php else: ?>
				<div class="alert">User not found.</div>
			<?php endif; ?>
		</form>
	</div>
</section>

<?php require page('includes/user-footer') ?>
