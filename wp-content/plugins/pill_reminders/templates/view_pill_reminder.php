<?php

function view_pill_reminder()
{
    ob_start();
    global $wpdb;
    $table = $wpdb->prefix . 'pill_reminders';
    $user_id = get_current_user_id();

    $results = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table WHERE user_id = %d OR user_id IS NULL OR user_id = 0", $user_id)
    );

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
                            <li class="breadcrumb-item"><svg width="8" height="16" viewBox="0 0 8 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.417091 0.929708C0.171254 1.22153 0.03705 1.58614 0.03705 1.96223C0.03705 2.33831 0.171254 2.70293 0.417091 2.99475L4.85987 7.98391L0.417091 12.9749C0.241979 13.1675 0.11673 13.3975 0.0516365 13.6459C-0.013457 13.8944 -0.0165382 14.1542 0.0426466 14.404C0.0879344 14.6437 0.198499 14.8676 0.363001 15.0525C0.527502 15.2374 0.74002 15.3768 0.978757 15.4564C1.20981 15.519 1.45513 15.5127 1.68239 15.4382C1.90965 15.3637 2.10819 15.2245 2.25187 15.039L7.61952 9.01733C7.86536 8.72551 7.99957 8.3609 7.99957 7.98481C7.99957 7.60873 7.86536 7.24411 7.61952 6.95229L2.25374 0.928804C2.14334 0.795328 2.00321 0.687558 1.84375 0.613492C1.68428 0.539426 1.50958 0.500965 1.33261 0.500965C1.15563 0.500965 0.980932 0.539426 0.821468 0.613492C0.662004 0.687558 0.521873 0.795328 0.411474 0.928804L0.417091 0.929708Z" fill="white" />
                                </svg></li>
                            <li class="breadcrumb-item">Pill Reminders</li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <!--Banner End-->

        <div id="pill-loader" class="pill-loader-overlay">
            <div class="pill-loader-spinner"></div>
        </div>

        <section class="bg-white content-inner-3">
            <div class="container">
                <div class="row align-items-center gy-3">
                    <div class="col-sm">
                        <h3 class="font-25 fw-bold text-secondary mb-0">Your pill reminders</h3>
                    </div>
                    <div class="col-sm-auto">
                        <a href="<?php echo home_url('/add_pill_reminder'); ?>" class="btn btn-danger fw-semibold btnhover p-3">Add New Reminder</a>
                    </div>
                </div>
            </div>

            <div class="container mt-4">
                <div class="row gy-3">
                    <?php
                    if ($results) {
                        foreach ($results as $row) {
                            $medicine_name = esc_html($row->medicine_name);
                            $dose_type     = esc_html($row->dose_type);
                            $frequency     = esc_html($row->frequency);
                            $instruction   = esc_html($row->instruction);
                            $from_date     = esc_html($row->from_date);
                            $to_date       = esc_html($row->to_date);
                            $medicine_id   = esc_html($row->id);
                            $status        = (int)$row->status;

                            // Clean and display reminder times nicely
                            $times_raw = trim($row->reminder_times);
                            $times_raw = preg_replace('/afterfood|withfood|beforefood|after food|before food.*/i', '', $times_raw);
                            $times_arr = json_decode($times_raw, true);

                            $times_display = is_array($times_arr) ? implode(', ', $times_arr) : esc_html($times_raw);

                            // Nice instruction display
                            $instruction_display = ucfirst(str_replace(['beforefood','withfood','afterfood'], 
                                ['Before Food', 'With Food', 'After Food'], strtolower($instruction)));
                    ?>
                            <div class="col-md-6">
                                <div class="card border bg-light">
                                    <div class="card-body">
                                        <div class="d-flex flex-row justify-content-between gap-3 mb-2">
                                            <h4 class="mb-4">
                                                <?php echo $medicine_name; ?> -
                                                <span style="background-color: <?php echo $status ? '#CFF3D1' : '#F3D1D1'; ?>; 
                                                    color: <?php echo $status ? '#388E3C' : '#8E3C3C'; ?>; 
                                                    font-size: 16px; padding: 6px 12px; border-radius: 5px;">
                                                    <?php echo $status ? 'Active' : 'Deactive'; ?>
                                                </span>
                                            </h4>

                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="medicine_id" value="<?php echo $medicine_id; ?>">
                                                <label class="switch-container">
                                                    <input type="checkbox" name="status" value="1" 
                                                        class="toggle-status" data-medicine-id="<?php echo $medicine_id; ?>"
                                                        <?php echo $status ? 'checked' : ''; ?> onchange="this.form.submit()">
                                                    <span class="switch-label"></span>
                                                </label>
                                            </form>

                                            <div class="dropdown">
                                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                                                    <svg width="6" height="26" viewBox="0 0 8 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="4" cy="4" r="4" fill="#7C7C7C" />
                                                        <circle cx="4" cy="16" r="4" fill="#7C7C7C" />
                                                        <circle cx="4" cy="28" r="4" fill="#7C7C7C" />
                                                    </svg>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="<?php echo home_url('/pill_reminder_details/'); ?>" class="dropdown-item">View</a>
                                                    <a href="<?php echo esc_url(add_query_arg('edit', $medicine_id, site_url('/add_pill_reminder/'))); ?>" class="dropdown-item">Edit</a>
                                                    <a href="javascript:;" class="dropdown-item text-danger delete-reminder" data-id="<?php echo $medicine_id; ?>">Delete</a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-row gap-3 mb-2">
                                            <span class="font-weight-600">Reminder Date:</span>
                                            <span class="font-weight-500"><?php echo $from_date; ?> - <?php echo $to_date; ?></span>
                                        </div>
                                        <div class="d-flex flex-row gap-3 mb-2">
                                            <span class="font-weight-600">Dose:</span>
                                            <span class="font-weight-500"><?php echo $dose_type; ?>, <?php echo $frequency; ?></span>
                                        </div>
                                        <div class="d-flex flex-row gap-3">
                                            <span class="font-weight-600">Medicine Instructions:</span>
                                            <span class="font-weight-500">
                                                <?php echo $times_display; ?> - <?php echo $instruction_display; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<p class='error-message text-center'>No reminders found.</p>";
                    }
                    ?>
                </div>
            </div>
        </section>
    </div>

<?php
    return ob_get_clean();
}
?>