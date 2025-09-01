<div class="srs-booking-form">
    <h3 class="srs-form-title"><?php echo esc_html($form_title); ?></h3>

    <div id="srs-booking-form" class="bs-stepper">
        <!-- Step navigation -->
        <div class="bs-stepper-header">
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
                    <h4><?php esc_html_e('Fitting Location', 'ski-ride-servetech'); ?></h4>

                    <div class="container my-3">
                        <div class="row">
                            <?php if (!empty($AdminSettings['locations'])): ?>
                                <?php foreach ($AdminSettings['locations'] as $index => $loc): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="location-card">
                                            <div class="card-body text-center">
                                                <input type="radio" name="fitting_location" id="location_<?php echo $index; ?>"
                                                    value="<?php echo esc_attr($loc); ?>" class="d-none" />
                                                <label for="location_<?php echo $index; ?>"
                                                    class="btn btn-outline-primary w-100">
                                                    <?php echo esc_html($loc); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>


                    <div class="container">
                        <div class="row">
                            <!-- Heading -->
                            <div class="col-12">
                                <h4><?php esc_html_e('Rental Period', 'ski-ride-servetech'); ?></h4>
                            </div>

                            <!-- Fitting Date -->
                            <div class="col-md-6 mb-3">
                                <label for="fitting_date" class="form-label">
                                    <?php esc_html_e('Fitting Date', 'ski-ride-servetech'); ?>
                                </label>
                                <input type="date" id="fitting_date" name="fitting_date" class="form-control"
                                    placeholder="<?php esc_attr_e('Select fitting date', 'ski-ride-servetech'); ?>" />
                            </div>

                            <!-- Last Ski Date -->
                            <div class="col-md-6 mb-3">
                                <label for="last_ski_date" class="form-label">
                                    <?php esc_html_e('Last Ski Date', 'ski-ride-servetech'); ?>
                                </label>
                                <input type="date" id="last_ski_date" name="last_ski_date" class="form-control"
                                    placeholder="<?php esc_attr_e('Last ski date', 'ski-ride-servetech'); ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="buttons">
                        <button type="button"
                            class="btn btn-secondary"><?php esc_html_e('CANCEL BOOKING', 'ski-ride-servetech'); ?></button>
                        <button type="button" class="btn btn-primary"
                            onclick="stepper.next()"><?php esc_html_e('CONTINUE', 'ski-ride-servetech'); ?></button>
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
                                <input type="text" id="first_name" name="first_name" class="form-control"
                                    placeholder="<?php esc_attr_e("First Name", "ski-ride-servetech"); ?>" />
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label fw-bold">
                                    <?php esc_html_e("Last Name", "ski-ride-servetech"); ?>
                                </label>
                                <input type="text" id="last_name" name="last_name" class="form-control"
                                    placeholder="<?php esc_attr_e("Last Name", "ski-ride-servetech"); ?>" />
                            </div>
                        </div>

                        <!-- Renting Options -->
                        <div class="mb-4">
                            <h5 class="fw-bold"><?php esc_html_e("What will they be renting?", "ski-ride-servetech"); ?></h5>
                            <div class="d-flex gap-3 flex-wrap">
                                <?php if (!empty($AdminSettings['renting_options'])): ?>
                                    <?php foreach ($AdminSettings['renting_options'] as $i => $option): ?>
                                        <input type="radio" class="btn-check" name="renting_option" id="renting_<?php echo $i; ?>"
                                            value="<?php echo esc_attr($option); ?>">
                                        <label class="btn btn-outline-secondary" for="renting_<?php echo $i; ?>">
                                            <?php echo esc_html($option); ?>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Abilities -->
                        <div class="mb-4">
                            <h5 class="fw-bold"><?php esc_html_e("What is their ability?", "ski-ride-servetech"); ?></h5>
                            <div class="d-flex gap-3 flex-wrap">
                                <?php if (!empty($AdminSettings['abilities'])): ?>
                                    <?php foreach ($AdminSettings['abilities'] as $i => $ability): ?>
                                        <input type="radio" class="btn-check" name="ability" id="ability_<?php echo $i; ?>"
                                            value="<?php echo esc_attr($ability); ?>">
                                        <label class="btn btn-outline-secondary" for="ability_<?php echo $i; ?>">
                                            <?php echo esc_html($ability); ?>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Age / Weight / Height -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="age" class="form-label fw-bold"><?php esc_html_e("Age", "ski-ride-servetech"); ?></label>
                                <select id="age" name="age" class="form-select">
                                    <option value=""><?php esc_html_e("Select Your Age", "ski-ride-servetech"); ?></option>
                                    <?php for ($i = 5; $i <= 80; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="weight" class="form-label fw-bold"><?php esc_html_e("Weight", "ski-ride-servetech"); ?></label>
                                <select id="weight" name="weight" class="form-select">
                                    <option value=""><?php esc_html_e("Select Your Weight", "ski-ride-servetech"); ?></option>
                                    <?php for ($i = 30; $i <= 150; $i+=5): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i . " kg"; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="height" class="form-label fw-bold"><?php esc_html_e("Height", "ski-ride-servetech"); ?></label>
                                <select id="height" name="height" class="form-select">
                                    <option value=""><?php esc_html_e("Select Your Height", "ski-ride-servetech"); ?></option>
                                    <?php for ($i = 120; $i <= 210; $i+=5): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i . " cm"; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" onclick="stepper.previous()">
                                <?php esc_html_e("BACK", "ski-ride-servetech"); ?>
                            </button>
                            <button type="submit" class="btn btn-success"><?php esc_html_e('Submit', 'ski-ride-servetech'); ?></button>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div id="group-overview" class="content">
                    <h3><?php esc_html_e("Your Group", "ski-ride-servetech"); ?></h3>
                    <div id="group-members-list">
                        <!-- members will load here via AJAX -->
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-primary" id="add-new-person">
                            <?php esc_html_e("Add Another Person", "ski-ride-servetech"); ?>
                        </button>
                        <button type="button" class="btn btn-success" id="goto-step-4"
                        onclick="stepper.to(4)"><?php esc_html_e('CONTINUE', 'ski-ride-servetech'); ?></button>
                    </div>
                </div>
                
      

            </form>
            <!-- Booking Main Form  -->
            <form id="srs-main-buying" method="post">
                <!-- Step 4: Group Overview + Equipment Selection -->
                <div id="equipment-selection" class="content">
                    <h3 class="text-center mb-4"><?php esc_html_e("SELECT EQUIPMENT", "ski-ride-servetech"); ?></h3>
                    <p class="text-center text-muted"><?php esc_html_e("Select what you need for your ride", "ski-ride-servetech"); ?></p>

                    <div class="row mt-4">

                        <!-- Packages -->
                        <?php if (!empty($AdminSettings['packages'])) : ?>
                            <div class="col-12 mb-4">
                                <h5><?php esc_html_e("Select A Ski Package (Boots Are Included)", "ski-ride-servetech"); ?></h5>
                                <div class="row">
                                    <?php foreach ($AdminSettings['packages'] as $package) : ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 border-primary">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input type="checkbox" name="packages" class="form-check-input" id="pkg_<?php echo esc_attr($package['product_id']); ?>">
                                                        <label class="form-check-label" for="pkg_<?php echo esc_attr($package['product_id']); ?>">
                                                            <h6 class="fw-bold"><?php echo esc_html($package['name']); ?></h6>
                                                        </label>
                                                    </div>
                                                    <p class="small text-muted mb-2"><?php echo esc_html($package['desc']); ?></p>
                                                </div>
                                                <div class="card-footer text-center">
                                                    <strong>₩<?php echo esc_html($package['price']); ?> / Day</strong>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Other Package Options -->
                        <div class="col-12 mb-4">
                            <h5><?php esc_html_e("Other Package Options", "ski-ride-servetech"); ?></h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="has-boots">
                                        <label class="form-check-label" for="has-boots"><?php esc_html_e("Has own boots", "ski-ride-servetech"); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="include-insurance">
                                        <label class="form-check-label" for="include-insurance"><?php esc_html_e("Include Insurance", "ski-ride-servetech"); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="gear-select"><?php esc_html_e("Need to rent other Gear?", "ski-ride-servetech"); ?></label>
                                    <select class="form-select" id="gear-select" name="gear_select">
                                        <?php foreach($gears as $gear): ?>
                                            <option value="<?php echo esc_attr($gear['product_id']); ?>">
                                                <?php echo esc_html($gear['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Sub-categories (Gloves, Goggles, Socks, etc.) -->
                        <?php
                        $sub_categories = ['gloves', 'goggles', 'socks'];
                        foreach ($sub_categories as $sub) :
                            if (!empty($AdminSettings[$sub])) :
                        ?>
                            <div class="col-12 mb-4">
                                <h5><?php echo ucfirst($sub); ?> <?php esc_html_e("To Buy And Keep", "ski-ride-servetech"); ?></h5>
                                <div class="row">
                                    <?php foreach ($AdminSettings[$sub] as $item) : ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 border-info">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="<?php echo esc_attr($sub . '_' . $item['product_id']); ?>">
                                                        <label class="form-check-label" for="<?php echo esc_attr($sub . '_' . $item['product_id']); ?>">
                                                            <h6 class="fw-bold"><?php echo esc_html($item['name'] ?? $item['title']); ?></h6>
                                                        </label>
                                                    </div>
                                                    <?php if (!empty($item['desc'])) : ?>
                                                        <p class="small text-muted mb-2"><?php echo esc_html($item['desc']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-footer text-center">
                                                    <strong>₩<?php echo esc_html($item['price']); ?></strong>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php
                            endif;
                        endforeach;
                        ?>

                    </div>

                    <!-- Footer Buttons -->
                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" id="back-to-group">
                            <?php esc_html_e("Back", "ski-ride-servetech"); ?>
                        </button>
                        <button type="button" class="btn btn-danger">
                            <?php esc_html_e("Cancel This Action", "ski-ride-servetech"); ?>
                        </button>
                        <button type="button" class="btn btn-success" id="continue-to-next-step" onclick="stepper.to(5)">
                            <?php esc_html_e("Continue", "ski-ride-servetech"); ?>
                        </button>
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
                        <button type="button" class="btn btn-primary">
                            <?php esc_html_e("VIEW PRICING", "ski-ride-servetech"); ?>
                        </button>
                    </div>

                    <!-- Pass Selection Form -->
                    <div class="card shadow-sm mx-auto" style="max-width: 400px;">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">James Parker</label>
                                <select class="form-select">
                                    <option><?php esc_html_e("Select Your Age Group", "ski-ride-servetech"); ?></option>
                                    <option><?php esc_html_e("Adult", "ski-ride-servetech"); ?></option>
                                    <option><?php esc_html_e("Child", "ski-ride-servetech"); ?></option>
                                    <option><?php esc_html_e("Senior", "ski-ride-servetech"); ?></option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php esc_html_e("Enter Birthday", "ski-ride-servetech"); ?></label>
                                <input type="date" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php esc_html_e("Select Pass", "ski-ride-servetech"); ?></label>
                                <select class="form-select">
                                    <option><?php esc_html_e("Select", "ski-ride-servetech"); ?></option>
                                    <?php if (!empty($AdminSettings['passes'])) : ?>
                                        <?php foreach ($AdminSettings['passes'] as $pass) : ?>
                                            <option value="<?php echo esc_attr($pass['product_id']); ?>">
                                                <?php echo esc_html($pass['title']); ?> - ₩<?php echo esc_html($pass['price']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Terms Checkbox -->
                    <div class="form-check mt-3 text-center">
                        <input class="form-check-input" type="checkbox" id="agree-terms">
                        <label class="form-check-label" for="agree-terms">
                            <?php esc_html_e("I have read and agree to the", "ski-ride-servetech"); ?>
                            <a href="#"><?php esc_html_e("Terms & Conditions", "ski-ride-servetech"); ?></a>
                        </label>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary">
                            <?php esc_html_e("Back", "ski-ride-servetech"); ?>
                        </button>
                        <button type="button" class="btn btn-outline-dark">
                            ✘ <?php esc_html_e("NO PASS", "ski-ride-servetech"); ?>
                        </button>
                        <button type="button" id="packages-button" class="btn btn-success">
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
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="First Name">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Last Name">
                        </div>
                        <div class="col-md-3">
                            <input type="email" class="form-control" placeholder="Email">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Phone Number">
                        </div>
                    </div>

                    <!-- How did you hear & delivery details -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <select class="form-select">
                                <option selected>How Did You Hear About Us?</option>
                                <option>Google</option>
                                <option>Friends</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Delivery Location Details">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="If collection location is different, please enter it here">
                        </div>
                    </div>

                    <!-- Promo Code & Comments -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="I Have A Promo Code">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Other Comments/Instruction?">
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
                                    <td>₩56,000</td>
                                    <td>3 Days</td>
                                    <td>₩56,000</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Insurance</td>
                                    <td>₩56,000</td>
                                    <td>3 Days</td>
                                    <td>₩56,000</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Helmet</td>
                                    <td>₩56,000</td>
                                    <td>3 Days</td>
                                    <td>₩56,000</td>
                                </tr>
                                <tr class="fw-bold border-top">
                                    <td colspan="4" class="text-end">Total Cost</td>
                                    <td>₩56,000</td>
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
                                    <input class="form-check-input" type="checkbox" id="fullPayment">
                                    <label class="form-check-label fw-bold" for="fullPayment">₩56,000</label>
                                    <div class="small text-muted">Full payment</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="depositPayment">
                                    <label class="form-check-label fw-bold" for="depositPayment">₩56,000</label>
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
                        <button type="button" class="btn btn-secondary">Back</button>
                        <button type="button" class="btn btn-warning fw-bold">PAYMENT</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>



<script>

jQuery(document).ready(function($) {
    var groupData = null

    $.post('<?php echo admin_url('admin-ajax.php'); ?>', { action: 'check_user_group' }, function(response) {
        if (response.success && response.data.has_group) {
            var stepper = new window.Stepper(document.querySelector('#srs-booking-form'), {
                linear: true,
                animation: true
            });
            groupData = response.data;
            stepper.to(3); 
            $('#group-members-list').html(response.data.html);
        }
    });


    $('#srs-booking-form-inner').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.post('<?php echo admin_url('admin-ajax.php'); ?>', formData, function(response) {
            if (response.success) {
                alert('Group saved successfully!');
                stepper.to(3); 
            } else {
                alert('Error: ' + response.data);
            }
        });
    });



    // Add new person
    $("#add-new-person").on("click", function (e) {
        e.preventDefault();

        if (groupData) {
            // pre-fill fitting location
            if (groupData.fitting_location) {
                $("input[name='fitting_location'][value='" + groupData.fitting_location + "']").prop("checked", true).trigger("change");
            }
            // pre-fill dates
            if (groupData.fitting_date) {
                $("#fitting_date").val(groupData.fitting_date);
            }
            if (groupData.last_ski_date) {
                $("#last_ski_date").val(groupData.last_ski_date);
            }
        }

        // go to first step
        stepper.to(1);
    });


    // Edit person
    $(document).on("click", ".edit-member", function (event) {
        event.preventDefault();
        if (groupData) {
            // pre-fill fitting location
            if (groupData.fitting_location) {
                $("input[name='fitting_location'][value='" + groupData.fitting_location + "']").prop("checked", true).trigger("change");
            }
            // pre-fill dates
            if (groupData.fitting_date) {
                $("#fitting_date").val(groupData.fitting_date);
            }
            if (groupData.last_ski_date) {
                $("#last_ski_date").val(groupData.last_ski_date);
            }

            let memberIndex = $(this).data("index");
            let member = groupData.members[memberIndex];
            console.log(member);

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
            stepper.to(1);
        }

     
    });

    // Delete person
    $(document).on("click", ".delete-member", function (e) {
        e.preventDefault();

        let memberIndex = $(this).data("index");

        if (!confirm("Are you sure you want to delete this person?")) return;

        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: "POST",
            data: {
                action: "ski_ride_delete_member",
                member_index: memberIndex,
            },
            success: function (response) {
                if (response.success) {
                    $(`.member-item[data-index="${memberIndex}"]`).remove();
                }
            }
        });
    });


    $('#packages-button').on('click', function(e) {
        e.preventDefault();
        var chosed_package_step = $('#srs-main-buying');

        var formData = $(chosed_package_step).serialize();
        console.log('Serialized formData:', formData);

        // Optional: convert to an object for easier reading
        var formDataObj = $(chosed_package_step).serializeArray().reduce(function(obj, item) {
            if (obj[item.name]) {
                // If multiple values for same name, convert to array
                if (!Array.isArray(obj[item.name])) {
                    obj[item.name] = [obj[item.name]];
                }
                obj[item.name].push(item.value);
            } else {
                obj[item.name] = item.value;
            }
            return obj;
        }, {});
        console.log('Form data object:', formDataObj);
    });


});

</script>