<?php require page('includes/header')?>
	
	<center><div class="section-title">Now Playing</div></center>

	<section class="content">
		
		<?php 
			$slug = $URL[1] ?? null;
			$query = "select * from songs where slug = :slug limit 1";
			$row = db_query_one($query,['slug'=>$slug]);

		?>

		<?php if(!empty($row)):?>
			<?php include page('song-full')?>
		<?php endif;?>

	</section>

<?php require page('includes/footer')?>