jQuery(document).ready(function ($) {
    // Initialize bs-stepper
    window.stepper = new window.Stepper(document.querySelector('#srs-booking-form'), {
        linear: true,
        animation: true
    });

  
});




jQuery(document).ready(function($) {
    var groupData = null
    Notiflix.Loading.standard();

    $.post(srs_ajax.ajax_url, { action: 'check_user_group' }, function(response) {
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

        $.post(srs_ajax.ajax_url, {
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

        $.post(srs_ajax.ajax_url, {
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

        $.post(srs_ajax.ajax_url, {
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

        $.post(srs_ajax.ajax_url, formData, function(response) {
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
            url: srs_ajax.ajax_url,
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
                    url: srs_ajax.ajax_url,
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