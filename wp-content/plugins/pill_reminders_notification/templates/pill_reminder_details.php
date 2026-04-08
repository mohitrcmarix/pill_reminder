<?php
function pill_reminder_details()
{
    global $wpdb;
    $table = $wpdb->prefix . 'pill_reminders';

    // Get all reminders for the logged-in user
    $user_id = get_current_user_id();

    $results = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table WHERE user_id = %d OR user_id IS NULL OR user_id = 0", $user_id)
    );

    if ($results) {
        foreach ($results as $row) {
            ?>
            <div class="row justify-content-center mb-3">
                <div class="col-12 m-b30 mb-lg-0 d-flex border-shop">
                    <div class="card shop-card shadow-none mb-lg-0 w-100">
                        <div class="card-body">
                            <div class="row gx-lg-5">
                                <div class="col-lg-6">
                                    <div class="d-flex font-18 align-items-center justify-content-between mb-2">
                                        <span class="fw-semibold">Medicine Name</span>
                                        <span class="font-weight-500"><?php echo esc_html($row->medicine_name); ?></span>
                                    </div>
                                    <div class="d-flex font-18 align-items-center justify-content-between mb-2">
                                        <span class="fw-semibold">Frequency</span>
                                        <span class="font-weight-500"><?php echo esc_html($row->frequency); ?></span>
                                    </div>
                                    <div class="d-flex font-18 align-items-center justify-content-between mb-2">
                                        <span class="fw-semibold">Instructions</span>
                                        <span class="font-weight-500"><?php echo esc_html($row->instruction); ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="d-flex font-18 align-items-center justify-content-between mb-2">
                                        <span class="fw-semibold">Dose</span>
                                        <span class="font-weight-500"><?php echo esc_html($row->dose_value); ?></span>
                                    </div>
                                    <div class="d-flex font-18 align-items-center justify-content-between mb-2">
                                        <span class="fw-semibold">Duration</span>
                                        <span class="font-weight-500"><?php echo esc_html($row->duration_type); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-auto">
                                <a href="<?php echo esc_url( add_query_arg( 'edit', $row->id, site_url('/add_pill_reminder/'))); ?>" class="btn btn-danger fw-semibold btnhover p-3">Edit Details</a>
                            </div>
                            <div class="separator border-bottom my-3"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p>No reminders found.</p>";
    }
}
