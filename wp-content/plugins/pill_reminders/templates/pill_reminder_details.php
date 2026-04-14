<?php
if (!function_exists('pill_reminder_detail')) {
    function pill_reminder_detail()
    {
        ob_start();
        $image = plugin_dir_url(dirname(__FILE__));
        $imageurl = $image . 'assets/img/banners/b1.png';
        $user_id = get_current_user_id();

        // Get reminders from Custom Post Type (pill_reminder)
        $args = array(
            'post_type' => 'pill_reminder',
            'posts_per_page' => -1,
            'author' => $user_id,     // Only current user's posts
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish'
        );
        $reminders = get_posts($args);
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
                    <?php
                    if ($reminders) {
                        foreach ($reminders as $post) {

                            // Get data from post meta (this is where metabox changes are saved)
                            $medicine_name = get_post_meta($post->ID, 'medicine_name', true);
                            $dose_value = get_post_meta($post->ID, 'dose_value', true);
                            $dose_type = get_post_meta($post->ID, 'dose_type', true);
                            $frequency = get_post_meta($post->ID, 'frequency', true);
                            $duration_type = get_post_meta($post->ID, 'duration_type', true);
                            $duration_value = get_post_meta($post->ID, 'duration_value', true);
                            $instruction = get_post_meta($post->ID, 'instruction', true);
                            $from_date = get_post_meta($post->ID, 'from_date', true);
                            $to_date = get_post_meta($post->ID, 'to_date', true);
                            $email = get_post_meta($post->ID, 'email', true);
                            $reminder_times = get_post_meta($post->ID, 'reminder_times', true);

                            // Decode reminder times if stored as JSON
                            $times = !empty($reminder_times) ? json_decode($reminder_times, true) : [];
                            ?>

                            <div class="row justify-content-center mb-3">
                                <div class="col-12 m-b30 mb-lg-0 d-flex border-shop">
                                    <div class="card shop-card shadow-none mb-lg-0 w-100">
                                        <div class="card-body">
                                            <div class="row gx-lg-5">
                                                <div class="col-lg-6">
                                                    <div class="d-flex font-18 align-items-center justify-content-between mb-2">
                                                        <span class="fw-semibold">Medicine Name</span>
                                                        <span
                                                            class="font-weight-500"><?php echo esc_html($medicine_name ?: $post->post_title); ?></span>
                                                    </div>
                                                    <div class="d-flex font-18 align-items-center justify-content-between mb-2">
                                                        <span class="fw-semibold">Frequency</span>
                                                        <span class="font-weight-500"><?php echo esc_html($frequency); ?></span>
                                                    </div>
                                                    <div class="d-flex font-18 align-items-center justify-content-between mb-2">
                                                        <span class="fw-semibold">Instructions</span>
                                                        <span class="font-weight-500"><?php echo esc_html($instruction); ?></span>
                                                    </div>

                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="d-flex font-18 align-items-center justify-content-between mb-2">
                                                        <span class="fw-semibold">Dose</span>
                                                        <span
                                                            class="font-weight-500"><?php echo esc_html($dose_value . ' ' . $dose_type); ?></span>
                                                    </div>
                                                    <div class="d-flex font-18 align-items-center justify-content-between mb-2">
                                                        <span class="fw-semibold">Duration</span>
                                                        <span
                                                            class="font-weight-500"><?php echo esc_html($duration_value . ' ' . $duration_type); ?></span>
                                                    </div>
                                                    <?php if (!empty($from_date) || !empty($to_date)): ?>
                                                        <div class="d-flex font-18 align-items-center justify-content-between mb-2">
                                                            <span class="fw-semibold">Date Range</span>
                                                            <span class="font-weight-500">
                                                                <?php echo esc_html($from_date); ?> → <?php echo esc_html($to_date); ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Edit Button -->
                                            <div class="mt-4">
                                                <a href="<?php echo esc_url(add_query_arg('edit', $post->ID, site_url('/add_pill_reminder/'))); ?>"
                                                    class="btn btn-danger fw-semibold btnhover p-3">
                                                    Edit Details
                                                </a>
                                            </div>

                                            <div class="separator border-bottom my-3"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p class='text-center'>No reminders found.</p>";
                    }
                    ?>
                </div>
            </section>
        </div>
        <?php
        return ob_get_clean();
    }
}
?>