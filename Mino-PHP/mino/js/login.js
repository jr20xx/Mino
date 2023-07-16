$(() => {
    let theme_toggler = $("#theme_toggler"),
        login_username_field = $("#username"), login_password_field = $("#password"), login_pass_revealer = $("#login_password_revealer"),
        create_user_dialog = $("#create_user_dialog"), cud_form = $("#cud_form"), cud_username_field = $("#cud_username"), cud_pass_field = $("#cud_password"), cud_r_pass_field = $("#cud_r_password"),
        cud_pass_revealer = $("#cud_password_revealer"), cud_r_pass_revealer = $("#cud_r_password_revealer");

    if (Cookies.get('theme') === 'dark') {
        $('html').attr('data-bs-theme', 'dark');
        theme_toggler.html('<use xlink:href="#light" />');
    }

    theme_toggler.click(() => {
        if ($('html').attr('data-bs-theme') === 'dark') {
            $('html').removeAttr('data-bs-theme');
            Cookies.set('theme', 'default');
            theme_toggler.html('<use xlink:href="#dark" />');
        }
        else {
            $('html').attr('data-bs-theme', 'dark');
            Cookies.set('theme', 'dark');
            theme_toggler.html('<use xlink:href="#light" />');
        }
    });
    login_pass_revealer.click(() => {
        if (login_password_field.attr("type") === "password") {
            login_password_field.attr("type", "text");
            login_pass_revealer.find("svg").html('<use xlink:href="#hide" />');
        }
        else {
            login_password_field.attr("type", "password");
            login_pass_revealer.find("svg").html('<use xlink:href="#show" />');
        }
    });

    $('#login_form').submit((e) => {
        e.preventDefault();
        let username = login_username_field.val().trim(), password = login_password_field.val().trim();

        if (username.length == 0 || password.length == 0) {
            Swal.fire({
                icon: 'warning',
                title: 'You must input your username and your password',
            });
            return false;
        } else {
            $.ajax({
                url: "../mino/user_check.php",
                type: "POST",
                datatype: "json",
                data: { user: username, password: password },
                success: (data) => {
                    if (data == null) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Wrong credentials!',
                        });
                    }
                    else
                        window.location.href = "../mino/notes.php";
                },
                error: () => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Server error!',
                        toast: true,
                        position: 'bottom',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    });
                }
            });
        }
    });

    $("#cud_triggerer").click(() => {
        cud_form.trigger("reset");
    });
    cud_pass_revealer.click(() => {
        if (cud_pass_field.attr("type") === "password") {
            cud_pass_field.attr("type", "text");
            cud_pass_revealer.find("svg").html('<use xlink:href="#hide" />');
        }
        else {
            cud_pass_field.attr("type", "password");
            cud_pass_revealer.find("svg").html('<use xlink:href="#show" />');
        }
    });
    cud_r_pass_revealer.click(() => {
        if (cud_r_pass_field.attr("type") === "password") {
            cud_r_pass_field.attr("type", "text");
            cud_r_pass_revealer.find("svg").html('<use xlink:href="#hide" />');
        }
        else {
            cud_r_pass_field.attr("type", "password");
            cud_r_pass_revealer.find("svg").html('<use xlink:href="#show" />');
        }
    });
    $("#create_user_dialog_ok_btn").click(() => {
        cud_form.trigger("submit");
    });
    cud_form.submit((e) => {
        e.preventDefault();
        let username = cud_username_field.val().trim(),
            password = cud_pass_field.val().trim(),
            r_password = cud_r_pass_field.val().trim();
        if (username.length == 0 || password.length == 0 || r_password == 0) {
            Swal.fire({
                icon: 'error',
                title: 'You must input all the requested data to create a new user account',
                toast: true,
                position: 'bottom',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        }
        else if (password !== r_password) {
            Swal.fire({
                icon: 'error',
                title: 'Passwords does not match!',
                toast: true,
                position: 'bottom',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        }
        else if (password.length < 4) {
            Swal.fire({
                icon: 'error',
                title: 'The password must contain at least 4 characters',
                toast: true,
                position: 'bottom',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        }
        else {
            $.ajax({
                url: '../mino/users_table_handler.php',
                method: 'POST',
                data: { username: username, password: password, operation_type: 0 },
                success: (response) => {
                    if (response == 201) {
                        Swal.fire({
                            icon: 'info',
                            title: 'User account created!',
                            toast: true,
                            position: 'bottom',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                        create_user_dialog.modal("hide");
                    }
                    else if (response == 400) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Username already in use',
                            toast: true,
                            position: 'bottom',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                    }
                    else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong...',
                            toast: true,
                            position: 'bottom',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                    }
                },
                error: () => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Server error!',
                        toast: true,
                        position: 'bottom',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    });
                }
            });
        }
    });
});