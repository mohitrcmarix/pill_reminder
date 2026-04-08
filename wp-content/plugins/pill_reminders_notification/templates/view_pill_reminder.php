<?php

function view_pill_reminder()
{
    global $wpdb;
    $table = $wpdb->prefix . 'pill_reminders';
    $user_id = get_current_user_id();

    $results = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table WHERE user_id = %d OR user_id IS NULL OR user_id = 0", $user_id)
    );
    ?>

    <section class="bg-white content-inner-3">
        <div class="container">
            <div class="row align-items-center gy-3">
                <div class="col-sm">
                    <h3 class="font-25 fw-bold text-secondary mb-0">Your pill reminders</h3>
                </div>
                <div class="col-sm-auto">
                    <a href="<?php echo home_url('/add_pill_reminder'); ?>" class="btn  btn-danger fw-semibold btnhover p-3">Add
                        New
                        Reminder</a>
                </div>
            </div>
        </div>
     <div class="container mt-4">
        <div class="row gy-3">
            <?php
            if ($results) {
                foreach ($results as $row) {
                    $medicine_name = esc_html($row->medicine_name);
                    $dose_type = esc_html($row->dose_type);
                    $frequency = esc_html($row->frequency);
                    $instruction = esc_html($row->instruction);
                    $from_date = esc_html($row->from_date);
                    $to_date = esc_html($row->to_date);
                    $time = esc_html($row->reminder_times);
                    $medicine_id = esc_html($row->id);
                    $status = esc_html($row->status);
                    ?>
            <div class="col-md-6">
                <div class="card border bg-light">
                    <div class="card-body">
                        <div class="d-flex flex-row justify-content-between gap-3 mb-2">
                            <h4 class="mb-4">
                                <a href="<?php echo get_the_permalink(); ?>">
                                    <?php echo $medicine_name; ?>
                                </a> -
                                <span style="background-color: <?php echo $status ? '#CFF3D1' : '#F3D1D1'; ?>;
                                color: <?php echo $status ? '#388E3C' : '#8E3C3C'; ?>;
                                font-size: 16px;
                                padding: 10px;
                                border-radius: 5px;">
                                    <?php echo $status ? 'Active' : 'Deactive'; ?>
                                </span>
                            </h4>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="medicine_id" value="<?php echo $medicine_id; ?>">
                                <label class="switch-container">
                                    <input type="checkbox"
                                        name="status"
                                        value="1"
                                        class="toggle-status"
                                        data-medicine-id="<?php echo $medicine_id; ?>"
                                        data-current-status="<?php echo $status; ?>"
                                        <?php echo $status ? 'checked' : ''; ?>
                                        onchange="this.form.submit()">
                                    <span class="switch-label"></span>
                                </label>
                            </form>
                            <div class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <svg width="6" height="26" viewBox="0 0 8 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="4" cy="4" r="4" fill="#7C7C7C"/>
                                        <circle cx="4" cy="16" r="4" fill="#7C7C7C"/>
                                        <circle cx="4" cy="28" r="4" fill="#7C7C7C"/>
                                    </svg>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="<?php echo home_url('/pill_reminder_details/'); ?>" class="dropdown-item font-weight-500">View</a>
                                    <a href="<?php echo esc_url( add_query_arg( 'edit', $medicine_id, site_url('/add_pill_reminder/'))); ?>" class="dropdown-item font-weight-500">Edit</a>
                                    <a href="javascript:;" class="dropdown-item text-danger delete-reminder" data-id="<?php echo $medicine_id; ?>">Delete</a>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-row gap-3 mb-2">
                            <span class="font-weight-600">Reminder Date:</span>
                            <span class="font-weight-500"><?php echo $from_date; ?> -
                                <?php echo $to_date; ?></span>
                        </div>
                        <div class="d-flex flex-row gap-3 mb-2">
                            <span class="font-weight-600">Dose:</span>
                            <span class="font-weight-500"><?php echo $dose_type; ?>,
                                <?php echo $frequency; ?></span>
                        </div>
                        <div class="d-flex flex-row gap-3">
                            <span class="font-weight-600">Medicine Instructions:</span>
                            <span class="font-weight-500"><?php echo $time; ?><?php echo $instruction; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
                } else {
                    echo "<p>No reminders found.</p>";
                }
            ?>
        </div>
                </div>
            </section>
            <?php
          
}

