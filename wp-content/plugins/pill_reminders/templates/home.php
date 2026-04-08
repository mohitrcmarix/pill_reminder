<?php
function home()
{
	ob_start();
	?>
	<div class="page-content bg-white">
		<section class="bg-white content-inner-3">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-10 text-center">
						<h3 class="font-25 fw-bold text-secondary m-b15">Pill Reminders</h3>

						<?php
						$reminders = [];
					
						if (empty($reminders)) {
							?>
							<p class="font-16 fw-medium text-secondary m-b30 text-center">
								There are no any pill reminder.<br>
								Click the button below to add reminder.
							</p>
							<a href="<?php echo esc_url(home_url('/add_pill_reminder/')); ?>"
								class="btn btn-danger fw-semibold btnhover p-3">
								Add Reminder
							</a>
							<?php
						} else {
							echo '<ul class="pill-reminder-list">';
							echo '</ul>';
						}
						?>
					</div>
				</div>
			</div>
		</section>
	</div>
	<?php
	return ob_get_clean();
}
