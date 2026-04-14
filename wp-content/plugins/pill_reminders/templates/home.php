<?php
function home()
{
	ob_start();
	$image = plugin_dir_url(dirname(__FILE__));
	$imageurl = $image . 'assets/img/banners/b1.png';
	?>
	<div class="page-content bg-white">
		<!--Banner Start-->
		<div class="dz-bnr-inr bg-secondary" style="background-image:url(<?php echo esc_url($imageurl); ?>);">
			<div class="container">
				<div class="dz-bnr-inr-entry">
					<h1 class="font-42 fw-bold">Pill Reminders</h1>
					<nav aria-label="breadcrumb" class="breadcrumb-row">
						<ul class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo esc_url(home_url())?>"> Home</a></li>
							<li class="breadcrumb-item"><svg width="8" height="16" viewBox="0 0 8 16" fill="none"
									xmlns="http://www.w3.org/2000/svg">
									<path
										d="M0.417091 0.929708C0.171254 1.22153 0.03705 1.58614 0.03705 1.96223C0.03705 2.33831 0.171254 2.70293 0.417091 2.99475L4.85987 7.98391L0.417091 12.9749C0.241979 13.1675 0.11673 13.3975 0.0516365 13.6459C-0.013457 13.8944 -0.0165382 14.1542 0.0426466 14.404C0.0879344 14.6437 0.198499 14.8676 0.363001 15.0525C0.527502 15.2374 0.74002 15.3768 0.978757 15.4564C1.20981 15.519 1.45513 15.5127 1.68239 15.4382C1.90965 15.3637 2.10819 15.2245 2.25187 15.039L7.61952 9.01733C7.86536 8.72551 7.99957 8.3609 7.99957 7.98481C7.99957 7.60873 7.86536 7.24411 7.61952 6.95229L2.25374 0.928804C2.14334 0.795328 2.00321 0.687558 1.84375 0.613492C1.68428 0.539426 1.50958 0.500965 1.33261 0.500965C1.15563 0.500965 0.980932 0.539426 0.821468 0.613492C0.662004 0.687558 0.521873 0.795328 0.411474 0.928804L0.417091 0.929708Z"
										fill="white" />
								</svg></li>
							<li class="breadcrumb-item">Pill Reminders</li>
						</ul>
					</nav>
				</div>
			</div>
		</div>
		<!--Banner End-->
		<section class="bg-white content-inner-3">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-10 text-center">
						<h3 class="font-25 fw-bold text-secondary m-b15">Pill Reminders</h3>

						<?php

						if (!is_user_logged_in()) {
							?>
							<p class="font-16 fw-medium text-secondary m-b30 text-center">
								There are no any pill reminder.<br>
								Click the button below to add reminder.
							</p>
							<a href="<?php echo esc_url(home_url('/sign-in/')); ?>"
								class="btn btn-danger fw-semibold btnhover p-3">
								Add Reminder
							</a>
							<?php
						} else {
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
