<?php

   if (is_user_logged_in()) {
            $current_user_id = get_current_user_id();
            $existing = get_posts([
                'post_type' => 'customer_groups',
                'post_status' => 'publish',
                'author' => $current_user_id,
                'numberposts' => 1,
            ]);

        if ($existing) {
            $group_id = $existing[0]->ID;
            $members = get_post_meta($group_id, 'group_members', true);
            // echo "<pre>";
            // print_r($members);
            // echo "</pre>";
            // Function to filter items by renting option
            // 1. Collect all renting options from members
            $selected_option_indices = [];
            foreach ($members as $member) {
                $option_name = $member['renting_option'];
                $option_index = array_search($option_name, $renting_options);
                if ($option_index !== false && !in_array($option_index, $selected_option_indices)) {
                    $selected_option_indices[] = $option_index;
                }
            }

            // 2. Filter function
            function filter_items_by_renting_option($items, $selected_option_indices) {
                $filtered = [];
                foreach ($items as $item) {
                    if (!empty($item['renting_options'])) {
                        foreach ($item['renting_options'] as $opt_index) {
                            if (in_array($opt_index, $selected_option_indices)) {
                                $filtered[] = $item;
                                break; 
                            }
                        }
                    }
                }
                return $filtered;
            }



            // 3. Filter all items based on selected options
            $all_packages = filter_items_by_renting_option($packages, $selected_option_indices);
            $all_gears = filter_items_by_renting_option($gears, $selected_option_indices);
            $all_gloves = filter_items_by_renting_option($gloves, $selected_option_indices);
            $all_goggles = filter_items_by_renting_option($goggles, $selected_option_indices);
            $all_socks = filter_items_by_renting_option($socks, $selected_option_indices);
            $all_passes = filter_items_by_renting_option($passes, $selected_option_indices);

        }
    }





?>
<div class="srs-booking-form">
    <h3 class="srs-form-title">
        <?php 
        // echo esc_html($form_title);
        //  ?>
         </h3>

    <div id="srs-booking-form" class="bs-stepper">
        <!-- Step navigation -->
        <div class="bs-stepper-header d-none">
            <div class="step" data-target="#ride-options">
                <button type="button" class="step-trigger">
                    <span class="bs-stepper-circle">1</span>
                    <span class="bs-stepper-label"><?php esc_html_e('Ride Options', 'ski-ride-servetech'); ?></span>
                </button>
            </div>
            <div class="line"></div>
            <div class="step" data-target="#user-details">
                <button type="button" class="step-trigger">
                    <span class="bs-stepper-circle">2</span>
                    <span class="bs-stepper-label"><?php esc_html_e('Your Details', 'ski-ride-servetech'); ?></span>
                </button>
            </div>
            <div class="line"></div>
            <div class="step" data-target="#group-overview">
                <button type="button" class="step-trigger">
                    <span class="bs-stepper-circle">3</span>
                    <span class="bs-stepper-label"><?php esc_html_e('Your Group', 'ski-ride-servetech'); ?></span>
                </button>
            </div>
            <div class="line"></div>
            <div class="step" data-target="#equipment-selection">
                <button type="button" class="step-trigger">
                    <span class="bs-stepper-circle">4</span>
                    <span class="bs-stepper-label"><?php esc_html_e('Package', 'ski-ride-servetech'); ?></span>
                </button>
            </div>
            <div class="line"></div>
           <div class="step" data-target="#lift-pass-selection">
                <button type="button" class="step-trigger">
                    <span class="bs-stepper-circle">5</span>
                    <span class="bs-stepper-label"><?php esc_html_e('Passess', 'ski-ride-servetech'); ?></span>
                </button>
            </div>
            <div class="line"></div>
           <div class="step" data-target="#booking-step">
                <button type="button" class="step-trigger">
                    <span class="bs-stepper-circle">6</span>
                    <span class="bs-stepper-label"><?php esc_html_e('Form', 'ski-ride-servetech'); ?></span>
                </button>
            </div>
        </div>

        <!-- Step content -->
        <div class="bs-stepper-content">
            <form id="srs-booking-form-inner" method="post">
                 <input type="hidden" name="action" value="save_customer_group">
                <?php wp_nonce_field('save_customer_group_nonce', 'customer_group_nonce'); ?>
                <!-- Step 1 -->
                <div id="ride-options" class="content">
                    <h4 class="fw-bold mb-3 fs-5"><?php esc_html_e('Fitting Location', 'ski-ride-servetech'); ?></h4>

                    <div class="container mb-4">
                        <div class="row g-3">
                            <?php if (!empty($AdminSettings['locations'])): ?>
                                <?php foreach ($AdminSettings['locations'] as $index => $loc): ?>
                                    <div class="col-md-4 col-12">
                                        <input type="radio" name="fitting_location" id="location_<?php echo $index; ?>"
                                            value="<?php echo esc_attr($loc); ?>" class="d-none"/>
                                        <label for="location_<?php echo $index; ?>" 
                                            class="location-option w-100 text-center py-3">
                                            <span class="fw-bold"><?php echo esc_html($loc); ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Rental Period -->
                    <div class="container">
                        <h4 class="fw-bold mb-3 fs-5"><?php esc_html_e('Rental Period', 'ski-ride-servetech'); ?></h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="date" id="fitting_date" name="fitting_date" 
                                    class="form-control dev_required"
                                    placeholder="<?php esc_attr_e('Select fitting date', 'ski-ride-servetech'); ?>" required/>
                            </div>
                            <div class="col-md-6">
                                <input type="date" id="last_ski_date" name="last_ski_date" 
                                    class="form-control dev_required"
                                    placeholder="<?php esc_attr_e('Last ski date', 'ski-ride-servetech'); ?>" required/>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->

                    <div class="row mt-4 g-2 justify-content-between">
                        <div class="col-12 col-md-auto ">
                           <button type="button" class="btn btn-secondary px-4 w-100">
                                <?php esc_html_e('CANCEL BOOKING', 'ski-ride-servetech'); ?>
                            </button>
                        </div>

                        <div class="col-12 col-md-auto">    
                          <button type="button" class="btn btn-outline-dark px-4 w-100 dev-cancel-button d-none cancel-edit" onclick="stepper.to(3)">
                            <img src="<?php echo esc_url( plugins_url( 'assets/images/vector-cancel.png', WP_PLUGIN_DIR . '/ski-ride-servetech/ski-ride-servetech.php' ) ); ?>"
                                alt="<?php esc_attr_e('Cancel', 'ski-ride-servetech'); ?>"
                                class="me-2"
                                style="width:16px; height:16px;">
                            <?php esc_html_e("CANCEL THIS ACTION", "ski-ride-servetech"); ?>
                        </button>
                        </div>

                        <div class="col-12 col-md-auto">
                            <button type="button" class="btn btn-warning text-dark w-100 fw-bold px-4 dev-continue-button dev-go-next" data-next-step="2">
                                <?php esc_html_e('CONTINUE', 'ski-ride-servetech'); ?>
                            </button>
                        </div>
                    </div>


                </div>

                <!-- Step 2 -->
                <div id="user-details" class="content">
                    <h3 class="text-center mb-4"><?php esc_html_e("LET'S ADD FIRST PERSON", "ski-ride-servetech"); ?></h3>

                    <div class="container">
                        <!-- First / Last Name -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label fw-bold">
                                    <?php esc_html_e("First Name", "ski-ride-servetech"); ?>
                                </label>
                                <input type="text" id="first_name" name="first_name" class="form-control dev_required"
                                    placeholder="<?php esc_attr_e("First Name", "ski-ride-servetech"); ?>" required />
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label fw-bold">
                                    <?php esc_html_e("Last Name", "ski-ride-servetech"); ?>
                                </label>
                                <input type="text" id="last_name" name="last_name" class="form-control dev_required"
                                    placeholder="<?php esc_attr_e("Last Name", "ski-ride-servetech"); ?>" required/>
                            </div>
                        </div>

                        <!-- Renting Options -->
                        <div class="row mb-4">

                            <div class="mb-4 col-md-6">
                                <h5 class="fw-bold"><?php esc_html_e("What will they be renting?", "ski-ride-servetech"); ?></h5>
                                <div class="d-flex gap-3 flex-wrap">
                                    <?php if (!empty($AdminSettings['renting_options'])): ?>
                                        <?php foreach ($AdminSettings['renting_options'] as $i => $option): ?>
                                            <input type="radio" class="btn-check dev_required " name="renting_option" id="renting_<?php echo $i; ?>"
                                                value="<?php echo esc_attr($option); ?>" required>
                                            <label class="btn btn-outline-secondary" for="renting_<?php echo $i; ?>">
                                                <?php echo esc_html($option); ?>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                                <!-- Abilities -->
                            <div class="mb-4 col-md-6">
                                <h5 class="fw-bold"><?php esc_html_e("What is their ability?", "ski-ride-servetech"); ?></h5>
                                <div class="d-flex gap-3 flex-wrap">
                                    <?php if (!empty($AdminSettings['abilities'])): ?>
                                        <?php foreach ($AdminSettings['abilities'] as $i => $ability): ?>
                                            <input type="radio" required class="btn-check dev_required" name="ability" id="ability_<?php echo $i; ?>"
                                                value="<?php echo esc_attr($ability); ?>" >
                                            <label class="btn btn-outline-secondary" for="ability_<?php echo $i; ?>">
                                                <?php echo esc_html($ability); ?>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>

                    
                        <!-- Age / Weight / Height -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="age" class="form-label fw-bold"><?php esc_html_e("Age", "ski-ride-servetech"); ?></label>
                                <select id="age" name="age" class="form-select dev_required" required> 
                                    <option value=""><?php esc_html_e("Select Your Age", "ski-ride-servetech"); ?></option>
                                    <?php for ($i = 5; $i <= 80; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="weight" class="form-label fw-bold"><?php esc_html_e("Weight", "ski-ride-servetech"); ?></label>
                                <select id="weight" name="weight" class="form-select dev_required" required>
                                    <option value=""><?php esc_html_e("Select Your Weight", "ski-ride-servetech"); ?></option>
                                    <?php for ($i = 30; $i <= 150; $i+=5): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i . " kg"; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="height" class="form-label fw-bold"><?php esc_html_e("Height", "ski-ride-servetech"); ?></label>
                                <select id="height" name="height" class="form-select dev_required" required>
                                    <option value=""><?php esc_html_e("Select Your Height", "ski-ride-servetech"); ?></option>
                                    <?php for ($i = 120; $i <= 210; $i+=5): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i . " cm"; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="hidden-member-index" id="hidden-member-index" value="">
                        <!-- Buttons -->

                        <div class="row mt-4 g-2 justify-content-between">
                            <div class="col-12 col-md-auto ">
                                <button type="button" class="btn dev-back-btn w-100" onclick="stepper.previous()">
                                    <?php esc_html_e("BACK", "ski-ride-servetech"); ?>
                                </button>
                            </div>

                            <div class="col-12 col-md-auto">    
                                <button type="button" class="btn btn-outline-dark px-4 dev-cancel-button d-none w-100 cancel-edit ">
                                    <img src="<?php echo esc_url( plugins_url( 'assets/images/vector-cancel.png', WP_PLUGIN_DIR . '/ski-ride-servetech/ski-ride-servetech.php' ) ); ?>"
                                        alt="<?php esc_attr_e('Cancel', 'ski-ride-servetech'); ?>"
                                        class="me-2"
                                        style="width:16px; height:16px;">
                                    <?php esc_html_e("CANCEL THIS ACTION", "ski-ride-servetech"); ?>
                                </button>
                            </div>

                            <div class="col-12 col-md-auto">
                                 <button type="submit" class="btn btn-warning text-dark fw-bold px-4 w-100 dev-continue-button dev-go-next"><?php esc_html_e('Continue', 'ski-ride-servetech'); ?></button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Step 3 -->
                <div id="group-overview" class="content text-center">

                    <!-- Heading -->
                    <h3 class="fw-bold text-uppercase mb-2">
                        <?php esc_html_e("Your Group", "ski-ride-servetech"); ?>
                    </h3>
                    <p class="text-muted mb-4">
                        <?php esc_html_e("You can edit or remove people from your group using the icons next to their names.", "ski-ride-servetech"); ?>
                    </p>

                    <!-- Members List -->
                    <div id="group-members-list" class="d-flex flex-column align-items-center gap-3">
                        <!-- members will load here via AJAX -->
                    </div>

                    <!-- Add Person -->
                    <div class="my-4">
                        <button type="button" class="btn btn-primary px-4 dev-blue-btn" id="add-new-person">
                            <?php esc_html_e("Add Another Person", "ski-ride-servetech"); ?>
                        </button>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn px-4 dev-back-btn dev-step-back3">
                            <?php esc_html_e("BACK", "ski-ride-servetech"); ?>
                        </button>
                        <button type="button" class="btn btn-warning text-dark fw-bold px-4 dev-continue-button" id="goto-step-4"
                                onclick="stepper.to(4)">
                            <?php esc_html_e('CONTINUE', 'ski-ride-servetech'); ?>
                        </button>
                    </div>
                </div>

                
      

            </form>
            <!-- Booking Main Form  -->
            <div id="srs-main-buying">
                <!-- Step 4: Group Overview + Equipment Selection -->
                <div id="equipment-selection" class="content container">

                    <!-- Heading -->
                    <h3 class="text-center mb-3 fw-bold">
                        <?php esc_html_e("SELECT EQUIPMENT", "ski-ride-servetech"); ?>
                    </h3>
                    <p class="text-center text-muted mb-4">
                        <?php esc_html_e("Select what you need for your ride", "ski-ride-servetech"); ?>
                    </p>

                    <div class="row">

                        <!-- Packages -->
                        <?php if (!empty($all_packages)) : ?>
                        <div class="col-12 mb-5">
                            <h5 class="fw-semibold mb-3 mt-30">
                                <?php esc_html_e("Select A Ski Package (Boots Are Included)", "ski-ride-servetech"); ?>
                            </h5>
                            <div class="row g-3">
                                <?php foreach ($all_packages as $package) : ?>
                                <div class="col-md-4">
                                    <div class="card equipment-card">
                                        <div class="card-body">
                                            <div class="form-check mb-2">
                                                <input type="radio" name="packages"
                                                    class="form-check-input dev_required dev-radio-design"
                                                    id="pkg_<?php echo esc_attr($package['product_id']); ?>" required>
                                                <label class="form-check-label fw-bold w-700 package-lable-size package-name"
                                                    for="pkg_<?php echo esc_attr($package['product_id']); ?>">
                                                    <?php echo esc_html($package['name']); ?>
                                                </label>
                                            </div>
                                            <div class="card-footer text-center price-footer fs-5">
                                                <strong>¥<?php echo esc_html($package['price']); ?> </strong><span class="small">Per Day</span>
                                            </div>
                                            <p class="text-muted equipment-card-para">
                                                <?php echo esc_html($package['desc']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Other Package Options -->
                        <div class="col-12 mb-5">
                            <h5 class="fw-semibold mb-3 mt-30">
                                <?php esc_html_e("Other Package Options", "ski-ride-servetech"); ?>
                            </h5>
                            <div class="row g-3 align-items-center">
                                <div class="col-md-4 mt-30">
                                    <div class="option-box p-2 border rounded-2">
                                        <div class="form-check d-flex align-center gap-2">
                                            <input type="checkbox" class="form-check-input"
                                                id="has-boots"
                                                data-discount="<?php echo esc_attr($AdminSettings['boots_discount'] ?? 0); ?>">
                                            <label class="form-check-label fw-bold mb-0 fs-6" for="has-boots">
                                                <?php esc_html_e("Has own boots", "ski-ride-servetech"); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mt-30">
                                    <div class="option-box p-2 border rounded-2">
                                        <div class="form-check d-flex align-center gap-2">
                                            <input type="checkbox" class="form-check-input"
                                                id="include-insurance"
                                                data-price="<?php echo esc_attr($AdminSettings['insurance_price'] ?? 0); ?>"
                                                data-product-id="<?php echo esc_attr($AdminSettings['insurance_product_id'] ?? ''); ?>">
                                            <label class="form-check-label fw-bold mb-0 fs-6" for="include-insurance">
                                                <?php esc_html_e("Include Insurance", "ski-ride-servetech"); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mt-0">
                                    <div class="option-box ">
                                        <label for="gear-select" class="form-label fw-bold">
                                            <?php esc_html_e("Need to rent other Gear?", "ski-ride-servetech"); ?>
                                        </label>
                                        <select placeholder="Accessories" class="form-select p-2 border rounded-2 fs-6 fw-bold" id="gear-select" name="gear_select">
                                            <option value=""></option>
                                            <?php foreach ($all_gears as $gear): ?>
                                            <option value="<?php echo esc_attr($gear['product_id']); ?>"
                                                data-price="<?php echo esc_attr($gear['price']); ?>"
                                                data-name="<?php echo esc_attr($gear['name']); ?>">
                                                <?php echo esc_html($gear['name']); ?> - ¥<?php echo esc_html($gear['price']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sub-categories (Gloves, Goggles, Socks, etc.) -->
                        <?php
                        $sub_categories = ['gloves', 'goggles', 'socks'];
                        foreach ($sub_categories as $sub) :
                            if (!empty('all_' . $sub)) :
                        ?>
                        <div class="col-12 mb-5">
                            <h5 class="fw-semibold mb-3 mt-30">
                                <?php echo ucfirst($sub); ?> <?php esc_html_e("To Buy And Keep", "ski-ride-servetech"); ?>
                            </h5>
                            <div class="row g-3">
                                <?php foreach ($$sub as $item) : ?>
                                <div class="col-md-4">
                                    <div class="card equipment-card">
                                        <div class="card-body">
                                            <div class="form-check mb-2">
                                                <input type="radio" class="form-check-input dev-radio-design"
                                                    id="<?php echo esc_attr($sub . '_' . $item['product_id']); ?>">
                                                <label class="form-check-label fw-bold w-700 package-lable-size gear-names"
                                                    for="<?php echo esc_attr($sub . '_' . $item['product_id']); ?>">
                                                    <?php echo esc_html($item['name'] ?? $item['title']); ?>
                                                </label>
                                            </div>
                                            <div class="card-footer text-center price-footer fs-5 gear-prices">
                                                ¥<?php echo esc_html($item['price']); ?>
                                            </div>                                            
                                            <?php if (!empty($item['desc'])) : ?>
                                            <p class="text-muted equipment-card-para w-"><?php echo esc_html($item['desc']); ?></p>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif;
                        endforeach; ?>
                    </div>

                    <!-- Footer Buttons -->

                    <div class="row mt-4 g-2 justify-content-between">
                        <div class="col-12 col-md-auto ">
                            <button type="button" class="btn px-4 dev-back-btn w-100" id="back-to-group" onclick="stepper.to(3)">
                                <?php esc_html_e("Back", "ski-ride-servetech"); ?>
                            </button>
                        </div>

                        <div class="col-12 col-md-auto">    
                            <button type="button" class="btn btn-outline-dark px-4 dev-cancel-button w-100" onclick="stepper.to(3)">
                                <img src="<?php echo esc_url( plugins_url( 'assets/images/vector-cancel.png', WP_PLUGIN_DIR . '/ski-ride-servetech/ski-ride-servetech.php' ) ); ?>"
                                    alt="<?php esc_attr_e('Cancel', 'ski-ride-servetech'); ?>"
                                    class="me-2"
                                    style="width:16px; height:16px;">
                                <?php esc_html_e("CANCEL THIS ACTION", "ski-ride-servetech"); ?>
                            </button>
                        </div>

                        <div class="col-12 col-md-auto">
                            <button type="button" class="btn btn-warning px-4 fw-semibold dev-continue-button dev-go-next w-100" id="continue-to-next-step">
                                <?php esc_html_e("Continue", "ski-ride-servetech"); ?>
                            </button>
                        </div>
                    </div>

                </div>


                <!-- Step 5: Lift Pass Selection -->
                <div id="lift-pass-selection" class="content">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold"><?php esc_html_e("SKIP THE QUEUE. WE DELIVER LIFT PASSES TOO.", "ski-ride-servetech"); ?></h3>
                        <p class="text-muted">
                            <?php esc_html_e("Save your time and money! Snopro conveniently offers ski passes for Cardrona and Treble Cone alongside your ski rentals! Cardrona has expanded for 2025 to become NZ’s largest ski area!", "ski-ride-servetech"); ?>
                        </p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <ul>
                                <li><?php esc_html_e("Single day passes must be booked for a specific date and mountain", "ski-ride-servetech"); ?></li>
                                <li><?php esc_html_e("Snopro guests enjoy lowest price", "ski-ride-servetech"); ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li><?php esc_html_e("Multiday passes valid at Cardrona & Treble Cone", "ski-ride-servetech"); ?></li>
                                <li><?php esc_html_e("Multiday passes are flexible and valid all season", "ski-ride-servetech"); ?></li>
                                <li><?php esc_html_e("Book now and pay on delivery", "ski-ride-servetech"); ?></li>
                            </ul>
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        <h6 class="fw-bold"><?php esc_html_e("Purchase Now To Save On Window Price!", "ski-ride-servetech"); ?></h6>
                        <button type="button" class="btn dev-blue-btn">
                            <?php esc_html_e("VIEW PRICING", "ski-ride-servetech"); ?>
                        </button>
                    </div>

                    <!-- Pass Selection Form -->
                    <?php if (!empty($members)) : ?>
                        <div class="row">
                            <?php foreach ($members as $index => $member) : ?>
                                <div class="col-md-6 col-lg-4 mt-1"> <!-- adjust column size as needed -->
                                    <div class="card shadow-sm mb-4 h-100">
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold pass_owner_name" data-start-date="<?php echo $member['fitting_date'] ?>" data-end-date="<?php echo $member['last_ski_date'] ?>">
                                                    <?php echo esc_html($member['first_name'] . ' ' . $member['last_name']); ?>
                                                </label>
                                                <select class="form-select dev_required" name="member[<?php echo $index; ?>][age_group]" required>
                                                    <option><?php esc_html_e("Select Your Age Group", "ski-ride-servetech"); ?></option>
                                                    <option value="adult"><?php esc_html_e("Adult", "ski-ride-servetech"); ?></option>
                                                    <option value="child"><?php esc_html_e("Child", "ski-ride-servetech"); ?></option>
                                                    <option value="senior"><?php esc_html_e("Senior", "ski-ride-servetech"); ?></option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold"><?php esc_html_e("Enter Birthday", "ski-ride-servetech"); ?></label>
                                                <input type="date" class="member-birthday dev_required" class="form-control" name="member[<?php echo $index; ?>][birthday]" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold"><?php esc_html_e("Select Pass", "ski-ride-servetech"); ?></label>
                                                <select class="form-select dev_required" required name="member[<?php echo $index; ?>][pass_id]">
                                                    <option><?php esc_html_e("Select", "ski-ride-servetech"); ?></option>
                                                    <?php if (!empty($AdminSettings['passes'])) : ?>
                                                        <?php foreach ($AdminSettings['passes'] as $pass) : ?>
                                                            <option 
                                                                value="<?php echo esc_attr($pass['product_id']); ?>"
                                                                data-price="<?php echo esc_attr($pass['price']); ?>"
                                                                data-title="<?php echo esc_attr($pass['title']); ?>"
                                                            >
                                                                <?php echo esc_html($pass['title']); ?> - ¥<?php echo esc_html($pass['price']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>


                    <!-- Terms Checkbox -->
                    <div class="form-check mt-3 text-center d-flex gap-2 justify-content-center">
                        <input class="form-check-input dev_required" name="agree_terms" type="checkbox" id="agree-terms" required>
                        <label class="form-check-label mb-0" for="agree-terms">
                            <?php esc_html_e("I have read and agree to the", "ski-ride-servetech"); ?>
                            <a href="#"><?php esc_html_e("Terms & Conditions", "ski-ride-servetech"); ?></a>
                        </label>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" class="btn dev-back-btn" onclick="stepper.to(4)">
                            <?php esc_html_e("Back", "ski-ride-servetech"); ?>
                        </button>
                        <button type="button" class="btn btn-outline-dark dev-skip-pass" onclick="stepper.next()">
                              <img src="<?php echo esc_url( plugins_url( 'assets/images/vector-cancel.png', WP_PLUGIN_DIR . '/ski-ride-servetech/ski-ride-servetech.php' ) ); ?>"
                                alt="<?php esc_attr_e('Cancel', 'ski-ride-servetech'); ?>"
                                class="me-2"
                                style="width:16px; height:16px;">
                                 <?php esc_html_e("NO PASS", "ski-ride-servetech"); ?>
                        </button>
                        <button type="button" id="packages-button" class="btn dev-continue-button">
                            <?php esc_html_e("Continue", "ski-ride-servetech"); ?>
                        </button>
                    </div>
                </div>

                <!-- Step 6: Booking Form Step -->
                <div id="booking-step" class="content container py-4">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h3 class="fw-bold">YOUR BOOKING</h3>
                        <p class="text-muted">We are almost there!<br>Just a few more things...</p>
                    </div>

                    <!-- Contact Details -->
                    <?php $current_user = wp_get_current_user(); ?>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <input type="text" required class="form-control dev_required" id="booking-first-name" 
                                placeholder="First Name" required
                                value="<?php echo is_user_logged_in() ? esc_attr( $current_user->user_firstname ) : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <input type="text" required class="form-control dev_required" id="booking-last-name"  
                                placeholder="Last Name" required
                                value="<?php echo is_user_logged_in() ? esc_attr( $current_user->user_lastname ) : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <input type="email" required class="form-control dev_required" id="booking-email"  
                                placeholder="Email" required
                                value="<?php echo is_user_logged_in() ? esc_attr( $current_user->user_email ) : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <input type="text" required class="form-control dev_required" id="booking-phone-no"  
                                placeholder="Phone Number" required
                                value="<?php echo is_user_logged_in() ? esc_attr( get_user_meta( $current_user->ID, 'billing_phone', true ) ) : ''; ?>">
                        </div>
                    </div>


                    <!-- How did you hear & delivery details -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="how-did-hear">
                                <option selected>How Did You Hear About Us?</option>
                                <option>Google</option>
                                <option>Friends</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="delivery-location" placeholder="Delivery Location Details">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control"  id="collection-location"  placeholder="If collection location is different, please enter it here">
                        </div>
                    </div>

                    <!-- Promo Code & Comments -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="promo-code" placeholder="I Have A Promo Code">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="other-comment" placeholder="Other Comments/Instruction?">
                        </div>
                    </div>

                    <!-- Cost Summary -->
                    <h5 class="fw-bold mb-3">Cost Summary</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>James Parker</td>
                                    <td>Progression Ski</td>
                                    <td>¥56,000</td>
                                    <td>3 Days</td>
                                    <td>¥56,000</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Insurance</td>
                                    <td>¥56,000</td>
                                    <td>3 Days</td>
                                    <td>¥56,000</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Helmet</td>
                                    <td>¥56,000</td>
                                    <td>3 Days</td>
                                    <td>¥56,000</td>
                                </tr>
                                <tr class="fw-bold border-top">
                                    <td colspan="4" class="text-end">Total Cost</td>
                                    <td>¥56,000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Deposit Amount -->
                    <h5 class="fw-bold mb-3">Deposit Amount</h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="card p-3">
                                <div class="form-check">
                                    <input class="form-check-input dev_required dev-radio-design" type="radio" name="dev_payment_type" id="fullPayment" required>
                                    <label class="form-check-label fw-bold" for="fullPayment">¥56,000</label>
                                    <div class="small text-muted">Full payment</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3">
                                <div class="form-check">
                                    <input class="form-check-input dev_required dev-radio-design" type="radio" name="dev_payment_type" id="depositPayment" required>
                                    <label class="form-check-label fw-bold" for="depositPayment">¥56,000</label>
                                    <div class="small text-muted">10% Deposit payment</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Marketing Opt-in -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="marketingOptin">
                        <label class="form-check-label small text-muted" for="marketingOptin">
                            I’m happy to receive the occasional email from snowsports
                        </label>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn dev-back-btn" onclick="stepper.to(5)">Back</button>
                        <button type="button" class="btn btn-warning fw-bold go-to-payment dev-continue-button">PAYMENT</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modals   -->

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content p-3">
        <div class="modal-header">
            <h5 class="modal-title">Login</h5><small> &nbsp;&nbsp;&nbsp;(Before Proceed, You must logged in.)</small>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="popup-login-form">
            <div class="mb-3">
                <input type="email" class="form-control" id="login-email" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" id="login-password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <div class="text-center mt-2">
            <a href="#" id="showRegister">Don’t have an account? Register</a>
            </div>
            <div class="text-center mt-2">
            <a href="#" id="showForgotPassword">Forgot password?</a>
            </div>
        </div>
        </div>
    </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content p-3">
        <div class="modal-header">
            <h5 class="modal-title">Register</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="popup-register-form">
            <div class="mb-3">
                <input type="text" class="form-control" id="register-name" placeholder="Full Name" required>
            </div>
            <div class="mb-3">
                <input type="tel" class="form-control" id="register-phone" placeholder="Phone Number" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" id="register-email" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" id="register-password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Register</button>
            </form>
            <div class="text-center mt-2">
            <a href="#" id="showLogin">Already have an account? Login</a>
            </div>
        </div>
        </div>
    </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content p-3">
        <div class="modal-header">
            <h5 class="modal-title">Reset Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="popup-forgot-form">
            <div class="mb-3">
                <input type="email" class="form-control" id="forgot-email" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn btn-warning w-100">Send Reset Link</button>
            </form>
            <div class="text-center mt-2">
            <a href="#" id="backToLogin">Back to Login</a>
            </div>
        </div>
        </div>
    </div>
    </div>



</div>



<script>

jQuery(document).ready(function($) {
    var groupData = null
    Notiflix.Loading.standard();

    $.post('<?php echo admin_url('admin-ajax.php'); ?>', { action: 'check_user_group' }, function(response) {
        if (response.success && response.data.has_group) {
            // ✅ user has group
            var stepper = new window.Stepper(document.querySelector('#srs-booking-form'), {
                linear: true,
                animation: true
            });
            groupData = response.data;

            stepper.to(3); 
            $('#group-members-list').html(response.data.html);

        } else if (!response.success && response.data.reason === 'not_logged_in') {
            // ❌ user not logged in
            $("#loginModal").modal("show");

        } else {
            // ⚠️ user logged in but has no group
            // $("#noGroupModal").modal("show"); 
            // or redirect, or show a message: "Please create a group first"
        }

        Notiflix.Loading.remove();
    });

    // Switch modals
    $("#showRegister").on("click", function(e){
        e.preventDefault();
        $("#loginModal").modal("hide");
        $("#registerModal").modal("show");
    });

    $("#showLogin").on("click", function(e){
        e.preventDefault();
        $("#registerModal").modal("hide");
        $("#loginModal").modal("show");
    });

    // Switch to Forgot Password modal
    $("#showForgotPassword").on("click", function(e){
        e.preventDefault();
        $("#loginModal").modal("hide");
        $("#forgotPasswordModal").modal("show");
    });

    // Back to login
    $("#backToLogin").on("click", function(e){
        e.preventDefault();
        $("#forgotPasswordModal").modal("hide");
        $("#loginModal").modal("show");
    });


    // Handle login
    $("#popup-login-form").on("submit", function(e){
        e.preventDefault();
        Notiflix.Loading.standard();

        $.post("<?php echo admin_url('admin-ajax.php'); ?>", {
            action: "popup_user_login",
            email: $("#login-email").val(),
            password: $("#login-password").val()
        }, function(res){
            Notiflix.Loading.remove();
            if(res.success){
                toastr.success("Welcome back!");
                $("#loginModal").modal("hide");
                window.location.reload();
            } else {
                toastr.error(res.data.message || "Login failed");
            }
        });
    });

    // Handle register
    $("#popup-register-form").on("submit", function(e){
        e.preventDefault();
        Notiflix.Loading.standard();

        $.post("<?php echo admin_url('admin-ajax.php'); ?>", {
            action: "popup_user_register",
            name: $("#register-name").val(),
            phone: $("#register-phone").val(),
            email: $("#register-email").val(),
            password: $("#register-password").val()
        }, function(res){
            Notiflix.Loading.remove();
            if(res.success){
                toastr.success("Account created!");
                $("#registerModal").modal("hide");
                window.location.reload();
            } else {
                toastr.error(res.data.message || "Registration failed");
            }
        });
    });


    
    // Handle forgot password submit
    $("#popup-forgot-form").on("submit", function(e){
        e.preventDefault();
        Notiflix.Loading.standard();

        $.post("<?php echo admin_url('admin-ajax.php'); ?>", {
            action: "popup_user_forgot_password",
            email: $("#forgot-email").val()
        }, function(res){
            Notiflix.Loading.remove();
            if(res.success){
                toastr.success("Reset link sent to your email!");
                $("#forgotPasswordModal").modal("hide");
            } else {
                toastr.error(res.data.message || "Something went wrong");
            }
        });
    });

    // first 3rd step  
    $('#srs-booking-form-inner').on('submit', function(e) {
        Notiflix.Loading.standard();
        e.preventDefault();

        var formData = $(this).serialize();

        $.post('<?php echo admin_url('admin-ajax.php'); ?>', formData, function(response) {
            Notiflix.Loading.remove(); 

            if (response.success) {
                toastr.success(
                    response.data?.message || 'Person added to your group successfully!',
                    'Success'
                );

                if (typeof stepper !== 'undefined') {
                    stepper.to(3);
                }

             
                window.location.reload(); 
            } else {
                let errorMessage = response.data || 'An unexpected error occurred.';
                toastr.error(errorMessage, 'Error');
                window.location.reload();
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            Notiflix.Loading.remove();
            let failMsg = `AJAX request failed: ${textStatus} - ${errorThrown}`;
            toastr.warning(failMsg, 'Warning');
        });

    });



    // Add new person
    $("#add-new-person").on("click", function (e) {
        Notiflix.Loading.standard();
        e.preventDefault();
        $(".cancel-edit").removeClass('d-none');
        if (groupData) {
            // pre-fill fitting location
            if (groupData.members[0].fitting_location) {
                $("input[name='fitting_location'][value='" + groupData.members[0].fitting_location + "']").prop("checked", true).trigger("change");
            }
            // pre-fill dates
            if (groupData.members[0].fitting_date) {
                $("#fitting_date").val(groupData.members[0].fitting_date);
            }
            if (groupData.members[0].last_ski_date) {
                $("#last_ski_date").val(groupData.members[0].last_ski_date);
            }
        }

        // go to first step
        Notiflix.Loading.remove();
        stepper.to(1);
    });


    // Edit person
    $(document).on("click", ".edit-member", function (event) {
        Notiflix.Loading.standard();
        event.preventDefault();
        if (groupData) {
    
            let memberIndex = $(this).data("index");
            let member = groupData.members[memberIndex];
            // pre-fill fitting location
            if (member.fitting_location) {
                $("input[name='fitting_location'][value='" + member.fitting_location + "']").prop("checked", true).trigger("change");
            }
            // pre-fill dates
            if (member.fitting_date) {
                $("#fitting_date").val(member.fitting_date);
            }
            if (member.last_ski_date) {
                $("#last_ski_date").val(member.last_ski_date);
            }

            $("#first_name").val(member.first_name);
            $("#last_name").val(member.last_name);

            // Store member index in hidden input if needed
            $("#hidden-member-index").val(memberIndex);

            // Fill renting option (radio buttons)
            if (member.renting_option) {
                $(`input[name="renting_option"][value="${member.renting_option}"]`).prop("checked", true);
            }

            // Fill ability (radio buttons)
            if (member.ability) {
                $(`input[name="ability"][value="${member.ability}"]`).prop("checked", true);
            }

            // Fill select fields
            $("#age").val(member.age);
            $("#weight").val(member.weight);
            $("#height").val(member.height);
            $(".cancel-edit").removeClass('d-none');

            Notiflix.Loading.remove();
            stepper.to(1);
        }

     
    });

    // Cancel Action 
    $(document).on("click", ".cancel-edit", function (event) {
        event.preventDefault();
        stepper.to(3);
        $(this).addClass('d-none');
    });


    // Delete person
    $(document).on("click", ".delete-member", function (e) {
        Notiflix.Loading.standard();
        e.preventDefault();

        let memberIndex = $(this).data("index");

        if (!confirm("Are you sure you want to delete this person?")){
            Notiflix.Loading.remove();
            return;
        }

        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: "POST",
            data: {
                action: "ski_ride_delete_member",
                member_index: memberIndex,
            },
            success: function (response) {
                Notiflix.Loading.remove();
                if (response.success) {
                    $(`.member-item[data-index="${memberIndex}"]`).remove();
                }
                toastr.success('User Successfully Removed', 'Success');
            }
        });
    });

    $(document).on("click", ".dev-step-back3", function (e) {
        $(".cancel-edit").removeClass('d-none');
        stepper.previous();

    });



    $("#packages-button, .dev-skip-pass").on("click", function(e) {
        Notiflix.Loading.standard();
        e.preventDefault();
        let is_pass = true;

        if ($(this).is(".dev-skip-pass")) {
            is_pass = false;
        }
    
        let selectedData = {
            packages: [],
            gears: [],
            gloves: [],
            goggles: [],
            socks: [],
            passes: [],
            options: {
                has_own_boots: $("#has-boots").is(":checked"),
                boots_discount: parseFloat($("#has-boots").data("discount") || 0),
                insurance: $("#include-insurance").is(":checked"),
                insurance_price: parseFloat($("#include-insurance").data("price") || 0),
                insurance_product_id: $("#include-insurance").data("product-id") || ''
            }
        };

        // ---- Packages ----
        $("input[name='packages']:checked").each(function() {
            let productId = $(this).attr("id").replace("pkg_", "");
            let card = $(this).closest(".card");

            selectedData.packages.push({
                name: card.find(".package-name").text().trim(),
                price: card.find(".card-footer strong").text().replace("¥", "").replace("/ Day", "").trim(),
                product_id: productId
            });
        });

        // ---- Gear (single select) ----
        let gearSelect = $("#gear-select").val();
        if (gearSelect) {
            let option = $("#gear-select option:selected");
            selectedData.gears.push({
                name: option.data("name"),
                price: option.data("price"),
                product_id: gearSelect
            });
        }

        // ---- Gloves / Goggles / Socks ----
        ["gloves", "goggles", "socks"].forEach(sub => {
            $(`input[id^='${sub}_']:checked`).each(function() {
                let productId = $(this).attr("id").split("_")[1];
                let card = $(this).closest(".card");

                selectedData[sub].push({
                    name: card.find(".gear-names").text().trim(),
                    price: card.find(".gear-prices").text().replace("¥", "").trim(),
                    product_id: productId
                });
            });
        });

        // ---- Passes (per member) ----
        selectedData.passes = [];
        $("#lift-pass-selection .card").each(function() {
            let memberName = $(this).find(".pass_owner_name").text().trim();
            let ageGroup = $(this).find("select[name*='[age_group]']").val();
            let birthday = $(this).find(".member-birthday").val();
            let passSelect = $(this).find("select[name*='[pass_id]']").val();
            let passOption = $(this).find("select[name*='[pass_id]'] option:selected");
            let label = $(this).find(".pass_owner_name");
            let fitting_date = label.data("start-date"); 
            let last_ski_date = label.data("end-date");

            selectedData.passes.push({
                member: memberName,
                age_group: ageGroup,
                birthday: birthday,
                fitting_date: fitting_date,
                last_ski_date: last_ski_date,
                pass: passSelect ? {
                    title: passOption.data("title"),
                    price: passOption.data("price"),
                    product_id: passSelect
                } : null
            });
        });

        renderBookingSummary(selectedData, is_pass);
        Notiflix.Loading.remove();
        
        // If Passess are applied  
        if ($(this).is("#packages-button")) {
            // Check The passess and validation  
            checkValidations();
        }else{
            stepper.to(6)
        }
    });


    // ---- Fill Booking Summary Table ----
    function renderBookingSummary(data, is_pass) {
        let tbody = $("#booking-step table tbody");
        tbody.empty(); 

        let total = 0;
        let totalBootsDiscount = 0;
        // loop through members
        data.passes.forEach(member => {
            let memberTotal = 0;
              // Calculate rental days for this member
            let fittingDateObj = new Date(member.fitting_date);
            let lastSkiDateObj = new Date(member.last_ski_date);
            let diffTime = Math.abs(lastSkiDateObj - fittingDateObj);
            let days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
   
            // header row for member
            tbody.append(`
                <tr class="table-secondary">
                    <td colspan="5"><strong>${member.member}</strong> (${member.age_group}, ${member.birthday})</td>
                </tr>
            `);

            // package(s) for this member
            if (data.packages && data.packages.length) {
                
                data.packages.forEach(pkg => {
                    let cost = parseFloat(pkg.price) * days;
                    memberTotal += cost;
                    tbody.append(`
                        <tr>
                            <td></td>
                            <td>${pkg.name} <span class="badge bg-light color">Package</span></td>
                            <td>¥${pkg.price}</td>
                            <td>${days} Days</td>
                            <td>¥${cost.toLocaleString()}</td>
                        </tr>
                    `);
                });
            }

            // gear(s)
            if (data.gears && data.gears.length > 0) {
                data.gears.forEach(gear => {
                    let cost = parseFloat(gear.price) * days;
                    memberTotal += cost;
                    tbody.append(`
                        <tr>
                            <td></td>
                            <td>${gear.name}<span class="badge bg-light color">Gear</span></td>
                            <td>¥${gear.price}</td>
                            <td>${days} Days</td>
                            <td>¥${cost.toLocaleString()}</td>
                        </tr>
                    `);
                });
            }

            // gloves / goggles / socks
            ["gloves", "goggles", "socks"].forEach(sub => {
                if (data[sub] && data[sub].length > 0) {
                    data[sub].forEach(item => {
                        let cost = parseFloat(item.price);
                        memberTotal += cost;
                        tbody.append(`
                            <tr>
                                <td></td>
                                <td>${item.name}<span class="badge bg-light color">${sub.charAt(0).toUpperCase() + sub.slice(1)}</span></td>
                                <td>¥${item.price}</td>
                                <td>-</td>
                                <td>¥${cost.toLocaleString()}</td>
                            </tr>
                        `);
                    });
                }
            });

            // Optional Pass (only if selected)
            if(is_pass){
                if (member.pass && !isNaN(parseFloat(member.pass.price))) {
                    let cost = parseFloat(member.pass.price);
                    memberTotal += cost;

                    tbody.append(`
                        <tr>
                            <td></td>
                            <td>${member.pass.title || 'Pass'} <span class="badge bg-light color">Pass</span></td>
                            <td>¥${cost.toLocaleString()}</td>
                            <td>-</td>
                            <td>¥${cost.toLocaleString()}</td>
                        </tr>
                    `);
                }
            }
   
            // Options (Insurance / Own Boots)
            if (data.options.insurance) {
                let insurancePrice = parseFloat(data.options.insurance_price || 0);
                memberTotal += insurancePrice * days;
                tbody.append(`
                    <tr>
                        <td></td>
                        <td>Insurance</td>
                        <td>¥${insurancePrice}</td>
                        <td>${days} Days</td>
                        <td>¥${(insurancePrice * days).toLocaleString()}</td>
                    </tr>
                `);
            }

            if (data.options.has_own_boots) {
                let discountPerDay = parseFloat(data.options.boots_discount || 0);
                let bootsDiscount = discountPerDay * days;
                memberTotal -= bootsDiscount;
                totalBootsDiscount += bootsDiscount;


                if (bootsDiscount > 0) {
                    tbody.append(`
                        <tr>
                            <td></td>
                            <td>Boots Discount Applied</td>
                            <td>-¥${discountPerDay.toLocaleString()}</td>
                            <td>${days} Days</td>
                            <td>-¥${bootsDiscount.toLocaleString()}</td>
                        </tr>
                    `);
                }
            }

            // Subtotal per member
            tbody.append(`
                <tr class="fw-bold border-top">
                    <td colspan="4" class="text-end">Subtotal for ${member.member}</td>
                    <td>¥${memberTotal.toLocaleString()}</td>
                </tr>
            `);

            total += memberTotal;
            // Adding rental_days  
            member.rental_days = days;
        });

        // ---- Grand total row ----
        tbody.append(`
            <tr class="fw-bold table-dark">
                <td colspan="4" class="text-end">Grand Total</td>
                <td>¥${total.toLocaleString()}</td>
            </tr>
        `);
        // ---- Update deposit section ----
        $("#fullPayment + label").text("¥" + total.toLocaleString());
        $("#depositPayment + label").text("¥" + (total * 0.1).toLocaleString());



        // Last Step Jquery 

        $("#booking-step .go-to-payment").off("click").on("click", function(e){ 
            let is_valid = checkValidations(false);
            Notiflix.Loading.standard();
            if(is_valid){

                e.preventDefault();
                let bookingData = {
                    customer: {
                        first_name: $("#booking-first-name").val(),
                        last_name: $("#booking-last-name").val(),
                        email: $("#booking-email").val(),
                        phone: $("#booking-phone-no").val(),
                        delivery_address: $("#delivery-location").val(),
                        collection_address: $("#collection-location").val(),
                        hear_about: $("#how-did-hear").val(),
                        promo_code: $("#promo-code").val(),
                        comments: $("#other-comment").val(),
                        marketing_optin: $("#marketingOptin").is(":checked")
                    },
                    payment_type: $("#fullPayment").is(":checked") ? "full" : "deposit",
                    products: [] 
                };

                // Example: add packages
                data.packages.forEach(pkg => {
                    if (!isNaN(parseFloat(pkg.price))) {
                        data.passes.forEach(member => {
                            bookingData.products.push({
                                product_id: pkg.product_id,
                                name: pkg.name, 
                                quantity: 1,
                                price: pkg.price,
                                rental_days: member.rental_days
                            });
                        });
                    }
                });

                // Add gears, gloves, goggles, socks
                ["gears","gloves","goggles","socks"].forEach(type => {
                    data[type].forEach(item => {
                        if (!isNaN(parseFloat(item.price))) {
                            data.passes.forEach(member => {
                                bookingData.products.push({
                                    product_id: item.product_id,
                                    name: item.name,
                                    quantity: 1, 
                                    price: item.price,
                                    rental_days: member.rental_days
                                });
                            });
                        }
                    });
                });

                // Add passes
                if(is_pass){
                    data.passes.forEach(member => {
                        if (member.pass && !isNaN(parseFloat(member.pass.price))) {
                            bookingData.products.push({
                                product_id: member.pass.product_id,
                                name: member.pass.title,
                                quantity: 1, 
                                price: member.pass.price,
                                rental_days: member.rental_days
                            });
                        }
                    });
                }

                // Add options
                if (data.options.insurance && !isNaN(parseFloat(data.options.insurance_price))) {
                    data.passes.forEach(member => {
                        bookingData.products.push({
                            product_id: data.options.insurance_product_id,
                            name: "Insurance",
                            quantity: 1, 
                            price: data.options.insurance_price,
                            rental_days: member.rental_days
                        });
                    });
                }


                if(data.options.has_own_boots){
                bookingData.boots_discount = totalBootsDiscount;
                }

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    method: 'POST',
                    data: {
                        action: 'srs_add_rental_to_cart',
                        booking_data: bookingData
                    },
                    success: function (res) {
                        Notiflix.Loading.remove();
                        if (res.success && res.data.redirect) {
                            // Redirect to checkout
                            toastr.success('Added To Cart', 'Success');
                            window.location.href = res.data.redirect;
                        } else {
                            toastr.error('Facing Issue Please try again', 'Error');
                            $(document.body).trigger('wc_fragment_refresh');
                        }
                    },
                    error: function (err) {
                        Notiflix.Loading.remove();
                        console.error(err);
                        toastr.error(err, 'Error');
                    }
                });
            }else{
                Notiflix.Loading.remove();
            }
           
        });

    }

    // Initializing date library 
    flatpickr("#fitting_date", {
        dateFormat: "Y-m-d",
        minDate: "today"
    });

    flatpickr("#last_ski_date", {
        dateFormat: "Y-m-d",
        minDate: "today"
    });

    flatpickr(".member-birthday", {
        dateFormat: "Y-m-d",
    });

    $('.dev-go-next').on('click', function() {
        checkValidations();
    });

    
    function checkValidations(is_next = true) {
        // Select all fields with class 'dev_required'
        var $fieldsToCheck = $('.content.active .dev_required');

        var valid = true;
        var firstInvalid = null;

        $fieldsToCheck.each(function() {
            var $field = $(this);
            var value = $field.val();

            // For radio buttons, check if any in the group is selected
            if ($field.attr('type') === 'radio') {
                var name = $field.attr('name');
                if (!$('input[name="' + name + '"]:checked').length) {
                    valid = false;
                    $field.closest('div').find('label').css('border', '2px solid red');
                    if (!firstInvalid) firstInvalid = $field;
                } else {
                    $field.closest('div').find('label').css('border', '');
                }
            } else if ($field.attr('type') === 'checkbox') {
                var name = $field.attr('name');
                if (!$('input[name="' + name + '"]:checked').length) {
                    valid = false;
                    $field.closest('div').find('label').css('border', '2px solid red');
                    if (!firstInvalid) firstInvalid = $field;
                } else {
                    $field.closest('div').find('label').css('border', '');
                }
            } 
            else if ($field.is('select')) {
                var selectedValue = $field.val();
                if (!selectedValue || selectedValue === 'Select Your Age Group' ||  selectedValue === 'Select') {
                    valid = false;
                    $field.css('border', '2px solid red');
                    if (!firstInvalid) firstInvalid = $field;
                } else {
                    $field.css('border', '');
                }
            }else {
                // Normal input/textarea/select validation
                if (!value || value.trim() === '') {
                    valid = false;
                    $field.css('border', '2px solid red');
                    if (!firstInvalid) firstInvalid = $field;
                } else {
                    $field.css('border', '');
                }
            }
        });

        // Scroll to first invalid
        if (!valid && firstInvalid) {
            $('html, body').animate({
                scrollTop: firstInvalid.offset().top - 100
            }, 500);
            firstInvalid.focus();
            return false;
        } else if (valid) {
            if(is_next){
                stepper.next(); 
            }else{
                return true;
            }
        }
    }


});

</script>


<style>
/* Location selection styles */
    /* First Step Styling   */
    .location-option {
        border: 1px solid #ddd;
        border-radius: 6px;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        display: block;
    }

    input[type="radio"]:checked + .location-option {
        background: #f1f1f1;
        border-color: #000;
        color: #000;
    }

    /* Buttons */
    .dev-continue-button {
        background-color: #ffeb00 !important;
        border: none;
    }
    .dev-continue-button:hover {
        background-color: #ffe600 !important;
    }

/* Step 2 styles */

    /* Members list wrapper */
    #group-members-list .list-group {
        width: 100%;
        max-width: 600px; 
        margin: 0 auto;
        padding: 0;
    }

    /* Each list item */
    #group-members-list .list-group-item {
        display: flex;
        align-items: center;
        border: none;
        border-bottom: 1px solid #ddd;
        padding: 12px 16px;
        background: #fff;
    }

    /* Member name centered */
    #group-members-list .list-group-item span {
        text-align: center;
        font-weight: bold;
        font-size: 16px;
    }

    /* Icon buttons */
    #group-members-list .edit-member,
    #group-members-list .delete-member {
        border: none;
        background: transparent;
        font-size: 16px;
        color: #666;
        transition: color 0.2s ease;
    }

    /* Hover effects */
    #group-members-list .edit-member:hover {
        color: #0d6efd;
    }

    #group-members-list .delete-member:hover {
        color: #dc3545; 
    }

    /* Add another person button */
    #add-new-person {
        background-color: #3498db; /* bright blue */
        border: none;
    }
    #add-new-person:hover {
        background-color: #2980b9;
    }


    /* Step 5 styling  */

    /* Equipment cards */
    .equipment-card {
        border: none;
        background: #D7EEFF;
        border-radius: 8px;
        transition: all 0.3s ease-in-out;
    }
    .equipment-card:hover {
        border-color: #007bff;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
    }

    /* Checkbox alignment */
    .equipment-card .form-check-input {
        margin-top: 0.35rem;
    }

    /* Price footer */
    .price-footer {
        background: transparent;
        font-size: 15px;
        padding: 8px;
        border-top: 2px solid #ffffff;
    }
    /* primary css  */

    .dev-blue-btn{
        background-color: #5FAAE3;
        color: white;
    }

    .package-lable-size{
        font-size: 20px;
    }

    .equipment-card-para{
        font-size: 18px;
        line-height: 20px;
        font-weight: 400;
    }


    .dev-cancel-button {
        background-color: #F2F9FF;
    }

    .dev-back-btn{
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .dev-radio-design {
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        width: 18px !important;
        height: 18px !important;
        border: 2px solid #555 !important;
        border-radius: 4px !important; 
        display: inline-block !important;
        position: relative !important;
        cursor: pointer !important;
        margin-right: 6px !important;
    }

    /* Checked state: add a checkmark or filled box */
    .dev-radio-design:checked {
        background-color: #007bff !important;
        border-color: #007bff !important;
    }

    .dev-radio-design:checked::after {
        content: "✔" !important;
        color: white !important;
        font-size: 12px !important;
        position: absolute !important;
        top: -2px !important;
        left: 2px !important;
    }

</style>