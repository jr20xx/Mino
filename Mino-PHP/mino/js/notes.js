$(() => {
    if (Cookies.get('theme') === 'dark')
        $('html').attr('data-bs-theme', 'dark');

    let currentNoteTitle = "", currentNoteBody = "",
        note_writer_modal = $("#note_writer_modal"), note_writer_form = $("#note_writer_form"),
        notes_list_container = $("#notes_list_container"), note_viewer_container = $("#note_viewer_container"), notes_list = $("#notes_list"),
        note_viewer_timestamp = $("#note_viewer_timestamp"), note_viewer_title = $("#note_viewer_title"), note_viewer_body = $("#note_viewer_body"), note_viewer_save_button = $("#note_viewer_save_button"),
        actions_checker_dialog = $("#actions_checker_dialog"), acd_title = $("#actions_checker_dialog_title"), acd_message = $("#actions_checker_dialog_message"), acd_ok_button = $("#actions_checker_dialog_ok_btn"),
        password_changer_dialog = $("#password_changer_dialog"), pcd_form = $("#pcd_form"),
        pcd_op = $("#pcd_old_password"), pcd_np = $("#pcd_new_password"), pcd_np_r = $("#pcd_new_r_password"), pcd_ok_button = $("#password_changer_dialog_ok_btn");

    note_viewer_container.hide();

    $.ajax({
        url: "responder.php",
        type: "POST",
        dataType: 'json',
        data: { action: 'get_notes' },
        success: (data) => {
            $.each(data, (_, item) => {
                addNote(item.ID, item.TITLE, item.BODY, item.TIME_STAMP);
            });
        }
    });

    $('#notes_searcher').on('input', function () {
        var searchText = $(this).val().toLowerCase();
        $('#notes_list li').filter(function () {
            return $(this).text().toLowerCase().indexOf(searchText) === -1;
        }).hide();
        $('#notes_list li').filter(function () {
            return $(this).text().toLowerCase().indexOf(searchText) !== -1;
        }).show();
    });

    $('#note_adder_button').click(() => {
        note_writer_form.trigger("reset");
    });
    $('#theme_toggle').click(() => {
        if ($('html').attr('data-bs-theme') === 'dark') {
            $('html').removeAttr('data-bs-theme');
            Cookies.set('theme', 'default');
        }
        else {
            $('html').attr('data-bs-theme', 'dark');
            Cookies.set('theme', 'dark');
        }
    });
    $('#logout_button').click(() => {
        acd_title.text("Log out");
        acd_message.text("Are you sure that you want to finish your current session?");
        acd_ok_button.attr('data-action', 'logout');
    });

    $("#note_adder_ok_btn").click(() => {
        note_writer_form.trigger("submit");
    });
    note_writer_form.submit((e) => {
        e.preventDefault();
        var noteTitle = $('#note_writer_title').val().trim(), noteBody = $('#note_writer_body').val().trim();
        if (noteTitle.length > 0 && noteBody.length > 0) {
            $.ajax({
                url: "responder.php",
                type: "POST",
                dataType: "json",
                data: { title: noteTitle, body: noteBody, action: 'create_note' },
                success: (data) => {
                    addNote(data[0].ID, data[0].TITLE, data[0].BODY, data[0].TIME_STAMP);
                    note_writer_modal.modal("hide");
                }
            });
        }
        else {
            Swal.fire({
                icon: 'error',
                title: 'You must provide a title and a body to save your note',
                toast: true,
                position: 'bottom',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        }
    });

    $(document).on("click", "#note_item", function () {
        item = $(this).closest("li");
        if (!item.hasClass("active")) {
            if (note_viewer_save_button.hasClass("disabled")) {
                currentNoteTitle = item.find('#note_item_title').text();
                currentNoteBody = item.find('#note_item_body').text();
                item.siblings().removeClass("active");
                item.siblings().addClass("bg-body-secondary");
                item.removeClass("bg-body-secondary");
                item.addClass("active");
                note_viewer_timestamp.text(item.find('#note_item_timestamp').text());
                note_viewer_title.val(currentNoteTitle);
                note_viewer_body.val(currentNoteBody);
                note_viewer_container.attr("note-id", item.attr("note-id"));
                note_viewer_container.fadeIn(350);
            }
            else {
                acd_title.text("Warning");
                acd_message.html("Are you sure that you want to open a new note and discard all the changes you have done in the currently opened one? All the changes you have done to it <strong>will be lost</strong>");
                acd_ok_button.attr('data-action', 'discard_edits');
                acd_ok_button.attr('note-id', item.attr("note-id"));
                actions_checker_dialog.modal("show");
            }
        }
    });

    $("#note_viewer_list_toggler").click(() => {
        toggleNotesListVisibility();
    });
    note_viewer_save_button.click(() => {
        acd_title.text("Warning");
        acd_message.html("Are you sure that you want to discard all the previous values from this note and save the new ones? This action <strong>can not</strong> be undone");
        acd_ok_button.attr('data-action', 'save_changes');
    });
    $('#note_viewer_delete_button').click(() => {
        acd_title.text("Warning");
        acd_message.html("Are you sure that you want to delete this note? This action <strong>can not</strong> be undone");
        acd_ok_button.attr('data-action', 'delete_note');
    });
    $('#close_note_viewer_button').click(() => {
        if (note_viewer_save_button.hasClass("disabled"))
            closeNoteViewer();
        else {
            acd_title.text("Warning");
            acd_message.html("Are you sure that you want to close this note without saving the modifications you have done to it? All your changes <strong>will be lost</strong>");
            acd_ok_button.attr('data-action', 'discard_changes');
            actions_checker_dialog.modal("show");
        }
    });

    $('#note_viewer_title, #note_viewer_body').on('input', function () {
        let currentTitle = note_viewer_title.val().trim(), currentBody = note_viewer_body.val().trim();
        disableSaveButton((currentTitle === currentNoteTitle && currentBody === currentNoteBody) || currentTitle.length == 0 || currentBody.length == 0);
    });

    $("#password_changer_button").click(() => {
        pcd_form.trigger("reset");
    });

    $("#destroy_account_button").click(() => {
        acd_title.html("<strong>Warning!</strong>");
        acd_message.html("Your account and all your notes will be erased. <strong>This action can not be undone!</strong>");
        acd_ok_button.attr('data-action', 'warn_account_removal');
    });

    $('#pcd_old_password_revealer, #pcd_new_password_revealer, #pcd_new_r_password_revealer').click(function () {
        inputField = $(this).siblings().find('input');
        if (inputField.attr("type") === "password") {
            inputField.attr("type", "text");
            $(this).find("svg").html('<use xlink:href="#hide" />');
        }
        else {
            inputField.attr("type", "password");
            $(this).find("svg").html('<use xlink:href="#show" />');
        }
    });

    pcd_ok_button.click(() => {
        pcd_form.trigger("submit");
    });

    pcd_form.submit((e) => {
        e.preventDefault();
        let old_pass = pcd_op.val().trim(), new_pass = pcd_np.val().trim(), new_r_pass = pcd_np_r.val().trim();
        if (old_pass.length < 4 || new_pass.length < 4 || new_r_pass.length < 4)
            Swal.fire({
                icon: 'error',
                title: 'All the passwords must contain at least 4 characters',
                toast: true,
                position: 'bottom',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        else if (new_pass !== new_r_pass)
            Swal.fire({
                icon: 'error',
                title: "The new password and its repetition must have the same value",
                toast: true,
                position: 'bottom',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        else if (new_pass === old_pass)
            Swal.fire({
                icon: 'error',
                title: "The new password and the old one can not be the same",
                toast: true,
                position: 'bottom',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        else {
            $.ajax({
                url: 'responder.php',
                method: 'POST',
                dataType: "json",
                data: { password: old_pass, newPassword: new_pass, action: 'update_password' },
                success: (response) => {
                    if (response == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: "Password changed!",
                            toast: true,
                            position: 'bottom',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                        password_changer_dialog.modal("hide");
                    }
                    else
                        Swal.fire({
                            icon: 'error',
                            title: "Please, verify the value of your current password",
                            toast: true,
                            position: 'bottom',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                },
                error: () => {
                    Swal.fire({
                        icon: 'error',
                        title: "Server error!",
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

    function addNote(id, title, body, timestamp, isActive = false) {
        notes_list.prepend("<li note-id='" + id + "' id='note_item' class='list-group-item list-group-item-action py-3 lh-sm " + (isActive ? "active" : "bg-body-secondary") + "'>" +
            "<div class='d-flex w-100 align-items-center justify-content-between'>" +
            "<strong class='unselectable-text one-line' id='note_item_title'>" + title + "</strong></div>" +
            "<div class='col-10 mb-1 small unselectable-text two-lines' id='note_item_body'>" + body + "</div>" +
            "<small id='note_item_timestamp' class='text-body-secondary unselectable-text one-line' style='font-size: xx-small; text-align: end;'>" + formatDate(timestamp) + "</small></li>");
    }

    function removeActiveNote() {
        notes_list.find('.active').remove();
    }

    function toggleNotesListVisibility() {
        notes_list_container.animate({ width: notes_list_container.width() == 340 ? 0 : 340 }, 350);
    }

    function closeNoteViewer() {
        if (notes_list_container.width() == 0)
            notes_list_container.animate({ width: 340 }, 350);
        currentNoteTitle = currentNoteBody = "";
        note_viewer_container.fadeOut(350);
        notes_list.children().removeClass("active");
        notes_list.children().addClass("bg-body-secondary");
        note_viewer_container.removeAttr("note-id");
        disableSaveButton();
    }

    function disableSaveButton(isDisabled = true) {
        if (isDisabled)
            note_viewer_save_button.addClass("disabled");
        else
            note_viewer_save_button.removeClass("disabled")
    }

    function formatDate(timestamp) {
        let date = new Date(timestamp * 1000), today = new Date(),
            month = ('0' + (date.getMonth() + 1)).slice(-2),
            day = ('0' + date.getDate()).slice(-2),
            year = date.getFullYear(),
            hours = ('0' + (date.getHours() % 12 || 12)).slice(-2),
            minutes = ('0' + date.getMinutes()).slice(-2),
            formattedHour = `${hours}:${minutes} ${date.getHours() < 12 ? 'AM' : 'PM'}`;
        return date.toDateString() === today.toDateString() ? formattedHour : `${day}/${month}/${year} - ${formattedHour}`;
    }

    acd_ok_button.click(() => {
        switch (acd_ok_button.attr('data-action')) {
            case 'logout':
                $.ajax({
                    url: "../mino/responder.php",
                    method: 'POST',
                    dataType: 'json',
                    data: { action: 'deauthenticate' },
                    success: () => {
                        window.location.href = "../index.php";
                    }
                });
                break;
            case 'save_changes':
                currentNoteTitle = note_viewer_title.val().trim();
                currentNoteBody = note_viewer_body.val().trim();
                $.ajax({
                    url: "responder.php",
                    type: "POST",
                    dataType: "json",
                    data: { note_id: note_viewer_container.attr('note-id'), title: currentNoteTitle, body: currentNoteBody, action: 'update_note' },
                    success: (data) => {
                        removeActiveNote();
                        disableSaveButton();
                        let rawTimestamp = data[0].TIME_STAMP;
                        addNote(data[0].ID, data[0].TITLE, data[0].BODY, rawTimestamp, true);
                        note_viewer_timestamp.text(formatDate(rawTimestamp));
                        actions_checker_dialog.modal("hide");
                    }
                });
                break;
            case 'delete_note':
                $.ajax({
                    url: "responder.php",
                    type: "POST",
                    dataType: "json",
                    data: { note_id: note_viewer_container.attr('note-id'), action: 'remove_note' },
                    success: () => {
                        removeActiveNote();
                        closeNoteViewer();
                        actions_checker_dialog.modal("hide");
                    }
                });
                break;
            case 'discard_changes':
                closeNoteViewer();
                actions_checker_dialog.modal("hide");
                break;
            case 'discard_edits':
                disableSaveButton();
                notes_list.find('[note-id="' + acd_ok_button.attr('note-id') + '"]').click();
                acd_ok_button.removeAttr('note-id');
                actions_checker_dialog.modal("hide");
                break;
            case 'warn_account_removal':
                acd_title.html("<strong>Warning!</strong>");
                acd_message.html("Are you completely sure that you want to delete your account and all its related notes? <strong>This action can not be undone!</strong>");
                acd_ok_button.attr('data-action', 'remove_account');
                actions_checker_dialog.modal('show');
                break;
            case 'remove_account':
                $.ajax({
                    url: 'responder.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { action: 'remove_account' },
                    success: (response) => {
                        if (response == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: "Your account was successfuly removed!",
                                toast: true,
                                position: 'bottom',
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false,
                            });
                            window.location.href = "../index.php";
                        }
                        else
                            Swal.fire({
                                icon: 'error',
                                title: "Your account could not be removed",
                                toast: true,
                                position: 'bottom',
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false,
                            });
                    },
                    error: () => {
                        Swal.fire({
                            icon: 'error',
                            title: "Server error!",
                            toast: true,
                            position: 'bottom',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                    }
                });
                break;
        }
    });
});