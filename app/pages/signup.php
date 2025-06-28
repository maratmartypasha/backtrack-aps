<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $errors = [];

    // Validasi Username
    if (empty($_POST['username'])) {
        $errors['username'] = "Username wajib diisi";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $_POST['username'])) {
        $errors['username'] = "Username hanya boleh huruf tanpa spasi";
    } else {
        // Cek apakah username sudah ada
        $check = db_query_one("SELECT * FROM users WHERE username = :username", ['username' => trim($_POST['username'])]);
        if ($check) {
            $errors['username'] = "Username sudah digunakan";
        }
    }

    // Validasi Email
    if (empty($_POST['email'])) {
        $errors['email'] = "Email wajib diisi";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email tidak valid";
    } else {
        // Cek apakah email sudah ada
        $check = db_query_one("SELECT * FROM users WHERE email = :email", ['email' => trim($_POST['email'])]);
        if ($check) {
            $errors['email'] = "Email sudah digunakan";
        }
    }

    // Validasi Password
    if (empty($_POST['password'])) {
        $errors['password'] = "Password wajib diisi";
    } elseif ($_POST['password'] != $_POST['retype_password']) {
        $errors['password'] = "Konfirmasi password tidak cocok";
    } elseif (strlen($_POST['password']) < 8) {
        $errors['password'] = "Password minimal 8 karakter";
    }

    // Jika tidak ada error, simpan data
    if (empty($errors)) {
        $values = [];
        $values['username'] = trim($_POST['username']);
        $values['email']    = trim($_POST['email']);
        $values['role']     = 'user'; // role otomatis user
        $values['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $values['date']     = date("Y-m-d H:i:s");

        $query = "INSERT INTO users (username, email, password, role, date)
                  VALUES (:username, :email, :password, :role, :date)";
        db_query($query, $values);

        message("Pendaftaran berhasil. Silakan login.");
        redirect('login');
    }
}
?>

<?php require page('includes/header') ?>

<section class="content">
    <div class="login-holder" style="max-width: 500px; margin: auto;">
        <h2>Create your account!</h2>

        <?php if (message()): ?>
            <div class="alert"><?= message('', true) ?></div>
        <?php endif; ?>

        <form method="post">
            <input class="form-control my-1" value="<?= set_value('username') ?>" type="text" name="username" placeholder="Username">
            <?php if (!empty($errors['username'])): ?>
                <small class="error"><?= $errors['username'] ?></small>
            <?php endif; ?>

            <input class="form-control my-1" value="<?= set_value('email') ?>" type="email" name="email" placeholder="Email">
            <?php if (!empty($errors['email'])): ?>
                <small class="error"><?= $errors['email'] ?></small>
            <?php endif; ?>

            <input class="form-control my-1" type="password" name="password" placeholder="Password">
            <?php if (!empty($errors['password'])): ?>
                <small class="error"><?= $errors['password'] ?></small>
            <?php endif; ?>

            <input class="form-control my-1" type="password" name="retype_password" placeholder="Confirm Password">

            <button class="btn bg-orange my-1">Sign Up</button>

            <p style="text-align:center; margin-top:10px;">
                Already have an account? <a href="<?= ROOT ?>/login">Login</a>
			</p>            
        </form>
    </div>
</section>

<?php require page('includes/footer') ?>
