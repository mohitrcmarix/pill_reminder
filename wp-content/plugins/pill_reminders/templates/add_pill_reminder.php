<?php
function add_pill_reminder()
{
	ob_start();
	if (!is_user_logged_in()) {
		wp_redirect(home_url('/sign-in/'));
		exit;
	}

	$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
	$reminder = null;

	if ($edit_id > 0) {
		global $wpdb;
		$table = $wpdb->prefix . 'pill_reminders';

		$reminder = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table WHERE id = %d",
			$edit_id
		));

		// 	echo "<pre>";
		// print_r($reminder);
		// echo "</pre>";

		if (!$reminder) {
			echo '<div class="alert alert-danger">Reminder not found or you don\'t have permission.</div>';
		}
	}
	?>
	<div class="page-content bg-white">
		<section class="bg-white content-inner-3">
			<form action="" method="POST" autocomplete="on" id="reminderForm">
				<div class="container">
					<div class="row justify-content-center mb-3">
						<div class="col-12 m-b30 mb-lg-0 d-flex border-shop">
							<div class="card shop-card shadow-none mb-lg-0 w-100">
								<div class="card-body">
									<div class="border-bottom mb-3">
										<h4 class="fw-bold pb-2">
											<?php echo $edit_id > 0 ? 'Edit Pill Reminder' : 'Fill below info to add reminder'; ?>
										</h4>
									</div>
									<?php if ($edit_id > 0): ?>
										<input type="hidden" name="edit_id" value="<?php echo esc_attr($edit_id); ?>">
									<?php endif; ?>
									<div>
										<label class="label-title m-b10">Title for reminder</label>
										<input type="text" name="title" class="form-control form-control-solid"
											placeholder="Type here"
											value="<?php echo $reminder ? esc_attr($reminder->reminder_title) : ''; ?>">
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row justify-content-center mb-3">
						<div class="col-12 m-b30 mb-lg-0 d-flex border-shop">
							<div class="card shop-card shadow-none mb-lg-0 w-100">
								<div class="card-body">
									<div class="border-bottom mb-3">
										<h4 class="fw-bold pb-2">Add Medicine information</h4>
									</div>
									<div class="row align-items-center gy-3">
										<div class="col-lg-12">
											<div class="row align-items-center">
												<div class="col-lg">
													<div>
														<label class="label-title m-b10">Medicine Name</label>
														<input name="medicine_name" required
															class="form-control form-control-solid" placeholder="Type here"
															type="text"
															value="<?php echo $reminder ? esc_attr($reminder->medicine_name) : ''; ?>">
													</div>
												</div>
											</div>
										</div>
										<div class="col-lg-12">
											<div class="row align-items-center">
												<div class="col-lg-6">
													<label class="label-title m-b10">Set Dose</label>
													<div class="row align-items-center">
														<div class="col-lg-6">
															<select class="form-select form-control-solid" name="dose_type">
																<option value="">Select</option>
																<option value="spoon" <?php echo ($reminder && $reminder->dose_type == 'spoon') ? 'selected' : ''; ?>>
																	Spoon</option>
																<option value="ml" <?php echo ($reminder && $reminder->dose_type == 'ml') ? 'selected' : ''; ?>>
																	Milliliter</option>
																<option value="mm" <?php echo ($reminder && $reminder->dose_type == 'mm') ? 'selected' : ''; ?>>
																	Millimeter</option>
																<option value="number" <?php echo ($reminder && $reminder->dose_type == 'number') ? 'selected' : ''; ?>>
																	Number</option>
															</select>
														</div>
														<div class="col-lg-6">
															<div
																class="dose-wrapper add-cart d-flex align-items-center gap-1">
																<a href="#" class="btn-minus lh-normal"><svg width="27"
																		height="26" viewBox="0 0 27 26" fill="none"
																		xmlns="http://www.w3.org/2000/svg">
																		<circle cx="13.5" cy="13" r="12.75" stroke="#E0E0DF"
																			stroke-width="0.5" />
																		<path
																			d="M17.5801 14.1693H9.41012C8.87179 14.1693 8.4375 13.8036 8.4375 13.3503C8.4375 12.897 8.87179 12.5312 9.41012 12.5312H17.5801C18.1185 12.5312 18.5527 12.897 18.5527 13.3503C18.5527 13.8036 18.1185 14.1693 17.5801 14.1693Z"
																			fill="#1A1A1A" />
																	</svg></a>
																<input type="number"
																	class="dose-input form-control-solid d-block bg-white text-center"
																	value="<?php echo $reminder ? esc_attr($reminder->dose_value) : ''; ?>"
																	name="dose_value">

																<a href="#" class="btn-plus lh-normal"><svg width="25"
																		height="26" viewBox="0 0 25 26" fill="none"
																		xmlns="http://www.w3.org/2000/svg">
																		<circle cx="12.5" cy="13" r="12.5" fill="#1A1A1A" />
																		<path
																			d="M13.3508 13.8534L17.3429 13.8534C17.5707 13.8534 17.7891 13.7629 17.9501 13.6019C18.1111 13.4408 18.2016 13.2224 18.2016 12.9947C18.2016 12.767 18.1111 12.5486 17.9501 12.3876C17.7891 12.2266 17.5707 12.1361 17.3429 12.1361L13.3508 12.1361L13.3488 8.14599C13.3488 7.91827 13.2583 7.69987 13.0973 7.53885C12.9363 7.37783 12.7179 7.28736 12.4902 7.28736C12.2624 7.28736 12.044 7.37783 11.883 7.53885C11.722 7.69987 11.6315 7.91827 11.6315 8.14599L11.6336 12.1361L7.64345 12.1381C7.5307 12.1381 7.41905 12.1603 7.31487 12.2035C7.2107 12.2466 7.11604 12.3099 7.03631 12.3896C6.95658 12.4693 6.89333 12.564 6.85018 12.6682C6.80703 12.7723 6.78482 12.884 6.78482 12.9967C6.78482 13.1095 6.80703 13.2212 6.85018 13.3253C6.89333 13.4295 6.95658 13.5242 7.03631 13.6039C7.11604 13.6836 7.2107 13.7469 7.31487 13.79C7.41905 13.8332 7.5307 13.8554 7.64345 13.8554L11.6336 13.8534L11.6336 17.8455C11.6336 18.0732 11.724 18.2916 11.885 18.4526C12.0461 18.6136 12.2645 18.7041 12.4922 18.7041C12.7199 18.7041 12.9383 18.6136 13.0993 18.4526C13.2604 18.2916 13.3508 18.0732 13.3508 17.8455L13.3508 13.8534Z"
																			fill="white" />
																	</svg></a>
															</div>
														</div>
													</div>
												</div>
												<div class="col-lg-6">
													<label class="label-title m-b10">Add Frequency</label>
													<select class="form-select form-control-solid" name="frequency">
														<option value="">Select</option>
														<option value="daily" <?php echo ($reminder && $reminder->frequency == 'daily') ? 'selected' : ''; ?>>Just once
														</option>
														<option value="onceaday" <?php echo ($reminder && $reminder->frequency == 'onceaday') ? 'selected' : ''; ?>>Once a
															day</option>
														<option value="atnight" <?php echo ($reminder && $reminder->frequency == 'atnight') ? 'selected' : ""; ?>>At night
														</option>
														<option value="every4hrs" <?php echo ($reminder && $reminder->frequency == 'every4hrs') ? 'selected' : ''; ?>>Every 4
															hrs</option>
														<option value="every6hrs" <?php echo ($reminder && $reminder->frequency == 'every6hrs') ? 'selected' : ''; ?>>Every 6
															hrs</option>
														<option value="every8hrs" <?php echo ($reminder && $reminder->frequency == 'every8hrs') ? 'selected' : ''; ?>>Every 8
															hrs</option>
														<option value="every12hrs" <?php echo ($reminder && $reminder->frequency == 'every12hrs') ? 'selected' : ''; ?>>Every
															12 hrs</option>
													</select>
												</div>
											</div>
										</div>
										<div class="col-lg-12">
											<div class="row align-items-center">
												<div class="col-lg-6">
													<label class="label-title">Set Duration</label>
													<div class="row align-items-center">
														<div class="col-lg-6">
															<select class="form-select form-control-solid"
																name="duration_type">
																<option value="">Select</option>
																<option value="6-Month" <?php echo ($reminder && $reminder->duration_type == '6-Month') ? 'selected' : ''; ?>>
																	6 Month</option>
																<option value="1-Year" <?php echo ($reminder && $reminder->duration_type == '1-Year') ? 'selected' : ''; ?>>1
																	Year</option>
																<option value="Life-time" <?php echo ($reminder && $reminder->duration_type == 'Lift-time') ? 'selected' : ''; ?>>Life time</option>
															</select>
														</div>
														<div class="col-lg-6">
															<div
																class="dose-wrapper add-cart d-flex align-items-center gap-1">
																<a href="#" class="btn-minus lh-normal"><svg width="27"
																		height="26" viewBox="0 0 27 26" fill="none"
																		xmlns="http://www.w3.org/2000/svg">
																		<circle cx="13.5" cy="13" r="12.75" stroke="#E0E0DF"
																			stroke-width="0.5" />
																		<path
																			d="M17.5801 14.1693H9.41012C8.87179 14.1693 8.4375 13.8036 8.4375 13.3503C8.4375 12.897 8.87179 12.5312 9.41012 12.5312H17.5801C18.1185 12.5312 18.5527 12.897 18.5527 13.3503C18.5527 13.8036 18.1185 14.1693 17.5801 14.1693Z"
																			fill="#1A1A1A" />
																	</svg></a>
																<input type="number" name="duration_value"
																	class="dose-input form-control-solid d-block bg-white text-center"
																	value="<?php echo $reminder ? esc_attr($reminder->duration_value) : ''; ?>">
																<a href="#" class="btn-plus lh-normal"><svg width="25"
																		height="26" viewBox="0 0 25 26" fill="none"
																		xmlns="http://www.w3.org/2000/svg">
																		<circle cx="12.5" cy="13" r="12.5" fill="#1A1A1A" />
																		<path
																			d="M13.3508 13.8534L17.3429 13.8534C17.5707 13.8534 17.7891 13.7629 17.9501 13.6019C18.1111 13.4408 18.2016 13.2224 18.2016 12.9947C18.2016 12.767 18.1111 12.5486 17.9501 12.3876C17.7891 12.2266 17.5707 12.1361 17.3429 12.1361L13.3508 12.1361L13.3488 8.14599C13.3488 7.91827 13.2583 7.69987 13.0973 7.53885C12.9363 7.37783 12.7179 7.28736 12.4902 7.28736C12.2624 7.28736 12.044 7.37783 11.883 7.53885C11.722 7.69987 11.6315 7.91827 11.6315 8.14599L11.6336 12.1361L7.64345 12.1381C7.5307 12.1381 7.41905 12.1603 7.31487 12.2035C7.2107 12.2466 7.11604 12.3099 7.03631 12.3896C6.95658 12.4693 6.89333 12.564 6.85018 12.6682C6.80703 12.7723 6.78482 12.884 6.78482 12.9967C6.78482 13.1095 6.80703 13.2212 6.85018 13.3253C6.89333 13.4295 6.95658 13.5242 7.03631 13.6039C7.11604 13.6836 7.2107 13.7469 7.31487 13.79C7.41905 13.8332 7.5307 13.8554 7.64345 13.8554L11.6336 13.8534L11.6336 17.8455C11.6336 18.0732 11.724 18.2916 11.885 18.4526C12.0461 18.6136 12.2645 18.7041 12.4922 18.7041C12.7199 18.7041 12.9383 18.6136 13.0993 18.4526C13.2604 18.2916 13.3508 18.0732 13.3508 17.8455L13.3508 13.8534Z"
																			fill="white" />
																	</svg></a>
															</div>
														</div>
													</div>
												</div>
												<div class="col-lg-6">
													<label class="label-title">Medicine Instructions</label>
													<div class="row align-items-end">
														<div class="col-lg-6">
															<select class="form-select form-control-solid"
																name="instruction">
																<option value="">Select</option>
																<option value="beforefood" <?php echo ($reminder && $reminder->instruction == 'beforefood') ? 'selected' : ''; ?>>Before food</option>
																<option value="withfood" <?php echo ($reminder && $reminder->instruction == 'withfood') ? 'selected' : ''; ?>>
																	With food</option>
																<option value="afterfood" <?php echo ($reminder && $reminder->instruction == 'afterfood') ? 'selected' : ''; ?>>
																	After food</option>
															</select>
														</div>
														<div class="col-lg-6">
															<input class="form-control-solid form-control-lg" type="time"
																name="time" value="">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row justify-content-center mb-3">
						<div class="col-12 m-b30 mb-lg-0 d-flex border-shop">
							<div class="card shop-card shadow-none mb-lg-0 w-100">
								<div class="card-body">
									<div class="border-bottom mb-3">
										<h4 class="fw-bold pb-2">Other information</h4>
									</div>
									<div class="row align-items-center gy-3">
										<div class="col-lg-12">
											<div class="row">
												<div class="col-lg-6">
													<div class="row align-items-center">
														<div class="col-lg-6">
															<label class="label-title m-b10">Select Reminder Date</label>
															<input name="from_date" required=""
																class="form-control form-control-solid"
																placeholder="Type here" type="date"
																value="<?php echo $reminder ? esc_attr($reminder->from_date) : ''; ?>">
														</div>
														<div class="col-lg-6">
															<label class="label-title opacity-0 m-b10">.</label>
															<input name="to_date" required=""
																class="form-control form-control-solid"
																placeholder="Type here" type="date"
																value="<?php echo $reminder ? esc_attr($reminder->to_date) : ''; ?>">
														</div>
													</div>
												</div>
												<div class="col-lg-6">
													<label class="label-title m-b10">Email</label>
													<input name="email" required="" class="form-control form-control-solid"
														placeholder="Type here" type="email"
														value="<?php echo $reminder ? esc_attr($reminder->email) : ''; ?>">
												</div>
											</div>
										</div>

										<div class="col-lg-12">
											<label class="label-title m-b10">Add Reminder Time</label>

											<?php
											$reminder_times = array();

											if ($reminder && !empty($reminder->reminder_times)) {
												// Case 1: If stored as serialized array
												$reminder_times = maybe_unserialize($reminder->reminder_times);

												// Case 2: If stored as JSON
												if (!is_array($reminder_times)) {
													$reminder_times = json_decode($reminder->reminder_times, true);
												}

												// Case 3: If stored as comma-separated (e.g. "08:00,14:30,20:00")
												if (!is_array($reminder_times)) {
													$reminder_times = array_filter(array_map('trim', explode(',', $reminder->reminder_times)));
												}
											}

											// If no times exist (new reminder or empty), show at least one empty field
											if (empty($reminder_times)) {
												$reminder_times = array('');
											}
											?>

											<?php foreach ($reminder_times as $index => $time_value): ?>
												<div class="row mb-2 reminder-time-row">
													<div class="col-lg-10">
														<input name="times[]" class="form-control form-control-solid"
															type="time" value="<?php echo esc_attr($time_value); ?>">
													</div>
													<div class="col-lg-auto">
														<?php if ($index === 0): ?>
															<!-- Add more button (first row) -->
															<a href="#" class="font-16 font-weight-500 add-time-btn">
																<svg width="25" height="26" viewBox="0 0 25 26" fill="none"
																	xmlns="http://www.w3.org/2000/svg">
																	<a href="#" class="btn-plus lh-normal"><svg width="25"
																			height="26" viewBox="0 0 25 26" fill="none"
																			xmlns="http://www.w3.org/2000/svg">
																			<circle cx="12.5" cy="13" r="12.5" fill="#1A1A1A" />
																			<path
																				d="M13.3508 13.8534L17.3429 13.8534C17.5707 13.8534 17.7891 13.7629 17.9501 13.6019C18.1111 13.4408 18.2016 13.2224 18.2016 12.9947C18.2016 12.767 18.1111 12.5486 17.9501 12.3876C17.7891 12.2266 17.5707 12.1361 17.3429 12.1361L13.3508 12.1361L13.3488 8.14599C13.3488 7.91827 13.2583 7.69987 13.0973 7.53885C12.9363 7.37783 12.7179 7.28736 12.4902 7.28736C12.2624 7.28736 12.044 7.37783 11.883 7.53885C11.722 7.69987 11.6315 7.91827 11.6315 8.14599L11.6336 12.1361L7.64345 12.1381C7.5307 12.1381 7.41905 12.1603 7.31487 12.2035C7.2107 12.2466 7.11604 12.3099 7.03631 12.3896C6.95658 12.4693 6.89333 12.564 6.85018 12.6682C6.80703 12.7723 6.78482 12.884 6.78482 12.9967C6.78482 13.1095 6.80703 13.2212 6.85018 13.3253C6.89333 13.4295 6.95658 13.5242 7.03631 13.6039C7.11604 13.6836 7.2107 13.7469 7.31487 13.79C7.41905 13.8332 7.5307 13.8554 7.64345 13.8554L11.6336 13.8534L11.6336 17.8455C11.6336 18.0732 11.724 18.2916 11.885 18.4526C12.0461 18.6136 12.2645 18.7041 12.4922 18.7041C12.7199 18.7041 12.9383 18.6136 13.0993 18.4526C13.2604 18.2916 13.3508 18.0732 13.3508 17.8455L13.3508 13.8534Z"
																				fill="white" />
																		</svg></a>
																</svg>
																<span>Add more</span>
															</a>
														<?php else: ?>
															<!-- Remove button -->
															<a href="#" class="font-16 font-weight-500 text-danger remove-time-btn">
																<svg width="25" height="26" viewBox="0 0 25 26" fill="none"
																	xmlns="http://www.w3.org/2000/svg">
																	<svg width="25" height="26" viewBox="0 0 25 26" fill="none"
																		xmlns="http://www.w3.org/2000/svg">
																		<circle cx="12.5" cy="13" r="12.5" fill="#FF2A2A" />
																		<path
																			d="M13.7065 12.9947L16.5293 10.1719C16.6903 10.0108 16.7808 9.79245 16.7808 9.56473C16.7808 9.33701 16.6903 9.11861 16.5293 8.95759C16.3683 8.79656 16.1499 8.7061 15.9222 8.7061C15.6945 8.7061 15.4761 8.79656 15.315 8.95759L12.4922 11.7804L9.66932 8.96044C9.5083 8.79942 9.2899 8.70896 9.06218 8.70896C8.83446 8.70896 8.61606 8.79942 8.45504 8.96044C8.29401 9.12147 8.20355 9.33986 8.20355 9.56759C8.20355 9.79531 8.29401 10.0137 8.45504 10.1747L11.2779 12.9947L8.4579 15.8176C8.37817 15.8973 8.31492 15.992 8.27177 16.0961C8.22862 16.2003 8.20641 16.312 8.20641 16.4247C8.20641 16.5375 8.22862 16.6491 8.27177 16.7533C8.31492 16.8575 8.37817 16.9521 8.4579 17.0319C8.53763 17.1116 8.63228 17.1749 8.73646 17.218C8.84063 17.2612 8.95228 17.2834 9.06504 17.2834C9.1778 17.2834 9.28945 17.2612 9.39362 17.218C9.4978 17.1749 9.59245 17.1116 9.67218 17.0319L12.4922 14.209L15.315 17.0319C15.4761 17.1929 15.6945 17.2834 15.9222 17.2834C16.1499 17.2834 16.3683 17.1929 16.5293 17.0319C16.6903 16.8708 16.7808 16.6525 16.7808 16.4247C16.7808 16.197 16.6903 15.9786 16.5293 15.8176L13.7065 12.9947Z"
																			fill="white" />
																	</svg>
																</svg>
																<span>Remove</span>
															</a>
														<?php endif; ?>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div>
						<button type="submit" class="btn btn-danger px-lg-5 fw-semibold btnhover p-3" name="set_reminder">
							<?php echo $edit_id > 0 ? 'Update Reminder' : 'Set Reminder'; ?>
						</button>
					</div>
				</div>
			</form>
		</section>
	</div>
	<?php
	return ob_get_clean();
}
