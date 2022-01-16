	<hr>
	<footer>
		<div class="container-fluid">
			<p class="copyright">
				&copy; <?php echo date("Y"); ?> Thimineur
			</p>
			
			<!-- Logout link -->
			<?php if (!isset($no_logout)): ?>
				<div style="padding-bottom:10px;">
					<a href="logout.php">Logout<a>
				</div>
				<?php unset($no_logout); ?>
			<?php endif; ?>
		</div>
	</footer>
</body>
</html>