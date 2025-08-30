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
            <div class="step" data-target="#confirmation">
                <button type="button" class="step-trigger">
                    <span class="bs-stepper-circle">3</span>
                    <span class="bs-stepper-label"><?php esc_html_e('Confirmation', 'ski-ride-servetech'); ?></span>
                </button>
            </div>
        </div>

        <!-- Step content -->
        <div class="bs-stepper-content">
            <form id="srs-booking-form-inner" action="#">
                <!-- Step 1 -->
                <div id="ride-options" class="content">
                    <p><?php esc_html_e('Step 1: Select your ride options', 'ski-ride-servetech'); ?></p>
                    <input type="text" name="ride_type" placeholder="<?php esc_attr_e('Type of Ride', 'ski-ride-servetech'); ?>" />
                    <button type="button" class="btn btn-primary" onclick="stepper.next()">Next</button>
                </div>

                <!-- Step 2 -->
                <div id="user-details" class="content">
                    <p><?php esc_html_e('Step 2: Enter your details', 'ski-ride-servetech'); ?></p>
                    <input type="text" name="name" placeholder="<?php esc_attr_e('Your Name', 'ski-ride-servetech'); ?>" />
                    <input type="email" name="email" placeholder="<?php esc_attr_e('Your Email', 'ski-ride-servetech'); ?>" />
                    <button type="button" class="btn btn-secondary" onclick="stepper.previous()">Back</button>
                    <button type="button" class="btn btn-primary" onclick="stepper.next()">Next</button>
                </div>

                <!-- Step 3 -->
                <div id="confirmation" class="content">
                    <p><?php esc_html_e('Step 3: Confirm your booking', 'ski-ride-servetech'); ?></p>
                    <button type="button" class="btn btn-secondary" onclick="stepper.previous()">Back</button>
                    <button type="submit" class="btn btn-success"><?php esc_html_e('Submit', 'ski-ride-servetech'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
